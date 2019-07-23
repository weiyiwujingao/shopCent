var vm = new Vue({
    el: '#app',
    data: {
        isLoading: false,
        noData: false,
        refundList: [],
        page: 0,
        pageSize: 10,
        type: 1,
        orderSn: ''
    },
    created: function () {
        var params = getUrlParams();
        this.type = params.type ? params.type : 1;
        if (params.order_sn) {
            this.orderSn = params.order_sn;
        }
        this.getList(this.type);
    },
    methods: {
        scrollInit: function () {
            var self = this;
            $('#tab_panel_list').scroll(function () {
                if (self.stHandle) {
                    clearTimeout(self.stHandle);
                }
                self.stHandle = setTimeout(function () {
                    var scrollTop = $('#get_more').length > 0 ? $('#get_more').offset().top : 0,
                        windowHeight = $(document).height() || document.documentElement.clientHeight;
                    //myalert(scrollTop + ':' + windowHeight);
                    if (scrollTop < windowHeight) {
                        self.getRefundList();
                    }
                }, 200)
            });
        },
        getList: function (type) {
            /*if (type == this.type) {
             return;
             }*/
            this.noData = false;
            this.type = type;
            this.page = 0;
            var self = this;
            setTimeout(function () {
                self.scrollInit();
            }, 500);
            this.getRefundList();
        },
        getRefundList: function () {
            if (this.isLoading || this.noData) {
                return;
            }
            this.page++;
            var self = this, data = {
                'page': this.page,
            };
            if (this.type == 1) {
                data.apply_status = 0;
            }
            if (this.orderSn) {
                data.order_sn = this.orderSn;
            }
            ajaxPost('/mctApi/merchant/Order/refundList', data, function (res) {
                self.isLoading = false;
                if (res.status == 1) {
                    var data = res.data, len = data.length;
                    if (self.orderSn && self.page == 1 && len == 1) {
                        location.replace('refundDetail?apply_id=' + data[0].apply_id);
                        return;
                    }
                    if (len < 1) {
                        self.noData = true;
                    } else {
                        for (i in data) {
                            for (j in data[i].goods_list) {
                                var gdata = data[i].goods_list[j];
                                data[i].goods_list[j].total_price = gdata.goods_price * gdata.goods_number;
                            }
                            self.refundList.push(data[i]);
                        }
                        if (len < self.pageSize) {
                            self.noData = true;
                        }
                    }
                }
            });
        },
    }
});
