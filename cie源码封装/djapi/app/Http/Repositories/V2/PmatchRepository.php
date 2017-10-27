<?php
namespace App\Http\Repositories\V0;

use App\Http\Repositories\Repository;
use App\Models\V0\PmatchModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Error_;
use ErrCode;

class PmatchRepository extends Repository {

    public $error_;
    public $pmatch_model;
    public function __construct(Request $request)
    {
        $this->error_ = app('Error_');
        $this->pmatch_model = new PmatchModel();
        parent::__construct($request);
    }


    /*
     * 我的约战列表-待确认战绩
     */
    public function getPmatchMyReserveList($params){

        $where = [
            ['A.status', '=','2'],
            ['A.del', '=', '0'],    //约战表
            ['B.del', '=', '0'],    //我的游戏信息表
            ['U.del', '=', '0'],    //用户信息表
            ['A.uuid', '=', $params['uuid']],
        ];
        $orWhere = [
            ['A.status', '=','2'],
            ['A.del', '=', '0'],
            ['B.del', '=', '0'],
            ['U.del', '=', '0'],
            ['A.o_uuid', '=', $params['uuid']],
        ];

        $listObj = DB::table('pmatch as A')
        ->select('A.id as id','A.g_id as g_id','A.g_name as name','A.type as type','A.status as status','A.date as date','B.points as point','U.icon as icon')
        ->leftJoin('mygame as B', function ($join) {
            $join->on('A.uuid', '=', 'B.uuid')->on('A.g_id', '=', 'B.g_id');
        })
        ->leftJoin('user as U','A.uuid','=','U.uuid')
        ->where($where)
        ->orWhere($orWhere)
        ->paginate($params['count']);

        $list	= $this->stdclassToArray($listObj);
        if (!$list['data']) {
            $this->error_->set_error(ErrCode::ERR_DB_NO_DATA);
            writeLog('game_info_empty:暂无待确认战绩',$params);
            return false;
        }
        $data['pagecount']	= (int)ceil($list['total']/$params['count']);
        $data['list']		= $list['data'];
        return $data;
    }

    /*
     * 我的约战列表-历史战绩
     */
    public function getPmatchMyRecordList($params){

        $where = [
            ['A.status', '=','4'],
            ['A.del', '=', '0'],    //约战表
            ['B.del', '=', '0'],    //我的游戏信息表
            ['U.del', '=', '0'],    //用户信息表
            ['A.uuid', '=', $params['uuid']],
        ];
        $orWhere = [
            ['A.status', '=','4'],
            ['A.del', '=', '0'],
            ['B.del', '=', '0'],
            ['U.del', '=', '0'],
            ['A.o_uuid', '=', $params['uuid']],
        ];

        $listObj = DB::table('pmatch as A')
        ->select('A.id as id','A.g_id as g_id','A.g_name as name','A.type as type','A.result as result','A.date as date','B.points as point','U.icon as icon')
        ->leftJoin('mygame as B', function ($join) {
            $join->on('A.uuid', '=', 'B.uuid')->on('A.g_id', '=', 'B.g_id');
        })
        ->leftJoin('user as U','A.uuid','=','U.uuid')
        ->where($where)
        ->orWhere($orWhere)
        ->paginate($params['count']);

        $list	= $this->stdclassToArray($listObj);
        if (!$list['data']) {
            $this->error_->set_error(ErrCode::ERR_DB_NO_DATA);
            writeLog('game_info_empty:暂无历史战绩',$params);
            return false;
        }
        $data['pagecount']	= (int)ceil($list['total']/$params['count']);
        $data['list']		= $list['data'];
        return $data;
    }

    /*
     * 获取未完成约战数
     */
    public function getNum($params){

        $where = [
            ['status', '=', '1'],
            ['del', '=', '0'],
            ['uuid', '=', $params['uuid']],
        ];
        $orWhere = [
            ['status', '=', '1'],
            ['del', '=', '0'],
            ['o_uuid', '=', $params['uuid']],
        ];

        $num = DB::table('pmatch')
        ->where($where)
        ->orWhere($orWhere)
        ->count();
        $data['num']	= $num;
        return $data;
    }

    /*
     * 获取约战列表
     */
    public function getPmatchReserveList($params) {

        //获取前一天时间，为了关联游戏胜率
        $ydate = date("Ymd",strtotime("-1 day"));
        $where = [
            ['pm.status', '=', 1],
            ['pm.g_id', '=', $params['id']],
            ['pm.del', '=', 0],     //约战表
            ['mg.del', '=', 0],     //我的游戏信息表
            ['us.del', '=', 0],     //用户信息表
            ['pmat.del', '=', 0],   //胜率表
            ['pmat.date', '=', $ydate],
        ];
        $listObj = DB::table('pmatch as pm')
        ->select('pm.id as id','pm.uuid as uuid','pm.g_id as g_id','pm.type as type','pm.date as date','mg.points as point','us.nickname as nickname','us.icon as icon','ga.name as name','pmat.percent as percent')
        ->leftJoin('mygame as mg', function ($join) {
            $join->on('pm.uuid', '=', 'mg.uuid')->on('pm.g_id', '=', 'mg.g_id');
        })
        ->leftJoin('user as us','pm.uuid','=','us.uuid')
        ->leftJoin('game as ga','pm.g_id','=','ga.id')
        ->leftJoin('pmatchstatis as pmat', function ($join) {
            $join->on('pm.uuid', '=', 'pmat.uuid')->on('pm.g_id', '=', 'pmat.g_id');
        })
        ->where($where)
        ->paginate($params['count']);

        $list	= $this->stdclassToArray($listObj);
        if (!$list['data']) {
            $this->error_->set_error(ErrCode::ERR_DB_NO_DATA);
            writeLog('game_info_empty:暂无约战信息',$params);
            return false;
        }
        $data['g_id']		= $params['id'];
        $data['name']		= $list['data'][0]['name'];
        $data['pagecount']	= (int)ceil($list['total']/$params['count']);
        $data['list']		= $list['data'];
        foreach ($data['list'] as $key => $val) {
            unset($data['list'][$key]['name']);
            unset($data['list'][$key]['g_id']);
        }
        return $data;
    }


