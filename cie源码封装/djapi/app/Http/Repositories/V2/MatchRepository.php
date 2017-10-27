<?php
namespace App\Http\Repositories\V0;

use App\Http\Repositories\Repository;
use Illuminate\Http\Request;
use App\Models\V0\MatchModel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Error_;
use ErrCode;

class MatchRepository extends Repository {

    public $error_;
    public $match_model;
    public $WeChat_ACCESS_TOKEN_	= 'WeChat_ATOKEN_';
    public $wechat_qrcode_url		= array(
    	'A'	=> 'https://api.weixin.qq.com/wxa/getwxacode',
		'B'	=> 'https://api.weixin.qq.com/wxa/getwxacodeunlimit',
		'C'	=> 'https://api.weixin.qq.com/cgi-bin/wxaapp/createwxaqrcode',
	);
    public function __construct(Request $request)
    {
        $this->error_ = app('Error_');
        $this->match_model =  new MatchModel();
        parent::__construct($request);
    }

    /**
     * 获取--大赛可报名游戏列表
     */
    public function getGameList($params)
    {
        // 获取赛事报名列表
		$table	= 'game';
		$fields	= ['id','name', 'icon','img' ,'type','status'];
		$where	= [
			['type',$params['type']],
			['del',0]
		];
		$list	= $this->match_model->getList($where,$table,$fields,$params['count']);
        if (!$list['data']) {
            $this->error_->set_error(ErrCode::ERR_DB_NO_DATA);
            return false;
        }

        // 拼接游戏图片
		foreach ($list['data'] as $k=>&$v) {
        	$path	= config('passport.img');
			if ($v['icon']) {
				$v['icon']	= $path.$v['icon'];
			}
			if ($v['img']) {
				$v['img']	= $path.$v['img'];
			}
		}

        // 返回数据总页数
        $data['pagecount']  = (int)ceil($list['total']/$params['count']);
        $data['list']        = $list['data'];
        return $data;
    }

    /**
     * 获取我的赛事报名列表
     * @param array $params
     */
    public function getMyApplyList($params)
    {
        // 获取我所在的战队ID
		$where	= array('uuid'=>$params['uuid'],'del'=>0);
		$fields	= array('id','group_id','group_name','type');
		$table	= 'groupuser';
		$group_list	= $this->match_model->getList($where,$table,$fields);
		$ids	= '';
		if ($group_list) {
			$ids 	= trim(implode(array_column($group_list,'group_id'),','),',');
		}

        // 获取我报名的列表
		$fields	= 'A.id as id,A.name as name,A.icon as icon,A.img as img,A.type as type,B.join_id as join_id,B.status as status';
		$sql 	= 'select '.$fields.' from dj_game as A  LEFT JOIN dj_matchapply as B ON A.id = B.g_id and (B.join_id = :uuid AND A.type = 1 or B.join_id in (:ids) AND A.type = 2 )  AND B.del = 0 ';
		$sql   .= 'WHERE A.status = 1 AND A.del = 0';
		$para	= [':uuid'=>$params['uuid'],':ids'=>$ids];
		$list	= $this->match_model->getSQL($sql,$para);
		if (!$list) {
			$this->error_->set_error(ErrCode::ERR_DB_NO_DATA);
			return false;
		}

        // 添加报名列表-类型 1:已报名apply2未报名noapply
		$path	= config('passport.img');
        foreach ($list as $k=>$v) {
            $v['join_id'] 	= (int)$v['join_id'];
            $v['status'] 	= (int)$v['status'];
			$v['group_id']	= 0;
            $v['icon']   	= $v['icon']?$path.$v['icon']:'';
            $v['img']   	= $v['img']?$path.$v['img']:'';
            if ($v['join_id']) {
            	if ($v['type'] == 2) {
					$v['group_id']	= $v['join_id'];
				}
				unset($v['join_id']);
                $data['apply'][]  = $v;
            } else {
				unset($v['join_id']);
                $data['noapply'][]  = $v;
            }
        }
        return $data;
    }


    /**
     * 获取游戏报名-所需填写信息
     */
    public function getGameApplyInfo($params)
    {
        // 获取申请大赛-游戏需填写信息
        $data['g_info'] = $this->getGameInfo($params);
        if (!$data['g_info']) {
            $this->error_->set_error(ErrCode::ERR_DB_NO_DATA);
            return false;
        }
        // 获取游戏区服信息
        if ($data['g_info']['g_zone']) {
            $z_list = $this->getZoneList($params);
            if (!$z_list) {
                $this->error_->set_error(ErrCode::ERR_DB_NO_DATA);
                return false;
            }
        }
        $data['zone_list'] = $z_list;
        return $data;
    }

    /**
     * 获取游戏信息---报名时需填写信息
     */
    public function getGameInfo($params)
    {
        // 获取游戏报名所需填写信息
        $where	= array('id'=>$params['id'],'del'=>0);
		$fields = array('id','name','icon','img','g_account','g_nickname','g_level','g_zone');
		$g_info	= $this->match_model->getOne($where,'game',$fields);
        if (!$g_info) {
            return false;
        }
        // 拼接游戏图标
		$path				= config('passport.img');
		$g_info['icon']	= $g_info['icon']?$path.$g_info['icon']:'';
		$g_info['img']		= $g_info['img']?$path.$g_info['img']:'';
        return $g_info;
    }


