<?php

/**
 * Laravel-admin - admin builder based on Laravel.
 * @author z-song <https://github.com/z-song>
 *
 * Bootstraper for Admin.
 *
 * Here you can remove builtin form field:
 * Encore\Admin\Form::forget(['map', 'editor']);
 *
 * Or extend custom form field:
 * Encore\Admin\Form::extend('php', PHPEditor::class);
 *
 * Or require js and css assets:
 * Admin::css('/packages/prettydocs/css/styles.css');
 * Admin::js('/packages/prettydocs/js/main.js');
 *
 */
use App\Admin\Extensions\WangEditor;
use Encore\Admin\Form;
use Encore\Admin\Grid\Column;
use App\Admin\Extensions\Popover;

Encore\Admin\Form::forget(['map']);
app('view')->prependNamespace('admin', resource_path('views/admin'));
Admin::js('/vendor/echarts/echarts.common.min.js');
Admin::js('/vendor/echarts/shine.js');
//Form::extend('wangeditor', WangEditor::class);
//Column::extend('popover', Popover::class);
//<script>
//// 第二个参数可以指定前面引入的主题
//var chart = echarts.init(document.getElementById('main'), 'shine');
//chart.setOption({
//    ...
//});
//</script>