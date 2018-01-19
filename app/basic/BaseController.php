<?php
namespace Basic;

class BaseController extends \Phalcon\Mvc\Controller
{
    protected $_user;
    
    private $_whiteRouter = [
        'index' => '*',
        'dld' => ['config'],
    ];

	#初始化
	public function initialize()
	{
	    $this->_user = $this->session->has('user') ? $this->session->get('user') : null;
	    $this->accessRestriction();
    }
    
    /**
     * 访问权限控制
     * @return boolean
     * @create_time 2017年11月15日
     */
    private function accessRestriction()
    {
        $moduleName = $this->router->getModuleName();
        $controllerName = $this->router->getControllerName();
        foreach ($this->_whiteRouter as $key => $val) {
            if($val == '*' && $key == $moduleName)return true;
             
            if($key == $moduleName && is_string($val) && $val != '*')continue;
             
            if($key == $moduleName && in_array($controllerName, $val))return false;
        }
        
        if(!$this->_islogin()){
            return $this->ajaxReturn('', 1, '未登录');
        }
        return true;
    }
    
    protected function _islogin()
    {
        return $this->_user ? true : false;
    }

	/**
	 * Ajax方式返回数据到客户端
	 * @access protected
	 * @param mixed $data 要返回的数据
	 * @param int $code
	 * @param string $msg
	 * @param String $type AJAX返回数据格式
	 */
	protected function ajaxReturn($data, $code= '', $msg='', $type='json')
	{
		if ($code !== '' && $msg != ''){
		    $returnMsg = ['code'=>$code, 'msg'=>$msg];
		    if(!empty($data))$returnMsg['data'] 	= $data;
		    $data = $returnMsg;
		}

	    switch (strtoupper($type)){
	        case 'JSON' :
	            // 返回JSON数据格式到客户端 包含状态信息
	            header('Content-Type:application/json; charset=utf-8');
	            exit(json_encode($data,JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE) ); // php 5.11
	        case 'EVAL' :
	            // 返回可执行的js脚本
	            header('Content-Type:text/html; charset=utf-8');
	            exit($data);
	        default     :
	            var_dump($data);
	    }
	}
    

}
