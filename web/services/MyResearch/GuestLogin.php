<?php
/**
 * Login action for MyResearch module
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
 * @author   Vaclav Rosecky <xrosecky@gmail.com>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_a_module Wiki
 */
require_once "Action.php";
require_once 'services/MyResearch/MyResearch.php';
require_once 'sys/User.php';

/**
 * Login action for MyResearch module
 *
 * @category VuFind
 * @package  Controller_MyResearch
 * @author   Vaclav Rosecky <xrosecky@gmail.com>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_a_module Wiki
 */
class GuestLogin extends MyResearch
{

    public function __construct()
    {
    }

    /**
     * Process parameters and display the page.
     *
     * @param string $msg Message to display on the page (optional).
     *
     * @return void
     * @access public
     */
    function launch($msg = null)
    {
        $id =  GuestLogin::getRandomString(12) . "@guest";
        $user = new User();
        $user->email = $id;
        $user->username = $id;
        $user->password = $id;
        $user->firstname = "temp";
        $user->lastname = "temp";
        $user->created = date('Y-m-d h:i:s');
        $user->guest = true;
        $user->insert();
        UserAccount::updateSession($user);
        /*
        $user = UserAccount::login();
        if (PEAR::isError($user)) {
            print $user->getMessage() . "<BR>";
        }*/
        if (isset($_REQUEST["redirect"])) {
          header('Location:' . $_REQUEST["redirect"]);
        } else {
          header('Location: Favorites');
        }
        die();
    }

    function getRandomString($length)
    {
        $charset='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        $str = '';
        $count = strlen($charset);
        while ($length--) {
           $str .= $charset[mt_rand(0, $count-1)];
        }
        return $str;
    }

}
