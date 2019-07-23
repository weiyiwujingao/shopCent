<?php
/**
 * 隐号功能定时解绑
 */
namespace App\Repositories\other;

use Library\axPrivateNumber;

class AxprivateRepo
{
    private $axPrivateNumber;

    public function __construct(axPrivateNumber $axPrivateNumber)
    {
        $this->axPrivateNumber = $axPrivateNumber;
    }

    public function check()
    {
        $time = time();
        try {
            $endTime = $time - 30 * 60;//30分钟
            $msgArr = [];
            $subData = \DB::table('ecs_axprivate')->select(['id', 'subscription_id'])->whereBetween('bind_time', [1, $endTime])->get();
            //print_r($subData);exit;
            foreach ($subData as $item) {
                $sId = $item->subscription_id;
                $res = $this->axPrivateNumber->unBindById($sId);
                if ($res['status'] == 1 || $res['data']['resultcode'] == '1016001') {
                    $uData = [
                        'orig_num'        => '',
                        'subscription_id' => '',
                        'bind_time'       => 0,
                        'unbind_time'     => date('Y-m-d H:i:s', time() + 3600 * 8),
                    ];
                    \DB::table('ecs_axprivate')->where('id', $item->id)->update($uData);
                    \DB::table('ecs_axprivate_log')->where('subscription_id', $sId)->update(['unbind_time' => $uData['unbind_time']]);
                } else {
                    $msg = "axprivate_check:" . \GuzzleHttp\json_encode($res);
                    \Log::error($msg);
                }
                $msgArr[] = $sId;
            }
            if (!empty($msgArr)) {
                $msg = implode(',', $msgArr);
                \Log::info('axprivate_check' . $msg);
            }
        } catch (\Exception $e) {
            $msg = "axprivate_check:" . $e->getMessage();
            \Log::error($msg);
        }
    }
}