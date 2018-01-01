<?php

namespace App\Admin\Extensions;

use App\Models\Daily;
use Encore\Admin\Grid\Exporters\AbstractExporter;
use Maatwebsite\Excel\Facades\Excel;

class ExcelExpoter extends AbstractExporter
{
    public function export()
    {
        Excel::create('Filename', function($excel) {

            $excel->sheet('Sheetname', function($sheet) {
                $header = ['id'=>'讲师ID','name'=>'讲师姓名','date'=>'日期','ques_total'=>'问题数','ques_answered'=>'回答数',
                'fee_total'=>'本日收入','fee_refund'=>'退款申请金额','fee_due'=>'结算收入','fee_owe'=>'未结清金额','state'=>'结算状态'];
                // 这段逻辑是从表格数据中取出需要导出的字段
                $list = $this->getData();
                $list = array_map(function($item)use($header){
                    $row = $item;
                    $row['state'] = @Daily::STATE[$row['state']];
                    $info['id'] = $item['expert']['expid'];
                    $info['name'] = $item['expert']['real_name'];
                    $row = $info + $row;
                    $row = array_only($row,array_keys($header));
                    return $row;
                },$list);
                array_unshift($list,$header);

                $sheet->rows($list);

            });

        })->export('xls');
    }
}