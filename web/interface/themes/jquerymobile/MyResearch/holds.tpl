<div data-role="page" id="MyResearch-holds">
  {include file="header.tpl"}
  <div data-role="content">
    {if $user->cat_username}
      <h3>{translate text='Your Holds and Recalls'}</h3>
      {if $recordList}
        <ul class="results holds" data-role="listview">
        {foreach from=$recordList item=resource name="recordLoop"}
          <li>
            {if !empty($resource.id)}<a rel="external" href="{$path}/Record/{$resource.id|escape}">{/if}
            <div class="result">
              {* If $resource.id is set, we have the full Solr record loaded and should display a link... *}
              {if !empty($resource.id)}
                <h3>{$resource.title|trim:'/:'|escape}</h3>
              {* If the record is not available in Solr, perhaps the ILS driver sent us a title we can show... *}
              {elseif !empty($resource.ils_details.title)}
                <h3>{$resource.ils_details.title|trim:'/:'|escape}</h3>
              {* Last resort -- indicate that no title could be found. *}
              {else}
                <h3>{translate text='Title not available'}</h3>
              {/if}
              {if !empty($resource.author)}
                <p>{translate text='by'} {$resource.author}</p>
              {/if}
              {if !empty($resource.format)}
              <p>
              {foreach from=$resource.format item=format}
                <span class="iconlabel {$format|lower|regex_replace:"/[^a-z0-9]/":""}">{translate text=$format}</span>
              {/foreach}
              </p>
              {/if} 
              <p><strong>{translate text='Created'}:</strong> {$resource.ils_details.create|escape} |
              <strong>{translate text='Expires'}:</strong> {$resource.ils_details.expire|escape}</p>
            </div>
            {if !empty($resource.id)}</a>{/if}
          </li>
        {/foreach}
        </ul>
      {else}
        <p>{translate text='You do not have any holds or recalls placed'}.</p>
      {/if}
    {else}
      {include file="MyResearch/catalog-login.tpl"}
    {/if}
  </div>
  {include file="footer.tpl"}
</div>

