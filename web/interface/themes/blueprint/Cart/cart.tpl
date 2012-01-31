{if !empty($records)}
  <ul class="cartContent">
  {foreach from=$records item=record}
    {* assuming we're dealing with VuFind records *}
    <li><a href="{$url}/Cart/Home?delete={$record.id|escape}" class="delete" title="{translate text='Remove Item'}"></a> <a title="{translate text='View Record'}" href="{$url}/Record/{$record.id|escape}">{$record.title|escape}</a></li>
  {/foreach}
  </ul>
{else}
  <p>{translate text='Your cart is empty'}.</p>
{/if}
