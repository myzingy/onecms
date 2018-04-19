<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticalDaily extends Model
{
    const TYPE_RZ=0;    //入账
    const TYPE_ZC=1;    //支出
    const TYPE=[
        self::TYPE_RZ=>'入账',
        self::TYPE_ZC=>'支出'
    ];
    protected $table = 'artical_daily';

    public $timestamps = false;

    public function expert()
    {
        return $this->hasOne(Expert::class,'expid','expid');
    }
    public static function getTypeStr($state=self::TYPE_ZC){
        if(empty(self::TYPE[$state])){
            return self::TYPE[self::TYPE_ZC];
        }
        return self::TYPE[$state];
    }
}
