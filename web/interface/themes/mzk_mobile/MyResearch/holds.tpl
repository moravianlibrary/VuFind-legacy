{if $user->cat_username}
  <ul class="pageitem">
  {if is_array($recordList)}
  {foreach from=$recordList item=record name="recordLoop"}
    <li class="result holds">
    {if !empty($record.id)}
    <a href="{$url}/Record/{$record.id|escape:"url"}">
    {/if}
    <div class="data">
    <span class="name">
    {* TODO: implement resource icons in mobile template: <img src="images/{$record.format|lower|regex_replace:"/[^a-z0-9]/":""}.png"> *}
      {* If $record.id is set, we have the full Solr record loaded and should display a link... *}
      {if !empty($record.id)}
        {$record.title|escape}
      {* If the record is not available in Solr, perhaps the ILS driver sent us a title we can show... *}
      {elseif !empty($record.ils_details.title)}
        {$record.ils_details.title|escape}
      {* Last resort -- indicate that no title could be found. *}
      {else}
        {translate text='Title not available'}
      {/if}
     </span>
    <table>
      <tr>
      		<th>{translate text='Created'}:</th>
      		<td>{$record.ils_details.create|escape}</td>
      </tr>
      <tr>
      		<th>{translate text='Expires'}:</th>
      		<td>{$record.ils_details.expire|escape}</td>
      </tr>
    </table>
    </div>
    <span class="arrow"/>
    {if !empty($record.id)}
    </a>
    {/if}
    </li>
  {/foreach}
  {else}
  <li class="textbox">{translate text='You do not have any holds or recalls placed'}.</li>
  {/if}
  </ul>
{else}
  {include file="MyResearch/catalog-login.tpl"}
{/if}

{include file="MyResearch/menu.tpl"}
