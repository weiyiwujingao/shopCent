<?php

namespace App\Http\Requests\Api\ProductCenter;

use Illuminate\Foundation\Http\FormRequest;

class GoodSellersRequest extends FormRequest
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
			'brandId' => 'required',
			'goodsId' => 'required|exists:ecs_goods,goods_id',
			'cityId' => 'required',
		];
	}
	public function messages()
	{
		return [
			'goodsId.required'  => '商品id不能为空！',
			'goodsId.exists'  => '商品不存在！',
			'brandId.required'  => '参数错误！',
			'cityId.required'  => '参数错误！',
		];
	}
}
