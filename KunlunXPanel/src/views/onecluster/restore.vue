<template>
  <div class="app-container">
    <el-form ref="form" :model="form" :rules="rules" label-width="120px">
        <el-form-item label="原集群名称:" prop="old_cluster_id">
          <!-- <el-input v-model="retreatedtemp.old_cluster_name" /> -->
          <el-select v-model="form.old_cluster_id" clearable placeholder="请选择原集群名称" style="width:100%;">
            <el-option
              v-for="item in oldClusterList"
              :key="item.id"
              :label="item.name"
              :value="item.id">
            </el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="回档集群名称:" prop="nick_name">
            <span>{{form.name}}</span>
        </el-form-item>
         <el-form-item label="回档业务名称:" prop="nick_name">
            <span>{{form.nick_name}}</span>
        </el-form-item>
        <el-form-item label="回档时间:" prop="retreated_time">
          <el-date-picker v-model="form.retreated_time"  type="datetime" value-format="yyyy-MM-dd HH:mm:ss" placeholder="请选择回档时间"></el-date-picker>
        </el-form-item>
        <el-form-item>
            <el-button type="primary" @click="onSubmit(form)">回档</el-button>
            <!-- <el-button>取消</el-button> -->
        </el-form-item>
    </el-form>
    <!--状态框 -->
    <el-dialog :visible.sync="dialogStatusVisible" custom-class="single_dal_view" width="400px"  :close-on-click-modal="false" :before-close="beforeRestoreDestory">
      <div class="block">
        <el-timeline>
          <el-timeline-item
            v-for="(activity, index) in activities"
            :key="index"
            :icon="activity.icon"
            :type="activity.type"
            :color="activity.color"
            :size="activity.size"
            :timestamp="activity.timestamp">
            {{activity.content}}
          </el-timeline-item>
        </el-timeline>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import {getOldCluster,restoreCluster} from '@/api/cluster/list'
import { messageTip,handleCofirm,getNowDate} from "@/utils";
import {version_arr,timestamp_arr} from "@/utils/global_variable"
export default {
    name: "list",
    props:{
		listsent:{typeof:Object}
    },
    data() {  
        const validateretreatedTime = (rule, value, callback) => {
            const time=getNowDate();
            if(!value){
                callback(new Error("请选择回档时间"));
            }else if(value>time){
                callback(new Error("回档时间不能晚于当前时间"));
            }else {
            callback();
            }
        };
        const validateOldClusterId = (rule, value, callback) => {
            if(!value){
                callback(new Error("请选择原集群名称"));
            }
            else {
                callback();
            }
        };
        return{
            form: {
                name: '',
                nick_name: '',
                id: '',
                old_cluster_id:'',
                retreated_time:''
            },
            oldClusterList:[],
            dialogStatusVisible:false,
            activities: [],
            timer:null,
            rules:{
                retreated_time:[
                    {required: true, trigger: "blur",validator: validateretreatedTime }
                ],
                old_cluster_id:[
                    {required: true, trigger: "blur",validator: validateOldClusterId }
                ],
            }
        }
    },
    mounted(){
        this.form.name=this.listsent.name;
        this.form.nick_name=this.listsent.nick_name;
        this.form.id=this.listsent.id;
        let temp={cluster_id:this.listsent.id}
        //获取原集群名称
        getOldCluster(temp).then((res) => {
            this.oldClusterList = res.list;
        });
        this.$nextTick(() => {
            if(this.$refs["form"]){
            this.$refs["form"].clearValidate();
            }
        });
    },
    destroyed() {
      clearInterval(this.timer)
      this.timer = null
    },
    methods:{
        beforeRestoreDestory(){
            clearInterval(this.timer)
            this.dialogStatusVisible=false;
            this.timer = null;
        },
        onSubmit(row) {
            this.$refs["form"].validate((valid) => {
            if (valid) {
            //const tempData = Object.assign({}, this.row);
            const restoreData={};
            restoreData.user_name=sessionStorage.getItem('login_username');
            restoreData.job_id = '';
            restoreData.version=version_arr[0].ver;
            restoreData.job_type='cluster_restore';
            restoreData.timestamp=timestamp_arr[0].time+'';
            const paras={}
            paras.src_cluster_id = row.old_cluster_id;
            paras.dst_cluster_id = row.id;
            paras.restore_time=row.retreated_time;
            // paras.machinelist=machinelist;
            restoreData.paras=paras;
            //console.log(row);return;
            //console.log(restoreData);return;
            handleCofirm(row.nick_name+"集群的数据将被覆盖，确定要进行回档操作么?").then( () =>{
            restoreCluster(restoreData).then((response) => {
                let res = response;
                if(res.status=='accept'){
                this.dialogRetreatedVisible = false;
                this.dialogStatusVisible=true;
                this.activities=[];
                const newArr={
                    content:'正在回档集群...',
                    timestamp: getNowDate(),
                    size: 'large',
                    type: 'primary',
                    icon: 'el-icon-more'
                };
                this.activities.push(newArr);
                //this.message_tips = '正在恢复...';
                //this.message_type = 'success';
                //调获取状态接口
                let i=0;this.timer=null;
                this.getStatus(this.timer,res.job_id,i++)
                this.timer = setInterval(() => {
                    this.getStatus(this.timer,res.job_id,i++)
                }, 5000)
                }
                else if(res.status=='ongoing'){
                this.message_tips = '系统正在操作中，请等待一会！';
                this.message_type = 'error';
                messageTip(this.message_tips,this.message_type);
                }else{
                this.message_tips = res.error_info;
                this.message_type = 'error';
                messageTip(this.message_tips,this.message_type);
                }
            });
            }).catch(() => {
                    messageTip('已取消回档','info');
                }); 
            }
            })
        }
    }
};
</script>