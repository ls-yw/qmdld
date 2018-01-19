<?php
namespace Modules\Index\Controllers;

use Basic\BaseController;

class LogController extends BaseController{

    public function currentAction() {
        $uid = $this->_user['id'];
        $file = $this->config->application->actionDir.$uid.'/'.date('Ymd').'.log';
        if(!file_exists($file)){
            return $this->ajaxReturn('暂无日志', 0, '成功');
        }
        $file_arr = file($file);
        $startNum = count($file_arr) - 1;
        $maxNum = count($file_arr) <= 500 ? 0 : ($startNum - 500);
        $content = '';
       
        for($i=$startNum;$i>=$maxNum;$i--){//逐行读取文件内容
            $content .= $file_arr[$i]."<br />";
        }
        return $this->ajaxReturn($content, 0, '成功');
    }
}