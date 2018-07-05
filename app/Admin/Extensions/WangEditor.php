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
        '/vendor/wangEditor-3.1.1/release/wangEditor.min.css',
    ];

    protected static $js = [
        '/vendor/wangEditor-3.1.1/release/wangEditor.min.js',
    ];

    public function render()
    {
        $name = $this->formatName($this->column);
        $uploadImgUrl = config('wang-editor.uploadImgUrl', '/admin/upload');
        $token = csrf_token();
        $table=$this->form->model()->getTable();
        $menu="";
        if('artical_notes'==$table){
            $menu=<<<END
editor.customConfig.menus = ['emoticon'];
END;

        }else{
            $menu=<<<END
editor.customConfig.menus = [
    'head',  // 标题
    'bold',  // 粗体
    'fontSize',  // 字号
    'fontName',  // 字体
    'italic',  // 斜体
    'underline',  // 下划线
    'strikeThrough',  // 删除线
    'foreColor',  // 文字颜色
    'backColor',  // 背景颜色
    'link',  // 插入链接
    'list',  // 列表
    'justify',  // 对齐方式
    'quote',  // 引用
    'emoticon',  // 表情
    'image',  // 插入图片
    'undo',  // 撤销
    'redo'  // 重复
];
END;
        }
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
{$menu}
editor.create()

EOT;
        return parent::render();
    }
}