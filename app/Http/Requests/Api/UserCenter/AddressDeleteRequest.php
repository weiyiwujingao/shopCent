<?php

namespace App\Http\Requests\Api\UserCenter;

use Illuminate\Foundation\Http\FormRequest;

class AddressDeleteRequest extends FormRequest
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
			'address_id' => 'required|integer|exists:ecs_user_address,address_id',
		];
	}
	public function messages()
	{
		return [
			'address_id.required'  => '地址参数不能为空！',
			'address_id.integer'  => '地址参数类型有误！',
			'address_id.exists'  => '已删除！',
		];
	}
}
