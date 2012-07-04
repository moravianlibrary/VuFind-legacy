<div data-role="page" id="Search-list-none">
  {include file="header.tpl"}
  <div data-role="content">
    <p>{translate text='nohit_prefix'} - <strong>{$lookfor|escape}</strong> - {translate text='nohit_suffix'}</p>
    {if $noResultsRecommendations}
      {foreach from=$noResultsRecommendations item="recommendations" key='key' name="noResults"}
        {include file=$recommendations}
     {/foreach}
    {/if}
  </div>
  {include file="footer.tpl"}
</div>