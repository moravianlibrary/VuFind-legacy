{assign var=records value=$bookBag->getRecordDetails()}
<div class="recordsubcontent">
{if !empty($records)}
<table cellpadding="2" cellspacing="0" border="0" class="citation">
  <tbody>
    <tr>
      <th></th>
      <th>{translate text="Main Author"}</th>
      <th>{translate text="Title"}</th>
      <th>{translate text="Published"}</th>
    </tr>
{foreach from=$records item=record}
<tr>
  <td>
    <input id="checkbox_{$record.id|regex_replace:'/[^a-z0-9]/':''|escape}" type="checkbox" name="ids[]" value="{$record.id|escape}" class="checkbox"/>
  </td>
  <td>
    {if !empty($record.author)}
        <a href="{$url}/Author/Home?author={$record.author|escape:"url"}">{$record.author|escape}</a>
    {/if}
  </td>
  <td>
    <a href="{$url}/Record/{$record.id|escape:"url"}" class="title">{$record.title}</a>
  </td>
  <td>{if $record.publishDate_display}{$record.publishDate_display[0]}{/if}</td>
</tr>
{/foreach}
</tbody>
</table>
{else}
  <p>{translate text='bookbag_is_empty'}.</p>
{/if}
</div>

