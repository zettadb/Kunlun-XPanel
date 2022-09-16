<template>
  <div class="app-container">
    <el-form ref="form" :model="form" label-width="100px" :rules="rules">
        <el-form-item label="集群名称">
            <span>{{form.name}}</span>
        </el-form-item>
        <el-form-item label="业务名称">
            <span>{{form.nick_name}}</span>
        </el-form-item>
        <el-form-item label="shard名称:" prop="shard_id" >
            <el-select v-model="form.shard_id" clearable placeholder="请选择shard名称">
                <el-option
                    v-for="shard in shardList"
                    :key="shard.id"
                    :label="shard.name"
                    :value="shard.id"
                    @click.native="ChangeSaler(shard)"
                    >
                </el-option>
            </el-select>
        </el-form-item>
        <el-form-item label="主节点:" prop="primary_node">
            <span>{{form.primary_node}}</span>
        </el-form-item>
        <el-form-item label="备机节点:" prop="replica" >
            <el-select v-model="form.replica" clearable placeholder="请选择备机节点">
            <el-option
                v-for="item in options"
                :key="item.value"
                :label="item.label"
                :value="item.label">
            </el-option>
            </el-select>
        </el-form-item>
        <el-form-item>
            <el-button type="primary" @click="onSubmit(form)">保存</el-button>
            <!-- <el-button>取消</el-button> -->
        </el-form-item>
    </el-form>
     <!-- 主备切换状态框 -->
    <el-dialog :title="job_id" :visible.sync="dialogStatusFVisible" custom-class="single_dal_view" :close-on-click-modal="false" :before-close="beforeSwitchDestory">
      <div style="height: 400px;">
        <el-steps direction="vertical" :active="active" finish-status="success">
          <el-step
              v-for="(item,index) of stepParams"
              :key="index"
              :title="item.title"
              :icon="item.icon"
              :status="item.status"
              :description="item.description"
              :class="stepSuc.includes(index) ? 'stepSuc':'stepErr'"
              @click.native="handleStep(index)"
          />
        </el-steps>
      </div> 
    </el-dialog>
  </div>
</template>

