<?php

namespace App\Http\Requests\Api\UserCenter;

use Illuminate\Foundation\Http\FormRequest;

class CollectAddRequest extends FormRequest
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
			'store_id' => 'required|integer',
		];
	}
	public function messages()
	{
		return [
			'store_id.required'  => '店铺参数不能为空！',
			'store_id.integer'  => '店铺参数类型有误！',
		];
	}
}
