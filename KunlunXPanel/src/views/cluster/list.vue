<template>
  <div class="app-container">
    <div class="filter-container">
      <div class="table-list-search-wrap">
        <el-input
          class="list_search_keyword"
          v-model="listQuery.name"
          placeholder="可输入集群名称搜索"
          @keyup.enter.native="handleFilter"
        />
        <el-button  icon="el-icon-search" @click="handleFilter">
          查询
        </el-button>
        <el-button  icon="el-icon-refresh-right" @click="handleClear">
          重置
        </el-button>
         <el-button
          class="filter-item"
          type="primary"
          icon="el-icon-plus"
          @click="handleCreate"
          v-if="cluster_creata_priv==='Y'"
        >新增</el-button>
        <div v-text="info" v-show="installStatus===true" class="info"></div>
      </div>
      </div>

    <el-table
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

      <el-table-column label="集群名称" align="center">
        <template slot-scope="{row}">
          <span class="link-type" @click="handleDetail(row)">{{ row.nick_name }}</span>
        </template>
      </el-table-column>
      <el-table-column
            prop="ha_mode"
            align="center"
            label="高可用模式">
      </el-table-column>
      <el-table-column
            prop="comptotal"
            align="center"
            label="计算节点总数">
      </el-table-column>

      <el-table-column
            prop="shardtotal"
            align="center"
            label="shard分配"
            :show-overflow-tooltip="true"
            >
      </el-table-column>
      <!-- <el-table-column
            prop="when_created"
            align="center"
            label="创建时间">
      </el-table-column> -->
      <el-table-column
            prop="back_up"
            align="center"
            label="最近备份时间">
            <template slot-scope="scope">
            <span v-if="scope.row.first_backup===''">无备份</span>
            <span v-else-if="scope.row.first_backup!==''">{{scope.row.first_backup}}</span>
        </template>
      </el-table-column>
      <el-table-column
        label="操作"
        align="center"
        width="350"
        class-name="small-padding fixed-width"
        v-if="storage_node_create_priv==='Y'||shard_create_priv==='Y'||compute_node_create_priv==='Y'||restore_priv==='Y'||backup_priv==='Y'||cluster_drop_priv==='Y'||row.ha_mode==='rbr'"
      >
        <template slot-scope="{row,$index}">
           <el-button type="primary" size="mini" @click="handleUpdate(row)" v-if="storage_node_create_priv==='Y'&&shard_create_priv==='Y'&&compute_node_create_priv==='Y'">+</el-button>
          <el-button type="primary" size="mini" @click="handleRestore(row)" v-if="restore_priv==='Y'">恢复</el-button>
          <el-button type="primary" size="mini" @click="handleBackUp(row,$index)" v-if="backup_priv==='Y'">备份</el-button>
          <el-button type="primary" size="mini" @click="handleSwitchOver(row,$index)" v-if="row.ha_mode==='rbr'">主备切换</el-button>
          <el-button
            size="mini"
            type="danger"
            @click="handleDelete(row,$index)"
            v-if="cluster_drop_priv==='Y'"
          >删除</el-button>
        </template>
      </el-table-column>
    </el-table>

    <pagination v-show="total>0" :total="total" :page.sync="listQuery.pageNo" :limit.sync="listQuery.pageSize" @pagination="getList" />
    <!-- 新增 -->
    <el-dialog :title="textMap[dialogStatus]" :visible.sync="dialogFormVisible" custom-class="single_dal_view">
      <el-form
        ref="dataForm"
        :rules="rules"
        :model="temp"
        label-position="left"
        label-width="250px"
        >
      <el-form-item label="集群名称:" prop="nick_name"  v-show="dialogStatus==='create'||'detail'">
        <el-input  v-model="temp.nick_name" class="right_input" placeholder="请输入集群名称" :disabled="dialogStatus==='detail'"/>
      </el-form-item>
      <el-form-item label="选择计算机:" prop="machinelist"  v-if="dialogStatus==='create'">
        <el-checkbox-group 
        v-model="temp.machinelist"
        :min="minMachine"
        :max="machineTotal">
          <el-checkbox v-for="machine in machines" :label="machine.hostaddr" :key="machine.id">{{machine.hostaddr}}</el-checkbox>
        </el-checkbox-group>
      </el-form-item>
       <el-form-item label="高可用模式:" prop="ha_mode" v-show="dialogStatus==='create'||'detail'" >
          <el-select v-model="temp.ha_mode" placeholder="请选择高可用模式" :disabled="dialogStatus==='detail'">
            <el-option
              v-for="item in hamodeData"
              :key="item.id"
              :label="item.label"
              :value="item.id">
            </el-option>
          </el-select>
        </el-form-item>
  
        <div v-show="dialogStatus==='create'||'detail'">
        <el-form-item label="shard个数:" prop="shards_count" >
          <el-input  v-model="temp.shards_count" class="right_input" placeholder="请输入shard个数" :disabled="dialogStatus==='detail'">
            <i slot="suffix" style="font-style:normal;margin-right: 10px; line-height: 30px;">个</i>
          </el-input>
        </el-form-item>
        </div>

        <el-form-item label="副本数:" prop="snode_count"  v-show="dialogStatus==='create'||'detail'">
          <el-input  v-model="temp.snode_count" class="right_input"  placeholder="副本数至少是3，且不能大于所选计算机数" :disabled="dialogStatus==='detail'">
           <i slot="suffix" style="font-style:normal;margin-right: 10px; line-height: 30px;">个</i>
          </el-input>
        </el-form-item>

        <el-form-item label="缓冲池大小:" prop="buffer_pool"  v-show="dialogStatus==='create'||'detail'">
          <el-input  v-model="temp.buffer_pool" class="right_input"  placeholder="缓冲池大小单位为GB" :disabled="dialogStatus==='detail'">
            <i slot="suffix" style="font-style:normal;margin-right: 10px; line-height: 30px;">GB</i>
          </el-input>
        </el-form-item>

        <div v-show="isShow"  v-if="dialogStatus==='create'?true:false">
          <el-form-item label="每个计算节点最大连接数:"  prop="max_connections">
            <el-input  v-model="temp.max_connections" class="right_input"  placeholder="请输入每个计算节点最大连接数" />
          </el-form-item>
          <el-form-item label="每个计算节点的cpu核数:" prop="per_computing_node_cpu_cores">
            <el-input  v-model="temp.per_computing_node_cpu_cores" class="right_input"  placeholder="请输入每个计算节点的cpu核数" />
          </el-form-item>
          <el-form-item label="每个计算节点最大的存储值:" prop="per_computing_node_max_mem_size">
            <el-input  v-model="temp.per_computing_node_max_mem_size" class="right_input"  placeholder="请输入每个计算节点最大的存储值">
              <i slot="suffix" style="font-style:normal;margin-right: 10px; line-height: 30px;">MB</i>
            </el-input>
          </el-form-item>
          <el-form-item label="每个存储节点的cpu核数:" prop="per_storage_node_cpu_cores">
            <el-input  v-model="temp.per_storage_node_cpu_cores" class="right_input"  placeholder="请输入每个存储节点的cpu核数"/>
          </el-form-item>
          <el-form-item label="每个存储节点innodb缓冲池大小:" prop="per_storage_node_innodb_buffer_pool_size">
            <el-input  v-model="temp.per_storage_node_innodb_buffer_pool_size" class="right_input"  placeholder="请输入每个存储节点innodb缓冲池大小">
              <i slot="suffix" style="font-style:normal;margin-right: 10px; line-height: 30px;">MB</i>
            </el-input>
          </el-form-item>
          <el-form-item label="每个存储节点rocksdb缓冲池大小:" prop="per_storage_node_rocksdb_buffer_pool_size">
            <el-input  v-model="temp.per_storage_node_rocksdb_buffer_pool_size" class="right_input"  placeholder="请输入每个存储节点rocksdb缓冲池大小">
              <i slot="suffix" style="font-style:normal;margin-right: 10px; line-height: 30px;">MB</i>
            </el-input>
          </el-form-item>
          <el-form-item label="每个存储节点初始化存储值:" prop="per_storage_node_initial_storage_size">
            <el-input  v-model="temp.per_storage_node_initial_storage_size" class="right_input"  placeholder="请输入每个存储节点初始化存储值">
              <i slot="suffix" style="font-style:normal;margin-right: 10px; line-height: 30px;">GB</i>
            </el-input>
          </el-form-item>
          <el-form-item label="每个存储节点最大存储值:" prop="per_storage_node_max_storage_size">
            <el-input  v-model="temp.per_storage_node_max_storage_size" class="right_input"  placeholder="请输入每个存储节点最大存储值">
              <i slot="suffix" style="font-style:normal;margin-right: 10px; line-height: 30px;">GB</i>
            </el-input>
          </el-form-item>
        </div>
        <div :class="isShow === false?'ro-90':'ro90'"  @click="toggle"  v-if="dialogStatus==='create'?true:false"><i class="el-icon-d-arrow-left" /></div>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="dialogFormVisible = false" v-show="!dialogDetail">关闭</el-button>
        <el-button type="primary" @click="dialogStatus==='create'?createData():updateData(row)"  v-show="!dialogDetail">确认</el-button>
      </div>
    </el-dialog>

    <!-- 添加节点 -->
    <el-dialog title="添加节点" :visible.sync="dialogNodeVisible" custom-class="single_dal_view">
      <el-form
        ref="nodeForm"
        :model="nodetemp"
        :rules="rules"
        label-position="left"
        label-width="110px"
      >
        <el-form-item label="选择计算机:" prop="machinelist"  v-if="dialogStatus==='update'">
        <el-checkbox-group 
        v-model="nodetemp.machinelist"
        :min="minMachine"
        :max="machineTotal">
          <el-checkbox v-for="machine in machines" :label="machine.hostaddr" :key="machine.id">{{machine.hostaddr}}</el-checkbox>
        </el-checkbox-group>
      </el-form-item>
        <el-form-item label="集群名称:" prop="nick_name"  v-if="dialogStatus==='update'?true:false" >
          <el-input v-model="nodetemp.nick_name" :disabled="true" />
        </el-form-item>
        <el-form-item label="类型:" prop="node_type" v-if="dialogStatus==='update'?true:false">
          <el-select v-model="nodetemp.node_type" placeholder="请选择类型"  v-if="dialogStatus=== 'update'?true:false">
          <el-option
            v-for="item in node_types"
            :key="item.id"
            :label="item.label"
            :value="item.id">
          </el-option>
        </el-select>
      </el-form-item>
      <el-form-item label="shard名称:" prop="shard_name" v-if="nodetemp.node_type=='add_nodes' &&dialogStatus==='update'" >
          <el-select v-model="nodetemp.shard_name" placeholder="请选择shard名称"  v-if="dialogStatus=== 'update'?true:false">
          <el-option
            v-for="shard in shardList"
            :key="shard.id"
            :label="shard.name"
            :value="shard.name">
          </el-option>
        </el-select>
      </el-form-item>
       <el-form-item label="个数:" prop="shards" v-if="dialogStatus==='update'">
            <el-select v-model="nodetemp.shards" placeholder="请选择个数" :disabled="dialogStatus==='detail'">
            <el-option
              v-for="item in shardsDate"
              :key="item.id"
              :label="item.label"
              :value="item.id">
            </el-option>
          </el-select>
        </el-form-item>
      </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button @click="dialogNodeVisible = false">关闭</el-button>
          <el-button type="primary" @click="updateData(row)">确认</el-button>
        </div>
    </el-dialog>
    <!-- 恢复-->
    <el-dialog title="恢复集群" :visible.sync="dialogRestoreVisible" custom-class="single_dal_view">
      <el-form
        ref="restoreForm"
        :model="restoretemp"
        :rules="rules"
        label-position="left"
        label-width="110px"
      >
      <el-form-item label="选择计算机:" prop="machinelist"  v-if="dialogStatus==='restore'">
        <el-checkbox-group 
        v-model="restoretemp.machinelist"
        :min="minMachine"
        :max="machineTotal">
          <el-checkbox v-for="machine in machines" :label="machine.hostaddr" :key="machine.id">{{machine.hostaddr}}</el-checkbox>
        </el-checkbox-group>
      </el-form-item>
        <el-form-item label="原集群名称:" prop="old_cluster_name">
          <el-input v-model="restoretemp.old_cluster_name" :disabled="true" />
        </el-form-item>
        <el-form-item label="新集群名称:" prop="nick_name">
          <el-input v-model="restoretemp.nick_name" placeholder="请输入新集群名称" />
        </el-form-item>
        <el-form-item label="回档时间:" prop="now">
          <el-date-picker v-model="restoretemp.now"  type="datetime" value-format="yyyy-MM-dd HH:mm:ss" placeholder="请选择回档时间"></el-date-picker>
        </el-form-item>
        <el-form-item label="最早备份时间:" prop="end_ts">
          <el-input  v-model="restoretemp.end_ts"  :disabled="true" />
        </el-form-item>
      </el-form>

        <div slot="footer" class="dialog-footer">
          <el-button @click="dialogRestoreVisible = false">关闭</el-button>
          <el-button type="primary" @click="restoreData(restoretemp)">确认</el-button>
        </div>
    </el-dialog>
    <!--  状态框 -->
    <el-dialog :visible.sync="dialogStatusVisible" custom-class="single_dal_view" width="400px">
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
 import { messageTip,handleCofirm,getNowDate,getNextMonth,createCode } from "@/utils";
 import Pagination from '@/components/Pagination' 
 import {ha_mode_arr,shards_arr,per_shard_arr,norepshards_arr,node_type_arr,version_arr,storage_type_arr,timestamp_arr} from "@/utils/global_variable"
 import {getClusterList,ifBackUp,getAllMachine,getShards,uAssign} from '@/api/cluster/list'
 import {addShards,createCluster,addComps,addNodes,getEvStatus,delCluster,backeUpCluster,restoreCluster,getMetaMode,getBackUpStorage} from '@/api/cluster/listInterface'
