<ul class="commentList" id="commentList">
{* Pull in comments from a separate file -- this separation allows the same template
   to be used for refreshing this list via AJAX. *}
{include file="Record/view-comments-list.tpl"}
</ul>

{if $user}
<form name="commentForm" id="commentForm" action="{$url}/Record/{$id|escape:"url"}/UserComments" method="POST">
  <p><textarea name="comment" rows="4" cols="50"></textarea></p>
  <input type="submit" value="{translate text="Add your comment"}"/>
</form>
{else}
<a href="{$sessionInitiator}">{translate text='You must be logged in to comment'}</a>
{/if}