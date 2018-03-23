<?php

namespace App\Admin\Extensions;

use Encore\Admin\Admin;

class Takenow
{
    public function __construct($defRefundFee)
    {
        $this->defRefundFee=$defRefundFee;
    }

    /**
     * {@inheritdoc}
     */
    public function script()
    {

        $script = <<<SCRIPT
        var flag=true;
$('.tixian').unbind('click').click(function() {
    
    var defRefundFee = {$this->defRefundFee};
    var maxRefundFee = 19999;
    var ajax=true;

    swal({
          type: "input",
            title: '输入提现金额',  
            input: 'text',  
            inputPlaceholder: '输入提现金额',  
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "提 现",
            closeOnConfirm: false,
            cancelButtonText: "取 消",
            showCancelButton: true,
            inputValue:defRefundFee,
        },
        function(inputValue){
            if (inputValue === false) return false;
            if (inputValue === "") {
                swal.showInputError("请输入提现金额!");
                return false
            }
            if (!/^[1-9][0-9]*$/.test(inputValue)) {
                swal.showInputError("提现金额必须是整数!");
                return true
            }
            if (inputValue <1 || inputValue>maxRefundFee) {
                swal.showInputError("提现金额在 1-"+maxRefundFee+" 之间!");
                return true
            }
            if(!ajax) return;
            ajax=false;
            $.ajax({
                method: 'post',
                url: '{$this->getResource()}/1/edit?act=trans&fee='+inputValue,
                data: {
                    _method:'get',
                    _token:LA.token,
                },
                success: function (data) {
                    flag=true;
                    $.pjax.reload('#pjax-container');
                    swal('操作成功', '', 'success');
                },
                error: function(x, e) {
                    flag=true;
                    if (x.status == 500) {
                        swal(x.responseJSON.message, '', 'error');
                    }
                },
            });
        });
    });

SCRIPT;
        return $script;
    }
    protected function render()
    {
        Admin::script($this->script());

        return "<button class=\"btn btn-primary tixian\" type=\"submit\">我要提现</button>";
    }

    public function __toString()
    {
        return $this->render();
    }
    public function getResource($path = null)
    {
        if (!empty($path)) {
            $this->resourcePath = $path;

            return $this;
        }

        if (!empty($this->resourcePath)) {
            return $this->resourcePath;
        }

        return app('request')->getPathInfo();
    }
}