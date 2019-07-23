<?php

namespace App\Http\Requests\Api\Wechat;

use Illuminate\Foundation\Http\FormRequest;

class WxqrcodeRequest extends FormRequest
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
			'path' => 'required',

		];
	}

	public function messages()
	{
		return [
			'path.required' => '参数有误！',
		];
	}
}
