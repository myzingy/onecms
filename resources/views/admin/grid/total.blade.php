<script class="totaljs">
    $(function(){
        var $tb=$('.totaljs').parents('table');
        var totalIndex={!! $total !!};
        var totalData={};
        $tb.find('tr').each(function(){
            $td=$(this).find('td');
            for(var i in totalIndex){
                var index=totalIndex[i];
                if(typeof totalData[index]=='undefined'){totalData[index]=0;}
                var val=$.trim($td.eq(index).text());
                if(val){
                    totalData[index]+=parseFloat(val);
                }
            }
        });
        var $tds=$tb.find('tr:last td');
        $tds.each(function(i){
            if(i==0){$(this).html('合 计:')}
            if(typeof totalData[i]!='undefined'){
                $(this).html(totalData[i].toFixed(2));
            }
        });
        console.log(totalData);
    });
</script>