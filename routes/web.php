<?php

/**
 * 后台路由
 */

/**后台模块**/
Route::group(['namespace' => 'Admin', 'prefix' => 'admin'], function () {

    Route::get('login', 'AdminsController@showLoginForm')->name('login');  //后台登陆页面

    Route::post('login-handle', 'AdminsController@loginHandle')->name('login-handle'); //后台登陆逻辑

    Route::get('logout', 'AdminsController@logout')->name('admin.logout'); //退出登录

    /**需要登录认证模块**/
    Route::middleware(['auth:admin', 'rbac'])->group(function () {

        Route::resource('index', 'IndexsController', ['only' => ['index']]);  //首页

        Route::get('index/main', 'IndexsController@main')->name('index.main'); //首页数据分析

        Route::get('admins/status/{statis}/{admin}', 'AdminsController@status')->name('admins.status');

        Route::get('admins/delete/{admin}', 'AdminsController@delete')->name('admins.delete');

        Route::resource('admins', 'AdminsController', ['only' => ['index', 'create', 'store', 'update', 'edit']]); //管理员

        Route::get('roles/access/{role}', 'RolesController@access')->name('roles.access');

        Route::post('roles/group-access/{role}', 'RolesController@groupAccess')->name('roles.group-access');

        Route::resource('roles', 'RolesController', ['only' => ['index', 'create', 'store', 'update', 'edit', 'destroy']]);  //角色

        Route::get('rules/status/{status}/{rules}', 'RulesController@status')->name('rules.status');

        Route::resource('rules', 'RulesController', ['only' => ['index', 'create', 'store', 'update', 'edit', 'destroy']]);  //权限

        Route::resource('actions', 'ActionLogsController', ['only' => ['index', 'destroy']]);  //日志
    });
});
/*微信相关*/
Route::any('/wechat', 'WeChatController@serve');
Route::group(['middleware' => ['wechat.oauth:snsapi_userinfo']], function () {
    Route::get('/bindWechat/{token}', 'Api\Merchant\UserController@bindWechat');
});
//前台路由
Route::group(['prefix' => 'home'], function () {
    Route::get('app', function () {
        return redirect()->to('/home/index/login');
    });
    Route::get('login', function () {
        return view('home/index/login');
    });
    Route::get('{module}/{action}', function ($module, $action) {
	    if ($action == 'undefined') {
	        return;
	    }
	    return view('home/' . $module . '/' . $action);
    });
});

