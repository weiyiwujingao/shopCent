@extends('home.layouts.layout')
@section('css')
    <link type="text/css" rel="stylesheet" href="{{loadEdition('/js/jedate/skin/jedate.css')}}"/>
    <style>
        .weui-cells {
            margin-top: 60px;
        }

        .weui-select {
            color: #666;
        }

        .weui-cell__bd {
            color: #666;
            font-size: 0.9rem;
        }

        .weui-agree__checkbox:checked:before {
            font-size: 1.2rem;
        }

        .datePicker, .hourPicker {
            text-align: center;
        }
    </style>
@endsection
@section('content')
    @include('home.common.header',['title'=>'店铺设置','css'=>'1','ritCnt'=>'<i class="weui-icon-info" id="warnBtn"></i>'])
    <div class="weui-cells" id="app">
        <div class="weui-cell">
            <div class="weui-cell__hd"><label class="weui-label">线上时间：</label></div>
            <div class="weui-cell__bd weui-cell_primary">
                <div class="weui-flex">
                    <div class="weui-flex__item">
                        <input class="weui-input datePicker" type="text" :value="shopInfo.open_time" id="open_time"
                               placeholder="开店时间" readonly>
                    </div>
                    <div class="weui-flex__item" style="color: #aaa;text-align: center;">至</div>
                    <div class="weui-flex__item">
                        <input class="weui-input datePicker" type="text" :value="shopInfo.close_time" id="close_time"
                               placeholder="闭店时间" readonly>
                    </div>
                </div>
            </div>
        </div>
        <div class="weui-cell">
            <div class="weui-cell__hd"><label class="weui-label">门店时间：</label></div>
            <div class="weui-cell__bd weui-cell_primary">
                <div class="weui-flex">
                    <div class="weui-flex__item">
                        <input class="weui-input datePicker" type="text" :value="shopInfo.uptime_start" id="uptime_start"
                               placeholder="开店时间" readonly>
                    </div>
                    <div class="weui-flex__item" style="color: #aaa;text-align: center;">至</div>
                    <div class="weui-flex__item">
                        <input class="weui-input datePicker" type="text" :value="shopInfo.uptime_end" id="uptime_end"
                               placeholder="闭店时间" readonly>
                    </div>
                </div>
            </div>
        </div>
        <div class="weui-cell">
            <div class="weui-cell__hd"><label class="weui-label">取货时间：</label></div>
            <div class="weui-cell__bd weui-cell_primary">
                <div class="weui-flex">
                    <div class="weui-flex__item">
                        <input class="weui-input hourPicker" type="text" :value="shopInfo.picktime_start"
                               placeholder="开始时间" id="picktime_start" readonly>
                    </div>
                    <div class="weui-flex__item" style="color: #aaa;text-align: center;">至</div>
                    <div class="weui-flex__item">
                        <input class="weui-input hourPicker" type="text" :value="shopInfo.picktime_end"
                               placeholder="结束时间" id="picktime_end" readonly>
                    </div>
                </div>
            </div>
        </div>
        <div class="weui-cell weui-cell_select weui-cell_select-after">
            <div class="weui-cell__hd"><label class="weui-label">门店状态：</label></div>
            <div class="weui-cell__bd">
                <select class="weui-select" name="select2" v-model="status">
                    <option :value="key" v-for="(item,key,index) in gsStatus">@{{ item }}</option>
                </select>
            </div>
        </div>
        <div class="weui-cell">
            <div class="weui-cell__hd"><label class="weui-label">配送方式：</label></div>
            <div class="weui-cell__bd">
                <div class="weui-flex">
                    <div class="weui-flex__item">
                        <label><input type="checkbox" class="weui-agree__checkbox" name="pickup_mode"
                                      v-model="pickupMode" value="1"/> 门店自提</label>
                    </div>
                    <div class="weui-flex__item">
                        <label><input type="checkbox" class="weui-agree__checkbox" name="pickup_mode"
                                      v-model="pickupMode" value="2"/> 商户配送</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="weui-cell">
            <div class="weui-cell__hd"><label class="weui-label">门店公告：</label></div>
            <div class="weui-cell__bd">
                <textarea class="weui-textarea" placeholder="请输入文本，100字符以内"
                          rows="3" v-model="gsNotice"></textarea>
                <div class="weui-textarea-counter"><span>@{{ noticeLen }}</span>/100</div>
            </div>
        </div>
        <div class="weui-cell" style="padding-top: 30px;">
            <button class="weui-btn weui-btn_primary" @click="saveShopSet" style="width: 80%;">
            保存
            </button>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{loadEdition('/home/js/user_set.js?8')}}"></script>
@endsection