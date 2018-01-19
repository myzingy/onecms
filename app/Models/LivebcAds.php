<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LivebcAds extends Model
{
    protected $table = 'livebc_ads';

    public $timestamps = false;
    public function expert()
    {
        return $this->hasOne(Expert::class,'expid','expid');
    }
}
