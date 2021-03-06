<?php
namespace Services;

use Basic\BaseService;
use Library\Curl;
use Library\Log;

class ZyhxHomeService extends BaseService
{
    public $_configData = [
        'plant' => [
            'crop' => [
                '59200044' => '风铃草',
                '59200045' => '紫色风铃草',
                '59200046' => '风雨兰',
                '59200047' => '紫色风雨兰',
				'59200048' => '玫瑰',
                '59200049' => '紫色玫瑰',
                '59200050' => '牵牛花',
                '59200051' => '紫色牵牛花',
                '59200052' => '向日葵',
                '59200053' => '紫色向日葵',
                '59200054' => '萱草',
                '59200055' => '紫色萱草',
                '59200056' => '薰衣草',
                '59200057' => '紫色薰衣草',
                '59200058' => '月季',
                '59200059' => '紫色月季',
                '59200060' => '紫罗兰',
                '59200061' => '紫色紫罗兰',
                '59200062' => '醉蝶花',
                '59200063' => '紫色醉蝶花',
                '59200064' => '摇钱树',
                '59990174' => '紫色摇钱树',
                
                '59200004' => '苹果',
                '59200005' => '紫色苹果',
                '59200006' => '葡萄',
                '59200007' => '紫色葡萄',
				'59200008' => '桃子',
                '59200009' => '紫色桃子',
                '59200010' => '橘子',
                '59200011' => '紫色橘子',
                '59200012' => '香蕉',
                '59200013' => '紫色香蕉',
                '59200014' => '红枣',
                '59200015' => '紫色红枣',
                '59200016' => '桑果',
                '59200017' => '紫色桑果',
                '59200018' => '甘蔗',
                '59200019' => '紫色甘蔗',
                '59200020' => '樱桃',
                '59200021' => '紫色樱桃',
                
                '59200024' => '槐树',
                '59200025' => '紫色槐树',
                '59200026' => '桃树',
                '59200027' => '紫色桃树',
				'59200028' => '桂树',
                '59200029' => '紫色桂树',
                '59200030' => '竹子',
                '59200031' => '紫色竹子',
                '59200032' => '桦树',
                '59200033' => '紫色桦树',
                '59200034' => '梅花',
                '59200035' => '紫色梅花',
                '59200036' => '梧桐',
                '59200037' => '紫色梧桐',
                '59200038' => '水杉',
                '59200039' => '紫色水杉',
//                 '59200040' => '桦树',
//                 '59200041' => '紫色桦树',
                
            ],
        ]
    ];
	
	public $_plantList = [
		'stone' => [59200044,59200046,59200048,59200050,59200052],
		'wood' => [59200024,59200026,59200028,59200030,59200032],
		'food' => [59200004,59200006,59200008,59200010,59200012,59200064],
	];
	
// 	public $_plantStone = ['ResID'=>59200048, 'num'=>2];
// 	public $_plantWood  = ['ResID'=>59200028, 'num'=>2];
// 	public $_plantFood  = ['ResID'=>59200008, 'num'=>2];
    public $_plantImportant = 59200064;
	
