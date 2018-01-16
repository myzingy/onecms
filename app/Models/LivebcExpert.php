<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LivebcExpert extends Model
{
    const STATE_DISABLED=0; //禁用
    const STATE_ENABLE=1;   //启用
    const STATE=[
        self::STATE_DISABLED=>'未直播',
        self::STATE_ENABLE=>'直播中',
    ];
    protected $table = 'livebc_expert';

    public $timestamps = false;
    protected $primaryKey = 'expid';
    protected $fillable = ['expid','name','notice','fee_bc','state'];
    public function expert()
    {
        return $this->hasOne(Expert::class,'expid','expid');
    }
    public static function getStateStr($state=self::STATE_DISABLED){
        if(empty(self::STATE[$state])){
            return self::STATE[self::STATE_DISABLED];
        }
        return self::STATE[$state];
    }
}
