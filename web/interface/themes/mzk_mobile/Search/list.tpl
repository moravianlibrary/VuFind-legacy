{if $subpage}
  {if $recordCount}
  <span class="graytitle">
    {translate text="Showing"}
    <b>{$recordStart}</b> - <b>{$recordEnd}</b>
    {translate text='of'} <b>{$recordCount}</b>
  </span>
  {/if}

  <ul class="pageitem autolist">
  {include file=$subpage}
  {if $pageLinks.all}
  <div id="pagination">
  	<div id="paginationPrev">{$pageLinks[0]}</div>
  	<div id="paginationNext" >{$pageLinks[2]}</div>
  </div>
  {/if}
  </ul>

{else}
  {$pageContent}
{/if}
