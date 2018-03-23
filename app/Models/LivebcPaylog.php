<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LivebcPaylog extends Model
{
    const STATE_WZF=0;      //未支付
    const STATE_YZF=1;      //已支付
    const STATE_YTK=2;      //已退款
    const STATE_YWC=3;      //订单完成
    //const STATE_YSB=4;      //支付失败

    const STATE=[
        self::STATE_WZF=>'未支付',
        self::STATE_YZF=>'支付成功',
        self::STATE_YTK=>'已退款',
        self::STATE_YWC=>'订单完成',
    ];
    protected $table = 'livebc_paylog';

    protected $primaryKey = 'trade_no';
    public $incrementing = false; //非自增

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
