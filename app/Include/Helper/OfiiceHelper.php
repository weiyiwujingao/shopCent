<?php

namespace Helper;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class OfiiceHelper
{
	public function __construct()
	{

	}

	/***
	 * 导出表格数据
	 * @author: colin
	 * @date: 2018/12/12 17:42
	 * @param $titles
	 * @param $dataArr
	 * @param string $filename
	 * @param bool $url
	 * @return string
	 * @throws \PhpOffice\PhpSpreadsheet\Exception
	 * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
	 */
	public static function exportExcelOne($titles,$dataArr,$filename='export',$url=false)
	{
		$spreadsheet = new Spreadsheet();
		$sheet = $spreadsheet->getActiveSheet();
		$spreadsheet->setActiveSheetIndex(0);
		$spreadsheet->getActiveSheet()->setTitle('infos');

		$azs = range('A','Z');

		// 写入表格数据
		$i = 0;
		foreach ($titles as $key => $title) {
			$sheet->setCellValue(($azs[$i]).'1', $title);
			foreach ($dataArr[$key] as $n => $val) {
				$sheet->setCellValue(($azs[$i]).($n+2), $val);
			}
			++$i;
		}

		$filename = $filename."_".date('Y-m-d').".xlsx";

		header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");//告诉浏览器输出07Excel文件
		header("Content-Disposition: attachment;filename=".$filename);//告诉浏览器输出浏览器名称
		$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');

		if($url){
			//创建导出目录
			$disPath = PUBLIC_PATH."upload/export/";

			if(!is_dir($disPath)){
				\Helper\CFunctionHelper::createFolder($disPath);
			}
			$filePath = $disPath.$filename;

			$writer->save($filePath);
		}else{
			$writer->save('php://output');
		}

		//释放内存
		$spreadsheet->disconnectWorksheets();
		unset($spreadsheet);

		return $filename;
	}
	/***
	 * 导出表格-可以多sheet附属表
	 * @author: colin
	 * @date: 2018/12/12 17:39
	 * @param $titles
	 * @param $dataArr
	 * @param string $filename
	 * @param array $tabletwo
	 * @return string
	 */
	public static function exportExcel($titles,$dataArr,$filename='export',$tabletwo = [],$pran = 'order_sn',$url=false)
	{
		try{
			$spreadsheet = new Spreadsheet();
			$sheet = $spreadsheet->getActiveSheet();
			$spreadsheet->setActiveSheetIndex(0);
			$spreadsheet->getActiveSheet()->setTitle($filename);

			$azs = range('A','Z');
			$styleArray = [
				'alignment' => [
					'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
					'readOrder' => \PhpOffice\PhpSpreadsheet\Style\Alignment::READORDER_LTR,
				],
			];
			// 写入表格数据
			$i = 0;
			foreach ($titles as $key => $title) {
				$sheet->setCellValue(($azs[$i]).'1', $title);
				switch ($azs[$i]){
					case 'G':
						$sheet->getColumnDimension(($azs[$i]))->setWidth(25);
						break;
					case 'M':
						$sheet->getColumnDimension(($azs[$i]))->setWidth(25);
						break;
					case 'A':
						$sheet->getColumnDimension(($azs[$i]))->setWidth(16);
						break;
					case 'C':
						$sheet->getColumnDimension(($azs[$i]))->setWidth(16);
						break;
					case 'F':
						$sheet->getColumnDimension(($azs[$i]))->setWidth(15);
						break;
					default:
						$sheet->getColumnDimension(($azs[$i]))->setWidth(8);
						break;

				}
				$sheet->getStyle($azs[$i].'1')->applyFromArray($styleArray);
				foreach ($dataArr[$key] as $n => $val) {
					$sheet->setCellValue(($azs[$i]).($n+2), $val);
				}
				++$i;
			}
			$spreadsheet->getActiveSheet()->getStyle('A1:N1')->getFont()->setBold(true)->setSize(10);
			$sheet->freezePane('A2');

			if(!empty($tabletwo)){
				foreach($tabletwo as $k => $item){
				    $worksheets = 'worksheet'.$k;
					$$worksheets = $spreadsheet->createSheet();
					$$worksheets->setTitle($item['name']);
					// 写入表格数据
					$i = 0;
					$valLast = '';
					foreach ($item['titlestwo'] as $key => $title) {
						$$worksheets->setCellValue(($azs[$i]).'1', $title);
						foreach ($item['datatwo'][$key] as $n => $val) {
							$$worksheets->setCellValue(($azs[$i]).($n+2), $val);
							//附属表指定字段跨行合并
							if($key == $pran && $val == $valLast && $n>=1){
								$prange = ($azs[$i]).($n+1).':'.($azs[$i]).($n+2);
								$$worksheets->mergeCells($prange);
							}else if($key == 'order_sn'){
								$valLast = $val;
							}
						}
						++$i;
					}
				}
			}
			$filename = $filename."_".date('Y-m-d').".xlsx";

			header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");//告诉浏览器输出07Excel文件
			header("Content-Disposition: attachment;filename=".$filename);//告诉浏览器输出浏览器名称
			header('Cache-Control: max-age=0');


			$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
			if($url){
				//创建导出目录
				$disPath = PUBLIC_PATH."temp/";

				if(!is_dir($disPath)){
					\Helper\CFunctionHelper::createFolder($disPath);
				}
				$filePath = $disPath.$filename;

				$writer->save($filePath);
			}else{
				$writer->save('php://output');
			}
			//释放内存
			$spreadsheet->disconnectWorksheets();
			unset($spreadsheet);
		}catch(\Exception $e){
			return ['status'=>\Enum\EnumMain::HTTP_CODE_FAIL,'error'=>$e->getMessage()];
		}
		return ['status'=>\Enum\EnumMain::HTTP_CODE_OK,'fileName'=>$filename];
	}
}
