<div class="searchheader homepage"> <!-- <div class="searchheader homepage"> -->
   <div class="searchcontent">
	 <div class="login-box">
	{if is_array($allLangs) && count($allLangs) > 1}
		<form method="post" name="langForm" action="">
			<input type="hidden" name="mylang" id="mylang" />
		</form>
                {foreach from=$allLangs key=langCode item=langName}
                  {if $userLang != $langCode}
                     <a href="javascript:switch_lang('{$langCode}')"><img src="{$path}/interface/themes/mzk/images/{$langCode}.png"/>{translate text=$langName}</a>
                  {else}
                     <img src="{$path}/interface/themes/mzk/images/{$langCode}.png"/>{translate text=$langName}
                  {/if}
                {/foreach}
 	{/if}
          <div id="logoutOptions"{if !$user} style="display: none;"{/if}>
            <a class="register" href="{$path}/MyResearch/Home">{translate text="Your Account"}</a> |
            <!-- <a href="{$path}/MyResearch/Logout">{translate text="Log Out"}</a> -->
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
          {*
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
          *}
        </div>
        </div>
</div>

<div class="searchHome">
  <b class="btop"><b></b></b>
  <div class="searchHomeContent">
    <div align="center"> <a href="http://www.mzk.cz/"> <img src="{$path}/interface/themes/mzk/images/mzk_logo_large.gif" alt="VuFind"> </a> </div>
    
    <div class="searchHomeForm">
      {include file="Search/searchbox.tpl"}
      <script language="JavaScript" type="text/javascript">
         document.searchForm.lookfor.focus();
      </script>
    </div>

  </div>
</div>

<!--
{if $facetList}
  <div class="searchHomeBrowseHeader">
    {foreach from=$facetList item=details key=field}
      {* Special case: extra-wide header for call number facets: *}
      <div{if $field == "callnumber-first" || $field == "dewey-hundreds"} class="searchHomeBrowseExtraWide"{/if}>
        <h2>{translate text="home_browse"} {translate text=$details.label}</h2>
      </div>
    {/foreach}
    <br clear="all">
  </div>
  
  <div class="searchHomeBrowse">
    <div class="searchHomeBrowseInner">
      {foreach from=$facetList item=details key=field}
        {assign var=list value=$details.sortedList}
        {* Special case: single, extra-wide column for Dewey call numbers... *}
        <div{if $field == "dewey-hundreds"} class="searchHomeBrowseExtraWide"{/if}>
          <ul>
            {* Special case: two columns for LC call numbers... *}
            {if $field == "callnumber-first"}
              {foreach from=$list item=url key=value name="callLoop"}
                <li><a href="{$url|escape}">{$value|escape}</a></li>
                {if $smarty.foreach.callLoop.iteration == 10}
                  </ul>
                  </div>
                  <div>
                  <ul>
                {/if}
              {/foreach}
            {else}
              {assign var=break value=false}
              {foreach from=$list item=url key=value name="listLoop"}
                {if $smarty.foreach.listLoop.iteration > 12}
                  {if !$break}
                    <li><a href="{$path}/Search/Advanced"><strong>{translate text="More options"}...</strong></a></li>
                    {assign var=break value=true}
                  {/if}
                {else}
                  <li><a href="{$url|escape}">{$value|escape}</a></li>
                {/if}
              {/foreach}
            {/if}
          </ul>
        </div>
      {/foreach}
      <br clear="all">
    </div>
    <b class="gbot"><b></b></b>
  </div>
{/if}
-->
