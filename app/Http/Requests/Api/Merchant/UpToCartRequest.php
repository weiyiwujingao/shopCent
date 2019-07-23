<?php

namespace App\Http\Requests\Api\Merchant;

use Illuminate\Foundation\Http\FormRequest;

class UpToCartRequest extends FormRequest
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
			'recId' => 'required|integer',
			'number' => 'required|integer',
			'goodsId' => 'required|integer',

        ];
    }
	public function messages()
	{
		return [
			'goodsId.required' => '商品不能为空！',
			'goodsId.integer' => '商品类型有误！',
			'number.required' => '商品数量不能为空！',
			'number.integer' => '商品类型有误！',
			'recId.required' => '购物车id不能为空！',
			'recId.integer' => '购物车id类型有误！',

		];
	}
}
