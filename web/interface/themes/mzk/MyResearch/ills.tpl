<div id="bd">
  <div id="yui-main" class="content">
    <div class="yui-b first">
    <b class="btop"><b></b></b>
        {if $user->cat_username}          
          <div class="page">
		  <h3>{translate text='Interlibrary loans'}</h3>

          {if $ills}

              <ul class="filters">
              {foreach from=$ills item=resource name="recordLoop"}
                {if ($smarty.foreach.recordLoop.iteration % 2) == 0}
                <li class="result alt">
                {else}
                <li class="result">
                {/if}
                {*
                {if $renewForm}
                  {if $resource.ils_details.renewable && $resource.ils_details.renew_details}
                    <div class="hiddenLabel"><label for="checkbox_{$resource.id|regex_replace:'/[^a-z0-9]/':''|escape}">{translate text="Select this record"}</label></div>
                    <input type="checkbox" name="renewSelectedIDS[]" value="{$resource.ils_details.renew_details}" class="ui_checkboxes" id="checkbox_{$resource.id|regex_replace:'/[^a-z0-9]/':''|escape}" />
                    <input type="hidden" name="renewAllIDS[]" value="{$resource.ils_details.renew_details}" />
                  {/if}
                {/if}
                *}
                  <div class="yui-ge">
                    <div class="yui-u first" style="background-color:transparent">
                      <img src="{$path}/bookcover.php?isn={$resource.isbn|@formatISBN}&amp;size=small" class="alignleft" alt="{$resource.title|escape}">
                      <div class="resultitem">
                        <table>
                        <p class="resultItemLine2">{translate text='Req No.'}: {$resource.docno|escape}</p>
                        <p class="resultItemLine2">{translate text='Author'}: {$resource.author|escape}</p>
                        <p class="resultItemLine2">{translate text='Title'}: {$resource.title|escape}</p>
                        <p class="resultItemLine2">{translate text='Imprint'}: {$resource.imprint|escape}</p>
                        <p class="resultItemLine2">{translate text='Periodical article title'}: {$resource.article_title|escape}</p>
                        <p class="resultItemLine2">{translate text='Periodical article author'}: {$resource.article_author|escape}</p>
                        <p class="resultItemLine2">{translate text='Pickup location'}: {$resource.pickup_location|escape}</p>
                        <p class="resultItemLine2">{translate text='Requested media'}: {$resource.media|escape}</p>
                        <p class="resultItemLine2">{translate text='Required by'}: {$resource.required_by|escape}</p>
                        </table>
                      </div>
                    </div>
                  </div>
                </li>
              {/foreach}
              </ul>
            {if $renewForm}
              </form>
            {/if}
          {else}
            {translate text='You do not have any interlibrary loans'}.
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
