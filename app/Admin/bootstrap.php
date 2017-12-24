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

Encore\Admin\Form::forget(['map', 'editor']);
app('view')->prependNamespace('admin', resource_path('views/admin/views'));
Admin::js('/vendor/echart/echarts.common.min.js');
Admin::js('/vendor/echart/shine.js');
//<script>
//// 第二个参数可以指定前面引入的主题
//var chart = echarts.init(document.getElementById('main'), 'shine');
//chart.setOption({
//    ...
//});
//</script>