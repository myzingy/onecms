<div style="max-width: 100%; overflow-x: auto;">
    <div id="echartsMain" style="width: 1200px;height:400px;"></div>
</div>
<script>

$(function () {
    // 基于准备好的dom，初始化echarts实例
    var myChart = echarts.init(document.getElementById('echartsMain'));
    console.log(echartsConfig);
    myChart.setOption(echartsConfig);
});
</script>