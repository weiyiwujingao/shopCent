<?php

namespace Library\ProductCenter;

use App;
use Illuminate\Http\Request;
use \Exception;
use Enum\EnumKeys;
use Illuminate\Support\Facades\Cache;
use \Helper\CFunctionHelper as help;

class StoreMenu extends \Library\CBase
{
	protected $request;
	protected $userCMd;

	public function __construct(Request $request)
	{
		$this->request = $request;
		$this->StoreMd = new App\Repositories\StoreMenuRepository();
		parent::__construct(__CLASS__);
	}
	/**
	 * 门店列表
	 * @author: colin
	 * @date: 2019/1/16 10:06
	 * @return \Library\type
	 */
	public function brands()
	{
		try {
			$param = $this->request->all();
			$where = $this->SearchArr();
			$result = $this->StoreMd->brands($param,$where);
			if($result === false){
				return [];
			}
		} catch (\Exception $e) {
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => "StoreMenu index:" . json_encode(1) . ",reason:" . $e->getMessage(),
				'userMsg' => $e->getMessage(),
				'line' => __LINE__,
			]);
		}
		return $result;

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
					case 'support_shipping':
						$query->where('support_shipping', $value);
						break;
					default:
						break;

				}
			}
		};
		return $where;
	}
	/**
	 * 门店列表
	 * @author: colin
	 * @date: 2019/1/16 10:06
	 * @return \Library\type
	 */
	public function brandSeller()
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
				'errorMsg' => "StoreMenu brandSeller:" . json_encode(1) . ",reason:" . $e->getMessage(),
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
			$where = $this->SearchBrand();
			$data = $this->StoreMd->getSellers($param,$where);
		} catch (Exception $e) {
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => "StoreMenu getSellers:" . json_encode($param) . ",reason:" . $e->getMessage(),
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
	public function SearchBrand()
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
								$query->whereRaw("(gs.gs_region_id in ('{$value}','{$cityInfo['parent_id']}') or find_in_set('{$cityInfo['parent_id']}',gs.show_citys))");
								break;
							case 4:
								$query->where('gs.gs_region_sq', $value);
								break;
							default:
								$query->whereRaw("(gs.city_id = '{$value}' or find_in_set('{$value}',gs.show_citys))");
								break;
						}
						break;
					case 'brandId':
						$brandIdArr = explode(',',$value);
						if(count($brandIdArr)>1)
							$query->whereIn('gs.gs_brand_id', $brandIdArr);
						else
							$query->where('gs.gs_brand_id', $value);
						break;
					default:
						break;

				}
			}
		};
		//$where($param);die;
		return $where;
	}

	/**
	 * 获取品牌详情
	 * @author: colin
	 * @date: 2019/5/20 13:38
	 * @return bool|\Library\type
	 */
	public function brandInfo()
	{
		try {
			$brandId = $this->request->input('brandId');
			$result = $this->StoreMd->brandInfo($brandId);
			if($result === false){
				return false;
			}
		} catch (\Exception $e) {
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => "StoreMenu catSellers:" . json_encode(1) . ",reason:" . $e->getMessage(),
				'userMsg' => $e->getMessage(),
				'line' => __LINE__,
			]);
		}
		return $result;

	}
	/**
	 * 根据品种分类获取店铺列表
	 * @author: colin
	 * @date: 2019/1/22 14:32
	 * @return array|bool|\Library\type
	 */
	public function catSellers()
	{
		try {
			$param = $this->request->all();
			$where = $this->SearchCatArr();
			$result = $this->StoreMd->catSellers($param,$where);
			if($result === false){
				return false;
			}
		} catch (\Exception $e) {
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => "StoreMenu catSellers:" . json_encode(1) . ",reason:" . $e->getMessage(),
				'userMsg' => $e->getMessage(),
				'line' => __LINE__,
			]);
		}
		return $result;
	}
	/**
	 * 搜索条件
	 * @author: colin
	 * @date: 2019/1/16 11:10
	 * @return \Closure
	 */
	public function SearchCatArr()
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
								$query->whereIn('gs.gs_region_id', [$value,$cityInfo['parent_id']]);
								break;
							case 4:
								$query->where('gs.gs_region_sq', $value);
								break;
							default:
								$cityArr = $goodRegion->getTownIds($value);
								$query->whereIn('gs.gs_region_id', array_merge([$value],$cityArr));
								break;
						}
						break;
					case 'filter_store_ids':
						$value = explode(',',$value);
						$query->whereNotIn('gs.gs_id', $value);
						break;
					default:
						break;

				}
			}
		};
		return $where;
	}

	/**
	 * 店铺详情
	 * @author: colin
	 * @date: 2019/1/23 8:58
	 * @return bool|\Library\type
	 */
	public function sellerDetail()
	{
		try {
			$param = $this->request->all();
			$result = $this->StoreMd->sellerDetail($param);
			if($result === false){
				return false;
			}
		} catch (\Exception $e) {
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => "StoreMenu sellerDetail:" . json_encode(1) . ",reason:" . $e->getMessage(),
				'userMsg' => $e->getMessage(),
				'line' => __LINE__,
			]);
		}
		return $result;
	}

}