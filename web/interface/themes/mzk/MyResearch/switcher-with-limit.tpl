<table>
  <tr align="left">
    {if $limitList|@count gt 1}
      <td>
        <form id="limitList" name="limitList" method="get">
          <label for="limit">{translate text='Results per page'}</label>
            <select id="limit" name="limit" onChange="document.forms['limitList'].submit()">
              {foreach from=$limitList item=limit}
                <option value="{$limit|escape}"{if $limit == $currentLimit} selected="selected"{/if}>{$limit|escape}</option>
              {/foreach}
              <option value="0" {if $currentLimit == 0}selected="selected"{/if}>{translate text='unlimited'}</option>
            </select>
            {if $currentView}
              <input type="hidden" name="view" value="{$currentView|escape}"/>
            {/if}
          <noscript><input type="submit" value="{translate text="Set"}" /></noscript>
        </form>
      </td>
    {/if}
    <td>
      {include file="MyResearch/switcher.tpl"}
    </td>
  </tr>
</table>