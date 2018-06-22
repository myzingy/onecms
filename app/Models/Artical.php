<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Artical extends Model
{
    const ENABLETIPS_NO=0;  //禁用
    const ENABLETIPS_YES=1; //启用
    const ENABLETIPS=[
        self::ENABLETIPS_NO=>'禁用',
        self::ENABLETIPS_YES=>'启用',
    ];

    protected $table = 'artical';

    public $timestamps = false;

    public function countNotes(){//评论数
        return $this->hasMany(ArticalNotes::class,'artid','id')
            ->count();
    }
    public function countViews(){//阅读数
        return $this->hasMany(ArticalStat::class,'artid','id')
            ->where(['tp'=>ArticalStat::TYEP_YD])
            ->count();
    }
    public function countStats(){//点赞数
        return $this->hasMany(ArticalStat::class,'artid','id')
            ->where(['tp'=>ArticalStat::TYEP_DZ])
            ->count();
    }
    public function countFee(){//打赏收入
        return $this->hasMany(ArticalPaylog::class,'artid','id')
            ->where(['state'=>ArticalPaylog::STATE_YZF])
            ->sum('fee');
    }
    public function expert()
    {
        return $this->hasOne(Expert::class,'expid','expid');
    }

    public static function getStateStr($state=self::ENABLETIPS_NO,$const='ENABLETIPS'){
        eval("\$arr=self::$const;");
        if(empty($arr[$state])){
            return $arr[self::ENABLETIPS_NO];
        }
        return $arr[$state];
    }
}
