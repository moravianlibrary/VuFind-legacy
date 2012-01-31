<form class="std" onSubmit='SaveTag(&quot;{$id|escape}&quot;, this,
    {literal}{{/literal}success: &quot;{translate text='add_tag_success'}&quot;, load_error: &quot;{translate text='load_tag_error'}&quot;, save_error: &quot;{translate text='add_tag_error'}&quot;{literal}}{/literal}
    ); return false;' method="POST">
<input type="hidden" name="submit" value="1" />
<table>
  <tr>
	<td><label for="tag">{translate text="Tags"}:</label></td>
	<td><input class="text" type="text" name="tag" id="tag" value="" size="50"></td>
  </tr>
  <tr>
	<td>&nbsp;</td>
	<td>{translate text="add_tag_note"}</td>
  </tr>
  <tr>
	<td></td><td><input type="submit" class="form-submit" value="{translate text='Save'}"></td>
  </tr>
</table>
</form>