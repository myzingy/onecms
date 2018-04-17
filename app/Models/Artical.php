<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Artical extends Model
{
    protected $table = 'artical';

    public $timestamps = false;

    public function countNotes(){//评论数
        return $this->hasMany(ArticalNotes::class,'artid','id')
            ->count();
    }
    public function countViews(){//阅读数
        return $this->hasMany(ArticalStat::class,'artid','id')
            ->where('type='.ArticalStat::TYEP_YD)
            ->count();
    }
    public function countStats(){//点赞数
        return $this->hasMany(ArticalStat::class,'artid','id')
            ->where('type='.ArticalStat::TYEP_DZ)
            ->count();
    }
}
