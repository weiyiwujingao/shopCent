<?php

namespace App\Http\Requests\Api\UserCenter;

use Illuminate\Foundation\Http\FormRequest;

class AccountBillRequest extends FormRequest
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
			'year' => 'required|integer|between:2016,2050',
			'month' => 'required|integer|between:1,12',
		];
	}
	public function messages()
	{
		return [
			'year.required'  => '年份不能为空！',
			'year.integer'  => '年份类型有误！',
			'year.between'  => '年份参数非法！',
			'month.required'  => '月份不能为空！',
			'month.integer'  => '月份类型有误！',
			'month.between'  => '月份参数非法！',
		];
	}
}
