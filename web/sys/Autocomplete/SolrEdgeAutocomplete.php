<?php
/**
 * Solr Autocomplete Module
 *
 * PHP version 5
 *
 * Copyright (C) Villanova University 2010.
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
 * @package  Autocomplete
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/autocomplete Wiki
 */
require_once 'sys/Autocomplete/Interface.php';

/**
 * Solr Autocomplete Module
 *
 * This class provides suggestions by using the local Solr index.
 *
 * @category VuFind
 * @package  Autocomplete
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/autocomplete Wiki
 */
class SolrEdgeAutocomplete implements AutocompleteInterface
{

    protected $handler;
    protected $displayField;
    protected $defaultDisplayField = 'title';
    protected $sortField;
    protected $filters;
    protected $searchObject;
    protected $url;

    /**
     * Constructor
     *
     * Establishes base settings for making autocomplete suggestions.
     *
     * @param string $params Additional settings from searches.ini.
     *
     * @access public
     */
    public function __construct($params)
    {
        // Save the basic parameters:
        $params = explode(':', $params);
        $this->displayField = (isset($params[0]))?split(',', $params[0]):null;
        if ($this->displayField[0] == "") {
            $this->displayField = null;
        }
        $this->initSearchObject();
        global $configArray;
        $searchSettings = getExtraConfigArray('searches');
        $core = $searchSettings["SolrEdgeAutocomplete"]["core"];
        if (isset($searchSettings["SolrEdgeAutocomplete"]["url"])) {
            $this->url = $searchSettings["SolrEdgeAutocomplete"]["url"] . "/" . $core;
        } else {
            $this->url = $configArray["Index"]["url"] . "/" . $core;
        }
    }

    /**
     * initSearchObject
     *
     * Initialize the search object used for finding recommendations.
     *
     * @return void
     * @access  protected
     */
    protected function initSearchObject()
    {
    }

    /**
     * mungeQuery
     *
     * Process the user query to make it suitable for a Solr query.
     *
     * @param string $query Incoming user query
     *
     * @return string       Processed query
     * @access protected
     */
    protected function mungeQuery($query)
    {
        // Modify the query so it makes a nice, truncated autocomplete query:
        $forbidden = array(':', '(', ')', '*', '+', '"', '-');
        $query = str_replace($forbidden, " ", $query);
        return $query;
    }

    /**
     * getSuggestions
     *
     * This method returns an array of strings matching the user's query for
     * display in the autocomplete box.
     *
     * @param string $query The user query
     *
     * @return array        The suggestions for the provided query
     * @access public
     */
    public function getSuggestions($query)
    {
        $query = trim($this->mungeQuery($query));
        if ($query == "") {
           return array();
        }
        $search = "key:$query";
        if (strpos($query, " ") !== false) {
           $search = "";
           $sep = "";
           foreach(split(" ", $query) as $term) {
              if (trim($term) != "") {
                 $search .= $sep . "key:" . trim($term);
                 $sep = " AND ";
              }
           }
        }
        if ($this->displayField != null) {
           $search .= " AND (";
           $sep = "";
           foreach ($this->displayField as $df) {
              $search .= $sep . "field:" . $df;
              $sep = " OR ";
           }
           $search .= ")";
        }
        $search = urlencode($search);
        $url = $this->url . "/select/?q=$search&fl=key&wt=json&indent=on&echoParams=none&rows=10&sort=count%20desc";
        $json = json_decode(file_get_contents($url));
        $result = array();
        foreach($json->{'response'}->{'docs'} as $item) {
            $result[] = (string) $this->escape($item->{"key"});
        }
        return $result;
    }

    protected function escape($term) {
        return str_replace(array(' :', ' -'), array(':', '-'), $term);
    }

    /**
     * setDisplayField
     *
     * Set the display field list.  Useful for child classes.
     *
     * @param array $new Display field list.
     *
     * @return void
     * @access protected
     */
    protected function setDisplayField($new)
    {
        $this->displayField = $new;
    }

    /**
     * setSortField
     *
     * Set the sort field list.  Useful for child classes.
     *
     * @param string $new Sort field list.
     *
     * @return void
     * @access protected
     */
    protected function setSortField($new)
    {
        $this->sortField = $new;
    }
}

?>