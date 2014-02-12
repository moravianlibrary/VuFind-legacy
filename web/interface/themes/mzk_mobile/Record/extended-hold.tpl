<div class="pageitem myForm">
{if ( $order > 1)}
    <div class="error">{translate text="Item is requested. Your request sequence for this item is:"} {$order|escape}</div>
{/if}
{if $duedate}
    <div class="error">{translate text="On loan until"}: {$duedate|escape}</div>
{/if}
{if $error}
    <div class="error">{translate text=$error}</div>
{else}
<form method="post" action="{$url}{$formTargetPath|escape}" name="popupForm" id="puthold"
      onSubmit='writePutHoldDate()'>
  <input type="hidden" name="item" value="{$item|escape}">
  <div class="label">{translate text="Delivery location"}:</div>
  <select name="location">
  {foreach from=$locations key=val item=details}
  	<option value="{$val}">{$details|escape}</option>
  {/foreach}
  </select>
  <div class="label">{translate text="Last interest date"}:</div>
  <!-- <input type="text" name="to" value="{$last_interest_date|escape}" id="calendar" autocomplete="off"> -->
  <!-- <input class="text" type="text" name="to" value="{$last_interest_date|escape}" id="calendar" autocomplete="off"> -->
  {*<input type="date" value="{$last_interest_date|date_format:'%Y-%m-%d'|escape}" name="to" id="calendar"/>*}
  {html_select_date end_year='+3' prefix='PutHoldDate' field_order='DMY' month_format='%m'}
  <input type="hidden" name="to"/>
  
  <div class="label">{translate text="Comment"}:</div>
  <input class="text" type="text" name="comment">
  <input id="" type="submit" name="submit" value='{translate text="PutHold"}'>
  
  <div id="cal1Container">
    {image src="transparent.gif" onload="putHoldInit();"}
  </div>
</form>
{/if}
</div>


