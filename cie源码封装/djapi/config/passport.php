<?php
/**
 * Created by PhpStorm.
 * User: weida
 * Date: 2017-07-24
 * Time: 14:58
 */


return [
    // 系统配置参数
    'sign_key' 			=> 'aiyindj372jdjhsj',// 签名sign_key
    'token_key'			=> 'terstetsestes',// Token校验key
    'token_expire'		=> 30*24*60,// Token有效期 单位：分钟

    'register_type' 		=> array(1,2,3),// 1手机号|2微信唯一标识|3QQ唯一标识
    'login_type'	 		=> array(1,2,3),// 1pc2ios3android


	// 短信验证吗
    'sms_interval_time'	=> 60,// 发送短息验证码间隔时间，单位:秒
	'sms_expire' 			=> 5,// 短息验证码有效时间，单位:分钟
	'sms_appid'			=> '1400032145',
	'sms_appkey'			=> 'd7fdf8c54aff14e105b1539178f30071',


    // 资源路径配置
    'register_url'	=> 'http://user.aiyin.com/public',// 底层注册url
    'img_url'    		=> 'http://apis.dianjin.com/public/',// 用户上传图片路径
	'img'				=> 'http://apis.dianjin.com/public/',// 系统图片路径 活动、游戏等图片资源
	'qrcode_path'		=> public_path('img').'/qrcode/',
	
	// 微信小程序配置
	'wx_xcx'	=> array(
		'appid' => 'wx9a68d4bde36aa73f',
		'secret'=> 'dbf7cc3f26b3585ef1b1c66d9c3a8fac',
	),

	// 前端战队二维码分享-跳转地址
	'qrcode_url'	=> 'pages/personal/index',
	// 'qrcode_url'	=> 'https://www.baidu.com',



];
