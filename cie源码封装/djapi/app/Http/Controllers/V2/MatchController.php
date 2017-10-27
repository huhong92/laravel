<?php

namespace App\Http\Controllers\V0;

use Validator;
use App\Http\Controllers\Controller;
use App\Http\Repositories\V0\MatchRepository;
use Illuminate\Http\Request;
use ErrCode;

/**
 * Class PmatchController 大赛模块
 * @package App\Http\Controllers\v0
 * @author huhong
 * @date 2017-08-04 15:00:00
 */
class MatchController extends Controller
{
    protected $match_repo;
    public function __construct(MatchRepository $match_repo)
    {
        parent::__construct(false);
        $this->match_repo	= $match_repo;
        $this->error_   	= $match_repo->error_;
    }

	/**
	 * @SWG\Get(
	 *   path="/match/game_list",
	 *   summary="获取--大赛可报名游戏列表",
	 *   tags={"大赛模块"},
	 *   @SWG\Parameter(name="uuid",  in="query", required=true, description="用户uuid", type="string"),
	 *   @SWG\Parameter(name="token", in="query", required=true, description="token令牌", type="string"),
	 *   @SWG\Parameter(name="sign",  in="query", required=true, description="签名", type="string"),
	 *   @SWG\Parameter(name="page",  in="query", required=true, description=" 分页起始页 1开始", type="integer"),
	 *   @SWG\Parameter(name="count", in="query", required=true, description="每页显示条数", type="integer"),
	 *   @SWG\Parameter(name="type",  in="query", required=true, description="列表类型[1个人赛2团队赛]", type="array",@SWG\Items(type="integer",enum={"1", "2"},default="1")),
	 *   @SWG\Response(
	 *     response=200,
	 *     description="操作成功"
	 *   ),
	 *   @SWG\Response(
	 *     response="default",
	 *     description="an ""unexpected"" error"
	 *   )
	 * )
	 */
    public function gameList(Request $request)
    {
        // 接收参数
        $params 			= $this->getPublicParams($request);
        if (!$params) return $this->return_output_json();
        $params['page'] 	= (int)$request->input('page');// 起始page 1开始
        $params['count']	= (int)$request->input('count');// 每页显示条数
        $params['type'] 	= (int)$request->input('type');// 1个人赛2团队赛

        // 校验参数
        if (!$params['page'] || !$params['count'] || !$params['type']) {
            $this->error_->set_error(ErrCode::ERR_PARA);
            writeLog('参数错误',$params);
            return $this->return_output_json();
        }

        // 校验签名
        if (!checkSign($params,$params['sign'])) {
            $this->error_->set_error(ErrCode::ERR_PARAM_SIGN);
            writeLog('签名错误',$params);
            return $this->return_output_json();
        }

        // 获取个人赛报名列表
        $data   = $this->match_repo->getGameList($params);
        if (!$data) {
            return $this->return_output_json();
        }
        // 返回结果
        return $this->return_output_json($data);
    }

	/**
	 * @SWG\Get(
	 *   path="/match/myapply_list",
	 *   summary="获取--我的大赛报名列表",
	 *   tags={"大赛模块"},
	 *   @SWG\Parameter(name="uuid",  in="query", required=true, description="用户uuid", type="string"),
	 *   @SWG\Parameter(name="token", in="query", required=true, description="token令牌", type="string"),
	 *   @SWG\Parameter(name="sign",  in="query", required=true, description="签名", type="string"),
	 *   @SWG\Response(
	 *     response=200,
	 *     description="操作成功"
	 *   ),
	 *   @SWG\Response(
	 *     response="default",
	 *     description="an ""unexpected"" error"
	 *   )
	 * )
	 */
    public function myApplyList(Request $request)
    {
        // 接收参数
        $params	= $this->getPublicParams($request);
        if (!$params) return $this->return_output_json();

        // 校验签名
        if (!checkSign($params,$params['sign'])) {
            $this->error_->set_error(ErrCode::ERR_PARAM_SIGN);
            writeLog('参数错误',$params);
            return $this->return_output_json();
        }

        // 获取我的大赛报名列表
        $data   = $this->match_repo->getMyApplyList($params);
        if (!$data) {
            return $this->return_output_json();
        }
        // 返回结果
        return $this->return_output_json($data);
    }

