<?php

namespace App\Http\Requests\Api\UserCenter;

use Illuminate\Foundation\Http\FormRequest;

class ShowPayCodeRequest extends FormRequest
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
			'type' => 'required|integer|between:1,3',
		];
	}
	public function messages()
	{
		return [
			'type.required'  => '图片类型不能为空！',
			'type.integer'  => '图片类型有误！',
			'type.between'  => '图片类型参数非法！',
		];
	}
}
