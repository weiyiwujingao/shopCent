@extends('home.layouts.layout')
@section('css')
    <link type="text/css" rel="stylesheet" href="/min/b=home/css&amp;f=header.css,goods_detail.css"/>
    <link type="text/css" rel="stylesheet" href="/home/css/swiper.min.css"/>
@endsection
@section('content')
    @include('home.common.header',['title'=>'商品详情'])
    <div id="app">
        <div class="goods_detail">
            <div class="head_img swiper-container" v-if="goodsData.imgs && goodsData.imgs.length > 1">
                <div class="swiper-wrapper">
                    <div class="swiper-slide" v-for="(item,key,idx) in goodsData.imgs">
                        <img :src="item">
                    </div>
                </div>
                <div class="swiper-pagination" id="swiper-pagination1"></div>
            </div>
            <div class="head_img" v-else>
                <img :src="goodsData.goods_img">
            </div>
            <div class="goods_cnt">
                <div class="goods_name">@{{ goodsData.goods_name }}</div>
                <div class="goods_price">￥@{{ goodsPrice }}</div>
                <dl class="guige">
                    <template v-for="(item,key,idx) in specData">
                        <dt>@{{ item.name }}：</dt>
                        <dd>
                            <span class="weui-btn weui-btn_mini weui-btn_default" @click="specSelect(idx,spec)"
                            :class="{'active': selSpec[idx].id == spec.id}"
                            v-for="spec in item.values">@{{ spec.label }}</span>
                        </dd>
                    </template>
                </dl>
                <div class="weui-flex buybx">
                    <div class="weui-flex__item">购买数量：</div>
                    <div class="opetate">
                        <div class="icon" @click="jian">
                        <i class="iconfont icon-jian-copy-copy"></i>
                    </div>
                    <div class="input">
                        <input type="tel" class="weui-input" value="1" v-model="buyNum">
                    </div>
                    <div class="icon" @click="plus">
                    <i class="iconfont icon-icon_plus-copy"></i>
                </div>
            </div>
        </div>
        <div class="weui-flex gwcb">
            {{--<div class="weui-flex__item" v-if="goodsData.sale_status == 1">
                <button class="weui-btn weui-btn_mini weui-btn_default" @click="addShopCart">加入购物车</button>
            </div>--}}
            {{--data-location="location='{{ url('/home/order/settlement') }}';"--}}
            <div class="weui-flex__item right">
                <button class="weui-btn weui-btn_mini weui-btn_primary" @click="addShopCart" v-if="
                goodsData.sale_status == 1">加入购物车</button>
                <button class="weui-btn weui-btn_mini weui-btn_default" v-else>已下架</button>
            </div>
        </div>
        <div class="weui-article detail_mess">
            <h1 class="title" v-if="goodsData.gs_extrac != '' || goodsData.gs_desc !=''">温馨提示：</h1>

            <div class="extract">@{{ goodsData.gs_extract }}</div>
            <div class="tips" v-html="goodsData.gs_desc"></div>
            <div class="desc" v-html="descData"></div>
        </div>
        <div class="shopcart-content" v-if="totalcount > 0">
            <div class="content-left">
                <div class="logo-wrapper">
                    <div class="icon">
                        <i class="iconfont icon-hoom_icon_cart"></i>
                    </div>
                    <div class="num">@{{ totalcount }}</div>
                </div>
                <div class="price">￥@{{ totalPrice }}</div>
                <div class="desc"></div>
            </div>
            <div class="content-right" @click="payOrder">
            <div class="pay">去结算</div>
        </div>
    </div>
</div>
@endsection
@section('js')
<script src="{{loadEdition('/home/js/goods_detail.js?5')}}"></script>
@endsection