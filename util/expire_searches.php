<?php
/**
 * Command-line tool to clear unwanted entries from search history database table.
 *
 * PHP version 5
 *
 * Copyright (C) Villanova University 2010.
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
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/jira/browse/VUFIND-235 JIRA Ticket
 */

/**
 * Set up util environment
 */
require_once 'util.inc.php';
require_once 'services/MyResearch/lib/Search.php';
require_once 'sys/ConnectionManager.php';

// Use command line value as expiration age, or default to 2.
$daysOld = isset($argv[1]) ? intval($argv[1]) : 2;

// Die if we have an invalid expiration age.
if ($daysOld < 2) {
    die("Expiration age must be at least two days.\n");
}

// Retrieve values from configuration file
$configArray = readConfig();

// Setup Local Database Connection
ConnectionManager::connectToDatabase();

// Delete the expired searches -- this cleans up any junk left in the database
// from old search histories that were not caught by the session garbage collector.
$search = new SearchEntry();
$expired = $search->getExpiredSearches($daysOld);
if (empty($expired)) {
    die("No expired searches to delete.\n");
}
$count = count($expired);
foreach ($expired as $oldSearch) {
    $oldSearch->delete();
}
echo "{$count} expired searches deleted.\n";
?>