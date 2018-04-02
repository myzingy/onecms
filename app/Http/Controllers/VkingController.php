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
            $experts=[
                '137'=>[
                    'expid'=>137,
                    'name'=>'林荫大道123(石寿玉)',
                    'asker_name'=>'林荫大道',
                ],
                '114'=>[
                    'expid'=>114,
                    'name'=>'金鼎股战场(杨惊涛)',
                    'asker_name'=>'金鼎',
                ],
                '111'=>[
                    'expid'=>111,
                    'name'=>'狗蛋私坊(蒋晓明)',
                    'asker_name'=>'狗蛋',
                ]

            ];
            $expid=Input::get('expid',137);
            $experts[$expid]['selected']='selected="selected"';
            echo <<<ENDS
<form action="" method="post" style="" id="changeExpid">
<input type="hidden" name="_token" value="{$input}"/>
<input name="_method" type="hidden" value="PATCH">
<input name="{$input}" value="{$pass}" type="hidden">
<div>
    <select name="expid" style="font-size: 35px;padding: 5px 0 20px;margin: 20px 0;width:100%;" 
        onchange="document.getElementById('changeExpid').submit();">
ENDS;
            foreach ($experts as $ex){
                echo "<option value=\"{$ex['expid']}\" {$ex['selected']}>{$ex['name']}</option>";
            }
            echo <<<ENDS
        </select> 
</div>
</form>
ENDS;

            $res=\DB::table('question')->where([
                'expid'=>$expid,
                'state'=>1,
                'asker_name'=>$experts[$expid]['asker_name']
            ])->orderByDesc('timestamp')->limit(50)->get();
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
