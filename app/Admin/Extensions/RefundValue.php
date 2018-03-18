<?php

namespace App\Admin\Extensions;

use App\Models\Paylog;
use Encore\Admin\Admin;
use Encore\Admin\Grid\Displayers\AbstractDisplayer;

class RefundValue extends AbstractDisplayer
{
    const TYPE_YES=1;
    const TYPE_NO=0;

    /**
     * {@inheritdoc}
     */
    public function display()
    {
        if($this->row->state!=Paylog::STATE_YZF
            || (strtotime($this->row->timestamp)+(86400*$this->row->days))<time() ) return "";
        $feeDay=$this->row->fee/$this->row->days;
        $lastDays=$this->row->days-ceil((time()-strtotime($this->row->timestamp))/86400);
        $this->defRefundFee=(int)($lastDays*$feeDay);
        $this->maxRefundFee=$this->row->fee-$this->row->refund_fee;
        $script = <<<SCRIPT

$('.paylog-refund').unbind('click').click(function() {
    var id = $(this).data('id');
    var defRefundFee = $(this).data('defRefundFee');
    var maxRefundFee = $(this).data('maxRefundFee');
    var ajax=true;
    swal({
            type: "input",
            title: '输入退款金额',  
            input: 'text',  
            inputPlaceholder: '输入退款金额',  
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "退 款",
            closeOnConfirm: false,
            cancelButtonText: "取 消",
            showCancelButton: true,
            inputValue:$(this).data('defRefundFee'),
        },
        function(inputValue){
            if(!ajax) return;
            ajax=false;
            if (inputValue === false) return false;
            if (inputValue === "") {
                swal.showInputError("请输入退款金额!");
                return false
            }
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
    title="点击退款" data-id="{$this->row->trade_no}" 
    data-def-refund-fee="{$this->defRefundFee}"
    data-max-refund-fee="{$this->maxRefundFee}">
    退款
</button>

EOT;
    }
}
