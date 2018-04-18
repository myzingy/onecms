<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticalPaylog extends Model
{
    CONST STATE_WZF=0;  //未支付
    CONST STATE_YZF=1;  //已支付

    protected $table = 'artical_paylog';

    public $timestamps = false;

    public function mpuser(){
        return $this->hasOne(Mpuser::class,'opid_mp','opid');
    }
    public function artical(){
        return $this->belongsTo(Artical::class,'artid','id');
    }
    public function expert()
    {
        return $this->hasOne(Expert::class,'expid','expid');
    }
}
