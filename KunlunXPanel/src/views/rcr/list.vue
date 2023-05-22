<template>
  <div class="app-container">
    <div class="filter-container">
      <div class="table-list-search-wrap">
        <el-input
          class="list_search_keyword"
          v-model="listQuery.hostaddr"
          placeholder="可输入主元数据ip搜索"
          @keyup.enter.native="handleFilter"
        />
        <el-button icon="el-icon-search" @click="handleFilter">
          查询
        </el-button>
        <el-button icon="el-icon-refresh-right" @click="handleClear">
          重置
        </el-button>
        <el-button
          class="filter-item"
          type="primary"
          icon="el-icon-plus"
          @click="handleCreate"
        >新增RCR</el-button>
        <el-button
          class="filter-item"
          type="primary"
          icon=""
          @click="drawer = true"
        >元数据管理</el-button>
      </div>
      <div class="table-list-wrap"></div>
    </div>
    <el-table
    ref="multipleTable"
      :key="tableKey"
      v-loading="listLoading"
      :data="list"
      border
      highlight-current-row
      style="width: 100%;margin-bottom: 20px;">
      <el-table-column
        type="index"
        align="center"
        label="序号"
        width="50">
      </el-table-column>

      <el-table-column label="主元数据名称" align="center" >
        <template slot-scope="{row}">
          <span class="link-type" @click="handleDetail(row)">{{row.master_meta_name}}</span>
        </template>
      </el-table-column>
      <el-table-column label="主元数据" align="center" width="160">
        <template slot-scope="{row}">
          <span>{{ row.master_rcr_meta| masterMetaWrap(row.master_rcr_meta)}}</span>
        </template>
      </el-table-column>

      <el-table-column
        prop="master_cluster_id"
        align="center"
        label="主元数据集群ID">
      </el-table-column>
       <el-table-column 
       label="备元数据名称"
        align="center"
        prop="slave_meta_name">
      </el-table-column>
      <el-table-column
        prop="slave_rcr_meta"
        align="center"
        label="备元数据"
        width="160"
        >
         <template slot-scope="{row}">
          <span class="link-type">{{ row.slave_rcr_meta| salveMetaWrap(row.slave_rcr_meta)}}</span>
        </template>
      </el-table-column>
      <el-table-column
        prop="slave_cluster_id"
        align="center"
        :show-overflow-tooltip="true"
        label="备元数据集群ID">
      </el-table-column>
      <el-table-column
        prop="shard_maps"
        align="center"
        label="shard同步信息">
        <template slot-scope="scope">
          <el-button type="text" @click="shardDetail(scope.row.shard_maps)">详情</el-button>
        </template>
      </el-table-column>
      <el-table-column
        prop="node_stats"
        align="center"
        label="状态" width="170">
        <template slot-scope="scope">
          <span v-if="scope.row.status === 'running'" style="color: #00ed37">运行中</span>
          <span v-else-if="scope.row.status === 'creating'" style="color: #c7c9d1">安装中</span>
          <span v-else-if="scope.row.status === 'manual_stop'" style="color: #c7c9d1">停止</span>
          <span v-else-if="scope.row.status === 'manual_rcrsw'" style="color: #409EFF">手动切换</span>
          <span v-else></span>
        </template>
      </el-table-column>
      <el-table-column
        prop="replica_delay"
        align="center"
        label="延迟时间">
      </el-table-column>
      <el-table-column
        label="操作"
        align="center"
        width="250"
        class-name="small-padding fixed-width">
        <template slot-scope="{row,$index}">
          <el-button type="primary" size="mini" @click="handleDalay(row)">设置延迟</el-button>
          <el-button type="primary" size="mini" v-show="row.status=='running'||row.status=='manual_stop'" @click="handleAtion(row)" style="margin-bottom: 3px">
              <span v-if="row.status === 'running'">停止</span>
              <span v-else-if="row.status === 'manual_stop'">启动</span>
          </el-button>
          <el-button type="primary" size="mini" @click="handleUpdate(row)">手动切换</el-button>
          <el-button
            size="mini"
            type="danger"
            @click="handleDelete(row,$index)"
          >删除</el-button>
        </template>
      </el-table-column>
    </el-table>
    <pagination v-show="total>0" :total="total" :page.sync="listQuery.pageNo" :limit.sync="listQuery.pageSize" @pagination="getList" />
    <!-- 新增rcr -->
    <el-dialog :title="textMap[dialogStatus]" :visible.sync="dialogFormVisible" custom-class="single_dal_view">
      <el-form
        ref="dataForm"
        :rules="rules"
        :model="temp"
        label-position="left"
        label-width="140px"
      >
        <el-form-item label="主元数据:" prop="master_rcr_meta">
          <el-select v-model="temp.master_rcr_meta" filterable  placeholder="请选择主元数据" :disabled="dialogStatus==='detail'">
            <el-option
              v-for="item in master_rcr_metas"
              :key="item.label"
              :label="item.label"
              :value="item.value"
              @click.native="masterMetaChange(item)"
              >
            </el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="主集群:" prop="master_cluster_id">
          <el-select v-model="temp.master_cluster_id" filterable  placeholder="请选择主集群"  :disabled="dialogStatus==='detail'">
            <el-option
              v-for="i in master_clusters"
              :key="i.label"
              :label="i.label"
              :value="i.value"
              @click.native="masterClusterChange(i)"
              >
            </el-option>
          </el-select>
        </el-form-item>
         <el-form-item label="备元数据:" prop="slave_rcr_meta">
          <el-select v-model="temp.slave_rcr_meta" filterable  placeholder="请选择备元数据" :disabled="dialogStatus==='detail'">
            <el-option
              v-for="item in slave_rcr_metas"
              :key="item.label"
              :label="item.label"
              :value="item.value"
              @click.native="slaveMetaChange(item)"
              >
            </el-option>
          </el-select>
        </el-form-item>
         <el-form-item label="备集群:" prop="slave_cluster_id">
          <el-select v-model="temp.slave_cluster_id" filterable  placeholder="请选择备集群"  :disabled="dialogStatus==='detail'">
            <el-option
              v-for="t in slave_clusters"
              :key="t.label"
              :label="t.label"
              :value="t.value"
              @click.native="handleChange(t)"
              >
            </el-option>
          </el-select>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="dialogFormVisible = false" v-show="!dialogDetail">关闭</el-button>
        <el-button type="primary" @click="createData()"  v-show="!dialogDetail">确认</el-button>
      </div>
    </el-dialog>
    <!-- 设置rcr延迟时间 -->
    <el-dialog :title="textMap[dialogStatus]" :visible.sync="dialogSetDalayVisible" custom-class="single_dal_view">
      <el-form
        ref="setForm"
        :rules="rules"
        :model="setform"
        label-position="left"
        label-width="140px"
      >
     <el-form-item label="最大延迟时间:" prop="maxtime" >
        <el-input  v-model="setform.maxtime" placeholder="请输入最大延迟时间">
        <i slot="suffix" style="font-style:normal;margin-right: 10px; line-height: 30px;">s</i>
        </el-input>
      </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="dialogSetDalayVisible = false" >关闭</el-button>
        <el-button type="primary" @click="setDalayData(setform)">确认</el-button>
      </div>
    </el-dialog>
    <!-- shard同步信息 -->
    <el-dialog :title="textMap[dialogStatus]" :visible.sync="dialogShardVisible" custom-class="single_dal_view"
      :close-on-click-modal="false">
      <el-form    label-position="left">
        <el-form-item  label="主元数据的主节点:" prop="master_master_host">
          <span>{{shardtemp.master_master_host}}</span>
        </el-form-item>
        <el-form-item  label="主集群shard_id:" prop="master_shard_id">
          <span>{{shardtemp.master_shard_id}}</span>
        </el-form-item>
        <el-form-item  label="主元数据的同步节点:" prop="master_sync_host">
          <span>{{shardtemp.master_sync_host}}</span>
        </el-form-item>
        <el-form-item  label="备集群shard_id:" prop="slave_shard_id">
          <span>{{shardtemp.slave_shard_id}}</span>
        </el-form-item>
      </el-form>
    </el-dialog>
     <!--状态框 -->
    <el-dialog :title="job_id" :visible.sync="dialogStatusVisible" custom-class="single_dal_view" :close-on-click-modal="false" :before-close="beforeDestory">
        <div style="width: 100%;background: #fff;padding:0 20px;">
          <el-steps direction="vertical" :active="comp_active" >
            <el-step
                v-for="(item,index) of activities"
                :key="index"
                :title="item.title"
                :icon="item.icon"
                :status="item.status"
                :description="item.description"
            >
            </el-step>
          </el-steps>
        </div>
      </el-dialog>
      <!-- 元数据管理 -->
      <el-drawer :visible.sync="drawer" direction="rtl" size="50%" :destroy-on-close="true">
        <Mate />
      </el-drawer>
  </div>
