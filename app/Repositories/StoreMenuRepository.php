<?php

namespace App\Repositories;

use App\Http\Requests\Api\ProductCenter\CatSellersRequest;
use App\Models\Goods;
use \Exception;
use DB;
use App\Models\GoodsRegion;
use App\Models\GoodsAttr;
use App\Models\StoresUser;
use App\Models\Brand;
use App\Models\BrandExtend;
use Illuminate\Support\Facades\Cache;
use Enum\EnumKeys;
use Helper\CFunctionHelper as help;

class StoreMenuRepository
{
	/**
	 * 获取品牌门店列表
	 * @author: colin
	 * @date: 2019/1/18 15:32
	 * @param $param
	 * @param $where
	 * @return array|bool
	 */
	public function brands($param, $where = '')
	{
		try {
			$barnd = Brand::where('region_id', '<>', '');
			if ($where) {
				$barnd = $barnd->where($where);
			}
			$barnd = $barnd->orderByRaw("sort_order asc,brand_id ASC")->get()->toArray();
			$brandId = StoresUser::where(['gs_region_sq'=>$param['cityId'],'gs_stats'=>1])->value("gs_brand_id");
			$data = [];
			$k = 0;
			foreach ($barnd as $key => $item) {
				if (empty($item['region_id'])) {
					continue;
				}
				$regionId = unserialize($item['region_id']);
				if (!in_array($param['cityId'], $regionId) && $brandId != $item['brand_id']) {
					continue;
				}
				$data[$key] = [
					'city_id' => $param['cityId'],
					'brand_id' => $item['brand_id'],
					'brand_name' => $item['brand_name'],
					'sort' => $item['sort_order'],
					'brand_avatar' => $item['site_url'],
					'brand_logo' => config('app.static_domain') . "data/brandlogo/" . $item['brand_logo'],
				];
				$brandIdArr[] = $item['brand_id'];
				//城市地区排序
				$brandSort = !empty($item['region_sort']) ? unserialize($item['region_sort']) : '';
				$sort = isset($brandSort[$param['cityId']]) ? intval($brandSort[$param['cityId']]) : 0;
				$regionSort[$key] = $sort;
				$defaultSort[$key] = $item['sort_order'];
				$k = $key + 1;
			}
			if (!empty($param['support_shipping']) && $param['support_shipping'] == 1) {
				$data[$k] = [
					'brand_name' => '配送推荐',
					'brand_id' => implode(',', $brandIdArr),
				];
				$regionSort[$k] = 10000;
				$defaultSort[$k] = 0;
			}
			array_multisort($regionSort, SORT_DESC, $defaultSort, SORT_ASC, $data);
		} catch (\Exception $e) {
			return false;
		}
		return $data;
	}

