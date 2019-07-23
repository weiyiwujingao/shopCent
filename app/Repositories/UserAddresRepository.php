<?php

namespace App\Repositories;

use App\Models\UsersAddress;
use App\Models\Region;

class UserAddresRepository
{
    /**
     * 创建用户地址
     * @param array $params
     * @return mixed
     */
    public function create(array $params)
    {
        return UsersAddress::create($params);
    }
	/**
	 * 修改收货地址
	 * @author: colin
	 * @date: 2019/1/9 16:23
	 * @param $params
	 * @param $where
	 * @return mixed
	 */
	public function update($params,$where)
	{
		return UsersAddress::where($where)->update($params);
	}
	/**
	 * 删除选中收货地址
	 * @author: colin
	 * @date: 2019/1/9 16:37
	 * @param $where
	 * @return mixed
	 */
	public function delete($where)
	{
		return UsersAddress::where($where)->delete();
	}
    /**
     * 根据id获取用户地址资料
     * @param $id
     * @return mixed
     */
    public function ById($id)
    {
        return UsersAddress::find($id);
    }
    /**
     * 根据手机号码获取用户地址资料
     * @param $orderSn
     * @return mixed
     */
    public function ByMobileId($mobile)
    {
        try {
            $orderInfo = UsersAddress::where('user_mobile', $mobile)->orderBy('order_id', 'desc')->firstOrFail();
        } catch (\Exception $e) {
            return false;
        }
        return $orderInfo;

    }
    /**
     * 获取用户地址列表
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getList($userId,$pageSize=20)
    {
       try{
		   $pageSize = $pageSize ? $pageSize : 20;
		   $dataList = UsersAddress::selectRaw("address_id,consignee as user_name,country,if(province>0,province,'') as province,if(city>0,city,'') as city,if(district>0,district,'') as district,address as detail,mobile,sex,is_default")
			   ->where('user_id',$userId)
			   ->orderBy('is_default','desc')
			   ->orderBy('address_id','asc')
			   ->take($pageSize)
			   ->get();//is_default desc,address_id asc
		   $region = [];
		   foreach ($dataList as $item) {
			   $region[] = $item->country;
			   $region[] = $item->province;
			   $region[] = $item->city;
			   $region[] = $item->district;
		   }
		   $region = array_unique(array_filter($region));
		   if(empty($region)){
		   		return [];
		   }
		   $reginList = Region::select('region_id','region_name')->whereIn('region_id',$region)->get()->toArray();
		   $reginList = array_column($reginList,'region_name','region_id');
		   $list = [];
		   foreach ($dataList as $key=>$value){
			   $list[$key] = [
			   		'address_id' => $value->address_id,
			   		'country_id' => $value->country,
			   		'province_id' => $value->province,
			   		'city_id' => $value->city,
			   		'district_id' => $value->district,
			   		'city' => $reginList[$value->city],
				    'district' => $reginList[$value->district],
				    'province' => $reginList[$value->province],
			   		'detail' => $value->detail,
			   		'is_default' => $value->is_default,
			   		'mobile' => $value->mobile,
			   		'sex' => $value->sex,
			   		'user_name' => $value->user_name,
			   ];
		   }
	   }catch(\Exception $e){
       		return false;
	   }

        return $list;
    }
	/**
	 * 获取用户地址列表
	 * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
	 */
	public function getUserAdd($userId,$addressId)
	{
		try{
			$data = UsersAddress::selectRaw("address_id,consignee as user_name,country,if(province>0,province,'') as province,if(city>0,city,'') as city,if(district>0,district,'') as district,address as detail,mobile,sex,is_default")
				->where('user_id',$userId)
				->where('address_id',$addressId)
				->orderBy('is_default','desc')
				->orderBy('address_id','asc')
				->firstOrFail();
			$region = [];
			$region[] = $data->country;
			$region[] = $data->province;
			$region[] = $data->city;
			$region[] = $data->district;

			$region = array_unique(array_filter($region));
			if(empty($region)){
				return [];
			}
			$reginList = Region::select('region_id','region_name')->whereIn('region_id',$region)->get()->toArray();
			$reginList = array_column($reginList,'region_name','region_id');
			$resulrt = [
				'address_id' => $data->address_id,
				'country_id' => $data->country,
				'province_id' => $data->province,
				'city_id' => $data->city,
				'district_id' => $data->district,
				'city' => $reginList[$data->city],
				'district' => $reginList[$data->district],
				'province' => $reginList[$data->province],
				'detail' => $data->detail,
				'is_default' => $data->is_default,
				'mobile' => $data->mobile,
				'sex' => $data->sex,
				'user_name' => $data->user_name,
			];

		}catch(\Exception $e){
			return false;
		}

		return $resulrt;
	}

}