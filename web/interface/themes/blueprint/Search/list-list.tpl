{js filename="check_item_statuses.js"}
{js filename="check_save_statuses.js"}
{js filename="jquery.cookie.js"}
{js filename="cart.js"}
{js filename="openurl.js"}
{if $showPreviews}
{js filename="preview.js"}
{/if}

<form method="post" name="addForm" action="{$url}/Cart/Home">
  {* hide until complete
  <div class="bulkActionButtons">
    <noscript>
      <input type="submit" class="cartAdd" name="add" value="{translate text='Add selected items to cart'}"/>
    </noscript>
    <div id="cartSummary">
      <a title="{translate text='View cart'}" class="cart viewCart" href="{$url}/Cart/Home"><strong><span id="cartSize">0</span></strong> {translate text='items'}</a>
    </div>
    <div class="clear"></div>
  </div>
  *}
  <ul class="recordSet">
  {foreach from=$recordSet item=record name="recordLoop"}
    <li class="result{if ($smarty.foreach.recordLoop.iteration % 2) == 0} alt{/if}">
      <span class="recordNumber">{$recordStart+$smarty.foreach.recordLoop.iteration-1}</span>
      {* This is raw HTML -- do not escape it: *}
      {$record}
    </li>
  {/foreach}
  </ul>
</form>
