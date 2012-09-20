<?php
function smarty_function_hint($params, &$smarty)
{
    global $configArray;
    $url = $configArray['Site']['url'];
    if (isset($params['translate']) && $params['translate']) {
        $title = htmlspecialchars(translate($params['title']));
        if (isset($params['text'])) {
            $text = htmlspecialchars(translate($params['text']));
        }
    } else {
        $title = htmlspecialchars($params['title']);
        if (isset($params['text'])) {
            $text = $params['text'];
        }
    }
    $param = urlencode($params['text']);
    $lang = urlencode($smarty->getLanguage());
    $append = '';
    $element = 'span';
    if (isset($params['href'])) {
        $append = 'href="' . htmlspecialchars($params['href']) . '"';
        $element = 'a';
    }
    if (isset($params['text'])) {
        return "<$element class='jt' rel='$url/AJAX/MZKStatus?status=$title&lang=$lang' title='$title' $href>$text</$element>";
    } else {
        return "<$element class='jt' rel='$url/AJAX/MZKStatus?status=$title&lang=$lang' title='$title' $href/>";
    }
}
?>