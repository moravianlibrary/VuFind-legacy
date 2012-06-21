<?php
/**
 * A derivative of the Search Object for use with EBSCO.
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
require_once 'sys/SearchObject/Base.php';

/**
 * A derivative of the Search Object for use with EBSCO.
 *
 * @category VuFind
 * @package  SearchObject
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_a_search_object Wiki
 */
class SearchObject_EBSCOHost extends SearchObject_Base
{
    protected $_baseUrl = 'http://eit.ebscohost.com/Services/SearchService.asmx/Search';
    protected $_params = array();
    protected $_indexResult; // EBSCO Search Response;

    public function __construct()
    {
        parent::__construct();
        $this->resultsModule = 'EBSCOHost';
        $this->resultsAction = 'Search';
        $config = getExtraConfigArray("EBSCOHost");
        foreach ($config['Facets'] as $key => $value) {
            $parts = explode(',', $key);
            $facetName = trim($parts[0]);
            $this->facetConfig[$facetName] = $value;
        }
        // Set up search options
        $this->basicTypes = $config['Basic_Searches'];
        $this->recommendIni = 'EBSCOHost';
        
        if (isset($config['General']['url'])) {
            $this->_baseUrl = $config['General']['url'];
        }
        $this->_params['orgid'] = $config['General']['orgid'];
        $this->_params['prof'] = $config['General']['prof'];
        $this->_params['pwd'] = $config['General']['pwd'];
        $this->_params['authType'] = $config['General']['authType'];
        $this->_params['ipprof'] = $config['General']['ipprof'];
        $this->_params['db'] = isset($config['General']['db']) ? $config['General']['db'] : null;
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

    /**
    * Add a field to facet on.
    *
    * @param string $newField Field name
    * @param string $newAlias Optional on-screen display label
    *
    * @return void
    * @access public
    */
    public function addFacet($newField, $newAlias = null)
    {
        // Save the full field name (which may include extra parameters);
        // we'll need these to do the proper search using the Summon class:
        $this->_fullFacetSettings[] = $newField;
    
        // Strip parameters from field name if necessary (since they get
        // in the way of most Search Object functionality):
        $newField = explode(',', $newField);
        $newField = trim($newField[0]);
        parent::addFacet($newField, $newAlias);
    }
    
    /**
     * Returns the stored list of facets for the last search
     *
     * @param array $filter         Array of field => on-screen description listing
     * all of the desired facet fields; set to null to get all configured values.
     * @param bool  $expandingLinks If true, we will include expanding URLs (i.e.
     * get all matches for a facet, not just a limit to the current search) in the
     * return array.
     *
     * @return array                Facets data arrays
     * @access public
     */
    public function getFacetList($filter = null, $expandingLinks = false)
    {
        // If there is no filter, we'll use all facets as the filter:
        if (is_null($filter)) {
            $filter = $this->facetConfig;
        } else {
            // If there is a filter, make sure the field names are properly
            // stripped of extra parameters:
            $oldFilter = $filter;
            $filter = array();
            foreach ($oldFilter as $key => $value) {
                $key = explode(',', $key);
                $key = trim($key[0]);
                $filter[$key] = $value;
            }
        }

        // We want to sort the facets to match the order in the .ini file.  Let's
        // create a lookup array to determine order:
        $i = 0;
        $order = array();
        foreach ($filter as $key => $value) {
            $order[$key] = $i++;
        }
        $list = array();
        // Loop through every field returned by the result set
        $validFields = array_keys($filter);
        foreach ($this->_indexResult['facetFields'] as $data) {
            $field = $data['id'];
            $tag = $data['tag']; 
            // Skip filtered fields and empty arrays:
            if (!in_array($field, $validFields) || count($data) < 1) {
                continue;
            }
            $i = $order[$field];
            $list[$i]['label'] = $filter[$field];
            $list[$i]['tag'] = $tag;
            // Should we translate values for the current facet?
            $translate = in_array($field, $this->translatedFacets);
            // Loop through values:
            foreach ($data['values'] as $facet) {
                // Initialize the array of data about the current facet:
                $currentSettings = array();
                $currentSettings['value']
                    = $translate ? translate($translationPrefix . $facet) : $facet;
                $currentSettings['untranslated'] = $facet;
                $currentSettings['count'] = '-';
                $currentSettings['isApplied'] = false;
                $currentSettings['url']
                    = $this->renderLinkWithFilter("$field:".$facet);
                // If we want to have expanding links (all values matching the
                // facet) in addition to limiting links (filter current search
                // with facet), do some extra work:
                if ($expandingLinks) {
                    $currentSettings['expandUrl']
                        = $this->getExpandingFacetLink($field, $facet);
                }
                // Is this field a current filter?
                if (in_array($field, array_keys($this->filterList))) {
                    // and is this value a selected filter?
                    if (in_array($facet, $this->filterList[$field])) {
                        $currentSettings['isApplied'] = true;
                    }
                }
        
                // Put the current facet cluster in order based on the .ini
                // settings, then override the display name again using .ini
                // settings.
                $currentSettings['label'] = $filter[$field];
                
                // Store the collected values:
                $list[$i]['list'][] = $currentSettings;
            }
        }
        ksort($list);

        // Rewrite the sorted array with appropriate keys:
        $finalResult = array();
        foreach ($list as $current) {
            $finalResult[$current['tag']] = $current;
        }

        return $finalResult;
    }

    /**
    * Load all available facet settings.  This is mainly useful for showing
    * appropriate labels when an existing search has multiple filters associated
    * with it.
    *
    * @param string $preferredSection Section to favor when loading settings; if
    * multiple sections contain the same facet, this section's description will be
    * favored.
    *
    * @return void
    * @access public
    */
    public function activateAllFacets($preferredSection = false)
    {
        foreach ($this->allFacetSettings as $section => $values) {
            foreach ($values as $key => $value) {
                $this->addFacet($key, $value);
            }
        }

        if ($preferredSection
            && is_array($this->allFacetSettings[$preferredSection])
        ) {
            foreach ($this->allFacetSettings[$preferredSection] as $key => $value) {
                $this->addFacet($key, $value);
            }
        }
    }
    
    public function processSearch($returnIndexErrors = false, 
        $recommendations = false) 
    {
        // $docs = array(array("title" => "1", "id" => 'test', 'recordtype' => 'marc'), array("title" => "1"));
        /*$docs = $this->executeSearch("foo");
        $this->_indexResult = array('responseHeader' => array('start' => 0), 'recordCount' => 2, 'response' => array('numFound' => 2, 'start' => 0, 'maxScore' => 1, 'docs' => $docs ));*/

        // Get time before the query
        $this->startQueryTimer();
        
        if ($recommendations) {
            $this->initRecommendations();
        }
        $query = $this->searchTerms[0]['lookfor'];
        $startRec = ($this->page - 1) * $this->limit;
        //print "$recordStart<BR>";
        $this->_indexResult = $this->executeSearch($this->buildURL($query, $startRec, $this->limit));
        
        // Get time after the query
        $this->stopQueryTimer();
        
        $this->resultsTotal = $this->_indexResult['recordCount']; // $this->getFacetList(null, false)
        //$this->_indexResult['response']['facet_counts'] = array(array('facet_queries' => $this->getFacetList(), 'facet_fields' => $this->getFacetList()));
        //var_export($this->searchTerms); print "<BR>";
        
        // If extra processing is needed for recommendations, do it now:
        if ($recommendations && is_array($this->recommend)) {
            foreach ($this->recommend as $currentSet) {
                foreach ($currentSet as $current) {
                    $current->process();
                }
            }
        }
        
        return $this->_indexResult;
    }

    public function buildURL($query, $startRec, $limit) 
    {
        $filterQuery = '';
        foreach ($this->getFilterList() as $filters) {
            foreach ($filters as $filter) {
                $field = $filter['field'];
                $value = $filter['value'];
                if ($filterQuery) {
                    $filterQuery .= '+AND+';
                }
                $filterQuery .= '(' . urlencode($field) . '+(' . urlencode($value) . '))';
            }
        }
        
        $params = array("numrec" => $limit, "format" => "full", 
        	"clusters" => "true", "clusters" => "true", "sort" => "relevance");
        $params += $this->_params;
        if ($startRec > 0) {
           $params = array_merge($params, array("startrec" => $startRec));
        }
        $url = $this->_baseUrl . '?' . http_build_query($params) . '&query=(' . urlencode($query) . ')';
        if ($filterQuery) {
            $url .= "+AND+$filterQuery";
        }
        return $url;
    }

    public function executeSearch($url) 
    {
        $response = file_get_contents($url);
        $xml = simplexml_load_string($response);
        $hits = $xml->{"Hits"};
        $hits = (string) $hits[0];
        $results = array();
        foreach ($xml->xpath('//rec') as $item) {
            $id = $item->attributes()->id;
            $authors = array();
            foreach ($item->xpath('.//aug/au') as $author) {
               $authors[] = (string) $author;
            }
            $title = $item->xpath('.//tig/atl');
            $url = $item->xpath('.//plink/text()');
            $pdf = $item->xpath('.//pdfLink/text()');
            $result = array("author" => $authors, "title" => (string) $title[0], "id" => $id, "url" => $url[0]);
            if ($pdf) {
                $result['pdf'] = (string) $pdf[0];
            }
            $results[] = $result;
        }
        $facets = array();
        $xml->registerXPathNamespace('ssresp', 'http://epnet.com/webservices/SearchService/Response/2007/07/');
        foreach ($xml->xpath('//ssresp:Facets/ssresp:Clusters/ssresp:ClusterCategory') as $item) {
            $id = (string) $item->attributes()->ID;
            $tag = (string) $item->attributes()->Tag;
            $values = array();
            $item->registerXPathNamespace('ssresp', 'http://epnet.com/webservices/SearchService/Response/2007/07/');
            foreach ($item->xpath('./ssresp:Cluster') as $cluster) {
                $values[] = (string) $cluster;
            }
            $facets[] = array('id' => $tag, 'tag' => $tag, 'values' => $values);
        }
        return array('recordCount' => $hits, 'response' => array('numFound' => $hits, 'start' => 0, 'docs' => $results), 'facetFields' => $facets);
    }

}
