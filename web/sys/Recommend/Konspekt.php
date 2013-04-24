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
        if ($this->checkCondition($filters)) {
            $this->_searchObject->addFacet($this->_categoryFacet, $this->_categoryFacetName);
            $this->_searchObject->addFacet($this->_subcategoryFacet, $this->_subcategoryFacetName);
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
        if ($this->checkCondition($filters)) {
            $interface->assign('showKonspekt', true);
            $interface->assign(
                'konspektFacetSet', $this->_searchObject->getFacetList($this->_facets, false)
            );
            $interface->assign('topFacetSettings', $this->_baseSettings);
        } else {
            $interface->assign('showKonspekt', false);
        }
    }
    
    private function checkCondition($filters) {
        return isset($filters[$this->_categoryFacet]) || isset($filters[$this->_subcategoryFacet]);
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

}

?>
