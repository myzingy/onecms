<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Paylog extends Model
{
    const STATE_WZF=0;      //未支付
    const STATE_YZF=1;      //支付成功
    const STATE_YSB=2;      //支付失败
    const STATE_YTK=3;      //已退款
    const STATE_TSB=4;      //退款失败
    const STATE=[
        self::STATE_WZF=>'未支付',
        self::STATE_YZF=>'支付成功',
        self::STATE_YSB=>'支付失败',
        self::STATE_YTK=>'已退款',
        self::STATE_TSB=>'退款失败',
    ];

    const SVC_TYPE_QUE=0;  //提问
    const SVC_TYPE_SEE=1;  //查看

    protected $table = 'paylog';
    protected $primaryKey = 'payid';
    public $timestamps = false;

    public function question()
    {
        return $this->hasOne(Question::class,'qid','quesid');
    }
    public function expert()
    {
        return $this->hasOne(Expert::class,'expid','expid');
    }
    public static function getStateStr($state=self::STATE_WZF){
        if(empty(self::STATE[$state])){
            return self::STATE[self::STATE_WZF];
        }
        return self::STATE[$state];
    }

}
