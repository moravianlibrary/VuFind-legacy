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

