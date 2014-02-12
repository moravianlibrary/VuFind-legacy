<li class="result">
  <a class="noeffect" href="{$url}/Record/{$summId|escape:"url"}">
    {* TODO: improve resource icons in mobile template: *}
    {image src="formats/`$summFormats.0`.png"}
	<div class="data">
    	<span class="name">{if !$summTitle}{translate text='Title not available'}{else}{$summTitle|highlight:$lookfor}{/if}</span>
    <span class="desc">
	{if $summAuthor}
    	{translate text='Author'}: <span class="author">{$summAuthor}</span>
    {/if}
    {if $summDate}
    	{translate text='Published'} {$summDate.0|escape}.
	{/if}
	</span>
    </div>
  </a>
  <span class="arrow"></span>
</li>
