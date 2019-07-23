<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Mockery\Exception;

class PublicsController extends Controller
{
    private $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * 绑定号码
     * @return array
     */
    public function axBind()
    {
        try {
            $params = $this->request->all();
            $origNum = isset($params['orig_num']) ? trim($params['orig_num']) : '';
            $privateNum = isset($params['private_num']) ? trim($params['private_num']) : '';

            if (empty($origNum) || empty($privateNum)) {
                throw new \Exception('参数orig_num,private_num都不能为空');
            }
            $obj = new \Library\axPrivateNumber();
            $res = $obj->bind($origNum, $privateNum);
            if ($res['status'] == 1) {
                $uData = [
                    'orig_num'        => $origNum,
                    'subscription_id' => $res['data']['subscriptionId'],
                    'bind_time'       => time()
                ];
                \DB::table('ecs_axprivate')->where('private_num', $privateNum)->update($uData);
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
     * 解绑号码
     * @return array
     */
    public function axUnBind()
    {
        try {
            $params = $this->request->all();
            $origNum = isset($params['orig_num']) ? trim($params['orig_num']) : '';
            $privateNum = isset($params['private_num']) ? trim($params['private_num']) : '';
            $subscriptionId = isset($params['subscription_id']) ? trim($params['subscription_id']) : '';
            $obj = new \Library\axPrivateNumber();
            if (!empty($subscriptionId)) {
                $res = $obj->unBindById($subscriptionId);
            } else {
                if (empty($origNum) || empty($privateNum)) {
                    throw new \Exception('参数orig_num,private_num都不能为空');
                }
                $res = $obj->unbind($origNum, $privateNum);
                if ($res['status'] == 1) {
                    $uData = [
                        'orig_num'        => '',
                        'subscription_id' => '',
                        'bind_time'       => 0,
                        'unbind_time'     => date('Y-m-d H:i:s'),
                    ];
                    \DB::table('ecs_axprivate')->where('private_num', $privateNum)->update($uData);
                }
            }
        } catch (\Exception $e) {
            $res = [
                'status'  => 0,
                'message' => $e->getMessage()
            ];
        }
        return $res;
    }
}
