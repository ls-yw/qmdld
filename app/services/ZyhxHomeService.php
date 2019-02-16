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
                '59200004' => '苹果',
            ],
        ]
    ];
    
    public function main($user)
    {
        Log::zyhx($user['id'], '查看家园');
        $homeData = $this->getInfo($user);
        if($homeData){
            Log::zyhx($user['id'], '家园有'.$homeData['Plant']['ListNum'].'块土地');
            foreach ($homeData['Plant']['List'] as $val){
                $Mature = $val['MatureTime'] - time();
                Log::zyhx($user['id'], '土地'.$val['NurseryID'].'，种植'.$this->_configData['plant']['crop'][$val['CropID']].",{$val['ProductNum']}/{$val['TotalNum']}，".($Mature > 0 ? "还有{$Mature}秒后成熟" : '已成熟'));
                if($Mature <= 0){
                    //已成熟，去采摘
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
            return $data;
        }else{
            return false;
        }
    }
}