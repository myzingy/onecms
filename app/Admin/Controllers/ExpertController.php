<?php

namespace App\Admin\Controllers;

use App\Models\Auth;
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

            $content->header('讲师管理');
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
        if(Auth::isLecturer()){
            if($id != Admin::user()->id){
                throw new \Exception('无权访问此地址');
            }
        }
        return Admin::content(function (Content $content) use ($id) {

            $content->header('讲师管理');
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

            $content->header('讲师管理');
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
            //$grid->disableFilter();
            $grid->disableRowSelector();
            $grid->disableCreation();
            $grid->disableExport();
            $grid->model()->orderBy('expid', 'asc');

            if(Auth::isLecturer()){
                $grid->model()->where('expid', '=', Admin::user()->id);

                $grid->actions(function ($actions) {
                    $actions->disableDelete();
                });
            }
            if(Auth::isAdministrator() || Auth::isManager()){
                $grid->actions(function ($actions) {
                    $actions->disableDelete();
                    $actions->disableEdit();
                    $actions->append('<a href="/admin/lecturer/users/'.$actions->getKey().'/edit?">编辑</a>');
                    $actions->append(' | ');
                    $actions->append('<a href="/admin/lecturer/publicity/create?id='.$actions->getKey().'">推荐</a>');
                });

            }


            $grid->expid('讲师ID');
            $grid->real_name('真实姓名');
            $grid->wx_name('微信昵称')->popover(['placement'=>'right','column'=>'wx_qrcode']);
            $grid->mp_name('公众号名称')->popover(['placement'=>'right','column'=>'mp_qrcode']);
            $grid->openid('OpenID');
            $grid->mobile('手机号');
            $grid->svc_type('服务方式')->display(function ($v) {
                return !empty(Expert::$svcTypeOptions[$v]) ? Expert::$svcTypeOptions[$v] : '';
            });
            $grid->column('full_price','费用(提问/查看)')->display(function () {
                return $this->price_ask.'/'.$this->price_see;
            });
            $grid->share_ratio('分成比例');



            if(Auth::isAdministrator()){
                $grid->state('状态')->editable('select', Expert::$stateOptions);
            }else{
                $grid->state('状态')->display(function ($v) {
                    return Expert::$stateOptions[$v];
                });
            }

//            $grid->wx_img_url('微信头像')->display(function ($name) {
//                return $name ? "<img width='80px' src='$name' />" : '';
//            });


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

            if(Auth::isAdministrator() || Auth::isManager()){
                //讲师信息
                $form->display('expid', '讲师ID');
                $form->text('real_name', '真实姓名')->rules('required|min:1');
                $form->text('wx_name', '微信昵称');
                $form->text('wx_img_url', '微信头像');
                //联系方式
                $form->text('mobile', '手机号')->rules('required|numeric|min:6');
                $form->text('qq', 'QQ');
                // $form->text('wx_qrcode', '微信二维码');
                $form->image('wx_qrcode','微信二维码');
                if(Auth::isAdministrator()){
                    $form->text('openid', 'openID');
                    $form->text('unionid', 'unionId');
//                    $form->text('openid_mini', 'openId mini');
                }else{
                    $form->display('openid', 'openID');
                    $form->display('unionid', 'unionId');
                }

                //公众号信息
                $form->text('mp_name', '公众号名称');
                $form->image('mp_img_url', '公众号图片');
                //$form->text('mp_qrcode', '公众号二维码');
                $form->image('mp_qrcode','公众号二维码');
                $form->text('mp_appid', '公众号AppId');
                $form->text('mp_secret', '公众号Secret');
                $form->radio('mp_auth', '是否认证')->options(Expert::$enableOptions);
                $form->text('mp_verify_file_url', 'js安全域名');//->rules('url');
                if(Auth::isAdministrator()){
                    $form->radio('state','状态')->options(Expert::$stateOptions);
                }


            }

            $form->editor('exp_intro', '讲师介绍');
            $form->image('exp_bg_url', '背景图')->rules('dimensions:min_width=100,min_height=200,max_width=500,max_height=1000',[
                'dimensions' => '图片有效长宽为：100x200至500x1000',
            ]);
            $form->text('price_ask', '提问费用')->rules('required|numeric|min:10|max:100');
            $form->text('price_see', '查看费用')->rules('required|numeric|min:10|max:100');
            $form->text('max_question', '每日提问上限')->rules('required|numeric|min:0');
            //服务方式
            $form->radio('svc_type','服务方式')->options(ExpertApplication::$svcTypeOptions);


        });
    }
}
