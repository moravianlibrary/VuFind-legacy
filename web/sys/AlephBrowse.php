<?php
/**
 * Solr Search Object class
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
 * @package  SearchObject
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/building_a_search_object Wiki
 */
require_once 'sys/Proxy_Request.php';   // needed for constant definitions
require_once 'sys/SearchObject/Base.php';
require_once 'RecordDrivers/Factory.php';

/**
 * MZKBrowse module
 *
 * @category VuFind
 * @package  Support_Classes
 * @author   Václav Rosecký <vufind-tech@lists.sourceforge.net>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/system_classes#index_interface Wiki
 */
class AlephBrowse
{

    public function __construct()
    {
        global $configArray;
        $this->config = $config = getExtraConfigArray('browse');
        $this->limit = (int) $this->config['Global']['limit'];
        $this->db = &DB::connect($configArray['Database']['database']);
        $this->db->query("SET NAMES UTF8;");
        $this->replace = array(
            "Č" => "C|", "Š" => "S|", "Ř" => "R|", "Ž" => "Z|",
            "č" => "C|", "š" => "S|", "ř" => "R|", "ž" => "Z|",
            "CH" => 'H|', "ch" => "h|"
        );
    }

    public function alphabeticBrowse($source, $from, $page, $page_size = 20,
        $returnSolrError = false
    ) { 
        $type = $this->config[$source]['id'];
        $offset = (int) $page * $page_size;
        $limit = (int) $page_size;
        if ($offset < 0) {
            $offset = 0;
        }
        $from = strtoupper(strtr($from, $this->replace));
        $sql  = "SELECT sort_text AS sort_text, display_text AS display_text, "
              . " COUNT(id) AS count, GROUP_CONCAT(id) AS ids FROM browse"
              . " WHERE type = ? AND sort_text >= ? GROUP BY sort_text ORDER BY sort_text LIMIT ? OFFSET ?";
        $res = $this->db->query($sql, array($type, $from, $limit + 1, $offset));
        $items = array();
        while ($row = &$res->fetchRow()) {
            $heading = $this->getDisplayText($source, $row[1]);
            $items[] = array("heading" => $heading, "lookfor" => $row[0], "ids" => split(',', $row[3]), "count" => $row[2], "source" => $type);
        }
        if (count($items) == $limit + 1) {
            array_pop($items);
            $totalCount = $offset + $page_size + 1;
        } else {
            $totalCount = $page_size;
        }
         // $row[0] - $offset;
        $result = array("totalCount" => $totalCount, "offset" => $offset, "startRow" => $from, "items" => $items);
        return array("Browse" => $result);
    }

    protected function getDisplayText($source, $text) {
        $display = $this->config[$source]['display'];
        $heading = "";
        foreach (str_split($display) as $field) {
            $matches = array();
            $regex = "/\\\$\\\$$field([^\\$]+)(?::\\\$\\\$)?/";
            if (preg_match($regex, $text, &$matches)) {
                $heading .= $matches[1] . " ";
            }
        }
        return $heading;
    }

    public function getDocuments($source, $index) {
        $ids = array();
        $sql = "SELECT id AS id FROM browse WHERE type = ? AND sort_text = ? LIMIT ?";
        $res = $this->db->query($sql, array($source, $index, $this->limit));
        while ($row = &$res->fetchRow()) {
            $ids[] = $row[0];
        }
        return array ("ids" => $ids, "display" => $index);
    }

}

?>
