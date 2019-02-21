<?php
namespace Basic;

use Phalcon\DI;
use Library\Log;
use Models\User;
use Services\ZyhxLoginService;

class BaseService{
    
    protected $_config;
    protected $_user;

	#初始化
	public function __construct(){
	    
        $this->_config = DI::getDefault()->get('config');
//         $this->_user = DI::getDefault()->get('session')->get('user');
	}

	protected function dealResult($result, $user_id) {
	   if($result['result'] == '110'){  //鉴权失败
            Log::dld($user_id, $result['msg']);
            (new User())->updateData(['h5token'=>''], ['id'=>$user_id]);
            if(!empty($this->_user)){
                $this->_user['h5token'] = '';
                DI::getDefault()->get('session')->set('user', $this->_user);
            }
            return false;
        }
        return true;
	}
	
	protected function dealZyhxResult($result, $user_id) {
	    if(isset($result['Ret'])){  //鉴权失败
	        if($result['Ret'] == -402 || $result['Ret'] == -401){
	            Log::zyhx($user_id, $result['Msg']);
	            
	            //token失效，从新获取token
	            $newToken = (new ZyhxLoginService())->updateToken($user_id);
	            if($newToken == false){
	                $a = (new User())->updateData(['h5token'=>''], ['id'=>$user_id]);
	                if(!empty($this->_user)){
	                    $this->_user['h5token'] = '';
	                    if(PHP_SAPI != 'cli')DI::getDefault()->get('session')->set('user', $this->_user);
	                }
	                return false;
	            }
	            Log::zyhx($user_id, '重新获取token:'.$newToken);
	            $a = (new User())->updateData(['h5token'=>$newToken], ['id'=>$user_id]);
	            if(!empty($this->_user)){
	                $this->_user['h5token'] = $newToken;
	                if(PHP_SAPI != 'cli')DI::getDefault()->get('session')->set('user', $this->_user);
	            }
	            return false;
	        }
	        Log::zyhx($user_id, $result['Msg']);
	    }
	    return true;
	}
	
	/**
	 * 获取奖励名称
	 * @param unknown $awards
	 * @create_time 2018年1月18日
	 */
    protected function getAwardsName($awards)
    {
        $content = '';
        if(empty($awards))return $content;
        if(isset($awards['attrs']) && !empty($awards['attrs'])){
            foreach ($awards['attrs'] as $val) {
                if(!isset($val['name'])){
                    switch ($val['id']){
                        case 'kContrib':
                            $val['name'] = '个人帮贡';
                            break;
                        case 'kCavePoint':
                            $val['name'] = '矿洞积分';
                            break;
                        case 'kDoushenMedal':
                            $val['name'] = '斗神币';
                            break;
                        default:
                            $val['name'] = '';
                            break;
                    }
                }
                $content .= $val['name'].' '.($val['num'] > 0 ? '+' : '-').$val['num'].'  ';
            }
        }
        if(isset($awards['items']) && !empty($awards['items'])){
            foreach ($awards['items'] as $val) {
                $content .= $val['name'].' '.($val['num'] > 0 ? '+' : '-').$val['num'].'  ';
            }
        }
        return $content;
    }
}
