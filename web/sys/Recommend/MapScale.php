<?php
/**
 * MapScale
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
 * MapScale Recommendations Module
 *
 * @category VuFind
 * @package  Recommendations
 * @author   Václav Rosecký <xrosecky@gmail.com>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_a_recommendations_module Wiki
 */
class MapScale implements RecommendationInterface
{
    private $_searchObject;
    private $_field;
    private $_scales;

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
        list($this->_field, $this->_condKey, $this->_condVal, $scales) = explode(":", $params);
        $this->_scales = explode(",", $scales);
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
        // nothing to do
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
            $interface->assign('showMapScale', true);
            if (isset($filters[$this->_field])) {
                foreach ($filters[$this->_field] as $filter) {
                    if ($range = VuFindSolrUtils::parseRange($filter)) {
                        $from = $range['from'] == '*' ? '' : $range['from'];
                        $to = $range['to'] == '*' ? '' : $range['to'];
                        $interface->assign('rangeFrom', $from);
                        $interface->assign('rangeTo', $to);
                        break;
                    }
                }
            }
            $interface->assign('scales', $this->_scales);
            $interface->assign('field', $this->_field);
        } else {
            $interface->assign('showMapScale', false);
        }
    }

    private function checkCondition($filters) {
       if (isset($filters[$this->_condKey])) {
           foreach ($filters[$this->_condKey] as $val) {
               if ($val == $this->_condVal) {
                   return true;
               }
           }
       } else {
           return false;
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
        return 'Search/Recommend/MapScale.tpl';
    }

}

?>
