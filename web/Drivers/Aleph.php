<?php
/**
 * Aleph ILS driver
 *
 * PHP version 5
 *
 * Copyright (C) UB/FU Berlin
 *
 * last update: 7.11.2007
 * tested with X-Server Aleph 18.1.
 *
 * TODO: login, course information, getNewItems, duedate in holdings, https connection to x-server, ...
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty ofF
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * @category VuFind
 * @package  ILS_Drivers
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_an_ils_driver Wiki
 */
require_once 'Interface.php';
require_once 'sys/Proxy_Request.php';
require_once 'sys/VuFindCache.php';

class AlephTranslator
{

    function __construct()
    {
        $configArray = parse_ini_file('conf/Aleph.ini', true);
        $this->charset = $configArray['util']['charset'];
        $this->table15 = $this->parsetable($configArray['util']['tab15'], "AlephTranslator::tab15_callback");
        $this->table40 = $this->parsetable($configArray['util']['tab40'], "AlephTranslator::tab40_callback");
        $this->table_sub_library = $this->parsetable($configArray['util']['tab_sub_library'], "AlephTranslator::tab_sub_library_callback");
    }

    function parsetable($file, $callback) {
        $result = array();
        $file_handle = fopen($file, "r, ccs=UTF-8");
        $rgxp = "";
        while (!feof($file_handle) ) {
            $line = fgets($file_handle);
            $line = chop($line);
            if (preg_match("/!!/", $line)) {
                $line = chop($line);
                $rgxp = AlephTranslator::regexp($line);
            } if (preg_match("/!.*/", $line) || $rgxp == "" || $line == "") {
            } else {
                $line = str_pad($line, 80);
                $matches = "";
                if (preg_match($rgxp, $line, $matches)) {
                    call_user_func($callback, $matches, &$result, $this->charset);
                }
            }
        }
        fclose($file_handle);
        return $result;
    }

    function tab40_translate($collection, $sublib) {
        $findme = $collection . "|" . $sublib;
        $desc = $this->table40[$findme];
        if ($desc == NULL) {
            $findme = $collection . "|";
            $desc = $table40[$findme];
        }
        return $desc;
    }

    function tab_sub_library_translate($sl) {
        return $this->table_sub_library[$sl];
    }

    function tab15_translate($slc, $isc, $ipsc) {
        $tab15 = $this->tab_sub_library_translate($slc);
        if ($tab15 == NULL) {
            print "tab15 is null!<br>";
        }
        $findme = $tab15["tab15"] . "|" . $isc . "|" . $ipsc;
        $result = $this->table15[$findme];
        if ($result == NULL) {
            $findme = $tab15["tab15"] . "||" . $ipsc;
            $result = $this->table15[$findme];
        }
        $result["sub_lib_desc"] = $tab15["desc"];
        return $result;
    }

    static function tab15_callback($matches, $tab15, $charset) {
        $lib = $matches[1];
        $no1 = $matches[2];
        if ($no1 == "##") $no1="";
        $no2 = $matches[3];
        if ($no2 == "##") $no2="";
        $desc = iconv($charset, 'UTF-8', $matches[5]);
        $loan = $matches[6];
        $request = $matches[8];
        $opac = $matches[10];
        $key = trim($lib) . "|" . trim($no1) . "|" . trim($no2);
        $tab15[trim($key)] = array( "desc" => trim($desc), "loan" => $matches[6], "request" => $matches[8], "opac" => $matches[10] );
    }

    static function tab40_callback($matches, $tab40, $charset) {
        $code = trim($matches[1]);
        $sub = trim($matches[2]);
        $sub = trim(preg_replace("/#/", "", $sub));
        $desc = trim(iconv($charset, 'UTF-8', $matches[4]));
        $key = $code . "|" . $sub;
        $tab40[trim($key)] = array( "desc" => $desc );
    }

    static function tab_sub_library_callback($matches, $tab_sub_library, $charset) {
        $sublib = trim($matches[1]);
        $desc = trim(iconv($charset, 'UTF-8', $matches[5]));
        $tab = trim($matches[6]);
        $tab_sub_library[$sublib] = array( "desc" => $desc, "tab15" => $tab );
    }

    static function regexp($string) {
        $string = preg_replace("/\\-/", ")\\s(", $string);
        $string = preg_replace("/!/", ".", $string);
        $string = preg_replace("/[<>]/", "", $string);
        $string = "/(" . $string . ")/";
        return $string;
    }
    
}

/**
 * Aleph Exception
 *
 */
class AlephException extends Exception
{

    public function __construct($message, $code = 0) {
        parent::__construct($message, $code);
        $this->xml = null;
    }

    public function setXML($xml) {
        $this->xml = $xml;
    }

    public function getXML() {
        return $this->xml;
    }
}

/**
 * Aleph ILS driver
 *
 * @category VuFind
 * @package  ILS_Drivers
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_an_ils_driver Wiki
 */
class Aleph implements DriverInterface
{

    /**
     * Constructor
     *
     * @access public
     */
    function __construct()
    {
        // Load Configuration for this Module
        $configArray = parse_ini_file('conf/Aleph.ini', true);
        $this->host = $configArray['Catalog']['host'];
        $this->bib = split(',', $configArray['Catalog']['bib']);
        $this->useradm = $configArray['Catalog']['useradm'];
        $this->admlib = $configArray['Catalog']['admlib'];
        if (isset($configArray['Catalog']['wwwuser']) && isset($configArray['Catalog']['wwwpasswd'])) {
            $this->wwwuser = $configArray['Catalog']['wwwuser'];
            $this->wwwpasswd = $configArray['Catalog']['wwwpasswd'];
            $this->xserver_enabled = true;
        } else {
            $this->xserver_enabled = false;
        }
        $this->dlfport = $configArray['Catalog']['dlfport'];
        $this->sublibadm = $configArray['sublibadm'];
        if (isset($configArray['duedates'])) {
            $this->duedates = $configArray['duedates'];
        }
        $this->disable_ils_auth = false;
        if (isset($configArray['Catalog']['disable_ils_auth'])) {
            $this->disable_ils_auth = $configArray['Catalog']['disable_ils_auth'];
        }
        $this->available_statuses = split(',', $configArray['Catalog']['available_statuses']);
        $this->quick_availability = $configArray['Catalog']['quick_availability'];
        $this->debug_enabled = $configArray['Catalog']['debug'];
        if (isset($configArray['Catalog']['default_patron'])) {
            $this->default_patron = $configArray['Catalog']['default_patron'];
        } else {
            $this->default_patron = null;
        }
        if (isset($configArray['util']['tab40']) && isset($configArray['util']['tab15']) && isset($configArray['util']['tab_sub_library'])) {
            if (isset($configArray['Cache']['type'])) {
                $cache = new VuFindCache($configArray['Cache']['type'], 'aleph');
                $this->translator = $cache->load('translator');
                if ($this->translator == false) {
                    $this->translator = new AlephTranslator();
                    $cache->save($this->translator, 'translator');
                }
            } else {
                $this->translator = new AlephTranslator();
            }
        } else {
            $this->translator = false;
        }
        $this->ill_hidden_statuses = explode(',', $configArray['ILL']['hidden_statuses']);
        $this->statuses_limit = 50;
        if (isset($configArray['Catalog']['statuses_limit'])) {
            $this->statuses_limit = $configArray['Catalog']['statuses_limit'];
        }
        $this->favourites_url = null;
        if (isset($configArray['Catalog']['fav_cgi_url'])) {
            $this->favourites_url = $configArray['Catalog']['fav_cgi_url'];
        }
        $this->user_cgi_url = null;
        if (isset($configArray['Catalog']['user_cgi_url'])) {
            $this->user_cgi_url = $configArray['Catalog']['user_cgi_url'];
        }
        $this->history_limit = 50;
        if (isset($configArray['History']['limit'])) {
            $this->history_limit = $configArray['History']['limit'];
        }
    }

