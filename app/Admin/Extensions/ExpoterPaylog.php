<?php

namespace App\Admin\Extensions;

use App\Models\Daily;
use App\Models\Paylog;
use Encore\Admin\Grid\Exporters\AbstractExporter;
use Maatwebsite\Excel\Facades\Excel;

class ExpoterPaylog extends AbstractExporter
{
    public function export()
    {
        Excel::create('Paylog', function($excel) {

            $excel->sheet('Paylog', function($sheet) {
                $header = [
                    'id'=>'ID',
                    'trade_no'=>'订单号',
                    'openid'=>'OPENID',
                    'mpuser_nickname'=>'提问者昵称',
                    'question_asker_name'=>'提问者姓名',
                    'question_question'=>'问题',
                    'expert_real_name'=>'讲师姓名',
                    'fee'=>'金额',
                    'timestamp'=>'时间',
                    'state'=>'支付状态',
                ];
                // 这段逻辑是从表格数据中取出需要导出的字段
                $list = $this->getData();
                $list = array_map(function($item)use($header){
                    $info['id']=$item['payid'];
                    $info['trade_no'] = $item['trade_no'];
                    $info['openid'] = $item['openid'];
                    $info['mpuser_nickname'] = $item['mpuser']['nickname'];
                    $info['question_asker_name'] = $item['question']['asker_name'];
                    $info['question_question'] = $item['question']['question'];
                    $info['expert_real_name'] = $item['expert']['real_name'];
                    $info['fee']=$item['fee']/100;
                    $info['timestamp']=$item['timestamp'];
                    $info['state'] = Paylog::getStateStr($item['state']);
                    $info = array_only($info,array_keys($header));
                    return $info;
                },$list);
                array_unshift($list,$header);

                $sheet->rows($list);

            });

        })->export('xls');
    }
}