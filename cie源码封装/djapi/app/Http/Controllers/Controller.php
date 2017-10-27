<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Validator;
use Illuminate\Support\Facades\Cache;
use Error_;
use ErrCode;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public $error_;
    public $L_RE_TOKEN;
    public $L_RE_SendSMS;
    public function __construct()
    {
        $this->error_ = app('Error_');
        $this->L_RE_TOKEN   = 'RD_TOKEN_';
        $this->L_RE_SendSMS = 'RD_SendSMS_';
    }

    /**
     * 返回标准json格式数据
     * @param array $data
     */
    public function return_output_json($data = array())
    {
        $code   = $this->error_->get_error();
        if (!$code) {
            $code = '0000';
        }
        $msg    = $this->error_->error_msg($code);
        if (!$data) {
			$data	= array();
		}
        return response()->json(['code'=>$code,'msg' => $msg,'data' => $data]);exit;
    }


    /**
     * 生成有效用户Token
     * @param $uuid
     * @param $login_type 登陆设备类型
     * @param $account    登陆账号[微信QQ第三方唯一标识/手机号]
     * @return string
     */
    public function setToken($uuid,$login_type,$account)
    {
        $token_key  = config('passport.token_key');
        $expire     = config('passport.token_expire');
        $login_ts   = time();
        $login_expire_ts = $login_ts+$expire;

        $item['token']              = md5($uuid.'_'.$login_type.$account.'_'.$login_ts);
        $item['token_expire_ts']  = $login_expire_ts;
        $item['login_ts']          = $login_ts;
        Cache::put($this->L_RE_TOKEN.$uuid,$item,$expire);
        return $item['token'];
    }

    /**
     * 获取用户Token
     * @param $uuid
     */
    public function getToken($uuid)
    {
        return Cache::get($this->L_RE_TOKEN.$uuid);
    }

    /**
     *  校验用户Token是有有效
     */
    public function checkIsLogin($uuid,$token)
    {
        if (getenv('APP_ENV') == 'local') {
            return true;
        }
        $token_info = Cache::get($this->L_RE_TOKEN.$uuid);
        // 未登录-无token信息
        if (!$token_info || !$token_info['token']) {
            $this->error_->set_error(ErrCode::ERR_LOGIN_TOKEN_EXPIRE);
            writeLog('get_token_fail:获取token失败','uuid:'.$uuid.';token:'.$token);
            return false;
        }
        // token错误
        if ($token_info['token'] != $token) {
            $this->error_->set_error(ErrCode::ERR_LOGIN_TOKEN_FAIL);
            writeLog('token_error:Token错误','uuid:'.$uuid.';token:'.$token);
            return false;
        }
        // token超时
        if ($token_info['token_expire_ts'] < time()) {
            $this->error_->set_error(ErrCode::ERR_LOGIN_TOKEN_EXPIRE);
            writeLog('token_error:Token过期',$uuid.';token:'.$token.'-'.json_encode($token_info));
            return false;
        }
        return true;
    }

    /**
     * 获取接口公共参数
     * @param $request
     */
    public function getPublicParams($request)
    {
        // 接收校验参数
        $validator = Validator::make($request->all(), [
            'uuid'        => 'required|integer',
            'token'       => 'required|string',
            'sign'        => 'required|string',
        ]);
        if ($validator->fails()) {
            $this->error_->set_error(ErrCode::ERR_PARA);
            writeLog('参数错误',$request->all());
            return false;
        }

        $params['uuid']     = $request->input('uuid');
        $params['token']    = $request->input('token');
        $params['sign']     = $request->input('sign');

        // 校验Token
		if(!$this->checkIsLogin($params['uuid'],$params['token'])) {
			writeLog('账号未登录',$params);
			return false;
		}

        return $params;
    }




}