	/**
	 * 获取我的游戏列表
	 * @param $params
	 */
    public function getMyGameList($params)
	{
		$join_where	= array('game.id' => 'mygame.g_id');
		$where		= array('mygame.uuid'=>$params['uuid'],'mygame.del'=>0,'game.del'=>0);
		$tableA		= 'game';
		$tableB 	= 'mygame';
		$fields		= array('game.id as id','game.name as name','game.icon as icon','game.img as img','game.type as type');
		$list		= $this->match_model->join($join_where,$where,$tableA,$tableB,$fields,$params['count']);
		if (!$list['total']) {
			$this->error_->set_error(ErrCode::ERR_DB_NO_DATA);
			return false;
		}
		// 返回数据总页数
		$data['pagecount']  = (int)ceil($list['total']/$params['count']);
		$data['list']        = $list['data'];
		return $data;
	}

	/**
	 * 获取我的游戏信息
	 */
	public function getMygameInfo($params)
	{
		$join_where	= array('game.id' => 'mygame.g_id');
		$where		= array('game.id'=>$params['id'],'mygame.uuid'=>$params['uuid'],'mygame.del'=>0,'game.del'=>0);
		$tableA		= 'game';
		$tableB 	= 'mygame';
		$fields		= array('game.id as id','game.name as name','game.icon as icon','game.img as img','game.type as type','mygame.is_default as is_default','mygame.g_account as g_account','mygame.g_nickname as g_nickname','mygame.g_level as g_level','mygame.g_zone as g_zone','mygame.points as points');
		$data		= $this->match_model->join($join_where,$where,$tableA,$tableB,$fields);
		if (!$data) {
			$this->error_->set_error(ErrCode::ERR_DB_NO_DATA);
			return false;
		}

		return $data[0];
	}


	/**
     * 获取游戏区服列表
     * @param $params
     * @return bool
     */
    public function getZoneList($params)
    {
        // 获取游戏区服信息
		$where 	= array('g_id'=>$params['id'],'del'=>0);
		$fields = array('id','name as z_name');
		$list	= $this->match_model->getList($where,'zone',$fields);
        if (!$list) {
            // $this->error_->set_error(ErrCode::ERR_DB_NO_DATA);
            return false;
        }
        return $list;
    }

    /**
     * 完善游戏信息
     */
    public function doSaveMyGameInfo($params)
    {
        // 验证 游戏账号、昵称、level、区服id
        $g_info =  $this->getGameInfo($params);
        if ($g_info['g_account'] && !$params['account']) {
            $this->error_->set_error(ErrCode::ERR_GACCOUNT_NOTALLOW_EMPTY_FALI);
            writeLog('参数错误',$params);
            return false;
        }
        if ($g_info['g_nickname'] && !$params['nickname']) {
            $this->error_->set_error(ErrCode::ERR_GNICKNAME_NOTALLOW_EMPTY_FALI);
            writeLog('参数错误',$params);
            return false;
        }
        if ($g_info['g_level'] && !$params['level']) {
            $this->error_->set_error(ErrCode::ERR_GLEVEL_NOTALLOW_EMPTY_FALI);
            writeLog('参数错误',$params);
            return false;
        }
        if ($g_info['g_zone'] && !$params['z_id']) {
            $this->error_->set_error(ErrCode::ERR_GZONE_NOTALLOW_EMPTY_FALI);
            writeLog('参数错误',$params);
            return false;
        }
        // 验证区服id是否正确
        if ($g_info['g_zone']) {
            // 获取区服列表
            $z_list = $this->getZoneList($params);
            if ($z_list) {
                $z_info = array_column($z_list,'id');
                if (!in_array($params['z_id'],$z_info)) {
                    $this->error_->set_error(ErrCode::ERR_GZONE_ID_FALI);
                    writeLog('参数错误-区服id错误',$params);
                    return false;
                }
            }
        }

        // 查看我的游戏信息是否为空
        $mygame_info    = $this->getMygameInfo($params);
        $table          = 'mygame';
        if (!$mygame_info) {
			$this->error_->set_error(ErrCode::ERR_OK);
            // 插入我的游戏信息
            $data = array(
                'uuid'  		=> $params['uuid'],
                'g_id'  		=> $params['id'],
                'is_default'  => 0,
                'g_account'  	=> (string)$params['account'],
                'g_nickname'  => (string)$params['nickname'],
                'g_level'  	=> (string)$params['level'],
                'g_zone'  		=> (int)$params['z_id'],
                'points'  		=> 0,
            );
			$id	= $this->match_model->insertData($data,$table);
            if (!$id) {
                $this->error_->set_error(ErrCode::ERR_GZONE_ID_FALI);
                return false;
            }
        } else {
            // 更新我的游戏信息
            $where = array(
                'uuid'=>$params['uuid'],
                'g_id'=>$params['id'],
                'del'=>0
            );
            $data = array(
                'g_account'  => (string)$params['account'],
                'g_nickname'  => (string)$params['nickname'],
                'g_level'  => (string)$params['level'],
                'g_zone'  => (int)$params['z_id'],
                'update_time'  => $this->tz,
            );
			$res	= $this->match_model->updateData($where,$data,$table);
            if (!$res) {
                $this->error_->set_error(ErrCode::ERR_GZONE_ID_FALI);
                return false;
            }
        }
        return true;
    }

