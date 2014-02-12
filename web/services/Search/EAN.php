<?php

require_once 'Action.php';

/**
* OpenSearch action for Search module
*
* @category VuFind
* @package  Controller_Search
* @author   Erich Duda <erich.duda@mzk.cz>
*/
class EAN extends Action
{
	public function launch()
	{
		global $interface;
		
		$format = isset($_GET['format'])?$_GET['format']:"EAN_13";
		$type = $_GET['type'];
		
		if ($format != "EAN_13") {
			PEAR::raiseError("Unsupported format.");
		}
		
		if ($type != "ISBN") {
			$interface->assign('type', $type);
			$interface->setTemplate("wrongean.tpl");
			$interface->display("layout.tpl");
			return;
		}
		
		if (!isset($_GET['code'])) {
			PEAR::raiseError("Parameter code is not set.");
		}		
		$code = $_GET['code'];
		$prefix = substr($code, 0, 3);
		$isbn10 = $this->_computeCheckFig(substr($code, 3, -1));
		$isbn13 = $code;
		
		// Redirect to search query
		$host = $_SERVER['HTTP_HOST'];
		$query = "Search/Results?join=AND&bool0%5B%5D=OR&lookfor0%5B%5D=$isbn10&type0%5B%5D=ISN&lookfor0%5B%5D=$isbn13&type0%5B%5D=ISN&limit=20";
		header("Location: http://$host/$query");
	}
	
	/**
	 * Compute last figure in isbn 10.
	 * @param string $isbn10
	 */
	private function _computeCheckFig($isbn10) {
		
		$figures = str_split($isbn10);
		$sum = 0;
		$weight = 10;
		
		foreach ($figures as $fig) {
			$sum += intval($fig) * $weight--;	
		}
		
		return $isbn10.(11 - $sum % 11);
	}
}

?>