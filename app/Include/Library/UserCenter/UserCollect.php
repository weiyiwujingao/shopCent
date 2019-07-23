<?php

namespace Library\UserCenter;

use App;
use Illuminate\Http\Request;
use \Exception;

class UserCollect extends \Library\CBase
{
	protected $request;
	protected $userCMd;

	public function __construct(Request $request)
	{
		$this->request = $request;
		$this->userCMd = new App\Repositories\UserCollectRepository();
		parent::__construct(__CLASS__);
	}

	/**
	 * 获取收藏店铺
	 * @author: colin
	 * @date: 2019/1/9 13:58
	 * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Library\type
	 */
	public function getlist()
	{
		try {
			$param = $this->request->all();
			$param['page'] = !empty($param['page']) ? intval($param['page']) : 1;
			$param['pageSize'] = !empty($param['pageSize']) ? intval($param['pageSize']) : 20;
			$param['uid'] = $this->request->input('uid');
			$list = $this->userCMd->getList($param);
			if ($list === false) {
				throw new Exception('获取收藏店铺失败！');
			}
		} catch (\Exception $e) {
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => "UserCollect getlist:" . json_encode($param) . ",reason:" . $e->getMessage(),
				'userMsg' => $e->getMessage(),
				'line' => __LINE__,
			]);
		}
		return $list;
	}
	/***
	 * 删除选中的收藏店铺
	 * @author: colin
	 * @date: 2019/1/9 16:42
	 * @return bool|\Library\type
	 */
	public function delete()
	{
		try {
			$param = $this->request->all();
			$param['user_id'] = $this->request->input('uid');
			$delete = $this->userCMd->delete(['user_id'=>$param['user_id'],'gs_id'=>$param['store_id']]);
			if(empty($delete)){
				throw new \Exception('删除失败！');
			}
		} catch (\Exception $e) {
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => "UserCollect delete:" . json_encode($param) . ",reason:" . $e->getMessage(),
				'userMsg' => $e->getMessage(),
				'line' => __LINE__,
			]);
		}
		return true;
	}
	/***
	 * 收藏店铺
	 * @author: colin
	 * @date: 2019/1/9 16:42
	 * @return bool|\Library\type
	 */
	public function add()
	{
		try {
			$param = $this->request->all();
			$param['user_id'] = $this->request->input('uid');
			$dataInfo = [
				'user_id' => $param['user_id'],
				'gs_id' => $param['store_id'],
			];
			//判断是否收藏
			$isCollect = $this->userCMd->isCollect($dataInfo);
			if($isCollect){
				$msg = 1;
				$delete = $this->userCMd->delete(['user_id'=>$param['user_id'],'gs_id'=>$param['store_id']]);
				if(empty($delete))
					throw new \Exception('删除失败！');
			}else{
				$msg = 2;
				$create = $this->userCMd->create($dataInfo);
				if(empty($create))
					throw new \Exception('已添加收藏，不用重复添加！');
			}

		} catch (\Exception $e) {
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => "UserCollect add:" . json_encode($param) . ",reason:" . $e->getMessage(),
				'userMsg' => '已添加收藏，不用重复添加！',
				'line' => __LINE__,
			]);
		}
		return $msg;
	}

	/***
	 * 判断是否收藏
	 * @author: colin
	 * @date: 2019/5/24 20:16
	 * @return bool|\Library\type
	 */
	public function isCollect()
	{
		try {
			$param = $this->request->all();
			$param['user_id'] = $this->request->input('uid');
			if(!isset($param['user_id']) || empty($param['user_id']))
				return false;
			$where = [
				'user_id' => $param['user_id'],
				'gs_id' => $param['store_id'],
			];
			$isCollect = $this->userCMd->isCollect($where);
		} catch (\Exception $e) {
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => "UserCollect add:" . json_encode($param) . ",reason:" . $e->getMessage(),
				'userMsg' => '没添加 收藏！',
				'line' => __LINE__,
			]);
		}
		return $isCollect;
	}

}