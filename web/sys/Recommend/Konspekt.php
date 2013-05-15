<?php
/**
 * MapScale
 *
 * PHP version 5
 *
 * Copyright (C) Václav Rosecký 2013.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @category VuFind
 * @package  Recommendations
 * @author   Václav Rosecký <xrosecky@gmail.com>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_a_recommendations_module Wiki
 */

require_once 'sys/Recommend/Interface.php';

/**
 * Konspekt Recommendations Module
 *
 * @category VuFind
 * @package  Recommendations
 * @author   Václav Rosecký <xrosecky@gmail.com>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_a_recommendations_module Wiki
 */
class Konspekt implements RecommendationInterface
{
    private $_searchObject;
    private $_categoryFacet = "category_txtF";
    private $_categoryFacetName = "Conspectus category";
    private $_subcategoryFacet = "subcategory_txtF";
    private $_subcategoryFacetName = "Conspectus subcategory";
    private $_facetsToActivate = array("acq_int");

    /**
     * Constructor
     *
     * Establishes base settings for making recommendations.
     *
     * @param object $searchObject The SearchObject requesting recommendations.
     * @param string $params       Additional settings from searches.ini.
     *
     * @access public
     */
    public function __construct($searchObject, $params)
    {
        //Save the basic parameters:
        $this->_searchObject = $searchObject;

        // Parse the additional parameters:
        $params = explode(':', $params);
        $section = empty($params[0]) ? 'ResultsTop' : $params[0];
        $iniFile = isset($params[1]) ? $params[1] : 'facets';

        // Load the desired facet information:
        $config = getExtraConfigArray($iniFile);
        $this->_facets = array($this->_subcategoryFacet => $this->_subcategoryFacetName);

        // Load other relevant settings:
        $this->_baseSettings = array(
            'rows' => $config['Results_Settings']['top_rows'],
            'cols' => $config['Results_Settings']['top_cols']
        );
    }

    /**
     * init
     *
     * Called before the SearchObject performs its main search.  This may be used
     * to set SearchObject parameters in order to generate recommendations as part
     * of the search.
     *
     * @return void
     * @access public
     */
    public function init()
    {
        $filters = $this->_searchObject->getFilters();
        if ($this->checkConditionForSubcategories($filters)) {
            $this->_searchObject->addFacet($this->_categoryFacet, $this->_categoryFacetName);
            $this->_searchObject->addFacet($this->_subcategoryFacet, $this->_subcategoryFacetName);
        }
        if ($this->checkConditionForCategories($filters)) {
            $this->_searchObject->addFacet($this->_categoryFacet, $this->_categoryFacetName);
        }
    }

    /**
     * process
     *
     * Called after the SearchObject has performed its main search.  This may be
     * used to extract necessary information from the SearchObject or to perform
     * completely unrelated processing.
     *
     * @return void
     * @access public
     */
    public function process()
    {
        global $interface;
        $filters = $this->_searchObject->getFilters();
        if ($this->checkConditionForSubcategories($filters)) {
            $interface->assign('showConspectusSubcategories', true);
            $facets = $this->_searchObject->getFacetList($this->_facets, false);
            usort($facets[$this->_subcategoryFacet]['list'], "Konspekt::compare");
            $interface->assign(
                'konspektFacetSet', $facets
            );
            $interface->assign('topFacetSettings', $this->_baseSettings);
        } else if ($this->checkConditionForCategories($filters)) {
            $interface->assign('showConspectusCategories', true);
            $facets = $this->_searchObject->getFacetList(array($this->_categoryFacet => $this->_categoryFacetName), false);
            $facets = $this->_processFacets($facets, $this->_searchObject);
            $facets = $facets[$this->_categoryFacetName];
            $interface->assign('konspektFacetSet', $facets);
            $interface->assign('conspectusCategoriesExcludeFields', array($this->_categoryFacet));
        } else {
            $interface->assign('showConspectusSubcategories', false);
            $interface->assign('showConspectusCategories', false);
        }
    }
    
    private function checkConditionForSubcategories($filters) {
        return (isset($filters[$this->_categoryFacet]) || isset($filters[$this->_subcategoryFacet]));
    }
    
    private function checkConditionForCategories($filters) {
        foreach ($this->_facetsToActivate as $facet) {
            if (isset($filters[$facet])) {
                return true;
            }
        }
    }

    /**
     * getTemplate
     *
     * This method provides a template name so that recommendations can be displayed
     * to the end user.  It is the responsibility of the process() method to
     * populate all necessary template variables.
     *
     * @return string The template to use to display the recommendations.
     * @access public
     */
    public function getTemplate()
    {
        return 'Search/Recommend/Konspekt.tpl';
    }
    
    public static function compare($a, $b) {
        return strcoll($a['value'], $b['value']);
    }
    
    // taken from web/services/Search/Advanced.php
    private function _processFacets($facetList, $searchObject = false)
    {
        // Process the facets, assuming they came back
        $facets = array();
        foreach ($facetList as $facet => $list) {
            $currentList = array();
            foreach ($list['list'] as $value) {
                // Build the filter string for the URL:
                $fullFilter = $facet.':"'.$value['untranslated'].'"';
    
                // If we haven't already found a selected facet and the current
                // facet has been applied to the search, we should store it as
                // the selected facet for the current control.
                if ($searchObject && $searchObject->hasFilter($fullFilter)) {
                    $selected = true;
                    // Remove the filter from the search object -- we don't want
                    // it to show up in the "applied filters" sidebar since it
                    // will already be accounted for by being selected in the
                    // filter select list!
                    $searchObject->removeFilter($fullFilter);
                } else {
                    $selected = false;
                }
                $currentList[$value['value']]
                = array('filter' => $fullFilter, 'selected' => $selected);
            }
    
            // Perform a natural case sort on the array of facet values:
            $keys = array_keys($currentList);
            natcasesort($keys);
            foreach ($keys as $key) {
                $facets[$list['label']][$key] = $currentList[$key];
            }
        }
        return $facets;
    }

}

?>
