<?php

namespace App\Admin\Extensions;

use App\Models\LivebcPaylog;
use Encore\Admin\Grid\Exporters\AbstractExporter;
use Maatwebsite\Excel\Facades\Excel;

class ExpoterLivebcPaylog extends AbstractExporter
{
    public function export()
    {
        Excel::create('LivebcPaylog', function($excel) {

            $excel->sheet('LivebcPaylog', function($sheet) {
                $header = [
                    'trade_no'=>'订单号',
                    'openid'=>'OPENID',
                    'mpuser_nickname'=>'订阅者昵称',
                    'expert_real_name'=>'讲师姓名',
                    'fee'=>'金额',
                    'timestamp'=>'时间',
                    'days'=>'订阅天数',
                    'refund_fee'=>'已退金额',
                    'state'=>'支付状态',
                ];
                // 这段逻辑是从表格数据中取出需要导出的字段
                $list = $this->getData();
                $list = array_map(function($item)use($header){
                    $info['trade_no'] = $item['trade_no'];
                    $info['openid'] = $item['openid'];
                    $info['mpuser_nickname'] = $item['mpuser']['nickname'];
                    $info['expert_real_name'] = $item['expert']['real_name'];
                    $info['fee']=$item['fee']/100;
                    $info['timestamp']=$item['timestamp'];
                    $info['days'] = $item['days'];
                    $info['refund_fee'] = $item['refund_fee']/100;
                    $info['state'] = LivebcPaylog::getStateStr($item['state']);
                    $info = array_only($info,array_keys($header));
                    return $info;
                },$list);
                array_unshift($list,$header);

                $sheet->rows($list);

            });

        })->export('xls');
    }
}