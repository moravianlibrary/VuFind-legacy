<?php
require_once 'RecordDrivers/IndexRecord.php';
require_once 'RecordDrivers/MarcRecord.php';

class MzkRecord extends MarcRecord
{

    public function __construct($record)
    {
        parent::__construct($record);
    }

    public function getCoreMetadata()
    {
        global $interface;
        $result = parent::getCoreMetadata();
        $interface->assign('itemLink', $this->fields['itemlink']);
        $interface->assign('EOD', $this->getEOD());
        $interface->assign('callNumber', $this->getCallNumber());
        $interface->assign('physical', $this->getPhysicalDescriptions());
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
        return $result;
    }

    public function getSearchResult($view = 'list')
    {
        global $configArray;
        global $interface;

        $interface->assign('summId', $this->getUniqueID());
        $interface->assign('summFormats', $this->getFormats());
        //$interface->assign('summHighlightedTitle', $this->getHighlightedTitle());
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
        }
        $interface->assign('itemLink', $this->fields['itemlink']);
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
        $interface->assign('summAjaxStatus', true);
        // Send back the template to display:
        return 'RecordDrivers/Index/result-' . $view . '.tpl';
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
                    if ($desc != 'index obsahu dokumentu') {
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

    protected function getCallNumber()
    {
        return isset($this->fields['callnumber']) ? $this->fields['callnumber'] : '';
    }

}