	/**
	 * 获取赛区列表
	 * @param $params
	 */
    public function getMatchZoneList($params)
	{
		$table	= 'matchzone';
		$where	= array('g_id'=>$params['id'],'del'=>0);
		$fields	= array('id','name');
		$list	= $this->match_model->getList($where,$table,$fields);
		if (!$list) {
			$this->error_->set_error(ErrCode::ERR_DB_NO_DATA);
			return false;
		}

		$data['list']	= $list;
		return $data;
	}

	/**
	 * 创建战队[一个战队对应一个游戏]
	 * @param int    $params['uuid']	用户uuid
	 * @param string $params['id']	游戏ID
	 * @param string $params['m_id']	赛区id
	 * @param string $params['name']	战队名
	 * @param string $params['icon']	战队图标
	 */
    public function doCreateGroup($params)
	{
		// 1.判断该游戏是否属于团队赛
		$table	= 'game';
		$where	= array('id'=>$params['id'],'del'=>0);
		$fields	= array('type','name');
		$info	= $this->match_model->getOne($where,$table,$fields);
		if ($info['type'] != 2) {
			$this->error_->set_error(ErrCode::ERR_NOT_TEAMGAME);
			return false;
		}

		// 2.校验赛区ID
		$matchzone_info	= $this->getMatchZoneList($params);
		if (!$matchzone_info) {
			return false;
		}
		if (in_array($params['m_id'],$matchzone_info)) {
			$this->error_->set_error(ErrCode::ERR_MATCH_ZONE_ID_FAIL);
			writeLog('match_zone_id_fail:创建战队,赛区id错误',$params);
			return false;
		}

		// 3.判断用户是否已有该游戏战队
		$table2		= 'groupuser';
		$where2		= array('uuid'=>$params['uuid'],'g_id'=>$params['id'],'del'=>0);
		$fields2	= array('id','group_id');
		$group		= $this->match_model->getList($where2,$table2,$fields2);
		if ($group) {
			$this->error_->set_error(ErrCode::ERR_GAME_GROUP_EXISTS);
			return false;
		}

		// 4.判断战队名称是否重复
		$table2		= 'group';
		$where2		= array('g_id'=>$params['id'],'del'=>0);
		$fields2	= array('id','name');
		$group_list	= $this->match_model->getList($where2,$table2,$fields2);
		if ($group_list) {
			$name_info	= array_column($group_list,'name');
			if (in_array($params['name'],$name_info)) {
				$this->error_->set_error(ErrCode::ERR_GROUP_NAME_EXISTS);
				return false;
			}
		}

		// 5.创建战队-插入战队表
		$data	= array(
			'name'			=> $params['name'],
			'icon'			=> (string)$params['icon'],
			'info'			=> '',
			'g_id'			=> $params['id'],
			'g_name'		=> $info['name'],
			'm_id'			=> $params['m_id'],
			'num'			=> 1,
			'status'		=> 1,
		);
		$id = $this->match_model->insertData($data,$table2);
		if (!$id) {
			$this->error_->set_error(ErrCode::ERR_CREATE_GROUP_FAILE);
			return false;
		}

		// 插入战队成员表
		$data2	= array(
			'group_id'		=> $id,
			'group_name'	=> $params['name'],
			'g_id'			=> $params['id'],
			'uuid'			=> $params['uuid'],
			'type'			=> 1,
		);
		$id2	= $this->insertGroupUser($data2);
		if (!$id2) {
			return false;
		}

		// 6.生成二维码
		$data	= $this->getQRcode(array('uuid'=>$params['uuid'],'id'=>$id));
		return $data;
	}

	/**
	 * 插入战队成员表
	 * @param $params
	 * @return bool
	 */
	public function insertGroupUser($params)
	{
		$table 	= 'groupuser';
		$data	= array(
			'group_id'		=> $params['group_id'],
			'group_name'	=> $params['group_name'],
			'g_id'			=> $params['g_id'],
			'uuid'			=> $params['uuid'],
			'type'			=> 1,
		);
		$id = $this->match_model->insertData($data,$table);
		if (!$id) {
			$this->error_->set_error(ErrCode::ERR_GROUP_USER_ADD_FAIL);
			return false;
		}
		return true;
	}

	/**
	 * 获取我的战队列表
	 * @param $params
	 */
	public function getGroupList($params)
	{
		// 获取的战队ID列表
		$table		= 'groupuser';
		$where		= array('uuid'=>$params['uuid'],'del'=>0);
		$fields		= array('group_id','g_id','type');
		$group_list	= $this->match_model->getList($where,$table,$fields);
		if (!$group_list) {
			$this->error_->set_error(ErrCode::ERR_DB_NO_DATA);
			return false;
		}

		// 获取我的战队列表信息
		$group_info	= array_column($group_list,'group_id');
		$table		= 'group';
		$whereIn	= array('id',$group_info);
		$where		= array('del'=>0);
		$fields		= array('id','name','icon','info','g_id','g_name','num','status');
		$group_list	= $this->match_model->whereIn($whereIn,$table,$fields,$params['count'],$where);
		if (!$group_list) {
			$this->error_->set_error(ErrCode::ERR_DB_NO_DATA);
			return false;
		}
		$ids		= array_column($group_list['data'],'id');
		$num_info	= $this->getGroupUserNum($ids);
		// 拼接战队图标
		$path	= config('passport.img_url');
		foreach ($group_list['data'] as $k=>&$v) {
			$v['icon']	= $path.$v['icon'];
			$v['num']	= (int)$num_info[$v['id']];
		}

		// 返回数据
		$data['pagecount']	= (int)ceil($group_list['total']/$params['count']);
		$data['list']		= $group_list['data'];
		return $data;
	}

