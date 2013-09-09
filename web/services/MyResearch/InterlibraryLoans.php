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
require_once 'HTML/QuickForm2.php';

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
        $form = null;
        if (isset($_REQUEST['new'])) {
            $type = $_REQUEST['new'];
            if ($type == 'monography') {
                $form = $this->getILLFormForMonography();
            } else if ($type == 'serial') {
                $form = $this->getILLFormForSerial();
            }
        }
        if (isset($_GET['info'])) {
            $interface->assign('illMessage', 'Your ILL request has been submitted');
            $id = $_GET['id'];
            $interface->assign('illNewReqId', $id);
        }
        if (isset($_POST['new']) && $form != null) {
            $type = $_POST['new'];
            $attrs = $form->getValue();
            $hmac = $attrs['hmac'];
            if ($form->validate()) {
                if ($attrs['confirmation'] != 'true') {
                    $form->getElementById('confirmation')->setValue('true');
                    $confirmationText = $form->getElementById('confirmation_text');
                    $confirmationText->setContent('<b>' . $confirmationText->getContent() .  '</b>');
                    $form->toggleFrozen(true);
                    $interface->assign('form', $form);
                    $interface->setTemplate('ill-new.tpl');
                } else {
                    unset($attrs['submit']);
                    unset($attrs['ill_confirmation']);
                    $attrs['ill-unit'] = 'MVS';
                    $attrs['pickup-location'] = 'MVS';
                    $attrs['allowed-media'] = $attrs['media'];
                    $attrs['send-directly'] = 'N';
                    $attrs['delivery-method'] = 'S';
                    list($day, $month, $year) = split("\.", $attrs['last-interest-date']);
                    $attrs['last-interest-date'] = $year . str_pad($month, 2, "0", STR_PAD_LEFT) . str_pad($day, 2, "0", STR_PAD_LEFT);
                    $result = null;
                    if ($hmac == $this->getHMAC()) {
                        $result = $this->catalog->createInterlibraryLoan($patron, $attrs);
                    } else {
                        $result = array('success' => false, 'sysMessage' => 'Form has been tampered with');
                    }
                    if (!$result['success']) {
                        $interface->assign('form', $form);
                        $interface->assign('ill_error', $result['sysMessage']);
                        $interface->setTemplate('ill-new.tpl');
                    } else {
                        header("Location: " . $configArray['Site']['url'] . '/MyResearch/InterlibraryLoans?info=new&id=' . urlencode($result['id']));
                    }
                }
            } else {
                $interface->assign('form', $form);
                $interface->setTemplate('ill-new.tpl');
            }
        } else if (isset($_GET['new']) && $form != null) {
            $type = $_POST['new'];
            $interface->assign('form', $form);
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

    protected function getILLFormForMonography() {
        $textParams = array('size' => 32, 'maxlength' => 255);
        $form = new HTML_QuickForm2('ill', 'post',  array('action' => $configArray['Site']['url'] . '/MyResearch/InterlibraryLoans'));
        $form->addElement('hidden', 'new')->setValue('monography');
        // Information about book
        $book      = $form->addElement('fieldset')->setLabel(translate('ILL request for monography'));
        $author    = $book->addElement('text', 'author', $textParams)->setLabel(translate('author'));
        $author->addRule('required', translate('author is required'));
        $authors   = $book->addElement('text', 'additional_authors', $textParams)->setLabel(translate('additional_authors'));
        $title     = $book->addElement('text', 'title', $textParams)->setLabel(translate('title'));
        $title->addRule('required', translate('title is required'));
        // labels swapped
        $edition   = $book->addElement('text', 'edition', $textParams)->setLabel(translate('ill_edition'));
        $pubPlace  = $book->addElement('text', 'place-of-publication', $textParams)->setLabel(translate('publication place'));
        $publisher = $book->addElement('text', 'publisher', $textParams)->setLabel(translate('publisher'));
        $published = $book->addElement('text', 'year-of-publication', $textParams)->setLabel(translate('year of publication'));
        $published->addRule('required', translate('year of publication is required'));
        $isbn      = $book->addElement('text', 'isbn', $textParams)->setLabel('ISBN');
        $series    = $book->addElement('text', 'series', $textParams)->setLabel(translate('ill_series'));
        $source    = $book->addElement('text', 'source', $textParams)->setLabel(translate('source'));
        // Information about the part of the book
        $sub       = $form->addElement('fieldset')->setLabel(translate('ILL part of the monography'));
        $subAuthor = $sub->addElement('text', 'sub-author', $textParams)->setLabel(translate('ill_sub_author'));
        $subTitle  = $sub->addElement('text', 'sub-title', $textParams)->setLabel(translate('ill_sub_title'));
        $pages     = $sub->addElement('text', 'pages', $textParams)->setLabel(translate('ill_sub_pages'));
        $note      = $sub->addElement('text', 'note', $textParams)->setLabel(translate('ill_sub_note'));
        // Administration information
        $adm              = $form->addElement('fieldset')->setLabel(translate('ILL Administration information'));
        $lastInterestDate = $adm->addElement('text', 'last-interest-date', $textParams)->setLabel(translate('last-interest-date'));
        $lastInterestDate->setId('calendar');
        $lastInterestDate->addRule('required', translate('last interest date is required'));
        $media = $adm->addSelect('media')->setLabel(translate('mvs_media'))->loadOptions(array(
            'L-PRINTED' => translate('ill_loan'),
            'C-PRINTED' => translate('ill_photocopy')
        ));
        $this->addAuthorsRightsResctriction($form);
        $payment     = $form->addElement('fieldset')->setLabel(translate('ILL payment options'));
        $paymentType = $payment->addSelect('payment')->setLabel(translate("ill type"))->loadOptions(array(
            '50'   => translate('ILL request from Czech Republic'),
            '300'  => translate('ILL request from Europe'),
            '600'  => translate('ILL request from Great Britain or oversea')
        ));
        $this->addConfirmation($payment);
        $form->addElement('hidden', 'hmac')->setValue($this->getHMAC());
        // Submit button
        $form->addSubmit('submit', array('value' => translate('Submit')))->addClass('form-submit');
        $this->setDefaults($form);
        return $form;
    }

    protected function getILLFormForSerial() {
        $textParams = array('size' => 32, 'maxlength' => 255);
        $form = new HTML_QuickForm2('ill', 'post',  array('action' => $configArray['Site']['url'] . '/MyResearch/InterlibraryLoans'));
        $form->addElement('hidden', 'new')->setValue('serial');
        // Information about book
        $book      = $form->addElement('fieldset')->setLabel(translate('ILL request for serial'));
        $title     = $book->addElement('text', 'title', $textParams)->setLabel(translate('article title'));
        $title->addRule('required', translate('title is required'));
        $title->addRule('required', 'title is required');
        $issn      = $book->addElement('text', 'issn', $textParams)->setLabel('ISSN');
        $published = $book->addElement('text', 'year', $textParams)->setLabel(translate('year of publication'));
        $published->addRule('required', translate('year of publication is required'));
        $volume    = $book->addElement('text', 'volume', $textParams)->setLabel(translate('ill_volume'));
        $issue     = $book->addElement('text', 'issue', $textParams)->setLabel(translate('ill_issue'));
        $source     = $book->addElement('text', 'source', $textParams)->setLabel(translate('ill_source'));
        // Information about the article
        $sub           = $form->addElement('fieldset')->setLabel(translate('Article information'));
        $articleAuthor = $sub->addElement('text', 'sub-author', $textParams)->setLabel(translate('ill_article_author'));
        $articleTitle  = $sub->addElement('text', 'sub-title', $textParams)->setLabel(translate('ill_article_title'));
        $pages         = $sub->addElement('text', 'pages', $textParams)->setLabel(translate('ill_article_pages'));
        $note          = $sub->addElement('text', 'note', $textParams)->setLabel(translate('ill_article_note'));
        $media         = $sub->addSelect('media')->setLabel(translate('mvs_media'))->loadOptions(array(
            'C-COPY' => translate('ill_serial_photocopy'),
            'L-COPY' => translate('ill_serial_loan_physical'),
        ));
        $this->addAuthorsRightsResctriction($form);
        // Administration information
        $adm              = $form->addElement('fieldset')->setLabel(translate('ILL Administration information'));
        $lastInterestDate = $adm->addElement('text', 'last-interest-date', array('size' => 8, 'maxlength' => 8))->setLabel(translate('last-interest-date'))->setId('calendar');
        $lastInterestDate->addRule('required', translate('last interest date is required'));
        $payment     = $form->addElement('fieldset')->setLabel(translate('ILL payment options'));
        $paymentType = $adm->addSelect('payment')->setLabel(translate('Payment'))->loadOptions(array(
            '100-200'   => translate('ILL serial request from abroad'),
            'kopie ČR'  => translate('ILL serial request from Czech Republic'),
        ));
        $this->addConfirmation($payment);
        $form->addElement('hidden', 'hmac')->setValue($this->getHMAC());
        // Submit button
        $form->addSubmit('submit', array('value' => translate('Submit'), 'class' => 'form-submit'));
        $this->setDefaults($form);
        return $form;
    }

    protected function addAuthorsRightsResctriction($form) {
        $warning = $form->addElement('fieldset')->setLabel(translate('ILL author rights restriction'));
        $warning = $form->addElement('static')->setContent(translate('ILL author rights restriction text') . '<br></br>');
    }

    protected function addConfirmation($form) {
        $illConfirm  = $form->addCheckbox('ill_confirmation')->setContent(translate('ILL confirmation') . '<BR /><BR />')->setLabel('')->setId('confirmation_text');
        $illConfirm->addRule('required', translate('confirmation is required'));
        $confirmAfterSubmit = $form->addElement('hidden', 'confirmation')->setValue('false')->setId('confirmation');
    }

    protected function setDefaults($form) {
        $form->addDataSource(new HTML_QuickForm2_DataSource_Array(array(
            'last-interest-date' => date('j.n.Y', strtotime('+1 month'))
        )));
    }

    protected function getHMAC() {
        return hash_hmac('md5', session_id(), $configArray['Security']['HMACkey']);
    }
}
