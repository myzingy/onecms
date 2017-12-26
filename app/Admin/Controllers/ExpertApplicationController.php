<?php

namespace App\Admin\Controllers;

use App\Admin\Models\ExpertApplication;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class ExpertApplicationController extends Controller
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
        return Admin::grid(ExpertApplication::class, function (Grid $grid) {
            $grid->model()->orderBy('created_at', 'desc');

            $grid->apid('申请ID')->sortable();
            $grid->real_name('真实姓名')->sortable();
            $grid->mobile('手机号')->sortable();
            $grid->qq('qq')->sortable();
            $grid->mp_name('公众号名称')->sortable();

            $grid->wx_name('微信昵称')->sortable();
            $grid->wx_img_url('微信头像')->display(function ($name) {
                return $name ? "<img width='80px' src='$name' />" : '';
            });
            $grid->state('状态')->sortable()->display(function ($v) {
                return ExpertApplication::$statusOptions[$v];
            });

            $grid->created_at('申请时间')->sortable();
            $grid->updated_at('更新时间');

            $grid->filter(function($filter){

                // 去掉默认的id过滤器
                $filter->disableIdFilter();

                // 在这里添加字段过滤器
                $filter->like('real_name', '真实姓名');
                $filter->like('mp_name', '公众号');
                $filter->like('wx_name', '昵称');
                $filter->like('mobile', '手机号');
                $filter->in('state', '状态')->multipleSelect(ExpertApplication::$statusOptions);

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
        return Admin::form(ExpertApplication::class, function (Form $form) {

            $form->hidden('apid');
            //讲师信息
            $form->text('real_name', '真实姓名')->rules('required|min:2');
            $form->text('wx_name', '微信昵称');
            $form->text('wx_img_url', '微信头像');
            //联系方式
            $form->text('mobile', '手机号')->rules('required|numeric|min:6');
            $form->text('qq', 'QQ');
           // $form->text('wx_qrcode', '微信二维码');
            $form->image('wx_qrcode','微信二维码');
            $form->text('openid', 'openID');
            $form->text('unionid', 'unionId');
            //公众号信息
            $form->text('mp_name', '公众号名称');
            $form->text('mp_img_url', '公众号图片');
            //$form->text('mp_qrcode', '公众号二维码');
            $form->image('mp_qrcode','公众号二维码');
            $form->text('mp_appid', '公众号AppId');
            $form->text('mp_secret', '公众号Secret');
            $form->select('mp_auth', '是否认证')->options(ExpertApplication::$enableOptions);
            $form->text('mp_verify_file_url', '验证文件网址');//->rules('url');
            //审核操作
            $form->select('state','状态')->options(ExpertApplication::$statusOptions);
            $form->select('svc_type','驳回原因')->options(ExpertApplication::$svcTypeOptions);
            //服务方式
            $form->select('service_type','服务方式')->options(ExpertApplication::$serviceTypeOptions);

//
//            $form->display('created_at', 'Created At');
//            $form->display('updated_at', 'Updated At');
        });
    }
}
