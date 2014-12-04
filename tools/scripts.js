var p_url=location.search.substring(1);
var parametr=p_url.split("&");
var POSTvalues= new Array();

for(i in parametr) {
    var j=parametr[i].split("=");
    POSTvalues[j[0]]=unescape(j[1]);
}
mach = (typeof POSTvalues['mach'] != "undefined") ? POSTvalues['mach'] : 0;
type = (typeof POSTvalues['type'] != "undefined") ? POSTvalues['type'] : 0;


function initDateTime() {
	currentTime = new Date();
	month = currentTime.getMonth() + 1;
		if (month < 10) month = '0' + month;
	day = currentTime.getDate();
		if (day < 10) day = '0' + day;
	year = currentTime.getFullYear();
	hours = currentTime.getHours();
		if (hours < 10) hours = '0' + hours;
	minutes = currentTime.getMinutes();
		if (minutes < 10) minutes = '0' + minutes;
}
function timeFormatting(myDate) {
	var month = myDate.getMonth() + 1;
		if (month < 10) month = '0' + month;
	var day = myDate.getDate();
		if (day < 10) day = '0' + day;
	var year = myDate.getFullYear();
	var hours = myDate.getHours();
		if (hours < 10) hours = '0' + hours;
	var minutes = myDate.getMinutes();
		if (minutes < 10) minutes = '0' + minutes;
	myDateTime = day + '.' + month + '.' + year + ' ' + hours + ':' + minutes;
	return myDateTime;
}

var d1_now = new Date(); var d2_now = new Date();
d1_now.setHours(08, 00, 00);
if (d2_now.getHours() < 8) { // ещё вчера :)
	d1_now = addDays(d1_now, -1);
}


$(document).ready(function(){
	$('#dateBegin').datetimepicker({
	hourGrid: 4,
	minuteGrid: 10,
    onClose: function(dateText, inst) {
        var endDateTextBox = $('#dateEnd');
        if (endDateTextBox.val() != '') {
            var testStartDate = new Date(dateText);
            var testEndDate = new Date(endDateTextBox.val());
            if (testStartDate > testEndDate)
                endDateTextBox.val(dateText);
        }
        else {
            endDateTextBox.val(dateText);
        }
    },
    onSelect: function (selectedDateTime){
        var start = $(this).datetimepicker('getDate');
        $('#dateEnd').datetimepicker('option', 'minDate', new Date(start.getTime()));
    }
});
	$('#dateEnd').datetimepicker({
		hourGrid: 4,
		minuteGrid: 10,
		onClose: function(dateText, inst) {
			var startDateTextBox = $('#dateBegin');
			if (startDateTextBox.val() != '') {
				var testStartDate = new Date(startDateTextBox.val());
				var testEndDate = new Date(dateText);
				if (testStartDate > testEndDate)
					startDateTextBox.val(dateText);
			}
			else {
				startDateTextBox.val(dateText);
			}
		},
		onSelect: function (selectedDateTime){
			var end = $(this).datetimepicker('getDate');
			$('#dateBegin').datetimepicker('option', 'maxDate', new Date(end.getTime()) );
		}
	});
	
	d1 = (typeof POSTvalues['d1'] != "undefined") ? POSTvalues['d1'] : timeFormatting(d1_now);
	d2 = (typeof POSTvalues['d2'] != "undefined") ? POSTvalues['d2'] : timeFormatting(d2_now);
	$('#dateBegin').attr( 'value', d1 );
	$('#dateEnd').attr( 'value', d2 );
	
});

function submitControl() {
	machine = $('input[name=machine]:checked').attr('value');
	type = $('input[name=type]:checked').attr('value');
	d1 = strToMYSQLDate($('#dateBegin').val());
	d2 = strToMYSQLDate($('#dateEnd').val());
	
	if (typeof POSTvalues['machine'] == "undefined") newUrl = window.location.href + '?&machine=' + machine;
		else newUrl = window.location.href.replace(POSTvalues['machine'], machine);
	if (typeof POSTvalues['type'] == "undefined") newUrl = newUrl + '&type=' + type;
		else newUrl = newUrl.replace(POSTvalues['type'], type);
	if (typeof POSTvalues['d1'] == "undefined") newUrl = newUrl + '&d1=' + d1;
		else newUrl = newUrl.replace(POSTvalues['d1'], d1);
	if (typeof POSTvalues['d2'] == "undefined") newUrl = newUrl + '&d2=' + d2;
		else newUrl = newUrl.replace(POSTvalues['d2'], d2);
		
	window.location.href = newUrl;
}
function strToMYSQLDate(str) {
	var temp = str.split(' ');
	var days = temp[0].split(".");
	var time = temp[1];

	return days[2] + '-' + days[1] + '-' + days[0] + ' ' + time;
}
function resetFilter() {
	var temp = window.location.href.split("?");
	window.location.href = temp[0];
}