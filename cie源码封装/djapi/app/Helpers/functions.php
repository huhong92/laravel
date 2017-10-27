<?php
/**
 * 全局公共方法[驼峰命名法,首字母小写]
 * @date 2017-07024
 * @author huhong
 */

/**
 * 签名认证
 * $params   array 生成签名的数组        必须
 * $sign     str   客户端发送的对照签名  必须
 * $platform str   生成指定游戏平台的签名
 */
function checkSign($params, $sign) {
    if($sign == "" || !$params){
        return false;
    }
    $new_sign = getSign($params);
    if(getenv('APP_ENV') != "local"){
        if($sign != $new_sign){
			writeLog('sign_err:签名错误','sign:'.$sign.';get_sign:'.$new_sign);
            return false;
        }
    }
    return true;
}

/**
 * 获取sign签名
 * @param array $params 参数
 * @return string
 */
 function getSign($params)
{
    //除去数组中的空值和签名参数
    while (list($key, $val) = each($params)) {
        if ($key == "sign" || $val === "" || $val === null || $key == 'sign_key') {
            continue;
        } else {
            $para[$key] = $params[$key];
        }
    }

    //对数组进行字母排序
    ksort($para);
    reset($para);
    $arg    = '';
    while (list($key, $val) = each($para)) {
        $arg .= $key . "=" . $val . "&";
    }

    $sign_key =   config('passport.sign_key');
    $arg .= "key=" . $sign_key;
    $new_sign = md5($arg);
    return $new_sign;
}

/**
 * 定义日志格式
 * @param $info 日志描述【eg:insert_user_fail:用户信息插入失败】
 * @param $params 接口参数
 * @param array $do_data 执行数据【insert、update】
 * @param array $return_data 返回数据
 * @return string  返回日志信息
 */
function writeLog($info,$params,$do_data = array(),$return_data = array())
{
    if (is_array($params)) {
        $params = json_encode($params);
    }
    $info .= ';params-'.$params;
    if($do_data) {
		$info .=$do_data[0].':'.json_encode($do_data[1]);
    }
    if ($return_data) {
        $info .= 'return_data:'.json_encode($return_data);
    }
    Log::error($info.';time-'.date('Y-m-d H:i:s',time()));
    return true;
}


/**
 * 上传图片
 * @param $file input名称
 * @param string $upload_path 上传路径
 * @param int 允许上传文件大小（单位M）
 * @param array $ext 允许上传后缀名
 * @param array $mime 允许上传mime类型
 * @return bool|string 返回上传路径
 */
function uploadPicture($file,$upload_path = '',$size = 0,$ext = array(),$mime = array())
{
    // 检验一下上传的文件是否有效.
    if ($file->isValid()) {
        // 缓存在tmp文件夹中的文件名 例如 php8933.tmp 这种类型的.
        $clientName = $file->getClientOriginalName();
        // 文件后缀校验
        if ($ext) {
			$extension = $file->getClientOriginalExtension();
            if (!in_array(strtolower($extension), $ext)) {
                writeLog('upload_image_fail:文件上传失败', $file . "不允许该后缀名");
                return false;
            }
        }

        // mimeType校验
        if ($mime) {
			$mimeType = $file->getMimeType();
            if (!in_array(strtolower($mimeType), $mime)) {
                writeLog('upload_image_fail:文件上传失败', $file . "不允许该MimeType");
                return false;
            }
        }

        // 上传大小校验
		if ($size) {
			if ($file->getSize() > ($size*1024*1024)) {
				writeLog('upload_image_fail:文件上传失败', $file . "上传文件超过".$size."M");
				return false;
			}
		}

		// 设置上传路径为 public/uploads
		$path = 'img/uploads/';
        if ($upload_path) {
            $upload_path = $path.$upload_path;
        } else {
			$upload_path = $path;
		}
        // 判断目录是否存在
        if (!is_dir($upload_path)) {
            @mkdir($upload_path, 0777);
        }

        // 移动文件
        $newName = date("YmdHis") . "_" .rand(1,9999).rand(1,9999);
        $path = $file->move($upload_path, $newName);
        return $upload_path . '/' . $newName;
    }

    writeLog('upload_image_fail:文件上传失败', $file . "文件不合法");
    return false;
}

/**
 * 发送验证码
 * @param string $mobile 发送手机号[多个同时发送，隔开]
 * @param string $code   发送验证码
 * @return bool
 */
function sendSMS($mobile = '15900000000',$code = '8888')
{
	$appid 	= config('passport.sms_appid');
	$appkey = config('passport.sms_appkey');
	$min	= config('passport.sms_expire');
	$singleSender = new SmsSingleSender($appid, $appkey);

	// 指定模板单发
	// 假设模板内容为：{1}为您的登录验证码，请于{2}分钟内填写。如非本人操作，请忽略本短信。
	$nationCode	= '86';
	$templId	= '32922';
	$params		= array($code,$min);
	$result_json= $singleSender->sendWithParam($nationCode, $mobile,$templId, $params);
	$result 	= json_decode($result_json,true);
	if($result['result']=='0') {
		return true;
	} else {
		writeLog('验证码发送失败',$result['errmsg']);
		return false;
	}
}

/**
 * 获取手机号验证码
 * @param $length 长度
 */
function getMobileCode($length,$type = null)
{
    switch ($type) {
        case 'reduced':
            $chars = array(
                "a", "c", "d", "e", "f", "g", "h", "i", "j", "k",
                "m", "n", "p", "r", "s", "t", "u", "v",
                "w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G",
                "H", "J", "K", "L", "M", "N", "P", "Q", "R",
                "S", "T", "U", "V", "W", "X", "Y", "Z", "2",
                "3", "4", "5", "7", "8",
            );
            break;
        case 'numeric':
            $chars = array("0", "1", "2", "3", "4", "5", "6", "7", "8", "9");
            break;
        default:
            $chars = array(
                "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k",
                "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v",
                "w", "x", "y", "z", "A", "B", "C", "D", "E", "F", "G",
                "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R",
                "S", "T", "U", "V", "W", "X", "Y", "Z", "0", "1", "2",
                "3", "4", "5", "6", "7", "8", "9",
            );
            break;
    }
    $chars_len = count($chars) - 1;
    shuffle($chars);
    $output = "";
    for ($i = 0; $i < $length; $i++) {
        $output .= $chars[mt_rand(0, $chars_len)];
    }
    return $output;
}

/**
 * 验证手机号
 * @param $mobile 手机号
 */
function isMobile($mobile)
{
    if (substr($mobile,0,1) == 1) {
        return true;
    }
    return false;
}

/**
 * 外部请求-post
 * @param $url 请求url
 * @param $post_data  post数据
 */
function curlPost($url,$post_data)
{
    if (is_array($post_data)) {
        $qry_str = http_build_query($post_data);
    } else {
        $qry_str = $post_data;
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, '15');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $qry_str);

    $content = trim(curl_exec($ch));
    curl_close($ch);
    return $content;
}

/**
 * 外部请求-get
 * @param $url 请求url
 * @param $post_data  get数据
 */
function curlGet($url, $fields = array())
{
    if (is_array($fields)) {
        $qry_str = http_build_query($fields);
    } else {
        $qry_str = $fields;
    }
    if (trim($qry_str) != '') {
        $url = $url . '?' . $qry_str;
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, '100');
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    $content = trim(curl_exec($ch));
    curl_close($ch);
    return $content;
}








