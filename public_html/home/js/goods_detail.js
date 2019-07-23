var vm = new Vue({
    el: '#app',
    data: {
        goodsId: 0,
        goodsData: {},
        specData: [],//规格
        selSpec: {0: {'id': 0}, 1: {'id': 0}, 2: {'id': 0}},//已选规格
        buyNum: 1,
        descData: '',
        totalcount: 0,
        totalPrice: 0,
        goodsPrice: '-'
    },
    created: function () {
        var params = getUrlParams();
        this.goodsId = Number(params.goods_id);
        if (this.goodsId) {
            this.getGoodsDetail();
        }
        this.getShopCart();
    }
    ,
    methods: {
        getGoodsDetail: function () {
            var self = this,
                data = {id: this.goodsId};
            ajaxPost('/mctApi/merchant/Goods/info', data, function (res) {
                if (res.status == 1) {
                    self.goodsData = res.data.goods;
                    self.goodsData.gs_extract = res.data.gs_extract;
                    self.goodsData.gs_desc = res.data.gs_desc;
                    self.descData = self.goodsData.description;
                    self.goodsData.imgs =[];
                    self.goodsData.imgs.push(self.goodsData.goods_img);
                    if (self.goodsData.pimgs.length) {
                        for (var i = 0; i < self.goodsData.pimgs.length; i++) {
                            self.goodsData.imgs.push(self.goodsData.pimgs[i]);
                        }
                        loadJs('/js/swiper.min.js', function () {
                            var mySwiper = new Swiper('.swiper-container', {
                                //autoplay: false,//可选选项，自动滑动
                                loop : true,
                                pagination: {
                                    el: '.swiper-pagination',
                                    type: 'bullets',
                                },
                            })
                        });
                    }
                    //console.log(self.goodsData.imgs);
                    if (res.data.properties) {
                        self.specData = res.data.properties;
                        var idx = 0;
                        for (i in self.specData) {
                            self.specSelect(idx, self.specData[i].values[0]);
                            idx++;
                        }
                    }
                    if (!self.specSelect[0]) {
                        self.goodsPrice = self.goodsData.shop_price;
                    }
                }
            });
        },
        getShopCart: function () {
            var self = this;
            ajaxPost('/mctApi/merchant/Goods/checkout', null, function (res) {
                //console.log(res);
                if (res.status == 1 && res.data.total) {
                    self.totalcount = res.data.total.count;
                    self.totalPrice = res.data.total.goodsAmount;
                }
            }, false)
        },
        plus: function () {
            this.buyNum++;
        },
        jian: function () {
            if (this.buyNum > 1) {
                this.buyNum--;
            }
        },
        specSelect: function (idx, spec) {
            if (this.selSpec[idx] == spec) {
                return
            }
            this.selSpec[idx] = spec;
            if (this.sth) {
                clearTimeout(this.sth);
            }
            var self = this;
            this.sth = setTimeout(function () {
                self.getPrice();
            }, 200);
        },
        addShopCart: function () {//添加至购物车
            var data = {
                'goodsId': this.goodsId,
                'price': this.goodsPrice,
                'number': this.buyNum
            };
            var selSpec = [];
            for (i in this.selSpec) {
                if (this.selSpec[i].id > 0) {
                    selSpec.push(this.selSpec[i].id);
                }
            }
            data.spec = selSpec;
            // console.log(data);
            var self = this;
            ajaxPost('/mctApi/merchant/Goods/addToCart', data, function (res) {
                // console.log(res);
                if (res.status == 1) {
                    toast('添加成功！');
                    if (res.data.total) {
                        self.totalcount = res.data.total.count;
                        self.totalPrice = res.data.total.goodsAmount;
                    }
                }
            })
            //toast('已加入购物车');
        },
        payOrder: function () {
            location = '/home/order/settlement';
        },
        getPrice: function () {
            var selSpec = [];
            for (i in this.selSpec) {
                if (this.selSpec[i].id > 0) {
                    selSpec.push(this.selSpec[i].id);
                }
            }
            var self = this, data = {
                'goodsId': this.goodsId,
                'number': 1,
                'spec': selSpec
            };
            ajaxPost('/mctApi/merchant/Goods/price', data, function (res) {
                if (res.status == 1) {
                    self.goodsPrice = res.data.price;
                }
            }, false);
        }
    }
});