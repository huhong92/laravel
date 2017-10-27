<?php

class Error_ {
	
	const ERR_OK = '0000';
    private $_error_code = null;
    private $_error_msg = null;

    function __construct($config = array()) {
        require_once 'Inc/Err.php';
        require_once 'Inc/ErrCode.php';
    }

    //返回调用成功返回的错误码
    function get_success() {
        return self::ERR_OK;
    }

    //返回错误码
    function get_error() {
        return $this->_error_code;
    }

    //设置错误码
    function set_error($error_code) {
        $argv = func_get_args();
        $this->_error_code = $error_code;
        $this->_error_msg = call_user_func_array(array($this, 'error_msg'), $argv);
    }
	

    //获取错误码对应的错误信息
    function error_msg($error_code = null) {
        $argv = func_get_args();
        if ($error_code === null) {
            return $this->_error_msg;
        }
        if (!isset(Err::$arErrCode[$error_code])) {
            $argv[0] = '未定义的错误码：[' . $error_code . ']';
        } else {
            $argv[0] = Err::$arErrCode[$error_code];
        }
        return $argv[0];
    }
	
}