<template>
    <div class="pieChart"></div>
</template>

<script>
import {getClusterShards} from '@/api/cluster/list'
export default {
    name: '',
    props:{
            clusterName:{typeof:Object,default:[]}
        },
    data() {
        return {cluster:[],shard:[],comp:[],nodes:[] }

    },
    methods: {
        async setChart() {
            for (let i = 0; i < this.clusterName.length; i++) {
                let param = this.clusterName[i];
                const newArr={"name":param.name,"icon":"circle"};
                this.cluster.push(newArr)
                //获取每个cluster的shard，comp，nodes数量
                const res=await getClusterShards(param.id);
                const shardArr={"value":res.shard,"name":param.name};
                this.shard.push(shardArr)
                const nodesArr={"value":res.nodes,"name":param.name};
                this.nodes.push(nodesArr)
                const compArr={"value":res.comp,"name":param.name};
                this.comp.push(compArr)

            }
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
                    data: this.cluster,
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
                        data: this.shard,
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
                        data: this.nodes,
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
                        data: this.comp,
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