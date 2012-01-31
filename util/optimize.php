<?php
/**
 * Command-line tool to optimize the Solr index.
 *
 * PHP version 5
 *
 * Copyright (C) Villanova University 2009.
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
 * @link     http://vufind.org/wiki/performance#index_optimization Wiki
 */
ini_set('memory_limit', '50M');
ini_set('max_execution_time', '3600');

/**
 * Set up util environment
 */
require_once 'util.inc.php';
require_once 'sys/ConnectionManager.php';

// Read Config file
$configArray = parse_ini_file(dirname(__FILE__) . '/../web/conf/config.ini', true);

// Setup Solr Connection -- Allow core to be specified as first command line param.
$solr = ConnectionManager::connectToIndex('Solr', isset($argv[1]) ? $argv[1] : '');

// Commit and Optimize the Solr Index
$solr->commit();
$solr->optimize();

?>