    public function main($user)
    {
        $this->_user = $user;
        Log::zyhx($this->_user['id'], '查看家园');
        $homeData = $this->getInfo($this->_user);
        if($homeData){
            Log::zyhx($this->_user['id'], '家园有'.$homeData['Plant']['ListNum'].'块土地有作物');
			Log::zyhx($this->_user['id'], "粮食{$homeData['FoodNum']} 木材{$homeData['WoodNum']} 石材{$homeData['StoneNum']}");
			
			$plantArr = ['food'=>$homeData['FoodNum'], 'wood'=>$homeData['WoodNum'], 'stone'=>$homeData['StoneNum']];
			$muValue = min($plantArr);
			$mu = array_search($muValue, $plantArr);
			$plantId = end($this->_plantList[$mu]);
			
            for($i=0;$i<6;$i++){
                $plantInfo = '';
                foreach ($homeData['Plant']['List'] as $val){
                    if($val['NurseryID'] == ($i+1))$plantInfo = $val;
                }
                if(empty($this->_user['h5token']))return false;
                if(!empty($plantInfo)){
                    $Mature = $plantInfo['MatureTime'] - time();
                    Log::zyhx($this->_user['id'], '土地'.$plantInfo['NurseryID'].' '.$this->_configData['plant']['crop'][$plantInfo['CropID']].",{$plantInfo['ProductNum']}/{$plantInfo['TotalNum']}，".($Mature > 0 ? "还有{$Mature}秒后成熟" : '可摘'));
                    if($Mature <= 0 && !empty($this->_user['h5token'])){
                        //已成熟，去采摘
                        $this->harvest($this->_user, $plantInfo, $plantId);
                    }
                }else{
                    //去种植
                    $this->add($this->_user, ($i+1), $plantId);
                }
            }
            
        }
        
    }
    
    
    /**
     * url:https://homeland.ffom.qq.com/home/simple?timestamp=1550283247&plat=1&token=a22828dc5da694b01fbbdfce645035d8&userdata=2
     * return:{"Data":{"HomeID":18020345973303003,"WorldID":2869,"GardenID":105,"HouseID":1,"Name":"","OwnerRole":6460484098682207206,"SpouseID":6460448867065478113,"Level":2,"ItemGID":0,"Pattern":1,"LastMaintenanceTime":1550246403,"FreezeTime":0,"CreateTime":1549417309,"AcquireTime":1549417309,"RecyleTime":0},"Building":{"ListNum":3,"List":[{"ResID":1,"Level":2,"UpgradeEndTime":0},{"ResID":2,"Level":2,"UpgradeEndTime":0},{"ResID":3,"Level":2,"UpgradeEndTime":0}],"LastUpgradeTime":1550172830},"Plant":{"PlantID":674,"ListNum":6,"List":[{"PlantID":669,"NurseryID":1,"CropID":59200044,"ProductResID":59200044,"ProductNum":25,"MatureTime":1550280142,"WitherTime":1550366542,"TotalNum":25,"RoleID":6460448867065478113},{"PlantID":670,"NurseryID":2,"CropID":59200044,"ProductResID":59200044,"ProductNum":25,"MatureTime":1550280147,"WitherTime":1550366547,"TotalNum":25,"RoleID":6460448867065478113},{"PlantID":671,"NurseryID":4,"CropID":59200044,"ProductResID":59200044,"ProductNum":25,"MatureTime":1550280151,"WitherTime":1550366551,"TotalNum":25,"RoleID":6460448867065478113},{"PlantID":672,"NurseryID":3,"CropID":59200044,"ProductResID":59200045,"ProductNum":25,"MatureTime":1550280154,"WitherTime":1550366554,"TotalNum":25,"RoleID":6460448867065478113},{"PlantID":673,"NurseryID":5,"CropID":59200044,"ProductResID":59200044,"ProductNum":25,"MatureTime":1550280158,"WitherTime":1550366558,"TotalNum":25,"RoleID":6460448867065478113},{"PlantID":674,"NurseryID":6,"CropID":59200044,"ProductResID":59200044,"ProductNum":25,"MatureTime":1550280162,"WitherTime":1550366562,"TotalNum":25,"RoleID":6460448867065478113}]},"PlaceNum":25,"GuestNum":0,"PlaceScore":528,"FoodNum":4656,"WoodNum":4788,"StoneNum":5075,"VegetealTimes":0,"userdata":"2"}
     */
    public function getInfo($user)
    {
        $url = $this->_config->zyhxUrl->url."/home/simple?timestamp=".time()."&plat=1&userdata=2&token={$user['h5token']}";
        $params = [];
        $params['HomeID']=18020345973303003;
        $result = Curl::zyhx($url, $params);
        if($result['code'] == 0){
            $data = $result['data'];
            $r = $this->dealZyhxResult($data, $user['id']);
            if(!$r)return false;
            return $data;
        }else{
            return false;
        }
    }
    
    public function harvest($user, $home, $plantId)
    {
        $url = $this->_config->zyhxUrl->url."/plant/harvest?timestamp=".time()."&plat=1&userdata=17&token={$user['h5token']}";
        $params = [];
        $params['HomeID']=18020345973303003;
        $params['NurseryID']=$home['NurseryID'];
        $params['RoleID']=(int)$user['uid'];
        
        
        $result = Curl::zyhx($url, $params);
        
        if($result['code'] == 0){
            $data = $result['data'];
            $r = $this->dealZyhxResult($data, $user['id']);
            if(!$r)return false;
            Log::zyhx($user['id'], '采摘'.$home['NurseryID'].'号土地，+'.$data['Num'].$this->_configData['plant']['crop'][$data['ResID']]);
            //去种植
            $this->add($user, $home['NurseryID'], $plantId);
            return true;
        }else{
            return false;
        }
    }
    
