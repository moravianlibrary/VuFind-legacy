<?php
/**
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
 */
 
require_once 'CatalogConnection.php';
require_once 'Record.php';
require_once 'Drivers/Aleph.php';

class ExtendedHold extends Record
{
    private $userInfo;

    function __construct()
    {
        $this->userInfo = UserAccount::isLoggedIn();
        parent::__construct();
    }
    
    function launch()
    {
        global $interface;
        global $user;

        $interface->assign('id', $_GET['id']);
        $interface->assign('recordId', $this->recordDriver->getUniqueID());
        $interface->setPageTitle(
            translate('request_place_text') . ': ' .
            $this->recordDriver->getBreadcrumb()
        );

        if (!UserAccount::isLoggedIn()) {
            // Needed for "back to record" link in view-alt.tpl:
            $interface->assign('barcode', $_GET['barcode']);
            // Needed for login followup:
            $interface->assign('recordId', $_GET['id'] . "," . $_GET['lookfor']);
            if (isset($_GET['lightbox'])) {
                $interface->assign('title', $_GET['message']);
                $interface->assign('message', 'You must be logged in first');
                $interface->assign('followup', true);
                $interface->assign('followupModule', 'Record');
                $interface->assign('followupAction', "ExtendedHold");
                return $interface->fetch('AJAX/login.tpl');
            } else {
                $sessionInitiator = $interface->get_template_vars("sessionInitiator");
                header("Location: $sessionInitiator");
                die();
            }
            exit();
        }

        if (isset($_POST['submit'])) {
            $result = $this->placeHold();
            if (!$result['success']) {
                $interface->assign('error', true);
                $interface->assign('error_str', $result['sysMessage']);
            }
            $interface->assign('subTemplate', 'extended-hold-status.tpl');
            $interface->setTemplate('view-alt.tpl');
            $interface->display('layout.tpl');
        } else {
            return $this->display();
        }
    }
    
    function display()
    {
        global $interface;
        try {
            $catalog = new Aleph(); // CatalogConnection($configArray['Catalog']['driver']);
        } catch (PDOException $e) {
            return new PEAR_Error('Cannot connect to ILS');
        }
        $id = $_GET['id'];
        if (!isset($_REQUEST['barcode'])) {
            $interface->assign('error', true);
            $interface->assign('error_str', "barcode is missing!");
            $interface->assign('subTemplate', 'extended-hold-status.tpl');
            $interface->setTemplate('view-alt.tpl');
            $interface->display('layout.tpl');
            return;
        }
        $group = $_REQUEST['barcode'];
        if (strpos($id, ",") !== false) {
           list($id, $group) = split(",", $id); 
        }
        $patron = $catalog->patronLogin($this->userInfo->cat_username, $this->userInfo->cat_password);
        try {
           $info = $catalog->getHoldingInfoForItem($patron['id'], $id, $group);
           $interface->assign('order', $info['order']);
           $interface->assign('duedate', $info['duedate']);
           $interface->assign('locations', $info['pickup-locations']);
           $interface->assign('last_interest_date', $info['last-interest-date']);
        } catch (Exception $e) {
           $interface->assign('error', "You have no rights to place holds");
        }
        $interface->assign('item', $group); // interface->assign('item', $_GET['barcode']);
        $interface->assign('formTargetPath',
            '/Record/' . urlencode($id) . '/ExtendedHold?barcode=' . urlencode($group));
        if (isset($_GET['lightbox'])) {
            // Use for lightbox
            $interface->assign('title', $_GET['message']);
            return $interface->fetch('Record/extended-hold.tpl');
        } else {
            // Display Page
            $interface->setPageTitle('Hold');
            $interface->assign('subTemplate', 'extended-hold.tpl');
            $interface->setTemplate('view-alt.tpl');
            $interface->display('layout.tpl', 'RecordExtendedHold' . $_GET['id']);
        }
    }

    function placeHold() {
        global $configArray;
        global $interface;
        $id = $_REQUEST['id'];
        $to = $_REQUEST['to'];
        $comment = $_REQUEST['comment'];
        $item = $_REQUEST['item'];
        $location = $_REQUEST['location'];
        list($day, $month, $year) = split("\.", $to);
        $to = $year . str_pad($month, 2, "0", STR_PAD_LEFT) . str_pad($day, 2, "0", STR_PAD_LEFT);
        try {
            $catalog = new Aleph(); // CatalogConnection($configArray['Catalog']['driver']);
        } catch (PDOException $e) {
            return new PEAR_Error('Cannot connect to ILS');
        }
        if ($id && $to && $item && $location) {
            $patron = $catalog->patronLogin($this->userInfo->cat_username, $this->userInfo->cat_password);
            // return $catalog->placeHold($patron['id'], $id, $item, $location, $to, $comment);
            $requiredBy = $month . "-" . $day . "-" . $year;
            $details = array( "id" => $id, "patron" => $patron, "item_id" => $item,  "requiredBy" => $requiredBy,
                "pickup_location" => $location, "comment" => $comment);
            $result = $catalog->placeHold($details);
            return $result;
        } else {
            return new PEAR_Error('Cannot connect to ILS');
        }
    }
}
?>
