<?php

namespace App\Http\Requests\Api\Merchant;

use Illuminate\Foundation\Http\FormRequest;

class SetExpress extends FormRequest
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
            'orderSn' => 'required',
            'exId'    => 'required|integer',
            'exNum'   => ['required_unless:exId,-1'],
            'exMess'  => ['required_if:exId,-1'],
        ];
    }

    public function messages()
    {
        return [
            'orderSn.required' => '订单号不能为空！',
            'exId.required'    => '快递id不能为空！',
            'exId.integer'     => '快递id不合法！',
            'exNum.required'   => '快递单号不能为空！',
            'exMess.required'  => '请填写快递信息！',
        ];
    }

}
