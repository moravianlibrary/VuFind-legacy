{foreach from=$holdings item=holding key=location}
<h4>{translate text=$location}</h4>
<table class="holdings" summary="{translate text='Holdings details from'} {translate text=$location}">
  {if $holding.0.callnumber}
  <tr>
    <th>{translate text="Call Number"}: </th>
    <td>{$holding.0.callnumber|escape}</td>
  </tr>
  {/if}
  {if $holding.0.summary}
  <tr>
    <th>{translate text="Volume Holdings"}: </th>
    <td>
      {foreach from=$holding.0.summary item=summary}
      {$summary|escape}<br>
      {/foreach}
    </td>
  </tr>
  {/if}
  {if $holding.0.notes}
  <tr>
    <th>{translate text="Notes"}: </th>
    <td>
      {foreach from=$holding.0.notes item=data}
      {$data|escape}<br>
      {/foreach}
    </td>
  </tr>
  {/if}
  {foreach from=$holding item=row}
    {if $row.barcode != ""}
  <tr>
    <th>{translate text="Copy"} {$row.number}</th>
    <td>
      {if $row.reserve == "Y"}
      {translate text="On Reserve - Ask at Circulation Desk"}
      {else}
        {if $row.availability}
      <span class="available">{translate text="Available"}</span> | 
      <a href="{$path}/Record/{$id|escape:"url"}/Hold">{translate text="Place a Hold"}</a>
        {else}
      <span class="checkedout">{$row.status|escape}</span>
          {if $row.duedate}
      {translate text="Due"}: {$row.duedate|escape} | 
      <a href="{$path}/Record/{$id|escape:"url"}/Hold">{translate text="Recall This"}</a>
          {/if}
        {/if}
      {/if}
    </td>
  </tr>
    {/if}
  {/foreach}
</table>
{/foreach}

{if $history}
<h4>{translate text="Most Recent Received Issues"}</h4>
<ul>
  {foreach from=$history item=row}
  <li>{$row.issue|escape}</li>
  {/foreach}
</ul>
{/if}