<?php

namespace Library\UserCenter;

use DB;
use App;
use Enum\EnumKeys;
use Validator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;
use \Exception;
use Helper\CFunctionHelper as help;
use \App\Models\CustomerServiceTel;

class CustomerService extends \Library\CBase
{
	protected $request;
	protected $userMd;

	public function __construct(Request $request)
	{
		$this->request = $request;
		$this->userAMd = new App\Repositories\UserAddresRepository();
		parent::__construct(__CLASS__);
	}

	/**
	 * 客服中心
	 * @author: colin
	 * @date: 2019/1/15 8:49
	 * @return \Illuminate\Database\Eloquent\Collection|\Library\type|static[]
	 */
	public function getlist()
	{
		try {
			$uid = $this->request->input('uid');
			$list = \App\Models\ArticleCat::with(['list' => function ($query) {
				$query->select('cat_id', 'title', 'article_id');
			}]);
			$list = $list->select('cat_id', 'cat_name','img')
				->where(['parent_id' => 13, 'show_in_nav' => 1])
				->orderBy('sort_order', 'asc')
				->get()->toArray();
			if (empty($list)) {
				throw new Exception('获取客服问题失败！');
			}
			foreach ($list as $key=>$value){
				$list[$key]['img'] = config('app.static_domain').$value['img'];
			}
		} catch (\Exception $e) {
			echo $e->getMessage();die;
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => "CustomerService getlist:" . json_encode($uid) . ",reason:" . $e->getMessage(),
				'userMsg' => '获取客服问题失败',
				'line' => __LINE__,
			]);
		}
		return $list;
	}
	/**
	 * 问题详情
	 * @author: colin
	 * @date: 2019/1/15 9:46
	 * @return bool|\Library\type
	 */
	public function detail()
	{
		try {
			$id= $this->request->input('id');
			$result = \App\Models\Article::where('article_id',$id)->select('article_id','title','content')->firstOrFail();
			if(empty($result)){
				throw new \Exception('没有内容！');
			}
			return $result->toArray();
		} catch (\Exception $e) {
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => "UserAddress create:" . json_encode($param) . ",reason:" . $e->getMessage(),
				'userMsg' => $e->getMessage(),
				'line' => __LINE__,
			]);
		}
	}

	/**
	 * 获取客服电话
	 * @author: colin
	 * @date: 2019/5/27 17:46
	 * @return \Library\type
	 */
	public function servicTel()
	{
		try {
			$cityId = $this->request->input('cityId',1);
			$tel = CustomerServiceTel::where('city_id',$cityId)->value('tel');
			$data = [
				'city_id' => $cityId,
				'tel' => $tel ?? '',
			];
		} catch (\Exception $e) {
			return $this->setErrorAndReturn([
				'return' => false,
				'code' => \Enum\EnumMain::HTTP_CODE_FAIL,
				'errorMsg' => "servicTel:" . json_encode($cityId) . ",reason:" . $e->getMessage(),
				'userMsg' => '获取客服电话失败！',
				'line' => __LINE__,
			]);
		}
		return $data;
	}

}