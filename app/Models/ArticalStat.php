<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticalStat extends Model
{
    CONST TYEP_YD=0;    //阅读
    CONST TYEP_DZ=1;    //点赞

    protected $table = 'artical_stat';

    public $timestamps = false;

}
