<?php
namespace App\Http\Repositories\V0;

use App\Http\Repositories\Repository;
use App\Models\V0\PayModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Error_;
use ErrCode;

class PayRepository extends Repository {

    public $error_;
    public $pay_model;
    public function __construct(Request $request)
    {
        $this->error_ = app('Error_');
		$this->pay_model = new PayModel();
        parent::__construct($request);
    }




}