<?php

namespace App\Http\Requests\Api\Merchant;
use Illuminate\Http\Request;
use Illuminate\Foundation\Http\FormRequest;

class CheckOutRequest extends FormRequest
{
	public function __construct(Request $request)
	{
		$this->errorType = 1;
		$this->request = $request;
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
		$pycode = FormRequest::input('pycode','');
		if(empty($pycode)){
			return [
			'user_login_name' => 'required',
			'order_lxr' => 'required',
			'code' => 'required|integer',
			];
		}else{
			return [
				'pycode' => 'required|size:16',
			];
		}

	}

	public function messages()
	{
		return [
			'user_login_name.required' => '收货用户名不能为空！',
			'order_lxr.required' => '联系人不能为空！',
			'pycode.required' => '收款码不能为空！',
			'pycode.size' => '收款码不合法！',
			'code.required' => '验证码不能为空！',
			'code.integer' => '验证码不合法！',
		];
	}

}
