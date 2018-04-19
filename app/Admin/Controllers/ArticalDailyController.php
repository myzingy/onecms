<?php

namespace App\Admin\Controllers;

use App\Models\ArticalDaily;

use App\Models\Auth;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Layout\Row;
use Encore\Admin\Widgets\Box;

class ArticalDailyController extends Controller
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
            if ($isLecturer) {
                $where['expid'] = Admin::user()->id;
            }
            $fee[ArticalDaily::TYPE_RZ] = ArticalDaily::where($where)->sum('fee_exp') / 100;
            $where = ['type' => ArticalDaily::TYPE_TX];
            if ($isLecturer) {
                $where['expid'] = Admin::user()->id;
            }
            $fee[ArticalDaily::TYPE_TX] = ArticalDaily::where($where)->sum('fee_exp') / 100;
            if ($isLecturer) {
                $fee[ArticalDaily::TYPE_RZ] = $fee[ArticalDaily::TYPE_RZ];
                $box = new Box('打赏收入', number_format($fee[ArticalDaily::TYPE_RZ], 2));
                $box->style('danger');
                //$box->solid();
                $row->column(4, $box);

                $box = new Box('已提现', number_format($fee[ArticalDaily::TYPE_TX], 2));
                $box->style('info');
                //$box->solid();
                $row->column(4, $box);

                $box = new Box('未提现', number_format($fee[ArticalDaily::TYPE_RZ] - $fee[ArticalDaily::TYPE_TX], 2));
                $box->style('success');

                //$box->solid();
                $max_fee = $fee[ArticalDaily::TYPE_RZ] - $fee[ArticalDaily::TYPE_TX];
                $box->content(number_format($max_fee, 2)
                    . ' '
                    . (($isLecturer && $max_fee > 0) ? new Takenow($max_fee > 19999 ? 19999 : $max_fee) : '')
                );
                $row->column(4, $box);
            } else {
                $where = ['type' => ArticalDaily::TYPE_RZ];
                $fee_plat = ArticalDaily::where($where)->sum('fee_plat') / 100;

                $box = new Box('平台收入', number_format($fee_plat, 2));
                $box->style('danger');
                $row->column(3, $box);

                $box = new Box('讲师收入', number_format($fee[ArticalDaily::TYPE_RZ], 2));
                $box->style('info');
                $row->column(3, $box);

                $box = new Box('已提现', number_format($fee[ArticalDaily::TYPE_TX], 2));
                $box->style('success');
                $row->column(3, $box);

                $box = new Box('未提现', number_format($fee[ArticalDaily::TYPE_RZ] - $fee[ArticalDaily::TYPE_TX], 2));
                $box->style('primary');
                $row->column(3, $box);
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
        return Admin::grid(ArticalDaily::class, function (Grid $grid) {
            $isLecturer=Auth::isLecturer();
            $grid->disableRowSelector();
            $grid->disableExport();
            $grid->disableCreation();
            $grid->disableActions();

            $grid->column('expert.real_name','讲师')->style('width:80px;');
            $grid->fee_exp('讲师收益')->display(function($fee){
                return number_format($fee/100,2);
            })->style('width:80px;');
            if(!$isLecturer){
                $grid->fee_plat('平台收益')->display(function($fee){
                    return number_format($fee/100,2);
                })->style('width:80px;');
            }
            $grid->type('类型')->display(function($type){
                return ArticalDaily::getTypeStr($type);
            });
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
                $filter->in('type', '类型')->checkbox(ArticalDaily::TYPE);
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
        return Admin::form(ArticalDaily::class, function (Form $form) {

            $form->display('id', 'ID');

            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }
}
