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
 * @author   Vaclav Rosecky <xrosecky@gmail.com>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/autocomplete Wiki
 */
class SolrEdgeFacetedAutocomplete implements AutocompleteInterface
{

    protected $fields;
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
        global $configArray;
        foreach (explode(':', $params) as $part) {
            list($facetField, $autoField) = explode(',', $part);
            $this->fields[$facetField] = $autoField;
        }
        $this->url = $configArray["Index"]["url"] . "/" . $configArray["Index"]["default_core"];
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
    
    protected function escapeQuery($query) {
        return str_replace(array('-'), array('\-'), $query);
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
        $params = array(
            'q'              => "*:*",
            'q.op'           => 'AND',
            'rows'           => '0',
            'facet'          => 'true',
            'facet.mincount' => '1',
            'facet.limit'    => '20',
            'echoParams'     => 'none',
            'wt'             => 'json',
            'json.nl'        => 'arrarr',
            'indent'         => 'on',
        );
        $params['fq'] = array();
        $params['facet.field'] = array();
        foreach($this->fields as $facetField => $autoField) {
            $excludeFields = array();
            foreach($this->fields as $exFacetField => $exAutoField) {
                if ($autoField != $exAutoField) {
                    $excludeFields[] = $exAutoField;
                }
            }
            $excludeFields = implode(',',  $excludeFields);
            $facetFieldLocalParam = "{!ex=$excludeFields}";
            $params['fq'][] = "{!tag=$autoField}$autoField:($query)";
            $params['facet.field'][] = $facetFieldLocalParam . $facetField;
        }
        $query = $this->build($params);
        $url = $this->url . '/select?' . $query;
        $response = file_get_contents($url);
        $json = json_decode(file_get_contents($url));
        $result = array();
        foreach($this->fields as $facetField => $autoField) {
            foreach($json->{'facet_counts'}->{'facet_fields'}->{$facetField} as $item) {
                $value = $this->escapeQuery($item[0]);
                $count = $item[1];
                $result[$value] = $count;
            }
        }
        arsort($result);
        $suggestions = array_keys($result);
        array_splice($suggestions, 10);
        return $suggestions;
    }
    
    protected function build($params) {
        $query = '';
        $sep = '&';
        foreach ($params as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $item) {
                    $query .= $key . '=' . urlencode($item) . $sep;
                }
            } else {
                $query .= $key . '=' . urlencode($value) . $sep;
            }
        }
        return $query;
    }

}

?>