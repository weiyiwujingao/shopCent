<?php

namespace App\Http\Requests\Api\Merchant;

use Illuminate\Foundation\Http\FormRequest;

class ExpressDetail extends FormRequest
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
			'orderSn' => 'required',
		];
	}

	public function messages()
	{
		return [
			'orderSn.required' => '订单号不能为空！',
		];
	}

}
