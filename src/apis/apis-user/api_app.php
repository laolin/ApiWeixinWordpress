<?php
// ================================
/*
*/
namespace RequestByApiShell;

class class_app {

  /**
   * 接口： app/wx_code_to_token_uid
   * 用code换取用户登录票据和uid
   *
   * @request name: 公众号名称
   * @request code
   *
   * @return uid
   * @return token
   * @return tokenid
   * @return timestamp
   */
  public static function wx_code_login($request) {
    $code = $request->query['code'];
    $name = $request->query['name'];

    $json = \DJApi\API::post(SERVER_API_ROOT, "user/mix/wx_code_to_token_uid", ['name'=>$name, 'code'=>$code]);
    \DJApi\API::debug(['wx_code_to_token_uid', $json]);

    return $json;
  }

  /**
   * 接口： app/verify_token
   * 根据票据和签名，进行用户登录，获取uid
   * @request uid: 可选
   * @request tokenid
   * @request timestamp: 5分钟之内
   * @request sign: 签名 = md5($api.$call.$uid.$token.$timestamp) 或 md5($token.$timestamp)
   *
   * @return uid, 由于用户签名时，必须用到token, 所以，不再返回
   */
  public static function verify_token($request) {
    return \DJApi\API::post(SERVER_API_ROOT, "user/user/verify_token", $request->query);
  }


  /**
   * 接口： app/me
   * 根据票据和签名，进行用户登录，用户信息
   * @request tokenid
   * @request timestamp: 5分钟之内
   * @request sign: 签名 = md5($api.$call.$uid.$token.$timestamp) 或 md5($token.$timestamp)
   *
   * @return uid, 由于用户签名时，必须用到token, 所以，不再返回
   */
  public static function me($request) {
    $uid = \MyClass\SteeUser::sign2uid($request->query);
    \DJApi\API::debug(['sign2uid()', $uid, $request->query]);
    if(!$uid){
      return \DJApi\API::error(\DJApi\API::E_NEED_LOGIN, '未登录');
    }

    $data = ['uid' => $uid];

    $db = \DJApi\DB::db();
    $data['nFac'] = $db->count(\MyClass\SteeStatic::$table['steefac'], ['OR'=>['mark'=>'', 'mark#2'=>null]]);
    $data['nProj'] = $db->count(\MyClass\SteeStatic::$table['steeproj'], ['OR'=>['mark'=>'', 'mark#2'=>null]]);

    // 微信信息
    $wxInfoJson = \DJApi\API::post(SERVER_API_ROOT, "user/mix/wx_infos", ['uid'=>$uid, 'bindtype'=>'wx-unionid']);
    \DJApi\API::debug(['读取微信信息', $uid, $wxInfoJson]);
    if(\DJApi\API::isOk($wxInfoJson)){
      $data['wx'] = $wxInfoJson['datas']['list'][0];
    }

    $userRow = \MyClass\SteeUser::readSteeUser($uid);
    $data['me'] = $userRow;

    return \DJApi\API::OK($data);
  }

}
