<?php

namespace App\Http\Requests\Api\Merchant;

use Illuminate\Foundation\Http\FormRequest;

class ShopSetRequest extends FormRequest
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
			'gs_stats' => 'required|regex:/^[0,1]{1}$/',
			'pickup_mode' => 'required|regex:/^[1,2,3]{1}$/',
			'gs_notice' => 'max:500',
        ];
    }

	public function messages()
	{
		return [
			'gs_stats.required' => '店铺经营状态不能为空',
			'gs_stats.regex' => '店铺经营状态参数设置有误',
			'pickup_mode.required' => '取货方式不能为空',
			'pickup_mode.regex' => '取货方式参数设置有误',
			'gs_notice.max' => '店铺公告不能操作500个字符',
		];
	}
}
