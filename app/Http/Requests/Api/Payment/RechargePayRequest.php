<?php

namespace App\Http\Requests\Api\Payment;

use Illuminate\Foundation\Http\FormRequest;

class RechargePayRequest extends FormRequest
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
		$type = \Request::input('type');//类型：充值(recharge)、购物(shopping)
		if($type=='recharge'){
			return [
				'order_id' => 'required|integer|exists:ecs_bonus_order,bo_id',
				'pay_id' => 'required|integer|exists:ecs_payment,pay_id',
			];
		}else{
			return [
				'order_sn' => 'required|exists:ecs_order_info,order_sn',
				'pay_id' => 'required|integer|exists:ecs_payment,pay_id',
			];
		}

	}

	public function messages()
	{
		return [
			'order_id.required' => '参数有误！',
			'order_id.integer' => '参数有误！',
			'order_id.exists' => '参数有误！',
			'pay_id.required' => '参数有误！',
			'pay_id.integer' => '参数有误！',
			'pay_id.exists' => '参数有误！',
			'order_sn.required' => '参数有误！',
			'order_sn.exists' => '参数有误！',
		];
	}
}
