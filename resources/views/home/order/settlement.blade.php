@extends('home.layouts.layout')
@section('css')
    <link type="text/css" rel="stylesheet" href="/min/b=home/css&amp;f=header.css,order_settlement.css?2"/>
@endsection
@section('content')
    @include('home.common.header',['title'=>'订单结算','backUrl'=>''])
    <div id="app">
        <div class="order_detail">
            <div class="weui-flex store_title">
                <div class="weui-flex__item">@{{ storesName }}</div>
                <div class="icon" @click="delGoods"><i class="iconfont icon-icon_delete"></i></div>
            </div>
            <article class="weui-article">
                <div class="weui-flex item" v-for="(item,key) in goodsList">
                    <div class="select" @click="selectToggle(key)">
                        <i class="iconfont icon-icon_Select" v-if="item.select === true"></i>
                        <i class="iconfont icon-icon_uncheck" v-else></i>
                    </div>
                    <div class="left"><img :src="item.goods_thumb"/></div>
                    <div class="weui-flex__item">
                        <div class="title">@{{ item.goods_name }}</div>
                        <div class="taste" v-if="item.goods_attr">
                            @{{ item.goods_attr }}
                        </div>
                    </div>
                    <div class="right">
                        <strong>￥@{{ item.goods_price }}</strong>
                        <div class="opetate">
                            <div class="icon" @click="reduceNum(key)">
                            <i class="iconfont icon-jian-copy-copy"></i>
                        </div>
                        <div class="input">
                            <input type="tel" class="weui-input" :value="item.goods_number" readonly>
                        </div>
                        <div class="icon" @click="plusNum(key)">
                            <i class="iconfont icon-icon_plus-copy"></i>
                        </div>
                    </div>
                </div>
            </article>
        </div>
        <div class="frm_mess" v-if="totalPrice > 0">
            <div class="weui-tab">
                <div class="weui-navbar">
                    <div class="weui-navbar__item" :class="{'weui-bar__item_on':payTypeIdx == 0}" @click="changePayType(0)">
                     收款码下单
                    </div>
                    <div class="weui-navbar__item" :class="{'weui-bar__item_on':payTypeIdx == 1}" @click="changePayType(1)">
                        验证码下单
                    </div>
                </div>
            </div>
            <div class="weui-tab__panel">
                <div id="nav_cnt_1" :class="{'hide':payTypeIdx == 1}">
                    <div class="weui-flex skmbx">
                        <div>收款码：</div>
                        <div class="weui-flex__item">
                            <input type="tel" class="weui-input" placeholder="请输入16位数字收款码" v-model="payCode" maxlength="16">
                        </div>
                    </div>
                    <div class="tips">提示：收款码有效时间为50秒，请快速完成输入</div>
                    <div class="scan" v-if="isApp || isWeixin">
                        <button class="weui-btn weui-btn_plain-primary" style="width: 40%;" @click="scanQRCode">手机扫码</button>
                    </div>
                </div>
                <div id="nav_cnt_2" :class="{'hide':payTypeIdx == 0}">
                    <div class="weui-flex skmbx">
                        <div>请输入手机号：</div>
                        <div class="weui-flex__item">
                            <input type="tel" class="weui-input" placeholder="" maxlength="12" v-model="phoneNumbe">
                        </div>
                    </div>
                    <div class="weui-flex">
                        <div class="weui-flex__item">
                            <div class="weui-flex skmbx">
                                <div>验证码：</div>
                                <div class="weui-flex__item">
                                    <input type="tel" class="weui-input" placeholder="" maxlength="8" v-model="phoneCode">
                                </div>
                            </div>
                        </div>
                        <div class="yzmbx">
                            <button class="weui-btn weui-btn_primary" @click="getPhoneCode" v-if="isTimeRun === false">
                            获取验证</button>
                            <button class="weui-btn weui-btn_default weui-btn_disabled" v-else>@{{ timeLeft }}秒</button>
                        </div>
                    </div>
                    <div class="weui-flex skmbx">
                        <div>取货人：</div>
                        <div class="weui-flex__item">
                            <input type="text" class="weui-input" placeholder="" maxlength="20" v-model="orderLxr">
                        </div>
                    </div>
                    <div class="weui-flex skmbx">
                        <div>取货时间：</div>
                        <div class="weui-flex__item">
                            <input type="text" class="weui-input" placeholder="" v-model="orderPickTime"
                                   maxlength="30" @click="selectPickTime" readonly>
                        </div>
                    </div>
                    <div class="tips">提示：下单前请先确认用户幸福券及余额充足。</div>
                </div>
            </div>
            <div class="weui-flex settlement" v-if="footShow && totalPrice > 0">
                <div class="weui-flex__item">
                    <div class="total">总计：￥<span>@{{ totalPrice }}</span></div>
                </div>
                <div class="weui-btn weui-btn_primary" @click="cfmOrder">确认下单</div>
            </div>
        </div>
    </div>
@endsection
@section('js')
<script src="//res.wx.qq.com/open/js/jweixin-1.4.0.js"></script>
<script>var nowtime = '{{time()}}';</script>
<script src="{{loadEdition('/home/js/settlement.js')}}"></script>
@endsection