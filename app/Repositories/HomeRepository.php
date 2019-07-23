<?php

namespace App\Repositories;

use App\Models\Goods;
use Complex\Exception;
use DB;
use App\Models\GoodsRegion;
use App\Models\Brand;
use App\Models\AD;
use App\Models\StoresUser;
use App\Models\GoodsAttr;
use App\Models\HomeNav;
use Illuminate\Support\Facades\Cache;
use Enum\EnumKeys;
use Helper\CFunctionHelper as help;

class HomeRepository
{
	/**
	 * 根据gr_id获取指定的地区表字段
	 * @param $name
	 * @return mixed
	 */
	public function getById($id, $column)
	{
		try {
			$key = md5(EnumKeys::CACHE_GR_NAME_GR_ID . '_' . $id . '_' . $column);
			$result = Cache::get($key);
			if ($result) {
				return $result;
			}
			$result = GoodsRegion::where('gr_id', $id)->value($column);
			if ($result) {
				Cache::put($key, $result, 60 * 24 * 30);
			}
		} catch (\Exception $e) {
			return false;
		}
		return $result;
	}

	/**
	 * 获取首页广告列表
	 * @author: colin
	 * @date: 2019/1/15 16:35
	 * @param $city
	 * @return array|bool
	 */
	public function getSlider($city)
	{
		try {
			$time = time();
			$result = AD::selectRaw("ad_id,ad_name,ad_link,ad_code,city,sort,wechat_link")
				->where(['position_id' => 11, 'enabled' => 1])
				->where('start_time', '<=', $time)
				->where('end_time', '>=', $time)
				->where('city', 'like', '%' . $city . '%');
			$result = $result->orderBy('sort', 'asc')->get()->toArray();
			foreach ($result as $item) {
				$src = strtolower($item['ad_code']);
				$pos = strpos($src, 'http');
				$src = ($pos === false) ? config('app.static_domain') . 'data/afficheimg/' . $src : $src;
				$url = empty(trim($item['ad_link'])) ? 'javascript:;' : trim($item['ad_link']);
				$pages[] = ['id' => $item['ad_id'], 'src' => $src, 'url' => $url, 'text' => $item['ad_name'], 'sort' => $item['sort'], 'city' => $item['city'],'wechat_link'=>$item['wechat_link']];
			}
		} catch (\Exception $e) {
			return false;
		}
		return $pages;
	}

	/**
	 * 新品
	 * @author: colin
	 * @date: 2019/1/15 17:50
	 * @param $city
	 * @param $positionId
	 * @param $num
	 * @return bool
	 */
	public function getNewGoods($city, $positionId, $num)
	{
		try {
			$keycache = md5(EnumKeys::CACHE_HOME_NEW_GOODS . '_' . $city . '_' . $positionId . '_' . $num);
			$sdata = Cache::get($keycache);
			if ($sdata) {
				return $sdata;
			}
			$time = time();
			$result = AD::leftJoin('ecs_ad_position as p', 'ecs_ad.position_id', '=', 'p.position_id')
				->selectRaw("ecs_ad.ad_id, ecs_ad.position_id, ecs_ad.media_type, ecs_ad.ad_link, ecs_ad.ad_code, ecs_ad.ad_name,ecs_ad.details, p.ad_width, ecs_ad.city, ecs_ad.wechat_link, p.ad_height, p.position_style")
				->where(['ecs_ad.position_id' => $positionId, 'ecs_ad.enabled' => 1])
				->where('ecs_ad.start_time', '<=', $time)
				->where('ecs_ad.end_time', '>=', $time)
				->where('ecs_ad.city', 'like', '%' . $city . '%');
			$result = $result->orderBy('sort', 'asc')->take($num)->get()->toArray();
			$sdata = [];
			foreach ($result as $key => $val) {
				$sdata[$key]["id"] = $val['ad_id'];
				$sdata[$key]["ad_link"] = $val['ad_link'];
				$sdata[$key]["details"] = $val['details'];
				$sdata[$key]["img"] = config('app.static_domain') . 'data/afficheimg/' . $val["ad_code"];
				$sdata[$key]["name"] = $val["ad_name"];
				$sdata[$key]["wechat_link"] = $val["wechat_link"];
			}
			if ($sdata) {
				Cache::put($keycache, $sdata, 1);
			}

		} catch (\Exception $e) {
			echo $e->getMessage();
			die;
			return false;
		}
		return $sdata;
	}

	/***
	 * 首页分类列表
	 * @author: colin
	 * @date: 2019/1/16 9:30
	 * @return bool|mixed
	 */
	public function getNav()
	{
		try {
			$keycache = md5(EnumKeys::CACHE_HOME_NAV_LIST . '_home');
			$result = Cache::get($keycache);
			$result = '';
			if ($result) {
				return $result;
			}
			$staticHost = config('app.static_domain');
			$result = HomeNav::selectRaw("name,concat('{$staticHost}',img) as img, url, wechat_url")
				->where(['enbale' => 1]);
			$result = $result->orderBy('sort', 'desc')->get()->toArray();
			if ($result) {
				Cache::put($keycache, $result, 10);
			}
		} catch (\Exception $e) {
			return false;
		}
		return $result;
	}

