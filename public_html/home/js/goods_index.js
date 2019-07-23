var ispc = isPC();
$(function () {
    //搜索
    var $searchBar = $('#searchBar'),
        $searchResult = $('#searchResult'),
        $searchText = $('#searchText'),
        $searchInput = $('#searchInput'),
        $searchClear = $('#searchClear'),
        $searchCancel = $('#searchCancel');

    function hideSearchResult() {
        $searchResult.hide();
        $searchInput.val('');
    }

    function cancelSearch() {
        hideSearchResult();
        $searchBar.removeClass('weui-search-bar_focusing');
        $searchText.show();
    }

    $searchText.on('click', function () {
        $searchBar.addClass('weui-search-bar_focusing');
        $searchInput.focus();
    });
    $searchInput
        .on('blur', function () {
            if (!this.value.length) cancelSearch();
        })
        .on('input', function () {
            /*if (this.value.length) {
             $searchResult.show();
             } else {
             $searchResult.hide();
             }*/
        })
    ;
    $searchClear.on('click', function () {
        hideSearchResult();
        $searchInput.focus();
    });
    $searchCancel.on('click', function () {
        cancelSearch();
        $searchInput.blur();
    });
    $('#switchSort').on('click', function () {
        var txt = '排序';
        vm.switchSort();
        if (vm.isSort) {
            txt = '完成';
            if (!this.hasTip) {
                topTips('提示：序号越大越靠前');
                this.hasTip = true;
            }
        }
        $(this).text(txt);
    });
});
var vm = new Vue({
    el: '#app',
    data: {
        catId: '',
        kw: '',
        pageSize: 20,
        page: 0,
        goodsList: [],
        stockList: [],
        goodsCatData: [],
        noData: false,
        isLoading: false,
        isSort: false,
        showOpenDiv: false,
        goodsId: '',
        isPromote: false,
        saleStatus: {
            1: '上架',
            2: '下架'
        }
    },
    created: function () {
        this.getGoodsList();
        this.getGoodsCatData();
    },
    methods: {
        getGoodsList: function () {
            if (this.isLoading || this.noData) {
                return;
            }
            this.page++;
            this.isLoading = true;
            var data = {
                'keyword': this.kw,
                'id': this.catId,
                'page': this.page,
                'pageSize': this.pageSize
            };
            var self = this;
            ajaxPost('/mctApi/merchant/Goods/goodsList', data, function (res) {
                self.isLoading = false;
                if (res.status == 1) {
                    var goodsData = res.data, len = goodsData.length;
                    if (len < 1) {
                        self.noData = true;
                    } else {
                        for (i in goodsData) {
                            if (goodsData[i].sale_status == 1 || goodsData[i].sale_status == 2) {
                                var s = goodsData[i].sale_status;
                                goodsData[i].sale_status_txt = self.saleStatus[s];
                            } else {
                                goodsData[i].sale_status_txt = self.saleStatus[2];
                            }
                            self.goodsList.push(goodsData[i]);
                        }
                        if (len < self.pageSize) {
                            self.noData = true;
                        }
                    }
                }
            })
        },
        getGoodsCatData: function () {
            var self = this;
            ajaxPost('/mctApi/merchant/Goods/goodsType', null, function (res) {
                if (res.status == 1) {
                    self.goodsCatData = res.data;
                }
            }, false);
        },
        switchSort: function () {
            this.isSort = !this.isSort;
            if (!this.isSort && this.changeSortVal == true) {
                this.changeSortVal = false;
                this.search();
            }
            // console.log('ees');
        },
        changeSort: function (gid) {
            this.changeSortVal = true;
            var val = event.srcElement.value;
            if (val === '') {
                return;
            }
            var data = {
                goods_id: gid,
                sort: val
            };
            ajaxPost('/mctApi/merchant/Goods/setSort', data, function (res) {
                if (res.status == 1) {
                    toast('设置成功！');
                }
            });
        },
        sxjia: function (goodsId, index) {
            var self = this;
            if (ispc) {
                weui.confirm('请选择操作！', {
                    title: '',
                    buttons: [{
                        label: '取消',
                        type: 'default',
                        onClick: function () {
                            console.log('no');
                        }
                    }, {
                        label: '下架',
                        type: 'warn',
                        onClick: function () {
                            var url = '/mctApi/merchant/Goods/setSale';
                            var val = 2;
                            var data = {'goods_id': goodsId, 'sale_status': val};
                            ajaxPost(url, data, function (res) {
                                if (res.status == 1) {
                                    toast('设置成功！');
                                    self.goodsList[index].sale_status = val;
                                    self.goodsList[index].sale_status_txt = '下架';
                                }
                            });
                        }
                    }, {
                        label: '上架',
                        type: 'primary',
                        onClick: function () {
                            var url = '/mctApi/merchant/Goods/setSale';
                            var val = 1;
                            var data = {'goods_id': goodsId, 'sale_status': val};
                            ajaxPost(url, data, function (res) {
                                if (res.status == 1) {
                                    toast('设置成功！');
                                    self.goodsList[index].sale_status = val;
                                    self.goodsList[index].sale_status_txt = '上架';
                                }
                            });
                        }
                    }]
                });
                return false;
            }
            weui.picker([
                {
                    label: '上架',
                    value: 1
                },
                {
                    label: '下架',
                    value: 2
                }
            ], {
                className: 'custom-classname',
                container: 'body',
                defaultValue: [1],
                onChange: function (result) {
                    //console.log(result)
                },
                onConfirm: function (result) {
                    //console.log(result)
                    var oval = self.goodsList[index].sale_status,
                        val = result[0],
                        txt = self.saleStatus[val];
                    if (oval != val) {
                        var data = {
                            'goods_id': goodsId,
                            'sale_status': val
                        };
                        ajaxPost('/mctApi/merchant/Goods/setSale', data, function (res) {
                            if (res.status == 1) {
                                toast('设置成功！');
                                self.goodsList[index].sale_status = val;
                                self.goodsList[index].sale_status_txt = txt;
                            }
                        });
                    }
                }
            });
        },
        changeType: function (type) {
            this.isPromote = type;
        },
        search: function () {
            this.page = 0;
            this.noData = false;
            this.goodsList = [];
            this.getGoodsList();
        },
        cancelSearch: function () {
            this.kw = '';
        },
        closeOpenDiv: function () {
            this.showOpenDiv = false;
            this.stockList = [];
            this.isPromote = false;
        },
        kcSet: function (goodsId) {
            //this.isLoading = true;
            this.goodsId = goodsId;
            var self = this;
            ajaxPost('/mctApi/merchant/Goods/getStock', {id: goodsId}, function (res) {
                //self.isLoading = false;
                if (res.status == 1) {
                    self.stockList = res.data;
                    self.showOpenDiv = true;
                    setTimeout(function () {
                        var $obj = $('#cntbx');
                        var ht = $obj.height();
                        //$obj.css('top', '0');
                        $obj.animate({
                            'marginTop': '-' + (ht / 2) + 'px',
                            'top': '50%',
                            'visibility': 'visible'
                        }, 'fast');
                    }, 200);
                } else {
                    myalert(res.message || '获取数据失败！');
                }
            });
        },
        saveStock: function () {//保存库存
            var data = $('#stockfrm').serialize();
            data += '&id=' + this.goodsId;
            var promote = this.isPromote ? 1 : 0;
            data += '&is_promote=' + promote;
            var self = this;
            myconfirm('确认保存？', function () {
                //self.isLoading = true;
                ajaxPost('/mctApi/merchant/Goods/saveStock', data, function (res) {
                    //self.isLoading = false;
                    if (res.status == 1) {
                        self.closeOpenDiv();
                        toast('保存成功！');
                    } else {
                        myalert(res.message || '保存数据失败！');
                    }
                });
            });
        }
    },
    watch: {
        catId: function () {
            this.search();
        },
        kw: function () {
            this.search();
        }
    }
})