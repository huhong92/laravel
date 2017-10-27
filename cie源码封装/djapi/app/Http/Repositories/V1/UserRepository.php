<?php
namespace App\Http\Repositories\V0;

use App\Http\Repositories\Repository;
use App\Models\V0\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Error_;
use ErrCode;

class UserRepository extends Repository {

    public $error_;
    public $user_model;
    public function __construct(Request $request)
    {
        $this->error_ = app('Error_');
		$this->user_model = new UserModel();
        parent::__construct($request);
    }

    /**
     *  获取小程序openId
     */
    public function getOpenId($params)
    {
    	// return array('openid'=>time());
        // 调用微信小程序接口-获取openId session_key
        $url    = 'https://api.weixin.qq.com/sns/jscode2session';
        $wx_conf= config('passport.wx_xcx');
        $data   = array(
            'appid'		=> $wx_conf['appid'],
            'secret'		=> $wx_conf['secret'],
            'js_code'		=> $params['js_code'],
            'grant_type'	=>'authorization_code'
        );
        $json_info  = curlGet($url,$data);
        $wx_info    = json_decode($json_info,true);
        if ($wx_info['errcode']) { // "errcode" ='40029' "errmsg": "invalid code"
            $this->error_->set_error(ErrCode::ERR_GET_WXXCX_OPENID_FALI);
            writeLog('wx_getSession_fail:微信小程序获取openId,Session失败',$params,array(),$wx_info);
            return false;
        }
        return $wx_info;
    }

    /**
     * 注册用户
     * @param $params
     * @return array 返回用户信息
     */
    public function doRegister_($params)
    {
        // 调用注册接口
        $url    			= config('passport.register_url').'/user/register_third/v1';
        $params['sign'] 	= getSign($params);
        $result 			= curlPost($url,$params);
        $result 			= json_decode($result,true);
        var_dump($result);exit;
        if ($result['code'] != '0000') {
            $this->error_->set_error(ErrCode::ERR_USER_REGISTER_FALI);
            writeLog('register_fail:调用外部注册接口失败',$params);
            return false;
        }
        $r_info = $result['data'];

        // 判断用户是否存在
        $u_info  = $this->checkIsExsistUser($r_info['uuid']);
        if (!$u_info['uuid']) {// 无该用户信息
            // 插入用户信息表
            $u_data = array(
                'uuid'			=> $r_info['uuid'],
                'id_card'   	=> '',
                'name'      	=> '',
                'nickname'  	=> $params['nickname'],
                'wx_account'	=> '',
                'mobile'     	=> (string)$params['mobile'],
                'icon'       	=> (string)$params['icon'],
                'sex'        	=> (string)$params['sex'],
                'age'        	=> (int)$params['age'],
                'address'   	=> (string)$params['address'],
                'points'    	=> 0,
                'status'    	=> 1,
                'time'        	=> $this->tz,
                'update_time'	=> $this->tz
            );
			$id_	= $this->user_model->insertData($u_data,'user');
            if (!$id_) {
                $this->error_->set_error(ErrCode::ERR_UNKOWNL);
                return false;
            }

            // 获取用户信息
			$where	= [['uuid',$r_info['uuid']],['del',0]];
			$fields	= ['uuid', 'nickname','mobile','icon','sex','age','address'];
			$u_info	= $this->user_model->getOne($where,'user',$fields);
            if (!$u_info) {
                $this->error_->set_error(ErrCode::ERR_UNKOWNL);
                return false;
            }
        }

        // 记录用户登录历史记录
        $l_data = array(
            'uuid'        	=> $r_info['uuid'],
            'login_type' 	=>$params['login_type'],
            'ip'       		=> $this->ip,
            'del'        	=> 0,
            'time'        	=> $this->tz,
            'update_time'	=>$this->tz,
        );
		$rg_id	= $this->user_model->insertData($l_data,'loginhis');
        if (!$rg_id) {
            $this->error_->set_error(ErrCode::ERR_UNKOWNL);
            return false;
        }
        return $u_info;
    }

    /**
     * 校验用户uuid是否存在
     * @param $account[包括：手机号、微信第一方唯一ID、邮箱等]
     */
    public function checkIsExsistUser($uuid)
    {
		$fields	= ['uuid','id_card','name','nickname','mobile','icon','sex','age','address','id_card','name'];
		$where	= [['uuid','=',$uuid],['del','=',0]];
		$account = $this->user_model->getOne($where,'user',$fields);
		if (!$account) {
			return false;
		}
		return $account;
    }

    /**
     * 获取用户信息
     * @param $params
     */
    public function getUserInfo($params)
    {
        // 获取用户信息
		$where	= [
			['uuid',$params['uuid']],
			['del',0]
		];
		$table 	= 'user';
		$fields	= ['uuid','nickname','mobile','icon','sex','age','address','id_card','name'];
		$u_info	= $this->user_model->getOne($where,$table,$fields);
		if (!$u_info) {
            $this->error_->set_error(ErrCode::ERR_USER_REGISTER_FALI);
            writeLog('get_user_fail:用户信息获取失败',$params);
            return false;
        }
        // 拼接用户头像
        if ($u_info['icon']) {
            if (strpos($u_info['icon'],"http://") !== 0) {
                $u_info['icon'] = config('passport.img_url').$u_info['icon'];
            }
        }
        return ($u_info);
    }

    /**
     * 更新用户信息
     * @param $params
     */
    public function doUpdateUserInfo($uuid,$params)
    {
        // 更新数据
        if ($params['nickname'])  $data['nickname']   = $params['nickname'];
        if ($params['icon'])       $data['icon']   = $params['icon'];
        if ($params['sex'])        $data['sex']   = $params['sex'];
        if ($params['age'] || (int)$params['age'] === 0) $data['age']   = (int)$params['age'];
        if ($params['address']) $data['address']   = $params['address'];
        if ($params['id_card']) $data['id_card']   = $params['id_card'];
        if ($params['name'])     $data['name']   = $params['name'];
        if (!$data) {
            return true;
        }

        // 修改用户信息
		$table 	= 'user';
		$where	= [
			['uuid',$uuid],
			['del',0]
		];
		$upt	= $this->user_model->updateData($where,$data,$table);
        if (!$upt) {
            $this->error_->set_error(ErrCode::ERR_UPDATE_USER_FALI);
            return false;
        }
        return  true;
    }

    /**
     * 手机号绑定认证
     */
    public function doAuthMobile($params)
    {
        // 获取用户信息
        $u_info = $this->getUserInfo($params);
        if ($u_info['mobile'] == $params['mobile']) {
            return true;
        }

        // 修改用户信息
		$where	= [['uuid',$params['uuid']],['del',0]];
        $data 	= array(
            'mobile' 		=> $params['mobile'],
            'update_time'	=> $this->tz
        );
        $upt	= $this->user_model->updateData($where,$data,'user');
        if (!$upt) {
            $this->error_->set_error(ErrCode::ERR_BIND_MOBILE_FALI);
            writeLog('bind_mobile_fail:手机号绑定失败',$params,$data);
            return false;
        }
        return  true;
    }

}