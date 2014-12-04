var plotVariants = [
	raute_shell = [ // 0
		{
			title: 'Производительность',
			machine: 'raute_shell',
			field: 'curr_vol',
			operation: 'powerFromValue',
			valueScale: 1000000000, // делитель для пересчёта объёма из мм^3 в м^3
			units: 'м<sup>3</sup>/час',
			yAxisMin: 0,
			yAxisFormat: '%.3f'
		},
		{
			title: 'Объём поданного кряжа',
			machine: 'raute_shell',
			field: 'curr_vol',
			operation: 'sum',
			valueScale: 1000000000, // делитель для пересчёта объёма из мм^3 в м^3
			units: 'м<sup>3</sup>',
			yAxisMin: 0,
			yAxisFormat: '%.3f'
		},
		{
			title: '% выхода лущения',
			machine: 'raute_shell',
			field: 'curr_vol, percent_exit',
			operation: 'avg_percent',
			valueScale: 1,
			units: '%',
			yAxisMin: 40,
			yAxisFormat: '%.1f'
		},
		{
			title: '% на карандаш',
			machine: 'raute_shell',
			field: 'curr_vol, percent_core',
			operation: 'avg_percent',
			valueScale: 10,
			units: '%',
			yAxisMin: 0,
			yAxisFormat: '%.1f'
		},
		{
			title: 'Объём карандаша',
			machine: 'raute_shell',
			field: 'core_vol',
			operation: 'sum',
			valueScale: 1000000000,
			units: 'м<sup>3</sup>',
			yAxisMin: 0,
			yAxisFormat: '%.3f'
		},
		{
			title: 'Средний диаметр чурака',
			machine: 'raute_shell',
			field: 'r_init',
			operation: 'avg_diameter',
			valueScale: 10,
			units: 'см',
			yAxisMin: 15,
			yAxisFormat: '%.1f'
		},
		{
			title: 'Средний диаметр карандаша',
			machine: 'raute_shell',
			field: 'r_core',
			operation: 'avg_diameter',
			valueScale: 10,
			units: 'см',
			yAxisMin: 1,
			yAxisFormat: '%.1f'
		},
		{
			title: 'Количество чураков',
			machine: 'raute_shell',
			field: 'id',
			operation: 'count',
			valueScale: 1,
			units: 'шт',
			yAxisMin: 0,
			yAxisFormat: '%.0f'
		},
	],
	raute_cutter = [
		{
			title: 'Производительность',
			machine: 'raute_cutter',
			field: 'bin1_incr, bin2_incr, bin3_incr',
			operation: 'powerFromSumOnBins',
			valueScale: 1000000000,
			units: 'м<sup>3</sup>/час',
			yAxisMin: 0,
			yAxisFormat: '%.3f'
		},
		{
			title: 'Прирост объёма на столе 1',
			machine: 'raute_cutter',
			field: 'bin1_incr',
			operation: 'sum',
			valueScale: 1000000000,
			units: 'м<sup>3</sup>',
			yAxisMin: 0,
			yAxisFormat: '%.3f'
		},
		{
			title: 'Прирост объёма на столе 2',
			machine: 'raute_cutter',
			field: 'bin2_incr',
			operation: 'sum',
			valueScale: 1000000000,
			units: 'м<sup>3</sup>',
			yAxisMin: 0,
			yAxisFormat: '%.3f'
		},
		{
			title: 'Прирост объёма на столе 3',
			machine: 'raute_cutter',
			field: 'bin3_incr',
			operation: 'sum',
			valueScale: 1000000000,
			units: 'м<sup>3</sup>',
			yAxisMin: 0,
			yAxisFormat: '%.3f'
		},
		{
			title: 'Суммарный объём',
			machine: 'raute_cutter',
			field: 'bin1_incr, bin2_incr, bin3_incr',
			operation: 'sumOnBins',
			valueScale: 1000000000,
			units: 'м<sup>3</sup>',
			yAxisMin: 0,
			yAxisFormat: '%.3f'
		},
	],
	boiler = [
		{
			title: 'Заданная температура',
			machine: 'boiler',
			field: 'temp_specified',
			operation: 'avg',
			valueScale: 10,
			units: '°С',
			yAxisMin: 0,
			yAxisFormat: '%.1f'
		},
		{
			title: 'Прямая температура',
			machine: 'boiler',
			field: 'temp_send',
			operation: 'avg',
			valueScale: 10,
			units: '°С',
			yAxisMin: 0,
			yAxisFormat: '%.1f'
		},
		{
			title: 'Обратная температура',
			machine: 'boiler',
			field: 'temp_receive',
			operation: 'avg',
			valueScale: 10,
			units: '°С',
			yAxisMin: 0,
			yAxisFormat: '%.1f'
		},
		{
			title: 'Мощность',
			machine: 'boiler',
			field: 'power',
			operation: 'avg',
			valueScale: 10,
			units: '%',
			yAxisMin: 0,
			yAxisFormat: '%.1f'
		}
	],
	warmer = [
		{
			title: 'Производительность',
			machine: 'warmer',
			field: 'totalSq',
			operation: 'powerFromSquare',
			valueScale: 100,
			units: 'м<sup>3</sup>/час',
			yAxisMin: 0,
			yAxisFormat: '%.1f'
		},
		{
			title: 'Подано партий в минуту',
			machine: 'warmer',
			field: 'packages',
			operation: 'avg',
			valueScale: 1,
			units: 'парт/мин',
			yAxisMin: 0,
			yAxisFormat: '%.0f'
		},
		{
			title: 'Скорость внутри сушилки',
			machine: 'warmer',
			field: 'velocity',
			operation: 'avg',
			valueScale: 16.66667,
			units: 'м/мин',
			yAxisMin: 0,
			yAxisFormat: '%.1f'
		},
		{
			title: 'Степень заполнения стола',
			machine: 'warmer',
			field: 'sqLd',
			operation: 'avg_notzero',
			valueScale: 1,
			units: '%',
			yAxisMin: 0,
			yAxisFormat: '%.0f'
		},
		{
			title: 'Подано сырого шпона за время',
			machine: 'warmer',
			field: 'totalSq',
			operation: 'sum',
			valueScale: 100,
			units: 'м<sup>2</sup>',
			yAxisMin: 0,
			yAxisFormat: '%.1f'
		},
	],
	lopping = [
		{
			title: 'Производительность',
			machine: 'lopping',
			field: 'value',
			operation: 'powerFromValue',
			valueScale: 1000000000,
			units: 'м<sup>3</sup>/час',
			yAxisMin: 0,
			yAxisFormat: '%.1f'
		},
		{
			title: 'Объём опиленных листов',
			machine: 'lopping',
			field: 'value',
			operation: 'sum',
			valueScale: 1000000000,
			units: 'м<sup>3</sup>',
			yAxisMin: 0,
			yAxisFormat: '%.1f'
		},
		{
			title: 'Количество опиленных листов',
			machine: 'lopping',
			field: 'part_id',
			operation: 'count',
			valueScale: 1,
			units: 'шт',
			yAxisMin: 0,
			yAxisFormat: '%.0f'
		},
		{
			title: 'Заданная толщина',
			machine: 'lopping',
			field: 'thickness',
			operation: 'exactVals',
			valueScale: 1,
			units: 'мм',
			yAxisMin: 0,
			yAxisFormat: '%.0f'
		},
		{
			title: 'Скорость 1-й опиловки',
			machine: 'lopping',
			field: 'velocity_long',
			operation: 'avg',
			valueScale: 1,
			units: 'м/мин',
			yAxisMin: 0,
			yAxisFormat: '%.0f'
		},
		{
			title: 'Скорость 2-й опиловки',
			machine: 'lopping',
			field: 'velocity_short',
			operation: 'avg',
			valueScale: 1,
			units: 'м/мин',
			yAxisMin: 0,
			yAxisFormat: '%.0f'
		},
	],
	grinding = [
		{
			title: 'Производительность',
			machine: 'grinding',
			field: 'value_bin1, value_bin2, value_bin3',
			operation: 'powerFromSumOnBins',
			valueScale: 1,
			units: 'м<sup>3</sup>/час',
			yAxisMin: 0,
			yAxisFormat: '%.1f'
		},
		{
			title: 'Объём листов на столе 1',
			machine: 'grinding',
			field: 'value_bin1',
			operation: 'sum',
			valueScale: 1000000000,
			units: 'м<sup>3</sup>',
			yAxisMin: 0,
			yAxisFormat: '%.1f'
		},
		{
			title: 'Объём листов на столе 2',
			machine: 'grinding',
			field: 'value_bin2',
			operation: 'sum',
			valueScale: 1000000000,
			units: 'м<sup>3</sup>',
			yAxisMin: 0,
			yAxisFormat: '%.1f'
		},
		{
			title: 'Объём листов на столе 3',
			machine: 'grinding',
			field: 'value_bin3',
			operation: 'sum',
			valueScale: 1000000000,
			units: 'м<sup>3</sup>',
			yAxisMin: 0,
			yAxisFormat: '%.1f'
		},
		{
			title: 'Скорость шлифовки',
			machine: 'grinding',
			field: 'velocity',
			operation: 'avg',
			valueScale: 1,
			units: 'м/мин',
			yAxisMin: 0,
			yAxisFormat: '%.1f'
		},
		{
			title: 'Количество поданных листов',
			machine: 'grinding',
			field: 'part_id',
			operation: 'count',
			valueScale: 1,
			units: 'шт',
			yAxisMin: 0,
			yAxisFormat: '%.0f'
		},
		{
			title: 'Заданная толщина',
			machine: 'grinding',
			field: 'thickness',
			operation: 'exactVals',
			valueScale: 1,
			units: 'мм',
			yAxisMin: 0,
			yAxisFormat: '%.0f'
		},
		{
			title: 'Суммарный объём',
			machine: 'grinding',
			field: 'value_bin1, value_bin2, value_bin3',
			operation: 'sumOnBins',
			valueScale: 1,
			units: 'мм',
			yAxisMin: 0,
			yAxisFormat: '%.1f'
		},
	],
	merger = [
		{
			title: 'Производительность (нов)',
			machine: 'merger',
			field: 'merg_total_value',
			operation: 'powerFromValue',
			valueScale: 1000000000,
			units: 'м<sup>3</sup>/час',
			yAxisMin: 0,
			yAxisFormat: '%.1f'
		},
		{
			title: 'Производительность (стар)',
			machine: 'merger_old',
			field: 'merg_total_value',
			operation: 'powerFromValue',
			valueScale: 1000000000,
			units: 'м<sup>3</sup>/час',
			yAxisMin: 0,
			yAxisFormat: '%.1f'
		},
		{
			title: 'Объём (новая линия)',
			machine: 'merger',
			field: 'merg_total_value',
			operation: 'sum',
			valueScale: 1000000000,
			units: 'м<sup>3</sup>',
			yAxisMin: 0,
			yAxisFormat: '%.2f'
		},
		{
			title: 'Объём с пресса 1',
			machine: 'merger',
			field: 'press1_merg_value',
			operation: 'sum',
			valueScale: 1000000000,
			units: 'м<sup>3</sup>',
			yAxisMin: 0,
			yAxisFormat: '%.2f'
		},
		{
			title: 'Объём с пресса 2',
			machine: 'merger',
			field: 'press2_merg_value',
			operation: 'sum',
			valueScale: 1000000000,
			units: 'м<sup>3</sup>',
			yAxisMin: 0,
			yAxisFormat: '%.2f'
		},
		{
			title: 'Объём с пресса 3',
			machine: 'merger',
			field: 'press3_merg_value',
			operation: 'sum',
			valueScale: 1000000000,
			units: 'м<sup>3</sup>',
			yAxisMin: 0,
			yAxisFormat: '%.2f'
		},
		{
			title: 'Всего сращиваний',
			machine: 'merger',
			field: 'press1_merg_count, press2_merg_count, press3_merg_count',
			operation: 'sumOnBins',
			valueScale: 1,
			units: 'шт',
			yAxisMin: 0,
			yAxisFormat: '%.0f'
		},
		{
			title: 'Число листов на усовке',
			machine: 'merger',
			field: 'count',
			operation: 'sum',
			valueScale: 1,
			units: 'шт',
			yAxisMin: 0,
			yAxisFormat: '%.0f'
		},
		{
			title: 'Длина опиловки',
			machine: 'merger',
			field: 'length',
			operation: 'exactVals',
			valueScale: 1,
			units: 'мм',
			yAxisMin: 0,
			yAxisFormat: '%.0f'
		},
		{
			title: 'Ширина опиловки',
			machine: 'merger',
			field: 'width',
			operation: 'exactVals',
			valueScale: 1,
			units: 'мм',
			yAxisMin: 0,
			yAxisFormat: '%.0f'
		},
		{
			title: 'Число сращиваний на прессе 1',
			machine: 'merger',
			field: 'press1_merg_count',
			operation: 'sum',
			valueScale: 1,
			units: 'шт',
			yAxisMin: 0,
			yAxisFormat: '%.0f'
		},
		{
			title: 'Число сращиваний на прессе 2',
			machine: 'merger',
			field: 'press2_merg_count',
			operation: 'sum',
			valueScale: 1,
			units: 'шт',
			yAxisMin: 0,
			yAxisFormat: '%.0f'
		},
		{
			title: 'Число сращиваний на прессе 3',
			machine: 'merger',
			field: 'press3_merg_count',
			operation: 'sum',
			valueScale: 1,
			units: 'шт',
			yAxisMin: 0,
			yAxisFormat: '%.0f'
		},
		
		
		
		{
			title: 'Объём (старая линия)',
			machine: 'merger_old',
			field: 'merg_total_value',
			operation: 'sum',
			valueScale: 1000000000,
			units: 'м<sup>3</sup>',
			yAxisMin: 0,
			yAxisFormat: '%.2f'
		},
		{
			title: 'Объём с пресса 1',
			machine: 'merger_old',
			field: 'press1_merg_value',
			operation: 'sum',
			valueScale: 1000000000,
			units: 'м<sup>3</sup>',
			yAxisMin: 0,
			yAxisFormat: '%.2f'
		},
		{
			title: 'Объём с пресса 2',
			machine: 'merger_old',
			field: 'press2_merg_value',
			operation: 'sum',
			valueScale: 1000000000,
			units: 'м<sup>3</sup>',
			yAxisMin: 0,
			yAxisFormat: '%.2f'
		},
		{
			title: 'Объём с пресса 3',
			machine: 'merger_old',
			field: 'press3_merg_value',
			operation: 'sum',
			valueScale: 1000000000,
			units: 'м<sup>3</sup>',
			yAxisMin: 0,
			yAxisFormat: '%.2f'
		},
		{
			title: 'Всего сращиваний',
			machine: 'merger_old',
			field: 'press1_merg_count, press2_merg_count, press3_merg_count',
			operation: 'sumOnBins',
			valueScale: 1,
			units: 'шт',
			yAxisMin: 0,
			yAxisFormat: '%.0f'
		},
		{
			title: 'Число листов на усовке',
			machine: 'merger_old',
			field: 'count',
			operation: 'sum',
			valueScale: 1,
			units: 'шт',
			yAxisMin: 0,
			yAxisFormat: '%.0f'
		},
		{
			title: 'Длина опиловки',
			machine: 'merger_old',
			field: 'length',
			operation: 'exactVals',
			valueScale: 1,
			units: 'мм',
			yAxisMin: 0,
			yAxisFormat: '%.0f'
		},
		{
			title: 'Ширина опиловки',
			machine: 'merger_old',
			field: 'width',
			operation: 'exactVals',
			valueScale: 1,
			units: 'мм',
			yAxisMin: 0,
			yAxisFormat: '%.0f'
		},
		{
			title: 'Число сращиваний на прессе 1',
			machine: 'merger_old',
			field: 'press1_merg_count',
			operation: 'sum',
			valueScale: 1,
			units: 'шт',
			yAxisMin: 0,
			yAxisFormat: '%.0f'
		},
		{
			title: 'Число сращиваний на прессе 2',
			machine: 'merger_old',
			field: 'press2_merg_count',
			operation: 'sum',
			valueScale: 1,
			units: 'шт',
			yAxisMin: 0,
			yAxisFormat: '%.0f'
		},
		{
			title: 'Число сращиваний на прессе 3',
			machine: 'merger_old',
			field: 'press3_merg_count',
			operation: 'sum',
			valueScale: 1,
			units: 'шт',
			yAxisMin: 0,
			yAxisFormat: '%.0f'
		}
	],
	saw = [
		{
			title: 'Подано стволов',
			machine: 'saw',
			field: 'tree_format',
			operation: 'count',
			valueScale: 1,
			units: 'шт',
			yAxisMin: 0,
			yAxisFormat: '%.0f'
		}
	]
];