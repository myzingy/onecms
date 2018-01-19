<?php

namespace App\Admin\Controllers;

use App\Models\LivebcAds;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class AdsController extends Controller
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

            $content->header('广告管理');
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

            $content->header('广告编辑');
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

            $content->header('广告编辑');
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
        return Admin::grid(LivebcAds::class, function (Grid $grid) {

            $grid->id('ID')->sortable();

            $grid->model()->with(['expert']);
            $grid->column('expert.real_name','讲师 (ID)')->display(function($real_name) {
                return ($this->expid>0?$real_name:'公用')." ({$this->expid})";
            });
            $grid->imgurl('图片')->image();
            $grid->column('url','链接');
            $grid->column('expires','到期时间');
            //disableExport
            $grid->disableExport();
            $grid->disableRowSelector();
            // filter($callback)方法用来设置表格的简单搜索框
            $grid->filter(function($filter){
                // 如果过滤器太多，可以使用弹出模态框来显示过滤器.
                $filter->useModal();
                // 禁用id查询框
                $filter->disableIdFilter();

                $filter->like('expert.real_name','讲师');
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
        return Admin::form(LivebcAds::class, function (Form $form) {

            $form->text('expid','讲师 ID')->help('为0时公用');
            //$form->select('expid','讲师')->options('/admin/lecturer/users?act=api');
            $form->image('imgurl','广告图片')->help('建议 640*200');
            $form->url('url','广告链接');
            $form->date('expires','到期时间');
            $form->saved(function (Form $form) {
                if($form->model()->imgurl && false === strpos($form->model()->imgurl,'http')){
                    $form->model()->imgurl = config('filesystems.disks.admin.url') .'/'.$form->model()->imgurl;
                }
                $form->model()->save();
            });
        });
    }
}
