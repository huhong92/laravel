<?php

namespace App\Models\V0;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\BaseModel;


/**
 * Class UserModel 支付模块
 * @package App\Models\v0
 */
class PayModel extends BaseModel
{
    public function __construct()
	{
		parent::__construct();
	}


}