	/**
	 * @SWG\Get(
	 *   path="/match/mygame_list",
	 *   summary="获取--我的游戏列表",
	 *   tags={"大赛模块"},
	 *   @SWG\Parameter(name="uuid",  in="query", required=true, description="用户uuid", type="string"),
	 *   @SWG\Parameter(name="token", in="query", required=true, description="token令牌", type="string"),
	 *   @SWG\Parameter(name="sign",  in="query", required=true, description="签名", type="string"),
	 *   @SWG\Parameter(name="page",  in="query", required=true, description=" 分页起始页 1开始", type="integer"),
	 *   @SWG\Parameter(name="count", in="query", required=true, description="每页显示条数", type="integer"),
	 *   @SWG\Response(
	 *     response=200,
	 *     description="操作成功"
	 *   ),
	 *   @SWG\Response(
	 *     response="default",
	 *     description="an ""unexpected"" error"
	 *   )
	 * )
	 */
    public function myGameList(Request $request)
	{
		// 接收参数
		$params 		= $this->getPublicParams($request);
		if (!$params) return $this->return_output_json();
		$params['page'] 	= (int)$request->input('page');// 起始page 1开始
		$params['count']	= (int)$request->input('count');// 每页显示条数

		// 校验参数
		if (!$params['page'] || !$params['count']) {
			$this->error_->set_error(ErrCode::ERR_PARA);
			writeLog('参数错误',$params);
			return $this->return_output_json();
		}

		// 校验签名
		if (!checkSign($params,$params['sign'])) {
			$this->error_->set_error(ErrCode::ERR_PARAM_SIGN);
			writeLog('参数错误',$params);
			return $this->return_output_json();
		}
		// 获取我的游戏信息
		$data	= $this->match_repo->getMygameList($params);
		// 返回结果
		return $this->return_output_json($data);
	}

	/**
	 * @SWG\Get(
	 *   path="/match/mygame_info",
	 *   summary="获取--我的游戏信息",
	 *   tags={"大赛模块"},
	 *   @SWG\Parameter(name="uuid",  in="query", required=true, description="用户uuid", type="string"),
	 *   @SWG\Parameter(name="token", in="query", required=true, description="token令牌", type="string"),
	 *   @SWG\Parameter(name="sign",  in="query", required=true, description="签名", type="string"),
	 *   @SWG\Parameter(name="id",  in="query", required=true, description="游戏ID", type="integer"),
	 *   @SWG\Response(
	 *     response=200,
	 *     description="操作成功"
	 *   ),
	 *   @SWG\Response(
	 *     response="default",
	 *     description="an ""unexpected"" error"
	 *   )
	 * )
	 */
	public function myGameInfo(Request $request)
	{
		// 接收参数
		$params 		= $this->getPublicParams($request);
		if (!$params) return $this->return_output_json();
		$params['id']	= (int)$request->input('id');// 游戏ID

		// 校验参数
		if (!$params['id']) {
			$this->error_->set_error(ErrCode::ERR_PARA);
			writeLog('参数错误',$params);
			return $this->return_output_json();
		}

		// 校验签名
		if (!checkSign($params,$params['sign'])) {
			$this->error_->set_error(ErrCode::ERR_PARAM_SIGN);
			writeLog('参数错误',$params);
			return $this->return_output_json();
		}
		// 获取我的游戏信息
		$data	= $this->match_repo->getMygameInfo($params);
		// 返回结果
		return $this->return_output_json($data);
	}

	/**
	 * @SWG\Get(
	 *   path="/match/apply_info",
	 *   summary="获取--游戏报名所需填写信息",
	 *   tags={"大赛模块"},
	 *   @SWG\Parameter(name="uuid",  in="query", required=true, description="用户uuid", type="string"),
	 *   @SWG\Parameter(name="token", in="query", required=true, description="token令牌", type="string"),
	 *   @SWG\Parameter(name="sign",  in="query", required=true, description="签名", type="string"),
	 *   @SWG\Parameter(name="id",    in="query", required=true, description="游戏ID", type="integer"),
	 *   @SWG\Response(
	 *     response=200,
	 *     description="操作成功"
	 *   ),
	 *   @SWG\Response(
	 *     response="default",
	 *     description="an ""unexpected"" error"
	 *   )
	 * )
	 */
    public function gameApplyInfo(Request $request)
    {
        // 接收参数
        $params 		= $this->getPublicParams($request);
        if (!$params) return $this->return_output_json();
        $params['id']	= (int)$request->input('id');// 游戏ID

        // 校验参数
        if (!$params['id']) {
            $this->error_->set_error(ErrCode::ERR_PARA);
            writeLog('参数错误',$params);
            return $this->return_output_json();
        }

        // 校验签名
        if (!checkSign($params,$params['sign'])) {
            $this->error_->set_error(ErrCode::ERR_PARAM_SIGN);
            writeLog('参数错误',$params);
            return $this->return_output_json();
        }

        // 获取报名赛事报名列表
        $data   = $this->match_repo->getGameApplyInfo($params);
        if (!$data) {
            return $this->return_output_json();
        }
        // 返回结果
        return $this->return_output_json($data);
    }

