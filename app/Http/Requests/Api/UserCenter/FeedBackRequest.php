<?php

namespace App\Http\Requests\Api\UserCenter;

use Illuminate\Foundation\Http\FormRequest;

class FeedBackRequest extends FormRequest
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
			'content' => 'required|between:5,500',
		];
	}
	public function messages()
	{
		return [
			'content.required'  => '内容不能为空！',
			'tycontentpe.between'  => '内容介于5-500个字符！',
		];
	}
}
