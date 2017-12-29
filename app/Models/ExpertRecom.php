<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpertRecom extends Model
{
    protected $table = 'expert_recom';

    protected $primaryKey = 'expid';

    public $timestamps = false;

    public function expert()
    {
        return $this->belongsTo(Expert::class,'expid','expid');
    }

}
