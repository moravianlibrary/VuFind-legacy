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
    {css media="screen" filename="thickbox.css"}
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
    {js filename="jquery-1.7.min.js"}
    {js filename="europeana.js"}
    {js filename="ajax.yui.js"}
    {js filename="thickbox-compressed.js"}
    {js filename="jquery.cluetip.min.js"}
    <script language="JavaScript" type="text/javascript">
    {literal}
    $(document).ready(function() {       
	   $('a.jt').cluetip({cluetipClass: 'jtip', dropShadow: true, hoverIntent: false, delayedClose: 5000, mouseOutClose: false });
           $('a.jt_sticky').cluetip({cluetipClass: 'jtip', dropShadow: true, hoverIntent: false, sticky: true,  closePosition: 'bottom'});
	});
    {/literal}
    </script>
    <!-- Begin of modifications for Obalky knih -->
    <script language="JavaScript" type="text/javascript">
      var cover_text = "{translate text='Cover'}";
      var content_text = "{translate text='TOC'}";
      {literal}
      function obalky_display_image_small(element, data) {
        href = data["cover_thumbnail_url"];
        if (href != undefined) {
          var img_format = '#' + $(element).attr('id') + "_format";
          $(img_format).remove();
          $(element).prepend("<a href='" + data["backlink_url"] + "'><img align='left' src='" + data["cover_medium_url"] + "' alt='" + cover_text + "' height='80' width='63'></img></a>");
        }
      }
      function obalky_display_image(element, data) {
        href = data["cover_thumbnail_url"];
        if (href != undefined) {
          var img_format = '#' + $(element).attr('id') + "_format";
          $(img_format).remove();
          $(element).prepend("<a href='" + data["backlink_url"] + "'><img align='left' src='" + data["cover_medium_url"] + "' alt='" + cover_text + "'></img></a>");
        }
      }
      {/literal}
    </script>
    <!-- End of modifications for Obalky knih  -->
    <script language="JavaScript" type="text/javascript">
      {literal}
      function switch_lang(lang) {
         document.langForm.mylang.value = lang;
         document.langForm.submit() ;
      }
      {/literal}
    </script>
  </head>

  <body onLoadDisabled="document.searchForm.lookfor.focus();">

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
				{if $module=="Summon" || $module=="WorldCat" || $module=="Authority" || $module=="EBSCO" || $module=="PCI"}
				  {include file="`$module`/searchbox.tpl"}
				{else}
				  {include file="Search/searchbox.tpl"}
				{/if}
			  {/if}
		  </div>
        <!--{/if}-->
		

		<div class="login-box">
	{if is_array($allLangs) && count($allLangs) > 1}
		<form method="post" name="langForm" action="">
			<input type="hidden" name="mylang" id="mylang" />
		</form>
                {foreach from=$allLangs key=langCode item=langName}
                  {if $userLang != $langCode}
                     <a href="javascript:switch_lang('{$langCode}')"><img src="{$path}/interface/themes/mzk/images/{$langCode}.png"/>{$langName}</a>
                  {else}
                     <img src="{$path}/interface/themes/mzk/images/{$langCode}.png"/>{translate text=$langName}
                  {/if}
                {/foreach}
 	{/if}
          <div id="logoutOptions"{if !$user} style="display: none;"{/if}>
            <a class="register" href="{$path}/MyResearch/Home">{translate text="Your Account"}</a> |
            <a class="login" href="{$path}/MyResearch/Logout">{translate text="Log Out"}</a>
          </div>
          <div id="loginOptions"{if $user} style="display: none;"{/if}>
            {if $authMethod == 'Shibboleth'}
              <a class="register" href="https://aleph.mzk.cz/cgi-bin/predregistrace/predregistrace.pl">{translate text="Registration"}</a>
              <a class="login" href="{$sessionInitiator}">{translate text="Login"}</a>
            {else}
              <a href="{$path}/MyResearch/Home">{translate text="Login"}</a>
            {/if}
          </div>
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

