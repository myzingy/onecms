<?php
namespace App\Admin\Controllers;

use App\Admin\Extensions\PlatformExpoter;
use App\Models\Auth;
use App\Models\Daily;
use App\Models\Daily2017;
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
    private $EchartsData=[];

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
            $tab->add('图表', view('admin.echarts.bar'));
            $this->setTodayStat($content);
            $content->body($tab->render());
        });
    }
    private function setTabTable(){
        return Admin::grid(Daily::class, function (Grid $grid) {
            $grid->paginate(31);
            $grid->perPages([10, 20, 31, 40, 50]);
            $grid->model()->select(
                DB::raw('`date`,sum(ques_total) as ques_total'
                .',sum(ques_answered) as ques_answered'
                    .',sum(fee_total) as fee_total'
                    .',sum(fee_refund) as fee_refund'
                    .',sum(fee_due) as fee_due'
                    .',sum(fee_owe) as fee_owe'
                    //.',(sum(fee_total)-sum(fee_due)-sum(fee_owe)-sum(fee_refund)) as fee_money'
                    .',(sum(fee_total)-sum(fee_due)) as fee_money'
                    .',substring(`date`,-10,7) as `month`'
                    .",DATE_FORMAT(`date`,'%Y年第%u周') weeks"
                )

            );
            $group='';
            foreach (Input::get() as $qu=>$val){
                if(strlen($qu)==32){
                    $group=$val;
                }
            }
            if($group==1){
                $grid->model()->groupBy(DB::raw("DATE_FORMAT(`date`,'%Y%u')"));
                $grid->weeks('按周日期')->sortable();
            }elseif ($group==2){
                $grid->model()->groupBy(DB::raw('substring(`daily`.`date`,-10,7)'));
                $grid->month('按月日期')->sortable();
            }else{
                $grid->model()->groupBy('date');
                $grid->date('日期')->sortable();
            }
            $grid->model()->orderBy('date', 'desc');
            $grid->ques_total('问题数量');
            $grid->ques_answered('回答数量');
            $grid->fee_total('总收入')->display(function ($fee) {
                return $fee/100;
            });
            $grid->fee_refund('当日退款')->display(function ($fee) {
                return $fee/100;
            });
            $grid->fee_due('支付大V结算')->display(function ($fee) {
                return $fee/100;
            });
            if(Auth::isAdministrator()) {
                $grid->fee_owe('手动结算')->display(function ($fee) {
                    return $fee / 100;
                });
            }
            $grid->fee_money('平台收入')->display(function ($fee) {
                return $fee/100;
            });
            //$grid->disableExport();
            $grid->exporter(new PlatformExpoter());
            $grid->disableRowSelector();
            $grid->disableCreation();
            $grid->disableActions();
            $grid->filter(function($filter){
                // 如果过滤器太多，可以使用弹出模态框来显示过滤器.
                //$filter->useModal();
                // 禁用id查询框
                $filter->disableIdFilter();
                $filter->between('date', '日期')->date();

                $filter->where(function ($query){

                },'统计')->radio([
                    ''   => '按日',
                    1    => '按周',
                    2    => '按月',
                ]);

            });
            $grid->footer(function() use($grid){
                echo view('admin.grid.total',['total'=>'[1,2,3,4,5,6,7]']);
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
            $infoBoxToday = new InfoBox('今日 分成收入/流水收入', 'money', 'aqua', 'javascript://', ''.number_format(($fee/100)*0.4,2).' / '.number_format($fee/100,2));
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
                    //.',(sum(fee_total)-sum(fee_due)-sum(fee_owe)-sum(fee_refund)) as fee_money'
                    .',(sum(fee_total)-sum(fee_due)) as fee_money'
                    )
            )->first();

            $infoBoxAll = new InfoBox('历史 分成收入/流水收入', 'money', 'green', 'javascript://', ''.number_format($feex->fee_money/100,2).' / '.number_format($feex->fee_total/100,2));
            $row->column(6, $infoBoxToday->render());
            $row->column(6, $infoBoxAll->render());
        });


    }
    public function lecturer(){
        return Admin::content(function (Content $content) {
            $content->header('历史统计');
            $content->description('');

            $tab = new Tab();
            $tab->add('报表', $this->setTabTableHistory());
            //$tab->add('图表', view('admin.echarts.bar'));
            $this->setTodayStatHistory($content);
            $content->body($tab->render());
        });
    }
    private function setTabTableHistory(){
        return Admin::grid(Daily2017::class, function (Grid $grid) {
            $grid->paginate(31);
            $grid->perPages([10, 20, 31, 40, 50]);
            $grid->model()->orderBy('date', 'desc');
            $grid->mp_name('公众号');
            $grid->date('收益日期')->sortable();
            $grid->name('收款人');
            $grid->fee('金额');
            $grid->pay_time('支付时间')->display(function ($fee) {
                return ($fee>0)?$fee:'';
            });
            $grid->pay_method('支付方式');
            $grid->disableExport();
            //$grid->exporter(new PlatformExpoter());
            $grid->disableRowSelector();
            $grid->disableCreation();
            $grid->disableActions();
            $grid->filter(function($filter){
                // 如果过滤器太多，可以使用弹出模态框来显示过滤器.
                //$filter->useModal();
                // 禁用id查询框
                $filter->disableIdFilter();
                $filter->between('date', '日期')->date();
                $filter->equal('name', '收款人');
                $filter->equal('mp_name', '公众号');
                /*
                $filter->where(function ($query){

                },'统计')->radio([
                    ''   => '按日',
                    1    => '按周',
                    2    => '按月',
                ]);
                */
            });
            $grid->footer(function(){
                echo view('admin.grid.total',['total'=>'[3]']);
            });
        });
    }
    private function setTodayStatHistory($content){

        //Daily::find()->where()->all();
        $content->row(function (Row $row) {
            $paylog=new Daily2017();
            $total=$paylog->sum('fee');
            $totalBox = new InfoBox('总收益', 'money', 'blue', 'javascript://', number_format($total,2));

            $totalP=$paylog->where(['pay_method'=>'平台'])->sum('fee');
            $totalPBox = new InfoBox('平台收益', 'money', 'green', 'javascript://', number_format($totalP,2));


            $totalEBox = new InfoBox('讲师收益', 'money', 'yellow', 'javascript://', number_format($total-$totalP,2));



            $row->column(4, $totalBox->render());
            $row->column(4, $totalEBox->render());
            $row->column(4, $totalPBox->render());
        });


    }

}
