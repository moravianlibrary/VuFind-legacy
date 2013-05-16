{if $showKonspekt}
  {foreach from=$konspektFacetSet item=cluster key=title}
  <div class="authorbox">
  <table class="facetsTop navmenu narrow_begin">
    <thead>
      <tr>
        <th colspan="{$topFacetSettings.cols}" width="{math equation="floor(100/x)" x=$topFacetSettings.cols}%">
          <span id="{$title}_konspekt_title" style="display:none;">{translate text=$cluster.label}</span>
          <a href="#" onclick="$('#{$title}_konspekt_facets').show(); $('#{$title}_konspekt_title').show(); $(this).remove(); return false;">{translate text="Limit by `$cluster.label`"}</a>
        </th>
      </tr>
    </thead>
    <tbody style="display:none;" id="{$title}_konspekt_facets">
    {foreach from=$cluster.list item=thisFacet name="narrowLoop"}
      {if $smarty.foreach.narrowLoop.iteration % $topFacetSettings.cols == 1}
        <tr>
      {/if}
      {if $thisFacet.isApplied}
        <td>{$thisFacet.value|escape} <img src="{$path}/images/silk/tick.png" alt="Selected"></td>
      {else}
        <td><a href="{$thisFacet.url|escape}">{$thisFacet.value|escape}</a> ({$thisFacet.count})</td>
      {/if}
      {if $smarty.foreach.narrowLoop.iteration % $topFacetSettings.cols == 0 || $smarty.foreach.narrowLoop.last}
        </tr>
      {/if}
    {/foreach}
    </tbody>
  </table>
  </div>
  {/foreach}
{/if}
