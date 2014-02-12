<li class="result">
  
    {* TODO: improve resource icons in mobile template: *}
    <img src="{$path}/images/silk/book.png"/>
	<div class="data">
    	<a class="noeffect" href="{$url}/EBSCO/Record?id={$record.id}">
    		<span class="name">{if !$record.Items.Title}{translate text='Title not available'}{else}{$record.Items.Title.Data}{/if}</span>
    	</a>
	    <span class="desc">
		{if !empty($record.Items.Author)}
	    	{translate text='Author'}: <span class="author">{$record.Items.Author.Data}</span>
	    {/if}
	    {if $record.Items.TitleSource}
	    	<br/>{translate text='Published'} {$record.Items.TitleSource.Data}.
		{/if}
		</span>
    </div>
  <span class="arrow"></span>
</li>
