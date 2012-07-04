{if !$hideLogin && $offlineMode != "ils-offline"}
  {if ($driverMode && !empty($holdings)) || $titleDriverMode}
    {if $showLoginMsg || $showTitleLoginMsg}
      <div class="userMsg">
        <a href="{$path}/MyResearch/Home?followup=true&followupModule=Record&followupAction={$id}">{translate text="Login"}</a> {translate text="hold_login"}
      </div>
    {/if}
    {if $user && !$user->cat_username}
      {include file="MyResearch/catalog-login.tpl"}
    {/if}
  {/if}
{/if}

{if $holdingTitleHold}
    <a rel="external" class="holdPlace" href="{$holdingTitleHold|replace:"#tabnav":""|escape}">{translate text="title_hold_place"}</a>
{/if}

{foreach from=$holdings item=holding key=location}
<h4>{$location|translate|escape}</h4>
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
    <th>{translate text="Copy"} {$row.number|escape}</th>
    <td>
      {if $row.reserve == "Y"}
      {translate text="On Reserve - Ask at Circulation Desk"}
      {elseif $row.use_unknown_message}
      <span class="unknown">{translate text="status_unknown_message"}</span>
      {else}
        {if $row.availability}
        {* Begin Available Items (Holds) *}
           <span class="available">{translate text="Available"}</span>
          {if $row.link}
            <br />
            <a class="holdPlace" rel="external" href="{$row.link|replace:"#tabnav":""|escape}"><span>{translate text="Place a Hold"}</span></a>
          {/if}
        {else}
        {* Begin Unavailable Items (Recalls) *}
          <span class="checkedout">{translate text=$row.status}</span>
          {if $row.returnDate} <span class="statusExtra">{$row.returnDate|escape}</span>{/if}
          {if $row.duedate}
          <span class="statusExtra">{translate text="Due"}: {$row.duedate|escape}</span>
          {/if}
          {if $row.requests_placed > 0}
            <span>{translate text="Requests"}: {$row.requests_placed|escape}</span>
          {/if}
          {if $row.link}
            <br />
            <a class="holdPlace" rel="external" href="{$row.link|replace:"#tabnav":""|escape}"><span>{translate text="Recall This"}</span></a>
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