<?php

namespace App\Http\Requests\Api\UserCenter;

use Illuminate\Foundation\Http\FormRequest;

class CardRelayRequest extends FormRequest
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
			'cardId' => 'required|exists:ecs_user_bonus,bonus_id',
		];
	}
	public function messages()
	{
		return [
			'cardId.required'  => '幸福券不能为空！',
			'cardId.exists'  => '幸福券不存在！',
		];
	}
}
