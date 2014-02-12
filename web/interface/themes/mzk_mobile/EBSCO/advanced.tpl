<form method="GET" action="{$url}/EBSCO/Search" name="searchForm" id="searchForm" class="search">
  <input type="hidden" name="bool0" value="AND">
  <ul class="pageitem">
    <li class="form"><input type="hidden" name="type0[]" value="Title"><input type="text" name="lookfor0[]" placeholder="{translate text="adv_search_title"}"></li>
    <li class="form"><input type="hidden" name="type0[]" value="Author"><input type="text" name="lookfor0[]" placeholder="{translate text="adv_search_author"}"></li>
    <li class="form"><input type="hidden" name="type0[]" value="Subject"><input type="text" name="lookfor0[]" placeholder="{translate text="adv_search_subject"}"></li>
    <li class="form"><input type="hidden" name="type0[]" value="AllFields"><input type="text" name="lookfor0[]" placeholder="{translate text="adv_search_all"}"></li>
  </ul>
  
	<div class="submit">
		<input type="submit" name="submit" value="{translate text="Find"}">
	</div>
</form>