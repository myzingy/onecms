<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LivebcSubs extends Model
{
    const STATE_FF=1;      //付费
    const STATE_TY=2;      //体验
    const STATE=[
        self::STATE_FF=>'付费',
        self::STATE_TY=>'体验',
    ];
    protected $table = 'livebc_subs';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = ['expid','openid','expires','timestamp','state'];
    public function expert()
    {
        return $this->hasOne(Expert::class,'expid','expid');
    }
    public function mpuser()
    {
        return $this->hasOne(Mpuser::class,'opid_mp','openid');
    }
    public function extend()
    {
        return $this->hasOne(LivebcSubsExtend::class,'sub_id','id');
    }
    public static function getStateStr($state=self::STATE_WZF){
        if(empty(self::STATE[$state])){
            return self::STATE[self::STATE_WZF];
        }
        return self::STATE[$state];
    }
}
