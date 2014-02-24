<div id="bd">
  <div id="yui-main" class="content">
    <div class="yui-b first">
      <div class="page">
        <div class="resulthead">
          <h3>{translate text='Your Bookings'}</h3>
        </div>
        {if $errorMsg}
          <p class="error">{$errorMsg|translate}</p>
        {/if}
        <div class="recordsubcontent">
          {if $bookings}
          <form name="renewals" action="{$url}/MyResearch/Bookings" method="post" id="renewals">
            {if $deleteForm}
            <div class="toolbar">
              <ul>
                <li><input type="submit" class="button delete" name="deleteSelected" value="{translate text='delete_selected'}" /></li>
              </ul>
            </div>
            {/if}
            <br/>
            <table class="citation">
              <tbody>
                <tr>
                  <th></th>
                  <th>{translate text="description"}</th>
                  <th>{translate text="booking_start"}</th>
                  <th>{translate text="booking_end"}</th>
                </tr>
                {foreach from=$bookings item=resource name="recordLoop"}
                  {if ($smarty.foreach.recordLoop.iteration % 2) == 0}
                    <tr class="result alt">
                  {else}
                    <tr class="result">
                  {/if}
                    <td>
                      <span class="order">{$smarty.foreach.recordLoop.iteration}.&nbsp;</span>
                      {if $deleteForm}
                        {if $resource.ils_details.delete && $resource.ils_details.item_id}
                          <div class="hiddenLabel"><label for="checkbox_{$resource.id|regex_replace:'/[^a-z0-9]/':''|escape}">{translate text="Select this record"}</label></div>
                          <input type="checkbox" name="deleteSelectedIDS[]" value="{$resource.ils_details.item_id}" class="ui_checkboxes" id="checkbox_{$resource.id|regex_replace:'/[^a-z0-9]/':''|escape}" />
                          <input type="hidden" name="deleteAllIDS[]" value="{$resource.ils_details.item_id}" />
                        {/if}
                      {/if}
                    </td>
                    <td><a href="{$url}/Record/{$resource.id|escape:" url"}" class="title">{$resource.title|escape}</a></td>
                    <td>{$resource.ils_details.start|escape}</td>
                    <td>{$resource.ils_details.end|escape}</td>
                </tr>
              </form>
              {/foreach}
            </tbody>
          </table>
          {else}
            {translate text='You do not have any bookings'}.
          {/if}
        </div>
      </div>
    </div>
  </div>

  {include file="MyResearch/menu.tpl"}

</div>