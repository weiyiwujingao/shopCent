<?php

namespace Helper;

//require_once "Function.php";

final class CLoggerHelper {

    private $objFile;
    private $strIp;
    private $maxFileSize;
    private $booArch;
    private $logDir;
    private $logFileName;
    private $maxLogFileNum;

    const LOG_LEVEL_INFO = 1; //日志分级：信息级
    const LOG_LEVEL_WARNING = 2; //日志分级：警告
    const LOG_LEVEL_ERROR = 3; //日志分级：错误

    /**
     * 
     * @param type $logDir 日志目录
     * @param type $logFileName 日志文件名
     * @param type $booArch 是否归档文件
     * @param type $maxLogFileNum 日志文件最大数量
     */

    public function __construct($logDir, $logFileName = "default", $booArch = true, $maxLogFileNum = 20) {
        $remoteIp = CFunctionHelper::getRealIP();
        $this->strIp = (!empty($remoteIp)) ? $remoteIp : '0.0.0.0';
        $this->booArch = $booArch;
        $this->logFileName = strpos($logFileName, ".log") === false ? $logFileName . ".log" : $logFileName;
        $this->maxLogFileNum = $maxLogFileNum;
        $this->logDir = $logDir;
        $this->maxFileSize = 5 * 1024 * 1024;
    }

    /**
     * 设置常规日志信息
     * @param type $strMsg 日志内容
     */
    public function logNotice($strMsg, $logLevel = self::LOG_LEVEL_INFO) {
        $this->setLog($strMsg, false, $logLevel);
    }

    /**
     * 设置错误日志信息
     * @param type $strMsg 日志内容
     */
    public function logError($strMsg, $logLevel = self::LOG_LEVEL_ERROR) {
        $this->setLog($strMsg, true, $logLevel);
    }

    private function setLog($strMsg, $isLogError = false, $logLevel = self::LOG_LEVEL_INFO) {
        $this->_openLogFile($isLogError);
        if ($this->objFile == false) {
            return false;
        }
        $logContent = "[" . date('Y-m-d H:i:s') . "]" . $this->strIp . "|" . getmypid() . " " . $strMsg;
        if ($logLevel == self::LOG_LEVEL_ERROR) {
            $logContent = "\033[1;31m [ERROR] " . $logContent . "\033[0m \n";
        } elseif ($logLevel == self::LOG_LEVEL_WARNING) {
            $logContent = "\033[1;33m [WARNING] " . $logContent . "\033[0m \n";
        } else {
            $logContent = "[INFO] " . $logContent . " \n";
        }
        fwrite($this->objFile, $logContent);
        fclose($this->objFile);
    }

    /**
     * 打开日志文件
     */
    private function _openLogFile($isLogError = false) {
        // 清理file stat的缓存
        clearstatcache();
        if (is_dir($this->logDir) === false) {
            if (CFunctionHelper::createDir($this->logDir) == false) {
                return false;
            }
        }

        $logFilePath = $this->logDir . '/' . $this->logFileName;
        $logFilePath = $isLogError === true ? str_replace(".log", "_error.log", $logFilePath) : $logFilePath;
        if (file_exists($logFilePath)) {
            if ($this->booArch === true) {
                //多进程去处理的话会有并发问题，这里作一个错误捕捉
                try{
                    $intFileSize = filesize($logFilePath);
                }catch(\ErrorException $e){
                    $intFileSize = 0;
                }
                // 归档文件
                if ($intFileSize >= $this->maxFileSize) {
                    $fileNameWithoutExt = $this->logDir . '/' . $this->logFileName;
                    $lastFileNameGZ = $fileNameWithoutExt . '_' . sprintf('%01d', $this->maxLogFileNum) . '.gz';
                    if (file_exists($lastFileNameGZ)) {
                        //多进程去处理的话会有并发问题，这里作一个错误捕捉
                        try{
                            unlink($lastFileNameGZ);
                        }catch(\ErrorException $e){
                            //var_dump($e->getMessage());
                        }
                    }
                    for ($i = $this->maxLogFileNum - 1; $i >= 0; $i--) {
                        if ($i == 0) {
                            if (file_exists($logFilePath)) {
                                //多进程去处理的话会有并发问题，这里作一个错误捕捉
                                try{
                                    $strFileContent = file_get_contents($logFilePath);
                                    $logFilePathGz = $fileNameWithoutExt . '_' . sprintf('%01d', $i + 1) . '.gz';
                                    $strFileContentGz = gzencode($strFileContent, 9);
                                    $objFileGz = $this->_fopenLogFile($logFilePathGz, 'w+');
                                    fwrite($objFileGz, $strFileContentGz);
                                    fclose($objFileGz);
                                    unlink($logFilePath);
                                }catch(\ErrorException $e){
                                    //var_dump($e->getMessage());
                                }
                            }
                            $this->objFile = $this->_fopenLogFile($logFilePath, 'w+');
                        } else {
                            $currFilePath = $fileNameWithoutExt . '_' . sprintf('%01d', $i) . '.gz';
                            if (file_exists($currFilePath)) {
                                $newLogFilePath = $fileNameWithoutExt . '_' . sprintf('%01d', $i + 1) . '.gz';
                                //多进程去处理的话会有并发问题，这里作一个错误捕捉
                                try{
                                    rename($currFilePath, $newLogFilePath);
                                }catch(\ErrorException $e){
                                    //var_dump($e->getMessage());
                                }
                            }
                        }
                    }
                } else {
                    $this->objFile = $this->_fopenLogFile($logFilePath, 'a+');
                }
            } else {
                $this->objFile = $this->_fopenLogFile($logFilePath, 'a+');
            }
        } else {
            $this->objFile = $this->_fopenLogFile($logFilePath, 'w+');
        }
    }

    /**
     * 以指定的模式打开文件
     *
     * @param string $strFile
     *        	文件路径
     * @param string $strMode
     *        	打开方式
     * @return boolean resource
     */
    private function _fopenLogFile($strFile, $strMode) {
        $objFile = fopen($strFile, $strMode);
        if ($objFile == FALSE) {
            return false;
        }
        if ($strMode == 'w+') {
            //多进程去处理的话会有并发问题，这里作一个错误捕捉
            try{
                chmod($strFile, 0775);
            }catch(\ErrorException $e){
                //return false;
            }
        }
        return $objFile;
    }

}
