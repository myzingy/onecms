<?php

namespace App\Admin\Controllers;

use App\Models\ArticalNotes;

use App\Models\Auth;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;

class ArticalNotesController extends Controller
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

            $content->header('评论管理');
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
        $act=Input::get('act','');
        if ($act=='stick'){//置顶
            $m=$this->form()->edit($id)->model();
            $m->pinnedtime=$m->pinnedtime>$m->timestamp?$m->timestamp:date('Y-m-d H:i:s',time()+86400*30);
            $m->save();
            return $m;
        }
        return Admin::content(function (Content $content) use ($id) {

            $content->header('编辑评论');
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
        return Admin::grid(ArticalNotes::class, function (Grid $grid) {
            $grid->disableRowSelector();
            $grid->disableExport();
            $grid->disableCreation();

            $grid->id('ID')->sortable()->style('width:60px;');
            $where=[];
            $artid=Input::get('artid');
            $title=Input::get('artical.title');
            if($artid && !$title){
                $where['artid']=$artid;
            }else{
                if(Auth::isLecturer()){
                    //$where=DB::raw(' artid in (select id from artical where expid='.Admin::user()->id.') and 1 ');
                    //$where['artid']=['in'=>DB::raw('select *')];
                    $where['artical.expid']=Admin::user()->id;
                    $grid->model()
                        ->select('artical_notes.*')
                        ->join('artical',function($join){
                        $join->on('artical.id','=','artid');
                    });
                }
            }

            $grid->model()->where($where);
            $grid->column('artical.title','文章标题');
            $grid->column('mpuser.nickname','评论者')->style('width:80px;');
            $grid->column('content','评论内容');
            $grid->column('reply','回复内容');
            $grid->column('statNote','评论点赞')->display(function(){
                return $this->statNote();
            })->style('width:80px;');
            $grid->column('statReply','回复点赞')->display(function(){
                return $this->statReply();
            })->style('width:80px;');
            $grid->column('timestamp','评论时间')->style('width:120px;');
            $grid->pinnedtime('置顶')->stick()->sortable()->style('width:80px;');
            $grid->filter(function($filter){
                // 如果过滤器太多，可以使用弹出模态框来显示过滤器.
                $filter->useModal();
                // 禁用id查询框
                $filter->disableIdFilter();
                $filter->like('artical.title', '文章标题');
                $filter->like('content', '评论内容');
                $filter->like('reply', '回复内容');
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
        return Admin::form(ArticalNotes::class, function (Form $form) {

            $form->ignore(['reply']);
            $form->display('artical.title', '文章标题');
            $form->text('content','评论内容')->rules('required|max:1024');
            $form->editor('reply','评论内容')->rules('max:1024');
            $form->saving(function (Form $form){
                $reply=Input::get('reply');
                $reply=preg_replace("/<[^>]+>/","",$reply);
                $reply=trim($reply);
                if($reply){
                    $form->model()->reply=$reply;
                    if(!$form->model()->replytime){
                        $form->model()->replytime=date('Y-m-d H:i:s',time());
                    }
                }
            });
        });
    }
}
