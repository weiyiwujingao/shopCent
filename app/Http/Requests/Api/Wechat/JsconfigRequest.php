<?php

namespace App\Http\Requests\Api\Wechat;

use Illuminate\Foundation\Http\FormRequest;

class JsconfigRequest extends FormRequest
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
			'url' => 'required',

		];
	}

	public function messages()
	{
		return [
			'url.required' => 'url不能为空！',
		];
	}
}