    /*
     * 发布约战
     */
    public function getPmatchReserve($params){

        //判断发布时间是否冲突
        $table2 = 'pmatch';

        $wheredate = [
            ['date', '=',$params['date']],
            ['uuid', '=', $params['uuid']],
            ['del', '=', '0'],
        ];
        $orWheredate = [
            ['date', '=',$params['date']],
            ['o_uuid', '=', $params['uuid']],
            ['del', '=', '0'],
        ];

        $pmatchObj = DB::table('pmatch')
        ->select('id')
        ->where($wheredate)
        ->orWhere($orWheredate)
        ->first();

        $pmatchid	= $this->stdclassToArray($pmatchObj);
        if($pmatchid){
            $this->error_->set_error(ErrCode::ERR_MATCH_PUBLISH_TIME_CONFLICT);
            return false;
        }

        //根据游戏ID查询游戏name
        $table = 'game';
        $where	= array('id'=>$params['id'],'del'=>0);
        $fields	= array('name');
        $info	= $this->pmatch_model->getOne($where,$table,$fields);
        //插入数据

        $data	= array(
            'uuid'			=> $params['uuid'],
            'g_id'			=> $params['id'],
            'g_name'		=> $info['name'],
            'type'			=> $params['type'],
            'date'          => $params['date'],
            'o_uuid'		=> 0,
            'status'		=> 1,
            'time'			=> date("Y-m-d H:i:s"),
        );

        $id = $this->pmatch_model->insertData($data,$table2);
        if (!$id) {
            $this->error_->set_error(ErrCode::ERR_MATCH_PUBLISH_FALI);
            return false;
        }
        return $id;
    }

    /*
     * 迎战|组队操作
     */
    public function getPmatchDoPmatch($params){

        //根据约战ID更新约战列表
        $table = 'pmatch';
        $where	= array('id'=>$params['id'],'del'=>0);
        $updata	= array(
            'o_uuid'		=> $params['uuid'],
            'status'		=> 2,
        );
        $info	= $this->pmatch_model->updateData($where,$updata,$table);
        //返回对手信息
        $tableB = 'user';
        $fields	= array('pmatch.id as id','pmatch.type as type','pmatch.date as date','user.wx_account as wx_account');
        $join_where = array($table.'.uuid'=>$tableB.'.uuid');
        $where = array(
            'pmatch.id'=>$params['id'],
            'pmatch.del'=>0,
            'user.del'=>0
        );
        $pinfo	= $this->pmatch_model->leftJoin($join_where,$where,$table,$tableB,$fields);
        if (empty($pinfo)) {
            $this->error_->set_error(ErrCode::ERR_DB_NO_DATA);
            return false;
        }
        $data = $pinfo[0];
        return $data;
    }


    /*
     * 比赛结果处理
     */
    public function getPmatchDoResult($params){

        //根据约战ID查询约战信息
        $table = 'pmatch';
        $where	= array('id'=>$params['id'],'del'=>0);
        $fields	= array('id','uuid','type');
        $info	= $this->pmatch_model->getOne($where,$table,$fields);

        //插入数据
        $table2 = 'pmatchhis';
        $data	= array(
            'p_id'		=> $params['id'],
            'type'		=> $info['type'],
            'uuid'		=> $info['uuid'],
            'o_type'	=> $params['type'],
            'pic'	=> 1,
        );
        $id = $this->pmatch_model->insertData($data,$table2);
        if (!$id) {
            $this->error_->set_error(ErrCode::ERR_NOT_UPLOAD_GAMEPIC_FALI);
            return false;
        }
        return $id;
    }


    /*
     * 上传比赛截图
     */
    public function getPmatchUploadImg($params,$file){

        $upload_path = $params['uuid'];
        $ext = array('jpg','jpeg','png','bmp');
        $respath = uploadPicture($file,$upload_path,0,$ext);
        //判断上传图片类型是否OK
        if (!$respath) {
            $this->error_->set_error(ErrCode::ERR_UPLOAD_GAMEPIC_TYPE_FALI);
            return false;
        }
        //根据约战ID查询约战信息
        $table = 'pmatch';
        $where	= array('id'=>$params['id'],'del'=>0);
        $fields	= array('id','uuid','type');
        $info	= $this->pmatch_model->getOne($where,$table,$fields);

        //插入数据
        $table2 = 'pmatchhis';
        $data	= array(
            'p_id'		=> $info['id'],
            'type'		=> $info['type'],
            'uuid'		=> $info['uuid'],
            'o_type'	=> 3,
            'pic'       => $respath,
            'del'		=> 0,
        );
        $id = $this->pmatch_model->insertData($data,$table2);
        if (!$id) {
            $this->error_->set_error(ErrCode::ERR_NOT_UPLOAD_GAMEPIC_FALI);
            return false;
        }
        return $id;

    }


}
