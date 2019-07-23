<?php

namespace App\Http\Requests\Api\UserCenter;

use Illuminate\Foundation\Http\FormRequest;

class OrderListRequest extends FormRequest
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
			'type' => 'required|integer|between:1,4',
			'page' => 'required|integer|between:1,1000',
			'pageSize' => 'required|integer|between:1,100',
		];
	}
	public function messages()
	{
		return [
			'type.required'  => '订单状态不能为空！',
			'type.integer'  => '订单状态类型有误！',
			'type.between'  => '订单状态参数非法！',
			'page.required'  => '当前页不能为空！',
			'page.integer'  => '当前页类型有误！',
			'page.between'  => '当前页参数非法！',
			'pageSize.required'  => '单页数量不能为空！',
			'pageSize.integer'  => '单页数量类型有误！',
			'pageSize.between'  => '单页数量参数非法！',
		];
	}
}
