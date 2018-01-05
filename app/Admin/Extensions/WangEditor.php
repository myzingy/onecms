<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/29 0029
 * Time: 下午 9:29
 */
namespace App\Admin\Extensions;

use Encore\Admin\Form\Field;

class WangEditor extends Field
{
    protected $view = 'admin.wang-editor';

    protected static $css = [
        '/vendor/wangEditor-3.0.9/release/wangEditor.min.css',
    ];

    protected static $js = [
        '/vendor/wangEditor-3.0.9/release/wangEditor.min.js',
    ];

    public function render()
    {
        $name = $this->formatName($this->column);
        $uploadImgUrl = config('wang-editor.uploadImgUrl', '/admin/upload');
        $token = csrf_token();
        $this->script = <<<EOT

var E = window.wangEditor
var editor = new E('#{$this->id}');
editor.customConfig.zIndex = 0
//editor.customConfig.uploadImgShowBase64 = true
editor.customConfig.uploadImgServer = "{$uploadImgUrl}";
editor.customConfig.uploadImgMaxSize = 10 * 1024 * 1024
editor.customConfig.uploadImgMaxLength = 1
editor.customConfig.uploadImgParams = {
        _token : '{$token}'
};
editor.customConfig.uploadImgHeaders = {
    'Accept': 'text/x-json'
}
editor.customConfig.uploadFileName = 'file';

editor.customConfig.onchange = function (html) {
    $('input[name=$name]').val(html);
}
editor.customConfig.customAlert = function (info) {
    //swal(info, '', 'warning');
}
editor.customConfig.uploadImgHooks = {
    fail: function (xhr, editor, result) {
        console.log('fail',arguments);
        swal(result.message, '', 'warning');
    },
    error: function (xhr, editor) {
        console.log('error',arguments);
        swal(result.message, '', 'warning');
    },
}
editor.create()

EOT;
        return parent::render();
    }
}