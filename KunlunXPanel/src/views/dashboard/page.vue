<template>
    <div class="page">
        <Row class='content'>
            <Col span="8">
                <div class="list">
                    <div class="left">
                        <span class='title'><span class="title-4">集群在/离线统计</span></span>
                        <span class="angle1"></span>
                        <span class="angle2"></span>
                        <span class="angle3"></span>
                        <span class="angle4"></span>
                        <div class="chart-68">
                            <clusterStatus ref="chart1" v-if="clusterTotal" :clusterCount="clusterTotal"></clusterStatus>
                            <!-- <area-chart ref="chart1" id="left_1" :select-range-date="selectRangeDate" :config="cnfigData1"></area-chart> -->
                        </div>
                        <div class="chart-32">
                            <!-- <div>
                                <span>集群名称</span>
                                <span>comp离线数</span>
                                <span>storage离线数</span>
                            </div> -->
                            <radar-chart ref="chart4" id="left_4" v-if="officeRadarData.indicator.length" :data="officeRadarData"></radar-chart>
                            <!-- <div class="ai_camera_list">
                                <p class="title"> 
                                    <span class="equip_name" style="width:137px;">设备名称</span>
                                    <span class="addr">所在区域</span>
                                </p>
                            </div>
                            <div class="slide_wrap">
                                <div class="bd">
                                    <vue-seamless-scroll class="seamless-warp" :data="cameraList" :class-option="optionSingleHeightTime" >
                                    <ul>
                                        <li v-for="(item,index) in cameraList" :key="index"><span class="equip_name"  v-text="item.baseEquipmentName" style="width:137px"></span><span class="addr">{{item.address | ellipsis}}</span></li>
                                    </ul>
                                    </vue-seamless-scroll>
                                </div>
                            </div> -->
                            <!-- <radar-chart ref="chart2" id="left_2" :data="chatRadarData"></radar-chart> -->
                        </div>
                    </div>
                </div>
                <div class="list" style="margin-top:200px;">
                    <div class="left">
                        <span class='title'><span class="title-8">操作记录列表</span></span>
                        <span class="angle1"></span>
                        <span class="angle2"></span>
                        <span class="angle3"></span>
                        <span class="angle4"></span>
                        <div class="chart-68">
                            <div class="per_inout_scroll_wrap">
                            <p class="title_list">
                                <span class="userName">用户名</span>
                                <span class="personType">任务名称</span>
                                <span class="passType">操作对象</span>
                                <span class="address">状态</span>
                                <span class="createTime">开始时间</span>
                            </p>
                             <div class="slide_wrap">
                                <div class="bd">
                                    <vue-seamless-scroll class="seamless-warp" :data="listData" :class-option="optionSingleHeightTime" >
                                    <ul>
                                        <li v-for="(item,index) in listData" :key="index"><span class="userName">{{item.user_name|ellipsis}}</span><span class="personType"  v-text="item.job_type"></span><span class="passType" >{{item.object|ellipsis}}</span><span class="address">{{item.status|ellipsis}}</span><span class="createTime" v-text="item.when_started"></span></li>
                                    </ul>
                                    </vue-seamless-scroll>
                                </div>
                            </div>
                        </div>
                            <!-- <bar-chart ref="chart3" id="left_3" :config="configData2"></bar-chart> -->
                        </div>
                        <div class="chart-32">
                            <!-- <radar-chart ref="chart4" id="left_4" :data="officeRadarData"></radar-chart> -->
                        </div>
                    </div>
                </div>
            </Col>
            <Col span="8">
                <div class="circlePie" id="circlePie">
                    <canvas id="main" width="500" height="500"></canvas>
                    <canvas id="dot"></canvas>
                </div>
            </Col>
            <Col span="8">
                <div class="list">
                    <div class="right">
                        <span class='title'><span class="title-4">设备磁盘使用情况</span></span>
                        <span class="angle1"></span>
                        <span class="angle2"></span>
                        <span class="angle3"></span>
                        <span class="angle4"></span>
                        <!-- <div class="chart-32">
                            <radar-chart ref="chart5" id="right_1" :data="friendRadarData"></radar-chart>
                        </div> -->
                        <div class="chart-68" style="position:relative;">
                            <div class="backbg">
                            <select name="status" id="tabs" class="selectGroup changeItem sel" v-model="ip" @change="ipFunc(ip)">
                                <option v-for="item in ips" :key="item.value" :label="item.label" :value="item.value"></option>
                            </select>
                        </div>
                            <double-bar-chart ref="chart6" v-if="selectedIP.length" :selectResult="selectedIP"  ></double-bar-chart>
                        </div>
                    </div>
                </div>
                <div class="list" style="margin-top:200px;">
                    <div class="right">
                        <span class='title'><span class="title-8">设备状态统计</span></span>
                        <span class="angle1"></span>
                        <span class="angle2"></span>
                        <span class="angle3"></span>
                        <span class="angle4"></span>
                        <div class="chart-32">
                            <radar-chart ref="chart7" id="right_3" v-if="momentsRadarData.indicator.length" :data="momentsRadarData"></radar-chart>
                        </div>
                        <div class="chart-68">
                            <equipStatus ref="chart8" v-if="machineList.machineTotal" :machineStatus="machineList"></equipStatus>
                            <!-- <single-area-chart ref="chart8" :selectRangeDate="selectRangeDate" id="right_4"></single-area-chart> -->
                        </div>
                    </div>
                </div>
                
            </Col>
        </Row>
        <Row class="bottom-list" style="margin-top:30px;">
            <Col span="16">
                <div class="list">
                    <div class="bottom">
                        <span class='title'><span class="title-10">集群节点列表</span></span>
                        <span class="angle1"></span>
                        <span class="angle2"></span>
                        <span class="angle3"></span>
                        <span class="angle4"></span>
                        <double-line ref="chart9" id="bottom_1" v-if="clusterName.length" :clusterNode="clusterName"></double-line>
                    </div>
                </div>
                <div class="list">
                    <div class="bottom">
                        <span class='title'><span class="title-10">设备节点列表</span></span>
                        <span class="angle1"></span>
                        <span class="angle2"></span>
                        <span class="angle3"></span>
                        <span class="angle4"></span>
                        <double-bars ref="chart10" id="bottom_2" v-if="hostaddr.length" :hostAddrList="hostaddr"></double-bars>
                    </div>
                </div>
                <div class="list">
                    <div class="bottom">
                        <span class='title'><span class="title-10">操作记录统计</span></span>
                        <span class="angle1"></span>
                        <span class="angle2"></span>
                        <span class="angle3"></span>
                        <span class="angle4"></span>
                        <three-bar-chart ref="chart11" id="bottom_3"></three-bar-chart>
                    </div>
                </div>
            </Col>
            <Col span="8">
                <div class="list right-bottom">
                    <div class="bottom bottom1">
                        <span class='title'><span class="title-10">集群各个节点的分布情况</span></span>
                        <span class="angle1"></span>
                        <span class="angle2"></span>
                        <span class="angle3"></span>
                        <span class="angle4"></span>
                        <pie-chart ref="chart12" id="bottom_4" v-if="clusterName.length" :clusterName="clusterName"></pie-chart>
                    </div>
                </div>
            </Col>
        </Row>
    </div>
