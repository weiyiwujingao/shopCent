<?php

namespace App\Repositories;

use App\Models\Goods;
use App\Models\GoodsRegion;
use App\Models\Region;
use \Exception;
use DB;
use App\Models\GoodsAttr;
use App\Models\GoodStoreAttr;
use App\Models\StoresUser;
use App\Models\Brand;
use Illuminate\Support\Facades\Cache;
use Enum\EnumKeys;
use Helper\CFunctionHelper as help;

class GoodsRepository
{
	/**
	 * 根据id获取商品信息
	 * @param $id
	 * @return mixed
	 */
	public function ById($id)
	{
		try {
			if (empty($id))
				throw new  Exception('商品id不为空！');
			$goodInfo = Goods::where('goods_id', $id)->firstOrFail()->toArray();
			return $goodInfo;
		} catch (\Exception $e) {
			return false;
		}

	}

	/**
	 * 获取品牌门店列表
	 * @author: colin
	 * @date: 2019/1/18 15:32
	 * @param $param
	 * @param $where
	 * @return array|bool
	 */
	public function getGoods($param, $whereStore, $whereGoods)
	{
		try {
			$goodsIdArr = [];
			$storeArr = StoresUser::selectRaw("gs_goods_id,gs_id,gs_auth")->where($whereStore)->get()->toArray();
			foreach ($storeArr as $item) {
				$goodsArr = !empty($item['gs_goods_id']) ? unserialize($item['gs_goods_id']) : [];
				if (empty($goodsArr)) {
					continue;
				}
				if ($item['gs_auth'] == 1) {
					$offGoods = $this->getOffGoods($item['gs_id']);
					if (!empty($offGoods)) {
						$goodsArr = array_diff($goodsArr, $offGoods);
					}
				}
				$goodsIdArr = array_merge($goodsIdArr, $goodsArr);
			}
			if (empty($goodsIdArr)) {
				return [];
			}
			$offset = ($param['page'] - 1) * $param['pageSize'];
			$where = ["a.is_on_sale" => "1", "a.is_delete" => 0];
			if (!empty($param['sellerId'])) {
				$sellerId = $param['sellerId'];
				$goods = Goods::from('ecs_goods as a')->join('ecs_brand as b', 'a.brand_id', '=', 'b.brand_id')->leftJoin('ecs_goods_stores_attribute as c', function ($join) use ($sellerId) {
					$join->on("c.goods_id", '=', 'a.goods_id')->where('c.gs_id', $sellerId);
				})->selectRaw("a.*,b.reserve_type")
					->where($where)
					->where($whereGoods);
				if ($goodsIdArr)
					$goods = $goods->whereIn('a.goods_id', $goodsIdArr);
				$goods = $goods->skip($offset)->take($param['pageSize'])->orderByRaw("c.sort desc,a.saleqt desc")->get()->toArray();
			} else {
				$goods = Goods::from('ecs_goods as a')->join('ecs_brand as b', 'a.brand_id', '=', 'b.brand_id')
					->selectRaw("a.*,b.reserve_type")
					->where($where)
					->where($whereGoods);
				if ($goodsIdArr)
					$goods = $goods->whereIn('a.goods_id', $goodsIdArr);
				$goods = $goods->skip($offset)->take($param['pageSize'])->orderByRaw("a.saleqt desc")->get()->toArray();
			}
			$brandIdArr = $goodsData = [];
			foreach ($goods as $key => $val) {
				if (isset($param['isBest']) && $param['isBest'] && isset($brandIdArr[$val['brand_id']]))
					continue;
				$brandIdArr[$val['brand_id']] = 1;
				$goodsArrid = GoodsAttr::where('goods_id', $val['goods_id'])->value('goods_attr_id');

				$sdata['sizesLength'] = !empty($goodsArrid) ? true : false;
				$sdata["is_promote"] = $val['is_promote'];
				$sdata["foodid"] = $val['goods_id'];
				$sdata["goods_sn"] = $val["goods_sn"];
				$sdata["saleqt"] = $val["saleqt"];
				$sdata["brandid"] = $val['brand_id'];
				$sdata["support_shipping"] = $val["pickup_mode"];
				$sdata["name"] = $val["goods_name"];
				$sdata["type"] = $val["cat_id"];
				$sdata["reserve_type"] = $val["reserve_type"];
				$sdata["pay_types"] = $val["pay_types"];
				$sdata["pay_type_limit"] = $val["pay_type_limit"];
				$sdata["miniprice"] = ($val['is_promote'] == 1 && time() >= $val['promote_start_date'] && time() <= $val['promote_end_date']) ? $val["promote_price"] : $val["shop_price"];
				$sdata["oldprice"] = $val["shop_price"];
				$sdata["icon"] = config('app.static_domain') . $val["goods_thumb"];
				$sdata["image"] = config('app.static_domain') . $val["original_img"];
				if ($sdata['miniprice'] == 0) {
					$attrPrice = GoodsAttr::from('ecs_goods_attr as a')->join('ecs_attribute as b', 'a.attr_id', 'b.attr_id')
						->where('goods_id', $item['goods_id'])->orderByRaw("CAST(attr_price AS SIGNED)")->value('attr_price');
					$sdata["miniprice"] = $attrPrice;
					$sdata["oldprice"] = $attrPrice;
				}
				array_push($goodsData, $sdata);
			}
			return $goodsData;

		} catch (\Exception $e) {
			echo $e->getMessage();die;
			return false;
		}
	}

