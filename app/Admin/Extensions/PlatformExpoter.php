<?php

namespace App\Admin\Extensions;

use App\Models\Daily;
use Encore\Admin\Grid\Exporters\AbstractExporter;
use Maatwebsite\Excel\Facades\Excel;

class PlatformExpoter extends AbstractExporter
{
    public function export()
    {
        Excel::create('Platform', function($excel) {

            $excel->sheet('Platform', function($sheet) {
                $header = ['date'=>'日期','ques_total'=>'问题数量','ques_answered'=>'回答数量'
                    ,'fee_total'=>'总收入','fee_refund'=>'当日退款','fee_due'=>'自动结算(支付大V)'
                    ,'fee_owe'=>'手动结算','fee_money'=>'平台收入'];
                // 这段逻辑是从表格数据中取出需要导出的字段
                $list = $this->getData();
                $list = array_map(function($item)use($header){
                    $item['fee_total']=$item['fee_total']/100;
                    $item['fee_refund']=$item['fee_refund']/100;
                    $item['fee_due']=$item['fee_due']/100;
                    $item['fee_owe']=$item['fee_owe']/100;
                    $item['fee_money']=$item['fee_money']/100;
                    $row = array_only($item,array_keys($header));
                    return $row;
                },$list);
                array_unshift($list,$header);

                $sheet->rows($list);

            });

        })->export('xls');
    }
}