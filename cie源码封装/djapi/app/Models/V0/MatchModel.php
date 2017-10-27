<?php

namespace App\Models\V0;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\BaseModel;


class MatchModel extends BaseModel
{
    public function __construct(array $attributes = [])
	{
		parent::__construct($attributes);
	}



}
