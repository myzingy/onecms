<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LivebcSubs extends Model
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
    protected $table = 'livebc_subs';

    public $timestamps = false;
    public function expert()
    {
        return $this->hasOne(Expert::class,'expid','expid');
    }
    public function mpuser()
    {
        return $this->hasOne(Mpuser::class,'opid_mp','openid');
    }
    public static function getStateStr($state=self::STATE_WZF){
        if(empty(self::STATE[$state])){
            return self::STATE[self::STATE_WZF];
        }
        return self::STATE[$state];
    }
}
