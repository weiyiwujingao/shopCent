<?php

namespace Helper;
use App\Models\GoodsStock;
use Enum\EnumKeys;
use Enum\EnumLang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

final class CFunctionHelper
{

    /**
     * 获取环境变量
     *
     * @param string $key
     * @return string
     */
    public static function getEnv($key)
    {
        if (isset($_ENV[$key]) && !empty($_ENV[$key])) {
            return $_ENV[$key];
        } elseif (isset($_SERVER[$key]) && !empty($_SERVER[$key])) {
            return $_SERVER[$key];
        }
        return '';
    }

    /**
     * 获取环境变量
     * @return type
     */
    public static function getEnvConst()
    {
        return self::getEnv('APP_ENV');
    }
	/**
	 * 获取环境变量
	 * @return type
	 */
	public static function getEnvLocal() {
		return self::getEnv('APP_ENV');
	}
	/**
	 * 获取是否运营环境的方法
	 * @return type
	 */
	public static function isLocal() {
		$env = self::getEnvLocal();
		return $env == "local" ? true : false;
	}

    /**
     * 获取真实的ip地址
     *
     * @return string
     */
    public final static function getRealIP()
    {
        if (isset($_SERVER)) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
                /* 取X-Forwarded-For中第一个非unknown的有效IP字符串 */
                foreach ($arr AS $ip) {
                    $ip = trim($ip);
                    if ($ip != 'unknown') {
                        $realip = $ip;
                        break;
                    }
                }
            } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $realip = $_SERVER['HTTP_CLIENT_IP'];
            } else {
                if (isset($_SERVER['REMOTE_ADDR'])) {
                    $realip = $_SERVER['REMOTE_ADDR'];
                } else {
                    $realip = '0.0.0.0';
                }
            }
        } else {
            if (getenv('HTTP_X_FORWARDED_FOR')) {
                $realip = getenv('HTTP_X_FORWARDED_FOR');
            } elseif (getenv('HTTP_CLIENT_IP')) {
                $realip = getenv('HTTP_CLIENT_IP');
            } else {
                $realip = getenv('REMOTE_ADDR');
            }
        }
        $onlineip = array();
        preg_match("/[\d\.]{7,15}/", $realip, $onlineip);
        $realip = !empty($onlineip[0]) ? $onlineip[0] : '0.0.0.0';
        return $realip;
    }

    /**
     * 获取请求的URL
     * @return type
     */
    public static function getRequestUrl()
    {
        return isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : "";
    }

    /**
     * 获取完整的请求URL
     * @return type
     */
    public static function getFullRequestUrl()
    {
        if (self::getPort() != 80) {
            return "http://" . self::getHost() . ":" . self::getPort() . self::getRequestUrl();
        } else {
            return "http://" . self::getHost() . self::getRequestUrl();
        }
    }

    /**
     * 对数组进行非法值过滤
     *
     * @param array $arr
     */
    public final static function addslashes($arr)
    {
        if (!get_magic_quotes_gpc()) {
            $rtn = array();
            foreach ($arr as $key => $value) {
                // csrf js
                $val = str_replace(array('<', '>'), array('', ''), $value);
                // sql insert
                if (is_array($val)) {
                    $rtn[$key] = self::addslashes($val);
                } else {
                    $rtn[$key] = addslashes($val);
                }
            }
            return $rtn;
        }
        return $arr;
    }

    //创建目录
    public static function createFolder($path, $recursive = false)
    {
        if (!file_exists($path)) {
            self::createFolder(dirname($path));

            mkdir($path, 0755, $recursive); //0755可以不写
        }
    }

    //创建目录
    public static function createDir($dir)
    {
        $dir_arr = explode('/', $dir);
        $path = '';
        foreach ($dir_arr as $v) {
            $path .= $v . '/';
            if (is_dir($path)) {
                continue;
            } else {
                if (@mkdir($path) == false) {
                    return false;
                } else { //创建文件夹时继承父目录的属组，0表示8进制，不加有可能会出错，2表示setgid，该目录下创建的文件继承父目录的属组
                    if (@chmod($path, 02775) == false) {
                        return false;
                    }
                }
            }
        }
        return true;
    }

    /**
     * GET请求
     * @param type $url
     * @param type $second
     * @param type $ip
     * @param type $cookie
     * @return boolean
     */
    public static function curlGetContents($url, $second = 10, $ip = '', $cookie = '', $gzip = false)
    {
        $url_arr = parse_url($url);
        $header[] = "Referer: " . $url_arr['scheme'] . "://" . $url_arr['host'] . "/";
        $header[] = "Host: " . $url_arr['host'];
        $header[] = "User-Agent: Mozilla/5.0 (Windows; U; Windows NT 6.0; zh-CN; rv:1.9.0.8) Gecko/2009032609 Firefox/3.0.8 (.NET CLR 3.5.30729)";
        $header[] = "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8";
        $header[] = "Accept-Language: zh-cn,zh;q=0.5";
        $header[] = "Accept-Charset: utf-8";
        $header[] = "Connection: close";
        $header[] = $cookie;

        $ch = curl_init();
        if ($ch !== false) {
            if ($ip != '') {
                $count = null;
                $url = str_replace($url_arr['scheme'] . "://" . $url_arr['host'] . "/", $url_arr['scheme'] . "://" . $ip . "/", $url, $count);
                if ($count != 1) {
                    curl_close($ch);
                    return false;
                }
            }
            if ($url_arr['scheme'] == 'https') {
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            }
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, $second);
            curl_setopt($ch, CURLOPT_VERBOSE, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); //支持重定向
            curl_setopt($ch, CURLOPT_MAXREDIRS, 2); //最大重定向两次
            if ($gzip === true) {
                curl_setopt($ch, CURLOPT_ENCODING, "gzip");
            }

            $content = curl_exec($ch);
            $last_retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($last_retcode != 200) { //返回错误
                $content = false;
            }
            curl_close($ch);
            unset($ch);
        } else {
            $content = false;
        }
        return $content;
    }

    /**
     * CURL 获取远程图片内容
     * @param $url
     * @param int $timeout
     * @return bool|mixed
     */
    public static function curlGetPicContent($url, $timeout = 10)
    {
        $url_arr = parse_url($url);
        $ch = curl_init();
        if ($ch !== false) {
            if ($url_arr['scheme'] == 'https') {
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            }
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $img = curl_exec($ch);
        $last_retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if ($last_retcode != 200) { //返回错误
            $img = false;
        }
        curl_close($ch);
        unset($ch);
        return $img;
    }

    /**
     * POST请求
     * @param type $url
     * @param type $postData
     * @param type $cookie
     * @param type $second
     * @param type $content_type
     * @return boolean
     */
    public static function curlPostContents($url, $postData, $cookie = '', $second = 10, $content_type = 'application/x-www-form-urlencoded')
    {
        if (!is_string($postData)) {
            $data = http_build_query($postData);
        } else {
            $data = $postData;
        }
        $url_arr = parse_url($url);
        $header[] = "Referer: " . $url_arr['scheme'] . "://" . $url_arr['host'] . "/";
        $header[] = "Host: " . $url_arr['host'];
        $header[] = "User-Agent: Mozilla/5.0 (Windows; U; Windows NT 6.0; zh-CN; rv:1.9.0.8) Gecko/2009032609 Firefox/3.0.8 (.NET CLR 3.5.30729)";
        $header[] = "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8";
        $header[] = "Accept-Language: zh-cn,zh;q=0.5";
        $header[] = "Accept-Charset: utf-8";
        $header[] = "Cache-Control: no-cache";
        $header[] = "Content-type: {$content_type}";
        $header[] = "Content-length: " . strlen($data);
        $header[] = "Connection: close";
        $header[] = $cookie;

        $ch = curl_init();
        if ($ch !== false) {
            if ($url_arr['scheme'] == 'https') {
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            }
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, $second);
            curl_setopt($ch, CURLOPT_VERBOSE, false);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); //支持重定向
            curl_setopt($ch, CURLOPT_MAXREDIRS, 2); //最大重定向两次

            $content = curl_exec($ch);
            $last_retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($last_retcode != 200) { //返回错误
                $content = false;
            }
            curl_close($ch);
            unset($ch);
        } else {
            $content = false;
        }
        return $content;
    }

    /**
     * 按字符统计长度
     * @param type $str
     * @param type $charset
     * @return int
     */
    public static function sstrlen($str, $charset = "utf-8")
    {
        $n = 0;
        $p = 0;
        $c = '';
        $len = strlen($str);
        if ($charset == 'utf-8') {
            for ($i = 0; $i < $len; $i++) {
                $c = ord($str{$i});
                if ($c > 252) {
                    $p = 5;
                } elseif ($c > 248) {
                    $p = 4;
                } elseif ($c > 240) {
                    $p = 3;
                } elseif ($c > 224) {
                    $p = 2;
                } elseif ($c > 192) {
                    $p = 1;
                } else {
                    $p = 0;
                }
                $i += $p;
                $n++;
            }
        } else {
            for ($i = 0; $i < $len; $i++) {
                $c = ord($str{$i});
                if ($c > 127) {
                    $p = 1;
                } else {
                    $p = 0;
                }
                $i += $p;
                $n++;
            }
        }
        return $n;
    }

    /**
     * 中文字符串分割
     * @param type $string
     * @param type $len
     * @return type
     */
    public static function mbStrSplit($string, $len = 1)
    {
        $start = 0;
        $strlen = mb_strlen($string);
        while ($strlen) {
            $array[] = mb_substr($string, $start, $len, "utf8");
            $string = mb_substr($string, $len, $strlen, "utf8");
            $strlen = mb_strlen($string);
        }
        return $array;
    }

    /**
     * (保持单词完整性)英文字符串分割
     * @param type $string
     * @param type $len
     * @return string
     */
    public function enStrSplit($string, $len = 60)
    {
        $chipsArr = explode(' ', $string);
        $str = '';
        foreach ($chipsArr as $chips) {
            $str .= $chips . ' ';
            $strLen = strlen($str);
            if ($strLen > $len) {
                $strArr[] = rtrim($str, $chips . ' ');
                $str = '';
                $str .= $chips . ' ';
            }
        }
        $strArr[] = $str;
        return $strArr;
    }

    /**
     * 获取唯一key
     * @return string
     */
    public static function getUniqId()
    {
        return str_replace(".", "", uniqid(getmypid(), true));
    }

    /**
     * 验证身份证号
     * @param $vStr
     * @return bool
     */
    public static function validateIdentity($vStr)
    {
        $vCity = array(
            '11', '12', '13', '14', '15', '21', '22',
            '23', '31', '32', '33', '34', '35', '36',
            '37', '41', '42', '43', '44', '45', '46',
            '50', '51', '52', '53', '54', '61', '62',
            '63', '64', '65', '71', '81', '82', '91',
        );

        if (!preg_match('/^([\d]{17}[xX\d]|[\d]{15})$/', $vStr)) return false;

        if (!in_array(substr($vStr, 0, 2), $vCity)) return false;

        $vStr = preg_replace('/[xX]$/i', 'a', $vStr);
        $vLength = strlen($vStr);

        if ($vLength == 18) {
            $vBirthday = substr($vStr, 6, 4) . '-' . substr($vStr, 10, 2) . '-' . substr($vStr, 12, 2);
        } else {
            $vBirthday = '19' . substr($vStr, 6, 2) . '-' . substr($vStr, 8, 2) . '-' . substr($vStr, 10, 2);
        }

        if (date('Y-m-d', strtotime($vBirthday)) != $vBirthday) return false;
        if ($vLength == 18) {
            $vSum = 0;

            for ($i = 17; $i >= 0; $i--) {
                $vSubStr = substr($vStr, 17 - $i, 1);
                $vSum += (pow(2, $i) % 11) * (($vSubStr == 'a') ? 10 : intval($vSubStr, 11));
            }

            if ($vSum % 11 != 1) return false;
        }

        return true;
    }

    /**
     * 判断一个身份证号持有人是否未成年
     */
    public static function isMinor($vStr)
    {
        $vLength = strlen($vStr);
        if ($vLength == 18) {
            $vBirthday = substr($vStr, 6, 4) . '-' . substr($vStr, 10, 2) . '-' . substr($vStr, 12, 2);
        } else {
            $vBirthday = '19' . substr($vStr, 6, 2) . '-' . substr($vStr, 8, 2) . '-' . substr($vStr, 10, 2);
        }
        if ((time() - strtotime($vBirthday)) > 18 * 365 * 24 * 60 * 60) {
            //成年
            return false;
        } else {
            //未成年
            return true;
        }
    }

    /**
     * 通过身份证号码获取出生年月日
     */
    public static function getBirthByIdCard($idCard)
    {
        $birthday = strlen($idCard) == 15 ? ('19' . substr($idCard, 6, 6)) : substr($idCard, 6, 8);

        return $birthday;
    }

    /**
     * 通过身份证号码判断性别
     * 15位身份证的最后一位，18位身份证的第17位代表性别，奇数代表男性，偶数代表女性
     */
    public static function getSexByIdCard($idCard)
    {
        $sex = substr($idCard, (strlen($idCard) == 15 ? -1 : -2), 1) % 2;

        return $sex; //1:男， 0：女
    }

    /**
     * 判断是否是正确的邮箱
     */
    public static function isEmail($email)
    {
        return preg_match("/\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/", $email) ? true : false;
    }

    /**
     * 获取毫秒级别时间戳
     */
    public static function getMillisecond()
    {
        list($t1, $t2) = explode(' ', microtime());
        return (float)sprintf('%.0f', (floatval($t1) + floatval($t2)) * 1000);
    }

    /**
     * 检查url是否存在
     */
    public static function urlExists($url)
    {
        $hdrs = @get_headers($url);
        return is_array($hdrs) ? preg_match('/^HTTP\\/\\d+\\.\\d+\\s+2\\d\\d\\s+.*$/', $hdrs[0]) : false;
    }

    /**
     * 下载远程图片到本地服务器
     * @param $url  图片地址
     * @param $saveDir  保存在本地目录
     * @param $recursive 是否设置递归模式
     * @return array|bool
     */
    public static function getImg($url, $saveDir, $recursive = false)
    {
        if (trim($url) == '' || trim($saveDir) == '') {
            return false;
        }

        //检查图片后缀名是否合法
        $ext = strrchr($url, '.');
        if ($ext != '.gif' && $ext != '.jpg' && $ext != '.png' && $ext != '.jpeg' && $ext != '.bmp') {
            return false;
        }
        $fileName = date('YmdHis') . rand(1000, 9999) . $ext;
        $img = self::curl_get_pic_content($url);
        if (!is_dir($saveDir)) {
            \Helper\CFunctionHelper::createFolder($saveDir, $recursive);
        }
        if ($img !== false) {
            //文件大小
            $fp2 = @fopen($saveDir . $fileName, 'a');
            if ($fp2) {
                fwrite($fp2, $img);
                fclose($fp2);
                unset($img, $url);
            }

            return array('fileName' => $fileName, 'localFilePath' => $saveDir . $fileName);
        }
        return false;
    }

    public static function GenerateSaveUser2Scripts($userName, $password)
    {
        $lowerName = strtolower($userName);
        $script = "<script type='text/javascript'>\n" .
            "$(document).ready(function(){\n" .
            "	var ua = window.navigator.userAgent.toLowerCase(); \n" .
            "	if(ua.match(/jiabeiAppAndroid/i) == 'jiabeiappandroid' && window.AndroidWebView.saveUser2){\n" .
            "		window.AndroidWebView.saveUser2('$userName','$password');}\n" .
            "	else if(ua.match(/jiabeiAppIOS/i) == 'jiabeiappios' && typeof(iosWeixinPay) != 'undefined' && iosWeixinPay.saveUser2){\n" .
            "		iosWeixinPay.saveUser2('{\"name\":\"$lowerName\",\"pwd\":\"$password\"}');}\n" .
            "});\n" .
            '</script>';
        return $script;
    }

    /**
     * 格式化商品价格
     *
     * @access  public
     * @param   float $price 商品价格
     * @return  string
     */
    public static function priceFormat($price, $changePrice = true)
    {
        if ($price === '') {
            $price = 0;
        }
        $price = number_format($price, 2, '.', '');
        if ($changePrice === false) {
            return $price;
        }
        return sprintf('￥%s', $price);
    }

    //判断是否为APP
    public static function getDeviceType()
    {
        $agent = 'aa' . (isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '');
        $type = false;
        if (strpos($agent, 'jiabeiApp') > 0) {
            $type = true;
        }
        return $type;
    }

    //判断是否为移动端
    public static function isMobile()
    {
        // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
        if (isset ($_SERVER['HTTP_X_WAP_PROFILE'])) {
            return true;
        }
        // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
        if (isset ($_SERVER['HTTP_VIA'])) {
            // 找不到为flase,否则为true
            return stristr($_SERVER['HTTP_VIA'], "wap") ? true : false;
        }
        // 脑残法，判断手机发送的客户端标志,兼容性有待提高
        if (isset ($_SERVER['HTTP_USER_AGENT'])) {
            $clientkeywords = array('nokia',
                'sony',
                'ericsson',
                'mot',
                'samsung',
                'htc',
                'sgh',
                'lg',
                'sharp',
                'sie-',
                'philips',
                'panasonic',
                'alcatel',
                'lenovo',
                'iphone',
                'ipod',
                'blackberry',
                'meizu',
                'android',
                'netfront',
                'symbian',
                'ucweb',
                'windowsce',
                'palm',
                'operamini',
                'operamobi',
                'openwave',
                'nexusone',
                'cldc',
                'midp',
                'wap',
                'mobile',
            );
            // 从HTTP_USER_AGENT中查找手机浏览器的关键字
            if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
                return true;
            }
        }
        // 协议法，因为有可能不准确，放到最后判断
        if (isset ($_SERVER['HTTP_ACCEPT'])) {
            // 如果只支持wml并且不支持html那一定是移动设备
            // 如果支持wml和html但是wml在html之前则是移动设备
            if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
                return true;
            }
        }
        return false;
    }

    /**
     * 过滤和排序所有分类，返回一个带有缩进级别的数组
     *
     * @access  private
     * @param   int $cat_id 上级分类ID
     * @param   array $arr 含有所有分类的数组
     * @param   int $level 级别
     * @return  void
     */
    public static function catOptions($spec_cat_id, $arr)
    {
        static $cat_options = array();

        if (isset($cat_options[$spec_cat_id])) {
            return $cat_options[$spec_cat_id];
        }

        if (!isset($cat_options[0])) {
            $level = $last_cat_id = 0;
            $options = $cat_id_array = $level_array = array();
            $data = false;
            if ($data === false) {
                while (!empty($arr)) {
                    foreach ($arr AS $key => $value) {
                        $cat_id = $value['cat_id'];
                        if ($level == 0 && $last_cat_id == 0) {
                            if ($value['parent_id'] > 0) {
                                break;
                            }

                            $options[$cat_id] = $value;
                            $options[$cat_id]['level'] = $level;
                            $options[$cat_id]['id'] = $cat_id;
                            $options[$cat_id]['name'] = $value['cat_name'];
                            unset($arr[$key]);

                            if ($value['has_children'] == 0) {
                                continue;
                            }
                            $last_cat_id = $cat_id;
                            $cat_id_array = array($cat_id);
                            $level_array[$last_cat_id] = ++$level;
                            continue;
                        }

                        if ($value['parent_id'] == $last_cat_id) {
                            $options[$cat_id] = $value;
                            $options[$cat_id]['level'] = $level;
                            $options[$cat_id]['id'] = $cat_id;
                            $options[$cat_id]['name'] = $value['cat_name'];
                            unset($arr[$key]);

                            if ($value['has_children'] > 0) {
                                if (end($cat_id_array) != $last_cat_id) {
                                    $cat_id_array[] = $last_cat_id;
                                }
                                $last_cat_id = $cat_id;
                                $cat_id_array[] = $cat_id;
                                $level_array[$last_cat_id] = ++$level;
                            }
                        } elseif ($value['parent_id'] > $last_cat_id) {
                            break;
                        }
                    }

                    $count = count($cat_id_array);
                    if ($count > 1) {
                        $last_cat_id = array_pop($cat_id_array);
                    } elseif ($count == 1) {
                        if ($last_cat_id != end($cat_id_array)) {
                            $last_cat_id = end($cat_id_array);
                        } else {
                            $level = 0;
                            $last_cat_id = 0;
                            $cat_id_array = array();
                            continue;
                        }
                    }

                    if ($last_cat_id && isset($level_array[$last_cat_id])) {
                        $level = $level_array[$last_cat_id];
                    } else {
                        $level = 0;
                    }
                }
                //如果数组过大，不采用静态缓存方式
                if (count($options) <= 2000) {
                    //  write_static_cache('cat_option_static', $options);
                }
            } else {
                $options = $data;
            }
            $cat_options[0] = $options;
        } else {
            $options = $cat_options[0];
        }

        if (!$spec_cat_id) {
            return $options;
        } else {
            if (empty($options[$spec_cat_id])) {
                return array();
            }

            $spec_cat_id_level = $options[$spec_cat_id]['level'];

            foreach ($options AS $key => $value) {
                if ($key != $spec_cat_id) {
                    unset($options[$key]);
                } else {
                    break;
                }
            }

            $spec_cat_id_array = array();
            foreach ($options AS $key => $value) {
                if (($spec_cat_id_level == $value['level'] && $value['cat_id'] != $spec_cat_id) ||
                    ($spec_cat_id_level > $value['level'])
                ) {
                    break;
                } else {
                    $spec_cat_id_array[$key] = $value;
                }
            }
            $cat_options[$spec_cat_id] = $spec_cat_id_array;

            return $spec_cat_id_array;
        }
    }

    /**
     * 判断某个商品是否正在特价促销期
     *
     * @access  public
     * @param   float $price 促销价格
     * @param   string $start 促销开始日期
     * @param   string $end 促销结束日期
     * @return  float   如果还在促销期则返回促销价，否则返回0
     */
    public static function bargainPrice($price, $start, $end)
    {
        if ($price == 0) {
            return 0;
        } else {
            $time = time();
            if ($time >= $start && $time <= $end) {
                return $price;
            } else {
                return 0;
            }
        }
    }

    /**
     * 添加商品名样式
     * @param   string $goods_name 商品名称
     * @param   string $style 样式参数
     * @return  string
     */
    public static function addStyle($goods_name, $style)
    {
        $goods_style_name = $goods_name;

        $arr = explode('+', $style);

        $font_color = !empty($arr[0]) ? $arr[0] : '';
        $font_style = !empty($arr[1]) ? $arr[1] : '';

        if ($font_color != '') {
            $goods_style_name = '<font color=' . $font_color . '>' . $goods_style_name . '</font>';
        }
        if ($font_style != '') {
            $goods_style_name = '<' . $font_style . '>' . $goods_style_name . '</' . $font_style . '>';
        }
        return $goods_style_name;
    }

    /**
     * 重新获得商品图片与商品相册的地址
     *
     * @param string $image 原商品相册图片地址
     * @return string   $url
     */
    public static function getImagePath($image = '')
    {
        $_CFG = \Enum\EnumLang::loadConfig();
        $url = empty($image) ? $_CFG['no_picture'] : $image;
        return $url;
    }

    /**
     * 获取顶级分类信息
     * @author: colin
     * @date: 2018/11/9 17:58
     * @param $value
     * @param string $id
     * @return string
     */
    public static function getParent($value, $id = '')
    {
        if ($value != 0) {
            $res = \App\Models\Category::where('cat_id', $value)->value('parent_id');
            return self::getParent($res, $value);
        } else {
            return $id;
        }
    }

    /***
     * 商户操作日志
     * @author: colin
     * @date: 2018/11/13 17:12
     * @param $gsId      integer 商户id
     * @param $business  integer 业务类型
     * @param $table     string   表名
     * @param $type      integer  1增2改3删除
     * @param $comment   string   说明
     */
    public static function setStoreLogs($gsId, $business, $table, $type, $comment)
    {
        $dataInfo = [
            'gs_id'    => $gsId,
            'business' => $business,
            'table'    => $table,
            'type'     => $type,
            'comment'  => $comment,
            'ip'       => self::getRealIP(),
        ];
        \App\Models\StoreLogs::create($dataInfo);
    }

	/***
	 * 组装数据 获取指定键的数组
	 * @author: colin
	 * @date: 2018/12/5 10:11
	 * @param $keys
	 * @param $arr
	 * @return array
	 */
	public static function setParamData($keys,$arr)
	{
		if(!is_array($keys) || !is_array($arr)){
			return [];
		}
		$data = [];
		foreach ($arr as $k=>$v){
			if(in_array($k,$keys)){
				$data[$k] = $v;
			}
		}
		return $data;
	}

    /**
     * 判断某个商品是否正在特价促销期
     *
     * @access  public
     * @param   float $price 促销价格
     * @param   string $start 促销开始日期
     * @param   string $end 促销结束日期
     * @return  float   如果还在促销期则返回促销价，否则返回0
     */
    public static function isPromote($price, $start, $end)
    {
        if ($price == 0)
            return 0;
        $time = time();
        if ($time >= $start && $time <= $end) {
            return $price;
        }
        return 0;
    }

	/***
	 * 获取数组最小值
	 * @author: colin
	 * @date: 2018/12/5 14:50
	 * @param $data  数据列表
	 * @param int $min  最小值排除
	 * @return mixed
	 */
    public static function getMinAll($data, $min = 0)
	{
		if(!is_array($data))
			return $data;
		foreach($data as $key => $val){
			if($val<=$min)
				unset($data[$key]);
		}
		if(empty($data)){
			return 0;
		}
		$minData = min($data);
		return $minData;
	}

    /**
     * 图片绝对地址处理
     * @param string $str
     * @return mixed|string
     */
    public static function descIMgReplace($str = '')
    {
        return !empty($str) ? str_replace('src="/images', 'src="' . env('STATIC_HOST') . 'images', $str) : $str;
    }

    /**
     * 快递查询
     * @param string $gsType 物流公司代码
     * @param string $number 快递单号
     */
    public static function kuaidicx($gsType = '', $number = '')
    {
        try {
            if (empty($gsType)) {
                throw new \Exception('快递公司为空');
            }
            if (empty($number)) {
                throw new \Exception('快递单号为空');
            }
            $cacheKey = md5($gsType . '|' . $number);
            $exData = cache()->get($cacheKey);
            if (empty($exData)) {
                $postData = array();
                $postData["customer"] = '3DC558DE49E9F55F578F3800C200DFD8';
                $key = 'jBvFiMUo6011';
                $params = array(
                    'com' => $gsType,//快递公司编码
                    'num' => $number,//快递单号
                );
                $postData["param"] = json_encode($params);

                $apiUrl = 'http://poll.kuaidi100.com/poll/query.do';
                $postData["sign"] = md5($postData["param"] . $key . $postData["customer"]);
                $postData["sign"] = strtoupper($postData["sign"]);
                $oStr = "";
                foreach ($postData as $k => $v) {
                    $oStr .= "$k=" . urlencode($v) . "&";        //默认UTF-8编码格式
                }
                $postData = substr($oStr, 0, -1);
                $result = \Helper\CFunctionHelper::curlHttp($apiUrl, $postData, 'POST');
                $resArr = json_decode($result, true);
                if (isset($resArr['status']) && $resArr['status'] == 200) {
                    $res = [
                        'status' => 1,
                        'data'   => [
                            'state'   => $resArr['state'],
                            'ischeck' => $resArr['ischeck'],
                            'list'    => $resArr['data'],
                        ],
                    ];
                    cache()->set($cacheKey, json_encode($res), 60 * 60 * 2);//缓存两小时
                } else {
                    $msg = isset($resArr['message']) ? $resArr['message'] : '查询失败，请隔段时间再查';
                    throw new \Exception($msg);
                }
            } else {
                $res = json_decode($exData, true);
            }
        } catch (\Exception $e) {
            $res = array(
                'status'  => 0,
                'message' => $e->getMessage(),
            );
        }
        return $res;
    }

    /** curl 请求接口
     * @param string $url
     * @param array $data
     * @return mixed
     */
    public static function curlHttp($url, $data = '', $method = 'GET')
    {

        $curl = curl_init(); // 启动一个CURL会话
        curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 对认证证书来源的检查
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false); // 从证书中检查SSL加密算法是否存在
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            curl_setopt($curl, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']); // 模拟用户使用的浏览器
        }
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1); // 自动设置Referer
        if ($method == 'POST') {
            curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
            if ($data != '') {
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data); // Post提交的数据包
            }
        }
        curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
        curl_setopt($curl, CURLOPT_HEADER, 0); // 显示返回的Header区域内容
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
        $tmpInfo = curl_exec($curl); // 执行操作
        curl_close($curl); // 关闭CURL会话
        return $tmpInfo;
    }

    /**
     * 获得团购的状态
     *
     * @access  public
     * @param   array
     * @return  integer
     */
    public static function groupBuyStatus($groupBuy)
    {
        $now = time();
        switch ($groupBuy['is_finished']) {
            case 0:
                /* 未处理 */
                if ($now < $groupBuy['start_time']) {
                    $status = \Enum\EnumKeys::GBS_PRE_START;
                } elseif ($now > $groupBuy['end_time']) {
                    $status = \Enum\EnumKeys::GBS_FINISHED;
                } else {
                    if ($groupBuy['restrict_amount'] == 0 || $groupBuy['valid_goods'] < $groupBuy['restrict_amount']) {
                        $status = \Enum\EnumKeys::GBS_UNDER_WAY;
                    } else {
                        $status = \Enum\EnumKeys::GBS_FINISHED;
                    }
                }
                break;
            case \Enum\EnumKeys::GBS_SUCCEED:
                /* 已处理，团购成功 */
                $status = \Enum\EnumKeys::GBS_SUCCEED;
                break;
            case \Enum\EnumKeys::GBS_FAIL:
                /* 已处理，团购失败 */
                $status = \Enum\EnumKeys::GBS_FAIL;
                break;


        }
        return $status;
    }

    /***
     * 短信发送
     * @author: colin
     * @date: 2018/11/23 14:37
     * @param array $mobiles
     * @param string $message
     * @param $userId
     * @param string $remark
     * @return mixed
     */
    public static function send($mobiles = array(), $message = '', $userId, $remark = '')
    {
        $env = strtolower(env('APP_ENV'));
        $mobiles = implode(',', $mobiles);
        if ($env == 'product') {//生产环境才发送短信
            $post_data = array();
            $post_data['cpName'] = 'xingfujiabeiyx';
            $post_data['cpPwd'] = 'b123456';
            $post_data['msg'] = $message;//'您的手机验证码：123456';
            $post_data['phones'] = $mobiles;
            $url = 'http://api.itrigo.net/mt.jsp';
            $options = array(
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HEADER         => false,
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => http_build_query($post_data),
            );
            $ch = curl_init($url);
            curl_setopt_array($ch, $options);
            $result = curl_exec($ch);
            curl_close($ch);
        } else {
            $result = 0;
        }
        $sendInfo = [
            'user_id'       => $userId,
            'mobile_number' => $mobiles,
            'content'       => $message,
            'result'        => ($result == 0) ? 1 : 0,
            'dfrom'         => 1,
            'remark'        => $remark,
        ];
        \App\Models\SmsSendLog::create($sendInfo);
        return $result;
    }

	/**
	 * 商户支付手机验证码
	 * @author: colin
	 * @date: 2018/12/3 10:19
	 * @param $mobile
	 * @param $mobileCode
	 * @param $userId
	 * @return bool
	 */
    public static function sendMobileSmsCode($mobile, $mobileCode,$userId){
		$isTest = self::isLocal();
		if (!$isTest) {
			if (is_array($mobile)) {
				$mobile = implode(',', $mobile);
			}
			$params = array(
				'code' => $mobileCode,
			);
			$tplcode = config('sms.captcha.tplCode');
			$result = \Library\sendMobileSmsAliyun::getInstance()->sendSms($mobile, $params, $tplcode);
			if ($result && $result->Code == 'OK') {
				$res = true;
			} else {
				$res = false;
			}
		} else {
			$res = true;
		}

		try {
			$logArr = array(
				'user_id'       => intval($userId),
				'mobile_number' => $mobile,
				'content'       => $mobileCode,
				'result'        => ($res == true) ? 1 : 0,
			);
			if ($res == false && $result && isset($result->Message)) {
				$logArr['remark'] = $result->Message;
			}
			\App\Models\SmsSendLog::create($logArr);
		} catch (\Exception $e) {
			return false;
		}
		return $res;
	}
	/**
	 * 发送短信-记录日志
	 * @author: colin
	 * @date: 2019/6/5 16:47
	 * @param string $mobileNumber    手机号码
	 * @param string $sendContent     发送的短信内容
	 * @param string $orderSn         订单号
	 * @param int $sign               是否签名
	 * @return bool|int
	 */
	public static function sendMobileSms($mobileNumber = '', $sendContent = '', $orderSn = '', $sign = 1)
	{
		if (!empty($mobileNumber) && !empty($sendContent)) {
			if (!is_array($mobileNumber)) {
				$mobileNumber = explode(',', $mobileNumber);
			}
			if ($sign == 1) {
				$sendContent = '【幸福加焙】' . $sendContent;
			}
			$res = 1;
			$data = [
				'user_id'       => intval(\Request::input('uid')),
				'mobile_number' => implode(',', $mobileNumber),
				'content'       => $sendContent,
				'order_sn'      => $orderSn,
				'result'        => ($res == true) ? 1 : 0,
			];
			\App\Models\SmsSendLog::create($data);
			return $res;
		}
		return false;
	}
	/**
	 * 取得收货人信息
	 * @param   int     $userId    用户编号
	 * @return  array
	 */
	public static function getConsignee($userId)
	{
		if(empty($userId))
			return [];
		$arr = \App\Models\UsersAddress::from('ecs_user_address as ua')->join('ecs_users as u','ua.address_id','=','u.address_id')->selectRaw("ua.*")->where('u.user_id',$userId)->where('ua.user_id',$userId)->get()->toArray();
		if(empty($arr)){
			$arr = \App\Models\UsersAddress::where('user_id',$userId)->first()->toArray();
		}
		return $arr;

	}
	/**
	 * 根据订单中的商品总额来获得包装的费用
	 *
	 * @access  public
	 * @param   integer $pack_id
	 * @param   float   $goods_amount
	 * @return  float
	 */
	public static function packFee($packId, $goodsAmount)
	{
		$pack = self::packInfo($packId);

		$val = (floatval($pack['free_money']) <= $goodsAmount && $pack['free_money'] > 0) ? 0 : floatval($pack['pack_fee']);

		return $val;
	}
	/**
	 * 取得包装信息
	 * @param   int     $pack_id    包装id
	 * @return  array   包装信息
	 */
	public static function packInfo($packId)
	{
		$result = \App\Models\Pack::where('pack_id',$packId)->first()->toArray();
		return $result;
	}
	/**
	 * 根据订单中商品总额获得需要支付的贺卡费用
	 *
	 * @access  public
	 * @param   integer $card_id
	 * @param   float   $goods_amount
	 * @return  float
	 */
	public static function cardFee($cardId, $goodsAmount)
	{
		$card = self::cardInfo($cardId);
		return ($card['free_money'] <= $goodsAmount && $card['free_money'] > 0) ? 0 : $card['card_fee'];
	}
	/**
	 * 取得贺卡信息
	 * @param   int     $card_id    贺卡id
	 * @return  array   贺卡信息
	 */
	public static function cardInfo($cardId)
	{
		$result = \App\Models\Card::where('card_id',$cardId)->first()->toArray();
		return $result;
	}
	/**
	 * 取得某配送方式对应于某收货地址的区域信息
	 * @param   int     $shipping_id        配送方式id
	 * @param   array   $region_id_list     收货人地区id数组
	 * @return  array   配送区域信息（config 对应着反序列化的 configure）
	 */
	public static function shippingAreaInfo($shippingId, $regionIdList)
	{
		$row = \App\Models\Shipping::join('ecs_shipping_area as a','ecs_shipping.shipping_id','=','a.shipping_id')->join('ecs_area_region as r','r.shipping_area_id','=','a.shipping_area_id')
			->selectRaw("ecs_shipping.shipping_code, ecs_shipping.shipping_name, ecs_shipping.shipping_desc, ecs_shipping.insure, ecs_shipping.support_cod, a.configure ")
			->where('ecs_shipping.shipping_id',$shippingId)
			->where('ecs_shipping.enabled',1)
			->whereIn('ecs_shipping.shipping_id',$regionIdList)
			->get()->toArray();
		if (!empty($row))
		{
			$shippingConfig = self::unserializeConfig($row['configure']);
			if (isset($shippingConfig['pay_fee']))
			{
				if (strpos($shippingConfig['pay_fee'], '%') !== false)
				{
					$row['pay_fee'] = floatval($shippingConfig['pay_fee']) . '%';
				}
				else
				{
					$row['pay_fee'] = floatval($shippingConfig['pay_fee']);
				}
			}
			else
			{
				$row['pay_fee'] = 0.00;
			}
		}

		return $row;
	}
	/**
	 * 处理序列化的支付、配送的配置参数
	 * 返回一个以name为索引的数组
	 *
	 * @access  public
	 * @param   string       $cfg
	 * @return  void
	 */
	public static function unserializeConfig($cfg)
	{
		if (is_string($cfg) && ($arr = unserialize($cfg)) !== false)
		{
			$config = [];

			foreach ($arr AS $key => $val)
			{
				$config[$val['name']] = $val['value'];
			}

			return $config;
		}
		else
		{
			return false;
		}
	}
	/**
	 * 格式化重量：小于1千克用克表示，否则用千克表示
	 * @param   float $weight 重量
	 * @return  string  格式化后的重量
	 */
	public static function formatedWeight($weight)
	{
		$weight = round(floatval($weight), 3);
		if ($weight > 0) {
			if ($weight < 1) {
				/* 小于1千克，用克表示 */
				return intval($weight * 1000) .'克';
			} else {
				/* 大于1千克，用千克表示 */
				return $weight . '千克';
			}
		} else {
			return 0;
		}
	}
	/**
	 * 计算积分的价值（能抵多少钱）
	 * @param   int     $integral   积分
	 * @return  float   积分价值
	 */
	public static function valueOfIntegral($integral)
	{
		$CFG = \Enum\EnumLang::loadConfig();
		$scale = floatval($CFG['integral_scale']);
		return $scale > 0 ? round(($integral / 100) * $scale, 2) : 0;
	}
	/**
	 *  生成一个用户自定义时区日期的GMT时间戳
	 *
	 * @access  public
	 * @param   int     $hour
	 * @param   int     $minute
	 * @param   int     $second
	 * @param   int     $month
	 * @param   int     $day
	 * @param   int     $year
	 *
	 * @return void
	 */
	public static function localMktime($hour = NULL , $minute= NULL, $second = NULL,  $month = NULL,  $day = NULL,  $year = NULL)
	{
		$CFG = \Enum\EnumLang::loadConfig();
		$timezone = session()->get('timezone');
		$timezone = $timezone ?? $CFG['timezone'];
		/**
		 * $time = mktime($hour, $minute, $second, $month, $day, $year) - date('Z') + (date('Z') - $timezone * 3600)
		 * 先用mktime生成时间戳，再减去date('Z')转换为GMT时间，然后修正为用户自定义时间。以下是化简后结果
		 **/
		$time = mktime($hour, $minute, $second, $month, $day, $year) - $timezone * 3600;

		return $time;
	}
	/**
	 * 将GMT时间戳格式化为用户自定义时区日期
	 *
	 * @param  string       $format
	 * @param  integer      $time       该参数必须是一个GMT的时间戳
	 *
	 * @return  string
	 */

	public static function localDate($format, $time = NULL)
	{
		$CFG = \Enum\EnumLang::loadConfig();
		$timezone = session()->get('timezone');
		$timezone = $timezone ?? $CFG['timezone'];

		if ($time === NULL)
		{
			$time = time();
		}
		elseif ($time <= 0)
		{
			return '';
		}

		$time += ($timezone * 3600);

		return date($format, $time);
	}
	/**
	 * 获得用户所在时区指定的时间戳
	 *
	 * @param   $timestamp  integer     该时间戳必须是一个服务器本地的时间戳
	 *
	 * @return  array
	 */
	public static function localGettime($timestamp = NULL)
	{
		$tmp = self::localGetdate($timestamp);
		return $tmp[0];
	}
	/**
	 * 获得用户所在时区指定的日期和时间信息
	 *
	 * @param   $timestamp  integer     该时间戳必须是一个服务器本地的时间戳
	 *
	 * @return  array
	 */
	public static function localGetdate($timestamp = NULL)
	{
		$CFG = \Enum\EnumLang::loadConfig();
		$timezone = session()->get('timezone');
		$timezone = $timezone ?? $CFG['timezone'];

		/* 如果时间戳为空，则获得服务器的当前时间 */
		if ($timestamp === NULL)
		{
			$timestamp = time();
		}

		$gmt        = $timestamp - date('Z');       // 得到该时间的格林威治时间
		$local_time = $gmt + ($timezone * 3600);    // 转换为用户所在时区的时间戳

		return getdate($local_time);
	}
	/**
	 * 得到新订单号
	 * @return  string
	 */
	public static function getOrderSn()
	{
		/* 选择一个随机的方案 */
		mt_srand((double)microtime() * 1000000);
		$orderSn = self::localDate('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
		return $orderSn;
	}

	/**
	 * 记录帐户变动
	 * @param   int $user_id 用户id
	 * @param   float $user_money 可用余额变动
	 * @param   float $frozen_money 冻结余额变动
	 * @param   int $rank_points 等级积分变动
	 * @param   int $pay_points 消费积分变动
	 * @param   string $change_desc 变动说明
	 * @param   int $change_type 变动类型：参见常量文件
	 * @return  void
	 */
	public static function logAccountChange($userId, $userMoney = 0, $frozenMoney = 0, $rankPoints = 0, $payPoints = 0, $changeDesc = '', $changeType = \Enum\EnumKeys::ACT_OTHER)
	{
		/* 插入帐户变动记录 */
		$accountLog = [
			'user_id'      => $userId,
			'user_money'   => $userMoney,
			'frozen_money' => $frozenMoney,
			'rank_points'  => $rankPoints,
			'pay_points'   => $payPoints,
			'change_time'  => time(),
			'change_desc'  => $changeDesc,
			'change_type'  => $changeType,
		];
		\App\Models\AccountLog::create($accountLog);
		/* 更新用户信息 */
        $userInfo = \App\Models\Users::where('user_id',$userId)->first();
		$userInfo->user_money = $userInfo->user_money+$userMoney;
		$userInfo->frozen_money = $userInfo->frozen_money+$frozenMoney;
		$userInfo->rank_points = $userInfo->rank_points+$rankPoints;
		$userInfo->pay_points = $userInfo->pay_points+$payPoints;
		$userInfo->save();

	}
	public static function logAccountChangeTwo($userId, $userMoney = 0, $orderSn = 0, $storesId = 0, $changeDesc = '', $bonusId = 0)
	{
		/* 插入帐户变动记录 */
		$accountLog = [
			'user_id'     => $userId,
			'user_money'  => $userMoney,
			'order_sn'    => $orderSn,
			'stores_id'   => $storesId,
			'bonus_id'    => intval($changeDesc),
			'change_time' => time(),
			'change_desc' => $bonusId,
		];
		\App\Models\AccountLogBonus::create($accountLog);
	}
	/**
	 * 检查收货人信息是否完整
	 * @param   array   $consignee  收货人信息
	 * @param   int     $flow_type  购物流程类型
	 * @return  bool    true 完整 false 不完整
	 */
	public static function checkConsigneeInfo($consignee, $flowType,$token)
	{
		if (self::existRealGoods(0, $flowType,$token))
		{
			/* 如果存在实体商品 */
			$res = !empty($consignee['consignee']) && !empty($consignee['country']);
			if ($res)
			{
				if (empty($consignee['province']))
				{
					/* 没有设置省份，检查当前国家下面有没有设置省份 */
					$pro = self::getRegions(1, $consignee['country']);
					$res = empty($pro);
				}
				elseif (empty($consignee['city']))
				{
					/* 没有设置城市，检查当前省下面有没有城市 */
					$city = self::getRegions(2, $consignee['province']);
					$res = empty($city);
				}
				elseif (empty($consignee['district']))
				{
					$dist = self::getRegions(3, $consignee['city']);
					$res = empty($dist);
				}
			}
			return $res;
		}
		else
		{
			/* 如果不存在实体商品 */
			return !empty($consignee['consignee']);
		}
	}
	/**
	 * 查询购物车（订单id为0）或订单中是否有实体商品
	 * @param   int     $order_id   订单id
	 * @param   int     $flow_type  购物流程类型
	 * @return  bool
	 */
	public static function existRealGoods($orderid = 0, $flowtype = EnumKeys::CART_GENERAL_GOODS,$sessid)
	{
		if ($orderid <= 0)
		{
			$count = \App\Models\Cart::where(['session_id'=>$sessid, 'is_real'=>1, 'rec_type'=>$flowtype])->count();
		}
		else
		{
			$count = \App\Models\OrderGoods::where(['order_id'=>$orderid, 'is_real'=>1])->count();
		}

		return $count > 0;
	}
	/**
	 * 获得指定国家的所有省份
	 *
	 * @access      public
	 * @param       int     country    国家的编号
	 * @return      array
	 */
	public static function getRegions($type = 0, $parent = 0)
	{
		$region = \App\Models\Region::select('region_id', 'region_name')->where(['region_type'=>$type,'parent_id'=>$parent])->get()->toArray();
		return $region;
	}

	/**
	 * 用户密码规则
	 * @author: colin
	 * @date: 2019/1/8 9:36
	 * @param $password
	 * @param $salt
	 * @return string
	 */
	public static function setPassWord($password,$salt)
	{
		return md5(md5($password).$salt);
	}
	/**
	 * 根据经纬度和半径计算出范围
	 * @param string $lat 纬度
	 * @param String $lng 经度
	 * @param float $radius 半径
	 * @return Array 范围数组
	 */
	public static function calcScope($lat, $lng, $radius)
	{
		$degree = (24901 * 1609) / 360.0;
		$dpmLat = 1 / $degree;
		$pi = pi();

		$radiusLat = $dpmLat * $radius;
		$minLat = $lat - $radiusLat;       // 最小纬度
		$maxLat = $lat + $radiusLat;       // 最大纬度

		$mpdLng = $degree * cos($lat * ($pi / 180));
		$dpmLng = 1 / $mpdLng;
		$radiusLng = $dpmLng * $radius;
		$minLng = $lng - $radiusLng;      // 最小经度
		$maxLng = $lng + $radiusLng;      // 最大经度

		$scope = [
			'min_lat' => $minLat,
			'max_lat' => $maxLat,
			'min_lng' => $minLng,
			'max_lng' => $maxLng,
		];
		return $scope;
	}
	/**
	 * 计算两个坐标之间的距离(米)--和百度地图误差在0.01米左右
	 * @param float $fP1Lat 起点(纬度)
	 * @param float $fP1Lon 起点(经度)
	 * @param float $fP2Lat 终点(纬度)
	 * @param float $fP2Lon 终点(经度)
	 * @return float  （千米）
	 */
	public static function distanceBetween($fP1Lat, $fP1Lon, $fP2Lat, $fP2Lon)
	{
		$distance = 6371.012 *
			acos(cos(acos(-1) / 180 * $fP1Lat) *
				cos(acos(-1) / 180 * $fP2Lat) *
				cos(acos(-1) / 180 * $fP1Lon - acos(-1) / 180 * $fP2Lon) +
				sin(acos(-1) / 180 * $fP1Lat) *
				sin(acos(-1) / 180 * $fP2Lat)) * 1;
//    return sprintf("%.2f", $distance);
		//保留两位小数，不四舍五入
		return sprintf("%.1f", substr(sprintf("%.3f", $distance), 0, -2));
	}
	/**
	 * 取得商品最终使用价格
	 *
	 * @param   string $goodsId 商品编号
	 * @param   string $goodsNum 购买数量
	 * @param   boolean $isSpecPrice 是否加入规格价格
	 * @param   mix $spec 规格ID的数组或者逗号分隔的字符串
	 *
	 * @return  商品最终购买价格
	 */
	public static function getFinalPrice($goodsId, $goodsNum = '1', $isSpecPrice = false, $spec = [])
	{
		$finalPrice = '0'; //商品最终购买价格
		$volumePrice = '0'; //商品优惠价格
		$promotePrice = '0'; //商品促销价格
		$userPrice = '0'; //商品会员价格

		//取得商品优惠价格列表
		$priceList = self::getVolumePriceList($goodsId, '1');
		foreach ($priceList as $value) {
			if ($goodsNum >= $value['number']) {
				$volumePrice = $value['price'];
			}
		}
		//取得商品促销价格列表
		$goods = \App\Models\Goods::selectRaw("shop_price,promote_price,promote_start_date,promote_end_date")
			->where(['goods_id' => $goodsId, 'is_on_sale' => 1, 'is_delete' => 0])
			->first();
		/* 修正促销价格 */
		if ($goods->promote_price > 0) {
			$promotePrice = self::isPromote($goods->promote_price, $goods->promote_start_date, $goods->promote_end_date);
		}
		//比较商品 会员价格，优惠价格,促销价格 获取大于0的最小值
		$finalPrice = self::getMinAll([$goods->shop_price, $volumePrice, $promotePrice]);
		//如果需要加入规格价格
		if ($isSpecPrice) {
			if (!empty($spec)) {
				$specPrice = self::specPrice($spec);
				$finalPrice += $specPrice;
			}
		}
		//返回商品最终购买价格
		return $finalPrice;
	}
	/**
	 * 取得商品优惠价格列表
	 *
	 * @param   string $goodsId 商品编号
	 * @param   string $priceType 价格类别(0为全店优惠比率，1为商品优惠价格，2为分类优惠比率)
	 *
	 * @return  优惠价格列表
	 */
	public static function getVolumePriceList($goodsId, $priceType = '1')
	{
		$volumePrice = [];
		$res = \App\Models\VolumePrice::where(['goods_id' => $goodsId, 'price_type' => $priceType])->orderBy('volume_number', 'desc')->get()->toArray();
		foreach ($res as $k => $v) {
			$volumePrice[] = [
				'number' => $v['volume_number'],
				'price' => $v['volume_price'],
				'format_price' => \Helper\CFunctionHelper::priceFormat($v['volume_price']),
			];
		}
		return $volumePrice;
	}
	/***
	 * 规格价格
	 * @author: colin
	 * @date: 2018/11/27 13:43
	 * @param $spec
	 * @return float|int|type
	 */
	public static function specPrice($spec)
	{
		if (empty($spec))
			return 0;
		$specNew = $spec;
		try {
			$danGao = 0;
			foreach ($spec as $key => $val) {
				$attrId = \App\Models\GoodsAttr::where('goods_attr_id', $val)->value('attr_id');
				if ($attrId == 213) {
					$danGao = 1;
					$specBangId = $val;
					array_splice($specNew, $key, 1);
				}
			}
			if ($danGao > 0) {
				$priceOther = \App\Models\GoodsAttr::selectRaw("SUM(attr_price) AS attr_price")->whereIn('goods_attr_id', $specNew)->first();
				$priceOther = $priceOther->attr_price;
				$resBang = \App\Models\GoodsAttr::select('attr_price', 'attr_value')->where('goods_attr_id', $specBangId)->firstOrFail();
				$resBang->attr_price = str_replace("磅", "", $resBang->attr_price);
				$priceBang = floatval($resBang->attr_price);
				$priceBangValue = floatval($resBang->attr_value);
				$price = $priceBang + $priceOther * $priceBangValue;
			} else {
				$price = \App\Models\GoodsAttr::selectRaw("SUM(attr_price) AS attr_price")->whereIn('goods_attr_id', $spec)->firstOrFail();
				$price = $price->attr_price;
			}
		} catch (\Exception $e) {
			return false;
		}
		return $price;
	}
	/**
	 * 取得真正的支付id
	 * @param int $pays
	 * @return int
	 */
	public static function getRealPamentId($pays = 0)
	{
		switch ($pays) {
			case 0://支付宝
				$pid = 6;
				break;
			case 1://微信
				$pid = 5;
				break;
			case 2://余额
				$pid = 1;
				break;
			default:
				$pid = 0;
		}
		return $pid;
	}
	/**
	 * 查询配送区域属于哪个办事处管辖
	 * @param   array   $regions    配送区域（1、2、3、4级按顺序）
	 * @return  int     办事处id，可能为0
	 */
	public static function getAgencyByRegions($regions)
	{
		try{
			if (!is_array($regions) || empty($regions))
				return 0;
			$result= \App\Models\Region::whereIn('region_id',$regions)->where('region_id','>','0')->where('agency_id','>',0)->get()->toArray();
			if(empty($result))
				return 0;
			$data = array_column($result,'agency_id','region_id');
			for ($i = count($regions) - 1; $i >= 0; $i--)
			{
				if (isset($data[$regions[$i]]))
				{
					return $data[$regions[$i]];
				}
			}
		}catch(\Exception $e){
			return 0;
		}
	}

	/**
	 * 异常抛出
	 * @author: colin
	 * @date: 2019/1/28 11:16
	 * @param $status
	 * @param $message
	 * @throws \Exception
	 */
	public static function throwError($status,$message)
	{
		if($status === false){
			throw new \Exception($message);
		}
	}
	/**
	 * 获得订单中的费用信息
	 *
	 * @access  public
	 * @param   array $order
	 * @param   array $goods
	 * @param   array $consignee
	 * @param   bool $isGbDeposit 是否团购保证金（如果是，应付款金额只计算商品总额和支付费用，可以获得的积分取 $giftIntegral）
	 * @return  array
	 */
	public static function orderFee($order, $goods)
	{
		/* 初始化订单的扩展code */
		$order['extension_code'] = '';
		$total = [
			'real_goods_count' => 0,
			'gift_amount' => 0,
			'goods_price' => 0,
			'market_price' => 0,
			'discount' => 0,
			'pack_fee' => 0,
			'card_fee' => 0,
			'shipping_fee' => 0,
			'shipping_insure' => 0,
			'integral_money' => 0,
			'bonus' => 0,
			'surplus' => 0,
			'cod_fee' => 0,
			'pay_fee' => 0,
			'goods_price_zy' => 0,
			'tax' => 0,
		];

		/* 商品总价 */
		foreach ($goods AS $val) {
			$total['real_goods_count']++;
			$total['goods_price'] += $val['goods_price'] * $val['goods_number'];
			$total['market_price'] += $val['market_price'] * $val['goods_number'];
			$val['goods_price'] = $val['exceed_promote_price'];
			//计算直营店价格
			$total['goods_price_zy'] += $val['goods_price'] * $val['goods_number'];
		}
		$total['saving'] = $total['market_price'] - $total['goods_price'];
		$total['save_rate'] = $total['market_price'] ? round($total['saving'] * 100 / $total['market_price']) . '%' : 0;

		$total['goods_price_formated'] = self::priceFormat($total['goods_price'], false);
		$total['market_price_formated'] = self::priceFormat($total['market_price'], false);
		$total['saving_formated'] = self::priceFormat($total['saving'], false);
		$total['discount_formated'] = self::priceFormat($total['discount'], false);

		/* 税额 */
		if (!empty($order['need_inv']) && $order['inv_type'] != '') {
			/* 查税率 */
			$rate = 0;
			$CFG = \Enum\EnumLang::loadConfig();
			foreach ($CFG['invoice_type']['type'] as $key => $type) {
				if ($type == $order['inv_type']) {
					$rate = floatval($CFG['invoice_type']['rate'][$key]) / 100;
					break;
				}
			}
			if ($rate > 0) {
				$total['tax'] = $rate * $total['goods_price'];
			}
		}
		$total['tax_formated'] = self::priceFormat($total['tax'], false);

		/* 包装费用 */
		if (!empty($order['pack_id'])) {
			$total['pack_fee'] = self::packFee($order['pack_id'], $total['goods_price']);
		}
		$total['pack_fee_formated'] = self::priceFormat($total['pack_fee'], false);

		/* 贺卡费用 */
		if (!empty($order['card_id'])) {
			$total['card_fee'] = self::cardFee($order['card_id'], $total['goods_price']);
		}
		$total['card_fee_formated'] = self::priceFormat($total['card_fee'], false);

		/* 配送费用 */
		$shippingCodFee = NULL;
		$total['shipping_fee_formated'] = self::priceFormat($total['shipping_fee'], false);
		$total['shipping_insure_formated'] = self::priceFormat($total['shipping_insure'], false);

		// 购物车中的商品能享受红包支付的总额
//		$bonusAmount = $this->computeDiscountAmount();
		$bonusAmount = 0;
		// 红包和积分最多能支付的金额为商品总额
		$maxAmount = $total['goods_price'] == 0 ? $total['goods_price'] : $total['goods_price'] - $bonusAmount;

		/* 计算订单总额 */
		if ($order['extension_code'] == 'group_buy' ) {//&& $groupBuy['deposit'] > 0
			$total['amount'] = $total['goods_price'];
		} else {
			$total['amount'] = $total['goods_price'] - $total['discount'] + $total['tax'] + $total['pack_fee'] + $total['card_fee'] +
				$total['shipping_fee'] + $total['shipping_insure'] + $total['cod_fee'];
		}

		/* 余额 */
		$order['surplus'] = $order['surplus'] > 0 ? $order['surplus'] : 0;
		if ($total['amount'] > 0) {
			if (isset($order['surplus']) && $order['surplus'] > $total['amount']) {
				$order['surplus'] = $total['amount'];
				$total['amount'] = $total['amount'];
			} else {
				$total['amount'] -= floatval($order['surplus']);
			}
		} else {
			$order['surplus'] = 0;
			$total['amount'] = 0;
		}
		$total['surplus'] = $order['surplus'];
		$total['surplus_formated'] = self::priceFormat($order['surplus'], false);

		/* 积分 */
		$order['integral'] = $order['integral'] > 0 ? $order['integral'] : 0;
		if ($total['amount'] > 0 && $maxAmount > 0 && $order['integral'] > 0) {
			$integralMoney = self::valueOfIntegral($order['integral']);

			// 使用积分支付
			$useIntegral = min($total['amount'], $maxAmount, $integralMoney); // 实际使用积分支付的金额
			$total['amount'] -= $useIntegral;
			$total['integral_money'] = $useIntegral;
			$order['integral'] = self::valueOfIntegral($useIntegral);
		} else {
			$total['integral_money'] = 0;
			$order['integral'] = 0;
		}
		$total['integral'] = $order['integral'];
		$total['integral_formated'] = self::priceFormat($total['integral_money'], false);

		$flowType = EnumKeys::CART_GENERAL_GOODS;

		/* 支付费用 */
		if (!empty($order['pay_id']) && ($total['real_goods_count'] > 0 || $flowType != EnumKeys::CART_EXCHANGE_GOODS)) {
			$total['pay_fee'] = self::payFee($order['pay_id'], $total['amount'], $shippingCodFee);
		}
		$total['pay_fee_formated'] = self::priceFormat($total['pay_fee'], false);

		$total['amount'] += $total['pay_fee']; // 订单总额累加上支付费用
		$total['amount_formated'] = self::priceFormat($total['amount'], false);

		//计算直营 加盟 价格
		if (isset($total['goods_price_zy']) && $total['goods_price_zy'] > 0) {
			$ortherAmount = $total['amount'] - $total['goods_price'];
			$total['amount_zy'] = $total['goods_price_zy'] + $ortherAmount;
		} else {
			$total['amount_zy'] = $total['amount'];
		}

		$total['will_get_bonus'] = 0;
		$total['will_get_integral'] = 0;
		$total['formated_goods_price'] = self::priceFormat($total['goods_price'], false);
		$total['formated_market_price'] = self::priceFormat($total['market_price'], false);
		$total['formated_saving'] = self::priceFormat($total['saving'], false);


		return $total;
	}
	/**
	 * 获得订单需要支付的支付费用
	 *
	 * @access  public
	 * @param   integer $paymentId
	 * @param   float $orderAmount
	 * @param   mix $codFee
	 * @return  float
	 */
	public static function payFee($paymentId, $orderAmount, $codFee = null)
	{
		$payment = self::paymentInfo($paymentId);
		$rate = ($payment['is_cod'] && !is_null($codFee)) ? $codFee : $payment['pay_fee'];

		if (strpos($rate, '%') !== false) {
			/* 支付费用是一个比例 */
			$val = floatval($rate) / 100;
			$payFee = $val > 0 ? $orderAmount * $val / (1 - $val) : 0;
		} else {
			$payFee = floatval($rate);
		}

		return round($payFee, 2);
	}
	/**
	 * 取得支付方式信息
	 * @param   int $payId 支付方式id
	 * @return  array   支付方式信息
	 */
	public static function paymentInfo($payId)
	{
		$payment = \App\Models\Payment::where(['pay_id' => $payId, 'enabled' => 1])->first()->toArray();
		return $payment;
	}
	/**
	 * 将支付LOG插入数据表
	 * @author: colin
	 * @date: 2019/5/28 14:42
	 * @param $id           订单编号
	 * @param $amount       订单金额
	 * @param int $type		 支付类型
	 * @param int $isPaid   是否已支付
	 * @return int
	 */
	public static function insertPayLog($id, $amount, $type = EnumKeys::PAY_SURPLUS, $isPaid = 0)
	{
		try {
			$data = [
				'order_id' => $id,
				'order_amount' => $amount,
				'order_type' => $type,
				'is_paid' => $isPaid,
			];
			$create = \App\Models\PayLog::create($data);
			$insertId = $create->log_id;
		} catch (\Exception $e) {
			return false;
		}
		return $insertId;
	}
	/**
	 * 获取门店自定义下架的商品id
	 * @author: colin
	 * @date: 2019/5/16 18:05
	 * @param int $gsId
	 * @param bool $cache
	 * @return array|mixed
	 * @throws \Exception
	 * @throws \Psr\SimpleCache\InvalidArgumentException
	 */
	public static function getOffGoods($gsId = 0, $cache = true)
	{
		if (empty($gsId) || empty(self::getGsAuth($gsId)))
			return [];
		$cacheName = EnumKeys::CACHE_GET_OFF_GOODS . $gsId;;
		$goodsId = $cache ? Cache::get($cacheName) : [];
		if (empty($goodsId)) {
			$goodsId = \App\Models\GoodStoreAttr::where('gs_id',$gsId)->pluck('goods_id');
			Cache::put($cacheName, $goodsId, 60);
		}
		return $goodsId;
	}

	/**
	 * 获取商户权限
	 * @author: colin
	 * @date: 2019/5/16 18:54
	 * @param int $gsId
	 * @return mixed
	 * @throws \Exception
	 * @throws \Psr\SimpleCache\InvalidArgumentException
	 */
	public static function getGsAuth($gsId = 0)
	{
		if (!empty($gsId)) {
			$cacheName = EnumKeys::CACHE_GET_GS_AUTH . $gsId;
			$data = Cache::get($cacheName);
			if (empty($data)) {
				$gsAuth = \App\Models\StoresUser::where('gs_id',$gsId)->value('gs_auth');
				$data = ['res' => $gsAuth];
				Cache::put($cacheName, $data, 60);
			}
			return $data['res'];
		}
	}
	/**
	 * 取得贺卡信息
	 * @param   int     $card_id    贺卡id
	 * @return  array   贺卡信息
	 */
	public static function addBonus($uid, $bounsSn, $bonusPass)
	{
		try{
			$result = ['status'  => 1];
			/* 查询幸福券信息 */
			$bonusInfo = \App\Models\UserBonus::where('bonus_sn',$bounsSn)->firstOrFail()->toArray();
			if(!empty($bonusInfo['user_id'])){
				$msg = ($bonusInfo['user_id']===$uid) ? '已经激活过了' : '已经被别人激活过了';
				throw new \Exception($msg);
			}
			if($bonusInfo['bonus_pass'] != md5($bonusPass.$bonusInfo['bonus_salt']))
				throw new \Exception('密码错误！');
			//红包没有被使用
			$bonusTime = \App\Models\BonusType::selectRaw("send_end_date,use_end_date,type_money")->where('type_id',$bonusInfo['bonus_type_id'])->first()->toArray();
			$endDate = $bonusTime['use_end_date'];
			$now = time();
			if ($bonusInfo['bonus_end_date'] > 0) {
				$end_date = $bonusInfo['bonus_end_date'];
			}
			if ($bonusInfo['bonus_start_date'] > 0) {
				$startDate = $bonusInfo['bonus_start_date'];
				if ($startDate > $now)
					throw new \Exception('请在有效期内(' . date('Y.m.d', $startDate) . '-' . date('Y.m.d', $endDate) . ')激活');
			}
			$bonusStatus = 1;
			if ($now > $endDate) {
				$bonusStatus = 3;
			}
			$bonusMoney = $bonusInfo['bonus_money'];
			//会员表跟新 start
			$dataInfo = [
				'user_id'        => $uid,
				'used_time'      => $now,
				'bonus_status'   => $bonusStatus,
				'balance'        => $bonusMoney,
				'bonus_end_date' => $endDate,
			];
			$update = \App\Models\UserBonus::where('user_id','<',1)->where('bonus_id',$bonusInfo['bonus_id'])->update($dataInfo);
			//会员表跟新 end
			if(empty($update))
				throw new \Exception('更新失败！');
		}catch(\Exception $e){
			$result = [
				'status'  => false,
				'message' => $e->getMessage(),
			];
		}
		return $result;
	}

	/**
	 * 根据商品id 判断是否促销商品
	 * @author: colin
	 * @date: 2019/5/23 15:50
	 * @param int $goodsId
	 * @return bool|mixed
	 */
	public static function isGoodsPromote($goodsId = 0)
	{
		try{
			$res = false;
			$time = time();

			if (empty($goodsId))
				return $res;
			$data = \App\Models\Goods::where('goods_id',$goodsId)->firstOrFail()->toArray();
			if ($data['is_promote'] == 1 && $time >= $data['promote_start_date'] && $time <= $data['promote_end_date'])
				$res =  true;
			return $res;
		}catch(\Exception $e){
			return false;
		}

	}

	/**
	 * 修改商户定义的库存
	 * @author: colin
	 * @date: 2019/5/23 16:03
	 * @param int $sellerId
	 * @param int $goodsId
	 * @param int $num
	 * @param bool $isPromote
	 * @param string $sizeId
	 * @param string $tastesId
	 * @return bool
	 */
	public static function updateGoodsStock($sellerId = 0, $goodsId = 0, $num = 1, $isPromote = false, $sizeId = '', $tastesId = '')
	{
		try{
			$gsAuth = self::getGsAuth($sellerId);
			if ($gsAuth) {
				$attrIds = '0';
				if (!empty($sizeId) && !empty($tastesId)) {
					$attrIds = $sizeId . '-' . $tastesId;
				} elseif (!empty($sizeId)) {
					$attrIds = $sizeId;
				} elseif (!empty($tastesId)) {
					$attrIds = $tastesId;
				}
				$zd = $isPromote ? 'num_promotion' : 'num';
				$where = ['gs_id'=>$sellerId, 'goods_id'=>$goodsId, 'attr_ids'=>$attrIds];
				$data = GoodsStock::where($where)->first();
				if(empty($data))
					return true;
				if($data->$$zd>0)
					$data->$$zd += $num;
				else
					$data->$$zd -= abs($num);
				$data->save();
			}
		}catch(\Exception $e){
			return false;
		}
		return true;
	}
	/**
	 * 记录订单操作记录
	 *
	 * @access  public
	 * @param   string $order_sn 订单编号
	 * @param   integer $order_status 订单状态
	 * @param   integer $shipping_status 配送状态
	 * @param   integer $pay_status 付款状态
	 * @param   string $note 备注
	 * @param   string $username 用户名，用户自己的操作则为 buyer
	 * @return  void
	 */
	public static function orderAction($orderId, $orderStatus, $shippingStatus, $payStatus, $note = '', $username = null, $place = 0)
	{
		try{
			$logInfo = [
				'order_id' => $orderId,
				'action_user' => $username,
				'order_status' => $orderStatus,
				'shipping_status' => $shippingStatus,
				'pay_status' => $payStatus,
				'action_place' => $place,
				'action_note' => $note,
				'log_time' => time(),
			];
			\App\Models\OrderAction::create($logInfo);
		}catch (\Exception $e){
			return false;
		}
		return true;
	}
	/**
	 * 返回调用栈string
	 * @author: colin
	 * @date: 2019/5/28 11:12
	 * @param $backTrace debug_backtrace()
	 * @return string
	 */
	public static function backtraceToString($backTrace) {
		$file = '';
		foreach ($backTrace as $vvv) {
			if (empty($vvv) || !isset($vvv['file'])) {
				continue;
			}
			$file .= "{$vvv['file']} ";
			$file .= isset($vvv['line']) ? $vvv['line'] . " \n " : "\n ";
		}
		return $file;
	}

	/**
	 * 记录微信支付日志
	 * @author: colin
	 * @date: 2019/6/4 19:12
	 * @param $data
	 * @param $name
	 */
	public static function wechatPayLog($data,$name){
		$name = $name.'_'.date('Ymd');
		$logger = new \Helper\CLoggerHelper(LOG_PATH . "WechatPay/" . date("Ym") . "/", $name);
		$logger->logError($data);

	}

	/***
	 * 商品支付-修改订单的支付状态
	 * @author: colin
	 * @date: 2019/6/5 14:41
	 */
	public static function orderPaid($logId, $payStatus = EnumKeys::PS_PAYED, $note = '')
	{
		try{
			$result = ['status'=>1];
			if(empty($logId))
				throw new \Exception('订单记录id为空！');
        	$payLog = \App\Models\PayLog::where('log_id',$logId)->firstOrFail()->toArray();
			if ($payLog['is_paid'] != 0) {
				throw new \Exception('订单记录状态已经支付！');
			}
			/* 修改此次支付操作的状态为已付款 */
			\App\Models\PayLog::where('log_id',$logId)->update(['is_paid'=>1]);
			switch($payLog['order_type']){
				case EnumKeys::PAY_ORDER:
					$order = \App\Models\OrderInfo::where(['order_id'=>$payLog['order_id']])->firstOrFail()->toArray();
					$orderSn = $order['order_sn'];
					$orderId = $order['order_id'];
					$userId = $order['user_id'];
					$userName = \App\Models\Users::where('user_id',$userId)->value('user_name');
					$orderRes = $order;
					if (!empty($orderRes['bonus_id'])) {//使用单卡
						$bonusInfo = self::bonusInfo($orderRes['bonus_id']);
						if (empty($bonusInfo) || $bonusInfo['user_id'] != $userId) {
							throw new \Exception('选择的幸福券不存在，请检查');
						}
						if ($bonusInfo['bonus_status'] > 1 || $bonusInfo['balance'] < $orderRes['bonus']) {
							throw new \Exception('选取的幸福券异常，请检查');
						}

						self::logAccountChangeTwo($userId, $orderRes['bonus'], $orderSn, $orderRes['order_pick_stores'], sprintf('支付订单 %s', $orderSn), $orderRes['bonus_id']);
						$umoney = $orderRes['bonus'];
						$balance = $bonusInfo['balance'] - $umoney;
						$ubData = [
							'used_money' => $bonusInfo['used_money'] + $umoney,
							'balance'    => $balance,
						];
						if ($balance == 0) {
							$ubData['bonus_status'] = 2;//已用完
						}
						// 更新卡数据
						\App\Models\UserBonus::where('bonus_id',$orderRes['bonus_id'])->update($ubData);
						$bonusCompany = $bonusInfo['bonus_company'] . '(幸福券)';
					}elseif (!empty($orderRes['bonus']) && empty($orderRes['bonus_id'])) {//可选择多卡
						$usedTotalMoney = 0;
						$orderBonusUsed = \App\Models\OrderBonusUser::where('order_sn',$orderSn)->where('status','<','1')->get()->toArray();
						$bonusCompanyArr = [];
						foreach ($orderBonusUsed as $item) {
							$bonusInfo = self::bonusInfo($item['bonus_id']);
							if (empty($bonusInfo) || $bonusInfo['user_id'] != $userId) {
								throw new Exception('选择的幸福券不存在，请检查');
							}
							if ($bonusInfo['bonus_status'] > 1 || $bonusInfo['balance'] < $item['used_money']) {
								throw new Exception('选取的幸福券异常，请检查');
							}
							$umoney = $item['used_money'];
							$balance = $bonusInfo['balance'] - $umoney;
							$ubData = [
								'used_money' => $bonusInfo['used_money'] + $umoney,
								'balance'    => $balance,
							];
							if ($balance == 0) {
								$ubData['bonus_status'] = 2;//已用完
							}
							// 更新卡数据
							\App\Models\UserBonus::where('bonus_id',$item['bonus_id'])->update($ubData);
							self::logAccountChangeTwo($userId, $orderRes['bonus'], $orderSn, $orderRes['order_pick_stores'], sprintf('支付订单 %s', $orderSn), $item['bonus_id']);
							$bonusCompanyArr[] = $bonusInfo['bonus_company'];
							$usedTotalMoney += $item['used_money'];
						}
						if (!empty($bonusCompanyArr)) {
							$bonusCompanyArr = array_unique($bonusCompanyArr);
							$bonusCompany = implode('|', $bonusCompanyArr) . '(幸福券)';
						}
						if ($orderRes['bonus'] != $usedTotalMoney) {
							throw new Exception('订单数据错误，请联系客服');
						}
						\App\Models\OrderBonusUser::where('order_sn',$orderSn)->update(['status'=>1,'change_time'=>date('Y-m-d H:i:s')]);
					}
					if(!isset($bonusCompany) || empty($bonusCompany)){
						$bonusCompany = '充值';
					}
					/* 修改订单状态为已付款 */
					$nowTime = time();
					$uData = array(
						'order_status'  => '5',
						'confirm_time'  => $nowTime,
						'pay_status'    => $payStatus,
						'pay_time'      => $nowTime,
						'bonus_company' => $bonusCompany,
						'last_cfm_time' => strtotime('+4 days', $nowTime + 8 * 3600),
					);
					$orderRes['is_shipping'] < 1 && $uData['shipping_status'] = 1;//自提类的，设置为发货
				    \App\Models\OrderInfo::where('order_id',$orderId)->update($uData);
					/* 记录订单操作记录 */
					self::orderAction($orderId, EnumKeys::OS_CONFIRMED, EnumKeys::SS_UNSHIPPED, $payStatus, $note, $userName);
					$payName = \App\Models\Payment::where('pay_id',$order['pay_id'])->value('pay_name');
					$payName = strip_tags($payName) . '充值';
					self::logAccountChangeTwo($order['user_id'], 0 - $order['surplus'], $orderSn, 0, $payName);//充值记录
					self::logAccountChangeTwo($order['user_id'], $order['surplus'], $orderSn, $order['order_pick_stores'], '支付订单' . $orderSn);//消费记录
					/* 如果需要，发短信 */
					$configData = EnumLang::loadConfig();
					if ($configData['sms_order_payed'] == '1' && $configData['sms_shop_mobile'] != '') {
						self::send($configData['sms_shop_mobile'],
							sprintf($configData['order_payed_sms'], $orderSn, $order['consignee'], $order['tel']), '', 13);
					}
					//给门店发送短信 start
					$stores = \App\Models\StoresUser::selectRaw("gs_id,gs_name,gs_address,gs_contacter,gs_login_name,gs_mobile")->where('gs_id',$order['order_pick_stores'])->firstOrFail()->toArray();
					if (!empty($stores['gs_mobile'])) {
						if ($orderRes['is_shipping'] == 1) {
							$isSendMsg = 1;
						} else {
							$cakeResList = \App\Models\OrderGoods::from("ecs_order_goods as a")
								->join("ecs_goods as b",'a.goods_id','=','b.goods_id')
								->join("ecs_category as c",'b.cat_id','=','c.cat_id')
								->join("ecs_order_info as d",'a.order_id','=','d.order_id')
								->selectRaw("c.cat_id,c.parent_id,c.is_send_msg")
							    ->whereRaw("d.order_sn='{$order['order_sn']}'")
							    ->groupBy("c.cat_id")
							    ->get()->toArray();
							$isSendMsg = 0;
							foreach ($cakeResList as $cakeRes) {
								if ($cakeRes['is_send_msg'] == 1) {
									$isSendMsg = 1;
								} elseif ($cakeRes['is_send_msg'] == 0 && $cakeRes['parent_id'] > 0) {
									$isSendMsg = \App\Models\Category::where('cat_id',$cakeRes['parent_id'])->value('is_send_msg');
								}
								if ($isSendMsg == 1) {
									break;
								}
							}
						}
						$mlist = explode('|', $stores['gs_mobile']);
						$gsMobiles = ($isSendMsg == 1) ? $mlist : [$mlist[0]];
						self::sendMobileSms($gsMobiles, $stores['gs_contacter'] . '，您好！您有一个新订单！订单编号是：' . $order['order_sn'] . '。', $order['order_sn']);
					}
					if (!empty($stores['gs_login_name'])) {
						$tools = new \Library\Third\Push\PushTools();
						$tools->notifyNewOrder($stores['gs_login_name']);
						\App\Models\StoresUser::where('gs_login_name',$stores['gs_login_name'])->update(['max_order_time'=>time()]);
					}
					//给门店发送短信 end
					break;
				default:
					break;
			}


		}catch(\Exception $e){
			$result = [
				'status'=>0,
				'msg'=>$e->getMessage(),
			];
		}
		return $result;
	}

	/**
	 * 取得红包信息
	 * @author: colin
	 * @date: 2019/6/5 15:22
	 * @param $bonusId   红包id
	 * @param $bonusSn   红包序列
	 */
	public static function bonusInfo($bonusId,$bonusSn='')
	{
		try{
			$where = $bonusId>0 ? "b.bonus_id='{$bonusId}'" : "b.bonus_sn='{$bonusSn}'";
			$bonus = \App\Models\BonusType::from("ecs_bonus_type as t")->join('ecs_user_bonus as b','t.type_id','=','b.bonus_type_id')
			->selectRaw("t.*, b.*")->whereRaw($where)->firstOrFail()->toArray();
			return $bonus;
		}catch(\Exception $e){
			return false;
		}
	}

}
