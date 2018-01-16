<?php

namespace App\Admin\Controllers;

use App\Models\LivebcExpert;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class LivebcExpertController extends Controller
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

            $content->header('直播管理');
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

            $content->header('直播管理');
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
        return Admin::grid(LivebcExpert::class, function (Grid $grid) {

            $grid->expid('讲师ID')->sortable();
            if(Admin::user()->isRole('lecturer')){
                $LivebcExpert=LivebcExpert::find(Admin::user()->id);
                if(!$LivebcExpert){
                    $expid=Admin::user()->id;
                    $name=Admin::user()->name;
                    \Log::info([$expid,$name]);

                    $LivebcExpert=LivebcExpert::create([
                        'expid'=>$expid,
                        'name'=>$name,
                        'notice'=>'',
                        'fee_bc'=>0,
                        'state'=>LivebcExpert::STATE_DISABLED
                    ]);
                    $LivebcExpert->save();
                }
                $grid->model()->where('expid', '=', Admin::user()->id);
            }
            $grid->model()->with(['expert']);
            $grid->column('expert.real_name','讲师');
            $grid->column('name','直播名称');
            $grid->column('fee_bc','直播价格');
            $grid->column('notice','直播公告')->style('max-width:600px;');
            $grid->column('state','状态')->display(function($state){
                return LivebcExpert::getStateStr($state);
            });
            //disableCreation
            $grid->disableCreation();
            //disableExport
            $grid->disableExport();
            $grid->disableRowSelector();
            $grid->actions(function ($actions) {
                $actions->disableDelete();
                //$actions->disableEdit();
            });
            // filter($callback)方法用来设置表格的简单搜索框
            $grid->filter(function($filter){
                // 如果过滤器太多，可以使用弹出模态框来显示过滤器.
                $filter->useModal();
                // 禁用id查询框
                $filter->disableIdFilter();

                $filter->like('expert.real_name','讲师');
                $filter->like('name','直播名称');

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
        return Admin::form(LivebcExpert::class, function (Form $form) {

            $form->display('id', 'ID');

            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }
}
