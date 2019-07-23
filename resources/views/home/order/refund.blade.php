@extends('home.layouts.layout')
@section('css')
    <link type="text/css" rel="stylesheet" href="/min/b=home/css&amp;f=header.css,refund.css?8"/>
@endsection

@section('content')
    @include('home.common.header',['title'=>'售后申请','backUrl'=>'/home/index/index'])
    <div id="app">
        <div style="position: relative;flex: 1 1 auto;">
            <div class="tap">
                <div>
                    <input type="radio" name="tap" id="tapA" checked="checked" v-if="type == 1"/>
                    <label for="tapA" data-location="?type=1"><span>进行中</span></label>

                    <div class="tapcont contA" id="tab_panel_list" v-if="type == 1">
                        <ul class="lb24">
                            <li v-for="(item,key) in refundList">
                                <div class="f"><span>订单号：@{{ item.order_sn }}</span><span>请处理退款申请</span></div>
                                <div class="c"v-for="(goods, i) in item.goods_list" onclick="toPage(this);" :data-location="'/home/order/refundDetail?apply_id='+item.apply_id">
                                    <div class="icon"
                                        :style="{backgroundImage: 'url(' + goods.goods_thumb+ ')'}"></div>
                                    <div class="info">
                                        <h5>@{{ goods.goods_name }}</h5>

                                        <p><span v-for="(kw, j) in goods.goods_attr"> @{{kw.attr_name}} : @{{kw.attr_value}} </span> &nbsp;</p>

                                        <p>数量：×@{{goods.goods_number}}</p>
                                    </div>
                                    <div class="last">
                                        <h5>¥@{{goods.total_price}}</h5>

                                        <p></p>
                                    </div>
                                </div>
                                <div class="l"><span>申请时间：@{{ item.apply_time }}</span><span class="btn" onclick="toPage(this);" :data-location="'/home/order/refundDetail?apply_id='+item.apply_id">处理申请</span></div>
                            </li>
                        </ul>
                        <div class="weui-loadmore" v-if="noData == false && isLoading == false && refundList.length">
                            <p class="page__desc" @click="getRefundList" id="get_more">加载更多</p>
                        </div>
                        <div class="weui-loadmore" v-if="isLoading">
                            <i class="weui-loading"></i>
                            <span class="weui-loadmore__tips">正在加载</span>
                        </div>
                        <div class="weui-loadmore weui-loadmore_line" v-if="noData && refundList.length">
                            <span class="weui-loadmore__tips nobg">到底啦~</span>
                        </div>
                        <div class="weui-loadmore weui-loadmore_line" style="margin-top: 200px;"
                             v-else-if="noData && refundList.length < 1">
                            <span class="weui-loadmore__tips nobg">没有相关数据~</span>
                        </div>
                    </div>
                </div>
                <div>
                    <input type="radio" name="tap" id="tapB" checked="checked" v-if="type == 0"/>
                    <label for="tapB" data-location="?type=0"><span>全部</span></label>

                    <div class="tapcont contA" id="tab_panel_list" v-if="type == 0">
                        <ul class="lb24">
                            <li v-for="(item,key) in refundList">
                                <div class="f"><span>订单号：@{{ item.order_sn }}</span>
                                    <span v-if="item.apply_status == 0">请处理退款申请</span>
                                    <span v-else-if="item.apply_status == 1">退款成功</span>
                                    <span v-else-if="item.apply_status == 2">已拒绝退款</span>
                                </div>
                                <div class="c"v-for="(goods, i) in item.goods_list" onclick="toPage(this);" :data-location="'/home/order/refundDetail?apply_id='+item.apply_id">
                                    <div class="icon"
                                         :style="{backgroundImage: 'url(' + goods.goods_thumb+ ')'}"></div>
                                    <div class="info">
                                        <h5>@{{ goods.goods_name }}</h5>

                                        <p><span v-for="(kw, j) in goods.goods_attr"> @{{kw.attr_name}} : @{{kw.attr_value}} </span> &nbsp;</p>

                                        <p>数量：×@{{goods.goods_number}}</p>
                                    </div>
                                    <div class="last">
                                        <h5>¥@{{goods.total_price}}</h5>

                                        <p></p>
                                    </div>
                                </div>
                                <div class="l">
                                    <span>申请时间：@{{ item.apply_time }}</span>
                                    <span class="btn" onclick="toPage(this);" :data-location="'/home/order/refundDetail?apply_id='+item.apply_id" v-if="item.apply_status == 0">处理申请</span>
                                    <span class="btn" onclick="toPage(this);" :data-location="'/home/order/refundDetail?apply_id='+item.apply_id" v-else>查看详情</span>
                                </div>
                            </li>
                        </ul>
                        <div class="weui-loadmore" v-if="noData == false && isLoading == false && refundList.length">
                            <p class="page__desc" @click="getRefundList" id="get_more">加载更多</p>
                        </div>
                        <div class="weui-loadmore" v-if="isLoading">
                            <i class="weui-loading"></i>
                            <span class="weui-loadmore__tips">正在加载</span>
                        </div>
                        <div class="weui-loadmore weui-loadmore_line" v-if="noData && refundList.length">
                            <span class="weui-loadmore__tips nobg">到底啦~</span>
                        </div>
                        <div class="weui-loadmore weui-loadmore_line" style="margin-top: 200px;"
                             v-else-if="noData && refundList.length < 1">
                            <span class="weui-loadmore__tips nobg">没有相关数据~</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{loadEdition('/home/js/refund.js?6')}}"></script>
@endsection