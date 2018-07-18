<?php

namespace App\Admin\Controllers;

use App\Models\Auth;
use App\Models\LivebcCourse;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class LivebcCourseController extends Controller
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

            $content->header('视频管理');
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

            $content->header('视频管理');
            $content->description('');

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

            $content->header('视频管理');
            $content->description('');

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
        return Admin::grid(LivebcCourse::class, function (Grid $grid) {



            //disable
            //$grid->disableCreation();
            $grid->disableExport();
            $grid->disableRowSelector();
            //$grid->disableActions();
            $grid->disableFilter();
            //$grid->disablePagination();
            if(Auth::isLecturer()){
                $grid->model()->where([
                    'expid'=>Admin::user()->id
                ]);
            }

            $grid->model()->orderBy('timestamp','desc');

            $grid->id('ID')->sortable()->style('width:50px;');
            $grid->column('expert.real_name','关联讲师')->display(function($name){
                return $name?$name:"无";
            })->style('width:80px;');
            $grid->column('thumbnail','缩略图')->image()->style('width:240px;');
            $grid->name('名称')->style('min-width:200px;');;
            $grid->column('timestamp','时间')->style('width:200px;');
            $grid->state('状态')->select(LivebcCourse::STATE)->style('width:100px;');
            $grid->actions(function ($actions) {
                $actions->disableDelete();
                //$actions->disableEdit();
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
        return Admin::form(LivebcCourse::class, function (Form $form) {
            if(Auth::isLecturer()){
                $form->hidden('expid', '讲师ID')->default(Admin::user()->id);
            }else{
                $form->text('expid', '讲师ID')->rules('required')->default(0);
            }
            $form->text('name', '视频名称')->rules('required');
            $form->image('thumbnail', '缩略图')->uniqueName();
            $form->url('video_url', '视频地址')->rules('required');
            $form->radio('state','状态')->options(LivebcCourse::STATE)->default(LivebcCourse::STATE_ENABLE);
            $form->saved(function (Form $form) {
                if($form->model()->thumbnail && false === strpos($form->model()->thumbnail,'http')){
                    $form->model()->thumbnail = config('filesystems.disks.admin.url') .'/'.$form->model()->thumbnail;
                }
                if(!$form->model()->timestamp){
                    $form->model()->timestamp = date("Y-m-d H:i:s",time());
                }
                $form->model()->save();
            });
        });
    }
}
