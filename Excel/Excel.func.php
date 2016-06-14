<?php 

/**
 * 导出为Excel表格
 * @param  [array]   $header   [表头]
 * @param  [array]   $content  [表内容]
 * @param  [string]  $name     [表名]
 * @param  [boolean] $sign     [索引 or 关联]
 */
function outExcel($header, $content, $name, $sign=true){
	$rootDir = $_SERVER['DOCUMENT_ROOT'];
	$excelDir = dirname($rootDir).'/plugin/phpExcel/';
	require($excelDir.'Classes/PHPExcel.php');
	$objExcel = new PHPExcel();
	
	$objsheet = $objExcel->getActiveSheet();

	//设置默认文字水平垂直居中
	$objsheet->getDefaultStyle()->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER)
			 ->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

	$rows = range('A','Z');

	/**
	 * 设置表头
	 */
	//判断 $header 是否为一维数组
	if (count($header) == count($header,1)) {
		//一维数组
		$i = 0;
		foreach ($header as $k => $v) {
			$objsheet->setCellValue($rows[$i].'1', $v);
			$i++;
		}
		$col = 2;
	} else {
		//多维数组
		foreach ($header as $k => $v) {
			$i = 0; $j = $k + 1;
			foreach ($v as $ko => $vo) {
				if (is_array($vo)) {
					$objsheet->setCellValue($rows[$i].$j, $vo[0]);
					$objsheet->mergeCells($rows[$i].$j.':'.$rows[$i+$vo[1]-1].$j);
					$i += $vo[1];
				} else {
					$objsheet->setCellValue($rows[$i].$j, $vo);
					$i++;
				}
				
			}
		}
		$col = $j + 1;
		$header = array_pop($header);
	}

	/**
	 * 设置表内容
	 */

	foreach ($content as $v) {
		$i = 0;
		if (!$sign) $header = $v;  //变为索引数组
		foreach ($header as $ko => $vo) {
			if (!$sign) {
				$item = $vo;
			} else {
				$item = $v[$ko];
			}

			if (is_array($item)) {
				$objsheet->setCellValue($rows[$i].$col, $item[0]);
				$objsheet->getComment($rows[$i].$col)->getText()->createTextRun($item[1]);
			} else {
				$objsheet->setCellValue($rows[$i].$col, $item);
			}
			$i++;
		}
		$col++;
	}

	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
	header('Content-Disposition: attachment;filename="'.$name.'.xlsx"');
	header('Cache-Control: max-age=0');
	
	$objWrite = PHPExcel_IOFactory::createWriter($objExcel, 'Excel2007');
	$objWrite->save('php://output');
}
//$header = array('张三','李四');
/*$b = array(
		array(array('ab',2),'c'),
		array('a','b','c'),
		array('a'=>'姓名','b'=>'年龄','c'=>'籍贯')
	);*/
$b = array('a'=>'姓名','b'=>'年龄','c'=>'籍贯');
$c = array(
	array('a'=>array('张三','sadas'),'b'=>'24','c'=>'四川'),
	array('b'=>'32','a'=>'李四','c'=>'湖南'),
	array('c'=>'隔壁','a'=>'王五','b'=>'41'),
	);
outExcel($b,$c,'04',false);