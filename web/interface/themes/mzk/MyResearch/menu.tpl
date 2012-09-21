<div class="yui-b">
  <div class="sidegroup">
    <h4>{$user->firstname}</h4>
    <ul class="bulleted">
      <li{if $pageTemplate=="favorites.tpl"} class="active"{/if} style="float: none;"><a href="{$url}/MyResearch/Favorites">{translate text='Favorites'}</a></li>
      <li{if $pageTemplate=="checkedout.tpl"} class="active"{/if} style="float: none;"><a href="{$url}/MyResearch/CheckedOut">{translate text='Checked Out Items'}</a></li>
      <li{if $pageTemplate=="checkedout_history.tpl"} class="active"{/if} style="float: none;"><a href="{$url}/MyResearch/CheckedOutHistory">{translate text='Checkedout History'}</a></li>
      <li{if $pageTemplate=="holds.tpl"} class="active"{/if} style="float: none;"><a href="{$url}/MyResearch/Holds">{translate text='Holds and Recalls'}</a></li>
      <li{if $pageTemplate=="fines.tpl"} class="active"{/if} style="float: none;"><a href="{$url}/MyResearch/Fines">{translate text='Fines'}</a></li>
      <li{if $pageTemplate=="profile.tpl"} class="active"{/if} style="float: none;"><a href="{$url}/MyResearch/Profile">{translate text='Profile'}</a></li>
      {* Only highlight saved searches as active if user is logged in: *}
      <li{if $user && $pageTemplate=="history.tpl"} class="active"{/if} style="float: none;"><a href="{$url}/Search/History?require_login">{translate text='history_saved_searches'}</a></li>
    </ul>
  </div>

  <!-- Begin of costumization for MZK -->
  {if $pageTemplate=="profile.tpl"}
     <div class="sidegroup">
        <h4>{translate text=User}</h4>
        <ul class="bulleted">
          <li><a href="https://aleph.mzk.cz/F/?func=file&file_name=bor-update-password" target="_new">{translate text="Change password"}</a></li>
          <li><a href="https://aleph.mzk.cz/F/?func=bor-update" target="_new">{translate text="Change address"}</a></li>
        </ul>
     </div>
  {/if}
  <!-- End of costumization for MZK -->
  
  <div class="sidegroup">
	  <div class="listTags">
		{if $listList}
		<h4>{translate text='Your Lists'}</h4>
		<ul class="bulleted">
		  {foreach from=$listList item=listItem}
		  <li>
			{if $list && $listItem->id == $list->id}
			  <strong>{$listItem->title|escape:"html"}</strong>
			{else}
			  <a href="{$url}/MyResearch/MyList/{$listItem->id}">{$listItem->title|escape:"html"}</a>
			{/if}
			({$listItem->cnt})
		  </li>
		  {/foreach}
		</ul>
		{/if}

		{if $tagList}
		<h3 class="tag">{if $list}{$list->title|escape:"html"} {translate text='Tags'}{else}{translate text='Your Tags'}{/if}</h3>

		{if $tags}
		<ul>
		{foreach from=$tags item=tag}
		  <li>{translate text='Tag'}: {$tag|escape:"html"}
			<a href="{$url}/MyResearch/{if $list}MyList/{$list->id}{else}Favorites{/if}?{foreach from=$tags item=mytag}{if $tag != $mytag}tag[]={$mytag|escape:"url"}&amp;{/if}{/foreach}">X</a>
		</li>
		{/foreach}
		</ul>
		{/if}

		<ul class="bulleted">
		{foreach from=$tagList item=tag}
		  <li>
			<a href="{$url}/MyResearch/{if $list}MyList/{$list->id}{else}Favorites{/if}?tag[]={$tag->tag|escape:"url"}{foreach from=$tags item=mytag}&amp;tag[]={$mytag|escape:"url"}{/foreach}">{$tag->tag|escape:"html"}</a> ({$tag->cnt})
		  </li>
		{/foreach}
		</ul>
		{/if}
	  </div>
  </div>
  
</div>
