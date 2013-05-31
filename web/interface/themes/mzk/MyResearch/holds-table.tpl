<div class="recordsubcontent">
<table class="citation">
  <tbody>
  
  <tr>
    <th></th>
    <th>{translate text="Main Author"}</th>
    <th>{translate text="Title"}</th>
    <th>{translate text="Status"}</th>
  </tr>
  
  {foreach from=$recordList item=resource name="recordLoop"}
    {if ($smarty.foreach.recordLoop.iteration % 2) == 0}
      <tr class="result alt">
    {else}
      <tr class="result">
    {/if}
    <td>
      <span class="order">{$smarty.foreach.recordLoop.iteration}.&nbsp;</span>
      {if $cancelForm && $resource.ils_details.cancel_details}
        <input type="hidden" name="cancelAllIDS[]" value="{$resource.ils_details.cancel_details|escape}" />
        <input type="checkbox" name="cancelSelectedIDS[]" value="{$resource.ils_details.cancel_details|escape}" class="ui_checkboxes" />
      {else}
        <input type="checkbox" name="cancelSelectedIDS[]" value="{$resource.ils_details.cancel_details|escape}" class="ui_checkboxes" disabled="disabled" />
      {/if}
    </td>

    <td>
      {if $resource.author}
        <a href="{$url}/Author/Home?author={$resource.author|escape:"url"}" class="title">{$resource.author|escape}</a><br>
      {/if}
    </td>
    
    <td>
      {* If $resource.id is set, we have the full Solr record loaded and should display a link... *}
      {if !empty($resource.id)}
        <a href="{$url}/Record/{$resource.id|escape:"url"}" class="title">{$resource.title|escape}</a>
      {* If the record is not available in Solr, perhaps the ILS driver sent us a title we can show... *}
      {elseif !empty($resource.ils_details.title)}
        {$resource.ils_details.title|escape}
      {* Last resort -- indicate that no title could be found. *}
      {else}
        {translate text='Title not available'}
      {/if}
    </td>
    
    <td>
      <strong>{translate text='Created'}:</strong> {$resource.ils_details.create|escape} <br />
      <strong>{translate text='Expires'}:</strong> {$resource.ils_details.expire|escape} <br />
      <strong>{translate text='Status'}:</strong> {$resource.ils_details.status|cat:_status|escape|translate} <br />
      <strong>{translate text='Delivery location'}:</strong> {translate text=$resource.ils_details.location|escape} <br />
      
      {foreach from=$cancelResults item=cancelResult key=itemId}
        {if $itemId == $resource.ils_details.item_id && $cancelResult.success == false}
          <div class="error">{translate text=$cancelResult.status}{if $cancelResult.sysMessage} : {translate text=$cancelResult.sysMessage|escape}{/if}</div>
        {/if}
      {/foreach}

      {if $resource.ils_details.available == true}
        <div class="userMsg">{translate text="hold_available"}</div>
      {else}
        {if $resource.ils_details.position}
          <p><strong>{translate text='hold_queue_position'}:</strong> {$resource.ils_details.position|escape}</p>
        {/if}
      {/if}
      {if $resource.ils_details.cancel_link}
        <p><a href="{$resource.ils_details.cancel_link|escape}">{translate text='hold_cancel'}</a></p>
      {/if}
    </td>
  </tr>
  {/foreach}
  </tbody>
</table>
</div>