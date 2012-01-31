<?php
/**
 * A derivative of the Search Object for use with Summon.
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
 * @package  SearchObject
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_a_search_object Wiki
 */
require_once 'sys/EBSCO.php';
require_once 'sys/SearchObject/Base.php';

/**
 * A derivative of the Search Object for use with Summon.
 *
 * @category VuFind
 * @package  SearchObject
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_a_search_object Wiki
 */
class SearchObject_EBSCO extends SearchObject_Base
{

    public function __construct()
    {
        parent::__construct();
        $this->resultsModule = 'EBSCO';
        $this->resultsAction = 'Search';
        $config = getExtraConfigArray("EBSCO");
        foreach ($config['Facets'] as $key => $value) {
            $parts = explode(',', $key);
            $facetName = trim($parts[0]);
            $this->facetConfig[$facetName] = $value;
        }
        // Set up search options
        $this->basicTypes = $config['Basic_Searches'];
        $this->recommendIni = 'EBSCO';
    }

    /**
     * Turn the list of spelling suggestions into an array of urls
     *   for on-screen use to implement the suggestions.
     *
     * @return array Spelling suggestion data arrays
     * @access public
     */
    public function getSpellingSuggestions()
    {
        return array();
    }

    public function getIndexError()
    {
        return false;
    }

    public function init()
    {
        global $module;
        global $action;

        // Call the standard initialization routine in the parent:
        parent::init();

        //********************
        // Check if we have a saved search to restore -- if restored successfully,
        // our work here is done; if there is an error, we should report failure;
        // if restoreSavedSearch returns false, we should proceed as normal.
        $restored = $this->restoreSavedSearch();
        if ($restored === true) {
            return true;
        } else if (PEAR::isError($restored)) {
            return false;
        }

        //********************
        // Initialize standard search parameters
        $this->initView();
        $this->initPage();
        $this->initSort();
        $this->initFilters();
        $this->initLimit();

        //********************
        // Basic Search logic
        if ($this->initBasicSearch()) {
            // If we found a basic search, we don't need to do anything further.
        } else if (isset($_REQUEST['tag']) && $module != 'MyResearch') {
            // Tags, just treat them as normal searches for now.
            // The search processer knows what to do with them.
            if ($_REQUEST['tag'] != '') {
                $this->searchTerms[] = array(
                    'index'   => 'tag',
                    'lookfor' => $_REQUEST['tag']
                );
            }
        } else {
            $this->initAdvancedSearch();
        }
        $this->limit = 10;
    }

    public function getFacetList($filter = null, $expandingLinks = false)
    {
       print "debug<BR>";
       $result = array ( 'statuses' => array ( 'label' => 'Loan type', 'list' => array ( 0 => array ( 'value' => 'prezenční', 'untranslated' => 'present', 'count' => 244, 'isApplied' => false, 'url' => 'https://vufind-trunk.mzk.cz/Search/Results?lookfor=trest&type=AllFields&filter[]=statuses%3A%22present%22&sort=year&view=list&limit=20', ), 1 => array ( 'value' => 'absenční', 'untranslated' => 'absent', 'count' => 90, 'isApplied' => false, 'url' => 'https://vufind-trunk.mzk.cz/Search/Results?lookfor=trest&type=AllFields&filter[]=statuses%3A%22absent%22&sort=year&view=list&limit=20', ), ), ) );
       return $result;
    }

    public function processSearch(
        $returnIndexErrors = false, $recommendations = false
    ) {
        // $docs = array(array("title" => "1", "id" => 'test', 'recordtype' => 'marc'), array("title" => "1"));
        /*$docs = $this->executeSearch("foo");
        $this->_indexResult = array('responseHeader' => array('start' => 0), 'recordCount' => 2, 'response' => array('numFound' => 2, 'start' => 0, 'maxScore' => 1, 'docs' => $docs ));*/
        if ($recommendations) {
            $this->initRecommendations();
        }
        $query = $this->searchTerms[0]['lookfor'];
        $startRec = ($this->page - 1) * $this->limit;
        //print "$recordStart<BR>";
        $this->_indexResult = $this->executeSearch($this->buildURL($query, $startRec, $this->limit));
        $this->resultsTotal = $this->_indexResult['recordCount']; // $this->getFacetList(null, false)
        //$this->_indexResult['response']['facet_counts'] = array(array('facet_queries' => $this->getFacetList(), 'facet_fields' => $this->getFacetList()));
        //var_export($this->searchTerms); print "<BR>";
        return $this->_indexResult;
    }

    public function buildURL($query, $startRec, $limit) {
        $baseURL = "http://eit.ebscohost.com/Services/SearchService.asmx/Search";
        $params = array("authType" => "ip", "ipprof" => "eit", "numrec" => $limit,
           "db" => "a9h", "format" => "full", "clusters" => "true", "query" => $query);
        if ($startRec > 0) {
           $params = array_merge($params, array("startrec" => $startRec));
        }
        $url = $this->appendQueryString($baseURL, $params);
        return $url;
    }

    //  ?prof=&pwd=&authType=ip&ipprof=eit&db=a9h&format=full&query=test&clusters=true";
    protected function appendQueryString($url, $params) {
        $sep = (strpos($url, "?") === false)?'?':'&';
        if ($params != null) {
           foreach ($params as $key => $value) {
              $url.= $sep . $key . "=" . urlencode($value);
              $sep = "&";
           }
        }
        return $url;
    }


    public function executeSearch($url) {
        $xml = simplexml_load_string(file_get_contents($url));
        $hits = $xml->{"Hits"};
        $hits = (string) $hits[0];
        $result = array();
        foreach ($xml->xpath('//rec') as $item) {
            $authors = array();
            foreach ($item->xpath('.//aug/au') as $author) {
               $authors[] = (string) $author;
            }
            $title = $item->xpath('.//tig/atl');
            $url = $item->xpath('.//plink/text()');
            $pdf = $item->xpath('.//pdfLink/text()');
            $result[] = array("author" => $authors, "title" => (string) $title[0], "id" => "1", "url" => $url[0], "pdf" => $pdf[0]);
        }
        return array('recordCount' => $hits, 'response' => array('numFound' => $hits, 'start' => 0, 'docs' => $result));
    }

}
