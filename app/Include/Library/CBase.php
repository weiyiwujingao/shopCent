<?php

namespace Library;
use Illuminate\Support\Facades\Cache;

class CBase {

    protected $errorNo; /* 错误码 */
    protected $errorMsg; /* 错误消息，用于底层记录，用于DEBUG */
    protected $userMsg; /* 错误消息，用于返回给前端提示给用户 */
    protected $logger; /* 日志对象 */
    protected $gsId = 0;
    public function __construct($class = __CLASS__) {
        if (!($this->logger instanceof CLoggerHelper)) {
            $class = explode("\\", $class);
            $lotName = array_pop($class);
            $this->logger = new \Helper\CLoggerHelper(LOG_PATH . "Library/" . date("Ymd") . "/", $lotName);
        }
    }

    /**
     * 获取session数据
     */
    public function getSession($request){
		$token = $request->header("token") ?? '';
		if(empty($token)){
			$res = array('status' => -99,'data'=>'','message' => '未登录！');
			return response()->json($res);
		}
        $sessionData = Cache::get($token);
        if(!isset($sessionData['gs_id'])){
            return false;
        }
        $this->gsId = (int)$sessionData['gs_id'];
        return $sessionData;
    }

    /**
     * 业务日志记录
     * @param type $msg
     * @param type $line
     */
    public function logBusi($msg, $line = 0, $level = \Helper\CLoggerHelper::LOG_LEVEL_INFO) {
        $lotyName = isset($this->lotyName) ? $this->lotyName : '';
        $this->logger->logNotice($lotyName.'|'.$msg . ",line:{$line}", $level);
    }

    /**
     * 错误日志记录
     * @param type $msg
     * @param type $line
     */
    protected function logErr($msg, $line = 0, $level = \Helper\CLoggerHelper::LOG_LEVEL_ERROR) {
        $lotyName = isset($this->lotyName) ? $this->lotyName : '';
        $this->logger->logError($lotyName.'|'.$msg . ",line:{$line}", $level);
    }

    /**
     * 获取错误码
     * @return type
     */
    public function getErrorNo() {
        return $this->errorNo;
    }

    /**
     * 获取提示用户的日志
     * @return type
     */
    public function getUserMsg() {
        return $this->userMsg;
    }

    /**
     * 获取系统错误日志
     * @return type
     */
    public function getErrorMsg() {
        return $this->errorMsg;
    }

    /**
     * 设置错误信息，记录错误日志并返回
     * @return type
     */
    public function setErrorAndReturn($errorData = array()) {
        $this->errorNo = isset($errorData['code']) ? $errorData['code'] : \Enum\EnumMain::HTTP_CODE_FAIL;
        $this->errorMsg = isset($errorData['errorMsg']) ? $errorData['errorMsg'] : "";
        $this->userMsg = isset($errorData['userMsg']) ? $errorData['userMsg'] : "";
        $line = isset($errorData['line']) ? $errorData['line'] : 0;
        $retrun = isset($errorData['return']) ? $errorData['return'] : false;
        $this->logErr("errorMsg:{$this->errorMsg},userMsg:{$this->userMsg}", $line);
        return $retrun;
    }

}