    protected function doXRequest($op, $params, $auth=false)
    {
        $url = "http://$this->host/X?op=$op";
        $url = $this->appendQueryString($url, $params);
        if ($auth) {
           $url = $this->appendQueryString($url, array('user_name' => $this->wwwuser, 'user_password' => $this->wwwpasswd));
        }
        if ($post) {
            $result = $this->doHTTPRequest($url, 'POST', $post);
        } else {
           $result = $this->doHTTPRequest($url);
        }
        if ($result->error) {
           if ($this->debug_enabled) {
               $this->debug("XServer error, URL is $url, error message: $result->error.");
           }
           throw new Exception("XServer error: $result->error.");
        }
        return $result;
    }
    
    protected function doXRequestUsingPost($op, $params, $auth=true)
    {
        $url = "http://$this->host/X?";
        $body = '';
        $sep = '';
        $params['op'] = $op;
        if ($auth) {
            $params['user_name'] = $this->wwwuser;
            $params['user_password'] = $this->wwwpasswd;
        }
        foreach ($params as $key => $value) {
            $body .= $sep . $key . '=' . urlencode($value);
            $sep = '&';
        }
        $result = $this->doHTTPRequest($url, 'POST', $body);
        if ($result->error) {
            if ($op == 'update-doc' && preg_match('/Document: [0-9]+ was updated successfully\\./', trim($result->error)) === 1) {
                return $result;
            }
            if ($this->debug_enabled) {
                $this->debug("XServer error, URL is $url, error message: $result->error.");
            }
            throw new Exception("XServer error: $result->error.");
        }
        return $result;
    }

    protected function doPDSRequest($op, $params, $auth=false)
    {
        $url = "http://$this->host/pds?func=$op";
        $url = $this->appendQueryString($url, $params);
        $result = $this->doHTTPRequest($url);
        if ($result->error) {
           if ($this->debug_enabled) {
               $this->debug("PDS error, URL is $url, error message: $result->error.");
           }
           throw new Exception("PDS error: $result->error.");
        }
        return $result;
    }

    protected function doRestDLFRequest($path_elements, $params = null, $method='GET', $body = null) 
    {
        $path = '';
        foreach ($path_elements as $path_element) {
           $path .= urlencode($path_element) . "/";
        }
        $url = "http://$this->host:$this->dlfport/rest-dlf/" . $path;
        $url = $this->appendQueryString($url, $params);
        $result = $this->doHTTPRequest($url, $method, $body);
        $replyCode = (string) $result->{'reply-code'};
        if ($replyCode != "0000") {
           $replyText = $result->{'reply-text'};
           $note = $result->xpath("//note[@type='error']/text()");
           if(!empty($note)) {
               $replyText = (string) $note[0];
           }
           $this->handleError($replyText, $url, $replyCode, $result);
        }
        return $result;
    }

    protected function handleError($errorMessage, $url, $replyCode, $xml)
    {
        if ($this->debug_enabled) {
           $this->debug($errorMessage);
        }
        $ex = new AlephException($errorMessage);
        $ex->setXML($xml);
        throw $ex;
    }

    protected function appendQueryString($url, $params)
    {
        $sep = (strpos($url, "?") === false)?'?':'&';
        if ($params != null) {
           foreach ($params as $key => $value) {
              $url.= $sep . $key . "=" . urlencode($value);
              $sep = "&";
           }
        }
        return $url;
    }

    protected function doHTTPRequest($url, $method='GET', $body = null)
    {
        if ($this->debug_enabled) {
            $this->debug("URL: '$url'");
        }
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if ($body != null) {
           curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        }
        $answer = curl_exec($ch);
        if (!$answer) {
           $error = curl_error($ch);
           $message = "HTTP request failed with message: $error, URL: '$url'.";
           if ($this->debug_enabled) {
               $this->debug($message);
           }
           throw new Exception($message);
        }
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($http_code != 200) { // Do not throw exeption, continue
           $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
           if ($this->debug_enabled) {
               $this->debug("Request failed with http code: $http_code, URL: '$url'");
           }
        }
        curl_close($ch);
        $answer = str_replace('xmlns=', 'ns=', $answer);
        $result = @simplexml_load_string($answer);
        if (!$result) {
           if ($this->debug_enabled) {
               $this->debug("XML is not valid, URL: '$url'");
           }
           throw new Exception("XML is not valid, URL: '$url'.");
        }
        return $result;
    }

    protected function debug($msg) 
    {
        print($msg . "<BR>");
    }


    protected function parseId($id) 
    {
        if (count($this->bib)==1) {
            return array($this->bib[0], $id);
        } else {
            return split('-', $id);
        }
    }

    /**
     * Get Status
     *
     * This is responsible for retrieving the status information of a certain
     * record.
     *
     * @param string $id The record id to retrieve the holdings for
     *
     * @return mixed     On success, an associative array with the following keys:
     * id, availability (boolean), status, location, reserve, callnumber; on
     * failure, a PEAR_Error.
     * @access public
     */
    public function getStatus($id)
    {
        $statuses = $this->getHolding($id);
        foreach($statuses as &$status) {
            $status['status'] = ($status['availability'] == 1) ? 'available' : 'unavailable';
        }
        return $statuses;
    }

    public function getStatusesX($bib, $ids)
    {
        $doc_nums = "";
        $sep = "";
        foreach($ids as $id) {
           $doc_nums .= $sep . $id;
           $sep = ",";
        }
        $xml = $this->doXRequest("publish_avail", array('library' => $bib, 'doc_num' => $doc_nums), false);
        $holding = array();
        foreach ($xml->xpath('/publish-avail/OAI-PMH') as $rec) {
           $identifier = $rec->xpath(".//identifier/text()");
           $id = "$bib" . "-" . substr($identifier[0], strrpos($identifier[0], ':') + 1);
           $temp = array();
           foreach ($rec->xpath(".//datafield[@tag='AVA']") as $datafield) {
              $status = $datafield->xpath('./subfield[@code="e"]/text()');
              $location = $datafield->xpath('./subfield[@code="a"]/text()');
              $signature = $datafield->xpath('./subfield[@code="d"]/text()');
              $availability = ($status[0] == 'available' || $status[0] == 'check_holdings');
              $reserve = true;
              $callnumber = $signature;
              $temp[] = array('id' => $id,
                              'availability' => $availability,
                              'status' => (string) $status[0],
                              'location' => (string) $location[0],
                              'signature' => (string) $signature[0],
                              'reserve' => $reserve,
                              'callnumber' => (string) $signature[0]
                           );
           }
           $holding[] = $temp;
        }
        return $holding;
    }

    /**
     * Get Statuses
     *
     * This is responsible for retrieving the status information for a
     * collection of records.
     *
     * @param array $idList The array of record ids to retrieve the status for
     *
     * @return mixed        An array of getStatus() return values on success,
     * a PEAR_Error object otherwise.
     * @access public
     */
    public function getStatuses($idList)
    {   
        if (!$this->xserver_enabled) {
            if (!$this->quick_availability) {
                return array();
            }
            $result = array();
            foreach ($idList as $id) {
                $items = $this->getStatus($id);
                $result[] = $items;
            }
            return $result;
        }
        $ids = array();
        $holdings = array();
        foreach ($idList as $id) {
            list($bib, $sys_no) = $this->parseId($id);
            $ids[$bib][] = $sys_no;
        }
        foreach ($ids as $key => $values) {
            if (in_array($key, $this->bib)) {
                $chunks = array_chunk($values, $this->statuses_limit);
                foreach ($chunks as $chunk) {
                    $holds = $this->getStatusesX($key, $chunk);
                    foreach ($holds as $hold) {
                        $holdings[] = $hold;
                    }
                }
            }
        }
        return $holdings;
    }