	/**
	 * @SWG\Post(
	 *   path="/match/save_mygameinfo",
	 *   summary="完善我的游戏信息",
	 *   tags={"大赛模块"},
	 *   @SWG\Parameter(name="uuid",  in="query", required=true, description="用户uuid", type="string"),
	 *   @SWG\Parameter(name="token", in="query", required=true, description="token令牌", type="string"),
	 *   @SWG\Parameter(name="sign",  in="query", required=true, description="签名", type="string"),
	 *   @SWG\Parameter(name="id",     in="query", required=true, description=" 游戏ID", type="integer"),
	 *   @SWG\Parameter(name="account", in="query", required=true, description="游戏账号", type="string"),
	 *   @SWG\Parameter(name="nickname",in="query", required=false, description="游戏昵称", type="string"),
	 *   @SWG\Parameter(name="level",   in="query", required=false, description="游戏等级", type="string"),
	 *   @SWG\Parameter(name="z_id",    in="query", required=false, description="游戏区服ID", type="integer"),
	 *     @SWG\Response(
	 *     response=200,
	 *     description="操作成功"
	 *   ),
	 *   @SWG\Response(
	 *     response="default",
	 *     description="an ""unexpected"" error"
	 *   )
	 * )
	 */
    public function saveMyGameInfo(Request $request)
    {
        // 接收参数
        $params 				= $this->getPublicParams($request);
        if (!$params) return $this->return_output_json();
        $params['id']			= (int)$request->input('id');// 游戏id
        $params['account']		= $request->input('account');
        $params['nickname']	= urldecode($request->input('nickname'));
        $params['level']   	= $request->input('level');
        $params['z_id']     	= $request->input('z_id');

        // 校验参数
        if (!$params['id']) {
            $this->error_->set_error(ErrCode::ERR_PARA);
            writeLog('参数错误',$params);
            return $this->return_output_json();
        }

		// 校验签名
		if (!checkSign($params,$params['sign'])) {
			$this->error_->set_error(ErrCode::ERR_PARAM_SIGN);
			writeLog('参数错误',$params);
			return $this->return_output_json();
		}

        // 完善游戏信息
        $this->match_repo->doSaveMyGameInfo($params);
        return $this->return_output_json();
    }


	/**
	 * @SWG\Get(
	 *   path="/match/matchzone_list",
	 *   summary="获取-游戏赛区列表",
	 *   tags={"大赛模块"},
	 *   @SWG\Parameter(name="uuid",  in="query", required=true, description="用户uuid", type="string"),
	 *   @SWG\Parameter(name="token", in="query", required=true, description="token令牌", type="string"),
	 *   @SWG\Parameter(name="sign",  in="query", required=true, description="签名", type="string"),
	 *   @SWG\Parameter(name="id",    in="query", required=true, description="游戏ID", type="integer"),
	 *   @SWG\Response(
	 *     response=200,
	 *     description="操作成功"
	 *   ),
	 *   @SWG\Response(
	 *     response="default",
	 *     description="an ""unexpected"" error"
	 *   )
	 * )
	 */
    public function matchZoneList(Request $request)
	{
		// 接收参数
		$params 		= $this->getPublicParams($request);
		if (!$params) return $this->return_output_json();
		$params['id']	= (int)$request->input('id');// 游戏ID

		// 校验参数
		if (!$params['id']) {
			$this->error_->set_error(ErrCode::ERR_PARA);
			writeLog('参数错误',$params);
			return $this->return_output_json();
		}

		// 校验签名
		if (!checkSign($params,$params['sign'])) {
			$this->error_->set_error(ErrCode::ERR_PARAM_SIGN);
			writeLog('参数错误',$params);
			return $this->return_output_json();
		}

		// 获取赛区列表
		$data	= $this->match_repo->getMatchZoneList($params);
		return $this->return_output_json($data);
	}

