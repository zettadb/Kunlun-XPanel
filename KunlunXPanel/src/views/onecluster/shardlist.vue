<template>
  <div class="app-container">
    <div class="filter-container">
      <div class="table-list-search-wrap">
        <el-input
          class="list_search_keyword"
          v-model="listQuery.hostaddr"
          placeholder="可输入ip搜索"
          @keyup.enter.native="handleFilter"
        />
        <el-select v-model="listQuery.master" placeholder="请选择主/备节点" class="list_search_select" style="width:150px;">
          <el-option label="主节点" value="source"></el-option>
          <el-option label="备节点" value="replica"></el-option>
        </el-select>
        <el-select v-model="listQuery.status" placeholder="请选择状态" class="list_search_select" style="width:150px;">
          <el-option label="运行中" value="active"></el-option>
          <el-option label="安装中" value="creating"></el-option>
          <el-option label="停止" value="manual_stop"></el-option>
          <el-option label="异常" value="inactive"></el-option>
        </el-select>
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
          v-if="shard_create_priv==='Y'"
        >添加shard</el-button>
         <el-button
          class="filter-item"
          type="primary"
          icon="el-icon-plus"
          @click="handleCreateStorage"
          v-if="storage_node_create_priv==='Y'"
        >添加储存节点</el-button>
      </div>
    </div>
    <el-table
      ref="multipleTable"
      :key="tableKey"
      v-loading="listLoading"
      :data="list"
      border
      highlight-current-row
      default-expand-all
      style="width: 100%;margin-bottom: 20px;">
      <el-table-column
        type="expand"
        align="center"
        label=""
        width="50" >
        <template slot-scope="scope">
          <el-table class="sectable" :data="scope.row.shardList" stripe style="width: 100%" >
            <el-table-column type="index" label="序号" align="center" width="50"></el-table-column>
            <el-table-column prop="hostaddr" label="IP" align="center"></el-table-column>
            <el-table-column prop="port" label="端口" align="center"></el-table-column>
            <el-table-column prop="master" label="主/备节点" align="center">
              <template slot-scope="scope">
                <span v-if="scope.row.master==='true'" style="color: #409eff;">主</span>
                <span v-else-if="scope.row.master==='false'">备</span>
                <span v-else></span>
              </template>
            </el-table-column>
            <el-table-column prop="delay" label="延迟时间" align="center" sortable>
              <template slot-scope="scope">
                <span>{{scope.row.delay+'s'}}</span>
              </template>
            </el-table-column>
            <el-table-column prop="status" label="状态" align="center">
               <template slot-scope="scope">
                <span v-if="scope.row.status==='online'" style="color: #00ed37">运行中</span>
                <span v-if="scope.row.status==='creating'" style="color: #00ed37">运行中</span>
                <span v-if="scope.row.status==='inactive'" style="color: red">异常</span>
                <span v-else-if="scope.row.status==='offline'" style="color: #c7c9d1;">停止</span>
                <span v-else></span>
              </template>
            </el-table-column>
            <el-table-column label="操作" align="center" width="380" class-name="small-padding fixed-width">
              <template slot-scope="{row,$index}">
                <el-button size="mini" type="primary" v-if="row.status=='creating'"  @click="nodeMonitor(row)">节点监控</el-button>
                <!-- <el-button size="mini" type="primary" v-if="row.status!=='online'"  @click="handleControlNode(row,'start')">启用</el-button>
                <el-button size="mini" type="primary" v-if="row.master!=='true'&&row.status!=='offline'" @click="handleControlNode(row,'stop')">禁用</el-button>
                <el-button size="mini" type="primary" v-if="row.master!=='true'"  @click="handleControlNode(row,'restart')">重启</el-button> -->
                <!-- <el-button size="mini" type="primary" v-if="row.master=='true'"  @click="handleSwitch(row)">主备切换</el-button>
                <el-button size="mini" type="primary" v-if="row.master=='true'"  @click="handleReDo(row)">重做备机节点</el-button> -->
                <el-button
                  size="mini"
                  type="danger"
                  @click="handleDeleteStorage(row,$index)"
                  v-if="storage_node_drop_priv==='Y'&&row.master!=='true'"
                >删除</el-button>
              </template>
            </el-table-column>
          </el-table>
        </template>
      </el-table-column>

      <el-table-column
        type="index"
        align="center"
        label="序号"
        width="50" >
      </el-table-column>
       <el-table-column  prop="shardID" label="shardID" align="center" >
        <template slot-scope="{row}">
          <span class="link-type" @click="handleDetail(row)">{{ row.id }}</span>
        </template>
      </el-table-column>
      <el-table-column
        prop="db_cluster_id"
        align="center"
        label=" 集群ID">
      </el-table-column>
      <el-table-column
        prop="name"
        align="center"
        label="shard名称">
      </el-table-column>
      <el-table-column
        prop="num_nodes"
        align="center"
        label="副本数">
      </el-table-column>
      <el-table-column
        label="操作"
        align="center"
        width="100"
        class-name="small-padding fixed-width">
        <template slot-scope="{row,$index}">
          <el-button
            size="mini"
            type="danger"
            @click="handleDelete(row,$index)"
             v-if="shard_drop_priv==='Y'"
          >删除</el-button>
        </template>
      </el-table-column>
    </el-table>
    <pagination v-show="total>0" :total="total" :page.sync="listQuery.pageNo" :limit.sync="listQuery.pageSize" @pagination="getList" />
    <!--添加shard -->
    <el-dialog :title="textMap[dialogStatus]" :visible.sync="dialogNodeVisible" custom-class="single_dal_view"  :close-on-click-modal="false">
      <el-form
        ref="dataForm"
        :model="temp"
        :rules="rules"
        label-position="left"
        label-width="130px">
        <el-form-item label="集群名称:" prop="name" >
          <span>{{temp.name}}</span>
        </el-form-item>
        <el-form-item label="业务名称:" prop="nick_name" >
          <span>{{temp.nick_name}}</span>
        </el-form-item>
        <el-form-item label="选择计算机:" prop="machinelist" >
          <el-select v-model="temp.machinelist" multiple placeholder="请选择">
            <el-option
              v-for="machine in strogemachines"
              :key="machine.id"
              :label="machine.hostaddr"
              :value="machine.hostaddr">
            </el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="shard名称:" prop="shard_name" v-if="dialogStatus==='createstorage'" >
          <el-select v-model="temp.shard_name" placeholder="请选择shard名称"  v-if="dialogStatus=== 'createstorage'?true:false">
            <el-option
              v-for="shard in shard_arr"
              :key="shard.id"
              :label="shard.name"
              :value="shard.id">
            </el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="shard个数:" prop="shards_count" v-if="dialogStatus==='create'">
          <el-input  v-model="temp.shards_count" class="right_input" placeholder="请输入shard个数">
            <i slot="suffix" style="font-style:normal;margin-right: 10px; line-height: 30px;">个</i>
          </el-input>
        </el-form-item>
        <el-form-item :label="label_node" prop="nodes" >
          <el-input  v-model="temp.nodes" class="right_input" :placeholder="placeholder_node">
            <i slot="suffix" style="font-style:normal;margin-right: 10px; line-height: 30px;">个</i>
          </el-input>
        </el-form-item>
      </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button @click="dialogNodeVisible = false">关闭</el-button>
          <el-button type="primary" @click="dialogStatus==='create'?createData():createStorageData()">确认</el-button>
        </div>
    </el-dialog>
    <!--添加删除状态框 -->
    <el-dialog :title="job_id" :visible.sync="dialogStatusShowVisible" custom-class="single_dal_view" :close-on-click-modal="false" :before-close="beforeDestory">
      <div style="width: 100%;background: #fff;padding:0 20px;">
        <el-steps direction="vertical" :active="init_active">
          <el-step :title="init_title" icon="el-icon-more" v-if="init_show"></el-step>
          <!-- <el-step :title="computer_title" :status="computer_state" :icon="computer_icon" :description="computer_description" v-if="computer_show">
            <template slot="description">
              <span>{{computer_description}}</span>
            <div style="padding:20px;">
              <el-steps direction="vertical" :active="comp_active" >
                <el-step
                    v-for="(item,index) of computer"
                    :key="index"
                    :title="item.title"
                    :icon="item.icon"
                    :status="item.status"
                    :description="item.description"
                >
                </el-step>
              </el-steps>
            </div>
            </template>
          </el-step> -->
          <el-step :title="shard_title"  :status="storage_state" :icon="shard_icon" :description="shard_description" v-if="shard_show">
            <template slot="description">
              <span>{{shard_description}}</span>
            <div style="padding:20px;">
              <el-steps direction="vertical" :active="shard_active" >
                <el-step
                    v-for="(item,index) of shard"
                    :key="index"
                    :title="item.title"
                    :icon="item.icon"
                    :status="item.status"
                    :description="item.description"
                    @click.native="thisDetail(item.shard_id)"
                >
                </el-step>
              </el-steps>
            </div>
            </template>
          </el-step>
          <el-step :title="finish_title" :icon="finish_icon" :description="finish_description" :status="finish_state" v-if="finish_show"></el-step>
        </el-steps>
      </div>
    </el-dialog>
    <!--  状态框 -->
    <el-dialog :visible.sync="dialogStatusVisible" custom-class="single_dal_view" width="400px" :close-on-click-modal="false" :before-close="beforeSyncDestory">
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
    <!--shard信息框 -->
    <el-dialog :title="dialogStatus" :visible.sync="dialogShardInfo" custom-class="single_dal_view" :close-on-click-modal="false">
      <json-viewer :value="shardInfo"></json-viewer>
    </el-dialog>
  </div>
