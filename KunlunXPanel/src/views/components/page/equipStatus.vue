<template>
    <div class="equip"></div>
</template>

<script>
export default {
    name: '',
	props:{
		machineStatus:{typeof:Object,default:{machineTotal:0,machineOnline:0,machineOffline:0,machineIdle:0}}
		},
    data() {
        return {
			colors: ['#ffc933','#2a81cd','#f74f64']
		}
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
						text:this.machineStatus.machineTotal,
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
        this.setPies([{name: "离线设备", value: this.machineStatus.machineOffline},{name: "在线设备", value: this.machineStatus.machineOnline},{name: "不允许添加实例设备", value: this.machineStatus.machineIdle}],this.colors,[{name: "离线设备", icon: "circle"},{name: "在线设备", icon: "circle"},{name: "不允许添加实例设备", icon: "circle"}])
    },
}
</script>

<style lang="less" scoped>
.equip {
    width: 300px;
    height: 200px;
    }
</style>