<?php

namespace App\Http\Requests\Api\Merchant;

use Illuminate\Foundation\Http\FormRequest;

class SetSortRequest extends FormRequest
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
			'goods_id'    => 'required|integer',
			'sort' => 'required|integer',
		];
	}

	public function messages()
	{
		return [
			'goods_id.required' => '商品id不能为空！',
			'goods_id.integer' => '商品id不合法！',
			'sort.integer' => '商品排序权重不能为空！',
			'sort.required' => '商品排序权重不合法！',
		];
	}

}