	/**
	 * 获取门店自定义下架的商品id
	 * @param int $gsId
	 * @return array|bool
	 */
	public function getOffGoods($gsId = 0)
	{
		$goodsId = [];
		if (!empty($gsId)) {
			$keycache = md5(EnumKeys::CACHE_STORE_OFF_GOODS . '_goodslist_' . $gsId);
			$goodsId = Cache::get($keycache);
			if (!empty($goodsId)) {
				return $goodsId;
			}
			$goodsId = GoodStoreAttr::where(['gs_id' => $gsId, 'sale_status' => 2])->pluck("goods_id")->toArray();
			Cache::put($keycache, $goodsId, 5);

		}
		return $goodsId;
	}

	/**
	 * 获取最新商品信息
	 * @author: colin
	 * @date: 2019/1/23 17:16
	 * @param $goodsId
	 */
	public function goodMess($goodsId)
	{
		try {
			$data = Goods::from('ecs_goods as g')->join('ecs_brand as b', 'g.brand_id', '=', 'b.brand_id')
				->selectRaw("g.goods_id,g.reserve_hours,g.pay_type_limit,g.pay_types,b.reserve_type,g.pickup_mode as support_shipping")
				->whereIn('g.goods_id', $goodsId);
			$data = $data->get()->toArray();
		} catch (\Exception $e) {
			return false;
		}
		return $data;
	}

	/**
	 * 统计销量
	 * @author: colin
	 * @date: 2019/1/29 11:39
	 * @param $goodsId
	 * @param $goodsNumber
	 */
	public function salesNum($goodsId, $goodsNumber)
	{
		$data = Goods::where('goods_id', $goodsId)->first();
		$data->saleqt += $goodsNumber;
		$data->save();
	}

	/**
	 * 统计销量(店铺)
	 * @author: colin
	 * @date: 2019/1/29 11:39
	 * @param $goodsId
	 * @param $goodsNumber
	 */
	public function storeNum($gsId, $number)
	{
		$data = StoresUser::where('gs_id', $gsId)->first();
		$data->SaleAmount += $number;
		$data->save();
	}