export default {
  name: "list",
  components: { Pagination }, 
  data() {
    const validatemachine = (rule, value, callback) => {
     if(value.length === 0){
        callback(new Error("请选择计算机"));
      }
      // else if(value.length <3){
      //   callback(new Error("所选计算机数不能小于3"));
      // }
      else {
        callback();
      }
    };
    const validateHaMode = (rule, value, callback) => {
     if(!value){
        callback(new Error("请选择高可用模式"));
      }
      else {
        callback();
      }
    };
     const validateShardsCount = (rule, value, callback) => {
     if(!value){
        callback(new Error("请输入shard个数"));
      }
      else {
        callback();
      }
    };
    const validateSnodeCount = (rule, value, callback) => {
     if(!value){
        callback(new Error("请输入副本数"));
      }else if(!(/^[0-9_]+$/.test(value))){
        callback(new Error("副本数只能输入数字"));
      }
      else if(value<3){
        callback(new Error("副本数不能小于3"));
      }
      // else if(value>this.temp.machinelist.length){
      //   callback(new Error("副本数不能大于所选计算机数"));
      // }
      else {
        callback();
      }
    };
    const validateCompCount = (rule, value, callback) => {
     if(!value){
        callback(new Error("请输入计算节点总个数"));
      }else if (!(/^[0-9]+$/.test(value)) ) {
        callback(new Error("计算节点总个数只能输入数字"));
      }
      else {
        callback();
      }
    };
    const validateBufferPool = (rule, value, callback) => {
     if(!value){
        callback(new Error("请输入缓冲池大小"));
      }else if (!(/^[0-9]+$/.test(value)) ) {
        callback(new Error("缓冲池大小只能输入数字"));
      }
      else {
        callback();
      }
    };
    const validateRestoreTime = (rule, value, callback) => {
      //console.log(value);return;
      const time=getNowDate();
      const netxMonth=getNextMonth(time);
      if(!this.restoretemp.now){
        callback(new Error("请选择回档时间"));
      }else if(this.restoretemp.nowlue<this.restoretemp.end_ts){
        callback(new Error("回档时间不能早于最早备份时间"));
      }else if(this.restoretemp.now>netxMonth){
        callback(new Error("回档时间选择范围应在当前系统时间开始一个月之内"));
      }else {
      callback();
      }
    };
    const validateNodeType = (rule, value, callback) => {
     if(!value){
        callback(new Error("请选择类型"));
      }
      else {
        callback();
      }
    };
    const validateShardName = (rule, value, callback) => {
      if(this.temp.node_type==='add_nodes'){
        if(!value){
          callback(new Error("请选择shard名称"));
        }
        else {
          callback();
        }
      }else{
         callback();
      }
    };
     const validateNodeTotal = (rule, value, callback) => {
     if(!value){
        callback(new Error("请选择个数"));
      }
      else {
        callback();
      }
    };
    // const validateClusterName = (rule, value, callback) => {
    //  if(!value){
    //     callback(new Error("请输入集群名称"));
    //   }
    //   else {
    //     callback();
    //   }
    // };
    const validateNickName = (rule, value, callback) => {
      if(!value){
        callback(new Error("请输入新集群名称"));
      }
      else {
      callback();
      }
    };
    // const validateNowName = (rule, value, callback) => {
    //   const time=getNowDate();
    //   const netxMonth=getNextMonth(time);
    //   if(!value){
    //     callback(new Error("请选择回档时间"));
    //   }else if(value<this.restoretemp.end_ts){
    //     callback(new Error("回档时间不能早于最早备份时间"));
    //   }else if(value>netxMonth){
    //     callback(new Error("回档时间选择范围应在当前系统时间开始一个月之内"));
    //   }else {
    //   callback();
    //   }
    // };
    return {
      tableKey: 0,
      list: null,
      listLoading: true,
      searchLoading:false,
      total: 0,
      listQuery: {
        pageNo: 1,
        pageSize: 10,
        name: ''
      },
      temp: {
        ha_mode:'',
        shards_count:'0',
        snode_count: '3',
        comp_count:'',
        buffer_pool:'1',
        max_connections:'6',
        per_computing_node_cpu_cores:'8',
        per_computing_node_max_mem_size:'',
        per_storage_node_cpu_cores:'8',
        per_storage_node_innodb_buffer_pool_size:'',
        per_storage_node_rocksdb_buffer_pool_size:'',
        per_storage_node_initial_storage_size:'20',
        per_storage_node_max_storage_size:'20',
        nick_name:'',
        machinelist:[],
        node_type:'',
        shard_name:'',
        shards:'',
      },
      restoretemp:{
        old_cluster_name:'',
        now:'',
        end_ts:'',
        machinelist:[],
        backup_cluster_name:'',
        nick_name:''
      },
      nodetemp:{
        machinelist:[],
        node_type:'',
        shard_name:'',
        shards:'',
        name:'',
        nick_name:''
      },
      dialogFormVisible: false,
      dialogRestoreVisible:false,
      dialogNodeVisible:false,
      dialogStatusVisible:false,
      dialogStatus: "",
      textMap: {
        update: "添加节点",
        create: "新增集群",
        detail: "详情",
        restore:'恢复集群'
      },
      dialogDetail: false,
      message_tips:'',
      message_type:'',
      user_add_priv:'',
      user_drop_priv:'',
      user_edit_priv:'',
      row:{},
      hamodeData:ha_mode_arr,
      shardsDate:shards_arr,
      norepshardsDate:norepshards_arr,
      pershardDate:per_shard_arr,
      isShow:false,
      machinelist:[],
      machines:[],
      minMachine:0,
      machineTotal:0,
      node_types:node_type_arr,
      shardList:[],
      backup_priv:JSON.parse(sessionStorage.getItem('priv')).backup_priv,
      restore_priv:JSON.parse(sessionStorage.getItem('priv')).restore_priv,
      cluster_creata_priv:JSON.parse(sessionStorage.getItem('priv')).cluster_creata_priv,
      cluster_drop_priv:JSON.parse(sessionStorage.getItem('priv')).cluster_drop_priv,
      shard_create_priv:JSON.parse(sessionStorage.getItem('priv')).shard_create_priv,
      storage_node_create_priv:JSON.parse(sessionStorage.getItem('priv')).storage_node_create_priv,
      compute_node_create_priv:JSON.parse(sessionStorage.getItem('priv')).compute_node_create_priv,
      timer:null,
      installStatus:false,
      info:'',
      activities: [],
      //dialogBackUpStorageVisible:false,
      //stypelist:storage_type_arr,
      rules: {
        machinelist: [
          { required: true, trigger: "blur",validator: validatemachine },
        ],
        ha_mode: [
          { required: true, trigger: "blur",validator: validateHaMode },
        ],
         shards_count: [
          { required: true, trigger: "blur",validator: validateShardsCount },
        ],
         snode_count: [
          { required: true, trigger: "blur",validator: validateSnodeCount },
        ],
        comp_count: [
          { required: true, trigger: "blur",validator: validateCompCount },
        ],
        buffer_pool: [
          { required: true, trigger: "blur",validator: validateBufferPool },
        ],
        now: [
           { required: true, trigger: "blur",validator: validateRestoreTime },
        ],
        node_type: [
            { required: true, trigger: "blur",validator: validateNodeType },
        ],
        shard_name: [
            { required: true, trigger: "blur",validator: validateShardName},
        ],
        shards: [
            { required: true, trigger: "blur",validator: validateNodeTotal },
        ],
        // cluster_name: [
        //     { required: true, trigger: "blur",validator: validateClusterName },
        // ],
        nick_name: [
            { required: true, trigger: "blur",validator: validateNickName },
        ],
        // now:[
        //   {required: true, trigger: "blur",validator: validateNowName }
        //   ]
      },
  
    };
  },
  created() {
    this.getList();
    //this.getMode();
  },
  watch: {
    'temp.machinelist': {
      handler: function(val,oldVal) {
       //旧数据中包含时无需push
      //   this.temp.machinelist=[];
      //   this.temp.machinelist.forEach(item => {
      //   if (this.temp.machinelist.indexOf(item) == -1) {
      //     const newArr={"hostaddr":item};
      //     this.temp.machinelist.push(newArr)
      //   }
      // })
      this.temp.shards_count=this.temp.machinelist.length;
      },
    }
  },
  methods: {
    // async getMode() {
    //   const temp={};
    //   temp.job_type='get_meta_mode';
    //   temp.ver=version_arr[0].ver;
    //   temp.job_id=uuidv4();
    //   const res = await getMetaMode(temp);
    //   console.log(res);
    //   if(res.length>0){
    //     const ha_mode_arr={'id':res.mode,'label':res.mode};
    //     this.hamodeData=ha_mode_arr;
    //   }
    // },
    toggle(){
       this.isShow = !this.isShow //取反
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
        let queryParam = Object.assign({}, this.listQuery)
        queryParam.effectCluster= sessionStorage.getItem('affected_clusters');
        queryParam.apply_all_cluster= sessionStorage.getItem('apply_all_cluster');
        //模糊搜索
        getClusterList(queryParam).then(response => {
          this.list = response.list;
          this.total = response.total;
          setTimeout(() => {
            this.listLoading = false
          }, 0.5 * 1000)
        });
    },
    resetTemp() {
      this.temp = {
       ha_mode:'',
        shards_count:'0',
        snode_count: '3',
        comp_count:'',
        buffer_pool:'1',
        max_connections:'6',
        per_computing_node_cpu_cores:'8',
        per_computing_node_max_mem_size:'',
        per_storage_node_cpu_cores:'8',
        per_storage_node_innodb_buffer_pool_size:'',
        per_storage_node_rocksdb_buffer_pool_size:'',
        per_storage_node_initial_storage_size:'20',
        per_storage_node_max_storage_size:'20',
        nick_name:'',
        machinelist:[],
        node_type:'',
        shard_name:'',
        shards:'',
      };
    },
    handleCreate() {
      this.resetTemp();
      this.dialogStatus = "create";
      this.dialogFormVisible = true;
      this.dialogDetail = false;
      getAllMachine().then((res) => {
        this.machines = res.list;
        this.minMachine=0;
        this.machineTotal=res.total;
      });
      
      // const temp={};
      // temp.job_type='get_meta_mode';
      // temp.version=version_arr[0].ver;
      // temp.job_id='';
      // temp.timestamp=timestamp_arr[0].time+'';
      // temp.paras={}
      // getMetaMode(temp).then((res) => {
      //   if(res){
      //     const ha_mode=[{"id":res.attachment.mode,"label":res.attachment.mode}];
      //     this.hamodeData=ha_mode;
      //   };
      // });
      this.$nextTick(() => {
        this.$refs.dataForm.clearValidate();
      });
    },
    createData() {
      this.$refs["dataForm"].validate((valid) => {
         if (valid) {
          const tempData = Object.assign({}, this.temp);
          //处理machinelist的格式
          let machinelist=[];
          tempData.machinelist.forEach(item => {
            const newArr={"hostaddr":item};
            machinelist.push(newArr)
          })
          const clusterData={};
          const paras={};
          clusterData.user_name=sessionStorage.getItem('login_username');
          clusterData.job_id='';
          clusterData.version=version_arr[0].ver;
          clusterData.timestamp=timestamp_arr[0].time+'';
          clusterData.job_type='create_cluster';

          paras.comps=tempData.machinelist.length+'';
          paras.shards=tempData.shards_count+'';
          paras.nodes=tempData.snode_count;
          paras.max_storage_size=tempData.per_storage_node_max_storage_size;
          paras.max_connections=tempData.max_connections;
          paras.innodb_size=tempData.buffer_pool;
          paras.cpu_cores=tempData.per_computing_node_cpu_cores;
          paras.ha_mode=tempData.ha_mode;
          paras.machinelist=machinelist;
          paras.nick_name=tempData.nick_name;
          paras.dbcfg='1';
          clusterData.paras=paras;
          //发送接口
          createCluster(clusterData).then(response=>{
            let res = response;
            if(res.status=='accept'){
              this.dialogFormVisible = false;
              this.dialogStatusVisible=true;
              this.activities=[];
              const newArr={
                content:'正在新增集群...',
                timestamp: getNowDate(),
                size: 'large',
                type: 'primary',
                icon: 'el-icon-more'
              };
              this.activities.push(newArr)
              // this.message_tips = '正在新增...';
              // this.message_type = 'success';
              //调获取状态接口
              let i=0;
              let timer = setInterval(() => {
                setTimeout(()=>{
                  const postarr={};
                  postarr.job_type='get_status';
                  postarr.version=version_arr[0].ver;
                  postarr.job_id=res.job_id;
                  postarr.timestamp=timestamp_arr[0].time+'';
                  postarr.paras={};
                  getEvStatus(postarr).then((ress) => {
                  if(ress.status=='done'||ress.status=='failed'){
                    clearInterval(timer);
                    //this.info=ress.error_info;
                    if(ress.status=='done'){
                      const newArrdone={
                        content:ress.error_info,
                        timestamp: getNowDate(),
                        color: '#0bbd87',
                        icon: 'el-icon-circle-check'
                      };
                      this.activities.push(newArrdone)
                      //查是否应用于全部集群，不是，增加cluster_id
                      const  apply_all_cluster=sessionStorage.getItem('apply_all_cluster');
                      if(apply_all_cluster==2){
                          const arrs= {};
                          arrs.effectCluster=sessionStorage.getItem('affected_clusters');
                          arrs.cluster_name=ress.attachment.cluster_name;
                          arrs.username=sessionStorage.getItem('login_username');
                          arrs.type='add';
                          uAssign(arrs).then((responses) => {
                            let res_update = responses;
                            if(res_update.code==200){
                              this.dialogFormVisible = false;
                              sessionStorage.setItem('affected_clusters',res_update.effectCluster);
                              //console.log(sessionStorage.getItem('affected_clusters'));
                            }
                          });
                      }
                      setTimeout(() => {
                        this.getList();
                        this.dialogStatusVisible=false;
                      }, 3000);
                    }else{
                      const newArrdone={
                        content:ress.error_info,
                        timestamp: getNowDate(),
                        color: 'red',
                        icon: 'el-icon-circle-close'
                      };
                      this.activities.push(newArrdone)
                      //this.installStatus = true;
                    }
                  }else{
                     const newArrgoing={
                      content:ress.error_info,
                      timestamp: getNowDate(),
                      color: '#0bbd87'
                    };
                    this.activities.push(newArrgoing)
                    //this.info=ress.error_info;
                    //this.installStatus = true;
                  }
                });
                  if(i>=86400){
                      clearInterval(timer);
                  }
                  i++;
                }, 0)
              
               }, 1000)
            }else if(res.status=='ongoing'){
              this.message_tips = '系统正在操作中，请等待一会！';
              this.message_type = 'error';
            }else{
              this.message_tips = res.error_info;
              this.message_type = 'error';
            }
            messageTip(this.message_tips,this.message_type);
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
    handleUpdate(row) {
      this.temp = Object.assign({}, row); 
      this.nodetemp.nick_name=this.temp.nick_name;
      this.nodetemp.name=this.temp.name;
      this.dialogStatus = "update";
      this.dialogNodeVisible = true;
      this.dialogDetail = false;
      //获取计算机名称
      getAllMachine().then((res) => {
      this.machines = res.list;
        this.minMachine=0;
        this.machineTotal=res.total;
       });
       //获取分片名称
       getShards(row.id).then((response) => {
          let res = response;
          if(res.code==200){
          this.shardList=res.list;
          }
        });
      this.$nextTick(() => {
        this.$refs["nodeForm"].clearValidate();
      });
      
    },
    updateData() {
      this.$refs["nodeForm"].validate((valid) => {
        if (valid) {
          const tempData = Object.assign({}, this.nodetemp);
          //处理machinelist的格式
          let machinelist=[];
          tempData.machinelist.forEach(item => {
            const newArr={"hostaddr":item};
            machinelist.push(newArr)
          })
          tempData.job_id = '';
          tempData.version=version_arr[0].ver;
          tempData.user_name=sessionStorage.getItem('login_username');
          tempData.job_type=tempData.node_type;
          tempData.timestamp=timestamp_arr[0].time+'';
          const paras={}
          paras.machinelist=machinelist;
          paras.cluster_name = tempData.name;
          paras.nick_name=tempData.nick_name;
          //删除参数
          this.$delete(tempData,'node_type');
          this.$delete(tempData,'name');
          this.$delete(tempData,'nick_name');
          this.$delete(tempData,'machinelist');
          // this.$delete(tempData,'shardtotal');
          if(tempData.job_type=='add_shards'){
            //新增shard
            paras.shards = tempData.shards;
            this.$delete(tempData,'shard_name');
            this.$delete(tempData,'shards');
            tempData.paras=paras;
            addShards(tempData).then((response) => {
            let res = response;
            if(res.status=='accept'){
              this.dialogNodeVisible = false;
              this.dialogStatusVisible=true;
              this.activities=[];
              const newArr={
                content:'正在新增shard...',
                timestamp: getNowDate(),
                size: 'large',
                type: 'primary',
                icon: 'el-icon-more'
              };
              this.activities.push(newArr)
              //this.message_tips = '正在新增shard...';
              //this.message_type = 'success';
              //调获取状态接口
              let i=0;
              let timer = setInterval(() => {
                this.getStatus(timer,res.job_id,i++)
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
          }else if(tempData.job_type=='add_comps'){
            paras.comps=tempData.shards;
            tempData.paras=paras;
            this.$delete(tempData,'shards');
            this.$delete(tempData,'shard_name');
            //新增计算节点
            addComps(tempData).then((response) => {
            let res = response;
            if(res.status=='accept'){
             this.dialogNodeVisible = false;
             this.dialogStatusVisible=true;
             this.activities=[];
             const newArr={
                content:'正在新增计算节点...',
                timestamp: getNowDate(),
                size: 'large',
                type: 'primary',
                icon: 'el-icon-more'
             };
             this.activities.push(newArr)
              //this.message_tips = '正在新增计算节点...';
              //this.message_type = 'success';
              //调获取状态接口
              let i=0;
              let timer = setInterval(() => {
                this.getStatus(timer,res.job_id,i++)
              }, 1000)
            }
            else if(res.status=='ongoing'){
              this.message_tips = '系统正在操作中，请等待一会！';
              this.message_type = 'error';
            }else{
              this.message_tips = res.error_info;
              this.message_type = 'error';
            }
            messageTip(this.message_tips,this.message_type);
          });
          }else if(tempData.job_type=='add_nodes'){
             paras.nodes=tempData.shards;
             paras.shard_name=tempData.shard_name;
             tempData.paras=paras;
             this.$delete(tempData,'shards');
             this.$delete(tempData,'shard_name');
            //新增存储节点
            addNodes(tempData).then((response) => {
            let res = response;
            if(res.status=='accept'){
              this.dialogNodeVisible = false;
              this.dialogStatusVisible=true;
              this.activities=[];
              const newArr={
                  content:'正在新增存储节点...',
                  timestamp: getNowDate(),
                  size: 'large',
                  type: 'primary',
                  icon: 'el-icon-more'
              };
              this.activities.push(newArr)
              //this.message_tips =  '正在新增存储节点...';
              //this.message_type = 'success';
              //调获取状态接口
              let i=0;
              let timer = setInterval(() => {
                this.getStatus(timer,res.job_id,i++)
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
          
        }
      });
    },
    handleDelete(row) {
      //const code=createCode();
      handleCofirm("此操作将永久删除该数据, 是否继续?").then( () =>{
        //先执行删权限
        const arrs= {};
        arrs.effectCluster=sessionStorage.getItem('affected_clusters');
        arrs.cluster_name=row.name;
        arrs.username=sessionStorage.getItem('login_username');
        arrs.type='del';
        uAssign(arrs).then((responses) => {
          let res_update = responses;
          if(res_update.code==200){
            this.dialogFormVisible = false;
            sessionStorage.setItem('affected_clusters',res_update.effectCluster);
          }
        });
        //调接口删集群
         const tempData={};
          tempData.user_name=sessionStorage.getItem('login_username');
          tempData.job_id = '';
          tempData.version=version_arr[0].ver;
          tempData.job_type='delete_cluster';
          tempData.timestamp=timestamp_arr[0].time+'';
          tempData.paras={'cluster_name':row.name,'nick_name':row.nick_name}
          delCluster(tempData).then((response)=>{
            let res = response;
            if(res.status=='accept'){
              this.dialogFormVisible = false;
              this.dialogStatusVisible=true;
              this.activities=[];
              const newArr={
                content:'正在删除集群...',
                timestamp: getNowDate(),
                size: 'large',
                type: 'primary',
                icon: 'el-icon-more'
              };
              this.activities.push(newArr);
              //this.message_tips = '正在删除...';
              //this.message_type = 'success';
              //调获取状态接口
              let i=0;
              let timer = setInterval(() => {
                this.getStatus(timer,res.job_id,i++)
              }, 1000)
            }
            else if(res.status=='ongoing'){
              this.message_tips = '系统正在操作中，请等待一会！';
              this.message_type = 'error';
            }else{
              this.message_tips = res.error_info;
              this.message_type = 'error';
            }
            messageTip(this.message_tips,this.message_type);
      })
      }).catch(() => {
          console.log('quxiao')
          messageTip('已取消删除','info');
      }); 
    },
    handleBackUp(row) {
      //先验证是否备份存储介质
      const arrs= {};
      arrs.version=version_arr[0].ver;
      arrs.job_id='';
      arrs.job_type='get_backup_storage';
      arrs.timestamp=timestamp_arr[0].time+'';
      arrs.paras={}
      getBackUpStorage(arrs).then((res) => {
        if(res.attachment.list_backup_storage!==null){
          handleCofirm("确定要备份"+row.nick_name+"这个集群么?").then( () =>{
          const tempData = Object.assign({}, row);
          const backupData={};
          backupData.user_name=sessionStorage.getItem('login_username');
          backupData.job_id = '';
          backupData.paras = {'backup_cluster_name':tempData.name,'nick_name':tempData.nick_name};
          backupData.version=version_arr[0].ver;
          backupData.timestamp=timestamp_arr[0].time+'';
          backupData.job_type='backup_cluster';
          backeUpCluster(backupData).then((response)=>{
            let res = response;
            if(res.status=='accept'){
              this.dialogFormVisible = false;
              this.dialogStatusVisible=true;
              this.activities=[];
              const newArr={
                content:'正在备份集群...',
                timestamp: getNowDate(),
                size: 'large',
                type: 'primary',
                icon: 'el-icon-more'
              };
              this.activities.push(newArr);
              //this.message_tips = '正在备份...';
              //this.message_type = 'success';
              //调获取状态接口
              let i=0;
              let timer = setInterval(() => {
                this.getStatus(timer,res.job_id,i++)
              }, 1000)
            }
            else if(res.status=='ongoing'){
                this.message_tips = '系统正在操作中，请等待一会！';
                this.message_type = 'error';
              }else{
                this.message_tips = res.error_info;
                this.message_type = 'error';
              }
            messageTip(this.message_tips,this.message_type);
          })
        }).catch(() => {
            messageTip('已取消备份','info');
        }); 
        }else{  
          messageTip('请先添加备份存储目标!','error');
        }
      });
     
    },
    handleRestore(row) {
      this.restoretempDate = Object.assign({}, row); 
      this.dialogStatus = "restore";
      this.dialogFormVisible = false;
      this.dialogDetail = false;
      //判断是否存在备份文件
       ifBackUp(row.id).then((response) => {
        let res = response;
        if(res.code==200){
         this.dialogRestoreVisible=true;
         this.restoretemp.end_ts=response.list[0].end_ts;
         this.restoretemp.old_cluster_name=row.nick_name;
         this.restoretemp.backup_cluster_name = row.name;
         this.restoretemp.now=getNowDate();
          getAllMachine().then((res) => {
            this.machines = res.list;
            this.minMachine=0;
            this.machineTotal=res.total;
          });
        }
        else{
          this.message_tips = res.message;
          this.message_type = 'error';
          messageTip(this.message_tips,this.message_type);
        }
      });
      this.$nextTick(() => {
        if(this.$refs["dataForm"]){
          this.$refs["dataForm"].clearValidate();
        }
      });
    },
    restoreData() {
      this.$refs["restoreForm"].validate((valid) => {
        if (valid) {
      // if(!this.restoretemp.now){
      //   messageTip("请选择回档时间");return;
      // }else if(!this.restoretemp.now<this.restoretemp.end_ts){
      //   messageTip("回档时间不能早于最早备份时间!");return;
      // }else{
      //   const time=getNowDate();
      //   const netxMonth=getNextMonth(time);
      //   if(this.restoretemp.now>netxMonth){
      //     messageTip("回档时间选择范围应在当前系统时间开始一个月之内");return;
      //   }
      // }
      // if(this.restoretemp.machinelist.length===0){
      //   messageTip("请选择计算机");return;
      // }
      // if(this.restoretemp.machinelist.length<3){
      //   messageTip("所选计算机数不能小于3");return;
      // }
      // if(!this.restoretemp.nick_name){
      //   messageTip("请输入新集群名称");return;
      // }
      const tempData = Object.assign({}, this.restoretemp);
      //处理machinelist的格式
      let machinelist=[];
      tempData.machinelist.forEach(item => {
        const newArr={"hostaddr":item};
        machinelist.push(newArr)
      })
      const restoreData={};
      restoreData.user_name=sessionStorage.getItem('login_username');
      restoreData.job_id = '';
      restoreData.version=version_arr[0].ver;
      restoreData.job_type='restore_new_cluster';
      restoreData.timestamp=timestamp_arr[0].time+'';
      const paras={}
      paras.nick_name = tempData.nick_name;
      paras.backup_cluster_name = tempData.backup_cluster_name;
      paras.timestamp=tempData.now;
      paras.machinelist=machinelist;
      restoreData.paras=paras;
      restoreCluster(restoreData).then((response) => {
        let res = response;
        if(res.status=='accept'){
          this.dialogRestoreVisible = false;
          this.dialogStatusVisible=true;
          this.activities=[];
          const newArr={
            content:'正在恢复集群...',
            timestamp: getNowDate(),
            size: 'large',
            type: 'primary',
            icon: 'el-icon-more'
          };
          this.activities.push(newArr);
          //this.message_tips = '正在恢复...';
          //this.message_type = 'success';
          //调获取状态接口
          let i=0;
          let timer = setInterval(() => {
            this.getStatus(timer,res.job_id,i++)
          }, 1000)
        }
        else if(res.status=='ongoing'){
          this.message_tips = '系统正在操作中，请等待一会！';
          this.message_type = 'error';
        }else{
          this.message_tips = res.error_info;
          this.message_type = 'error';
        }
        messageTip(this.message_tips,this.message_type);

      });
        }
      })
    },
    handleSwitchOver(row){
      this.$router.push({
        path: '/cluster/switchover',
        query: {
          id: row.id
        }
        })
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
          //this.info=res.error_info;
          if(res.status=='done'){
            const newArrdone={
            content:res.error_info,
              timestamp: getNowDate(),
              color: '#0bbd87',
              icon: 'el-icon-circle-check'
            };
            this.activities.push(newArrdone)
            this.getList();
            this.dialogStatusVisible=false;
          }else{
            const newArr={
              content:res.error_info,
              timestamp: getNowDate(),
              color: 'red',
              icon: 'el-icon-circle-close'
            };
            this.activities.push(newArr);
            //this.installStatus = true;
          }
        }else{
           const newArrgoing={
            content:res.error_info,
            timestamp: getNowDate(),
            color: '#0bbd87'
          };
          this.activities.push(newArrgoing)
          //this.info=res.error_info;
          //this.installStatus = true;
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
<style scoped>
.right_input{
  width:60%;
}
.block{
  margin-left: 20px;
  max-height: 400px;
  overflow: auto;
}
.el-timeline{
  margin-left: 2px;
}
/* 修改滚动条宽度 */
::-webkit-scrollbar {
	width: 5px;
}
::-webkit-scrollbar-thumb{
  border-radius: 5px;
  -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,.3);
  background-color: rgba(0,0,0,0.1);
}
</style>