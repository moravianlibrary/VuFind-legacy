<div class="pageitem myForm">
<h2>{translate text='add_favorite_prefix'} {$record.title|escape:"html"} {translate text='add_favorite_suffix'}</h2>
<form onSubmit="saveRecord('{$id|escape}', this, {literal}{{/literal}add: '{translate text='Add to favorites'}', error: '{translate text='add_favorite_fail'}', load_error: '{translate text='load_tag_error'}'{literal}}{/literal}); return false;">
<input type="hidden" name="submit" value="1" />
{if !empty($containingLists)}
  <div class="label">
  {translate text='This item is already part of the following list/lists'}:
  </div>
  <ul>
  {foreach from=$containingLists item="list"}
    <li><a href="{$url}/MyResearch/MyList/{$list.id}">{$list.title|escape:"html"}</a></li>
  {/foreach}
  </ul>
{/if}

{* Only display the list drop-down if the user has lists that do not contain
 this item OR if they have no lists at all and need to create a default list *}
{if (!empty($nonContainingLists) || (empty($containingLists) && empty($nonContainingLists))) }
  {assign var="showLists" value="true"}
{/if}

{if $showLists}
<div class="label">{translate text='Choose a List'}:</div>
<select name="list" id="list">
	{foreach from=$nonContainingLists item="list"}
    	<option value="{$list.id}"{if $list.id==$lastListUsed} selected="selected"{/if}>{$list.title|escape:"html"}</option>
    {foreachelse}
        <option value="">{translate text='My Favorites'}</option>
    {/foreach}
</select>
{/if}
<div class="label">
<a href="{$url}/MyResearch/ListEdit?id={$id|escape:"url"}" onClick="getLightbox('MyResearch', 'ListEdit', '{$id|escape}', '', '{translate text='Create a List'}', 'Record', 'Save', '{$id|escape}'); return false;">{translate text="or create a new list"}</a>
</div>
{if $showLists}
<div class="label">{translate text='Add Tags'}:</div>
<input type="text" name="mytags" id="mytags" value="" size="50">
<div class="label">{translate text='add_tag_note'}:</div>
<textarea name="notes" id="notes" rows="3" cols="50"></textarea>
<input type="submit" value="{translate text='Save'}">
{/if}
</form>
</div>
