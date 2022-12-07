<template>
  <div class="app-container">
    <div class="filter-container">
      <div class="table-list-search-wrap">
        <el-input
          class="list_search_keyword"
          v-model="listQuery.hostaddr"
          placeholder="可输入IP搜索"
          @keyup.enter.native="handleFilter"
        />
        <el-button  icon="el-icon-search" @click="handleFilter">
          查询
        </el-button>
        <el-button  icon="el-icon-refresh-right" @click="handleClear">
          重置
        </el-button>
         <!-- <el-button
          class="filter-item"
          type="primary"
          icon="el-icon-plus"
          @click="handleUpdate"
          v-if="storage_node_create_priv==='Y'&&work_mode=='enterprise'"
        >添加节点</el-button> -->
        <el-button
          class="filter-item"
          type="primary"
          @click="handleSwitch"
          v-if="meta_ha_mode==='rbr'"
        ><i class="el-icon-sort" style="transform:rotate(90deg);"></i>主备切换</el-button>
        <el-button
          class="filter-item"
          type="primary"
          icon="el-icon-copy-document"
          @click="handleRedo"
          v-if="meta_ha_mode==='rbr'"
        >重做备机</el-button>
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
      <el-table-column label="IP" align="center">
        <template slot-scope="{row}">
          <span class="link-type" @click="handleDetail(row)">{{ row.hostaddr }}</span>
        </template>
      </el-table-column>
      <el-table-column   
        prop="port" 
        label="端口"
        align="center">
      </el-table-column>
      <el-table-column
        align="center"
        label="高可用模式">
        <template>
          <span>{{meta_ha_mode}}</span>
        </template>
      </el-table-column>
      <el-table-column
        align="center"
        label="节点状态">
        <template slot-scope="scope">
          <span v-if="scope.row.member_state==='source'">主节点</span>
          <span v-else-if="scope.row.member_state=='replica'">备机节点</span>
          <span v-else></span>
        </template>
      </el-table-column>

      <el-table-column
        align="center"
        label="同步状态">
        <template slot-scope="scope">
          <span v-if="scope.row.sync_state==='fsync'">同步</span>
          <span v-else-if="scope.row.sync_state=='async'">异步</span>
          <span v-else></span>
        </template>
      </el-table-column>
      <el-table-column
        prop="replica_delay" 
        align="center"
        label="主备延迟时间(s)">
      </el-table-column>
      <!-- <el-table-column
        label="操作"
        align="center"
        width="250"
        fixed="right"
        class-name="small-padding fixed-width"
      >
        <template slot-scope="{row,$index}">
          <el-button
            size="mini"
            type="danger"
            @click="handleDelete(row,$index)"
            v-if="cluster_drop_priv==='Y'"
          >删除</el-button>
        </template>
      </el-table-column> -->
    </el-table>

    <pagination v-show="total>0" :total="total" :page.sync="listQuery.pageNo" :limit.sync="listQuery.pageSize" @pagination="getList" />
    <!-- 添加节点 -->
    <el-dialog :title="textMap[dialogStatus]" :visible.sync="dialogNodeVisible" custom-class="single_dal_view"  :close-on-click-modal="false">
      <el-form
        ref="nodeForm"
        :model="nodetemp"
        :rules="rules"
        label-position="left"
        label-width="110px"
      >
      <el-form-item label="选择计算机:" prop="machinelist"  v-if="dialogStatus==='update'">
        <el-select v-model="nodetemp.machinelist" multiple placeholder="请选择">
            <el-option
              v-for="machine in strogemachines"
              :key="machine.id"
              :label="machine.hostaddr"
              :value="machine.hostaddr">
            </el-option>
          </el-select>
      </el-form-item>
        <el-form-item label="节点个数:" prop="nodes" >
          <el-input  v-model="nodetemp.nodes" class="right_input" placeholder="请输入节点个数" :disabled="dialogStatus==='detail'">
            <i slot="suffix" style="font-style:normal;margin-right: 10px; line-height: 30px;">个</i>
          </el-input>
        </el-form-item>
      </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button @click="dialogNodeVisible = false">关闭</el-button>
          <el-button type="primary" @click="updateData(row)">确认</el-button>
        </div>
    </el-dialog>
    <el-dialog :visible.sync="dialogStatusVisible" custom-class="single_dal_view" width="400px"  :close-on-click-modal="false">
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
    <el-dialog :title="job_id" :visible.sync="dialogStatusShowVisible" custom-class="single_dal_view" :close-on-click-modal="false" :before-close="beforeDestory">
      <!-- <div style="height: 400px;">
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
      </div> -->
     <!-- <div class="stepComponent" >
    <div class="approvalProcess" >
        <el-steps :active="active" finish-status="success" direction="vertical" >
           <el-step :title="item.label"  v-for="item in approvalProcessProject" :id="item.id">
            <template slot="description" >
             <div class="step-row" v-for="item in approvalProcessProject">
               <table width="100%" border="0" cellspacing="0" cellpadding="0" class="processing_content">
                         <tr>
                            <td style="color:#98A6BE">
                                <div class="processing_content_detail" style="float:left;width:70%"><span >192.168.0.136_47001</span></div> 
                              <div class="processing_content_detail" style="float:right;"><span ><i class="el-icon-time"></i>&nbsp;&nbsp;昨天12:24</span> </div>
                            </td>
                          </tr>
                          <tr>
                            <td>
                                <div class="processing_content_detail" style="float:left;width:70%">
                                <div style="float:left;width: 2px;height: 20px; background:#C7D4E9;margin-left:10px;margin-right:10px"></div> 
                                <span style="color:#919FB8">ongoing</span></div> 
                            </td>
                          </tr>
                </table>
           </div>
         </template>
 
           </el-step>
        </el-steps>
         <el-button style="margin-top: 12px;" @click="next">下一步</el-button>
 
  </div>
 
</div> -->
      
      <!-- <div style="width: 100%;background: #fff;padding:20px; ">
      <el-steps class="hoverSteps" direction="vertical" :space="80" :active="2">
        <el-step v-for="(value, key) in lists" :key="key">
          <template slot="description">
              <div @mouseover="mouseOver(key)"
                   @mouseleave="mouseLeave(key)" :id="key">
                   <div class="stepNoBtn" :class="current===key? 'stepBtn':''">
                      <div class="step-title-font">{{ value.title }}</div>
                    <div>{{value.description}}</div>
                    <div class="btnPosition">
                      <el-button v-if="current===key" size="mini" type="primary">详情</el-button>
                    </div>
                   </div>
            </div>
          </template>
        </el-step>
      </el-steps>
    </div> -->

      <div style="width: 100%;background: #fff;padding:0 20px;">
        <el-steps direction="vertical" :active="init_active">
          <el-step :title="init_title" icon="el-icon-more" v-if="init_show"></el-step>
          <el-step :title="computer_title" :status="computer_state" :icon="computer_icon" :description="computer_description" v-if="computer_show">
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
                <!-- <template slot="description">
                  <el-button size="mini" type="primary"  @click="thisDetail(item.computer_id)">详情</el-button>
                </template> -->
                </el-step>
              </el-steps>
            </div>
            </template>
          </el-step>
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
                <!-- <template slot="description">
                  <el-button size="mini" type="primary" @click="thisDetail(item.shard_id)">详情</el-button>
                </template> -->
                </el-step>
              </el-steps>
            </div>
            </template>
          </el-step>
          <!-- <el-step :title="finish_title" :icon="finish_icon" :description="finish_description" color='red'></el-step> -->
        </el-steps>
      </div>
    </el-dialog>
    <!--shard信息框 -->
    <el-dialog :title="dialogStatus" :visible.sync="dialogShardInfo" custom-class="single_dal_view" :close-on-click-modal="false">
      <json-viewer :value="shardInfo"></json-viewer>
    </el-dialog>

    <!--主备切换-->
    <el-dialog title="主备切换" :visible.sync="dialogSwitchOVisible" custom-class="single_dal_view">
      <el-form
        ref="switchForm"
        :model="switchtemp"
        :rules="rules"
        label-position="left"
        label-width="100px"
      >
      <el-form-item label="主节点:" prop="primary_node">
        <span>{{switchtemp.primary_node}}</span>
      </el-form-item>
      <el-form-item label="备机节点:" prop="replica" >
        <el-select v-model="switchtemp.replica" clearable placeholder="请选择备机节点">
          <el-option
            v-for="item in options"
            :key="item.value"
            :label="item.label"
            :value="item.label">
          </el-option>
        </el-select>
      </el-form-item>
      </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button @click="dialogSwitchOVisible = false">关闭</el-button>
          <el-button type="primary" @click="switchData(switchtemp)">确认</el-button>
        </div>
    </el-dialog>
    <!--重做备机节点-->
    <el-dialog title="重做备机节点" :visible.sync="dialogRedoVisible" custom-class="single_dal_view">
      <el-form
        ref="redoForm"
        :model="redotemp"
        :rules="rules"
        label-position="left"
        label-width="180px"
      >
      <el-form-item label="需重做的备机节点:" prop="redolist" >
        <el-select v-model="redotemp.redolist" multiple placeholder="请选择" @change="change">
          <el-option
            v-for="item in options"
            :key="item.value"
            :label="item.label"
            :value="item.label">
          </el-option>
        </el-select>
       </el-form-item>
       
       <el-form-item label="是否从主节点上拉取数据:" prop="allow_pull_from_master" >
        <el-radio v-model="redotemp.allow_pull_from_master" label="1">是</el-radio>
        <el-radio v-model="redotemp.allow_pull_from_master" label="0">否</el-radio>
      </el-form-item>
      <el-form-item label="主备延迟:" prop="allow_replica_delay"  v-if="redotemp.allow_pull_from_master=='0'">
        <el-input  v-model="redotemp.allow_replica_delay" class="right_input"  placeholder="主备延迟">
          <i slot="suffix" style="font-style:normal;margin-right: 10px; line-height: 30px;">s</i>
        </el-input>
      </el-form-item>
       <el-form-item label="是否备份:" prop="need_backup" >
        <el-radio v-model="redotemp.need_backup" label="1">是</el-radio>
        <el-radio v-model="redotemp.need_backup" label="0">否</el-radio>
      </el-form-item>
      <el-form-item label="备份存储目标:" prop="hdfs_host"  v-if="redotemp.need_backup=='1'">
        <el-select v-model="redotemp.hdfs_host" placeholder="请选择">
          <el-option
            v-for="item in hdfs_options"
            :key="item.value"
            :label="item.label"
            :value="item.label">
          </el-option>
        </el-select>
       </el-form-item>
      <el-form-item label="限速:" prop="pv_limit" >
        <el-input  v-model="redotemp.pv_limit" class="right_input"  placeholder="限速">
          <i slot="suffix" style="font-style:normal;margin-right: 10px; line-height: 30px;">KB/s</i>
        </el-input>
      </el-form-item>

      </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button @click="dialogRedoVisible = false">关闭</el-button>
          <el-button type="primary" @click="redoData(redotemp)">确认</el-button>
        </div>
    </el-dialog>
    <!-- 重做备机状态框 -->
    <el-dialog :title="job_id" :visible.sync="dialogStatusRedoVisible" custom-class="single_dal_view" :close-on-click-modal="false">
      <div style="width: 100%;background: #fff;padding:0 20px;" class="block">
        <el-steps direction="vertical" :active="init_active">
          <el-step 
          :title="items.title"  
          v-for="(items, key) in statusList" 
          :key="key" 
          :icon="items.icon" 
          :status="items.status"
          :description="items.description">
            <template slot="description">
              <span>{{items.description}}</span>
            <div style="padding:20px;">
              <el-steps direction="vertical" :active="second_active" >
                <el-step
                    v-for="(item,index) of statusList[key].second"
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
          </el-step>
        </el-steps>
      </div>
    </el-dialog>
  </div>
