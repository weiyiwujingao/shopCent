var vm = new Vue({
    el: '#app',
    data: {
        orderData: {},
        goodsList: [],
        expList: [],
        expMess: {},
        expMsg: '',
        expressList: [],//物流详情
        orderSn: '',
        shippingTypeArr: ['门店自提', '商户配送'],
        shippingTypeText: '',
        wExpre: false,
        exId: '',
        exNum: '',
        exMess: '',
        showUserBtn: false,
        showUserName: false
    },
    created: function () {
        var params = getUrlParams();
        this.orderSn = params.order_sn ? params.order_sn : '';
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
        this.getOrderData();
    },
    methods: {
        getOrderData: function () {
            var self = this, data = {
                'orderSn': this.orderSn
            };
            ajaxPost('/mctApi/merchant/Order/detail', data, function (res) {
                if (res.status == 1) {
                    var data = res.data;
                    if (data.order_tel != data.user_name) {
                        self.showUserBtn = true;
                    }
                    self.orderData = data;
                    self.goodsList = data.goods_list;
                    self.expMess = data.order_exp;
                    if (data.address != '') {
                        self.shippingTypeText = self.shippingTypeArr[1];
                    } else {
                        self.shippingTypeText = self.shippingTypeArr[0];
                    }
                    if (self.expMess && self.expMess.ex_id > 0) {
                        self.express();
                    } else if (self.expMess && self.expMess.ex_id == -1) {
                        self.exMess = self.expMess.ex_mess;
                    }
                }
            });
        },
        showOrderUser: function () {
            this.showUserName = !this.showUserName;
        },
        express: function () {
            var data = {'orderSn': this.orderSn}, self = this;
            ajaxPost('/mctApi/merchant/Order/expressDetail', data, function (res) {
                console.log(res);
                if (res.status == 1) {
                    self.expressList = res.data.list;
                } else {
                    self.expMsg = res.message;
                }
            }, false, false);
        },
        setExpMess: function () {
            this.wExpre = !this.wExpre;
            if (this.wExpre) {
                var self = this;
                this.getExpList(function () {
                    if (self.expMess) {
                        self.exId = self.expMess.ex_id;
                        self.exNum = self.expMess.ex_num;
                        setTimeout(function () {
                            //$("html,body").animate({scrollTop: "1000px"}, 500);
                            $('#tab_panel').scrollTop(1000);
                        }, 1000);
                    }
                });
            }
        },
        getExpList: function (callback) {
            if (this.expList.length < 1) {
                var self = this;
                ajaxPost('/mctApi/merchant/Order/expressInfo', null, function (res) {
                    if (res.status == 1) {
                        self.expList = res.data;
                        typeof callback == 'function' && callback();
                    }
                });
            } else {
                typeof callback == 'function' && callback();
            }
        },
        saveExpMess: function () {
            if (this.exId == '') {
                return myalert('请选择快递公司！');
            }
            if (this.exId > 0 && this.exNum == '') {
                return myalert('请填写快递单号！');
            } else if (this.exId == -1 && this.exMess == '') {
                return myalert('请填写其它快递信息！');
            }
            var data = {
                'orderSn': this.orderSn,
                'exId': this.exId,
                'exNum': this.exNum,
                'exMess': this.exMess
            };
            var self = this;
            ajaxPost('/mctApi/merchant/Order/setExpress', data, function (res) {
                if (res.status == 1) {
                    location.reload();
                } else {
                    myalert(res.message || '网络异常，请重试！');
                }
            });
        },
        callPhone: function (orderSn, type) {
            var data = {'orderSn': orderSn, 'type': type};
            ajaxPost('/mctApi/merchant/Order/getPrivateNum', data, function (res) {
                if (res.status == 1) {
                    var ispc = isPC();
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
                    }
                } else {
                    myalert(res.message || '网络异常，请重试！');
                }
            });
        }
    }
});

copyInit();
function copyInit() {
    var clipboard = new ClipboardJS('.copy');
    clipboard.on('success', function (e) {
        e.clearSelection();
        toast('复制成功！');
    });
    clipboard.on('error', function (e) {
        e.clearSelection();
        myalert('复制失败！');
    });
}