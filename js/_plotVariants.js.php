<?php
$machineNames = array(
	'Лущение',
	'Ножницы',
	'Котельная',
	'Сушилка',
	'Опиловка',
	'Шлифовка',
	'Сращивание',
	'Распиловка'
);
echo 'machineNames = ' . json_encode($machineNames) . '; ';

$plotVariants = array(
	array( // 0. raute_shell
		array(
			'title' => 'Производительность',
			'machine' => 'raute_shell',
			'field' => 'curr_vol',
			'operation' => 'powerFromValue',
			'valueScale' => 1000000000, // делитель для пересчёта объёма из мм^3 в м^3
			'units' => 'м<sup>3</sup>/час',
			'yAxisMin' => 0,
			'yAxisFormat' => '%.3f'
		),
		array(
			'title' => 'Объём поданного кряжа',
			'machine' => 'raute_shell',
			'field' => 'curr_vol',
			'operation' => 'sum',
			'valueScale' => 1000000000,
			'units' => 'м<sup>3</sup>',
			'yAxisMin' => 0,
			'yAxisFormat' => '%.3f'
		),
		array(
			'title' => '% выхода лущения',
			'machine' => 'raute_shell',
			'field' => 'curr_vol, percent_exit',
			'operation' => 'avg_percent',
			'valueScale' => 1,
			'units' => '%',
			'yAxisMin' => 40,
			'yAxisFormat' => '%.1f'
		),
		array(
			'title' => '% на карандаш',
			'machine' => 'raute_shell',
			'field' => 'curr_vol, percent_core',
			'operation' => 'avg_percent',
			'valueScale' => 10,
			'units' => '%',
			'yAxisMin' => 0,
			'yAxisFormat' => '%.1f'
		),
		array(
			'title' => 'Объём карандаша',
			'machine' => 'raute_shell',
			'field' => 'core_vol',
			'operation' => 'sum',
			'valueScale' => 1000000000,
			'units' => 'м<sup>3</sup>',
			'yAxisMin' => 0,
			'yAxisFormat' => '%.3f'
		),
		array(
			'title' => 'Средний диаметр чурака',
			'machine' => 'raute_shell',
			'field' => 'r_init',
			'operation' => 'avg_diameter',
			'valueScale' => 10,
			'units' => 'см',
			'yAxisMin' => 15,
			'yAxisFormat' => '%.1f'
		),
		array(
			'title' => 'Средний диаметр карандаша',
			'machine' => 'raute_shell',
			'field' => 'r_core',
			'operation' => 'avg_diameter',
			'valueScale' => 10,
			'units' => 'см',
			'yAxisMin' => 1,
			'yAxisFormat' => '%.1f'
		),
		array(
			'title' => 'Количество чураков',
			'machine' => 'raute_shell',
			'field' => 'id',
			'operation' => 'count',
			'valueScale' => 1,
			'units' => 'шт',
			'yAxisMin' => 0,
			'yAxisFormat' => '%.0f'
		),
	),
	array( // 1. raute_cutter
		array(
			'title' => 'Производительность',
			'machine' => 'raute_cutter',
			'field' => 'bin1_incr, bin2_incr, bin3_incr',
			'operation' => 'powerFromSumOnBins',
			'valueScale' => 1000000000,
			'units' => 'м<sup>3</sup>',
			'yAxisMin' => 0,
			'yAxisFormat' => '%.3f'
		),
		array(
			'title' => 'Прирост объёма на столе 1',
			'machine' => 'raute_cutter',
			'field' => 'bin1_incr',
			'operation' => 'sum',
			'valueScale' => 1000000000,
			'units' => 'м<sup>3</sup>',
			'yAxisMin' => 0,
			'yAxisFormat' => '%.3f'
		),
		array(
			'title' => 'Прирост объёма на столе 2',
			'machine' => 'raute_cutter',
			'field' => 'bin2_incr',
			'operation' => 'sum',
			'valueScale' => 1000000000,
			'units' => 'м<sup>3</sup>',
			'yAxisMin' => 0,
			'yAxisFormat' => '%.3f'
		),
		array(
			'title' => 'Прирост объёма на столе 3',
			'machine' => 'raute_cutter',
			'field' => 'bin3_incr',
			'operation' => 'sum',
			'valueScale' => 1000000000,
			'units' => 'м<sup>3</sup>',
			'yAxisMin' => 0,
			'yAxisFormat' => '%.3f'
		),
		array(
			'title' => 'Суммарный объём',
			'machine' => 'raute_cutter',
			'field' => 'bin1_incr, bin2_incr, bin3_incr',
			'operation' => 'sumOnBins',
			'valueScale' => 1000000000,
			'units' => 'м<sup>3</sup>',
			'yAxisMin' => 0,
			'yAxisFormat' => '%.3f'
		),
	),
	array( // 2. boiler
		array(
			'title' => 'Заданная температура',
			'machine' => 'boiler',
			'field' => 'temp_specified',
			'operation' => 'avg',
			'valueScale' => 10,
			'units' => '°С',
			'yAxisMin' => 0,
			'yAxisFormat' => '%.1f'
		),
		array(
			'title' => 'Прямая температура',
			'machine' => 'boiler',
			'field' => 'temp_send',
			'operation' => 'avg',
			'valueScale' => 10,
			'units' => '°С',
			'yAxisMin' => 0,
			'yAxisFormat' => '%.1f'
		),
		array(
			'title' => 'Обратная температура',
			'machine' => 'boiler',
			'field' => 'temp_receive',
			'operation' => 'avg',
			'valueScale' => 10,
			'units' => '°С',
			'yAxisMin' => 0,
			'yAxisFormat' => '%.1f'
		),
		array(
			'title' => 'Мощность',
			'machine' => 'boiler',
			'field' => 'power',
			'operation' => 'avg',
			'valueScale' => 10,
			'units' => '°С',
			'yAxisMin' => 0,
			'yAxisFormat' => '%.1f'
		)
	),
	array( // 3. warmer
		array(
			'title' => 'Производительность',
			'machine' => 'warmer',
			'field' => 'totalSq',
			'operation' => 'powerFromSquare',
			'valueScale' => 100,
			'units' => 'м<sup>3</sup>/час',
			'yAxisMin' => 0,
			'yAxisFormat' => '%.1f'
		),
		array(
			'title' => 'Подано партий в минуту',
			'machine' => 'warmer',
			'field' => 'totalSq',
			'operation' => 'avg',
			'valueScale' => 1,
			'units' => 'парт/мин',
			'yAxisMin' => 0,
			'yAxisFormat' => '%.0f'
		),
		array(
			'title' => 'Скорость внутри сушилки',
			'machine' => 'warmer',
			'field' => 'velocity',
			'operation' => 'avg',
			'valueScale' => 16.66667,
			'units' => 'м/мин',
			'yAxisMin' => 0,
			'yAxisFormat' => '%.1f'
		),
		array(
			'title' => 'Степень заполнения стола',
			'machine' => 'warmer',
			'field' => 'sqLd',
			'operation' => 'avg',
			'valueScale' => 100,
			'units' => '%',
			'yAxisMin' => 0,
			'yAxisFormat' => '%.0f'
		),
		array(
			'title' => 'Подано сырого шпона за время',
			'machine' => 'warmer',
			'field' => 'sqLd',
			'operation' => 'sum',
			'valueScale' => 100,
			'units' => 'м<sup>2</sup>',
			'yAxisMin' => 0,
			'yAxisFormat' => '%.1f'
		),
	),
	array( // 4. lopping
		array(
			'title' => 'Производительность',
			'machine' => 'lopping',
			'field' => 'value',
			'operation' => 'powerFromValue',
			'valueScale' => 1000000000,
			'units' => 'м<sup>3</sup>/час',
			'yAxisMin' => 0,
			'yAxisFormat' => '%.1f'
		),
		array(
			'title' => 'Объём опиленных листов',
			'machine' => 'lopping',
			'field' => 'value',
			'operation' => 'sum',
			'valueScale' => 1000000000,
			'units' => 'м<sup>3</sup>',
			'yAxisMin' => 0,
			'yAxisFormat' => '%.1f'
		),
		array(
			'title' => 'Количество опиленных листов',
			'machine' => 'lopping',
			'field' => 'part_id',
			'operation' => 'count',
			'valueScale' => 1,
			'units' => 'шт',
			'yAxisMin' => 0,
			'yAxisFormat' => '%.0f'
		),
		array(
			'title' => 'Заданная толщина',
			'machine' => 'lopping',
			'field' => 'thickness',
			'operation' => 'exactVals',
			'valueScale' => 1,
			'units' => 'мм',
			'yAxisMin' => 0,
			'yAxisFormat' => '%.0f'
		),
		array(
			'title' => 'Скорость 1-й опиловки',
			'machine' => 'lopping',
			'field' => 'velocity_long',
			'operation' => 'avg',
			'valueScale' => 1,
			'units' => 'м/мин',
			'yAxisMin' => 0,
			'yAxisFormat' => '%.0f'
		),
		array(
			'title' => 'Скорость 2-й опиловки',
			'machine' => 'lopping',
			'field' => 'velocity_short',
			'operation' => 'avg',
			'valueScale' => 1,
			'units' => 'м/мин',
			'yAxisMin' => 0,
			'yAxisFormat' => '%.0f'
		),
	),
	array( // 5. grinding
		array(
			'title' => 'Производительность',
			'machine' => 'grinding',
			'field' => 'value_bin1, value_bin2, value_bin3',
			'operation' => 'powerFromSumOnBins',
			'valueScale' => 1,
			'units' => 'м<sup>3</sup>/час',
			'yAxisMin' => 0,
			'yAxisFormat' => '%.1f'
		),
		array(
			'title' => 'Объём листов на столе 1',
			'machine' => 'grinding',
			'field' => 'value_bin1',
			'operation' => 'sum',
			'valueScale' => 1000000000,
			'units' => 'м<sup>3</sup>',
			'yAxisMin' => 0,
			'yAxisFormat' => '%.1f'
		),
		array(
			'title' => 'Объём листов на столе 2',
			'machine' => 'grinding',
			'field' => 'value_bin2',
			'operation' => 'sum',
			'valueScale' => 1000000000,
			'units' => 'м<sup>3</sup>',
			'yAxisMin' => 0,
			'yAxisFormat' => '%.1f'
		),
		array(
			'title' => 'Объём листов на столе 3',
			'machine' => 'grinding',
			'field' => 'value_bin3',
			'operation' => 'sum',
			'valueScale' => 1000000000,
			'units' => 'м<sup>3</sup>',
			'yAxisMin' => 0,
			'yAxisFormat' => '%.1f'
		),
		array(
			'title' => 'Скорость шлифовки',
			'machine' => 'grinding',
			'field' => 'velocity',
			'operation' => 'avg',
			'valueScale' => 1,
			'units' => 'м/мин',
			'yAxisMin' => 0,
			'yAxisFormat' => '%.1f'
		),
		array(
			'title' => 'Количество поданных листов',
			'machine' => 'grinding',
			'field' => 'part_id',
			'operation' => 'count',
			'valueScale' => 1,
			'units' => 'шт',
			'yAxisMin' => 0,
			'yAxisFormat' => '%.0f'
		),
		array(
			'title' => 'Заданная толщина',
			'machine' => 'grinding',
			'field' => 'thickness',
			'operation' => 'exactVals',
			'valueScale' => 1,
			'units' => 'мм',
			'yAxisMin' => 0,
			'yAxisFormat' => '%.0f'
		),
		array(
			'title' => 'Суммарный объём',
			'machine' => 'grinding',
			'field' => 'value_bin1, value_bin2, value_bin3',
			'operation' => 'sumOnBins',
			'valueScale' => 1,
			'units' => 'мм',
			'yAxisMin' => 0,
			'yAxisFormat' => '%.1f'
		),
	),
	array( // 6. merger
		array(
			'title' => 'Производительность (нов)',
			'machine' => 'merger',
			'field' => 'merg_total_value',
			'operation' => 'powerFromValue',
			'valueScale' => 1000000000,
			'units' => 'м<sup>3</sup>/час',
			'yAxisMin' => 0,
			'yAxisFormat' => '%.1f'
		),
		array(
			'title' => 'Производительность (стар)',
			'machine' => 'merger_old',
			'field' => 'merg_total_value',
			'operation' => 'powerFromValue',
			'valueScale' => 1000000000,
			'units' => 'м<sup>3</sup>/час',
			'yAxisMin' => 0,
			'yAxisFormat' => '%.1f'
		),
		array(
			'title' => 'Объём (новая линия)',
			'machine' => 'merger',
			'field' => 'merg_total_value',
			'operation' => 'powerFromValue',
			'valueScale' => 1000000000,
			'units' => 'м<sup>3</sup>',
			'yAxisMin' => 0,
			'yAxisFormat' => '%.2f'
		),
		array(
			'title' => 'Объём с пресса 1',
			'machine' => 'merger',
			'field' => 'press1_merg_value',
			'operation' => 'sum',
			'valueScale' => 1000000000,
			'units' => 'м<sup>3</sup>',
			'yAxisMin' => 0,
			'yAxisFormat' => '%.2f'
		),
		array(
			'title' => 'Объём с пресса 2',
			'machine' => 'merger',
			'field' => 'press2_merg_value',
			'operation' => 'sum',
			'valueScale' => 1000000000,
			'units' => 'м<sup>3</sup>',
			'yAxisMin' => 0,
			'yAxisFormat' => '%.2f'
		),
		array(
			'title' => 'Объём с пресса 3',
			'machine' => 'merger',
			'field' => 'press3_merg_value',
			'operation' => 'sum',
			'valueScale' => 1000000000,
			'units' => 'м<sup>3</sup>',
			'yAxisMin' => 0,
			'yAxisFormat' => '%.2f'
		),
		array(
			'title' => 'Всего сращиваний',
			'machine' => 'merger',
			'field' => 'press1_merg_count, press2_merg_count, press3_merg_count',
			'operation' => 'sumOnBins',
			'valueScale' => 1,
			'units' => 'шт',
			'yAxisMin' => 0,
			'yAxisFormat' => '%.0f'
		),
		array(
			'title' => 'Число листов на усовке',
			'machine' => 'merger',
			'field' => 'count',
			'operation' => 'sum',
			'valueScale' => 1,
			'units' => 'шт',
			'yAxisMin' => 0,
			'yAxisFormat' => '%.0f'
		),
		array(
			'title' => 'Длина опиловки',
			'machine' => 'merger',
			'field' => 'length',
			'operation' => 'exactVals',
			'valueScale' => 1,
			'units' => 'мм',
			'yAxisMin' => 0,
			'yAxisFormat' => '%.0f'
		),
		array(
			'title' => 'Длина опиловки',
			'machine' => 'merger',
			'field' => 'width',
			'operation' => 'exactVals',
			'valueScale' => 1,
			'units' => 'мм',
			'yAxisMin' => 0,
			'yAxisFormat' => '%.0f'
		),
		array(
			'title' => 'Число сращиваний на прессе 1',
			'machine' => 'merger',
			'field' => 'press1_merg_count',
			'operation' => 'sum',
			'valueScale' => 1,
			'units' => 'шт',
			'yAxisMin' => 0,
			'yAxisFormat' => '%.0f'
		),
		array(
			'title' => 'Число сращиваний на прессе 2',
			'machine' => 'merger',
			'field' => 'press2_merg_count',
			'operation' => 'sum',
			'valueScale' => 1,
			'units' => 'шт',
			'yAxisMin' => 0,
			'yAxisFormat' => '%.0f'
		),
		array(
			'title' => 'Число сращиваний на прессе 3',
			'machine' => 'merger',
			'field' => 'press3_merg_count',
			'operation' => 'sum',
			'valueScale' => 1,
			'units' => 'шт',
			'yAxisMin' => 0,
			'yAxisFormat' => '%.0f'
		),
		
		
		array(
			'title' => 'Объём (старая линия)',
			'machine' => 'merger_old',
			'field' => 'merg_total_value',
			'operation' => 'powerFromValue',
			'valueScale' => 1000000000,
			'units' => 'м<sup>3</sup>',
			'yAxisMin' => 0,
			'yAxisFormat' => '%.2f'
		),
		array(
			'title' => 'Объём с пресса 1',
			'machine' => 'merger_old',
			'field' => 'press1_merg_value',
			'operation' => 'sum',
			'valueScale' => 1000000000,
			'units' => 'м<sup>3</sup>',
			'yAxisMin' => 0,
			'yAxisFormat' => '%.2f'
		),
		array(
			'title' => 'Объём с пресса 2',
			'machine' => 'merger_old',
			'field' => 'press2_merg_value',
			'operation' => 'sum',
			'valueScale' => 1000000000,
			'units' => 'м<sup>3</sup>',
			'yAxisMin' => 0,
			'yAxisFormat' => '%.2f'
		),
		array(
			'title' => 'Объём с пресса 3',
			'machine' => 'merger_old',
			'field' => 'press3_merg_value',
			'operation' => 'sum',
			'valueScale' => 1000000000,
			'units' => 'м<sup>3</sup>',
			'yAxisMin' => 0,
			'yAxisFormat' => '%.2f'
		),
		array(
			'title' => 'Всего сращиваний',
			'machine' => 'merger_old',
			'field' => 'press1_merg_count, press2_merg_count, press3_merg_count',
			'operation' => 'sumOnBins',
			'valueScale' => 1,
			'units' => 'шт',
			'yAxisMin' => 0,
			'yAxisFormat' => '%.0f'
		),
		array(
			'title' => 'Число листов на усовке',
			'machine' => 'merger_old',
			'field' => 'count',
			'operation' => 'sum',
			'valueScale' => 1,
			'units' => 'шт',
			'yAxisMin' => 0,
			'yAxisFormat' => '%.0f'
		),
		array(
			'title' => 'Длина опиловки',
			'machine' => 'merger_old',
			'field' => 'length',
			'operation' => 'exactVals',
			'valueScale' => 1,
			'units' => 'мм',
			'yAxisMin' => 0,
			'yAxisFormat' => '%.0f'
		),
		array(
			'title' => 'Длина опиловки',
			'machine' => 'merger_old',
			'field' => 'width',
			'operation' => 'exactVals',
			'valueScale' => 1,
			'units' => 'мм',
			'yAxisMin' => 0,
			'yAxisFormat' => '%.0f'
		),
		array(
			'title' => 'Число сращиваний на прессе 1',
			'machine' => 'merger_old',
			'field' => 'press1_merg_count',
			'operation' => 'sum',
			'valueScale' => 1,
			'units' => 'шт',
			'yAxisMin' => 0,
			'yAxisFormat' => '%.0f'
		),
		array(
			'title' => 'Число сращиваний на прессе 2',
			'machine' => 'merger_old',
			'field' => 'press2_merg_count',
			'operation' => 'sum',
			'valueScale' => 1,
			'units' => 'шт',
			'yAxisMin' => 0,
			'yAxisFormat' => '%.0f'
		),
		array(
			'title' => 'Число сращиваний на прессе 3',
			'machine' => 'merger_old',
			'field' => 'press3_merg_count',
			'operation' => 'sum',
			'valueScale' => 1,
			'units' => 'шт',
			'yAxisMin' => 0,
			'yAxisFormat' => '%.0f'
		),
	),
	array( // 7. saw
		array(
			'title' => 'Подано стволов',
			'machine' => 'saw',
			'field' => 'tree_format',
			'operation' => 'count',
			'valueScale' => 1,
			'units' => 'шт',
			'yAxisMin' => 0,
			'yAxisFormat' => '%.0f'
		),
	),
);

echo 'plotVariants = ' . json_encode($plotVariants);

?>