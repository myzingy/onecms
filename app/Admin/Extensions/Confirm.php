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
            $.ajax({
                method: 'post',
                url: '{$this->url}',
                data: {
                    _method:'get',
                    _token:LA.token,
                },
                success: function (data) {
                    $.pjax.reload('#pjax-container');
    
                    if (typeof data === 'object') {
                        if (data.status) {
                            swal(data.message, '', 'success');
                        } else {
                            swal(data.message, '', 'error');
                        }
                    }
                },
                error: function(x, e) {
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