<?php

namespace App\Admin\Controllers;

use App\Models\Auth;
use App\Models\LivebcSubs;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class LivebcSubsController extends Controller
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

            $content->header('订阅管理');
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
        return Admin::grid(LivebcSubs::class, function (Grid $grid) {

            $grid->id('ID')->sortable();

            $grid->model()->orderBy('timestamp', 'desc');
            $grid->trade_no('订单号');
            $grid->openid('OPENID');
            $grid->column('mpuser.nickname','订阅者昵称');
            $grid->column('expert.real_name','讲师姓名');
            $grid->fee('金额')->display(function ($fee) {
                return $fee/1;
            });
            $grid->timestamp('订阅时间');
            $grid->expires('到期时间');
            $grid->column('state','支付状态')->display(function ($state) {
                return LivebcSubs::getStateStr($state);
            });
            if(Auth::isAdministrator()){
                $grid->column('state_x','退款')->refund();
            }
            $grid->disableRowSelector();
            //disableExport
            $grid->disableExport();
            //disableCreation
            $grid->disableCreation();
            $grid->disableActions();
            $grid->filter(function($filter){
                // 如果过滤器太多，可以使用弹出模态框来显示过滤器.
                $filter->useModal();
                // 禁用id查询框
                $filter->disableIdFilter();
                $filter->equal('expires', '到期时间')->date();
                $filter->between('timestamp', '订阅时间')->datetime();
                $filter->like('mpuser.nickname', '订阅者昵称');
                $filter->in('state', '支付状态')->checkbox(LivebcSubs::STATE);

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
        return Admin::form(LivebcSubs::class, function (Form $form) {

            $form->display('id', 'ID');

            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }
}
