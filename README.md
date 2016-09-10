﻿

# 代码使用说明

## `src目录`
源代码目录下的`src`目录，称之为`src目录`
`src目录`目录下有3个文件:
- `index.php`
- `index.config.php`
- `.htaccess`

## `web目录`
在linux系统下，建议git clone整个代码到某个目录下，而不一定要直接放在`DOC_ROOT`下。
建议把`src目录`下的`index.php`和`.htaccess`这2个文件用符号链接到`DOC_ROOT`下的某目录下。
比如`DOC_ROOT/api/v1.0/`目录下。
该目录称之为`web目录`。

注：在windows系统下，由于没有符号链接，`src目录`和`web目录`通常是一样的。

## 配置文件
在linux系统下，并且`src目录`和`web目录`不一样时。
在`web目录`下，可复制一份`index.config.php`，并修改为自己的设置内容。
可实现修改配置而不用修改git原代码目录里的文件。

## api的URI格式
`API_ROOT_PATH/$api/$call/?xxxxxx`

## api对应的php文件
在`.htaccess`定义规则：

`RewriteRule ^([a-zA-Z]\w*)/([a-zA-Z]\w*)$ index.php?api=$1&call=$2&%{QUERY_STRING}	[L]`

$1:$api

$2:$call

每个api对应一个/apis/目录下的一个文件。
可以在`src目录/apis`，也可以在`web目录/apis`下。
`web目录/apis`优先。

## api URI和php文件的对应
要求：

php文件名：`"/api_$api.php"`

类名：`"class_$api"`

函数：`public static function $call() {}`

# 辅助函数

## `API::json`
`API::json($data,$jsonp='')`
用来返回json，如果jsonp指定了回调函数名，则返回jsonp。

如果URI里包含了一个非0的jsonp参数，也会自动返回jsonp。

比如API是这样的 `/api/apipath/apifunc?jsonp=FUNC_NAME`

会返回 `FUNC_NAME( {原json内容})`;


## `API::msg`
`API::msgmsg( $code, $msg='',$jsonp='')`是json的一个快捷函数。

## `API::db`
`API::db()`返回一个能用的`medoo`数据库对象。

## `API::input`
`API::input()`用于非 GET 方式 （POST, PUT, DELETE方式）的数据获取

# API 测试
`apitest`目录下可用来测试API的页面。