	/**
	 * @SWG\Post(
	 *   path="/match/create_group",
	 *   summary="创建战队",
	 *   consumes={"multipart/form-data"},
	 *   tags={"大赛模块"},
	 *   @SWG\Parameter(name="uuid",  in="query", required=true, description="用户uuid", type="string"),
	 *   @SWG\Parameter(name="token", in="query", required=true, description="token令牌", type="string"),
	 *   @SWG\Parameter(name="sign",  in="query", required=true, description="签名", type="string"),
	 *   @SWG\Parameter(name="id",  in="query", required=true, description=" 游戏ID", type="integer"),
	 *   @SWG\Parameter(name="m_id", in="query", required=true, description="赛区ID", type="integer"),
	 *   @SWG\Parameter(name="name",  in="query", required=true, description="战队名", type="string"),
	 *   @SWG\Parameter(name="file",  in="formData", required=true, description="战队图标", type="file"),
	 *   @SWG\Response(
	 *     response=200,
	 *     description="操作成功"
	 *   ),
	 *   @SWG\Response(
	 *     response="default",
	 *     description="an ""unexpected"" error"
	 *   )
	 * )
	 */
    public function createGroup(Request $request)
    {
		// 接收参数
		$params 			= $this->getPublicParams($request);
		if (!$params) return $this->return_output_json();
		$params['id']		= (int)$request->input('id');// 游戏ID
		$params['m_id']	= (int)$request->input('m_id');// 赛区ID
		$params['name']	= urldecode($request->input('name'));
		$file				= $request->file('file');

		// 校验参数
		if (!$params['id'] || !$params['name'] || !$params['m_id'] || !$file) {
			$this->error_->set_error(ErrCode::ERR_PARA);
			writeLog('参数错误',$params);
			return $this->return_output_json();
		}

		// 校验战队名称长度
		if (mb_strlen($params['name'],'utf-8') > 16 && strlen($params['name'] > 32)) {
			$this->error_->set_error(ErrCode::ERR_GROUP_NAME_TOO_LONG);
			return $this->return_output_json();
		}

		// 校验签名
		if (!checkSign($params,$params['sign'])) {
			$this->error_->set_error(ErrCode::ERR_PARAM_SIGN);
			writeLog('签名错误',$params);
			return $this->return_output_json();
		}

		// 上传头像
		$file_ = uploadPicture($file,$params['uuid'],5,array('png','jpg'));
		if (!$file_) {
			$this->error_->set_error(ErrCode::ERR_UPLOAD_FILE_FAIL);
			return $this->return_output_json();
		}
		$params['icon']  = $file_;

		// 创建战队 返回邀请二维码
		$data	= $this->match_repo->doCreateGroup($params);
		return $this->return_output_json($data);
    }

	/**
	 * @SWG\Get(
	 *   path="/match/group_list",
	 *   summary="获取--战队列表",
	 *   tags={"大赛模块"},
	 *   @SWG\Parameter(name="uuid",  in="query", required=true, description="用户uuid", type="string"),
	 *   @SWG\Parameter(name="token", in="query", required=true, description="token令牌", type="string"),
	 *   @SWG\Parameter(name="sign",  in="query", required=true, description="签名", type="string"),
	 *   @SWG\Parameter(name="page",  in="query", required=true, description=" 分页起始页 1开始", type="integer"),
	 *   @SWG\Parameter(name="count", in="query", required=true, description="每页显示条数", type="integer"),
	 *	 @SWG\Response(
	 *     response=200,
	 *     description="操作成功"
	 *   ),
	 *   @SWG\Response(
	 *     response="default",
	 *     description="an ""unexpected"" error"
	 *   )
	 * )
	 */
    public function groupList(Request $request)
	{
		// 接收参数
		$params 			= $this->getPublicParams($request);
		if (!$params) return $this->return_output_json();
		$params['page'] 	= $request->input('page');
		$params['count'] 	= $request->input('count');

		// 校验参数
		if (!$params['page'] || !$params['count']) {
			$this->error_->set_error(ErrCode::ERR_PARA);
			writeLog('参数错误',$params);
			return $this->return_output_json();
		}

		// 校验签名
		if (!checkSign($params,$params['sign'])) {
			$this->error_->set_error(ErrCode::ERR_PARAM_SIGN);
			writeLog('参数错误',$params);
			return $this->return_output_json();
		}

		// 获取我的战队列表
		$data	= $this->match_repo->getGroupList($params);
		return $this->return_output_json($data);
	}

