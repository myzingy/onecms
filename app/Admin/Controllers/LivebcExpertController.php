<?php

namespace App\Admin\Controllers;

use App\Models\Auth;
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
                        'fee_bc'=>100,
                        'state'=>LivebcExpert::STATE_ENABLE,
                        'discount'=>0
                    ]);
                    $LivebcExpert->save();
                }
                $grid->model()->where('expid', '=', Admin::user()->id);
                $grid->disableFilter();
                $grid->disablePagination();
            }
            $grid->model()->with(['expert']);
            $grid->column('expert.real_name','讲师');
            $grid->column('name','直播名称')->display(function($name){
                if(Admin::user()->isRole('administrator')) {
                    return "<a href=\"/admin/livebcAdmin?expid={$this->expid}\">{$name} (进入直播)</a>";
                }
                return $name;
            });
            $grid->column('fee_bc','直播价格')->display(function($fee_bc){
                return $this->discount==0?'免费':"{$fee_bc} ($this->discount 折)";
            });
            $grid->column('notice','直播公告')->style('max-width:600px;');
            if(!Auth::isLecturer()){
                $grid->enable('状态')->select(LivebcExpert::STATE)->style('width:120px;');
            }
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

            $form->ignore(['fee_type']);
            $form->display('expid', '讲师ID');

            $form->text('name', '直播名称');
            $form->textarea('notice', '直播公告')->rows(10);
            $form->radio('fee_type','是否收费')
                ->options(['免费直播','收费直播'])->default(function() use ($form){
                    $feeType=$form->model()->discount<1?0:1;
                    if($feeType==0){
                        echo('<script>$(function(){$(\'input[name="fee_bc"]\').parents(\'.form-group\').hide();$(\'input[name="discount"]\').parents(\'.form-group\').hide();});</script>');
                    }else{
                        echo('<script>$(function(){$(\'input[name="fee_type"]\').parents(\'.form-group\').hide();});</script>');
                    }
                    return $feeType;
                });

            //$form->currency('fee_bc', '直播价格')->symbol('￥')->rules('integer|digits_between:100,1000',[
            $form->text('fee_bc', '直播价格')->rules('integer|min:100|max:1000',[
                'integer'=>'请输入整数',
                'min'=>'价格必须在100-1000之间',
                'max'=>'价格必须在100-1000之间'
            ]);
            $form->slider('discount', '折 扣')
                ->options(['max' => 100, 'min' => 10, 'step' => 5, 'postfix' => ' 折'])
                ->default(10);
            //$form->radio('state','是否直播')->options(LivebcExpert::STATE);
            $form->display('expid', '直播地址')->with(function ($expid) {
                return '<div style="width: 100%;word-break: break-all;">'
                    .'https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx95a4d6b085cd926a&redirect_uri=http%3A//dv.cnfol.com/expert/livebc/'
                    .$expid
                    .'&response_type=code&scope=snsapi_userinfo&state=0#wechat_redirect'
                    .'<a href="#"></a></div>';
            });
            $js=<<<JSEND
<script>
$(function(){
    setTimeout(function(){
        function ihAction(ih){
            var val=ih.prev().val();
            if(val==1){
                //var xv=$('input[name="discount"]').val();
                //$('input[name="discount"]').val(xv>0?xv:1);
                $('input[name="fee_bc"]').parents('.form-group').show();
                $('input[name="discount"]').parents('.form-group').show();        
            }else{
                $('input[name="discount"]').val(0);
                $('input[name="fee_bc"]').parents('.form-group').hide();
                $('input[name="discount"]').parents('.form-group').hide();    
            }  
        };
        $('input[name="fee_type"]').parents('.radio-inline').click(function(){
            var ih=$(this).find('.iCheck-helper');
            ihAction(ih);
        });
        $('input[name="fee_type"]').next().click(function(){
            var ih=$(this);
            ihAction(ih);
        });   
    },100);  
});
</script>
JSEND;

            $form->html($js);
            $form->saved(function(Form $form){
                $form->model()->discount=$form->discount<=10?$form->discount*10:$form->discount;
                $form->model()->type=$form->model()->discount>0?1:0;
                $form->model()->save();
            });
        });
    }
}
