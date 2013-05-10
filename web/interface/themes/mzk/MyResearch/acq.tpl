<div id="bd">
  <div id="yui-main" class="content">
    <div class="yui-b first">
    <b class="btop"><b></b></b>
    {if $user->cat_username}
      {if empty($acquisitionRequests)}
        <div class="page">
        {translate text='You do not have any acquisition requests'}
      {else}
        <div class="resulthead"><h3>{translate text='Your acquisition requests'}</h3></div>
        <div class="page recordsubcontent">
        <table class="citation" summary="{translate text='Your Fines'}">
        <tr>
          <th>{translate text='Author'}</th>
          <th>{translate text='Title'}</th>
          <th>{translate text='Publisher'}</th>
          <th>{translate text='Last update'}</th>
          <th>{translate text='Status'}</th>
          <th>{translate text='Note'}</th>
        </tr>
        {foreach from=$acquisitionRequests item=record}
          <tr>
            <td>{$record.author|escape}</td>
            <td>{$record.title|escape}</td>
            <td>{$record.publisher|escape}</td>
            <td>{$record.updated|escape}</td>
            <td>{$record.status|translate|escape}</td>
            <td>{$record.note|escape}</td>
          </tr>
        {/foreach}
        </table>
      {/if}
    {else}
      <div class="page">
      {include file="MyResearch/catalog-login.tpl"}
    {/if}</div>
    <b class="bbot"><b></b></b>
    </div>
  </div>

  {include file="MyResearch/menu.tpl"}

</div>