	/**
	 * @SWG\Get(
	 *   path="/match/group_info",
	 *   summary="获取--战队详情",
	 *   tags={"大赛模块"},
	 *   @SWG\Parameter(name="uuid",  in="query", required=true, description="用户uuid", type="string"),
	 *   @SWG\Parameter(name="token", in="query", required=true, description="token令牌", type="string"),
	 *   @SWG\Parameter(name="sign",  in="query", required=true, description="签名", type="string"),
	 *   @SWG\Parameter(name="id",  	in="query", required=true, description=" 战队ID", type="integer"),
	 *	 @SWG\Response(
	 *     response=200,
	 *     description="操作成功"
	 *   ),
	 *   @SWG\Response(
	 *     response="default",
	 *     description="an ""unexpected"" error"
	 *   )
	 * )
	 */
	public function groupInfo(Request $request)
	{
		// 接收参数
		$params 		= $this->getPublicParams($request);
		if (!$params) return $this->return_output_json();
		$params['id']	= (int)$request->input('id');// 战队ID

		// 校验参数
		if (!$params['id']) {
			$this->error_->set_error(ErrCode::ERR_PARA);
			writeLog('参数错误',$params);
			return $this->return_output_json();
		}

		// 校验签名
		if (!checkSign($params,$params['sign'])) {
			$this->error_->set_error(ErrCode::ERR_PARAM_SIGN);
			writeLog('参数错误',$params);
			return $this->return_output_json();
		}

		// 获取战队信息
		$data	= $this->match_repo->getGroupInfo($params);
		return $this->return_output_json($data);
	}

	/**
	 * @SWG\Get(
	 *   path="/match/groupuser_info",
	 *   summary="获取--战队成员详情",
	 *   tags={"大赛模块"},
	 *   @SWG\Parameter(name="uuid",  in="query", required=true, description="用户uuid", type="string"),
	 *   @SWG\Parameter(name="token", in="query", required=true, description="token令牌", type="string"),
	 *   @SWG\Parameter(name="sign",  in="query", required=true, description="签名", type="string"),
	 *   @SWG\Parameter(name="id",  	 in="query", required=true, description=" 游戏ID", type="integer"),
	 *   @SWG\Parameter(name="o_uuid", in="query", required=true, description="成员uuid", type="integer"),
	 *   @SWG\Response(
	 *     response=200,
	 *     description="操作成功"
	 *   ),
	 *   @SWG\Response(
	 *     response="default",
	 *     description="an ""unexpected"" error"
	 *   )
	 * )
	 */
	public function groupUserInfo(Request $request)
	{
		// 接收参数
		$params 		= $this->getPublicParams($request);
		if (!$params) return $this->return_output_json();
		$params['o_uuid']	= (int)$request->input('o_uuid');// 成员uuid
		$params['id']		= (int)$request->input('id');// 游戏id

		// 校验参数
		if (!$params['o_uuid'] || !$params['id']) {
			$this->error_->set_error(ErrCode::ERR_PARA);
			writeLog('参数错误',$params);
			return $this->return_output_json();
		}

		// 校验签名
		if (!checkSign($params,$params['sign'])) {
			$this->error_->set_error(ErrCode::ERR_PARAM_SIGN);
			writeLog('参数错误',$params);
			return $this->return_output_json();
		}

		// 获取战队信息
		$data	= $this->match_repo->getGroupUserInfo($params);
		return $this->return_output_json($data);
	}

