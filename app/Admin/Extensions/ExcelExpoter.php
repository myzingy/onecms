<?php

namespace App\Admin\Extensions;

use App\Models\Daily;
use Encore\Admin\Grid\Exporters\AbstractExporter;
use Maatwebsite\Excel\Facades\Excel;

class ExcelExpoter extends AbstractExporter
{
    public function export()
    {
        Excel::create('Expert', function($excel) {

            $excel->sheet('Expert', function($sheet) {
                $header = ['id'=>'讲师ID','mp_name'=>'公众号','real_name'=>'讲师姓名','date'=>'日期','ques_total'=>'问题数','ques_answered'=>'回答数',
                'fee_total'=>'本日收入','fee_refund'=>'退款申请金额','fee_due'=>'结算收入','fee_owe'=>'未结清金额','state'=>'结算状态'];
                // 这段逻辑是从表格数据中取出需要导出的字段
                $list = $this->getData();
                $list = array_map(function($item)use($header){
                    $row = $item;
                    $row['fee_total']=$row['fee_total']/100;
                    $row['fee_refund']=$row['fee_refund']/100;
                    $row['fee_due']=$row['fee_due']/100;
                    $row['fee_owe']=$row['fee_owe']/100;

                    $row['state'] = @Daily::STATE[$row['state']];
                    $info['id'] = $item['expert']['expid'];
                    $info['mp_name'] = $item['expert']['mp_name'];
                    $info['real_name'] = $item['expert']['real_name'];
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