<template>
    <div class="threeBarChart"></div>
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
                // title: {
                //     text: 'Funnel',
                //     left: 'left',
                //     top: 'bottom'
                // },
                tooltip: {
                    trigger: 'item',
                    formatter: '{a} <br/>{b} : {c}%'
                },
                // toolbox: {
                //     orient: 'vertical',
                //     top: 'center',
                //     feature: {
                //     dataView: { readOnly: false },
                //     restore: {},
                //     saveAsImage: {}
                //     }
                // },
                legend: {
                    orient: 'vertical',
                    left: 'left',
                    data: ['新增集群', 'Click', 'Visit', 'Inquiry', 'Order','Show1', 'Click1', 'Visit1', 'Inquiry1', 'Order1','Show2', 'Click2', 'Visit2', 'Inquiry2', 'Order2'],
                    itemWidth: 5,
                    itemHeight: 5,
                    textStyle: {
                        color: '#5CB1C1',
                        fontSize: 10
                    }
                },
                series: [
                    {
                    name: 'Funnel',
                    type: 'funnel',
                    width: '50%',
                    height: '90%',
                    left: '30%',
                    top: '10%',
                    data: [
                        { value: 60, name: '新增集群' },
                        { value: 30, name: 'Inquiry' },
                        { value: 10, name: 'Order' },
                        { value: 80, name: 'Click' },
                        { value: 100, name: 'Show' },
                        { value: 60, name: 'Visit1' },
                        { value: 30, name: 'Inquiry1' },
                        { value: 10, name: 'Order1' },
                        { value: 80, name: 'Click1' },
                        { value: 100, name: 'Show1' },
                        { value: 60, name: 'Visit2' },
                        { value: 30, name: 'Inquiry2' },
                        { value: 10, name: 'Order2' },
                        { value: 80, name: 'Click2' },
                        { value: 100, name: 'Show2' }
                    ]
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
.threeBarChart {
    height: 200px;
    width: 300px;
}
</style>