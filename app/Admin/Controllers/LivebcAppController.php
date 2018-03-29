<?php

namespace App\Admin\Controllers;

use App\Models\LivebcApp;

use App\Models\LivebcSubs;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class LivebcAppController extends Controller
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

            $content->header('体验申请');
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
        return Admin::grid(LivebcApp::class, function (Grid $grid) {

            $grid->id('ID')->sortable();

            $grid->model()->where('state', '=', LivebcApp::STATE_APPLY);

            $grid->column('opid','OPENID');
            $grid->column('nickname','用户昵称');
            $grid->column('expert.real_name','讲师');
            $grid->column('fee','对讲师消费');
            $grid->column('totalfee','对平台消费');
            $grid->column('timestamp','申请时间');
            $grid->column('expert.real_name','讲师');
            $grid->state('审核状态')->select(LivebcApp::STATE);

            $grid->disableCreation();
            $grid->disableExport();
            $grid->disableRowSelector();
            $grid->disableActions();
            $grid->filter(function($filter){
                // 如果过滤器太多，可以使用弹出模态框来显示过滤器.
                $filter->useModal();
                // 禁用id查询框
                $filter->disableIdFilter();

                $filter->like('expert.real_name','讲师');
                $filter->like('nickname','用户昵称');
                $filter->between('timestamp', '申请时间')->datetime();
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
        return Admin::form(LivebcApp::class, function (Form $form) {

            $form->display('id', 'ID');
            $form->hidden('state');
            $form->saved(function (Form $from){
                if($from->model()->state==LivebcApp::STATE_SUCCESS){
                    $m=LivebcSubs::create([
                        'expid'=>$from->model()->expid,
                        'openid'=>$from->model()->opid,
                        'expires'=>date("Y-m-d",time()+86400*5),
                        'timestamp'=>date("Y-m-d H:i:s",time()),
                        'state'=>LivebcSubs::STATE_TY,
                    ]);
                    $m->save();
                }
            });
            return back()->with(compact('success'));
        });
    }
}
