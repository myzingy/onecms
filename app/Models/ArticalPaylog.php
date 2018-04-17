<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticalPaylog extends Model
{
    CONST STATE_WZF=0;  //未支付
    CONST STATE_YZF=1;  //已支付

    protected $table = 'artical_paylog';

    public $timestamps = false;

}
