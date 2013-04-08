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
require_once 'Aleph.php';

/**
 * Aleph ILS driver
 *
 * @category VuFind
 * @package  ILS_Drivers
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_an_ils_driver Wiki
 */
class AlephMzk extends Aleph
{
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
        $foundIds = array();
        $in = implode(',', array_fill(0, count($idList), '?'));
        $sql = "SELECT record_id, absent_total, present_total, absent_total - absent_on_loan AS absent_avail, present_total - present_on_loan AS present_avail FROM record_status WHERE record_id IN ( $in );";
        $conn = new PDO('mysql:host=localhost;dbname=vufind_trunk', 'vufind_trunk', 'vufind_trunk');
        $stmt = $conn->prepare($sql);
        $stmt->execute($idList);
        while($row = $stmt->fetch()) {
            $foundIds[] = $row['record_id'];
            $holding = array(
                "id" => $row['record_id'],
                "absent_total" => $row['absent_total'],
                "absent_avail" => $row['absent_avail'],
                "present_total" => $row['present_total'],
                "present_avail" => $row['present_avail'],
                "availability" => ($row['absent_avail'] > 0 || $row['present_avail'] > 0),
            );
            $holdings[] = array($holding);
        }
        $missingIds = array_diff($idList, $foundIds);
        foreach ($missingIds as $missingId) {
            $holding = array(
                "id" => $missingId,
                "absent_total" => 0,
                "absent_avail" => 0,
                "present_total" => 0,
                "present_avail" => 0,
                "availability" => false,
            );
            $holdings[] = array($holding);
        }
        return $holdings;
    }

}
