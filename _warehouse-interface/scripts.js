$(document).ready(function(e) {
	$("body").on("keypress", function(e) {
		if ( e.keyCode == 27 ) {
			$("#quantityDialogOuter").hide();
		}
	});
	$("#quantityBlock").on("click", function() {
		$("#quantityDialogOuter").toggle();
	});
});