</template>
<script>
 import {messageTip,createCode,gotoCofirm,getNowDate} from "@/utils";
 import {shardList,getEvStatus,getStroMachine,addShards,getShardsCount,delShard,getShardsJobLog,getShards,addNodes,getNodesCount,delSnode,controlInstance} from '@/api/cluster/list'
 import {version_arr,timestamp_arr,ip_arr} from "@/utils/global_variable"
 import Pagination from '@/components/Pagination' 
 import JsonViewer from 'vue-json-viewer'
 import {mysqlDashboard} from '@/api/grafana/list'
export default {
  name: "complist",
  components: { Pagination,JsonViewer },
  props:{
		listsent:{typeof:Object}
  },
  data() {
   const validateNodes = (rule, value, callback) => {
      if(!value){
        let string=this.label_node.substr(0,this.label_node.length-1);
        callback(new Error("请输入"+string));
      }else if(!(/^[0-9]+$/.test(value))){
        callback(new Error(string+"只能输入数字"));
      }else if(value>256){
        callback(new Error(string+"不能大于256"));
      }
      else {
        callback();
      }
    };
    const validateShardsCount = (rule, value, callback) => {
     if(!value){
        callback(new Error("请输入shard个数"));
      }else if(!(/^[0-9]+$/.test(value))){
        callback(new Error("shard个数只能输入数字"));
      }
      else if(value>256){
        callback(new Error("shard个数不能大于256"));
      }
      else {
        callback();
      }
    };
    const validateShardName = (rule, value, callback) => {
      if(!value){
        callback(new Error("请选择shard名称"));
      }
      else {
        callback();
      }
    };
    return {
      tableKey: 0,
      list: [],
      listLoading: true,
      searchLoading:false,
      total: 0,
      listQuery: {
        pageNo: 1,
        pageSize: 10,
        hostaddr: '',
        id:'',
        ha_mode:'',
        master:'',
        status:''
      },
      temp: {
        nodes: '',
        name:'',
        nick_name:'',
        machinelist:'',
        cluster_id:'',
        shards_count:'',
        shard_name:''
      },
      dialogFormVisible: false,
      dialogEditVisible:false,
      dialogUploadVisible:false,
      dialogStatus: "",
      textMap: {
        create: "添加shard",
        createstorage: "添加存储节点",
        detail: "详情",
      },
      dialogDetail: false,
      dialogNodeVisible:false,
      message_tips:'',
      message_type:'',
      installStatus:false,
      info:'',
      row:{},
      shard_create_priv:JSON.parse(sessionStorage.getItem('priv')).shard_create_priv,
      shard_drop_priv:JSON.parse(sessionStorage.getItem('priv')).shard_drop_priv,
      storage_node_create_priv:JSON.parse(sessionStorage.getItem('priv')).storage_node_create_priv,
      storage_node_drop_priv:JSON.parse(sessionStorage.getItem('priv')).storage_node_drop_priv,
      storage_status:false,
      comp_status:false,
      comp_active:1,
      job_id:null,
      timer:null,
      strogemachines:[],
      shard:[],
      storage_state:'',
      finish_state:'',
      shard_icon:'',
      shard_title:'',
      shard_active:0,
      init_title:'',
      init_show:true,
      finish_show:false,
      computer_show:true,
      shard_show:true,
      init_active:0,
      finish_title:'',
      finish_icon:'',
      finish_description:'',
      shard_description:'',
      shardInfo:'',
      dialogShardInfo:false,
      job_id:'',
      dialogStatusShowVisible:false,
      shard_arr:[],
      label_node:'',
      placeholder_node:'',
      dialogStatusVisible:false,
      activities:[],
      rules: {
        nodes: [
          { required: true, trigger: "blur",validator: validateNodes },
        ],
         shards_count: [
          { required: true, trigger: "blur",validator: validateShardsCount },
        ],
        shard_name: [
          { required: true, trigger: "blur",validator: validateShardName},
        ],
      },
    };
  },
  created() {
    this.listQuery.id=this.listsent.id;
    this.listQuery.ha_mode=this.listsent.ha_mode;
    this.getList()
  },
  destroyed() {
    clearInterval(this.timer)
    this.timer = null
  },
  methods:{
    nodeMonitor(row){
      const mparas={};
      mparas['cluster_id']=row.cluster_id;
      mparas['hostaddr']=row.hostaddr;
      mparas['port']=row.port;
      mparas['user_name']=sessionStorage.getItem('login_username');
      mysqlDashboard(mparas).then((myres) => {
        if(myres.code=='200'){
          window.open(ip_arr[0].ip+myres.url+'?orgId=1&refresh=5s');
        }
      })
    },
    thisDetail(id){
      const temp ={id:id};
      getShardsJobLog(temp).then((response) => {
        this.dialogStatus = 'shard_'+id+'详细信息';
        this.dialogShardInfo=true;
        this.shardInfo=response.shard_nodes

      });
    },
    //清除定时器
    beforeDestory(){
      clearInterval(this.timer)
      this.dialogStatusShowVisible=false;
      this.timer=null;
    },
    beforeSyncDestory(){
      clearInterval(this.timer)
      this.dialogStatusVisible=false;
      this.timer=null;
    },
    handleFilter() {
      this.listQuery.pageNo = 1
      this.getList()
    },
    handleClear(){
      this.listQuery.hostaddr = ''
      this.listQuery.master = ''
      this.listQuery.status = ''
      this.listQuery.pageNo = 1
      this.listQuery.id = this.listsent.id
      this.listQuery.ha_mode = this.listsent.ha_mode
      this.getList()
    },
    getList() {
      this.listLoading = true
      let queryParam = Object.assign({}, this.listQuery)
      //模糊搜索
      shardList(queryParam).then(response => {
        this.list = response.list;
        this.total = response.total;
        setTimeout(() => {
          this.listLoading = false
        }, 0.5 * 1000)
      });
    },
    resetTemp() {
      this.temp = {
        nodes: '',
        name:'',
        nick_name:'',
        machinelist:'',
        cluster_id:'',
        shards_count:'1',
        shard_name:''
      };
    },
    handleCreate() {
      this.resetTemp();
      this.dialogStatus = "create";
      this.label_node='副本数:';
      this.placeholder_node='请输入副本数';
      this.temp.nodes='3';
      this.dialogNodeVisible = true;
      this.temp.name=this.listsent.name;
      this.temp.nick_name=this.listsent.nick_name;
      this.temp.cluster_id=this.listsent.id;
      //获取存储机器
        getStroMachine().then((res) => {
          this.strogemachines=[];
          this.strogemachines = res.list;
        });
      this.$nextTick(() => {
        this.$refs.dataForm.clearValidate();
      });
    },
    createData() {
      this.$refs["dataForm"].validate((valid) => {
        if (valid) {
          const tempData = {};
          tempData.user_name = sessionStorage.getItem('login_username');
          tempData.job_id ='';
          tempData.job_type ='add_shards';
          tempData.version=version_arr[0].ver;
          tempData.timestamp=timestamp_arr[0].time+'';
          let machinelist=[];
          this.temp.machinelist.forEach(item => {
            machinelist.push(item)
          })
          const paras ={};
          paras.nodes=this.temp.nodes;
          paras.cluster_id=this.temp.cluster_id;
          paras.nick_name=this.temp.nick_name;
          paras.shards=this.temp.shards_count;
          if(machinelist.length>0){
            paras.storage_iplists=machinelist;
          }
          tempData.paras=paras;
          //console.log(tempData);return;
          //新增shard
          addShards(tempData).then((response) => {
            let res = response;
            if(res.status=='accept'){
              this.dialogNodeVisible = false;
              this.dialogStatusShowVisible=true;
              //把之前的数据清空
              this.shard=[];this.storage_state='';this.shard_icon='';this.shard_title='';this.shard_active=0;this.strogemachines=[];this.init_title='';this.init_active=0;this.finish_title='';this.finish_icon='';this.finish_description='';this.shard_description='';this.job_id='';this.timer=null;
              this.init_show=true;this.finish_show=false;this.finish_state='';
              const info='添加shard'
              let i=0;
              this.getFStatus(this.timer,res.job_id,i++,info,'')
              this.timer = setInterval(() => {
                this.getFStatus(this.timer,res.job_id,i++,info,'')
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
        }
      });
    },
    handleCreateStorage() {
      this.resetTemp();
      this.dialogStatus = "createstorage";
      this.label_node='存储节点个数:';
      this.placeholder_node='请输入存储节点个数';
      this.temp.nodes='1';
      this.dialogNodeVisible = true;
      this.temp.name=this.listsent.name;
      this.temp.nick_name=this.listsent.nick_name;
      this.temp.cluster_id=this.listsent.id;
      //获取存储机器
        getStroMachine().then((res) => {
          this.strogemachines=[];
          this.strogemachines = res.list;
        });
        //获取分片名称
        getShards(this.listsent.id).then((response) => {
          let res = response;
          if(res.code==200){
          this.shard_arr=res.list;
          }
        });
      this.$nextTick(() => {
        this.$refs.dataForm.clearValidate();
      });
    },
    createStorageData() {
      this.$refs["dataForm"].validate((valid) => {
        if (valid) {
          const tempData = {};
          tempData.user_name = sessionStorage.getItem('login_username');
          tempData.job_id ='';
          tempData.job_type ='add_nodes';
          tempData.version=version_arr[0].ver;
          tempData.timestamp=timestamp_arr[0].time+'';
          let machinelist=[];
          this.temp.machinelist.forEach(item => {
            machinelist.push(item)
          })
          const paras ={};
          paras.nodes=this.temp.nodes;
          paras.cluster_id=this.temp.cluster_id;
          paras.nick_name=this.temp.nick_name;
          paras.shard_id=this.temp.shard_name;
          if(machinelist.length>0){
            paras.storage_iplists=machinelist;
          }
          tempData.paras=paras;
          //console.log(tempData);return;
          //新增shard
          addNodes(tempData).then((response) => {
            let res = response;
            if(res.status=='accept'){
              this.dialogNodeVisible = false;
              this.dialogStatusShowVisible=true;
              //把之前的数据清空
              this.shard=[];this.storage_state='';this.shard_icon='';this.shard_title='';this.shard_active=0;this.strogemachines=[];this.init_title='';this.init_active=0;this.finish_title='';this.finish_icon='';this.finish_description='';this.shard_description='';this.job_id='';this.timer=null;
              this.init_show=true;this.finish_show=false;this.finish_state='';
              const info='添加存储节点'
              let i=0;
              this.getFStatus(this.timer,res.job_id,i++,info,'')
              this.timer = setInterval(() => {
                this.getFStatus(this.timer,res.job_id,i++,info,'')
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
      //先判断该集群是否只有一个shard，如果只有一个shard不予许删除
      let temp={id:row.db_cluster_id};
      getShardsCount(temp).then((res) => {
        if(res.total==1){
          messageTip('该集群当前有且仅有一个shard,不能进行删除操作','error');
        }else if(res.total>1){
          const code=createCode();
          let string="此操作将永久删除"+row.name+",是否继续?code="+code;
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
              tempData.job_type ='delete_shard';
              tempData.version=version_arr[0].ver;
              tempData.timestamp=timestamp_arr[0].time+'';
              const paras={};
              paras.cluster_id=row.db_cluster_id;
              paras.nick_name=this.listsent.nick_name;
              paras.shard_id=row.id;
              tempData.paras=paras;
              //console.log(tempData);return;
              delShard(tempData).then((response)=>{
                let res = response;
                if(res.status=='accept'){
                  this.dialogStatusShowVisible=true;
                  //调获取状态接口
                  let i=0;
                  this.shard=[];this.storage_state='';this.shard_icon='';this.shard_title='';this.shard_active=0;this.strogemachines=[];this.init_title='';this.init_active=0;this.finish_title='';this.finish_icon='';this.finish_description='';this.shard_description='';this.job_id='';this.timer=null;
                  this.init_show=true;this.finish_show=false;this.finish_state='';
                  const info='删除shard'
                  this.getFStatus(this.timer,res.job_id,i++,info,'')
                  this.timer = setInterval(() => {
                    this.getFStatus(this.timer,res.job_id,i++,info,'')
                  }, 1000)
                }
                else{
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
        }
      });
    },
    handleDeleteStorage(row) {
      //先判断该集群是否只有一个存储节点，如果只有一个存储节点不予许删除
      let temp={id:row.cluster_id,shard_id:row.shard_id};
      getNodesCount(temp).then((res) => {
        if(res.total==1){
          messageTip('该集群当前有且仅有一个储存节点,不能进行删除操作','error');
        }else if(res.total>1){
          const code=createCode();
          let string="此操作将永久删除存储节点"+row.hostaddr+"("+row.port+"),是否继续?code="+code;
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
              tempData.job_type ='delete_node';
              tempData.version=version_arr[0].ver;
              tempData.timestamp=timestamp_arr[0].time+'';
              const paras={};
              paras.cluster_id=row.cluster_id;
              paras.nick_name=this.listsent.nick_name;
              paras.shard_id=row.shard_id;
              paras.hostaddr=row.hostaddr;
              paras.port=row.port;
              tempData.paras=paras;
              //console.log(tempData);return;
              delSnode(tempData).then((response)=>{
                let res = response;
                if(res.status=='accept'){
                  this.dialogStatusShowVisible=true;
                  //调获取状态接口
                  let i=0;
                  this.shard=[];this.storage_state='';this.shard_icon='';this.shard_title='';this.shard_active=0;this.strogemachines=[];this.init_title='';this.init_active=0;this.finish_title='';this.finish_icon='';this.finish_description='';this.shard_description='';this.job_id='';this.timer=null;
                  this.init_show=true;this.finish_show=false;this.finish_state='';
                  const info='删除存储节点'
                  const ip_port=row.hostaddr+'_'+row.port;
                  this.getFStatus(this.timer,res.job_id,i++,info,ip_port)
                  this.timer = setInterval(() => {
                    this.getFStatus(this.timer,res.job_id,i++,info,ip_port)
                  }, 3000)
                }
                else{
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
        }
      });
    },
    handleControlNode(row,action) {
      let action_name='';
      if(action=='start'){
        action_name='启用';
      }else if(action=='stop'){
        action_name='禁用';
      }else if(action=='restart'){
        action_name='重启';
      }
      const code=createCode();
      let string="将对该存储节点"+row.hostaddr+"("+row.port+")进行"+action_name+"操作,是否继续?code="+code;
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
          tempData.job_type ='control_instance';
          tempData.version=version_arr[0].ver;
          tempData.timestamp=timestamp_arr[0].time+'';
          const paras={};
          paras.control=action;
          paras.hostaddr=row.hostaddr;
          paras.port=row.port;
          paras.cluster_id=this.listsent.id;
          paras.machine_type='storage';
          tempData.paras=paras;
          let arr={
            list:tempData,
            type:'mysql'
          }
          controlInstance(arr).then((response)=>{
            let res = response;
            if(res.status=='accept'){
              this.dialogStatusVisible=true;
              this.activities=[];
              const newArr={
                content:'正在'+action_name+'储存节点'+row.hostaddr+"("+row.port+")",
                timestamp: getNowDate(),
                size: 'large',
                type: 'primary',
                icon: 'el-icon-more'
              };
              this.activities.push(newArr);
              //调获取状态接口
              let i=0;
              this.timer = setInterval(() => {
                this.getStatus(this.timer,res.job_id,i++,action_name)
              }, 5000)
              //this.isShowNodeMenuPanel = false
            }
            else{
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
    getFStatus (timer,data,i,info,ip) {
      setTimeout(()=>{
        const postarr={};
        postarr.job_type='get_status';
        postarr.version=version_arr[0].ver;
        postarr.job_id=data;
        postarr.timestamp=timestamp_arr[0].time+'';
        postarr.paras={};
        this.job_id='任务ID:'+data;
        getEvStatus(postarr).then((ress) => {
          //新增shard,存储节点
          if(info=='添加shard'||info=='添加存储节点'||info=='删除shard'||info=='删除存储节点'){
            //新增shard,存储节点
            if(ress.attachment!==null){
              console.log(5);
              if(info=='添加shard'||info=='添加存储节点'||info=='删除shard'||info=='删除存储节点'){
                this.shard_show=true;
                this.init_show=false;
                this.computer_show=false;
              }
              //存储
              if(ress.attachment.hasOwnProperty('storage_state')){
                if(ress.attachment.storage_state=='ongoing'){
                  this.storage_state='process';
                  this.shard_icon='el-icon-loading'
                  this.shard_title='正在'+info;
                }else if(ress.attachment.storage_state=='done'){
                  this.storage_state='success';
                  this.shard_icon='el-icon-circle-check'
                  this.shard_title=info+'成功';
                  //遍历存储节点改状态
                  if(this.shard.length>0){
                    for(let c=0;c<this.shard.length;c++){
                      let shard_ids='';
                      if(info=='添加shard'){
                        shard_ids=ress.attachment.shard_ids;
                        for(let e=0;e<shard_ids.length;e++){
                          for(var item in shard_ids[e]){
                            var shard_idsValue=shard_ids[e][item];
                            if(this.shard[c].shard_id==shard_idsValue){
                              this.shard[c].icon='el-icon-circle-check';
                              this.shard[c].status='success';
                            }
                          }
                        }
                      }else if(info=='添加存储节点'){
                        shard_ids=ress.attachment.shard_hosts;
                        for(let e=0;e<shard_ids.length;e++){
                          for(var item in shard_ids[e]){
                            var shard_idsValue=shard_ids[e][item];
                            if(this.shard[c].shard_id==shard_idsValue){
                              this.shard[c].icon='el-icon-circle-check';
                              this.shard[c].status='success';
                            }
                          }
                        }
                      }else if(info=='删除shard'){
                        const arr=ress.attachment.storage_hosts.substr(0,ress.attachment.storage_hosts.length-1);
                        shard_ids=arr.split(';');
                        for(let e=0;e<shard_ids.length;e++){
                          // for(var item in shard_ids[e]){
                          //   var shard_idsValue=shard_ids[e][item];
                            if(this.shard[c].shard_id==shard_ids[e]){
                              this.shard[c].icon='el-icon-circle-check';
                              this.shard[c].status='success';
                            }
                          // }
                        }
                      }else{
                        shard_ids=ip;
                        if(this.shard[c].shard_id==shard_ids){
                          this.shard[c].icon='el-icon-circle-check';
                          this.shard[c].status='success';
                        }
                      }
                    }
                  }
                }else if(ress.attachment.storage_state=='failed'){
                  console.log(5);
                  this.storage_state='error';
                  this.shard_icon='el-icon-circle-close'
                  this.shard_title=info+'失败';
                  //遍历存储节点改状态
                  if(this.shard.length>0){
                    for(let c=0;c<this.shard.length;c++){
                      let shard_ids='';
                      if(info=='添加shard'){
                        shard_ids=ress.attachment.shard_ids;
                        for(let e=0;e<shard_ids.length;e++){
                          for(var item in shard_ids[e]){
                            var shard_idsValue=shard_ids[e][item];
                            if(this.shard[c].shard_id==shard_idsValue){
                              this.shard[c].icon='el-icon-circle-close';
                              this.shard[c].status='error';
                            }
                          }
                        }
                      }else if(info=='添加存储节点'){
                        shard_ids=ress.attachment.shard_hosts;
                        for(let e=0;e<shard_ids.length;e++){
                          for(var item in shard_ids[e]){
                            var shard_idsValue=shard_ids[e][item];
                            if(this.shard[c].shard_id==shard_idsValue){
                              this.shard[c].icon='el-icon-circle-close';
                              this.shard[c].status='error';
                            }
                          }
                        }
                      }else if(info=='删除shard'){
                        const arr=ress.attachment.storage_hosts.substr(0,ress.attachment.storage_hosts.length-1);
                        shard_ids=arr.split(';');
                        for(let e=0;e<shard_ids.length;e++){
                          // for(var item in shard_ids[e]){
                          //   var shard_idsValue=shard_ids[e][item];
                            if(this.shard[c].shard_id==shard_ids[e]){
                              this.shard[c].icon='el-icon-circle-close';
                              this.shard[c].status='error';
                            }
                          // }
                        }
                      }else{
                        shard_ids=ip;
                        if(this.shard[c].shard_id==shard_ids){
                          this.shard[c].icon='el-icon-circle-close';
                          this.shard[c].status='error';
                        }
                      }
                    }
                    this.shard_description=ress.error_info;
                  }
                  //clearInterval(timer);
                }else{
                  this.storage_state='process';
                  this.shard_icon='el-icon-loading'
                  this.shard_title='正在'+info;
                }
              }
              this.init_title='正在'+info;
              this.init_active=1;
              if(this.shard.length==0){
                console.log(1);
                if(ress.status=='ongoing'){
                  this.storage_state='process';
                  this.shard_icon='el-icon-loading'
                  this.shard_title='正在'+info;
                  if(ress.attachment.hasOwnProperty('storage_state')){
                    let shard_ids='';
                    if(info=='添加shard'){
                      shard_ids=ress.attachment.shard_ids;
                      for(let e=0;e<shard_ids.length;e++){
                        for(var item in shard_ids[e]){
                          let shardgoing={}
                          var shard_idsValue=shard_ids[e][item];
                          console.log(shard_idsValue);
                          const shard_text=item+':'+shard_idsValue;
                          console.log(shard_text);
                          shardgoing.title=shard_idsValue!==''?shard_text:'正在'+info;
                          shardgoing.icon='el-icon-loading';
                          shardgoing.status= 'process';
                          shardgoing.description='';
                          shardgoing.shard_id=shard_idsValue;
                          this.shard.push(shardgoing)
                        }
                      }
                    }else if(info=='添加存储节点'){
                      shard_ids=ress.attachment.shard_hosts;
                      for(let e=0;e<shard_ids.length;e++){
                        for(var item in shard_ids[e]){
                          let shardgoing={}
                          var shard_idsValue=shard_ids[e][item];
                          console.log(shard_idsValue);
                          const shard_text=item+':'+shard_idsValue;
                          console.log(shard_text);
                          shardgoing.title=shard_idsValue!==''?shard_text:'正在'+info;
                          shardgoing.icon='el-icon-loading';
                          shardgoing.status= 'process';
                          shardgoing.description='';
                          shardgoing.shard_id=shard_idsValue;
                          this.shard.push(shardgoing)
                        }
                      }
                    }else if(info=='删除shard'){
                      const arr=ress.attachment.storage_hosts.substr(0,ress.attachment.storage_hosts.length-1);
                      shard_ids=arr.split(';');
                      for(let e=0;e<shard_ids.length;e++){
                        // for(var item in shard_ids[e]){
                        //   let shardgoing={}
                        //   var shard_idsValue=shard_ids[e][item];
                        //   console.log(shard_idsValue);
                        //   const shard_text=item+':'+shard_idsValue;
                        //   console.log(shard_text);
                         let shardgoing={}
                          shardgoing.title=shard_ids[e]!==''?shard_ids[e]:'正在'+info;
                          shardgoing.icon='el-icon-loading';
                          shardgoing.status= 'process';
                          shardgoing.description='';
                          shardgoing.shard_id=shard_ids[e];
                          this.shard.push(shardgoing)
                        // }
                      }
                    }else{
                      shard_ids=ip;
                      let shardgoing={}
                      shardgoing.title=shard_ids;
                      shardgoing.icon='el-icon-loading';
                      shardgoing.status= 'process';
                      shardgoing.description='';
                      shardgoing.shard_id=shard_ids;
                      this.shard.push(shardgoing)
                    }
                    
                  }
                }else if(ress.status=='failed'){
                  console.log(2);
                  this.storage_state='error';
                  this.shard_icon='el-icon-circle-close'
                  this.shard_title=info+'失败';
                  this.init_show=false;
                  this.shard_description=ress.error_info;

                  this.finish_title=info+'失败'
                  this.finish_icon='el-icon-circle-close'
                  this.finish_state='error';
                  this.init_active=1
                  this.finish_description=ress.error_info;
                  
                  //遍历存储节点改状态
                  if(this.shard.length>0){
                    for(let c=0;c<this.shard.length;c++){
                      let shard_ids='';
                      if(ress.attachment.hasOwnProperty('shard_hosts')){
                        if(info=='添加shard'){
                          shard_ids=ress.attachment.shard_ids;
                          for(let e=0;e<shard_ids.length;e++){
                            for(var item in shard_ids[e]){
                              var shard_idsValue=shard_ids[e][item];
                              if(this.shard[c].shard_id==shard_idsValue){
                                this.shard[c].icon='el-icon-circle-close';
                                this.shard[c].status='error';
                              }
                            }
                          }
                        }else if(info=='添加存储节点'){
                          shard_ids=ress.attachment.shard_hosts;
                          for(let e=0;e<shard_ids.length;e++){
                            for(var item in shard_ids[e]){
                              var shard_idsValue=shard_ids[e][item];
                              if(this.shard[c].shard_id==shard_idsValue){
                                this.shard[c].icon='el-icon-circle-close';
                                this.shard[c].status='error';
                              }
                            }
                          }
                        }else if(info=='删除shard'){
                          const arr=ress.attachment.storage_hosts.substr(0,ress.attachment.storage_hosts.length-1);
                          shard_ids=arr.split(';');
                          for(let e=0;e<shard_ids.length;e++){
                            // for(var item in shard_ids[e]){
                            //   var shard_idsValue=shard_ids[e][item];
                              if(this.shard[c].shard_id==shard_ids[e]){
                                this.shard[c].icon='el-icon-circle-close';
                                this.shard[c].status='error';
                              }
                            // }
                          }
                        }else{
                          shard_ids=ip;
                          if(this.shard[c].shard_id==shard_ids){
                            this.shard[c].icon='el-icon-circle-close';
                            this.shard[c].status='error';
                          }
                        }
                      }
                    }
                    this.shard_description=ress.error_info;
                  }else{
                     if(ress.attachment.hasOwnProperty('storage_state')){
                      let shard_ids='';
                      if(info=='添加shard'){
                        shard_ids=ress.attachment.shard_ids;
                        for(let e=0;e<shard_ids.length;e++){
                          for(var item in shard_ids[e]){
                            let shardgoing={}
                            var shard_idsValue=shard_ids[e][item];
                            console.log(shard_idsValue);
                            const shard_text=item+':'+shard_idsValue;
                            console.log(shard_text);
                            shardgoing.title=shard_idsValue!==''?shard_text:'正在'+info;
                            shardgoing.icon='el-icon-circle-close';
                            shardgoing.status= 'error';
                            shardgoing.description='';
                            shardgoing.shard_id=shard_idsValue;
                            this.shard.push(shardgoing)
                          }
                        }
                      }else if(info=='添加存储节点'){
                        shard_ids=ress.attachment.shard_hosts;
                        for(let e=0;e<shard_ids.length;e++){
                          for(var item in shard_ids[e]){
                            let shardgoing={}
                            var shard_idsValue=shard_ids[e][item];
                            console.log(shard_idsValue);
                            const shard_text=item+':'+shard_idsValue;
                            console.log(shard_text);
                            shardgoing.title=shard_idsValue!==''?shard_text:'正在'+info;
                            shardgoing.icon='el-icon-circle-close';
                            shardgoing.status= 'error';
                            shardgoing.description='';
                            shardgoing.shard_id=shard_idsValue;
                            this.shard.push(shardgoing)
                          }
                        }
                      }else if(info=='删除shard'){
                        const arr=ress.attachment.storage_hosts.substr(0,ress.attachment.storage_hosts.length-1);
                        shard_ids=arr.split(';');
                        for(let e=0;e<shard_ids.length;e++){
                          // for(var item in shard_ids[e]){
                          //   let shardgoing={}
                          //   var shard_idsValue=shard_ids[e][item];
                          //   console.log(shard_idsValue);
                          //   const shard_text=item+':'+shard_idsValue;
                          //   console.log(shard_text);
                            let shardgoing={}
                            shardgoing.title=shard_ids[e]!==''?shard_ids[e]:'正在'+info;
                            shardgoing.icon='el-icon-circle-close';
                            shardgoing.status= 'error';
                            shardgoing.description='';
                            shardgoing.shard_id=shard_ids[e];
                            this.shard.push(shardgoing)
                          // }
                        }
                      }else{
                        shard_ids=ip;
                        let shardgoing={}
                        shardgoing.title=shard_ids!==''?shard_ids:'正在'+info;
                        shardgoing.icon='el-icon-circle-close';
                        shardgoing.status= 'error';
                        shardgoing.description='';
                        shardgoing.shard_id=shard_ids;
                        this.shard.push(shardgoing)
                      }
                    }
                  }
                  clearInterval(timer);
                  console.log(7);
                }else if(ress.status=='done'){
                  console.log(3);
                  this.storage_state='success';
                  this.shard_icon='el-icon-circle-check'
                  this.shard_title=info+'成功';

                  this.finish_title=info+'成功'
                  this.finish_icon='el-icon-circle-check'
                  this.finish_state='success';
                  this.init_active=1
                  this.finish_description=ress.error_info;
                  //遍历存储节点改状态
                  if(this.shard.length>0){
                    for(let c=0;c<this.shard.length;c++){
                      let shard_ids='';
                      if(ress.attachment.hasOwnProperty('shard_hosts')){
                        if(info=='添加shard'){
                          shard_ids=ress.attachment.shard_ids;
                          for(let e=0;e<shard_ids.length;e++){
                            for(var item in shard_ids[e]){
                              var shard_idsValue=shard_ids[e][item];
                              if(this.shard[c].shard_id==shard_idsValue){
                                this.shard[c].icon='el-icon-circle-check';
                                this.shard[c].status='success';
                              }
                            }
                          }
                        }else if(info=='添加存储节点'){
                          shard_ids=ress.attachment.shard_hosts;
                          for(let e=0;e<shard_ids.length;e++){
                            for(var item in shard_ids[e]){
                              var shard_idsValue=shard_ids[e][item];
                              if(this.shard[c].shard_id==shard_idsValue){
                                this.shard[c].icon='el-icon-circle-check';
                                this.shard[c].status='success';
                              }
                            }
                          }
                        }else if(info=='删除shard'){
                          const arr=ress.attachment.storage_hosts.substr(0,ress.attachment.storage_hosts.length-1);
                          shard_ids=arr.split(';');
                          for(let e=0;e<shard_ids.length;e++){
                            // for(var item in shard_ids[e]){
                            //   var shard_idsValue=shard_ids[e][item];
                              if(this.shard[c].shard_id==shard_ids[e]){
                                this.shard[c].icon='el-icon-circle-check';
                                this.shard[c].status='success';
                              }
                            // }
                          }
                        }else{
                          shard_ids=ip;
                          if(this.shard[c].shard_id==shard_ids){
                            this.shard[c].icon='el-icon-circle-check';
                            this.shard[c].status='success';
                          }
                        }
                      }
                    }
                  }else{
                    if(ress.attachment.hasOwnProperty('storage_state')){
                      let shard_ids='';
                      if(info=='添加shard'){
                        shard_ids=ress.attachment.shard_ids;
                        for(let e=0;e<shard_ids.length;e++){
                          for(var item in shard_ids[e]){
                            let shardgoing={}
                            var shard_idsValue=shard_ids[e][item];
                            console.log(shard_idsValue);
                            const shard_text=item+':'+shard_idsValue;
                            console.log(shard_text);
                            shardgoing.title=shard_idsValue!==''?shard_text:'正在'+info;
                            shardgoing.icon='el-icon-circle-check';
                            shardgoing.status= 'success';
                            shardgoing.description='';
                            shardgoing.shard_id=shard_idsValue;
                            this.shard.push(shardgoing)
                          }
                        }
                      }else if(info=='添加存储节点'){
                        shard_ids=ress.attachment.shard_hosts;
                        for(let e=0;e<shard_ids.length;e++){
                          for(var item in shard_ids[e]){
                            let shardgoing={}
                            var shard_idsValue=shard_ids[e][item];
                            console.log(shard_idsValue);
                            const shard_text=item+':'+shard_idsValue;
                            console.log(shard_text);
                            shardgoing.title=shard_idsValue!==''?shard_text:'正在'+info;
                            shardgoing.icon='el-icon-circle-check';
                            shardgoing.status= 'success';
                            shardgoing.description='';
                            shardgoing.shard_id=shard_idsValue;
                            this.shard.push(shardgoing)
                          }
                        }
                      }else if(info=='删除shard'){
                        const arr=ress.attachment.storage_hosts.substr(0,ress.attachment.storage_hosts.length-1);
                        shard_ids=arr.split(';');
                        for(let e=0;e<shard_ids.length;e++){
                          // for(var item in shard_ids[e]){
                          //   let shardgoing={}
                          //   var shard_idsValue=shard_ids[e][item];
                          //   console.log(shard_idsValue);
                          //   const shard_text=item+':'+shard_idsValue;
                          //   console.log(shard_text);
                            let shardgoing={}
                            shardgoing.title=shard_ids[e]!==''?shard_ids[e]:'正在'+info;
                            shardgoing.icon='el-icon-circle-check';
                            shardgoing.status= 'success';
                            shardgoing.description='';
                            shardgoing.shard_id=shard_ids[e];
                            this.shard.push(shardgoing)
                          // }
                        }
                      }else{
                        shard_ids=ip;
                        let shardgoing={}
                        shardgoing.title=shard_ids!==''?shard_ids:'正在'+info;
                        shardgoing.icon='el-icon-circle-check';
                        shardgoing.status= 'success';
                        shardgoing.description='';
                        shardgoing.shard_id=shard_ids;
                        this.shard.push(shardgoing)
                      }
                    }
                  }
                  clearInterval(timer);
                  this.getList();
                }
              }else{
                console.log(4);
                if(ress.status=='ongoing'){
                  if(ress.attachment.hasOwnProperty('storage_state')){
                    if(ress.attachment.storage_state=='done'){
                      this.storage_state='success';
                      this.shard_icon='el-icon-circle-check'
                      this.shard_title=info+'成功';
                      //遍历存储节点改状态
                      if(this.shard.length>0){
                        for(let c=0;c<this.shard.length;c++){
                          let shard_ids='';
                          if(info=='添加shard'){
                            shard_ids=ress.attachment.shard_ids;
                            for(let e=0;e<shard_ids.length;e++){
                              for(var item in shard_ids[e]){
                                var shard_idsValue=shard_ids[e][item];
                                if(this.shard[c].shard_id==shard_idsValue){
                                  this.shard[c].icon='el-icon-circle-check';
                                  this.shard[c].status='success';
                                }
                              }
                            }
                          }else if(info=='添加存储节点'){
                            shard_ids=ress.attachment.shard_hosts;
                            for(let e=0;e<shard_ids.length;e++){
                              for(var item in shard_ids[e]){
                                var shard_idsValue=shard_ids[e][item];
                                if(this.shard[c].shard_id==shard_idsValue){
                                  this.shard[c].icon='el-icon-circle-check';
                                  this.shard[c].status='success';
                                }
                              }
                            }
                          }else if(info=='删除shard'){
                            const arr=ress.attachment.storage_hosts.substr(0,ress.attachment.storage_hosts.length-1);
                            shard_ids=arr.split(';');
                            for(let e=0;e<shard_ids.length;e++){
                              // for(var item in shard_ids[e]){
                              //   var shard_idsValue=shard_ids[e][item];
                                if(this.shard[c].shard_id==shard_ids[e]){
                                  this.shard[c].icon='el-icon-circle-check';
                                  this.shard[c].status='success';
                                }
                              // }
                            }
                          }else{
                            shard_ids=ip;
                            if(this.shard[c].shard_id==shard_ids){
                              this.shard[c].icon='el-icon-circle-check';
                              this.shard[c].status='success';
                            }
                          }
                        }
                      }
                    }else if(ress.attachment.storage_state=='failed'){
                      console.log(6);
                      this.storage_state='error';
                      this.shard_icon='el-icon-circle-close'
                      this.shard_title=info+'失败';
                      //遍历存储节点改状态
                      if(this.shard.length>0){
                        for(let c=0;c<this.shard.length;c++){
                          let shard_ids='';
                          if(info=='添加shard'){
                            shard_ids=ress.attachment.shard_ids;
                            for(let e=0;e<shard_ids.length;e++){
                              for(var item in shard_ids[e]){
                                var shard_idsValue=shard_ids[e][item];
                                if(this.shard[c].shard_id==shard_idsValue){
                                  this.shard[c].icon='el-icon-circle-close';
                                  this.shard[c].status='error';
                                }
                              }
                            }
                          }else if(info=='添加存储节点'){
                            shard_ids=ress.attachment.shard_hosts;
                            for(let e=0;e<shard_ids.length;e++){
                              for(var item in shard_ids[e]){
                                var shard_idsValue=shard_ids[e][item];
                                if(this.shard[c].shard_id==shard_idsValue){
                                  this.shard[c].icon='el-icon-circle-close';
                                  this.shard[c].status='error';
                                }
                              }
                            }
                          }else if(info=='删除shard'){
                            const arr=ress.attachment.storage_hosts.substr(0,ress.attachment.storage_hosts.length-1);
                            shard_ids=arr.split(';');
                            for(let e=0;e<shard_ids.length;e++){
                              // for(var item in shard_ids[e]){
                              //   var shard_idsValue=shard_ids[e][item];
                                if(this.shard[c].shard_id==shard_ids[e]){
                                  this.shard[c].icon='el-icon-circle-close';
                                  this.shard[c].status='error';
                                }
                              // }
                            }
                          }else{
                            shard_ids=ip;
                              if(this.shard[c].shard_id==shard_ids){
                                this.shard[c].icon='el-icon-circle-close';
                                this.shard[c].status='error';
                              }
                          }
                        }
                        this.shard_description=ress.error_info;
                      }
                      //clearInterval(timer);
                    }else{
                      this.storage_state='process';
                      this.shard_icon='el-icon-loading'
                      this.shard_title='正在'+info;
                    }
                  }
                }else if(ress.status=='failed'){
                  console.log(7);
                  this.storage_state='error';
                  this.shard_icon='el-icon-circle-close'
                  this.shard_title=info+'失败';
                  this.init_show=false;
                  this.finish_title=info+'失败'
                  this.finish_icon='el-icon-circle-close'
                  this.finish_state='error';
                  this.init_active=1
                  this.finish_description=ress.error_info;
                 
                  //遍历存储节点改状态
                  if(this.shard.length>0){
                    for(let c=0;c<this.shard.length;c++){
                      let shard_ids='';
                      if(ress.attachment.hasOwnProperty('shard_hosts')){
                        if(info=='添加shard'){
                          shard_ids=ress.attachment.shard_ids;
                          for(let e=0;e<shard_ids.length;e++){
                            for(var item in shard_ids[e]){
                              var shard_idsValue=shard_ids[e][item];
                              if(this.shard[c].shard_id==shard_idsValue){
                                this.shard[c].icon='el-icon-circle-close';
                                this.shard[c].status='error';
                              }
                            }
                          }
                        }else if(info=='添加存储节点'){
                          shard_ids=ress.attachment.shard_hosts;
                          for(let e=0;e<shard_ids.length;e++){
                            for(var item in shard_ids[e]){
                              var shard_idsValue=shard_ids[e][item];
                              if(this.shard[c].shard_id==shard_idsValue){
                                this.shard[c].icon='el-icon-circle-close';
                                this.shard[c].status='error';
                              }
                            }
                          }
                        }else if(info=='删除shard'){
                          const arr=ress.attachment.storage_hosts.substr(0,ress.attachment.storage_hosts.length-1);
                          shard_ids=arr.split(';');
                          for(let e=0;e<shard_ids.length;e++){
                            // for(var item in shard_ids[e]){
                            //   var shard_idsValue=shard_ids[e][item];
                              if(this.shard[c].shard_id==shard_ids[e]){
                                this.shard[c].icon='el-icon-circle-close';
                                this.shard[c].status='error';
                              }
                            // }
                          }
                        }else{
                          shard_ids=ip;
                          if(this.shard[c].shard_id==shard_ids){
                            this.shard[c].icon='el-icon-circle-close';
                            this.shard[c].status='error';
                          }
                        }
                        
                      }
                    }
                    this.shard_description=ress.error_info;
                  }
                  clearInterval(timer);
                }else if(ress.status=='done'){
                  this.storage_state='success';
                  this.shard_icon='el-icon-circle-check'
                  this.shard_title=info+'成功';

                  this.finish_title=info+'成功'
                  this.finish_icon='el-icon-circle-check'
                  this.finish_state='success';
                  this.init_active=1
                  this.finish_description=ress.error_info;
                  //遍历存储节点改状态
                  if(this.shard.length>0){
                    for(let c=0;c<this.shard.length;c++){
                      let shard_ids='';
                      if(ress.attachment.hasOwnProperty('shard_hosts')){
                        if(info=='添加shard'){
                          shard_ids=ress.attachment.shard_ids;
                          for(let e=0;e<shard_ids.length;e++){
                            for(var item in shard_ids[e]){
                              var shard_idsValue=shard_ids[e][item];
                              if(this.shard[c].shard_id==shard_idsValue){
                                this.shard[c].icon='el-icon-circle-check';
                                this.shard[c].status='success';
                              }
                            }
                          }
                        }else if(info=='添加存储节点'){
                          shard_ids=ress.attachment.shard_hosts;
                          for(let e=0;e<shard_ids.length;e++){
                            for(var item in shard_ids[e]){
                              var shard_idsValue=shard_ids[e][item];
                              if(this.shard[c].shard_id==shard_idsValue){
                                this.shard[c].icon='el-icon-circle-check';
                                this.shard[c].status='success';
                              }
                            }
                          }
                        }else if(info=='删除shard'){
                          const arr=ress.attachment.storage_hosts.substr(0,ress.attachment.storage_hosts.length-1);
                          shard_ids=arr.split(';');
                          for(let e=0;e<shard_ids.length;e++){
                            // for(var item in shard_ids[e]){
                            //   var shard_idsValue=shard_ids[e][item];
                              if(this.shard[c].shard_id==shard_ids[e]){
                                this.shard[c].icon='el-icon-circle-check';
                                this.shard[c].status='success';
                              }
                            // }
                          }
                        }else{
                          shard_ids=ip;
                          if(this.shard[c].shard_id==shard_ids){
                            this.shard[c].icon='el-icon-circle-check';
                            this.shard[c].status='success';
                          }
                        }
                      }
                    }
                  }
                  clearInterval(timer);
                  this.getList();
                }
              }
            }else if(ress.attachment==null&&ress.error_code=='70001'&&ress.status=='failed'){
              if(info=='添加shard'||info=='添加存储节点'||info=='删除shard'||info=='删除存储节点'){
                this.shard_show=true;
                this.init_show=false;
                this.computer_show=false;
                this.storage_state='process';
                this.shard_icon='el-icon-loading'
                this.shard_title='正在'+info;
                if(i>5){
                  this.storage_state='error';
                  this.shard_icon='el-icon-circle-close'
                  this.shard_title=info+'失败';
                  clearInterval(timer);
                }
              }
            }else if(ress.attachment==null&&ress.status=='ongoing'){
               if(info=='添加shard'||info=='添加存储节点'||info=='删除shard'||info=='删除存储节点'){
                this.shard_show=true;
                this.init_show=false;
                this.computer_show=false;
                this.storage_state='process';
                this.shard_icon='el-icon-loading'
                this.shard_title='正在'+info;
              }
            }else if(ress.attachment==null&&ress.status=='done'){
              if(info=='添加shard'||info=='添加存储节点'){
                this.shard_show=true;
                this.init_show=false;
                this.computer_show=false;
                this.storage_state='success';
                this.shard_icon='el-icon-circle-check'
                this.shard_title=info+'成功';
              }
              clearInterval(timer);
            }else{
              if(info=='添加shard'||info=='添加存储节点'||info=='删除shard'||info=='删除存储节点'){
                this.shard_show=true;
                this.init_show=false;
                this.computer_show=false;
                this.storage_state='error';
                this.shard_icon='el-icon-circle-close'
                this.shard_title=info+'失败';
              }
              clearInterval(timer);
            }
          }
        });
        if(i>=86400){
            clearInterval(timer);
        }
      }, 0)
    },
    getStatus (timer,data,i,action_name) {
      setTimeout(()=>{
        const postarr={};
        postarr.job_type='get_status';
        postarr.version=version_arr[0].ver;
        postarr.job_id=data;
        postarr.timestamp=timestamp_arr[0].time+'';
        postarr.paras={};
        getEvStatus(postarr).then((res) => {
        if(res.status=='done'||res.status=='failed'){
          //clearInterval(timer);
          //this.info=res.error_info;
          if(res.status=='done'){
            const newArrdone={
              content:action_name+'成功',
              timestamp: getNowDate(),
              color: '#0bbd87',
              icon: 'el-icon-circle-check'
            };
            this.activities.push(newArrdone)
            this.getList()
            //this.dialogStatusVisible=false;
            clearInterval(timer);
          }else{
            if(res.attachment==null&&res.error_code=='70001'&&res.status=='failed'){
              if(i>5){
                const newArr={
                  content:res.error_info,
                  timestamp: getNowDate(),
                  color: 'red',
                  icon: 'el-icon-circle-close'
                };
                this.activities.push(newArr);
                clearInterval(timer);
              }
            }else{
              const newArr={
                content:res.error_info,
                timestamp: getNowDate(),
                color: 'red',
                icon: 'el-icon-circle-close'
              };
              this.activities.push(newArr);
              clearInterval(timer);
            }
            //this.installStatus = true;
          }
        }
        // else{
        //    const newArrgoing={
        //     content:res.error_info,
        //     timestamp: getNowDate(),
        //     color: '#0bbd87'
        //   };
        //   this.activities.push(newArrgoing)
        //   this.info=res.error_info;
        //   this.installStatus = true;
        // }
      });
        if(i>=86400){
            clearInterval(timer);
        }
      }, 0)
    },
    beforeDestroy() {         
      clearInterval(this.timer);                 
      this.timer = null;
    }
  },
};
</script>
<style >
.right_input_min{
  width:25%;
}
.el-table__expanded-cell[class*=cell]{
  padding:0px 0px ;
}
</style>
<style scoped>
.el-input{
    width:inherit;
}
</style>