</template>

<script>
import {getAllCluster} from '@/api/system/access'
import {getOptionList} from '@/api/operation/record'
import {getAllMachineStatus} from '@/api/machine/list'
// import {v4 as uuidv4 } from 'uuid';
// import {version_arr,timestamp_arr} from "@/utils/global_variable"
// import { getOperationList } from '@/api/operation/record';
//import equipStatus from '../components/page/equipStatus.vue';
const areaChart = ()=> import('../components/areaChart.vue');
const radarChart = () => import('../components/radar');
const barChart = () => import('../components/page/barChart');
const doubleBarChart = ()=> import('../components/page/doubleBarChart');
const singleAreaChart = () => import('../components/page/singleAreaChart');
const doubleLine  = () => import('../components/page/doubleLine');
const threeBarChart = () => import('../components/page/threeBarChart');
const pieChart = () => import('../components/page/pieChart');
const doubleBars = () => import('../components/page/doubleBars');
const equipStatus = () => import('../components/page/equipStatus');
const clusterStatus = () => import('../components/page/clusterStatus'); 
export default {
    name: 'page',
    props: ['clusterCount'],
    components: {
        areaChart,
        radarChart,
        barChart,
        doubleBarChart,
        singleAreaChart,
        doubleLine,
        threeBarChart,
        pieChart,
        doubleBars,
        equipStatus,
        clusterStatus
    },
     filters: {
        ellipsis (_val) {
        if (!_val) return ''
        if (_val.length > 10) {
            return _val.slice(0, 10) + '...'
        }
        return _val
        }
    },
    data() {
        return {
            everyPer: 0,
            xOffset: 0,
            circle: {
                x: 250,
                y: 250,
                radius: 218
            },
            title: ['累计集群总数:0', '累计设备总数:0', '当前设备在线总数:0', '当前设备离线总数:0', '当前集群在线总数:0', '当前集群离线总数:0'],
            cnfigData1: {
                color: '#5CB1C1',
                name: ['（次）', '（人）'],
                data: [
                    {
                        name: '聊天次数',
                        color: ['#9e70ff', '#6e5eff'],
                        data: [200, 12, 21, 54, 260, 130, 210],
                    },
                    {
                        name: '聊天人数',
                        color: ['#48cefd', '#5356f1'],
                        data: [50, 182, 234, 191, 190, 30, 10],
                    }
                ]
            },
            configData2: {
                data: [213, 190, 137, 99, 63, 196, 248, 212, 248, 112]
            },
            chatRadarData: {
                title: '各部门聊天对比',
                position: ['center', '85%'],
                center: ['50%', '50%'],
                indicator: [
                    {text: 'cluster1'},
                    {text: 'cluster2'},
                    {text: 'cluster3'},
                    {text: 'cluster4'},
                    {text: 'cluster5'},
                    {text: 'cluster6'}
                ],
                data: [
                    {
                        name: '聊天次数',
                        color: '#0DF5F8',
                        value: [100, 8, 0.40, -80, 2000, 332],
                    },
                    {
                        name: '聊天人数',
                        color: '#7921AD',
                        value: [60, 5, 0.30, -100, 1500, 221],
                    }
                ]
            },
            officeRadarData: {
                title: '各集群名称',
                position: ['center', '85%'],
                center: ['50%', '50%'],
                indicator: [
                    // {text: 'cluster1'},
                    // {text: 'cluster2'},
                    // {text: 'cluster3'},
                    // {text: 'cluster4'},
                    // {text: 'cluster5'},
                    // {text: 'cluster6'}
                ],
                data: [
                    {
                        name: '集群名称',
                        color: '#55D35B',
                        //value: [100, 8, 0.40, -80, 2000, 332],
                    }
                ]
            },
            friendRadarData: {
                title: '磁盘使用情况对比',
                position: ['center', '85%'],
                center: ['50%', '50%'],
                indicator: [
                    {text: 'cluster1'},
                    {text: 'cluster2'},
                    {text: 'cluster3'},
                    {text: 'cluster4'},
                    {text: 'cluster5'},
                    {text: 'cluster6'}
                ],
                data: [
                    {
                        name: '集群总数',
                        color: '#FA8486',
                        value: [100, 8, 0.40, -80, 2000, 332],
                    }
                ]
            },
            momentsRadarData: {
                title: '设备的IP列表',
                position: ['center', '85%'],
                center: ['50%', '50%'],
                indicator: [
                    // {text: '192.168.0.168'},
                    // {text: '192.168.0.127'},
                    // {text: '192.168.0.128'},
                    // {text: '192.168.0.1'},
                    // {text: '192.168.0.2'},
                    // {text: '192.168.0.3'}
                ],
                data: [
                    {
                        name: '设备IP',
                        color: '#D91748',
                        // value: [100, 8, 0.40, -80, 2000, 332],
                    }
                ]
            },
            warea: {x: 250, y: 250, max: 700},
            dots: [],
            resizeFn: null,
            animationFrame1:null,
            animationFrame2: null,
            centerBox: {
                width: 0,
                height: 0
            },
            clusterTotal:0,
            clusterList:[],
            clusterName:[],
            machineList:{
                machineTotal:0,
                machineOnline:0,
                machineOffline:0,
                // machineIdle:0
            },
            hostaddr:[],
            ips:[],
            ip:'',
            selectedIP:'',
             listData:[
                {'user_name':'super_dba','job_type':'新增集群','object':'test','status':'done','when_started':'2020-8-9 16:34:01'},
                {'user_name':'super_dba','job_type':'删除集群','object':'mgr','status':'failed','when_started':'2020-8-10 16:34:01'},
                {'user_name':'super_dba','job_type':'新增计算节点','object':'testmgr','status':'done','when_started':'2020-8-10 16:34:01'},
                {'user_name':'super_dba','job_type':'新增存储节点','object':'testmgr','status':'done','when_started':'2020-8-10 16:34:01'},
                {'user_name':'super_dba','job_type':'新增shard','object':'testmgr','status':'done','when_started':'2020-8-10 16:34:01'},
                {'user_name':'super_dba','job_type':'备份集群','object':'testmgr','status':'failed','when_started':'2020-8-10 16:34:01'},
                {'user_name':'super_dba','job_type':'删除存储节点','object':'testmgr','status':'done','when_started':'2020-8-10 16:34:01'},
            ],
        }
    },
    created(){
        this.getCluster();
        // this.getClusterStauts();
        this.getMachineStauts();
        this.optionList();
    },
    computed: {
        optionSingleHeightTime () {
            return {
                step: 0.8, // 数值越大速度滚动越快
                limitMoveNum: 5, // 开始无缝滚动的数据量 this.dataList.length
                hoverStop: true, // 是否开启鼠标悬停stop
                direction: 1, // 0向下 1向上 2向左 3向右
                openWatch: true, // 开启数据实时监控刷新dom
                singleHeight: 0, // 单步运动停止的高度(默认值0是无缝不停止的滚动) direction => 0/1
                singleWidth: 0, // 单步运动停止的宽度(默认值0是无缝不停止的滚动) direction => 2/3
                waitTime: 100                    // 单步运动停止的时间(默认值1000ms)
            }
        }
    },
    methods: {
        optionList(){
            getOptionList().then(response => {
                this.listData =response.list;
            });
        },
        ipFunc(val){
        this.selectedIP=val;
        },
        async getCluster() {
            const res = await getAllCluster();
            this.clusterTotal=res.total+'';
            if(res.total>0){
                this.officeRadarData.indicator=[];
                for(let i=0;i<res.total;i++){
                    const newArr={"id":res.list[i].id,"name":res.list[i].nick_name};
                    this.clusterName.push(newArr)
                    let indicatorArr={text:res.list[i].nick_name};
                    this.officeRadarData.indicator.push(indicatorArr);
                }
                //console.log(this.officeRadarData.indicator);
            }
        },
        // async getClusterStauts(){
        //     const tempData = {};
        //     tempData.job_id ='';
        //     tempData.job_type ='get_cluster_summary';
        //     tempData.version=version_arr[0].ver;
        //     tempData.paras={};
        //     tempData.timestamp=timestamp_arr[0].time+'';
        //     const res=await getAllClusterStatus(tempData)
            
        // },
        async getMachineStauts(){
            //获取机器的状态
            const res = await getAllMachineStatus();
            if(res.list.length>0){
                this.momentsRadarData.indicator=[];
                this.machineList.machineTotal=res.list.length;
                for(let i=0;i<res.list.length;i++){
                    let newArr={value:res.list[i].hostaddr,label:res.list[i].hostaddr};
                    this.ips.push(newArr);
                    let indicatorArr={text:res.list[i].hostaddr};
                    this.momentsRadarData.indicator.push(indicatorArr);
                    if(i==0){
                        this.selectedIP=res.list[i].hostaddr
                        this.ip=res.list[i].hostaddr
                    }
                    this.hostaddr.push(res.list[i].hostaddr)
                    if(res.list[i].status=='running'){
                        this.machineList.machineOnline++;
                    }else if(res.list[i].status=='dead'){
                        this.machineList.machineOffline++;
                    }
                    // else if(res.list[i].status=='idle'){
                    //     this.machineList.machineIdle++
                    // }
                }
                //console.log(this.machineList);
            }
            // const tempData = {};
            // tempData.job_id ="";
            // tempData.job_type ='get_machine_summary';
            // tempData.version=version_arr[0].ver;
            // tempData.timestamp=timestamp_arr[0].time+'';
            // tempData.paras={};
            // const res=await getAllMachineStatus(tempData)
            // this.machineList.machineTotal=res.attachment.list_machine.length;
            // if(res.attachment.list_machine.length>0){
            //     this.momentsRadarData.indicator=[];
            //     for(let i=0;i<res.attachment.list_machine.length;i++){
            //         let newArr={value:res.attachment.list_machine[i].hostaddr,label:res.attachment.list_machine[i].hostaddr};
            //         this.ips.push(newArr);
            //         //let indicator={};
            //         let indicatorArr={text:res.attachment.list_machine[i].hostaddr};
            //         this.momentsRadarData.indicator.push(indicatorArr);
            //         //console.log(this.momentsRadarData.indicator);
            //         if(i==0){
            //             this.selectedIP=res.attachment.list_machine[i].hostaddr
            //             this.ip=res.attachment.list_machine[i].hostaddr
            //         }
            //         this.hostaddr.push(res.attachment.list_machine[i].hostaddr)
            //         if(res.attachment.list_machine[i].status=='online'){
            //             this.machineList.machineOnline++;
            //         }else if(res.attachment.list_machine[i].status=='offline'){
            //             this.machineList.machineOffline++;
            //         }
            //     }
            //     //console.log(this.hostaddr);
            // }
        },
        drawDot() {
            let canvas = document.getElementById('dot');
            canvas.width = document.querySelector('#circlePie').offsetWidth;
            canvas.height = document.querySelector('#circlePie').offsetHeight;
            let ctx = canvas.getContext('2d');
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            // 将鼠标坐标添加进去，产生一个用于比对距离的点数组
            let ndots = [this.warea].concat(this.dots);
            this.dots.forEach((dot)=> {
                // 粒子位移
                dot.x += dot.xa;
                dot.y += dot.ya;
                // 遇到边界将加速度反向
                dot.xa *= (dot.x > canvas.width || dot.x < 0) ? -1 : 1;
                dot.ya *= (dot.y > canvas.height || dot.y < 0) ? -1 : 1;
                // 绘制点
                ctx.fillStyle = '#ffffff';
                ctx.beginPath();
                ctx.arc(dot.x - 0.5, dot.y - 0.5, 2, 0, 2 * Math.PI, true);
                ctx.closePath();
                ctx.fill();
                // 循环比对粒子间的距离
                for (let i = 0; i < ndots.length; i++) {
                    let d2 = ndots[i];
                    if (dot === d2 || d2.x === null || d2.y === null) continue;
                    let xc = dot.x - d2.x;
                    let yc = dot.y - d2.y;
                    // 两个粒子之间的距离
                    let dis =Math.sqrt(xc * xc + yc * yc);
                    // 距离比
                    let ratio;
                    // 如果两个粒子之间的距离小于粒子对象的max值，则在两个粒子间画线
                    if (dis < d2.max) {
                        // 计算距离比
                        ratio = (d2.max - dis) / d2.max;
                        // 画线
                        ctx.beginPath();
                        ctx.lineWidth = ratio / 2;
                        if (d2 === this.warea) {
                            ctx.strokeStyle = 'rgba(255,255,255,0)';
                        } else {
                            // 距离变大 连线颜色变浅
                            ctx.strokeStyle = 'rgba(255,255,255,' + (ratio + 0.2) + ')';
                        }
                        ctx.moveTo(dot.x, dot.y);
                        ctx.lineTo(d2.x, d2.y);
                        ctx.stroke();
                    }
                }
                // 将已经计算过的粒子从数组中删除
                ndots.splice(ndots.indexOf(dot), 1);
            });
            this.animationFrame1 = window.requestAnimationFrame(this.drawDot);
        },
        rads(x) { // 弧度转换
            return Math.PI * x / 180;
        },
        act() {
            //清空画布
            const canvas = document.querySelector('#main');
            canvas.style.width = this.centerBox.height + 'px';
            canvas.style.height = this.centerBox.height + 'px';
            const context = canvas.getContext('2d');
            context.clearRect(0, 0, canvas.width, canvas.height);
            this.drawPie(this.everyPer, context);
            this.animationFrame2 = window.requestAnimationFrame(this.act);
            this.everyPer += Math.PI / 180;
            let speed = 0.07; //波浪速度，数越大速度越快
            this.xOffset += speed;
        },
        drawPie(everyPer, context) {
            context.save();
            context.fillStyle = 'rgba(18,55,88,.2)';
            context.beginPath();
            context.arc(this.circle.x, this.circle.y, 245, 0, 2 * Math.PI, true);
            context.closePath();
            context.fill();
            context.restore();

            //外圆
            context.save();
            context.shadowBlur = 50;
            context.shadowColor = "#123959";
            context.fillStyle = '#080D27';
            context.beginPath();
            context.arc(this.circle.x, this.circle.y, 235, 0, 2 * Math.PI, true);
            context.closePath();
            context.fill();
            context.restore();
            this.title=['累计集群总数:'+this.clusterTotal, '累计设备总数:'+this.machineList.machineTotal, '当前设备在线总数:'+this.machineList.machineOnline, '当前设备离线总数:'+this.machineList.machineOffline, '当前集群在线总数:'+this.clusterTotal, '当前集群离线总数:0'];
            for (let i = 0; i < this.title.length; i++) {//绘制文字。
                context.save()
                // 画文字
                this.drawCircularText(this.circle, this.title[i], this.rads(i * 60 - 110), this.rads(i * 60 - 65), i, context);
                context.restore();
            }
            // 旋转小球
            let x = 240 * Math.cos(everyPer);
            let y = 240 * Math.sin(everyPer);
            context.save();
            context.fillStyle = 'rgb(56,252,253)';
            context.shadowBlur = 80;
            context.shadowColor = "#39E9EE";
            context.translate(this.circle.x, this.circle.y);
            context.beginPath();
            context.arc(x,y,5,0,2*Math.PI);
            context.arc(-x,-y,5,0,2*Math.PI);
            context.closePath();
            context.fill();
            context.restore();
            //
            context.save();
            context.fillStyle = '#153776';
            context.beginPath();
            context.arc(this.circle.x, this.circle.y, 200, 0, 2 * Math.PI, true);
            context.closePath();
            context.fill();

            context.fillStyle = "#121535";
            context.beginPath();
            context.arc(this.circle.x, this.circle.y, 190, 0, 2 * Math.PI, true);
            context.closePath();
            context.fill();
            //内圆
            let nowRange = this.clusterTotal;
            context.save();
            this.drawCircle(context);
            this.drawSin(this.xOffset, context, nowRange);
            this.drawText(context, nowRange);
            context.restore();
            for (let i = 0; i < 6; i++) {//绘制刻度。
                context.save();
                context.translate(this.circle.x, this.circle.y);
                context.rotate((-Math.PI / 2 + Math.PI / 6) + i * Math.PI / 3);  //旋转坐标轴。坐标轴x的正方形从 向上开始算起
                context.beginPath();
                context.moveTo(190, 0);
                context.lineTo(200, 0);
                context.lineWidth = 4;
                context.strokeStyle = '#0A122D';
                context.stroke();
                context.closePath();
                context.restore();
            }
        },
        drawCircle(ctx) { // 画圆 中心圆
            ctx.beginPath();
            ctx.fillStyle = '#209ADF';
            ctx.arc(this.circle.x, this.circle.y, 120, 0, 2 * Math.PI);
            ctx.fill();
            ctx.beginPath();
            ctx.arc(this.circle.x, this.circle.y, 120, 0, 2 * Math.PI);
            ctx.clip();
        },
        drawSin(xOffset, ctx, nowRange) { //画sin 曲线函数
            let mW = 240;
            let mH = 240;
            let sX = 0;
            let axisLength = mW; //轴长
            let waveWidth = 0.04; //波浪宽度,数越小越宽
            let waveHeight = 12; //波浪高度,数越大越高
            ctx.save();
            ctx.translate(130, 130);
            let points = []; //用于存放绘制Sin曲线的点
            ctx.beginPath();
            //在整个轴长上取点
            for (let x = sX; x < sX + axisLength; x += 20 / axisLength) {
                //此处坐标(x,y)的取点，依靠公式 “振幅高*sin(x*振幅宽 + 振幅偏移量)”
                let y = -Math.sin((sX + x) * waveWidth + xOffset);
                let dY = mH * (1 - nowRange / 100);
                points.push([x, dY, dY + y * waveHeight]);
                ctx.lineTo(x, dY + y * waveHeight);
            }
            //封闭路径
            ctx.lineTo(axisLength, mH);
            ctx.lineTo(sX, mH);
            ctx.lineTo(points[0][0], points[0][1]);
            ctx.fillStyle = '#2C50B1';
            ctx.fill();

            ctx.restore();
        },
        // 中心显示文字
        drawText(ctx, nowRange) {
            ctx.save();
            ctx.translate(130, 130);
            let size = 50;
            ctx.font = size + 'px Microsoft Yahei';
            ctx.textAlign = 'center';
            ctx.fillStyle = "#95EFFF";
            ctx.fillText(nowRange + '', 120, 120 - size / 2);
            ctx.restore();
            ctx.save()
            size = 25;
            ctx.translate(130, 130);
            ctx.font = size + 'px Microsoft Yahei';
            ctx.textAlign = 'center';
            ctx.fillStyle = "#95EFFF";
            ctx.fillText("集群总数", 120, 120 + size);
            ctx.restore();
        },
        // 旋转的文字
        drawCircularText(s, string, startAngle, endAngle, n, context) {
            let radius = s.radius, // 文字环绕的中心圆半径
                angleDecrement, // 一个文字所占的角度
                angle = parseFloat(startAngle), // 文字的起始角度
                index = 0, // 文字的索引值
                character; // 当前要画的文字
            let arr = string.split(':')
            context.save();
            context.fillStyle = '#fff';
            context.font = '12px 微软雅黑 ';
            context.textAlign = 'center';
            context.textBaseline = 'middle';
            if (n < 2 || n === 5) { // 上三个不需要反转的文字
                while (index < string.length) {
                    character = string.charAt(index);
                    if (arr[0].indexOf(character) >= 0) {
                        if (arr[0].length > 6) {
                            angleDecrement = (startAngle - endAngle) / (string.length - 3)
                        } else {
                            angleDecrement = (startAngle - endAngle) / (string.length - 1)
                        }

                    } else {
                        angleDecrement = (startAngle - endAngle) / (string.length + 6)
                    }
                    context.save();
                    context.beginPath();
                    context.translate(s.x + Math.cos(angle) * radius,
                        s.y + Math.sin(angle) * radius);
                    context.rotate(Math.PI / 2 + angle);
                    context.fillText(character, 0, 0);
                    angle -= angleDecrement;
                    index++;
                    context.restore();
                }
            } else { // 下面三个需要反转的文字
                while (index < string.length) {
                    character = string.split("").reverse().join("").charAt(index);// 字符串反转
                    if (arr[1].indexOf(character) >= 0) {
                        angleDecrement = (startAngle - endAngle) / (string.length + 6)
                    } else {
                        if (arr[0].length > 6) {
                            angleDecrement = (startAngle - endAngle) / (string.length - 3)
                        } else {
                            angleDecrement = (startAngle - endAngle) / (string.length - 1)
                        }
                    }
                    context.save();
                    context.beginPath();
                    context.translate(s.x + Math.cos(angle) * radius,
                        s.y + Math.sin(angle) * radius);
                    context.rotate(-Math.PI / 2 + angle);// 旋转文字
                    context.fillText(character, 0, 0);
                    angle -= angleDecrement;
                    index++;
                    context.restore();
                }
            }
            context.restore();
        },

    },
    mounted() {
        this.centerBox = {
            width: document.querySelector('#circlePie').offsetWidth,
            height: document.querySelector('#circlePie').offsetHeight
        }
        for (let i = 0; i < 200; i++) {
            // 随机200个运动的圆点
            let x = Math.random() * this.centerBox.width; // 随机的x偏移量
            let y = Math.random() * this.centerBox.height; // 随机y轴偏移量
            let xa = Math.random() * 2 - 1; // x轴运动速度
            let ya = Math.random() * 2 - 1; // y轴运动速度
            this.dots.push({
                x: x,
                y: y,
                xa: xa,
                ya: ya,
                // 两个圆点之间需要连线的距离
                max: 40
            })
        }
        this.act();
        this.drawDot();
        this.resizeFn = this.$debounce(()=> {
            // 通过捕获系统的onresize事件触发我们需要执行的事件
            this.centerBox = {
                width: document.querySelector('#circlePie').offsetWidth,
                height: document.querySelector('#circlePie').offsetHeight
            }
            for (let i = 1; i < 13; i++) {
                this.$refs['chart' + i].setChart();
            }
        }, 500)
        window.addEventListener('resize', this.resizeFn)
    },
    beforeDestroy() {
        window.removeEventListener('resize', this.resizeFn)
        window.cancelAnimationFrame(this.animationFrame1)
        window.cancelAnimationFrame(this.animationFrame2)
    }
}
</script>

