<?php

namespace Library\ProductCenter;

use App;
use Illuminate\Http\Request;
use \Exception;
use Enum\EnumKeys;
use Illuminate\Support\Facades\Cache;
use \Helper\CFunctionHelper as help;
use \App\Models\GoodsRegion;
use \App\Models\Category;
use \App\Models\GoodsStock;
use \App\Models\StoresUser;
use \App\Models\GoodsAttr;
use \App\Models\SecKillGood;
use \App\Models\SecPre;
use \App\Models\Brand;

class Goods extends \Library\CBase
{
	protected $request;
	protected $userCMd;

	public function __construct(Request $request)
	{
		$this->request = $request;
		$this->GoodsMd = new App\Repositories\GoodsRepository();
		parent::__construct(__CLASS__);
	}

	/**
	 * 商品列表
	 * @author: colin
	 * @date: 2019/1/16 10:06
	 * @return \Library\type
	 */
	public function goods()
	{
		try {
			$param = $this->request->all();
			$whereStore = $this->SearchArr();
			$whereGoods = $this->SearchGoods();
			$result = $this->GoodsMd->getGoods($param, $whereStore, $whereGoods);
			if ($result === false) {
				return false;
			}
		} catch (\Exception $e) {
			echo $e->getMessage();
			die;
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => "Home index:" . json_encode(1) . ",reason:" . $e->getMessage(),
				'userMsg' => $e->getMessage(),
				'line' => __LINE__,
			]);
		}
		return $result;

	}

	/**
	 * 商户搜索条件
	 * @author: colin
	 * @date: 2019/1/16 11:10
	 * @return \Closure
	 */
	public function SearchArr()
	{
		$param = $this->request->all();
		$where = function ($query) use ($param) {
			foreach ($param as $key => $value) {
				if (empty($value)) continue;
				switch ($key) {
					case 'sellerId':
						$query->where('gs_id', $value);
						break;
					case 'cityId':
						if (!empty($param['sellerId']))
							break;
						$goodRegion = new \App\Repositories\GoodReginRepository();
						$gsRegionIds = $goodRegion->getTownIds($value);
						if ($gsRegionIds === false) {
							$gsRegionIds = [$value];
						}
						$gsRegionIds = array_merge([$value], $gsRegionIds);
						$query->whereIn('gs_region_id', $gsRegionIds);
						break;
					default:
						break;

				}
			}
		};
		return $where;
	}

	/**
	 * 商品搜索条件
	 * @author: colin
	 * @date: 2019/1/16 11:10
	 * @return \Closure
	 */
	public function SearchGoods()
	{
		$param = $this->request->all();
		$where = function ($query) use ($param) {
			foreach ($param as $key => $value) {
				if (empty($value)) continue;
				switch ($key) {
					case 'type':
						$time = time();
						if ($value == 10000001)
							$query->whereRaw("(a.is_promote=1 and '{$time}'>=a.promote_start_date and '{$time}'<=a.promote_end_date )");
						else
							$query->where("a.cat_id", $value);
						break;
					case 'shipping':
						$query->where('a.support_shipping', $value);
						break;
					case 'brandId':
						$query->where('a.brand_id', $value);
						break;
					case 'isBest':
						$query->whereRaw("a.is_best = 1");
						break;
					default:
						break;

				}
			}
		};
		return $where;
	}

	/**
	 * 购物车结算获取最新商品信息
	 * @author: colin
	 * @date: 2019/1/23 16:53
	 * @return array|bool|\Library\type
	 */
	public function goodMess()
	{
		try {
			$goodsDataArr = $this->request->input('goodsData');
			if (empty($goodsDataArr))
				throw new Exception('没有商品');
			if(!is_array($goodsDataArr))
				$goodsDataArr = \GuzzleHttp\json_decode($goodsDataArr,true);
			$goodsIdArr = array_column($goodsDataArr, 'goods_id');
			if (empty($goodsIdArr))
				throw new Exception('没有商品！');

			$result = $this->GoodsMd->goodMess($goodsIdArr);
			if ($result === false)
				throw new Exception('获取商品信息失败！');

			$goodsDataTmp = [];
			foreach ($result as $item) {
				$id = (int)$item['goods_id'];
				$goodsDataTmp[$id] = $item;
			}
			foreach ($goodsDataArr as &$item) {
				$id = (int)$item['goods_id'];
				$item = array_merge($item, $goodsDataTmp[$id]);
			}

		} catch (\Exception $e) {
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => "Goods goodMess:" . json_encode($goodsDataArr) . ",reason:" . $e->getMessage(),
				'userMsg' => $e->getMessage(),
				'line' => __LINE__,
			]);
		}
		return $goodsDataArr;

	}

	/**
	 * 商品详情
	 * @author: colin
	 * @date: 2019/1/30 17:02
	 * @return array|\Library\type
	 */
	public function goodDetail()
	{
		$goodsId = $this->request->input('id');
		$gsId = $this->request->input('seller_id');
		$isSec = $this->request->input('is_sec');
		$id = $this->request->input('id');
		try {
			if ($isSec === 1 && !empty($id)) {
				$time = date('Y-m-d H:i');
				$nowtime = time();
				$sec = SecKillGood::from("sec_kill_goods as kg")->join('sec_base as b', 'kg.sec_id', '=', 'b.id')
					->selectRaw("kg.id,kg.stores_id,b.start_time,b.date")
					->where(['kg.status'=>1,'b.status'=>1,'kg.id'=>$id])
					->first()
					->toArray();
				if (empty($sec)) {
					throw new Exception('不存在商品！');
				}
				$sec_time = $sec['date'] . ' ' . $sec['start_time'];
				$sec_t = strtotime($sec_time);
				$sec['sec_status'] = ($time >= $sec_time) ? 1 : 2;
				$sec['sec_time'] = $sec_time;
				$sec['sec_second'] = ($sec['sec_status'] == 2) ? $sec_t - $nowtime : 0;
			}
			$gsAuth = $gsId ? help::getGsAuth($gsId) : 0;
			$goods = \App\Models\Goods::where(['ecs_goods.goods_id' => $goodsId])
				->where(['ecs_goods.is_on_sale' => 1])
				->get()->toArray();
			if (!isset($goods['0']['goods_id']))
				throw new Exception('不存在的商品！');
			/* 品牌描述 */
			$brand_desc = \App\Models\Brand::select('gs_desc_16', 'gs_extract_16', 'reserve_type')->where(['brand_id' => $goods[0]['brand_id']])->get()->toArray();
			$gs_extract = $gs_desc = '';
			if ($brand_desc['0']) {
				$gs_extract = $brand_desc['0']['gs_extract_16'];
				$gs_desc = $brand_desc['0']['gs_desc_16'];
				$goods['0']['reserve_type'] = $brand_desc['0']['reserve_type'];
			}
			$offGoodsId = help::getOffGoods($gsId);
			$goods['0']['is_sold_out'] = 0;
			if (!empty($offGoods) && in_array($goods['0']['goods_id'], $offGoodsId)) {
				$goods['0']['is_sold_out'] = 1;
			}
			$goods['0']["stock"] = -1;
			$isPromote = ($goods['0']['is_promote'] == 1 && time() >= $goods['0']['promote_start_date'] && time() <= $goods['0']['promote_end_date']) ? true : false;
			if (!isset($sec) && $gsAuth && $goods['0']['is_sold_out'] == 0) {//查库存
				$zname = $isPromote ? 'num_promotion' : 'num';
				$stockData = GoodsStock::selectRaw("sum({$zname}) as stock_num,count(st_id) as stnum")->where('gs_id', $gsId)->where('goods_id', $goods['0']['goods_id'])->first();
				$goods['0']["stock"] = !empty($stockData->stnum) ? $stockData->stock_num : -1;
				if ($goods['0']["stock"] === 0) {
					$goods['0']['is_sold_out'] = 1;
				}
			}
			if (isset($sec)) {
				$sdata["stores_id"] = $sec['stores_id'];
				$sdata["sec_status"] = $sec['sec_status'];
				$sdata["sec_time"] = $sec['sec_time'];
				$sdata["sec_second"] = $sec['sec_second'];
				$gsIds = explode(',', $sec['stores_id']);
				$sdata['is_sold_out'] = 0;
				foreach ($gsIds as $gs) {//查库存
					$zname = $isPromote ? 'num_promotion' : 'num';
					$sdata["stock"] = 0;
					$stockData = GoodsStock::selectRaw("sum({$zname}) as stock_num,count(st_id) as stnum")->where('gs_id', $gs)->where('goods_id', $goodsId)->first()->toArray();
					$sdata["stock"] += $stockData['stock_num'];
					if ($sdata["stock"] <= 0) {
						$sdata['is_sold_out'] = 1;
					} else {
						$sdata['is_sold_out'] = 0;
					}
				}
				$sdata["pre_status"] = 0;
				$userId = $this->request->input('uid');
				if (!empty($userId)) {
					$secPre = SecPre::where(['user_id'=>$userId,'goods_id'=>$goodsId,'kill_id'=>$sec['id']])->value('id');
					$sdata["pre_status"] = !empty($secPre) ? 1 : 0;
				}
			}
			if ($gsId) {
				$rdata = StoresUser::from('ecs_goods_stores as s')->join('ecs_goods_region as r', 'r.gr_id', '=', 's.gs_region_id')->where('s.gs_id', $gsId)->first();
				$cityId = $rdata->gs_id;
				if (!empty($rdata->parent_id)) {
					$cityId = $rdata->parent_id;
				}
				if ($cityId) {
					$gridArr = GoodsRegion::where(['parent_id' => $cityId, 'enable' => 1])->pluck('gr_id')->toArray();
					$goods['0']['store_num'] = StoresUser::whereIn('gs_region_id', $gridArr)->where('gs_stats', 1)->whereRaw("gs_goods_id like '%{$goodsId}%'")->count('gs_id');
				}
			}

		} catch (\Exception $e) {
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => $e->getMessage(),
				'userMsg' => $e->getMessage(),
				'line' => __LINE__,
			]);
		}
		$goods = self::goodsInfo($goods[0]);
		self::sizes($goods);
		$goodsInfo = [
			'goods' => $goods,
			'gs_extract' => $gs_extract,
			'gs_desc' => $gs_desc,
		];
		return $goodsInfo;
	}

	/***
	 * 商品分类属性
	 * @author: colin
	 * @date: 2018/11/19 10:49
	 * @param $properties
	 * @return array
	 */
	private function properties($properties)
	{
		if (empty($properties))
			return [];
		foreach ($properties AS $row) {
			$arr[$row['attr_id']]['attr_type'] = $row['attr_type'];
			$arr[$row['attr_id']]['name'] = $row['attr_name'];
			$arr[$row['attr_id']]['values'][] = [
				'label' => $row['attr_value'],
				'price' => $row['attr_price'],
				'id' => $row['goods_attr_id'],
			];
		}
		return $arr;
	}

	/**
	 * 商品分类属性
	 * @author: colin
	 * @date: 2019/5/17 19:09
	 * @param $goodsId
	 */
	private function sizes(&$goods)
	{
		try {
			$resType = GoodsAttr::from("ecs_goods_attr as a")->join("ecs_attribute as b", 'a.attr_id', '=', 'b.attr_id')->orderByRaw("CAST(attr_price AS SIGNED)")->where('a.goods_id', $goods['goods_id'])->get()->toArray();
			$goods["sizes"] = [];
			$size = $tastes = [];
			foreach ($resType as $k => $v) {
				$v['img'] = !empty($v['img']) ? config('app.static_host') . $v['img'] : $v['img'];
				if ($v["attr_id"] == 217) {
					if ($goods["miniprice"] == 0)
						$goods["miniprice"] = $v["attr_price"];
					if ($goods["oldprice"] == 0)
						$goods["oldprice"] = $v["attr_price"];
					$arr = explode('|', $v["attr_value"]);
					$sizeData = [
						"size" => $arr[0],
						"tastes" => $arr[1],
						"price" => $v["attr_price"],
						"original_price" => $v["original_price"],
						"tastesunit" => '',
						"sizeunit" => '',
						"tastesprice" => 0,
						"sizeprice" => 0,
						"tastesid" => $v["goods_attr_id"],
						"sizeid" => $v["goods_attr_id"],
						'img' => $v['img'],
						'stock' => -1
					];
					array_push($goods["sizes"], $sizeData);
				} else {
					if ($v["attr_id"] == 215)
						array_push($tastes, ["name" => $v["attr_value"], "price" => $v["attr_price"], "original_price" => $v["original_price"], "unit" => $v["attr_name"], "tastesid" => $v["goods_attr_id"], 'img' => $v['img']]);
					else
						array_push($size, ["name" => str_replace($v["attr_name"], "", $v["attr_value"]), "unit" => $v["attr_name"], "price" => $v["attr_price"], "original_price" => $v["original_price"], "sizeid" => $v["goods_attr_id"], 'img' => $v['img']]);
				}
			}
			if (empty($sdata["sizes"])) {
				foreach ($size as $s) {
					foreach ($tastes as $t) {
						$price = help::priceFormat($t["price"],false) + help::priceFormat($s["price"],false) + help::priceFormat($goods["miniprice"],false);
						$stock = -1;
						$thsize = [
							"size" => $s["name"] . (in_array($s["unit"], array('个数', '规格')) ? "" : $s["unit"]),
							"tastes" => $t["name"],
							"price" => $price,
							"original_price" => $s["original_price"],
							"tastesunit" => $t["unit"],
							"sizeunit" => $s["unit"],
							"tastesprice" => $t["price"],
							"sizeprice" => $s["price"],
							"tastesid" => $t["tastesid"],
							"sizeid" => $s["sizeid"],
							'img' => $t['img'],
							'stock' => $stock
						];
						array_push($goods["sizes"], $thsize);
					}
					if ($tastes == null) {
						$stock = -1;
						$s["price"] = help::priceFormat($s["price"],false);
						$thtast = [
							"size" => $s["name"] . (in_array($s["unit"], ['个数', '规格']) ? "" : $s["unit"]),
							"price" => $s["price"] + $goods["miniprice"],
							"original_price" => $s["original_price"],
							"sizeunit" => $s["unit"],
							"sizeprice" => $s["price"],
							"sizeid" => $s["sizeid"],
							'img' => $s['img'],
							'stock' => $stock
						];
						array_push($goods["sizes"], $thtast);
					}
				}
				if ($tastes != null && $size == null) {
					foreach ($tastes as $t) {
						$stock = -1;
						$thtast = [
							"tastes" => $t["name"],
							"price" => ($t["price"] + $goods["miniprice"]),
							"original_price" => $t["original_price"],
							"tastesunit" => $t["unit"],
							"tastesprice" => $t["price"],
							"tastesid" => $t["tastesid"],
							'img' => $t['img'],
							'stock' => $stock
						];
						array_push($goods["sizes"], $thtast);
					}
				}
			}
		} catch (\Exception $e) {
			return false;
		}
	}

	/**
	 * 商品基础信息
	 * @author: colin
	 * @date: 2018/11/19 11:15
	 * @param $goods
	 * @return array
	 */
	private function goodsInfo($row)
	{
		if (empty($row))
			return [];
		$isPromote = 0;
		/* 修正促销价格 */
		if ($row['promote_price'] > 0) {
			$row['promote_price'] = \Helper\CFunctionHelper::isPromote($row['promote_price'], $row['promote_start_date'], $row['promote_end_date']);
			$isPromote = $row['promote_price'] ? 1 : 0;
		}
		if ($row['goods_img']) {
			$row['goods_img'] = config('merchant.static_host') . $row['goods_img'];
		}
		/* 商户商品上下架状态 */
		$sale_status = \App\Models\GoodStoreAttr::where(['goods_id' => $row['goods_id'], 'gs_id' => $this->request->input('gsId')])->value('sale_status');
		$sale_status = $sale_status ?? 1;
		$arr = [
			'type' => $row['cat_id'],
			'brandid' => $row['brand_id'],
			'goods_id' => $row['goods_id'],
			'goods_sn' => $row['goods_sn'],
			'name' => $row['goods_name'],
			'shop_price' => $row['shop_price'],
			'oldprice' => $row['shop_price'],
			'is_promote' => $isPromote,
			'miniprice' => $isPromote ? $row["promote_price"] : $row["shop_price"],
			'market_price' => $row['market_price'],
			'promote_price' => $row['promote_price'],
			'image' => config('app.static_domain') . $row['original_img'],
			'icon' => config('app.static_domain') . $row['goods_thumb'],
			'goods_number' => $row['goods_number'],
			'free_post' => $row['free_post'],
			'is_on_sale' => $row['is_on_sale'],
			'is_sold_out' => $row['is_sold_out'],
			'pay_type_limit' => $row['pay_type_limit'],
			'pay_types' => $row['pay_types'],
			'reserve_type' => $row['reserve_type'],
			'stock' => $row['stock'],
			'reserve_hours' => $row['reserve_hours'],
			'support_shipping' => $row['pickup_mode'],
			'store_num' => isset($row['store_num']) ? $row['store_num'] : '',
			'sale_status' => $sale_status,
			'description' => help::descIMgReplace($row['goods_desc']),
		];
		return $arr;
	}

	/**
	 * 取得商品最终使用价格
	 *
	 * @param   string $goodsId 商品编号
	 * @param   string $goodsNum 购买数量
	 * @param   boolean $isSpecPrice 是否加入规格价格
	 * @param   mix $spec 规格ID的数组或者逗号分隔的字符串
	 *
	 * @return  商品最终购买价格
	 */
	public function getFinalPrice($goodsId, $goodsNum = '1', $isSpecPrice = false, $spec = [])
	{
		$finalPrice = '0'; //商品最终购买价格
		$volumePrice = '0'; //商品优惠价格
		$promotePrice = '0'; //商品促销价格
		$userPrice = '0'; //商品会员价格

		//取得商品优惠价格列表
		$priceList = $this->getVolumePriceList($goodsId, '1');
		foreach ($priceList as $value) {
			if ($goodsNum >= $value['number']) {
				$volumePrice = $value['price'];
			}
		}
		//取得商品促销价格列表
		$goods = \App\Models\Goods::selectRaw("shop_price,promote_price,promote_start_date,promote_end_date")
			->where(['goods_id' => $goodsId, 'is_on_sale' => 1, 'is_delete' => 0])
			->first();
		/* 修正促销价格 */
		if ($goods->promote_price > 0) {
			$promotePrice = \Helper\CFunctionHelper::isPromote($goods->promote_price, $goods->promote_start_date, $goods->promote_end_date);
		}
		//比较商品 会员价格，优惠价格,促销价格 获取大于0的最小值
		$finalPrice = help::getMinAll([$goods->shop_price, $volumePrice, $promotePrice]);
		//如果需要加入规格价格
		if ($isSpecPrice) {
			if (!empty($spec)) {
				$specPrice = self::specPrice($spec);
				$finalPrice += $specPrice;
			}
		}
		$finalPrice = help::priceFormat($finalPrice, false);
		//返回商品最终购买价格
		return $finalPrice;
	}

	/***
	 * 规格价格
	 * @author: colin
	 * @date: 2018/11/27 13:43
	 * @param $spec
	 * @return float|int|type
	 */
	private function specPrice($spec)
	{
		if (empty($spec))
			return 0;
		$specNew = $spec;
		try {
			$danGao = 0;
			foreach ($spec as $key => $val) {
				$attrId = \App\Models\GoodsAttr::where('goods_attr_id', $val)->value('attr_id');
				if ($attrId == 213) {
					$danGao = 1;
					$specBangId = $val;
					array_splice($specNew, $key, 1);
				}
			}
			if ($danGao > 0) {
				$priceOther = \App\Models\GoodsAttr::selectRaw("SUM(attr_price) AS attr_price")->whereIn('goods_attr_id', $specNew)->first();
				$priceOther = $priceOther->attr_price;
				$resBang = \App\Models\GoodsAttr::select('attr_price', 'attr_value')->where('goods_attr_id', $specBangId)->firstOrFail();
				$resBang->attr_price = str_replace("磅", "", $resBang->attr_price);
				$priceBang = floatval($resBang->attr_price);
				$priceBangValue = floatval($resBang->attr_value);
				$price = $priceBang + $priceOther * $priceBangValue;
			} else {
				$price = \App\Models\GoodsAttr::selectRaw("SUM(attr_price) AS attr_price")->whereIn('goods_attr_id', $spec)->firstOrFail();
				$price = $price->attr_price;
			}
		} catch (\Exception $e) {
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => $e->getMessage(),
				'userMsg' => $e->getMessage(),
				'line' => __LINE__,
			]);
		}
		return $price;
	}

	/**
	 * 取得商品优惠价格列表
	 *
	 * @param   string $goodsId 商品编号
	 * @param   string $priceType 价格类别(0为全店优惠比率，1为商品优惠价格，2为分类优惠比率)
	 *
	 * @return  优惠价格列表
	 */
	public function getVolumePriceList($goodsId, $priceType = '1')
	{
		$volumePrice = [];
		$res = \App\Models\VolumePrice::where(['goods_id' => $goodsId, 'price_type' => $priceType])->orderBy('volume_number', 'desc')->get()->toArray();
		foreach ($res as $k => $v) {
			$volumePrice[] = [
				'number' => $v['volume_number'],
				'price' => $v['volume_price'],
				'format_price' => \Helper\CFunctionHelper::priceFormat($v['volume_price']),
			];
		}
		return $volumePrice;
	}

	/**
	 * 搜索
	 * @author: colin
	 * @date: 2019/1/31 9:44
	 * @return array|bool|\Library\type
	 */
	public function search()
	{
		try {
			$param = $this->request->all();
			$where = $this->searchParam();
			$result = $this->GoodsMd->search($param, $where);
			if ($result === false) {
				return false;
			}
		} catch (\Exception $e) {
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => "Good search:" . json_encode(1) . ",reason:" . $e->getMessage(),
				'userMsg' => $e->getMessage(),
				'line' => __LINE__,
			]);
		}
		return $result;
	}

	/**
	 * 查询条件
	 * @author: colin
	 * @date: 2019/1/31 10:39
	 * @return \Closure|string
	 */
	public function searchParam()
	{
		$param = $this->request->all();
		$where = function ($query) use ($param) {
			foreach ($param as $key => $value) {
				if (empty($value)) continue;
				switch ($key) {
					case 'keyword':
						$query->whereRaw("gs_name like '%" . $value . "%'");
						break;
					default:
						break;

				}
			}
		};
		return $where;
	}

	/**
	 * 产品分类
	 * @author: colin
	 * @date: 2019/5/15 16:36
	 * @return array|bool|\Library\type
	 */
	public function classify()
	{
		try {
			$cityId = $this->request->input('cityId');
			$classifyCacheKey = md5(EnumKeys::CACHE_CLASSIFY_LIST_HOME . '_' . $cityId);
			$cate = Cache::get($classifyCacheKey);
			if (!empty($cate))
				return $cate;
			if ($cityId)
				$city = GoodsRegion::where('gr_id', $cityId)->value('gr_name');
			$where = ['parent_id' => 0, 'show_in_nav' => 1];
			$cate = Category::selectRaw("cat_id, cat_name as name")->where($where);
			if (!empty($city))
				$cate = $cate->whereRaw("city like '%{$city}%'");
			$cate = $cate->orderBy('sort_order', 'asc')->orderBy('cat_id', 'asc')->get()->toArray();
			Cache::put($classifyCacheKey, $cate, 60);
		} catch (\Exception $e) {
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => "商户的商品类别获取有误reason:" . $e->getMessage(),
				'userMsg' => '商品类别获取不成功！',
				'line' => __LINE__,
			]);
		}
		return $cate;
	}

	/**
	 * 根据产品推荐门店
	 * @author: colin
	 * @date: 2019/5/20 15:28
	 * @return \Library\type|mixed
	 */
	public function goodSellers()
	{
		try {
			$param = $this->request->all();
			$str = implode('_',$param);
			$sellersKey = md5(EnumKeys::CACHE_GOODS_SELLER_RECOMENT . '_' . $str);
			$data = Cache::get($sellersKey);
			if (!empty($data))
				return $data;
			$searchWhere = $this->goodStoreSearch();

			$storeData = StoresUser::selectRaw("gs_id,gs_goods_id,gs_name,gs_address,gs_lng,gs_lat,gs_stats,gs_auth,post_fee,post_fee_2")->where($searchWhere)->get()->toArray();
			if (!empty($storeData)) {
				//品牌
				$brandData = Brand::selectRaw("brand_id,brand_name,brand_logo")->where('brand_id',$param['brandId'])->first()->toArray();
				if (!empty($brandData)) {
					$brandData["brand_logo"] = config('app.static_domain') . "/data/brandlogo/" . $brandData["brand_logo"];
				}
			}
			$top = $data = [];
			$lng = !empty($param['lng']) ? $param['lng'] : "113.75"; //当前经度;
			$lat = !empty($param['lat']) ? $param['lat'] : "23.05"; //当前纬度;
			foreach ($storeData as $key => $val) {
				$storeData[$key]['distance'] = help::distanceBetween($lat, $lng, $val['gs_lat'], $val['gs_lng']);
				$top[] = help::distanceBetween($lat, $lng, $val['gs_lat'], $val['gs_lng']);
				if (!empty($brandData)) {
					$storeData[$key]['brand_id'] = $brandData['brand_id'];
					$storeData[$key]['brand_name'] = $brandData['brand_name'];
					$storeData[$key]['brand_logo'] = $brandData['brand_logo'];
				}
				$data[$key] = $storeData[$key];
			}
			if(!empty($top) && !empty($data))
				array_multisort($top, SORT_ASC, $data);
			Cache::put($sellersKey, $data, 60);
		} catch (\Exception $e) {
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => "推荐门店获取有误reason:" . \GuzzleHttp\json_encode($e->getMessage()).'_param:'.json_decode($param),
				'userMsg' => '推荐门店获取不成功！',
				'line' => __LINE__,
			]);
		}
		return $data;
	}

	/**
	 * 推荐门店查询条件
	 * @author: colin
	 * @date: 2019/5/20 16:52
	 * @param $param
	 * @return \Closure
	 */
	public function goodStoreSearch()
	{
		$param = $this->request->all();
		$where = function ($query) use ($param) {
			foreach ($param as $key => $value) {
				if (empty($value)) continue;
				switch ($key) {
					case 'is_sec':
						if(empty($param['id']))
							break;
						$storeId = SecKillGood::where(['status'=>1,'id'=>$param['id'],'goods_id'=>$param['goodsId']])->value('stores_id');
						if(empty($storeId))
							break;
						$query->where('gs_id',$storeId);
						break;
					case 'brandId':
						$query->where('gs_brand_id',$param['brandId']);
						break;
					case 'goodsId':
						$query->whereRaw("gs_goods_id like '%{$param['goodsId']}%'");
						break;
					case 'cityId':
						$query->whereRaw("(city_id = " . $param['cityId'] . " or find_in_set(" . $param['cityId'] . ",show_citys))");
						break;
					default:
						break;

				}
			}
		};
		return $where;
	}
	/**
	 * 根据产品推荐门店
	 * @author: colin
	 * @date: 2019/5/20 15:28
	 * @return \Library\type|mixed
	 */
	public function sellerRec()
	{
		try {
			$storeId = $this->request->input('sellerId');
			$sellersKey = md5(EnumKeys::CACHE_SELLER_RECOMENT_GOODS . '_' . $storeId);
			$data = Cache::get($sellersKey);
			if (!empty($data))
				return $data;
			$recGoods = StoresUser::where('gs_id',$storeId)->value('rec_goods_ids');
			if (empty($recGoods)) {
				throw new \Exception('无相关数据');
			}
			$goodsData = \App\Models\Goods::selectRaw("goods_id,goods_name,goods_thumb,promote_start_date,promote_end_date,is_promote,promote_price,shop_price")
				->where(['is_on_sale'=>1, 'is_delete' => 0])
				->whereRaw("goods_id in ({$recGoods})")
				->take(10)
				->get()
				->toArray();
			if (empty($goodsData)) {
				throw new Exception('无相关数据!');
			}
			$time = time();
			foreach ($goodsData as &$item) {
				$item["miniprice"] = ($item['is_promote'] == 1 && $time >= $item['promote_start_date'] && $time <= $item['promote_end_date']) ? $item["promote_price"] : $item["shop_price"];
				$item["icon"] = config("app.static_domain") . $item["goods_thumb"];
			}
			Cache::put($sellersKey, $data, 60);
		} catch (\Exception $e) {
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => "门店推荐获取商品获取有误reason:" . $e->getMessage().'param:'.$storeId,
				'userMsg' => '门店推荐商品不成功！',
				'line' => __LINE__,
			]);
		}
		return $goodsData;
	}

	/**
	 * 热门搜索
	 * @author: colin
	 * @date: 2019/5/27 19:20
	 * @return array
	 */
	public function hotSearch()
	{
		$data = ['蛋糕', '面包', '榴莲', '千层', '芒果'];
		return $data;
	}
}