	/**
	 * 获取战队成员数
	 * @param Array|int $group_id
	 * @return Array array('group_id'=>7)
	 */
	public function getGroupUserNum($group_id)
	{
		// 拼接条件
		if (is_array($group_id)) {
			$ids	= implode($group_id,',');
			$where	= 'group_id in (:id)';
		} else {
			$ids	= $group_id;
			$where	= 'group_id = :id';
		}

		// 拼接sql语句
		$sql	= 'SELECT COUNT(id) as num,group_id FROM dj_groupuser WHERE '.$where.' AND del = 0 GROUP BY group_id';
		$params	= array(':id'=>$ids);
		$num	= $this->match_model->getSQL($sql,$params);
		if (!$num) {
			return false;
		}

		// 返回值
		if (is_array($group_id)) {
			return array_column($num,'num','group_id');
		}
		return $num[0]['num'];
	}

	/**
	 * 获取我的战队信息
	 * @param $params
	 */
	public function getGroupInfo($params)
	{
		// 获取战队详情
		$table	= 'group';
		$where	= array('id'=>$params['id'],'del'=>0);
		$fields	= array('id','name','icon','info','g_id','g_name','num','status','m_id');
		$info	= $this->match_model->getOne($where,$table,$fields);
		if (!$info) {
			$this->error_->set_error(ErrCode::ERR_DB_NO_DATA);
			return false;
		}
		$info['num']	= (int)$this->getGroupUserNum($info['id']);

		// 获取赛区名
		$table2	= 'matchzone';
		$where2	= array('id'=>$info['m_id'],'del'=>0);
		$fields2= array('name');
		$m_info	= $this->match_model->getOne($where2,$table2,$fields2);
		$info['m_name']	= '';
		if ($m_info['name']) {
			$info['m_name']	= $m_info['name'];
		}

		// 获取成员UUID
		$info['u_list']	= array();
		$table3	= 'groupuser';
		$where3	= array('group_id'=>$info['id'],'del'=>0);
		$fields3= array('uuid','type');
		$user	= $this->match_model->getList($where3,$table3,$fields3);
		if (!$user) {
			return $info;
		}

		// 成员信息
		$uuids		= array_column($user,'uuid');
		$table4		= 'user';
		$whereIn	= array('uuid',$uuids);
		$fields4	= array('uuid','nickname','icon','status');
		$u_list		= $this->match_model->whereIn($whereIn,$table4,$fields4);
		if (!$u_list) {
			return $info;
		}
		$arr	= array_column($user,'type','uuid');
		foreach ($u_list as $k=>&$v) {
			$v['type']	= $arr[$v['uuid']];
		}
		$info['u_list']	= $u_list;
		return $info;
	}

	/**
	 * 获取战队成员详情
	 */
	public function getGroupUserInfo($params)
	{
		// 获取战队成员详情
		$table	= 'user';
		$where	= array('uuid'=>$params['o_uuid'],'del'=>0);
		$fields	= array('uuid','nickname','id_card','name','sex','age','icon','wx_account','status');
		$user	= $this->match_model->getOne($where,$table,$fields);
		if (!$user) {
			$this->error_->set_error(ErrCode::ERR_DB_NO_DATA);
			return false;
		}

		// 获取队员身份
		$table2	= 'groupuser';
		$where2	= array('uuid'=>$params['o_uuid'],'g_id'=>$params['id'],'del'=>0);
		$fields2= array('type','group_id');
		$type	= $this->match_model->getOne($where2,$table2,$fields2);
		if (!$type) {
			$this->error_->set_error(ErrCode::ERR_DB_NO_DATA);
			return false;
		}
		$user['type']	= $type['type'];
		$user['icon']	= config('passport.img_url').$user['icon'];
		$data['user']	= $user;

		// 获取游戏信息
		$data['g_info']	= array();
		$tableA		= 'mygame';
		$tableB		= 'game';
		$join_where	= array($tableA.'.g_id'=>$tableB.'.id');
		$where3		= array($tableA.'.uuid'=>$params['o_uuid'],$tableA.'.g_id'=>$params['id'],$tableA.'.del'=>0,$tableB.'.del'=>0);
		$fields3	= array('mygame.uuid as id','game.name as name','game.icon as icon','game.img as img','mygame.g_account as g_account','mygame.g_nickname as g_nickname','mygame.g_level as g_level','mygame.g_zone as g_zone');
		$list		= $this->match_model->join($join_where,$where3,$tableA,$tableB,$fields3);
		if ($list) {
			$list[0]['icon']	= $list[0]['icon']?config('passport.img').$list[0]['icon']:'';
			$list[0]['img']		= $list[0]['img']?config('passport.img').$list[0]['img']:'';
			$data['g_info']	= $list[0];
		}
		return $data;
	}

