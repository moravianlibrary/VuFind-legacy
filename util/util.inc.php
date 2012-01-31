<?php
/**
 * Environment initialization for stand-alone utilities.
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
 * @link     http://vufind.org/wiki/directory_layout Wiki
 */

/**
 * Require System Libraries
 */
require_once 'PEAR.php';

// Sets global error handler for PEAR errors
PEAR::setErrorHandling(PEAR_ERROR_CALLBACK, 'utilErrorHandler');

// Set up the search path so we can include modules from the web folder while
// running inside the util folder.
$actualPath = dirname(__FILE__);
$pathToWeb = str_replace(
    array("/util", "\util"), array("/web", "\web"), $actualPath
);
$includePaths = explode(PATH_SEPARATOR, get_include_path());
$includePaths[] = realpath($pathToWeb);
$includePaths = array_unique($includePaths);
set_include_path(implode(PATH_SEPARATOR, $includePaths));

require_once 'sys/ConfigArray.php';
require_once 'sys/Autoloader.php';

spl_autoload_register('vuFindAutoloader');

/**
 * Callback function to handle PEAR errors in a command-line-friendly way.
 *
 * @param PEAR_Error $error The error object.
 *
 * @return void
 */
function utilErrorHandler($error)
{
    echo $error->getMessage() . "\n";
    exit(1);
}

?>