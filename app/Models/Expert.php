<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Expert extends Model
{
    protected $table = 'expert';
    protected $primaryKey = 'expid';
    public $timestamps = false;
    use SoftDeletes;

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
        try{
            $user_id = DB::table('admin_users')->insertGetId(
                [
                    'username' => $info['mobile'],
                    'name' => $info['real_name'],
                    'password' => bcrypt('123456'),
                    'created_at'=>date('Y-m-d H:i:s',time()),
                    'updated_at'=>date('Y-m-d H:i:s',time()),
                ]);
            $res = $user_id && DB::table('admin_role_users')->insert(
                    [
                        'role_id' => 4,//讲师
                        'user_id' => $user_id
                    ]);
            $data = array_intersect_key($info,array_flip(['real_name', 'mobile', 'qq', 'mp_name', 'mp_img_url', 'wx_id', 'wx_name',
                'wx_img_url', 'wx_qrcode', 'openid', 'unionid']));
            $data = array_merge($data, [
                'expid' => $user_id,
                'state' => Expert::ENABLE
            ]);
            DB::table('expert')->insert($data);

            DB::table('tipsetting')->insert([
                'expid' => $user_id,
                'type'=>0,
                'state'=>0,
            ]);
            DB::commit();
        }catch (\Exception $e){
            //ExpertApplication::where('apid', '=', $form->apid)->update(['state' => ExpertApplication::STATE_IN_CHECK]);//还原
            DB::rollBack();
            //throw new \Error($e->getMessage());
        }
    }
}
