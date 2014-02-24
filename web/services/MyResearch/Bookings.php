<?php
/**
 * Bookings action for MyResearch module
 *
 * PHP version 5
 *
 * Copyright (C) Villanova University 2007.
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
 * @package  Controller_MyResearch
 * @author   Andrew S. Nagy <vufind-tech@lists.sourceforge.net>
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_a_module Wiki
 */
require_once 'services/MyResearch/MyResearch.php';

/**
 * CheckedOut action for MyResearch module
 *
 * @category VuFind
 * @package  Controller_MyResearch
 * @author   Andrew S. Nagy <vufind-tech@lists.sourceforge.net>
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_a_module Wiki
 */
class Bookings extends MyResearch
{
    /**
     * Process parameters and display the page.
     *
     * @return void
     * @access public
     */
    public function launch()
    {
        global $interface;
        
        if ($patron = UserAccount::catalogLogin()) {
            
            if (isset($_POST['deleteAll']) || isset($_POST['deleteSelected'])) {
                $this->_deleteItems($patron);
            }
            
            if (PEAR::isError($patron)) {
                PEAR::raiseError($patron);
            }
            
            $bookings = $this->catalog->getMyBookings($patron);
            if (PEAR::isError($bookings)) {
                PEAR::raiseError($bookings);
            }
            
            $bookingsList = array();
            $deleteForm = false;
            foreach ($bookings as $data) {
                $current = array('ils_details' => $data);
                if ($data['delete']) {
                    $deleteForm = true;
                }
                if ($record = $this->db->getRecord($data['id'])) {
                    $current += array(
                        'id' => $record['id'],
                        'title' => $record['title'],
                    );
                }
                $bookingsList[] = $current;
            }
            //var_export($bookingsList);
            $interface->assign('bookings', $bookingsList);
            $interface->assign('deleteForm', $deleteForm);
            $interface->setTemplate('bookings.tpl');
            $interface->setPageTitle('Bookings');
            $interface->display('layout.tpl');
        }
    }
    
    /**
     * Private method for renewing items
     *
     * @param array $patron An array of patron information
     *
     * @return null
     * @access private
     */
    private function _deleteItems($patron)
    {
        global $interface;

        $gatheredDetails['details'] = isset($_POST['deleteAll'])
            ? $_POST['deleteAllIDS'] : $_POST['deleteSelectedIDS'];

        if (is_array($gatheredDetails['details'])) {
            // Add Patron Data to Submitted Data
            $gatheredDetails['patron'] = $patron;
            $deleteResult = $this->catalog->deleteMyBookings($gatheredDetails);

            if ($deleteResult !== false) {
                // Assign Results to the Template
                $interface->assign('deleteResult', $deleteResult['details']);
            } else {
                 $interface->assign('errorMsg', 'delete_system_error');
            }
        } else {
            $interface->assign('errorMsg', 'delete_empty_selection');
        }
    }
    
}
