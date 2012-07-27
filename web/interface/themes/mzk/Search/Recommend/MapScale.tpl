{if $showMapScale}
{assign var=from value=`$rangeFrom`}
{assign var=to value=`$rangeTo`}
<div class="authorbox">
  <form name='{$field|escape}Filter' id='{$field|escape}Filter'>
    {translate text="Map scale"}
    <input type="hidden" name="daterange[]" value="{$field|escape}"/>
    <select name="{$field|escape}from" id="{$title|escape}from">
      {foreach from=$scales item=scale name=scale}
        {if ($smarty.foreach.scale.first && !$from) || $from == $scale}
          <option value="{$scale}" selected="selected">1:{$scale}</option>
        {else}
          <option value="{$scale}">1:{$scale}</option>
        {/if}
      {/foreach}
    </select>
    {translate text="to"}&nbsp;&nbsp;
    <select name="{$field|escape}to" id="{$title|escape}to">
      {foreach from=$scales item=scale}
        {if ($smarty.foreach.scale.last && !$to) || $to == $scale}
          <option value="{$scale}" selected="selected">1:{$scale}</option>
        {else}
          <option value="{$scale}">1:{$scale}</option>
        {/if}
      {/foreach}
    </select>
    {foreach from=$smarty.get item=paramValue key=paramName}
      {if is_array($smarty.get.$paramName)}
        {foreach from=$smarty.get.$paramName item=paramValue2}
          {if strpos($paramValue2, $field) !== 0}
            <input type="hidden" name="{$paramName}[]" value="{$paramValue2|escape}" />
          {/if}
        {/foreach}
      {else}
        {if (strpos($paramName, $field) !== 0)
          && (strpos($paramName, 'module') !== 0)
          && (strpos($paramName, 'action') !== 0)
          && (strpos($paramName, 'page') !== 0)}
           <input type="hidden" name="{$paramName}" value="{$paramValue|escape}" />
        {/if}
      {/if}
    {/foreach}
    <input type="submit" value="{translate text='Set'}" id="{$title|escape}goButton">
  </form>
</div>
{/if}
