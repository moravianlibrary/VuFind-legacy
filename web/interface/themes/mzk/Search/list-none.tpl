{js filename="ajax_common.js"}
{js filename="search.js"}

<div id="bd">
  <div id="yui-main" class="content">
    <div class="yui-b first">
      <b class="btop"><b></b></b>
      {* Recommendations *}
      {if $topRecommendations}
        {foreach from=$topRecommendations item="recommendations"}
          {include file=$recommendations}
        {/foreach}
      {/if}
      <div class="resulthead"><h3>{translate text='nohit_heading'}</h3></div>
      <div class="page">

        <p class="error">{translate text='nohit_prefix'} - <b>{$lookfor|escape:"html"}</b> - {translate text='nohit_suffix'}</p>

        {if $parseError}
            <p class="error">{translate text='nohit_parse_error'}</p>
        {/if}

        {if $spellingSuggestions}
        <div class="correction">{translate text='nohit_spelling'}:<br/>
        {foreach from=$spellingSuggestions item=details key=term name=termLoop}
          {$term|escape} &raquo; {foreach from=$details.suggestions item=data key=word name=suggestLoop}<a href="{$data.replace_url|escape}">{$word|escape}</a>{if $data.expand_url} <a href="{$data.expand_url|escape}"><img src="{$path}/images/silk/expand.png" alt="{translate text='spell_expand_alt'}"/></a> {/if}{if !$smarty.foreach.suggestLoop.last}, {/if}{/foreach}{if !$smarty.foreach.termLoop.last}<br/>{/if}
        {/foreach}
        </div>
        <br/>
        {/if}

        <div class="alternative_searches">
        {translate text='You may also try'}:
          <ul>
            <li>
              <a href="https://listky.mzk.cz/">
                {translate text='Our digitized card catalogues'}
              </a>
            </li>
            <li>
              <a href="http://aleph.muni.cz/">{translate text='The Union Catalogue of Masaryk University'}</a>
            </li>
            <li>
              <a href="http://sigma.nkp.cz/F/?func=file&file_name=find-b&local_base=SKC">{translate text='The Union Catalogue of the Czech Republic'}</a>
            </li>
            <li>
              <a href="http://www.mzk.cz/sluzby/pujcovani/meziknihovni-sluzby">
                {translate text='Interlibrary loan'}
              </a>
            </li>
            <li>
              <a href="http://www.mzk.cz/tipy-na-nakup-0">
                {translate text='Acquisition Request Form'}
              </a>
            </li>
          </ul>
        </div>
        
      </div>
      <b class="bbot"><b></b></b>
    </div>
  </div>

  {* Narrow Search Options *}
  <div class="yui-b">
    {if $sideRecommendations}
      {foreach from=$sideRecommendations item="recommendations"}
        {include file=$recommendations}
      {/foreach}
    {/if}
    <br />
  </div>
  {* End Narrow Search Options *}
</div>