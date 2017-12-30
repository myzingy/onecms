<?php

namespace App\Admin\Controllers;

use App\Models\ExpertApplication;

use App\Models\Expert;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Support\Facades\DB;

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

            $content->header('申请入驻');
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
        return Admin::grid(ExpertApplication::class, function (Grid $grid) {
//            $grid->disableFilter();
            $grid->disableExport();
            $grid->disableRowSelector();
            $grid->actions(function ($actions) {
               // $actions->disableDelete();
            });
            if(Admin::user()->isRole('service')){
                $grid->model()->where('state', '!=', ExpertApplication::STATE_PASS);
            }
            $grid->model()->orderBy('created_at', 'desc');

            $grid->apid('申请ID');
            $grid->real_name('真实姓名');
            $grid->mp_name('公众号名称')->popover(['placement'=>'right','column'=>'mp_qrcode']);
            $grid->mobile('手机号');
            $grid->wx_name('微信昵称')->popover(['placement'=>'right','column'=>'wx_qrcode']);
            $grid->wx_img_url('微信头像')->display(function ($name) {
                return $name ? "<img width='60px' src='$name' />" : '';
            })->popover(['placement'=>'right','column'=>'wx_qrcode']);
            $grid->state('状态')->display(function ($v) {
                return ExpertApplication::$statusOptions[$v];
            });

//            $grid->qq('qq');



//            $grid->created_at('申请时间')->sortable();

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
            $form->image('mp_img_url', '公众号图片');
            //$form->text('mp_qrcode', '公众号二维码');
            $form->image('mp_qrcode','公众号二维码');
            $form->text('mp_appid', '公众号AppId');
            $form->text('mp_secret', '公众号Secret');
            $form->select('mp_auth', '是否认证')->options(ExpertApplication::$enableOptions);
            $form->text('mp_verify_file_url', 'js安全域名');//->rules('url');
            //审核操作
            $form->radio('state','状态')->options(ExpertApplication::$statusOptions);
            $form->radio('rejected_reason','驳回原因')->options(ExpertApplication::$RejectedOptions);
            //服务方式
            $form->radio('svc_type','服务方式')->options(ExpertApplication::$svcTypeOptions);

//
//            $form->display('created_at', 'Created At');
//            $form->display('updated_at', 'Updated At');
            $form->saved(function (Form $form) {

                if($form->state == ExpertApplication::STATE_PASS){//审核通过
                    $mm = new Expert();
                    $mm->addToExpert($form);
                }
            });
        });
    }
}
