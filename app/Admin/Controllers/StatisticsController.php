<?php
namespace App\Admin\Controllers;

use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

class StatisticsController extends Controller
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

            $content->header('header');
            $content->description('description');

            $content->body('...');
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


        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {

    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {

    }
    public function platform(){
        return Admin::content(function (Content $content) {
            $content->header('header');
            $content->description('description');
            $content->body(view('admin.echarts.bar'));
        });
    }
    public function lecturer(){
        return Admin::content(function (Content $content) {

            $content->header('header');
            $content->description('description');
            $content->body(view('admin.echarts.bar'));
        });
    }

}
