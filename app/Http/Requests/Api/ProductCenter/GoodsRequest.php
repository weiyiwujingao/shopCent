<?php

namespace App\Http\Requests\Api\ProductCenter;

use Illuminate\Foundation\Http\FormRequest;

class GoodsRequest extends FormRequest
{
	public function __construct( )
	{
		$this->errorType = 1;
	}
	/**
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		return true;
	}

	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			'cityId' => 'required|integer|exists:ecs_goods_region,gr_id',
			'page' => 'required|integer|between:1,1000',
			'pageSize' => 'required|integer|between:1,100',
		];
	}
	public function messages()
	{
		return [
			'cityId.required'  => '缺少参数cityid！',
			'cityId.integer'  => 'cityid参数有误！',
			'cityId.exists'  => '暂未开通该地区！',
			'page.required'  => '当前页不能为空！',
			'page.integer'  => '当前页类型有误！',
			'page.between'  => '当前页参数非法！',
			'pageSize.required'  => '单页数量不能为空！',
			'pageSize.integer'  => '单页数量类型有误！',
			'pageSize.between'  => '单页数量参数非法！',
		];
	}
}