	/**
	 * 获取战队二维码
	 * @param $params['uuid'] 邀请人uuid
	 * @param $params['group_id'] 邀请战队id
	 * @return mixed
	 */
	public function getGroupURL($params)
	{
		$data['url']	= getenv('APP_URL').'match/apply_group?uuid='.$params['uuid'].'&group_id='.$params['id'];
		return $data;
	}

	/**
	 * 申请加入战队
	 * @param $params
	 */
	public function doApplyGroup($params)
	{
		// 判断用户是否已提交过申请
		$table1	= 'invitation';
		$wher1	= array('i_uuid'=>$params['uuid'],'group_id'=>$params['id'],'del'=>0);
		$fields	= array('id');
		$exists	= $this->match_model->getOne($wher1,$table1,$fields);
		if ($exists) {
			$this->error_->set_error(ErrCode::ERR_EXISTS_INVITATION_FAIL);
			return false;
		}

		// 获取战队信息
		$table2	= 'group';
		$where2	= array('id'=>$params['id'],'del'=>0);
		$fields	= array('name','g_id','g_name','m_id');
		$info	= $this->match_model->getOne($where2,$table2,$fields);
		if (!$info) {
			$this->error_->set_error(ErrCode::ERR_GROUP_NOT_EXISTS_FAIL);
			return false;
		}

		// 判断用户是否加入该游戏的其他战队
		$table3	= 'groupuser';
		$where3	= array('uuid'=>$params['uuid'],'g_id'=>$info['g_id'],'del'=>0);
		$fields	= array('id');
		$exists	= $this->match_model->getOne($where3,$table3,$fields);
		if ($exists) {
			$this->error_->set_error(ErrCode::ERR_GAME_GROUP_EXISTS);
			return false;
		}

		// 插入申请表
		$data	= array(
			'uuid'			=> $params['o_uuid'],
			'group_id'		=> $params['id'],
			'group_name'	=> $info['name'],
			'i_uuid'		=> $params['uuid'],
			'status'		=> 1,
		);
		$id	= $this->match_model->insertData($data,$table1);
		if (!$id) {
			$this->error_->set_error(ErrCode::ERR_ENTER_GROUP_FAIL);
			return false;
		}
		return true;
	}

	/**
	 * 获取战队申请列表
	 */
	public function getGroupApplyList($params)
	{
		// 判断该用户是否是队长、副队长
		$table	= 'groupuser';
		$where	= array('group_id'=>$params['id'],'uuid'=>$params['uuid'],'del'=>0);
		$fields	= array('type','g_id');
		$info	= $this->match_model->getOne($where,$table,$fields);
		if (!$info) {
			$this->error_->set_error(ErrCode::ERR_GROUP_NOT_EXISTS_FAIL);
			return false;
		}
		if ($info['type'] == 3) {
			$this->error_->set_error(ErrCode::ERR_NOTALLOW_GET_GROUPLIST_FAIL);
			return false;
		}
		// 获取战队申请列表
		$tableA		= 'invitation';
		$tableB		= 'user';
		$join_where	= array('invitation.i_uuid'=>'user.uuid');
		$where		= array('invitation.status'=>1,'invitation.del'=>0,'user.del'=>0);
		$fields		= array('invitation.id as id','user.uuid as uuid','user.nickname as nickname','user.icon as icon','user.wx_account as wx_account');
		$list		= $this->match_model->join($join_where,$where,$tableA,$tableB,$fields,$params['count']);
		if (!$list['total']) {
			$this->error_->set_error(ErrCode::ERR_DB_NO_DATA);
			return false;
		}

		// 返回数据
		$data['list']		= $list['data'];
		$data['pagecount']	= (int)ceil($list['total']/$params['count']);
		return $data;
	}

	/**
	 * 战队操作[1剔除队员2同意用户加入战队3忽略4自动退出战队5指定副队长6取消副队长]
	 */
	public function doGroup($params)
	{
		// 获取操作者信息
		$table	= 'groupuser';
		$where	= array('group_id'=>$params['id'],'uuid'=>$params['uuid'],'del'=>0);
		$fields	= array('type','g_id','group_name');
		$info	= $this->match_model->getOne($where,$table,$fields);
		if (!$info) {
			$this->error_->set_error(ErrCode::ERR_GROUP_NOT_EXISTS_FAIL);
			return false;
		}

		// 判断用户是否有权限[操作申请列表]
		if ($params['type'] != 4 && $info['type'] == 3) {// 普通队员-只能退出战队
			$this->error_->set_error(ErrCode::ERR_NOTALLOW_GET_GROUPLIST_FAIL);
			return false;
		}

		// 执行战队操作
		$params['u_type']	= $info['type'];
		$params['g_id']	= $info['g_id'];
		$params['name']	= $info['group_name'];
		if ($params['type'] == 1) { // 操作类型[1剔除队员]
			$this->doDelGroup($params);
		}elseif($params['type'] == 4) {// 4自动退出战队
			$params['o_uuid']	= $params['uuid'];
			$this->doCancelGroup($params);
		} elseif($params['type'] == 5 || $params['type'] == 6) {// 5指定副队长6取消队长
			if ($params['type'] == 5) {
				$type	= 1;
			} else {
				$type	= 2;
			}
			$this->doViceUser($params,$type);
		} else {// 2同意用户加入战队3忽略
			$this->doGroupUser($params);
		}
		return true;
	}

