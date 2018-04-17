<?php

namespace App\Admin\Controllers;

use App\Models\ArticalExpert;

use App\Models\Expert;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Support\Facades\Input;

class ArticalExpertController extends Controller
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

            $content->header('作者管理');
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

            $content->header('编辑作者');
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

            $content->header('添加作者');
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
        return Admin::grid(ArticalExpert::class, function (Grid $grid) {

            $grid->expid('讲师ID')->sortable();
            $grid->column('expert.real_name','讲师姓名');
            $grid->enable('状态')->editable('select', ArticalExpert::ENABLE);
            $grid->column('tipenable','打赏状态')->editable('select', ArticalExpert::TIPENABLE);
            //$grid->column('tipprices','打赏金额');
            //$grid->column('tipsign','打赏感谢语');
            $grid->column('countArtical','文章数')->display(function(){
                return $this->countArtical();
            });
            $grid->column('countViews','阅读量')->display(function(){
                return $this->countViews();
            });
            $grid->column('countFee','打赏收入')->display(function(){
                return $this->countFee();
            });

            $grid->disableRowSelector();
            $grid->disableExport();

            $grid->actions(function ($actions) {
                $actions->disableDelete();
                //$actions->disableEdit();
            });

            $grid->filter(function($filter){
                // 如果过滤器太多，可以使用弹出模态框来显示过滤器.
                $filter->useModal();
                // 禁用id查询框
                $filter->disableIdFilter();
                $filter->like('expert.real_name', '讲师姓名');
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
        return Admin::form(ArticalExpert::class, function (Form $form) {

            $form->ignore(['expert.real_name','tipprices']);
            $form->text('expert.real_name', '讲师姓名')->rules('required');
            $form->radio('enable', '状态')
                ->options(ArticalExpert::ENABLE)
                ->default(ArticalExpert::ENABLE_NO);

            $form->radio('tipenable', '打赏状态')
                ->options(ArticalExpert::TIPENABLE)
                ->default(ArticalExpert::TIPENABLE_NO);
            $form->text('tipsign', '打赏感谢语')->rules('max:200');
            $form->tags('tipprices', '打赏默认金额(6组)')->default([8,18,88,188,588,888]);
            if(Input::get('_editable')!=1){
                $form->saving(function (Form $form){
                    $expert=Expert::where(['real_name'=>Input::get('expert.real_name')])->first();
                    if(empty($expert->expid)){
                        throw new \Exception('讲师真实姓名不存在');
                    }
                    $form->model()->expid=$expert->expid;
                    //var_dump(Input::get('tipprices'));
                    //exit;
                    $tipprices_data=[];
                    $tipprices_index=0;
                    $tipprices=Input::get('tipprices',[]);

                    foreach ( $tipprices as $i=>&$val){
                        if(is_numeric($val)){
                            $val=(int)$val;
                            array_push($tipprices_data,$val);
                            $tipprices_index+=1;
                        }
                        if($tipprices_index>5) break;
                    }
                    $form->model()->tipprices=implode(',',$tipprices_data);
                });
            }

            return false;
        });
    }
}
