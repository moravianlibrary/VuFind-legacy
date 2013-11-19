<link rel="stylesheet" type="text/css" href="/interface/themes/mzk/css/calendar.css">
{if ( $order > 1)}
    <div class="error">{translate text="Item is requested. Your request sequence for this item is:"} {$order|escape}</div>
{/if}
{if $duedate}
    <div class="error">{translate text="On loan until"}: {$duedate|escape}</div>
{/if}
<div class="yui-skin-sam">
{if $error}
    <div class="error">{translate text=$error}</div>
{else}
<form class="std" method="post" action="{$url}{$formTargetPath|escape}" name="popupForm" id="puthold"
      onSubmit='PutHold(&quot;{$id|escape}&quot;, this.elements[&quot;to&quot;].value,
                this.elements[&quot;comment&quot;].value, this.elements[&quot;item&quot;].value,
                this.elements[&quot;location&quot;][this.elements[&quot;location&quot;].selectedIndex].value,
                {* Pass translated strings to Javascript -- ugly but necessary: *}
                {literal}{{/literal}sending: &quot;{translate text='sms_sending'}&quot;, 
                 success: &quot;{translate text='hold_success'}&quot;,
                 failure: &quot;{translate text='hold_failure'}&quot;{literal}}{/literal}
                ); return false;'>
  <input type="hidden" name="item" value="{$item|escape}">
  <table>
  <tr>
    <td>{translate text="Delivery location"}: </td>
    <td>
      <select name="location">
        {foreach from=$locations key=val item=details}
        <option value="{$val}">{$details|translate|escape}</option>
        {/foreach}
      </select>
    </td>
  </tr>
  <tr>
    <td>{translate text="Last interest date"}: </td>
    <td>
      <input class="text" type="text" name="to" value="{$last_interest_date|escape}" id="calendar" autocomplete="off">
    </td>
  </tr>
  <tr>
    <td>{translate text="Comment"}: </td>
    <td>
      <input class="text" type="text" name="comment">
    </td>
  </tr>
  <tr>
    <td></td>
    <td><input class="form-submit" type="submit" name="submit" value='{translate text="PutHold"}'></td>
  </tr>
  </table>
  <div id="cal1Container">
    {image src="transparent.gif" onload="putHoldInit();"}
  </div>
</form>
</div>
{/if}