	/**
	 * 用户自动退出战队
	 */
	public function doCancelGroup($params)
	{
		// 获取战队成员列表
		$table	= 'groupuser';
		$where	= array('group_id'=>$params['id'],'del'=>0);
		$fields	= array('uuid','type','time');
		$orderby= array('time','asc');
		$u_list	= $this->match_model->getList($where,$table,$fields,0,$orderby);
		if (count($u_list) == 1) {
			// 删除成员
			$where2	= array('uuid'=>$params['uuid'],'del'=>0);
			$data2	= array('del'=>1);
			$res 	= $this->match_model->updateData($where2,$data2,$table);
			if (!$res) {
				$this->error_->set_error(ErrCode::ERR_SELF_DEL_GROUP_FAIL);
				return false;
			}

			// 删除战队
			$table3	= 'group';
			$where3	= array('id'=>$params['id'],'del'=>0);
			$data3	= array('del'=>1);
			$res	= $this->match_model->updateData($where3,$data3,$table3);
			if (!$res) {
				$this->error_->set_error(ErrCode::ERR_SELF_DEL_GROUP_FAIL);
				return false;
			}
			return true;
		}

		// 判断退队成员的type身份、选择新队长$_uuid
		if ($params['u_type'] == 1) {// 队长退出战队
			foreach ($u_list as $k=>$v) {
				if ($v['type'] == 1) {
					unset($u_list[$k]);
				}
				if ($v['type'] == 2) {
					$_uuid	= $v['uuid'];
				}
			}
			if (!$_uuid) {
				$_uuid	= array_values($u_list)[0]['uuid'];
			}

			$upt_data	= array(array('uuid'=>$_uuid,'type'=>1,'del'=>0));
			$ist_data	= array(array('group_id'=> $params['id'], 'group_name'=> $params['name'], 'uuid'=> $_uuid,'o_uuid'=>0,'type'=> 4));
		}

		// 更新战队成员表
		$upt_data[]	= array('uuid'=>$params['uuid'],'type'=>$params['u_type'],'del'=>1);
		$upt	= $this->match_model->updateBatch($upt_data,$table);
		if (!$upt) {
			$this->error_->set_error(ErrCode::ERR_SELF_DEL_GROUP_FAIL);
			return false;
		}

		// 记录战队成员变更表
		$table2		= 'grouphis';
		$ist_data[]	= array('group_id'=> $params['id'], 'group_name'=> $params['name'], 'uuid'=> $params['uuid'],'o_uuid'=>0,'type'=> 2);
		$id			= $this->match_model->insertBatch($ist_data,$table2);
		if (!$id) {
			$this->error_->set_error(ErrCode::ERR_SELF_DEL_GROUP_FAIL);
			return false;
		}
		return ture;
	}

	/**
	 * 踢出战队成员
	 */
	public function doDelGroup($params)
	{
		// 校验是否有该成员
		$table		= 'groupuser';
		$where		= array('group_id'=>$params['id'],'uuid'=>$params['o_uuid'],'del'=>0);
		$fields		= array('type');
		$guser_info	= $this->match_model->getOne($where,$table,$fields);
		if (!$guser_info) {
			return true;
		}
		// 校验用户是否有足够权限
		if ($guser_info['type'] ==1) {
			$this->error_->set_error(ErrCode::ERR_NOT_ALLOW_DEL_GROUP_USER_FAIL);
			return false;
		}

		// 更新战队成员表
		$data	= array('del'=>1);
		$where2	= array('uuid'=>$params['o_uuid'],'del'=>0);
		$upt	= $this->match_model->updateData($where2,$data,$table);
		if (!$upt) {
			$this->error_->set_error(ErrCode::ERR_DEL_GROUP_USER_FAIL);
			return false;
		}

		// 记录战队成员变更表【type = 1】
		$table3	= 'grouphis';
		$data3	= array(
			'group_id'		=> $params['id'],
			'group_name'	=> $params['name'],
			'uuid'			=> $params['o_uuid'],// 被踢出成员
			'o_uuid'		=> $params['uuid'],// 操作者
			'type'			=> 3,
		);
		$id	= $this->match_model->insertData($data3,$table3);
		if (!$id) {
			$this->error_->set_error(ErrCode::ERR_DO_GROUP_FAIL);
			return false;
		}
		return true;
	}

