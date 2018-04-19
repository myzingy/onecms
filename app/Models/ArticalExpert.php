<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ArticalExpert extends Model
{
    const ENABLE_NO=0;  //默认禁用
    const ENABLE_YES=1; //启用
    const ENABLE=[
        self::ENABLE_NO=>'禁用',
        self::ENABLE_YES=>'启用',
    ];

    const TIPENABLE_NO=0;  //默认禁用
    const TIPENABLE_YES=1; //启用
    const TIPENABLE=[
        self::TIPENABLE_NO=>'禁用',
        self::TIPENABLE_YES=>'启用',
    ];
    protected $table = 'artical_expert';

    public $timestamps = false;
    protected $primaryKey = 'expid';

    public static function getStateStr($state=self::ENABLE_NO,$const='ENABLE'){
        eval("\$arr=self::$const;");
        if(empty($arr[$state])){
            return $arr[self::ENABLE_NO];
        }
        return $arr[$state];
    }

    public function countArtical(){//文章数
        return $this->hasMany(Artical::class,'expid','expid')
            ->count();
    }
    public function countViews(){//阅读量
        return $this->hasMany(ArticalStat::class,'expid','expid')
            ->where(['type'=>ArticalStat::TYEP_YD])
            ->count();
    }
    public function countFee(){//打赏收入
        return $this->hasMany(ArticalPaylog::class,'expid','expid')
            ->where(['state'=>ArticalPaylog::STATE_YZF])
            ->sum('fee');
    }
    public function expert()
    {
        return $this->hasOne(Expert::class,'expid','expid');
    }
}
