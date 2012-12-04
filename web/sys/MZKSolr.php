<?php
/**
 * Solr HTTP Interface
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
 * @package  Support_Classes
 * @author   Andrew S. Nagy <vufind-tech@lists.sourceforge.net>
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/system_classes#index_interface Wiki
 */
require_once 'sys/Proxy_Request.php';
require_once 'sys/IndexEngine.php';
require_once 'sys/ConfigArray.php';
require_once 'sys/SolrUtils.php';

require_once 'services/MyResearch/lib/Change_tracker.php';

require_once 'XML/Unserializer.php';
require_once 'XML/Serializer.php';

/**
 * Solr HTTP Interface
 *
 * @category VuFind
 * @package  Support_Classes
 * @author   Andrew S. Nagy <vufind-tech@lists.sourceforge.net>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     http://vufind.org/wiki/system_classes#index_interface Wiki
 */
class MZKSolr extends Solr
{

    public function search($query, $handler = null, $filter = null, $start = 0,
        $limit = 20, $facet = null, $spell = '', $dictionary = null,
        $sort = null, $fields = null,
        $method = HTTP_REQUEST_METHOD_POST, $returnSolrError = false
    ) {
        global $configArray;
        $fields = "*";
        $filters = array();
        $default = true;
        if ($filter != null) {
            foreach ($filter as $element) {
                $addFilter = true;
                if (isset($configArray['MultiSolr'])) {
                    $element_norm = strtr($element, array('"' => '', "'" => ''));
                    foreach ($configArray['MultiSolr'] as $key => $value) {
                        if ($element_norm == $key) {
                            $this->host = $value . '/' . $this->core;
                            $default = false;
                            $addFilter = false;
                        }
                    }
                }
                if ($addFilter) {
                    $filters[] = $element;
                }
            }
        }
        if ($default && isset($configArray['MultiSolr']['default'])) {
            $this->host = $configArray['MultiSolr']['default'] . '/' . $this->core;
        }
        return parent::search($query, $handler, $filters, $start, $limit, $facet, $spell, $dictionary, $sort, $fields, $method, $returnSolrErrror);
    }

    public function alphabeticBrowse($source, $from, $page, $page_size = 20,
        $returnSolrError = false
    ) {
        $alephBrowse = new AlephBrowse();
        return $alephBrowse->alphabeticBrowse($source, $from, $page, $page_size, $returnSolrError);
    }

}
