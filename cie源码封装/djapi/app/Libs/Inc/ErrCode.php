<?php
// namespace app\Libs\Inc;

class ErrCode {

	const ERR_OK =   '0000'; //成功

    //1001  --- 1099  系统错误
    const ERR_PARAM_SIGN 			=   '1001'; //签名认证不通过
    const ERR_DB 					    =   '1002'; //服务器异常
    const ERR_PARA 					=   '1003'; //参数错误
    const ERR_FILE 					=   '1004'; //操作文件失败
    const ERR_DB_NO_DATA 			=   "1005";//没有可操作的数据
    const ERR_LOGIN_TOKEN_FAIL 		=   '1006'; //令牌认证失败，请重新登录
    const ERR_LOGIN_TOKEN_EXPIRE 	=   '1007'; //令牌已过期，请重新登录
    const ERR_MC_FAIL 				=   '1008'; //mc异常
    const ERR_TOKEN_EMPTY   			=   '1009';// token不能为空
	const ERR_UPLOAD_FILE_FAIL  	=   '1010';// 文件上传失败

	const ERR_REQUEST_METHOD 		=   '1097'; //请使用正确的请求方式
	const ERR_NOT_FOUND 				=   '1098'; //路由错误
    const ERR_UNKOWNL 				=   '1099'; //未知错误

    // 2001  --- 2099  用户错误
    const ERR_USER_REGISTER_FALI    			=   '2001'; //用户注册失败
    const ERR_USER_REGISTER_TYPE_FALI    		=   '2002'; //用户注册类型填写失败
    const ERR_MOBILE_FROMAT_FALI    			=   '2003'; //手机号格式错误
    const ERR_MOBILE_SMS_FAST_FALI    			=   '2004'; //验证码发送太过频繁
    const ERR_MOBILE_SMS_EXPIRE_FALI    		=   '2005'; //验证码已失效
    const ERR_MOBILE_SMS_CODE_FALI    			=   '2006'; //验证码错误
    const ERR_BIND_MOBILE_FALI  				=   '2007'; //手机号绑定认证失败
    const ERR_GET_WXXCX_OPENID_FALI    		=   '2008'; //微信小程序openId、session凭证获取失败
    const ERR_OTHER_THIRD_NOT_COMPLET_FALI	=   '2009'; //其他第三方注册未开发
	const ERR_UPDATE_USER_FALI    				=   '2010'; //用户信息修改失败






    // 3001  --- 3099  约战错误
		const ERR_MATCH_PUBLISH_FALI                =   '3001'; //约战发布失败
    const ERR_NOT_UPLOAD_GAMEPIC_FALI           =   '3002'; //未上传游戏截图
    const ERR_UPLOAD_GAMEPIC_TYPE_FALI          =   '3003'; //上传游戏截图类型错误
    const ERR_MATCH_PUBLISH_TIME_CONFLICT       =   '3004'; //约战发布时间冲突


    //  4001  --- 4099  报名大赛
    const ERR_GACCOUNT_NOTALLOW_EMPTY_FALI    =   '4001'; //请填写游戏账号
    const ERR_GNICKNAME_NOTALLOW_EMPTY_FALI 	=   '4002'; //请填写游戏昵称
    const ERR_GLEVEL_NOTALLOW_EMPTY_FALI    	=   '4003'; //请填写游戏Level
    const ERR_GZONE_NOTALLOW_EMPTY_FALI    	=   '4004'; //请选择游戏区服
    const ERR_GZONE_ID_FALI    					=   '4005'; //请选择正确的区服ID
	const ERR_NOT_TEAMGAME    					=   '4006'; //该游戏不是团队赛
	const ERR_GROUP_NAME_EXISTS    				=   '4007'; //该战队名已存在，请重新输入
	const ERR_GAME_GROUP_EXISTS    				=   '4008'; //你已有该游戏战队，请退出其它战队
	const ERR_CREATE_GROUP_FAILE  				=   '4009'; //战队创建失败
	const ERR_GROUP_NAME_TOO_LONG  				=   '4010'; //战队名称过长
	const ERR_MATCH_ZONE_ID_FAIL  				=   '4011'; //请选择正确的赛区
	const ERR_GROUP_USER_ADD_FAIL  				=   '4012'; //战队加入失败
	const ERR_GROUP_NOT_EXISTS_FAIL  			=   '4013'; //暂无该战队信息
	const ERR_ENTER_GROUP_FAIL  				=   '4014'; //战队申请失败
	const ERR_NOTALLOW_GET_GROUPLIST_FAIL  	=   '4015'; //暂无权限查看申请列表
	const ERR_DO_GROUP_FAIL  					=   '4016'; //战队操作失败
	const ERR_DO_GROUP_TYPE_FAIL  				=   '4017'; //请选择正确的战队操作类型
	const ERR_GROUP_USER_NOT_EMPTY_FAIL  		=   '4018'; //请选择用户
	const ERR_NOT_ALLOW_DEL_GROUP_USER_FAIL  	=   '4019'; //你没有权限踢出队长
	const ERR_DEL_GROUP_USER_FAIL  				=   '4020'; //队员踢出失败
	const ERR_ADD_GROUP_USER_FAIL  				=   '4021'; //队员加入失败
	const ERR_SELF_DEL_GROUP_FAIL  				=   '4022'; //队员自动退队失败
	const ERR_IGOLE_GROUP_USER_FAIL  			=   '4023'; //队员忽略失败，请稍后再试
	const ERR_NOT_ALLOW_ADD_VICE_FAIL  		=   '4024'; //你没有权限指定副队长
	const ERR_EXISTS_GROUP_VICE_FAIL  			=   '4025'; //该战队已有副队长
	const ERR_ZD_VICE_FAIL  						=   '4026'; //副队长指定失败
	const ERR_EXISTS_INVITATION_FAIL  			=   '4027'; //你已提交申请，请勿重复提交

	const ERR_GET_WECHAT_ACCESS_TOKEN_FAIL  	=   '4028'; //获取微信access token 失败
	const ERR_GET_WECHAT_QRCODE_FAIL  			=   '4029'; //二维码获取失败
	const ERR_INSERT_SHARE_FAIL  				=   '4030'; //战队分享记录插入失败


	//  5001  --- 5099  Pub公共模块
	const ERR_DECODE_QRCODE_FAIL  				=   '5001'; //二维码解析失败


	//  6001  --- 6099  支付模块




}
