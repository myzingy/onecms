<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LivebcCourse extends Model
{
    const STATE_DISABLE=0; //禁用
    const STATE_ENABLE=1;   //启用
    const STATE=[
        self::STATE_DISABLE=>'已禁用',
        self::STATE_ENABLE=>'启用中',
    ];
    protected $table = 'livebc_course';

    public $timestamps = false;

    public static function getStateStr($state=self::STATE_DISABLE){
        if(empty(self::STATE[$state])){
            return self::STATE[self::STATE_DISABLE];
        }
        return self::STATE[$state];
    }
    public function expert()
    {
        return $this->hasOne(Expert::class,'expid','expid');
    }

}
