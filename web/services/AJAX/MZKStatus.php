<?php

require_once 'Action.php';

class MZKStatus extends Action
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
    }

    public function translateStatus($language, $status) {
       $statuses = $this->getStatuses($language);
       return $statuses[trim($status)];
    }

    public function getStatuses($language)
    {
       global $configArray;
       $result = array();
       $file = trim('conf/' . $configArray['Statuses']['File']);
       $contents = file($file);
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
