@extends('home.layouts.layout')
@section('css')
    <link type="text/css" rel="stylesheet" href="/min/b=home/css&amp;f=header.css,goods_index.css?v2"/>
@endsection
@section('content')
    @include('home.common.header',['title'=>'商品管理','backUrl'=>'/home/index/index','ritCnt'=>'<span id="switchSort" style="color:#666;">排序</span>'])
    <div id="app">
        <div class="top_search_bx">
            <div class="weui-search-bar" id="searchBar">
                <form class="weui-search-bar__form" onsubmit="return false;">
                    <div class="weui-flex">
                        <div class="weui-cell weui-cell_select weui-cell_select-before">
                            <div class="weui-cell__hd">
                                <select class="weui-select" name="select2" v-model="catId">
                                    <option value="">全部分类</option>
                                    <option :value="item.cat_id"
                                            v-for="(item,key) in goodsCatData">@{{ item.cat_name }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="weui-flex__item">
                            <div class="weui-search-bar__box">
                                <i class="weui-icon-search"></i>
                                <input type="search" class="weui-search-bar__input" id="searchInput" v-model="kw"
                                       placeholder="商品搜索" required/>
                                <a href="javascript:" class="weui-icon-clear" id="searchClear" @click="cancelSearch"></a>
                            </div>
                            <label class="weui-search-bar__label" id="searchText">
                                <i class="weui-icon-search"></i>
                                <span>商品搜索</span>
                            </label>
                        </div>
                    </div>
                </form>
                <a href="javascript:" class="weui-search-bar__cancel-btn" @click="cancelSearch" id="searchCancel">取消</a>
            </div>
            <div class="weui-cells searchbar-result" id="searchResult" style="display: none;">
                {{--<div class="weui-cell weui-cell_access">
                    <div class="weui-cell__bd weui-cell_primary">
                        <p>实时搜索文本</p>
                    </div>
                </div>--}}
            </div>
        </div>
        <div class="goods_list">
            <div class="weui-flex item" v-for="(item,key) in goodsList">
                <div class="weui-flex__item left" onclick="toPage(this)"
                     :data-location="'/home/goods/detail?goods_id='+item.goods_id">
                    <img :src="item.goods_thumb"/>
                </div>
                <div class="weui-flex__item title" onclick="toPage(this)"
                     :data-location="'/home/goods/detail?goods_id='+item.goods_id">
                    <p>@{{ item.goods_name }}</p>

                    <p class="sales">销量：@{{ item.goods_number }}</p>

                    <p class="price">售价：@{{ item.shop_price }}</p>
                </div>
                <div class="weui-flex__item right" v-if="isSort == false">
                    <button class="weui-btn weui-btn_mini"
                            :class="item.sale_status == 1 ? 'weui-btn_primary':'bg_gray'"
                    @click="sxjia(item.goods_id,key)">
                    <span>@{{ item.sale_status_txt }}</span>
                    <i class="iconfont icon-icon_down-copy"></i>
                    </button>
                    <button class="weui-btn weui-btn_mini weui-btn_default" @click="kcSet(item.goods_id)">库存设置</button>
                </div>
                <div class="weui-flex__item right" v-else>
                    <input type="tel" class="wuipt" @blur="changeSort(item.goods_id)" placeholder="序号" :value="item.sort">
                </div>
            </div>
            <div class="weui-loadmore" v-if="noData == false && isLoading == false">
                <p class="page__desc" @click="getGoodsList">加载更多</p>
            </div>
            <div class="weui-loadmore" v-if="isLoading">
                <i class="weui-loading"></i>
                <span class="weui-loadmore__tips">正在加载</span>
            </div>
            <div class="weui-loadmore weui-loadmore_line" v-if="noData && goodsList.length">
                <span class="weui-loadmore__tips nobg">到底啦~</span>
            </div>
            <div class="weui-loadmore weui-loadmore_line" style="margin-top: 200px;"
                 v-else-if="noData && goodsList.length < 1">
                <span class="weui-loadmore__tips nobg">没有相关商品数据~</span>
            </div>
        </div>
        <div class="opendiv" v-if="showOpenDiv">
            <div class="cntbx" id="cntbx">
                <span class="close" @click="closeOpenDiv()">&times;</span>
                {{--<div class="s-1"><input type="number" placeholder="库存" value="0" class="iptkc"></div>--}}
                <form id="stockfrm">
                    <div class="mtitle">
                        <div class="stock" :class="!isPromote ? 'active':''" @click="changeType(false)">普通库存</div>
                        <div class="stock" :class="isPromote ? 'active':''" @click="changeType(true)">打折库存</div>
                    </div>
                    <div class="s-2">
                        <div class="weui-flex s-3" v-for="(item,key) in stockList">
                            <div class="s-left">@{{ item.name }}</div>
                            <div class="s-right">
                                <input type="number" placeholder="库存" :name="'stock['+item.attr_ids+']'" :value="item.num_promotion || 9999" class="iptkc" v-if="isPromote == true">
                                <input type="number" placeholder="库存" :name="'stock['+item.attr_ids+']'" :value="item.num || 9999" class="iptkc" v-else>
                            </div>
                        </div>
                    </div>
                    <div class="s-1">
                        <button class="weui-btn weui-btn_mini weui-btn_default" type="button" @click="closeOpenDiv()">取消</button>
                        <button class="weui-btn weui-btn_mini weui-btn_primary" type="button" @click="saveStock()" style="margin-left: 30px;">保存</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{loadEdition('/home/js/goods_index.js?16')}}"></script>
@endsection