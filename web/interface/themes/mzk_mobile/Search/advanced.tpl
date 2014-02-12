<form method="GET" action="{$url}/Search/Results" name="searchForm" id="searchForm" class="search">
  <input type="hidden" name="bool0" value="AND">
  <ul class="pageitem">
    <li class="form"><input type="hidden" name="type0[]" value="Title"><input type="text" name="lookfor0[]" placeholder="{translate text="adv_search_title"}"></li>
    <li class="form"><input type="hidden" name="type0[]" value="Author"><input type="text" name="lookfor0[]" placeholder="{translate text="adv_search_author"}"></li>
    <li class="form"><input type="hidden" name="type0[]" value="year"><input type="number" name="lookfor0[]" placeholder="{translate text="adv_search_year"}"></li>
    <li class="form"><input type="hidden" name="type0[]" value="ISN"><input type="number" name="lookfor0[]" placeholder="{translate text="adv_search_isn"}"></li>
    <li class="form">
    	<select name="filter[]" id="selectStatuses">
    		<option value="" selected disabled>{translate text="Availability"}</option>
    		<option value="statuses:absent">{translate text="absent"}</option>
    		<option value="statuses:present">{translate text="present"}</option>
    		<option value="statuses:free-stack">{translate text="free-stack"}</option>
    		<option value="statuses:available_online">{translate text="available_online"}</option>
    		<option value="statuses:available_for_eod">{translate text="available_for_eod"}</option>
    	</select>
    </li>
  </ul>
  <div class="submit">
  <input type="submit" name="submit" value="{translate text="Find"}">
  </div>
</form>

<script type="text/javascript">
var allRecords = '{translate text="All records"}';
var availability = '{translate text="Availability"}';
{literal}
	function selectChanged() {
		if (this.className == "empty") {
			this.className = "";
			var option = this.firstElementChild;
			option.disabled = false;
			option.label = allRecords;
		} else if (this.selectedIndex == 0) {
			this.className = "empty";
			var option = this.firstElementChild;
			option.disabled = true;
			option.label = availability;
		}
	}
	var select = document.getElementById("selectStatuses");
	select.onchange = selectChanged;

	if (select.selectedIndex == 0) {
		select.className = "empty";
	} else {
		select.className = "";
		var option = select.firstElementChild;
		option.disabled = false;
		option.label = allRecords;
	}

	var form = document.getElementById("searchForm");
	form.onsubmit = function() {
		var select = document.getElementById("selectStatuses");
		if (select.selectedIndex == 0) {
			select.parentNode.removeChild(select);
		}
		return true;
	}
{/literal}
</script>