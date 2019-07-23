<?php

namespace App\Http\Requests\Api\UserCenter;

use Illuminate\Foundation\Http\FormRequest;

class AddressUpdateRequest extends FormRequest
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
			'address_id' => 'required|integer|exists:ecs_user_address,address_id',
			'consignee' => 'required|between:1,32',
			'sex' => 'required|integer|between:1,2',
			'province' => 'required|integer|exists:ecs_region,region_id',
			'city' => 'required|integer',
			'district' => 'required|integer',
			'address' => 'required|between:1,60',
			'mobile' => 'required|digits:11',
			'is_default' => 'required|integer|between:0,1',
		];
	}
	public function messages()
	{
		return [
			'address_id.required'  => '地址参数不能为空！',
			'address_id.integer'  => '地址参数类型有误！',
			'address_id.exists'  => '地址参数有误！',
			'consignee.required' => '收货人名称不为空！',
			'consignee.between'  => '收货人名称长度必须为1-32个字符之间！',
			'sex.required' => '性别不能为空！',
			'sex.integer' => '性别类型有误！',
			'sex.between' => '性别参数有误！',
			'province.required'  => '省份不能为空！',
			'province.integer'  => '省份类型有误！',
			'province.exists'  => '省份参数有误！',
			'city.required'  => '城市不能为空！',
			'city.integer'  => '城市类型有误！',
			'city.exists'  => '城市参数有误！',
			'district.required'  => '地区不能为空！',
			'district.integer'  => '地区类型有误！',
			'district.exists'  => '地区参数有误！',
			'address.required'      => '详细地址不能为空',
			'address.between'      => '密码必须为1-60个字符之间！',
			'mobile.required' => '手机号码不为空！',
			'mobile.digits' => '手机号码不合法！',
			'is_default.required' => '默认类型不能为空！',
			'is_default.integer' => '默认类型有误！',
			'is_default.between' => '默认参数有误！',
		];
	}
}
