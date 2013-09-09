<?php
/**
 * CheckedOut action for MyResearch module
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
class CheckedOut extends MyResearch
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
        
        // Set last page parameters
        $_SESSION['lastModule'] = $_GET['module'];
        $_SESSION['lastAction'] = $_GET['action'];

        // Get My Transactions
        if ($patron = UserAccount::catalogLogin()) {
            if (PEAR::isError($patron)) {
                PEAR::raiseError($patron);
            }

            // Renew Items
            if (isset($_POST['renewAll']) || isset($_POST['renewSelected'])) {
                $this->_renewItems($patron);
            }

            $result = $this->catalog->getMyTransactions($patron);
            if (PEAR::isError($result)) {
                PEAR::raiseError($result);
            }

            $transList = array();
            foreach ($result as $data) {
                $current = array('ils_details' => $data);
                if ($record = $this->db->getRecord($data['id'])) {
                    $current += array(
                        'id' => $record['id'],
                        'isbn' => isset($record['isbn']) ? $record['isbn'] : null,
                        'author' =>
                            isset($record['author']) ? $record['author'] : null,
                        'title' =>
                            isset($record['title']) ? $record['title'] : null,
                        'format' =>
                            isset($record['format']) ? $record['format'] : null,
                    );
                }
                $transList[] = $current;
            }

            if ($this->checkRenew) {
                $transList = $this->_addRenewDetails($transList);
            }
        }
        
        $interface->assign('patron', $patron);
        $interface->assign('transList', $transList);
        $interface->assign('currentView', $this->view);
        $interface->setTemplate('checkedout.tpl');
        $interface->setPageTitle('Checked Out Items');
        $interface->display('layout.tpl');
    }

    /**
     * Adds a link or form details to existing checkout details
     *
     * @param array $transList An array of patron items
     *
     * @return array An array of patron items with links / form details
     * @access private
     */
    private function _addRenewDetails($transList)
    {
        global $interface;

        foreach ($transList as $key => $item) {

            // Reset $renew_details for next item
            $renew_details = "";
            if ($this->checkRenew['function'] == "renewMyItemsLink") {
                // Build OPAC URL
                $transList[$key]['ils_details']['renew_link']
                    = $this->catalog->renewMyItemsLink($item['ils_details']);
            } else {
                // Form Details
                if ($transList[$key]['ils_details']['renewable']) {
                    $interface->assign('renewForm', true);
                }
                $transList[$key]['ils_details']['renew_details']
                    = $this->catalog->getRenewDetails($item['ils_details']);
            }
        }
        return $transList;
    }

    /**
     * Private method for renewing items
     *
     * @param array $patron An array of patron information
     *
     * @return null
     * @access private
     */
    private function _renewItems($patron)
    {
        global $interface;

        $gatheredDetails['details'] = isset($_POST['renewAll'])
            ? $_POST['renewAllIDS'] : $_POST['renewSelectedIDS'];

        if (is_array($gatheredDetails['details'])) {
            // Add Patron Data to Submitted Data
            $gatheredDetails['patron'] = $patron;
            $renewResult = $this->catalog->renewMyItems($gatheredDetails);

            if ($renewResult !== false) {
                // Assign Blocks to the Template
                $interface->assign('blocks', $renewResult['block']);

                // Assign Results to the Template
                $interface->assign('renewResult', $renewResult['details']);
            } else {
                 $interface->assign('errorMsg', 'renew_system_error');
            }
        } else {
            $interface->assign('errorMsg', 'renew_empty_selection');
        }
    }
}

?>