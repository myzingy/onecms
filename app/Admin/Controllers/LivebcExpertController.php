<?php

namespace App\Admin\Controllers;

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
                        'fee_bc'=>0,
                        'state'=>LivebcExpert::STATE_ENABLE
                    ]);
                    $LivebcExpert->save();
                }
                $grid->model()->where('expid', '=', Admin::user()->id);
            }
            $grid->model()->with(['expert']);
            $grid->column('expert.real_name','讲师');
            $grid->column('name','直播名称');
            $grid->column('fee_bc','直播价格');
            $grid->column('notice','直播公告')->style('max-width:600px;');
            $grid->state('状态')->select(LivebcExpert::STATE)->style('width:120px;');
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

            $form->display('expid', '讲师ID');

            $form->text('name', '直播名称');
            $form->textarea('notice', '直播公告')->rows(10);
            //$form->radio('fee_type','是否收费')->options(['0' => '免费直播', '1'=> '收费直播'])->default('0');
            $form->currency('fee_bc', '直播价格')->symbol('￥');
            $form->radio('state','是否直播')->options(LivebcExpert::STATE);
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
    
});
</script>
JSEND;

            $form->html($js);
        });
    }
}
