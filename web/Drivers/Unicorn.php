<?php
/**
 * SirsiDynix Unicorn ILS Driver
 *
 * IMPORTANT:  This is not the latest Unicorn driver.  For better functionality,
 * please visit the vufind-unicorn project: http://code.google.com/p/vufind-unicorn/
 *
 * PHP version 5
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
 * @package  ILS_Drivers
 * @author   Drew Farrugia <vufind-unicorn-l@lists.lehigh.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://code.google.com/p/vufind-unicorn/ vufind-unicorn project
 */
require_once 'Interface.php';

/**
 * SirsiDynix Unicorn ILS Driver
 *
 * IMPORTANT:  This is not the latest Unicorn driver.  For better functionality,
 * please visit the vufind-unicorn project: http://code.google.com/p/vufind-unicorn/
 *
 * @category VuFind
 * @package  ILS_Drivers
 * @author   Drew Farrugia <vufind-unicorn-l@lists.lehigh.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://code.google.com/p/vufind-unicorn/ vufind-unicorn project
 */
class Unicorn implements DriverInterface
{
    private $_host;
    private $_port;
    private $_searchProg;

    /**
     * Constructor
     *
     * @access public
     */
    public function __construct()
    {
        // Load Configuration for this Module

        $configArray = parse_ini_file('conf/Unicorn.ini', true);

        $this->_host = $configArray['Catalog']['host'];
        $this->_port = $configArray['Catalog']['port'];
        $this->_searchProg = $configArray['Catalog']['search_prog'];
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

        $params = array('search' => 'holding', 'id' => $id);
        $xml = $this->_searchSirsi($params);

        foreach ($xml->record as $record) {
            $callnum_rec = $record->catalog->callnum_records;
            $item_rec = $record->catalog->item_record;

            // Unicorn doesn't give status or availability; so make them up
            $status = "Available";
            $availability = 1;

            if ( $item_rec->date_time_due ) {
                $status = "Checked Out";
                $availability = 0;
            }

            $holding[] = array (
                'status' => $status,
                'availability' => $availability,
                'id' => $id,
                'number' => $item_rec->copy_number,
                'duedate' => $item_rec->date_time_due,
                'callnumber' => $callnum_rec->item_number,
                'reserve' => $callnum_rec->copies_on_reserve,
                'location' => $item_rec->location,
                // can also get these values from Unicorn
                //'ncopies' => $callnum_rec->number_of_copies,
                //'item_type' => $item_rec->item_type,
                //'barcode' => $item_rec->item_id
            );
        }

        return $holding;

    } // end getStatus

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
        /* want the params array to look like so:
             $params = array (
                'search' => 'holdings',
                'id0' => "$idList[0]",
                'id1' => "$idList[1]",
                'id2' => "$idList[2]",
            );
         */

        $params['search'] = 'holdings';

        for ($i=0; $i<count($idList); $i++) {
            $params["id$i"] = "$idList[$i]";
        }

        $i = 0; // to get the id from $params in foreach loops below

        $xml = $this->_searchSirsi($params);

        foreach ($xml->titles as $titles) {
            $holdings = array();

            foreach ($titles->record as $record) {
                $callnum_rec = $record->catalog->callnum_records;
                $item_rec = $record->catalog->item_record;

                // Unicorn doesn't give status or availability; make them up
                $status = "Available";
                $availability = 1;

                if ($item_rec->date_time_due) {
                    $status = "Checked Out";
                    $availability = 0;
                }

                $holdings[] = array (
                    'status' => $status,
                    'availability' => $availability,
                    'id' => $params["id$i"],
                    'number' => $item_rec->copy_number,
                    'duedate' => $item_rec->date_time_due,
                    'callnumber' => $callnum_rec->item_number,
                    'reserve' => $callnum_rec->copies_on_reserve,
                     'location' => $item_rec->location,
                     // can also get following values from Unicorn
                     //'ncopies' => $callnum_rec->number_of_copies,
                     //'item_type' => $item_rec->item_type,
                     //'barcode' => $item_rec->item_id
                 );
            } // end foreach ($titles->record as $record) {

            $items[] = $holdings;
            $i++; // increment to get item id
        }

        return $items;

    } // end getStatuses

    /**
     * Communicate with the ILS.
     *
     * @param array $params Parameters to send to ILS.
     *
     * @return object       Response (SimpleXML object).
     * @access private
     */
    private function _searchSirsi($params)
    {
        $url = $this->_buildQuery($params);
        $response = file_get_contents($url);

        $xml = simplexml_load_string($response);

        if ($xml === false) {
            echo "<br/>" .
                "simplexml_load_string failed in Unicorn.php, _searchSirsi()<br/>";
            exit(1);
        }
        return $xml;
    }

    /**
     * Build a query URL to communicate with the ILS.
     *
     * @param array $params Parameters to send to ILS.
     *
     * @return string       Query URL
     * @access private
     */
    private function _buildQuery($params)
    {
        $query_string = '?';
        $url = $this->_host;

        if ($this->_port) {
            $url=  $url . ":" . $this->_port . "/" . $this->_searchProg;
        } else {
            $url =  $url . "/" . $this->_searchProg;
        }

        $url = $url . '?' . http_build_query($params);

        return $url;
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
        return $this->getStatus($id);
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

}

?>
