<?php

namespace App\Http\Requests\Api\UserCenter;

use Illuminate\Foundation\Http\FormRequest;

class CustomerdetailRequest extends FormRequest
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
			'id' => 'required|integer|exists:ecs_article,article_id',
		];
	}
	public function messages()
	{
		return [
			'id.required'  => 'id不能为空！',
			'id.integer'  => 'id类型有误！',
			'id.exists'  => '不存在内容！',
		];
	}
}
