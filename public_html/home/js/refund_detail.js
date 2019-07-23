var vm = new Vue({
    el: '#app',
    data: {
        refundData: [],
        applyId: 0,
    },
    created: function () {
        var params = getUrlParams();
        this.applyId = params['apply_id'];
        this.getRefundData();
    },
    methods: {
        getRefundData: function () {
            var self = this, data = {
                'apply_id': this.applyId
            };
            ajaxPost('/mctApi/merchant/Order/refundDetail', data, function (res) {
                if (res.status == 1) {
                    var data = res.data;
                    self.refundData = data;
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
        },
        refund: function () {
            var self = this;
            var ispc = isPC();
            if (ispc) {
                weui.confirm('确认操作退货？', {
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
                            var data = {'apply_id': self.applyId};
                            ajaxPost(url, data, function (res) {
                                if (res.status == 1) {
                                    self.refundData.apply_status = 2;
                                    toast('操作成功！');
                                }
                            });
                        }
                    }, {
                        label: '同意',
                        type: 'primary',
                        onClick: function () {
                            var url = '/mctApi/merchant/Order/orderReturn';
                            var data = {'apply_id': self.applyId};
                            ajaxPost(url, data, function (res) {
                                if (res.status == 1) {
                                    self.refundData.apply_status = 1;
                                    toast('操作成功！');
                                }
                            });
                        }
                    }]
                });
            } else {
                self.showRefund();
            }
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
                    var val = result, data = {'apply_id': self.applyId}, url = '';
                    if (val == 1) {
                        url = '/mctApi/merchant/Order/orderReturn';
                        data.note = '商家退货';
                    } else {
                        url = '/mctApi/merchant/Order/denyRefund';
                    }
                    ajaxPost(url, data, function (res) {
                        if (res.status == 1) {
                            self.refundData.apply_status = (val == 1) ? 1 : 2;
                            toast('操作成功！');
                        }
                    });
                }
            });
        },
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