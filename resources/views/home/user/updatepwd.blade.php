@extends('home.layouts.layout')
@section('css')
    <style>
        body {
            background: #eee;
        }

        .weui-cells {
            margin-top: 60px;
        }
    </style>
@endsection
@section('content')
    @include('home.common.header',['title'=>'修改密码','css'=>'1'])
    <div class="weui-cells">
        <div class="weui-cell">
            <div class="weui-cell__hd"><label class="weui-label">当前密码：</label></div>
            <div class="weui-cell__bd weui-cell_primary">
                <input class="weui-input" type="password" id="cur_pwd" placeholder=""/>
            </div>
        </div>
        <div class="weui-cell">
            <div class="weui-cell__hd"><label class="weui-label">新密码：</label></div>
            <div class="weui-cell__bd weui-cell_primary">
                <input class="weui-input" type="password" id="new_pwd" placeholder=""/>
            </div>
        </div>
        <div class="weui-cell">
            <div class="weui-cell__hd"><label class="weui-label">确认新密码：</label></div>
            <div class="weui-cell__bd weui-cell_primary">
                <input class="weui-input" type="password" id="repeat_pwd" placeholder=""/>
            </div>
        </div>
        <div class="weui-cell" style="padding-top: 30px;">
            <button class="weui-btn weui-btn_primary" id="upwdBtn" style="width: 80%;">确认</button>
        </div>
    </div>
@endsection
@section('js')
    <script>
        $(function () {
            $('#upwdBtn').on('click', function () {
                var data = {
                    'password': $('#cur_pwd').val(),
                    'new_password': $('#new_pwd').val(),
                    'new_password_confirmation': $('#repeat_pwd').val(),
                };
                if (data.password.length < 6) {
                    return topTips('请输入正确的当前密码')
                }
                if (data.new_password.length < 6) {
                    return topTips('请输入不少于6个字符的新密码')
                }
                if (data.new_password != data.new_password_confirmation) {
                    return topTips('两次输入的新密码不一致')
                }
                ajaxPost('/mctApi/merchant/User/modifyPsw', data, function () {
                    myalert('修改成功，请用新密码重新登录', function () {
                        ajaxPost('/mctApi/merchant/User/logout', null, function () {
                            location = '/home/login';
                        })
                    })
                });
            });
        });
    </script>
@endsection