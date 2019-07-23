<?php

namespace App\Http\Requests\Api\UserCenter;

use Illuminate\Foundation\Http\FormRequest;

class GetWxtelRequest extends FormRequest
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
			'code' => 'required',
			'iv' => 'required',
			'encryptedData' => 'required',
		];
	}
	public function messages()
	{
		return [
			'code.required'  => '缺少参数！',
			'iv.required'  => '缺少参数！',
			'encryptedData.required'  => '缺少参数！',
		];
	}
}
