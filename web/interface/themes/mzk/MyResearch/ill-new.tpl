<div id="bd">
  <div id="yui-main" class="yui-skin-sam content">
    <div class="yui-b first">
    <b class="btop"><b></b></b>
      <div class="page yui-skin-sam">
        {if $user->cat_username}
          {if $ill_error}
            <p class="error">{$ill_error|translate}</p>
          {/if}
          {$form}
          {translate text="* denotes required fields"}          
        {/if}
      </div>
    </div>
    <div id="cal1Container">
      {image src="transparent.gif" onload="putHoldInit();"}
    </div>
  </div>
  
  {include file="MyResearch/menu.tpl"}
  
</div>
