<a id="addToFav" href="{$url}/Record/{$id|escape:'url'}/Save">{translate text='Add to favorites'}</a>
{if !empty($holdingURLs) || $holdingsOpenURL}
  <h3>{translate text="Internet"}</h3>
  {if !empty($holdingURLs)}
    {foreach from=$holdingURLs item=desc key=currentUrl name=loop}
      <a href="{if $proxy}{$proxy}/login?qurl={$currentUrl|escape:"url"}{else}{$currentUrl|escape}{/if}">{$desc|escape}</a><br/>
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
<table id="holdingsTable" cellpadding="2" cellspacing="0" border="0" class="citation" summary="{translate text='Holdings details from'} {translate text=$location}">
  {foreach from=$holding item=row}
  {if $row.availability}
  <tr class="available">
  {else}
  <tr class="checkedout">
  {/if}
    <th>{$row.status|translate|escape}</th>
    <th>
    {if $row.link}
        <a class="request" href="{$url}/Record/{$id|escape:'url'}/ExtendedHold?barcode={$row.item_id|escape:'url'}">
           {if $row.availability}
           {translate text="Order from stock"} 
           {else}
           {translate text="Reserve"}
           {/if}
        </a>
      {/if}
    </th>
  </tr>
  <tr>
  	<th>{translate text='due date'}</th>
  	<td>
  		{if $row.duedate}
          {$row.duedate|translate|escape}
        {else}
          {mzk_holdings_status status=$row.status duedate_status=$row.duedate_status}
        {/if}
  	</td>
  </tr>
  {if $row.collection_desc}
  <tr>
  	<th>{translate text='collection'}</th>
  	<td>{$row.collection_desc|escape}</td>
  </tr>
  {/if}
  {if $row.sig2}
  <tr>
  	<th>{hint title='location / second signature' text='location' translate=true}</th>
  	<td>{$row.sig2|escape}</td>
  </tr>
  {/if}
  {if $row.description}
  <tr>
  	<th>{translate text='description'}</th>
  	<td>{$row.description|escape}</td>
  </tr>
  {/if}
  {if $row.note}
  <tr>
  	<th>{translate text='note'}</th>
  	<td>{$row.note|escape}</td>
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