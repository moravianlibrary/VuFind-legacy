<?php
require_once 'RecordDrivers/IndexRecord.php';
require_once 'RecordDrivers/MarcRecord.php';

class MzkRecord extends MarcRecord
{
    
    public function __construct($record)
    {
        parent::__construct($record);
        array_push($this->forbiddenSnippetFields, "relevancy");
    }

    public function getCoreMetadata()
    {
        global $interface;
        $result = parent::getCoreMetadata();
        $interface->assign('itemLink', $this->fields['itemlink']);
        $interface->assign('EOD', $this->getEOD());
        $interface->assign('callNumber', $this->getCallNumber());
        $interface->assign('physical', $this->getPhysicalDescriptions());
        $interface->assign('id', $this->getUniqueID());
        $this->addBibinfoForObalkyKnih();
        return $result;
    }

    public function getHoldings($patron = false)
    {
        global $interface;
        $result = parent::getHoldings($patron);
        $interface->assign('id', $this->getUniqueID());
        $itemLink = $this->fields['itemlink'];
        if ($this->getUniqueID() != $itemLink) {
            $interface->assign('itemLink', $itemLink);
            if (strpos($this->getUniqueID(), "MZK04-") === 0) {
                $interface->assign('itemLinkType', "norms");
            } else {
                $interface->assign('itemLinkType', "LKR");
            }
        }
        $interface->assign('holdingsRestrictions', $this->getRestrictions());
        return $result;
    }

    public function getSearchResult($view = 'list')
    {
        global $configArray;
        global $interface;

        $interface->assign('summId', $this->getUniqueID());
        $interface->assign('summFormats', $this->getFormats());
        $interface->assign('summHighlightedTitle', $this->getHighlightedTitle());
        $interface->assign('summTitle', $this->getTitle());
        $interface->assign('summHighlightedAuthor', $this->getHighlightedAuthor());
        $interface->assign('summAuthor', $this->getPrimaryAuthor());
        $interface->assign('summDate', $this->getPublicationDates());
        $interface->assign('summISBN', $this->getCleanISBN());
        $interface->assign('summThumb', $this->getThumbnail());
        $interface->assign('summThumbLarge', $this->getThumbnail('large'));
        $issn = $this->getCleanISSN();
        $interface->assign('summISSN', $issn);
        $interface->assign('summLCCN', $this->getLCCN());
        $interface->assign('summOCLC', $this->getOCLC());
        $interface->assign('summCallNo', $this->getCallNumber());
        // Begin of costumizations for MZK
        // Norms in MZK04
        if (strpos($this->getUniqueID(), "MZK04-") === 0) {
            $interface->assign('validity', $this->_getFirstFieldValue('520', array('a')));
            $interface->assign('summAjaxStatus', false);
        } else {
            $interface->assign('validity', null);
            $interface->assign('summAjaxStatus', true);
        }
        $interface->assign('itemLink', $this->fields['adm_id']);
        $statuses = $this->fields['statuses'];
        if ($statuses == null) {
           $statuses = array();
        }
        if (in_array("absent", $statuses)) {
            $interface->assign("status", "absent");
        } else if (in_array("present", $statuses)) {
            $interface->assign("status", "present");
        } else if (in_array("free-stack", $statuses)) {
            $interface->assign("status", "free-stack");
        } else {
            $interface->assign("status", "no items");
        }
        // End of costumizations for MZK
        // Obtain and assign snippet information:
        $snippet = $this->getHighlightedSnippet();
        $interface->assign(
            'summSnippetCaption', $snippet ? $snippet['caption'] : false
        );
        $interface->assign('summSnippet', $snippet ? $snippet['snippet'] : false);

        // Only display OpenURL link if the option is turned on and we have
        // an ISSN.  We may eventually want to make this rule more flexible,
        // but for now the ISSN restriction is designed to be consistent with
        // the way we display items on the search results list.
        $hasOpenURL = ($this->openURLActive('results') && $issn);
        //$openURL = $this->getOpenURL();
        $interface->assign('summOpenUrl', $hasOpenURL ? $openURL : false);

        // Always provide an OpenURL for COinS purposes:
        $interface->assign('summCOinS', $openURL);
        $interface->assign('summURLs', $this->getURLsFromSolr()); // modifications for MZK

        // By default, do not display AJAX status; we won't assume that all
        // records exist in the ILS.  Child classes can override this setting
        // to turn on AJAX as needed:
        $this->addBibinfoForObalkyKnih();
        // Send back the template to display:
        return 'RecordDrivers/Index/result-' . $view . '.tpl';
    }
    
