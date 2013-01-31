{* Display Title *}
{literal}
  <script language="JavaScript" type="text/javascript">
    // <!-- avoid HTML validation errors by including everything in a comment.
    function subjectHighlightOn(subjNum, partNum)
    {
        // Create shortcut to YUI library for readability:
        var yui = YAHOO.util.Dom;

        for (var i = 0; i < partNum; i++) {
            var targetId = "subjectLink_" + subjNum + "_" + i;
            var o = document.getElementById(targetId);
            if (o) {
                yui.addClass(o, "hoverLink");
            }
        }
    }

    function subjectHighlightOff(subjNum, partNum)
    {
        // Create shortcut to YUI library for readability:
        var yui = YAHOO.util.Dom;

        for (var i = 0; i < partNum; i++) {
            var targetId = "subjectLink_" + subjNum + "_" + i;
            var o = document.getElementById(targetId);
            if (o) {
                yui.removeClass(o, "hoverLink");
            }
        }
    }
    // -->
  </script>
{/literal}

<!-- Begin of modifications for Obalky knih -->
{* Display Cover Image *}
      <!-- <span id="obalka_{$summId}"></span> -->
      <script>
        $(document).ready(function() {ldelim}
          var bibinfo = {literal}{{/literal} 
            "authors": [ "{$summAuthor}" ],
            "isbn": "{$isbn}",
            //"issn": "{$summISSN}",
            "title": "{$summTitle}"
          {rdelim};
          var permalink = "{$url}/Record/{$id|escape:'url'}";
          obalky.process("obalky_display_cover", "obalka_{$id}", permalink, bibinfo);
        {rdelim});
      </script>

      <div class="alignright">
      <table align="left">
      <tr>
      <td id="obalka_{$id}">{image id="obalka_`$id`_format" src="formats/$recordFormat[0].png" align="left" title="$recordFormat[0]"|translate}</td>
      </tr>
      <tr>
      <td class="format">{translate text=$recordFormat[0]}</td>
      </tr>
      </table>
      </div>
<!-- End of modifications for Obalky knih -->

{* Display Title *}
<div itemscope itemtype="http://schema.org/Book">
<h1 itemprop="name">{$coreShortTitle|escape}
{if $coreSubtitle}{$coreSubtitle|escape}{/if}
&nbsp;{if $coreTitleSection}{$coreTitleSection|escape}{/if}
{* {if $coreTitleStatement}{$coreTitleStatement|escape}{/if} *}
</h1>
{* End Title *}

{if $coreSummary}<p>{$coreSummary|truncate:300:"..."|escape}.&nbsp;<a href='{$url}/Record/{$id|escape:"url"}/Description#tabnav'>{translate text='Full description'}</a></p>{/if}

{* Display Main Details *}
<table cellpadding="2" cellspacing="0" border="0" class="citation" summary="{translate text='Bibliographic Details'}" id="bibliographic_details">
  {if !empty($coreNextTitles)}
  <tr valign="top">
    <th>{translate text='New Title'}: </th>
    <td>
      {foreach from=$coreNextTitles item=field name=loop}
        <a href="{$url}/Search/Results?lookfor=%22{$field|escape:"url"}%22&amp;type=Title&filter={$filter|escape:"url"}">{$field|escape}</a><br>
      {/foreach}
    </td>
  </tr>
  {/if}

  {if !empty($corePrevTitles)}
  <tr valign="top">
    <th>{translate text='Previous Title'}: </th>
    <td>
      {foreach from=$corePrevTitles item=field name=loop}
        <a href="{$url}/Search/Results?lookfor=%22{$field|escape:"url"}%22&amp;type=Title&filter={$filter|escape:"url"}">{$field|escape}</a><br>
      {/foreach}
    </td>
  </tr>
  {/if}

  {if !empty($coreMainAuthor)}
  <tr valign="top">
    <th>{translate text='Main Author'}: </th>
    <td><a itemprop="author" href="{$url}/Author/Home?author={$coreMainAuthor|escape:"url"}&filter={$filter|escape:"url"}">{$coreMainAuthor|escape}</a></td>
  </tr>
  {/if}

  {if !empty($coreCorporateAuthor)}
  <tr valign="top">
    <th>{translate text='Corporate Author'}: </th>
    <td><a itemprop="author" href="{$url}/Author/Home?author={$coreCorporateAuthor|escape:"url"}&filter={$filter|escape:"url"}">{$coreCorporateAuthor|escape}</a></td>
  </tr>
  {/if}

  {if !empty($coreContributors)}
  <tr valign="top">
    <th>{translate text='Other Authors'}: </th>
    <td>
      {foreach from=$coreContributors item=field name=loop}
        <a itemprop="contributor" href="{$url}/Author/Home?author={$field|escape:"url"}&filter={$filter|escape:"url"}">{$field|escape}</a>{if !$smarty.foreach.loop.last}, {/if}
      {/foreach}
    </td>
  </tr>
  {/if}

  <!--
  <tr valign="top">
    <th>{translate text='Format'}: </th>
    <td>
     {if is_array($recordFormat)}
      {foreach from=$recordFormat item=displayFormat name=loop}
        {*<span class="iconlabel {$displayFormat|lower|regex_replace:"/[^a-z0-9]/":""}">{translate text=$displayFormat}</span>*}
        <img src="{$path}/interface/themes/mzk/images/formats/small/{$displayFormat}.png" title="{translate text=$displayFormat}" align="left"/>&nbsp;{translate text=$displayFormat}
      {/foreach}
    {else}
      <img src="{$path}/interface/themes/mzk/images/formats/small/{$displayFormat}.png" title="{translate text=$displayFormat}" align="left"/>&nbsp;{translate text=$displayFormat}
      {*<span class="iconlabel {$recordFormat|lower|regex_replace:"/[^a-z0-9]/":""}">{translate text=$recordFormat}</span>*}
    {/if}  
    </td>
  </tr>
  -->

  <tr valign="top">
    <th>{translate text='Language'}: </th>
    <td>{foreach from=$recordLanguage item=lang}{$lang|escape}<br>{/foreach}</td>
  </tr>

  {if !empty($corePublications)}
  <tr valign="top">
    <th>{translate text='Published'}: </th>
    <td>
      {foreach from=$corePublications item=field name=loop}
        {$field|escape}<br>
      {/foreach}
    </td>
  </tr>
  {/if}

  {if !empty($coreEdition)}
  <tr valign="top">
    <th>{translate text='Edition'}: </th>
    <td itemprop="bookEdition">
      {$coreEdition|escape}
    </td>
  </tr>
  {/if}

  {* Display series section if at least one series exists. *}
  {if !empty($coreSeries)}
  <tr valign="top">
    <th>{translate text='Series'}: </th>
    <td>
      {foreach from=$coreSeries item=field name=loop}
        {* Depending on the record driver, $field may either be an array with
           "name" and "number" keys or a flat string containing only the series
           name.  We should account for both cases to maximize compatibility. *}
        {if is_array($field)}
          {if !empty($field.name)}
            <a href="{$url}/Search/Results?lookfor=%22{$field.name|escape:"url"}%22&amp;type=Series&filter={$filter|escape:"url"}&filter={$filter|escape:"url"}">{$field.name|escape}</a>
            {if !empty($field.number)}
              {$field.number|escape}
            {/if}
            <br>
          {/if}
        {else}
          <a href="{$url}/Search/Results?lookfor=%22{$field|escape:"url"}%22&amp;type=Series&filter={$filter|escape:"url"}&filter={$filter|escape:"url"}">{$field|escape}</a><br>
        {/if}
      {/foreach}
    </td>
  </tr>
  {/if}

  {if !empty($coreSubjects)}
  <tr valign="top">
    <th>{translate text='Subjects'}: </th>
    <td>
      {foreach from=$coreSubjects item=field name=loop}
        {assign var=subject value=""}
        {foreach from=$field item=subfield name=subloop}
          {if !$smarty.foreach.subloop.first} &gt; {/if}
          {assign var=subject value="$subject $subfield"}
          <a itemprop="keywords" id="subjectLink_{$smarty.foreach.loop.index}_{$smarty.foreach.subloop.index}"
            href="{$url}/Search/Results?lookfor=%22{$subject|escape:"url"}%22&amp;type=Subject&filter={$filter|escape:"url"}"
          onmouseover="subjectHighlightOn({$smarty.foreach.loop.index}, {$smarty.foreach.subloop.index});"
          onmouseout="subjectHighlightOff({$smarty.foreach.loop.index}, {$smarty.foreach.subloop.index});">{$subfield|escape}</a>
        {/foreach}
        <br>
      {/foreach}
    </td>
  </tr>
  {/if}

  <tr valign="top">
    <th>{translate text='Online Access'}: </th>
    <td>
      {foreach from=$summURLs item=desc key=currentUrl name=loop}
        <a href="https://proxy.mzk.cz/login?auth=shibboleth&url={$currentUrl|escape}" target="new">{translate text="Online"}</a><br/>
      {/foreach}
    </td>
  </tr>
  

  {if !empty($coreRecordLinks)}
  {foreach from=$coreRecordLinks item=coreRecordLink}
  <tr valign="top">
    <th>{translate text=$coreRecordLink.title}: </th>
    <td><a href="{$coreRecordLink.link|escape}">{$coreRecordLink.value|escape}</a></td>
  </tr>
  {/foreach}
  {/if}

  <tr valign="top">
    <th>{translate text='Tags'}: </th>
    <td>
      <span style="float:right;">
        <a href="{$url}/Record/{$id|escape:"url"}/AddTag" class="tool add"
           onClickDisabled="getLightbox('Record', 'AddTag', '{$id|escape}', null, '{translate text="Add Tag"}'); return false;">{translate text="Add"}</a>
      </span>
      <div id="tagList">
        {if $tagList}
          {foreach from=$tagList item=tag name=tagLoop}
        <a href="{$url}/Search/Results?tag={$tag->tag|escape:"url"}">{$tag->tag|escape:"html"}</a> ({$tag->cnt}){if !$smarty.foreach.tagLoop.last}, {/if}
          {/foreach}
        {else}
          {translate text='No Tags'}, {translate text='Be the first to tag this record'}!
        {/if}
      </div>
    </td>
  </tr>

  <!-- begin of costumization for MZK -->
  {if $callNumber}
    <tr valign="top">
      <th>{translate text='Signature'}: </th>
        <td>
        {foreach from=$callNumber item=callNo}
           {$callNo}
        {/foreach}
        </td>
    </tr>
  {/if}

  <!-- end of costumization for MZK -->
</table>

<br />
<div id="links">
   {if $EOD}
           <table>
              <tr>
                 <td>
                    <a href="http://books2ebooks.eu/odm/orderformular.do?formular_id=131&sys_id={$sys_no}" target="blank">
                       <img src='/interface/themes/mzk/images/eod_button_{$userLang}.gif'/>
                    </a>
                 </td>
                 <td>&nbsp;&nbsp;</td>
                 <td><span style="color: #EC9136; font-size: 120%;">{translate text='EOD'}</span></td>
              </tr>
           </table>
   {/if}
</div>

</div>
{literal}
<!-- AddThis Button BEGIN -->
<div class="addthis_toolbox addthis_default_style addthis_32x32_style">
<a class="addthis_button_preferred_1"></a>
<a class="addthis_button_preferred_2"></a>
<a class="addthis_button_preferred_3"></a>
<a class="addthis_button_preferred_4"></a>
<a class="addthis_button_compact"></a>
<a class="addthis_counter addthis_bubble_style"></a>
</div>
<script type="text/javascript">var addthis_config =
{"data_track_addressbar":false};</script>
<script type="text/javascript"
src="https://s7.addthis.com/js/250/addthis_widget.js#pubid=ra-4ffed26866462853"></script>
<!-- AddThis Button END -->
{/literal}

{* End Main Details *}
