<ul class="filters">
  {foreach from=$recordList item=resource name="recordLoop"}
    {if ($smarty.foreach.recordLoop.iteration % 2) == 0}
      <li class="result alt">
    {else}
      <li class="result">
    {/if}
    <span class="order">{$smarty.foreach.recordLoop.iteration}.&nbsp;</span>
      {if $cancelForm && $resource.ils_details.cancel_details}
        <input type="hidden" name="cancelAllIDS[]" value="{$resource.ils_details.cancel_details|escape}" />
        <input type="checkbox" name="cancelSelectedIDS[]" value="{$resource.ils_details.cancel_details|escape}" class="ui_checkboxes" />
      {else}
        <input type="checkbox" name="cancelSelectedIDS[]" value="{$resource.ils_details.cancel_details|escape}" class="ui_checkboxes" disabled="disabled" />
      {/if}
      <div class="yui-ge">
        <div class="yui-u first" style="background-color:transparent;">
        <img src="{$path}/bookcover.php?isn={$resource.isbn.0|@formatISBN}&amp;size=small" class="alignleft">

        <div class="resultitem">
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
          <br />
          {if $resource.author}
            {translate text='by'}: <a href="{$url}/Author/Home?author={$resource.author|escape:"url"}">{$resource.author|escape}</a><br>
          {/if}
          {if $resource.tags}
            {translate text='Your Tags'}:
            {foreach from=$resource.tags item=tag name=tagLoop}
              <a href="{$url}/Search/Results?tag={$tag->tag|escape:"url"}">{$tag->tag|escape}</a>{if !$smarty.foreach.tagLoop.last},{/if}
            {/foreach}
          <br />
          {/if}

          {if $resource.notes}
            {translate text='Notes'}: {$resource.notes|escape}<br>
          {/if}

          {if is_array($resource.format)}
            {foreach from=$resource.format item=format}
              <span class="iconlabel {$format|lower|regex_replace:"/[^a-z0-9]/":""}">{translate text=$format}</span><br />
            {/foreach}
          {elseif isset($resource.format)}
            <span class="iconlabel {$resource.format|lower|regex_replace:"/[^a-z0-9]/":""}">{translate text=$resource.format}</span><br />
          {/if}

          {if $resource.ils_details.description}
            <strong>{translate text='Description'}:</strong> {$resource.ils_details.description|escape}<br />
          {/if}
          
          {if $resource.ils_details.volume}
            <strong>{translate text='Volume'}:</strong> {$resource.ils_details.volume|escape}<br />
          {/if}

          {if $resource.ils_details.publication_year}
            <strong>{translate text='Year of Publication'}:</strong> {$resource.ils_details.publication_year|escape}<br />
          {/if}

          {if $pickup}
            <strong>{translate text='pick_up_location'}:</strong>
            {foreach from=$pickup item=library}
              {if $library.locationID == $resource.ils_details.location}
                {translate text=$library.locationDisplay}
              {/if}
            {/foreach}
          <br />
          {/if}
          <strong>{translate text='Created'}:</strong> {$resource.ils_details.create|escape} |
          <strong>{translate text='Expires'}:</strong> {$resource.ils_details.expire|escape} <br />
          <strong>{translate text='Status'}:</strong> {$resource.ils_details.status|cat:_status|escape|translate} <br />
          <strong>{translate text='Delivery location'}:</strong> {translate text=$resource.ils_details.location|escape}
          <br />

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
        </div>
      </div>
    </div>
  </li>
  {/foreach}
</ul>