<style lang="less" scoped>
.backbg{
    margin-left:40px;
    position:absolute;
    z-index: 99;
}
.sel{
    background-color: #03044a;
    border: #143f93 1px solid;
    color: #65bdbd;
    margin-top: 15px;
}
.page {
    height: 100%;
    width: 100%;
    padding: 14px 20px 20px;
    background: #03044A;
    overflow: hidden;
    //overflow-x: auto;
    .content {
        height: 65%;

        .ivu-col {
            height: 100%;
        }

        .circlePie {
            height: 392px;
            padding: 0 0 40px;
            text-align: center;
            position: relative;

            canvas {
                position: absolute;
                left: 50%;
                top: 0;
                transform: translate(-50%, 0);
            }

            #dot {
                background: rgba(0, 0, 0, 0);
            }
        }

        .list {
            height: 48%;

            .left, .right {
                background: #0D1341;
            }

            .left, .right, .center {
                width: 100%;
                height: 90%;
                border: 1px solid #0D2451;
                position: relative;

                #left1, #left2, #left3, #right1, #right2, #right3, #center2 {
                    height: 100%;
                }

                .chart-68 {
                    width: 68%;
                    height: 100%;
                    float: left;
                }

                .chart-32 {
                    width: 32%;
                    height: 100%;
                    float: left;
                }
            }
        }
    }

    .bottom-list {
        height: 35%;

        .ivu-col {
            height: 100%;

            .list {
                height: 100%;
                width: 33.3333%;
                padding-right: 1.5%;
                float: left;

                #bottom_4 {
                    padding: 0 20px;
                }

                .bottom {
                    width: 100%;
                    height: 100%;
                    border: 1px solid #0D2451;
                    position: relative;
                }
            }

            .right-bottom {
                width: 100%;
                padding-right: 0;

                .bottom1 {
                    width: 100%;
                }
            }
        }
    }

}


