<template>
  <div class="app-container">
    <el-form ref="form" :model="form" label-width="240px">
        <el-form-item label="集群ID:">
            <span>{{form.id}}</span>
        </el-form-item>
        <el-form-item label="集群名称:">
            <span>{{form.name}}</span>
        </el-form-item>
        <el-form-item label="业务名称:">
            <span>{{form.nick_name}}</span>
        </el-form-item>
         <el-form-item label="高可用模式:">
            <span>{{form.ha_mode}}</span>
        </el-form-item>
         <el-form-item label="shard分配:">
            <span>{{form.shardtotal}}</span>
        </el-form-item>
        <el-form-item label="计算节点总个数:">
            <span>{{form.comptotal+'个'}}</span>
        </el-form-item>
        <el-form-item label="缓冲池大小1:">
            <span>{{form.buffer_pool+'MB'}}</span>
        </el-form-item>
        <el-form-item label="每shard中强同步备机应当个数:">
            <span>{{form.fullsync_level+'个'}}</span>
        </el-form-item>
        <!-- <el-form-item>
            <el-button type="primary" @click="onSubmit(form)">保存</el-button>
            <el-button>取消</el-button>
        </el-form-item> -->
    </el-form>
  </div>
</template>

<script>
export default {
    name: "commonset",
    props:{
		listsent:{typeof:Object}
    },
    data() {  
        return{
            form: {
            name: '',
            nick_name: '',
            id: '',
            ha_mode:''
            }
        }
    },
    mounted(){
        // console.log(this.listsent);
        this.form.name=this.listsent.name;
        this.form.nick_name=this.listsent.nick_name;
        this.form.id=this.listsent.id;
        this.form.ha_mode=this.listsent.ha_mode;
        this.form.fullsync_level=this.listsent.fullsync_level;
        this.form.buffer_pool=this.listsent.buffer_pool;
        this.form.comptotal=this.listsent.comp_count;
        // this.form.shards_count=this.listsent.shards_count;
        this.form.shardtotal=this.listsent.shardtotal;
    },
    methods:{
        onSubmit(row) {
            // console.log(row);
        }
    }
};
</script>
<style scoped>
</style>