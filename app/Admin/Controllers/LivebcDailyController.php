<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\Confirm;
use App\Admin\Extensions\RefundValue;
use App\Admin\Extensions\Takenow;
use App\Models\Auth;
use App\Models\Expert;
use App\Models\LivebcDaily;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Layout\Row;
use Encore\Admin\Widgets\Box;
use Encore\Admin\Widgets\InfoBox;
use Illuminate\Support\Facades\Input;

class LivebcDailyController extends Controller
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

            $content->header('收支管理');
            $content->description('');
            $this->setTodayStat($content);
            $content->body($this->grid());
        });
    }
    private function setTodayStat($content){

        $content->row(function (Row $row) {
            $isLecturer=Auth::isLecturer();
            $where=['type'=>LivebcDaily::TYPE_RZ];
            if($isLecturer){
                $where['expid']=Admin::user()->id;
            }
            $fee[LivebcDaily::TYPE_RZ]=LivebcDaily::where($where)->sum('fee')/100;
            $where=['type'=>LivebcDaily::TYPE_TX];
            if($isLecturer){
                $where['expid']=Admin::user()->id;
            }
            $fee[LivebcDaily::TYPE_TX]=LivebcDaily::where($where)->sum('fee')/100;
            if($isLecturer) {
                $fee[LivebcDaily::TYPE_RZ]=$fee[LivebcDaily::TYPE_RZ];
                $box = new Box('直播收入', number_format($fee[LivebcDaily::TYPE_RZ], 2));
                $box->style('danger');
                //$box->solid();
                $row->column(4, $box);

                $box = new Box('已提现', number_format($fee[LivebcDaily::TYPE_TX], 2));
                $box->style('info');
                //$box->solid();
                $row->column(4, $box);

                $box = new Box('未提现', number_format($fee[LivebcDaily::TYPE_RZ] - $fee[LivebcDaily::TYPE_TX], 2));
                $box->style('success');

                //$box->solid();
                $max_fee=$fee[LivebcDaily::TYPE_RZ]-$fee[LivebcDaily::TYPE_TX];
                $box->content(number_format($max_fee,2)
                    .' '
                    .(($isLecturer && $max_fee>0)?new Takenow($max_fee>19999?19999:$max_fee):'')
                );
                $row->column(4, $box);
            }else{
                $box = new Box('直播流水', number_format($fee[LivebcDaily::TYPE_RZ]/0.6, 2));
                $box->style('warning');
                $row->column(3, $box);

                $box = new Box('平台收入', number_format($fee[LivebcDaily::TYPE_RZ]*(2/3), 2));
                $box->style('danger');
                $row->column(3, $box);

                $box = new Box('已提现', number_format($fee[LivebcDaily::TYPE_TX], 2));
                $box->style('success');
                $row->column(3, $box);

                $box = new Box('未提现', number_format($fee[LivebcDaily::TYPE_RZ] - $fee[LivebcDaily::TYPE_TX], 2));
                $box->style('primary');
                $row->column(3, $box);
            }

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
        if($act=='trans') {//退款
            return $this->trans();
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
        return Admin::grid(LivebcDaily::class, function (Grid $grid) {

            //$grid->id('ID')->sortable();
            $isLecturer=Auth::isLecturer();
            $grid->model()->orderBy('timestamp', 'desc');
            if($isLecturer){
                $grid->model()->where(['expid'=>Admin::user()->id]);
            }
            $grid->column('expert.real_name','讲师姓名')->display(function($real_name){
                return $real_name?$real_name:"ID:{$this->expid}";
            });
            $grid->timestamp('操作时间');
            if($isLecturer){
                $grid->fee('收支金额')->display(function ($fee) {
                    $fee=$fee/100;
                    if($this->type==LivebcDaily::TYPE_TX) return -$fee;
                    return $fee;
                });
            }else{
                $grid->column('fee','收支金额')->display(function ($fee) {
                    $fee=$fee/100;
                    if($this->type==LivebcDaily::TYPE_TX) return -$fee;
                    return number_format($fee,2);
                });
                $grid->column('fee_dv','平台收入')->display(function ($fee) {
                    if($this->type==LivebcDaily::TYPE_TX) return 0;
                    return number_format(($this->fee*(2/3))/100,2);
                });
            }
            //$grid->expires('到期时间');
            $grid->column('type','操作类型')->display(function ($state) {
                return LivebcDaily::getTypeStr($state);
            });
            if(Auth::isAdministrator()){
                //$grid->column('state_x','退款')->refund();
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
                //$filter->equal('expires', '到期时间')->date();
                $filter->between('timestamp', '操作时间')->datetime();
                $filter->like('expert.real_name', '讲师姓名');
                $filter->in('type', '操作类型')->checkbox(LivebcDaily::TYPE);

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
        return Admin::form(LivebcDaily::class, function (Form $form) {

            $form->display('id', 'ID');

            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }
    function trans(){
        $fee=Input::get('fee','');
        $expid=Admin::user()->id;
        $url='http://dv.cnfol.com/lvbc/trans?expid='
            .$expid.'&fee='
            .$fee.'&code='
            .md5($expid.$fee.'OJjjfdfjsdfsdfji@!&*@^*&^^jjjfdsfjds');
        \Log::info('lvbc-refund-url:'.$url);
        $res=$this->curl_get_contents($url,30);
        \Log::info('lvbc-refund-res:'.$res);
        if($res=='OK'){
            $m=LivebcDaily::create([
                'fee'=>$fee*100,
                'expid'=>$expid,
                'type'=>LivebcDaily::TYPE_TX,
                'timestamp'=>date("Y-m-d H:i:s",time()),
            ]);
            $m->save();
        }else{
            throw new \Exception('提现失败');
        }
        return $m;
    }
    function curl_get_contents($url,$timeout=1) {
        list($url,$post_data)=explode('?',$url);
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
