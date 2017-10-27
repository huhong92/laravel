<?php

namespace App\Http\Controllers\V0;

use Validator;
use App\Http\Controllers\Controller;
use App\Http\Repositories\V0\PayRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;
use ErrCode;

/**
 * Class PayController 支付模块
 * @package App\Http\Controllers\v0
 * @author huhong
 * @date 2017-08-22 17:56:00
 */
class PayController extends Controller
{
    protected $pay_repo;
    public function __construct(PayRepository $pay_repo)
    {
        parent::__construct(false);
		$this->pay_repo	= $pay_repo;
		$this->error_   	= $pay_repo->error_;
    }


}
