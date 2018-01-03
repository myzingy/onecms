<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\ExcelExpoter;
use App\Models\Daily;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Support\Facades\Redirect;

class DailyController extends Controller
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

            $content->header('讲师收入');
            $content->description('');

            $content->body($this->grid());
            //$content->body(view('admin.grid.total',[3,5,6,7]));
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
        $m=$this->form()->edit($id)->model();
        $m->state=Daily::STATE_YJQ_MANUAL;
        $m->save();
        return redirect('/admin/daily');
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
        return Admin::grid(Daily::class, function (Grid $grid) {
            $grid->model()->orderBy('date', 'desc');
            $grid->paginate(31);
            $grid->perPages([10, 20, 31, 40, 50]);
            $grid->model()->with(['expert']);
            if(Admin::user()->isRole('lecturer')){
                $grid->model()->where('expid', '=', Admin::user()->id);
                $grid->disableActions();
            }
            $grid->expid('讲师ID')->sortable();
            $grid->column('expert.mp_name','公众号');
            $grid->column('expert.real_name','讲师姓名');
            $grid->fee_total('本日收入')->display(function ($fee) {
                return $fee/100;
            });
            $grid->date('日期');
            $grid->fee_refund('退款申请金额')->display(function ($fee) {
                return $fee/100;
            });
            $grid->fee_due('结算收入')->display(function ($fee) {
                return $fee/100;
            });
            $grid->fee_owe('未结清金额')->display(function ($fee) {
                return $fee/100;
            });
            $grid->column('state','结算状态')->display(function ($state) {
                return Daily::STATE[$state];
            });
            $grid->actions(function ($actions) {
                $actions->disableDelete();
                $actions->disableEdit();
                // append一个操作
                if($actions->row->state==Daily::STATE_WJS || $actions->row->state==Daily::STATE_WJQ){
                    $actions->append('<a href="/admin/daily/'.$actions->getKey().'/edit" onclick="if(!confirm(\'确认已经结算？\')) return false;">手动结算</a>');
                }

            });
            $grid->disableRowSelector();
            //disableExport
            //$grid->disableExport();
            $grid->exporter(new ExcelExpoter());
            //disableCreation
            $grid->disableCreation();
            // filter($callback)方法用来设置表格的简单搜索框
            $grid->filter(function($filter){
                // 如果过滤器太多，可以使用弹出模态框来显示过滤器.
                $filter->useModal();
                // 禁用id查询框
                $filter->disableIdFilter();
                $filter->between('date', '日期')->date();
                // 关系查询，查询对应关系`profile`的字段
                $filter->where(function ($query) {
                    $input = $this->input;
                    $query->whereHas('expert', function ($query) use ($input) {
                        $query->where('real_name', 'like', "%{$input}%");
                    });
                }, '讲师姓名');
                $filter->equal('state', '结算状态')->checkbox(Daily::STATE);

            });
            $grid->footer(function() use($grid){
                //\Log::info(json_encode());
                //$grid->column('fee_total','本日收入');
                return true;
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
        return Admin::form(Daily::class, function (Form $form) {
            $form->title('手动结算');
            $form->display('id', 'ID');

            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }

    function balance($id){
        return Admin::content(function (Content $content) use ($id) {

            $content->header('手动结算');
            $content->description('');

            $content->body($this->form()->edit($id));
        });
    }
}
