<?php

namespace App\Http\Requests\Api\ProductCenter;

use Illuminate\Foundation\Http\FormRequest;

class SellersRequest extends FormRequest
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
//			'cityId' => 'required|integer|exists:ecs_goods_region,gr_id',
			'brandId' => 'required',
		];
	}
	public function messages()
	{
		return [
//			'cityId.required'  => '缺少参数cityid！',
//			'cityId.integer'  => 'cityid参数有误！',
//			'cityId.exists'  => '该地区暂未入驻！',
			'brandId.required'  => '缺少参数brandid！',
		];
	}
}
