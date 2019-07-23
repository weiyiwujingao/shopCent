<?php

namespace App\Http\Requests\Api\Merchant;

use Illuminate\Foundation\Http\FormRequest;

class GoodRequest extends FormRequest
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
			'id' => 'required|integer',
 		];
	}

	public function messages()
	{
		return [
			'id.required' => '商品id不能为空！',
			'id.integer' => '商品id不合法！',
		];
	}

}