    public function add($user, $nurseryID, $plantId)
    {
        $url = $this->_config->zyhxUrl->url."/plant/add?timestamp=".time()."&plat=1&userdata=14&token={$user['h5token']}";
        $params = [];
        $params['HomeID']=18020345973303003;
        $params['NurseryID']=$nurseryID;
        $params['RoleID']=(int)$user['uid'];
        $params['ResID']= $plantId;
        
        if(isset($this->_plantImportant) && !empty($this->_plantImportant)){
            $params['ResID']=$this->_plantImportant;
        }else{
            if(isset($this->_plantStone) && isset($this->_plantWood) && isset($this->_plantFood)){
                $plant = $this->getInfo($user);
                if($plant){
                    $stoneNum = $woodNum = $foodNum = 0;
                    foreach ($plant['Plant']['List'] as $val){
                        if(in_array($val['CropID'], $this->_plantList['stone']))$stoneNum++;
                        if(in_array($val['CropID'], $this->_plantList['wood']))$woodNum++;
                        if(in_array($val['CropID'], $this->_plantList['food']))$foodNum++;
                    }
                    if($stoneNum < $this->_plantStone['num']){
                        $params['ResID']= $this->_plantStone['ResID'];
                    }elseif ($woodNum < $this->_plantWood['num']){
                        $params['ResID']= $this->_plantWood['ResID'];
                    }elseif ($foodNum < $this->_plantFood['num']){
                        $params['ResID']= $this->_plantFood['ResID'];
                    }
                }
            }
        }
        
        
        $result = Curl::zyhx($url, $params);
        
        if($result['code'] == 0){
            $data = $result['data'];
            $r = $this->dealZyhxResult($data, $user['id']);
            if(!$r)return false;
            Log::zyhx($user['id'], $nurseryID.'号土地，种植'.$this->_configData['plant']['crop'][$params['ResID']].'成功，在'.date('Y-m-d H:i:s', $data['Data']['MatureTime']).' 时成熟');
            return true;
        }else{
            return false;
        }
    }
    
    //POST /plant/list?timestamp=1550302616&plat=1&token=f40b80b85b7ec31b0c3d4486b02e18cd&userdata=13 HTTP/1.1
    //{"HomeID":18020345973303003,"OpenID":"o62FBvxeKw6R2pcSJWSt_ifAAzTo"}
    //
    
    
    //https://homeland.ffom.qq.com/plant/list?timestamp=1550733029&plat=1&token=b37f24886cc0ca1c405f159576c6f1e3&userdata=13
//     public function plantList()
//     {
        
//     }
    
    
    /**采摘单个土地
     * https://homeland.ffom.qq.com/plant/harvest?timestamp=1550302722&plat=1&token=f40b80b85b7ec31b0c3d4486b02e18cd&userdata=17
     * {"NurseryID":3,"HomeID":18020345973303003,"RoleID":6460484098682207206}
     * {"HomeID":18020345973303003,"NurseryID":3,"ResID":59200004,"Num":10,"userdata":"17"}
     * 
     * FoodNum=5481
     * StoneNum=6065
     * WoodNum=4788
     * 
     * 种植
     * https://homeland.ffom.qq.com/plant/add?timestamp=1550302806&plat=1&token=f40b80b85b7ec31b0c3d4486b02e18cd&userdata=14
     * {"HomeID":18020345973303003,"NurseryID":3,"RoleID":6460484098682207206,"ResID":59200004} //苹果
     * {"HomeID":18020345973303003,"NurseryID":2,"RoleID":6460484098682207206,"ResID":59200044}//风铃草
     * {"HomeID":18020345973303003,"NurseryID":4,"RoleID":6460484098682207206,"ResID":59200024}//槐树
     * 
     * {"HomeID":18020345973303003,"Data":{"PlantID":693,"NurseryID":3,"CropID":59200004,"ProductResID":0,"ProductNum":0,"MatureTime":1550303406,"WitherTime":1550389806,"TotalNum":0,"RoleID":6460484098682207206},"userdata":"14"}
     */
}