	/**
	 * 首页获取店铺列表
	 * @author: colin
	 * @date: 2019/1/16 16:30
	 * @return bool
	 */
	public function getSellers($param, $where)
	{
		try {
			$result = StoresUser::from('ecs_goods_stores as gs')->join('ecs_brand as d', 'gs.gs_brand_id', '=', 'd.brand_id')
				->selectRaw("DISTINCT gs.gs_id, gs.gs_name, gs.gs_goods_id, gs.rec_goods_ids, gs.gs_address,gs.gs_lng,gs.gs_lat,d.brand_name,d.brand_id,d.brand_logo");
			$result = $result->where('gs.gs_stats', 1);
			$result = $result->where('gs.gs_goods_id', '!=', '');
			$resAll = $result;

			if (!empty($param['lng']) && !empty($param['lat'])) {
				$scope = help::calcScope($param['lat'], $param['lng'], 30 * 1000);//取最大/小经纬度
				$result = $result->whereBetween('gs.gs_lng', [$scope['min_lng'], $scope['max_lng']])->whereBetween('gs.gs_lat', [$scope['min_lat'], $scope['max_lat']]);
			}
			$result = $result->where($where);

			$result = $result->get()->toArray();
			if (empty($result)) {
				$result = $resAll->take(50)->get()->toArray();
			}
			$goodsIdArr = [];
			$sdata = [];
			foreach ($result as $key => $item) {
				$sdata[$key]["lng"] = !empty($param['lng']) ? $param['lng'] : "113.75"; //当前经度;
				$sdata[$key]["lat"] = !empty($param['lat']) ? $param['lat'] : "23.05"; //当前纬度;
				$sdata[$key]['gs_lng'] = $item['gs_lng'];
				$sdata[$key]['gs_lat'] = $item['gs_lat'];
				$sdata[$key]["distance"] = help::distanceBetween($param['lat'], $param['lng'], $item['gs_lat'], $item['gs_lng']);
				$sdata[$key]["brand_logo"] = config('app.static_domain') . "data/brandlogo/" . $item["brand_logo"];
				$sdata[$key]['name'] = $item['gs_name'];
				$sdata[$key]['sellerid'] = $item['gs_id'];
				$sdata[$key]['address'] = $item['gs_address'];
				$sdata[$key]['brand_name'] = $item['brand_name'];
				$sdata[$key]['brand_id'] = $item['brand_id'];
				$sdata[$key]['goods'] = $this->goods($item['gs_goods_id'], $item['rec_goods_ids'], $goodsIdArr, $item['gs_id']);
				foreach ($sdata[$key]['goods'] as $tp) {
					$fid = $tp['goods_id'];
					$goodsIdArr[$fid] = 1;//保存已查出的商品
				}
				$sort[] = $sdata[$key]["distance"];

			}

			if($sdata)
				array_multisort($sort, SORT_ASC, $sdata);
			$sdata = array_slice($sdata, 0, 30);
		} catch (\Exception $e) {
			return false;
		}
		return $sdata;
	}

