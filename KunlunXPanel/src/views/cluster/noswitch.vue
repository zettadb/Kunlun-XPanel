<template>
  <div class="app-container">
    <div class="filter-container">
      <div class="table-list-search-wrap">
        <!-- <el-input
          class="list_search_keyword"
          v-model="listQuery.name"
          placeholder="可输入目标名称搜索"
          @keyup.enter.native="handleFilter"
        />
        <el-button icon="el-icon-search" @click="handleFilter">
          查询
        </el-button>
        <el-button icon="el-icon-refresh-right" @click="handleClear">
          重置
        </el-button> -->
        <el-button
          class="filter-item"
          type="primary"
          @click="handleCreate"
          v-if="user_name=='super_dba'"
        >免切设置</el-button>
        <div v-text="info" v-show="installStatus===true" class="info"></div>
      </div>
      <div class="table-list-wrap">

      </div>
    </div>

    <el-table
      :key="tableKey"
      v-loading="listLoading"
      :data="list"
      border
      highlight-current-row
      style="width: 100%;margin-bottom: 20px;">
    >
      <el-table-column
        type="index"
        align="center"
        label="序号"
        width="50">
      </el-table-column>

      <el-table-column label="集群ID" align="center">
        <template slot-scope="scope">
          <span v-if="scope.row.cluster_id==='all'">所有集群</span>
          <span v-else>{{scope.row.cluster_id}}</span>
        </template>
      </el-table-column>
      <el-table-column
        prop="shard_id"
        align="center"
        label="shardID">
        <template slot-scope="scope">
          <span v-if="scope.row.shard_id">{{scope.row.shard_id}}</span>
          <span v-else>所有shard</span>
        </template>
      </el-table-column>
      <el-table-column
            prop="param.timestamp"
            align="center"
            label="超时时间">
          <template slot-scope="{row}">
            <span class="link-type" @click="handleDetail(row)">{{ row.param.timestamp|formatDate }}</span>
          </template>
      </el-table-column>
      <el-table-column
            align="center"
            label="是否超时">
        <template slot-scope="scope">
          <span >{{scope.row.param.timestamp|compareTime}}</span>
        </template>
      </el-table-column>
      
      <el-table-column
        label="操作"
        align="center"
        width="300"
        class-name="small-padding fixed-width"
        v-if="user_name=='super_dba'"
      >
        <template slot-scope="{row,$index}">
          <el-button
            size="mini"
            type="danger"
            @click="handleDelete(row,$index)"
            v-if="user_name=='super_dba'"
          >删除</el-button>
          <div v-text="info" v-show="installStatus===true" class="info"></div>
        </template>
      </el-table-column>
    </el-table>

    <pagination v-show="total>0" :total="total" :page.sync="listQuery.pageNo" :limit.sync="listQuery.pageSize" @pagination="getList" />

    <el-dialog :title="textMap[dialogStatus]" :visible.sync="dialogFormVisible" custom-class="single_dal_view">
      <el-form
        ref="dataForm"
        :rules="rules"
        :model="temp"
        label-position="left"
        label-width="140px"
      >
        <el-form-item label="集群名称:" prop="nick_name" >
          <div style="max-height: 100px;overflow-y: auto;">
         <el-checkbox :indeterminate="isIndeterminate" v-model="checkAll" @change="handleCheckAllChange">all<span style="font-size: 12px;color: #F56C6C;">（勾选all表示所有的集群+所有shard都设置免切）</span></el-checkbox>
          <div style="margin: 15px 0;"></div>
          <el-checkbox-group v-model="checkedCluster" prop="checkedCluster" @change="handleCheckedCitiesChange">
            <el-checkbox v-for="cluster in clusters" :label="cluster.id" :key="cluster.id">{{cluster.nick_name}}</el-checkbox>
          </el-checkbox-group>
          </div>
        </el-form-item>
        <el-form-item label="shard名称:" prop="shard_id" v-if="checkedCluster.length==1">
          <el-select v-model="temp.shard_id" clearable placeholder="请选择shard名称">
              <el-option
                v-for="shard in shardList"
                :key="shard.id"
                :label="shard.name"
                :value="shard.id">
              </el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="超时时间:" prop="timeout">
          <el-input  v-model="temp.timeout" placeholder="请输入超时时间" >
            <i slot="suffix" style="font-style:normal;margin-right: 10px; line-height: 30px;">s</i>
          </el-input>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="dialogFormVisible = false" v-show="!dialogDetail">关闭</el-button>
        <el-button type="primary" @click="dialogStatus==='create'?createData():updateData(row)"  v-show="!dialogDetail">确认</el-button>
      </div>
    </el-dialog>

  </div>
