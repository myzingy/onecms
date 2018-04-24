<?php

namespace App\Admin\Controllers;

use App\Models\Auth;
use App\Models\Redpack;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class RedpackController extends Controller
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

            $content->header('红包领取记录');
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
        return Admin::grid(Redpack::class, function (Grid $grid) {

            $grid->disableRowSelector();
            $grid->disableExport();
            $grid->disableActions();
            $grid->disableCreation();
            $grid->paginate(30);
            $grid->perPages([30, 100, 300, 5000, 1000]);
            if(Auth::isLecturer()){
                $grid->model()->where(['expid'=>Admin::user()->id]);
            }
            $grid->model()->orderBy('id', 'desc');
            $grid->id('ID')->sortable();
            $grid->actid('红包活动')->display(function($actid){
                return "($actid){$this->redpack->act_name}";
            });
            $grid->column('expert.real_name','讲师');
            $grid->column('mpuser.nickname','领取者');
            $grid->fee_exp('领取金额')->display(function(){
                return number_format($this->fee/100,2);
            })->style('width:80px;');
            $grid->timestamp('领取时间');

            $grid->filter(function($filter){
                // 如果过滤器太多，可以使用弹出模态框来显示过滤器.
                $filter->useModal();
                // 禁用id查询框
                $filter->disableIdFilter();
                if(!Auth::isLecturer()) {
                    $filter->like('expert.real_name', '讲师');
                }
                $filter->like('redpack.act_name', '活动名称');

                $filter->between('timestamp', '时间')->datetime();
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
        return Admin::form(Redpack::class, function (Form $form) {

            $form->display('id', 'ID');

            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }
}
