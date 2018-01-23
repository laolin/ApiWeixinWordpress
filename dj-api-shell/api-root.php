<?php
/**
 * PHP 接口通用定义
 *
 */

namespace DJApi;

class Configs {
  static $values = [];

  /**
   * 设置一个配置值
   */
  static function set($keys, $value){
    if(!is_array($key)){
      $keys = [$keys];
    }
    $arr = &self::$values;
    foreach($keys as $k){
      if(!is_array($arr)) $arr = [];
      if(!isset($arr[$k])) $arr[$k] = '';
      $arr = &$arr[$k];
    }
    return $arr = $value;
  }

  /**
   * 读取一个设置值
   */
  static function get($keys){
    if(!is_array($keys)){
      $keys = [$keys];
    }
    $arr = &self::$values;
    foreach($keys as $k){
      if(!isset($arr[$k])) return "";
      $arr = &$arr[$k];
    }
    return $arr;
  }

  /**
   * 从当前文件夹的某个上级文件夹开始查找配置文件，并使用配置
   */
  static function readConfig($fileName = 'config.inc.php', $deep = 5, $path = ''){
    if(!$path){
      $path = dirname($_SERVER['PHP_SELF']);
    }
    if($deep > 0 && strlen($path) > 1){
      self::readConfig($fileName, $deep - 1, dirname($path));
    }
    if(file_exists("{$_SERVER['DOCUMENT_ROOT']}$path/$fileName")){
      require_once("{$_SERVER['DOCUMENT_ROOT']}$path/$fileName");
    }
  }
}

class API{
  const E_NEED_LOGIN         = 1;  // 未登录
  const E_NEED_RIGHT         = 2;  // 权限不足
  const E_API_NOT_EXITS      = 101;
  const E_CLASS_NOT_EXITS    = 102;
  const E_FUNCTION_NOT_EXITS = 103;


  static function cn_json($arr){
    return json_encode($arr, JSON_UNESCAPED_UNICODE);
  }

  static function toJson($res) {
    if(is_array($res)){
      $json = ['errcode'=>$res[0]];
      if($res[0]){
        $json['errmsg'] = $res[1];
        if(isset($res[2]) && $res[2] !== false)$json['datas' ] = $res[2];
      }
      else{
        $json['datas'] = $res[1];
      }
      return $json;
    }
    return json_decode($res, true);
  }

  public static function error($code, $msg='error', $additional_datas = false) {
    return self::toJson([$code, $msg, $additional_datas]);
  }

  public static function OK($datas = []) {
    return self::toJson([0, $datas]);
  }

  /**
   * 两个旧版函数
   */
  public static function msg($code, $msg='error', $additional_datas = false) {
    return self::error($code, $msg, $additional_datas);
  }
  public static function datas($datas = []) {
    return self::OK([0, $datas]);
  }

  /**
   * 向其它模块发出请求
   * 方式：curl, post
   */
  static function post($module, $api, $param) {
    $url = $module . $api;
    $res = self::httpPost($url, $param);
    return self::toJson($res);
  }

  /**
   * 向其它模块发出请求
   * 方式：curl, get
   */
  static function get($url) {
    $res = self::httpGet($url, $param);
    return self::toJson($res);
  }
  // ┏━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┓
  // ┃             后台网页请求                                               ┃
  // ┗━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━┛
  static function httpGet($url) {
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 500);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_URL, $url);
    $res = curl_exec($curl);
    curl_close($curl);
    return $res;
  }

  static function httpPost($url, $param) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt ($ch, CURLOPT_REFERER, "http://pgy");
    curl_setopt($ch, CURLOPT_POST, 1);
    if(is_array($param) && count($param)>0){
      curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($param));
    }
    else if(is_string($param)){
      curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
    }
    else{
      curl_setopt($ch, CURLOPT_POSTFIELDS, []);
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //信任任何证书
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); // 检查证书中是否设置域名,0不验证
    $output = curl_exec($ch);
    //$info = curl_getinfo($ch);
    //error_log(curl_error($ch));
    curl_close($ch);
    //if($output===false)return curl_error($ch);
    return $output;
  }
}
