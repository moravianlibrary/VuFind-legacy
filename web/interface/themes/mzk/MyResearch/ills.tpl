<div id="bd">
  <div id="yui-main" class="content">
    <div class="yui-b first">
    <b class="btop"><b></b></b>
        {if $user->cat_username}          
          <div class="page">
            <h3>{translate text='Interlibrary loans'}</h3>
            {translate text='Create new ILL request for'}:
            <a href="https://aleph.mzk.cz/F/?func=new-ill-request-l&request_type=BOOK">{translate text='Book'}</a>&nbsp;|&nbsp;
            <a href="https://aleph.mzk.cz/F/?func=new-ill-request-l&request_type=JOURNAL">{translate text='Journal/Article'}</a>
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
            {else}
              <ul class="filters">
              {translate text='You do not have any interlibrary loans'}.
              </ul>
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
