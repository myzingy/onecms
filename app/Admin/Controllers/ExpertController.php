<?php

namespace App\Admin\Controllers;

use App\Models\Expert;

use App\Models\ExpertApplication;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class ExpertController extends Controller
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

            $content->header('header');
            $content->description('description');

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
            if(Admin::user()->isRole('lecturer') && $id != Admin::user()->id)  throw new \Exception('无权访问此地址');
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
        return Admin::grid(Expert::class, function (Grid $grid) {

            if(Admin::user()->isRole('lecturer')){
                $grid->model()->where('expid', '=', Admin::user()->id);
                $grid->disableFilter();
                $grid->disableCreation();
                $grid->actions(function ($actions) {
                    $actions->disableDelete();
                });
            }
            $grid->model()->orderBy('expid', 'desc');

            $grid->real_name('真实姓名')->sortable();
            $grid->mobile('手机号')->sortable();
            $grid->qq('qq')->sortable();
            $grid->mp_name('公众号名称')->sortable();

            $grid->wx_name('微信昵称')->sortable();
            $grid->wx_img_url('微信头像')->display(function ($name) {
                return $name ? "<img width='80px' src='$name' />" : '';
            });


//            $grid->created_at('申请时间')->sortable();

            $grid->filter(function($filter){

                // 去掉默认的id过滤器
                $filter->disableIdFilter();

                // 在这里添加字段过滤器
                $filter->like('real_name', '真实姓名');
                $filter->like('mp_name', '公众号');
                $filter->like('wx_name', '昵称');
                $filter->like('mobile', '手机号');

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
        return Admin::form(Expert::class, function (Form $form) {
            $form->hidden('expid');
            if(Admin::user()->isRole('manager') || Admin::user()->isRole('administrator')){
                //讲师信息
                $form->text('real_name', '真实姓名')->rules('required|min:2');
                $form->text('wx_name', '微信昵称');
                $form->text('wx_img_url', '微信头像');
                //联系方式
                $form->text('mobile', '手机号')->rules('required|numeric|min:6');
                $form->text('qq', 'QQ');
                // $form->text('wx_qrcode', '微信二维码');
                $form->image('wx_qrcode','微信二维码');
                $form->display('openid', 'openID');
                $form->display('unionid', 'unionId');
                //公众号信息
                $form->text('mp_name', '公众号名称');
                $form->image('mp_img_url', '公众号图片');
                //$form->text('mp_qrcode', '公众号二维码');
                $form->image('mp_qrcode','公众号二维码');
                $form->text('mp_appid', '公众号AppId');
                $form->text('mp_secret', '公众号Secret');
                $form->radio('mp_auth', '是否认证')->options(Expert::$enableOptions);
                $form->text('mp_verify_file_url', '验证文件网址');//->rules('url');
                //审核操作
                $form->radio('state','是否启用')->options(Expert::$enableOptions);

            }

            $form->text('exp_intro', '讲师介绍');
            $form->image('exp_bg_url', '背景图');
            $form->text('price_ask', '提问费用');
            $form->text('price_see', '查看费用');
            //服务方式
            $form->radio('svc_type','服务方式')->options(ExpertApplication::$svcTypeOptions);


        });
    }
}
