<?php

namespace App\Http\Requests\Api\UserCenter;

use Illuminate\Foundation\Http\FormRequest;

class CardActivateRequest extends FormRequest
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
			'bonusSn' => 'required|exists:ecs_user_bonus,bonus_id',
			'cardpwd' => 'required',
		];
	}
	public function messages()
	{
		return [
			'bonusSn.required'  => '幸福券账号不能为空！',
			'bonusSn.exists'  => '幸福券账号不存在！',
			'cardpwd.cardpwd'  => '幸福券密码不能为空！',
		];
	}
}