Route::group(['prefix' => 'api'], function () {
    Route::any('axBind', 'Api\PublicsController@axBind');
    Route::any('axUnBind', 'Api\PublicsController@axUnBind');
});
//接口
Route::group(['prefix' => 'mctApi'], function () {
    /*商户中心*/
    Route::group(['prefix' => 'merchant'], function () {
        Route::group(['namespace' => 'Api\Merchant'], function () {
            Route::post('User/login', 'UserController@login');
            Route::post('Order/expressInfo', 'OrderController@expressInfo');
            Route::get('Order/exceltest', 'OrderController@exceltest');

        });
        //需要登录验证的接口
        Route::middleware(['login'])->group(function () {
            Route::group(['namespace' => 'Api\Merchant'], function () {
                //判断商户是否有权限操作商户中心
                Route::middleware(['store.auth'])->group(function () {
                    Route::post('Goods/setSort', 'GoodsController@setSort');
                    Route::post('Goods/setSale', 'GoodsController@setSale');
                    Route::post('User/ShopSet', 'UserController@shopSet');
                    Route::post('Goods/getStock', 'GoodsController@getStock');
                    Route::post('Goods/saveStock', 'GoodsController@saveStock');
                    Route::post('Goods/getStock', 'GoodsController@getStock');
                    Route::post('Goods/saveStock', 'GoodsController@saveStock');
                });
                /* 用户管理 */
                Route::post('User/logout', 'UserController@logout');
                Route::post('User/UntieWeixin', 'UserController@UntieWeixin');
                Route::post('User/modifyPsw', 'UserController@modifyPsw');
                Route::post('User/isLogin', 'UserController@isLogin');
                Route::post('User/branchStore', 'UserController@branchStore');
                /*商品管理*/
                Route::post('Goods/info', 'GoodsController@goodInfo');
                Route::post('Goods/addToCart', 'GoodsController@addToCart');
                Route::post('Goods/cart', 'GoodsController@cart');
                Route::post('Goods/updateGroupCart', 'GoodsController@updateGroupCart');
                Route::post('Goods/dropGoods', 'GoodsController@dropGoods');
                Route::post('Goods/upCheckCart', 'GoodsController@upCheckCart');
                Route::post('Goods/checkoutAct', 'GoodsController@checkoutAct');
                Route::post('Goods/sendSmsStores', 'GoodsController@sendSmsStores');
                Route::post('Goods/checkSmscode', 'GoodsController@checkSmscode');
                Route::post('Goods/price', 'GoodsController@price');
                /*订单管理*/
                Route::post('Order/detail', 'OrderController@orderDetail');
                Route::post('Order/expressDetail', 'OrderController@expressDetail');
                Route::post('Order/orderReturn', 'OrderController@orderReturn');
                Route::post('Order/denyRefund', 'OrderController@denyRefund');
                Route::post('Order/take', 'OrderController@take');
                Route::post('Order/delivery', 'OrderController@delivery');
                Route::post('Order/setExpress', 'OrderController@setExpress');
                Route::post('Order/refundList', 'OrderController@refundList');
                Route::post('Order/refundDetail', 'OrderController@refundDetail');
//				Route::get('Order/excelSettlement', 'OrderController@excelSettlement');

            });
            Route::post('{module}/{action}', function ($module, $action) {
                $class = App::make("\\App\\Http\\Controllers\\Api\\Merchant\\" . $module . 'Controller');
                return $class->$action();
            });
            Route::get('{module}/{action}', function ($module, $action) {
                $class = App::make("\\App\\Http\\Controllers\\Api\\Merchant\\" . $module . 'Controller');
                return $class->$action();
            });
        });
    });
    /* 微信接口 */
    Route::group(['prefix' => 'wechat'], function () {
        Route::group(['namespace' => 'Api\Wechat'], function () {
            Route::post('App/jsconfig', 'AppController@jsconfig');
            Route::any('App/login', 'AppController@login');
            Route::any('App/wxlogin', 'AppController@wxlogin');
            Route::any('App/qrcode', 'AppController@qrcode');
        });
        Route::post('{module}/{action}', function ($module, $action) {
            $class = App::make("\\App\\Http\\Controllers\\Api\\Wechat\\" . $module . 'Controller');
            return $class->$action();
        });
    });
    /* 数据运维接口 */
    Route::group(['prefix' => 'office'], function () {
        Route::group(['namespace' => 'Api\DataOffice'], function () {
            Route::get('DataExpload/excelData', 'DataExploadController@excelData');
        });
    });
    /* 用户中心接口 */
    Route::group(['prefix' => 'user'], function () {
        Route::group(['namespace' => 'Api\UserCenter'], function () {
            Route::post('login', 'UserController@login');
            Route::post('register', 'UserController@register');
            Route::post('sendSms', 'UserController@sendSms');
            Route::post('smsLogin', 'UserController@smsLogin');
            Route::post('registerSendSms', 'UserController@registerSendSms');
            Route::post('modifysendSms', 'UserController@modifysendSms');
            Route::any('vcode', 'UserController@vcode');
            Route::any('getVcode', 'UserController@getVcode');
            Route::post('getCity', 'UserAddressController@getCity');
            Route::post('getwxtel', 'UserController@getwxtel');
			Route::post('modifyPswBymobile', 'UserController@modifyPswBymobile');
			Route::post('customerService/servicTel', 'CustomerServiceController@servicTel');
        });
        //需要登录验证的接口
        Route::middleware(['userlogin'])->group(function () {
            Route::group(['namespace' => 'Api\UserCenter'], function () {
                Route::post('isLogin', 'UserController@isLogin');
                Route::post('logout', 'UserController@logout');
                Route::post('upload', 'UserController@upload');
                Route::post('modifyUser', 'UserController@modifyUser');
                Route::post('modifyPsw', 'UserController@modifyPsw');
                Route::post('userDetail', 'UserController@userDetail');
                Route::post('accoountBill', 'UserController@accoountBill');
                Route::post('card/statis', 'UserController@userCardStatis');
                Route::post('card/list', 'UserController@userCardList');
                Route::post('card/delay', 'UserController@cardDelay');
                Route::post('card/activate', 'UserController@activate');
                Route::post('createPayCode', 'UserController@createPayCode');
                Route::post('checkPayCode', 'UserController@checkPayCode');
                Route::any('showPayCode', 'UserController@showPayCode');
                Route::any('notification', 'UserController@notification');
                Route::any('notifidetail', 'UserController@notifidetail');
                Route::post('feedBack', 'UserController@feedBack');
                Route::post('userAddress', 'UserAddressController@list');
                Route::post('userAddress/detail', 'UserAddressController@userAddressDetail');
                Route::post('userAddress/create', 'UserAddressController@create');
                Route::post('userAddress/update', 'UserAddressController@update');
                Route::post('userAddress/delete', 'UserAddressController@delete');
                Route::post('collect/list', 'UserCollectController@list');
                Route::post('collect/delete', 'UserCollectController@delete');
                Route::post('collect/add', 'UserCollectController@add');
                Route::post('collect/isCollect', 'UserCollectController@isCollect');
                Route::post('order/statis', 'UserOrderController@statis');
                Route::post('order/list', 'UserOrderController@list');
                Route::post('order/confirm', 'UserOrderController@confirm');
                Route::post('customerService/list', 'CustomerServiceController@list');
                Route::post('customerService/detail', 'CustomerServiceController@detail');
            });
        });
    });
    /* 产品接口 */
    Route::group(['prefix' => 'product'], function () {
		//不需要登录验证的接口
		Route::middleware(['usermessage'])->group(function () {
			Route::group(['namespace' => 'Api\ProductCenter'], function () {
				Route::post('home', 'HomeController@index');
				Route::any('sellers', 'HomeController@sellers');
				Route::any('position', 'HomeController@position');
				Route::any('citys', 'HomeController@citys');
				Route::any('allCity', 'HomeController@allCity');
				Route::any('systemTime', 'HomeController@systemTime');
				Route::any('brands', 'StoreMenuController@brands');
				Route::any('brandInfo', 'StoreMenuController@brandInfo');
				Route::any('brandSeller', 'StoreMenuController@brandSeller');
				Route::any('catSellers', 'StoreMenuController@catSellers');
				Route::any('sellerDetail', 'StoreMenuController@sellerDetail');
				Route::any('goods', 'GoodsController@goods');
				Route::any('goodMess', 'GoodsController@goodMess');
				Route::any('goodDetail', 'GoodsController@goodDetail');
				Route::any('price', 'GoodsController@price');
				Route::any('search', 'GoodsController@search');
				Route::any('hotSearch', 'GoodsController@hotSearch');
				Route::any('classify', 'GoodsController@classify');
				Route::any('goodSellers', 'GoodsController@goodSellers');
				Route::any('sellerRec', 'GoodsController@sellerRec');
			});
		});
        //需要登录验证的接口
        Route::middleware(['userlogin'])->group(function () {
            Route::group(['namespace' => 'Api\ProductCenter'], function () {
                Route::post('subOrder', 'OrderController@subOrder');
                Route::post('order/detail', 'OrderController@detail');
                Route::post('cancelOrder', 'OrderController@cancelOrder');
                Route::post('chargeBack', 'OrderController@chargeBack');
				Route::post('recharge/subOrder', 'RechargeOrderController@subOrder');
				Route::post('recharge/list', 'RechargeOrderController@list');
            });
        });
    });
	/* 支付接口 start */
	Route::group(['prefix' => 'payment'], function () {
		//需要登录验证的接口
		Route::middleware(['userlogin'])->group(function () {
			Route::group(['namespace' => 'Api\Payment'], function () {
				Route::post('recharge/pay', 'RechargeController@pay');
			});
		});
		Route::group(['namespace' => 'Api\Payment'], function () {
			Route::any('recharge/notify', 'RechargeController@notify');
		});
	});
	/* 支付接口 end */
});
