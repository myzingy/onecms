<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model
{
    use SoftDeletes;
    const STATE_WHD=0;
    const STATE_YHD=1;
    const STATE_YJJ=2;
    const STATE=[
        self::STATE_WHD=>'未回答',
        self::STATE_YHD=>'已回答',
        self::STATE_YJJ=>'已拒绝',
    ];

    const ISPUB_NO=0;   //不公开
    const ISPUB_YES=1;  //公开

    protected $table = 'question';

    public $timestamps = false;
    protected $primaryKey = 'qid';
    public function paylog()
    {
        //return $this->hasOne(Paylog::class,'quesid','qid');
        return $this->hasMany(Paylog::class,'quesid','qid');
    }
    public function expert()
    {
        return $this->hasOne(Expert::class,'expid','expid');
    }
    public static function getStateStr($state=self::STATE_WHD){
        if(empty(self::STATE[$state])){
            return self::STATE[self::STATE_WHD];
        }
        return self::STATE[$state];
    }
    public static function getIspubStr($state=self::ISPUB_NO){
        return self::ISPUB_YES==$state?'是':'否';
    }
}
