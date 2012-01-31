#!/usr/bin/php
<?php
/**
 * Command-line tool FIXME
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
 * @author   Vaclav Rosecky <xrosecky@gmail.com>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 *
 */
ini_set('memory_limit', '50M');
ini_set('max_execution_time', '3600');

/**
 * Set up util environment
 */
require_once 'util.inc.php';

// Read Config file
$configArray = parse_ini_file(dirname(__FILE__) . '/../web/conf/Aleph.ini', true);

function parsetable($file, $callback) {
   $file_handle = fopen($file, "r, ccs=UTF-8");
   $rgxp = "";
   while (!feof($file_handle) ) {
      $line = fgets($file_handle);
      $line = chop($line);
      // $line = utf8_decode(chop($line));
      // print "$line\n";
      if (preg_match("/!!/", $line)) {
         $line = chop($line);
         $rgxp = regexp($line);
      // was preg_match
      } if (preg_match("/!.*/", $line) || $rgxp == "" || $line == "") {
         // comment
      } else {
        $line = str_pad($line, 80);
        $matches = "";
         if (preg_match($rgxp, $line, $matches)) {
            call_user_func($callback, $matches);
         }
      }
   }
   fclose($file_handle);
}


function regexp($string) {
   $string = preg_replace("/\\-/", ")\\s(", $string);
   $string = preg_replace("/!/", ".", $string);
   $string = preg_replace("/[<>]/", "", $string);
   $string = "/(" . $string . ")/";
   return $string;
}

$tab15 = array();
function tab15_callback($matches) {
   global $tab15;
   $lib = $matches[1];
   $no1 = $matches[2];
   if ($no1 == "##") $no1="";
   $no2 = $matches[3];
   if ($no2 == "##") $no2="";
   $desc = $matches[5];
   $loan = $matches[6];
   $request = $matches[8];
   $opac = $matches[10];
   $key = trim($lib) . "|" . trim($no1) . "|" . trim($no2);
   $tab15[trim($key)] = array( "desc" => trim($matches[5]), "loan" => $matches[6], "request" => $matches[8], "opac" => $matches[10] ); # was "opac" => $matches[10]
}
parsetable($configArray['util']['tab15'], "tab15_callback");

$tab40 = array();
function tab40_callback($matches) {
   global $tab40;
   $code = trim($matches[1]);
   $sub = trim($matches[2]);
   $sub = trim(preg_replace("/#/", "", $sub));
   $desc = trim($matches[4]);
   $key = $code . "|" . $sub;
   $tab40[trim($key)] = array( "desc" => $desc );
}
parsetable($configArray['util']['tab40'], "tab40_callback");

$tab_sub_library = array();
function tab_sub_library_callback($matches) {
   global $tab_sub_library;
   $sublib = trim($matches[1]);
   $desc = trim($matches[5]);
   $tab = trim($matches[6]);
   $tab_sub_library[$sublib] = array( "desc" => $desc, "tab15" => $tab );
   // print "DEBUG:$sublib\n";
}
parsetable($configArray['util']['tab_sub_library'], "tab_sub_library_callback");

$out = '<?php
function tab40_translate($collection, $sublib) {
   $table40 = TAB_40;
   $findme = $collection . "|" . $sublib;
   $desc =$table40[$findme];
   if ($desc == NULL) {
      $findme = $collection . "|";
      $desc = $table40[$findme];
   }
   return $desc;
}

function tab15_translate($slc, $isc, $ipsc) {
  $table_sub_library = TAB_SUB_LIBRARY;
  $table15 = TAB_15;
  $tab15 = $table_sub_library[$slc];
  if ($tab15 == NULL) {
     print "tab15 is null!<br>";
  }
  $findme = $tab15["tab15"] . "|" . $isc . "|" . $ipsc;
  $result = $table15[$findme];
  if ($result == NULL) {
     $findme = $tab15["tab15"] . "||" . $ipsc;
     $result = $table15[$findme];
  }
  $result["sub_lib_desc"] = $tab15["desc"];
  return $result;
}

?>
';

$charset = $configArray['util']['charset'];
$out = str_replace('TAB_SUB_LIBRARY', var_export($tab_sub_library, true), $out);
$out = str_replace('TAB_40', var_export($tab40, true), $out);
$out = str_replace('TAB_15', var_export($tab15, true), $out);
$conv =  iconv($charset, 'UTF-8', $out); 
$fh = fopen($configArray['util']['output'], 'w') or die("can't open file");
fwrite($fh, $conv);
fclose($fh);
//print $conv;

// tab15
$fh = fopen($configArray['util']['tab15_output'], 'w') or die("can't open file");
foreach ($tab15 as $key => $value) {
  $desc = iconv($charset, 'UTF-8', $value['desc']);
  $loan = $value['loan'];
  $request = $value['request'];
  $opac = $value['opac'];
  $out = "$key,$desc,$loan,$request,$opac";
  fwrite($fh, "$out\n");
}
fclose($fh);

// libraries
$fh = fopen($configArray['util']['libraries_output'], 'w') or die("can't open file");
foreach ($tab_sub_library as $key => $value) {
  $desc = iconv($charset, 'UTF-8', $value['desc']);
  $tab15 = $value['tab15'];;
  $out = "$key,$desc,$tab15";
  fwrite($fh, "$out\n");
}
fclose($fh);
