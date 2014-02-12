<li class="result">
  <a class="noeffect" href="{$url}/Record/{$listId|escape:"url"}">
    {image src="formats/`$listFormats.0`.png"}

    <div class="data">
	    <span class="name">{if !$listTitle}{translate text='Title not available'}{else}{$listTitle|highlight:$lookfor}{/if}</span>
    {if $listAuthor}
    <span class="desc">{translate text='Author'}: <span class="author">{$listAuthor}</span></span>
	{/if}
    </div>
    <span class="arrow"></span>
  </a>
</li>
