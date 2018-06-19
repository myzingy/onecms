<?php

namespace App\Admin\Controllers;

use App\Models\Auth;
use App\Models\Tipsetting;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Support\Facades\Input;

class TipsettingController extends Controller
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

            $content->header('打赏管理');
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

            $content->header('打赏设置');
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
        return Admin::grid(Tipsetting::class, function (Grid $grid) {

            $grid->expid('讲师ID')->sortable();
            $grid->column('expert.real_name','讲师姓名');
            //$grid->type('打赏类型')->editable('select', Tipsetting::TYPE);
            $grid->type('打赏类型')->display(function($type){
                return Tipsetting::getStateStr($type,'TYPE');
            });
            $grid->prices('赏金价格');
            $grid->prices2('礼物价格');
            $grid->sign('打赏感谢语');
            $grid->ratio('分成比例')->display(function($ratio){
                return "$ratio%";
            });
            if(Auth::isLecturer()){
                $grid->model()->where(['expid'=>Admin::user()->id]);
//                $grid->state('状态')->display(function($enable){
//                    return Tipsetting::getStateStr($enable,'STATE');
//                });
            }
            //$grid->state('状态')->editable('select', Tipsetting::STATE);
            $grid->state('状态')->display(function($enable){
                return '已'.Tipsetting::getStateStr($enable,'STATE');
            });
            $grid->disableRowSelector();
            $grid->disableExport();
            $grid->disableCreation();

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
        return Admin::form(Tipsetting::class, function (Form $form) {

            $form->ignore(['prices','prices2']);
            $form->display('expert.real_name', '讲师姓名');
            $form->radio('state', '状态')
                ->options(Tipsetting::STATE)
                ->default(Tipsetting::STATE_NO);
            $form->rate('ratio', '分成比例')->rules('numeric|between:1,100');

            $form->radio('type', '打赏类型')
                ->options(Tipsetting::TYPE)
                ->default(Tipsetting::TYPE_MONEY);
            $form->tags('prices', '赏金价格(6组)')->default([5,10,20,50,100,200]);
            $form->tags('prices2', '礼物价格(8组)')->default([8,18,58,118,218,518,666,888]);
            $form->text('sign', '打赏感谢语')->rules('max:200');
            if(Input::get('_editable')!=1){
                $form->saving(function (Form $form){
                    function _v($key='prices',$length=6){
                        $prices_data=[];
                        $prices_index=0;
                        $prices=Input::get($key,[]);
                        foreach ( $prices as $i=>&$val){
                            if(is_numeric($val)){
                                $val=(int)$val;
                                array_push($prices_data,$val);
                                $prices_index+=1;
                            }
                            if($prices_index>=$length) break;
                        }
                        array_multisort($prices_data);
                        return implode(',',$prices_data);
                    }
                    $form->model()->prices=_v('prices',6);
                    $form->model()->prices2=_v('prices2',8);
                });
            }

            return false;
        });
    }
}
