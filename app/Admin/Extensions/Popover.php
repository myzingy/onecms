<?php

namespace App\Admin\Extensions;

use Encore\Admin\Admin;
use Encore\Admin\Grid\Displayers\AbstractDisplayer;

class Popover extends AbstractDisplayer
{
    public function display($param = [])
    {
        $column = $param['column'];
        $placement = $param['placement'];
        Admin::script("$('[data-toggle=\"popover\"]').popover({html: true});");

        $image = $this->row[$column] ? "<image width=120 height=120 src='".$this->row[$column]."' />" : "";
        $title = '微信二维码';
        if($column=='mp_qrcode'){
            $title = '公众号二维码';
        }

        if(!$image) return $this->value;
        return <<<EOT
<button type="button"
    class="btn btn-secondary"
    title="{$title}"
    data-container="body"
    data-toggle="popover"
    data-placement="$placement"
    data-content="{$image}"
    >
  {$this->value}
</button>

EOT;

    }
}