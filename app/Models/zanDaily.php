<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class zanDaily extends Model
{
    protected $table = 'zan_daily';
    public function expert()
    {
        return $this->hasOne(Expert::class,'expid','vid');
    }
}