</template>
<script>
 import { messageTip,handleCofirm,getNowDate } from "@/utils";
 import { getRCRList,getMetaMachine,getStandbyMeta,addRCR,delMachine,update,createMetaTable,actionRCR,setRCRMaxDalay,findMetaDB,findMetaCluster} from '@/api/rcr/list'
 import { getEvStatus } from '@/api/cluster/list'
 import {version_arr,timestamp_arr,machine_type_arr,node_stats_arr} from "@/utils/global_variable"
 import Pagination from '@/components/Pagination' 
 import Mate from "../rcr/matelist.vue";
export default {
  name: "account",
  components: { Pagination,Mate },
  data() {
   const validateMeta=(rule, value, callback)=>{
      if (!value) {
        callback(new Error('请选择元数据'))
      } else {
        callback()
      }
    };
   const validateSlaveMeta=(rule, value, callback)=>{
      if (!value) {
        callback(new Error('请选择备数据'))
      } else {
        callback()
      }
    };
    const validateDataDir = (rule, value, callback) => {
      if (!value) {
        callback(new Error('请选择主集群'))
      } else {
        callback()
      }
    };
    const validateLogDir = (rule, value, callback) => {
     if (!value) {
        callback(new Error('请选择备集群'))
      } else {
        callback()
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
    return {
      tableKey: 0,
      list: null,
      listLoading: true,
      searchLoading:false,
      total: 0,
      listQuery: {
        pageNo: 1,
        pageSize: 10,
        hostaddr: '',
        user_name:sessionStorage.getItem('login_username')
      },
      temp: {
        master_rcr_meta: '',
        slave_rcr_meta:'',
        master_cluster_id:'',
        slave_cluster_id:''
      },
      dialogFormVisible: false,
      dialogShardVisible:false,
      dialogStatus: "",
      textMap: {
        create: "新增RCR",
        set_dalay: "设置延迟时间",
        detail: "详情",
        shardDetail:'shard同步信息'
      },
      dialogDetail: false,
      message_tips:'',
      message_type:'',
      info:'',
      row:{},
      comp_active:1,
      job_id:null,
      activities: [],
      dialogStatusVisible:false,
      timer:null,
      master_rcr_metas:[],
      master_clusters:[],
      slave_rcr_metas:[],
      slave_clusters:[],
      status:'',
      ation:'',
      shows:false,
      drawer: false,
      dialogSetDalayVisible:false,
      setform:{
        maxtime:'',
        rcr_id:'',
        master_rcr_meta:'',
        master_cluster_id:'',
        slave_cluster_id:''
      },
      shardtemp:{
        master_master_host:'',
        master_shard_id:'',
        master_sync_host:'',
        slave_shard_id:''
      },
      rules: {
        master_rcr_meta: [
          { required: true, trigger: "blur",validator: validateMeta },
        ],
        slave_rcr_meta: [
          { required: true, trigger: "blur",validator: validateSlaveMeta },
        ],
        master_cluster_id: [
          { required: true, trigger: "blur",validator: validateDataDir },
        ],
        slave_cluster_id: [
          { required: true, trigger: "blur",validator: validateLogDir },
        ],
        maxtime: [
          { required: true, trigger: "blur",validator: validatemaxtime },
        ],
      },
    };
  },
  created() {
    this.getList()
  },
  filters: {
    masterMetaWrap(value) {
      return value.replace(eval(`/${','}/g`), `\n`)
    },
    salveMetaWrap(value) {
      return value.replace(eval(`/${','}/g`), `\n`)
    }
  },
  methods:{
    shardDetail(info){
      this.dialogStatus = 'shardDetail'
      let info1=info.replace("\\", ""); 
      let shardtemp=  JSON.parse(info1.replace(/\r\n/g,'').replace(/\n/g,''));
      this.shardtemp.master_master_host=shardtemp[0].master_master_host;
      this.shardtemp.master_shard_id=shardtemp[0].master_shard_id;
      this.shardtemp.master_sync_host=shardtemp[0].master_sync_host;
      this.shardtemp.slave_shard_id=shardtemp[0].slave_shard_id;
      this.dialogShardVisible=true;
    },
    slaveMetaChange(row){
      this.temp.slave_cluster_id='';
      //先查当前元数据
      findMetaDB().then((response) => {
        if(response.ips!==row.value){
          //连上该元数据的集群id
          this.slave_clusters=[];let temp={};
          temp.res=row.value;
          if(this.temp.master_rcr_meta!==row.value){
            //主元备元不同直接取全部
            temp.cluster_id='';
          }else{
            //主元备元相同传主集群id过滤
            temp.cluster_id=this.temp.master_cluster_id;
          }
          findMetaCluster(temp).then((res) => {
            if (res.code == 200) {
              this.slave_clusters = []
              if (res.res.list.length > 0) {
                for (let i = 0; i < res.res.list.length; i++) {
                  const arr = { 'value': res.res.list[i].cluster_id, 'label': res.res.list[i].nick_name }
                  this.slave_clusters.push(arr)
                }
              }
            }else{
              this.message_tips = res.message
              this.message_type = 'error'
              messageTip(this.message_tips, this.message_type)
            }
          });
       }else{
          const temps = {}
          temps.cluster_id = this.temp.master_cluster_id;
          getStandbyMeta(temps).then((response) => {
          if (response.code == 200) {
            this.slave_clusters = []
            if (response.list.length > 0) {
              for (let i = 0; i < response.list.length; i++) {
                const arr = { 'value': response.list[i].id, 'label': response.list[i].nick_name }
                this.slave_clusters.push(arr)
              }
            }
          }else{
            this.message_tips = response.message
            this.message_type = 'error'
            messageTip(this.message_tips, this.message_type)
          }
        })
       }
      });
    },
    masterMetaChange(row){
      this.temp.slave_cluster_id='';
      this.temp.slave_rcr_meta='';
      this.temp.master_cluster_id='';
      //先查当前元数据
      findMetaDB().then((response) => {
       if(response.ips!==row.value){
        //连上该元数据的集群id
        this.master_clusters=[];
        let temp={res:row.value,cluster_id:''};
        findMetaCluster(temp).then((res) => {
          if (res.code == 200) {
            this.master_clusters = []
            if (res.res.list.length > 0) {
              for (let i = 0; i < res.res.list.length; i++) {
                const arr = { 'value': res.res.list[i].cluster_id, 'label': res.res.list[i].nick_name }
                this.master_clusters.push(arr)
              }
            }
          }else{
              this.message_tips = res.message
              this.message_type = 'error'
              messageTip(this.message_tips, this.message_type)
            }
          
        });
       }else{
          const temps = {}
          temps.cluster_id = this.temp.master_cluster_id
          getStandbyMeta(temps).then((response) => {
          if (response.code == 200) {
            this.master_clusters = []
            if (response.list.length > 0) {
              for (let i = 0; i < response.list.length; i++) {
                const arr = { 'value': response.list[i].id, 'label': response.list[i].nick_name }
                this.master_clusters.push(arr)
              }
            }
          }else{
            this.message_tips = response.message
            this.message_type = 'error'
            messageTip(this.message_tips, this.message_type)
          }
        })
       }
      });
    },
    setDalayData(row) {
      this.$refs["setForm"].validate((valid) => {
        if (valid) {
  
          const temp = {};
          temp.user_name = sessionStorage.getItem('login_username');
          temp.job_id ='';
          temp.job_type ='modify_rcr';
          temp.version=version_arr[0].ver;
          temp.timestamp=timestamp_arr[0].time+'';
          const paras={}
          paras.cluster_id=row.slave_cluster_id;
          paras.master_info={'meta_db':row.master_rcr_meta,'cluster_id':row.master_cluster_id};
          paras.work_mode = 'modify_sync_delay';
          paras.sync_delay = row.maxtime;
          temp.paras = paras;
          actionRCR(temp).then((response)=>{
            let res = response;
            //异步接口
            if(res.status=='accept'){
              this.dialogSetDalayVisible = false;
              this.job_id='设置同步延迟时间('+res.job_id+')';
              this.dialogStatusVisible=true;
              this.activities=[];
              const newArr={
                title:'正在设置同步延迟时间',
                status: 'process',
                icon: 'el-icon-loading',
                description:''
              };
              this.activities.push(newArr)
              let info='设置同步延迟时间';
              let i=0;
              this.timer = setInterval(() => {
                this.getStatus(this.timer,res.job_id,i++,info)
              }, 1000)   
            }else{
              this.message_tips = res.error_info;
              this.message_type = 'error';
              messageTip(this.message_tips,this.message_type);
            }
          });
        }
      });
    },
    handleDalay(row) {
      this.resetTemp();
      this.dialogStatus = "set_dalay";
      this.dialogSetDalayVisible = true;
      this.dialogDetail = false;
      this.setform.rcr_id=row.id;
      this.setform.master_cluster_id=row.master_cluster_id;
      this.setform.master_rcr_meta=row.master_rcr_meta;
      this.setform.slave_cluster_id=row.slave_cluster_id;
      this.$nextTick(() => {
        this.$refs.setForm.clearValidate();
      });
    },
    handleChange(event) {
      this.temp.slave_cluster_id = event.value
    },
    //清除定时器
    beforeDestory(){
      clearInterval(this.timer)
      this.dialogStatusVisible=false;
    },
    masterClusterChange(value) {
      this.temp.slave_cluster_id='';
      //获取备集群
      const temps = {}
      temps.cluster_id = value.value
      getStandbyMeta(temps).then((res) => {
        if (res.code == 200) {
          this.slave_clusters = []
          if (res.list.length > 0) {
            for (let i = 0; i < res.list.length; i++) {
              const arr = { 'value': res.list[i].id, 'label': res.list[i].nick_name }
              this.slave_clusters.push(arr)
            }
          }
        }else{
          this.message_tips = res.message
          this.message_type = 'error'
          messageTip(this.message_tips, this.message_type)
        }
      })
    },
    handleFilter() {
      this.listQuery.pageNo = 1
      this.getList()
    },
    handleClear(){
      this.listQuery.hostaddr = ''
      this.listQuery.pageNo = 1
      this.getList()
    },
    getList() {
      this.listLoading = true
      let queryParam = Object.assign({}, this.listQuery)
      //模糊搜索
      getRCRList(queryParam).then(response => {
        this.list = response.list;
        this.total = response.total;
        const tempData = {};
        tempData.user_name = sessionStorage.getItem('login_username');
        createMetaTable(tempData);
        setTimeout(() => {
          this.listLoading = false
        }, 0.5 * 1000)
      });
    },
    resetTemp() {
      this.temp = {
       master_rcr_meta: '',
       slave_rcr_meta: '',
       master_cluster_id:'',
       slave_cluster_id:'',
      };
    },
    handleCreate() {
      this.resetTemp();
      this.dialogStatus = "create";
      this.master_rcr_metas=[];
      this.slave_rcr_metas=[];
      getMetaMachine().then((res) => {
        let rcr_metas = res.list
        let createCond =
          rcr_metas === null ||
          rcr_metas.length === 0 ||
          rcr_metas === false
        if (createCond) {
          messageTip('请先添加元数据再新增RCR!', 'error')
        } else {
          this.dialogFormVisible = true;
          this.dialogDetail = false;
          if (res.list !== null) {
            for (let i = 0; i < res.list.length; i++) {
              const arr = { 'value':res.list[i].rcr_meta, 'label': res.list[i].name }
              this.master_rcr_metas.push(arr)
            }
            this.slave_rcr_metas=this.master_rcr_metas;
            if(rcr_metas.length===1){
              //获取主集群、备集群
                const temps = {}
                temps.cluster_id = ''
                getStandbyMeta(temps).then((response) => {
                if (response.code == 200) {
                  this.master_clusters = []
                  if (response.list.length > 0) {
                    for (let i = 0; i < response.list.length; i++) {
                      const arr = { 'value': response.list[i].id, 'label': response.list[i].nick_name }
                      this.master_clusters.push(arr)
                    }
                  }
                }else{
                  this.message_tips = response.message
                  this.message_type = 'error'
                  messageTip(this.message_tips, this.message_type)
                }
              })
              this.temp.master_rcr_meta=this.master_rcr_metas[0].value
              this.temp.slave_rcr_meta=this.master_rcr_metas[0].value
            }
          }    
        }
      })
    },
    createData() {
      this.$refs["dataForm"].validate((valid) => {
        if (valid) {
          const tempData = {};
          tempData.user_name = sessionStorage.getItem('login_username');
          tempData.job_id ='';
          tempData.job_type ='create_rcr';
          tempData.version=version_arr[0].ver;
          tempData.timestamp=timestamp_arr[0].time+'';
          const paras={};
          paras.cluster_id=this.temp.slave_cluster_id;
          paras.master_info={'meta_db':this.temp.master_rcr_meta,'cluster_id':this.temp.master_cluster_id}
          tempData.paras = paras;
          let postjson={postData:tempData,slave:this.temp.slave_rcr_meta}
          addRCR(postjson).then(response=>{
            let res = response;
            if(res.status=='accept'){
              this.dialogFormVisible = false;
              this.job_id='新增RCR('+res.job_id+')';
              this.dialogStatusVisible=true;
              this.activities=[];
              const newArr={
                title:'正在新增RCR',
                status: 'process',
                icon: 'el-icon-loading',
                description:''
              };
              this.activities.push(newArr)
              let i=0;
              let info='新增RCR';
              this.timer = setInterval(() => {
                this.getStatus(this.timer,res.job_id,i++,info)
              }, 1000)
            }
            else{
              this.message_tips = res.error_info;
              this.message_type = 'error';
              messageTip(this.message_tips,this.message_type);
            }
          })
        }
      });
    },
    handleDetail(row){
      this.dialogStatus = "detail"
      this.dialogFormVisible = true
      this.temp = Object.assign({}, row);
      this.dialogDetail = true
    },
    
    handleDelete(row) {
        handleCofirm("此操作将永久删除该数据, 是否继续?").then( () =>{
         const tempData = {};
          tempData.user_name = sessionStorage.getItem('login_username');
          tempData.job_id ='';
          tempData.job_type ='delete_rcr';
          tempData.version=version_arr[0].ver;
          tempData.timestamp=timestamp_arr[0].time+'';
          const paras={}
          paras.cluster_id=row.slave_cluster_id;
          paras.master_info={'meta_db':row.master_rcr_meta,'cluster_id':row.master_cluster_id}
          tempData.paras = paras;
          //console.log(tempData);return
          delMachine(tempData).then((response)=>{
            let res = response;
            if(res.status=='accept'){
              this.dialogFormVisible = false;
              this.job_id='删除RCR('+res.job_id+')';
              this.dialogStatusVisible=true;
              this.activities=[];
             const newArr={
                title:'正在删除RCR',
                status: 'process',
                icon: 'el-icon-loading',
                description:''
              };
              this.activities.push(newArr)
              let info='删除RCR';
              let i=0;
              this.timer = setInterval(() => {
                this.getStatus(this.timer,res.job_id,i++,info)
              }, 1000)   
            }else{
              this.message_tips = res.error_info;
              this.message_type = 'error';
              messageTip(this.message_tips,this.message_type);
            }
        })
        }).catch(() => {
            console.log('quxiao')
            messageTip('已取消删除','info');
        });   
    },
    handleUpdate(row) {
        handleCofirm("此操作切换集群的数据将被覆盖, 是否继续?").then( () =>{
         const tempData = {};
          tempData.user_name = sessionStorage.getItem('login_username');
          tempData.job_id ='';
          tempData.job_type ='manualsw_rcr';
          tempData.version=version_arr[0].ver;
          tempData.timestamp=timestamp_arr[0].time+'';
          const paras={}
          paras.cluster_id=row.master_cluster_id;
          paras.allow_sw_delay='100';
          paras.slave_info={'meta_db':row.master_rcr_meta,'cluster_id':row.slave_cluster_id}
          tempData.paras=paras;
          update(tempData).then((response) => {
            let res = response;
            if(res.status=='accept'){
              this.dialogFormVisible = false;
              this.job_id='手动切换RCR('+res.job_id+')';
              this.dialogStatusVisible=true;
              this.activities=[];
              const newArr={
                title:'正在手动切换RCR',
                status: 'process',
                icon: 'el-icon-loading',
                description:''
              };
              this.activities.push(newArr)
              let i=0;
              let info='手动切换RCR';
              this.timer = setInterval(() => {
                this.getStatus(this.timer,res.job_id,i++,info)
              }, 1000)
            }
            else{
              this.message_tips = res.error_info;
              this.message_type = 'error';
              messageTip(this.message_tips,this.message_type);
            }

          });
        }).catch(() => {
            console.log('quxiao')
            messageTip('已取消删除','info');
        });   
    },
    handleAtion(row) {
      let action='';
      let type='';
      if(row.status=='running'){
        action='停止'
        type='stop_rcr'
      }else if(row.status=='manual_stop'){
        action='启动'
        type='start_rcr'
      }
      handleCofirm("确定要"+action+"该RCR吗, 是否继续?").then( () =>{
        const tempData = {};
        tempData.user_name = sessionStorage.getItem('login_username');
        tempData.job_id ='';
        tempData.job_type ='modify_rcr';
        tempData.version=version_arr[0].ver;
        tempData.timestamp=timestamp_arr[0].time+'';
        const paras={}
        paras.cluster_id=row.slave_cluster_id;
        paras.master_info={'meta_db':row.master_rcr_meta,'cluster_id':row.master_cluster_id};
        paras.work_mode = type;
        tempData.paras = paras;
        actionRCR(tempData).then((response)=>{
          let res = response;
          if(res.status=='accept'){
            this.dialogFormVisible = false;
            this.job_id=action+'RCR('+res.job_id+')';
            this.dialogStatusVisible=true;
            this.activities=[];
            const newArr={
              title:'正在'+action+'RCR',
              status: 'process',
              icon: 'el-icon-loading',
              description:''
            };
            this.activities.push(newArr)
            let info=action+'RCR';
            let i=0;
            this.timer = setInterval(() => {
              this.getStatus(this.timer,res.job_id,i++,info)
            }, 1000)   
          }else{
            this.message_tips = res.error_info;
            this.message_type = 'error';
            messageTip(this.message_tips,this.message_type);
          }
      })
    }).catch(() => {
        console.log('quxiao')
        messageTip('已取消删除','info');
    });   
    },
    getStatus (timer,data,i,info) {
      setTimeout(()=>{
        const postarr={};
        postarr.job_type='get_status';
        postarr.version=version_arr[0].ver;
        postarr.job_id=data;
        postarr.timestamp=timestamp_arr[0].time+'';
        postarr.paras={}
        getEvStatus(postarr).then((res) => {
        if(res.status=='done'||res.status=='failed'){
          clearInterval(timer);
          if(res.status=='done'){
            this.activities[0].title=info+'成功'
            this.activities[0].icon='el-icon-circle-check'
            this.activities[0].status='success'
            if(info=='设置同步延迟时间'){
              let tempData={};
              tempData.rcr_id = this.setform.rcr_id;
              tempData.maxtime =this.setform.maxtime;
              tempData.user_name = sessionStorage.getItem('login_username');
              setRCRMaxDalay(tempData)
            }
            this.getList();
          }else{
            this.activities[0].title=info+'失败'
            this.activities[0].icon='el-icon-circle-close'
            this.activities[0].status='error'
            this.activities[0].description=res.error_info
          }
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
<style >
.right_input_min{
  width:25%;
}
</style>