<?php
//WWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWWW
/*
api: hello
method: get, post, put, delete
功能：就是测试用的，没有什么别的功能。
*/
class class_hello{
    public static function main() {
      return class_hello::world();
    }
    public static function world() {
      $method=$_SERVER['REQUEST_METHOD'];;
      $res=['Welcome'=> "Hello, world!",
        'My time'=>api_g('time')];
      $res['data_GET']=$_GET;
      $res['data_POST']=$_POST; 
      $res['data_for_PUT_or_DELETE_or_POST']=API::input(); 
      $res['data_method']=$method;      
      $res['info']='GET OK @'.  api_g('time');
      return $res;
    }
}