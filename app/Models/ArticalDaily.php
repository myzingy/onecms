<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticalDaily extends Model
{
    const TYPE_RZ=0;    //入账
    const TYPE_CZ=1;    //支出

    protected $table = 'artical_daily';

    public $timestamps = false;

}
