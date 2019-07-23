var vm = new Vue({
    el: '#app',
    data: {
        storesName: '',
        stHandle: null,
        goodsList: [],
        oprIndex: null,
        payTypeIdx: 0,
        phoneNumbe: '',
        phoneCode: '',
        orderLxr: '',
        orderPickTime: '',
        payCode: '',
        isWeixin: false,
        isApp: false,
        isTimeRun: false,
        timeMax: 60,
        timeLeft: 60,
        footShow: true
    },
    created: function () {
        this.isApp = isApp();
        if (!this.isApp) {
            this.isWeixin = isWeixin();
            if (this.isWeixin) {
                var self = this;
                setTimeout(function () {
                    self.wxInit();
                }, 1000);
            }
        } else {
            var self = this
            window.appScanResult = function (result) { // app扫码后调用该方法
                self.scanQRCodeDo(self, result);
            }
        }
        this.getCartGoods();
    },
    methods: {
        getCartGoods: function () {
            var self = this;
            ajaxPost('/mctApi/merchant/Goods/checkout', null, function (res) {
                //console.log(res);
                if (res.status == 1) {
                    var dataList = res.data.goodsList;
                    for (i in dataList) {
                        self.storesName = dataList[i].storesName;
                        dataList[i].select = false;
                        self.goodsList.push(dataList[i]);
                    }
                    if (self.goodsList.length < 1) {
                        myalert('购物车为空！', function () {
                            location = '/home/goods/index';
                        })
                    }
                }
            });
        },
        getPhoneCode: function () {
            //console.log(this.phoneNumbe.length);
            var self = this;
            if (this.phoneNumbe.length == 11) {
                var data = {mobile: this.phoneNumbe};
                ajaxPost('/mctApi/merchant/Goods/sendSmsStores', data, function (res) {
                    if (res.status == 1) {
                        self.isTimeRun = true;
                        self.timeLeft = self.timeMax;
                        self.timeRun();
                        toast('发送成功！')
                    }
                });
            } else {
                myalert('请输入正确的手机号码！')
            }
        },
        selectToggle: function (idx) {
            this.goodsList[idx].select = !this.goodsList[idx].select;
        },
        getTime: function (a) {
            // 定义取货时间
            if (nowtime) {
                var timeStamp = Number(nowtime + '000');
                var mydate = new Date(timeStamp);
            } else {
                var mydate = new Date();
            }
            mydate.setDate(mydate.getDate() + a);
            var year = mydate.getFullYear();
            var math = (mydate.getMonth() + 1) < 10 ? '0' + (mydate.getMonth() + 1) : (mydate.getMonth() + 1);
            var day = mydate.getDate() < 10 ? '0' + mydate.getDate() : mydate.getDate();
            var week = mydate.getDay();
            // let tim = mydate.getHours()
            var str;
            switch (week) {
                case 0:
                    str = '日';
                    break;
                case 1:
                    str = '一';
                    break;
                case 2:
                    str = '二';
                    break;
                case 3:
                    str = '三';
                    break;
                case 4:
                    str = '四';
                    break;
                case 5:
                    str = '五';
                    break;
                case 6:
                    str = '六';
                    break;
            }
            var timer = year + '-' + math + '-' + day + ' ' + '星期' + str;
            return timer;
        },
        selectPickTime: function () {
            var dates = [], d1 = [],
                hours = [], d2 = ['10:30-12:30', '14:00-16:00', '16:00-19:00', '19:00-21:00'],
                self = this;
            var days = new Array(4);
            var arr = [];
            for (var i = 0; i < days.length; i++) {
                var c = this.getTime(i);
                arr.push(c);
            }
            d1 = arr;
            //console.log(arr);
            for (i in d1) {
                dates.push({
                    label: d1[i],
                    value: d1[i]
                });
            }
            for (i in d2) {
                hours.push({
                    label: d2[i],
                    value: d2[i]
                });
            }
            weui.picker(dates, hours, {
                defaultValue: [dates[0]['label'], hours[0]['label']],
                onChange: function (result) {
                    //console.log(result);
                },
                onConfirm: function (result) {
                    var time = result[0] + ' ' + result[1]
                    self.orderPickTime = time;
                }
            });
        },
        delGoods: function () {
            var dataList = this.goodsList, recId = [];
            this.goodsList = [];
            for (var i = 0; i < dataList.length; i++) {
                if (dataList[i].select === true) {
                    recId.push(dataList[i].rec_id);
                } else {
                    this.goodsList.push(dataList[i]);
                }
            }
            if (recId.length) {
                var data = {};
                data.recIds = recId.join(',');
                ajaxPost('/mctApi/merchant/Goods/dropGoods', data, function (res) {
                    if (res.status == 1) {
                        toast('删除成功！')
                    } else {
                        setTimeout('location.reload()', 1000);
                    }
                })
            }
        },
        plusNum: function (idx) {
            var num = this.goodsList[idx].goods_number, self = this;
            this.goodsList[idx].goods_number = num + 1;
            if (this.oprIndex == idx && this.stHandle !== null) {
                clearTimeout(this.stHandle);
            }
            this.oprIndex = idx;
            this.stHandle = setTimeout(function () {
                self.updateGoods(idx);
            }, 500);
        },
        reduceNum: function (idx) {
            var num = this.goodsList[idx].goods_number, self = this;
            num--;
            if (num <= 0) {
                myconfirm('确认删除该商品？', function () {
                    self.goodsList[idx].select = true;
                    self.delGoods();
                })
                return;
            }
            this.goodsList[idx].goods_number = num;
            if (this.oprIndex == idx && this.stHandle !== null) {
                clearTimeout(this.stHandle);
            }
            this.oprIndex = idx;
            this.stHandle = setTimeout(function () {
                self.updateGoods(idx);
            }, 500);
        },
        updateGoods: function (idx) {
            var goodsData = this.goodsList[idx], data = {};
            data.goodsId = goodsData.goods_id;
            data.number = goodsData.goods_number;
            data.recId = goodsData.rec_id;
            ajaxPost('/mctApi/merchant/Goods/updateGroupCart', data, function (res) {
                if (res.status != 1) {
                    setTimeout('location.reload()', 1000);
                }
            }, false)
        },
        changePayType: function (idx) {
            this.payTypeIdx = idx;
        },
        cfmOrder: function () {
            if (this.goodsList.length < 1) {
                return myalert('您还没有选择商品！', function () {
                    location = '/home/goods/index';
                });
            }
            var data = {};
            data.order_lxr = this.orderLxr;
            data.order_pick_time = this.orderPickTime;

            if (this.payTypeIdx == 1) {//手机验证码下单
                if (this.phoneNumbe.length < 11) {
                    return myalert('请输入正确的手机号码');
                }
                data.user_login_name = this.phoneNumbe;
                if (this.phoneCode.length < 6) {
                    return myalert('请输入正确的手机验证码');
                }
                data.code = this.phoneCode;
            } else {//收款码
                if (this.payCode.length < 16) {
                    return myalert('收款码有误，请检查！');
                }
                data.pycode = this.payCode;
            }
            ajaxPost('/mctApi/merchant/Goods/checkoutAct', data, function (res) {
                if (res.status == 1) {
                    toast('下单成功！', function () {
                        location = '/home/order/index?type=1';
                    });
                }
            });
        },
        timeRun: function () {
            this.timeLeft--;
            if (this.timeLeft <= 0) {
                this.isTimeRun = false;
                return;
            } else {
                var self = this;
                setTimeout(function () {
                    self.timeRun();
                }, 1000)
            }
        },
        scanQRCode: function () {
            if (this.isApp && window.AndroidWebView) {
                if (typeof window.AndroidWebView.startScanCode === 'function') {
                    window.AndroidWebView.startScanCode('appScanResult');
                } else {
                    return myalert('该版本不支付扫码功能，请更新APP至3.4版本后');
                }
            } else if (this.isApp && window.iosWeixinPay) {
                if (typeof window.iosWeixinPay.startScanCode === 'function') {
                    window.iosWeixinPay.startScanCode('appScanResult');
                } else {
                    return myalert('该版本不支付扫码功能，请更新APP至3.4版本后');
                }
            } else if (this.isWeixin) {
                var self = this;
                wx.ready(function () {
                    wx.scanQRCode({
                        needResult: 1, // 默认为0，扫描结果由微信处理，1则直接返回扫描结果，
                        scanType: ['qrCode'], // 可以指定扫二维码还是一维码，默认二者都有
                        success: function (res) {
                            self.scanQRCodeDo(self, res.resultStr);
                        }
                    })
                })
            }
        },
        scanQRCodeDo: function (obj, result) { // 扫码后处理方法
            if (result !== '') {
                obj.payCode = result;
                obj.cfmOrder();
            }
        },
        wxInit: function () {
            var data = {
                url: window.location.href
            };
            ajaxPost('/mctApi/wechat/App/jsconfig', data, function (res) {
                if (res.status == 1) {
                    var data = res.data;
                    wx.config({
                        debug: false,
                        appId: data.appId, // 必填，公众号的唯一标识
                        timestamp: data.timestamp, // 必填，生成签名的时间戳
                        nonceStr: data.nonceStr, // 必填，生成签名的随机串
                        signature: data.signature,// 必填，签名
                        jsApiList: ['scanQRCode'] // 必填，需要使用的JS接口列表
                    });
                }
            }, false);
        }
    },
    computed: {
        totalPrice: function () {
            var price = 0;
            var goodsData = this.goodsList;
            var length = goodsData.length;
            for (var i = 0; i < length; i++) {
                price += Number(goodsData[i].goods_price) * goodsData[i].goods_number;
            }
            if (price) {
                price = price.toFixed(2);
            }
            return price;
        }
    },
    mounted: function () {
        this.clientHeight = document.documentElement.clientHeight || document.body.clientHeight;
        var self = this;
        window.onresize = function () {
            var cHeight = document.documentElement.clientHeight || document.body.clientHeight;
            if (cHeight < self.clientHeight) {
                self.footShow = false;
            } else {
                self.footShow = true;
            }
        }
    },
});