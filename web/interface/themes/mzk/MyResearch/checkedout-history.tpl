<div id="bd">
  <div id="yui-main" class="content">
    <div class="yui-b first">
    <b class="btop"><b></b></b>
    
        {if $user->cat_username}          
          <div class="page">
		  <h3>{translate text='Your Checked Out History'}</h3>
          {if $blocks}
            {foreach from=$blocks item=block}
              <p class="info">{translate text=$block}</p>
            {/foreach}
          {/if}
          
          {if $transList}
              {include file="MyResearch/switcher-with-limit.tpl"}
              {if $currentView == 'table'}
                {include file="MyResearch/checkedout-history-table.tpl"}
              {else}
                {include file="MyResearch/checkedout-history-list.tpl"}
              {/if}
          {else}
            {translate text='You do not have any items checked out'}.
          {/if}
          </div>
        {else}
          <div class="page">
          {include file="MyResearch/catalog-login.tpl"}
          </div>
        {/if}

    <b class="bbot"><b></b></b>
    </div>
  </div>

  {include file="MyResearch/menu.tpl"}

</div>