	/**
	 * 战队申请操作【type=2 同意加入战队 3忽略加入战队】
	 */
	public function doGroupUser($params)
	{
		// 校验用户是否已存在战队中
		$table	= 'groupuser';
		$where	= array('group_id'=>$params['id'],'uuid'=>$params['o_uuid'],'del'=>0);
		$fields	= array('type','g_id','group_name');
		$info	= $this->match_model->getOne($where,$table,$fields);
		// 将成员加入战队-校验用户是否已在战队中
		if ($info && $params['type'] == 2) {
			return true;
		}

		// 更新战队邀请表
		$table	= 'invitation';
		$where	= array('group_id'=>$params['id'],'i_uuid'=>$params['o_uuid'],'del'=>0);
		$data	= array('status'=>$params['type']);
		$res 	= $this->match_model->updateData($where,$data,$table);
		if (!$res) {
			$this->error_->set_error(ErrCode::ERR_DO_GROUP_FAIL);
			return false;
		}

		// 插入邀请处理-历史记录表
		$table2	= 'invitationhis';
		$data2	= array(
			'uuid'		=>$params['uuid'],
			'group_id'	=>$params['id'],
			'i_uuid'	=>$params['o_uuid'],
			'status'	=>$params['type']==2?1:2,//邀请状态[1同意2忽略]
		);
		$id	= $this->match_model->insertData($data2,$table2);
		if (!$id) {
			$this->error_->set_error(ErrCode::ERR_DO_GROUP_FAIL);
			return false;
		}

		// 更新战队成员表 && 记录成员战队变更消息 【type = 2】
		if ($params['type'] == 2) {
			// 将用户加入战队
			$table3	= 'groupuser';
			$data3	= array(
				'group_id'		=> $params['id'],
				'g_id'			=> $params['g_id'],
				'group_name'	=> $params['name'],
				'uuid'			=> $params['o_uuid'],
				'type'			=> 3,
			);
			$id	= $this->match_model->insertData($data3,$table3);
			if (!$id) {
				$this->error_->set_error(ErrCode::ERR_DO_GROUP_FAIL);
				return false;
			}

			// 记录战队变更记录
			$table3	= 'grouphis';
			$data3	= array(
				'group_id'		=> $params['id'],
				'group_name'	=> $params['name'],
				'uuid'			=> $params['o_uuid'],// 被邀请加入用户、被忽略用户
				'o_uuid'		=> $params['uuid'],//  操作者
				'type'			=> 1,
			);
			$id	= $this->match_model->insertData($data3,$table3);
			if (!$id) {
				$this->error_->set_error(ErrCode::ERR_DO_GROUP_FAIL);
				return false;
			}
		}

		return true;
	}

	/**
	 * 指定副队长
	 * @param $params
	 * @param $type = 5 指定副队长 6取消副队长
	 */
	public function doViceUser($params,$type = 1)
	{
		// 校验用户是否是队长
		if ($params['u_type'] != 1) {
			$this->error_->set_error(ErrCode::ERR_NOT_ALLOW_ADD_VICE_FAIL);
			return false;
		}

		// 判断是否已经存在副队长
		$table		= 'groupuser';
		$where		= array('group_id'=>$params['id'],'type'=>2,'del'=>0);
		$fields		= array('id');
		$exists		= $this->match_model->getOne($where,$table,$fields);
		if ($exists && $type == 1) {
			$this->error_->set_error(ErrCode::ERR_EXISTS_GROUP_VICE_FAIL);
			return false;
		}

		// 更新该成员为副队长、取消副队长
		$where2	= array('group_id'=>$params['id'],'uuid'=>$params['o_uuid'],'del'=>0);
		$type	= $type==1?2:3;
		$data	= array('type'=>$type);
		$upt	= $this->match_model->updateData($where2,$data,$table);
		if (!$upt) {
			$this->error_->set_error(ErrCode::ERR_ZD_VICE_FAIL);
			return false;
		}

		// 记录战队成员历史记录
		if ($type = 5) {
			$table3	= 'grouphis';
			$data3	= array(
				'group_id'		=> $params['id'],
				'group_name'	=> $params['name'],
				'uuid'			=> $params['o_uuid'],// 被取消用户
				'o_uuid'		=> $params['uuid'],// 操作者
				'type'			=> 5,
			);
			$id	= $this->match_model->insertData($data3,$table3);
			if (!$id) {
				$this->error_->set_error(ErrCode::ERR_DO_GROUP_FAIL);
				return false;
			}
		}
		return true;
	}

	/**
	 * 插入分享战队二维码记录
	 * @param $params
	 */
	public function doShare($params)
	{
		// 小程序二维码样式
		if (!$params['type']) {
			$params['type']	= 1;
		}
		// 获取战队创建日期
		$table		= 'group';
		$where		= array('id'=>$params['id'],'del'=>0);
		$fields		= array('time','name');
		$info		= $this->match_model->getOne($where,$table,$fields);
		if (!$info) {
			$this->error_->set_error(ErrCode::ERR_GROUP_NOT_EXISTS_FAIL);
			return false;
		}

		// 拼接分享URL
		$filepath	= config('passport.qrcode_path').date('Ymd',strtotime($info['time'])).'/';
		$filename	= md5($params['id']).'_'.$params['type'].'.png';
		$path2		= explode('public',$filepath);
		$code_url	= getenv('APP_URL').trim($path2[1],'\\').$filename;
		// 插入分享记录
		$data = array(
			'uuid'  		=> $params['uuid'],
			'group_id'  	=> $params['id'],
			'group_name'  => $info['name'],
			'url'  			=> (string)$code_url,
		);
		$id	= $this->match_model->insertData($data,'sharehis');
		if (!$id) {
			$this->error_->set_error(ErrCode::ERR_INSERT_SHARE_FAIL);
			return false;
		}
		return true;
	}

