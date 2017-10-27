<?php
namespace App\Http\Repositories\V0;

use App\Http\Repositories\Repository;
use Illuminate\Http\Request;
use App\Models\V0\PmatchModel;
use Illuminate\Support\Facades\Log;
use Error_;
use ErrCode;
use QRcode;

class PubRepository extends Repository {

    public $error_;
	public $qrcode_path;
	public $pmatch_model;
    public function __construct(Request $request)
    {
        $this->error_ = app('Error_');
        parent::__construct($request);
		$this->qrcode_path		= config('passport.qrcode_path').date('Ymd').'/';
		$this->pmatch_model = new PmatchModel();
    }

	/**
	 * 解析二维码
	 * @param $params
	 */
	public function doDecodeQRcode($params)
	{
		//新建一个图像对象
		$image = new \ZBarCodeImage($params['img']);

		// 创建一个二维码识别器
		$scanner = new \ZBarCodeScanner();

		//识别图像
		$barcode = $scanner->scan($image);
		if (!$barcode) {
			$this->error_->set_error(ErrCode::ERR_DECODE_QRCODE_FAIL);
			return false;
		}
		return $barcode;
	}

	/**
	 * 生成普通二维码
	 * @param $params
	 */
	public function doGenerateQRcode($params)
	{
		$url        = isset($params["url"]) ? $params["url"] : 'help';
		$errorLevel = isset($params["e"]) ? $params["e"] : 'H';
		$PointSize  = isset($params["p"]) ? $params["p"] : '22.3';
		$margin     = isset($params["m"]) ? $params["m"] : '1';

		if (!is_dir($this->qrcode_path)) {
			@mkdir($this->qrcode_path, 0777);
		}
		$filename	= $this->qrcode_path.time().rand(100,999).'.png';
		preg_match('/http:\/\/([\w\W]*?)\//si', $url, $matches);
		QRcode::png($url, $filename, $errorLevel, $PointSize, $margin);

		// 返回二维码图片链接地址
		$path	= explode('public',$filename);
		return getenv('APP_URL').trim($path[1],'\\');
	}

	/**
	 * 生成固定格式二维码
	 */
	public function doGenerateQRcode2($params)
	{
		$url        = isset($params["url"]) ? $params["url"] : 'help';
		$errorLevel = isset($params["e"]) ? $params["e"] : 'H';
		$PointSize  = isset($params["p"]) ? $params["p"] : '22.3';
		$margin     = isset($params["m"]) ? $params["m"] : '5';
		$bgcolor	= ($params['bgcolor']) ? explode(',',trim($params['bgcolor'],',')) : array(255,255,255);
		// 二维码存放路径
		if (!is_dir($this->qrcode_path)) {
			@mkdir($this->qrcode_path, 0777);
		}
		$QR		= $this->qrcode_path.time().rand(100,999).'.png';
		QRcode::png($url, $QR, $errorLevel, $PointSize,$margin,false,$bgcolor);

		// 嵌入文字
		if ($params['text']) {
			// 字体颜色
			$color	= $params['color']?explode(',',trim($params['color'],',')):array(0,0,0);
			$info 	= getimagesize($QR);
			$type 	= image_type_to_extension($info[2],false);
			$fun 	= "imagecreatefrom" .$type;
			$image 	= $fun($QR);
			$fontsize	= $PointSize+3;
			$x		= ($info[0]-(mb_strlen($params['text'])+$margin)*$fontsize)/2;
			$y		= ($info[1]-$fontsize-$margin);

			$col 	= imagecolorallocate($image,$color[0],$color[1],$color[2]);
			// 写入文字
			// $font='C:/Windows/Fonts/simkai.ttf'; // windows字体文件
			$font	= '/usr/share/fonts/simkai.ttf';// ubuntu字体文件
			imagettftext($image,$fontsize, 0, $x, $y,$col,$font,$params['text']);
			$picinfo = pathinfo($QR);//解析源图像的名字和路径信息

			$pre = "n_";
			$newpicname= $this->qrcode_path.$pre.$picinfo["basename"];
			imagepng($image,$newpicname);
			imagedestroy($image);

			$QR	= $newpicname;
		}

		// 设置背景颜色
		if ($params['bgcolor']) {

		}

		// 嵌入logo
		$logo	= $params['logo'];
		if($logo !== FALSE){
			$QR = imagecreatefromstring(file_get_contents($QR));
			$logo = imagecreatefromstring(file_get_contents($logo));
			$QR_width = imagesx($QR);
			$QR_height = imagesy($QR);
			$logo_width = imagesx($logo);
			$logo_height = imagesy($logo);
			$logo_qr_width = $QR_width / 5;
			$scale = $logo_width / $logo_qr_width;
			$logo_qr_height = $logo_height / $scale;
			$from_width = ($QR_width - $logo_qr_width) / 2;
			imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width, $logo_qr_height, $logo_width, $logo_height);

			$logoname	= $this->qrcode_path.time().rand(100,999).'_logo.png';
			imagepng($QR,$logoname);
			$QR	= $logoname;
		}

