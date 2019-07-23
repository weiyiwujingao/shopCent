<?php

namespace App\Repositories;

use App\Models\GoodsRegion;
use Illuminate\Support\Facades\Cache;
use Enum\EnumKeys;

class GoodReginRepository
{
	/**
	 * 创建地区
	 * @param array $params
	 * @return mixed
	 */
	public function create(array $params)
	{
		return GoodsRegion::create($params);
	}

	/**
	 * 根据gr_id修改地区信息
	 * @param $name
	 * @return mixed
	 */
	public function updateByid($id, $data)
	{
		try {
			GoodsRegion::where('gr_id', $id)->update($data);
		} catch (\Exception $e) {
			return false;
		}
		return true;
	}

	/**
	 * 根据gr_id查询
	 * @param $name
	 * @return mixed
	 */
	public function getById($id, $column)
	{
		try {
			$result = GoodsRegion::where('gr_id', $id)->value($column);
		} catch (\Exception $e) {
			return false;
		}
		return $result;
	}

	/**
	 * 根据gr_id查询
	 * @param $name
	 * @return mixed
	 */
	public function ById($id)
	{
		try {
			$result = GoodsRegion::where('gr_id', $id)->firstOrFail();
			$result = $result->toArray();
		} catch (\Exception $e) {
			return false;
		}
		return $result;
	}

	/**
	 * 根据gr_id查询下一级的地区id集合
	 * @param $name
	 * @return mixed
	 */
	public function getTownIds($id)
	{
		try {
			if (empty($id)) {
				throw new \Exception('id空！');
			}
			$cacheKey = md5(EnumKeys::CACHE_REGION_SECONDE_LIST . '_' . $id);
			$result = Cache::get($cacheKey);
			if ($result) {
				return $result;
			}
			$result = GoodsRegion::where('parent_id', $id)->pluck('gr_id');
			$result = $result->toArray();
			Cache::put($cacheKey, $result, 60 * 24);
		} catch (\Exception $e) {
			return false;
		}
		return $result;
	}
}