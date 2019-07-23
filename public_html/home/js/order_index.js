var ispc = isPC();
if (ispc) {
    loadJs('/js/jedate/jedate.min.js', function () {
        if ($('#startDate').length > 0) {
            jeDate("#startDate", {
                theme: {bgcolor: "#00A1CB", color: "#ffffff", pnColor: "#00CCFF"},
                format: "YYYY-MM-DD",
                isTime: false,
            });
        }
        if ($('#endDate').length > 0) {
            jeDate("#endDate", {
                theme: {bgcolor: "#00A1CB", color: "#ffffff", pnColor: "#00CCFF"},
                format: "YYYY-MM-DD",
                isTime: false,
            });
        }
    });
}
function tkIng() {
    $('span.tk_ing').click(function () {
        var sn = $(this).data('sn');
        window.location = 'refund?order_sn=' + sn;
        return false;
    });
}
var vm = new Vue({
    el: '#app',
    data: {
        type: 1,
        isLoading: false,
        noData: false,
        startDate: '',
        endDate: '',
        orderSn: '',
        storeId: '',
        orderList: [],
        isToday: false,
        page: 0,
        pageSize: 20,
        typeArr: [0, 0, 101, 104, 103],
        branchStores: [],//分店
    },
    created: function () {
        var params = getUrlParams();
        this.type = params.type ? params.type : 0;
        if (this.type == 1) {
            // $('#searchBtn').hide();
            this.isToday = true;
        }
        if (params.order_sn) {
            this.orderSn = params.order_sn;
            $('#orderSn').val(this.orderSn);
        }
        var telTipTimes = getCookie('telTipTimes');
        if (!telTipTimes) {
            telTipTimes = 1;
        } else {
            telTipTimes++;
        }
        if (telTipTimes < 6) {
            topTips('温馨提示：加密电话号码请直接点击拨打');
            setCookie('telTipTimes', telTipTimes, 30);
        }
        this.getOrderList();
        this.getBranchStore();
        var self = this;
        $('#tab_panel').scroll(function () {
            if (self.stHandle) {
                clearTimeout(self.stHandle);
            }
            self.stHandle = setTimeout(function () {
                var scrollTop = $('#get_more').length > 0 ? $('#get_more').offset().top : 0,
                    windowHeight = $(document).height() || document.documentElement.clientHeight;
                //myalert(scrollTop + ':' + windowHeight);
                if (scrollTop < windowHeight) {
                    self.getOrderList();
                }
            }, 200)
        });
    },
    methods: {
        getOrderList: function () {
            if (this.isLoading || this.noData) {
                return;
            }
            this.page++;
            var self = this, data = {
                'dateStart': this.startDate,
                'dateEnd': this.endDate,
                'orderSn': this.orderSn,
                'storeId': this.storeId,
                'pageSize': this.pageSize,
                'page': this.page,
                'ot': this.typeArr[this.type],
            };
            if (this.isToday) {
                data.now = 1;
            }
            this.isLoading = true;
            ajaxPost('/mctApi/merchant/Order/settlement', data, function (res) {
                self.isLoading = false;
                if (res.status == 1) {
                    var orderData = res.data, len = orderData.length;
                    if (len < 1) {
                        self.noData = true;
                    } else {
                        for (i in orderData) {
                            if (orderData[i].order_lxr.length > 5) {
                                orderData[i].order_lxr = orderData[i].order_lxr.substr(0, 5) + '...';
                            }
                            self.orderList.push(orderData[i]);
                        }
                        if (len < self.pageSize) {
                            self.noData = true;
                        }
                        setTimeout('tkIng()',500);
                    }
                }
            });
        },
        exportData: function () {
            if (this.isLoading) {
                return;
            }
            var self = this, data = {
                'dateStart': this.startDate,
                'dateEnd': this.endDate,
                'orderSn': this.orderSn,
                'ot': this.typeArr[this.type],
            };
            if (this.isToday) {
                data.now = 1;
            }
            this.isLoading = true;
            ajaxPost('/mctApi/merchant/Order/excelSettlement', data, function (res) {
                self.isLoading = false;
                if (res.status == 1) {
                    console.log(res);
                    location = '/temp/' + res.data.fileName;
                }
            });
        },
        cfmOrder: function (item, idx) { // 确认提货
            var self = this, data = {
                'orderSn': item.order_sn
            };
            myconfirm('<p style="text-align:left;color: red;">请在客户收到货物后，再确认收货！否则可能造成客户投诉。</p>您确定收货完成交易吗？', function () {
                ajaxPost('/mctApi/merchant/Order/ConfirmDelivery', data, function (res) {
                    if (res.status == 1) {
                        toast('操作成功！');
                        self.orderList[idx].shipping_status = 2;
                    }
                });
            });
        },
        orderTake: function (orderSn, idx) {//商家接单
            var self = this,
                data = {
                    'orderSn': orderSn
                };
            myconfirm('确认接单？', function () {
                ajaxPost('/mctApi/merchant/Order/take', data, function (res) {
                    if (res.status == 1) {
                        toast('接单成功，请及时处理！');
                        self.orderList[idx].order_taking = 2;
                    } else {
                        myalert(res.message || '网络异常，请重试！');
                    }
                });
            });
        },
        delivery: function (orderSn, idx) {//发货
            var self = this,
                data = {
                    'orderSn': orderSn
                };
            myconfirm('确认发货？', function () {
                ajaxPost('/mctApi/merchant/Order/delivery', data, function (res) {
                    if (res.status == 1) {
                        toast('发货成功！');
                        self.orderList[idx].shipping_status = 1;
                    } else {
                        myalert(res.message || '网络异常，请重试！');
                    }
                });
            });
        },
        refund: function (orderSn, idx) {
            window.location = 'refund?order_sn=' + orderSn;//售后页面处理
            return;
            /*var self = this, returnReason = this.orderList[idx].return_reason || '无';
            self.selIndex = idx;
            if (ispc) {
                weui.confirm('订单(' + orderSn + ')申请退货，<p>原因：' + returnReason + ' 。</p>是否确认操作(同意/不同意)？', {
                    title: '',
                    buttons: [{
                        label: '取消',
                        type: 'default',
                        onClick: function () {
                            console.log('no');
                        }
                    }, {
                        label: '不同意',
                        type: 'warn',
                        onClick: function () {
                            var url = '/mctApi/merchant/Order/denyRefund';
                            var data = {'orderSn': orderSn};
                            ajaxPost(url, data, function (res) {
                                if (res.status == 1) {
                                    if (self.type == 3) {
                                        self.orderList.splice(self.selIndex, 1);
                                    } else {
                                        self.orderList[idx].shipping_status = 1;
                                    }
                                    toast('操作成功！');
                                }
                            });
                        }
                    }, {
                        label: '同意',
                        type: 'primary',
                        onClick: function () {
                            var url = '/mctApi/merchant/Order/orderReturn';
                            var data = {'orderSn': orderSn, 'note': '商家退货'};
                            ajaxPost(url, data, function (res) {
                                if (res.status == 1) {
                                    if (self.type == 3) {
                                        self.orderList.splice(self.selIndex, 1);
                                    } else {
                                        self.orderList[idx].order_status = 4;
                                    }
                                    toast('操作成功！');
                                }
                            });
                        }
                    }]
                });
            } else {
                myconfirm('订单(' + orderSn + ')申请退货，<p>原因：' + returnReason + ' 。</p>是否确认操作(同意/不同意)？', function () {
                    self.showRefund(orderSn, idx);
                });
            }*/
        },
        showRefund: function (orderSn, idx) {
            var self = this;
            self.selIndex = idx;
            weui.picker([
                {
                    label: '同意退货',
                    value: 1
                },
                {
                    label: '不同意退货',
                    value: 0
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
                    console.log(self.orderList);
                    var val = result, data = {'orderSn': orderSn}, url = '';
                    if (val == 1) {
                        url = '/mctApi/merchant/Order/orderReturn';
                        data.note = '商家退货';
                    } else {
                        url = '/mctApi/merchant/Order/denyRefund';
                    }
                    ajaxPost(url, data, function (res) {
                        if (res.status == 1) {
                            if (self.type == 3) {
                                self.orderList.splice(self.selIndex, 1);
                            } else {
                                if (val == 1) {
                                    self.orderList[idx].order_status = 4;
                                } else {
                                    self.orderList[idx].shipping_status = 1;
                                }
                            }
                            toast('操作成功！');
                        }
                    });
                }
            });
        },
        search: function () {
            this.startDate = $('#startDate').val();
            this.endDate = $('#endDate').val();
            this.orderSn = $('#orderSn').val();
            this.storeId = $('#store_id').val();
            //console.log(this.startDate);
            this.page = 0;
            this.noData = false;
            this.orderList = [];
            this.getOrderList();
            cancelSearch();
        },
        exportFile: function () {
            this.startDate = $('#startDate').val();
            this.endDate = $('#endDate').val();
            this.orderSn = $('#orderSn').val();
            this.storeId = $('#store_id').val();
            this.exportData();
        },
        callPhone: function (orderSn) {
            var data = {'orderSn': orderSn, 'type': 1};
            ajaxPost('/mctApi/merchant/Order/getPrivateNum', data, function (res) {
                if (res.status == 1) {
                    if (res.data[1] == 1) {
                        if (ispc) {
                            myalert('请拨打该“虚拟号”：' + res.data[0] + ' <br/>与对方联系，30分钟内有效！');
                        } else {
                            myconfirm('您将通过“虚拟号”与对方联系，<br/>请放心使用，30分钟内有效！', function () {
                                location = 'tel:' + res.data[0];
                            });
                        }
                    } else {
                        if (ispc) {
                            myalert('用户号码是：' + res.data[0]);
                        } else {
                            location = 'tel:' + res.data[0];
                        }
                        /*myconfirm('用户号码是：' + res.data[0] + '，<br/>点击确定立即拨打。', function () {
                         location = 'tel:' + res.data[0];
                         });*/
                    }
                } else {
                    myalert(res.message || '网络异常，请重试！');
                }
            });
        },
        getBranchStore: function () {
            var self = this, data = {r: Math.random()};
            ajaxPost('/mctApi/merchant/User/branchStore', data, function (res) {
                if (res.status == 1) {
                    if (res.data.length > 0) {
                        self.branchStores = res.data;
                    }
                }
            }, false, false);
        },
    }
})
function cancelSearch() {
    $('#searchbx,.search_bg').hide();
}
$(function () {
    $('#searchBtn').click(function () {
        var dispaly = $('#searchbx').css('display');
        if (dispaly == 'none') {
            $('#searchbx,.search_bg').show();
        } else {
            cancelSearch();
        }
    });
    if (!ispc) {
        $('.datePicker').focus(function () {
            var $this = $(this), date = new Date(), year = date.getFullYear(), month = date.getMonth();
            weui.datePicker({
                start: 2016,
                end: year,
                defaultValue: [year, month, 1],
                onChange: function (result) {
                    // console.log(result);
                },
                onConfirm: function (result) {
                    //console.log(result);
                    $this.val(result[0] + '-' + result[1] + '-' + result[2]);
                }
            });
        });
    }

});