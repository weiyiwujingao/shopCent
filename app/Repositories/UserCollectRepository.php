<?php

namespace App\Repositories;

use App\Models\CollectStores;
use App\Models\Region;

class UserCollectRepository
{
    /**
     * 创建收藏店铺
     * @param array $params
     * @return mixed
     */
    public function create(array $params)
    {
		try {
			$create = CollectStores::create($params);
		} catch (\Exception $e) {
			return false;
		}
		return $create;
    }
	/**
	 * 修改收藏店铺
	 * @author: colin
	 * @date: 2019/1/9 16:23
	 * @param $params
	 * @param $where
	 * @return mixed
	 */
	public function update($params,$where)
	{
		return CollectStores::where($where)->update($params);
	}
	/**
	 * 删除选中收藏店铺
	 * @author: colin
	 * @date: 2019/1/9 16:37
	 * @param $where
	 * @return mixed
	 */
	public function delete($where)
	{
		return CollectStores::where($where)->delete();
	}
    /**
     * 根据id获取收藏店铺资料
     * @param $id
     * @return mixed
     */
    public function ById($id)
    {
        return CollectStores::find($id);
    }
    /**
     * 根据手机号码获取收藏店铺资料
     * @param $orderSn
     * @return mixed
     */
    public function ByMobileId($mobile)
    {
        try {
            $orderInfo = CollectStores::where('user_mobile', $mobile)->orderBy('order_id', 'desc')->firstOrFail();
        } catch (\Exception $e) {
            return false;
        }
        return $orderInfo;

    }

	/**
	 * 判断是否收藏
	 * @author: colin
	 * @date: 2019/5/24 20:14
	 * @param $where
	 * @return bool
	 */
	public function isCollect($where)
	{
		try {
			CollectStores::where($where)->firstOrFail();
		} catch (\Exception $e) {
			return false;
		}
		return true;

	}
    /**
     * 获取收藏店铺列表
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getList($param)
    {
       try{
		   $param['page'] = ($param['page'] - 1) * $param['pageSize'];
		   $dataList = CollectStores::join('ecs_goods_stores as gs','gs.gs_id','=','ecs_collect_stores.gs_id')->leftJoin('ecs_brand as b','b.brand_id','=','gs.gs_brand_id')
			   ->selectRaw("ecs_collect_stores.gs_id,gs.gs_name,b.brand_id,b.brand_name,b.brand_logo")
			   ->where('ecs_collect_stores.user_id',$param['uid'])
			   ->orderBy('ecs_collect_stores.id','desc');
		   $dataList = $dataList->skip($param['page'])->take($param['pageSize'])->orderBy('ecs_collect_stores.id', 'DESC')->get()->toArray();
		   foreach ($dataList as $key=>$value){
			   $dataList[$key]['brand_logo'] = config('app.static_domain') . 'data/brandlogo/' .$dataList[$key]['brand_logo'];
		   }
	   }catch(\Exception $e){
       		return false;
	   }
        return $dataList;
    }

}