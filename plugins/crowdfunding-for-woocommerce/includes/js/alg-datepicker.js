jQuery(document).ready(function() {
	jQuery("input[display='date']").datepicker({
		dateFormat : 'yy/mm/dd',
		firstDay: 1
	});
	jQuery("input[display='time']").timepicker();
});