	/**
	 * 指定品牌店铺
	 * @author: colin
	 * @date: 2019/1/21 11:49
	 * @param $param
	 * @param $where
	 * @return array|bool
	 */
	public function getSellers($param, $where)
	{
		try {
			$result = StoresUser::from('ecs_goods_stores as gs')->join('ecs_brand as d', 'gs.gs_brand_id', '=', 'd.brand_id')
				->selectRaw("DISTINCT gs.gs_id,gs.gs_brand_id, gs.gs_name, gs.gs_goods_id, gs.rec_goods_ids, gs.gs_address,gs.gs_lng,gs.gs_lat,d.brand_name,d.brand_id,d.brand_logo");
			$result = $result->where('gs.gs_stats', 1);
			$result = $result->where('gs.gs_goods_id', '!=', '');

			$result = $result->where($where);

			$result = $result->take(200)->get()->toArray();

			$goodsIdArr = [];
			$sdata = [];
			foreach ($result as $key => $item) {
				$sdata[$key]["lng"] = !empty($param['lng']) ? $param['lng'] : "113.75"; //当前经度;
				$sdata[$key]["lat"] = !empty($param['lat']) ? $param['lat'] : "23.05"; //当前纬度;
				$sdata[$key]['sellerid'] = $item['gs_id'];
				$sdata[$key]['gs_lng'] = $item['gs_lng'];
				$sdata[$key]['gs_lat'] = $item['gs_lat'];
				$sdata[$key]["distance"] = help::distanceBetween($sdata[$key]['lat'], $sdata[$key]['lng'], $item['gs_lat'], $item['gs_lng']);
				$sdata[$key]["brand_logo"] = config('app.static_domain') . "data/brandlogo/" . $item["brand_logo"];
				$sdata[$key]['name'] = $item['gs_name'];
				$sdata[$key]['address'] = $item['gs_address'];
				$sdata[$key]['brand_name'] = $item['brand_name'];
				$sdata[$key]['brand_id'] = $item['brand_id'];
				$sdata[$key]['goods'] = $this->goods($item['gs_goods_id'], $item['rec_goods_ids'], $goodsIdArr);
				foreach ($sdata[$key]['goods'] as $tp) {
					$fid = $tp['goods_id'];
					$goodsIdArr[$fid] = 1;//保存已查出的商品
				}
				$sort[] = $sdata[$key]["distance"];

			}
			array_multisort($sort, SORT_ASC, $sdata);
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
	public function goods($goodsId, $recGoodsIds = '', $goodsIdArr, $catId = '')
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
				$goodsArrTmp = array_intersect($recGoodsIdArr, $goodsId);//array_intersect
				if (!empty($goodsArrTmp)) {
//					$goodsArrTmp = !empty($offGoods) ? getDiffArr($goodsArrTmp, $offGoods) : [];
//					dd($goodsArrTmp);
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
			if ($catId) {
				$result = $result->where('cat_id', $catId);
			}
			$result = $result->take(2)->orderByRaw("is_hot desc,is_best desc,is_new desc")->get()->toArray();
			$goodsData = [];
			foreach ($result as $item) {
				/* 品牌描述 */
				$reserveType = \App\Models\Brand::where(['brand_id' => $item['brand_id']])->value('reserve_type');
				$goodsArrid = GoodsAttr::where('goods_id', $item['goods_id'])->value('goods_attr_id');
				$sData = [
					'goods_id' => $item['goods_id'],
					'foodid' => $item['goods_id'],
					'name' => $item['goods_name'],
					'brand_id' => $item['brand_id'],
					'goods_sn' => $item['goods_sn'],
					'saleqt' => $item['saleqt'],
					'oldprice' => $item['shop_price'],
					'type' => $item['cat_id'],
					'reserve_type' => $reserveType,
					'support_shipping' => $item['pickup_mode'],
					'sizesLength' => !empty($goodsArrid) ? true : false,
					'miniprice' => ($item['is_promote'] == 1 && time() >= $item['promote_start_date'] && time() <= $item['promote_end_date']) ? $item["promote_price"] : $item["shop_price"],
					'icon' => config('app.static_domain') . $item['goods_thumb'],
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
			return false;
		}
		return $goodsData;
	}

	/**
	 * 首页获取店铺列表
	 * @author: colin
	 * @date: 2019/1/16 16:30
	 * @return bool
	 */
	public function catSellers($param, $where)
	{
		try {
			$result = StoresUser::from('ecs_goods_stores as gs')->join('ecs_brand as d', 'gs.gs_brand_id', '=', 'd.brand_id')
				->selectRaw("DISTINCT gs.gs_id, gs.gs_name, gs.gs_goods_id, gs.rec_goods_ids, gs.gs_address,gs.gs_lng,gs.gs_lat,d.brand_name,d.brand_id,d.brand_logo");
			$result = $result->where('gs.gs_stats', 1);
			$result = $result->where('gs.gs_goods_id', '!=', '');

			$result = $result->where($where);

			$result = $result->take(200)->orderByRaw("gs.SaleAmount desc,gs.gs_id desc")->get()->toArray();

			$goodsIdArr = [];
			$sdata = [];
			foreach ($result as $key => $item) {
				$sdata[$key]["sellerid"] = $item['gs_id'];
				$sdata[$key]["brand_id"] = $item['brand_id'];
				$sdata[$key]["lng"] = !empty($param['lng']) ? $param['lng'] : "113.75"; //当前经度;
				$sdata[$key]["lat"] = !empty($param['lat']) ? $param['lat'] : "23.05"; //当前纬度;
				$sdata[$key]['gs_lng'] = $item['gs_lng'];
				$sdata[$key]['gs_lat'] = $item['gs_lat'];
				$sdata[$key]["distance"] = help::distanceBetween($param['lat'], $param['lng'], $item['gs_lat'], $item['gs_lng']);
				$sdata[$key]["brand_logo"] = config('app.static_domain') . "data/brandlogo/" . $item["brand_logo"];
				$sdata[$key]['name'] = $item['gs_name'];
				$sdata[$key]['address'] = $item['gs_address'];
				$sdata[$key]['brand_name'] = $item['brand_name'];
				$sdata[$key]['goods'] = $this->catgoods($item['gs_goods_id'], $item['rec_goods_ids'], $goodsIdArr, $param['catId'], $item['gs_id']);
				if (empty($sdata[$key]['goods'])) {
					unset($sdata[$key]);
					continue;
				}
				foreach ($sdata[$key]['goods'] as $tp) {
					$goodsIdArr[$tp['foodid']] = 1;//保存已查出的商品
				}
				$sort[] = $sdata[$key]["distance"];

			}
			if (empty($sdata)) return [];
			array_multisort($sort, SORT_ASC, $sdata);
			$sdata = array_slice($sdata, 0, 20);
		} catch (\Exception $e) {
			echo $e->getMessage();
			die;
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
	public function catgoods($goodsId, $recGoodsIds = '', $goodsIdArr, $catId = '', $gsId = '')
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
			if ($catId) {
				$result = $result->where('cat_id', $catId);
			}
			$result = $result->take(2)->orderByRaw("is_hot desc,is_best desc,is_new desc")->get()->toArray();
			$reserveType = Brand::where(['brand_id' => $result[0]['brand_id']])->value('reserve_type');
			$goodsData = [];
			foreach ($result as $item) {
				$goodsArrid = GoodsAttr::where('goods_id', $item['goods_id'])->value('goods_attr_id');
				$sData = [
					'foodid' => $item['goods_id'],
					'type' => $item['cat_id'],
					'name' => $item['goods_name'],
					'goods_sn' => $item['goods_sn'],
					'saleqt' => $item['saleqt'],
					'reserve_type' => $reserveType,
					'support_shipping' => $item['pickup_mode'],
				    'oldprice' => $item['shop_price'],
					'sizesLength' => !empty($goodsArrid) ? true : false,
					'miniprice' => ($item['is_promote'] == 1 && time() >= $item['promote_start_date'] && time() <= $item['promote_end_date']) ? $item["promote_price"] : $item["shop_price"],
					'icon' => config('app.static_domain') . $item['goods_thumb'],
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
			return false;
		}
		return $goodsData;
	}

	/**
	 * 店铺详情
	 * @author: colin
	 * @date: 2019/1/23 9:03
	 */
	public function sellerDetail($param)
	{
		try {
			$storeKey = md5(EnumKeys::CACHE_STORE_DETAIL_BY_ID.'_detail_'.$param['sellerId']);
			$data = Cache::get($storeKey);
			$data = [];
			if(empty($data)) {
				$result = StoresUser::from('ecs_goods_stores as gs')->join('ecs_brand as d', 'gs.gs_brand_id', '=', 'd.brand_id')
					->selectRaw("DISTINCT gs.gs_id, gs.gs_name, gs.gs_mobile,gs.gs_goods_id,gs.city_id,gs.picktime_end,gs.picktime_start,gs.SaleAmount,gs.pickup_mode,gs.post_fee,gs.free_post_money, gs.gs_address,gs.gs_lng,gs.gs_lat,gs.store_pic,gs.open_time,gs.close_time,gs.gs_notice,gs.gs_stats,gs.post_fee_2,gs.uptime_end,gs.uptime_start,d.brand_name,d.brand_id,d.brand_logo,d.reserve_type,d.reserve_other");
				$result = $result->where('gs.gs_id', $param['sellerId'])->firstOrFail()->toArray();
				$gsMobile = explode('|', $result['gs_mobile']);
				$data = [
					'cityid' => $result['city_id'],
					'sellerid' => $result['gs_id'],
					'name' => $result['gs_name'],
					'address' => $result['gs_address'],
					'SaleAmount' => $result['SaleAmount'],
					'store_pic' => $result['store_pic'] ? config('app.static_domain') . "data/brandlogo/" . $result['store_pic'] : '',
					'brand' => $result['brand_name'],
					'brand_id' => $result['brand_id'],
					'brand_logo' => config('app.static_domain') . "data/brandlogo/" . $result['brand_logo'],
					'gs_lng' => $result['gs_lng'],
					'gs_lat' => $result['gs_lat'],
					'gs_stats' => $result['gs_stats'],
					'gs_notice' => $result['gs_notice'],
					'open_time' => $result['open_time'],
					'close_time' => $result['close_time'],
					'phone' => $gsMobile[0],
					'free_post_money' => $result['free_post_money'],
					'post_fee' => $result['post_fee'],
					'post_fee_2' => $result['post_fee_2'],
					'pickup_mode' => EnumKeys::$pickupModel[$result['pickup_mode']],
					'pickup_mode_id' => $result['pickup_mode'],
					'picktime_start' => $result['picktime_start'],
					'picktime_end' => $result['picktime_end'],
					'uptime_start' => $result['uptime_start'],
					'uptime_end' => $result['uptime_end'],
					'province_id' => isset(EnumKeys::$citysProvince[$result['city_id']]['province_id']) ? EnumKeys::$citysProvince[$result['city_id']]['province_id'] : 0,

				];
				if ($result["reserve_type"] == 10) {//其它
					$data["reserve_type"] = $result['reserve_other'];
				} else {
					$data["reserve_type"] = (isset(EnumKeys::$serveModel[$result['reserve_type']])) ? EnumKeys::$serveModel[$result['reserve_type']] : '';//蛋糕预定类型
				}
				$goodids = $result['gs_goods_id'] ? unserialize($result['gs_goods_id']) : [];
				$class = Goods::from('ecs_goods as a')->join('ecs_category as b', 'a.cat_id', '=', 'b.cat_id')
					->selectRaw("distinct a.cat_id as type,b.cat_name as name")
					->whereIn('a.goods_id', $goodids)
					->where(['a.is_on_sale' => 1, 'a.is_delete' => 0]);
				$class = $class->orderBy('b.sort_order', 'asc')->get()->toArray();
				$data['classify'] = $class;
				Cache::put($storeKey,$data,15);
			}
			//是否正在经营中
			$hmStr = strtotime('H:i');
			$data['is_sell_on'] = 1;
			if ($data["open_time"] != '' && $data["close_time"] != '') {
				$openTimeStr = strtotime($data['open_time']);
				$closeTimeStr = strtotime($data['close_time']);
				if ($openTimeStr <= $hmStr && $closeTimeStr >= $hmStr) {
					$sdata['is_sell_on'] = 1;
				} else {
					$sdata['is_sell_on'] = 0;
				}
			}
			$data['lng'] = !empty($param['lng']) ? $param['lng'] : "113.75";//当前经度;
			$data['lat'] = !empty($param['lat']) ? $param['lat'] : "23.05"; //当前纬度
			$data['distance'] = help::distanceBetween($data['lat'], $data['lng'], $data['gs_lat'], $data['gs_lng']);
			return $data;
		} catch (\Exception $e) {
			return false;
		}
	}

	/***
	 * 品牌详情
	 * @author: colin
	 * @date: 2019/5/20 14:30
	 * @param $brandId
	 * @return bool|mixed|void
	 * @throws \Psr\SimpleCache\InvalidArgumentException
	 */
	public function brandInfo($brandId)
	{
		try{
			$brandKey = md5(EnumKeys::CACHE_BRAND_INFO.'_'.$brandId);
			$data = Cache::get($brandKey);
			if(!empty($data))
				return $data;
			$data = Brand::selectRaw("brand_id,brand_name,brand_logo,brand_desc,sort_order as sort,site_url as brand_avatar")->where('brand_id',$brandId)->firstOrFail()->toArray();
			$data['brand_logo'] = config('app.static_domain') . "data/brandlogo/" .$data['brand_logo'] ;
			$data['desc'] = $data['brand_desc'] ;
			$data['extend'] = $this->getBrandExtendData($brandId) ;
			$data = Cache::put($brandKey,$data,60);
			return $data;
		}catch(\Exception $e){
			echo $e->getMessage();die;
			return false;
		}
	}

	/**
	 * 获取品牌扩展数据
	 * @author: colin
	 * @date: 2019/5/20 14:14
	 * @param int $brandId
	 * @return array|mixed
	 * @throws Exception
	 * @throws \Psr\SimpleCache\InvalidArgumentException
	 */
	public function getBrandExtendData($brandId = 0)
	{
		try{
			$brandExtendKey = md5(EnumKeys::CACHE_BRAND_EXTEND.'_'.$brandId);
			$data = Cache::get($brandExtendKey);
			if(!empty($data))
				return $data;
			$result = BrandExtend::selectRaw("cat_id,gs_extract,gs_desc")->where('brand_id',$brandId)->get()->toArray();
			foreach($result as $item){
				$catId = $item['cat_id'];
				unset($item['cat_id']);
				$data[$catId] = $item;
			}
			Cache::put($brandExtendKey,$data,60);
			return $data;
		}catch(\Exception $e){
			return false;
		}
	}
}