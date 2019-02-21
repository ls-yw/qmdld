<?php
namespace Services;

use Basic\BaseService;
use Models\User;
use Library\Log;

class ZyhxLoginService extends BaseService
{
    
    public function updateToken($userId)
    {
        $basePath = '/data/html/my/tmp/';
        
        $path = $basePath.'ffom.txt';
        if(!file_exists($path))return false;
        
        $content = file_get_contents($path, FILE_IGNORE_NEW_LINES);
        $content = iconv('UCS-2LE', 'UTF-8', $content);
        $newPath = $basePath.'ffom'.date('YmdHis').'.txt';
        $r = file_put_contents($newPath, $content);
        if($r)unlink($path);
        echo '1';
        $content = file($newPath);
        
        $user = (new User())->getById($userId);
        $role = $user['uid'];
        
        $muArr = [];
        if(!empty($content)){
            foreach ($content as $key => $val){
                $match = $nextMatch = [];
                $n = preg_match('/role\/info\?timestamp=[\d]+&plat=\d&token=([^&]*)/i', $val, $match);
                if($n > 0){
                    if(empty($match[1]))continue;
        
                    $nextContent = $content[$key+1];
                    $m = preg_match('/"RoleID":(\d+)/i', $nextContent, $nextMatch);
                    if($nextMatch[1] == $role)$muArr[] = $match[1];
                }
            }
        }
        print_r($muArr);
        if(!empty($muArr)){
            $newToken = end($muArr);
            if($newToken == $user['h5token']){
                Log::write('login', '新token和原token相同，file:'.$newPath.'。muArr:'.json_encode($muArr));
                return false;
            }
            return $newToken;
        }
    }
}