@extends('home.layouts.layout')
@section('css')
    <link type="text/css" rel="stylesheet" href="/min/b=home/css&amp;f=header.css,refund_detail.css?7"/>
@endsection
@section('content')
    @include('home.common.header',['title'=>'售后详情'])
    <div id="app">
        <div class="refund_detail">
            <div class="refund_top">
                <p v-if="refundData.apply_status == 0">请处理退款申请</p>
                <p v-else-if="refundData.apply_status == 1">退款成功</p>
                <p v-else-if="refundData.apply_status == 2">已拒绝退款</p>
                <p>@{{ refundData.apply_time }}</p>
            </div>
            <div class="refund_title">
                <div class="weui-flex">
                    <span class="uname">@{{ refundData.order_lxr }}</span>
                    <span class="utel" @click="callPhone(refundData.order_sn,1);">@{{ refundData.order_tel }}</span>
                    <span class="weui-flex__item lxmj" @click="callPhone(refundData.order_sn,2)"> <i class="iconfont icon-dianhua" style="margin-right: 5px;"></i>联系买家</span>
                </div>
                <div class="address" v-if="refundData.address != ''">@{{ refundData.address }}</div>
            </div>
            <div class="refund_goods">
                <article class="weui-article">
                    <div class="weui-flex head">
                        <div class="weui-flex__item mername"><i class="iconfont icon-icon_Merchant" style="margin-right: 5px;font-size: 18px;"></i><strong>@{{ refundData.gs_name }}</strong></div>
                        <div class="weui-flex__item" style="text-align: right;"><a  :data-location="'/home/order/detail?order_sn='+refundData.order_sn"><strong>订单详情</strong></a></div>
                    </div>
                    <div class="c"v-for="(goods, i) in refundData.goods_list">
                        <div class="icon"
                             :style="{backgroundImage: 'url(' + goods.goods_thumb+ ')'}"></div>
                        <div class="info">
                            <h5>@{{ goods.goods_name }}</h5>
                            <p><span v-for="(kw, j) in goods.goods_attr"> @{{kw.attr_name}} : @{{kw.attr_value}} </span> &nbsp;</p>
                            <p>数量：×@{{goods.goods_number}}</p>
                        </div>
                        <div class="last">
                            <a>¥@{{goods.total_price}}</a>
                        </div>
                    </div>
                    <div class="refund_item">
                        <div class="weui-flex">
                            <div class="title">退款金额</div>
                            <div class="weui-flex__item"><a>￥@{{ refundData.refund_total }}</a></div>
                        </div>
                        <div class="weui-flex">
                            <div class="title">退款原因</div>
                            <div class="weui-flex__item">@{{ refundData.return_reason }}</div>
                        </div>
                        <div class="weui-flex" v-if="refundData.dispose_time != ''">
                            <div class="title">退款时间</div>
                            <div class="weui-flex__item">@{{ refundData.dispose_time }}</div>
                        </div>
                        <div class="weui-flex">
                            <div class="title">订单编码</div>
                            <div class="weui-flex__item">@{{ refundData.order_sn }} <i class="copy" :data-clipboard-text="refundData.order_sn"></i></div>
                        </div>
                    </div>
                </article>
            </div>
            <div class="desc">
                <div class="title">提示</div>
                <div class="weui-flex">
                    <span>1.</span><div class="weui-flex__item">如非定制类产品，或定制类产品还未开始制作，可直接同意退款给顾客；</div>
                </div>
                <div class="weui-flex">
                    <span>2.</span><div class="weui-flex__item">如未定制类产品且已开始制作，请主动联系顾客说明情况，然后拒绝退货申请；</div>
                </div>
                <div class="weui-flex">
                    <span>3.</span><div class="weui-flex__item">如果为配送类产品且已发货，请主动联系顾客说明情况，然后拒绝退货申请，如未点击已发货，请去订单列表操作发货。</div>
                </div>
            </div>
            <div class="dfoot" v-if="refundData.apply_status == 0"><span @click="refund();">处理申请</span></div>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{loadEdition('/js/clipboard.min.js')}}"></script>
    <script src="{{loadEdition('/home/js/refund_detail.js?35')}}"></script>
@endsection