	/**
	 * 获取战队消息列表
	 * @param $params
	 */
	public function getGroupMsgList($params)
	{
		// join 查询列表
		$tableA		= 'grouphis';
		$tableB		= 'user';
		$join_where	= array('grouphis.uuid'=>'user.uuid');
		$where		= array('grouphis.del'=>0,'user.del'=>0);
		$fields		= array('grouphis.id as id','grouphis.uuid as uuid','grouphis.type as type','grouphis.o_uuid as o_uuid','grouphis.time as time','user.nickname as nickname','user.wx_account as wx_account','user.icon as icon');
		$list	= $this->match_model->join($join_where,$where,$tableA,$tableB,$fields,$params['count']);
		if (!$list['total']) {
			$this->error_->set_error(ErrCode::ERR_DB_NO_DATA);
			return false;
		}

		// 拼接用户头像
		$path	= config('passport.img_url');
		foreach ($list['data'] as $k=>&$v) {
			if ($v['icon']) {
				$v['icon']	= $path.$v['icon'];
			}
		}

		// 返回数据
		$data['list']		= $list['data'];
		$data['pagecount']	= (int)ceil($list['total']/$params['count']);
		return $data;
	}

	/**
	 * 获取战队分享二维码
	 * @param type 二维码类型 [1微信接口C 2微信接口B 3微信接口A]
	 */
	public function getQRcode($params)
	{
		// 小程序二维码样式
		if (!$params['type']) {
			$params['type']	= 1;
		}
		// 获取战队创建日期
		$table		= 'group';
		$where		= array('id'=>$params['id'],'del'=>0);
		$fields		= array('time');
		$info		= $this->match_model->getOne($where,$table,$fields);
		if (!$info) {
			$this->error_->set_error(ErrCode::ERR_GROUP_NOT_EXISTS_FAIL);
			return false;
		}

		// 校验二维码图片是否存在
		$filepath	= config('passport.qrcode_path').date('Ymd',strtotime($info['time'])).'/';
		$filename	= md5($params['id']).'_'.$params['type'].'.png';
		if (is_file($filepath.$filename)) {
			$path2			= explode('public',$filepath);
			$data['type']	= $params['type'];
			$data['img']	= getenv('APP_URL').trim($path2[1],'\\').$filename;
			return $data;
		}

		// 重新生成新二维码
		if (!is_dir($filepath)) {
			@mkdir($filepath, 0777);
		}
		// 获取微信接口HTTP
		$qrcode_url	= $this->wechat_qrcode_url;
		$path	= config('passport.qrcode_url');// 小程序跳转页面

		if ($params['type']	== 1) {
			$url			= $qrcode_url['C'];
			$para['width']	= 430;
			$para['path']	= $path.'?id='.$params['id'].'&uuid='.$params['uuid'];
		} elseif($params['type']	== 2){
			$url	= $qrcode_url['B'];
			 $para['scene']		= $params['id'].'_'.$params['uuid'];// 战队ID
			 $para['page']			= '';
			 $para['width']		= 430;
			 $para['auto_color']	= true;
			 $para['line_color']	= array('r'=>22,'g'=>34,'b'=>123);
		} else {
			$url					= $qrcode_url['A'];
			$para['width']			= 430;
			$para['path']			= $path.'?id='.$params['id'].'&uuid='.$params['uuid'];
			$para['auto_color']	= true;
			$para['line_color']	= array('r'=>22,'g'=>34,'b'=>123);
		}

		// 获取微信access_token
		$access_token	= $this->getAccessTokenByWeChat();
		$url			= $url.'?access_token='.$access_token['access_token'];
		$result			= curlPost($url,json_encode($para));
		$res		= file_put_contents($filepath.$filename, $result);
		if (!$res) {
			$this->error_->set_error(ErrCode::ERR_GET_WECHAT_QRCODE_FAIL);
			return false;
		}
		// 返回二维码图片路径 -- 路径规则 /public/qrcode/战队创建时间Ymd/MD5(战队ID)_TYPE.png
		$path2			= explode('public',$filepath);
		$data['type']	= $params['type'];// 二维码样式
		$data['img']	= getenv('APP_URL').trim($path2[1],'\\').$filename;
		return $data;
	}

	/**
	 * 获取微信access token
	 * 二维码接口B：适用于需要的码数量极多，或仅临时使用的业务场景
	 */
	public function getAccessTokenByWeChat()
	{
		// 获取配置参数
		$conf					= config('passport.wx_xcx');
		$params['grant_type']	= 'client_credential';
		$params['appid']		= $conf['appid'];
		$params['secret']		= $conf['secret'];

		// 获取微信access_token BY redis
		$access	= Cache::get($this->WeChat_ACCESS_TOKEN_.$conf['appid']);
		if ($access) {
			return $access;
		}

		// 请求微信接口
		$url	= 'https://api.weixin.qq.com/cgi-bin/token';
		$result	= curlGet($url,$params);
		$result	= json_decode($result,true);
		if (!$result['access_token']) {
			$this->error_->set_error(ErrCode::ERR_GET_WECHAT_ACCESS_TOKEN_FAIL);
			writeLog('get_WeChat_accessToken_fail:微信access_token获取失败',$result);
			return false;
		}

		// 保存微信access_token TO redis
		$item['access_token']   	= $result['access_token'];
		$item['get_ts'] 			= time();
		$item['expires_in']		= $result['expires_in'];
		$expire						= $result['expires_in']/60;// 时间单位：分
		Cache::put($this->WeChat_ACCESS_TOKEN_.$conf['appid'],$item,$expire);

		return $item;
	}



}