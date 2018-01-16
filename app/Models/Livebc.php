<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Livebc extends Model
{
    protected $table = 'livebc';

    public $timestamps = false;
    public function expert()
    {
        return $this->hasOne(Expert::class,'expid','expid');
    }
}
