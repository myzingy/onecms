<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticalNotes extends Model
{
    const STATE_NO=0;  //禁用
    const STATE_YES=1; //启用
    const STATE=[
        self::STATE_NO=>'隐藏',
        self::STATE_YES=>'显示',
    ];

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
    public static function getStateStr($state=self::STATE_NO,$const='STATE'){
        eval("\$arr=self::$const;");
        if(empty($arr[$state])){
            return $arr[self::STATE_NO];
        }
        return $arr[$state];
    }
}
