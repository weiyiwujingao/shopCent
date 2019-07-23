<?php

/**
 * 华为云隐号功能api
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/5/7
 * Time: 9:59
 */
namespace Library;
class axPrivateNumber
{
    private $realUrl = 'https://rtcapi.cn-north-1.myhuaweicloud.com:12543/rest/provision/caas/privatenumber/v1.0'; // APP接入地址+接口访问URI
    private $appKey;
    private $appSecret;

    function __construct()
    {
        $cfgData = config('hwcloud');
        $this->appKey = $cfgData['app_key'];
        $this->appSecret = $cfgData['app_secret'];
    }

    /**
     * AX模式绑定
     * @param string $origNum A号码
     * @param string $privateNum X号码(隐私号码)
     */
    public function bind($origNum = '', $privateNum = '')
    {
        // $privateNumType = 'mobile-virtual'; //固定为mobile-virtual
        $areaCode = '0755'; //需要绑定的X号码对应的城市码
        // $recordFlag = 'false'; //是否需要针对该绑定关系产生的所有通话录音
        // $recordHintTone = 'recordHintTone.wav'; //设置录音提示音

        $calleeNumDisplay = '0'; // 设置非A用户呼叫X时,A接到呼叫时的主显号码

        // $privateSms = 'true'; //设置该绑定关系是否支持短信功能

        // $callerHintTone = 'callerHintTone.wav'; //设置A拨打X号码时的通话前等待音
        // $calleeHintTone = 'calleeHintTone.wav'; //设置非A用户拨打X号码时的通话前等待音
        // $preVoice = [
        //     'callerHintTone' => $callerHintTone,
        //     'calleeHintTone' => $calleeHintTone
        // ];
        // 请求Body,可按需删除选填参数
        $data = json_encode([
            'origNum'          => '+86' . $origNum,
            'privateNum'       => '+86' . $privateNum,
            // 'privateNumType' => $privateNumType,
            'areaCode'         => $areaCode,
            // 'recordFlag' => $recordFlag,
            // 'recordHintTone' => $recordHintTone,
            'calleeNumDisplay' => $calleeNumDisplay,
            // 'privateSms' => $privateSms,
            // 'preVoice' => $preVoice
        ]);

        $contextOptions = [
            'http' => [
                'method'        => 'POST', // 请求方法为POST
                'header'        => $this->headers(),
                'content'       => $data,
                'ignore_errors' => true // 获取错误码,方便调测
            ],
            'ssl'  => [
                'verify_peer'      => false,
                'verify_peer_name' => false
            ] // 为防止因HTTPS证书认证失败造成API调用失败,需要先忽略证书信任问题
        ];
        try {

            $response = file_get_contents($this->realUrl, false, stream_context_create($contextOptions)); // 发送请求
            $arr = \GuzzleHttp\json_decode($response, true);
            if (isset($arr['resultcode']) && $arr['resultcode'] == 0) {
                $res = [
                    'status' => 1,
                    'data'   => $arr
                ];
            } else {
                $res = [
                    'status' => 0,
                    'data'   => $arr
                ];
            }
        } catch (\Exception $e) {
            $res = [
                'status'  => 0,
                'message' => $e->getMessage()
            ];
        }
        return $res;
    }

    /**
     * @param string $origNum A号码
     * @param string $privateNum X号码(隐私号码)
     * @param string $subscriptionId 绑定产生的ID
     */
    public function unbind($origNum = '', $privateNum = '', $subscriptionId = '')
    {
        // 请求URL参数
        if (!empty($subscriptionId)) {
            $params = [
                'subscriptionId' => $subscriptionId
            ];
        } else {
            $params = [
                'origNum'    => '+86' . $origNum,
                'privateNum' => '+86' . $privateNum
            ];
        }
        $data = http_build_query($params);
        // 完整请求地址
        $fullUrl = $this->realUrl . '?' . $data;
        $contextOptions = [
            'http' => [
                'method'        => 'DELETE', // 请求方法为DELETE
                'header'        => $this->headers(),
                'ignore_errors' => true // 获取错误码,方便调测
            ],
            'ssl'  => [
                'verify_peer'      => false,
                'verify_peer_name' => false
            ] // 为防止因HTTPS证书认证失败造成API调用失败,需要先忽略证书信任问题
        ];

        try {
            $response = file_get_contents($fullUrl, false, stream_context_create($contextOptions)); // 发送请求
            $arr = \GuzzleHttp\json_decode($response, true);
            if (isset($arr['resultcode']) && $arr['resultcode'] == 0) {
                $res = [
                    'status' => 1,
                    'data'   => $arr
                ];
            } else {
                $res = [
                    'status' => 0,
                    'data'   => $arr
                ];
            }
        } catch (Exception $e) {
            $res = [
                'status'  => 0,
                'message' => $e->getMessage()
            ];
        }
        return $res;
    }

    /**
     * 通过id解绑
     * @param string $subscriptionId
     */
    public function unBindById($subscriptionId = '')
    {
        return $this->unbind('', '', $subscriptionId);
    }

    /**
     * 请求Headers
     * @return array
     */
    private function headers()
    {
        $headers = [
            'Accept: application/json',
            'Content-Type: application/json;charset=UTF-8',
            'Authorization: WSSE realm="SDP",profile="UsernameToken",type="Appkey"',
            'X-WSSE: ' . $this->buildWsseHeader($this->appKey, $this->appSecret)
        ];
        return $headers;
    }

    /**
     * 构建X-WSSE值
     *
     * @param string $appKey
     * @param string $appSecret
     * @return string
     */
    private function buildWsseHeader($appKey, $appSecret)
    {
        date_default_timezone_set("UTC");
        $Created = date('Y-m-d\TH:i:s\Z'); //Created
        $nonce = uniqid(); //Nonce
        $base64 = base64_encode(hash('sha256', ($nonce . $Created . $appSecret), TRUE)); //PasswordDigest

        return sprintf("UsernameToken Username=\"%s\",PasswordDigest=\"%s\",Nonce=\"%s\",Created=\"%s\"", $appKey, $base64, $nonce, $Created);
    }
}