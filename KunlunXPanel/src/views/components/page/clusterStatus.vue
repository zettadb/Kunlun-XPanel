<template>
    <div class="equip"></div>
</template>

<script>
export default {
    name: '',
	props:{
		clusterCount:{typeof:String,default:'0'}
		},
    data() {
        return {
			colors: ['#ffc933','#2a81cd']
		}
    },
	// watch:{
    //     'clusterCount':function(val,olval){
	// 		// this.$nextTick(() => {
	// 			 this.clusterCount=val;
	// 		// })	
    //     },
	// 	// 一进页面就执行
    //     immediate: true,
    //     // 深度观察监听
    //     deep: true
    // },
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
						text:this.clusterCount,
						textAlign:"center",
						fill:"#fff",
						fontSize:40,
						
					}
				},
			legend: {
                itemWidth: 10, //图形宽度
                itemHeight: 10,//图形高度
                data:seriesData.title,
                textStyle:{//图例文字的样式
                    color:'#ccc',
                    fontSize:12,
					formatter:'{b} {c}'
                },
                x:'6%', 
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
        this.setPies([{name: "离线集群", value: 0},{name: "在线集群", value: this.clusterCount}],this.colors,[{name: "离线集群", icon: "circle"},{name: "在线集群", icon: "circle"}])
    },
}
</script>

<style lang="less" scoped>
.equip {
    width: 280px;
    height: 200px;
    }
</style>