		// 返回二维码图片链接地址
		$path	= explode('public',$QR);
		return getenv('APP_URL').trim($path[1],'\\');
	}





	/**
	 * 二次封装二维码
	 * @param $params
	 * 200,60,0,array(0,0,0,20),$imgname,$num
	 */
	public function doEncodeQRcode($params)
	{
		$fontsize	= 5;
		$color		= explode(',',trim($params['color'],','));
		$img		= $params['img'];
		$text		= $params['text'];

		foreach ($img as $key => $value) {
			$info = getimagesize($value);
			$type = image_type_to_extension($info[2],false);
			$fun = "imagecreatefrom" .$type;
			$image = $fun($value);
			$x	= ($info[0]/2 - strlen($text)*3);
			$y	= ($info[1]-70);
			$col = imagecolorallocatealpha($image,$color[0],$color[1],$color[2],$color[3]);
			imagestring($image,$fontsize,$x,$y,$text,$col);
			$picinfo = pathinfo($value);//解析源图像的名字和路径信息

			$pre = "n_";
			$newpicname= $this->qrcode_path.$pre.$picinfo["basename"];
			imagejpeg($image,$newpicname);
			imagedestroy($image);
			// 返回图片地址
			$new_img[]	= $newpicname;
		}

		return $new_img;
	}

	/**
	 * 统计约战胜率百分比
	 */
	public function getPmatchResultStatics()
	{
		// 约战列表
		$where	= array('type'=>1,'status'=>4);
		$table	= 'pmatch';
		$fields	= array('uuid','o_uuid','type','result','g_id','g_name');
		$list	= $this->pmatch_model->getList($where,$table,$fields);
		if (!$list) {
			return true;
		}

		// 拼接数据-统计用户胜利次数
		$data	= array();
		foreach ($list	as $k=>$v) {
			$data[$v['uuid']][$v['g_id']]['g_name']	= $v['g_name'];
			$data[$v['o_uuid']][$v['g_id']]['g_name']	= $v['g_name'];
			$data[$v['uuid']][$v['g_id']]['total']	+=1;
			$data[$v['o_uuid']][$v['g_id']]['total']	+=1;
			if ($v['result'] == 1) {
				$data[$v['uuid']][$v['g_id']]['win']	+=1;
			} elseif($v['result'] == 2) {
				$data[$v['o_uuid']][$v['g_id']]['win']	+=1;
			} else {
				$data[$v['uuid']][$v['g_id']]['win']	+=1;
				$data[$v['o_uuid']][$v['g_id']]['win']	+=1;
			}
		}

		// 插入data拼接
		$ist_data	= array();
		foreach ($data as $k=>$v) {
			foreach ($v as $key=>$val) {
				if ($val['win']) {
					$ist_data[]	= array(
						'uuid'		=> $k,
						'date'		=> date("Ymd",strtotime("-1 day")),
						'percent'	=> round(($val['win']/$val['total'])*100,1),
						'g_id'		=> $key,
						'g_name'	=> (string)$val['g_name'],
					);
				}
			}
		}
		if (!$ist_data) {
			return true;
		}

		// 胜率插入统计表
		$table2	= 'pmatchstatis';
		$ist	= $this->pmatch_model->insertBatch($ist_data,$table2);
		if (!$ist) {
			return false;
		}
		return true;
	}




}