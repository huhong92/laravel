<?php

namespace App\Http\Controllers\V0;

use Validator;
use App\Http\Controllers\Controller;
use App\Http\Repositories\V0\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use ErrCode;

class UserController extends Controller
{
    protected $user_repo;
    public function __construct(UserRepository $user_repo)
    {
        parent::__construct(false);
        $this->user_repo   = $user_repo;
        $this->error_       = $user_repo->error_;
    }

// ------------------------------  电竞 start------------------------------------------

    /**
     * @SWG\Post(
     *   path="/user/register_third",
     *   summary="第三方登入接口",
     *   tags={"User"},
     *   @SWG\Parameter(name="login_type",in="query",required=true,description="登入设备类型[1pc2ios3android]",type="array",@SWG\Items(type="integer",enum={"1", "2", "3"},default="1")),
     *   @SWG\Parameter(name="type",in="query",required=true,description="注册类型[1微信2qq目前只支持微信]",type="array",@SWG\Items(type="integer",enum={"1", "2"},default="1")),
     *   @SWG\Parameter(name="js_code",in="query",required=true,description="微信code",type="string"),
     *   @SWG\Parameter(name="nickname",in="query",required=false,description="昵称",type="string"),
     *   @SWG\Parameter(name="mobile",in="query",required=false,description="手机号",type="string"),
     *   @SWG\Parameter(name="icon",in="query",required=false,description="头像url",type="string"),
     *   @SWG\Parameter(name="sex",in="query",required=false,description="性别[0未知1男2女]",type="array",@SWG\Items(type="integer",enum={"0", "1", "2"},default="1")),
     *   @SWG\Parameter(name="age",in="query",required=false,description="年龄",type="integer"),
     *   @SWG\Parameter(name="address",in="query",required=false,description="住址",type="string"),
     *   @SWG\Parameter(name="sign",in="query",required=true,description="签名",type="string"),
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
    public function registerThirdParty(Request $request)
    {
        // 接收校验参数
        $params['login_type']	= (int)$request->input('login_type');// 设备类型[1pc2ios3android]
        $params['type']		= (int)$request->input('type');// 注册类型[1微信2QQ]
        $params['js_code']  	= (string)$request->input('js_code');// 微信js_code
        $params['nickname']  	= (string)$request->input('nickname');
        $params['icon']      	= (string)$request->input('icon');
        $params['sex'] 			= (string)$request->input('sex');
        $params['age'] 			= $request->input('age');
        $params['address']		= (string)$request->input('address');
        $params['sign'] 		= (string)$request->input('sign');

        // 校验参数
        if (!$params['type']  || !$params['login_type'] || !$params['js_code'] || !$params['sign']) {
            $this->error_->set_error(ErrCode::ERR_PARA);
            writeLog('参数错误', $params);
            return $this->return_output_json();
        }
        if (!in_array($params['login_type'], config('passport.login_type'))) {
            $this->error_->set_error(ErrCode::ERR_PARA);
            writeLog('设备类型错误', $params);
            return $this->return_output_json();
        }

        // 校验签名
        if (!checkSign($params, $params['sign'])) {
            $this->error_->set_error(ErrCode::ERR_PARAM_SIGN);
            writeLog('签名错误', $params);
            return $this->return_output_json();
        }

        // 根据js_code获取openId
        if ($params['type'] == 1) {
            $wx_info = $this->user_repo->getOpenId($params);
            if (!$wx_info) {
				return $this->return_output_json();
			}
            $params['account']  = $wx_info['openid'];
            unset($params['js_code']);
        } else {
            $this->error_->set_error(ErrCode::ERR_OTHER_THIRD_NOT_COMPLET_FALI);
            return $this->return_output_json();
        }

        // 调用注册接口
        $data = $this->user_repo->doRegister_($params);
        if (!$data) {
            return $this->return_output_json();
        }
        // 注册成功-返回唯一用户信息
        $data['token'] = $this->setToken($data['uuid'], $params['login_type'], $params['account']);
        return $this->return_output_json($data);
    }

    /**
     * @SWG\Get(
     *   path="/user/info",
     *   summary="获取用户信息接口",
     *   tags={"User"},
     *   @SWG\Parameter(name="uuid", in="query", required=true, description="用户uuid", type="string"),
     *   @SWG\Parameter(name="token", in="query", required=true, description="token令牌", type="string"),
     *   @SWG\Parameter(name="sign", in="query", required=true, description="签名", type="string"),
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
    public function userInfo(Request $request)
    {
        // 接收公共参数
        $params = $this->getPublicParams($request);
        if (!$params) {
            return $this->return_output_json();
        }

        // 校验签名
        if (!checkSign($params,$params['sign'])) {
            $this->error_->set_error(ErrCode::ERR_PARAM_SIGN);
            writeLog('参数错误',$params);
            return $this->return_output_json();
        }

        // 获取用户信息
        $data   = $this->user_repo->getUserInfo($params);
        if (!$data) {
            return $this->return_output_json();
        }
        // 注册成功，设置Token
        $data['token'] = $this->getToken($data['uuid'])['token'];
        if (!$data['token']) {
            $this->error_->set_error(ErrCode::ERR_TOKEN_EMPTY);
            writeLog('令牌错误',$params);
            return $this->return_output_json();
        }
        // 返回结果
        return $this->return_output_json($data);
    }

    /**
     * @SWG\Post(
     *   path="/user/update",
     *   summary="修改用户基础信息接口",
     *   tags={"User"},
     *   consumes={"multipart/form-data"},
     *   produces={"application/json"},
     *   @SWG\Parameter(name="uuid", in="query", required=true, description="用户uuid", type="string"),
     *   @SWG\Parameter(name="token", in="query", required=true, description="token令牌", type="string"),
     *   @SWG\Parameter(name="sign",in="query", required=true, description="签名", type="string"),
     *   @SWG\Parameter(name="nickname", in="query", required=true, description="用户昵称", type="string"),
     *   @SWG\Parameter(name="age", in="query", required=false, description="年龄", type="integer"),
     *   @SWG\Parameter(name="address", in="query", required=false, description="住址", type="string"),
     *   @SWG\Parameter(name="sex", in="query", required=false, description="性别[0未知1男2女]", type="integer"),
     *   @SWG\Parameter(name="file",in="formData", required=false, description="头像", type="file",format=""),
     *   @SWG\Response(
     *     response=200,
     *     description="update user"
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     */
    public function updateUserInfo(Request $request)
    {
        // 接收公共参数
        $params					= $this->getPublicParams($request);
        $params['nickname']	= $request->input('nickname');
        $params['age']  		= $request->input('age');
        $params['address']  	= $request->input('address');
        $params['sex']      	= $request->input('sex');
        $file               	= $request->file('file');

        // 校验签名
        if (!checkSign($params,$params['sign'])) {
            $this->error_->set_error(ErrCode::ERR_PARAM_SIGN);
            writeLog('参数错误',$params);
            return $this->return_output_json();
        }

        // 上传头像
        if ($file) {
            if ($file_ = uploadPicture($file)) {
                $params['icon']  = $file_;
            }
        }
        // 调用更新接口
        $this->user_repo->doUpdateUserInfo($params['uuid'],$params);
        return $this->return_output_json();
    }

