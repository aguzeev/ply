<?php

defined('_EXEC') or die;
$category = 1;
$ACCESSED_MODULE = 10;
$LOG_TITLE = 'Склад';

include('includes/cerber.php');
require_once('includes/init.php');

$activeComponent = 'warehouse';

?>
<script language="javascript" src="js/jquery.tmpl.min.js"></script>
<script language="javascript" src="js/module.warehouse.produced.js"></script>

<!--<p>Нормальная фанера</p>
<table class="whProduced">
	<thead>
      <tr>
        <td class="whType">Марка</td>
        <td class="whWidth">Ширина</td>
        <td class="whLength">Длина</td>
        <td class="whThickness">Толщина</td>
        <td class="whSort1">Сорт 1</td>
        <td class="whSort2">Сорт 2</td>
        <td class="whSanding">Шлифов.</td>
        <td class="whPacksQuantity">Количество пакетов</td>
        <td class="whListsInPack">Листов в пакете</td>
        <td class="whTotalLists">Всего листов</td>
        <td class="whVolume">Объём</td>
        <td class="whClient">Клиент</td>
        <td class="whActions">&nbsp;</td>
      </tr>
    </thead>
    <tbody>
<?php
  	$sqlSelect = "SELECT * FROM `wh_packs`";
	$resultSelect = mysql_query($sqlSelect, $connection_stat) or die( "error in query for 'resultSelect': " . mysql_error($connection_stat) );
	
	$prevReadyDate = "";
	if ( mysql_num_rows($resultSelect) > 0 ) {
	}
?>
      <tr>
        <td class="whType">ФСФ</td>
        <td class="whWidth">1220</td>
        <td class="whLength">2440</td>
        <td class="whThickness">18</td>
        <td class="whSort1">2</td>
        <td class="whSort2">3</td>
        <td class="whSanding">Ш2</td>
        <td class="whPacksQuantity">2</td>
        <td class="whListsInPack">75</td>
        <td class="whTotalLists">150</td>
        <td class="whVolume">6,79</td>
        <td class="whClient">ОАО «Газпромнефть-Новосибирск»</td>
        <td class="whActions">&nbsp;</td>
      </tr>
      <tr>
        <td class="whType">ФСФ</td>
        <td class="whWidth">1220</td>
        <td class="whLength">2440</td>
        <td class="whThickness">18</td>
        <td class="whSort1">2</td>
        <td class="whSort2">3</td>
        <td class="whSanding">Ш2</td>
        <td class="whPacksQuantity">2</td>
        <td class="whListsInPack">75</td>
        <td class="whTotalLists">150</td>
        <td class="whVolume">6,79</td>
        <td class="whClient">ОАО «МордовАгроМаш»</td>
        <td class="whActions">&nbsp;</td>
      </tr>
    </tbody>
</table>-->

<script id="packsTableTmpl" type="text/x-jquery-tmpl">
<table class="whProduced">
	<thead>
      <tr>
        <td class="whType">Марка</td>
        <td class="whWidth">Длина</td>
        <td class="whLength">Ширина</td>
        <td class="whThickness">Толщина</td>
        <td class="whSort1">Сорт 1</td>
        <td class="whSort2">Сорт 2</td>
        <td class="whSanding">Шлифов.</td>
        <td class="whPacksQuantity">Количество пакетов</td>
        <td class="whListsInPack">Листов в пакете</td>
        <td class="whTotalLists">Всего листов</td>
        <td class="whVolume">Объём</td>
        <!--<td class="whClient">Клиент</td>-->
		<td class="whClient">Примечание</td>
        <td class="whActions">&nbsp;</td>
      </tr>
    </thead>
    <tbody>
	{{each packs}}
      <tr{{if !isStandartWidth || !isStandartLength}} class="isNotStandart"{{/if}}>
        <td class="whType" data-value="${type}">${type_text}</td>
        <td class="whLength">${length}{{if !isStandartLength}} !{{/if}}</td>
		<td class="whWidth">${width}{{if !isStandartWidth}} !{{/if}}</td>
		<td class="whThickness">${thickness / 10}</td>
		<td class="whSortSelect whSort1">{{if sort_1 > 0}}${sort_1}{{else}}—{{/if}}</td>
		<td class="whSortSelect whSort2">{{if sort_2 > 0}}${sort_2}{{else}}—{{/if}}</td>
		<td class="whSanding" data-value="${sanding}">${sanding_text}</td>
		<td class="whQuantity">${packsCount}</td>
		<td class="whQuantity">${quantity}</td>
        <td class="whTotalLists">${packsCount * quantity}</td>
        <td class="whVolume">${volume}</td>
        <!--<td class="whClient">{{if client != null}}${client}{{else}}<span class="vacant">Своб.</span>{{/if}}</td>-->
		 <td class="whComment">${comment}</td>
        <td class="whActions">&nbsp;</td>
      </tr>
	{{/each}}
	</tbody>
</table>
</script>

<div id="packs"></div>