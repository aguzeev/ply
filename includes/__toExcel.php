<?php

class toExcel {
	private $_content;
	
	public function __construct() {
	}
	public function addContent($pContent) {
        $this->_content = $pContent;
    }
	public function getXLS() {
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Cache-Control: private", false);
		header("Content-Type: application/x-msexcel");
		header("Content-Disposition: attachment; filename=\"list.xls\";");
		header("Content-Transfer-Encoding:­ binary");
		
		echo "<html><table border='1'>";
		foreach ( $this->_content as $row ) {
			echo "<tr>";
			switch ( date("G:i", strtotime($row[0])) ) {
				case "16:00":
					echo "<td>" . date("d.m.Y", strtotime($row[0])) . "</td>";
					echo "<td>1 смена</td>";
				break;
				case "0:00":
					$date = date("d.m.Y", date("U", strtotime($row[0]) - 28800));
					echo "<td>" . $date . "</td>"; // смена относится к предыдущим суткам
					echo "<td>2 смена</td>";
				break;
				case "8:00":
					$date = date("d.m.Y", date("U", strtotime($row[0]) - 57600));
					echo "<td>" . $date . "</td>"; // смена относится к предыдущим суткам
					echo "<td>3 смена</td>";
				break;
				default:
					echo "<td>" . date("d.m.Y", strtotime($row[0])) . "</td>";
					echo "<td>" . date("G:i", strtotime($row[0])) . "</td>";
				break;
			}
			echo "<td>" . str_replace(".", ",", $row[1]) . "</td>";
			echo "</tr>";
		}
		echo "</table></html>";
	}
}

$_IS_INCLUDED = true;

require_once('init.php');
require_once('monitoring/plotVariants.php');
include("monitoring/getData2.php");


$objPHPExcel = new toExcel();
$objPHPExcel->addContent($total_result['payload']);
$objPHPExcel->getXLS();

?>