    /**
     * Get Holding
     *
     * This is responsible for retrieving the holding information of a certain
     * record.
     *
     * @param string $id     The record id to retrieve the holdings for
     * @param array  $patron Patron data
     *
     * @return mixed     On success, an associative array with the following keys:
     * id, availability (boolean), status, location, reserve, callnumber, duedate,
     * number, barcode; on failure, a PEAR_Error.
     * @access public
     */
    public function getHolding($id, $patron = false)
    {
        $bibId = $id;
        $params = array('view' => 'full');
        if (is_array($id)) {
            $bibId = $id['id'];
            if (isset($id['year'])) {
                $params['year'] = $id['year']; 
            }
            if (isset($id['volume'])) {
                $params['volume'] = $id['volume'];
            }
            if (isset($id['hide_loans']) && $id['hide_loans']) {
                $params['loaned'] = 'NO';
            }
        }
        $holding = array();
        list($bib, $sys_no) = $this->parseId($bibId);
        $resource = $bib . $sys_no;
        if ($patron) {
            $params['patron'] = $patron['id'];
        } else {
            $params['patron'] = $this->default_patron;
            $patron = $this->default_patron;
        }
        try {
            $xml = $this->doRestDLFRequest(array('record', $resource, 'items'), $params);
        } catch (Exception $ex) {
            return array();
        }
        foreach ($xml->xpath('//items/item') as $item) {
           $item_id = $item->xpath('@href');
           $item_id = substr($item_id[0], strrpos($item_id[0], '/') + 1);
           $item_status = $item->xpath('z30-item-status-code/text()'); // $isc
           $item_process_status = $item->xpath('z30-item-process-status-code/text()'); // $ipsc
           $sub_library = $item->xpath('z30-sub-library-code/text()'); // $slc
           if ($this->translator) {
              $item_status = $this->translator->tab15_translate((string) $sub_library[0], (string) $item_status[0], (string) $item_process_status[0]);
           } else {
              $z30_item_status = $item->xpath('z30/z30-item-status/text()');
              $z30_sub_library = $item->xpath('z30/z30-sub-library/text()');
              $item_status = array('opac' => 'Y', 'request' => 'C', 'desc' => $z30_item_status[0], 'sub_lib_desc' => $z30_sub_library[0]);
           }
           if ($item_status['opac'] != 'Y') {
                 continue;
           }
           $group = $item->xpath('@href');
           $group = substr(strrchr($group[0], "/"), 1);
           $status = $item->xpath('status/text()');
           $availability = false;
           $location = $item->xpath('z30/z30-sub-library-code/text()');
           $reserve = ($item_status['request'] == 'C')?'N':'Y';
           $callnumber = $item->xpath('z30/z30-call-no/text()');
           $barcode = $item->xpath('z30/z30-barcode/text()');
           $number = $item->xpath('z30/z30-inventory-number/text()');
           $collection = $item->xpath('z30/z30-collection/text()');
           $collection_code = $item->xpath('z30-collection-code/text()');
           if ($this->translator) {
              $collection_desc = $this->translator->tab40_translate((string) $collection_code[0], (string) $location[0]);
           } else {
              $z30_collection = $item->xpath('z30/z30-collection/text()');
              if (isset($z30_collection[0])) {
                 $collection_desc = array('desc' => $z30_collection[0]);
              }
           }
           $sig1 = $item->xpath('z30/z30-call-no/text()');
           $sig2 = $item->xpath('z30/z30-call-no-2/text()');
           $desc = $item->xpath('z30/z30-description/text()');
           $note = $item->xpath('z30/z30-note-opac/text()'); 
           $no_of_loans = $item->xpath('z30/z30-no-loans/text()');
           $requested = false;
           $status = $status[0];
           $duedate = null;
           $duedate_status = $status;
           if (in_array($status, $this->available_statuses)) {
               $availability = true;
           }
           if ($item_status['request'] == 'Y' && $availability == false) {
              $reserve = 'N';
           }
           if ($patron) {
              $hold_request = $item->xpath('info[@type="HoldRequest"]/@allowed');
              if ($hold_request[0] == 'N') {
                  $hold_request = $item->xpath('info[@type="ShortLoan"]/@allowed');
              }
              $reserve = ($hold_request[0] == 'Y')?'N':'Y';
           }
           $matches = array();
           if (preg_match("/([0-9]*\\/[a-zA-Z]*\\/[0-9]*);([a-zA-Z ]*)/", $status, &$matches)) {
               $duedate = $this->parseDate($matches[1]);
               $requested = (trim($matches[2]) == "Requested");
           } else if (preg_match("/([0-9]*\\/[a-zA-Z]*\\/[0-9]*)/", $status, &$matches)) {
               $duedate = $this->parseDate($matches[1]);
           } else {
               $duedate = null;
           }
           $holding[] = array('id' => $bibId,
                              'item_id' => $item_id,
                              'availability' => $availability, // was true
                              'status' => (string) $item_status['desc'],
                              'location' => (string) $location[0],
                              //'reserve' => $reserve, // was 'reserve' => 'N'
                              'callnumber' => isset($callnumber[0])?(string) $callnumber[0]:null,
                              'duedate' => (string) $duedate,
                              'number' => isset($number[0])?((string) $number[0]):null,
                              'collection' => isset($collection[0])?(string) $collection[0]:null,
                              'collection_desc' => isset($collection_desc['desc'])?(string) $collection_desc['desc']:null,
                              'barcode' => (string) $barcode[0],
                              'description' => isset($desc[0])?((string) $desc[0]):null,
                              'note' => isset($note[0])?((string) $note[0]):null,
                              'is_holdable' => ($reserve == 'N')?true:false,
                              'holdtype' => 'hold',
                              // below are optional attributes
                              'sig1' => isset($sig1[0])?((string) $this->unescapeXMLText($sig1[0])):null,
                              'sig2' => isset($sig2[0])?((string) $this->unescapeXMLText($sig2[0])):null,
                              'sub_lib_desc' => (string) $item_status['sub_lib_desc'],
                              'no_of_loans' => (integer) $no_of_loans[0],
                              'duedate_status' => (string) $status,
                              'requested' => (string) $requested);
        }
        return $holding;
    }

    public function getHoldingFilter($bibId) {
        list($bib, $sys_no) = $this->parseId($bibId);
        $resource = $bib . $sys_no;
        $years = array();
        $volumes = array();
        try {
            $xml = $this->doRestDLFRequest(array('record', $resource, 'filters'));
        } catch (Exception $ex) {
            return array();
        }
        if (isset($xml->{'record-filters'})) {
            if (isset($xml->{'record-filters'}->{'years'})) {
                foreach ($xml->{'record-filters'}->{'years'}->{'year'} as $year) {
                    $years[] = $year;
                }
            }
            if (isset($xml->{'record-filters'}->{'volumes'})) {
                foreach ($xml->{'record-filters'}->{'volumes'}->{'volume'} as $volume) {
                    $volumes[] = $volume;
                }
            }
        }
        return array('years' => $years, 'volumes' => $volumes);
    }

    public function unescapeXMLText($text) {
        $text = str_replace(array('&#38;', '&apos;'), array('&', "'"), $text);
        return html_entity_decode($text);
        
    }

    public function getMyHistory($user, $limit = 0)
    {
        return $this->getMyTransactions($user, true, $limit);
    }

