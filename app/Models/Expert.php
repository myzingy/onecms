<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Expert extends Model
{
    protected $table = 'expert';
    protected $primaryKey = 'expid';
    public $timestamps = false;

    const ENABLE = 1;
    const DISABLE = 0;

    const SVC_TYPE_QUESTION = 0;//问答
    const SVC_TYPE_SUBSCRIBE = 1;//订阅

    public static $stateOptions = [
        Expert::ENABLE=>'启用',
        Expert::DISABLE=>'禁用'];

    public static $enableOptions = [
        Expert::ENABLE=>'是',
        Expert::DISABLE=>'否',];

    public static $svcTypeOptions = [
        Expert::SVC_TYPE_QUESTION=>'问答',
        Expert::SVC_TYPE_SUBSCRIBE=>'订阅'];

    public function recom()
    {
        return $this->hasOne(ExpertRecom::class,'expid','expid');
    }


    public function addToExpert($form){
        $info = ExpertApplication::find($form->apid)->toArray();
        if($info['state'] != ExpertApplication::STATE_PASS) return;
        DB::beginTransaction();
        $user_id = DB::table('admin_users')->insertGetId(
            [
                'username' => $info['mobile'],
                'name' => $info['real_name'],
                'password' => bcrypt('123456'),
            ]);
        $res = $user_id && DB::table('admin_role_users')->insert(
                [
                    'role_id' => 4,//讲师
                    'user_id' => $user_id
                ]);
        $data = array_intersect_key($info,array_flip(['real_name', 'mobile', 'qq', 'mp_name', 'mp_img_url', 'wx_id', 'wx_name',
            'wx_img_url', 'wx_qrcode', 'openid', 'unionid', 'mp_auth', 'svc_type', 'mp_appid', 'mp_secret',
            'mp_qrcode', 'mp_verify_file_url', 'cfaid']));
        $data = array_merge($data, [
            'expid' => $user_id,
            'state' => Expert::ENABLE,
            'share_ratio' => 0.6,
            'price_ask' => 100,
            'price_see' => 88,
            'max_question' => 0,
            'entry_url' => '/expert/entry/'. $user_id
        ]);
        $res = $res && DB::table('expert')->insert($data);
        if($res){
            DB::commit();
        }else{
            if($info['state'] != ExpertApplication::STATE_PASS) return;
            ExpertApplication::where('apid', '=', $form->apid)->update(['status' => ExpertApplication::STATE_IN_CHECK]);//还原
            DB::rollBack();
        }
    }
}
