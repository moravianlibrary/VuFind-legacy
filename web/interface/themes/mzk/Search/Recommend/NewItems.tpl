{if $showNewItems}
<div class="authorbox">
  <table class="navmenu narrow_begin">
    <tr>
      <form name='newItemsFilter' id='newItemsFilter'>
      <td>
        {translate text='New acquisitions since'}:&nbsp;
      </td>
      <td>
      <select name="filter[]" id="{$newItemsField}" onChange="document.forms['newItemsFilter'].submit()">
        {foreach from=$newItemsDates item=val name=val key=desc}
          <option value="{$val.filter|escape}" {if $val.selected}selected{/if}>{$desc|escape}</option>
        {/foreach}
      </select>
      </td>
      {hiddenFiltersFromCurrentUrl exclude=$newItemsExcludeFields}
      <noscript>
        <td>
        <input type="submit" value="{translate text='Set'}" id="{$title|escape}goButton">
        </td>
      </noscript>
      </form>
    </tr>
    {if count($newItemsConspectusCategories) > 1}
    <tr>
      <form name='newItemsConspectusCategoriesFilter' id='newItemsConspectusCategoriesFilter'>
        <td>{translate text='Conspectus'}:&nbsp;</td>
        <td>
          <select name="filter[]" id="{$newItemsConspectusField}" onChange="document.forms['newItemsConspectusCategoriesFilter'].submit()">
            <option>{translate text='conspectus_categories_all'}</option>
            {foreach from=$newItemsConspectusCategories item=val name=val key=desc}
              <option value="{$newItemsConspectusField}:{$val.value|escape}" {if $val.isApplied}selected{/if}>{$val.value|escape}</option>
            {/foreach}
          </select>
        </td>
        {hiddenFiltersFromCurrentUrl exclude=$newItemsConspectusExcludeFields}
      </form>
    </tr>
    {/if}
  </table>
</div>
{/if}