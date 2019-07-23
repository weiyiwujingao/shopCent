<?php

namespace App\Http\Requests\Api\ProductCenter;

use Illuminate\Foundation\Http\FormRequest;

class RechargeUpOrderRequest extends FormRequest
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
			'type_id' => 'required|integer|exists:ecs_bonus_type,type_id',
			'payment' => 'required|integer',
		];
	}
	public function messages()
	{
		return [
			'type_id.required'  => '充值id不能为空，提交失败！',
			'type_id.exists'  => '参数错误！',
			'type_id.integer'  => '参数错误！',
			'payment.required'  => '参数错误！',
			'payment.integer'  => '参数错误！',
		];
	}
}
