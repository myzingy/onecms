<script class="totaljs">
    window.echartsConfig={
        //color: ['#3398DB'],
        legend: {
            data:['直接访问','邮件营销','联盟广告','视频广告','搜索引擎','百度','谷歌','必应','其他']
        },
        toolbox: {
            feature: {
                dataView: {show: false, readOnly: false},
                magicType: {show: true, type: ['line', 'bar']},
                restore: {show: true},
                saveAsImage: {show: false}
            }
        },
        tooltip : {
            trigger: 'axis',
            axisPointer : {            // 坐标轴指示器，坐标轴触发有效
                type : 'shadow'        // 默认为直线，可选为：'line' | 'shadow'
            }
        },
        grid: {
            left: '3%',
            right: '4%',
            bottom: '3%',
            containLabel: true
        },
        xAxis : [
            {
                type : 'category',
                data : ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                axisTick: {
                    alignWithLabel: true
                }
            }
        ],
        yAxis : [
            {
                type : 'value'
            }
        ],
        series : [
            {
                name:'直接访问',
                type:'bar',
                data:[10, 52, 200, 334, 390, 330, 220]
            }
        ]
    };
    $(function(){
        var $tb=$('.totaljs').parents('table');
        var totalIndex={!! $total !!};
        var totalData={};
        var ecxAxisData=[];
        var seriesData=[];
        var legendData=[];
        $tb.find('tr').each(function(tr_i){
            $td=$(this).find('td');
            for(var i in totalIndex){
                var index=totalIndex[i];
                if(tr_i==0){
                    $th=$(this).find('th');
                    var ss={
                        name:$.trim($th.eq(index).text()),
                        type:'bar',
                        //barWidth: (100/totalIndex.length)+'%',
                        data:[]
                    };
                    seriesData[i]=ss;
                    legendData.push(ss.name);
                }
                if(typeof totalData[index]=='undefined'){totalData[index]=0;}
                var val=$.trim($td.eq(index).text());
                if(val){
                    totalData[index]+=parseFloat(val);
                    if(tr_i>0){
                        seriesData[i].data.push(val);
                    }
                }
            }
            var x_val=$.trim($td.eq(0).text());
            if(x_val){
                ecxAxisData.push(x_val);
            }
        });
        var $tds=$tb.find('tr:last td');
        $tds.each(function(i){
            if(i==0){$(this).html('合 计:')}
            if(typeof totalData[i]!='undefined'){
                $(this).html(totalData[i].toFixed(2));
            }
        });
        echartsConfig.xAxis[0].data=ecxAxisData;
        echartsConfig.series=seriesData;
        echartsConfig.legend.data=legendData;
    });
</script>