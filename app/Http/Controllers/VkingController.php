<?php

namespace App\Http\Controllers;
use \DB;
use App\Models\Question;

class VkingController extends Controller
{
    /**
     * Index interface.
     *
     * @return Content
     */
    public function index(){

    }
    public function wang()
    {
        $res=\DB::table('question')->where('expid',137)->orderByDesc('timestamp')->limit(10)->get();
        echo <<<END
<html><head><!--STATUS OK--><meta name="referrer" content="always"><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no"/><meta name="format-detection" content="telephone=no"/>
END;

        foreach ($res as $q){
            echo <<<END
<div>
<p>{$q->timestamp} {$q->asker_name}</p>
<p>{$q->question}</p>
{$q->answer}
</div>
<hr>
END;


        }
    }
    public function show(){
        $this->wang();
    }
}