    /**
     * @SWG\Get(
     *   path="/user/sms",
     *   summary="获取验证码接口",
     *   tags={"User"},
     *   @SWG\Parameter(name="uuid", in="query", required=true, description="用户uuid", type="string"),
     *   @SWG\Parameter(name="token", in="query", required=true, description="token令牌", type="string"),
     *   @SWG\Parameter(name="sign",in="query", required=true, description="签名", type="string"),
     *   @SWG\Parameter(name="mobile", in="query", required=true, description="手机号", type="string"),
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
    public function getSMS(Request $request)
    {
        // 接收公共参数
        $params 			= $this->getPublicParams($request);
        $params['mobile'] 	= $request->input('mobile');
        if (!$params['mobile']) {
            $this->error_->set_error(ErrCode::ERR_PARA);
            writeLog('参数错误',$params);
            return $this->return_output_json();
        }
        if (!isMobile($params['mobile'])) {
            $this->error_->set_error(ErrCode::ERR_MOBILE_FROMAT_FALI);
            writeLog('手机号格式错误',$params);
            return $this->return_output_json();
        }

        // 校验签名
        if (!checkSign($params,$params['sign'])) {
            $this->error_->set_error(ErrCode::ERR_PARAM_SIGN);
            writeLog('参数错误',$params);
            return $this->return_output_json();
        }

        // 判断用户是否已获取验证码
        $interval_time	= config('passport.sms_interval_time');
        $expire         = config('passport.sms_expire');
		$sms_info   	= Cache::get($this->L_RE_SendSMS.$params['uuid'].'_'.$params['mobile']);
        if ($sms_info['send_ts'] + $interval_time > time()) {
            $this->error_->set_error(ErrCode::ERR_MOBILE_SMS_FAST_FALI);
            writeLog('验证码发送过快',$params);
            return $this->return_output_json();
        }

        // 获取验证码
        $code   = getMobileCode(4,'numeric');
        if (sendSMS($params['mobile'],$code)) {
        // if (1) {
            $item['send_ts']    = time();
            $item['code']        = $code;
            Cache::put($this->L_RE_SendSMS.$params['uuid'].'_'.$params['mobile'],$item,$expire);
        }
        // 返回数据
        $data['code']   = $code;
        return $this->return_output_json($data);
    }

    /**
     * @SWG\Post(
     *   path="/user/auth_mobile",
     *   summary="手机号认证接口",
     *   tags={"User"},
     *   @SWG\Parameter(name="uuid", in="query", required=true, description="用户uuid", type="string"),
     *   @SWG\Parameter(name="token", in="query", required=true, description="token令牌", type="string"),
     *   @SWG\Parameter(name="sign",in="query", required=true, description="签名", type="string"),
     *   @SWG\Parameter(name="mobile", in="query", required=true, description="手机号", type="string"),
     *   @SWG\Parameter(name="verity_code", in="query", required=true, description="验证码", type="string"),
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
    public function authMobile(Request $request)
    {
        // 接收公共参数
        $params 					= $this->getPublicParams($request);
        $params['mobile'] 			= $request->input('mobile');
        $params['verity_code'] 	= $request->input('verity_code');

        // 校验参数
        if (!$params['mobile'] || !$params['verity_code'] ) {
            $this->error_->set_error(ErrCode::ERR_PARA);
            writeLog('参数错误',$params);
            return $this->return_output_json();
        }
        if (!isMobile($params['mobile'])) {
            $this->error_->set_error(ErrCode::ERR_MOBILE_FROMAT_FALI);
            writeLog('手机号格式错误',$params);
            return $this->return_output_json();
        }

        // 校验签名
        if (!checkSign($params,$params['sign'])) {
            $this->error_->set_error(ErrCode::ERR_PARAM_SIGN);
            writeLog('参数错误',$params);
            return $this->return_output_json();
        }

        // 获取用户验证码
        $sms_info   = Cache::get($this->L_RE_SendSMS.$params['uuid'].'_'.$params['mobile']);
        if (!$sms_info) {
            $this->error_->set_error(ErrCode::ERR_MOBILE_SMS_EXPIRE_FALI);
            writeLog('验证码已失效',$params);
            return $this->return_output_json();
        }
        if ($sms_info['code'] != $params['verity_code']) {
            $this->error_->set_error(ErrCode::ERR_MOBILE_SMS_CODE_FALI);
            writeLog('验证码错误',$params);
            return $this->return_output_json();
        }

        // 绑定手机号
        $this->user_repo->doAuthMobile($params);
        // 返回数据
        return $this->return_output_json();
    }


    /**
     * @SWG\Post(
     *   path="/user/auth_idcard",
     *   summary="身份证验证接口",
     *   tags={"User"},
     *   @SWG\Parameter(name="uuid", in="query", required=true, description="用户uuid", type="string"),
     *   @SWG\Parameter(name="token", in="query", required=true, description="token令牌", type="string"),
     *   @SWG\Parameter(name="sign",in="query", required=true, description="签名", type="string"),
     *   @SWG\Parameter(name="id_card", in="query", required=true, description="身份证号", type="string"),
     *   @SWG\Parameter(name="name", in="query", required=true, description="真实姓名", type="string"),
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
    public function authIDcard()
    {
		$data	= array(
			'id_card'	=> '123456789',
			'name'		=> '张先生'
		);
		return $this->return_output_json($data);
    }

    public function updateUserView()
    {
        return response()->view('User.updateuser');
    }





}
