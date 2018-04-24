<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RedpackSetting extends Model
{
    protected $table = 'act_simple_redpack_setting';

    public $timestamps = false;
    protected $primaryKey = 'actid';
    public function expert()
    {
        return $this->hasOne(Expert::class,'expid','expid');
    }
    public function countReceiveNum(){//领取数量
        return $this->hasMany(Redpack::class,'actid','actid')
            ->count();
    }
    public function countReceiveFee(){//领取金额
        return $this->hasMany(Redpack::class,'actid','actid')
            ->sum('fee');
    }
}
