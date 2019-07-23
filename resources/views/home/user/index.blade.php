@extends('home.layouts.layout')
@section('css')
    <link type="text/css" rel="stylesheet" href="/min/b=home/css&amp;f=header.css,user_index.css"/>
@endsection
@section('content')
    @include('home.common.header',['title'=>'我的信息'])
    <div class="wrap" id="app">
        <div class="weui-flex my_mess" :style="{backgroundImage: 'url(' + userInfo.store_pic + ')', backgroundSize:'contain', backgroundRepeat:'none', backgroundPosition:'center top'}">
            {{--<div class="weui-flex__item logo">
                <img :src="userInfo.store_pic">
            </div>--}}
            <div class="weui-flex__item text" style="background: rgba(255,255,255,0.6);">
                <div class="weui-flex">
                    <div class="label">商户代码：</div>
                    <div class="weui-flex__item">@{{userInfo.gs_login_name}}</div>
                </div>
                <div class="weui-flex">
                    <div class="label">商户名称：</div>
                    <div class="weui-flex__item">@{{userInfo.gs_name}}</div>
                </div>
                <div class="weui-flex">
                    <div class="label">商户地址：</div>
                    <div class="weui-flex__item">@{{userInfo.gs_address}}</div>
                </div>
            </div>
        </div>
        <div class="weui-cells bdcells">
            <a class="weui-cell weui-cell_access" href="/home/user/updatepwd">
                <div class="weui-cell__bd">
                    <p><i class="iconfont icon-update"></i> 修改密码</p>
                </div>
                <div class="weui-cell__ft"></div>
            </a>
            {{--<a class="weui-cell weui-cell_access" href="javascript:;">
                <div class="weui-cell__bd">
                    <p><i class="iconfont icon-icon_Telephone"></i> 幸福热线：400-1363-778</p>
                </div>
                <div class="weui-cell__ft"></div>
            </a>--}}
        </div>
        <div class="weui-cells bdcells">
            <a class="weui-cell weui-cell_access" @click="unbindWx(item, key)" v-for="(item, key) in wxList">
            <div class="weui-cell__bd">
                <p><i class="iconfont icon-weixin"></i> 已绑定微信：@{{ item.nickname }}
            </div>
            <div class="weui-cell__ft">
            </div>
            </a>
            <a class="weui-cell weui-cell_access" @click="bindWechat"  v-if="bindWxShow">
            <div class="weui-cell__bd">
                <p><i class="iconfont icon-weixin"></i> 绑定微信</p>
            </div>
            <div class="weui-cell__ft">
            </div>
            </a>
        </div>
        <div class="weui-cells bdcells">
            <a class="weui-cell weui-cell_access" @click="logout">
            <div class="weui-cell__bd">
                <p><i class="iconfont icon-my_icon_exit"></i> 退出登录</p>
            </div>
            <div class="weui-cell__ft">
            </div>
            </a>
        </div>
    </div>
    @include('home.common.foot_menu',['idx'=>3])
@endsection
@section('js')
    <script>
        var vm = new Vue({
            el: '#app',
            data: {
                userInfo: {},
                wxList: [],
                bindWxShow: false,
                isWeixin: false
            },
            created: function () {
                this.getUserInfo();
                this.isWeixin = isWeixin();
            },
            methods: {
                getUserInfo: function () {
                    var self = this;
                    ajaxPost('/mctApi/merchant/User/userInfo', null, function (res) {
                        //console.log(res);
                        if (res.status == 1) {
                            self.userInfo = res.data;
                            self.wxList = res.data.gs_weixin;
                            if (self.wxList.length < 10 && self.isWeixin) {
                                self.bindWxShow = true;
                            }
                        }
                    })
                },
                unbindWx: function (item, index) { // 解绑微信
                    //console.log(index);
                    var self = this;
                    myconfirm('确认解绑微信：' + item.nickname + ' ?', function () {
                        //console.log(item);
                        ajaxPost('/mctApi/merchant/User/UntieWeixin', item, function (res) {
                            if (res.status == 1) {
                                self.wxList.splice(index, 1);
                            }
                        });
                    });
                },
                bindWechat: function () {
                    myconfirm('请确认该微信是否已关注“幸福加焙”公众号？', function () {
                        var token = getCookie('mct_token');
                        location = '/bindWechat/' + token;
                    })
                },
                logout: function () {
                    myconfirm('确认退出登录？', function () {
                        ajaxPost('/mctApi/merchant/User/logout', null, function (res) {
                            if (res.status == 1) {
                                myStorage.clear();
                                setTimeout(function () {
                                    location = '/home/login';
                                }, 500);
                            }
                        })
                    })
                }
            }
        })
    </script>
@endsection