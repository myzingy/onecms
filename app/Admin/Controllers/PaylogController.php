<?php

namespace App\Admin\Controllers;

use App\Models\Auth;
use App\Models\Paylog;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Support\Facades\Input;

class PaylogController extends Controller
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

            $content->header('逐笔明细');
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
            $m=$this->form()->edit($id)->model();
            if($m->state!=Paylog::STATE_YZF) throw new \Exception('未支付或已退款');
            $m->state=Paylog::STATE_YTK;
            $m->save();
            $url='http://dv.cnfol.com/refund/refund?tradeno='
            .$m->trade_no.'&fee='
                .$m->fee.'&code='
                .md5($m->trade_no.$m->fee.'OJjjfdfjsdfsdfji@!&*@^*&^^jjjfdsfjds');
            \Log::info('refund-url:'.$url);
            $res=$this->curl_get_contents($url,30);
            \Log::info('refund-res:'.$res);
            if($res!='OK'){
                $m->state=Paylog::STATE_TSB;
                $m->save();
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
        return Admin::grid(Paylog::class, function (Grid $grid) {
            if(Admin::user()->isRole('lecturer')){
                $grid->model()->where('expid', '=', Admin::user()->id);
            }
            $grid->model()->where('state','<>',Paylog::STATE_WZF);

            $grid->model()->with(['question','expert','mpuser']);
            $grid->model()->orderBy('payid', 'desc');
            $grid->payid('ID')->sortable();
            $grid->openid('OPENID');
            $grid->column('mpuser.nickname','提问者昵称');
            $grid->column('question.asker_name','提问者姓名');
            $grid->column('question.question','问题')->display(function ($que){
                return '<div style="max-width:500px;">'.$que.'</div>';
            });
            $grid->column('expert.real_name','讲师姓名');
            $grid->fee('金额')->display(function ($fee) {
                return $fee/100;
            });
            $grid->svc_type('类型')->display(function ($svc_type) {
                return $svc_type==Paylog::SVC_TYPE_SEE?'查 看':'提 问';
            });
            $grid->timestamp('时间');
            $grid->column('state','支付状态')->display(function ($state) {
                return Paylog::getStateStr($state);
            });
            if(Auth::isAdministrator()){
                $grid->column('state_x','退款')->refund();
            }
            $grid->disableRowSelector();
            //disableExport
            $grid->disableExport();
            //disableCreation
            $grid->disableCreation();
            $grid->disableActions();
            // filter($callback)方法用来设置表格的简单搜索框
            $grid->filter(function($filter){
                // 如果过滤器太多，可以使用弹出模态框来显示过滤器.
                $filter->useModal();
                // 禁用id查询框
                $filter->disableIdFilter();
                //$filter->equal('timestamp', '时间')->date();
                $filter->between('timestamp', '时间')->datetime();
                // 关系查询，查询对应关系`profile`的字段
//                $filter->where(function ($query) {
//                    $input = $this->input;
//                    $query->whereHas('expert', function ($query) use ($input) {
//                        $query->where('real_name', 'like', "%{$input}%");
//                    });
//                }, '讲师姓名');
                $filter->like('expert.real_name', '讲师姓名');
//                $filter->where(function ($query) {
//                    $input = $this->input;
//                    $query->whereHas('question', function ($query) use ($input) {
//                        $query->where('asker_name', 'like', "%{$input}%");
//                    });
//                }, '提问者姓名');
//                $filter->where(function ($query) {
//                    $input = $this->input;
//                    $query->whereHas('mpuser', function ($query) use ($input) {
//                        $query->where('nickname', 'like', "%{$input}%");
//                    });
//                }, '提问者昵称');
                $filter->like('mpuser.nickname', '提问者昵称');
                $filter->in('state', '支付状态')->checkbox(Paylog::STATE);

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
        return Admin::form(Paylog::class, function (Form $form) {

            $form->display('id', 'ID');

            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }
    function curl_get_contents($url,$timeout=1) {
        $curlHandle = curl_init();
        curl_setopt( $curlHandle , CURLOPT_URL, $url );
        curl_setopt( $curlHandle , CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt( $curlHandle , CURLOPT_TIMEOUT, $timeout );
        $result = curl_exec( $curlHandle );
        curl_close( $curlHandle );
        return $result;
    }
}
