<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tipsetting extends Model
{
    const TYPE_MONEY=0;  //钱
    const TYPE_GIFT=1; //礼物
    const TYPE=[
        self::TYPE_MONEY=>'钱',
        self::TYPE_GIFT=>'礼物',
    ];

    const STATE_NO=0;  //禁用
    const STATE_YES=1; //启用
    const STATE=[
        self::STATE_NO=>'禁用',
        self::STATE_YES=>'启用',
    ];

    protected $table = 'tipsetting';

    protected $primaryKey = 'expid';

    public $timestamps = false;
    public function expert()
    {
        return $this->hasOne(Expert::class,'expid','expid');
    }
    public static function getStateStr($state=self::STATE_NO,$const='STATE'){
        eval("\$arr=self::$const;");
        if(empty($arr[$state])){
            return $arr[self::STATE_NO];
        }
        return $arr[$state];
    }
}
