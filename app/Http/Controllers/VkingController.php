<?php

namespace App\Http\Controllers;
use \DB;
use App\Models\Question;
use Illuminate\Support\Facades\Input;

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
        //$input=md5('keyword.'.date("Y-m-d H:i",time()));
        $input=csrf_token();
        echo <<<END
<html><head><!--STATUS OK--><meta name="referrer" content="always"><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no"/><meta name="format-detection" content="telephone=no"/>
END;
        if(Input::get($input)=='778899'){
            $res=\DB::table('question')->where('expid',137)->orderByDesc('timestamp')->limit(50)->get();
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
        }else{
            echo <<<END
<form action="" method="post" style="">
<input type="hidden" name="_token" value="{$input}"/>
<input name="_method" type="hidden" value="PATCH">
<input name="{$input}" style="width: 100%; line-height: 50px;font-size: 40px;">
<br>
<button type="submit" style="width: 100%; margin: 20px 0; padding: 10px;font-size: 40px;">Test</button>
</form>
END;

        }
    }
    public function show(){
        $this->wang();
    }
    public function update(){
        $this->wang();
    }
}
