<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Redpack extends Model
{
    protected $table = 'act_simple_redpack';

    public $timestamps = false;
    public function expert()
    {
        return $this->hasOne(Expert::class,'expid','expid');
    }
    public function mpuser(){
        return $this->hasOne(Mpuser::class,'opid_mp','openid');
    }
    public function redpack(){
        return $this->hasOne(RedpackSetting::class,'actid','actid');
    }
}
