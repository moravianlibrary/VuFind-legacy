<?php
/**
 * SwitchType Recommendations Module
 *
 * PHP version 5
 *
 * Copyright (C) Villanova University 2009.
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
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_a_recommendations_module Wiki
 */

require_once 'sys/Recommend/Interface.php';

/**
 * CallNumber Recommendations Module
 *
 * This class recommends switching to a different search type.
 *
 * @category VuFind
 * @package  Recommendations
 * @author   Vaclav Rosecky <xrosecky@gmail.com>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_a_recommendations_module Wiki
 */
class RegexRecommend implements RecommendationInterface
{
    protected $searchObject;
    protected $recommendations;

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
        // Save the basic parameters:
        $this->searchObject = $searchObject;

        // Process parameters:
        $sectionName = $params;
        $params = explode(':', $params);
        $section = empty($params[0]) ? 'RegexRecommend' : $params[0];
        $iniFile = isset($params[1]) ? $params[1] : 'searches';
        
        // Load the desired facet information:
        $config = getExtraConfigArray($iniFile);
        $this->recommendations = $config[$section];
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
        // No action needed
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

        $searchType = $this->searchObject->getSearchIndex();
        $searchTerms = $this->searchObject->getSearchTerms();
        $query = trim($searchTerms[0]['lookfor']);
        $result = array();
        foreach ($this->recommendations as $type => $regex) {
            if (!is_null($searchType) && $searchType != $type && preg_match($regex, $query)) {
                $url = $this->searchObject->renderLinkWithReplacedIndex($searchType, $type);
                $result[$type] = $url;
            }
        }
        $interface->assign('regexRecommendRecommendations', $result);
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
        return 'Search/Recommend/RegexRecommend.tpl';
    }
}

?>