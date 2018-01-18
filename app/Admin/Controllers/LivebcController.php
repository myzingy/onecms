<?php

namespace App\Admin\Controllers;

use App\Models\Livebc;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class LivebcController extends Controller
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

            $content->header('今日直播');
            $content->description('');
            if(Admin::user()->isRole('lecturer')) {
                $content->body('<div style="max-height: 300px;overflow-y: auto;">'.$this->grid().'</div>');
                $content->body($this->form());
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

            $content->header('今日直播');
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
        return Admin::grid(Livebc::class, function (Grid $grid) {

            //disable
            $grid->disableCreation();
            $grid->disableExport();
            $grid->disableRowSelector();
            $grid->disableActions();
            $grid->disableFilter();
            $grid->disablePagination();
            $grid->model()->where([
                'expid'=>Admin::user()->id
            ]);
            $grid->model()->where('timestamp','>',date('Y-m-d 00:00:00',time()));
//            $grid->model()->where(function($query){
//                $query->where('timestamp','>',date('Y-m-d 00:00:00',time()));
//            });
            $grid->model()->orderBy('timestamp','desc');

            $grid->column('timestamp','时间')->display(function($timestamp){
                return substr($timestamp,-8);
            })->style('width:100px;');

            $grid->tag('类型')->display(function($tag){
                return Livebc::getTagStr($tag);
            })->style('width:100px;');
            $grid->column('content','点评内容')->display(function($html){
                return $html;
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
            $form->disableReset();
            $form->setAction('/admin/livebc');
            $form->radio('tag','点评类型')->options(Livebc::TAG)->default(0);
            $form->editor('content', '点评内容')->rules('required', [
                'required' => '必须填写',
            ]);;
            $form->time('timestamp', '时间')->default(date('H:i:s',time()));
            $js=<<<JSEND
<script>
$(function(){
    $('.form-horizontal .box-footer').css('margin-top','-90px'); 
    $('.form-horizontal').prev().remove(); 
});
</script>
JSEND;

            $form->html($js);
            $form->saving(function(Form $form){
                $form->timestamp=date('Y-m-d '.$form->timestamp,time());
                $form->model()->expid=Admin::user()->id;
            });
            $form->saved(function(Form $form){
                return redirect('/admin/livebc');
            });
        });
    }
}
