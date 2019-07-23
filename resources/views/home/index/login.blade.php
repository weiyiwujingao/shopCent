@extends('home.layouts.layout')
@section('css')
    <style>
        body {
            background: #eee;
        }

        .weui-cells {
            margin-top: 60px;
        }

        /*.header .back {*/
            /*visibility: hidden;*/
        /*}*/
    </style>
@endsection
@section('content')
    @include('home.common.header',['title'=>'商户登录','css'=>'1','ritCnt'=>'<i class="weui-icon-info" id="warnBtn"></i>','backUrl'=>'//m.xingfu-jiabei.com/stores.php'])
    <div class="weui-cells">
        <div class="weui-cell">
            <div class="weui-cell__hd"><label class="weui-label">商户代码：</label></div>
            <div class="weui-cell__bd weui-cell_primary">
                <input class="weui-input" id="uname" type="text" placeholder=""/>
            </div>
        </div>
        <div class="weui-cell">
            <div class="weui-cell__hd"><label class="weui-label">登录密码：</label></div>
            <div class="weui-cell__bd weui-cell_primary">
                <input class="weui-input" id="upwd" type="password" placeholder="不小于6位，区分大小写"/>
            </div>
        </div>
        {{--<div class="weui-cell weui-cell_vcode">
            <div class="weui-cell__hd"><label class="weui-label">验证码：</label></div>
            <div class="weui-cell__bd weui-cell_primary">
                <input class="weui-input" id="code" type="text" placeholder="请输入验证码"/>
            </div>
            <div class="weui-cell__ft">
                <img class="weui-vcode-img" id="imgCode" src="//weui.io/images/vcode.jpg">
            </div>
        </div>--}}
        <div class="weui-cell" style="padding-top: 30px;">
            <button class="weui-btn weui-btn_primary" id="loginBtn" style="width: 80%;">登录</button>
        </div>
    </div>
@endsection
@section('js')
    <script>
        $(function () {
            $('#warnBtn').click(function () {
                myalert('如有忘记密码，请直接找客服。');
            });
            $('#loginBtn').on('click', function () {
                var $uname = $('#uname'), $upwd = $('#upwd'),
                        data = {
                            'gs_login_name': $.trim($uname.val()),
                            'gs_login_pass': $upwd.val()
                        };
                if (data.gs_login_name == '') {
                    return topTips('请输入商户代码')
                }
                if (data.gs_login_pass == '') {
                    return topTips('请输入登录密码')
                }
                if (data.gs_login_pass.length < 6) {
                    return topTips('请输入不少于6位字符的密码')
                }
                ajaxPost('/mctApi/merchant/User/login', data, function (res) {
                    if (res.data.token) {
                        setCookie('mct_token', res.data.token);
                        setTimeout(function () {
                            location.href = '/home/index/index';
                        }, 500);
                    } else {
                        myalert('登录失败，请重试！');
                    }

                })
            });
        });
        checkIsLogin(function () {
            location.href = '/home/index/index';
        });
        function checkIsLogin(callback) {
            var loginToken = getCookie('mct_token');
            if (loginToken) {
                ajaxPost('/mctApi/merchant/User/isLogin', null, function (res) {
                    if (res.status == 1) {
                        if (typeof callback == 'function') {
                            callback();
                        }
                    }
                }, true, false);
            }
        }
    </script>
@endsection