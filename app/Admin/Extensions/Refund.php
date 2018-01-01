<?php

namespace App\Admin\Extensions;

use App\Models\Paylog;
use Encore\Admin\Admin;
use Encore\Admin\Grid\Displayers\AbstractDisplayer;

class Refund extends AbstractDisplayer
{
    const TYPE_YES=1;
    const TYPE_NO=0;
    /**
     * {@inheritdoc}
     */
    public function display()
    {
        if($this->row->state!=Paylog::STATE_YZF) return "";
        $script = <<<SCRIPT

$('.paylog-refund').unbind('click').click(function() {
    var id = $(this).data('id');
    var ajax=true;
    swal({
          title: "确认退款吗",
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#DD6B55",
          confirmButtonText: "退 款",
          closeOnConfirm: false,
          cancelButtonText: "取 消"
        },
        function(){
            if(!ajax) return;
             ajax=false;
            $.ajax({
                method: 'post',
                url: '{$this->getResource()}/' + id+'/edit?act=refund',
                data: {
                    _method:'get',
                    _token:LA.token,
                },
                success: function (data) {
                    $.pjax.reload('#pjax-container');
                    swal('操作成功', '', 'success');
                },
                error: function(x, e) {
                    if (x.status == 500) {
                        swal(x.responseJSON.message, '', 'error');
                    }
                },
            });
        }
    );
});

SCRIPT;
        Admin::script($script);

        return <<<EOT
<button type="button"
    class="btn btn-danger paylog-refund"
    title="点击退款" data-id="{$this->getKey()}">
    退款
</button>

EOT;
    }
}
