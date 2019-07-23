<?php

namespace App\Http\Requests\Api\ProductCenter;

use Illuminate\Foundation\Http\FormRequest;

class UpOrderRequest extends FormRequest
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
			'shopCart' => 'required',
			'message' => 'required',
		];
	}
	public function messages()
	{
		return [
			'shopCart.required'  => '订单为空，提交失败！',
			'message.required'  => '订单为空，提交失败！',
		];
	}
}
