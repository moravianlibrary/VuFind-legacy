{if $showConspectusSubcategories}
  {foreach from=$konspektFacetSet item=cluster key=title}
  <div class="authorbox">
  <table class="facetsTop navmenu narrow_begin">
    <tr><th colspan="{$topFacetSettings.cols}" width="{math equation="floor(100/x)" x=$topFacetSettings.cols}%">{translate text=$cluster.label}</th></tr>
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
  </table>
  </div>
  {/foreach}
{/if}
{if $showConspectusCategories}
  <div class="authorbox">
    <form name="conspectusCategoriesFilter" id="conspectusCategoriesFilter">
      {translate text='Conspectus'}:
      <select name="filter[]" onchange="document.forms['conspectusCategoriesFilter'].submit()">
      {foreach from=$konspektFacetSet item=cluster key=title}
        <option value="{$cluster.filter|escape}">{$title|escape}</option>
      {/foreach}
      </select>
      {hiddenFiltersFromCurrentUrl exclude=$conspectusCategoriesExcludeFields}
    </form>
  </div>
{/if}