	/**
	 * @SWG\Get(
	 *   path="/match/group_url",
	 *   summary="获取--战队分享URL[战队分享二维码]",
	 *   tags={"大赛模块"},
	 *   @SWG\Parameter(name="uuid",  in="query", required=true, description="用户uuid", type="string"),
	 *   @SWG\Parameter(name="token", in="query", required=true, description="token令牌", type="string"),
	 *   @SWG\Parameter(name="sign",  in="query", required=true, description="签名", type="string"),
	 *   @SWG\Parameter(name="id",    in="query", required=true, description=" 战队ID", type="integer"),
	 *   @SWG\Response(
	 *     response=200,
	 *     description="操作成功"
	 *   ),
	 *   @SWG\Response(
	 *     response="default",
	 *     description="an ""unexpected"" error"
	 *   )
	 * )
	 */
    public function groupUrl(Request $request)
	{
		// 接收参数
		$params 		= $this->getPublicParams($request);
		if (!$params) return $this->return_output_json();
		$params['id']	= (int)$request->input('id');// 战队ID
		// 校验参数
		if (!$params['id']) {
			$this->error_->set_error(ErrCode::ERR_PARA);
			writeLog('参数错误',$params);
			return $this->return_output_json();
		}

		// 校验签名
		if (!checkSign($params,$params['sign'])) {
			$this->error_->set_error(ErrCode::ERR_PARAM_SIGN);
			writeLog('参数错误',$params);
			return $this->return_output_json();
		}

		// 获取邀请二维码
		$data	= $this->match_repo->getGroupUrl($params);
		return $this->return_output_json($data);
	}


	/**
	 * @SWG\Post(
	 *   path="/match/apply_group",
	 *   summary="申请加入战队",
	 *   tags={"大赛模块"},
	 *   @SWG\Parameter(name="uuid",  in="query", required=true, description="用户uuid", type="string"),
	 *   @SWG\Parameter(name="token", in="query", required=true, description="token令牌", type="string"),
	 *   @SWG\Parameter(name="sign",  in="query", required=true, description="签名", type="string"),
	 *   @SWG\Parameter(name="id",    in="query", required=true, description=" 战队ID", type="integer"),
	 *   @SWG\Parameter(name="o_uuid", in="query", required=true, description="邀请人uuid", type="integer"),
	 *   @SWG\Response(
	 *     response=200,
	 *     description="操作成功"
	 *   ),
	 *   @SWG\Response(
	 *     response="default",
	 *     description="an ""unexpected"" error"
	 *   )
	 * )
	 */
	public function applyGroup(Request $request)
	{
		// 接收参数
		$params 			= $this->getPublicParams($request);
		if (!$params) return $this->return_output_json();
		$params['id']		= (int)$request->input('id');// 战队id
		$params['o_uuid']	= (int)$request->input('o_uuid');// 邀请人uuid

		// 校验参数
		if (!$params['id'] || !$params['o_uuid']) {
			$this->error_->set_error(ErrCode::ERR_PARA);
			writeLog('参数错误',$params);
			return $this->return_output_json();
		}

		// 校验签名
		if (!checkSign($params,$params['sign'])) {
			$this->error_->set_error(ErrCode::ERR_PARAM_SIGN);
			writeLog('参数错误',$params);
			return $this->return_output_json();
		}

		// 申请加入战队
		$this->match_repo->doApplyGroup($params);
		return $this->return_output_json();
	}

	/**
	 * @SWG\Get(
	 *   path="/match/groupapply_list",
	 *   summary="获取--战队申请列表",
	 *   tags={"大赛模块"},
	 *   @SWG\Parameter(name="uuid",  in="query", required=true, description="用户uuid", type="string"),
	 *   @SWG\Parameter(name="token", in="query", required=true, description="token令牌", type="string"),
	 *   @SWG\Parameter(name="sign",  in="query", required=true, description="签名", type="string"),
	 *   @SWG\Parameter(name="id",  in="query", required=true, description=" 战队ID", type="integer"),
	 *   @SWG\Parameter(name="page",  in="query", required=true, description=" 分页起始页 1开始", type="integer"),
	 *   @SWG\Parameter(name="count", in="query", required=true, description="每页显示条数", type="integer"),
	 *   @SWG\Response(
	 *     response=200,
	 *     description="操作成功"
	 *   ),
	 *   @SWG\Response(
	 *     response="default",
	 *     description="an ""unexpected"" error"
	 *   )
	 * )
	 */
	public function groupApplyList(Request $request)
	{
		// 接收参数
		$params 			= $this->getPublicParams($request);
		if (!$params) return $this->return_output_json();
		$params['id']		= (int)$request->input('id');// 战队id
		$params['page']	= (int)$request->input('page');
		$params['count']	= (int)$request->input('count');

		// 校验参数
		if (!$params['id'] || !$params['page'] || !$params['count']) {
			$this->error_->set_error(ErrCode::ERR_PARA);
			writeLog('参数错误',$params);
			return $this->return_output_json();
		}

		// 校验签名
		if (!checkSign($params,$params['sign'])) {
			$this->error_->set_error(ErrCode::ERR_PARAM_SIGN);
			writeLog('参数错误',$params);
			return $this->return_output_json();
		}

		// 获取战队申请列表
		$data	= $this->match_repo->getGroupApplyList($params);
		return $this->return_output_json($data);
	}

