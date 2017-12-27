<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Daily extends Model
{
    const STATE_WJS=0;      //未结算
    const STATE_YJQ_AUTO=1; //已结清，自动
    const STATE_WJQ=2;      //未结清
    const STATE_YJQ_MANUAL=3;//已结清，手动
    const STATE=[
        self::STATE_WJS=>'未结算',
        self::STATE_YJQ_AUTO=>'已结清，自动',
        self::STATE_WJQ=>'未结清',
        self::STATE_YJQ_MANUAL=>'已结清，手动',
    ];
    protected $table = 'daily';
    public $timestamps = false;
    public function expert()
    {
        return $this->hasOne(Expert::class,'expid','expid');
    }
}
