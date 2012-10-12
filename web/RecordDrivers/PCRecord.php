<?
require_once 'RecordDrivers/IndexRecord.php';
require_once 'RecordDrivers/MzkRecord.php';

class PCRecord extends IndexRecord
{

    public function getCoreMetadata()
    {
        global $interface;
        $result = parent::getCoreMetadata();
        $interface->assign('filter', 'source:"ALL"');
        return 'RecordDrivers/Index/core-pc.tpl';
    }

    public function getSearchResult($view = 'list')
    {
        global $interface;
        $result = parent::getSearchResult($view);
        $interface->assign('filter', 'source:"ALL"');
        $interface->assign('summURLs', $this->getURLs());
        return 'RecordDrivers/Index/result-pc-' . $view . '.tpl';
    }
}
?>
