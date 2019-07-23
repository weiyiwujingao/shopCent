<?php

namespace App\Http\Requests\Api\Merchant;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function __construct()
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
            'gs_login_name' => 'required|exists:ecs_goods_stores,gs_login_name',
            'gs_login_pass' => 'required|min:6|max:32|',
        ];
    }

    public function messages()
    {
        return [
            'gs_login_name.required' => '登录名称不能为空！',
            'gs_login_name.exists'   => '商户代码或密码错误',
            'gs_login_pass.required' => '登录密码不能为空！',
            'gs_login_pass.min'      => '登录密码不能小于6个字符',
            'gs_login_pass.max'      => '登录密码不能大于32个字符',
        ];
    }

}
