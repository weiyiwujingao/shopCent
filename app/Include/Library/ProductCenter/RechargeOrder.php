<?php

namespace Library\ProductCenter;

use App;
use Illuminate\Http\Request;
use \Exception;
use Enum\EnumKeys;
use Illuminate\Support\Facades\Cache;
use \Helper\CFunctionHelper as help;
use Enum\EnumLang;
use \DB;
use \App\Models\BonusType;

class RechargeOrder extends \Library\CBase
{
	protected $request;
	protected $userCMd;

	public function __construct(Request $request)
	{
		$this->request = $request;
		$this->BonusTypeMd = new App\Repositories\BonusTypeRepository();
		parent::__construct(__CLASS__);
	}

	/**
	 * 充值金额列表
	 * @author: colin
	 * @date: 2019/5/28 14:05
	 * @return array|\Library\type
	 */
	public function list()
	{
		try{
			$where = self::getRechargeSearch();
			$list = $this->BonusTypeMd->list($where);
			help::throwError($list,'没有充值金额列表！');
			$_CFG = \Enum\EnumLang::loadConfig();
			//处理充赠
			$realMoneyArr = [];
			$chongzeng = isset($_CFG['chongzeng']) ? $_CFG['chongzeng'] : "";
			if (!empty($chongzeng)) {
				$groupChz = explode('|', $chongzeng);
				foreach ($groupChz as $onepair) {
					list($key, $value) = explode('+', $onepair, 2);
					$realMoneyArr[$key] = $key + $value;
				}
			}
			foreach ($list as &$val) {
				$typeMoney = intval($val["type_money"]);
				$realMoney = isset($realMoneyArr[$typeMoney]) ? $realMoneyArr[$typeMoney] : $typeMoney;//实际到账金额
				$val['real_money'] = number_format($realMoney, 2);
				$val['give_money'] = $realMoney - $typeMoney;
			}
			$data = [
				'isDiscount' => !empty($realMoneyArr) ? true : false,//是否有优惠
				'mlist'      => $list,
				'time'       => date('Y-m-d H:i:s'),
			];
		}catch(\Exception $e){
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => "RechargeOrder list:" . json_encode(1) . ",reason:" . $e->getMessage(),
				'userMsg' => $e->getMessage(),
				'line' => __LINE__,
			]);
		}
		return $data;
	}
	/**
	 * 获取充值列表条件
	 * @author: colin
	 * @date: 2019/5/28 11:50
	 * @return \Closure
	 */
	private function getRechargeSearch()
	{
		$pramArr['time'] = time();
		$where = function ($query) use ($pramArr) {
			$query->where("send_type", 3)->where('is_display',1)->where('use_end_date','>=',$pramArr['time']);
		};
		return $where;
	}

	/**
	 * 提交充值订单
	 * @author: colin
	 * @date: 2019/5/28 15:04
	 * @return array|\Library\type
	 */
	public function subOrder()
	{
		try {
			$userId = $this->request->input('uid');
			\DB::beginTransaction();
			$typeId = $this->request->input('type_id');
			$payId = $this->request->input('payment');
			$randomNum = rand(111111, 999999) . time();
			$bonusDetail = $this->BonusTypeMd->detail(['type_id'=>$typeId]);
			help::throwError($bonusDetail,'参数错误！');
			$orderBonus = [
				 'bonus_type_id' => $bonusDetail['type_id'],
				 'pay_id' => $payId == 1 ? 5 : 6,
				 'ad_id' => 9,
				 'user_id' => $userId,
				 'addtime' => time(),
				 'bonus_amount' => $bonusDetail['type_money'],
				 'bo_randomNum' => $randomNum,
			];
			$num = 0;
			do {
				$orderBonus['bonus_order_sn'] = help::getOrderSn(); //获取新订单号
				$create = $this->BonusTypeMd->create($orderBonus);
				$num++;
			} while ($create === false && $num < 10); //如果是订单号重复则重新提交数据
			$orderBonus['bo_id'] = $create->bo_id;
			$orderBonus['log_id'] = help::insertPayLog($orderBonus['bo_id'], $orderBonus['bonus_amount'], EnumKeys::PAY_ORDER);
			help::throwError($orderBonus['log_id'],'创建支付记录失败!');
		} catch (\Exception $e) {
			\DB::rollBack();
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => "RechargeOrder subOrder:" . json_encode(1) . ",reason:" . $e->getMessage(),
				'userMsg' => $e->getMessage(),
				'line' => __LINE__,
			]);
		}
		\DB::commit();
		$result = ['bo_id' => $orderBonus['bo_id'],'log_id'=>$orderBonus['log_id']];
		return $result;

	}
}