	/**
	 * @SWG\Post(
	 *   path="/match/do_group",
	 *   summary="战队操作",
	 *   tags={"大赛模块"},
	 *   @SWG\Parameter(name="uuid",  in="query", required=true, description="用户uuid", type="string"),
	 *   @SWG\Parameter(name="token", in="query", required=true, description="token令牌", type="string"),
	 *   @SWG\Parameter(name="sign",  in="query", required=true, description="签名", type="string"),
	 *   @SWG\Parameter(name="id",   in="query", required=true, description="战队ID", type="integer"),
	 *   @SWG\Parameter(name="o_uuid",in="query", required=true, description="操作用户uuid", type="integer"),
	 *   @SWG\Parameter(name="type",  in="query", required=true, description="操作类型[1剔除队员2同意用户加入战队3忽略4自动退出战队5指定副队长6取消副队长]", type="array",@SWG\Items(type="integer",enum={1,2,3,4,5,6},default=1)),
	 *   @SWG\Response(
	 *     response=200,
	 *     description="操作成功"
	 *   ),
	 *   @SWG\Response(
	 *     response="default",
	 *     description="an ""unexpected"" error"
	 *   )
	 * )
	 */
	public function doGroup(Request $request)
	{
		// 接收参数
		$params 			= $this->getPublicParams($request);
		if (!$params) return $this->return_output_json();
		$params['id']		= (int)$request->input('id');// 战队id
		$params['type']	= (int)$request->input('type');// 操作类型[1剔除队员2同意用户加入战队3忽略4自动退出战队5指定副队长6取消副队长]
		$params['o_uuid']	= (int)$request->input('o_uuid');

		// 校验参数
		if (!$params['id'] || !$params['type']) {
			$this->error_->set_error(ErrCode::ERR_PARA);
			writeLog('参数错误',$params);
			return $this->return_output_json();
		}
		if (!in_array($params['type'],array(1,2,3,4,5,6))) {
			$this->error_->set_error(ErrCode::ERR_DO_GROUP_TYPE_FAIL);
			writeLog('参数错误',$params);
			return $this->return_output_json();
		}
		if ($params['type'] != 4 && !$params['o_uuid']) {
			$this->error_->set_error(ErrCode::ERR_GROUP_USER_NOT_EMPTY_FAIL);
			writeLog('参数错误',$params);
			return $this->return_output_json();
		}
		if ($params['type'] != 4 && $params['uuid'] == $params['o_uuid']) {
			$this->error_->set_error(ErrCode::ERR_DO_GROUP_TYPE_FAIL);
			writeLog('参数错误',$params);
			return $this->return_output_json();
		}

		// 校验签名
		if (!checkSign($params,$params['sign'])) {
			$this->error_->set_error(ErrCode::ERR_PARAM_SIGN);
			writeLog('参数错误',$params);
			return $this->return_output_json();
		}

		// 获取战队申请列表
		$this->match_repo->doGroup($params);
		return $this->return_output_json();
	}

	/**
	 * @SWG\Post(
	 *   path="/match/share",
	 *   summary="分享战队二维码",
	 *   tags={"大赛模块"},
	 *   @SWG\Parameter(name="uuid",  in="query", required=true, description="用户uuid", type="string"),
	 *   @SWG\Parameter(name="token", in="query", required=true, description="token令牌", type="string"),
	 *   @SWG\Parameter(name="sign",  in="query", required=true, description="签名", type="string"),
	 *   @SWG\Parameter(name="id",   in="query", required=true, description="战队ID", type="integer"),
	 *   @SWG\Response(
	 *     response=200,
	 *     description="操作成功"
	 *   ),
	 *   @SWG\Response(
	 *     response="default",
	 *     description="an ""unexpected"" error"
	 *   )
	 * )
	 */
	public function share(Request $request)
	{
		// 接收参数
		$params 			= $this->getPublicParams($request);
		if (!$params) return $this->return_output_json();
		$params['id']		= (int)$request->input('id');// 战队id
		$params['type']	= $request->input('type');// 二维码样式-分享样式[1微信接口C 2微信接口B 3微信接口A]

		// 校验参数
		if (!$params['id']) {
			$this->error_->set_error(ErrCode::ERR_PARA);
			writeLog('参数错误',$params);
			return $this->return_output_json();
		}

		// 校验签名
		if (!checkSign($params,$params['sign'])) {
			$this->error_->set_error(ErrCode::ERR_PARAM_SIGN);
			writeLog('参数错误',$params);
			return $this->return_output_json();
		}

		// 插入分享记录
		$this->match_repo->doShare($params);
		return $this->return_output_json();
	}

