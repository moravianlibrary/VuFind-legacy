function switch_lang(lang) {
      document.langForm.mylang.value = lang;
      document.langForm.submit() ;
}

function writePutHoldDate() {
	var dayElement = document.getElementsByName('PutHoldDateDay')[0];
	var monthElement = document.getElementsByName('PutHoldDateMonth')[0];
	var yearElement = document.getElementsByName('PutHoldDateYear')[0];
	var day = dayElement.options[dayElement.selectedIndex].value;
	var month = monthElement.options[monthElement.selectedIndex].value;
	var year = yearElement.options[yearElement.selectedIndex].value;
	var input = document.getElementsByName('to')[0];
	input.value = day + "." + month + "." + year;
	console.debug(day + "." + month + "." + year);
	return true;
}
