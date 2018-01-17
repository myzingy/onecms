<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Livebc extends Model
{
    const TAG_PMZS=0;  //盘面综述
    const TAG_ZPDP=1;  //早盘点评
    const TAG_WPDP=2;  //午间点评
    const TAG_SPDP=3;  //收盘点评
    const TAG_ZDGZ=4;  //重点关注
    const TAG_RDBK=5;  //热点板块
    const TAG_TFSJ=6;  //突发事件
    const TAG_DXTS=7;  //短线提示
    const TAG=[
        '盘面综述','早盘点评','午间点评','收盘点评','重点关注','热点板块','突发事件','短线提示'
    ];
    protected $table = 'livebc';

    public $timestamps = false;
    public function expert()
    {
        return $this->hasOne(Expert::class,'expid','expid');
    }
    public static function getTagStr($tag=self::TAG_PMZS){
        if(empty(self::TAG[$tag])){
            return self::TAG[self::TAG_PMZS];
        }
        return self::TAG[$tag];
    }
}
