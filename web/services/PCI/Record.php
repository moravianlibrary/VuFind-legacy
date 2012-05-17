<?php
/**
 * Record action for Summon module
 *
 * PHP version 5
 *
 * Copyright (C) Andrew Nagy 2008.
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
 * @package  Controller_Summon
 * @author   Andrew Nagy <vufind-tech@lists.sourceforge.net>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_a_module Wiki
 */
require_once 'Base.php';

require_once 'sys/SearchObject/PCI.php';

require_once 'services/MyResearch/lib/User.php';
require_once 'services/MyResearch/lib/Resource.php';
require_once 'services/MyResearch/lib/Resource_tags.php';
require_once 'services/MyResearch/lib/Tags.php';

/**
 * Record action for Summon module
 *
 * @category VuFind
 * @package  Controller_Summon
 * @author   Andrew Nagy <vufind-tech@lists.sourceforge.net>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_a_module Wiki
 */
class Record extends Base
{
    protected $record;

    /**
     * Constructor
     *
     * @access public
     */
    public function __construct()
    {
        global $interface;
        global $configArray;

        // Call parent constructor
        parent::__construct();

        // Fetch Record
        $pci = new SearchObject_PCI();
        $record = $pci->getRecord($_REQUEST['id']);
          

        // Set Proxy URL
        $interface->assign(
            'proxy', isset($configArray['EZproxy']['host'])
            ? $configArray['EZproxy']['host'] : false
        );

        // Send record ID to template
        $interface->assign('id', $_REQUEST['id']);
        
        // Send record data to template
        $interface->assign('record', $record);        

    }

    /**
     * Process parameters and display the page.
     *
     * @return void
     * @access public
     */
    public function launch()
    {
        global $interface;

        // Assign the ID of the last search so the user can return to it.
        $interface->assign('lastsearch',
            isset($_SESSION['lastSearchURL']) ? $_SESSION['lastSearchURL'] : false
        );     
        
        // Display Page
        $interface->setTemplate('xmlrecord.tpl');
        $interface->display('layout.tpl');
    }
}

?>
