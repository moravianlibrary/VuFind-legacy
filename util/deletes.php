<?php
/**
 * Command-line tool to batch-delete records from the Solr index.
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
 * @package  Utilities
 * @author   Andrew S. Nagy <vufind-tech@lists.sourceforge.net>
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/automation Wiki
 */

// Parse the command line parameters -- see if we are in "flat file" mode,
// find out what file we are reading in, and determine the index we are affecting!
$filename = isset($argv[1]) ? $argv[1] : null;
$mode = isset($argv[2]) ? $argv[2] : 'marc';
$index = isset($argv[3]) ? $argv[3] : 'Solr';

// No filename specified?  Give usage guidelines:
if (empty($filename)) {
    echo "Delete records from VuFind's index.\n\n";
    echo "Usage: deletes.php [filename] [format]\n\n";
    echo "[filename] is the file containing records to delete.\n";
    echo "[format] is the format of the file -- it may be one of the following:\n";
    echo "\tflat - flat text format (deletes all IDs in newline-delimited file)\n";
    echo "\tmarc - binary MARC format (delete all record IDs from 001 fields)\n";
    echo "\tmarcxml - MARC-XML format (delete all record IDs from 001 fields)\n";
    echo '"marc" is used by default if no format is specified.' . "\n";
    die();
}

// File doesn't exist?
if (!file_exists($filename)) {
    die("Cannot find file: {$filename}\n");
}

/**
 * Set up util environment
 */
require_once 'util.inc.php';        // set up util environment
require_once 'sys/ConnectionManager.php';

// Read Config file
$configArray = parse_ini_file(dirname(__FILE__) . '/../web/conf/config.ini', true);

// Setup Solr Connection
$solr = ConnectionManager::connectToIndex($index);

// Setup Local Database Connection
ConnectionManager::connectToDatabase();

// Count deleted records:
$i = 0;

// Flat file mode:
if ($mode == 'flat') {
    $ids = explode("\n", file_get_contents($filename));
    foreach ($ids as $id) {
        $id = trim($id);
        if (!empty($id)) {
            $solr->deleteRecord($id);
            $i++;
        }
    }
} else {
    // MARC file mode...

    // We need to load the MARC record differently if it's XML or binary:
    if ($mode == 'marcxml') {
        include_once 'File/MARCXML.php';
        $collection = new File_MARCXML($filename);
    } else {
        include_once 'File/MARC.php';
        $collection = new File_MARC($filename);
    }

    // Once the record is loaded, the rest of the logic is always the same:
    while ($record = $collection->next()) {
        $idField = $record->getField('001');
        $id = (string)$idField->getData();
        $solr->deleteRecord($id);
        $i++;
    }
}

// Commit and Optimize if necessary:
if ($i) {
    $solr->commit();
    $solr->optimize();
}
?>