<?php

namespace App\Admin\Controllers;

use App\Models\ArticalPaylog;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class ArticalPaylogController extends Controller
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

            $content->header('打赏列表');
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
        return Admin::grid(ArticalPaylog::class, function (Grid $grid) {
            $grid->disableRowSelector();
            $grid->disableExport();
            $grid->disableCreation();
            $grid->disableActions();

            $where=['state'=>ArticalPaylog::STATE_YZF];
            $grid->model()->where($where);

            $grid->tradeno('订单号')->style('width:180px;');
            $grid->column('artical.title','文章');
            $grid->column('expert.real_name','讲师')->style('width:80px;');
            $grid->column('mpuser.nickname','打赏者')->style('width:80px;');
            $grid->fee('金额')->display(function($fee){
                return number_format($fee/100,2);
            })->style('width:80px;');
            $grid->timestamp('时间')->style('width:180px;');

            $grid->filter(function($filter){
                // 如果过滤器太多，可以使用弹出模态框来显示过滤器.
                $filter->useModal();
                // 禁用id查询框
                $filter->disableIdFilter();
                $filter->like('artical.title', '文章标题');
                $filter->like('expert.real_name', '讲师');
                $filter->like('mpuser.nickname', '打赏者');
                $filter->like('mpuser.nickname', '打赏者');
                $filter->between('timestamp', '打赏时间')->datetime();
            });

            $grid->footer(function(){
                echo view('admin.grid.total', ['total' => '[4]']);
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
        return Admin::form(ArticalPaylog::class, function (Form $form) {

            $form->display('id', 'ID');

            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }
}