</template>

<script>
 import { messageTip,handleCofirm,getNowDate,getNextMonth,createCode,gotoCofirm } from "@/utils";
 import Pagination from '@/components/Pagination' 
 import {ha_mode_arr,shards_arr,per_shard_arr,norepshards_arr,node_type_arr,version_arr,storage_type_arr,timestamp_arr,policy_arr} from "@/utils/global_variable"
 import {getStroMachine,addNodes,getEvStatus,getShardsJobLog,getShardTable,getOtherShards,getExpandTableList,switchShard,getBackupStorageList,rebuildNode} from '@/api/cluster/list'
 import {getMetaClusterList,getMetaPrimary,getMetaStandbyNode} from '@/api/metacluster/list'
 import JsonViewer from 'vue-json-viewer'
export default {
  name: "list",
  components: { Pagination,JsonViewer }, 
  props: ['data', 'defaultActive'],
  data() {  
    const validateNodes = (rule, value, callback) => {
      if(!value){
        callback(new Error("请输入节点个数"));
      }else if(!(/^[0-9]+$/.test(value))){
        callback(new Error("节点个数只能输入数字"));
      }else if(value>256){
        callback(new Error("节点个数不能大于256"));
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
        hostaddr: ''
      },
      
      nodetemp:{
        machinelist:[],
        nodes:'1'
      },
      switchtemp:{
        replica:'',
        primary_node:'',
        shard_id:'-1',
        cluster_id:'-1',
      },
      redotemp:{
        redolist:[],
        allow_pull_from_master:'0',
        allow_replica_delay:'30',
        need_backup:'0',
        pv_limit:'10',
        hdfs_host:''   
      },
      dialogFormVisible: false,
      dialogRetreatedVisible:false,
      dialogNodeVisible:false,
      dialogStatusVisible:false,
      dialogExpandVisible:false,
      dialogExpandInfoVisible:false,
      dialogExpondInfo:false,
      expondSatus:true,
      expondResult:false,
      dialogStatus: "",
      textMap: {
        update: "添加节点",
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
      comp_machines:[],
      comp_minMachine:0,
      comp_machineTotal:0,
      node_types:node_type_arr,
      shardList:[],
      work_mode:sessionStorage.getItem('work_mode'),
      meta_ha_mode:sessionStorage.getItem('meta_ha_mode'),
      cluster_drop_priv:JSON.parse(sessionStorage.getItem('priv')).cluster_drop_priv,
      storage_node_create_priv:JSON.parse(sessionStorage.getItem('priv')).storage_node_create_priv,
      timer:null,
      installStatus:false,
      info:'',
      activities: [],
      dialogStatusShowVisible:false,
      active: 0,
      // 已选步骤
      stepSuc: [0],
      // 步骤参数
      stepParams: [],
      computer:[
        // {
        //   title: '192.168.0.136_47001;',
        //   icon: 'el-icon-loading',
        //   status: 'process',
        //   computer_id:'101',
        //   description:''
        // },
      ],
       shard:[
        // {
        //   title: '104',
        //   icon: 'el-icon-circle-check',
        //   status: 'success',
        //   shard_id:'104',
        //   description:''
        // },
      ],
      init_active:0,
      second_active:0,
      statusList:[],
      ipList:[],
      computer_state:'',
      storage_state:'',
      computer_title:'',
      computer_icon:'',
      shard_icon:'',
      shard_title:'',
      comp_active:0,
      shard_active:0,
      strogemachines:[],
      init_title:'',
      init_show:true,
      computer_show:true,
      shard_show:true,
      init_active:0,
      finish_title:'',
      finish_icon:'',
      finish_description:'',
      computer_description:'',
      shard_description:'',
      shardInfo:'',
      dialogShardInfo:false,
      job_id:'',
      oldClusterList:[],
      shardNameList:[],
      activeName:'',
      shardTable:[],
      shardsList:[],
      srcShardsList:[],
      expondInfo:'',
      expondSatus:true,
      expand_init:'',
      expand_end:'',
      expondInit:true,
      expondResult:false,
      outoExpandInfoVisible:false,
      policys:policy_arr,

      dialogSwitchOVisible:false,
      dialogStatusRedoVisible:false,
      dialogRedoVisible:false,
      hdfs_options:[],
      options:[],
      rules: {
        // ha_mode: [
        //   { required: true, trigger: "blur",validator: validateHaMode },
        // ],
        //  shards_count: [
        //   { required: true, trigger: "blur",validator: validateShardsCount },
        // ],
        //  snode_count: [
        //   { required: true, trigger: "blur",validator: validateSnodeCount },
        // ],
        // comp_count: [
        //   { required: true, trigger: "blur",validator: validateCompCount },
        // ],
        // buffer_pool: [
        //   { required: true, trigger: "blur",validator: validateBufferPool },
        // ],
        // now: [
        //    { required: true, trigger: "blur",validator: validateRestoreTime },
        // ],
        // node_type: [
        //     { required: true, trigger: "blur",validator: validateNodeType },
        // ],
        // shard_name: [
        //     { required: true, trigger: "blur",validator: validateShardName},
        // ],
        // shards: [
        //     { required: true, trigger: "blur",validator: validateNodeTotal },
        // ],
        nodes: [
            { required: true, trigger: "blur",validator: validateNodes },
        ],
        // nick_name: [
        //     { required: true, trigger: "blur",validator: validateNickName },
        // ],
        // fullsync_level:[
        //     { required: true, trigger: "blur",validator: validateFullsyncLevel },
        // ],
        // retreated_time:[
        //   {required: true, trigger: "blur",validator: validateretreatedTime }
        // ],
        // old_cluster_id:[
        //   {required: true, trigger: "blur",validator: validateOldClusterId }
        // ],
        // dst_shard_id:[
        //   {required: true, trigger: "blur",validator: validateDstShardName }
        // ],
        // policy:[
        //   {required: true, trigger: "blur",validator: validatePolicy }
        // ],
        // auto_shard_id:[
        //   {required: true, trigger: "blur",validator: validateAutoShardId }
        // ],
        // table_list:[
        //   {required: true, trigger: "blur",validator: validateTableList }
        // ]
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
      //this.temp.shards_count=this.temp.machinelist.length;
      },
    },
    'expandtemp.table_list': {
      handler: function(val,oldVal) {
        if(val.length>0){
          this.expandtemp.dsc_flag=true;
          this.expandtemp.title='提交'
        }else{
          this.expandtemp.dsc_flag=false;
          this.expandtemp.title='自动扩容'
          this.expandtemp.shard_name='';  
        }
      },
    },
    'autoexpandtemp.table_list': {
      handler: function(val,oldVal) {
        if(val.length>0){
          this.autoexpandtemp.dsc_flag=true;
        }else{
          this.autoexpandtemp.dsc_flag=false;
        }
      },
    },
  },
  methods: {
    autoChangeDscShardName(value){
      for(let i=0;i<this.shardsList.length;i++){
        if(this.shardsList[i].id==value){
          this.autoexpandtemp.dst_shard_name=this.shardsList[i].name;
        }
      }
    },
    selAutoChangeHandle(val){
      this.autoexpandtemp.table_list=val;
    },
    ChangeShardName(value){
      for(let i=0;i<this.shardsList.length;i++){
        if(this.shardsList[i].id==value){
          this.expandtemp.dst_shard_name=this.shardsList[i].name;
        }
      }
    },
    selectionChangeHandle(val){
      this.expandtemp.shard_name=this.expandtemp.list.shard_name;
      // if(this.expandtemp.table_list.length>0){
      //   //return 1;
      //   this.message_tips = '只允许勾选同一个shard的表数据';
      //   this.message_type = 'error';
      //   messageTip(this.message_tips,this.message_type);
      // }
      this.expandtemp.table_list=val;
      // console.log(this.expandtemp.table_list);
      if(this.expandtemp.shard_name){
        //获取shards名称
        let temp={cluster_id:this.expandtemp.list.cluster_id,shard_id:this.expandtemp.list.shard_id};
        getOtherShards(temp).then((res) => {
          this.shardsList = res.list;
        });
        this.$nextTick(() => {
          if(this.$refs["dataForm"]){
            this.$refs["dataForm"].clearValidate();
          }
        });
      }
    },
    handleClick(tab) {
      //console.log(tab);
      if(tab.$attrs.value){
        let ids=tab.$attrs.value.split('_');
        this.expandtemp.list.cluster_id=ids[1];
        this.expandtemp.list.shard_id=ids[0];
        this.expandtemp.list.shard_name=tab.name;
        //this.expandtemp.shard_name=tab.name;
        this.expandtemp.dst_shard_id='';
        //console.log(this.expandtemp.list);
        //获取shard下的table
        this.shardTable=[];
        let table={cluster_id:ids[1],id:ids[0]}
        getShardTable(table).then((res) => {
         if(res.code==200){
            this.shardTable = res.list;
          }else{
            this.message_tips = res.message;
            this.message_type = 'error';
            messageTip(this.message_tips,this.message_type);
          }
        });
        if(this.expandtemp.shard_name){
        //获取shards名称
        this.shardsList=[];
        let temp={cluster_id:this.expandtemp.list.cluster_id,shard_id:this.expandtemp.list.shard_id};
        getOtherShards(temp).then((res) => {
          this.shardsList = res.list;
        });
        this.$nextTick(() => {
          if(this.$refs["dataForm"]){
            this.$refs["dataForm"].clearValidate();
          }
        });
      }
      }
      // this.activeName = tab.name;
    },
    //清除定时器
    beforeDestory(){
      clearInterval(this.timer)
      this.dialogStatusShowVisible=false;
    },
    thisDetail(id){
      const temp ={id:id};
      getShardsJobLog(temp).then((response) => {
        this.dialogStatus = 'shard_'+id+'详细信息';
        this.dialogShardInfo=true;
        this.shardInfo=response.shard_nodes

      });
    },
    handleStatus(){
      this.dialogStatusShowVisible=true;
    },
    // ChangeSaler(value){
    //   //console.log(value);
    //   if(value=='add_shards'){
    //     //获取存储机器
    //     getStroMachine().then((res) => {
    //       this.strogemachines=[];
    //       this.strogemachines = res.list;
    //     });
    //     this.nodetemp.nodes='3';
    //     this.nodetemp.shards='1'
        
    //   }else if(value=='add_nodes'){
    //     //获取存储机器
    //      getStroMachine().then((res) => {
    //       this.strogemachines=[];
    //       this.strogemachines = res.list;
    //     });
    //      this.nodetemp.nodes='1'
    //   }else if(value=='add_comps'){
    //     //获取计算机器
    //     getCompMachine().then((res) => {
    //       this.strogemachines=[];
    //       this.strogemachines = res.list;
    //     });
    //     this.nodetemp.nodes='1'
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
      this.listQuery.hostaddr = ''
      this.listQuery.pageNo = 1
      this.getList()
    },
    getList() {
        //console.log(this.work_mode);
        this.listLoading = true
        let queryParam = Object.assign({}, this.listQuery)
        //queryParam.effectCluster= sessionStorage.getItem('affected_clusters');
        // queryParam.apply_all_cluster= sessionStorage.getItem('apply_all_cluster');
        //模糊搜索
        getMetaClusterList(queryParam).then(response => {
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
        shards_count:'1',
        snode_count: '3',
        comp_count:'1',
        buffer_pool:'1024',
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
        comp_machinelist:[],
        node_type:'',
        shard_name:'',
        shards:'',
        computer_user:'',
        computer_password:'',
        fullsync_level:'1'
      };
    },
    // autoExpandTemp(){
    //   this.autoexpandtemp={
    //     shard_name:'',
    //     list:{cluster_id:'',shard_id:'',shard_name:''},
    //     table_list:[],
    //     policy:'',
    //     auto_shard_id:'',
    //     tables:[],
    //     policy_name:'',
    //     dst_shard_id:'',
    //     dst_shard_name:'',
    //     dsc_flag:false,
    //     if_save:'0'
    //   }
    // },
    resetNodeTemp(){
      this.nodetemp = {
        machinelist:[],
        // node_type:'',
        // shard_name:'',
        // shards:'',
        // name:'',
        // nick_name:'',
        nodes:'1'
      };
    },
    resetSwitchtemp(){
      this.switchtemp={
        replica:'',
        primary_node:'',
        shard_id:'-1',
        cluster_id:'-1'
      }
    },
    resetRedoTemp(){
      this.redotemp={
        redolist:[],
        allow_pull_from_master:'0',
        allow_replica_delay:'30',
        need_backup:'0',
        pv_limit:'10',
        hdfs_host:''   
      }
    },
    handleSwitch() {
      this.resetSwitchtemp();
      this.dialogSwitchOVisible=true;
      //获取主节点
        getMetaPrimary().then((res) => {
          if(res.list.length>0){
             this.switchtemp.primary_node=res.list[0].ip+'('+res.list[0].port+')';
          }
        });
        //获取备机节点
        getMetaStandbyNode().then((res) => {
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
    switchData(row) {
      this.$refs["switchForm"].validate((valid) => {
        if (valid) {
          //this.dialogStatusVisible=true;
          const tempData = {};
          tempData.user_name = sessionStorage.getItem('login_username');
          tempData.job_id ='';
          tempData.job_type ='manual_switch';
          tempData.version=version_arr[0].ver;
          tempData.timestamp=timestamp_arr[0].time+'';
          let master_ip=(this.switchtemp.primary_node.substring(0,this.switchtemp.primary_node.length-1)).replace('(','_');
          let paras={};
          if(!this.switchtemp.replica.length){
            paras.shard_id=this.switchtemp.shard_id;
            paras.cluster_id=this.switchtemp.cluster_id;
            paras.master_hostaddr=master_ip;
          }else{
            let assign_ip=(this.switchtemp.replica.substring(0,this.switchtemp.replica.length-1)).replace('(','_');
            paras.shard_id=this.switchtemp.shard_id;
            paras.cluster_id=this.switchtemp.cluster_id;
            paras.master_hostaddr=master_ip;
            paras.assign_hostaddr=assign_ip;
          }
          tempData.paras=paras;
          //console.log(tempData);return;
          //发送接口
          switchShard(tempData).then(response=>{
            let res = response;
            if(res.status=='accept'){
              this.dialogSwitchOVisible = false;
              // this.activities=[];
              // const newArr={
              //   content:'正在进行主备切换...',
              //   timestamp: getNowDate(),
              //   size: 'large',
              //   type: 'primary',
              //   icon: 'el-icon-more'
              // };
              // this.activities.push(newArr);
              // this.message_tips = '正在进行主备切换...';
              // this.message_type = 'success';
              this.stepParams=[];
              let i=0;
              const info='主备切换';
              this.computer=[];this.shard=[];this.computer_state='';this.storage_state='';this.computer_title='';this.computer_icon='';this.shard_icon='';this.shard_title='';this.comp_active=0;this.shard_active=0;this.strogemachines=[];this.init_title='';this.init_active=0;this.finish_title='';this.finish_icon='';this.finish_description='';this.computer_description='';this.shard_description='';this.job_id='';this.timer=null;
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
        }
      });
    },
    change(){
      if(this.options.length==this.redotemp.redolist.length){
        this.redotemp.allow_pull_from_master='1'; 
      }else{
        this.redotemp.allow_pull_from_master='0'; 
      }
    },
    handleRedo() {
      this.resetRedoTemp();
      this.dialogRedoVisible=true;
      getBackupStorageList().then(response => {
        this.hdfs_options=[];
        if(response.list!==null){
          for (let i = 0; i < response.list.length; i++) {
              const arr={'value':i,'label':response.list[i].hostaddr+'('+response.list[i].port+')'};
              this.hdfs_options.push(arr);
          }
        }
      });
      //获取备机节点
      getMetaStandbyNode().then((res) => {
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
    redoData(row) {
      this.$refs["redoForm"].validate((valid) => {
        if (valid) {
          let tempData = {};
          tempData.user_name = sessionStorage.getItem('login_username');
          tempData.job_id ='';
          tempData.job_type ='rebuild_node';
          tempData.version=version_arr[0].ver;
          tempData.timestamp=timestamp_arr[0].time+'';
          let rebuild=[];
          let ipList=[]
          for(let i=0;i<this.redotemp.redolist.length;i++){
            let ip_arr=(this.redotemp.redolist[i].substring(0,this.redotemp.redolist[i].length-1)).split('(');
            let pv_limit='';
            if(!this.redotemp.pv_limit){
              pv_limit='10'
            }else{
              pv_limit=this.redotemp.pv_limit
            }
            let rb_paras={}
            if(this.redotemp.need_backup=='1'){
              const hdfs_host=(this.redotemp.hdfs_host.substring(0,this.redotemp.hdfs_host.length-1)).replace('(',':');
              rb_paras.hostaddr=ip_arr[0];
              rb_paras.port=ip_arr[1];
              rb_paras.need_backup=this.redotemp.need_backup;
              rb_paras.hdfs_host=hdfs_host;
              rb_paras.pv_limit=pv_limit
            }else{
              rb_paras.hostaddr=ip_arr[0];
              rb_paras.port=ip_arr[1];
              rb_paras.need_backup=this.redotemp.need_backup;
              rb_paras.pv_limit=pv_limit
            }
            rebuild.push(rb_paras)
            ipList.push({title:ip_arr[0]+'_'+ip_arr[1],error_code: '0',error_info: '',status: 'no_start',step: ''})
          }
           let paras={}
          if(this.redotemp.allow_pull_from_master=='1'){
            paras.shard_id='-1',
            paras.cluster_id='-1',
            paras.allow_pull_from_master=this.redotemp.allow_pull_from_master,
            paras.rb_nodes= rebuild
          }else{
            paras.shard_id='-1';
            paras.cluster_id='-1';
            paras.allow_pull_from_master=this.redotemp.allow_pull_from_master;
            paras.allow_replica_delay=this.redotemp.allow_replica_delay;
            paras.rb_nodes= rebuild
          }
          tempData.paras=paras;
          this.dialogStatusRedoVisible=true;
          this.dialogRedoVisible=false;
          //console.log(tempData);return;
          //发送接口
          rebuildNode(tempData).then(response=>{
            let res = response;
            if(res.status=='accept'){
              this.dialogRedoVisible = false;
              this.stepParams=[];
              let i=0;
              const info='重做备机节点';this.statusList=[];this.timer=null;this.init_active=0;this.second_active=0;
              //传重做备机节点的ipList
              this.getFStatus(this.timer,res.job_id,i++,info,ipList)
              this.timer= setInterval(() => {
                this.getFStatus(this.timer,res.job_id,i++,info,ipList)
              }, 5000)
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
    handleUpdate(row) {
      this.resetNodeTemp();
      this.dialogStatus = "update";
      this.dialogNodeVisible = true;
      this.dialogDetail = false;
      //获取存储机器
        getStroMachine().then((res) => {
          this.strogemachines=[];
          this.strogemachines = res.list;
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
            machinelist.push(item)
          })
          tempData.job_id = '';
          tempData.version=version_arr[0].ver;
          tempData.user_name=sessionStorage.getItem('login_username');
          tempData.job_type='add_nodes';
          tempData.timestamp=timestamp_arr[0].time+'';
          const paras={}
          paras.cluster_id = '-1';
          paras.nodes=tempData.nodes;
          paras.shard_id='-1';
          if(machinelist.length>0){
            paras.storage_iplists=machinelist;
          }
          tempData.paras=paras;
          this.$delete(tempData,'machinelist');
          this.$delete(tempData,'nodes');
          //console.log(tempData);return;
          //新增存储节点
          addNodes(tempData).then((response) => {
            let res = response;
            if(res.status=='accept'){
              this.dialogNodeVisible = false;
              this.dialogStatusShowVisible=true;
              //把之前的数据清空
              this.computer=[];this.shard=[];this.computer_state='';this.storage_state='';this.computer_title='';this.computer_icon='';this.shard_icon='';this.shard_title='';this.comp_active=0;this.shard_active=0;this.strogemachines=[];this.init_title='';this.init_active=0;this.finish_title='';this.finish_icon='';this.finish_description='';this.computer_description='';this.shard_description='';this.job_id='';this.timer=null;
              this.init_show=true;
              const info='添加存储节点'
              //调获取状态接口
              let i=0;
              this.getFStatus(this.timer,res.job_id,i++,info,'')
              this.timer= setInterval(() => {
                this.getFStatus(this.timer,res.job_id,i++,info,'')
              }, 5000)
            }else if(res.status=='ongoing'){
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
    handleDelete(row) {
      const code=createCode();
      let string="此操作将永久删除该数据, 是否继续?code="+code;
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
                  paras.cluster_id='-1';
                  paras.shard_id='-1';
                  paras.hostaddr=row.hostaddr;
                  paras.port=row.port;
                  tempData.paras=paras;
                  console.log(tempData);return;
                  delSnode(tempData).then((response)=>{
                    let res = response;
                    if(res.status=='accept'){
                      this.isShowNodeMenuPanel = false
                      this.dialogStatusShowVisible=true;
                      //调获取状态接口
                      let i=0;
                      this.computer=[];this.shard=[];this.computer_state='';this.storage_state='';this.computer_title='';this.computer_icon='';this.shard_icon='';this.shard_title='';this.comp_active=0;this.shard_active=0;this.strogemachines=[];this.init_title='';this.init_active=0;this.finish_title='';this.finish_icon='';this.finish_description='';this.computer_description='';this.shard_description='';this.job_id='';this.timer=null;
                      const info='删除存储节点'
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
       
    },
    getFStatus (timer,data,i,info,iparr) {
      setTimeout(()=>{
        const postarr={};
        postarr.job_type='get_status';
        postarr.version=version_arr[0].ver;
        postarr.job_id=data;
        postarr.timestamp=timestamp_arr[0].time+'';
        postarr.paras={};
        this.job_id='任务ID:'+data;
        getEvStatus(postarr).then((res) => {
          if(info=='添加shard'||info=='添加计算节点'||info=='添加存储节点'){
            //新增shard,计算节点，存储节点
            if(res.attachment!==null){
              if(info=='添加shard'||info=='添加存储节点'){
                this.shard_show=true;
                this.init_show=false;
                this.computer_show=false;
              }else if(info=='添加计算节点'){
                this.shard_show=false;
                this.init_show=false;
                this.computer_show=true;
              }
              //计算
              if(res.attachment.hasOwnProperty('computer_state')){
                if(res.attachment.computer_state=='ongoing'){
                  this.computer_state='process';
                  this.computer_icon='el-icon-loading'
                  this.computer_title='正在'+info;
                }else if(res.attachment.computer_state=='done'){
                  this.computer_state='success';
                  this.computer_icon='el-icon-circle-check'
                  this.computer_title=info+'成功';
                  //遍历计算节点改状态
                  if(this.computer.length>0){
                    for(let c=0;c<this.computer.length;c++){
                      const arr=res.attachment.computer_hosts.substr(0,res.attachment.computer_hosts.length-1);
                      const computer_hosts=arr.split(';');
                      for(let e=0;e<computer_hosts.length;e++){
                        if(this.computer[c].title==computer_hosts[e]){
                          this.computer[c].icon='el-icon-circle-check';
                          this.computer[c].status='success';
                        }
                      }
                    }
                  }
                }else if(res.attachment.computer_state=='failed'){
                  this.computer_state='error';
                  this.computer_icon='el-icon-circle-close'
                  this.computer_title=info+'失败';
                  //遍历计算节点改状态
                  if(this.computer.length>0){
                    for(let c=0;c<this.computer.length;c++){
                      const arr=res.attachment.computer_hosts.substr(0,res.attachment.computer_hosts.length-1);
                      const computer_hosts=arr.split(';');
                      for(let e=0;e<computer_hosts.length;e++){
                        if(this.computer[c].title==computer_hosts[e]){
                          this.computer[c].icon='el-icon-circle-close';
                          this.computer[c].status='error';
                        }
                      }
                    }
                  this.computer_description=res.error_info;
                  }
                }else{
                  this.computer_state='process';
                  this.computer_icon='el-icon-loading'
                  this.computer_title='正在'+info;
                }
              }
              //存储
              if(res.attachment.hasOwnProperty('storage_state')){
                if(res.attachment.storage_state=='ongoing'){
                  this.storage_state='process';
                  this.shard_icon='el-icon-loading'
                  this.shard_title='正在'+info;
                }else if(res.attachment.storage_state=='done'){
                  this.storage_state='success';
                  this.shard_icon='el-icon-circle-check'
                  this.shard_title=info+'成功';
                  //遍历存储节点改状态
                  if(this.shard.length>0){
                    for(let c=0;c<this.shard.length;c++){
                      let shard_ids='';
                      if(info=='添加shard'){
                        shard_ids=res.attachment.shard_ids;
                      }else{
                        shard_ids=res.attachment.shard_hosts;
                      }
                      //const shard_ids=res.attachment.shard_ids;
                      for(let e=0;e<shard_ids.length;e++){
                        for(var item in shard_ids[e]){
                          var shard_idsValue=shard_ids[e][item];
                          if(this.shard[c].shard_id==shard_idsValue){
                            this.shard[c].icon='el-icon-circle-check';
                            this.shard[c].status='success';
                          }
                        }
                      }
                    }
                  }
                }else if(res.attachment.storage_state=='failed'){
                  this.storage_state='error';
                  this.shard_icon='el-icon-circle-close'
                  this.shard_title=info+'失败';
                  //遍历存储节点改状态
                  if(this.shard.length>0){
                    for(let c=0;c<this.shard.length;c++){
                      let shard_ids='';
                      if(info=='添加shard'){
                        shard_ids=res.attachment.shard_ids;
                      }else{
                        shard_ids=res.attachment.shard_hosts;
                      }
                      for(let e=0;e<shard_ids.length;e++){
                        for(var item in shard_ids[e]){
                          var shard_idsValue=shard_ids[e][item];
                          if(this.shard[c].shard_id==shard_idsValue){
                            this.shard[c].icon='el-icon-circle-close';
                            this.shard[c].status='error';
                          }
                        }
                      }
                    }
                    this.shard_description=res.error_info;
                  }
                  //clearInterval(timer);
                }else{
                  this.storage_state='process';
                  this.shard_icon='el-icon-loading'
                  this.shard_title='正在'+info;
                }
              }
              this.init_title='正在'+info;
              //this.finish_title=info+'集群成功'
              this.init_active=1;
              if(this.computer.length==0||this.shard.length==0){
                if(this.computer.length==0){
                  if(res.attachment.hasOwnProperty('computer_hosts')){
                    const arr=res.attachment.computer_hosts.substr(0,res.attachment.computer_hosts.length-1);
                    const computer_hosts=arr.split(';');
                    if(res.attachment.computer_state=='done'){
                      for(let e=0;e<computer_hosts.length;e++){
                        let newArrgoing={}
                        newArrgoing.title=computer_hosts[e];
                        newArrgoing.icon='el-icon-circle-check';
                        newArrgoing.status= 'success';
                        newArrgoing.description='';
                        newArrgoing.computer_id=res.attachment.computer_id;
                        this.computer.push(newArrgoing)
                      }
                    }else{
                      for(let e=0;e<computer_hosts.length;e++){
                        let newArrgoing={}
                        newArrgoing.title=computer_hosts[e];
                        newArrgoing.icon='el-icon-loading';
                        newArrgoing.status= 'process';
                        newArrgoing.description='';
                        newArrgoing.computer_id=res.attachment.computer_id;
                        this.computer.push(newArrgoing)
                      }
                    }
                  }
                }
                if(this.shard.length==0){
                  if(res.attachment.hasOwnProperty('shard_hosts')){
                    let shard_ids='';
                    if(info=='添加shard'){
                      shard_ids=res.attachment.shard_ids;
                    }else{
                      shard_ids=res.attachment.shard_hosts;
                    }
                    for(let e=0;e<shard_ids.length;e++){
                      for(var item in shard_ids[e]){
                         let shardgoing={}
                        var shard_idsValue=shard_ids[e][item];
                        console.log(shard_idsValue);
                        const shard_text=item+':'+shard_idsValue;
                        //console.log(shard_text);
                        shardgoing.title=shard_idsValue!==''?shard_text:'正在'+info;
                        shardgoing.icon='el-icon-loading';
                        shardgoing.status= 'process';
                        shardgoing.description='';
                        shardgoing.shard_id=shard_idsValue;
                        this.shard.push(shardgoing)
                      }
                    }
                  }
                }
                if(res.status=='failed'){
                  this.storage_state='error';
                  this.shard_icon='el-icon-circle-close'
                  this.shard_title=info+'失败';
                  this.init_show=false;

                  this.computer_state='error';
                  this.computer_icon='el-icon-circle-close'
                  this.computer_title=info+'失败';

                  this.finish_title=info+'失败'
                  this.finish_icon='el-icon-circle-close'
                  this.init_active=1
                  this.finish_description=res.error_info;
                  //遍历计算节点改状态
                  if(this.computer.length>0){
                    for(let c=0;c<this.computer.length;c++){
                      if(res.attachment.hasOwnProperty('computer_hosts')){
                        const arr=res.attachment.computer_hosts.substr(0,res.attachment.computer_hosts.length-1);
                        const computer_hosts=arr.split(';');
                        for(let e=0;e<computer_hosts.length;e++){
                          if(this.computer[c].title==computer_hosts[e]){
                            this.computer[c].icon='el-icon-circle-close';
                            this.computer[c].status='error';
                          }
                        }
                      }
                    }
                  this.computer_description=res.error_info;
                  }
                  //遍历存储节点改状态
                  if(this.shard.length>0){
                    for(let c=0;c<this.shard.length;c++){
                      let shard_ids='';
                      if(res.attachment.hasOwnProperty('shard_hosts')){
                        if(info=='添加shard'){
                          shard_ids=res.attachment.shard_ids;
                        }else{
                          shard_ids=res.attachment.shard_hosts;
                        }
                        for(let e=0;e<shard_ids.length;e++){
                          for(var item in shard_ids[e]){
                            var shard_idsValue=shard_ids[e][item];
                            if(this.shard[c].shard_id==shard_idsValue){
                              this.shard[c].icon='el-icon-circle-close';
                              this.shard[c].status='error';
                            }
                          }
                        }
                      }
                    }
                    this.shard_description=res.error_info;
                  }
                  clearInterval(timer);
                }else if(res.status=='done'){
                  this.storage_state='success';
                  this.shard_icon='el-icon-circle-check'
                  this.shard_title=info+'成功';

                  this.computer_state='success';
                  this.computer_icon='el-icon-circle-check'
                  this.computer_title=info+'成功';

                  this.finish_title=info+'成功'
                  this.finish_icon='el-icon-circle-check'
                  this.init_active=1
                  this.finish_description=res.error_info;
                  //遍历计算节点改状态
                  if(this.computer.length>0){
                    for(let c=0;c<this.computer.length;c++){
                      if(res.attachment.hasOwnProperty('computer_hosts')){
                        const arr=res.attachment.computer_hosts.substr(0,res.attachment.computer_hosts.length-1);
                        const computer_hosts=arr.split(';');
                        for(let e=0;e<computer_hosts.length;e++){
                          if(this.computer[c].title==computer_hosts[e]){
                            this.computer[c].icon='el-icon-circle-check';
                            this.computer[c].status='success';
                          }
                        }
                      }
                    }
                  }
                  //遍历存储节点改状态
                  if(this.shard.length>0){
                    for(let c=0;c<this.shard.length;c++){
                      let shard_ids='';
                      if(res.attachment.hasOwnProperty('shard_hosts')){
                        if(info=='添加shard'){
                          shard_ids=res.attachment.shard_ids;
                        }else{
                          shard_ids=res.attachment.shard_hosts;
                        }
                        //const shard_ids=res.attachment.shard_ids;
                        for(let e=0;e<shard_ids.length;e++){
                          for(var item in shard_ids[e]){
                            var shard_idsValue=shard_ids[e][item];
                            if(this.shard[c].shard_id==shard_idsValue){
                              this.shard[c].icon='el-icon-circle-check';
                              this.shard[c].status='success';
                            }
                          }
                        }
                      }
                    }
                  }
                  clearInterval(timer);
                  this.getList();
                }
              }
            }else if(res.attachment==null&&res.error_code=='70001'&&res.status=='failed'){
              if(info=='添加shard'||info=='添加存储节点'){
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
              }else if(info=='添加计算节点'){
                this.shard_show=false;
                this.init_show=false;
                this.computer_show=true;
                this.computer_state='process';
                this.computer_icon='el-icon-loading'
                this.computer_title='正在'+info;
                if(i>5){
                  this.computer_state='error';
                  this.computer_icon='el-icon-circle-close'
                  this.computer_title=info+'失败';
                  clearInterval(timer);
                }
              }
            }else if(res.attachment==null&&res.status=='ongoing'){
               if(info=='添加shard'||info=='添加存储节点'){
                this.shard_show=true;
                this.init_show=false;
                this.computer_show=false;
                this.storage_state='process';
                this.shard_icon='el-icon-loading'
                this.shard_title='正在'+info;
              }else if(info=='添加计算节点'){
                this.shard_show=false;
                this.init_show=false;
                this.computer_show=true;
                this.computer_state='process';
                this.computer_icon='el-icon-loading'
                this.computer_title='正在'+info;
              }
            }else if(res.attachment==null&&res.status=='done'){
              if(info=='添加shard'||info=='添加存储节点'){
                this.shard_show=true;
                this.init_show=false;
                this.computer_show=false;
                this.storage_state='success';
                this.shard_icon='el-icon-circle-check'
                this.shard_title=info+'成功';
              }else if(info=='添加计算节点'){
                this.shard_show=false;
                this.init_show=false;
                this.computer_show=true;
                this.computer_state='success';
                this.computer_icon='el-icon-circle-check'
                this.computer_title=info+'成功';
              }
              clearInterval(timer);
            }else{
              if(info=='添加shard'||info=='添加存储节点'){
                this.shard_show=true;
                this.init_show=false;
                this.computer_show=false;
                this.storage_state='error';
                this.shard_icon='el-icon-circle-close'
                this.shard_title=info+'失败';
              }else if(info=='添加计算节点'){
                this.shard_show=false;
                this.init_show=false;
                this.computer_show=true;
                this.computer_state='error';
                this.computer_icon='el-icon-circle-close'
                this.computer_title=info+'失败';
              }
              clearInterval(timer);
            }
          }else if(info=='扩容'){
            if(res.attachment!==null){
              if(res.status=='failed'){
                this.expand_end="集群扩容失败"
                this.expondInit=false;
                this.expondSatus=true;
                this.expondResult=true;
                this.expondInfo=res.attachment.memo_info;
                console.log(res.attachment.memo_info);
                clearInterval(timer);
              }else if(res.status=='done'){
                this.expand_end="集群扩容成功"
                this.expondInit=false;
                this.expondSatus=true;
                this.expondResult=true;
                this.expondInfo=res.attachment.memo_info;
                //console.log(typeof res.attachment.memo_info);
                clearInterval(timer);
              }else{
                this.expand_init="正在进行集群扩容"
                this.expondInit=true;
                this.expondSatus=false;
                this.expondResult=false;
                this.expondInfo=res.attachment.memo_info;
              }
            }else if(res.attachment==null&&res.status=='failed'){
              this.expondInit=false;
              this.expondSatus=false;
              this.expondResult=true;
              this.expand_end="集群扩容失败"
              clearInterval(timer);
            }
          }else if(info=='主备切换'){
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
                for(let k=0;k<this.stepParams.length;k++){
                  if(res.attachment.step==this.stepParams[k].title){
                    this.active=k;
                    this.stepParams[k].icon='el-icon-loading';
                    this.stepParams[k].status='process';
                    if(k>0&&k<(this.active+1)){
                      //小于i的情况
                      for(let j=0;j<k;j++){
                        this.stepParams[j].icon='el-icon-circle-check';
                        this.stepParams[j].status='success';
                      }
                    }
                  }
                }
              }else if(res.status=='done'){
                for(let k=0;k<this.stepParams.length;k++){
                  this.active=this.stepParams.length;
                  this.stepParams[k].icon='el-icon-circle-check';
                  this.stepParams[k].status='success';
                }
                clearInterval(timer);
              }else if(res.status=='failed'){
                for(let k=0;k<this.stepParams.length;k++){
                  if(this.stepParams[k].status=='process'){
                    this.active=k;
                    this.stepParams[k].icon='el-icon-circle-close';
                    this.stepParams[k].status='error';
                    this.stepParams[k].description=res.error_info;
                  }
                }
                clearInterval(timer);
              }
            }
            }else if(res.attachment==null&&res.error_code=='70001'&&res.status=='failed'){
              if(i>5){
                clearInterval(timer);
              }
            }
          }else if(info=='重做备机节点'){
            if(res.attachment!==null){
              if(this.statusList.length==0){
                let statusgoing={}
                if(res.status=='failed'){
                  statusgoing={title:info+'失败',icon:'el-icon-circle-close',status:'error',description:res.error_code,second:[]}
                  this.statusList.push(statusgoing)
                  clearInterval(timer);
                }else{
                  //let statusgoing={}
                  // let iparr=[];
                  // for(let item in res.attachment){
                  //   if(item!=='job_step'){
                  //   let key=res.attachment[item];
                  //   this.$set(key, 'title', item)
                  //   iparr.push(key);
                  //   }
                  // }
                  for(let a=0;a<iparr.length;a++){
                    const steps=(res.attachment.job_step).replace(/\s+/g, '').split(',');
                    let secondgoing={}
                    let secondlist=[];
                    for(let b=0;b<steps.length;b++){
                      secondgoing={title:steps[b],icon:'',status: 'wait',description:''}
                      secondlist.push(secondgoing)
                    }
                    if(a==0){
                      statusgoing={title:iparr[a].title,icon:'el-icon-loading',status:'process',description:'',second:secondlist}
                      this.statusList.push(statusgoing)
                      if(iparr[a].status=='done'){
                        this.init_active=a+1;
                        this.statusList[a].icon='el-icon-circle-check';
                        this.statusList[a].status='success';
                        for(let c=0;c<this.statusList[a].second.length;c++){
                          this.statusList[a].second[c].icon='el-icon-circle-check';
                          this.statusList[a].second[c].status='success';
                        }
                      }else if(iparr[a].status=='ongoing'){
                        this.init_active=a;
                        this.statusList[a].icon='el-icon-loading';
                        this.statusList[a].status='process';
                        for(let c=0;c<this.statusList[a].second.length;c++){
                          if(iparr[a].step==this.statusList[a].second[c].title){
                            this.statusList[a].second[c].icon='el-icon-loading';
                            this.statusList[a].second[c].status='process';
                            for(let j=0;j<c;j++){
                              this.statusList[a].second[j].icon='el-icon-circle-check';
                              this.statusList[a].second[j].status='success';
                            }
                          }
                        }
                      }else if(iparr[a].status=='failed'){
                        this.statusList[a].icon='el-icon-circle-close';
                        this.statusList[a].status='error';
                        for(let c=0;c<this.statusList[a].second.length;c++){
                          if(iparr[a].step==this.statusList[a].second[c].title){
                            this.statusList[a].second[c].icon='el-icon-circle-close';
                            this.statusList[a].second[c].status='error';
                            for(let j=0;j<c;j++){
                              this.statusList[a].second[j].icon='el-icon-circle-check';
                              this.statusList[a].second[j].status='success';
                            }
                          }
                        }
                        this.statusList[a].description=iparr[a].error_info;
                      }
                    }
                    else{
                      const steps=(res.attachment.job_step).replace(/\s+/g, '').split(',');
                      let secondgoing={}
                      let secondlist=[];
                      for(let b=0;b<steps.length;b++){
                        secondgoing={title:steps[b],icon:'',status: 'wait',description:''}
                        secondlist.push(secondgoing)
                      }
                      if(this.init_active==0){
                        statusgoing={title:iparr[a].title,icon:'',status:'wait',description:'',second:secondlist}
                        this.statusList.push(statusgoing)
                      }else{
                        statusgoing={title:iparr[a].title,icon:'el-icon-loading',status:'process',description:'',second:secondlist}
                        this.statusList.push(statusgoing)
                      }
                      //第a个修改里边的状态
                      if(iparr[a].status=='done'){
                        this.init_active=a+1;
                        this.statusList[a].icon='el-icon-circle-check';
                        this.statusList[a].status='success';
                        for(let c=0;c<this.statusList[a].second.length;c++){
                          this.statusList[a].second[c].icon='el-icon-circle-check';
                          this.statusList[a].second[c].status='success';
                        }
                      }else if(iparr[a].status=='ongoing'){
                        this.init_active=a;
                        this.statusList[a].icon='el-icon-loading';
                        this.statusList[a].status='process';
                        for(let c=0;c<this.statusList[a].second.length;c++){
                          if(iparr[a].step==this.statusList[a].second[c].title){
                            this.statusList[a].second[c].icon='el-icon-loading';
                            this.statusList[a].second[c].status='process';
                            for(let j=0;j<c;j++){
                              this.statusList[a].second[j].icon='el-icon-circle-check';
                              this.statusList[a].second[j].status='success';
                            }
                          }
                        }
                      }else if(iparr[a].status=='failed'){
                        this.statusList[a].icon='el-icon-circle-close';
                        this.statusList[a].status='error';
                        for(let c=0;c<this.statusList[a].second.length;c++){
                          if(iparr[a].step==this.statusList[a].second[c].title){
                            this.statusList[a].second[c].icon='el-icon-circle-close';
                            this.statusList[a].second[c].status='error';
                            for(let j=0;j<c;j++){
                              this.statusList[a].second[j].icon='el-icon-circle-check';
                              this.statusList[a].second[j].status='success';
                            }
                          }
                        }
                        this.statusList[a].description=iparr[a].error_info;
                      }
                    }
                  }
                }
              }else{
                //statusList不为0的情况
                if(res.status=='ongoing'){
                  //let statusgoing={}
                  let iparr=[];
                  for(let item in res.attachment){
                    if(item!=='job_step'){
                    let key=res.attachment[item];
                    this.$set(key, 'title', item)
                    iparr.push(key);
                    }
                  }
                  for(let a=0;a<iparr.length;a++){
                    if(a==0){
                      if(iparr[a].status=='done'){
                        this.init_active=a+1;
                        this.statusList[a].icon='el-icon-circle-check';
                        this.statusList[a].status='success';
                        for(let c=0;c<this.statusList[a].second.length;c++){
                          this.statusList[a].second[c].icon='el-icon-circle-check';
                          this.statusList[a].second[c].status='success';
                        }
                      }else if(iparr[a].status=='ongoing'){
                        this.init_active=a;
                        this.statusList[a].icon='el-icon-loading';
                        this.statusList[a].status='process';
                        for(let c=0;c<this.statusList[a].second.length;c++){
                          if(iparr[a].step==this.statusList[a].second[c].title){
                            this.statusList[a].second[c].icon='el-icon-loading';
                            this.statusList[a].second[c].status='process';
                            for(let j=0;j<c;j++){
                              this.statusList[a].second[j].icon='el-icon-circle-check';
                              this.statusList[a].second[j].status='success';
                            }
                          }
                        }
                      }else if(iparr[a].status=='failed'){
                        this.statusList[a].icon='el-icon-circle-close';
                        this.statusList[a].status='error';
                        for(let c=0;c<this.statusList[a].second.length;c++){
                          if(iparr[a].step==this.statusList[a].second[c].title){
                            this.statusList[a].second[c].icon='el-icon-circle-close';
                            this.statusList[a].second[c].status='error';
                            for(let j=0;j<c;j++){
                              this.statusList[a].second[j].icon='el-icon-circle-check';
                              this.statusList[a].second[j].status='success';
                            }
                          }
                        }
                        this.statusList[a].description=iparr[a].error_info;
                      }
                    }
                    else{
                      //第a个修改里边的状态
                      if(iparr[a].status=='done'){
                        this.init_active=a+1;
                        this.statusList[a].icon='el-icon-circle-check';
                        this.statusList[a].status='success';
                        for(let c=0;c<this.statusList[a].second.length;c++){
                          this.statusList[a].second[c].icon='el-icon-circle-check';
                          this.statusList[a].second[c].status='success';
                        }
                      }else if(iparr[a].status=='ongoing'){
                        this.init_active=a;
                        this.statusList[a].icon='el-icon-loading';
                        this.statusList[a].status='process';
                        for(let c=0;c<this.statusList[a].second.length;c++){
                          if(iparr[a].step==this.statusList[a].second[c].title){
                            this.statusList[a].second[c].icon='el-icon-loading';
                            this.statusList[a].second[c].status='process';
                            for(let j=0;j<c;j++){
                              this.statusList[a].second[j].icon='el-icon-circle-check';
                              this.statusList[a].second[j].status='success';
                            }
                          }
                        }
                      }else if(iparr[a].status=='failed'){
                        this.statusList[a].icon='el-icon-circle-close';
                        this.statusList[a].status='error';
                        for(let c=0;c<this.statusList[a].second.length;c++){
                          if(iparr[a].step==this.statusList[a].second[c].title){
                            this.statusList[a].second[c].icon='el-icon-circle-close';
                            this.statusList[a].second[c].status='error';
                            for(let j=0;j<c;j++){
                              this.statusList[a].second[j].icon='el-icon-circle-check';
                              this.statusList[a].second[j].status='success';
                            }
                          }
                        }
                        this.statusList[a].description=iparr[a].error_info;
                      }
                    }
                  }
                }else if(res.status=='failed'){
                    let iparr=[];
                  for(let item in res.attachment){
                    if(item!=='job_step'){
                    let key=res.attachment[item];
                    this.$set(key, 'title', item)
                    iparr.push(key);
                    }
                  }
                  for(let a=0;a<iparr.length;a++){
                    if(a==0){
                      if(iparr[a].status=='done'){
                        this.init_active=a+1;
                        this.statusList[a].icon='el-icon-circle-check';
                        this.statusList[a].status='success';
                        for(let c=0;c<this.statusList[a].second.length;c++){
                          this.statusList[a].second[c].icon='el-icon-circle-check';
                          this.statusList[a].second[c].status='success';
                        }
                      }else if(iparr[a].status=='ongoing'){
                        this.init_active=a;
                        this.statusList[a].icon='el-icon-loading';
                        this.statusList[a].status='process';
                        for(let c=0;c<this.statusList[a].second.length;c++){
                          if(iparr[a].step==this.statusList[a].second[c].title){
                            this.statusList[a].second[c].icon='el-icon-loading';
                            this.statusList[a].second[c].status='process';
                            for(let j=0;j<c;j++){
                              this.statusList[a].second[j].icon='el-icon-circle-check';
                              this.statusList[a].second[j].status='success';
                            }
                          }
                        }
                      }else if(iparr[a].status=='failed'){
                        this.statusList[a].icon='el-icon-circle-close';
                        this.statusList[a].status='error';
                        for(let c=0;c<this.statusList[a].second.length;c++){
                          if(iparr[a].step==this.statusList[a].second[c].title){
                            this.statusList[a].second[c].icon='el-icon-circle-close';
                            this.statusList[a].second[c].status='error';
                            for(let j=0;j<c;j++){
                              this.statusList[a].second[j].icon='el-icon-circle-check';
                              this.statusList[a].second[j].status='success';
                            }
                          }
                        }
                        this.statusList[a].description=iparr[a].error_info;
                      }
                    }
                    else{
                      //第a个修改里边的状态
                      if(iparr[a].status=='done'){
                        this.init_active=a+1;
                        this.statusList[a].icon='el-icon-circle-check';
                        this.statusList[a].status='success';
                        for(let c=0;c<this.statusList[a].second.length;c++){
                          this.statusList[a].second[c].icon='el-icon-circle-check';
                          this.statusList[a].second[c].status='success';
                        }
                      }else if(iparr[a].status=='ongoing'){
                        this.init_active=a;
                        this.statusList[a].icon='el-icon-loading';
                        this.statusList[a].status='process';
                        for(let c=0;c<this.statusList[a].second.length;c++){
                          if(iparr[a].step==this.statusList[a].second[c].title){
                            this.statusList[a].second[c].icon='el-icon-loading';
                            this.statusList[a].second[c].status='process';
                            for(let j=0;j<c;j++){
                              this.statusList[a].second[j].icon='el-icon-circle-check';
                              this.statusList[a].second[j].status='success';
                            }
                          }
                        }
                      }else if(iparr[a].status=='failed'){
                        this.statusList[a].icon='el-icon-circle-close';
                        this.statusList[a].status='error';
                        for(let c=0;c<this.statusList[a].second.length;c++){
                          if(iparr[a].step==this.statusList[a].second[c].title){
                            this.statusList[a].second[c].icon='el-icon-circle-close';
                            this.statusList[a].second[c].status='error';
                            for(let j=0;j<c;j++){
                              this.statusList[a].second[j].icon='el-icon-circle-check';
                              this.statusList[a].second[j].status='success';
                            }
                          }
                        }
                        this.statusList[a].description=iparr[a].error_info;
                      }
                    }
                  }
                  let statusgoing={title:info+'失败',icon:'el-icon-circle-close',status:'error',description:res.error_info,second:''}
                  this.statusList.push(statusgoing)
                  clearInterval(timer);
                }else if(res.status=='done'){
                    let iparr=[];
                  for(let item in res.attachment){
                    if(item!=='job_step'){
                    let key=res.attachment[item];
                    this.$set(key, 'title', item)
                    iparr.push(key);
                    }
                  }
                  for(let a=0;a<iparr.length;a++){
                    if(a==0){
                      if(iparr[a].status=='done'){
                        this.init_active=a+1;
                        this.statusList[a].icon='el-icon-circle-check';
                        this.statusList[a].status='success';
                        for(let c=0;c<this.statusList[a].second.length;c++){
                          this.statusList[a].second[c].icon='el-icon-circle-check';
                          this.statusList[a].second[c].status='success';
                        }
                      }else if(iparr[a].status=='ongoing'){
                        this.init_active=a;
                        this.statusList[a].icon='el-icon-loading';
                        this.statusList[a].status='process';
                        for(let c=0;c<this.statusList[a].second.length;c++){
                          if(iparr[a].step==this.statusList[a].second[c].title){
                            this.statusList[a].second[c].icon='el-icon-loading';
                            this.statusList[a].second[c].status='process';
                            for(let j=0;j<c;j++){
                              this.statusList[a].second[j].icon='el-icon-circle-check';
                              this.statusList[a].second[j].status='success';
                            }
                          }
                        }
                      }else if(iparr[a].status=='failed'){
                        this.statusList[a].icon='el-icon-circle-close';
                        this.statusList[a].status='error';
                        for(let c=0;c<this.statusList[a].second.length;c++){
                          if(iparr[a].step==this.statusList[a].second[c].title){
                            this.statusList[a].second[c].icon='el-icon-circle-close';
                            this.statusList[a].second[c].status='error';
                            for(let j=0;j<c;j++){
                              this.statusList[a].second[j].icon='el-icon-circle-check';
                              this.statusList[a].second[j].status='success';
                            }
                          }
                        }
                        this.statusList[a].description=iparr[a].error_info;
                      }
                    }
                    else{
                      //第a个修改里边的状态
                      if(iparr[a].status=='done'){
                        this.init_active=a+1;
                        this.statusList[a].icon='el-icon-circle-check';
                        this.statusList[a].status='success';
                        for(let c=0;c<this.statusList[a].second.length;c++){
                          this.statusList[a].second[c].icon='el-icon-circle-check';
                          this.statusList[a].second[c].status='success';
                        }
                      }else if(iparr[a].status=='ongoing'){
                        this.init_active=a;
                        this.statusList[a].icon='el-icon-loading';
                        this.statusList[a].status='process';
                        for(let c=0;c<this.statusList[a].second.length;c++){
                          if(iparr[a].step==this.statusList[a].second[c].title){
                            this.statusList[a].second[c].icon='el-icon-loading';
                            this.statusList[a].second[c].status='process';
                            for(let j=0;j<c;j++){
                              this.statusList[a].second[j].icon='el-icon-circle-check';
                              this.statusList[a].second[j].status='success';
                            }
                          }
                        }
                      }else if(iparr[a].status=='failed'){
                        this.statusList[a].icon='el-icon-circle-close';
                        this.statusList[a].status='error';
                        for(let c=0;c<this.statusList[a].second.length;c++){
                          if(iparr[a].step==this.statusList[a].second[c].title){
                            this.statusList[a].second[c].icon='el-icon-circle-close';
                            this.statusList[a].second[c].status='error';
                            for(let j=0;j<c;j++){
                              this.statusList[a].second[j].icon='el-icon-circle-check';
                              this.statusList[a].second[j].status='success';
                            }
                          }
                        }
                        this.statusList[a].description=iparr[a].error_info;
                      }
                    }
                  }
                  let statusgoing={title:info+'成功',icon:'el-icon-circle-check',status:'success',description:'',second:''}
                  this.statusList.push(statusgoing)
                  clearInterval(timer);
                }else{
                  this.message_tips = res.error_info;
                  this.message_type = 'error';
                  messageTip(this.message_tips,this.message_type);
                }
              }
            }else if(res.attachment==null&&res.error_code=='70001'&&res.status=='failed'){
              // if(i==0){
              //   this.statusList=[];
              //   let statusgoing={}
              //   statusgoing={title:'正在'+info,icon:'el-icon-loading',status:'process',description:'',second:[]}
              //   this.statusList.push(statusgoing)
              // }
              if(i>5){
                let statusgoing={title:info+'失败',icon:'el-icon-circle-close',status:'error',description:res.error_code,second:[]}
                this.statusList.push(statusgoing)
                // this.statusList.title=info+'失败';
                // this.statusList.icon='el-icon-circle-close';
                // this.statusList.status='error';
                // this.statusList.description=res.error_code;
                clearInterval(timer);
              }
            }else if(res.attachment==null&&res.status=='ongoing'){
              this.statusList=[];
              let statusgoing={}
              statusgoing={title:'正在'+info,icon:'el-icon-loading',status:'process',description:'',second:[]}
              this.statusList.push(statusgoing)
            }else if(res.attachment==null&&res.status=='done'){
              this.statusList=[];
              let statusgoing={}
              statusgoing={title:info+'成功',icon:'el-icon-circle-check',status:'success',description:'',second:[]}
              this.statusList.push(statusgoing)
              clearInterval(timer);
            }else{
              this.statusList=[];
              let statusgoing={}
              statusgoing={title:info+'失败',icon:'el-icon-circle-close',status:'error',description:res.error_code,second:[]}
              this.statusList.push(statusgoing)
              clearInterval(timer);
            }
          }else if(info=='删除存储节点'){
          //删除存储节点
          if(res.attachment!==null){
            if(info=='删除存储节点'){
              this.shard_show=true;
              this.init_show=false;
              this.computer_show=false;
              console.log(10)
            }
            //存储
            if(res.attachment.storage_state=='ongoing'){
              this.storage_state='process';
              this.shard_icon='el-icon-loading'
              this.shard_title='正在'+info;
            }else if(res.attachment.storage_state=='done'){
               console.log(4);
              this.storage_state='success';
              this.shard_icon='el-icon-circle-check'
              this.shard_title=info+'成功';
              
            }else if(res.attachment.storage_state=='failed'){
               console.log(5);
              this.storage_state='error';
              this.shard_icon='el-icon-circle-close'
              this.shard_title=info+'失败';
              
            }else{
              console.log(6);
              this.storage_state='process';
              this.shard_icon='el-icon-loading'
              this.shard_title='正在'+info;
            }
            this.init_title='正在'+info;
            this.init_active=1;
            if(this.computer.length==0||this.shard.length==0){
              if(this.computer_show==true){
                if(this.computer.length==0){
                  console.log(1);
                  let newArrgoing={}
                  if(res.attachment.hasOwnProperty('computer_hosts')){
                    const arr=res.attachment.computer_hosts.substr(0,res.attachment.computer_hosts.length-1);
                    const computer_hosts=arr.split(';');
                    for(let e=0;e<computer_hosts.length;e++){
                      if(res.attachment.computer_state=='done'){
                        newArrgoing.title=res.attachment.hasOwnProperty('computer_hosts')===true?computer_hosts[e]:'正在'+info;
                        newArrgoing.icon='el-icon-circle-check';
                        newArrgoing.status= 'success';
                        newArrgoing.description='';
                        newArrgoing.computer_id=res.attachment.computer_id;
                        this.computer.push(newArrgoing)
                      }else if(res.attachment.computer_state=='failed'){
                        newArrgoing.title=res.attachment.hasOwnProperty('computer_hosts')===true?computer_hosts[e]:'正在'+info;
                        newArrgoing.icon='el-icon-circle-close';
                        newArrgoing.status= 'error';
                        newArrgoing.description=res.error_info;
                        newArrgoing.computer_id=res.attachment.computer_id;
                        this.computer.push(newArrgoing)
                      }else{
                        newArrgoing.title=res.attachment.hasOwnProperty('computer_hosts')===true?computer_hosts[e]:'正在'+info;
                        newArrgoing.icon='el-icon-loading';
                        newArrgoing.status= 'process';
                        newArrgoing.description='';
                        newArrgoing.computer_id=res.attachment.computer_id;
                        this.computer.push(newArrgoing)
                      }
                    }
                  }
                }
              }
              if(this.shard_show==true){
                if(this.shard.length==0){
                  console.log(2);
                  if(res.attachment.hasOwnProperty('storage_state')){
                    if(res.attachment.storage_state=='done'){
                      let shard_ids='';
                      // if(info=='新增shard'){
                      //   shard_ids=res.attachment.shard_ids;
                      //   let shardgoing={}
                      //   for(let e=0;e<shard_ids.length;e++){
                      //     for(var item in shard_ids[e]){
                      //       var shard_idsValue=shard_ids[e][item];
                      //       console.log(shard_idsValue);
                      //       const shard_text=item+':'+shard_idsValue;
                      //       shardgoing.title=shard_idsValue!==''?shard_text:'正在'+info;
                      //       shardgoing.icon='el-icon-circle-check';
                      //       shardgoing.status= 'success';
                      //       shardgoing.description='';
                      //       shardgoing.shard_id=shard_idsValue;
                      //       this.shard.push(shardgoing)
                      //     }
                      //   }
                      // }else{
                        console.log(3);
                        shard_ids=res.attachment.shard_id;
                        let shardgoing={}
                        shardgoing.title=shard_ids!==''?shard_ids:'正在'+info;
                        shardgoing.icon='el-icon-circle-check';
                        shardgoing.status= 'success';
                        shardgoing.description='';
                        shardgoing.shard_id=shard_idsValue;
                        this.shard.push(shardgoing)
                      // }

                    }else if(res.attachment.storage_state=='failed'){
                      let shard_ids='';
                      // if(info=='新增shard'){
                      //   shard_ids=res.attachment.shard_ids;
                      //   let shardgoing={}
                      //   for(let e=0;e<shard_ids.length;e++){
                      //     for(var item in shard_ids[e]){
                      //       var shard_idsValue=shard_ids[e][item];
                      //       console.log(shard_idsValue);
                      //       const shard_text=item+':'+shard_idsValue;
                      //       shardgoing.title=shard_idsValue!==''?shard_text:'正在'+info;
                      //       shardgoing.icon='el-icon-circle-close';
                      //       shardgoing.status= 'error';
                      //       shardgoing.description=res.error_info;
                      //       shardgoing.shard_id=shard_idsValue;
                      //       this.shard.push(shardgoing)
                      //     }
                      //   }
                      // }else{
                        shard_ids=res.attachment.shard_id;
                        let shardgoing={}
                        shardgoing.title=shard_ids!==''?shard_ids:'正在'+info;
                        shardgoing.icon='el-icon-circle-close';
                        shardgoing.status= 'error';
                        shardgoing.description=res.error_info;
                        shardgoing.shard_id=shard_idsValue;
                        this.shard.push(shardgoing)
                      // }
                    }else{
                      let shard_ids='';
                      // if(info=='新增shard'){
                      //   shard_ids=res.attachment.shard_ids;
                      //   let shardgoing={}
                      //   for(let e=0;e<shard_ids.length;e++){
                      //     for(var item in shard_ids[e]){
                      //       var shard_idsValue=shard_ids[e][item];
                      //       console.log(shard_idsValue);
                      //       const shard_text=item+':'+shard_idsValue;
                      //       shardgoing.title=shard_idsValue!==''?shard_text:'正在'+info;
                      //       shardgoing.icon='el-icon-loading';
                      //       shardgoing.status= 'process';
                      //       shardgoing.description='';
                      //       shardgoing.shard_id=shard_idsValue;
                      //       this.shard.push(shardgoing)
                      //     }
                      //   }
                      // }else{
                        shard_ids=res.attachment.shard_id;
                        let shardgoing={}
                        shardgoing.title=shard_ids!==''?shard_ids:'正在'+info;
                        shardgoing.icon='el-icon-loading';
                        shardgoing.status= 'process';
                        shardgoing.description='';
                        shardgoing.shard_id=shard_ids;
                        this.shard.push(shardgoing)
                      // }
                    }
                  }
                }
              }
              if(res.status=='failed'){
                console.log(7);
                this.storage_state='error';
                this.shard_icon='el-icon-circle-close'
                this.shard_title=info+'失败';
                this.init_show=false;


                this.finish_title=info+'失败'
                this.finish_icon='el-icon-circle-close'
                this.init_active=1
                this.finish_description=res.error_info;
                //遍历计算节点改状态
                // if(this.computer.length>0){
                //   for(let c=0;c<this.computer.length;c++){
                //     const arr=res.attachment.computer_hosts.substr(0,res.attachment.computer_hosts.length-1);
                //     const computer_hosts=arr.split(';');
                //     for(let e=0;e<computer_hosts.length;e++){
                //       if(this.computer[c].title==computer_hosts[e]){
                //         this.computer[c].icon='el-icon-circle-close';
                //         this.computer[c].status='error';
                //       }
                //     }
                //   }
                // this.computer_description=res.error_info;
                // }
                //遍历存储节点改状态
                if(this.shard.length>0){
                  for(let c=0;c<this.shard.length;c++){
                    let shard_ids='';
                    // if(info=='新增shard'){
                    //   shard_ids=res.attachment.shard_ids;
                    //   for(let e=0;e<shard_ids.length;e++){
                    //     for(var item in shard_ids[e]){
                    //       var shard_idsValue=shard_ids[e][item];
                    //       if(this.shard[c].shard_id==shard_idsValue){
                    //         this.shard[c].icon='el-icon-circle-close';
                    //         this.shard[c].status='error';
                    //       }
                    //     }
                    //   }
                    // }else{
                      shard_ids=res.attachment.shard_id;
                      if(this.shard[c].shard_id==shard_ids){
                        this.shard[c].icon='el-icon-circle-close';
                        this.shard[c].status='error';
                      }
                    // }
                  }
                  this.shard_description=res.error_info;
                }
                clearInterval(timer);
              }else if(res.status=='done'){
                console.log(8);
                this.storage_state='success';
                this.shard_icon='el-icon-circle-check'
                this.shard_title=info+'成功';

                // this.computer_state='success';
                // this.computer_icon='el-icon-circle-check'
                // this.computer_title=info+'成功';

                this.finish_title=info+'成功'
                this.finish_icon='el-icon-circle-check'
                this.init_active=1
                this.finish_description=res.error_info;
                //遍历计算节点改状态
                // if(this.computer.length>0){
                //   for(let c=0;c<this.computer.length;c++){
                //     const arr=res.attachment.computer_hosts.substr(0,res.attachment.computer_hosts.length-1);
                //     const computer_hosts=arr.split(';');
                //     for(let e=0;e<computer_hosts.length;e++){
                //       if(this.computer[c].title==computer_hosts[e]){
                //         this.computer[c].icon='el-icon-circle-check';
                //         this.computer[c].status='success';
                //       }
                //     }
                //   }
                // }
                //遍历存储节点改状态
                if(this.shard.length>0){
                  console.log(9);
                  for(let c=0;c<this.shard.length;c++){
                    let shard_ids='';
                    // if(info=='新增shard'){
                    //   shard_ids=res.attachment.shard_ids;
                    //   for(let e=0;e<shard_ids.length;e++){
                    //     for(var item in shard_ids[e]){
                    //       var shard_idsValue=shard_ids[e][item];
                    //       if(this.shard[c].shard_id==shard_idsValue){
                    //         this.shard[c].icon='el-icon-circle-check';
                    //         this.shard[c].status='success';
                    //       }
                    //     }
                    //   }
                    // }else{
                      shard_ids=res.attachment.shard_id;
                      if(this.shard[c].shard_id==shard_ids){
                        this.shard[c].icon='el-icon-circle-check';
                        this.shard[c].status='success';
                      }
                    // }
                  }
                }
                clearInterval(timer);
                this.getCluster();
              }
            }
          }else if(res.attachment==null&&res.error_code=='70001'&&res.status=='failed'){
            if(info=='删除存储节点'){
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
          }else if(res.attachment==null&&res.status=='ongoing'){
            if(info=='删除存储节点'){
              this.shard_show=true;
              this.init_show=false;
              this.computer_show=false;
              this.storage_state='process';
              this.shard_icon='el-icon-loading'
              this.shard_title='正在'+info;
            }
          }else if(res.attachment==null&&res.status=='done'){
            if(info=='删除存储节点'){
              this.shard_show=true;
              this.init_show=false;
              this.computer_show=false;
              this.storage_state='success';
              this.shard_icon='el-icon-circle-check'
              this.shard_title=info+'成功';
            }
            clearInterval(timer);
          }else{
            if(info=='删除存储节点'){
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
    getStatus (timer,data,i) {
      setTimeout(()=>{
        const postarr={};
        postarr.job_type='get_status';
        postarr.version=version_arr[0].ver;
        postarr.job_id=data;
        postarr.timestamp=timestamp_arr[0].time+'';
        postarr.paras={};
        getEvStatus(postarr).then((res) => {
          let error_info='';
          if(res.attachment!==null){
            if(res.attachment.memo_info.error_info!==''){
              error_info=res.attachment.memo_info.error_info
            }else{
              error_info=res.error_info
            }
          }
        if(res.status=='done'||res.status=='failed'){
          clearInterval(timer);
          //this.info=res.error_info;
          if(res.status=='done'){
            // if(error_info){
            //   const newArrdone={
            //     content:error_info,
            //     timestamp: getNowDate(),
            //     color: '#0bbd87',
            //     icon: 'el-icon-circle-check'
            //   };
            //   this.activities.push(newArrdone)
            // }else{
              const newArrdone={
                content:'集群回档成功',
                timestamp: getNowDate(),
                color: '#0bbd87',
                icon: 'el-icon-circle-check'
              };
              this.activities.push(newArrdone)
            // }
            this.getList();
            //this.dialogStatusVisible=false;
          }else{
            const newArr={
              content:error_info,
              timestamp: getNowDate(),
              color: 'red',
              icon: 'el-icon-circle-close'
            };
            this.activities.push(newArr);
            //this.installStatus = true;
          }
        }else{
          if(error_info){
            const newArrgoing={
              content:error_info,
              timestamp: getNowDate(),
              color: '#0bbd87'
            };
            this.activities.push(newArrgoing)
          }
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
  box-shadow: inset 0 0 6px rgba(0,0,0,.3);
  background-color: rgba(0,0,0,0.1);
}

.el-step.is-vertical .el-step__title{
  width: 80%;
  display: table-cell !important;
}
.el-step__description.is-success{
  display: table-cell !important;
}


/* 
.el-step__description.is-finish span{
  color: red !important;
}  */



 /* .stepComponent{
      background-color:#DFEBFF;
      width: 100%-20px;
      padding: 10px 10px 10px 10px;
      margin: 10px 10px 10px 10px;
  }
  .stepsTitle{
      margin: 10px 0px  10px  10px ;
  }
  .approvalProcess{
      color: #9EADC4;
      font-size: 14px;
      
      background:#DFEBFF;
      margin-left:20px;
      margin-right:0px;
      margin-top:10px;
  }
  .processing_content{
    background-color: #D9E5F9;
  }
  .processing_content_detail{
     margin-left: 10px;
     margin-top: 3.5px;
     margin-bottom: 3.5px;
       width:150px;
     display:inline-block;
  }
  .step-row{
     min-width:500px;
     margin-bottom:12px;
     margin-top:12px;
  } */
  

</style>
<style lang="less" scoped>


//  .hoverSteps{
//     /deep/ .ai-step__description{
//       padding-right:0 !important;
//     }
//   }
//   .stepNoBtn{
//     padding:12px 12px;
//     box-sizing: border-box;
//     margin-bottom:10px;
//     .step-title-font{
//       font-size:14px; font-weight: bold;;
//     }
//   }
//   .stepBtn{
//     box-sizing: border-box;
//     background: #fff;
//     /*width: 90%;*/
//     border-radius: 4px;
//     border: 1px solid #ebeef5;
//     line-height: 1.4;
//     box-shadow: 0 2px 12px 0 rgba(0,0,0,.1);
//     word-break: break-all;
//     .btnPosition{
//       text-align: right;
//     }
//   }
</style>