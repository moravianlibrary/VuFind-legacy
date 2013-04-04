<?php
/**
 * Delete action for MyResearch module
 *
 * PHP version 5
 *
 * Copyright (C) Villanova University 2011.
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
 * @author   Vaclav Rosecky <vufind-tech@lists.sourceforge.net>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_a_module Wiki
 */

require_once 'Action.php';
require_once 'sys/Language.php';
require_once 'services/MyResearch/MyResearch.php';
require_once 'RecordDrivers/Factory.php';
require_once 'services/MyResearch/lib/FavoriteHandler.php';

/**
 * Import of favourite items action for MyResearch module
 *
 * @category VuFind
 * @package  Controller_MyResearch
 * @author   Vaclav Rosecky <vufind-tech@lists.sourceforge.net>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_a_module Wiki
 */
class FavImport extends MyResearch
{
    public function launch()
    {
        global $configArray;
        if (isset($_POST['import'])) {
            $this->_importFavorites();
        }
        header('Location: ' . $configArray['Site']['url'] . '/MyResearch/Favorites?infoMsg=fav_import_success');
    }

    /**
     * Support method - display content inside a lightbox.
     *
     * @return void
     * @access private
     */
    private function _processLightbox()
    {
        global $interface;
        if (isset($_POST['import'])) {
            $this->_importFavorites();
        }
        $interface->assign('title', 'Favorite Import');
        return $interface->fetch('MyResearch/fav-import.tpl');
    }

    /**
     * Support method - display content outside of a lightbox.
     *
     * @return void
     * @access private
     */
    private function _processNonLightbox()
    {
        global $configArray;
        global $interface;
        global $user;
        $interface->setPageTitle(translate('Import Favorites'));
        $interface->assign('subTemplate', 'fav-import.tpl');
        $interface->setTemplate('view-alt.tpl');
        $interface->display('layout.tpl');
    }
    
    /**
     * 
     * 
     */
    private function _importFavorites() {
        global $interface;
        global $user;
        if ($patron = UserAccount::catalogLogin()) {
            if (PEAR::isError($patron)) {
                PEAR::raiseError($patron);
            }
            $favs = $this->catalog->getMyFavorites($patron);
            if (PEAR::isError($result)) {
                PEAR::raiseError($result);
            }
            foreach ($favs as $fav) {
                $resource = new Resource();
                $resource->record_id = $fav['id'];
                if ($resource->find(true) == 0) {
                    $resource->insert();
                }
                $userList = new User_list();
                $userList->user_id = $user->id;
                $userList->title = $fav['folder'];
                if ($userList->find(true) == 0) {
                    $userList->insert();
                }
                $userResource = new User_resource();
                $userResource->user_id = $user->id;
                $userResource->resource_id = $resource->id;
                $userResource->list_id = $userList->id;
                $userResource->notes = $fav['note'];
                if ($userResource->find(true) == 0) {
                    $userResource->insert();
                }
            }
        }
    }
}
?>