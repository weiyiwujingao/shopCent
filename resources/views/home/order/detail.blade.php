@extends('home.layouts.layout')
@section('css')
    <link type="text/css" rel="stylesheet" href="/min/b=home/css&amp;f=header.css,order_detail.css?16"/>
@endsection
@section('content')
    @include('home.common.header',['title'=>'订单详情'])
    <div id="app">
        <div class="order_detail">
            <article class="weui-article">
                <div class="weui-flex head">
                    <div class="weui-flex__item mername">
                        <i class="iconfont icon-icon_Merchant" style="margin-right: 10px; font-size: 18px;"></i><strong>@{{ orderData.gs_name }}</strong>
                        <i class="iconfont icon-icon_More"></i>
                    </div>
                </div>
                <div class="weui-flex item" v-for="(item,idx) in goodsList">
                    <div class="left"><img :src="item.goods_thumb"/></div>
                    <div class="weui-flex__item">
                        <div class="title">@{{ item.goods_name }}</div>
                        <div class="taste">
                            <span v-for="(attr, index) in item.goods_attr">@{{ attr.attr_name }}: @{{ attr.attr_value }}</span>&nbsp;
                        </div>
                        <div class="number">数量： x@{{ item.goods_number }}</div>
                    </div>
                    <div class="right">
                        <strong>￥@{{ item.goods_price }}</strong>
                        <span class="tk_suc" v-if="item.status == 2 || orderData.order_status == 4">退款成功</span>
                        <span class="tk_ing" v-else-if="item.status == 1">请处理退款申请</span>
                    </div>
                </div>
            </article>
            {{--<div class="foot">总计：￥@{{ orderData.total_fee }}</div>--}}
            <div class="order_item">
                <div class="weui-flex">
                    <div class="title">订单金额</div>
                    <div class="weui-flex__item"><a><strong>￥@{{ orderData.total_fee }}</strong></a></div>
                </div>
                <div class="weui-flex">
                    <div class="title">订单编码</div>
                    <div class="weui-flex__item">@{{ orderData.order_sn }}</div>
                </div>
                <div class="weui-flex">
                    <div class="title">支付方式</div>
                    <div class="weui-flex__item">@{{ orderData.pay_value }}</div>
                </div>
                <div class="weui-flex">
                    <div class="title">订单时间</div>
                    <div class="weui-flex__item">@{{ orderData.order_time }}</div>
                </div>
                <div class="weui-flex">
                    <div class="title">配送方式</div>
                    <div class="weui-flex__item">@{{ shippingTypeText }}</div>
                </div>
                <div class="weui-flex" v-if="orderData.order_note != ''">
                    <div class="title">用户留言</div>
                    <div class="weui-flex__item">@{{ orderData.order_note }}</div>
                </div>
            </div>
        </div>
        <div class="get_mess">
            <template v-if="orderData.address == ''">
                <div class="order_item qhxx">
                    <div class="weui-flex" style="padding-bottom: 5px;">
                        <div class="title" style="font-size: 14px;">取货信息</div>
                        <div class="weui-flex__item" style="right: 10px;" v-if="!expMess && wExpre == false && orderData.order_status == 5">
                            <button class="kbtn" @click="setExpMess">寄送快递</button>
                        </div>
                    </div>
                    <div class="weui-flex">
                        <div class="title">门店地址</div>
                        <div class="weui-flex__item">@{{ orderData.gs_address }}</div>
                    </div>
                    <div class="weui-flex">
                        <div class="title">取货时间</div>
                        <div class="weui-flex__item">@{{ orderData.order_pick_time }}</div>
                    </div>
                    <div class="weui-flex">
                        <div class="title">取货人</div>
                        <div class="weui-flex__item">@{{ orderData.order_lxr }}</div>
                    </div>
                    <div class="weui-flex">
                        <div class="title">手机号</div>
                        <div class="weui-flex__item">
                            <a @click="callPhone(orderData.order_sn,1);"> @{{ orderData.order_tel }}</a>
                            <span style="color: #999;" v-if="showUserBtn == true && showUserName == false" @click="showOrderUser();">联系不上取货人?</span>
                        </div>
                    </div>
                </div>
            </template>
            <template v-if="orderData.address != ''">
                <div class="order_item qhxx">
                    <div class="weui-flex" style="padding-bottom: 5px;">
                        <div class="title" style="font-size: 14px;">收货信息</div>
                        <div class="weui-flex__item" style="right: 10px;" v-if="!expMess && wExpre == false && orderData.order_status == 5">
                            <button class="kbtn" @click="setExpMess">寄送快递</button>
                        </div>
                    </div>
                    <div class="weui-flex">
                        <div class="title">收货地址</div>
                        <div class="weui-flex__item">@{{ orderData.address }}</div>
                    </div>
                    <div class="weui-flex">
                        <div class="title">收货时间</div>
                        <div class="weui-flex__item">@{{ orderData.order_pick_time }}</div>
                    </div>
                    <div class="weui-flex">
                        <div class="title">收货人</div>
                        <div class="weui-flex__item">@{{ orderData.order_lxr }}</div>
                    </div>
                    <div class="weui-flex">
                        <div class="title">手机号</div>
                        <div class="weui-flex__item">
                            <a @click="callPhone(orderData.order_sn,1);"> @{{ orderData.order_tel }}</a>
                            <span style="color: #999;" v-if="showUserBtn == true && showUserName == false" @click="showOrderUser();">联系不上取货人?</span>
                        </div>
                    </div>
                </div>
            </template>
        </div>
        <div class="get_mess" v-show="showUserName == true">
            <div class="weui-cells">
                <div class="weui-cell">
                    <div class="weui-cell__hd" style="font-size: 14px;">
                        联系下单用户
                    </div>
                    <div style="position: absolute; right: 10px;">
                        <button class="kbtn" @click="showOrderUser()">取消</button>
                    </div>
                </div>
            </div>
            <div class="order_item qhxx">
                <div class="weui-flex">
                    <div class="title">下单人手机号</div>
                    <div class="weui-flex__item">
                        <span v-if="orderData.encryption == 1"><a  @click="callPhone(orderData.order_sn,2)">@{{ orderData.user_name }}</a></span>
                        <span v-else><a :href="'tel:'+orderData.user_name" >@{{ orderData.user_name }}</a> <i class="copy" :data-clipboard-text="orderData.user_name"></i></span>
                    </div>
                </div>
            </div>
            {{--<div class="qhbx">
                <p class="link" v-if="orderData.encryption == 1">下单人手机号：<a  @click="callPhone(orderData.order_sn,2)">@{{ orderData.user_name }}</a> --}}{{--<i class="copy" :data-clipboard-text="orderData.user_name"></i>--}}{{--</p>
                <p class="link" v-else>下单人手机号：<a :href="'tel:'+orderData.user_name" >@{{ orderData.user_name }}</a> <i class="copy" :data-clipboard-text="orderData.user_name"></i></p>
            </div>--}}
        </div>
        <div class="get_mess" style="font-size: 14px;" id="expbx" v-if="wExpre">
            <div class="weui-cells">
                <div class="weui-cell">
                    <div class="weui-cell__hd" style="font-size: 14px;">
                        填写物流信息
                    </div>
                    <div style="position: absolute; right: 10px;">
                        <button class="kbtn" @click="setExpMess">取消</button>
                    </div>
                </div>
            </div>
            <div class="weui-cell weui-cell_select weui-cell_select-after">
                <div class="weui-cell__hd"><label class="weui-label">快递公司：</label></div>
                <div class="weui-cell__bd">
                    <select class="weui-select" name="select2" v-model="exId">
                        <option value="">-请选择-</option>
                        <option :value="item.ex_id" v-for="(item,key) in expList">@{{ item.ex_name }}</option>
                        <option value="-1">其它快递</option>
                    </select>
                </div>
            </div>
            <div class="weui-cell" v-if="exId == -1">
                <div class="weui-cell__hd"><label class="weui-label">快递信息：</label></div>
                <div class="weui-cell__bd weui-cell_primary">
                    <input class="weui-input" type="text" placeholder="填写公司名称,单号或联系方式" maxlength="100" v-model="exMess">
                </div>
            </div>
            <div class="weui-cell" v-else>
                <div class="weui-cell__hd"><label class="weui-label">快递单号：</label></div>
                <div class="weui-cell__bd weui-cell_primary">
                    <input class="weui-input" type="text" placeholder="" v-model="exNum">
                </div>
            </div>
            <div class="weui-cell" style="padding-top: 30px;">
                <button class="weui-btn weui-btn_primary" @click="saveExpMess" style="width: 80%;">
                保存
                </button>
            </div>
        </div>
        <div class="get_mess" v-if="expMess && wExpre == false">
            <div class="weui-cells">
                <div class="weui-cell">
                    <div class="weui-cell__hd" style="font-size: 14px;">
                        物流信息
                    </div>
                    <div style="position: absolute; right: 10px;">
                        <button class="kbtn" @click="setExpMess">修改</button>
                    </div>
                </div>
            </div>
            <div class="order_item qhxx">
                <div class="weui-flex" v-if="expMess.ex_id > 0">
                    <div class="title">快递单号</div>
                    <div class="weui-flex__item">@{{ expMess.ex_num }}</div>
                </div>
                <div class="weui-flex">
                    <div class="title">快递公司</div>
                    <div class="weui-flex__item">@{{ expMess.ex_name }}</div>
                </div>
                <div class="weui-flex" v-if="expMess.ex_id == -1">
                    <div class="title">快递信息</div>
                    <div class="weui-flex__item">@{{ expMess.ex_mess }}</div>
                </div>
                <div class="weui-flex" v-else>
                    <div class="title">物流跟踪</div>
                    <div class="weui-flex__item"><i class="err_msg">@{{ expMsg }}</i></div>
                </div>
                <ul class="express-list">
                    <li v-for="(item,key) in expressList">
                        <span>@{{ item.ftime }}</span>
                        <p>@{{ item.context }}</p>
                    </li>
                </ul>
            </div>
        </div>
    </div>
@endsection
@section('js')
  <script src="{{loadEdition('/js/clipboard.min.js')}}"></script>
  <script src="{{loadEdition('/home/js/order_detail.js?13')}}"></script>
@endsection