<?php

namespace Library\ProductCenter;

use App;
use Illuminate\Http\Request;
use \Exception;
use Enum\EnumKeys;
use Illuminate\Support\Facades\Cache;
use \Helper\CFunctionHelper as help;

class Home extends \Library\CBase
{
	protected $request;
	protected $userCMd;

	public function __construct(Request $request)
	{
		$this->request = $request;
		$this->HomeMd = new App\Repositories\HomeRepository();
		parent::__construct(__CLASS__);
	}

	/**
	 * 首页基础数据接口
	 * @author: colin
	 * @date: 2019/1/15 16:54
	 * @return \Library\type
	 */
	public function index()
	{
		try {
			$data = [];
			$cityId = $this->request->input('cityId');
			$city = $this->HomeMd->getById($cityId, 'gr_name');
			$data['city_name'] = $city;
			$data['slider'] = $this->getSlider($city);//轮播图
			$data['newGoodOne'] = $this->HomeMd->getNewGoods($city, 6, 2);
			$data['newGoodTwo'] = $this->HomeMd->getNewGoods($city, 2, 2);
			$data['is_best'] = $this->HomeMd->getNewGoods($city, 10, 5);//每周精选
			$data['activity'] = $this->HomeMd->getNewGoods($city, 9, 1);//活动
			$data['nav'] = $this->HomeMd->getNav();//栏目列表
		} catch (\Exception $e) {
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => "Home index:" . json_encode($city) . ",reason:" . $e->getMessage(),
				'userMsg' => $e->getMessage(),
				'line' => __LINE__,
			]);
		}
		return $data;
	}

	/***
	 * 获取广告列表
	 * @author: colin
	 * @date: 2019/1/15 16:39
	 * @param $city
	 * @return \Library\type
	 */
	public function getSlider($city)
	{
		try {
			if (empty($city)) {
				throw new \Exception('城市获取错误！');
			}
			$sliderCacheKey = md5(EnumKeys::CACHE_SLIDER_LIST . '_' . $city);
			$slider = Cache::get($sliderCacheKey);
			$slider = [];
			if ($slider) {
				return $slider;
			}
			$slider = $this->HomeMd->getSlider($city);
			Cache::put($sliderCacheKey, $slider, 5);
		} catch (\Exception $e) {
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => "home getSlider:" . json_encode($city) . ",reason:" . $e->getMessage(),
				'userMsg' => $e->getMessage(),
				'line' => __LINE__,
			]);
		}
		return $slider;
	}

	/**
	 * 门店列表
	 * @author: colin
	 * @date: 2019/1/16 10:06
	 * @return \Library\type
	 */
	public function sellers()
	{
		try {
			$result = $this->getSellers();
			if($result === false){
				return false;
			}
		} catch (\Exception $e) {
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
	 * 获取地区品牌所有门店
	 * [getBrandSellers ]
	 * @param  [type] $cityid   城市id
	 * @param  [type] $brandid  品牌id
	 * @param  [type] $lat      维度
	 * @param  [type] $lng      经度
	 * @param  [type] $type     数据类型
	 * @param  [type] $sellerid 卖家id
	 * @param int $catId 产品分类id
	 * @return [type]           [description]
	 */
	public function getSellers()
	{
		try {
			$param = $this->request->all();
			$where = $this->SearchArr();
			$data = $this->HomeMd->getSellers($param,$where);
		} catch (Exception $e) {
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => "Home getSellers:" . json_encode($param) . ",reason:" . $e->getMessage(),
				'userMsg' => $e->getMessage(),
				'line' => __LINE__,
			]);
		}
		return $data;
	}

	/**
	 * 搜索条件
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
					case 'cityId':
						$goodRegion = new \App\Repositories\GoodReginRepository();
						$cityInfo = $goodRegion->ById($value);
						if ($cityInfo === false) {
							break;
						}
						switch($cityInfo['type']){
							case 3:
								$query->whereRaw("gs.city_id='{$cityInfo['parent_id']}' or find_in_set({$cityInfo['parent_id']},a.show_citys)");
								break;
							case 4:
								$query->whereRaw("gs.city_id='{$cityInfo['parent_id']}' or find_in_set({$value},a.show_citys)");
								break;
							default:
								$cityArr = $goodRegion->getTownIds($value);
								$query->whereIn('gs.gs_region_id', $cityArr);
								break;
						}
						break;
					case 'brandid':
						$value = explode(',',$value);
						$query->whereIn('gs.gs_brand_id', $value);
						break;
					default:
						break;

				}
			}
		};
		return $where;
	}

	/**
	 * 根据地区名称获取地区id
	 * @author: colin
	 * @date: 2019/1/18 11:13
	 * @return bool|\Library\type
	 */
	public function position()
	{
		try {
			$city = $this->request->input('city','东莞市');
			$result = $this->HomeMd->position($city);
			if($result === false){
				return false;
			}
		} catch (\Exception $e) {
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => "Home position:" . json_encode(1) . ",reason:" . $e->getMessage(),
				'userMsg' => $e->getMessage(),
				'line' => __LINE__,
			]);
		}
		return $result;

	}

	/**
	 * 地区列表
	 * @author: colin
	 * @date: 2019/1/18 13:56
	 * @return bool|\Library\type|mixed
	 */
	public function citys()
	{
		try {
			$cityId = $this->request->input('cityId',0);
			$result = $this->HomeMd->citys($cityId);
			if($result === false){
				return false;
			}
		} catch (\Exception $e) {
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => "Home citys:" . json_encode(1) . ",reason:" . $e->getMessage(),
				'userMsg' => $e->getMessage(),
				'line' => __LINE__,
			]);
		}
		return $result;
	}
	/**
	 * 获取所有地区列表
	 * @author: colin
	 * @date: 2019/1/18 13:56
	 * @return bool|\Library\type|mixed
	 */
	public function allCity()
	{
		try {
			$cityId = $this->request->input('cityId');
			$result = $this->HomeMd->allCity($cityId);
			if($result === false){
				return false;
			}
		} catch (\Exception $e) {
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => "Home citys:" . json_encode(1) . ",reason:" . $e->getMessage(),
				'userMsg' => $e->getMessage(),
				'line' => __LINE__,
			]);
		}
		return $result;
	}

}