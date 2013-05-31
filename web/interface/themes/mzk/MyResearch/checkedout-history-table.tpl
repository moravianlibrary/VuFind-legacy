<div class="recordsubcontent">
<table class="citation">
  <tbody>
  
  <tr>
    <th></th>
    <th>{translate text="Main Author"}</th>
    <th>{translate text="Title"}</th>
    <th>{translate text="Returned"}</th>
  </tr>
  
  {foreach from=$transList item=resource name="recordLoop"}
    {if ($smarty.foreach.recordLoop.iteration % 2) == 0}
      <tr class="result alt">
    {else}
      <tr class="result">
    {/if}
	
	<td><span class="order">{$smarty.foreach.recordLoop.iteration}.&nbsp;</span></td>
	<td>
      {if $resource.author}
        <a href="{$url}/Author/Home?author={$resource.author|escape:"url"}" class="title">{$resource.author|escape}</a>
      {else}
        <a href="{$url}/Author/Home?author={$resource.ils_details.author|escape:"url"}" class="title">{$resource.ils_details.author|escape}</a>
      {/if}
    </td>
    <td>
      {if !empty($resource.id)}
        <a href="{$url}/Record/{$resource.id|escape:" url"}" class="title">{$resource.title|escape}</a>
      {elseif !empty($resource.ils_details.title)}
		<a href="{$url}/Search/Barcode?barcode={$resource.ils_details.barcode|escape:'url'}" class="title">{$resource.ils_details.title|escape}</a>
      {else}
		{translate text='Title not available'}
      {/if}
    </td>
    <td>
      {assign var="showStatus" value="show"}
      {if $renewResult[$resource.ils_details.item_id]}
        {if $renewResult[$resource.ils_details.item_id].success}
          {assign var="showStatus" value="hide"}
          <strong>{translate text='Due Date'}: {$renewResult[$resource.ils_details.item_id].new_date}
            {if $renewResult[$resource.ils_details.item_id].new_time}
              {$renewResult[$resource.ils_details.item_id].new_time|escape}
            {/if}
          </strong>
           <div class="userMsg">{translate text='renew_success'}</div>
        {else}
          <strong>
            {translate text='Due Date'}: {$resource.ils_details.duedate|escape}
            {if $resource.ils_details.dueTime}
              {$resource.ils_details.dueTime|escape}
            {/if}
          </strong>
          <div class="error">
            {translate text='renew_fail'}
            {if $renewResult[$resource.ils_details.item_id].sysMessage}: {$renewResult[$resource.ils_details.item_id].sysMessage|escape}{/if}
          </div>
        {/if}
      {else}
        {$resource.ils_details.returned|escape}
          {if $showStatus == "show"}
            {if $resource.ils_details.dueStatus == "overdue"}
              <div class="error">{translate text="renew_item_overdue"}</div>
            {elseif $resource.ils_details.dueStatus == "due"}
              <div class="userMsg">{translate text="renew_item_due"}</div>
            {/if}
          {/if}
      {/if}
      {if $showStatus == "show" && $resource.ils_details.message}
        <div class="userMsg">{translate text=$resource.ils_details.message}</div>
      {/if}
      {if $resource.ils_details.renewable && $resource.ils_details.renew_link}
        <a href="{$resource.ils_details.renew_link|escape}">{translate text='renew_item'}</a>
      {/if}
    </td>
  </tr>
  {/foreach}
  </tbody>
</table>
</div>