	/**
	 * @SWG\Get(
	 *   path="/match/groupmsg_list",
	 *   summary="获取--战队消息列表",
	 *   tags={"大赛模块"},
	 *   @SWG\Parameter(name="uuid",  in="query", required=true, description="用户uuid", type="string"),
	 *   @SWG\Parameter(name="token", in="query", required=true, description="token令牌", type="string"),
	 *   @SWG\Parameter(name="sign",  in="query", required=true, description="签名", type="string"),
	 *   @SWG\Parameter(name="id",   in="query", required=true, description="战队ID", type="integer"),
	 *   @SWG\Parameter(name="page",  in="query", required=true, description=" 分页起始页 1开始", type="integer"),
	 *   @SWG\Parameter(name="count", in="query", required=true, description="每页显示条数", type="integer"),
	 *   @SWG\Response(
	 *     response=200,
	 *     description="操作成功"
	 *   ),
	 *   @SWG\Response(
	 *     response="default",
	 *     description="an ""unexpected"" error"
	 *   )
	 * )
	 */
	public function groupMsgList(Request $request)
	{
		// 接收参数
		$params 			= $this->getPublicParams($request);
		if (!$params) return $this->return_output_json();
		$params['id']		= (int)$request->input('id');// 战队id
		$params['page']	= (int)$request->input('page');
		$params['count']	= (int)$request->input('count');

		// 校验参数
		if (!$params['id'] || !$params['page'] || !$params['count']) {
			$this->error_->set_error(ErrCode::ERR_PARA);
			writeLog('参数错误',$params);
			return $this->return_output_json();
		}

		// 校验签名
		if (!checkSign($params,$params['sign'])) {
			$this->error_->set_error(ErrCode::ERR_PARAM_SIGN);
			writeLog('参数错误',$params);
			return $this->return_output_json();
		}

		// 获取战队申请列表
		$data	= $this->match_repo->getGroupMsgList($params);
		return $this->return_output_json($data);
	}



	/**
	 * @SWG\Get(
	 *   path="/match/qrcode",
	 *   summary="获取战队分享二维码",
	 *   tags={"大赛模块"},
	 *   @SWG\Parameter(name="uuid",  in="query", required=true, description="用户uuid", type="string"),
	 *   @SWG\Parameter(name="token", in="query", required=true, description="token令牌", type="string"),
	 *   @SWG\Parameter(name="sign",  in="query", required=true, description="签名", type="string"),
	 *   @SWG\Parameter(name="id",    in="query", required=true, description=" 战队ID", type="integer"),
	 *   @SWG\Parameter(name="type",  in="query", required=false, description="默认1[1微信接口C样式 2微信接口B 3微信接口A]", type="integer",default="1"),
	 *   @SWG\Response(
	 *     response=200,
	 *     description="操作成功"
	 *   ),
	 *   @SWG\Response(
	 *     response="default",
	 *     description="an ""unexpected"" error"
	 *   )
	 * )
	 */
	public function QRcode(Request $request)
	{
		// 接收参数
		$params 		= $this->getPublicParams($request);
		if (!$params) return $this->return_output_json();
		$params['id']	= (int)$request->input('id');// 战队ID
		$params['type']= $request->input('type');// 1微信接口C 2微信接口B 3微信接口A]

		// 校验参数
		if (!$params['id']) {
			$this->error_->set_error(ErrCode::ERR_PARA);
			writeLog('参数错误',$params);
			return $this->return_output_json();
		}

		// 校验签名
		if (!checkSign($params,$params['sign'])) {
			$this->error_->set_error(ErrCode::ERR_PARAM_SIGN);
			writeLog('参数错误',$params);
			return $this->return_output_json();
		}

		// 获取邀请二维码
		$data	= $this->match_repo->getQRcode($params);
		return $this->return_output_json($data);
	}


}
