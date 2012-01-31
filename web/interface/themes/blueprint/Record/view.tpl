{if !empty($addThis)}
<script type="text/javascript" src="https://s7.addthis.com/js/250/addthis_widget.js?pub={$addThis|escape:"url"}"></script>
{/if}

{js filename="record.js"}
{js filename="check_save_statuses.js"}
{if $showPreviews}
  {js filename="preview.js"}
{/if}
{if $coreOpenURL || $holdingsOpenURL}
  {js filename="openurl.js"}
{/if}

<div class="span-18">
  <div class="toolbar">
    <ul>
      <li><a href="{$url}/Record/{$id|escape:"url"}/Cite" class="citeRecord cite" id="citeRecord{$id|escape}" title="{translate text="Cite this"}">{translate text="Cite this"}</a></li>
      <li><a href="{$url}/Record/{$id|escape:"url"}/SMS" class="smsRecord sms" id="smsRecord{$id|escape}" title="{translate text="Text this"}">{translate text="Text this"}</a></li>
      <li><a href="{$url}/Record/{$id|escape:"url"}/Email" class="mailRecord mail" id="mailRecord{$id|escape}" title="{translate text="Email this"}">{translate text="Email this"}</a></li>
      {if is_array($exportFormats) && count($exportFormats) > 0}
      <li>
        <a href="{$url}/Record/{$id|escape:"url"}/Export?style={$exportFormats.0|escape:"url"}" class="export exportMenu">{translate text="Export Record"}</a>
        <ul class="menu offscreen" id="exportMenu">
        {foreach from=$exportFormats item=exportFormat}
          <li><a {if $exportFormat=="RefWorks"}target="{$exportFormat}Main" {/if}href="{$url}/Record/{$id|escape:"url"}/Export?style={$exportFormat|escape:"url"}">{translate text="Export to"} {$exportFormat|escape}</a></li>
        {/foreach}
        </ul>
      </li>
      {/if}
      <li id="saveLink"><a href="{$url}/Record/{$id|escape:"url"}/Save" class="saveRecord fav" id="saveRecord{$id|escape}" title="{translate text="Add to favorites"}">{translate text="Add to favorites"}</a></li>
      {if !empty($addThis)}
      <li id="addThis"><a class="addThis addthis_button"" href="https://www.addthis.com/bookmark.php?v=250&amp;pub={$addThis|escape:"url"}">{translate text='Bookmark'}</a></li>
      {/if}
    </ul>
    <div class="clear"></div>
  </div>

  <div class="record recordId" id="record{$id|escape}">
    {if $errorMsg || $infoMsg}
      <div class="messages">
      {if $errorMsg}<div class="error">{$errorMsg|translate}</div>{/if}
      {if $infoMsg}<div class="info">{$infoMsg|translate}</div>{/if}
      </div>
    {/if}
    {if $previousRecord || $nextRecord}
    <div class="resultscroller">
      {if $previousRecord}<a href="{$url}/Record/{$previousRecord}">&laquo; {translate text="Prev"}</a>{/if}
      #{$currentRecordPosition} {translate text='of'} {$resultTotal}
      {if $nextRecord}<a href="{$url}/Record/{$nextRecord}">{translate text="Next"} &raquo;</a>{/if}
    </div>
    {/if}

    {include file=$coreMetadata}
  </div>

  <div id="tabnav">
    <ul>
      <li{if $tab == 'Holdings' || $tab == 'Hold'} class="active"{/if}>
        <a href="{$url}/Record/{$id|escape:"url"}/Holdings#tabnav">{translate text='Holdings'}</a>
      </li>
      <li{if $tab == 'Description'} class="active"{/if}>
        <a href="{$url}/Record/{$id|escape:"url"}/Description#tabnav">{translate text='Description'}</a>
      </li>
      {if $hasTOC}
      <li{if $tab == 'TOC'} class="active"{/if}>
        <a href="{$url}/Record/{$id|escape:"url"}/TOC#tabnav">{translate text='Table of Contents'}</a>
      </li>
      {/if}
      <li{if $tab == 'UserComments'} class="active"{/if}>
        <a href="{$url}/Record/{$id|escape:"url"}/UserComments#tabnav">{translate text='Comments'}</a>
      </li>
      {if $hasReviews}
      <li{if $tab == 'Reviews'} class="active"{/if}>
        <a href="{$url}/Record/{$id|escape:"url"}/Reviews#tabnav">{translate text='Reviews'}</a>
      </li>
      {/if}
      {if $hasExcerpt}
      <li{if $tab == 'Excerpt'} class="active"{/if}>
        <a href="{$url}/Record/{$id|escape:"url"}/Excerpt#tabnav">{translate text='Excerpt'}</a>
      </li>
      {/if}
      <li{if $tab == 'Details'} class="active"{/if}>
        <a href="{$url}/Record/{$id|escape:"url"}/Details#tabnav">{translate text='Staff View'}</a>
      </li>
    </ul>
    <div class="clear"></div>
  </div>


  <div class="recordsubcontent">
    {include file="Record/$subTemplate"}
  </div>

  {* Add COINS *}
  <span class="Z3988" title="{$openURL|escape}"></span>
</div>

<div class="span-5 last">
  <div class="sidegroup">
    <h4>{translate text="Similar Items"}</h4>
    {if is_array($similarRecords)}
    <ul class="similar">
      {foreach from=$similarRecords item=similar}
      <li>
        {if is_array($similar.format)}
        <span class="{$similar.format[0]|lower|regex_replace:"/[^a-z0-9]/":""}">
        {else}
        <span class="{$similar.format|lower|regex_replace:"/[^a-z0-9]/":""}">
        {/if}
          <a href="{$url}/Record/{$similar.id|escape:"url"}">{$similar.title|escape}</a>
        </span>
        {if $similar.author}<br/>{translate text='By'}: {$similar.author|escape}{/if}
        {if $similar.publishDate} {translate text='Published'}: ({$similar.publishDate.0|escape}){/if}
      </li>
      {/foreach}
    </ul>
    {else}
      <p>{translate text='Cannot find similar records'}</p>
    {/if}
  </div>

  {if is_array($editions)}
  <div class="sidegroup">
    <h4>{translate text="Other Editions"}</h4>
    <ul class="similar">
      {foreach from=$editions item=edition}
      <li>
        {if is_array($edition.format)}
          <span class="{$edition.format[0]|lower|regex_replace:"/[^a-z0-9]/":""}">
        {else}
          <span class="{$edition.format|lower|regex_replace:"/[^a-z0-9]/":""}">
        {/if}
        <a href="{$url}/Record/{$edition.id|escape:"url"}">{$edition.title|escape}</a>
        </span>
        {$edition.edition|escape}
        {if $edition.publishDate}({$edition.publishDate.0|escape}){/if}
      </li>
      {/foreach}
    </ul>
  </div>
  {/if}
</div>

<div class="clear"></div>
