<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticalNotes extends Model
{
    protected $table = 'artical_notes';

    public $timestamps = false;

    public function statNote(){//对评论点赞
        return $this->hasMany(ArticalNotesStat::class,'rpid','id')
            ->where(['type'=>ArticalNotesStat::TYEP_PL])
            ->count();
    }
    public function statReply(){//对回复点赞
        return $this->hasMany(ArticalNotesStat::class,'rpid','id')
            ->where(['type'=>ArticalNotesStat::TYEP_HF])
            ->count();
    }
    public function mpuser(){
        return $this->hasOne(Mpuser::class,'opid_mp','opid');
    }
    public function artical(){
        return $this->belongsTo(Artical::class,'artid','id');
    }
}
