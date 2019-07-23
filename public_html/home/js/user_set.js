var ispc = isPC();
if (ispc) {
    loadJs('/js/jedate/jedate.min.js', function () {
        var cfgData = {
            theme: {bgcolor: "#00A1CB", color: "#ffffff", pnColor: "#00CCFF"},
            format: "hh:mm",
            isTime: true,
        };
        jeDate("#open_time", cfgData);
        jeDate("#close_time", cfgData);
        jeDate("#picktime_start", cfgData);
        jeDate("#picktime_end", cfgData);
        jeDate("#uptime_start", cfgData);
        jeDate("#uptime_end", cfgData);
    });
}
$(function () {
    $('#warnBtn').click(function () {
        myalert('闭店时间大于或等于24点时，<br/>请设置为 23:59 。');
    });
    if (!ispc) {
        $('.datePicker').focus(function () {
            var hours = [], minutes = [], $this = $(this), val = $this.val();
            for (var i = 0; i <= 23; i++) {
                var v = i < 10 ? '0' + i : i + '';
                hours.push({
                    label: v,
                    value: v
                });
            }
            for (var i = 0; i < 60; i++) {
                var v = i < 10 ? '0' + i : i + '';
                minutes.push({
                    label: v,
                    value: v
                });
            }
            var defaultVal = ['08', '00'];
            if (val != '') {
                defaultVal = val.split(':');
            }
            weui.picker(hours, [{label: ':', value: ':'}], minutes, {
                defaultValue: [defaultVal[0], ':', defaultVal[1]],
                onChange: function (result) {
                    //console.log(result);
                },
                onConfirm: function (result) {
                    $this.val(result[0] + ':' + result[2]);
                }
            });
        });
        $('.hourPicker').focus(function () {
            var hours = [], $this = $(this);
            for (var i = 8; i <= 23; i++) {
                var v = i < 10 ? '0' + i : i + '';
                hours.push({
                    label: v + ' : 00',
                    value: v
                });
            }
            var defaultVal = ['08', '00'];
            weui.picker(hours, {
                defaultValue: [defaultVal[0]],
                onChange: function (result) {
                    //console.log(result);
                },
                onConfirm: function (result) {
                    //console.log(result);
                    $this.val(result[0] + ':00');
                }
            });
        });
    }
});
var vm = new Vue({
    el: '#app',
    data: {
        shopInfo: {},
        status: 0,
        pickupMode: [],
        gsStatus: {
            1: '正常运行',
            0: '闭店'
        },
        gsNotice: '',
        noticeLen: 0,
    },
    created: function () {
        this.getShopInfo();
    },
    methods: {
        getShopInfo: function () {
            var self = this;
            ajaxPost('/mctApi/merchant/User/ShopInfo', null, function (res) {
                if (res.status == 1) {
                    var data = res.data[0];
                    data.picktime_start += ':00';
                    data.picktime_end += ':00';
                    self.shopInfo = data;
                    self.status = data.gs_stats;
                    self.gsNotice = data.gs_notice;
                    var pickModel = data.pickup_mode;
                    if (pickModel == 3) {
                        self.pickupMode = [1, 2];
                    } else {
                        self.pickupMode.push(pickModel);
                    }
                }
            });
        },
        saveShopSet: function () {
            var openTime = $('#open_time').val(),
                closeTime = $('#close_time').val(),
                oarr = openTime.split(':'),
                carr = closeTime.split(':');
            if (openTime == '') {
                return topTips('请选择线上开店时间！');
            }
            if (closeTime == '') {
                return topTips('请选择线上闭店时间！');
            }
            if (Number(carr[0]) < Number(oarr[0])) {
                return topTips('线上闭店时间不能小于开店时间！');
            }
            var suptime = $('#uptime_start').val(),
                euptime = $('#uptime_end').val(),
                sarr = suptime.split(':'),
                earr = euptime.split(':');
            if (suptime == '') {
                return topTips('请选择门店开店时间！');
            }
            if (euptime == '') {
                return topTips('请选择门店闭店时间！');
            }
            if (Number(sarr[0]) > Number(earr[0])) {
                return topTips('门店闭店时间不能小于开店时间！');
            }
            var data = {
                'open_time': openTime,
                'close_time': closeTime,
                'picktime_start': $('#picktime_start').val(),
                'picktime_end': $('#picktime_end').val(),
                'uptime_start': $('#uptime_start').val(),
                'uptime_end': $('#uptime_end').val(),
                'gs_stats': this.status,
                'pickup_mode': 0,
                'gs_notice': this.gsNotice,
            };
            if (this.pickupMode.length > 1) {
                data.pickup_mode = 3;
            } else if (this.pickupMode.length < 1) {
                return topTips('请选择配送方式！');
            } else {
                data.pickup_mode = this.pickupMode[0];
            }
            ajaxPost('/mctApi/merchant/User/ShopSet', data, function (res) {
                if (res.status == 1) {
                    toast('保存成功！');
                }
            });
        }
    },
    watch: {
        gsNotice: function () {
            var len = this.gsNotice.length;
            if (len > 100) {
                this.gsNotice = this.gsNotice.substr(0, 100);
                this.noticeLen = 100;
            } else {
                this.noticeLen = len;
            }

        }
    }
});
