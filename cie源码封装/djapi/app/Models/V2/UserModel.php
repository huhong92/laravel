<?php

namespace App\Models\V2;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\BaseModel;


/**
 * Class UserModel 用户模块
 * @package App\Models\v2
 */
class UserModel extends BaseModel
{
    public function __construct()
	{
		parent::__construct();
	}


}
