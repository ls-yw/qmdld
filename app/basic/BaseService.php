<?php
namespace Basic;

use Phalcon\DI;
use Library\Log;
use Models\User;

class BaseService{
    
    protected $_config;
    protected $_user;

	#初始化
	public function __construct(){
        $this->_config = DI::getDefault()->get('config');
        $this->_user = DI::getDefault()->get('session')->get('user');
	}

	public function dealResult($result, $user_id) {
	   if($result['result'] == '110'){  //鉴权失败
            Log::dld($user_id, $result['msg']);
            (new User())->updateData(['h5token'=>''], ['id'=>$user_id]);
            if(!empty($this->_user)){
                $this->_user['h5token'] = '';
                DI::getDefault()->get('session')->set('user', $this->_user);
            }
            return false;
        }
	}
	
	/**
	 * 获取奖励名称
	 * @param unknown $awards
	 * @create_time 2018年1月18日
	 */
    public function getAwardsName($awards)
    {
        $content = '';
        if(empty($awards))return $content;
        if(isset($awards['attrs']) && !empty($awards['attrs'])){
            foreach ($awards['attrs'] as $val) {
                $content .= $val['name'].' +'.$val['num'].'  ';
            }
        }
        if(isset($awards['items']) && !empty($awards['items'])){
            foreach ($awards['items'] as $val) {
                $content .= $val['name'].' +'.$val['num'].'  ';
            }
        }
        return $content;
    }
}
