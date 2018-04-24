<?php

namespace App\Admin\Controllers;

use App\Models\Auth;
use App\Models\RedpackSetting;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class RedpackSettingController extends Controller
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

            $content->header('红包活动设置');
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
        return Admin::grid(RedpackSetting::class, function (Grid $grid) {
            $grid->disableRowSelector();
            $grid->disableExport();
            $grid->model()->orderBy('actid', 'desc');
            if(Auth::isLecturer()){
                $grid->model()->where(['expid'=>Admin::user()->id]);
            }
            $grid->actid('ID')->sortable();
            $grid->act_name('活动名称');
            $grid->column('expert.real_name','讲师');
            $grid->start_time('开始时间');
            $grid->end_time('结束时间');
            $grid->total_fee('总金额')->display(function(){
                return $this->total_fee/100;
            });
            $grid->total_num('总数量');
            $grid->column('_countReceiveFee','已领金额')->display(function(){
                return $this->countReceiveFee()/100;
            });
            $grid->column('countReceiveNum','已领数量')->display(function(){
                return $this->countReceiveNum();
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
        return Admin::form(RedpackSetting::class, function (Form $form) {

            $form->display('id', 'ID');

            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }
}
