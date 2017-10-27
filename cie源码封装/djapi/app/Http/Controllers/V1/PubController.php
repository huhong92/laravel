<?php
namespace App\Http\Controllers\V0;

use Validator;
use App\Http\Controllers\Controller;
use App\Http\Repositories\V0\PubRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use ErrCode;

/**
 * Class PubController 公共功能模块
 * @package App\Http\Controllers\v0
 * @author huhong
 * @date 2017-08-22 17:35:00
 */
class PubController extends Controller
{
    protected $pub_repo;
    public function __construct(PubRepository $pub_repo)
    {
        parent::__construct(false);
        $this->pub_repo	= $pub_repo;
        $this->error_   	= $pub_repo->error_;
    }

    /**
     * @SWG\Post(
     *   path="/pub/decode_qrcode",
     *   summary="解析二维码",
	 *   consumes={"multipart/form-data"},
     *   tags={"Pub"},
     *   @SWG\Parameter(name="file",in="formData",required=true,description="二维码图片",type="file"),
	 *   @SWG\Parameter(name="sign",in="query",required=true,description="sign校验",type="string"),
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
    public function decodeQRcode(Request $request)
    {
        // 接收校验参数
        $file				= $request->file('file');// 二维码图片
		$params['sign']	= $request->input('sign');

        // 校验参数
        if (!$file || !$params['sign']) {
            $this->error_->set_error(ErrCode::ERR_PARA);
            writeLog('参数错误', $params);
            return $this->return_output_json();
        }
        // 校验签名
//        if (!checkSign($params, $params['sign'])) {
//            $this->error_->set_error(ErrCode::ERR_PARAM_SIGN);
//            writeLog('签名错误', $params);
//            return $this->return_output_json();
//        }

		// 上传二维码图片
		if ($file_ = uploadPicture($file)) {
			$params['img']  = $file_;
		}

		$data = $this->pub_repo->doDecodeQRcode($params);
        return $this->return_output_json($data);
    }

	/**
	 * @SWG\Get(
	 *   path="/pub/generate_qrcode",
	 *   summary="根据url生成普通二维码",
	 *   tags={"Pub"},
	 *   @SWG\Parameter(name="url",in="query",required=true,description="二维码url",type="string"),
	 *   @SWG\Parameter(name="sign",in="query",required=true,description="sign校验",type="string"),
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
    public function genreateQRcode(Request $request)
	{
		// 接收校验参数
		$params['url']		= $request->input('url');
		$params['sign']	= $request->input('sign');

		// 校验参数
		if (!$params['url'] || !$params['sign']) {
			$this->error_->set_error(ErrCode::ERR_PARA);
			writeLog('参数错误', $params);
			return $this->return_output_json();
		}
		// 校验签名
		if (!checkSign($params, $params['sign'])) {
			$this->error_->set_error(ErrCode::ERR_PARAM_SIGN);
			writeLog('签名错误', $params);
			return $this->return_output_json();
		}

		// 生成普通二维码
		$data = $this->pub_repo->doGenerateQRcode($params);
		return $this->return_output_json($data);
	}

	/**
	 * @SWG\Post(
	 *   path="/pub/generate2_qrcode",
	 *   summary="根据url生成固定样式二维码",
	 *   consumes={"multipart/form-data"},
	 *   tags={"Pub"},
	 *   @SWG\Parameter(name="url",in="query",required=true,description="二维码url",type="string"),
	 *   @SWG\Parameter(name="text",in="query",required=false,description="二维码文字信息",type="string"),
	 *   @SWG\Parameter(name="color",in="query",required=false,description="文字颜色隔开",type="string"),
	 *   @SWG\Parameter(name="p",in="query",required=false,description="二维码大小",type="string"),
	 *   @SWG\Parameter(name="bgcolor",in="query",required=false,description="二维码背景色隔开",type="string"),
	 *   @SWG\Parameter(name="file",in="formData",required=false,description="嵌入logo",type="file"),
	 *   @SWG\Parameter(name="sign",in="query",required=true,description="sign校验",type="string"),
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
	public function generateQRcode2(Request $request)
	{
		// 接收校验参数
		$file					= $request->file('file');// 嵌入logo
		$params['url']			= $request->input('url');// 二维码url
		$params['text']		= $request->input('text');// 嵌入文字信息
		$params['color']		= $request->input('color');// 字体颜色,隔开
		$params['p']			= $request->input('p');// 二维码大小
		$params['bgcolor']		= $request->input('bgcolor');// 二维码背景色
		$params['sign']		= $request->input('sign');// 签名

		// 校验参数
		if (!$params['url'] || !$params['sign']) {
			$this->error_->set_error(ErrCode::ERR_PARA);
			writeLog('参数错误', $params);
			return $this->return_output_json();
		}

		// 校验签名
		if (!checkSign($params, $params['sign'])) {
			$this->error_->set_error(ErrCode::ERR_PARAM_SIGN);
			writeLog('签名错误', $params);
			return $this->return_output_json();
		}

		// 上传二维码图片
		if ($file) {
			if ($file_ = uploadPicture($file)) {
				$params['logo']	= $file_;
			}
		}

		// 生成固定格式二维码
		$data = $this->pub_repo->doGenerateQRcode2($params);
		return $this->return_output_json($data);
	}

	public function pmatchResultStatics()
	{
		$data = $this->pub_repo->getPmatchResultStatics();
		echo($data);
	}








}
