<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LivebcApp extends Model
{
    const STATE_APPLY=0; //新申请
    const STATE_SUCCESS=1;   //已批准
    const STATE=[
        self::STATE_APPLY=>'新申请',
        self::STATE_SUCCESS=>'已批准',
    ];
    protected $table = 'livebc_app';

    public $timestamps = false;

    public function expert()
    {
        return $this->hasOne(Expert::class,'expid','expid');
    }
    public static function getStateStr($state=self::STATE_APPLY){
        if(empty(self::STATE[$state])){
            return self::STATE[self::STATE_APPLY];
        }
        return self::STATE[$state];
    }
}
