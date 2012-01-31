{if $citationCount < 1}
  {translate text="No citations are available for this record"}.
{else}
  <div class="quote-list">
    {if $apa}
      <h4>{translate text="APA Citation"}</h4>
      <p>
        {include file=$apa}
      </p>
    {/if}

    {if $mla}
      <h4>{translate text="MLA Citation"}</h4>
      <p>
        {include file=$mla}
      </p>
    {/if}  
  <div class="note">{translate text="Warning: These citations may not always be 100% accurate"}.</div>
  </div>
{/if}