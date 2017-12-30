<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\Confirm;
use App\Models\Auth;
use App\Models\Expert;
use App\Models\Paylog;
use App\Models\Question;

use Encore\Admin\Auth\Database\Role;
use Encore\Admin\Auth\Permission;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Support\Facades\Input;
use Encore\Admin\Widgets\InfoBox;

class QuestionController extends Controller
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

            $content->header('问答管理');
            $content->description('');

            $content->body('<style>.table.table-hover{background-color: #ebebeb;}</style>'.$this->grid());
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
        //refuse
        if($act=='refuse'){//拒绝
            $m=$this->form()->edit($id)->model();
            $m->state=Question::STATE_YJJ;
            $m->save();
            return redirect('/admin/question');
        }elseif ($act=='stick'){//置顶
            $m=$this->form()->edit($id)->model();
            $m->pinned_time=$m->pinned_time>$m->timestamp?$m->timestamp:date('Y-m-d H:i:s',time());
            $m->save();
            return redirect('/admin/question');
        }elseif ($act=='answer'){
//            $m=$this->form()->edit($id)->model();
//            if($m->expid!=Admin::user()->id){
//                throw new \Exception('只允许讲师本人回复');
//            }
            return Admin::content(function (Content $content) use ($id) {

                $content->header('讲师解答');
                $content->description('');

                $content->body($this->answer()->edit($id));
            });
        }else{
            return Admin::content(function (Content $content) use ($id) {

                $content->header('新建问题');
                $content->description('');

                $content->body($this->form()->edit($id));
            });
        }

    }

    /**
     * Create interface.
     *
     * @return Content
     */
    public function create()
    {
        return Admin::content(function (Content $content) {

            $content->header('新建问题');
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
        return Admin::grid(Question::class, function (Grid $grid) {
            if(Admin::user()->isRole('lecturer')){
                $grid->model()->where('expid', '=', Admin::user()->id);
            }else{
                //disableCreation
                $grid->disableCreation();
            }
            $grid->model()->with(['expert','paylog']);
            $grid->model()->orderBy('pinned_time', 'desc');
            $grid->model()->orderBy('timestamp', 'desc');
            $grid->qid('ID')->sortable();
            $grid->asker_img_url('头像')->display(function($img){
                return '<img style="width:50px;height:50px;" src="'.$img.'"/>';
            });
            $grid->asker_name('提问者');
            $grid->timestamp('时间')->sortable();
            $grid->question('问题')->display(function($question){
                return '<div style="width: 250px">'.$question.'</div>';
            });
            $grid->answer('答案')->display(function($answer){
                return '<div style="width: 250px">'.$answer.'</div>';
            });
            $grid->ispub('公开提问？')->display(function($ispub){
                return Question::getIspubStr($ispub);
            });
            $grid->column('paylog.state','支付状态')->display(function ($state) {
                return Paylog::getStateStr($state);
            });
            $grid->column('expert.real_name','讲师');
            $grid->column('state','问题状态')->display(function ($state) {
                return Question::getStateStr($state);
            });
            $grid->pinned_time('置顶')->stick();

            $grid->disableRowSelector();
            //disableExport
            $grid->disableExport();


            $grid->actions(function ($actions) {
                if(!Admin::user()->isRole('administrator') && !Admin::user()->isRole('manager')) {
                    $actions->disableDelete();
                }
                $actions->disableEdit();
                // append一个操作
                if($actions->row->state==Question::STATE_WHD){
                    $actions->prepend(new Confirm($actions->getKey(),'拒 绝','确认拒绝答复？','/admin/question/'.$actions->getKey().'/edit?act=refuse'));
                    $actions->prepend(' | ');
                    $actions->prepend('<a href="/admin/question/'.$actions->getKey().'/edit?act=answer">回 答</a>');

                }else if($actions->row->state==Question::STATE_YHD){
                    $actions->prepend('<a href="/admin/question/'.$actions->getKey().'/edit?act=answer">修改答案</a>&nbsp;&nbsp;');
                }

            });
            // filter($callback)方法用来设置表格的简单搜索框
            $grid->filter(function($filter){
                // 如果过滤器太多，可以使用弹出模态框来显示过滤器.
                $filter->useModal();
                // 禁用id查询框
                $filter->disableIdFilter();
                //$filter->equal('timestamp', '时间')->date();
                //$filter->between('timestamp', '时间')->datetime();
                $filter->where(function ($query) {
                    $input = $this->input;
                    $query->where('asker_name', 'like', "%{$input}%");
                }, '提问者姓名');
                $filter->where(function ($query) {
                    $input = $this->input;
                    $query->where('question', 'like', "%{$input}%");
                }, '问题');
                // 关系查询，查询对应关系`profile`的字段
                $filter->where(function ($query) {
                    $input = $this->input;
                    $query->whereHas('expert', function ($query) use ($input) {
                        $query->where('real_name', 'like', "%{$input}%");
                    });
                }, '讲师姓名');
                //$filter->equal('state', '支付状态')->checkbox(Paylog::STATE);
                $filter->equal('state', '问题状态')->checkbox(Question::STATE);

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
        return Admin::form(Question::class, function (Form $form) {
            //\Log::info('form:'.json_encode($form));
            $form->text('question', '问题');
            $form->editor('answer', '回复');
            $form->saved(function (Form $form) {
                //\Log::info('form-saved:'.$form->model()->asker_openid);
                if(!$form->model()->asker_openid){
                    $expert=Expert::find(Admin::user()->id);
                    $form->model()->timestamp=date('Y-m-d H:i:s',time());
                    $form->model()->asker_name=$expert->wx_name;
                    $form->model()->asker_img_url=$expert->wx_img_url;
                    $form->model()->expid=$expert->expid;
                    $form->model()->ispub=Question::ISPUB_YES;
                    $form->model()->state=Question::STATE_WHD;
                    $form->model()->asker_openid=$expert->openid;
                    if($form->answer){
                        $form->model()->state=Question::STATE_YHD;
                    }
                    $form->model()->save();

                }
                if($form->model()->answer){
                    $form->model()->state=Question::STATE_YHD;
                    $form->model()->save();
                }
            });
        });
    }
    function answer()
    {
        return Admin::form(Question::class, function (Form $form) {
            $form->text('question', '问题');
            $form->editor('answer', '回复');
            \Log::info('answer:'.json_encode($form));
            $form->saved(function (Form $form) {
                \Log::info('answer-saved:'.json_encode($form));
            });
        });

    }
}
