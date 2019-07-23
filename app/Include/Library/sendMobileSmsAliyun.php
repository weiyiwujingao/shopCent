<?php
/**
 * 阿里云发短信接口
 */
namespace Library;
use DB;
use App;
use Cookie;
use Enum\EnumKeys;
use Validator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use \Exception;
class sendMobileSmsAliyun
{
    private $accessKeyId = "";
    private $accessKeySecret = "";
    private static $instance = null;

    private function __construct()
    {
        $this->accessKeyId = 'LTAIUv6mUwz9q8Vc';
        $this->accessKeySecret = 'bTQQ0MqF7R75AoZ80HG2QoseJsrcR5';

    }

    /**
     *  单例
     * @return sendMobileSmsAliyun|null
     */
    static public function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }


    /**
     * 发送单个号码
     * @param string $phoneNumbers 手机号码
     * @param array $templateParam
     * @param string $templateCode 短信模板
     * @return bool|stdClass
     */
    public function sendSms($phoneNumbers = '', $templateParam = array(), $templateCode = '')
    {
        $params = array();
        // fixme 必填: 请参阅 https://ak-console.aliyun.com/ 取得您的AK信息
        $accessKeyId = $this->accessKeyId;
        $accessKeySecret = $this->accessKeySecret;

        // fixme 必填: 短信接收号码
        $params["PhoneNumbers"] = $phoneNumbers;

        // fixme 必填: 短信签名，应严格按"签名名称"填写，请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
        $params["SignName"] = config('sms.captcha.signName');;

        // fixme 必填: 短信模板Code，应严格按"模板CODE"填写, 请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/template
        $params["TemplateCode"] = $templateCode;

        // fixme 可选: 设置模板参数, 假如模板中存在变量需要替换则为必填项
        $params['TemplateParam'] = $templateParam;

        // fixme 可选: 设置发送短信流水号
        //$params['OutId'] = "12345";

        // fixme 可选: 上行短信扩展码, 扩展码字段控制在7位或以下，无特殊需求用户请忽略此字段
        //$params['SmsUpExtendCode'] = "1234567";


        // *** 需用户填写部分结束, 以下代码若无必要无需更改 ***
        if (!empty($params["TemplateParam"]) && is_array($params["TemplateParam"])) {
            $params["TemplateParam"] = json_encode($params["TemplateParam"]);
        }
        //print_r($params);exit;
        // 此处可能会抛出异常，注意catch
        $content = $this->request(
            $accessKeyId,
            $accessKeySecret,
            'dysmsapi.aliyuncs.com',
            array_merge($params, array(
                "RegionId" => "cn-hangzhou",
                "Action"   => "SendSms",
                "Version"  => '2017-05-25',
            ))
        // fixme 选填: 启用https
        // ,true
        );

        return $content;
    }

    /**
     * 批量发送短信
     */
    public function sendBatchSms($phoneNumbers = array(), $templateParam = array(), $templateCode = '')
    {
        $params = array();
        // *** 需用户填写部分 ***
        // fixme 必填: 请参阅 https://ak-console.aliyun.com/ 取得您的AK信息
        $accessKeyId = $this->accessKeyId;
        $accessKeySecret = $this->accessKeySecret;

        // fixme 必填: 待发送手机号。支持JSON格式的批量调用，批量上限为100个手机号码,批量调用相对于单条调用及时性稍有延迟,验证码类型的短信推荐使用单条调用的方式
        $params["PhoneNumberJson"] = $phoneNumbers;

        // fixme 必填: 短信签名，支持不同的号码发送不同的短信签名，每个签名都应严格按"签名名称"填写，请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
        $params["SignNameJson"] = array(config('sms.captcha.signName'));

        // fixme 必填: 短信模板Code，应严格按"模板CODE"填写, 请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/template
        $params["TemplateCode"] = $templateCode;

        // fixme 必填: 模板中的变量替换JSON串,如模板内容为"亲爱的${name},您的验证码为${code}"时,此处的值为
        // 友情提示:如果JSON中需要带换行符,请参照标准的JSON协议对换行符的要求,比如短信内容中包含\r\n的情况在JSON中需要表示成\\r\\n,否则会导致JSON在服务端解析失败
        $params["TemplateParamJson"] = $templateParam;

        // todo 可选: 上行短信扩展码, 扩展码字段控制在7位或以下，无特殊需求用户请忽略此字段
        // $params["SmsUpExtendCodeJson"] = json_encode(array("90997","90998"));

        // *** 需用户填写部分结束, 以下代码若无必要无需更改 ***
        $params["TemplateParamJson"] = json_encode($params["TemplateParamJson"], JSON_UNESCAPED_UNICODE);
        $params["SignNameJson"] = json_encode($params["SignNameJson"], JSON_UNESCAPED_UNICODE);
        $params["PhoneNumberJson"] = json_encode($params["PhoneNumberJson"], JSON_UNESCAPED_UNICODE);

        if (!empty($params["SmsUpExtendCodeJson"]) && is_array($params["SmsUpExtendCodeJson"])) {
            $params["SmsUpExtendCodeJson"] = json_encode($params["SmsUpExtendCodeJson"], JSON_UNESCAPED_UNICODE);
        }
        // 此处可能会抛出异常，注意catch
        $content = $this->request(
            $accessKeyId,
            $accessKeySecret,
            'dysmsapi.aliyuncs.com',
            array_merge($params, array(
                "RegionId" => "cn-hangzhou",
                "Action"   => "SendBatchSms",
                "Version"  => '2017-05-25',
            ))
        // fixme 选填: 启用https
        // ,true
        );
        return $content;
    }

    /**
     * 生成签名并发起请求
     *
     * @param $accessKeyId string AccessKeyId (https://ak-console.aliyun.com/)
     * @param $accessKeySecret string AccessKeySecret
     * @param $domain string API接口所在域名
     * @param $params array API具体参数
     * @param $security boolean 使用https
     * @param $method boolean 使用GET或POST方法请求，VPC仅支持POST
     * @return bool|\stdClass 返回API接口调用结果，当发生错误时返回false
     */
    public function request($accessKeyId, $accessKeySecret, $domain, $params, $security = false, $method = 'POST')
    {
        $apiParams = array_merge(array(
            "SignatureMethod"  => "HMAC-SHA1",
            "SignatureNonce"   => uniqid(mt_rand(0, 0xffff), true),
            "SignatureVersion" => "1.0",
            "AccessKeyId"      => $accessKeyId,
            "Timestamp"        => gmdate("Y-m-d H:i:s"),
            "Format"           => "JSON",
        ), $params);
        ksort($apiParams);

        $sortedQueryStringTmp = "";
        foreach ($apiParams as $key => $value) {
            $sortedQueryStringTmp .= "&" . $this->encode($key) . "=" . $this->encode($value);
        }

        $stringToSign = "${method}&%2F&" . $this->encode(substr($sortedQueryStringTmp, 1));

        $sign = base64_encode(hash_hmac("sha1", $stringToSign, $accessKeySecret . "&", true));

        $signature = $this->encode($sign);

        $url = ($security ? 'https' : 'http') . "://{$domain}/";

        try {
            $content = $this->fetchContent($url, $method, "Signature={$signature}{$sortedQueryStringTmp}");
            return json_decode($content);
        } catch (Exception $e) {
            return false;
        }
    }

    private function encode($str)
    {
        $res = urlencode($str);
        $res = preg_replace("/\+/", "%20", $res);
        $res = preg_replace("/\*/", "%2A", $res);
        $res = preg_replace("/%7E/", "~", $res);
        return $res;
    }

    private function fetchContent($url, $method, $body)
    {
        $ch = curl_init();

        if ($method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        } else {
            $url .= '?' . $body;
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "x-sdk-client" => "php/2.0.0",
        ));

        if (substr($url, 0, 5) == 'https') {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        }

        $rtn = curl_exec($ch);

        if ($rtn === false) {
            trigger_error("[CURL_" . curl_errno($ch) . "]: " . curl_error($ch), E_USER_ERROR);
        }
        curl_close($ch);

        return $rtn;
    }
}