    /**
     * Get Patron Transactions
     *
     * This is responsible for retrieving all transactions (i.e. checked out items)
     * by a specific patron.
     *
     * @param array $user The patron array from patronLogin
     *
     * @return mixed      Array of the patron's transactions on success,
     * PEAR_Error otherwise.
     * @access public
     */
    public function getMyTransactions($user, $history=false, $limit = 0)
    {
        $userId = $user['id'];
        $transList = array();
        $params = array("view" => "full");
        if ($history) {
           $params["type"] = "history";
           if ($limit > 0) {
              $params["no_loans"] = $limit;
           }
        }
        $count = 0;
        $unreturned_books = 0;
        $xml = $this->doRestDLFRequest(array('patron', $userId, 'circulationActions', 'loans'), $params);
        foreach ($xml->xpath('//loan') as $item) {
           $z36 = $item->z36;
           $z13 = $item->z13;
           $z30 = $item->z30;
           $group = $item->xpath('@href');
           $group = substr(strrchr($group[0], "/"), 1);
           $renew = $item->xpath('@renew');
           $docno = (string) $z36->{'z36-doc-number'};
           $itemseq = (string) $z36->{'z36-item-sequence'};
           $seq = (string) $z36->{'z36-sequence'};
           $location = (string) $z36->{'z36_pickup_location'};
           $reqnum = (string) $z36->{'z36-doc-number'} .
              (string) $z36->{'z36-item-sequence'} . (string) $z36->{'z36-sequence'};
           $due = $returned = null;
           if ($history) {
               $due = $item->z36h->{'z36h-due-date'};
               $returned = $item->z36h->{'z36h-returned-date'};
           } else {
               $due = (string) $z36->{'z36-due-date'};
           }
           $loaned = (string) $z36->{'z36-loan-date'};
           $title = (string) $z13->{'z13-title'};
           $author = (string) $z13->{'z13-author'};
           $isbn = (string) $z13->{'z13-isbn-issn'};
           $barcode = (string) $z30->{'z30-barcode'};
           /* Check if item is loaned after due date */
           $current_date = strtotime(date('d.m.Y'));
           $due_date = strtotime($this->parseDate($due));
           $return_in_days = ($due_date - $current_date) / (60*60*24);
           $renewable = ($renew[0] == 'Y');
           $no_of_renewals = (string) $z36->{'z36-no-renewal'};
           $fine = (string) $item->{'fine'};
           $transListItem = array('type' => $type,
                               'id' => ($history)?null:$this->barcodeToID($barcode),
                               'item_id' => $group,
                               'location' => $location,
                               'title' => $title,
                               'author' => $author,
                               'isbn' => array($isbn),
                               'reqnum' => $reqnum,
                               'barcode' => $barcode,
                               'duedate' => $this->parseDate($due),
                               'no_of_renewals' => $no_of_renewals,
                               'returned' => $this->parseDate($returned),
                               'holddate' => $holddate,
                               'delete' => $delete,
                               'renewable' => $renewable,
                               'fine' => $fine,
                               'create' => $this->parseDate($create));
           if ($return_in_days < 0 && !$history) {
               $transListItem['message'] = "Unreturned book after due date, renewing is disabled. You can't renew items until you return this book.";
               $unreturned_books++;
           }
           $transList[] = $transListItem;
        }
        if ($history) {
           usort($transList, "Aleph::compareByReturnedDate");
        }
        if ($unreturned_books > 0) {
           foreach ($transList as &$transListItem) {
               $transListItem['renewable'] = false;
           }
        }
        return $transList;
    }
    
    public static function compareByReturnedDate($a, $b) {
        $a  = date("U", strtotime($a['returned']));
        $b = date("U", strtotime($b['returned']));
        return ($b - $a);
    }

    public function getRenewDetails($details)
    {
        return $details['item_id'];
    }

    public function renewMyItems($details)
    {
        $patron = $details['patron'];
        $result = array();
        foreach ($details['details'] as $id) {
           try {
               $xml = $this->doRestDLFRequest(array('patron', $patron['id'], 'circulationActions', 'loans', $id), null, 'POST', null);
               $result[$id] = array("sysMessage" => "", "success" => true);
           } catch (Exception $ex) {
               $message = $ex->getMessage();
               $xml = $ex->getXML();
               if ($xml != null) {
                   $message = (string) $xml->{"renewals"}->{"institution"}->{"loan"}->{"status"};
               }
               $result[$id] = array("sysMessage" => trim($message), "success" => false);
           }
        }
        return array('blocks' => false, 'details' => $result);
    }

    /**
     * Get Patron Holds
     *
     * This is responsible for retrieving all holds by a specific patron.
     *
     * @param array $user The patron array from patronLogin
     *
     * @return mixed      Array of the patron's holds on success, PEAR_Error
     * otherwise.
     * @access public
     */
    public function getMyHolds($user, $history=false)
    {
        $userId = $user['id'];
        $holdList = array();
        $params = array("view" => "full");
        if ($history) {
           $params["type"] = "history";
        }
        $xml = $this->doRestDLFRequest(array('patron', $userId, 'circulationActions', 'requests', 'holds'), $params);
        foreach ($xml->xpath('//hold-request') as $item) {
           $z37 = $item->z37;
           $z13 = $item->z13;
           $z30 = $item->z30;
           $delete = $item->xpath('@delete');
           $href = $item->xpath('@href');
           $item_id = substr($href[0], strrpos($href[0], '/') + 1);
           if ((string) $z37->{'z37-request-type'} == "Hold Request" || true) {
                $type = "hold";
                $docno = (string) $z37->{'z37-doc-number'};
                $itemseq = (string) $z37->{'z37-item-sequence'};
                $seq = 0;
                $item_status = preg_replace("/\s[\s]+/", " ", (string) $item->{'status'});
                if (preg_match("/Waiting in position ([0-9]+) in queue; current due date ([0-9]+\/[a-z|A-Z]+\/[0-9])+/", $item_status, $matches)) {
                     $seq = $matches[1];
                }
                $location = (string) $z37->{'z37-pickup-location'};
                $reqnum = (string) $z37->{'z37-doc-number'} .
                    (string) $z37->{'z37-item-sequence'} . (string) $z37->{'z37-sequence'};
                $expire = (string) $z37->{'z37-end-request-date'};
                $create = (string) $z37->{'z37-open-date'};
                $holddate = (string) $z37->{'z37-hold-date'};
                $holddate = ($holddate == "00000000") ? null : $this->parseDate($holddate); 
                $title = (string) $z13->{'z13-title'};
                $author = (string) $z13->{'z13-author'};
                $isbn = (string) $z13->{'z13-isbn-issn'};
                $barcode = (string) $z30->{'z30-barcode'};
                $description = (string) $z30->{'z30-description'};
                $status = (string) $z37->{'z37-status'};
                $on_hold_until = (string) $z37->{'z37-end-hold-date'};
                $on_hold_until = ($on_hold_until == "00000000") ? null : $this->parseDate($on_hold_until);
                $delete = ($delete[0] == "Y");
                $holdList[] = array('type' => $type,
                                    'item_id' => $item_id,
                                    'location' => $location,
                                    'title' => $title,
                                    'author' => $author,
                                    'isbn' => array($isbn),
                                    'reqnum' => $reqnum,
                                    'barcode' => $barcode,
                                    'id' => $this->barcodeToID($barcode), 
                                    'expire' => $this->parseDate($expire),
                                    'holddate' => $holddate,
                                    'on_hold_until' => $on_hold_until,
                                    'delete' => $delete,
                                    'create' => $this->parseDate($create),
                                    'status' => $status,
                                    'description' => $description,
                                    'position' => ltrim($seq, '0'));
           }
        }
        return $holdList;
    }