<script>
import {getShards,switchShard,getShardPrimary,getStandbyNode,getEvStatus } from '@/api/cluster/list'
import { messageTip,createCode,gotoCofirm} from "@/utils";
import {version_arr,timestamp_arr} from "@/utils/global_variable"
export default {
    name: "list",
    props:{
		listsent:{typeof:Object}
    },
    data() {  
        const validateShardName = (rule, value, callback) => {
            if(!value){
            callback(new Error("请选择shard名称"));
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
                primary_node:'',
                replica:'',
                shard_id:'',
                shard_name:''
            },
            shardList:[],
            options:[],
            dialogStatusFVisible:false,
            active: 0,
            // 已选步骤
            stepSuc: [0],
            stepParams: [],
            job_id:'',
            timer:null,
            rules:{
                shard_id: [
                    { required: true, trigger: "blur",validator: validateShardName},
                ],
            }
        }
    },
    created(){
        //获取分片名称
        getShards(this.listsent.id).then((response) => {
          let res = response;
          if(res.code==200){
          this.shardList=res.list;
          }
        });
    },
    mounted(){
        this.form.name=this.listsent.name;
        this.form.nick_name=this.listsent.nick_name;
        this.form.id=this.listsent.id;
    },
    destroyed() {
      clearInterval(this.timer)
      this.timer = null
    },
    methods:{
        onSubmit(row) {
            this.$refs["form"].validate((valid) => {
                if (valid) {
                const code=createCode();
                let string="将对"+this.form.shard_name+"进行主备切换,是否继续?code="+code;
                gotoCofirm(string).then( (res) =>{  
                    //先执行删权限
                    if(!res.value){
                    this.message_tips = 'code不能为空！';
                    this.message_type = 'error';
                    messageTip(this.message_tips,this.message_type);
                    }else if(res.value==code){
                        const tempData = {};
                        tempData.user_name = sessionStorage.getItem('login_username');
                        tempData.job_id ='';
                        tempData.job_type ='manual_switch';
                        tempData.version=version_arr[0].ver;
                        tempData.timestamp=timestamp_arr[0].time+'';
                        let master_ip=(this.form.primary_node.substring(0,this.form.primary_node.length-1)).replace('(','_');
                        let paras={};
                        if(!this.form.replica.length){
                            paras.shard_id=this.form.shard_id;
                            paras.cluster_id=this.form.id;
                            paras.master_hostaddr=master_ip;
                        }else{
                            let assign_ip=(this.form.replica.substring(0,this.form.replica.length-1)).replace('(','_');
                            paras.shard_id=this.form.shard_id;
                            paras.cluster_id=this.form.id;
                            paras.master_hostaddr=master_ip;
                            paras.assign_hostaddr=assign_ip;
                        }
                        paras.nick_name=this.listsent.nick_name;
                        tempData.paras=paras;
                        // console.log(tempData);return;
                        //发送接口
                        switchShard(tempData).then(response=>{
                            let res = response;
                            if(res.status=='accept'){
                                this.dialogSwitchOVisible = false;
                                this.stepParams=[];
                                let i=0;
                                const info='主备切换';
                                this.job_id='';this.timer=null;
                                this.timer = setInterval(() => {
                                    this.getFStatus(this.timer,res.job_id,i++,info,'')
                                }, 1000)
                            }else{
                                this.message_tips = res.error_info;
                                this.message_type = 'error';
                                messageTip(this.message_tips,this.message_type);
                            }
                        })
                    }else{
                        this.message_tips = 'code输入有误';
                        this.message_type = 'error';
                        messageTip(this.message_tips,this.message_type);
                    }
                })
                }
            });
        },
        ChangeSaler(value){
            // console.log(value);
            if(!value){
                this.form.primary_node='';
            }
            this.form.shard_name=value.name;
            //获取主节点
            const temp={};
            temp.cluster_id=this.listsent.id;
            temp.shard_id=value.id;
            temp.ha_mode=this.listsent.ha_mode;
            getShardPrimary(temp).then((res) => {
                if(res.list.length>0){
                    this.form.primary_node=res.list[0].ip+'('+res.list[0].port+')';
                }
            });
            //获取备机节点
            const temps={};
            temps.cluster_id=this.listsent.id;
            temps.shard_id=value.id;
            temps.ha_mode=this.listsent.ha_mode;
            getStandbyNode(temps).then((res) => {
                if(res.code==200){
                    this.options=[];
                    if(res.list.length>0){
                        for (let i = 0; i < res.list.length; i++) {
                            const arr={'value':i,'label':res.list[i].ip+'('+res.list[i].port+')'};
                            this.options.push(arr);
                        }
                    }
                }
            });
        },
        beforeSwitchDestory(){
            clearInterval(this.timer)
            this.dialogStatusFVisible=false;
            this.timer = null;
            this.resetform();
        },
        resetform(){
            this.form={
                 name: this.listsent.name,
                nick_name: this.listsent.nick_name,
                id:this.listsent.id,
                primary_node:'',
                replica:'',
                shard_id:'' ,
                shard_name:''
            }
        },
        getFStatus (timer,data,i,info,iparr) {
            setTimeout(()=>{
                const postarr={}
                postarr.job_type='get_status';
                postarr.version=version_arr[0].ver;
                postarr.timestamp=timestamp_arr[0].time+'';
                postarr.job_id=data;
                postarr.paras={};
                this.job_id='任务ID:'+data;
                getEvStatus(postarr).then((res) => {
                    if(info=='主备切换'){
                        if(res.attachment!==null){
                        this.dialogStatusFVisible=true;
                        const steps=(res.attachment.job_step).replace(/\s+/g, '').split(',');
                        if(this.stepParams.length==0){
                        for(let a=0;a<steps.length;a++){
                            let newArrgoing={}
                            if(a==0){
                            newArrgoing.title=steps[a];
                            newArrgoing.icon='el-icon-loading';
                            newArrgoing.status= 'process';
                            newArrgoing.description='';
                            }else{
                            newArrgoing.title=steps[a];
                            newArrgoing.icon= '';
                            newArrgoing.status= 'wait';
                            newArrgoing.description='';
                            }
                            if(res.attachment.step==steps[a]){
                            if(a>(this.active)){
                                for(let k=0;k<this.stepParams.length;k++){
                                if(k==a){
                                    if(this.stepParams[k].status=='wait'){
                                    this.stepParams[k].icon='el-icon-loading';
                                    this.stepParams[k].status='process';
                                    }
                                }
                                if(k>0&&k<a){
                                    //小于i的情况
                                    for(let j=0;j<k;j++){
                                    this.stepParams[j].icon='el-icon-circle-check';
                                    this.stepParams[j].status='success';
                                    }
                                }
                                }
                            }
                            this.active=a;
                            }
                            this.stepParams.push(newArrgoing)
                        }
                        }else{
                        if(res.status=='ongoing'){
                            //console.log(11);
                            for(let k=0;k<this.stepParams.length;k++){
                            //console.log(this.stepParams.length);
                            //console.log(typeof this.stepParams[k].title);
                            //console.log(this.stepParams[k].title);
                            //console.log(typeof res.attachment.step);
                            //console.log(res.attachment.step);
                            if(res.attachment.step==this.stepParams[k].title){
                                //console.log(44);
                                this.active=k;
                                this.stepParams[k].icon='el-icon-loading';
                                this.stepParams[k].status='process';
                                if(k>0&&k<(this.active+1)){
                                //小于i的情况
                                for(let j=0;j<k;j++){
                                    this.stepParams[j].icon='el-icon-circle-check';
                                    this.stepParams[j].status='success';
                                    //console.log(55);
                                }
                                }
                            }
                            }
                            //console.log(this.active);
                        }else if(res.status=='done'){
                            for(let k=0;k<this.stepParams.length;k++){
                            this.active=this.stepParams.length;
                            this.stepParams[k].icon='el-icon-circle-check';
                            this.stepParams[k].status='success';
                            }
                            clearInterval(timer);
                            //this.getCluster();
                        }else if(res.status=='failed'){
                            //console.log(22);
                            for(let k=0;k<this.stepParams.length;k++){

                            //if(res.attachment.step==this.stepParams[k].title){
                                if(this.stepParams[k].status=='process'){
                                this.active=k;
                                this.stepParams[k].icon='el-icon-circle-close';
                                this.stepParams[k].status='error';
                                this.stepParams[k].description=res.error_info;
                                }
                            //}
                            }
                            clearInterval(timer);
                        }
                        }
                        }else if(res.attachment==null&&res.error_code=='70001'&&res.status=='failed'){
                        if(i>5){
                            clearInterval(timer);
                        }
                        }
                    }
                });
                if(i>=86400){
                    clearInterval(timer);
                }
            }, 0)
        },
    }
};
</script>
<style scoped>
</style>