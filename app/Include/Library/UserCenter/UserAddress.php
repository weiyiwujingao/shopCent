<?php

namespace Library\UserCenter;

use DB;
use App;
use Enum\EnumKeys;
use Validator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use \Exception;
use Helper\CFunctionHelper as help;

class UserAddress extends \Library\CBase
{
	protected $request;
	protected $userMd;

	public function __construct(Request $request)
	{
		$this->request = $request;
		$this->userAMd = new App\Repositories\UserAddresRepository();
		parent::__construct(__CLASS__);
	}

	/**
	 * 获取收货地址
	 * @author: colin
	 * @date: 2019/1/9 13:58
	 * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Library\type
	 */
	public function getlist()
	{
		try {
			$uid = $this->request->input('uid');
			$pageSize = (int)$this->request->input('pageSize');
			$list = $this->userAMd->getList($uid,$pageSize);
		} catch (\Exception $e) {
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => "UserAddress getlist:" . json_encode($uid) . ",reason:" . $e->getMessage(),
				'userMsg' => $e->getMessage(),
				'line' => __LINE__,
			]);
		}
		return $list;
	}
	/**
	 * 获取用户收货地址详情
	 * @author: colin
	 * @date: 2019/5/22 11:25
	 * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Library\type
	 */
	public function userAddress()
	{
		try {
			$uid = $this->request->input('uid');
			$addressId = (int)$this->request->input('addressId');
			if(empty($addressId))
				throw new \Exception('参数有误！');
			$result = $this->userAMd->getUserAdd($uid,$addressId);
			if($result === false)
				throw new \Exception('没有地址！');
		} catch (\Exception $e) {
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => "UserAddress detail:" . json_encode($uid) . ",reason:" . $e->getMessage(),
				'userMsg' => $e->getMessage(),
				'line' => __LINE__,
			]);
		}
		return $result;
	}
	/**
	 * 添加收货地址
	 * @author: colin
	 * @date: 2019/1/9 16:14
	 * @return bool|\Library\type
	 */
	public function create()
	{
		try {
			$param = $this->request->all();
			$param['user_id'] = $this->request->input('uid');

			if(isset($param['is_default']) && $param['is_default'] == 1){
				$this->userAMd->update(['is_default'=>0],['user_id'=>$param['user_id']]);
			}
			$param = help::setParamData(['user_id','consignee','sex','province','city','district','address','mobile','is_default'], $param);
			$this->userAMd->create($param);
		} catch (\Exception $e) {
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => "UserAddress create:" . json_encode($param) . ",reason:" . $e->getMessage(),
				'userMsg' => $e->getMessage(),
				'line' => __LINE__,
			]);
		}
		return true;
	}
	/**
	 * 修改收货地址
	 * @author: colin
	 * @date: 2019/1/9 16:14
	 * @return bool|\Library\type
	 */
	public function update()
	{
		try {
			$param = $this->request->all();
			$param['user_id'] = $this->request->input('uid');
			$param = help::setParamData(['user_id','address_id','consignee','sex','province','city','district','address','mobile','is_default'], $param);
			if(isset($param['is_default']) && $param['is_default'] == 1){
				$this->userAMd->update(['is_default'=>0],['user_id'=>$param['user_id']]);
			}
			$this->userAMd->update($param,['user_id'=>$param['user_id'],'address_id'=>$param['address_id']]);
		} catch (\Exception $e) {
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => "UserAddress create:" . json_encode($param) . ",reason:" . $e->getMessage(),
				'userMsg' => $e->getMessage(),
				'line' => __LINE__,
			]);
		}
		return true;
	}
	/***
	 * 删除选中的收货地址
	 * @author: colin
	 * @date: 2019/1/9 16:42
	 * @return bool|\Library\type
	 */
	public function delete()
	{
		try {
			$param = $this->request->all();
			$param['user_id'] = $this->request->input('uid');
			$delete = $this->userAMd->delete(['user_id'=>$param['user_id'],'address_id'=>$param['address_id']]);
			if(empty($delete)){
				throw new \Exception('删除失败！');
			}
		} catch (\Exception $e) {
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => "UserAddress create:" . json_encode($param) . ",reason:" . $e->getMessage(),
				'userMsg' => $e->getMessage(),
				'line' => __LINE__,
			]);
		}
		return true;
	}

	/**
	 * 返回地区列表
	 * @author: colin
	 * @date: 2019/1/9 17:23
	 * @return \Library\type
	 */
	public  function getCity()
	{
		try{
			$parentId = $this->request->input('parentId',1);
			$where = ['parent_id'=>$parentId];
			$list = App\Models\Region::selectRaw("region_id as id,region_name as name")->where($where)->get()->toArray();
		}catch(\Exception $e){
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => "UserAddress getCity:" . json_encode($parentId) . ",reason:" . $e->getMessage(),
				'userMsg' => $e->getMessage(),
				'line' => __LINE__,
			]);
		}
		return $list;
	}

}