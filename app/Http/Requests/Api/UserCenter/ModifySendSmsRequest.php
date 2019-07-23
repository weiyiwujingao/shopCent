<?php

namespace App\Http\Requests\Api\UserCenter;

use Illuminate\Foundation\Http\FormRequest;

class ModifySendSmsRequest extends FormRequest
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
			'mobile' => 'required|digits:11',
 		];
	}

	public function messages()
	{
		return [
			'mobile.required' => '手机号码不为空！',
			'mobile.digits' => '手机号码不合法！',
		];
	}

}
