<?php

namespace App\Admin\Extensions;

use Encore\Admin\Admin;

class Confirm
{
    protected $id,$name,$title,$url;
    public function __construct($id,$name='',$title='',$url='')
    {
        $this->id = $id;
        $this->name=$name;
        $this->title=$title;
        $this->url=$url;
    }

    /**
     * {@inheritdoc}
     */
    public function script()
    {
        $deleteConfirm = $this->title;
        $confirm = trans('admin.confirm');
        $cancel = trans('admin.cancel');

        $script = <<<SCRIPT
        var flag=true;
$('.grid-row-confirm').unbind('click').click(function() {
    
    var id = $(this).data('id');

    swal({
          title: "$deleteConfirm",
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#DD6B55",
          confirmButtonText: "$confirm",
          closeOnConfirm: false,
          cancelButtonText: "$cancel"
        },
        function(){
            if(!flag) {
                toastr.warning('操作太快，请稍等一会');
                return;
            }
            flag=false;
            $.ajax({
                method: 'post',
                url: '{$this->url}'.replace('{id}',id),
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

        return "<a class='grid-row-confirm' data-id='{$this->id}'>{$this->name}</a>";
    }

    public function __toString()
    {
        return $this->render();
    }
}