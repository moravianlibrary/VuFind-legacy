<?php
/**
 * Smarty plugin
 * -------------------------------------------------------------
 * File:     function.useragent_match.php
 * Type:     function
 * Name:     useragent_match
 * -------------------------------------------------------------
 * Check user agent
 * @param array $params
 * @return number
 */
function smarty_function_useragent_match($params, &$smarty) {
    $result =  preg_match($params['pattern'], $_SERVER['HTTP_USER_AGENT']);
    $smarty->assign($params['var'], $result);
}

?>