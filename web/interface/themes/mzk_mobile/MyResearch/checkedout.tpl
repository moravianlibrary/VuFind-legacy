{if $user->cat_username}
  <div class="checkedout pageitem" id="container">
  {if $transList}
    {foreach from=$transList item=resource name="recordLoop"}
        {* TODO: implement resource icons in mobile template: <img src="images/{$resource.format|lower|regex_replace:"/[^a-z0-9]/":""}.png"> *}
        {* If $resource.id is set, we have the full Solr record loaded and should display a link... *}
        {if $renewForm}
            <form name="renewals" action="{$url}/MyResearch/CheckedOut" method="post" id="renewals">
            	<input type="checkbox" style="display: none;" name="renewSelectedIDS[]" value="{$resource.ils_details.renew_details}" id="checkbox_{$resource.id|regex_replace:'/[^a-z0-9]/':''|escape}" checked="checked" />
            	<input type="submit" name="renewSelected" value="{translate text="renew_item"}" />
            </form>
        {/if}
        <div class="data">
        {if !empty($resource.id)}
          <a href="{$url}/Record/{$resource.id|escape:"url"}" class="title">
		  <span class="name">{$resource.title|escape}</span>
        {* If the record is not available in Solr, perhaps the ILS driver sent us a title we can show... *}
        {elseif !empty($resource.ils_details.title)}
          {$resource.ils_details.title|escape}
        {* Last resort -- indicate that no title could be found. *}
        {else}
          {translate text='Title not available'}
        {/if}
        {assign var="showStatus" value="show"}
        {if $renewResult[$resource.ils_details.item_id]}
        	{if $renewResult[$resource.ils_details.item_id].success}
              {assign var="showStatus" value="hide"}
              <span class="dueDate">{translate text='Due Date'}: {$resource.ils_details.duedate|escape} {if $resource.ils_details.dueTime} {$resource.ils_details.dueTime|escape}{/if}</span>
              <span class="userMsg">{translate text='renew_success'}</span>
            {else}
              <span class="dueDate">{translate text='Due Date'}: {$resource.ils_details.duedate|escape} {if $resource.ils_details.dueTime} {$resource.ils_details.dueTime|escape}{/if}</span>
              <div class="error">{translate text='renew_fail'}{if $renewResult[$resource.ils_details.item_id].sysMessage}: {$renewResult[$resource.ils_details.item_id].sysMessage|translate|escape}{/if}</div>
            {/if}
        {else}
              <span class="dueDate">
                 {translate text='Due Date'}: {$resource.ils_details.duedate|escape}
              </span>   
                 {if $resource.ils_details.dueTime} {$resource.ils_details.dueTime|escape}{/if}
                 {if $resource.ils_details.no_of_renewals > 0}
                 {assign var="renewalsNum" value="true"}
                 <span class="userMsg">{translate text='No of renewals'}: {$resource.ils_details.no_of_renewals|escape}</span>
                 {/if}
                 {if $resource.ils_details.fine > 0}
                 {if $renewalsNum}
                 {else}
                 {/if}
                 <div class="error">{translate text='Fine'}: {$resource.ils_details.fine|escape}</div>
                 {/if}
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
        {if $errorMsg}
            <div class="error">{translate text=$errorMsg}</div>
        {/if}
    {/foreach}
  {else}
    <span>{translate text='You do not have any items checked out'}.</span>
  {/if}
  {if !empty($resource.id)}
  	</a>
  {/if}
  </div>
  </div>
{else}
  {include file="MyResearch/catalog-login.tpl"}
{/if}

{include file="MyResearch/menu.tpl"}
