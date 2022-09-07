<template>
  <div class="app-container">
    <el-form ref="form" :model="form" label-width="120px" :rules="rules">
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
        <el-form-item label="最大延迟时间:" prop="maxtime" >
            <el-input  v-model="form.maxtime" placeholder="请输入最大延迟时间">
            <i slot="suffix" style="font-style:normal;margin-right: 10px; line-height: 30px;">s</i>
            </el-input>
        </el-form-item>
        <el-form-item>
            <el-button type="primary" @click="onSubmit(form)">保存</el-button>
        </el-form-item>
    </el-form>
  </div>
</template>

<script>
import {getShards,getMaxDalay,setMaxDalay } from '@/api/cluster/list'
import { messageTip} from "@/utils";
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
        const validatemaxtime = (rule, value, callback) => {
            if(!value){
                callback(new Error("最大延迟时间不能为空"));
            }else if(!(/^[0-9]+$/.test(value))){
                callback(new Error("最大延迟时间只能输入数字"));
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
                shard_id:'',
                maxtime:''
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
                maxtime: [
                    { required: true, trigger: "blur",validator: validatemaxtime},
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
                    let tempData={};
                    tempData.cluster_id = row.id;
                    tempData.shard_id = row.shard_id;
                    tempData.maxtime = row.maxtime;
                    tempData.user_name = sessionStorage.getItem('login_username');
                    //console.log(tempData);return;
                    //发送接口
                    setMaxDalay(tempData).then(response=>{
                        let res = response;
                        if(res.code==200){
                        this.dialogDalayVisible = false;
                        this.message_tips = res.message;
                        this.message_type = 'success';
                        messageTip(this.message_tips,this.message_type);
                        }
                        else{
                        this.message_tips = res.message;
                        this.message_type = 'error';
                        messageTip(this.message_tips,this.message_type);
                        }
                    })
                }
            });
        },
        ChangeSaler(value){
            //获取最大延迟时间
            const temp={};
            temp.cluster_id=this.listsent.id;
            temp.shard_id=value;
            temp.user_name = sessionStorage.getItem('login_username');
            getMaxDalay(temp).then((res) => {
                if(res.code==200){
                    if(res.list==false){
                        this.form.maxtime='100';
                    }else{
                        this.form.maxtime=res.list[0].max_delay_time;
                    }
                }
            });

        },
        resetform(){
            this.form={
                name: '',
                nick_name: '',
                id: '',
                shard_id:'',
                maxtime:''
            }
        },
        
    }
};
</script>
<style scoped>
.el-input{
    width:inherit;
}
</style>