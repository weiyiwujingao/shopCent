@if(isset($css))
    <link type="text/css" rel="stylesheet" href="/min/b=home/css&amp;f=header.css"/>
@endif
<div class="weui-flex header">
    <div class="back" onclick="goBack('{{ isset($backUrl)? $backUrl:'' }}');">
        <i class="iconfont icon-icon_Return"></i>
    </div>
    <div class="weui-flex__item">{{ $title }}</div>
    <div class="right">{!! isset($ritCnt)? $ritCnt:'' !!}</div>
</div>