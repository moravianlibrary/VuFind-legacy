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
        $hasNext = false;
        $operator = ">=";
        $sort = 'asc';
        if ($page < 0) {
            $operator = "<";
            $sort = 'desc';
            $page = abs($page) - 1;
            $hasNext = true;
        }
        $type = $this->config[$source]['id'];
        $offset = (int) $page * $page_size;
        $limit = (int) $page_size;
        $from = strtoupper(strtr($from, $this->replace));
        $sql  = "SELECT sort_text AS sort_text, display_text AS display_text, "
              . " COUNT(id) AS count, GROUP_CONCAT(id) AS ids FROM browse"
              . " WHERE type = ? AND sort_text $operator ? GROUP BY sort_text ORDER BY sort_text $sort LIMIT ? OFFSET ?";
        $sql = "SELECT * FROM ($sql) AS a ORDER BY sort_text";
        $res = $this->db->query($sql, array($type, $from, $limit + 1, $offset));
        $items = array();
        while ($row = &$res->fetchRow()) {
            $heading = $this->getDisplayText($source, $row[1]);
            $items[] = array("heading" => $heading, "lookfor" => $row[0], "ids" => split(',', $row[3]), "count" => $row[2], "source" => $type);
        }
        if (count($items) == $limit + 1) {
            array_pop($items);
            $totalCount = $offset + $page_size + 1;
            $hasNext = true;
        } else {
            $totalCount = $page_size;
        }
        $first = $items[0]['heading'];
        $hasPrevious = $this->hasPrevious($type, $first); 
        $result = array(
            "totalCount" => $totalCount,
            "offset" => $offset,
            "startRow" => $from,
            "items" => $items,
            "hasNext" => $hasNext,
            "hasPrevious" => $hasPrevious
        );
        return array("Browse" => $result);
    }
    
    protected function hasPrevious($type, $from) {
        $sql = "SELECT COUNT(*) AS count  FROM browse WHERE type = ? AND sort_text < ?";
        $res = $this->db->query($sql, array($type, $from));
        $row = &$res->fetchRow();
        return ($row[0] > 0);
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
