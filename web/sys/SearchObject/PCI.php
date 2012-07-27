<?php
/**
 * A derivative of the Search Object for use with PCI.
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
 * A derivative of the Search Object for use with PCI.
 *
 * @category VuFind
 * @package  SearchObject
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_a_search_object Wiki
 */
class SearchObject_PCI extends SearchObject_Base
{
    protected $_baseUrl = '';
    protected $_params = array();
    protected $_indexResult; // PCI Search Response;

    public function __construct()
    {
        parent::__construct();
        $this->resultsModule = 'PCI';
        $this->resultsAction = 'Search';
        $config = getExtraConfigArray("PCI");
        foreach ($config['Facets'] as $key => $value) {
            $parts = explode(',', $key);
            $facetName = trim($parts[0]);
            $this->facetConfig[$facetName] = $value;
        }
        
        // Set up basic and advanced EBSCO search types; default to basic.
        $this->searchType = $this->basicSearchType = 'PCI';
        $this->advancedSearchType = 'PCIAdvanced';
        
            // Set up search options
        $this->basicTypes = $config['Basic_Searches'];
        if (isset($config['Advanced_Searches'])) {
            $this->advancedTypes = $config['Advanced_Searches'];
        }
        
        $this->recommendIni = 'PCI';
        
        if (isset($config['General']['url'])) {
            $this->_baseUrl = $config['General']['url'];
        }
        $this->_params['institution'] = $config['General']['institution'];
        $this->_params['onCampus'] = $config['General']['onCampus'];
        #$this->_params['indx'] = $config['General']['indx'];
        #$this->_params['bulkSize'] = $config['General']['bulkSize'];
        $this->_params['highlight'] = $config['General']['highlight'];
        $this->_params['lang'] = $config['General']['lang'];
        $this->_params['loc'] = $config['General']['loc'];

        $this->_params['db'] = isset($config['General']['db']) ? $config['General']['db'] : null;

        // Set up sort options
        $this->sortOptions = $config['Sorting'];
        // default sort for PCI is empty string
        $this->defaultSort = "";
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
                    = $translate ? translate($translationPrefix . $facet['value']) : $facet['value'];
                $currentSettings['untranslated'] = $facet['value'];
                $currentSettings['count'] = $facet['count'];
                $currentSettings['isApplied'] = false;
                $currentSettings['url']
                    = $this->renderLinkWithFilter("$field:".$facet['value']);
                // If we want to have expanding links (all values matching the
                // facet) in addition to limiting links (filter current search
                // with facet), do some extra work:
                if ($expandingLinks) {
                    $currentSettings['expandUrl']
                        = $this->getExpandingFacetLink($field, $facet['value']);
                }
                // Is this field a current filter?
                if (in_array($field, array_keys($this->filterList))) {
                    // and is this value a selected filter?
                    if (in_array($facet['value'], $this->filterList[$field])) {
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
        $startRec = ($this->page - 1) * $this->limit;

        $this->_indexResult = $this->executeSearch($this->buildURL($this->searchTerms, $startRec, $this->limit));
        
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

    public function buildURL($searchTerms, $startRec, $limit, $sort = false) 
    {
        $filterQuery = '';
        foreach ($this->getFilterList() as $filters) {
            foreach ($filters as $filter) {
                $field = $filter['field'];
                $value = $filter['value'];
                $filterQuery .= '&query=facet_' . urlencode($field) . ',exact,' . urlencode($value);
            }
        }
        $query = '';
                
        if ($this->searchType == 'PCIAdvanced') {
        	foreach ($searchTerms as $term) {        	
            	if ($query) {
            		$join = $term['join'];
         			$query .= '+' . $term['join'] . '+';
           		}
                    
            	$group = $term['group'];
            	foreach($group as $member) {
            		if ($member['field'] == 'AllFields') {
            			$query .= '&query=' . urlencode('any') . ',contains,' . urlencode($member['lookfor']);
            			}
            		else {
            			$query .= '&query=' . urlencode($member['field']) . ',contains,' . urlencode($member['lookfor']);
            		}           			
            	}
        	}	
        }
        else {
            $terms = explode(' ', trim($searchTerms[0]['lookfor']));
        	foreach ($terms as $term) {
            	$query .= '&query=' . urlencode($searchTerms[0]['index']) . ',contains,' . urlencode($term); 
        	}
        }
                
        $params = array("bulkSize" => $limit);
        $params += $this->_params;
        //if ($startRec > 0) {
           $params = array_merge($params, array("indx" => $startRec));
        //}
        $url = $this->_baseUrl . '?' . http_build_query($params) . $query;

        $url .= "&sortField=" . urlencode($this->sort);            

        if ($filterQuery) {
            $url .= $filterQuery;
        }
        return $url;
    }

    public function executeSearch($url) 
    {
        global $configArray;
        
        // Need to fake user_agent so that the service returns valid data
        ini_set("user_agent","Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0)");
        //print "$url<BR>";
        $xml = simplexml_load_file($url);
        $xml->registerXPathNamespace('prim', 'http://www.exlibrisgroup.com/xsd/primo/primo_nm_bib');
        $xml->registerXPathNamespace('sear', 'http://www.exlibrisgroup.com/xsd/jaguar/search');
        $ds = $xml->xpath('//sear:DOCSET');
        $hits = isset($ds[0]) ? (int)$ds[0]->attributes()->TOTALHITS : 0;
        foreach ($xml->xpath('//sear:DOC') as $item) {
            $ids = array();
            $item->registerXPathNamespace('prim', 'http://www.exlibrisgroup.com/xsd/primo/primo_nm_bib'); 
            foreach ($item->xpath('.//prim:search/prim:recordid') as $id) {
                $ids[] = $id;
            }    
            $authors = array();
            foreach ($item->xpath('.//prim:display/prim:creator') as $author) {
                foreach (explode(';', (string) $author) as $author) {
                    $authors[] = trim($author);
                }
            }    
            foreach ($item->xpath('.//prim:display/prim:contributor') as $author) {
                foreach (explode(';', (string) $author) as $author) {
                    $authors[] = trim($author);
                }
            }
            
            $publications = array();
            foreach ($item->xpath('.//prim:display/prim:ispartof') as $partof) {
                $publications[] = trim($partof);
            }

            $identifiers = array();
            foreach ($item->xpath('.//prim:display/prim:identifier') as $identifier) {
                $identifiers[] = trim($identifier);
            }
            
            $title = $item->xpath('.//prim:display/prim:title');
            $title = is_array($title) ? (string) $title[0] : NULL;
            // get url from Primo subfield
            $url = $item->xpath('.//prim:links/prim:backlink');
            $partsArr = array();
            if (isset($url[0])) {
                $partsArr = explode('http://', $url[0]);
            }
            if (count($partsArr) > 1) {
                $partsArr2 = explode('$$', $partsArr[1]);
                $url = 'http://' . $partsArr2[0];
            } else {
                $url = null;
            }
            
            $openurl = '';
            if (isset($configArray['OpenURL']['url']) && $configArray['OpenURL']['url']) {
                $openurl = $configArray['OpenURL']['url'] . "?";
                // Parse the OpenURL and extract parameters
                $link = $item->xpath('.//sear:LINKS/sear:openurl');
                if ($link) {
                    $params = explode('&', substr($link[0], strpos($link[0], '?') + 1));
                    $openurl .= 'rfr_id=' . urlencode($configArray['OpenURL']['rfr_id']);
                    foreach ($params as $param) {
                        if (substr($param, 0, 7) != 'rfr_id=') {
                            $openurl .= '&' . $param;
                        }
                    }
                }
            }

            $result = array('Author' => $authors, 'Title' => array($title), 'url' => $url, 
                'PublicationTitle' => $publications, 'ID' => $ids, 'identifiers' => $identifiers,
                'openUrl' => $openurl);
            $results[] = $result;
        }

        $facets = array();
        foreach ($xml->xpath('//sear:FACET') as $item) {
            $tag = (string) $item->attributes()->NAME;
            $values = array();
            foreach ($item->xpath('.//sear:FACET_VALUES') as $facetValue) {
                $key = (string) $facetValue->attributes()->KEY;
                $count = (string) $facetValue->attributes()->VALUE;
                $values[] = array('value'=> $key, 'count' => $count);
            }
            usort(&$values, array($this, 'compareFacetsByCount'));
            $facets[] = array('id' => $tag, 'tag' => $tag, 'values' => $values);

        }

        return array('recordCount' => $hits, 'response' => array('numFound' => $hits, 'start' => 0, 'docs' => $results), 'facetFields' => $facets);
    }

    public function compareFacetsByCount($a, $b) {
        return $b['count'] - $a['count'];
    }
    
    public function getRecord($id) {

        $params = $this->_params;
        $query = '&query=rid,exact,' . $id;
        $url = $this->_baseUrl . '?' . http_build_query($params) . $query;
        
        ini_set("user_agent","Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.0)");
        $xml = simplexml_load_file($url);
        $result = $this->xmlpp($xml->asXML(),true);

        return $result;    
    }      

    /** Prettifies an XML string into a human-readable and indented work of art 
    *  @param string $xml The XML as a string 
    *  @param boolean $html_output True if the output should be escaped (for use in HTML) 
    */  
    function xmlpp($xml, $html_output=false) {  
        $xml_obj = new SimpleXMLElement($xml);  
        $level = 4;  
        $indent = 0; // current indentation level  
        $pretty = array();  
      
        // get an array containing each XML element  
        $xml = explode("\n", preg_replace('/>\s*</', ">\n<", $xml_obj->asXML()));  
  
        // shift off opening XML tag if present  
        if (count($xml) && preg_match('/^<\?\s*xml/', $xml[0])) {  
            $pretty[] = array_shift($xml);  
        }  
  
        foreach ($xml as $el) {  
            if (preg_match('/^<([\w])+[^>\/]*>$/U', $el)) {  
                // opening tag, increase indent  
                $pretty[] = str_repeat(' ', $indent) . $el;  
                $indent += $level;  
            } else {  
                if (preg_match('/^<\/.+>$/', $el)) {              
                    $indent -= $level;  // closing tag, decrease indent  
                }  
                if ($indent < 0) {  
                    $indent += $level;  
                }  
                $pretty[] = str_repeat(' ', $indent) . $el;  
            }  
        }     
        $xml = implode("\n", $pretty);     
        return ($html_output) ? htmlentities($xml) : $xml;  
    }      
}


