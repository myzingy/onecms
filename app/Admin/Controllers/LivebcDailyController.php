<?php

namespace App\Admin\Controllers;

use App\Admin\Extensions\Confirm;
use App\Admin\Extensions\RefundValue;
use App\Admin\Extensions\Takenow;
use App\Models\Auth;
use App\Models\LivebcDaily;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Layout\Row;
use Encore\Admin\Widgets\Box;
use Encore\Admin\Widgets\InfoBox;

class LivebcDailyController extends Controller
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

            $content->header('收支管理');
            $content->description('');
            $this->setTodayStat($content);
            $content->body($this->grid());
        });
    }
    private function setTodayStat($content){

        $content->row(function (Row $row) {
            $fee[LivebcDaily::TYPE_RZ]=LivebcDaily::where(['type'=>LivebcDaily::TYPE_RZ])->sum('fee');
            $fee[LivebcDaily::TYPE_TX]=LivebcDaily::where(['type'=>LivebcDaily::TYPE_TX])->sum('fee');
            $box = new Box('直播收入', number_format($fee[LivebcDaily::TYPE_RZ],2));
            $box->style('danger');
            //$box->solid();
            $row->column(4, $box);

            $box = new Box('已提现', number_format($fee[LivebcDaily::TYPE_TX],2));
            $box->style('info');
            //$box->solid();
            $row->column(4, $box);

            $box = new Box('未提现', number_format($fee[LivebcDaily::TYPE_RZ]-$fee[LivebcDaily::TYPE_TX],2));
            $box->style('success');
            //$box->solid();
            $box->content(number_format($fee[LivebcDaily::TYPE_RZ]-$fee[LivebcDaily::TYPE_TX],2)
                .' '
                .new Takenow(1)
            );
            $row->column(4, $box);
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
        return Admin::grid(LivebcDaily::class, function (Grid $grid) {

            //$grid->id('ID')->sortable();

            $grid->model()->orderBy('timestamp', 'desc');
            $grid->column('expert.real_name','讲师姓名');
            $grid->timestamp('操作时间');
            $grid->fee('金额')->display(function ($fee) {
                return $fee/1;
            });
            //$grid->expires('到期时间');
            $grid->column('type','操作类型')->display(function ($state) {
                return LivebcDaily::getTypeStr($state);
            });
            if(Auth::isAdministrator()){
                //$grid->column('state_x','退款')->refund();
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
                //$filter->equal('expires', '到期时间')->date();
                $filter->between('timestamp', '操作时间')->datetime();
                $filter->like('expert.real_name', '讲师姓名');
                $filter->in('type', '操作类型')->checkbox(LivebcDaily::TYPE);

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
        return Admin::form(LivebcDaily::class, function (Form $form) {

            $form->display('id', 'ID');

            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }
}
