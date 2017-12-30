<?php

namespace App\Admin\Controllers;

use App\Models\Auth;
use App\Models\Expert;
use App\Models\ExpertRecom;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Support\Facades\Input;

class ExpertRecomController extends Controller
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

            $content->header('讲师推荐');
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
        if(!Auth::isAdministrator()){
            throw new \Exception('只有超级管理员有权操作');

        }
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
        $id = Input::get('id','');
        $max = 50;
        if(!$id) throw new \Exception('参数不正确');
        if(Auth::isAdministrator()){
            $info = Expert::find($id);
            $sum = ExpertRecom::count();
            if($sum > $max) throw new \Exception('推荐最多只有'.$max.'个');
            if(empty($info->recom)){
                $m = new ExpertRecom();
                $m->expid = $info->expid;
                $m->desc = '';
                $m->weight = 1;
                $m->save();
                return redirect('/admin/lecturer/publicity/'.$id.'/edit');
            }else{
                return redirect('/admin/lecturer/publicity/'.$id.'/edit');
            }


        }
        throw new \Exception('只有超级管理员有权操作');

    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(ExpertRecom::class, function (Grid $grid) {
            $grid->disableFilter();
            $grid->disableExport();
            $grid->disableCreation();
            $grid->disableRowSelector();
            $grid->actions(function ($actions) {

                // 没有`delete-image`权限的角色不显示删除按钮
                if (!Auth::isAdministrator()) {
                    $actions->disableDelete();
                }
            });
            $grid->expid('讲师ID');
            $grid->column('expert.real_name','讲师名');
            $grid->column('expert.mp_name','公众号');
            $grid->column('desc','简介');
//            $grid->desc('描述')->sortable();
            if (!Auth::isAdministrator()) {
                $grid->weight('权重');
            }else{
                $grid->weight('权重')->editable();
            }


            $grid->model()->orderBy('weight', 'desc');
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(ExpertRecom::class, function (Form $form) {

            $form->display('expid', '讲师ID');
            $form->display('expert.real_name', '讲师名');
            $form->display('expert.wx_img_url','头像')->with(function ($url) {
                return $url ? "<img width='80px' src='$url' />" : '';
            });
            $form->display('expert.price_ask', '提问金额');
            $form->textarea('desc', '简介')->rows(5);
            $form->text('weight', '排序权值')->rules('required|numeric');

        });
    }
}
