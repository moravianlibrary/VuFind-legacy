<style type="text/css">
{literal}
.cluetip-outer {
  borderBottom: '1px solid #900';
  background-color: white;
  text-align: left;
  padding: 8px 8px;
}
.cluetip-title {
  color: black;
  font-size: 13px;
}
a.jt {
  cursor: help;
}
{/literal}
</style>

{if !empty($holdingsRestrictions)}
  <div class="warning">
  {foreach from=$holdingsRestrictions item=restriction}
    {$restriction|escape}
  {/foreach}
  </div>
{/if}
{if $itemLink}
   {if $itemLinkType == "LKR"}
      <a href="{$url}/Record/{$itemLink|escape:'url'}">{translate text='To place a hold, visit this record.'}<a/>
   {/if}
   {if $itemLinkType == "norms"}
      <a href="{$url}/Record/{$itemLink|escape:'url'}">{translate text='To see a valid version of this norm, visit this record.'}<a/>
   {/if}
{/if}
{if $driverMode && !empty($holdings)}
  {if $showLoginMsg}
    <div class="userMsg">
      <a href="{$path}/MyResearch/Home?followup=true&followupModule=Record&followupAction={$id}">{translate text="Login"}</a> {translate text="hold_login"}
    </div>
  {/if}
  {if $user && !$user->cat_username}
    {include file="MyResearch/catalog-login.tpl"}
  {/if}
{/if}

{if !empty($holdingURLs) || $holdingsOpenURL}
  <h3>{translate text="Internet"}</h3>
  {if !empty($holdingURLs)}
    {foreach from=$holdingURLs item=desc key=currentUrl name=loop}
      <a href="{if $proxy}{$proxy}/login?qurl={$currentUrl|escape:"url"}{else}{$currentUrl|escape}{/if}" target="blank">{$desc|escape}</a><br/>
    {/foreach}
  {/if}
  {if $holdingsOpenURL}
    {include file="Search/openurl.tpl" openUrl=$holdingsOpenURL}<br/>
  {/if}
  <br/>
{/if}
{if (!empty($holdingLCCN)||!empty($isbn)||!empty($holdingArrOCLC))}
  <span style="">
    <a class="{if $isbn}gbsISBN{$isbn}{/if}{if $holdingLCCN}{if $isbn} {/if}gbsLCCN{$holdingLCCN}{/if}{if $holdingArrOCLC}{if $isbn|$holdingLCCN} {/if}{foreach from=$holdingArrOCLC item=holdingOCLC name=oclcLoop}gbsOCLC{$holdingOCLC}{if !$smarty.foreach.oclcLoop.last} {/if}{/foreach}{/if}" style="display:none" target="_blank"><img src="https://www.google.com/intl/en/googlebooks/images/gbs_preview_button1.png" border="0" style="width: 70px; margin: 0;"/></a>    
    <a class="{if $isbn}olISBN{$isbn}{/if}{if $holdingLCCN}{if $isbn} {/if}olLCCN{$holdingLCCN}{/if}{if $holdingArrOCLC}{if $isbn|$holdingLCCN} {/if}{foreach from=$holdingArrOCLC item=holdingOCLC name=oclcLoop}olOCLC{$holdingOCLC}{if !$smarty.foreach.oclcLoop.last} {/if}{/foreach}{/if}" style="display:none" target="_blank"><img src="{$path}/images/preview_ol.gif" border="0" style="width: 70px; margin: 0"/></a> 
    <a id="HT{$id|escape}" style="display:none"  target="_blank"><img src="{$path}/images/preview_ht.gif" border="0" style="width: 70px; margin: 0" title="{translate text='View online: Full view Book Preview from the Hathi Trust'}"/></a>
  </span>
{/if}

{foreach from=$holdings item=holding key=location}
<h3>{translate text=$location}</h3>
<table cellpadding="2" cellspacing="0" border="0" class="citation" summary="{translate text='Holdings details from'} {translate text=$location}">
  <tr>
    <th>{translate text='item status'}</th>
    <th>{translate text='due date'}</th>
    <th>{translate text='sublibrary'}</th>
    <th>{translate text='collection'}</th>
    <th>{hint title='location' text='location' translate=true}</th>
    <th>{translate text='description'}</th>
    <th>{translate text='note'}</th>
  </tr>
  {foreach from=$holding item=row}
    {if $row.barcode != ""}
  <tr>
    <td>
      {hint title=$row.status translate=true}
      {if $row.availability} <!-- == "Y" -->
         <span class="available">{$row.status|translate|escape}</span>
      {else}
         <span class="checkedout">{$row.status|translate|escape}</span>
      {/if}
      {if $row.link}
        <a class="request" href="{$url}/Record/{$id|escape:'url'}/ExtendedHold?barcode={$row.item_id|escape:'url'}">
           {translate text="Place a Hold"}
        </a>
      {/if}
    </td>
    <td>
        {if $row.duedate}
          {$row.duedate|translate|escape}
        {else}
          {mzk_holdings_status status=$row.status duedate_status=$row.duedate_status}
        {/if}
    </td>
    <td>
        {$row.sub_lib_desc|escape}
    </td>
    <td>
        {hint title=$row.collection_desc translate=false text=$row.collection_desc}
    </td>
    <td>
        {$row.sig2|escape}
    </td>
    <td>
        {$row.description|escape}
    </td>
    <td>
        {$row.note|escape}
    </td>
  </tr>
    {/if}
  {/foreach}
</table>

{/foreach}

{if $history}
<h3>{translate text="Most Recent Received Issues"}</h3>
<ul>
  {foreach from=$history item=row}
  <li>{$row.issue|escape}</li>
  {/foreach}
</ul>
{/if}
