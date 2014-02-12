<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <meta content="yes" name="apple-mobile-web-app-capable" />
    <meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
    <meta content="minimum-scale=1.0, width=device-width, maximum-scale=0.6667, user-scalable=no" name="viewport" />
	{*<link href="{$path}/interface/themes/mzk_mobile/iWebKit/css/style.css" rel="stylesheet" type="text/css" />*}
    {*css filename="../iWebKit/css/style.css"*}
    {css filename="style.css"}
    {css filename="extra_styles.css"}
    {*js filename="../iWebKit/javascript/functions.js"*}
    {*js filename="scripts.js"*}
    {js filename="myscripts.js"}
    <title>{$site.title}</title>
    {if $module == "EBSCO"}
    	{css filename = "ebsco.css"}
    {/if}
</head>


<body>
    <div id="topbar">
      {if !($module == "Search" && $pageTemplate == "home.tpl")}
      <div id="leftnav"><a href={if $module == 'EBSCO'}"{$path}/EBSCO/Search"{else}"{$path}/Search/Home"{/if}><img alt="home" src="{$path}/interface/themes/mzk_mobile/iWebKit/images/home.png" /></a></div>
      {/if}
      <div id="rightnav">
      {if is_array($allLangs) && count($allLangs) > 1}
		<form method="post" name="langForm" action="">
			<input type="hidden" name="mylang" id="mylang" />
		</form>
                {foreach from=$allLangs key=langCode item=langName}
                  {if $userLang != $langCode}
                     <a href="javascript:switch_lang('{$langCode}')">{image src="$langCode.png"}</a>
                  {/if}
                {/foreach}
 	{/if}
 	</div>
      <div id="title" {if $module == "Record"}{/if}>{$pageTitle}</div>
    </div>
    {*$module}/{$pageTemplate*}
    <div id="content">
      {include file="$module/$pageTemplate"}
    </div>
    <div id="footer">
      <a href="?ui=standard">{translate text="Go to Standard View"}</a>
    </div>
  </body>

</html>
