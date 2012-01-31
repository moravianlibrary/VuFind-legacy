<?php
class LazyMarcRecord {
    protected $record;
    protected $marcRecord;

    public function __construct($rec)
    {
        $this->record = $rec;
        $this->marcRecord = false;
    }

    public function load()
    {
        // Also process the MARC record:
        $marc = trim($this->record['fullrecord']);

        // check if we are dealing with MARCXML
        $xmlHead = '<?xml version';
        if (strcasecmp(substr($marc, 0, strlen($xmlHead)), $xmlHead) === 0) {
            $marc = new File_MARCXML($marc, File_MARCXML::SOURCE_STRING);
        } else {
            $marc = preg_replace('/#31;/', "\x1F", $marc);
            $marc = preg_replace('/#30;/', "\x1E", $marc);
            $marc = new File_MARC($marc, File_MARC::SOURCE_STRING);
        }

        $this->marcRecord = $marc->next();
        if (!$this->marcRecord) {
            PEAR::raiseError(new PEAR_Error('Cannot Process MARC Record'));
        }
    }

    public function getFields($field) {
        if (!$this->marcRecord) {
            $this->load();
        }
        return $this->marcRecord->getFields($field);
    }

    public function toXML() {
        if (!$this->marcRecord) {
            $this->load();
        }
        return $this->marcRecord->toXML($field);
    }

    public function getField() {
        if (!$this->marcRecord) {
            $this->load();
        }
        return $this->marcRecord->getField($field);
    }

    public function toRaw() {
        if (!$this->marcRecord) {
            $this->load();
        }
        return $this->marcRecord->getField($field);
    }

}
?>
