<div id="record{$summId|escape}" class="yui-ge">

  <div class="yui-u first" _id="obalka_{$summId}">
    <table align="left">
    <tr>
    <td>
      {if $bookBag}
        <!--<label for="checkbox_{$summId|regex_replace:'/[^a-z0-9]/':''|escape}" class="offscreen">{translate text="Select this record"}</label>-->
        <input id="checkbox_{$summId|regex_replace:'/[^a-z0-9]/':''|escape}" type="checkbox" name="ids[]" value="{$summId|escape}" class="checkbox_ui"/>
        <input type="hidden" name="idsAll[]" value="{$summId|escape}" />
      {/if}
    </td>
    <td id="obalka_{$summId}">{image id="obalka_`$summId`_format" src="formats/`$summFormats[0]`.png"
    title="$summFormats[0]"|translate align="left"}</td>
    </tr>
    <tr>
    <td></td>
    <td class="format">{translate text=$summFormats[0]}</td>
    </tr>
    </table>

    <!-- Begin of modifications for Obalky knih -->
      <!-- <span id="obalka_{$summId}"></span> -->
      <script>
        $(document).ready(function() {ldelim}
          var bibinfo = {literal}{{/literal} 
            "authors": [ "{$summAuthor}" ],
            "isbn": "{$summISBN}",
            "issn": "{$summISSN}",
            "title": "{$summTitle}"
          {rdelim};
          var permalink = "{$url}/Record/{$summId|escape:'url'}";
          obalky.process("obalky_display_thumbnail", "obalka_{$summId}", permalink, bibinfo);
        {rdelim});
      </script>
      <!--<img src="{$path}/bookcover.php" class="alignleft" alt="{translate text='Cover Image'}" id="obalka_{$summId}" height="80" width="63"/>-->
    
    {*
    {if $summThumb}
    <img src="{$summThumb|escape}" class="alignleft" alt="{translate text='Cover Image'}"/>
    {else}
    <img src="{$path}/bookcover.php" class="alignleft" alt="{translate text='No Cover Image'}"/>
    {/if}
    *}
    <!-- End of modifications for Obalky knih -->
    {if $summFormats[0] == "WEB"}
    <div class="resultitem">
      <div class="resultItemLine1">
         {foreach from=$summURLs key=recordurl item=urldesc}
         <a href="{$recordurl}" class="title">{$summTitle|truncate:180:"..."|escape}</a>
         {/foreach}
      </div>
      <br/>
      <span class="iconlabel electronic">{translate text='Web site'}</span>
    </div>    
    {else}
    <div class="resultitem">
      <div class="resultItemLine1">
      <a href="{$url}/Record/{$summId|escape:"url"}" class="title">{if !empty($summHighlightedTitle)}{$summHighlightedTitle|addEllipsis:$summTitle|highlight}{elseif !$summTitle}{translate text='Title not available'}{else}{$summTitle|truncate:180:"..."|escape}{/if}</a>
      {*<a href="{$url}/Record/{$summId|escape:"url"}" class="title">{if !$summTitle}{translate text='Title not available'}{else}{$summTitle|truncate:180:"..."|escape}{/if}</a>*}
      </div>

      <div class="resultItemLine2">
      {if !empty($summAuthor)}
      {translate text='by'}
      <a href="{$url}/Author/Home?author={$summAuthor|escape:"url"}&filter={$filter|escape:"url"}">{if !empty($summHighlightedAuthor)}{$summHighlightedAuthor|highlight}{else}{$summAuthor|escape}{/if}</a>
      {/if}
      {if $summDate}{translate text='Published'} {$summDate.0|escape}.{/if} {if $validity}{$validity|escape}{/if}
      </div>

      <div class="resultItemLine3">
      {if !empty($coreEdition)}
      {translate text='Edition'}: {$coreEdition|escape}<br />
      {/if}
      {if !empty($coreSeries)}
      {translate text='Series'}: {$coreSeries[0]|escape}
      {/if}
      </div>
      
      <div class="resultItemLine4">
      {if $summOpenUrl || !empty($summURLs)}
        {if $summOpenUrl}
          <br>
          {include file="Search/openurl.tpl" openUrl=$summOpenUrl}
        {/if}
        {foreach from=$summURLs key=recordurl item=urldesc}
          <br><a href="{if $proxy}{$proxy}/login?qurl={$recordurl|escape:"url"}{else}{$recordurl|escape}{/if}" class="fulltext" target="new">{if $recordurl == $urldesc}{translate text='Online'}{else}{$urldesc|escape}{/if}</a>
        {/foreach}
      {elseif $summAjaxStatus}
        <div class="status">
          <b>{translate text='Loan type'}:</b> {translate text=$status}</span>
        </div>
        {if $status != "no items"}
        <div class="status" id="status{$itemLink|escape}"> {*was {$summId|escape} *}
          <span class="unknown" style="font-size: 8pt;">{translate text='Loading'}...</span>
        </div>
        {/if}
      {/if}
        <div style="display: none;" id="locationDetails{$summId|escape}">&nbsp;</div>
      </div>
      <!--
      {foreach from=$summFormats item=format}
        <span class="iconlabel {$format|lower|regex_replace:"/[^a-z0-9]/":""}">{translate text=$format}</span>
      {/foreach}
      -->
    </div>
    {/if}
  </div>

  <div class="yui-u">
    <div id="saveLink{$summId|escape}">
      <a href="{$url}/Record/{$summId|escape:"url"}/Save" onClickDisabled="getLightbox('Record', 'Save', '{$summId|escape}', '', '{translate text='Add to favorites'}', 'Record', 'Save', '{$summId|escape}'); return false;" class="fav tool">{translate text='Add to favorites'}</a>
      <ul id="lists{$summId|escape}"></ul>
      <script language="JavaScript" type="text/javascript">
        getSaveStatuses('{$summId|escape:"javascript"}');
      </script>
    </div>
    {if $showPreviews}
      {if (!empty($summLCCN)|!empty($summISBN)|!empty($summOCLC))}
        {if $showGBSPreviews}      
          <div class="previewDiv"> 
            <a class="{if $summISBN}gbsISBN{$summISBN}{/if}{if $summLCCN}{if $summISBN} {/if}gbsLCCN{$summLCCN}{/if}{if $summOCLC}{if $summISBN|$summLCCN} {/if}{foreach from=$summOCLC item=OCLC name=oclcLoop}gbsOCLC{$OCLC}{if !$smarty.foreach.oclcLoop.last} {/if}{/foreach}{/if}" style="display:none" target="_blank">
              <img src="https://www.google.com/intl/en/googlebooks/images/gbs_preview_button1.png" border="0" style="width: 70px; margin: 0; padding-bottom:5px;"/>
            </a>    
          </div>
        {/if}
        {if $showOLPreviews}
          <div class="previewDiv">
            <a class="{if $summISBN}olISBN{$summISBN}{/if}{if $summLCCN}{if $summISBN} {/if}olLCCN{$summLCCN}{/if}{if $summOCLC}{if $summISBN|$summLCCN} {/if}{foreach from=$summOCLC item=OCLC name=oclcLoop}olOCLC{$OCLC}{if !$smarty.foreach.oclcLoop.last} {/if}{/foreach}{/if}" style="display:none" target="_blank">
              <img src="{$path}/images/preview_ol.gif" border="0" style="width: 70px; margin: 0"/>
            </a>
          </div> 
        {/if}
        {if $showHTPreviews}
          <div class="previewDiv">
            <a id="HT{$summId|escape}" style="display:none"  target="_blank">
              <img src="{$path}/images/preview_ht.gif" border="0" style="width: 70px; margin: 0" title="{translate text='View online: Full view Book Preview from the Hathi Trust'}"/>
            </a>
          </div> 
        {/if}
      {/if}
     {/if}
  </div>
</div>
{if $summCOinS}<span class="Z3988" title="{$summCOinS|escape}"></span>{/if}

{if $summAjaxStatus}
<script type="text/javascript">
  getStatuses('{$summId|escape:"javascript"}');
</script>
{/if}
{if $showPreviews}
<script type="text/javascript">
  {if $summISBN}getExtIds('ISBN{$summISBN|escape:"javascript"}');{/if}
  {if $summLCCN}getExtIds('LCCN{$summLCCN|escape:"javascript"}');{/if}
  {if $summOCLC}{foreach from=$summOCLC item=OCLC}getExtIds('OCLC{$OCLC|escape:"javascript"}');{/foreach}{/if}
  {if (!empty($summLCCN)|!empty($summISBN)|!empty($summOCLC))}
    getHTIds('id:HT{$summId|escape:"javascript"};{if $summISBN}isbn:{$summISBN|escape:"javascript"}{/if}{if $summLCCN}{if $summISBN};{/if}lccn:{$summLCCN|escape:"javascript"}{/if}{if $summOCLC}{if $summISBN || $summLCCN};{/if}{foreach from=$summOCLC item=OCLC name=oclcLoop}oclc:{$OCLC|escape:"javascript"}{if !$smarty.foreach.oclcLoop.last};{/if}{/foreach}{/if}')
  {/if}
</script>
{/if}
