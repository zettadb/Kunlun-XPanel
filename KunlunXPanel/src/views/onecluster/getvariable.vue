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
            <el-select v-model="form.shard_id" clearable placeholder="请选择shard名称" @change="ChangeSaler">
                <el-option
                    v-for="shard in shardList"
                    :key="shard.id"
                    :label="shard.name"
                    :value="shard.id">
                </el-option>
            </el-select>
        </el-form-item>
        <el-form-item label="节点:" prop="replica" >
            <el-select v-model="form.replica" clearable placeholder="请选择节点">
            <el-option
                v-for="item in options"
                :key="item.value"
                :label="item.label"
                :value="item.label">
            </el-option>
            </el-select>
        </el-form-item>
        <el-form-item label="变量名:" prop="variable" >
            <el-input  v-model="form.variable" placeholder="请输入变量名" />
        </el-form-item>
        <el-form-item>
            <el-button type="primary" @click="onSubmit(form)">保存</el-button>
        </el-form-item>
    </el-form>
     
  </div>
</template>

<script>
import {getShards,getVariable,getThisShardNodes } from '@/api/cluster/list'
import { messageTip} from "@/utils";
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
        const validateReplica = (rule, value, callback) => {
            if(!value){
            callback(new Error("请选择节点"));
            }
            else {
            callback();
            }
        };
        
        const variablename = (rule, value, callback) => {
            if(!value){
                callback(new Error("变量名不能为空"));
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
                replica:'',
                shard_id:'',
                variable:''
            },
            shardList:[],
            options:[],
            dialogStatusFVisible:false,
            rules:{
                shard_id: [
                    { required: true, trigger: "blur",validator: validateShardName},
                ],
                replica: [
                    { required: true, trigger: "blur",validator: validateReplica},
                ],
                variable:[
                    { required: true, trigger: "blur",validator: variablename },
                ]
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
    methods:{
        onSubmit(row) {
            this.$refs["form"].validate((valid) => {
                if (valid) {
                    let tempData = {};
                    tempData.user_name = sessionStorage.getItem('login_username');
                    tempData.job_id ='';
                    tempData.job_type ='get_variable';
                    tempData.version=version_arr[0].ver;
                    tempData.timestamp=timestamp_arr[0].time+'';
                    let paras={}
                    let ip_arr=(row.replica.substring(0,row.replica.length-1)).split('(');
                    paras.hostaddr=ip_arr[0];
                    paras.port=ip_arr[1];
                    paras.variable=row.variable;
                    tempData.paras=paras;
                    //发送接口
                    getVariable(tempData).then(response=>{
                        //this.isShowNodeMenuPanel=false;
                        let res = response;
                        this.message_tips = '正在获取实例变量...';
                        this.message_type = 'success';
                        messageTip(this.message_tips,this.message_type);
                        if(res.error_code=='0'&&res.status=='done'){
                            this.message_tips = '获取实例变量成功,value='+res.attachment.value;
                            this.message_type = 'success';
                            this.variableVisible=false;
                            messageTip(this.message_tips,this.message_type);
                        }else{
                            this.message_tips = res.error_info;
                            this.message_type = 'error';
                            messageTip(this.message_tips,this.message_type);
                        }
                    })
                }
            });
        },
        ChangeSaler(value){
            //获取节点
            const temps={};
            temps.cluster_id=this.listsent.id;
            temps.shard_id=value;
            temps.ha_mode=this.listsent.ha_mode;
            getThisShardNodes(temps).then((res) => {
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
        resetform(){
            this.form={
                 name: '',
                nick_name: '',
                id: '',
                replica:'',
                shard_id:'' ,
                variable:''
            }
        },
    }
};
</script>
<style scoped>
</style>