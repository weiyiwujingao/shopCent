$(function () {
    $('[data-location]').on('click', function () {
        var link = $(this).data('location');
        if (link) {
            window.location.href = link;
        }
    });
    setTimeout(function () {
        $('#app').css('visibility', 'visible');
    }, 100);
});

function toPage(obj) {
    var url = $(obj).data('location');
    if (url) {
        window.location.href = url;
    }
}

function myalert(str, callback) {
    weui.alert(str, callback);
    return false;
}

function myconfirm(str, yes, no, title) {
    var _title = title ? title : '';
    weui.confirm(str, function () {
        typeof yes == 'function' && yes();
    }, function () {
        typeof no == 'function' && no();
    }, {
        title: _title
    });
    return false;
}
function toast(str, callback) {
    weui.toast(str, {
        duration: 2000,
        className: 'custom-classname',
        callback: function () {
            typeof callback == 'function' && callback();
        }
    });
    return false;
}

function topTips(str, callback) {
    weui.topTips(str, {
        duration: 3000,
        className: 'custom-classname',
        callback: function () {
            typeof callback == 'function' && callback();
        }
    });
    return false;
}

function goBack(backUrl) {
    if (backUrl) {
        window.location = backUrl;
    } else {
        window.history.back();
    }
}
function getUrlParams() {
    var location = window.location.href,
        tmparr = location.split('?'),
        getParams = {};
    if (tmparr.length > 1) {
        var queryStr = tmparr[1];
        var queryArr = queryStr.split('&');
        //console.log(queryArr);
        for (i in tmparr) {
            if (tmparr[i].indexOf('=') > 0) {
                var arr = tmparr[i].split('=');
                getParams[arr[0]] = arr[1];
            }
        }
    }
    return getParams;
}
function showLoad(str) {
    weui.loading(str || '正在加载...');
}
function hideLoad() {
    weui.loading().hide();
}

function setCookie(name, value) {
    return myStorage.setItem(name, value);
    /*var d = new Date()
     d.setTime(d.getTime() + (exdays * 60 * 1000)); // 分钟
     var expires = 'expires=' + d.toGMTString();
     document.cookie = name + '=' + value + ';' + expires + ';'*/
}

function getCookie(name) {
    return myStorage.getItem(name);
    /*var reg = new RegExp('(^|)' + name + '=([^;]*)(;|$)');
     var arr = document.cookie.match(reg);
     if (arr) {
     return decodeURIComponent(arr[2]);
     } else {
     return null;
     }*/
}

function ajaxPost(url, data, callback, showd, showErr) {
    if (typeof showd == 'undefined') {
        showd = true;
    }
    if (typeof showErr == 'undefined') {
        showErr = true;
    }
    showd && showLoad();
    var csrfToken = $('meta[name="csrf-token"]').attr('content'),
        loginToken = getCookie('mct_token');
    $.ajax({
        url: url,
        data: data,
        dataType: 'json',
        type: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
            'token': loginToken ? loginToken : ''
        },
        complete: function () {
            showd && hideLoad();
        },
        success: function (res) {
            if (res.status == 1) {
                typeof callback == 'function' && callback(res);
            } else if (res.status == -99 && showErr) {
                myalert('身份验证失效，请重新登录', function () {
                    //setCookie('mct_token', '', -1);
                    myStorage.clear();
                    setTimeout(function () {
                        location.href = '/home/login';
                    }, 500);
                });
            } else {
                showErr && myalert(res.message || '网络异常')
                typeof callback == 'function' && callback(res);
            }
        },
        error: function () {
            myalert('网络异常！')
        }
    })
}

/**
 * 是否微信浏览器
 * @returns {boolean}
 */
function isWeixin() {
    var ua = window.navigator.userAgent.toLowerCase()
    if (ua.indexOf('micromessenger') !== -1) {
        return true
    } else {
        return false
    }
}

/**
 * 是否app
 * @returns {boolean}
 */
function isApp() {
    var ua = window.navigator.userAgent.toLowerCase()
    if (ua.indexOf('jiabeiapp') !== -1) {
        return true
    } else {
        return false
    }
}

window.cookieStorage = (new (function () {
    var maxage = 60 * 60 * 24 * 30;
    var path = '/';

    var cookie = getCookies();

    function getCookies() {
        var cookie = {};
        var all = document.cookie;
        if (all === "")
            return cookie;
        var list = all.split("; ");
        for (var i = 0; i < list.length; i++) {
            var cookies = list[i];
            var p = cookies.indexOf("=");
            var name = cookies.substring(0, p);
            var value = cookies.substring(p + 1);
            value = decodeURIComponent(value);
            cookie[name] = value;
        }
        return cookie;
    }

    var keys = [];
    for (var key in cookie)
        keys.push(key);

    this.length = keys.length;

    this.key = function (n) {
        if (n < 0 || n >= keys.length)
            return null;
        return keys[n];
    };

    this.setItem = function (key, value) {
        if (!(key in cookie)) {
            keys.push(key);
            this.length++;
        }
        var cookies = key + "=" + encodeURIComponent(value);
        if (maxage) {
            cookies += "; max-age=" + maxage;
        }

        if (path)
            cookies += "; path=" + path;

        document.cookie = cookies;
    };

    this.getItem = function (name) {
        return cookie[name] || null;
    };

    this.removeItem = function (key) {
        if (!(key in cookie))
            return;

        delete cookie[key];

        for (var i = 0; i < keys.length; i++) {
            if (keys[i] === key) {
                keys.splice(i, 1);
                break;
            }
        }
        this.length--;

        document.cookie = key + "=; max-age=0";
    };

    this.clear = function () {
        for (var i = 0; i < keys.length; i++)
            document.cookie = keys[i] + "; max-age=0";
        cookie = {};
        keys = [];
        this.length = 0;
    };
})());

//本地存储，localStorage类没有存储空间的限制，而cookieStorage有存储大小限制
//在不支持localStorage的情况下会自动切换为cookieStorage
window.myStorage = (new (function () {
    var storage;    //声明一个变量，用于确定使用哪个本地存储函数
    if (window.localStorage) {
        storage = localStorage;     //当localStorage存在，使用H5方式
    }
    else {
        storage = cookieStorage;    //当localStorage不存在，使用兼容方式
    }
    this.setItem = function (key, value) {
        storage.setItem(key, value);
    };

    this.getItem = function (name) {
        return storage.getItem(name);
    };

    this.removeItem = function (key) {
        storage.removeItem(key);
    };

    this.clear = function () {
        storage.clear();
    };
})());
