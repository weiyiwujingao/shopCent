<?php

namespace App\Http\Requests\Api\UserCenter;

use Illuminate\Foundation\Http\FormRequest;

class ModPswRequest extends FormRequest
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
			'password' => 'required|between:6,32',
			'new_password' => 'required|between:6,32|confirmed',
		];
	}
	public function messages()
	{
		return [
			'password.required'  => '密码不能为空！',
			'password.between'      => '密码必须为6-32个字符之间！',
			'new_password.required'      => '新密码不能为空！',
			'new_password.between'      => '新密码必须为6-32个字符之间！',
			'new_password.confirmed'  => '新密码和确认密码不匹配！',
		];
	}
}
