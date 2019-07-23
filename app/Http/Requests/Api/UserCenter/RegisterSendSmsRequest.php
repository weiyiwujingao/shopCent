<?php

namespace App\Http\Requests\Api\UserCenter;

use Illuminate\Foundation\Http\FormRequest;

class RegisterSendSmsRequest extends FormRequest
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
			'mobile' => 'required|digits:11|unique:ecs_users,user_name',
 		];
	}

	public function messages()
	{
		return [
			'mobile.required' => '手机号码不为空！',
			'mobile.digits' => '手机号码不合法！',
			'mobile.unique' => '该手机号码已经注册过了！',
		];
	}

}
