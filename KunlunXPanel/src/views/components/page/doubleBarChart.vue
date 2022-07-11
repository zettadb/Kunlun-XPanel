<template>
    <div class="doubleBarChart"></div>
</template>

<script>
import {getUsedList} from '@/api/machine/list'
export default {
    name: '',
    props:{
        selectResult:{typeof:String,default:''}
    },
    data() {
        return {
            used:[],
            avail:[]
        }
    },
    created(){
        this.getUsedResult();
    },
    methods: {
        getUsedResult(){
            let queryParam={hostAddrList:this.selectResult};
            getUsedList(queryParam).then(response => {
            this.used =response.used;
            this.avail =response.avail;
            this.setChart(this.used, this.avail)
            });
        },
        setChart(used,avail) {
            let option = {
                tooltip: {
                    trigger: 'axis',
                    axisPointer: {            // 坐标轴指示器，坐标轴触发有效
                        type: 'shadow'        // 默认为直线，可选为：'line' | 'shadow'
                    },
                    backgroundColor: '#11367a',
                    textStyle: {
                        color: '#6dd0e3',
                        fontSize: 10,
                    },
                },
                legend: [
                    {
                        top: '8%',
                        right: '5%',
                        itemWidth: 7,
                        itemHeight: 7,
                        textStyle: {
                            color: '#5CB1C1',
                        }
                    },
                ],
                grid:
                    {
                        top: '15%',
                        left: '3%',
                        right: '5%',
                        bottom: '8%',
                        containLabel: true,
                    },
                xAxis: [
                    {
                        type: 'category',
                        axisLabel: {
                            interval: 0,
                            color: '#61B9C8',
                            fontSize: 10
                        },
                        axisLine: {
                            symbol: ['none', 'arrow'],
                            symbolSize: [6, 6],
                            symbolOffset: [0, 5],
                            lineStyle: {
                                color: '#122C49'
                            }
                        },
                        axisTick: {show: false},
                        data: ['计算节点数据目录', '存储节点数据目录', 'wal日志目录', '日志目录']
                    },
                ],
                yAxis:
                    {
                        type: 'value',
                        min: 0,
                        max: 500,
                        axisLabel: {
                            color: '#61B9C8',
                            fontSize: 9,
                            showMaxLabel: false,
                        },
                        axisLine: {
                            symbol: ['none', 'arrow'],
                            symbolSize: [6, 6],
                            symbolOffset: [0, 5],
                            lineStyle: {
                                color: '#122C49'
                            }
                        },
                        axisTick: {
                            length: 3
                        },
                        name: '(GB)',
                        nameGap: -5,
                        nameTextStyle: {
                            color: '#61B9C8',
                            fontSize: 9,
                            align: 'right',
                            padding: [0, 6, 0, 0]
                        },
                        splitLine: {show: false}

                    },
                series: [
                    {
                        name: '已使用',
                        type: 'bar',
                        barWidth: 7,
                        stack: '总数',
                        itemStyle: {
                            color: {
                                type: 'linear',
                                x: 0,
                                y: 0,
                                x2: 0,
                                y2: 1,
                                colorStops: [{
                                    offset: 0, color: '#FC9386' // 0% 处的颜色
                                },
                                    {
                                        offset: 0.4, color: '#F87B86' // 40% 处的颜色
                                    }, {
                                        offset: 1, color: '#F36087' // 100% 处的颜色
                                    }],
                                global: false // 缺省为 false
                            }, //背景渐变色
                            barBorderRadius: [3.5, 3.5, 0, 0],
                        },
                        data: used
                    },
                    {
                        name: '未使用',
                        type: 'bar',
                        barWidth: 7,
                        stack: '总数',
                        itemStyle: {
                            color: '#8C14EA',
                            barBorderRadius: [3.5, 3.5, 0, 0,],
                        },
                        data: avail
                    },

                ]
            };
            let myChart = this.$echarts(this.$el);
            myChart.clear();
            myChart.resize()
            myChart.setOption(option);
        }
    },
    mounted() {
        this.setChart()
    },
}
</script>

<style lang="less" scoped>
.doubleBarChart {
    width: 450px;
    height: 180px;
}

</style>