</template>

<script>
 import { messageTip,gotoCofirm,createCode } from "@/utils";
 import {getShards,setNoSwitch,updateStorage,delSwitch,getEvStatus,getNoSwitchList} from '@/api/cluster/list'
 import {version_arr,storage_type_arr, timestamp_arr} from "@/utils/global_variable"
 import Pagination from '@/components/Pagination' 
 import {getAllCluster} from '@/api/system/access'


export default {
  name: "account",
  components: { Pagination },
  data() {
   const validateCheckedCluster=(rule, value, callback)=>{
      if(this.checkedCluster.length<=0){
          callback(new Error("请勾选集群名称"));
        }else{
          callback();
        }
    };
    const validateTimeOut = (rule, value, callback) => {
      const number= /^[0-9]*$/;
      if(!value){
        callback(new Error("请输入超时时间"));
      }else if(!number.test(value)){
        callback(new Error("超时时间只允许输入数字"));
      }else {
        callback();
      }
    };
    
    return {
      tableKey: 0,
      list: null,
      listLoading: true,
      searchLoading:false,
      total: 0,
      listQuery: {
        pageNo: 1,
        pageSize: 10,
        name: '',
      },
      temp: { 
        timeout:'',
        checkedCluster: [],
        shard_id:''
      },
      dialogFormVisible: false,
      dialogEditVisible:false,
      dialogStatus: "",
      textMap: {
        update: "编辑备份存储目标",
        create: "免切设置",
        detail: "详情"
      },
      dialogDetail: false,
      message_tips:'',
      message_type:'',
      installStatus:false,
      info:'',
      row:{},
      stypelist:storage_type_arr,
      user_name:sessionStorage.getItem('login_username'),
      clusters:[],
      clusterTotal:0,
      checkedCluster: [],
      checkAll: false,
      isIndeterminate: false,
      shardList:[],
      timer:null,
      rules: {
        nick_name: [
          { required: true, trigger: "blur",validator: validateCheckedCluster },
        ],
        timeout: [
          { required: true, trigger: "blur",validator: validateTimeOut },
        ]
      },
    };
  },
  created() {
    this.getList()
    this.getCluster()
  },
  filters: {
      formatDate: function (value) {
          let date = new Date(value *1000);//这个是纳秒的，想要毫秒的可以不用除以1000000
          let y = date.getFullYear();
          let MM = date.getMonth() + 1;
          MM = MM < 10 ? ('0' + MM) : MM;
          let d = date.getDate();
          d = d < 10 ? ('0' + d) : d;
          let h = date.getHours();
          h = h < 10 ? ('0' + h) : h;
          let m = date.getMinutes();
          m = m < 10 ? ('0' + m) : m;
          let s = date.getSeconds();
          s = s < 10 ? ('0' + s) : s;
          return y + '-' + MM + '-' + d + ' ' + h + ':' + m + ':' + s;
        },
        compareTime:function(value){
          if(value<timestamp_arr[0].time){
            return '是'
          }else{
            return '否'
          }
        }
    },
  watch: {
    'checkedCluster': {
      handler: function(val,oldVal) {
        this.temp.checkedCluster=[];
        this.checkedCluster.forEach(item => {
        if (this.temp.checkedCluster.indexOf(item) == -1) {
          this.temp.checkedCluster.push(item)
        }
        if(this.checkedCluster.length==1){
          //获取分片名称
          getShards(this.checkedCluster[0]).then((response) => {
            let res = response;
            if(res.code==200){
            this.shardList=res.list;
            }
          });
        }
      })
      },
      immediate: true
    }
  },
  methods: {
    handleCheckAllChange(val) {
      let ids=[];
      for(let i=0;i<this.clusters.length;i++){
        ids.push(this.clusters[i].id);
      }
      this.checkedCluster = val ? ids : [];
      this.isIndeterminate = false;
    },
    handleCheckedCitiesChange(value) {
      let checkedCount = value.length;
      this.checkAll = checkedCount === this.clusters.length;
      this.isIndeterminate = checkedCount > 0 && checkedCount < this.clusters.length;
    },
    async getCluster() {
      const res = await getAllCluster()
      this.clusters = res.list;
      this.clusterTotal=res.total;
    },
    handleFilter() {
      this.listQuery.pageNo = 1
      this.getList()
    },
    handleClear(){
      this.listQuery.name = ''
      this.listQuery.pageNo = 1
      this.getList()
    },
    getList() {
      this.listLoading = true
      this.installStatus = false
      let queryParam = Object.assign({}, this.listQuery)
      const temp={};
      temp.version=version_arr[0].ver;
      temp.job_id='';
      temp.job_type='list_noswitch';
      temp.timestamp=timestamp_arr[0].time+'';
      temp.paras={"cluster_id":"all"};
      //模糊搜索
      getNoSwitchList(temp).then(response => {
        if(response.attachment.length!==0){
          this.list = response.attachment.value;
          this.total = response.attachment.value.length;
          //console.log(this.list)
        }else{
          this.list =[];
          this.total=0;
        }
        setTimeout(() => {
          this.listLoading = false
        }, 0.5 * 1000)
      });
    },
    resetTemp() {
      this.temp = {
        timeout:'',
        checkedCluster: [],
        shard_id:''
      };
    },
    handleCreate() {
      this.resetTemp();
      this.dialogStatus = "create";
      this.dialogFormVisible = true;
      this.dialogDetail = false;
      this.$nextTick(() => {
        this.$refs.dataForm.clearValidate();
      });
    },
    createData() {
      this.$refs["dataForm"].validate((valid) => {
        if (valid) {
          const tempData = {};
          tempData.job_id ='';
          tempData.job_type ='set_noswitch';
          tempData.version=version_arr[0].ver;
          tempData.timestamp=timestamp_arr[0].time+'';
          tempData.user_name=sessionStorage.getItem('login_username');
          let paras={}
          if(this.temp.checkedCluster.length==this.clusters.length){
            paras.cluster_id='all';
            if(this.temp.shard_id!==''){
              paras.shard_id=this.temp.shard_id;
            }
            paras.timeout=this.temp.timeout;
            paras.type='1';
            tempData.paras=paras;
            //console.log(tempData);return;
            setNoSwitch(tempData).then(response=>{
              let res = response;
              this.message_tips = '正在设置免切...';
              this.message_type = 'success';
              messageTip(this.message_tips,this.message_type);
              if(res.error_code=='0'){
                this.dialogFormVisible = false;
                this.message_tips = '免切设置成功';
                this.message_type = 'success';
                messageTip(this.message_tips,this.message_type);
                this.getList();
              }else{
                this.message_tips = res.error_info;
                this.message_type = 'error';
                messageTip(this.message_tips,this.message_type);
              }
            })
          }else{
            for(let i=0;i<this.temp.checkedCluster.length;i++){
              paras.cluster_id=this.temp.checkedCluster[i];
              if(this.temp.shard_id!==''){
                paras.shard_id=this.temp.shard_id;
              }
              paras.timeout=this.temp.timeout;
              paras.type='1';
              tempData.paras=paras;
              //console.log(tempData);return;
              //发送接口
              setNoSwitch(tempData).then(response=>{
                let res = response;
                this.message_tips = '正在设置免切...';
                this.message_type = 'success';
                messageTip(this.message_tips,this.message_type);
                if(res.error_code=='0'){
                  this.dialogFormVisible = false;
                  this.message_tips = '免切设置成功';
                  this.message_type = 'success';
                  messageTip(this.message_tips,this.message_type);
                  this.getList();
                }else{
                  this.message_tips = res.error_info;
                  this.message_type = 'error';
                  messageTip(this.message_tips,this.message_type);
                }
              })
       
            }
          }
          }
      });
    },
    handleDetail(row){
      this.dialogStatus = "detail"
      this.dialogFormVisible = true
      this.temp = Object.assign({}, row);
      this.dialogDetail = true
    },
    handleUpdate(row) {
      this.temp = Object.assign({}, row); 
      this.dialogStatus = "update";
      this.dialogFormVisible = true;
      this.dialogDetail = false;
      this.$nextTick(() => {
        this.$refs["dataForm"].clearValidate();
      });
    },
    updateData() {
      this.$refs["dataForm"].validate((valid) => {
        if (valid) {
          const tempData = {};
          tempData.job_id ='';
          tempData.job_type ='update_backup_storage';
          tempData.version=version_arr[0].ver;
          tempData.timestamp=timestamp_arr[0].time+'';
          tempData.user_name=sessionStorage.getItem('login_username');
          tempData.paras=Object.assign({}, this.temp);
          updateStorage(tempData).then((response) => {
            let res = response;
            if(res.status=='accept'){
              this.dialogFormVisible = false;
              this.message_tips = '正在编辑备份存储目标...';
              this.message_type = 'success';
              //调获取状态接口
              let i=0;
              this.timer = setInterval(() => {
                this.getStatus(this.timer,res.job_id,i++)
              }, 1000)
            }else if(res.status=='ongoing'){
              this.message_tips = '系统正在操作中，请等待一会！';
              this.message_type = 'error';
            }else{
              this.message_tips = res.error_info;
              this.message_type = 'error';
            }
            messageTip(this.message_tips,this.message_type);

          });
        }
      });
    },
    handleDelete(row) {
      const code=createCode();
      let string="此操作将永久删除集群ID为"+row.cluster_id+"的免切数据,是否继续?code="+code;
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
          tempData.job_type ='del_noswitch';
          tempData.version=version_arr[0].ver;
          tempData.timestamp=timestamp_arr[0].time+'';
          const paras={};
          paras.cluster_id=row.cluster_id;
          if(row.shard_id){
            paras.shard_id=row.shard_id;
          }
          tempData.paras=paras;
          //console.log(tempData);return;
          delSwitch(tempData).then((response)=>{
            let res = response;
            this.message_tips = '正在删除';
            this.message_type = 'success';
            messageTip(this.message_tips,this.message_type);
            if(res.error_code=='0'){
              //this.dialogFormVisible = false;
              this.message_tips = '删除成功';
              this.message_type = 'success';
              messageTip(this.message_tips,this.message_type);
              this.getList();
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
      }).catch(() => {
      console.log('quxiao')
      messageTip('已取消删除','info');
      });
    },
    getStatus (timer,data,i) {
      setTimeout(()=>{
        const postarr={};
        postarr.job_type='get_status';
        postarr.version=version_arr[0].ver;
        postarr.job_id=data;
        postarr.timestamp=timestamp_arr[0].time+'';
        postarr.paras={};
        getEvStatus(postarr).then((res) => {
        if(res.status=='done'||res.status=='failed'){
          clearInterval(timer);
          this.info=res.error_info;
          if(res.status=='done'){
            this.getList();
          }else{
            this.installStatus = true;
          }
        }else{
          this.info=res.error_info;
          this.installStatus = true;
        }
      });
        if(i>=86400){
            clearInterval(timer);
        }
      }, 0)
    }

  },
};
</script>