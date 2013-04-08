<div class="recordsubcontent">
<table cellpadding="2" cellspacing="0" border="0" class="citation">
  <tbody>
    <tr>
      <th>{translate text="Main Author"}</th>
      <th>{translate text="Title"}</th>
      <th>{translate text="Published"}</th>
      <th>{translate text="Availability"}</th>
    </tr>
    {foreach from=$recordSet item=record name="recordLoop"}
       {$record}
    {/foreach}
  </tbody>
</table>
</div>

<script type="text/javascript">
  doGetStatuses({literal}{{/literal}
    unknown: '<span class="unknown">{translate text='Unknown'}<\/span>'
  {literal}}{/literal}, false);
  {if $user}
  doGetSaveStatuses();
  {/if}
</script>
