<template>
    <div class="pieChart"></div>
</template>

<script>
export default {
    name: '',
    data() {
        return {}
    },
    methods: {
        setChart() {
            let option = {
                title: [
                    {
                        text: "【shard】",
                        left: '12%',
                        bottom: '6%',
                        textStyle: {
                            color: "#fff",
                            fontSize: 12
                        }
                    },
                    {
                        text: "【存储节点】",
                        left: '46%',
                        bottom: '6%',
                        textStyle: {
                            color: "#fff",
                            fontSize: 12
                        }
                    },
                    {
                        text: "【计算节点】",
                        right: '12%',
                        bottom: '6%',
                        textStyle: {
                            color: "#fff",
                            fontSize: 12
                        }
                    }
                ],
                tooltip: {
                    trigger: 'item',
                    formatter: "{a} <br/>{b} : {c} ({d}%)"
                },
                legend: {
                    data: [
                        {name: 'cluster1', icon: 'circle'},
                        {name: 'cluster2', icon: 'circle'},
                        {name: 'cluster3', icon: 'circle'},
                        {name: 'cluster4', icon: 'circle'},
                        {name: 'cluster5', icon: 'circle'},
                    ],
                    left: "8%",
                    top: "10%",
                    itemWidth: 7,
                    itemHeight: 7,
                    textStyle: {
                        color: '#00CCFF',
                        fontSize: 10
                    }
                },
                series: [
                    {
                        name: '【shard】',
                        type: 'pie',
                        radius: '40%',
                        center: ['17%', '60%'],
                        data: [
                            {value: 335, name: 'cluster1'},
                            {value: 310, name: 'cluster2'},
                            {value: 234, name: 'cluster3'},
                            {value: 135, name: 'cluster4'},
                            {value: 1548, name: 'cluster5'}
                        ],
                        label: {
                            fontSize: 8,
                            color: '#00CCFF'
                        },
                        labelLine: {
                            length: 15,
                            length2: 10,
                            lineStyle: {
                                color: '#3F3F5C'
                            }
                        },
                        itemStyle: {
                            color: function (params) {
                                var colorList = ['#F74F64', '#00CCFF', '#315371', '#142AFE', '#9814FE'];
                                return colorList[params.dataIndex];
                            },
                            emphasis: {
                                shadowBlur: 10,
                                shadowOffsetX: 0,
                                shadowColor: 'rgba(0, 0, 0, 0.5)'
                            }
                        }
                    },
                    {
                        name: '【存储节点】',
                        type: 'pie',
                        radius: '40%',
                        center: ['50%', '60%'],
                        data: [
                            {value: 545, name: 'cluster1'},
                            {value: 210, name: 'cluster2'},
                            {value: 2534, name: 'cluster3'},
                            {value: 1335, name: 'cluster4'},
                            {value: 1548, name: 'cluster5'}
                        ],
                        label: {
                            fontSize: 8,
                            color: '#00CCFF'
                        },
                        labelLine: {
                            length: 15,
                            length2: 10,
                            lineStyle: {
                                color: '#3F3F5C'
                            }
                        },
                        itemStyle: {
                            color: function (params) {
                                var colorList = ['#F74F64', '#00CCFF', '#315371', '#142AFE', '#9814FE'];
                                return colorList[params.dataIndex];
                            },
                            emphasis: {
                                shadowBlur: 10,
                                shadowOffsetX: 0,
                                shadowColor: 'rgba(0, 0, 0, 0.5)'
                            }
                        }
                    },
                    {
                        name: '【计算节点】',
                        type: 'pie',
                        radius: '40%',
                        center: ['82%', '60%'],
                        data: [
                            {value: 435, name: 'cluster1'},
                            {value: 310, name: 'cluster2'},
                            {value: 334, name: 'cluster3'},
                            {value: 1135, name: 'cluster4'},
                            {value: 1548, name: 'cluster5'}
                        ],
                        label: {
                            fontSize: 8,
                            color: '#00CCFF'
                        },
                        labelLine: {
                            length: 15,
                            length2: 10,
                            lineStyle: {
                                color: '#3F3F5C'
                            }
                        },
                        itemStyle: {
                            color: function (params) {
                                var colorList = ['#F74F64', '#00CCFF', '#315371', '#142AFE', '#9814FE'];
                                return colorList[params.dataIndex];
                            },
                            emphasis: {
                                shadowBlur: 10,
                                shadowOffsetX: 0,
                                shadowColor: 'rgba(0, 0, 0, 0.5)'
                            }
                        }
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
.pieChart {
    height: 200px;
    width: 500px;
    padding: 0 20px;
}
</style>