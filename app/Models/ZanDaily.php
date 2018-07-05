<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ZanDaily extends Model
{
    protected $table = 'zan_daily';
    public function expert()
    {
        return $this->hasOne(Expert::class,'expid','vid');
    }
}
