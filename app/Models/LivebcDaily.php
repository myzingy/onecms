<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LivebcDaily extends Model
{
    const TYPE_RZ=0;      //入账
    const TYPE_TX=1;      //提现
    const TYPE=[
        self::TYPE_RZ=>'入账',
        self::TYPE_TX=>'提现',
    ];
    protected $table = 'livebc_daily';

    public $timestamps = false;
    public function expert()
    {
        return $this->hasOne(Expert::class,'expid','expid');
    }
    public static function getTypeStr($state=self::TYPE_RZ){
        if(empty(self::TYPE[$state])){
            return self::TYPE[self::TYPE_RZ];
        }
        return self::TYPE[$state];
    }
}
