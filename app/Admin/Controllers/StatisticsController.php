<?php
namespace App\Admin\Controllers;

use App\Models\Daily;
use App\Models\Paylog;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Layout\Row;
use Encore\Admin\Widgets\Tab;
use Encore\Admin\Grid;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Encore\Admin\Widgets\InfoBox;
class StatisticsController extends Controller
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

            $content->header('header');
            $content->description('description');

            $content->body('...');
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
        return Admin::content(function (Content $content) use ($id) {

            $content->header('header');
            $content->description('description');


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


        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {

    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {

    }
    public function platform(){
        return Admin::content(function (Content $content) {
            $content->header('平台统计');
            $content->description('');

            $tab = new Tab();
            $tab->add('报表', $this->setTabTable());
            //$tab->add('图表', view('admin.echarts.bar'));
            $this->setTodayStat($content);
            $content->body($tab->render());
        });
    }
    private function setTabTable(){
        return Admin::grid(Daily::class, function (Grid $grid) {
            $grid->model()->select(
                DB::raw('`date`,sum(ques_total) as ques_total'
                .',sum(ques_answered) as ques_answered'
                    .',sum(fee_total) as fee_total'
                    .',sum(fee_refund) as fee_refund'
                    .',sum(fee_due) as fee_due'
                    .',sum(fee_owe) as fee_owe'
                    .',(sum(fee_total)-sum(fee_due)-sum(fee_owe)-sum(fee_refund)) as fee_money'
                    .',substring(`date`,-10,7) as `month`')
            );
            $group=Input::get('4be929919c7d154229912f4bbc2df624','');
            if($group==1){
                $grid->model()->groupBy('date');
                $grid->date('日期')->sortable();
            }elseif ($group==2){
                $grid->model()->groupBy(DB::raw('substring(`date`,-10,7)'));
                $grid->month('日期')->sortable();
            }else{
                $grid->model()->groupBy('date');
                $grid->date('日期')->sortable();
            }

            $grid->ques_total('问题数量');
            $grid->ques_answered('回答数量');
            $grid->fee_total('总收入')->display(function ($fee) {
                return $fee/100;
            });
            $grid->fee_refund('当日退款')->display(function ($fee) {
                return $fee/100;
            });
            $grid->fee_due('自动结算')->display(function ($fee) {
                return $fee/100;
            });
            $grid->fee_owe('手动结算')->display(function ($fee) {
                return $fee/100;
            });
            $grid->fee_money('平台收入')->display(function ($fee) {
                return $fee/100;
            });
            $grid->disableRowSelector();
            $grid->disableCreation();
            $grid->disableActions();
            $grid->filter(function($filter){
                // 如果过滤器太多，可以使用弹出模态框来显示过滤器.
                //$filter->useModal();
                // 禁用id查询框
                $filter->disableIdFilter();
                $filter->between('date', '日期')->date();
                /*
                $filter->where(function ($query){

                },'统计')->radio([
                    ''   => '按日',
                    1    => '按周',
                    2    => '按月',
                ]);
                */
            });
        });
    }
    private function setTodayStat($content){

        //Daily::find()->where()->all();
        $content->row(function (Row $row) {
            $paylog=new Paylog();
            $stime=time()-86400;
            DB::connection()->enableQueryLog();
            $fee=$paylog->whereBetween('timestamp',
                [date('Y-m-d 21:00:00',$stime), date('Y-m-d 21:00:00',time())]
            )
                ->where('state',Paylog::STATE_YZF)
                ->sum('fee');
            //print_r(DB::getQueryLog());
            $infoBoxToday = new InfoBox('今日 分成收入/流水收入', 'money', 'aqua', 'javascript://', ''.(($fee/100)*0.4).' / '.($fee/100));
            //daily
            //print_r(DB::getQueryLog());
            $daily=new Daily();
            $feex=$daily->select(
                DB::raw('sum(ques_total) as ques_total'
                    .',sum(ques_answered) as ques_answered'
                    .',sum(fee_total) as fee_total'
                    .',sum(fee_refund) as fee_refund'
                    .',sum(fee_due) as fee_due'
                    .',sum(fee_owe) as fee_owe'
                    .',(sum(fee_total)-sum(fee_due)-sum(fee_owe)-sum(fee_refund)) as fee_money'
                    )
            )->first();

            $infoBoxAll = new InfoBox('历史 分成收入/流水收入', 'money', 'green', 'javascript://', ''.($feex->fee_money/100).' / '.($feex->fee_total/100));
            $row->column(6, $infoBoxToday->render());
            $row->column(6, $infoBoxAll->render());
        });


    }
    public function lecturer(){
        return Admin::content(function (Content $content) {

            $content->header('header');
            $content->description('description');
            $content->body(view('admin.echarts.bar'));
        });
    }

}
