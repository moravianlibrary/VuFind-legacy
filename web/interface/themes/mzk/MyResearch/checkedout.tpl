<div id="bd">
  <div id="yui-main" class="content">
    <div class="yui-b first">
    <b class="btop"><b></b></b>
        {if $user->cat_username}          
          <div class="page">
            <h3>{translate text='Your Checked Out Items'}</h3>
          {if $blocks}
            {foreach from=$blocks item=block}
              <p class="info">{translate text=$block}</p>
            {/foreach}
          {/if}
          
          {if $patron.checkedout_message}
            <p class="error">{$patron.checkedout_message|translate}</p>
          {/if}
          {if $transList}
            <form name="renewals" action="{$url}/MyResearch/CheckedOut" method="post" id="renewals">
              <div class="toolbar">
                <ul>
                  {if $renewForm}
                    <li><input type="submit" class="button renew" name="renewSelected" value="{translate text="renew_selected"}" /></li>
                    <li><input type="submit" class="button renewAll" name="renewAll" value="{translate text='renew_all'}" /></li>
                  {/if}
                  <li>&nbsp;&nbsp;{include file="MyResearch/switcher.tpl"}</li>
                </ul>
              </div>

            {if $errorMsg}
              <p class="error">{translate text=$errorMsg}</p>
            {/if}
              {if $currentView == 'table'}
                {include file="MyResearch/checkedout-table.tpl"}
              {else}
                {include file="MyResearch/checkedout-list.tpl"}
              {/if}
            </form>
          {else}
            {translate text='You do not have any items checked out'}.
          {/if}
        {else}
          <div class="page">
          {include file="MyResearch/catalog-login.tpl"}
        {/if}</div>

    <b class="bbot"><b></b></b>
    </div>
  </div>

  {include file="MyResearch/menu.tpl"}

</div>
