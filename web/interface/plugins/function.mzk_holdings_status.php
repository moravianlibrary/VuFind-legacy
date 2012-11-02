<?php
function smarty_function_mzk_holdings_status($params, &$smarty)
{
    $status = mb_substr($params['status'], 0, 6, 'UTF-8');
    $duedate_status = $params['duedate_status'];
    return translate(mzk_translate_statuses($status, $duedate_status));
}

function mzk_translate_statuses($status, $duedate_status) {
    if ($duedate_status == 'On Shelf') {
        if ($status == 'Jen do' || $status == 'Studov') {
            return "present only";
        } else if ($status == 'Příruč') {
            return "reference";
        } else if ($status == 'Ve zpr') {
            return "";
        }
    }
    if ($status == '0 po r') {
        return 'lost';
    } else if ($status == 'Nenale' || $duedate_status == 'Hledá ') {
        return 'lost - wanted';
    } else if ($status == 'Vyříze') {
        return 'lost by reader';
    }
    return $duedate_status;
}
?>
