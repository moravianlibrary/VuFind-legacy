<div class="recordsubcontent">
<table class="citation">
  <tbody>
  
  <tr>
    <th></th>
    <th>{translate text="Main Author"}</th>
    <th>{translate text="Title"}</th>
    <th>{translate text="Status"}</th>
  </tr>
  
  {foreach from=$transList item=resource name="recordLoop"}
    {if ($smarty.foreach.recordLoop.iteration % 2) == 0}
      <tr class="result alt">
    {else}
      <tr class="result">
    {/if}

    <td>
      <span class="order">{$smarty.foreach.recordLoop.iteration}.&nbsp;</span>
      {if $renewForm}
        {if $resource.ils_details.renewable && $resource.ils_details.renew_details}
          <div class="hiddenLabel"><label for="checkbox_{$resource.id|regex_replace:'/[^a-z0-9]/':''|escape}">{translate text="Select this record"}</label></div>
          <input type="checkbox" name="renewSelectedIDS[]" value="{$resource.ils_details.renew_details}" class="ui_checkboxes" id="checkbox_{$resource.id|regex_replace:'/[^a-z0-9]/':''|escape}" />
          <input type="hidden" name="renewAllIDS[]" value="{$resource.ils_details.renew_details}" />
        {/if}
      {/if}
    </td>
    
    <td>
      {if $resource.author}
        <a href="{$url}/Author/Home?author={$resource.author|escape:"url"}" class="title">{$resource.author|escape}</a>
      {else}
        <a href="{$url}/Author/Home?author={$resource.ils_details.author|escape:"url"}" class="title">{$resource.ils_details.author|escape}</a>
      {/if}
    </td>

    <td>
      {* If $resource.id is set, we have the full Solr record loaded and should display a link... *}
      {if !empty($resource.id)}
        <p class="resultItemLine1"><a href="{$url}/Record/{$resource.id|escape:"url"}" class="title">{$resource.title|escape}</a></p>
      {* If the record is not available in Solr, perhaps the ILS driver sent us a title we can show... *}
      {elseif !empty($resource.ils_details.title)}
        {$resource.ils_details.title|escape}
      {* Last resort -- indicate that no title could be found. *}
      {else}
        {translate text='Title not available'}
      {/if}
    </td>

    <td>
      {assign var="showStatus" value="show"}
      {if $renewResult[$resource.ils_details.item_id]}
        {if $renewResult[$resource.ils_details.item_id].success}
          {assign var="showStatus" value="hide"}
            <strong>{translate text='Due Date'}: {$resource.ils_details.duedate|escape} {if $resource.ils_details.dueTime} {$resource.ils_details.dueTime|escape}{/if}</strong>
            <div class="userMsg">{translate text='renew_success'}</div>
          {else}
            <strong>{translate text='Due Date'}: {$resource.ils_details.duedate|escape} {if $resource.ils_details.dueTime} {$resource.ils_details.dueTime|escape}{/if}</strong>
            <div class="error">{translate text='renew_fail'}{if $renewResult[$resource.ils_details.item_id].sysMessage}: {$renewResult[$resource.ils_details.item_id].sysMessage|translate|escape}{/if}</div>
        {/if}
      {else}
        <p>
          <strong>
            {translate text='Due Date'}: {$resource.ils_details.duedate|escape}
            {if $resource.ils_details.dueTime} {$resource.ils_details.dueTime|escape}{/if}
            {if $resource.ils_details.no_of_renewals > 0}
              | {translate text='No of renewals'}: {$resource.ils_details.no_of_renewals|escape}
            {/if}
            {if $resource.ils_details.fine > 0}
              | {translate text='Fine'}: {$resource.ils_details.fine|escape}
            {/if}
          </strong>
        </p>
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
