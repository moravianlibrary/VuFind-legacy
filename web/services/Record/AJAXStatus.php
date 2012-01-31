<?php

require_once 'Record.php';

class AJAXStatus extends Record
{
    
    /**
     * Process incoming parameters and display the page.
     *
     * @return void
     * @access public
     */
    public function launch()
    {
       $status = $_REQUEST['status'];
       $language = $_REQUEST['lang'];
       print $this->translateStatus($language, trim($status));
       //print "Lze půjčit absenčně domů na měsíc. Požadavky se zadávají elektronickou cestou.";
    }

    public function translateStatus($language, $status) {
       $statuses = $this->getStatuses($language);
       return $statuses[trim($status)];
    }

    public function getStatuses($language)
    {
       global $configArray;
       $result = array();
       $statuses = $configArray['Statuses']['File'];
       $contents = file($statuses);
       foreach($contents as $line) {
          list($status, $lang, $desc) = split('\\|', $line, 3);
          if ($language == trim($lang)) {
             $result[trim($status)] = trim($desc);
          }
       }
       return $result;
    }

}

?>