    public function getExtendedMetadata()
    {
        $result = parent::getExtendedMetadata();
        return 'RecordDrivers/Mzk/extended.tpl';
    }

    protected function getURLsFromSolr()
    {
        $urls = array();
        if (isset($this->fields['url']) && is_array($this->fields['url'])) {
            foreach ($this->fields['url'] as $url) {
                // The index doesn't contain descriptions for URLs, so we'll just
                // use the URL itself as the description.
                $urls[$url] = $url;
            }
        }
        return $urls;
    }

    protected function getURLs()
    {
        $result = array_merge($this->getURLsBySpec('856', 'u',  array('y', '3')), $this->getURLsBySpec('996', 'u', array('y')));
        return $result;
    }

    protected function getURLsBySpec($field, $addr_subfield, $desc_subfields)
    {
        $retVal = array();
        $urls = $this->marcRecord->getFields($field);
        if ($urls) {
            foreach ($urls as $url) {
                // Is there an address in the current field?
                $address = $url->getSubfield($addr_subfield);
                if ($address) {
                    $address = $address->getData();
                    // Is there a description?  If not, just use the URL itself.
                    $desc = null;
                    foreach ($desc_subfields as $desc_subfield) {
                        $desc = $url->getSubfield($desc_subfield);
                        if ($desc) break;
                    }
                    if ($desc) {
                        $desc = $desc->getData();
                    } else {
                        $desc = $address;
                    }
                    if (strpos($desc, 'index obsahu dokumentu') === FALSE) {
                        $retVal[$address] = $desc;
                    }
                }
            }
        }
        return $retVal;
    }

    protected function getEOD()
    {
        $eod = $this->_getFirstFieldValue('EOD', array('a'));
        return ($eod == 'Y')?true:false;
    }
    
    protected function getCNB()
    {
        return isset($this->fields['nbn']) ? $this->fields['nbn'] : NULL;
    }
    
    protected function getRestrictions()
    {
        $result = $this->_getFieldArray('540', array('a'));
        if (in_array("NewspaperOrJournal", $this->getFormats())) {
            $result[] = translate("NewspaperOrJournal notice");
        }
        return $result;
    }

    protected function getTitle()
    {
        return isset($this->fields['title_display']) ?
            $this->fields['title_display'] : '';
    }

    protected function getPublicationDates()
    {
        return isset($this->fields['publishDate_display']) ?
            $this->fields['publishDate_display'] : array();
    }

    public function getEditions()
    {
        return null;
    }
    
    public function getExportFormats()
    {
        $result = parent::getExportFormats();
        $result[] = 'PrintShort';
        $result[] = 'PrintFull';
        return $result;
    }
    
    public function getExport($format)
    {
        global $interface;
        
        switch(strtolower($format)) {
            case 'printshort':
                $this->getCoreMetadata();
                $interface->assign("full", false);
                $locations = array();
                $holdings = $this->marcRecord->getFields('Z30');
                if ($holdings) {
                    foreach ($holdings as $holding) {
                        $location = $holding->getSubfield('9');
                        if ($location) {
                            $locations[$location->getData()] = true;
                        }
                    }                    
                }
                $locations = array_keys($locations);
                $interface->assign("coreLocations", $locations);
                return 'RecordDrivers/Mzk/export-print.tpl';
            case 'printfull':
                $this->getCoreMetadata();
                $interface->assign("full", true);
                return 'RecordDrivers/Mzk/export-print.tpl';
            default:
                return parent::getExport($format);
        }
    }

    protected function getCallNumber()
    {
        return isset($this->fields['callnumber']) ? $this->fields['callnumber'] : '';
    }
    
    protected function addBibinfoForObalkyKnih()
    {
        global $configArray, $interface;
        $bibinfo = array(
            "authors" => array($this->getPrimaryAuthor()),
            "title" => $this->getTitle(),
        );
        $isbn = $this->getCleanISBN();
        if (!empty($isbn)) {
            $bibinfo['isbn'] = $isbn;
        }
        $year = $this->getPublicationDates();
        if (!empty($year)) {
            $bibinfo['year'] = $year[0];
        }
        $cnb = $this->getCNB();
        if (isset($cnb)) {
            $bibinfo['nbn'] = $cnb;
        } else {
            $prefix = 'BOA001';
            $bibinfo['nbn'] = $prefix . '-' . str_replace('-', '', $this->getUniqueID());
        }
        $permalink = $configArray['Site']['url'] . '/Record/' . urlencode($this->getUniqueID());
        $interface->assign('obalkyknih_permalink', $permalink);
        $interface->assign('obalkyknih_bibinfo', json_encode($bibinfo));
    }

}
