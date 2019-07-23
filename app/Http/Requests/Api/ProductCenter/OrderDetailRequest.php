<?php

namespace App\Http\Requests\Api\ProductCenter;

use Illuminate\Foundation\Http\FormRequest;

class OrderDetailRequest extends FormRequest
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
			'order_sn' => 'required|exists:ecs_order_info,order_sn',
		];
	}
	public function messages()
	{
		return [
			'order_sn.required'  => '订单号不能为空！',
			'order_sn.exists'  => '订单不存在！',
		];
	}
}
