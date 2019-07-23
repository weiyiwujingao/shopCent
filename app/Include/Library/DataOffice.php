<?php

namespace Library;

use DB;
use App;
use Cookie;
use Enum\EnumKeys;
use Validator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use \Exception;
use Helper\CFunctionHelper as help;

class DataOffice extends CBase
{
	protected $request;
	protected $gsId;

	public function __construct(Request $request)
	{
		$this->request = $request;
		parent::__construct(__CLASS__);
	}
	/***
	 * 导出订单数据
	 * @author: colin
	 * @date: 2018/12/12 9:16
	 * @return bool|type
	 */
	public function excelGoods()
	{
		$result = [];
		try {
			$list = $this->settlement();
			if (empty($list)) {
				return false;
			}
			$titles = [
				'goods_name' => '商品名称',
				'shop_price' => '价格',
				'brand_name' => '品牌名称',
				'gr_name' => '地区',
				'gs_name' => '门店名称',
			];
			$data = [];
			foreach ($list as $key => $val) {
				foreach ($val['gs'] as $gs=>$gsval){
					foreach ($titles as $k => $v) {
						if(in_array($k,['gs_name','gr_name','brand_name'])){
							$data[$k][] = $gsval[$k];
						}else{
							$data[$k][] = $val['good'][$k];
						}
					}
				}

			}
			$result = [
				'titles' => $titles,
				'data' => $data,
			];
			unset($list);
		} catch (\Exception $e) {
			echo $e->getMessage();die;
			return false;
		}
		return $result;
	}
	public function settlement()
	{
		$doOut = '';
		$offset = 0;
		$goodlist = [];
		$i=1;
		do{
			$good = \App\Models\StoresUser::whereRaw("gs_region_id in(SELECT gr_id from ecs_goods_region WHERE gr_id=1 or parent_id=1)")
				->select('gs_id','gs_name','gs_region_id','gs_brand_id','gs_goods_id')
			   ->skip($offset)->take(100)->orderBy('gs_id','desc')->get();
			if($good->isEmpty()){
				$doOut = 1;
			}
			$good = $good->toArray();
			foreach($good as $k=>$v){
				if(empty($v['gs_goods_id']))
					continue;
				$goodIds = unserialize($v['gs_goods_id']);
				$goodsall = \App\Models\Goods::whereIn('goods_id',$goodIds)->where('shop_price','>',0)->where('shop_price','<=',20)->where('is_on_sale',1)->select('goods_name','shop_price','goods_id')->get()->toArray();
				foreach ($goodsall as $g) {
					if(isset($goodlist[$g['goods_id']]['gs'][$v['gs_id']]))
						continue;
					$goodlist[$g['goods_id']]['gs'][$v['gs_id']] = [
						'gs_name' => $v['gs_name'],
						'gr_name' => \App\Models\GoodsRegion::where('gr_id',$v['gs_region_id'])->value('gr_name'),
						'brand_name' => \App\Models\Brand::where('brand_id',$v['gs_brand_id'])->value('brand_name'),
					];
					if(isset($goodlist[$g['goods_id']]['good']))
						continue;
					$goodlist[$g['goods_id']]['good'] = [
						'goods_id' => $g['goods_id'],
						'goods_name' => $g['goods_name'],
						'shop_price' => $g['shop_price'],
					];
				}
			}
			$offset += 100;
			$i++;
		}while($doOut!=1);
		return $goodlist;
	}


}