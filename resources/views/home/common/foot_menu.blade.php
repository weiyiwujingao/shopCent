<div class="weui-tabbar foot_menu">
    <a href="/home/index/index" class="weui-tabbar__item{{ (isset($idx) && $idx == 1)? ' weui-bar__item_on':'' }}">
        <i class="iconfont icon-tab_icon_home"></i>
        <p class="weui-tabbar__label">首页</p>
    </a>
    <a href="/home/goods/index" class="weui-tabbar__item{{ (isset($idx) && $idx == 2)? ' weui-bar__item_on':'' }}">
        <i class="iconfont icon-icon_shangping"></i>
        <p class="weui-tabbar__label">商品</p>
    </a>
    <a href="/home/user/index" class="weui-tabbar__item{{ (isset($idx) && $idx == 3)? ' weui-bar__item_on':'' }}">
        <i class="iconfont icon-tab_icon_master"></i>
        <p class="weui-tabbar__label">我的</p>
    </a>
</div>