<?php

namespace App\Http\Requests\Api\UserCenter;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
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
			'mobile' => 'required|digits:11|unique:ecs_users,user_name',
			'code' => 'required|digits:6',
			'password' => 'required|between:6,32|confirmed',
//			'password_confirm' => 'required|between:6,32|confirmed',
		];
	}
	public function messages()
	{
		return [
			'mobile.required' => '手机号码不为空！',
			'mobile.digits' => '手机号码不合法！',
			'mobile.unique' => '该手机号码已经注册过了！',
			'code.required' => '验证码不为空！',
			'code.digits' => '验证码不合法！',
			'password.required'  => '密码不能为空！',
			'password.between'      => '密码必须为6-32个字符之间！',
//			'new_password.required'      => '新密码不能为空！',
//			'new_password.between'      => '新密码必须为6-32个字符之间！',
//			'new_password.confirmed'  => '新密码和确认密码不匹配！',
		];
	}
}
