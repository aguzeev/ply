var dissipBaseValue =  // это считать за исходные 100%
{ // поступивший на ножницы объём
	plot_variant: '0-1',
};
var dissipSource = [
	{ // 0. подано стволов
		plot_variant: '9-0',
	},
	{ // 1. количество поданных чураков
		plot_variant: '0-7',
	},
	{ // 2. поступивший на лущение объём
		plot_variant: '0-1',
	},
	{ // 3. вышедший с ножниц объём
		plot_variant: '1-4',
	},
	{ // 4. объём карандаша
		plot_variant: '0-4',
	},
	{ // 5. выход нового сращивания
		plot_variant: '6-2',
	},
	{ // 6. выход старого сращивания
		plot_variant: '6-13',
	},
	{ // 7. выход опиловки
		plot_variant: '4-1',
	},
	{ // 8. подано на сушилку
		plot_variant: '3-4',
	},
	{ // 9. выход сушилки
		plot_variant: '3-5',
	}
];
var dissipSet = [
	{ 
		title: 'Распиловка',
		position: 'dissip-percent-1'
	},
	{ // количество поданных чураков
		title: 'Распиловка',
		position: 'dissip-percent-1'
	},
	{ // поступивший на ножницы объём
		title: 'Нарезанные листы',
		position: 'dissip-percent-2'
	},
	{ // вышедший с ножниц объём
		title: 'Распиловка',
		position: 'dissip-percent-3'
	},
	{ // объём карандаша
		title: 'Распиловка',
		position: 'dissip-percent-4'
	}
];

function drawDissipations() {
	$('#cpLoadingBar').show();
	
	var ajax_array = Array();
	var date_begin = moment($('#dateBegin').val(), "DD.MM.YYYY HH:mm").format("X");
	var date_end = moment($('#dateEnd').val(), "DD.MM.YYYY HH:mm").format("X");
	
	for (var index in dissipSource) {
		ajax_array[index] = $.ajax({
			url: 'includes/monitoring/getData2.php',
			type: 'GET',
			async: false,
			dataType: 'json',
			data: {
				plot_variant: dissipSource[index].plot_variant,
				date_begin: date_begin,
				date_end: date_end,
				divide: 0,
				use_corrections: 1,
				//callback_position: i
			},
			error: function() {
				alert("Ошибка загрузки значений для вычисления потерь.");
				$('.cpLoadingBar').hide();
			}
		});
	}
	
	$.when.apply( $, ajax_array ).done(function(cb0, cb1, cb2, cb3, cb4, cb5, cb6, cb7, cb8, cb9) {
		$('.cpLoadingBar').hide();
		
		val0 = parseFloat(cb0[0].payload); console.log(val0);
		val1 = parseFloat(cb1[0].payload); console.log(val1);
		val2 = parseFloat(cb2[0].payload); console.log(val2);
		val3 = parseFloat(cb3[0].payload); console.log(val3);
		val4 = parseFloat(cb4[0].payload); console.log(val4);
		val5 = parseFloat(cb5[0].payload); console.log(val5);
		val6 = parseFloat(cb6[0].payload); console.log(val6);
		val7 = parseFloat(cb7[0].payload); console.log(val7);
		val8 = parseFloat(cb8[0].payload); console.log(val8);
		val9 = parseFloat(cb9[0].payload); console.log(val9);
		
		// подано на лущение
		$("#dissip-shell .dissip-percent-1").text( Math.round(val2) + ' м³' );
		//$("#dissip-shell .dissip-percent-2").text( ' (' + Math.round(val3 / val2 * 100) + '%)' );
		
		// нарезка на листы
		$("#dissip-cutter .dissip-percent-1").text( Math.round(val3) + ' м³' );
		$("#dissip-cutter .dissip-percent-2").text( ' (' + Math.round(val3 / val2 * 100) + '%)' );
		
		// карандаш
		$("#dissip-pencil .dissip-percent-1").text( Math.round(val4) + ' м³' );
		$("#dissip-pencil .dissip-percent-2").text( ' (' + Math.round(val4 / val2 * 100) + '%)' );
		
		// опиловка
		$("#dissip-lopping .dissip-percent-1").text( Math.round(val7) + ' м³' );
		//$("#dissip-pencil .dissip-percent-2").text( Math.round(val7 / val2 * 100) + '%' );
		
		// сушилка
		$("#dissip-warmer-in .dissip-percent-1").text( Math.round(val8) + ' м³' );
		var dryingPercent = Math.round( val9 / val8 * 100 );
		$("#dissip-warmer-out .dissip-percent-1").text( Math.round(val9) + ' м³' );
		$("#dissip-warmer-out .dissip-percent-2").text( ' (' + dryingPercent + '%)' );
	});
	
}


$(document).ready(function(){
	$('.cpLoadingBar').hide();
	var startDateTextBox = $('#dateBegin');
	var endDateTextBox = $('#dateEnd');
	
	moment().hour() < 8 ? daysToSub = 1 : daysToSub = 0;
	//startDateTextBox.attr( 'value', moment().subtract('days', daysToSub).hour(8).minute(0).second(0).format('DD.MM.YYYY HH:mm') );
	startDateTextBox.attr( 'value', moment().startOf('month').hour(8).minute(0).second(0).format('DD.MM.YYYY HH:mm') );
	endDateTextBox.attr( 'value', moment().format('DD.MM.YYYY HH:mm') );
	
	startDateTextBox.datetimepicker({ 
		hourGrid: 4,
		minuteGrid: 10,
		defaultValue: '2013',
		//defaultValue: moment().hour(8).minute(0).second(0).format('DD.MM.YYYY HH:mm'),
		onClose: function(dateText, inst) {
			if (endDateTextBox.val() != '') {
				var testStartDate = startDateTextBox.datetimepicker('getDate');
				var testEndDate = endDateTextBox.datetimepicker('getDate');
				if (testStartDate > testEndDate)
					endDateTextBox.datetimepicker('setDate', testStartDate);
			}
			else {
				endDateTextBox.val(dateText);
			}
		},
		onSelect: function (selectedDateTime){
			var endBugfix = endDateTextBox.attr( 'value' ); // bugfix
			endDateTextBox.datetimepicker('option', 'minDate', startDateTextBox.datetimepicker('getDate') );
			endDateTextBox.attr( 'value', endBugfix ); // bugfix
		}
	});
	endDateTextBox.datetimepicker({
		hourGrid: 4,
		minuteGrid: 10,
		defaultValue: '20.06.2013',
		onClose: function(dateText, inst) {
			if (startDateTextBox.val() != '') {
				var testStartDate = startDateTextBox.datetimepicker('getDate');
				var testEndDate = endDateTextBox.datetimepicker('getDate');
				if (testStartDate > testEndDate)
					startDateTextBox.datetimepicker('setDate', testEndDate);
			}
			else {
				startDateTextBox.val(dateText);
			}
		},
		onSelect: function (selectedDateTime){
			var startBugfix = startDateTextBox.attr( 'value' ); // bugfix
			startDateTextBox.datetimepicker('option', 'maxDate', endDateTextBox.datetimepicker('getDate') );
			startDateTextBox.attr( 'value', startBugfix ); // bugfix
		}
	});

/* bugfix:
    onSelect: function (selectedDateTime){
        var start = $(this).datetimepicker('getDate');
		var end = $('#dateEnd').attr( 'value' ); // bugfix
        $('#dateEnd').datetimepicker('option', 'minDate', new Date(start.getTime()));
		$('#dateEnd').attr( 'value', end ); // bugfix
    }
*/
});