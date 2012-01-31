<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html lang="{$userLang}">

{* We should hide the top search bar and breadcrumbs in some contexts: *}
{if ($module=="Search" || $module=="Summon" || $module=="WorldCat" || $module=="Authority") && $pageTemplate=="home.tpl"}
    {assign var="showTopSearchBox" value=0}
    {assign var="showBreadcrumbs" value=0}
{else}
    {assign var="showTopSearchBox" value=1}
    {assign var="showBreadcrumbs" value=1}
{/if}

  <head>
    <title>{$pageTitle|truncate:64:"..."}</title>
    {if $addHeader}{$addHeader}{/if}
    <link rel="search" type="application/opensearchdescription+xml" title="Library Catalog Search" href="{$url}/Search/OpenSearch?method=describe">
    {css media="screen" filename="styles.css"}
    {css media="print" filename="print.css"}
    {css media="screen" filename="mzk.css"}
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8">
    <script language="JavaScript" type="text/javascript">
      path = '{$url}';
    </script>

    {js filename="yui/yahoo-dom-event.js"}
    {js filename="yui/connection-min.js"}
    {js filename="yui/datasource-min.js"}
    {js filename="yui/autocomplete-min.js"}
    {js filename="yui/dragdrop-min.js"}
    {js filename="scripts.js"}
    {js filename="rc4.js"}
    {js filename="calendar-min.js"}
    {js filename="calendar.js"}
    {js filename="obalkyknih/functions.js"}
    {js filename="obalkyknih/custom.js"}

    <!-- Begin of modifications for Obalky knih -->
    <script language="JavaScript" type="text/javascript">
      function obalky_display_image(img, data) {literal}{{/literal}
        href = data["cover_thumbnail_url"];
        if (href != undefined) {literal}{{/literal}
          img.setAttribute("src", data["cover_medium_url"]);
          img.onclick = function() {literal}{{/literal} window.location = data["backlink_url"]; {literal}}{/literal}
        {literal}}{/literal}
      {literal}}{/literal}
    </script>
    <!-- End of modifications for Obalky knih  -->

  </head>

  <body>

    {* LightBox *}
    <div id="lightboxLoading" style="display: none;">{translate text="Loading"}...</div>
    <div id="lightboxError" style="display: none;">{translate text="lightbox_error"}</div>
    <div id="lightbox" onClick="hideLightbox(); return false;"></div>
    <div id="popupbox" class="popupBox"><b class="btop"><b></b></b></div>
    {* End LightBox *}
    
    {if $showTopSearchBox}
	<div class="searchheader">
      <div class="searchcontent">
        <!--{if $showTopSearchBox}-->
          <div class="top-search-box">
			  <div class="logo">
				<a href="{$url}"><img src="{$path}/interface/themes/mzk/images/logo-mzk.png" alt="MZK"></a>
			  </div>
			  {if $pageTemplate != 'advanced.tpl'}
				{if $module=="Summon" || $module=="WorldCat" || $module=="Authority"}
				  {include file="`$module`/searchbox.tpl"}
				{else}
				  {include file="Search/searchbox.tpl"}
				{/if}
			  {/if}
		  </div>
        <!--{/if}-->
		
		<div class="login-box">
          <div id="logoutOptions"{if !$user} style="display: none;"{/if}>
            <a href="{$path}/MyResearch/Home">{translate text="Your Account"}</a> |
            <!-- <a href="{$path}/MyResearch/Logout">{translate text="Log Out"}</a> -->
            <a href="https://vufind-trunk.mzk.cz/Shibboleth.sso/Logout?return=https%3A%2F%2Fvufind-trunk.mzk.cz%2FMyResearch%2FLogout">{translate text="Log Out"}</a>
          </div>
          <div id="loginOptions"{if $user} style="display: none;"{/if}>
            {if $authMethod == 'Shibboleth'}
              <a href="https://aleph.mzk.cz/cgi-bin/predregistrace/predregistrace.pl">{translate text="Registration"}</a>
              <a href="{$sessionInitiator}">{translate text="Institutional Login"}</a>
            {else}
              <a href="{$path}/MyResearch/Home">{translate text="Login"}</a>
            {/if}
          </div>
          {if is_array($allLangs) && count($allLangs) > 1}
            <form method="post" name="langForm" action="">
              <div class="hiddenLabel"><label for="mylang">{translate text="Language"}:</label></div>
              <select id="mylang" name="mylang" onChange="document.langForm.submit();">
                {foreach from=$allLangs key=langCode item=langName}
                  <option value="{$langCode}"{if $userLang == $langCode} selected{/if}>{translate text=$langName}</option>
                {/foreach}
              </select>
              <noscript><input type="submit" value="{translate text="Set"}" /></noscript>
            </form>
          {/if}
        </div>
		
        <br clear="all">
      </div>
    </div>
	{/if}
    
    {if $showBreadcrumbs}
    <div class="breadcrumbs">
      <div class="breadcrumbinner">
        <a href="{$url}">{translate text="Home"}</a> <span>&gt;</span>
        {include file="$module/breadcrumbs.tpl"}
      </div>
    </div>
    {/if}
    
    <div id="doc2" class="yui-t4"> {* Change id for page width, class for menu layout. *}

      {if $useSolr || $useWorldcat || $useSummon}
      <div id="toptab">
        <ul>
          {if $useSolr}
          <li{if $module != "WorldCat" && $module != "Summon"} class="active"{/if}><a href="{$url}/Search/Results?lookfor={$lookfor|escape:"url"}">{translate text="University Library"}</a></li>
          {/if}
          {if $useWorldcat}
          <li{if $module == "WorldCat"} class="active"{/if}><a href="{$url}/WorldCat/Search?lookfor={$lookfor|escape:"url"}">{translate text="Other Libraries"}</a></li>
          {/if}
          {if $useSummon}
          <li{if $module == "Summon"} class="active"{/if}><a href="{$url}/Summon/Search?lookfor={$lookfor|escape:"url"}">{translate text="Journal Articles"}</a></li>
          {/if}
        </ul>
      </div>
      <div style="clear: left;"></div>
      {/if}

      {include file="$module/$pageTemplate"}

      <div id="ft">
      {include file="footer.tpl"}
      </div> {* End ft *}

    </div> {* End doc *}
    
  </body>
</html>

