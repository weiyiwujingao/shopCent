<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <meta name="renderer" content="webkit">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>商户管理中心</title>
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="/favicon.ico">
    <link type="text/css" rel="stylesheet" href="/min/b=home/css&amp;f=weui.min.css,common.css?2"/>
    <link type="text/css" rel="stylesheet" href="//at.alicdn.com/t/font_689249_91ahznayuxj.css" />
    @yield('css')
</head>
<body>
<div class="page">
    <div class="page__bd" style="height: 100%;">
        <div class="weui-tab">
            <div class="weui-tab__panel" id="tab_panel">
                @yield('content')
            </div>
        </div>
    </div>
</div>
<script src="{{loadEdition('/js/zepto.min.js')}}"></script>
<script src=//cdnjs.cloudflare.com/ajax/libs/vue/2.5.2/vue.min.js></script>
<script src="//res.wx.qq.com/open/libs/weuijs/1.0.0/weui.min.js"></script>
<script src="{{loadEdition('/home/js/common.js?8')}}"></script>
@yield('js')
<div style="display:none;"><script src="https://s96.cnzz.com/z_stat.php?id=1275870273&web_id=1275870273" language="JavaScript"></script></div>
</body>
</html>