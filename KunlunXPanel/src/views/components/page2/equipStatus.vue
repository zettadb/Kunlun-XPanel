<template>
    <div class="equip"></div>
</template>

<script>
export default {
    name: '',
    data() {
        return {colors: ['#ffc933','#2a81cd']}
    },
    methods: {
        setPies(seriesData,colors){
            let option = {
				//默认颜色,全局设置
				color:colors,
				graphic:{       //图形中间文字
					type:"text",
					left:"center",
					top:"center",
					style:{
						text:"24",
						textAlign:"center",
						fill:"#fff",
						fontSize:40,
						
					}
				},
				series:[{
					type: 'pie',
					radius: ['50%', '65%'],//饼图的半径，第一个为内半径，第二个为外半径
					avoidLabelOverlap: false,
					data:seriesData,
					//设置引导线的长度
					labelLine:{  
						normal:{  
							length:6, 
							length2:6  
						}  
					},  
					/*在series中添加itemStyle即可直观显示饼型数值*/
						itemStyle:{ 
							normal:{ 
							label:{
									show:true, 
		                            fontSize:10,
									formatter:'{b} {c}'
								} 
							} 
						}
				}]
            };
            let myChart = this.$echarts(this.$el);

            myChart.clear();
            myChart.resize()
            myChart.setOption(option);
        }
    },
    mounted() {
        this.setPies([{name: "离线设备", value: 2},{name: "在线设备", value: 22}],this.colors,[{name: "离线设备", icon: "circle"},{name: "在线设备", icon: "circle"}])
    },
}
</script>

<style lang="less" scoped>
.equip {
    width: 300px;
    height: 200px;
    }
</style>