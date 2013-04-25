<?php
function smarty_function_hiddenFiltersFromCurrentUrl($params, &$smarty) {
    $exclude = $params['exclude'];
    $out .= '';
    foreach ($_GET as $paramName => $paramValue) {
        if (is_array($paramValue)) {
            foreach ($paramValue as $paramValue2) {
                $field = substr($paramValue2, 0, strpos($paramValue2, ':'));
                if (!in_array($field, $exclude)) {
                    $value = htmlspecialchars($paramValue2);
                    $out .= "<input type='hidden' name='{$paramName}[]' value='$value' />";
                }
            }
        } else if (strpos($paramName, $field) !== 0 && strpos($paramName, 'module') !== 0
            && strpos($paramName, 'action') !== 0 && strpos($paramName, 'page') !== 0) {
            $value = htmlspecialchars($paramValue);
            $out .= "<input type='hidden' name='$paramName' value='$value' />";
        }
    }
    return $out;
}
?>