    public function getMyHoldsHistory($user) {
        return $this->getMyHolds($user, true);
    }

    public function getCancelHoldDetails($holdDetails)
    {
        if ($holdDetails['delete']) {
           return $holdDetails['item_id'];
        } else {
           return null;
        }
    }

    public function cancelHolds($details)
    {
        $patron = $details['patron'];
        $patronId = $patron['id'];
        $count = 0;
        $statuses = array();
        foreach ($details['details'] as $id) {
           try {
               $result = $this->doRestDLFRequest(array('patron', $patronId, 'circulationActions', 'requests', 'holds', $id), null, HTTP_REQUEST_METHOD_DELETE);
           } catch (Exception $ex) {
               $statuses[$id] = array('success' => false, 'status' => 'cancel_hold_failed', 'sysMessage' => (string) $ex->getMessage());
           }
           $count++;
           $statuses[$id] = array('success' => true, 'status' => 'cancel_hold_ok');
        }
        $statuses['count'] = $count;
        return $statuses;
    }
     

    /**
     * Get Patron Fines
     *
     * This is responsible for retrieving all fines by a specific patron.
     *
     * @param array $user The patron array from patronLogin
     *
     * @return mixed      Array of the patron's fines on success, PEAR_Error
     * otherwise.
     * @access public
     */
    public function getMyFines($user)
    {
        $finesList = array();
        $finesListSort = array();

        $xml = $this->doRestDLFRequest(array('patron', $user['id'], 'circulationActions', 'cash'), array("view" => "full"));

        foreach ($xml->xpath('//cash') as $item) {
            $z31 = $item->z31;
            $z13 = $item->z13;
            $z30 = $item->z30;
            $delete = $item->xpath('@delete');
            $title = (string) $z13->{'z13-title'};
            $description = (string) $z31->{'z31-description'};
            $transactiondate = date('d-m-Y', strtotime((string) $z31->{'z31-date'}));
            $transactiontype = (string) $z31->{'z31-credit-debit'};
            $id = (string) $z13->{'z13-doc-number'};
            $barcode = (string) $z30->{'z30-barcode'};
            $checkout = (string) $z31->{'z31-date'};
            $id = $this->barcodeToID($barcode);
            if($transactiontype=="Debit")
                $mult=-100;
            elseif($transactiontype=="Credit")
                $mult=100;
            $amount = (float)(preg_replace("/[\(\)]/", "", (string) $z31->{'z31-sum'}))*$mult;
            $cashref = (string) $z31->{'z31-sequence'};
            $cashdate = date('d-m-Y', strtotime((string) $z31->{'z31-date'}));
            $balance = 0;

            $finesListSort[$cashref]  = array(
                    "title"   => $title,
                    "description" => $description,
                    "barcode" => $barcode,
                    "amount" => $amount,
                    "transactiondate" => $transactiondate,
                    "transactiontype" => $transactiontype,
                    "checkout" => $this->parseDate($checkout),
                    "balance"  => $balance,
                    "id"  => $id
            ); 
        }
        ksort($finesListSort);
        foreach ($finesListSort as $key => $value){
            $title = $finesListSort[$key]["title"];
            $description = $finesListSort[$key]["description"];
            $barcode = $finesListSort[$key]["barcode"]; 
            $amount = $finesListSort[$key]["amount"]; 
            $checkout = $finesListSort[$key]["checkout"]; 
            $transactiondate = $finesListSort[$key]["transactiondate"]; 
            $transactiontype = $finesListSort[$key]["transactiontype"]; 
            $balance += $finesListSort[$key]["amount"];
            $id = $finesListSort[$key]["id"];
            $finesList[] = array(
                "title"   => $title,
                "description" => $description,
                "barcode"  => $barcode,
                "amount"   => $amount,
                "transactiondate" => $transactiondate,
                "transactiontype" => $transactiontype,
                "balance"  => $balance,
                "checkout" => $checkout,
                "id"  => $id,
                "printLink" => "test",
            ); 
        }
        return $finesList;
    }

    /**
     * Get Patron Profile
     *
     * This is responsible for retrieving the profile for a specific patron.
     *
     * @param array $user The patron array
     *
     * @return mixed      Array of the patron's profile data on success, PEAR_Error otherwise.
     * @access public
     */
    function getMyProfile($user)
    {
        if ($this->xserver_enabled) {
           return $this->getMyProfileX($user);
        } else {
           return $this->getMyProfileDLF($user);
        }
    }

    function getMyProfileX($user)
    {   
        $recordList=array();
        if (!$user['college']) {
           $user['college'] = $this->useradm;
        }
        $xml = $this->doXRequest("bor-info", array('loans' => 'N', 'cash' => 'N', 'hold' => 'N', 'library' => $user['college'],
            'bor_id' => $user['id']), true);
        $id = (string) $xml->z303->{'z303-id'};
        $address1 = (string) $xml->z304->{'z304-address-2'};
        $address2 = (string) $xml->z304->{'z304-address-3'};
        $zip = (string) $xml->z304->{'z304-zip'};
        $phone = (string) $xml->z304->{'z304-telephone'};
        $barcode = (string) $xml->z304->{'z304-address-0'};
        $group = (string) $xml->z305->{'z305-bor-status'};
        $expiry = (string) $xml->z305->{'z305-expiry-date'};
        $credit_sum = (string) $xml->z305->{'z305-sum'};
        $credit_sign = (string) $xml->z305->{'z305-credit-debit'};
        $name = (string) $xml->z303->{'z303-name'};
        if (strstr($name, ",")) {
           list($lastname, $firstname) = split(",", $name);
        } else {
           $lastname = $name;
           $firstname = "";
        }
        if ($credit_sign == null) {
           $credit_sign = "C";
        }
        $recordList['firstname'] = $firstname;
        $recordList['lastname'] = $lastname;
        if (isset($user['email'])) {
            $recordList['email'] = $user['email'];
        }
        $recordList['address1'] = $address1;
        $recordList['address2'] = $address2;
        $recordList['zip'] = $zip;
        $recordList['phone'] = $phone;
        $recordList['group'] = $group;
        $recordList['barcode'] = $barcode;
        $recordList['expire'] = $this->parseDate($expiry);
        $will_expire_in_days = ((strtotime($this->parseDate($recordList['expire'])) - strtotime(date('d.m.Y'))) / (60*60*24));
        if ($will_expire_in_days < 30) {
            $recordList['checkedout_message'] = "Your library card will expire in 30 days. Some loans thus can't be renewed.";
        }
        $recordList['credit'] = $expiry;
        $recordList['credit_sum'] = $credit_sum;
        $recordList['credit_sign'] = $credit_sign;
        $recordList['id'] = $id;
        return $recordList;
    }

    public function getMyProfileDLF($user)
    {
        $xml = $this->doRestDLFRequest(array('patron', $user['id'], 'patronInformation', 'address'));
        $address = $xml->xpath('//address-information');
        $address = $address[0];
        $address1 = (string)$address->{'z304-address-1'};
        $address2 = (string)$address->{'z304-address-2'};
        $address3 = (string)$address->{'z304-address-3'};
        $address4 = (string)$address->{'z304-address-4'};
        $address5 = (string)$address->{'z304-address-5'};
        $zip = (string)$address->{'z304-zip'};
        $phone = (string)$address->{'z304-telephone-1'};
        $email = (string)$address->{'z404-email-address'};
        $dateFrom = (string)$address->{'z304-date-from'};
        $dateTo = (string)$address->{'z304-date-to'};
        $recordList['firstname'] = $firstname;
        $recordList['lastname'] = $lastname;
        $recordList['address1'] = $address2;
        $recordList['address2'] = $address3;
        $recordList['barcode'] = $address1;
        $recordList['zip'] = $zip;
        $recordList['phone'] = $phone;
        $recordList['email'] = $email;
        $recordList['dateFrom'] = $dateFrom;
        $recordList['dateTo'] = $dateTo;
        $recordList['id'] = $user['id'];
        $xml = $this->doRestDLFRequest(array('patron', $user['id'], 'patronStatus', 'registration'));
        $status = $xml->xpath("//institution/z305-bor-status");
        $expiry = $xml->xpath("//institution/z305-expiry-date");
        $recordList['expire'] = $this->parseDate($expiry[0]);
        $recordList['group'] = $status[0];
        return $recordList;
    }

