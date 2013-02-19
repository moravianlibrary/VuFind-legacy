<div class="searchheader homepage"> <!-- <div class="searchheader homepage"> -->
  <div class="searchcontent">
    <div class="login-box">
      {if is_array($allLangs) && count($allLangs) > 1}
        <form method="post" name="langForm" action="">
          <input type="hidden" name="mylang" id="mylang" />
        </form>
        {foreach from=$allLangs key=langCode item=langName}
          {if $userLang != $langCode}
            <a href="javascript:switch_lang('{$langCode}')">{image src="$langCode.png"}{translate text=$langName}</a>
          {else}
            {image src="$langCode.png"}{translate text=$langName}
          {/if}
        {/foreach}
      {/if}
      <div id="logoutOptions"{if !$user} style="display: none;"{/if}>
        <a class="register" href="{$path}/MyResearch/Home">{translate text="Your Account"}</a> |
        <a class="login" href="{$path}/MyResearch/Logout">{translate text="Log Out"}</a>
      </div>
      <div id="loginOptions"{if $user} style="display: none;"{/if}>
        {if $authMethod == 'Shibboleth'}
          <a class="register" href="https://www.mzk.cz/registration_mzk">{translate text="Registration"}</a>
          <a class="login" href="{$sessionInitiator}">{translate text="Login"}</a>
        {else}
          <a href="{$path}/MyResearch/Home">{translate text="Login"}</a>
        {/if}
      </div>
    </div>
  </div>
</div>

<div class="searchHome">
  <b class="btop"><b></b></b>
  <div class="searchHomeContent">
    <div align="center"> <a href="http://www.mzk.cz/"> {image src="mzk_logo_large.gif" alt="VuFind"} </a> </div>    
    <div class="searchHomeForm">
      {include file="Search/searchbox.tpl"}
      <script language="JavaScript" type="text/javascript">
         document.searchForm.lookfor.focus();
      </script>
    </div>
  </div>
</div>
