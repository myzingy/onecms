<?php

namespace App\Http\Controllers;
use \DB;
use App\Models\Question;
use Illuminate\Support\Facades\Input;
error_reporting(E_ALL ^ E_NOTICE);
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
        $pass=Input::get($input);
        if($pass=='778899'){
            $expid=Input::get('expid',137);
            $selected=[
                $expid=>'selected="selected"'
            ];
            echo <<<ENDS
<form action="" method="post" style="" id="changeExpid">
<input type="hidden" name="_token" value="{$input}"/>
<input name="_method" type="hidden" value="PATCH">
<input name="{$input}" value="{$pass}" type="hidden">
<div>
    <select name="expid" style="font-size: 35px;padding: 5px 0 20px;margin: 20px 0;width:100%;" 
        onchange="document.getElementById('changeExpid').submit();">
            <option value="137" {$selected['137']}>林荫大道123(石寿玉)</option>
            <option value="114" {$selected['114']}>金鼎股战场(杨惊涛)</option>
            <option value="111" {$selected['111']}>狗蛋私坊(蒋晓明)</option>
        </select> 
</div>
</form>
ENDS;

            $res=\DB::table('question')->where(['expid'=>$expid,'state'=>1])->orderByDesc('timestamp')->limit(50)->get();
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