// 操作记录列表展示
.per_inout_scroll_wrap{
    width: 460px;
    font-size: 12px;
}
.title_list{
    color: #35CDE6;
    margin: 25px auto 5px auto;
}
.slide_wrap{
    font-size: 12px;
    line-height: 31px;
    height: 31px;
    /*margin-left:19px;*/
    color: rgb(255, 255, 255);
}
.slide_wrap li,.slide_wrap ul{
    padding:0;
    margin:0;
    list-style:none;
}
.per_inout_scroll_wrap .title_list span,.per_inout_scroll_wrap .slide_wrap span{
    display:inline-block;
}
.per_inout_scroll_wrap .title_list .userName,.per_inout_scroll_wrap .slide_wrap .userName{
    width: 70px;
    text-align:center;
    margin-left:10px;
}
.per_inout_scroll_wrap .title_list .personType,.per_inout_scroll_wrap .slide_wrap .personType{
    width: 100px;
    text-align:center;
}
.per_inout_scroll_wrap .title_list .passType,.per_inout_scroll_wrap .slide_wrap .passType{
    width: 85px;
    text-align:center;
}
.per_inout_scroll_wrap .title_list .address,.per_inout_scroll_wrap .slide_wrap .address{
    width: 40px;
    text-align:center;
}
.per_inout_scroll_wrap .title_list .createTime,.per_inout_scroll_wrap .slide_wrap .createTime{
    width: 155px;
    text-align:center;
}
 .seamless-warp {
	height: 140px;
	overflow: hidden;
}
</style>
