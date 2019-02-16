<?php
namespace Library;

class Curl
{
    public static function post($url, $data=[])
    {
        $result = self::fetch($url, $data, 'post');
        return $result;
    }
    
    public static function dld($url, $data=[], $type='post', $first=true) {
        Log::write('curl','URL：'.$url, '', 'curl');
        Log::write('curl','报文参数：'.json_encode($data, JSON_UNESCAPED_UNICODE), '', 'curl');
        $isHttps = preg_match('/^https?:/i', $url) > 0 ? true : false;
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    
            if($isHttps == true){
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            }
    
            if($type == 'post'){
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            }
            
            //设置头部
            $headers = [];
            $headers[] = 'Referer: https://servicewechat.com/wxc7bdffeaa050ca4c/4/page-frame.html';
            $headers[] = 'Content-Type: application/x-www-form-urlencoded';
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            
            curl_setopt($ch, CURLOPT_TIMEOUT, 20);
            $result = curl_exec($ch);
            if($result === false){
                $res = ['code'=>1, 'msg'=>curl_error($ch)];
                Log::write('curl','请求失败，错误：'.json_encode($res['msg'], JSON_UNESCAPED_UNICODE), '', 'curl');
                return $res;
            }
            Log::write('curl','返回结果：'.json_encode($result, JSON_UNESCAPED_UNICODE), '', 'curl');
            $info = curl_getinfo($ch);
            if($info['http_code'] != '200'){
                $res = ['code'=>1, 'msg'=>$info['http_code']];
                return $res;
            }
            curl_close($ch);
            $result = json_decode($result, true);
            //系统繁忙，重新发起请求
            if($result['result'] == '109'){
                sleep(1);
                return $this->dld($url, $data, $type, false);
            }
            $res = ['code'=>0, 'data'=>$result];
            return $res;
        }catch (\Exception $e){
            $res = ['code'=>1, 'msg'=>$e->getMessage()];
            Log::write('curl','请求失败，错误：'.json_encode($res['msg'], JSON_UNESCAPED_UNICODE), '', 'curl');
            return $res;
        }
    }
    
    public static function fetch($url, $data=[], $type='post') {
        Log::write('curl','URL：'.$url, '', 'curl');
        Log::write('curl','报文参数：'.json_encode($data, JSON_UNESCAPED_UNICODE), '', 'curl');
        $isHttps = preg_match('/^https?:/i', $url) > 0 ? true : false;
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            
            if($isHttps == true){
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            }
            
            if($type == 'post'){
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
            }
            curl_setopt($ch, CURLOPT_TIMEOUT, 20);
            $result = curl_exec($ch);
            if($result === false){
                $res = ['error_code'=>1, 'msg'=>curl_error($ch)];
                Log::write('curl','请求失败，错误：'.json_encode($res['msg'], JSON_UNESCAPED_UNICODE), '', 'curl');
                return $res;
            }
            Log::write('curl','返回结果：'.json_encode($result, JSON_UNESCAPED_UNICODE), '', 'curl');
            $info = curl_getinfo($ch);
            if($info['http_code'] != '200'){
                $res = ['error_code'=>1, 'msg'=>$info['http_code']];
                return $res;
            }
            curl_close($ch);
            $res = ['error_code'=>0, 'data'=>$result];
            return $res;
        }catch (\Exception $e){
            $res = ['error_code'=>1, 'msg'=>$e->getMessage()];
            Log::write('curl','请求失败，错误：'.json_encode($res['msg'], JSON_UNESCAPED_UNICODE), '', 'curl');
            return $res;
        }
    }
    
    
    public static function zyhx($url, $data, $type='post', $first=true) {
        Log::write('curl','URL：'.$url, '', 'curl');
        Log::write('curl','报文参数：'.json_encode($data, JSON_UNESCAPED_UNICODE), '', 'curl');
        $isHttps = preg_match('/^https?:/i', $url) > 0 ? true : false;
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    
            if($isHttps == true){
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            }
    
            if($type == 'post'){
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, is_array($data) ? json_encode($data) : $data);
            }
    
            //设置头部
            $headers = [];
            $headers[] = 'Referer: https://homeland.ffom.qq.com';
            $headers[] = 'Content-Type: text/plain';
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    
            curl_setopt($ch, CURLOPT_TIMEOUT, 20);
            $result = curl_exec($ch);
            if($result === false){
                $res = ['code'=>1, 'msg'=>curl_error($ch)];
                Log::write('curl','请求失败，错误：'.json_encode($res['msg'], JSON_UNESCAPED_UNICODE), '', 'curl');
                return $res;
            }
            Log::write('curl','返回结果：'.json_encode($result, JSON_UNESCAPED_UNICODE), '', 'curl');
            $info = curl_getinfo($ch);
            if($info['http_code'] != '200'){
                $res = ['code'=>1, 'msg'=>$info['http_code']];
                return $res;
            }
            curl_close($ch);
            $result = json_decode($result, true);
            //系统繁忙，重新发起请求
//             if($result['result'] == '109'){
//                 sleep(1);
//                 return $this->dld($url, $data, $type, false);
//             }
            $res = ['code'=>0, 'data'=>$result];
            return $res;
        }catch (\Exception $e){
            $res = ['code'=>1, 'msg'=>$e->getMessage()];
            Log::write('curl','请求失败，错误：'.json_encode($res['msg'], JSON_UNESCAPED_UNICODE), '', 'curl');
            return $res;
        }
    }
}