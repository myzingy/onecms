<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticalNotesStat extends Model
{
    CONST TYEP_PL=0;    //对评论点赞
    CONST TYEP_HF=1;    //对回复点赞

    protected $table = 'artical_notes_stat';

    public $timestamps = false;

}
