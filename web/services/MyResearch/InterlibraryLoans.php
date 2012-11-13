<?php
/**
 * InterLibraryLoans action for MyResearch module
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
 * InterLibraryLoans action for MyResearch module
 *
 * @category VuFind
 * @package  Controller_MyResearch
 * @author   Andrew S. Nagy <vufind-tech@lists.sourceforge.net>
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_a_module Wiki
 */
class InterlibraryLoans extends MyResearch
{

    public function __construct() {
        parent::__construct();
        /*
        $this->fields = array();
        $this->fields['book'] = array(
            array('type' => 'hidden', id => 'new', value => 'book'),
            array('type' => 'section', title => 'Basic information'),
            array('type' => 'text', 'required' => true, title => 'Author', id => 'author'),
            array('type' => 'text', 'required' => false, title => 'Other author', id => 'other_author'),
            array('type' => 'text', 'required' => true, title => 'Title', id => 'title'),
            array('type' => 'text', 'required' => false, title => 'Edition', id => 'edition'),
            array('type' => 'text', 'required' => false, title => 'Place of publication', id => 'place_of_publication'),
            array('type' => 'text', 'required' => false, title => 'Publisher', id => 'publisher'),
            array('type' => 'text', 'required' => true, title => 'Year of publication', id => 'year_of_publication'),
            array('type' => 'text', 'required' => false, title => 'ISBN', id => 'isbn'),
            array('type' => 'text', 'required' => false, title => 'Series', id => 'series'),
            array('type' => 'text', 'required' => false, title => 'Source', id => 'source'),
            array('type' => 'section', title => 'Information for copy'),
            array('type' => 'text', 'required' => false, title => 'Author of part', id => 'author_of_part'),
            array('type' => 'text', 'required' => false, title => 'Title of part', id => 'title_of_part'),
            array('type' => 'text', 'required' => false, title => 'Pages to photocopy', id => 'pages'),
            array('type' => 'textarea', 'required' => false, title => 'Free text note', id => 'note'),
            array('type' => 'section', title => 'Administration information'),
            array('type' => 'date', 'required' => true, title => 'Last interest date', id => 'last_interest_date'),
            array('type' => 'select', 'required' => true, id => 'media', title => 'Type of request', 'options' => array( '1' => 'copy', '2' => 'physical' )),
            array('type' => 'select', 'required' => true, id => 'media', title => 'Payment', 'options' => array( '1' => '50', '2' => '300', '3' => '600' )),
            array('type' => 'checkbox', 'required' => true, id => 'confirmation', title => 'Confirmation'),
            array('type' => 'button', id => 'submit', title => 'submit'),
        );
        */
    }

    protected function addFields(&$fields) {
        $fields[] = array('type' => 'button', id => 'submit', title => 'submit');
        $fields[] = array('type' => 'hidden', id => 'new', value => $type);
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

        if ($patron = UserAccount::catalogLogin()) {
            if (PEAR::isError($patron)) {
                PEAR::raiseError($patron);
            }
        }

        // Get My Transactions
        if (isset($_POST['new'])) {
            $type = $_GET['new'];
            $fields = $this->catalog->getInterlibraryLoanFields($patron, $type);
            $attrs = array();
            $error = false;
            foreach ($fields as &$field) {
                $id = $field['id'];
                if (isset($_POST[$id])) {
                    $value = $_POST[$id];
                    $field['value'] = $value;
                    if ($field['type'] != 'date') {
                       $attrs[$id] = $value;
                    } else {
                       list($day, $month, $year) = split("\.", $value);
                       $date = $year . str_pad($month, 2, "0", STR_PAD_LEFT) . str_pad($day, 2, "0", STR_PAD_LEFT);
                       $attrs[$id] = $date;
                    }
                } else {
                    if ($field['type'] == 'checkbox') {
                        $attrs[$id] = 'N';
                    }
                }
                if ($field['required']) {
                    if (isset($_POST[$field['id']])) {
                        $value = trim($_POST[$field['id']]);
                        if ($value == '') {
                            $field['error'] = true;
                            $error = true;
                        }
                    } else {
                        $field['error'] = true;
                        $error = true;
                    }
                }
            }
            if (!$error) {
                $this->catalog->createInterlibraryLoan($patron, $attrs);
            }
            $this->addFields($fields);
            $interface->assign('fields', $fields);
            $interface->setTemplate('ill-new.tpl');
        } else if (isset($_GET['new'])) {
            $type = $_GET['new'];
            $fields = $this->catalog->getInterlibraryLoanFields($patron, $type);
            $this->addFields($fields);
            $interface->assign('fields', $fields);
            $interface->setTemplate('ill-new.tpl');
        } else {
            $result = $this->catalog->getMyInterlibraryLoans($patron);
            if (PEAR::isError($result)) {
                PEAR::raiseError($result);
            }
            $interface->assign('ills', $result);
            $interface->setTemplate('ills.tpl');
        }
        $interface->setPageTitle('Interlibrary loans');
        $interface->display('layout.tpl');
    }
}
