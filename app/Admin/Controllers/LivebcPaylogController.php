<?php

namespace App\Admin\Controllers;

use App\Models\Auth;
use App\Models\LivebcPaylog;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Support\Facades\Input;

class LivebcPaylogController extends Controller
{
    use ModelForm;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('直播订阅-订单管理');
            $content->description('');

            $content->body($this->grid());
        });
    }

    /**
     * Edit interface.
     *
     * @param $id
     * @return Content
     */
    public function edit($id)
    {
        $act=Input::get('act','');
        //refuse
        if($act=='refund'){//退款
            $fee=Input::get('fee','');
            $m=$this->form()->edit($id)->model();
            if($m->state!=LivebcPaylog::STATE_YZF) throw new \Exception('未支付或已退款');
            if($fee<1 || $fee>$m->fee){
                throw new \Exception('退款金额异常，请重新填写');
            }
            $trade_no=$m->trade_no;//.'_test';
            $url='http://dv.cnfol.com/lvbc/refund?tradeno='
                .$trade_no.'&fee='
                .$fee.'&code='
                .md5($trade_no.$fee.'OJjjfdfjsdfsdfji@!&*@^*&^^jjjfdsfjds');
            \Log::info('lvbc-refund-url:'.$url);
            $res=$this->curl_get_contents($url,30);
            \Log::info('lvbc-refund-res:'.$res);
            if($res=='OK'){
                $m->state=LivebcPaylog::STATE_YTK;
                $m->refund_fee=$fee*100;
                $m->save();
            }else{
                //$m->state=LivebcPaylog::STATE_TSB;
                //$m->save();
                throw new \Exception('退款失败');
            }
            return $m;
        }
        return Admin::content(function (Content $content) use ($id) {

            $content->header('header');
            $content->description('description');

            $content->body($this->form()->edit($id));
        });
    }

    /**
     * Create interface.
     *
     * @return Content
     */
    public function create()
    {
        return Admin::content(function (Content $content) {

            $content->header('header');
            $content->description('description');

            $content->body($this->form());
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(LivebcPaylog::class, function (Grid $grid) {

            $grid->model()->orderBy('timestamp', 'desc');
            if(Auth::isLecturer()){
                $grid->model()->where(['expid'=>Admin::user()->id]);
            }
            $grid->model()->where('state','<>',LivebcPaylog::STATE_WZF);
            $grid->trade_no('订单号');
            $grid->openid('OPENID');
            $grid->column('mpuser.nickname','订阅者昵称');
            $grid->column('expert.real_name','讲师姓名');
            $grid->fee('金额')->display(function ($fee) {
                return $fee/100;
            });
            $grid->timestamp('订阅时间');
            $grid->days('订阅天数');
            $grid->refund_fee('已退金额')->display(function ($fee) {
                return $fee/100;
            });

            $grid->column('state','支付状态')->display(function ($state) {
                return LivebcPaylog::getStateStr($state);
            });
            if(Auth::isAdministrator()){
                $grid->column('state_x','退款')->refundValue();
            }
            $grid->disableRowSelector();
            //disableExport
            $grid->disableExport();
            //disableCreation
            $grid->disableCreation();
            $grid->disableActions();
            $grid->filter(function($filter){
                // 如果过滤器太多，可以使用弹出模态框来显示过滤器.
                $filter->useModal();
                // 禁用id查询框
                $filter->disableIdFilter();
                $filter->between('timestamp', '下单时间')->datetime();
                $filter->like('mpuser.nickname', '订阅者昵称');
                $filter->in('state', '支付状态')->checkbox([
                    LivebcPaylog::STATE_YZF=>'支付成功',
                    LivebcPaylog::STATE_YTK=>'已退款',
                    LivebcPaylog::STATE_YWC=>'订单完成',
                ]);

            });
            $grid->footer(function(){
                if(Auth::isAdministrator()){
                    echo view('admin.grid.total', ['total' => '[4,7]']);
                }else{
                    echo view('admin.grid.total', ['total' => '[4,7]']);
                }
            });
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(LivebcPaylog::class, function (Form $form) {

            $form->display('id', 'ID');

            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }
    function curl_get_contents($url,$timeout=1) {
        list($url,$post_data)=explode('?',$url);
        //parse_str($urlarr['query'],$post_data);
        $curlHandle = curl_init();
        curl_setopt( $curlHandle , CURLOPT_URL, $url );
        curl_setopt( $curlHandle , CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt( $curlHandle , CURLOPT_TIMEOUT, $timeout );
        curl_setopt( $curlHandle, CURLOPT_POST, 1);
        curl_setopt( $curlHandle, CURLOPT_POSTFIELDS, $post_data);
        $result = curl_exec( $curlHandle );
        curl_close( $curlHandle );
        return $result;
    }
}
