<?php

namespace App\Admin\Controllers;

use App\Models\ArticalDaily;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class ArticalDailyController extends Controller
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

            $content->header('收支列表');
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
        return Admin::grid(ArticalDaily::class, function (Grid $grid) {

            $grid->disableRowSelector();
            $grid->disableExport();
            $grid->disableCreation();
            $grid->disableActions();

            $grid->column('expert.real_name','讲师')->style('width:80px;');
            $grid->fee_exp('讲师收益')->display(function($fee){
                return number_format($fee/100,2);
            })->style('width:80px;');
            $grid->fee_plat('平台收益')->display(function($fee){
                return number_format($fee/100,2);
            })->style('width:80px;');
            $grid->type('类型')->display(function($type){
                return ArticalDaily::getTypeStr($type);
            });
            $grid->timestamp('时间');
            $grid->filter(function($filter){
                // 如果过滤器太多，可以使用弹出模态框来显示过滤器.
                $filter->useModal();
                // 禁用id查询框
                $filter->disableIdFilter();
                $filter->like('expert.real_name', '讲师');
                $filter->between('timestamp', '时间')->datetime();
                $filter->in('type', '类型')->checkbox(ArticalDaily::TYPE);
            });

            $grid->footer(function(){
                echo view('admin.grid.total', ['total' => '[1,2]']);
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
        return Admin::form(ArticalDaily::class, function (Form $form) {

            $form->display('id', 'ID');

            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }
}