	/**
	 * 获取首页展示的商品
	 * @author: colin
	 * @date: 2019/1/17 10:57
	 * @param $goodsId
	 * @return bool
	 */
	public function goods($goodsId, $recGoodsIds = '', $goodsIdArr, $gsID)
	{
		try {
			$goodsId = unserialize($goodsId);
			if (empty($goodsId)) {
				throw new \Exception('空goodid');
			}
			$tmpIdArr = [];
			foreach ($goodsId as $tpid) {
				if (!isset($goodsIdArr[$tpid])) {
					$tmpIdArr[] = $tpid;
				}
			}
			$goodsId = $tmpIdArr;

			if (!empty($recGoodsIds)) {
				$recGoodsIdArr = explode(',', $recGoodsIds);
				$goodsArrTmp = array_intersect($recGoodsIdArr, $goodsId);
				if (!empty($goodsArrTmp)) {
					$goodsArrTmp = !empty($offGoods) ? getDiffArr($goodsArrTmp, $offGoods) : [];
					if (!empty($goodsArrTmp)) {
						//检查推荐的商品是否正常
						$gcount = Goods::where(['is_on_sale' => 1, 'is_delete' => 0])->whereIn('goods_id', $goodsArrTmp)->count();
						$goodsId = ($gcount) ? $goodsArrTmp : $goodsId;
					}
				}
			}
			$result = Goods::where('is_on_sale', 1)
				->where('is_delete', 0)
				->whereIn('goods_id', $goodsId);
			$result = $result->take(2)->orderByRaw("is_hot desc,is_best desc,is_new desc")->get()->toArray();
			$brandIds = [];
			foreach($result as $v){
				if(!in_array($v['brand_id'],$brandIds))
					$brandIds[] = $v['brand_id'];
			}
			$reserveType = Brand::whereIn('brand_id',$brandIds)->pluck('reserve_type','brand_id')->toArray();
			foreach($reserveType as $k=>$v){
				if($v==10)
					$reserveType[$k] = Brand::where('brand_id',$k)->value('reserve_other');
			}
			$goodsData = [];
			foreach ($result as $item) {
				$isSizeLength = GoodsAttr::where('goods_id',$item['goods_id'])->value('goods_attr_id');
				$sData = [
					'goods_id' => $item['goods_id'],
					'type' => $item['cat_id'],
					'foodid' => $item['goods_id'],
					'name' => $item['goods_name'],
					'goods_sn' => $item['goods_sn'],
					'saleqt' => $item['saleqt'],
					'oldprice' => $item['shop_price'],
					'reserve_type' => $reserveType[$item['brand_id']],
					'support_shipping' => $item['pickup_mode'],
					'miniprice' => ($item['is_promote'] == 1 && time() >= $item['promote_start_date'] && time() <= $item['promote_end_date']) ? $item["promote_price"] : $item["shop_price"],
					'icon' => config('app.static_domain') . $item['goods_thumb'],
					'sizesLength' => empty($isSizeLength)? false : true,
				];
				if ($sData['miniprice'] == 0) {
					$attrPrice = GoodsAttr::from('ecs_goods_attr as a')->join('ecs_attribute as b', 'a.attr_id', 'b.attr_id')
						->where('goods_id', $item['goods_id'])->orderByRaw("CAST(attr_price AS SIGNED)")->value('attr_price');
					$sData["miniprice"] = $attrPrice;
					$sData["oldprice"] = $attrPrice;
				}
				array_push($goodsData, $sData);
			}

		} catch (\Exception $e) {
			echo $e->getMessage();die;
			return false;
		}
		return $goodsData;
	}

	/**
	 * 根据地区名称获取地区id
	 * @author: colin
	 * @date: 2019/1/18 11:22
	 * @param string $city
	 * @return bool|mixed
	 */
	public function position($city = '东莞市')
	{
		try {
			$keycache = md5(EnumKeys::CACHE_REGION_CITY_ID . '_home_' . $city);
			$result = Cache::get($keycache);
			if ($result) {
				return $result;
			}
			$result = GoodsRegion::selectRaw("gr_id as id, gr_name as name")
				->where(['enable' => 1, 'gr_name' => $city]);
			$result = $result->firstOrFail()->toArray();
			if ($result) {
				Cache::put($keycache, $result, 24 * 60 * 30);
			}
		} catch (\Exception $e) {
			return [];
		}
		return $result;
	}

	/**
	 * 获取地区列表
	 * @author: colin
	 * @date: 2019/1/18 13:58
	 * @param $cityId
	 * @return bool|mixed
	 */
	public function citys($cityId)
	{
		try {
			$keycache = md5(EnumKeys::CACHE_REGION_CITY_LIST . '_home_' . $cityId);
			$result = Cache::get($keycache);
			if ($result) {
				return $result;
			}
			$where = ['enable' => 1];
			if ($cityId) {
				$where['parent_id'] = $cityId;
			} else {
				$where['type'] = 2;//默认获取城市列表
			}
			$result = GoodsRegion::selectRaw("gr_id as id, gr_name as name")
				->where($where);
			$result = $result->get()->toArray();
			if ($result) {
				Cache::put($keycache, $result, 24 * 60);
			}
		} catch (\Exception $e) {
			return false;
		}
		return $result;
	}
	/**
	 * 获取所有地区列表
	 * @author: colin
	 * @date: 2019/1/18 13:58
	 * @param $cityId
	 * @return bool|mixed
	 */
	public function allCity($cityId)
	{
		try {
			$keycache = md5(EnumKeys::CACHE_REGION_CITY_LIST . '_home_ALL_'.$cityId);
			$result = Cache::get($keycache);
			if ($result) {
				return $result;
			}
			$where = ['enable' => 1, 'type'=>2];
			if (!empty($cityId)) {
				$where['gr_id'] = $cityId;
			}
			$result = GoodsRegion::selectRaw("gr_id as id, gr_name as city")
				->where($where);
			$result = $result->get()->toArray();
			foreach($result as $key=>$item){
				$data = GoodsRegion::selectRaw("gr_id as id, gr_name as name")
					->where('parent_id',$item['id']);
				$data = $data->get()->toArray();
				$result[$key]['towns'] = $data;
			}
			if ($result) {
				Cache::put($keycache, $result, 24 * 60);
			}
		} catch (\Exception $e) {
			return false;
		}
		return $result;
	}
}