@extends('home.layouts.layout')
@section('css')
    <link type="text/css" rel="stylesheet" href="/min/b=home/css&amp;f=header.css,order_index.css?2"/>
    <link type="text/css" rel="stylesheet" href="{{loadEdition('/js/jedate/skin/jedate.css')}}"/>
@endsection
@section('content')
    @include('home.common.header',['title'=>'订单管理','backUrl'=>'/home/index/index','ritCnt'=>'<i class="weui-icon-search" id="searchBtn"></i>'])
    <div id="app">
        <div class="weui-flex order_menu">
            <div class="weui-flex__item" :class="{ 'active' : type == 0}" data-location="/home/order/index">全部</div>
            <div class="weui-flex__item" :class="{ 'active' : type == 1}" data-location="?type=1">今日</div>
            <div class="weui-flex__item" :class="{ 'active' : type == 2}" data-location="?type=2">待确认</div>
            {{--<div class="weui-flex__item" :class="{ 'active' : type == 3}" data-location="?type=3">待退货</div>--}}
            <div class="weui-flex__item" :class="{ 'active' : type == 4}" data-location="?type=4">已完成</div>
        </div>
        <div id="searchbx" class="weui-cells">
            <div class="weui-cell" v-if="isToday == false">
                {{--<div class="weui-cell__hd"><label class="weui-label">日期：</label></div>--}}
                <div class="weui-cell__bd weui-cell_primary">
                    <div class="weui-flex">
                        <div class="weui-flex__item">
                            <input class="weui-input datePicker" type="text" id="startDate" placeholder="开始日期" style="text-align: center;" readonly>
                        </div>
                        <div class="weui-flex__item" style="color: #aaa;text-align: center;">至</div>
                        <div class="weui-flex__item">
                            <input class="weui-input datePicker" type="text" id="endDate" placeholder="结束日期" style="text-align: center;" readonly>
                        </div>
                    </div>
                </div>
            </div>
            <div class="weui-cell">
                <div class="weui-cell__hd"><label class="weui-label">订单号：</label></div>
                <div class="weui-cell__bd">
                    <input type="tel" class="weui-input" id="orderSn" maxlength="20">
                </div>
            </div>
            <div class="weui-cell"v-if="branchStores.length > 0">
                <div class="weui-cell__hd"><label class="weui-label">下属分店：</label></div>
                <div class="weui-cell__bd">
                    <select class="weui-select" id="store_id">
                        <option value="">--全部门店--</option>
                        <option :value="item.gs_id" v-for="(item,key,index) in branchStores">@{{ item.gs_name }}</option>
                    </select>
                </div>
            </div>
            <div class="weui-cell weui-flex schbtn">
                <div class="weui-flex__item">
                    <button class="weui-btn weui-btn_default" style="width: 50%;" @click="exportFile">导出</button>
                </div>
                <div class="weui-flex__item">
                    <button class="weui-btn weui-btn_primary" style="width: 50%;" @click="search">搜索</button>
                </div>
            </div>
        </div>
        <div class="search_bg" onclick="cancelSearch();"></div>
        <div class="order_list">
            <article class="weui-article"
                     :class="(item.order_status == 5 && item.shipping_status == 2)? 'completed':(item.order_status == 4 ? 'backgoods':(item.order_status == 2 ? 'cancelgds':''))"
                     v-for="(item,key) in orderList">

                <h3> 订单号：@{{ item.order_sn }} <span class="gs_name" v-if="item.gs_name">@{{ item.gs_name }}</span></h3>
                <div class="weui-flex">
                    <div class="weui-flex__item">
                        <h3>收货人：@{{ item.order_lxr }}</h3>
                    </div>
                    <div class="weui-flex__item">
                        <h3>
                            手机：<a @click="callPhone(item.order_sn,item.encryption)" v-if="item.encryption == 1">@{{ item.order_tel }}</a>
                            <a :href="'tel:'+item.order_tel" v-else>@{{ item.order_tel }}</a>
                        </h3>
                    </div>
                </div>
                <div :data-location="'/home/order/detail?order_sn='+item.order_sn" onclick="toPage(this);">
                    <div class="weui-flex item" v-for="(goods,idx) in item.goods_list">
                        <div class="left"><img :src="goods.goods_thumb"/></div>
                        <div class="weui-flex__item">
                            <div class="title">@{{ goods.goods_name }}</div>
                            <div class="taste">
                                <span v-for="(attr, index) in goods.goods_attr">@{{ attr.attr_name }}: @{{ attr.attr_value }}; </span>&nbsp;
                            </div>
                            <div class="number">数量： x@{{ goods.goods_number }}</div>
                        </div>
                        <div class="right">
                            <strong>￥@{{ goods.goods_price }}</strong>
                            <span class="tk_suc" v-if="item.order_status != 4 && goods.status == 2">退款成功</span>
                            <span class="tk_ing" :data-sn="item.order_sn" v-else-if="item.order_status != 4 && goods.status == 1">请处理退款申请</span>
                        </div>
                    </div>
                    <div class="weui-flex item" v-if="item.shipping_fee > 0">
                        <div class="left">配送费</div>
                        <div class="weui-flex__item"></div>
                        <div class="right">
                            <strong>￥@{{ item.shipping_fee }}</strong>
                        </div>
                    </div>
                    <div class="weui-flex foot">
                        <div class="weui-flex__item title">时间：@{{ item.order_time }}</div>
                        <div class=" title right">总价：￥@{{ item.total_fee }}</div>
                    </div>
                </div>
                <div class="oprate_bx" v-if="item.order_status != 2">
                    <button class="weui-btn weui-btn_mini weui-btn_warn" v-if="item.order_status == 2">已取消</button>
                    <button class="weui-btn weui-btn_mini weui-btn_primary" v-else-if="item.order_taking == 1 && item.order_status == 5"
                    @click="orderTake(item.order_sn,key)">待接单</button>
                    <button class="weui-btn weui-btn_mini weui-btn_disabled weui-btn_default"
                            v-if="item.order_taking == 2 && item.shipping_status < 2">已接单
                    </button>
                    <button class="weui-btn weui-btn_mini weui-btn_warn refund" v-if="item.shipping_status == 3"
                    @click="refund(item.order_sn,key)">已申请退货</button>
                    <button class="weui-btn weui-btn_mini weui-btn_primary"
                            v-if="item.order_status == 5 && item.shipping_status == 0 && item.order_taking != 1"
                    @click="delivery(item.order_sn,key)">确认发货
                    </button>
                    <button class="weui-btn weui-btn_mini weui-btn_primary"
                            v-if="item.order_status == 5 && item.shipping_status == 1 && item.order_taking != 1"
                    @click="cfmOrder(item,key)">确认完成
                    </button>
                </div>
            </article>
            <div class="weui-loadmore" v-if="noData == false && isLoading == false && orderList.length">
                <p class="page__desc" @click="getOrderList" id="get_more">加载更多</p>
            </div>
            <div class="weui-loadmore" v-if="isLoading">
                <i class="weui-loading"></i>
                <span class="weui-loadmore__tips">正在加载</span>
            </div>
            <div class="weui-loadmore weui-loadmore_line" v-if="noData && orderList.length">
                <span class="weui-loadmore__tips nobg">到底啦~</span>
            </div>
            <div class="weui-loadmore weui-loadmore_line" style="margin-top: 200px;"
                 v-else-if="noData && orderList.length < 1">
                <span class="weui-loadmore__tips nobg">没有相关订单数据~</span>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{loadEdition('/home/js/order_index.js?36')}}"></script>
@endsection