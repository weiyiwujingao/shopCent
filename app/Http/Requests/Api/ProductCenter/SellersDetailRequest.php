<?php

namespace App\Http\Requests\Api\ProductCenter;

use Illuminate\Foundation\Http\FormRequest;

class SellersDetailRequest extends FormRequest
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
			'sellerId' => 'required|integer|exists:ecs_goods_stores,gs_id',
		];
	}
	public function messages()
	{
		return [
			'sellerId.required'  => '缺少参数sellerId！',
			'sellerId.integer'  => 'sellerId参数有误！',
			'sellerId.exists'  => '该店铺不存在！',
		];
	}
}
