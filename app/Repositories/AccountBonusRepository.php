<?php

namespace App\Repositories;

use App\Models\AccountLog;
use App\Models\AccountLogBonus;
use App\Models\UserBonus;

class AccountBonusRepository
{
    /**
     * 创建账单
     * @param array $params
     * @return mixed
     */
    public function create(array $params)
    {
        return AccountLogBonus::create($params);
    }
	/**
	 * 修改账单
	 * @author: colin
	 * @date: 2019/1/9 16:23
	 * @param $params
	 * @param $where
	 * @return mixed
	 */
	public function update($params,$where)
	{
		return AccountLogBonus::where($where)->update($params);
	}
	/**
	 * 删除选中账单
	 * @author: colin
	 * @date: 2019/1/9 16:37
	 * @param $where
	 * @return mixed
	 */
	public function delete($where)
	{
		return AccountLogBonus::where($where)->delete();
	}
    /**
     * 根据id获取账单资料
     * @param $id
     * @return mixed
     */
    public function ById($id)
    {
        return AccountLogBonus::find($id);
    }

	/**
	 * 充值账单
	 * @author: colin
	 * @date: 2019/1/10 17:20
	 * @param $uid
	 * @param $startTime
	 * @param $endTime
	 * @return array|bool
	 */
    public function rechargeBill($uid,$startTime,$endTime,&$sort)
	{
		try{
			$dataList = AccountLogBonus::selectRaw('order_sn,user_money,change_time,change_desc,bonus_id')
				->where(['user_id' => $uid, 'stores_id' => 0])
				->where('change_time', '>=', $startTime)
				->where('change_time', '<=', $endTime);
			$dataList = $dataList->take(1000)->orderBy('logb_id', 'DESC')->get()->toArray();
			$accountData = [];
			foreach($dataList as $item){
				if (mb_strpos($item['change_desc'], '微信') !== false) {
					$type = 2;//微信
					$str = '微信充值';
				} elseif (mb_strpos($item['change_desc'], '支付宝') !== false) {
					$type = 3;//支付宝
					$str = '支付宝充值';
				} elseif ($item['bonus_id']) {
					$type = $item['user_money'] > 0 ? 6 : 7;// 退款/扣款
					$str = $item['change_desc'];
				} else {
					$type = 4;//其他充值
					$str = '其它充值';
				}
				$accountData[] = [
					'type'     => $type,
					'sn'       => $item['order_sn'],
					'ctimestr' => date('Y年m月d日 H:i', $item['change_time']),
					'money'    => $type == 7 ? $item['user_money'] : number_format(abs($item['user_money']), 2),
					'desc'     => $str,
					'ctime'    => $item['change_time'] + 3600 * 8,
				];
				$sort[] = $item['change_time'] + 3600 * 8;
			}
		}catch(\Exception $e){
			return false;
		}
		return $accountData;
	}

	/**
	 * 消费账单
	 * @author: colin
	 * @date: 2019/1/10 17:31
	 * @param $uid
	 * @param $startTime
	 * @param $endTime
	 * @return array|bool
	 */
	public function shopBill($uid,$startTime,$endTime,&$sort)
	{
		try{
			$dataList = AccountLogBonus::join('ecs_goods_stores as b','b.gs_id','=','ecs_account_log_bonus.stores_id')
				->join('ecs_brand as c','c.brand_id','=','b.gs_brand_id')
				->selectRaw('ecs_account_log_bonus.order_sn,ecs_account_log_bonus.stores_id,ecs_account_log_bonus.user_money,ecs_account_log_bonus.change_time,ecs_account_log_bonus.bonus_id,b.gs_name,c.brand_logo')
				->where(['user_id' => $uid])
				->where('stores_id','>',0)
				->where('change_time', '>=', $startTime)
				->where('change_time', '<=', $endTime);
			$dataList = $dataList->take(1000)->orderBy('ecs_account_log_bonus.logb_id', 'DESC')->get()->toArray();
			$accountData = [];
			foreach($dataList as $item){
				$brandLogo = !empty($item['brand_logo']) ? config('app.static_domain') . "data/brandlogo/" . $item["brand_logo"] : '';
				$desc = $item['gs_name'];
				if (!empty($item['bonus_id'])) {
					$bonusSn = UserBonus::where('bonus_id',$item['bonus_id'])->value('bonus_sn');
					$desc .= "- (幸福券{$bonusSn})";
				}
				$accountData[] = [
					'type'       => 5,//消费
					'sn'         => $item['order_sn'],
					'ctimestr'   => date('Y年m月d日 H:i', $item['change_time']),
					'money'      => '-' . number_format(abs($item['user_money']), 2),
					'desc'       => $desc,//门店名称
					'ctime'      => $item['change_time'] + 3600 * 8,
					'brand_logo' => $brandLogo,
				];
				$sort[] = $item['change_time'] + 3600 * 8;
			}
		}catch(\Exception $e){
			echo $e->getMessage();die;
			return false;
		}
		return $accountData;
	}

	/**
	 * 退款账单
	 * @author: colin
	 * @date: 2019/1/11 9:08
	 * @param $uid
	 * @param $startTime
	 * @param $endTime
	 * @return array|bool
	 */
	public function returnBill($uid,$startTime,$endTime,&$sort)
	{
		try{
			$dataList = AccountLog::selectRaw('user_money,change_time,change_desc,change_type')
				->where(['user_id' => $uid])
				->where('user_money','>',0)
				->where('change_time', '>=', $startTime)
				->where('change_time', '<=', $endTime);
			$dataList = $dataList->take(1000)->orderBy('log_id', 'DESC')->get()->toArray();
			$accountData = [];
			$reg = '/[\d]{13}/';
			foreach ($dataList as $item) {
				$type = ($item['change_type'] == 99) ? 6 : 7;
				$sn = '';
				preg_match($reg, $item['change_desc'], $result);
				if (!empty($result)) {
					$sn = $result[0];
				}
				$desc = $item['change_desc'];
				if ($type == 6) {
					$desc = $sn ? '订单(' . $sn . ')退款至余额' : '订单退款至余额';
				}
				$accountData[] = [
					'type'     => $type,
					'sn'       => $sn,
					'ctimestr' => date('Y年m月d日 H:i', $item['change_time']),
					'money'    => number_format($item['user_money'], 2),
					'desc'     => $desc,
					'ctime'    => $item['change_time'] + 3600 * 8,
				];
				$sort[] = $item['change_time'] + 3600 * 8;
			}
		}catch(\Exception $e){
			return false;
		}
		return $accountData;
	}

}