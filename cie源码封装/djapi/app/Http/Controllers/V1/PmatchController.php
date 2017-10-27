<?php

namespace App\Http\Controllers\V0;

use Validator;
use App\Http\Controllers\Controller;
use App\Http\Repositories\V0\PmatchRepository;
use Illuminate\Http\Request;
use ErrCode;

/**
 * Class PmatchController 约战模块
 * @package App\Http\Controllers\V0
 * @author huhong
 * @date 2017-08-01
 */
class PmatchController extends Controller
{
    protected $pmatch_repo;
    public function __construct(PmatchRepository $pmatch_repo)
    {
        parent::__construct(false);
        $this->pmatch_repo = $pmatch_repo;
        $this->error_       = $pmatch_repo->error_;
    }


    /**
     * @SWG\Get(
     *   path="/pmatch/myreserve_list",
     *   summary="获取我的约战列表接口",
     *   tags={"约战模块"},
     *   @SWG\Parameter(name="uuid", in="query", required=true, description="用户uuid", type="string"),
     *   @SWG\Parameter(name="token", in="query", required=true, description="token令牌", type="string"),
     *   @SWG\Parameter(name="sign",in="query", required=true, description="签名", type="string"),
     *   @SWG\Parameter(name="page", in="query", required=true, description="列表起始页", type="integer",default="1"),
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
    public function myReserveList(Request $request){

        // 接收公共参数
        $params 			= $this->getPublicParams($request);
        $params['page'] 	= (int)$request->input('page');
        $params['count']	= (int)$request->input('count');

        // 校验参数
        if (!$params['page'] || !$params['count']) {
            $this->error_->set_error(ErrCode::ERR_PARA);
            writeLog('参数错误',$params);
            return $this->return_output_json();
        }

        // 校验签名
        if (!checkSign($params,$params['sign'])) {
            $this->error_->set_error(ErrCode::ERR_PARA);
            writeLog('参数错误',$params);
            return $this->return_output_json();
        }

        $data = $this->pmatch_repo->getPmatchMyReserveList($params);
        if (!$data) {
            return $this->return_output_json();
        }
        // 返回我的约战列表
        return $this->return_output_json($data);

    }

    /**
     * @SWG\Get(
     *   path="/pmatch/num",
     *   summary="获取未完成约战数",
     *   tags={"约战模块"},
     *   @SWG\Parameter(name="uuid", in="query", required=true, description="用户uuid", type="string"),
     *   @SWG\Parameter(name="token", in="query", required=true, description="token令牌", type="string"),
     *   @SWG\Parameter(name="sign",in="query", required=true, description="签名", type="string"),
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
    public function num(Request $request){

        // 接收公共参数
        $params 			= $this->getPublicParams($request);
        // 校验签名
        if (!checkSign($params,$params['sign'])) {
            $this->error_->set_error(ErrCode::ERR_PARA);
            writeLog('参数错误',$params);
            return $this->return_output_json();
        }
        $data = $this->pmatch_repo->getNum($params);
        if (!$data) {
            return $this->return_output_json();
        }
        // 返回未完成约战数
        return $this->return_output_json($data);

    }

    /**
     * @SWG\Get(
     *   path="/pmatch/myrecord_list",
     *   summary="获取我的历史战绩接口",
     *   tags={"约战模块"},
     *   @SWG\Parameter(name="uuid", in="query", required=true, description="用户uuid", type="string"),
     *   @SWG\Parameter(name="token", in="query", required=true, description="token令牌", type="string"),
     *   @SWG\Parameter(name="sign",in="query", required=true, description="签名", type="string"),
     *   @SWG\Parameter(name="page", in="query", required=true, description="列表起始页", type="integer",default="1"),
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
    public function myRecordList(Request $request){
        //TODO   point_change  积分变更值
        // 接收公共参数
        $params 			= $this->getPublicParams($request);
        $params['page'] 	= (int)$request->input('page');
        $params['count']	= (int)$request->input('count');

        // 校验参数
        if (!$params['page'] || !$params['count']) {
            $this->error_->set_error(ErrCode::ERR_PARA);
            writeLog('参数错误',$params);
            return $this->return_output_json();
        }

        // 校验签名
        if (!checkSign($params,$params['sign'])) {
            $this->error_->set_error(ErrCode::ERR_PARA);
            writeLog('参数错误',$params);
            return $this->return_output_json();
        }

        $data = $this->pmatch_repo->getPmatchMyRecordList($params);
        if (!$data) {
            return $this->return_output_json();
        }
        // 返回我的历史战绩列表
        return $this->return_output_json($data);
    }

    /**
     * @SWG\Get(
     *   path="/pmatch/reserve_list",
     *   summary="获取约战列表接口",
     *   tags={"约战模块"},
     *   @SWG\Parameter(name="uuid", in="query", required=true, description="用户uuid", type="string"),
     *   @SWG\Parameter(name="token", in="query", required=true, description="token令牌", type="string"),
     *   @SWG\Parameter(name="sign",in="query", required=true, description="签名", type="string"),
     *   @SWG\Parameter(name="id", in="query", required=true, description="游戏ID", type="integer"),
     *   @SWG\Parameter(name="page", in="query", required=true, description="列表起始页", type="integer",default="1"),
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
    public function reserveList(Request $request){

        // 接收公共参数
        $params 			= $this->getPublicParams($request);
        $params['id'] 	    = (int)$request->input('id');
        $params['page'] 	= (int)$request->input('page');
        $params['count']	= (int)$request->input('count');

        // 校验参数
        if (!$params['page'] || !$params['count'] || !$params['id']) {
            $this->error_->set_error(ErrCode::ERR_PARA);
            writeLog('参数错误',$params);
            return $this->return_output_json();
        }

        // 校验签名
        if (!checkSign($params,$params['sign'])) {
            $this->error_->set_error(ErrCode::ERR_PARA);
            writeLog('参数错误',$params);
            return $this->return_output_json();
        }

        $data = $this->pmatch_repo->getPmatchReserveList($params);
        if (!$data) {
            return $this->return_output_json();
        }
        // 返回约战列表
        return $this->return_output_json($data);

    }


    /**
     * @SWG\Get(
     *   path="/pmatch/reserve",
     *   summary="发布约战接口",
     *   tags={"约战模块"},
     *   @SWG\Parameter(name="uuid", in="query", required=true, description="用户uuid", type="string"),
     *   @SWG\Parameter(name="token", in="query", required=true, description="token令牌", type="string"),
     *   @SWG\Parameter(name="sign",in="query", required=true, description="签名", type="string"),
     *   @SWG\Parameter(name="id", in="query", required=true, description="游戏ID", type="integer"),
     *   @SWG\Parameter(name="type", in="query", required=true, description="约战类型", type="integer",default="1"),
     *   @SWG\Parameter(name="date", in="query", required=true, description="约战时间", type="string"),
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
    public function reserve(Request $request){

        // 接收公共参数
        $params 			= $this->getPublicParams($request);
        $params['id'] 	    = (int)$request->input('id');    //游戏ID
        $params['type'] 	= (int)$request->input('type');    //约战类型
        $params['date']	    = $request->input('date');    //约战时间

        // 校验参数
        if (!$params['id'] || !$params['type'] || !$params['date']) {
            $this->error_->set_error(ErrCode::ERR_PARA);
            writeLog('参数错误',$params);
            return $this->return_output_json();
        }

        //校验时间 必须为当前时间之后
        $inputTime = strtotime($params['date']);
        $time = strtotime("now");
        if($inputTime < $time){
            $this->error_->set_error(ErrCode::ERR_PARA);
            writeLog('参数错误',$params);
            return $this->return_output_json();
        }
        //校验时间 必须为整点或者半点
        $timeI = date('i', $inputTime);
        $timeS = date('s', $inputTime);
        if($timeS != 0 || ($timeI != 0 && $timeI != 30))
        {
            $this->error_->set_error(ErrCode::ERR_PARA);
            writeLog('参数错误',$params);
            return $this->return_output_json();
        }

        // 校验签名
        if (!checkSign($params,$params['sign'])) {
            $this->error_->set_error(ErrCode::ERR_PARA);
            writeLog('参数错误',$params);
            return $this->return_output_json();
        }

        $data = $this->pmatch_repo->getPmatchReserve($params);
        if (!$data) {
            return $this->return_output_json();
        }
        return $this->return_output_json();

    }


    /**
     * @SWG\Get(
     *   path="/pmatch/do_pmatch",
     *   summary="迎战|组队操作接口",
     *   tags={"约战模块"},
     *   @SWG\Parameter(name="uuid", in="query", required=true, description="用户uuid", type="string"),
     *   @SWG\Parameter(name="token", in="query", required=true, description="token令牌", type="string"),
     *   @SWG\Parameter(name="sign",in="query", required=true, description="签名", type="string"),
     *   @SWG\Parameter(name="id", in="query", required=true, description="约战ID", type="integer"),
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
    public function doPmatch(Request $request){

        // 接收公共参数
        $params 			= $this->getPublicParams($request);
        $params['id'] 	    = (int)$request->input('id');    //约战ID

        // 校验参数
        if (!$params['id']) {
            $this->error_->set_error(ErrCode::ERR_PARA);
            writeLog('参数错误',$params);
            return $this->return_output_json();
        }

        // 校验签名
        if (!checkSign($params,$params['sign'])) {
            $this->error_->set_error(ErrCode::ERR_PARA);
            writeLog('参数错误',$params);
            return $this->return_output_json();
        }

        $data = $this->pmatch_repo->getPmatchDoPmatch($params);

        if (!$data) {
            return $this->return_output_json();
        }
        // 返回对战信息
        return $this->return_output_json($data);

    }


    /**
     * @SWG\Get(
     *   path="/pmatch/do_result",
     *   summary="比赛结果处理接口",
     *   tags={"约战模块"},
     *   @SWG\Parameter(name="uuid", in="query", required=true, description="用户uuid", type="string"),
     *   @SWG\Parameter(name="token", in="query", required=true, description="token令牌", type="string"),
     *   @SWG\Parameter(name="sign",in="query", required=true, description="签名", type="string"),
     *   @SWG\Parameter(name="id", in="query", required=true, description="约战ID", type="integer"),
     *   @SWG\Parameter(name="type", in="query", required=true, description="比赛结果处理", type="integer"),
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
    public function doResult(Request $request){

        // 接收公共参数
        $params 	    = $this->getPublicParams($request);
        $params['id'] 	= (int)$request->input('id');    //约战ID
        $params['type'] = (int)$request->input('type');    //比赛结果处理[1胜(赞)2负(弱爆了)]

        // 校验参数
        if (!$params['id'] || !$params['type']) {
            $this->error_->set_error(ErrCode::ERR_PARA);
            writeLog('参数错误',$params);
            return $this->return_output_json();
        }

        // 校验签名
        if (!checkSign($params,$params['sign'])) {
            $this->error_->set_error(ErrCode::ERR_PARA);
            writeLog('参数错误',$params);
            return $this->return_output_json();
        }

        $data = $this->pmatch_repo->getPmatchDoResult($params);

        if (!$data) {
            return $this->return_output_json();
        }
        return $this->return_output_json();

    }

    /**
     * @SWG\Post(
     *   path="/pmatch/upload_img",
     *   summary="上传比赛截图",
     *   tags={"约战模块"},
     *   consumes={"multipart/form-data"},
     *   @SWG\Parameter(name="uuid", in="query", required=true, description="用户uuid", type="string"),
     *   @SWG\Parameter(name="token", in="query", required=true, description="token令牌", type="string"),
     *   @SWG\Parameter(name="sign",in="query", required=true, description="签名", type="string"),
     *   @SWG\Parameter(name="id", in="query", required=true, description="约战ID", type="integer"),
     *   @SWG\Parameter(name="file", in="formData", required=true, description="图片", type="file"),
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
    public function uploadImg(Request $request){

        // 接收公共参数
        $params 		= $this->getPublicParams($request);
        $params['id']   = (int)$request->input('id');    //约战ID
        $file 	        = $request->file('file');    //比赛截图

        // 校验参数
        if (!$params['id']) {
            $this->error_->set_error(ErrCode::ERR_PARA);
            writeLog('参数错误',$params);
            return $this->return_output_json();
        }

        //判断文件是否上传
        if(!$file){
            $this->error_->set_error(ErrCode::ERR_NOT_UPLOAD_GAMEPIC_FALI);
            return $this->return_output_json();
        }

        // 校验签名
        if (!checkSign($params,$params['sign'])) {
            $this->error_->set_error(ErrCode::ERR_PARA);
            writeLog('参数错误',$params);
            return $this->return_output_json();
        }

        $data = $this->pmatch_repo->getPmatchUploadImg($params,$file);

        if (!$data) {
            return $this->return_output_json();
        }
        // 返回上传截图状态信息
        return $this->return_output_json();
   
    }

}
