<?php

namespace App\Admin\Controllers;

use App\Models\Paylog;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

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
            $grid->model()->with(['question','expert']);
            $grid->model()->orderBy('payid', 'desc');
            $grid->payid('ID')->sortable();
            $grid->openid('OPENID');
            $grid->column('expert.real_name','讲师姓名');
            $grid->column('question.asker_name','提问者');
            $grid->column('question.question','问题');
            $grid->fee('金额');
            $grid->timestamp('时间');
            $grid->column('state','支付状态')->display(function ($state) {
                return Paylog::getStateStr($state);
            });
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
                $filter->where(function ($query) {
                    $input = $this->input;
                    $query->whereHas('expert', function ($query) use ($input) {
                        $query->where('real_name', 'like', "%{$input}%");
                    });
                }, '讲师姓名');
                $filter->where(function ($query) {
                    $input = $this->input;
                    $query->whereHas('question', function ($query) use ($input) {
                        $query->where('asker_name', 'like', "%{$input}%");
                    });
                }, '提问者姓名');
                $filter->equal('state', '支付状态')->checkbox(Paylog::STATE);

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
}
