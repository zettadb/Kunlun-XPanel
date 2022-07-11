<template>
    <div class="doubleBars"></div>
</template>

<script>
import { getNodeList} from '@/api/machine/list'
export default {
    name: '',
    props:{
		hostAddrList:{typeof:Object,default:[]}
    },
    data() {
        return {
            comp:[],
            storage:[],
        }
    },
    created(){
        this.getNodes();
    },
    methods: {
        getNodes(){
            let queryParam={hostAddrList:this.hostAddrList};
            getNodeList(queryParam).then(response => {
            this.comp =response.comp;
            this.storage =response.storage;
            this.setChart(this.comp, this.storage)
            });
        },
        setChart(comp,storage) {
            let option = {
                legend: {
                    left: "8%",
                    top: "10%",
                    itemWidth: 7,
                    itemHeight: 7,
                    textStyle: {
                        color: '#5CB1C1',
                        fontSize: 10
                    }
                },
                grid: [{
                    show: false,
                    left: '6%',
                    top: '17%',
                    bottom: '3%',
                    containLabel: true,
                    width: '40%',
                }, {
                    show: false,
                    left: '53%',
                    top: '17%',
                    bottom: '3%',
                    width: '1%',
                }, {
                    show: false,
                    right: '6%',
                    top: '17%',
                    bottom: '3%',
                    containLabel: true,
                    width: '40%',
                },],
                xAxis: [{
                    splitNumber: 8,
                    type: 'value',
                    inverse: true,
                    axisLine: {show: false},
                    axisTick: {show: false},
                    position: 'bottom',
                    axisLabel: {show: false},
                    splitLine: {show: false}
                }, {
                    gridIndex: 1,
                    show: false
                }, {
                    gridIndex: 2,
                    splitNumber: 8,
                    type: 'value',
                    axisLine: {show: false},
                    axisTick: {show: false},
                    position: 'bottom',
                    axisLabel: {show: false},
                    splitLine: {show: false}
                }
                ],
                yAxis: [
                    //左边的标尺
                    {
                        type: 'category',
                        inverse: true,
                        position: 'left',
                        axisLine: {show: false},
                        axisTick: {show: false},
                        axisLabel: {show: false},
                    },
                    //中间的标尺
                    {
                        gridIndex: 1,
                        type: 'category',
                        inverse: true,
                        position: 'center',
                        axisLine: {show: false},
                        axisTick: {show: false},
                        axisLabel: {
                            show: true,
                            color: '#61B9C8',
                            fontSize: 8,
                            showMinLabel: true,
                            showMaxLabel: true,
                            interval: 0
                        },
                        data: this.hostAddrList
                    },
                    //右边的标尺
                    {
                        gridIndex: 2,
                        type: 'category',
                        inverse: true,
                        offset: 50,
                        position: 'left',
                        axisLine: {show: false},
                        axisTick: {show: false},
                        axisLabel: {show: false}
                    }
                ],
                series: [{
                    name: '计算节点',
                    type: 'bar',
                    barGap: 10,
                    barWidth: "40%",
                    itemStyle: {
                        normal: {
                            color: {
                                type: 'linear',
                                x: 0,
                                y: 0,
                                x2: 1,
                                y2: 0,
                                colorStops: [
                                    {offset: 0, color: 'rgba(1,176,223,1)'},
                                    {offset: 1, color: 'rgba(1,176,223,0)'}
                                ],
                                global: false // 缺省为 false
                            },
                            barBorderRadius: 5,
                        },

                        emphasis: {
                            show: false
                        }
                    },
                    data:comp
                }, {
                    name: '存储节点',
                    type: 'bar',
                    barGap: 10,
                    barWidth: "40%",
                    xAxisIndex: 2,
                    yAxisIndex: 2,

                    itemStyle: {
                        normal: {
                            color: {
                                type: 'linear',
                                x: 0,
                                y: 0,
                                x2: 1,
                                y2: 0,
                                colorStops: [
                                    {offset: 0, color: 'rgba(126,19,212,0)'},
                                    {offset: 1, color: 'rgba(126,19,212,1)'}
                                ],
                                global: false // 缺省为 false
                            },
                            barBorderRadius: 5,

                        },
                        emphasis: {
                            show: false
                        }
                    },
                    data: storage
                }]
            };

            let myChart = this.$echarts(this.$el);

            myChart.clear();
            myChart.resize()
            myChart.setOption(option);
        }
    },
    mounted() {
        //this.setChart(this.comp, this.storage)
    },
}
</script>

<style lang="less" scoped>
.doubleBars {
    width: 280px;
    height: 200px;
}
</style>