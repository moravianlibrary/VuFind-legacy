<div id="bd">
  <div id="yui-main" class="content">
    <div class="yui-b first">
    <b class="btop"><b></b></b>
        {if $user->cat_username}          
          <div class="page">
            <h3>{translate text='Interlibrary loans'}</h3>
            
            {if $illMessage}
              <p class="info">{translate text=$illMessage} {translate text='Req No.'}: {$illNewReqId|escape}</p>
            {/if}
            
            <p>
              <b>{translate text='Create new ILL request for'}:</b>
              <a href="{$path}/MyResearch/InterlibraryLoans?new=monography">{translate text='Book'}</a>&nbsp;|&nbsp;
              <a href="{$path}/MyResearch/InterlibraryLoans?new=serial">{translate text='Journal/Article'}</a>
            </p>
           
           {include file="MyResearch/ills_note_cz.tpl"}
            
            <h3>{translate text='Your interlibrary loan requests'}</h3>
            {if $ills}
              <ul class="filters">
              {foreach from=$ills item=resource name="recordLoop"}
                {if ($smarty.foreach.recordLoop.iteration % 2) == 0}
                <li class="result alt">
                {else}
                <li class="result">
                {/if}
                  <div class="yui-ge">
                    <div class="yui-u first" style="background-color:transparent">
                      <img src="{$path}/bookcover.php?isn={$resource.isbn|@formatISBN}&amp;size=small" class="alignleft" alt="{$resource.title|escape}">
                      <div class="resultitem">
                        <table>
                        <p class="resultItemLine2">{translate text='Req No.'}: {$resource.docno|escape}</p>
                        <p class="resultItemLine2">{translate text='Author'}: {$resource.author|escape}</p>
                        <p class="resultItemLine2">{translate text='Title'}: {$resource.title|escape}</p>
                        <p class="resultItemLine2">{translate text='Imprint'}: {$resource.imprint|escape}</p>
                        {if $resource.media eq "PHOTOCOPY (serial)"}
                          <p class="resultItemLine2">{translate text='Periodical article title'}: {$resource.article_title|escape}</p>
                          <p class="resultItemLine2">{translate text='Periodical article author'}: {$resource.article_author|escape}</p>
                        {/if}
                        <p class="resultItemLine2">{translate text='Pickup location'}: {$resource.pickup_location|escape}</p>
                        <p class="resultItemLine2">{translate text='Requested media'}: {$resource.media|translate|escape}</p>
                        <p class="resultItemLine2">{translate text='Required by'}: {$resource.required_by|escape}</p>
                        {if $resource.price}
                          <p class="resultItemLine2">{translate text='Price'}: {$resource.price|escape}</p>
                        {/if}
                        </table>
                      </div>
                    </div>
                  </div>
                </li>
              {/foreach}
              </ul>
            {else}
              <p>
              {translate text='You do not have any interlibrary loans'}.
              </p>
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