    /**
     * Patron Login
     *
     * This is responsible for authenticating a patron against the catalog.
     *
     * @param string $barcode The patron barcode
     * @param string $lname   The patron last name
     *
     * @return mixed          Associative array of patron info on successful login,
     * null on unsuccessful login, PEAR_Error on error.
     * @access public
     */
    public function patronLogin($user, $password)
    {
       if ($password == NULL && $this->disable_ils_auth) {
          $temp = array("id" => $user);
          $temp['college'] = $this->useradm;
          return $this->getMyProfile($temp);
       }
       try {
            if ($this->xserver_enabled) {
                $xml = $this->doXRequest('bor-auth', array('library' => $this->useradm, 'bor_id' => $user, 'verification' => $password), true);
            } else { // TODO:
                $xml = $this->doPDSRequest('authenticate', array('institute' => $this->useradm, 'bor_id' => $user,
                    'bor_verification' => $password, 'calling_system' => 'aleph'), true);
            }
        } catch (Exception $ex) {
            $patron = new PEAR_Error($ex->getMessage());
            return $patron;
        }
        $patron=array();
        $firstName = "";
        $lastName = "";
        $name = $xml->z303->{'z303-name'};
        list($lastName,$firstName) = split(",", $name); 
        $email_addr = $xml->z304->{'z304-email-address'};
        $id = $xml->z303->{'z303-id'};
        $home_lib = $xml->z303->z303_home_library;
        // Default the college to the useradm library and overwrite it if the
        // home_lib exists
        $patron['college'] = $this->useradm;
        if (($home_lib != '') && (array_key_exists("$home_lib",$this->sublibadm))) {
           if ($this->sublibadm["$home_lib"] != '') {
               $patron['college'] = $this->sublibadm["$home_lib"];
           }
        }
        $patron['id'] = (string) $id;
        $patron['barcode'] = (string) $barcode;
        $patron['firstname'] = (string) $firstName;
        $patron['lastname'] = (string) $lastName;
        $patron['cat_username'] = (string) $id;
        $patron['cat_password'] = $password;
        $patron['email'] = (string) $email_addr;
        $patron['major'] = NULL;
        return $patron;
    }

    public function getHoldingInfoForItem($patronId, $id, $group)
    {
        list($bib, $sys_no) = $this->parseId($id);
        $resource = $bib . $sys_no;
        $xml = $this->doRestDLFRequest(array('patron', $patronId, 'record', $resource, 'items', $group));
        $holdRequestAllowed = $xml->xpath("//item/info[@type='HoldRequest']/@allowed");
        $holdRequestAllowed = $holdRequestAllowed[0] == 'Y';
        if ($holdRequestAllowed) {
            return $this->extractHoldingInfoForItem($xml);
        }
        $shortLoanAllowed = $xml->xpath("//item/info[@type='ShortLoan']/@allowed");
        $shortLoanAllowed = $shortLoanAllowed[0] == 'Y';
        if ($shortLoanAllowed) {
            return $this->extractShortLoanInfoForItem($xml);
        }
    }
    
    private function extractHoldingInfoForItem($xml) {
        $locations = array();
        $status = $xml->xpath('//status/text()');
        $status = (string) $status[0];
        $availability = true;
        $duedate = null;
        if (!in_array($status, $this->available_statuses)) {
            $availability = false;
            $matches = array();
            if (preg_match("/([0-9]*\\/[a-zA-Z]*\\/[0-9]*);([a-zA-Z ]*)/", $status, &$matches)) {
                $duedate = $this->parseDate($matches[1]);
            } else if (preg_match("/([0-9]*\\/[a-zA-Z]*\\/[0-9]*)/", $status, &$matches)) {
                $duedate = $this->parseDate($matches[1]);
            } else {
                $duedate = null;
            }
        }
        $part = $xml->xpath('//pickup-locations');
        if ($part) {
            foreach ($part[0]->children() as $node) {
                $arr = $node->attributes();
                $code = (string) $arr['code'];
                $loc_name = (string) $node;
                $locations[$code] = $loc_name;
            }
        } else {
            throw new Exception('No pickup locations');
        }
        $str = $xml->xpath('//item/queue/text()');
        list($requests, $other) = split(' ', trim($str[0]));
        if ($requests == null) {
            $requests = 0;
        }
        $date = $xml->xpath('//last-interest-date/text()');
        $date = $date[0];
        $date = "" . substr($date, 6, 2) . "." . substr($date, 4, 2) . "." . substr($date, 0, 4);
        $result = array(
            'type' => 'hold',
            'pickup-locations' => $locations,
            'last-interest-date' => $date,
            'order' => $requests + 1,
            'duedate' => $duedate,
        );
        return $result;
    }
    
    private function extractShortLoanInfoForItem($xml) {
        $shortLoanInfo = $xml->xpath("//item/info[@type='ShortLoan']");
        $slots = array();
        foreach ($shortLoanInfo[0]->{'short-loan'}->{'slot'} as $slot) {
            $numOfItems = (int) $slot->{'num-of-items'};
            $numOfOccupied = (int) $slot->{'num-of-occupied'};
            $available = $numOfItems - $numOfOccupied;
            if ($available <= 0) {
                continue;
            }
            $start_date = $slot->{'start'}->{'date'};
            $start_time = $slot->{'start'}->{'hour'};
            $end_date = $slot->{'end'}->{'date'};
            $end_time = $slot->{'end'}->{'hour'};
            $time = substr($start_date, 6, 2) . "." . substr($start_date, 4, 2) . "." 
                . substr($start_date, 0, 4) . " " . substr($start_time, 0, 2) . ":" 
                . substr($start_time, 2, 2) . " - " . substr($end_time, 0, 2) . ":"
                . substr($end_time, 2, 2);
            $id = $slot->attributes()->id;
            $id = (string) $id[0];
            $slots[$id] = $time;
        }
        $result = array(
            'type'  => 'short',
            'slots' => $slots,
        );
        return $result;
    }

    public function placeHold($details)
    {
        list($bib, $sys_no) = $this->parseId($details['id']);
        $recordId = $bib . $sys_no;
        $itemId = $details['item_id'];
        $pickup_location = $details['pickup_location'];
        $patron = $details['patron'];
        $requiredBy = $details['requiredBy'];
        $comment = $details['comment'];
        list($month, $day, $year) = split("-", $requiredBy);
        $requiredBy = $year . str_pad($month, 2, "0", STR_PAD_LEFT) . str_pad($day, 2, "0", STR_PAD_LEFT);
        $patronId = $patron['id'];
        if (!$pickup_location) {
           $info = $this->getHoldingInfoForItem($patronId, $recordId, $itemId);
           // FIXME: choose preffered location
           foreach($info['pickup-locations'] as $key => $value) {
              $pickup_location = $key;
           }
        }
        $data = "post_xml=<?xml version='1.0' encoding='UTF-8'?>\n" .
           "<hold-request-parameters>\n" .
           "   <pickup-location>$pickup_location</pickup-location>\n" .
           "   <last-interest-date>$requiredBy</last-interest-date>\n" .
           "   <note-1>$comment</note-1>\n".
           "</hold-request-parameters>\n";
        try {
            $result = $this->doRestDLFRequest(array('patron', $patronId, 'record', $recordId, 'items', $itemId, 'hold'), null, HTTP_REQUEST_METHOD_PUT, $data);
        } catch (Exception $ex) {
           return array('success' => false, 'sysMessage' => $ex->getMessage()); 
        }
        return array('success' => true);
    }
    
