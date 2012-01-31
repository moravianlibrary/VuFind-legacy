<?php
/**
 * Command-line tool to delete suppressed records from the index.
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
ini_set('memory_limit', '50M');
ini_set('max_execution_time', '3600');

/**
 * Set up util environment
 */
require_once 'util.inc.php';
require_once 'sys/ConnectionManager.php';

// Read Config file
$configArray = parse_ini_file('../web/conf/config.ini', true);

// Setup Solr Connection
$solr = ConnectionManager::connectToIndex('Solr');

// Make ILS Connection
$catalog = ConnectionManager::connectToCatalog();

// Setup Local Database Connection
ConnectionManager::connectToDatabase();

// Get Suppressed Records and Delete from index
if ($catalog && $catalog->status) {
    $result = $catalog->getSuppressedRecords();
    if (!PEAR::isError($result)) {
        $status = $solr->deleteRecords($result);
        if ($status) {
            // Commit and Optimize
            $solr->commit();
            $solr->optimize();
        }
    }
} else {
    echo "Cannot connect to ILS.\n";
}
?>