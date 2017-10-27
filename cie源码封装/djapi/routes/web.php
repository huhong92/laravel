<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// 开发环境路由
Route::group(['namespace' => 'V0'], function(){
	Route::any('swagger/doc', 'SwaggerController@doc');
	Route::any('user/update2', 'UserController@updateUserView');

	// 用户模块
	Route::any('user/register_third', 'UserController@registerThirdParty');
	Route::any('user/info', 'UserController@userInfo');
	Route::any('user/update', 'UserController@updateUserInfo');
	Route::any('user/sms', 'UserController@getSMS');
	Route::any('user/auth_mobile', 'UserController@authMobile');
	Route::any('user/auth_idcard', 'UserController@authIDcard');

	// 约战模块
  Route::any('pmatch/myreserve_list', 'PmatchController@myReserveList');  //获取我的约战列表
	Route::any('pmatch/myrecord_list', 'PmatchController@myRecordList'); //获取我的历史战绩
	Route::any('pmatch/reserve_list', 'PmatchController@reserveList'); //获取约战列表
	Route::any('pmatch/num', 'PmatchController@num');  //获取未完成约战数
	Route::any('pmatch/reserve', 'PmatchController@reserve'); //发布约战
	Route::any('pmatch/do_pmatch', 'PmatchController@doPmatch'); //迎战|组队操作
	Route::any('pmatch/do_result', 'PmatchController@doResult'); //比赛结果处理
	Route::any('pmatch/upload_img', 'PmatchController@uploadImg'); //上传比赛截图

	// 大赛报名模块
	Route::any('match/game_list', 'MatchController@gameList');// 获取--大赛可报名游戏列表
	Route::any('match/myapply_list', 'MatchController@myApplyList');// 获取--我的大赛报名列表
	Route::any('match/mygame_list', 'MatchController@myGameList');// 获取--我的游戏列表
	Route::any('match/mygame_info', 'MatchController@myGameInfo');// 获取--我的游戏信息
	Route::any('match/apply_info', 'MatchController@gameApplyInfo');// 获取--游戏报名所需填写信息
	Route::any('match/save_mygameinfo', 'MatchController@saveMyGameInfo');// 完善我的游戏信息
	Route::any('match/matchzone_list', 'MatchController@matchZoneList');// 获取-游戏赛区列表
	Route::any('match/create_group', 'MatchController@createGroup');// 创建战队
	Route::any('match/group_list', 'MatchController@groupList');// "获取--战队列表"
	Route::any('match/group_info', 'MatchController@groupInfo');// 获取--战队详情
	Route::any('match/groupuser_info', 'MatchController@groupUserInfo');// 获取-战队成员详情
	Route::any('match/group_url', 'MatchController@groupUrl');// 获取--战队分享URL[战队分享二维码]"
	Route::any('match/apply_group', 'MatchController@applyGroup');// 申请加入战队
	Route::any('match/groupapply_list', 'MatchController@groupApplyList');// 获取--战队申请列表
	Route::any('match/do_group', 'MatchController@doGroup');// 战队操作[1同意加入战队2忽略3退出战队]
	Route::any('match/groupmsg_list', 'MatchController@groupMsgList');// 获取--战队消息列表

	Route::any('match/qrcode', 'MatchController@QRcode');// 获取--战队分享二维码
	Route::any('match/share', 'MatchController@share');// 战队二维码分享


	// 公共功能模块
	Route::any('pub/decode_qrcode', 'PubController@decodeQRcode');// 解析二维码
	Route::any('pub/generate_qrcode', 'PubController@genreateQRcode');// 生成普通二维码
	Route::any('pub/generate2_qrcode', 'PubController@generateQRcode2');// 生成固定格式二维码
	Route::any('pub/encode_qrcode', 'PubController@encodeQRcode');
	Route::any('pub/result_statics', 'PubController@pmatchResultStatics');



});


// Version 1
Route::group(['namespace' => 'V1'], function(){
	// swagger文档
	Route::any('swagger/doc/v1', 'SwaggerController@doc');

	// 用户模块
	Route::any('user/register_third/v1', 'UserController@registerThirdParty');
	Route::any('user/info/v1', 'UserController@userInfo');
	Route::any('user/update/v1', 'UserController@updateUserInfo');
	Route::any('user/sms/v1', 'UserController@getSMS');
	Route::any('user/auth_mobile/v1', 'UserController@authMobile');
	Route::any('user/auth_idcard/v1', 'UserController@authIDcard');

	// 约战模块
	Route::any('pmatch/list/v1', 'PmatchController@pmatchList');

	// 大赛报名模块
		Route::any('match/list/v1', 'MatchController@matchList');
		Route::any('match/apply_list/v1', 'MatchController@applyList');// 游戏参赛列表
		Route::any('match/myapply_list/v1', 'MatchController@myApplyList');// 我的参赛列表
		Route::any('match/game_info/v1', 'MatchController@gameApplyInfo');// 游戏需填写信息
});

// Version 2
Route::group(['namespace' => 'V2'], function(){
	// swagger文档
	Route::any('swagger/doc/v2', 'SwaggerController@doc');

	// 用户模块
	Route::any('user/register_third/v2', 'UserController@registerThirdParty');
	Route::any('user/info/v2', 'UserController@userInfo');
	Route::any('user/update/v2', 'UserController@updateUserInfo');
	Route::any('user/sms/v2', 'UserController@getSMS');
	Route::any('user/auth_mobile/v2', 'UserController@authMobile');
	Route::any('user/auth_idcard/v2', 'UserController@authIDcard');

	// 约战模块
	Route::any('pmatch/list/v2', 'PmatchController@pmatchList');

	// 大赛报名模块
	Route::any('match/list/v2', 'MatchController@matchList');
	Route::any('match/apply_list/v2', 'MatchController@applyList');// 游戏参赛列表
	Route::any('match/myapply_list/v2', 'MatchController@myApplyList');// 我的参赛列表
	Route::any('match/game_info/v2', 'MatchController@gameApplyInfo');// 游戏需填写信息
});
