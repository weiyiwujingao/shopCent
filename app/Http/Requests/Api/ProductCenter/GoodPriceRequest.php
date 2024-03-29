<?php

namespace App\Http\Requests\Api\ProductCenter;

use Illuminate\Foundation\Http\FormRequest;

class GoodPriceRequest extends FormRequest
{
	public function __construct()
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
			'goodsId' => 'required|integer',
			'number' => 'required|integer',
			'spec' => 'required',
 		];
	}

	public function messages()
	{
		return [
			'goodsId.required' => '商品不能为空！',
			'goodsId.integer' => '商品类型有误！',
			'number.required' => '商品数量不能为空！',
			'number.integer' => '商品数量有误！',
			'spec.required' => '商品属性不能为空！',
		];
	}

}
