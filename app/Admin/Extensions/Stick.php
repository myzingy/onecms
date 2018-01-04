<?php

namespace App\Admin\Extensions;

use Encore\Admin\Admin;
use Encore\Admin\Grid\Displayers\AbstractDisplayer;

class Stick extends AbstractDisplayer
{
    const TYPE_YES=1;
    const TYPE_NO=0;
    /**
     * {@inheritdoc}
     */
    public function display()
    {
        $script = <<<SCRIPT
var flag=true;
$('.question-stick').unbind('click').click(function() {
    if(!flag) {
        toastr.warning('操作太快，请稍等一会');
        return;
    }
    flag=false;
    //toastr.success('操作成功');
    var id = $(this).data('id');
    var name=$.trim($(this).text());
    if(name=='已置顶'){
        $(this).text('未置顶').attr('class','btn btn-warning question-stick').attr('title','未置顶,点击置顶');        
    }else{
        $(this).text('已置顶').attr('class','btn btn-danger question-stick').attr('title','已置顶,点击取消');
    }
    $.ajax({
        method: 'post',
        url: '{$this->getResource()}/' + id+'/edit?act=stick',
        data: {
            _method:'get',
            _token:LA.token,
        },
        success: function (data) {
            flag=true;
            $.pjax.reload('#pjax-container');

            if (typeof data === 'object') {
                if (data.status) {
                    swal(data.message, '', 'success');
                } else {
                    swal(data.message, '', 'error');
                }
            }
        },
        error:function(){
            flag=true;    
        }
    });
});

SCRIPT;
        Admin::script($script);
        if($this->row->pinned_time
            && $this->row->pinned_time>$this->row->timestamp){//已经置顶
            $name='已置顶';
            $title='已置顶,点击取消';
            $class='danger';
        }else{
            $name='未置顶';
            $title='未置顶,点击置顶';
            $class='warning';
        }
        return <<<EOT
<button type="button"
    class="btn btn-$class question-stick"
    title="$title" data-id="{$this->getKey()}">
    $name
</button>

EOT;
    }
}