    public function placeShortLoanRequest($details) {
        list($bib, $sys_no) = $this->parseId($details['id']);
        $recordId = $bib . $sys_no;
        $slot = $details['slot'];
        $itemId = $details['item_id'];
        $patron = $details['patron'];
        $patronId = $patron['id'];
        $body = new SimpleXMLElement(
            '<?xml version="1.0" encoding="UTF-8"?>'
            . '<short-loan-parameters></short-loan-parameters>'
        );
        $body->addChild('request-slot', $slot);
        $data = 'post_xml=' . $body->asXML();
        try {
            $result = $this->doRestDLFRequest(array('patron', $patronId, 'record', $recordId,
                'items', $itemId, 'shortLoan'), null, HTTP_REQUEST_METHOD_PUT, $data);
        } catch (Exception $ex) {
            return array('success' => false, 'sysMessage' => $ex->getMessage());
        }
        return array('success' => true);
    }

    public function barcodeToID($bar)
    {
        if (!$this->xserver_enabled) {
           return null;
        }
        foreach ($this->bib as $base) {
           try {
              $xml = $this->doXRequest("find", array("base" => $base, "request" => "BAR=$bar"), false);
              $docs = (int) $xml->{"no_records"};
              if ($docs == 1) {
                 $set = (string) $xml->{"set_number"};
                 $result = $this->doXRequest("present", array("set_number" => $set, "set_entry" => "1"), false);
                 $id = $result->xpath('//doc_number/text()');
                 if (count($this->bib)==1) {
                    return $id[0];
                 } else {
                    return $base . "-" . $id[0];
                 }
              }
           } catch (Exception $ex) {
           }
        }
        return new PEAR_Error('barcode not found');
    }

    function parseDate($date)
    {
       if (preg_match("/^[0-9]{8}$/", $date) === 1) {
           return substr($date, 6, 2) . "." .substr($date, 4, 2) . "." . substr($date, 0, 4);
        } else {
           list($day, $month, $year) = split("/", $date, 3);
           $translate_month = array ( 'jan' => '01', 'feb' => '02', 'mar' => '03', 'apr' => '04', 'may' => '05', 'jun' => '06',
              'jul' => '07', 'aug' => '08', 'sep' => '09', 'oct' => '10', 'nov' => '11', 'dec' => '12');
           return $day . "." . $translate_month[strtolower($month)] . "." . $year;
        }
    }

    public function getConfig($func)
    {
        if ($func == "Holds") {
           return array("HMACKeys" => "id:item_id", "extraHoldFields" => "comments:requiredByDate",
              "defaultRequiredDate" => "0:1:0");
        } else {
           return array();
        }
    }
    
    public function getMyAcquisitionRequests($user) {
        $userId = $user['id'];
        $params = array("view" => "full");
        $xml = $this->doRestDLFRequest(array('patron', $userId, 'circulationActions', 'requests', 'acq'), $params);
        $requests = array();
        foreach ($xml->xpath('//acq-request') as $item) { 
            $request = array();
            $z13 = $item->{'z13'};
            $z68 = $item->{'z68'};
            $request['author'] = (string) $z13->{'z13-author'};
            $request['title'] = (string) $z13->{'z13-title'};
            $request['publisher'] = (string) $z13->{'z13-imprint'};
            $request['isbn'] = (string) $z13->{'z13-issn'};
            $request['status'] = (string) $z68->{'z68-order-status'};
            $request['updated'] = $this->parseDate((string) $z13->{'z13-update-date'});
            $request['note'] = (string) $z68->{'z68-library-note'};
            $requests[] = $request;
        }
        return $requests;
    }

    public function getMyInterlibraryLoans($user) {
        $userId = $user['id'];
        $loans = array();
        $params = array("view" => "full");
        $count = 0;
        $xml = $this->doRestDLFRequest(array('patron', $userId, 'circulationActions', 'requests', 'ill'), $params);
        foreach ($xml->xpath('//ill-request') as $item) {
            $loan = array();
            $z13 = $item->z13;
            $status = (string) $item->z410->{'z410-status'};
            if (!in_array($status, $this->ill_hidden_statuses)) {
                $loan['docno'] = (string) $z13->{'z13-doc-number'};
                $loan['author'] = (string) $z13->{'z13-author'};
                $loan['title'] = (string) $z13->{'z13-title'};
                $loan['imprint'] = (string) $z13->{'z13-imprint'};
                $loan['article_title'] = (string) $item->{'title-of-article'};
                $loan['article_author'] = (string) $item->{'author-of-article'};
                $loan['price'] = (string) $item->{'z13u-additional-bib-info-1'};
                $loan['pickup_location'] = (string) $item->z410->{'z410-pickup-location'};
                $loan['media'] = (string) $item->z410->{'z410-media'};
                $loan['required_by'] = $this->parseDate((string) $item->z410->{'z410-last-interest-date'});
                $loans[] = $loan;
            }
        }
        return $loans; 
    }

    public function createInterlibraryLoan($user, $attrs) {
        $payment = $attrs['payment'];
        unset($attrs['payment']);
        $new = $attrs['new'];
        unset($attrs['new']);
        $additional_authors = $attrs['additional_authors']; 
        unset($attrs['additional_authors']);
        if ($new == 'serial') {
            $new = 'SE';
        } else if ($new == 'monography') {
            $new = 'MN';
        }
        $patronId = $user['id'];
        $illDom = new DOMDocument('1.0', 'UTF-8');
        $illRoot = $illDom->createElement('ill-parameters');
        $illRootNode = $illDom->appendChild($illRoot);
        foreach ($attrs as $key => $value) {
            $element = $illDom->createElement($key);
            $element->appendChild($illDom->createTextNode($value));
            $illRootNode->appendChild($element);
        }
        $xml = $illDom->saveXML();
        try {
            $result = $this->doRestDLFRequest(array('patron', $patronId, 'record', $new, 'ill'), null, HTTP_REQUEST_METHOD_PUT, 'post_xml=' . $xml);
        } catch (Exception $ex) {
           return array('success' => false, 'sysMessage' => $ex->getMessage()); 
        }
        $baseAndDocNumber = $result->{'create-ill'}->{'request-number'};
        $base = substr($baseAndDocNumber, 0, 5);
        $docNum = substr($baseAndDocNumber, 5);
        $findDocParams = array('base' => $base, 'doc_num' => $docNum);
        $document = $this->doXRequest('find-doc', $findDocParams, true);
        // create varfield for ILL request type
        $varfield = $document->{'record'}->{'metadata'}->{'oai_marc'}->addChild('varfield');
        $varfield->addAttribute('id', 'PNZ');
        $varfield->addAttribute('i1', ' ');
        $varfield->addAttribute('i2', ' ');
        $subfield = $varfield->addChild('subfield', $payment);
        $subfield->addAttribute('label', 'a');
        if (!empty($additional_authors)) {
            $varfield = $document->{'record'}->{'metadata'}->{'oai_marc'}->addChild('varfield');
            $varfield->addAttribute('id', '700');
            $varfield->addAttribute('i1', '1');
            $varfield->addAttribute('i2', ' ');
            $subfield = $varfield->addChild('subfield', $additional_authors);
            $subfield->addAttribute('label', 'a');
        }
        $updateDocParams = array('library' => $base, 'doc_num' => $docNum);
        $updateDocParams['xml_full_req'] = $document->asXml(); 
        $updateDocParams['doc_action'] = 'UPDATE';
        try {
            $update = $this->doXRequestUsingPost('update-doc', $updateDocParams, true);
        } catch (Exception $ex) {
           return array('success' => false, 'sysMessage' => $ex->getMessage()); 
        }
        return array('success' => true, 'id' => $docNum);
    }
    
