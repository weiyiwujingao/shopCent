<?php
/**
 * 数据运维类
 */
namespace App\Http\Controllers\Api\DataOffice;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DataExploadController extends Controller
{
	protected $request;
	protected $Obj;
	protected $userInfo;

	public function __construct(Request $request)
	{
		$this->request = $request;
		$this->Obj = new \Library\DataOffice($this->request);
	}
	/***
	 * 导出东莞门店商品信息
	 * @author: colin
	 * @date: 2018/11/1 10:58
	 * @param $param array 查询信息
	 */
	public function excelData()
	{
		$result = $this->Obj->excelGoods();
		if ($result === false) {
			return;
		}
		ini_set('memory_limit', '500M');
		\Helper\OfiiceHelper::exportExcelOne($result['titles'],$result['data'],'商品信息');
	}

}
