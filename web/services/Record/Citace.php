<?php

require_once 'Record.php';

/**
 * Support for citace.com
 *
 * @category VuFind
 * @package  Controller_Record
 * @author   Václav Rosecký <xrosecky@gmail.com>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_a_module Wiki
 */
class Citace extends Record
{
    /**
     * Process incoming parameters and display the page.
     *
     * @return void
     * @access public
     */
    public function launch()
    {
        global $interface;
        global $interface;
        global $configArray;

        // Do not cache holdings page
        $interface->caching = 0;
        $interface->setPageTitle(
            translate('Cite') . ': ' . $this->recordDriver->getBreadcrumb()
        );
        $interface->assign('subTemplate', 'citace.tpl');
        $baseURL = $configArray['Citace']['url'];
        $id = $this->recordDriver->getUniqueID();
        list($base, $sysno) = split("-", $id);
        $interface->assign('citace', array('url' => $baseURL . $sysno));
        $interface->setTemplate('view.tpl');
        // Display Page
        $interface->display('layout.tpl');
    }
}

?>