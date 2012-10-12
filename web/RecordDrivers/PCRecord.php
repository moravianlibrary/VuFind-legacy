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
}
?>
