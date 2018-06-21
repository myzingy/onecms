<?php

namespace App\Admin\Controllers;

use App\Models\Auth;
use App\Models\Expert;
use App\Models\RedpackSetting;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Support\Facades\Input;

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

            $content->header('编辑红包活动');
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

            $content->header('创建红包活动');
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
        return Admin::grid(RedpackSetting::class, function (Grid $grid) {
            $grid->disableRowSelector();
            $grid->disableExport();
            $grid->model()->orderBy('actid', 'desc');
            if(Auth::isLecturer()){
                $grid->disableCreation();
                $grid->disableActions();
                $grid->model()->where(['expid'=>Admin::user()->id]);
            }
            $grid->actid('ID')->sortable();
            $grid->act_name('活动名称');
            $grid->act_desc('红包祝福语');
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

            $grid->filter(function($filter){
                // 如果过滤器太多，可以使用弹出模态框来显示过滤器.
                $filter->useModal();
                // 禁用id查询框
                $filter->disableIdFilter();
                if(!Auth::isLecturer()){
                    $filter->like('expert.real_name','讲师');
                }
                $filter->like('act_name','活动名称');

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

            $form->ignore(['expert.real_name','total_feex','start_time','end_time']);
            $form->hidden('actid');
            //$form->hidden('expid')->default(Admin::user()->id);
            $form->text('act_name','红包活动名称')->rules('required|max:45');
            $form->text('act_desc','红包祝福语')->rules('required|max:200');
            if(Auth::isLecturer()){
                $form->hidden('expid')->default(Admin::user()->id);
            }else{
                $form->text('expert.real_name', '讲师姓名')->rules('required');
            }
            $form->date('start_time','开始日期')->rules('required');
            $form->date('end_time','结束日期')->rules('required');
            $form->text('total_feex','总金额')->rules('required|regex:/^[1-9]\d*$/', [
                'regex' => '必须为数字大于0的整数',
            ])->default(function() use ($form){
                return $form->model()->total_fee/100;
            });
            $form->text('total_num','总数量')->rules('required|regex:/^[1-9]\d*$/', [
                'regex' => '必须为数字大于0的整数',
            ])->default(1);
            $form->image('bg_img', '活动背景图')->uniqueName();
            $form->saving(function(Form $form){
                $form->model()->total_fee=Input::get('total_feex')*100;
                if($real_name=Input::get('expert.real_name')){
                    $expert=Expert::where(['real_name'=>$real_name])->first();
                    if(empty($expert->expid)){
                        throw new \Exception('讲师真实姓名不存在');
                    }
                    $form->model()->expid=$expert->expid;
                }
                if(Input::get('start_time')>Input::get('end_time')){
                    throw new \Exception('开始日期与结束日期设置错误');
                }
                $form->model()->start_time=Input::get('start_time')." 12:00:00";
                $form->model()->end_time=Input::get('end_time')." 23:59:59";
            });
            $form->saved(function (Form $form) {
                if($form->model()->bg_img && false === strpos($form->model()->bg_img,'http')){
                    $form->model()->bg_img = config('filesystems.disks.admin.url') .'/'.$form->model()->bg_img;
                    $form->model()->save();
                }
            });
        });
    }
}
