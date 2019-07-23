<?php

namespace App\Http\Requests\Api\UserCenter;

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
            'user_name' => 'required|exists:ecs_users,user_name',
            'pwd' => 'required|min:6|max:32|',
        ];
    }

    public function messages()
    {
        return [
            'user_name.required' => '登录名称不能为空！',
            'user_name.exists'   => '用户名称或密码错误！',
            'user_name.required' => '登录密码不能为空！',
            'pwd.min'      => '登录密码不能小于6个字符',
            'pwd.max'      => '登录密码不能大于32个字符',
        ];
    }

}
