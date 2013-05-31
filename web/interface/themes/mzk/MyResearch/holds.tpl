<div id="bd">
  <div id="yui-main" class="content">
    <div class="yui-b first">
    <b class="btop"><b></b></b>
      {if $user->cat_username}
        <div class="resulthead"><h3>{translate text='Your Holds and Recalls'}</h3></div>
        <div class="page">

          {if $cancelForm}
            <form name="cancelForm" action="{$url|escape}/MyResearch/Holds" method="POST" id="cancelHold">
              <div class="toolbar">
                <ul>
                  <li><input type="submit" class="button holdCancel" name="cancelSelected" value="{translate text="hold_cancel_selected"}" onClick="return confirm('{translate text="confirm_hold_cancel_selected_text}')" /></li>
                  <li><input type="submit" class="button holdCancelAll" name="cancelAll" value="{translate text='hold_cancel_all'}" onClick="return confirm('{translate text="confirm_hold_cancel_all_text}')" /></li>
                  <li>{include file="MyResearch/switcher.tpl"}</li>
                </ul>
              </div>
            <div class="clearer"></div>
          {/if}

          {if $holdResults.success}
            <div class="holdsMessage"><p class="userMsg">{translate text=$holdResults.status}</p></div>
          {/if}

          {if $errorMsg}
             <div class="holdsMessage"><p class="error">{translate text=$errorMsg}</p></div>
          {/if}

          {if $cancelResults.count > 0}
            <div class="holdsMessage"><p class="userMsg">{$cancelResults.count|escape} {translate text="hold_cancel_success_items"}</p></div>
          {/if}

          {if is_array($recordList)}
            {if $currentView == 'table'}
              {include file="MyResearch/holds-table.tpl"}
            {else}
              {include file="MyResearch/holds-list.tpl"}
            {/if}
          {else}
            {translate text='You do not have any holds or recalls placed'}.
          {/if}
        {if $cancelForm}
          </form>
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
