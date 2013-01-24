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
        $urls = $this->getURLs();
        if (!empty($urls)) {
            $interface->assign('summURLs', $urls);
        }
        return 'RecordDrivers/Index/core-pc.tpl';
    }

    public function getSearchResult($view = 'list')
    {
        global $interface;
        $result = parent::getSearchResult($view);
        $interface->assign('filter', 'source:"ALL"');
        $interface->assign('coreEdition', $this->getEdition());
        $interface->assign('coreSeries', $this->getSeries());
        $interface->assign('summURLs', $this->getURLs());
        return 'RecordDrivers/Index/result-pc-' . $view . '.tpl';
    }
}
?>
