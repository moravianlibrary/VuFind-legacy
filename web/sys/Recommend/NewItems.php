<?php
/**
 * NewItems
 *
 * PHP version 5
 *
 * Copyright (C) Václav Rosecký 2012.
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
 * NewItems Recommendations Module
 *
 * @category VuFind
 * @package  Recommendations
 * @author   Václav Rosecký <xrosecky@gmail.com>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_a_recommendations_module Wiki
 */
class NewItems implements RecommendationInterface
{
    private $_searchObject;
    private $_fieldDate = 'acq_int';
    private $_fieldDateLabel = 'Acquisition_range_facet';
    private $_categoryFacet = "category_txtF";
    private $_categoryFacetLabel = "category";

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
        $this->_searchObject = $searchObject;
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
        if (!$this->checkCondition($filters)) {
            $interface->assign('showNewItems', false);
            return;
        }
        $curr_date = date('Ym', strtotime('now'));
        $interface->assign('newItemsLink', $this->createFilter($this->createRange($curr_date, $curr_date)));
        $field = $this->_fieldDate;
        $curr_range = $filters[$field][0];
        $interface->assign('showNewItems', true);
        $s1 = date('Ym', strtotime('last year'));
        $e1 = date('Ym', strtotime('last year december'));
        $s2 = date('Ym', strtotime('this year january'));
        $e2 = $curr_date;
        $ranges = array_merge(range($e2, $s2), range($e1, $s1));
        foreach ($ranges as $date) {
            $range = $this->createRange($date, $e2);
            $label = $this->createLabel($date);
            $newItemsDates[$label] = array(
                'filter' => $this->createFilter($range),
                'selected' => ($curr_range == $range)
            );
        }
        $interface->assign('newItemsDates', $newItemsDates);
        $searchObject = SearchObjectFactory::initSearchObject();
        $searchObject->addFacet($this->_categoryFacet, $this->_categoryFacetLabel);
        $searchObject->init();
        $searchObject->processSearch();
        $facets = $searchObject->getFacetList();
        $facets = $facets[$this->_categoryFacet]['list'];
        usort($facets, "NewItems::compare");
        $interface->assign('newItemsConspectusCategories', $facets);
        $interface->assign('newItemsConspectusField', $this->_categoryFacet);
        $interface->assign('newItemsConspectusLabel', $this->_categoryFacetLabel);
        $interface->assign('newItemsExcludeFields', array($this->_fieldDate));
        $interface->assign('newItemsConspectusExcludeFields', array($this->_categoryFacet));
        $this->_searchObject->addFacet($this->_fieldDate, $this->_fieldDateLabel);
    }
    
    private function createFilter($range) {
        return "{$this->_fieldDate}:$range";
    }
    
    private function createRange($begin, $end) {
        return "[$begin TO $end]";
    }
    
    private function createLabel($date) {
        $date = substr($date, 0, 4) . '-' . substr($date, 4, 6);
        return strftime('%B %Y', strtotime($date));
    }

    private function checkCondition($filters) {
       return (isset($filters[$this->_fieldDate]) && $this->_searchObject->getResultTotal() > 0);
    }
    
    public static function compare($a, $b) {
        return strcoll($a['value'], $b['value']);
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
        return 'Search/Recommend/NewItems.tpl';
    }

}

?>