    /**
     * 
     * Get Favorite Items from ILS
     * 
     * @param mixed  $patron  Patron data
     * @return mixed          An array with favorite items (each item contains id, folder and note) 
     * @access public
     */
    public function getMyFavorites($patron) {
        if ($this->favourites_url == null) {
            return array(); // graceful degradation
        }
        $url = $this->appendQueryString($this->favourites_url, array('id' => $patron['id']));
        $xml = $this->doHTTPRequest($url);
        $result = array();
        foreach ($xml->{'favourite'} as $fav) {
            $result[] = array( 
                'id'     => (string) $fav->{'id'},
                'folder' => (string) $fav->{'folder'},
                'note'   => (string) $fav->{'note'} 
            );
        }
        return $result;
    }
    
    public function getMyBookings($patron) {
        $xml = $this->doRestDLFRequest(array('patron', $patron['id'], 'circulationActions',
            'requests', 'bookings'), array("view" => "full"));
        $result = array();
        foreach ($xml->xpath('//booking-request') as $item) {
            $delete = $item->xpath('@delete');
            $href = $item->xpath('@href');
            $item_id = substr($href[0], strrpos($href[0], '/') + 1);
            $z37 = $item->z37;
            $z30 = $item->z30;
            $barcode = (string) $z30->{'z30-barcode'};
            $startDate = $z37->{'z37-booking-start-date'};
            $startTime = $z37->{'z37-booking-start-hour'};
            $endDate = $z37->{'z37-booking-end-date'};
            $endTime = $z37->{'z37-booking-end-hour'};
            $start = substr($startDate[0], 6, 2) . '. ' . substr($startDate[0], 4, 2) . '. ' . substr($startDate[0], 0, 4)
                . ' ' . substr($startTime[0], 0, 2) . ':' .  substr($startTime[0], 2, 2);
            $end = substr($endDate[0], 6, 2) . '. ' . substr($endDate[0], 4, 2) . '. ' . substr($endDate[0], 0, 4)
                . ' ' . substr($endTime[0], 0, 2) . ':' .  substr($endTime[0], 2, 2);
            $delete = ($delete[0] == "Y");
            $result[] = array(
                'id'      => $this->barcodeToID($barcode),
                'start'   => $start,
                'end'     => $end,
                'delete'  => $delete,
                'item_id' => $item_id,
            );
        }
        return $result;
    }
    
    public function deleteMyBookings($details) {
        $patron = $details['patron'];
        $patronId = $patron['id'];
        $count = 0;
        $statuses = array();
        foreach ($details['details'] as $id) {
            try {
                $result = $this->doRestDLFRequest(array('patron', $patronId, 'circulationActions',
                    'requests', 'bookings', $id), null, HTTP_REQUEST_METHOD_DELETE);
            } catch (Exception $ex) {
                $statuses[$id] = array('success' => false, 'status' => 'cancel_hold_failed', 'sysMessage' => (string) $ex->getMessage());
            }
            $count++;
            $statuses[$id] = array('success' => true, 'status' => 'cancel_hold_ok');
        }
        $statuses['count'] = $count;
        return $statuses;
    }
    
    public function getUserNickname($patron) {
        $params = array(
            'op'           => 'get_nickname',
        );
        $xml = $this->changeUserRequest($patron, $params, true);
        if ($xml->error) {
            return new PEAR_Error($xml->error);
        } else {
            return $xml->nick;
        }
    }
    
    public function changeUserNickname($patron, $newAlias) {
        $params = array(
            'op'           => 'change_nickname',
            'new_nickname' => $newAlias,
        );
        return $this->changeUserRequest($patron, $params);
    }
    
    public function changeUserPassword($patron, $oldPassword, $newPassword) {
        $params = array(
            'op'      => 'change_password',
            'old_pwd' => $oldPassword,
            'new_pwd' => $newPassword,
        );
        return $this->changeUserRequest($patron, $params);
    }
    
    public function changeUserEmailAddress($patron, $newEmailAddress) {
        $params = array(
            'op'      => 'change_email',
            'email' => $newEmailAddress,
        );
        return $this->changeUserRequest($patron, $params);
    }
    
    protected function changeUserRequest($patron, $params, $returnResult = false) {
        if ($this->user_cgi_url == null) {
            return new PEAR_Error('not supported');
        }
        $params['id']            = $patron['id'];
        $params['user_name']     = $this->wwwuser;
        $params['user_password'] = $this->wwwpasswd;
        $url = $this->appendQueryString($this->user_cgi_url, $params);
        $xml = $this->doHTTPRequest($url);
        if ($returnResult) {
            return $xml;
        }
        if ($xml->error) {
            return new PEAR_Error($xml->error);
        } else {
            return true;
        }
    }

     /**
     * Get Purchase History
     *
     * This is responsible for retrieving the acquisitions history data for the
     * specific record (usually recently received issues of a serial).
     *
     * @param string $id The record id to retrieve the info for
     *
     * @return mixed     An array with the acquisitions data on success, PEAR_Error
     * on failure
     * @access public
     */
    public function getPurchaseHistory($id)
    {
        return array();
    }

    
    /**
     * Get New Items
     *
     * Retrieve the IDs of items recently added to the catalog.
     *
     * @param int $page    Page number of results to retrieve (counting starts at 1)
     * @param int $limit   The size of each page of results to retrieve
     * @param int $daysOld The maximum age of records to retrieve in days (max. 30)
     * @param int $fundId  optional fund ID to use for limiting results (use a value
     * returned by getFunds, or exclude for no limit); note that "fund" may be a
     * misnomer - if funds are not an appropriate way to limit your new item
     * results, you can return a different set of values from getFunds. The
     * important thing is that this parameter supports an ID returned by getFunds,
     * whatever that may mean.
     *
     * @return array       Associative array with 'count' and 'results' keys
     * @access public
     */
    public function getNewItems($page, $limit, $daysOld, $fundId = null)
    {
        $items = array();
        return $items;
    }

    /**
     * Get Departments
     *
     * Obtain a list of departments for use in limiting the reserves list.
     *
     * @return array An associative array with key = dept. ID, value = dept. name.
     * @access public
     */
    public function getDepartments()
    {
        $deptList = array();
        return $deptList;
    }

    /**
     * Get Instructors
     *
     * Obtain a list of instructors for use in limiting the reserves list.
     *
     * @return array An associative array with key = ID, value = name.
     * @access public
     */
    public function getInstructors()
    {
        $deptList = array();
        return $deptList;
    }

    /**
     * Get Courses
     *
     * Obtain a list of courses for use in limiting the reserves list.
     *
     * @return array An associative array with key = ID, value = name.
     * @access public
     */
    public function getCourses()
    {
        $deptList = array();
        return $deptList;
    }

    /**
     * Find Reserves
     *
     * Obtain information on course reserves.
     *
     * @param string $course ID from getCourses (empty string to match all)
     * @param string $inst   ID from getInstructors (empty string to match all)
     * @param string $dept   ID from getDepartments (empty string to match all)
     *
     * @return mixed An array of associative arrays representing reserve items (or a
     * PEAR_Error object if there is a problem)
     * @access public
     */
    public function findReserves($course, $inst, $dept)
    {
        $recordList = array();
        return $recordList;
    }

}

?>
