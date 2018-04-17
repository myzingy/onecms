<?php

namespace App\Admin\Controllers;

use App\Models\Artical;

use App\Models\Auth;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Support\Facades\Input;

class ArticalController extends Controller
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

            $content->header('文章管理');
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

            $content->header('编辑文章');
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

            $content->header('发布文章');
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
        return Admin::grid(Artical::class, function (Grid $grid) {

            $grid->disableRowSelector();
            $grid->disableExport();

            $grid->id('ID')->sortable();
            $grid->title('标题');
            $grid->timestamp('发布时间');

            //$grid->expid('讲师ID')->sortable();
            $grid->column('expert.real_name','讲师姓名');
            //$grid->url('阅读原文');

            $grid->column('countViews','阅读量')->display(function(){
                return $this->countViews();
            });
            $grid->column('countStats','点赞数')->display(function(){
                return $this->countStats();
            });
            $grid->column('countNotes','评论数')->display(function(){
                $nums=$this->countNotes();
                if($nums>0){
                    $nums.= ' <a href="/admin/articalNotes?artid='.$this->id.'">查看</a>';
                }
                return $nums;
            });
            $grid->column('countFee','打赏收入')->display(function(){
                return $this->countFee();
            });

            $grid->filter(function($filter){
                // 如果过滤器太多，可以使用弹出模态框来显示过滤器.
                $filter->useModal();
                // 禁用id查询框
                $filter->disableIdFilter();
                $filter->like('expert.real_name', '讲师姓名');
                $filter->like('title', '标题');
                $filter->like('content', '内容');
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
        return Admin::form(Artical::class, function (Form $form) {

            $form->hidden('id');
            $form->hidden('expid')->default(Admin::user()->id);
            $form->text('title','文章标题')->rules('required|max:80');
            $form->editor('content', '文章内容');
            $form->text('url', '查看原文')->rules(function (Form $form){
                $url=Input::get('url');
                if(!preg_match("/^http(s)?:\/\/.{4,}/",$url)){
                    throw new \Exception('查看原文链接地址错误');
                }
                return false;
            })
                ->placeholder('请输入查看原文链接地址')
                ->help('http://www.baidu.com/index.html 必须带http或https');
            $form->saving(function (Form $form){
                $form->model()->timestamp=date('Y-m-d H:i:s',time());
            });
        });
    }
}
