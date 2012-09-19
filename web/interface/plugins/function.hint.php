<?php
function smarty_function_hint($params, &$smarty)
{
    global $configArray;
    $url = $configArray['Site']['url'];
    if (isset($params['translate']) && $params['translate']) {
        $title = htmlspecialchars(translate($params['title']));
    } else {
        $title = htmlspecialchars($params['title']);
    }
    $param = urlencode($params['text']);
    $lang = urlencode($smarty->getLanguage());
    $append = '';
    if (isset($params['href'])) {
        $append = 'href="' . htmlspecialchars($params['href']) . '"';
    }
    if (isset($params['text'])) {
        $text = htmlspecialchars($params['text']);
        return "<a class='jt' rel='$url/AJAX/MZKStatus?status=$title&lang=$lang' title='$title' $href>$text</a>";
    } else {
        return "<a class='jt' rel='$url/AJAX/MZKStatus?status=$title&lang=$lang' title='$title' $href/>";
    }
}
?>