	/**
	 * 搜索
	 * @author: colin
	 * @date: 2019/1/31 10:17
	 * @param $keyword
	 * @param $cityId
	 */
	public function search($param, $where)
	{
		try {
			$lng = !empty($param['lng']) ? $param['lng'] : "113.75";
			$lat = !empty($param['lat']) ? $param['lat'] : "23.05";
			$type = empty($param['type']) ? 1 : intval($param['type']);//2店铺1产品
			$data = $dataArr = [];
			switch($type){
				case 1:
					$grIdArr = [];
					if (!empty($param['cityId'])) {
						$grIdArr = GoodsRegion::where('gr_id', $param['cityId'])->orWhere('parent_id', $param['cityId'])->pluck('gr_id')->toArray();
					}
					$stores = StoresUser::where('gs_stats', 1)->where('gs_goods_id', '!=', ' ');
					if (!empty($grIdArr))
						$stores = StoresUser::whereIn('gs_region_id', $grIdArr);
					$goodsIds = $stores->pluck('gs_goods_id')->toArray();
					$goodsIdArr = [];
					foreach ($goodsIds as $val) {
						if (empty($val))
							continue;
						$goodsArr = !empty($val) ? unserialize($val) : [];
						if (empty($goodsArr))
							continue;
						$goodsIdArr = array_merge($goodsIdArr, $goodsArr);
						$goodsIdArr = array_unique($goodsIdArr);
					}
					if(empty($goodsIdArr))
						return ['seller'=>$data,'goods'=>[]];
					$goods = Goods::whereIn('goods_id',$goodsIdArr)->where(['is_on_sale'=>1,'is_delete'=>0]);
					if($param['keyword'])
						$goods = $goods->where('goods_name','like',"%{$param['keyword']}%");
					$goods = $goods->take(10)->get()->toArray();
					foreach($goods as $item){
						$sData = [
							'goods_id' => $item['goods_id'],
							'goods_name' => $item['goods_name'],
							'goods_sn' => $item['goods_sn'],
							'saleqt' => $item['saleqt'],
							'old_price' => $item['shop_price'],
							'miniprice' => ($item['is_promote'] == 1 && time() >= $item['promote_start_date'] && time() <= $item['promote_end_date']) ? $item["promote_price"] : $item["shop_price"],
							'icon' => config('app.static_domain') . $item['goods_thumb'],
						];
						if ($sData['miniprice'] == 0) {
							$attrPrice = GoodsAttr::from('ecs_goods_attr as a')->join('ecs_attribute as b', 'a.attr_id', 'b.attr_id')
								->where('goods_id', $item['goods_id'])->orderByRaw("CAST(attr_price AS SIGNED)")->value('attr_price');
							$sData["miniprice"] = $attrPrice;
							$sData["oldprice"] = $attrPrice;
						}
						array_push($dataArr, $sData);
					}
					$data['food'] = $dataArr;
					break;
				case 2:
					$storeData = StoresUser::where('gs_stats', 1)->where($where);
					$storeData = $storeData->take(15)->orderByRaw("(gs_lng-{$lng})*(gs_lng-{$lng})+(gs_lat-{$lat})*(gs_lat-{$lat})")->get()->toArray();
					foreach ($storeData as $key => $val) {
						$val['brand_logo'] = Brand::where('brand_id', $val['gs_brand_id'])->value('brand_logo');
						$sdata["brand_logo"] = config('app.static_domain') . "/data/brandlogo/" . $val['brand_logo'];
						$sdata["distance"] = help::distanceBetween($lat, $lng, $val['gs_lat'], $val['gs_lng']);
						$sdata["name"] = $val["gs_name"];
						$sdata["sellerid"] = $val['gs_id'];
						array_push($dataArr, $sdata);
					}
					$data['sellers'] = $dataArr;
					break;
				default:
					break;
			}
		} catch (\Exception $e) {
			return false;
		}
		return $data;
	}

	/**
	 * 获取商品
	 * @author: colin
	 * @date: 2019/1/31 13:44
	 * @param $goodsIdArr
	 * @return array|bool
	 */
	public function goods($goodsIdArr)
	{
		try {
			$result = Goods::where('is_on_sale', 1)
				->where('is_delete', 0)
				->whereIn('goods_id', $goodsIdArr);
			$result = $result->take(2)->orderByRaw("is_hot desc,is_best desc,is_new desc")->get()->toArray();
			$goodsData = [];
			foreach ($result as $item) {
				$sData = [
					'goods_id' => $item['goods_id'],
					'goods_name' => $item['goods_name'],
					'goods_sn' => $item['goods_sn'],
					'saleqt' => $item['saleqt'],
					'old_price' => $item['shop_price'],
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
}