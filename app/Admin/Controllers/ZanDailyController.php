<?php

namespace App\Admin\Controllers;

use App\Models\ArticalDaily;
use App\Models\Auth;
use App\Models\ZanDaily;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Layout\Row;
use Encore\Admin\Widgets\Box;

class ZanDailyController extends Controller
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

            $content->header('收支列表');
            $content->description('');
            $this->setTodayStat($content);
            $content->body($this->grid());
        });
    }
    private function setTodayStat($content)
    {
        $content->row(function (Row $row) {
            $isLecturer = Auth::isLecturer();
            $where = ['type' => ArticalDaily::TYPE_RZ];
            $zanWhere = [];
            if ($isLecturer) {
                $zanWhere['vid'] = Admin::user()->id;
                $where['expid'] = $zanWhere['vid'];
            }
            //讲师总收入
            $fee_exp = ArticalDaily::where($where)->sum('fee_exp') / 100;

            //提现 trans
            $trans = ZanDaily::where($zanWhere)->sum('trans') / 100;

            if ($isLecturer) {
                $box = new Box('讲师收入', number_format($fee_exp, 2));
                $box->style('danger');
                //$box->solid();
                $row->column(6, $box);

                $box = new Box('已提现', number_format($trans, 2));
                $box->style('info');
                //$box->solid();
                $row->column(6, $box);
            } else {
                $where = ['type' => ArticalDaily::TYPE_RZ];
                $fee_plat = ArticalDaily::where($where)->sum('fee_plat') / 100;

                $box = new Box('平台收入', number_format($fee_plat, 2));
                $box->style('danger');
                $row->column(4, $box);

                $box = new Box('讲师收入', number_format($fee_exp, 2));
                $box->style('danger');
                //$box->solid();
                $row->column(4, $box);

                $box = new Box('已提现', number_format($trans, 2));
                $box->style('info');
                //$box->solid();
                $row->column(4, $box);
            }

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
        return Admin::grid(zanDaily::class, function (Grid $grid) {

            $isLecturer=Auth::isLecturer();
            $grid->disableRowSelector();
            $grid->disableExport();
            $grid->disableCreation();
            $grid->disableActions();
            $grid->model()->orderBy('timestamp', 'desc');
            if ($isLecturer) {
                $grid->model()->where(['vid'=>Admin::user()->id]);
            }
            $grid->column('expert.real_name','讲师')->style('width:80px;');
            $grid->fee('收益')->display(function($fee){
                return number_format($fee/100,2);
            })->style('width:80px;');
            $grid->trans('提现')->display(function($fee){
                return number_format($fee/100,2);
            })->style('width:80px;');
            $grid->timestamp('时间');
            $grid->filter(function($filter){
                // 如果过滤器太多，可以使用弹出模态框来显示过滤器.
                $filter->useModal();
                // 禁用id查询框
                $filter->disableIdFilter();
                if(!Auth::isLecturer()) {
                    $filter->like('expert.real_name', '讲师');
                }
                $filter->between('timestamp', '时间')->datetime();
            });
            if($isLecturer) {
                $grid->footer(function(){
                    echo view('admin.grid.total', ['total' => '[1]']);
                });
            }else{
                $grid->footer(function(){
                    echo view('admin.grid.total', ['total' => '[1,2]']);
                });
            }
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(zanDaily::class, function (Form $form) {

            $form->display('id', 'ID');

            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }
}
