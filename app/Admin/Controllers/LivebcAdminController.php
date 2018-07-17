<?php

namespace App\Admin\Controllers;

use App\Models\Expert;
use App\Models\Livebc;

use App\Models\LivebcExpert;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Support\Facades\Input;

class LivebcAdminController extends Controller
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
            if(!Admin::user()->isRole('lecturer')) {
                $expid=Input::get('expid',0);
                $le=LivebcExpert::find($expid);
                $content->header($le->name);
                $content->description($le->notice);

                $content->body($this->grid());
            }
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
            if(!Admin::user()->isRole('lecturer')) {
                $content->header('编辑直播内容');
                $content->description('');

                $content->body($this->form()->edit($id));
            }
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
        return Admin::grid(Livebc::class, function (Grid $grid) {

            $expid=Input::get('expid',0);
            $grid->model()->where([
                'expid'=>$expid
            ])->where('timestamp','>',date("Y-m-d 00:00:00",time()));
            //disable
            $grid->disableCreation();
            $grid->disableExport();
            $grid->disableRowSelector();
            //$grid->disableActions();
            //$grid->disableFilter();

            $grid->model()->orderBy('timestamp','desc');

            $grid->id('ID');
            $grid->column('timestamp','时间')->style('width:180px;');

            $grid->tag('类型')->display(function($tag){
                return Livebc::getTagStr($tag);
            })->style('width:100px;');
            $grid->column('content','点评内容')->display(function($html){
                return preg_replace("/<[^>]+>/","",$html);
            })->limit(200)->style('max-width:600px;');

            // filter($callback)方法用来设置表格的简单搜索框
            $grid->filter(function($filter){
                // 如果过滤器太多，可以使用弹出模态框来显示过滤器.
                $filter->useModal();
                // 禁用id查询框
                $filter->disableIdFilter();

                $filter->between('timestamp','直播时间')->datetime();
                $filter->like('content','直播内容');

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
        return Admin::form(Livebc::class, function (Form $form) {
            $form->model()->with(['expert']);
            $form->display('id', '直播内容 ID');
            $form->display('expert.mp_name', '微信名称');
            $form->display('expert.real_name', '讲师姓名');

            $form->editor('content', '内容');
        });
    }
}
