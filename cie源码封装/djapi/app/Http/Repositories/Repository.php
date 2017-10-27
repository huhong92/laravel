<?php
/**
 * 仓库基础方法
 */
namespace App\Http\Repositories;

use App\Http\Contracts\RepositoryInterface;
use App\Http\Repositories\Contracts;
use Illuminate\Support\Facades\DB;
use Error_;
use ErrCode;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

abstract class Repository implements RepositoryInterface {
    protected $tz;
    protected $ip;

    public function __construct(Request $request)
    {
        $this->tz = date('Y-m-d H:i:s',time());
        $ip = $request->getClientIp();
        $this->ip = $ip&&($ip!='::1')?$ip:'127.0.0.1';
    }

    /**
     * stdclass转换成array类型
     * @param $stdclass
     */
    public function stdclassToArray($stdclass)
    {
        return json_decode(json_encode($stdclass),true);
    }
}