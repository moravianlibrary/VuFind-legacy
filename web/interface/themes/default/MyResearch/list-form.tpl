<div align="left">
{if $listError}<p class="error">{$listError|translate}</p>{/if}
<form class="std" method="post" action="{$url}/MyResearch/ListEdit" name="listForm"
      onSubmit='addList(this, &quot;{translate text='add_list_fail'}&quot;); return false;'>
	<table>
		<tr>
			<td><label for="list_title">{translate text="List"}:</label></td>
			<td><input class="text" type="text" id="list_title" name="title" value="{$list->title|escape:"html"}" size="50"></td>
		</tr>
			<td><label for="list_desc">{translate text="Description"}:</label></td>
			<td><textarea class="textarea" id="list_desc" name="desc" rows="3" cols="50">{$list->desc|escape:"html"}</textarea></td>
		</tr>
		<tr>
			<td><span class="label">{translate text="Access"}:</span></td>
			<td>
				<p class="radio"><input type="radio" id="public1" name="public" value="1"> <label for="public1">{translate text="Public"}</label></p>
				<p class="radio"><input type="radio" id="public0" name="public" value="0" checked> <label for="public0">{translate text="Private"}</label></p>				
			</td>
		</tr>
		<tr>
			<td></td>
			<td>
				<input type="submit" class="form-submit" name="submit" value="{translate text="Save"}">
			</td>
		</tr>
		<tr>
			<td colspan="2">
				<input type="hidden" name="recordId" value="{$recordId}">
				<input type="hidden" name="followupModule" value="{$followupModule}">
				<input type="hidden" name="followupAction" value="{$followupAction}">
				<input type="hidden" name="followupId" value="{$followupId}">
				<input type="hidden" name="followupText" value="{translate text='Add to favorites'}">
			</td>
		</tr>
	</table>
</form>
</div>
