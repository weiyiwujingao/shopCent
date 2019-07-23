<?php

namespace App\Http\Requests\Api\UserCenter;

use Illuminate\Foundation\Http\FormRequest;

class ModPswByMobileRequest extends FormRequest
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
			'code' => 'required|between:6,32',
			'mobile' => 'required|digits:11|exists:ecs_users,user_name',
			'new_password' => 'required|between:6,32',
		];
	}
	public function messages()
	{
		return [
			'code.required'   => '验证码不能为空！',
			'code.between'   => '验证码格式有误！',
			'mobile.required'   => '手机号码不能为空！',
			'mobile.exists'   => '验证码错误！',
			'mobile.digits'   => '手机号码不正确！',
			'new_password.required'  => '密码不能为空！',
			'new_password.between'   => '密码必须为6-32个字符之间！',
		];
	}
}
