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
        <div class="red_clolor attion_fr"><p>设置延迟告警时间后，rcr关系的延迟时间超过最大延迟告警时间会进行延迟告警通知，不设置不告警</p></div>
      </div>
      <div class="table-list-wrap"></div>
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
        label="同步信息">
        <template slot-scope="scope">
          <el-button type="text" @click="shardDetail(scope.row)">详情</el-button>
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
        label="shard延迟时间(主)">
      </el-table-column>
      <el-table-column
        prop="conf_delay_sync"
        align="center"
        label="延迟复制时间">
      </el-table-column>
      <el-table-column
        label="操作"
        align="center"
        width="250"
        class-name="small-padding fixed-width">
        <template slot-scope="{row,$index}">
          <el-button type="primary" size="mini" @click="handleDalay(row)" style="margin-left: 10px; margin-bottom: 2px">设置延迟复制时间</el-button>
          <el-button type="primary" size="mini" @click="handleDalayAlarm(row)" style="margin-left: 10px; margin-bottom: 2px">设置延迟告警时间</el-button>
          <el-button type="primary" size="mini" v-show="row.status=='running'||row.status=='manual_stop'" @click="handleAtion(row)" style="margin-bottom: 2px">
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
        <el-form-item label="shard延迟时间(主):" prop="sync_host_delay" class="right_input">
          <el-input  v-model="temp.sync_host_delay" placeholder="shard延迟时间(主)">
            <i slot="suffix" class="input_width">s</i>
          </el-input>
        </el-form-item>
        <el-form-item label="延迟复制时间:" prop="conf_delay_sync" class="right_input" >
          <el-input  v-model="temp.conf_delay_sync" placeholder="延迟复制时间">
            <i slot="suffix" class="input_width">s</i>
          </el-input>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="dialogFormVisible = false" v-show="!dialogDetail">关闭</el-button>
        <el-button type="primary" @click="createData()"  v-show="!dialogDetail"  :loading="add_loading">确认</el-button>
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
      <el-form-item label="最大延迟复制时间:" prop="maxtime" >
        <el-input  v-model="setform.maxtime" placeholder="请输入最大同步复制时间">
        <i slot="suffix" style="font-style:normal;margin-right: 10px; line-height: 30px;">s</i>
        </el-input>
      </el-form-item>
      <!-- <span class="line"><span v-html="'&nbsp;&nbsp;'"></span><span></span><span v-html="'&nbsp;&nbsp;'"></span></span>秒 -->
      <p class="red_clolor">注意：设置完成后，主上写完数据后，相隔该时间后才同步数据到备上</p>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="dialogSetDalayVisible = false" >关闭</el-button>
        <el-button type="primary" @click="setDalayData(setform)">确认</el-button>
      </div>
    </el-dialog>
    <!-- 设置延迟告警时间 -->
    <el-dialog :title="textMap[dialogStatus]" :visible.sync="dialogSetDalayAlarmVisible" custom-class="single_dal_view">
      <el-form
        ref="setAlarmForm"
        :rules="rules"
        :model="setAlarmform"
        label-position="left"
        label-width="140px"
      >
      <el-form-item label="延迟复制时间:" prop="conf_delay_sync" >
       <span>{{setAlarmform.conf_delay_sync}}</span>
      </el-form-item>
     <el-form-item label="最大延迟告警时间:" prop="maxalarmtime" >
        <el-input  v-model="setAlarmform.maxalarmtime" placeholder="请输入最大延迟告警时间">
        <i slot="suffix" style="font-style:normal;margin-right: 10px; line-height: 30px;">s</i>
        </el-input>
      </el-form-item>
       <p class="red_clolor">注意：最大延迟告警时间应大于同步复制时间</p>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="dialogSetDalayAlarmVisible = false" >关闭</el-button>
        <el-button type="primary" @click="setDalayAlarmData(setAlarmform)">确认</el-button>
      </div>
    </el-dialog>
    <!-- shard同步信息 -->
    <el-dialog :title="textMap[dialogStatus]" :visible.sync="dialogShardVisible" custom-class="single_dal_view"
      :close-on-click-modal="false">
      <el-form  label-position="left">
        <div class="bold-text"><p>shard同步信息：</p>
          <el-table :key="tableKey" v-loading="listShardLoading" :data="shardtemp" border highlight-current-row
        style="width: 100%; margin-bottom: 20px">
            <el-table-column type="index" align="center" label="序号" width="50" />
            <el-table-column  prop="master_master_host" label="主元数据主节点" align="center" ></el-table-column>
            <el-table-column prop="master_shard_id" label=" 主shard_id" align="center" />
            <el-table-column  prop="master_sync_host" label="同步元数据主节点" align="center"></el-table-column>
            <el-table-column  prop="slave_shard_id"  label="备shard_id" align="center"></el-table-column>
          </el-table>
        </div>
        <div class="bold-text"><p>元数据同步信息：</p>
          <el-table :key="tableKey" v-loading="listMetaLoading" :data="metatemp" border highlight-current-row
        style="width: 100%; margin-bottom: 20px">
            <el-table-column type="index" align="center" label="序号" width="50" />
            <el-table-column  prop="dump_host" label="dump节点" align="center"  width="160" ></el-table-column>
            <el-table-column prop="meta_sync_state" label="状态" align="center" >
              <template slot-scope="scope">
                <span v-if="scope.row.meta_sync_state === 'dump'" style="color: #00ed37">全量同步中</span>
                <span v-else-if="scope.row.meta_sync_state === 'creating'" style="color: #00ed37">创建中</span>
                <span v-else-if="scope.row.meta_sync_state === 'sync'" style="color: #00ed37">增量同步中</span>
                <span v-else-if="scope.row.meta_sync_state === 'disconnect'" style="color: red">断开连接</span>
                <span v-else></span>
              </template>
            </el-table-column>
            <el-table-column prop="sync_binlog_file" label="同步binlog文件" align="center"></el-table-column>
            <el-table-column prop="sync_binlog_pos"  label="同步binlog位置" align="center"></el-table-column>
            <el-table-column prop="sync_gtid"  label="同步gtid" align="center">
              <template slot-scope="scope">
                <el-button class="filter-item" type="text" icon="el-icon-tickets"  @click="handleGtid(scope.row.sync_gtid)"></el-button>
              </template>
            </el-table-column>
            <el-table-column  prop="update_ts"  label="更新时间" align="center"></el-table-column>
          </el-table>
        </div>
      </el-form>
    </el-dialog>
    <!-- 同步gtid信息 -->
    <el-dialog title="" :visible.sync="dialogGtidVisible" custom-class="single_dal_view">
       <el-form
        ref="dataForm"
        :model="gtidtemp"
        label-position="left"
        label-width="140px"
      >
        <div style="white-space: pre-wrap;">{{ gtidtemp.sync_gtid| salveMetaWrap(gtidtemp.sync_gtid)}}</div>
      </el-form>
    </el-dialog>
     <!--状态框 -->
    <!-- <el-dialog :title="job_id" :visible.sync="dialogStatusVisible" custom-class="single_dal_view" :close-on-click-modal="false" :before-close="beforeDestory">
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
    </el-dialog> -->
    <!-- 元数据管理 -->
    <el-drawer :visible.sync="drawer" direction="rtl" size="50%" :destroy-on-close="true">
      <Mate />
    </el-drawer>
    <!-- rcr状态框 -->
    <el-dialog
      :title="job_id"
      :visible.sync="dialogStatusVisible"
      custom-class="single_dal_view"
      :close-on-click-modal="false"
      :before-close="beforeRedoDestory"
    >
      <div style="width: 100%;background: #fff;padding:0 20px;" class="block">
        <el-steps direction="vertical" :active="init_active">
          <el-step

            v-for="(items, key) in statusList"
            :key="key"
            :title="items.title"
            :icon="items.icon"
            :status="items.status"
            :description="items.description"
          >
            <template slot="description">
              <div style="padding:20px;max-height: 500px;overflow-y: auto;">
                <el-steps direction="vertical" :active="second_active">
                  <el-step
                    v-for="(item,index) of statusList[key].second"
                    :key="index"
                    :title="item.title"
                    :icon="item.icon"
                    :status="item.status"
                    :description="item.description"
                  >
                    <template slot="description" >
                      <el-collapse v-model="activeName" >
                      <el-collapse-item  :name="index">
                      <div style="padding:20px;" >
                        <el-steps direction="vertical" :active="three_active" >
                          <el-step
                            v-for="(three,thindex) of statusList[key].second[index].three"
                            :key="thindex"
                            :title="three.title"
                            :icon="three.icon"
                            :status="three.status"
                            :description="three.description" 
                          >
                            <template slot="description">
                              <div style="padding:20px;">
                                <el-steps direction="vertical" :active="four_active">
                                  <el-step
                                    v-for="(four,foindex) of statusList[key].second[index].three[thindex].four"
                                    :key="foindex"
                                    :title="four.title"
                                    :icon="four.icon"
                                    :status="four.status"
                                    :description="four.description"
                                    
                                  >
                                  <template slot="description">
                                    <div style="padding:20px;">
                                      <el-steps direction="vertical" :active="five_active">
                                        <el-step
                                          v-for="(five,fiindex) of statusList[key].second[index].three[thindex].four[foindex].five"
                                          :key="fiindex"
                                          :title="five.title"
                                          :icon="five.icon"
                                          :status="five.status"
                                          :description="five.description"
                                        />
                                      </el-steps>
                                    </div>
                                  </template>
                                  </el-step>
                                </el-steps>
                              </div>
                            </template>
                          </el-step>
                        </el-steps>
                      </div>
                      </el-collapse-item>
                      </el-collapse>
                    </template>
                  </el-step>
                </el-steps>
              </div>
            </template>
          </el-step>
        </el-steps>
      </div>
    </el-dialog>
    <!-- <Info /> -->
  </div>
</template>
<script>
 import { messageTip,handleCofirm,getNowDate,gotoCofirm,createCode } from "@/utils";
 import { getRCRList,getMetaMachine,getStandbyMeta,addRCR,delMachine,update,createMetaTable,actionRCR,setRCRMaxDalay,findMetaDB,findMetaCluster,delRCRMaxDalayInfo,getMetaSyncList} from '@/api/rcr/list'
 import { getEvStatus } from '@/api/cluster/list'
 import {version_arr,timestamp_arr,machine_type_arr,node_stats_arr} from "@/utils/global_variable"
 import Pagination from '@/components/Pagination' 
 import Mate from "../rcr/matelist.vue";
 import Info from "../cluster/info.vue";
export default {
  name: "account",
  components: { Pagination,Mate ,Info},
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
          callback(new Error("最大延迟复制时间不能为空"));
      }else if(!(/^[0-9]+$/.test(value))){
          callback(new Error("最大延迟复制时间只能输入数字"));
      }
      else {
          callback();
      }
    };
     const validateMaxAlarmtime = (rule, value, callback) => {
      if(!value){
          callback(new Error("最大延迟告警时间不能为空"));
      }else if(!(/^[0-9]+$/.test(value))){
          callback(new Error("最大延迟告警时间只能输入数字"));
      }else if(value<=this.setAlarmform.conf_delay_sync){
          callback(new Error("最大延迟告警时间应大于延迟复制时间"));
      }
      else {
          callback();
      }
    };
    const validSyncHostDelay = (rule, value, callback) => {
      if(!value){
          callback(new Error("shard延迟时间(主)不能为空"));
      }else if(!(/^[0-9]+$/.test(value))){
          callback(new Error("shard延迟时间(主)只能输入数字"));
      }
      else {
          callback();
      }
    };
    const validConfDelaySync = (rule, value, callback) => {
      if(!value){
          callback(new Error("延迟复制时间不能为空"));
      }else if(!(/^[0-9]+$/.test(value))){
          callback(new Error("延迟复制只能输入数字"));
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
        slave_cluster_id:'',
        sync_host_delay:'1800',
        conf_delay_sync:'0'
      },
      dialogFormVisible: false,
      dialogShardVisible:false,
      dialogStatus: "",
      textMap: {
        create: "新增RCR",
        set_dalay: "设置延迟复制时间",
        set_dalay_alarm: "设置延迟告警时间",
        detail: "详情",
        shardDetail:'同步信息'
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
      dialogSetDalayAlarmVisible:false,
      setform:{
        maxtime:'',
        rcr_id:'',
        master_rcr_meta:'',
        master_cluster_id:'',
        slave_cluster_id:''
      },
      listShardLoading: true,
      totalShard: 0,
      shardtemp:null,
      listMetaLoading: true,
      searchMetaLoading:false,
      totalMeta: 0,
      metatemp:null,
      dialogGtidVisible:false,
      gtidtemp:{
        sync_gtid:''
      },
      setAlarmform:{
        maxalarmtime:'',
        rcr_id:'',
        master_rcr_meta:'',
        master_cluster_id:'',
        slave_cluster_id:'',
        conf_delay_sync:''
      },
      dialogStatusRedoVisible: false,
      active: 0,
      job_id: '',
      timer: null,
      add_loading:false,
      activeName:'1',
      statusList:[],
      init_active: 0,
      second_active: 0,
      three_active:0,
      four_active:0,
      five_active:0,
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
        maxalarmtime: [
          { required: true, trigger: "blur",validator: validateMaxAlarmtime },
        ],
        sync_host_delay: [
          { required: true, trigger: "blur",validator: validSyncHostDelay },
        ],
        conf_delay_sync: [
          { required: true, trigger: "blur",validator: validConfDelaySync },
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
    handleChange() {
      if (this.init_active= 3) {
        this.init_active = 0;
      } else {
        this.init_active++;
      }
    },
    beforeRedoDestory() {
      clearInterval(this.timer)
      this.dialogStatusVisible = false
      this.timer = null
    },
     handleGtid(info) {
        //this.$message(info);
        console.log(info);
        this.gtidtemp.sync_gtid=info;
        this.dialogGtidVisible=true;
      },
    shardDetail(row){
      //let info=[{"master_master_host":"192.168.0.136:28004","master_shard_id":1,"master_sync_host":"192.168.0.125:28004","slave_shard_id":6},{"master_master_host":"192.168.0.125:28006","master_shard_id":2,"master_sync_host":"192.168.0.128:28006","slave_shard_id":7}];
      this.dialogStatus = 'shardDetail'
      this.shardtemp=JSON.parse(row.shard_maps);
      //this.shardtemp=info;
      //获取meta_sync信息
      let temp={rcr_id:row.id}
      getMetaSyncList(temp).then(response => {
        this.metatemp = response.list;
        this.metatotal = response.total;
        setTimeout(() => {
          this.listMetaLoading=false;
        }, 0.5 * 1000)
      });
      this.dialogShardVisible=true;
      this.listShardLoading=false;
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
          //选择的主元数据不是当前元数据时，备元数据只能是当前元数据
          // let arr=this.slave_rcr_metas;
          // let arr1 = arr.filter(item => {
          //   if(item.value==response.ips){
          //     return  item;
          //   }
          // })
          // this.slave_rcr_metas=arr1;
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
          paras.rcr_id=row.id;
          temp.paras = paras;
          
          actionRCR(temp).then((response)=>{
            let res = response;
            //异步接口
            if(res.status=='accept'){
              this.dialogSetDalayVisible = false;
              this.job_id='设置延迟复制时间(任务ID:'+res.job_id+')';
              this.dialogStatusVisible=true;
              // this.activities=[];
              // const newArr={
              //   title:'正在设置延迟复制时间',
              //   status: 'process',
              //   icon: 'el-icon-loading',
              //   description:''
              // };
              // this.activities.push(newArr)
              let info='设置延迟复制时间';
              let i=0;
              this.statusList = []
              this.timer = null
              this.init_active = 0
              this.second_active = 0
              this.three_active = 0
              this.four_active = 0
              this.five_active = 0
              //this.getStatus(this.timer,res.job_id,i++,info,'')
              this.timer = setInterval(() => {
                this.getStatus(this.timer,res.job_id,i++,info,'')
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
     setDalayAlarmData(row) {
      this.$refs["setAlarmForm"].validate((valid) => {
        if (valid) {
          let tempData={};
          tempData.rcr_id = this.setAlarmform.rcr_id;
          tempData.maxtime =this.setAlarmform.maxalarmtime;
          tempData.user_name = sessionStorage.getItem('login_username');
          setRCRMaxDalay(tempData).then((response)=>{
            let res = response;
             if (res.code === 200) {
              //this.getList()
              this.dialogSetDalayAlarmVisible = false
              this.message_tips = '设置成功'
              this.message_type = 'success'
            } else {
              this.message_tips = res.message
              this.message_type = 'error'
            }
            messageTip(this.message_tips,this.message_type);
          });
        }
      });
    },
    handleDalay(row) {
      const code = createCode()
      const string = '设置延迟复制时间，主上写完数据后，相隔该时间之后才同步数据到备上, 是否继续?code=' + code
      gotoCofirm(string).then((res) => {
        // 先执行删权限
        if (!res.value) {
          this.message_tips = 'code不能为空！'
          this.message_type = 'error'
          messageTip(this.message_tips, this.message_type)
        } else if (res.value == code) {
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
        } else {
          this.message_tips = 'code输入有误'
          this.message_type = 'error'
          messageTip(this.message_tips, this.message_type)
        } 
      }).catch(() => {
        console.log('quxiao')
        messageTip('已取消设置','info');
      }); 
    },
    handleDalayAlarm(row) {
      const code = createCode()
      const string = '将设置延迟告警时间, 是否继续?code=' + code
      gotoCofirm(string).then((res) => {
        // 先执行删权限
        if (!res.value) {
          this.message_tips = 'code不能为空！'
          this.message_type = 'error'
          messageTip(this.message_tips, this.message_type)
        } else if (res.value == code) {
          this.resetTemp();
          this.dialogStatus = "set_dalay_alarm";
          this.dialogSetDalayAlarmVisible = true;
          this.dialogDetail = false;
          this.setAlarmform.rcr_id=row.id;
          this.setAlarmform.master_cluster_id=row.master_cluster_id;
          this.setAlarmform.master_rcr_meta=row.master_rcr_meta;
          this.setAlarmform.slave_cluster_id=row.slave_cluster_id;
          this.setAlarmform.conf_delay_sync=row.conf_delay_sync;
          this.$nextTick(() => {
            this.$refs.setAlarmForm.clearValidate();
          });
        } else {
          this.message_tips = 'code输入有误'
          this.message_type = 'error'
          messageTip(this.message_tips, this.message_type)
        } 
      }).catch(() => {
        console.log('quxiao')
        messageTip('已取消设置','info');
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
      // let i=0;
      // let info='新增RCR';
      // this.statusList = []
      // this.timer = null
      // this.init_active = 0
      // this.second_active = 0
      // this.three_active = 0
      // this.dialogFormVisible = false;
      // this.dialogStatusVisible=true;
      // let res1={"attachment":{"build_rcrs":[{"job_steps":"rebuild_shard_data,build_rcr_relation,done","shard_id":"5","step":"rebuild_shard_data","sub_id":"357","sub_jobs":[{"192.168.0.125_28004":{"error_code":"0","error_info":"OK","job_steps":"check_param, xtracback_data, checksum_data, backup_old_data, clear_old_data, recover_data, rebuild_sync, done","status":"ongoing","step":"recover_data"}}]}],"job_steps":"check_params,check_shard_in_sw,build_rcrs,update_meta_record,done","master_cluster_id":"3","master_master_hosts":"192.168.0.136:28008,","master_rcr_meta":"192.168.0.136:28002,192.168.0.128:28002,192.168.0.125:28002","master_shard_ids":"3,","master_sync_hosts":"192.168.0.125:28008,","slave_cluster_id":"5","slave_cluster_name":"cluster_1686202130_000005","slave_master_hosts":"192.168.0.125:28004,","slave_rcr_meta":"192.168.0.136:28002,192.168.0.128:28002,192.168.0.125:28002","slave_shard_ids":"5,","step":"build_rcrs"},"error_code":"0","error_info":"OK","job_id":"","job_type":"","status":"ongoing","version":"1.0"}
      
      // this.getStatus(this.timer,'105',i++,info,res1)
      // this.timer = setInterval(() => {
        // let res={"attachment":{"build_rcrs":[{"job_steps":"rebuild_shard_data,build_rcr_relation,done","shard_id":"5","step":"rebuild_shard_data","sub_id":"357","sub_jobs":[{"192.168.0.125_28004":{"error_code":0,"error_info":"OK","job_steps":"check_param, xtracback_data, checksum_data, backup_old_data, clear_old_data, recover_data, rebuild_sync, done","status":"done","step":"done"}},{"192.168.0.128_28004":{"error_code":"0","error_info":"OK","job_steps":"check_param, xtracback_data, checksum_data, backup_old_data, clear_old_data, recover_data, rebuild_sync, done","status":"ongoing","step":"recover_data"}},{"192.168.0.136_28004":{"error_code":"0","error_info":"OK","job_steps":"check_param, xtracback_data, checksum_data, backup_old_data, clear_old_data, recover_data, rebuild_sync, done","status":"done","step":"done"}}]},{"job_steps":"rebuild_shard_data,build_rcr_relation,done","shard_id":"5","step":"rebuild_shard_data","sub_id":"357","sub_jobs":[{"192.168.0.125_28004":{"error_code":0,"error_info":"OK","job_steps":"check_param, xtracback_data, checksum_data, backup_old_data, clear_old_data, recover_data, rebuild_sync, done","status":"done","step":"done"}},{"192.168.0.128_28004":{"error_code":"0","error_info":"OK","job_steps":"check_param, xtracback_data, checksum_data, backup_old_data, clear_old_data, recover_data, rebuild_sync, done","status":"ongoing","step":"recover_data"}},{"192.168.0.136_28004":{"error_code":"0","error_info":"OK","job_steps":"check_param, xtracback_data, checksum_data, backup_old_data, clear_old_data, recover_data, rebuild_sync, done","status":"done","step":"done"}}]}],"job_steps":"check_params,check_shard_in_sw,build_rcrs,update_meta_record,done","master_cluster_id":"3","master_master_hosts":"192.168.0.136:28008,","master_rcr_meta":"192.168.0.136:28002,192.168.0.128:28002,192.168.0.125:28002","master_shard_ids":"3,","master_sync_hosts":"192.168.0.125:28008,","slave_cluster_id":"5","slave_cluster_name":"cluster_1686202130_000005","slave_master_hosts":"192.168.0.125:28004,","slave_rcr_meta":"192.168.0.136:28002,192.168.0.128:28002,192.168.0.125:28002","slave_shard_ids":"5,","step":"build_rcrs"},"error_code":"0","error_info":"OK","job_id":"","job_type":"","status":"ongoing","version":"1.0"}
        // this.getStatus(this.timer,res.job_id,i++,info,res)
    //  }, 10000)

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
       sync_host_delay:'1800',
       conf_delay_sync:'0'
      };
    },
    handleCreate() {
      this.resetTemp();
      this.dialogStatus = "create";
      this.master_rcr_metas=[];
      this.slave_rcr_metas=[];
      const tempData = {};
      tempData.user_name = sessionStorage.getItem('login_username');
      getMetaMachine(tempData).then((res) => {
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
              let arr = { 'value':res.list[i].rcr_meta, 'label': res.list[i].name }
              this.master_rcr_metas.push(arr)
              //先查当前元数据(备元数据只能选择当前元数据)
              findMetaDB().then((response) => {
                arr=this.master_rcr_metas;
                let arr1 = arr.filter(item => {
                  if(item.value==response.ips){
                    return  item;
                  }
                })
                this.slave_rcr_metas=arr1;
              });
            }
            //this.slave_rcr_metas=this.master_rcr_metas;
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
          this.add_loading=true;
          const tempData = {};
          tempData.user_name = sessionStorage.getItem('login_username');
          tempData.job_id ='';
          tempData.job_type ='create_rcr';
          tempData.version=version_arr[0].ver;
          tempData.timestamp=timestamp_arr[0].time+'';
          const paras={};
          paras.cluster_id=this.temp.slave_cluster_id;
          paras.master_info={'meta_db':this.temp.master_rcr_meta,'cluster_id':this.temp.master_cluster_id}
          paras.delay_sync=this.temp.conf_delay_sync;
          paras.sync_host_delay=this.temp.sync_host_delay;
          paras.slave_rcr_meta=this.temp.slave_rcr_meta;
          tempData.paras = paras;
          let postjson={postData:tempData,slave:this.temp.slave_rcr_meta}
          // console.log(tempData);return;
          
          addRCR(postjson).then(response=>{
            let res = response;
            if(res.status=='accept'){
              this.dialogFormVisible = false;
              this.job_id='新增RCR(任务ID:'+res.job_id+')';
              this.dialogStatusVisible=true;
              let i=0;
              let info='新增RCR';
              this.statusList = []
              this.timer = null
              this.init_active = 0
              this.second_active = 0
              this.three_active = 0
              this.four_active = 0
              this.five_active = 0
              //this.getStatus(this.timer,'105',i++,info,'')
              this.timer = setInterval(() => {
                this.getStatus(this.timer,res.job_id,i++,info,'')
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
      const code = createCode()
      const string = '此操作将永久删除该数据, 是否继续?code=' + code
      gotoCofirm(string).then((res) => {
        // 先执行删权限
        if (!res.value) {
          this.message_tips = 'code不能为空！'
          this.message_type = 'error'
          messageTip(this.message_tips, this.message_type)
        } else if (res.value == code) {
          // handleCofirm("此操作将永久删除该数据, 是否继续?").then( () =>{
          const tempData = {};
          tempData.user_name = sessionStorage.getItem('login_username');
          tempData.job_id ='';
          tempData.job_type ='delete_rcr';
          tempData.version=version_arr[0].ver;
          tempData.timestamp=timestamp_arr[0].time+'';
          const paras={}
          paras.cluster_id=row.slave_cluster_id;
          paras.master_info={'meta_db':row.master_rcr_meta,'cluster_id':row.master_cluster_id}
          paras.rcr_id=row.id;
          tempData.paras = paras;
          //console.log(tempData);return
         
          delMachine(tempData).then((response)=>{
            let res = response;
            if(res.status=='accept'){
              this.dialogFormVisible = false;
              this.job_id='删除RCR(任务ID:'+res.job_id+')';
              this.dialogStatusVisible=true;
          
              let info='删除RCR';
              let i=0;
              this.statusList = []
              this.timer = null
              this.init_active = 0
              this.second_active = 0
              this.three_active = 0
              this.four_active = 0
              this.five_active = 0
              this.timer = setInterval(() => {
                this.getStatus(this.timer,res.job_id,i++,info,row.id)
              }, 1000)   
            }else{
              this.message_tips = res.error_info;
              this.message_type = 'error';
              messageTip(this.message_tips,this.message_type);
            }
          })
        } else {
          this.message_tips = 'code输入有误'
          this.message_type = 'error'
          messageTip(this.message_tips, this.message_type)
        } 
      }).catch(() => {
          console.log('quxiao')
          messageTip('已取消删除','info');
      });
    },
    handleUpdate(row) {
      const code = createCode()
      const string = '此操作将进行该RCR手动切换, 是否继续?code=' + code
      gotoCofirm(string).then((res) => {
        // 先执行删权限
        if (!res.value) {
          this.message_tips = 'code不能为空！'
          this.message_type = 'error'
          messageTip(this.message_tips, this.message_type)
        } else if (res.value == code) {
        // handleCofirm("即将进行该RCR手动切换, 是否继续?").then( () =>{
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
          // paras.rcr_id=row.id;
          tempData.paras=paras;
          
          update(tempData).then((response) => {
            let res = response;
            if(res.status=='accept'){
              this.dialogFormVisible = false;
              this.job_id='手动切换RCR(任务ID:'+res.job_id+')';
              this.dialogStatusVisible=true;
              // this.activities=[];
              // const newArr={
              //   title:'正在手动切换RCR',
              //   status: 'process',
              //   icon: 'el-icon-loading',
              //   description:''
              // };
              // this.activities.push(newArr)
              let i=0;
              let info='手动切换RCR';
              this.statusList = []
              this.timer = null
              this.init_active = 0
              this.second_active = 0
              this.three_active = 0
              this.four_active = 0
              this.five_active = 0
              this.timer = setInterval(() => {
                this.getStatus(this.timer,res.job_id,i++,info,'')
              }, 1000)
            }
            else{
              this.message_tips = res.error_info;
              this.message_type = 'error';
              messageTip(this.message_tips,this.message_type);
            }

          });
        } else {
          this.message_tips = 'code输入有误'
          this.message_type = 'error'
          messageTip(this.message_tips, this.message_type)
        } 
        }).catch(() => {
            console.log('quxiao')
            messageTip('已取消手动切换','info');
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
      const code = createCode()
      const string = '确定要'+action+'该RCR吗, 是否继续?code=' + code
      gotoCofirm(string).then((res) => {
        // 先执行删权限
        if (!res.value) {
          this.message_tips = 'code不能为空！'
          this.message_type = 'error'
          messageTip(this.message_tips, this.message_type)
        } else if (res.value == code) {
          //handleCofirm("确定要"+action+"该RCR吗, 是否继续?").then( () =>{
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
          paras.rcr_id=row.id;
          tempData.paras = paras;
          
          actionRCR(tempData).then((response)=>{
            let res = response;
            if(res.status=='accept'){
              this.dialogFormVisible = false;
              this.job_id=action+'RCR(任务ID:'+res.job_id+')';
              this.dialogStatusVisible=true;
              // this.activities=[];
              // const newArr={
              //   title:'正在'+action+'RCR',
              //   status: 'process',
              //   icon: 'el-icon-loading',
              //   description:''
              // };
              // this.activities.push(newArr)
              let info=action+'RCR';
              let i=0;
              this.statusList = []
              this.timer = null
              this.init_active = 0
              this.second_active = 0
              this.three_active = 0
              this.four_active = 0
              this.five_active = 0
              //this.getStatus(this.timer,res.job_id,i++,info,'')
              this.timer = setInterval(() => {
                this.getStatus(this.timer,res.job_id,i++,info,'')
              }, 1000)   
            }else{
              this.message_tips = res.error_info;
              this.message_type = 'error';
              messageTip(this.message_tips,this.message_type);
            }
          })
        } else {
          this.message_tips = 'code输入有误'
          this.message_type = 'error'
          messageTip(this.message_tips, this.message_type)
        } 
      }).catch(() => {
          console.log('quxiao')
          messageTip('已取消'+action,'info');
      });   
    },
    // getStatus (timer,data,i,info,row) {
    //   setTimeout(()=>{
    //     const postarr={};
    //     postarr.job_type='get_status';
    //     postarr.version=version_arr[0].ver;
    //     postarr.job_id=data;
    //     postarr.timestamp=timestamp_arr[0].time+'';
    //     postarr.paras={}
    //     getEvStatus(postarr).then((res) => {
    //     if(res.status=='done'||res.status=='failed'){
    //       clearInterval(timer);
    //       if(res.status=='done'){
    //         this.activities[0].title=info+'成功'
    //         this.activities[0].icon='el-icon-circle-check'
    //         this.activities[0].status='success'
    //         // if(info=='设置延迟复制时间'){
    //         //   let tempData={};
    //         //   tempData.rcr_id = this.setform.rcr_id;
    //         //   tempData.maxtime =this.setform.maxtime;
    //         //   tempData.user_name = sessionStorage.getItem('login_username');
    //         //   setRCRMaxDalay(tempData)
    //         // }
    //         // if(info=='删除RCR'){let tempData={};
    //         //   tempData.rcr_id = row;
    //         //   tempData.user_name = sessionStorage.getItem('login_username');
    //         //   delRCRMaxDalayInfo(tempData)
    //         // }
    //         this.getList();
    //       }else{
    //         this.activities[0].title=info+'失败'
    //         this.activities[0].icon='el-icon-circle-close'
    //         this.activities[0].status='error'
    //         this.activities[0].description=res.error_info
    //       }
    //     }
    //   });
    //     if(i>=86400){
    //         clearInterval(timer);
    //     }
    //   }, 0)
    // }
    getStatus(timer, data, i, info, iparr) {
      setTimeout(() => {
        const postarr = {}
        postarr.job_type = 'get_status'
        postarr.version = version_arr[0].ver
        postarr.timestamp = timestamp_arr[0].time + ''
        postarr.job_id = data
        postarr.paras = {}
        //this.job_id = info+'(任务ID:' + data+')'
        getEvStatus(postarr).then((res) => {
          //  let res={"attachment":{"delete_rcrs":[{"shard_id":"2","step":"ongoing"}],"job_steps":"check_params,delete_rcrs,update_meta_record,done","step":"delete_rcrs"},"error_code":"0","error_info":"OK","job_id":"","job_type":"","status":"ongoing","version":"1.0"};
          //let res={"attachment":{"build_rcrs":[{"job_steps":"rebuild_shard_data,build_rcr_relation,done","shard_id":"4","step":"done","sub_id":"178","sub_jobs":[{"192.168.0.125_28010":{"error_code":0,"error_info":"OK","job_steps":"check_param, xtracback_data, checksum_data, backup_old_data, clear_old_data, recover_data, rebuild_sync, done","status":"done","step":"done"}},{"192.168.0.128_28010":{"error_code":0,"error_info":"OK","job_steps":"check_param, xtracback_data, checksum_data, backup_old_data, clear_old_data, recover_data, rebuild_sync, done","status":"done","step":"done"}},{"192.168.0.136_28010":{"error_code":0,"error_info":"OK","job_steps":"check_param, xtracback_data, checksum_data, backup_old_data, clear_old_data, recover_data, rebuild_sync, done","status":"done","step":"done"}}]}],"job_steps":"check_params,check_shard_in_sw,build_rcrs,update_meta_record,done","master_cluster_id":"3","master_master_hosts":"192.168.0.136:28008,","master_rcr_meta":"192.168.0.136:28002,192.168.0.128:28002,192.168.0.125:28002","master_shard_ids":"3,","master_sync_hosts":"192.168.0.125:28008,","slave_cluster_id":"4","slave_cluster_name":"cluster_1686037466_000004","slave_master_hosts":"192.168.0.125:28010,","slave_rcr_meta":"192.168.0.136:28002,192.168.0.128:28002,192.168.0.125:28002","slave_shard_ids":"4,","step":"done"},"error_code":"0","error_info":"OK","job_id":"","job_type":"","status":"done","version":"1.0"}
          //  let res={"attachment":{"job_steps":"check_params, shard_sync, stop_meta_sync, change_rcr_relation, update_meta_record, done","shard_sync":{"job_steps":"set_master_ro, check_slave_sync, done","jobs":[{"shard_id":"3","step":"done"}]},"step":"done"},"error_code":"0","error_info":"OK","job_id":"","job_type":"","status":"ongoing","version":"1.0"}
          // let res=iparr;
          //if (info == '新增RCR') {
            if(res.status=='failed'){
              clearInterval(timer)
            }else if(res.status=='done'){
              this.getList();
              clearInterval(timer)
              if(info=='删除RCR'){
                  let tempData={};
                  tempData.rcr_id = iparr;
                  tempData.user_name = sessionStorage.getItem('login_username');
                  delRCRMaxDalayInfo(tempData)
                }
            }
            if (res.attachment !== null) {
              if (this.statusList.length == 0) {
                let statusgoing = {}
                if (res.status == 'failed') { 
                  statusgoing = {
                    title: info + '失败',
                    icon: 'el-icon-circle-close',
                    status: 'error',
                    description: res.error_info,
                    second: []
                  }
                  this.statusList.push(statusgoing)
                  clearInterval(timer)
                } else {
                  if(info=='启动RCR'||info=='停止RCR'||info=='设置延迟复制时间'){
                    if(res.status=='done'||res.status=='failed'){
                      let action_title='';let action_description='';let action_icon='';let action_status=''
                      if(res.status=='done'){
                        action_title=info + ' 成功';
                        action_icon='el-icon-circle-check';
                        action_status='success';
                        this.getList();
                        clearInterval(timer)
                      }else{
                        action_title=info + '失败';
                        action_description=res.error_info
                        action_icon='el-icon-circle-close';
                        action_status='error';
                      }
                      statusgoing = {
                        title: action_title,
                        icon: action_icon,
                        status: action_status,
                        description: action_description,
                      }
                      this.statusList.push(statusgoing)
                      clearInterval(timer)
                    }else{
                      statusgoing = {
                        title: '正在'+info,
                        icon: 'el-icon-loading',
                        status: 'process',
                        description: '',
                      }
                      this.statusList.push(statusgoing)
                    }
                  }else{
                    const steps = (res.attachment.job_steps).replace(/\s+/g, '').split(',')
                    const firstlist = [];let secondlist=[];let threelist=[];let fourlist=[];let fivelist=[];
                    let first_title='';let first_description='';let first_status='';let first_icon='';
                    for (let b = 0; b < steps.length; b++) {
                      first_title=steps[b];
                      if(res.status=='done'){
                        this.init_active = b
                        first_status='success';
                        first_icon='el-icon-circle-check';
                      }
                      //第一层
                      let firstgoing = { title: first_title, icon: first_icon, status: first_status, description: first_description }
                      firstlist.push(firstgoing)
                      // 手动切换RCR第二第三层
                      if(info == '手动切换RCR'){ 
                        if(steps[b]=='shard_sync'){
                          console.log(res.attachment['shard_sync']['jobs'].length)
                          for (let t = 0; t < res.attachment['shard_sync']['jobs'].length; t++) {
                            let second_steps='shard_id:'+res.attachment['shard_sync']['jobs'][t]['shard_id']+'('+res.attachment['shard_sync']['jobs'][t]['step']+')';
                            let secondgoing = { title: second_steps, icon: '', status: 'wait', description: '',step:res.attachment['shard_sync']['jobs'][t]['step'] }
                            secondlist.push(secondgoing)
                            firstlist[b].second=secondlist;
                            //第三层
                            let three_steps=res.attachment['shard_sync']['job_steps'];
                              three_steps = (three_steps).replace(/\s+/g, '').split(',')
                            for (let g = 0; g < three_steps.length; g++) {
                            let threegoing = { title: three_steps[g], icon: '', status: 'wait', description: '' }
                            threelist.push(threegoing)
                            firstlist[b].second[t].three=threelist;
                            }
                          }
                        }
                      }else{
                        for(let d in Object.keys(res.attachment)){
                          if(steps[b]==Object.keys(res.attachment)[d]){
                            for(let f in res.attachment[Object.keys(res.attachment)[d]]){
                              //console.log(res.attachment[Object.keys(res.attachment)[d]]);
                              //第二层
                              let second_steps='shard_id:'+res.attachment[Object.keys(res.attachment)[d]][f]['shard_id']+'('+res.attachment[Object.keys(res.attachment)[d]][f]['step']+')';
                              let secondgoing = { title: second_steps, icon: '', status: 'wait', description: '',step:res.attachment[Object.keys(res.attachment)[d]][f]['step'] }
                              secondlist.push(secondgoing)
                              firstlist[b].second=secondlist;
                              if (info == '新增RCR') {
                              //第三层
                              let  three_steps=res.attachment[Object.keys(res.attachment)[d]][f]['job_steps'];
                              three_steps = (three_steps).replace(/\s+/g, '').split(',')
                              for (let g = 0; g < three_steps.length; g++) {
                                let threegoing = { title: three_steps[g], icon: '', status: 'wait', description: '' }
                                
                                threelist.push(threegoing)
                                firstlist[b].second[f].three=threelist;

                                //第四层
                                let fourlist=[];
                                let four_steps=res.attachment[Object.keys(res.attachment)[d]][f]['sub_jobs'];
                                for(let h=0;h<four_steps.length;h++){
                                  //console.log(Object.keys(four_steps[h])[0]);
                                  let fourgoing={title:Object.keys(four_steps[h])[0],icon: '', status: 'wait', description: ''};
                                  fourlist.push(fourgoing)
                                  
                                  //第五层job_steps
                                  let fivelist=[];
                                  let five_steps=four_steps[h][Object.keys(four_steps[h])[0]]['job_steps'];
                                  five_steps = (five_steps).replace(/\s+/g, '').split(',');
                                  for(let i=0;i<five_steps.length;i++){
                                    let fivegoing={title:five_steps[i],icon: '', status: 'wait', description: ''};
                                    fivelist.push(fivegoing);
                                  }
                                  //只有rebuild_shard_data的时候加入数据
                                  if(g<(three_steps.length-2)){
                                    firstlist[b].second[f].three[g].four=fourlist;  
                                    firstlist[b].second[f].three[g].four[h].five=fivelist;
                                  }
                                }
                                }
                              }
                            }
                          
                          }
                        } 
                      }
                     
                    }
                    this.statusList=firstlist;
                    console.log( this.statusList);
                    //第一层修改状态
                    for(let a=0;a<this.statusList.length;a++){
                      //大于build_rcrs时，改build_rcrs下的子状态(新增)//大于shard_sync时，改shard_sync下的子状态(手动切换)
                      if(res.attachment.step=='update_meta_record'||res.attachment.step=='done'||(res.attachment.step=='stop_meta_sync'||res.attachment.step=='change_rcr_relation'||res.attachment.step=='update_meta_record')){
                        let rcrs_arrs='';
                        if(info=='新增RCR'){
                          rcrs_arrs=res.attachment.build_rcrs;
                        }else if(info=='删除RCR'){
                          rcrs_arrs=res.attachment.delete_rcrs;
                        }else if(info == '手动切换RCR'){
                           rcrs_arrs=res.attachment.shard_sync.jobs;
                        }
                        for(let m=0;m<rcrs_arrs.length;m++){
                          if(info=='新增RCR'){
                            for (let l = 0;l< this.statusList[2].second.length; l++) {
                              let six_step=rcrs_arrs[m].sub_jobs
                              for(let p=0;p<six_step.length;p++){
                                let steps_arr = (rcrs_arrs[m].job_steps).replace(/\s+/g, '').split(',')
                                for(let q=0;q<steps_arr.length;q++){
                                  // //后面再返的参数push进去
                                  // if(six_step.length>this.statusList[2].second[l].three[0].four.length){
                                  //   if (this.statusList[2].second[l].three[0].four[p].title!==Object.keys(six_step[p])[0]) {
                                  //     let fourgoing={title:Object.keys(six_step[p])[0],icon: '', status: 'wait', description: '',five:this.statusList[2].second[l].three[0].four[0].five};
                                  //     this.statusList[2].second[l].three[0].four.push(fourgoing)
                                  //   }
                                  // }
                                  let five_len=this.statusList[2].second[l].three[0].four[p].five;
                                  for(let r=0;r<five_len.length;r++){
                                    this.statusList[2].second[l].three[0].four[p].five[r].icon = 'el-icon-circle-check'
                                    this.statusList[2].second[l].three[0].four[p].five[r].status = 'success'
                                    this.statusList[2].second[l].three[0].four[p].icon='el-icon-circle-check'
                                    this.statusList[2].second[l].three[0].four[p].status='success' 
                                  }
                                  this.statusList[2].second[l].three[q].icon='el-icon-circle-check'
                                  this.statusList[2].second[l].three[q].status='success'
                                }
                              }
                              this.statusList[2].second[l].icon='el-icon-circle-check'
                              this.statusList[2].second[l].status='success'
                            }
                          }else if(info=='删除RCR'){
                              for (let l = 0;l< this.statusList[1].second.length; l++) {
                              this.statusList[1].second[l].icon='el-icon-circle-check'
                              this.statusList[1].second[l].status='success'
                              }
                          }else if(info == '手动切换RCR'){
                            if(rcrs_arrs[m].step=='done'){
                              for (let l = 0;l< this.statusList[1].second.length; l++) {
                                this.statusList[1].second[l].icon='el-icon-circle-check'
                                this.statusList[1].second[l].status='success'
                                for (let v = 0;v< this.statusList[1].second[l].three.length; v++) {
                                  this.statusList[1].second[l].three[v].icon='el-icon-circle-check'
                                  this.statusList[1].second[l].three[v].status='success'
                                }
                              }
                            }else{
                              for (let l = 0;l< this.statusList[1].second.length; l++) {
                                this.statusList[1].second[l].icon = 'el-icon-loading'
                                this.statusList[1].second[l].status = 'process'
                                this.statusList[1].second[l].step=res.attachment.shard_sync.jobs[m].step
                                //修改第二层title括号内容
                                const sencond_arr=this.statusList[1].second[l].title;
                                let index1=sencond_arr.indexOf('(');
                                let index2=sencond_arr.indexOf(')');
                                let change_arr=sencond_arr.substring(index1+1,index2);
                                if(change_arr!=res.attachment.shard_sync.jobs[m].step){
                                  this.statusList[1].second[l].title=sencond_arr.substring(index1)+'('+res.attachment.shard_sync.jobs[m].step+')'
                                }
                                for (let k = 0; k < l; k++) { 
                                  this.statusList[1].second[k].icon = 'el-icon-circle-check'
                                  this.statusList[1].second[k].status = 'success'
                                }
                              }
                            }
                          }
                        }
                      }

                      if (res.attachment.step == this.statusList[a].title) {
                        this.init_active = a
                        if(res.status=='failed'){
                          this.statusList[a].icon = 'el-icon-circle-close'
                          this.statusList[a].status = 'error'
                          this.statusList[a].description = res.error_info
                          for (let k = 0; k < a; k++) { 
                            this.statusList[k].icon = 'el-icon-circle-check'
                            this.statusList[k].status = 'success'
                          }
                        }else if(res.status=='done'){
                          for (let k = 0; k <=a; k++) { 
                            this.statusList[k].icon = 'el-icon-circle-check'
                            this.statusList[k].status = 'success'
                          }
                        }else{
                          this.statusList[a].icon = 'el-icon-loading'
                          this.statusList[a].status = 'process'
                          for (let k = 0; k < a; k++) { 
                            this.statusList[k].icon = 'el-icon-circle-check'
                            this.statusList[k].status = 'success'
                          }
                        }
                      }
                      
                    }
                    //第三层修改状态
                    if(res.attachment.step =='build_rcrs'||res.attachment.step =='delete_rcrs'||res.attachment.step =='shard_sync'){
                      
                      if(info=='新增RCR'){
                      for (let l = 0;  l< this.statusList[2].second.length; l++) {
                        for(let m=0;m<res.attachment.build_rcrs.length;m++){
                          let sencond_len=0;
                          
                          if (res.attachment.build_rcrs[m].step == this.statusList[2].second[l].step) {
                            //不是rebuild_shard_data时，ip里的状态要改变
                            if(res.attachment.build_rcrs[m].step!=='rebuild_shard_data'){
                              let six_step=res.attachment.build_rcrs[m].sub_jobs
                              for(let p=0;p<six_step.length;p++){
                                // console.log(six_step)
                                // //后面再返的参数push进去
                                // if(six_step.length>this.statusList[2].second[l].three[0].four.length){
                                //   if (this.statusList[2].second[l].three[0].four[p].title!==Object.keys(six_step[p])[0]) {
                                //     let fourgoing={title:Object.keys(p)[0],icon: '', status: 'wait', description: '',five:this.statusList[2].second[l].three[0].four[0].five};
                                //     this.statusList[2].second[l].three[0].four.push(fourgoing)
                                //   }
                                // }
                                let five_len=this.statusList[2].second[l].three[0].four[p].five;
                                let six_ip=Object.keys(six_step[p])[0];
                                if(six_ip==this.statusList[2].second[l].three[0].four[p].title){
                                  for(let r=0;r<five_len.length;r++){
                                    this.statusList[2].second[l].three[0].four[p].five[r].icon = 'el-icon-circle-check'
                                    this.statusList[2].second[l].three[0].four[p].five[r].status = 'success'
                                    this.statusList[2].second[l].three[0].four[p].icon='el-icon-circle-check'
                                    this.statusList[2].second[l].three[0].four[p].status='success'
                                  }
                                }
                              }
                            }

                            if(res.attachment.build_rcrs[m].step !=='done'){
                              this.statusList[2].second[l].icon = 'el-icon-loading'
                              this.statusList[2].second[l].status = 'process'
                              this.statusList[2].second[l].step=res.attachment.build_rcrs[m].step
                              //修改第二层title括号内容
                              const sencond_arr=this.statusList[2].second[l].title;
                              let index1=sencond_arr.indexOf('(');
                              let index2=sencond_arr.indexOf(')');
                              let change_arr=sencond_arr.substring(index1+1,index2);
                              if(change_arr!=res.attachment.build_rcrs[m].step){
                                this.statusList[2].second[l].title=sencond_arr.substring(index1)+'('+res.attachment.build_rcrs[m].step+')'
                              }
                              for (let k = 0; k < l; k++) { 
                                this.statusList[2].second[k].icon = 'el-icon-circle-check'
                                this.statusList[2].second[k].status = 'success'
                              }
                              
                            }else {
                              //如果底层执行完了，第一层build_rcrs要改状态
                              sencond_len++;
                              console.log(sencond_len )
                              if(sencond_len==res.attachment.build_rcrs.length){
                                this.statusList[2].icon = 'el-icon-circle-check'
                                this.statusList[2].status = 'success'
                              }
                              this.statusList[2].second[l].icon = 'el-icon-circle-check'
                              this.statusList[2].second[l].status = 'success'
                            }
                            //修改第四层的状态
                            let four_str=this.statusList[2].second[l].three;
                            for(let n=0;n<four_str.length;n++){
                                let four_len=0;
                              if (res.attachment.build_rcrs[m].step == this.statusList[2].second[l].three[n].title) {
                                if(res.attachment.build_rcrs[m].step !=='done'){
                                  this.statusList[2].second[l].three[n].icon='el-icon-loading';
                                  this.statusList[2].second[l].three[n].status='process';
                                  for (let k = 0; k < n; k++) { 
                                    this.statusList[2].second[l].three[k].icon = 'el-icon-circle-check'
                                    this.statusList[2].second[l].three[k].status = 'success'
                                  }
                                }else{
                                  four_len++;
                                  if(four_len==res.attachment.build_rcrs.length){
                                    for (let k = 0; k < n; k++) { 
                                      this.statusList[2].second[l].three[k].icon = 'el-icon-circle-check'
                                      this.statusList[2].second[l].three[k].status = 'success'
                                    }
                                  }
                                  this.statusList[2].second[l].three[n].icon = 'el-icon-circle-check'
                                  this.statusList[2].second[l].three[n].status = 'success'  
                                }
                              }
                            }
                          }
                          //第五第六层改状态
                          if(res.attachment.build_rcrs[m].step=='rebuild_shard_data'){
                            let six_step=res.attachment.build_rcrs[m].sub_jobs;
                          
                            for(let p=0;p<six_step.length;p++){
                              let six_ip=Object.keys(six_step[p])[0];
                              let six_info=six_step[p][six_ip];
                              //后面再返的参数push进去
                              if(six_step.length>this.statusList[2].second[l].three[0].four.length){
                                if (this.statusList[2].second[l].three[0].four[0].title!==Object.keys(six_step[p])[0]) {
                                  let five_add=[]
                                  let five_add_steps=six_step[p][six_ip]['job_steps'];
                                  five_add_steps = (five_add_steps).replace(/\s+/g, '').split(',');
                                  for(let v=0;v<five_add_steps.length;v++){
                                    let five_add_going={title:five_add_steps[v],status:'wait',description: ''}
                                    five_add.push(five_add_going)
                                  }
                                  let fourgoing={title:Object.keys(six_step[p])[0],icon: '', status: 'wait', description: '',five:five_add};
                                  this.statusList[2].second[l].three[0].four.push(fourgoing)
                                }
                              }
                              if(six_ip==this.statusList[2].second[l].three[0].four[p].title){
                                if(six_info.status=='done'){
                                  console.log(1);
                                  let five_len=this.statusList[2].second[l].three[0].four[p].five;
                                  for(let r=0;r<five_len.length;r++){
                                    this.statusList[2].second[l].three[0].four[p].five[r].icon = 'el-icon-circle-check'
                                    this.statusList[2].second[l].three[0].four[p].five[r].status = 'success'
                                    this.statusList[2].second[l].three[0].four[p].icon='el-icon-circle-check'
                                    this.statusList[2].second[l].three[0].four[p].status='success'
                                  }
                                }else if(six_info.status=='failed'){
                                  let five_len=this.statusList[2].second[l].three[0].four[p].five;
                                  for(let r=0;r<five_len.length;r++){
                                    let this_index=0;
                                    if(six_info.step== this.statusList[2].second[l].three[0].four[p].five[r].title){
                                      this_index=r;
                                      this.statusList[2].second[l].three[0].four[p].five[r].icon = 'el-icon-circle-close'
                                      this.statusList[2].second[l].three[0].four[p].five[r].status = 'error'
                                      this.statusList[2].second[l].three[0].four[p].five[r].description=six_info.error_info
                                      //第五层改状态
                                      this.statusList[2].second[l].three[0].four[p].icon='el-icon-circle-close'
                                      this.statusList[2].second[l].three[0].four[p].status='error'
                                      this.statusList[2].second[l].three[0].four[p].description=six_info.error_info
                                      //第四层改状态
                                      this.statusList[2].second[l].three[0].icon='el-icon-circle-close'
                                      this.statusList[2].second[l].three[0].status='error'
                                      //第三层改状态
                                      this.statusList[2].second[l].icon='el-icon-circle-close'
                                      this.statusList[2].second[l].status='error'
                                      //第二层改状态
                                      this.statusList[2].icon='el-icon-circle-close'
                                      this.statusList[2].status='error'
                                      this.statusList[2].description=res.error_info
                                    }
                                    for(let s=0;s<this_index;s++){
                                      this.statusList[2].second[l].three[0].four[p].five[s].icon = 'el-icon-circle-check'
                                      this.statusList[2].second[l].three[0].four[p].five[s].status = 'success'
                                    }
                                  }
                                }else{
                                  let five_len=this.statusList[2].second[l].three[0].four[p].five;
                                  for(let r=0;r<five_len.length;r++){
                                    let this_index=0;
                                    if(six_info.step== this.statusList[2].second[l].three[0].four[p].five[r].title){
                                      this_index=r;
                                      this.statusList[2].second[l].three[0].four[p].five[r].icon = 'el-icon-loading'
                                      this.statusList[2].second[l].three[0].four[p].five[r].status = 'process'
                                      this.statusList[2].second[l].three[0].four[p].icon='el-icon-loading'
                                      this.statusList[2].second[l].three[0].four[p].status='process'
                                    }
                                    for(let s=0;s<this_index;s++){
                                      this.statusList[2].second[l].three[0].four[p].five[s].icon = 'el-icon-circle-check'
                                      this.statusList[2].second[l].three[0].four[p].five[s].status = 'success'
                                    }
                                  }
                                }
                              }
                            }
                          }
                        }
                      }
                      }else if(info=='删除RCR'){
                        for (let l = 0;  l< this.statusList[1].second.length; l++) {
                          for(let m=0;m<res.attachment.delete_rcrs.length;m++){
                              if(res.attachment.delete_rcrs[m].step !=='done'){
                              this.statusList[1].second[l].icon = 'el-icon-loading'
                              this.statusList[1].second[l].status = 'process'
                              this.statusList[1].second[l].step=res.attachment.delete_rcrs[m].step
                              //修改第二层title括号内容
                              const sencond_arr=this.statusList[1].second[l].title;
                              let index1=sencond_arr.indexOf('(');
                              let index2=sencond_arr.indexOf(')');
                              let change_arr=sencond_arr.substring(index1+1,index2);
                              if(change_arr!=res.attachment.delete_rcrs[m].step){
                                this.statusList[2].second[l].title=sencond_arr.substring(index1)+'('+res.attachment.delete_rcrs[m].step+')'
                              }
                              for (let k = 0; k < l; k++) { 
                                this.statusList[1].second[k].icon = 'el-icon-circle-check'
                                this.statusList[1].second[k].status = 'success'
                              }
                            }else {
                              //如果底层执行完了，第一层build_rcrs要改状态
                              sencond_len++;
                              if(sencond_len==res.attachment.delete_rcrs.length){
                                this.statusList[1].icon = 'el-icon-circle-check'
                                this.statusList[1].status = 'success'
                              }
                              this.statusList[1].second[l].icon = 'el-icon-circle-check'
                              this.statusList[1].second[l].status = 'success'
                            }
                          }
                        }
                      }else if(info=='手动切换RCR'){
                        let rcrs_arrs=res.attachment.shard_sync.jobs;
                        
                        for(let m=0;m<rcrs_arrs.length;m++){
                          if(rcrs_arrs[m].step=='done'){
                            for (let l = 0;l< this.statusList[1].second.length; l++) {
                              this.statusList[1].second[l].icon='el-icon-circle-check'
                              this.statusList[1].second[l].status='success'
                              for (let v = 0;v< this.statusList[1].second[l].three.length; v++) {
                                this.statusList[1].second[l].three[v].icon='el-icon-circle-check'
                                this.statusList[1].second[l].three[v].status='success'
                              }
                              if(l==rcrs_arrs.length-1){
                                this.statusList[1].icon = 'el-icon-circle-check'
                                this.statusList[1].status = 'success'
                              }
                            }
                            
                          }else{
                            for (let l = 0;l< this.statusList[1].second.length; l++) {
                              for (let v = 0;v< this.statusList[1].second[l].three.length; v++) {
                                console.log(this.statusList[1].second[l].three[v].title);
                                console.log(res.attachment.shard_sync.jobs[m].step);
                                if(this.statusList[1].second[l].three[v].title==res.attachment.shard_sync.jobs[m].step){
                                  this.statusList[1].second[l].icon = 'el-icon-loading'
                                  this.statusList[1].second[l].status = 'process'
                                  this.statusList[1].second[l].three[v].icon = 'el-icon-loading'
                                  this.statusList[1].second[l].three[v].status = 'process'
                                  this.statusList[1].second[l].step=res.attachment.shard_sync.jobs[m].step
                                  //修改第二层title括号内容
                                  const sencond_arr=this.statusList[1].second[l].title;
                                  let index1=sencond_arr.indexOf('(');
                                  let index2=sencond_arr.indexOf(')');
                                  let change_arr=sencond_arr.substring(index1+1,index2);
                                  if(change_arr!=res.attachment.shard_sync.jobs[m].step){
                                    this.statusList[1].second[l].title=sencond_arr.substring(index1)+'('+res.attachment.shard_sync.jobs[m].step+')'
                                  }
                                  for (let k = 0; k < l; k++) { 
                                    this.statusList[1].second[k].icon = 'el-icon-circle-check'
                                    this.statusList[1].second[k].status = 'success'
                                  }
                                  for (let kk = 0; kk < v; kk++) { 
                                    this.statusList[1].second[l].three[kk].icon = 'el-icon-circle-check'
                                    this.statusList[1].second[l].three[kk].status = 'success'
                                  }
                                }
                              }
                              
                            }
                          }
                        }
                        
                      }
                    }
                  }
                }
              } else {
                let statusgoing = {}
                if (res.status == 'failed') { 
                  statusgoing = {
                    title: info + '失败',
                    icon: 'el-icon-circle-close',
                    status: 'error',
                    description: res.error_info,
                    second: []
                  }
                  this.statusList.push(statusgoing)
                  clearInterval(timer)
                } else{
                //第一层修改状态
                for(let a=0;a<this.statusList.length;a++){
                  //大于build_rcrs时，改build_rcrs下的子状态(新增)//大于shard_sync时，改shard_sync下的子状态(手动切换)
                  if(res.attachment.step=='update_meta_record'||res.attachment.step=='done'||(res.attachment.step=='stop_meta_sync'||res.attachment.step=='change_rcr_relation'||res.attachment.step=='update_meta_record')){
                    let rcrs_arrs='';
                    if(info=='新增RCR'){
                      rcrs_arrs=res.attachment.build_rcrs;
                    }else if(info=='删除RCR'){
                      rcrs_arrs=res.attachment.delete_rcrs;
                    }else if(info == '手动切换RCR'){
                        rcrs_arrs=res.attachment.shard_sync.jobs;
                    }
                    for(let m=0;m<rcrs_arrs.length;m++){
                      if(info=='新增RCR'){
                        for (let l = 0;l< this.statusList[2].second.length; l++) {
                          let six_step=rcrs_arrs[m].sub_jobs
                          for(let p=0;p<six_step.length;p++){
                            let steps_arr = (rcrs_arrs[m].job_steps).replace(/\s+/g, '').split(',')
                            for(let q=0;q<steps_arr.length;q++){
                              // //后面再返的参数push进去
                              // if(six_step.length>this.statusList[2].second[l].three[0].four.length){
                              //   if (this.statusList[2].second[l].three[0].four[p].title!==Object.keys(six_step[p])[0]) {
                              //     let fourgoing={title:Object.keys(six_step[p])[0],icon: '', status: 'wait', description: '',five:this.statusList[2].second[l].three[0].four[0].five};
                              //     this.statusList[2].second[l].three[0].four.push(fourgoing)
                              //   }
                              // }
                              let five_len=this.statusList[2].second[l].three[0].four[p].five;
                              for(let r=0;r<five_len.length;r++){
                                this.statusList[2].second[l].three[0].four[p].five[r].icon = 'el-icon-circle-check'
                                this.statusList[2].second[l].three[0].four[p].five[r].status = 'success'
                                this.statusList[2].second[l].three[0].four[p].icon='el-icon-circle-check'
                                this.statusList[2].second[l].three[0].four[p].status='success' 
                              }
                              this.statusList[2].second[l].three[q].icon='el-icon-circle-check'
                              this.statusList[2].second[l].three[q].status='success'
                            }
                          }
                          this.statusList[2].second[l].icon='el-icon-circle-check'
                          this.statusList[2].second[l].status='success'
                        }
                      }else if(info=='删除RCR'){
                          for (let l = 0;l< this.statusList[1].second.length; l++) {
                          this.statusList[1].second[l].icon='el-icon-circle-check'
                          this.statusList[1].second[l].status='success'
                          }
                      }else if(info == '手动切换RCR'){
                        if(rcrs_arrs[m].step=='done'){
                          for (let l = 0;l< this.statusList[1].second.length; l++) {
                            this.statusList[1].second[l].icon='el-icon-circle-check'
                            this.statusList[1].second[l].status='success'
                            for (let v = 0;v< this.statusList[1].second[l].three.length; v++) {
                              this.statusList[1].second[l].three[v].icon='el-icon-circle-check'
                              this.statusList[1].second[l].three[v].status='success'
                            }
                          }
                        }else{
                          for (let l = 0;l< this.statusList[1].second.length; l++) {
                            this.statusList[1].second[l].icon = 'el-icon-loading'
                            this.statusList[1].second[l].status = 'process'
                            this.statusList[1].second[l].step=res.attachment.shard_sync.jobs[m].step
                            //修改第二层title括号内容
                            const sencond_arr=this.statusList[1].second[l].title;
                            let index1=sencond_arr.indexOf('(');
                            let index2=sencond_arr.indexOf(')');
                            let change_arr=sencond_arr.substring(index1+1,index2);
                            if(change_arr!=res.attachment.shard_sync.jobs[m].step){
                              this.statusList[1].second[l].title=sencond_arr.substring(index1)+'('+res.attachment.shard_sync.jobs[m].step+')'
                            }
                            for (let k = 0; k < l; k++) { 
                              this.statusList[1].second[k].icon = 'el-icon-circle-check'
                              this.statusList[1].second[k].status = 'success'
                            }
                          }
                        }
                      }
                    }
                  }
                  if (res.attachment.step == this.statusList[a].title) {
                        this.init_active = a
                        if(res.status=='failed'){
                          this.statusList[a].icon = 'el-icon-circle-close'
                          this.statusList[a].status = 'error'
                          this.statusList[a].description = res.error_info
                          for (let k = 0; k < a; k++) { 
                            this.statusList[k].icon = 'el-icon-circle-check'
                            this.statusList[k].status = 'success'
                          }
                        }else if(res.status=='done'){
                          for (let k = 0; k <=a; k++) { 
                            this.statusList[k].icon = 'el-icon-circle-check'
                            this.statusList[k].status = 'success'
                          }
                        }else{
                          this.statusList[a].icon = 'el-icon-loading'
                          this.statusList[a].status = 'process'
                          for (let k = 0; k < a; k++) { 
                            this.statusList[k].icon = 'el-icon-circle-check'
                            this.statusList[k].status = 'success'
                          }
                        }
                      }
                  
                }
                //第三层修改状态
                if(res.attachment.step =='build_rcrs'||res.attachment.step =='delete_rcrs'||res.attachment.step =='shard_sync'){
                  
                  if(info=='新增RCR'){
                  for (let l = 0;  l< this.statusList[2].second.length; l++) {
                    for(let m=0;m<res.attachment.build_rcrs.length;m++){
                      let sencond_len=0;
                      
                      if (res.attachment.build_rcrs[m].step == this.statusList[2].second[l].step) {
                        //不是rebuild_shard_data时，ip里的状态要改变
                        if(res.attachment.build_rcrs[m].step!=='rebuild_shard_data'){
                          let six_step=res.attachment.build_rcrs[m].sub_jobs
                          for(let p=0;p<six_step.length;p++){
                            // console.log(six_step)
                            // //后面再返的参数push进去
                            // if(six_step.length>this.statusList[2].second[l].three[0].four.length){
                            //   if (this.statusList[2].second[l].three[0].four[p].title!==Object.keys(six_step[p])[0]) {
                            //     let fourgoing={title:Object.keys(p)[0],icon: '', status: 'wait', description: '',five:this.statusList[2].second[l].three[0].four[0].five};
                            //     this.statusList[2].second[l].three[0].four.push(fourgoing)
                            //   }
                            // }
                            let five_len=this.statusList[2].second[l].three[0].four[p].five;
                            let six_ip=Object.keys(six_step[p])[0];
                            if(six_ip==this.statusList[2].second[l].three[0].four[p].title){
                              for(let r=0;r<five_len.length;r++){
                                this.statusList[2].second[l].three[0].four[p].five[r].icon = 'el-icon-circle-check'
                                this.statusList[2].second[l].three[0].four[p].five[r].status = 'success'
                                this.statusList[2].second[l].three[0].four[p].icon='el-icon-circle-check'
                                this.statusList[2].second[l].three[0].four[p].status='success'
                              }
                            }
                          }
                        }

                        if(res.attachment.build_rcrs[m].step !=='done'){
                          this.statusList[2].second[l].icon = 'el-icon-loading'
                          this.statusList[2].second[l].status = 'process'
                          this.statusList[2].second[l].step=res.attachment.build_rcrs[m].step
                          //修改第二层title括号内容
                          const sencond_arr=this.statusList[2].second[l].title;
                          let index1=sencond_arr.indexOf('(');
                          let index2=sencond_arr.indexOf(')');
                          let change_arr=sencond_arr.substring(index1+1,index2);
                          if(change_arr!=res.attachment.build_rcrs[m].step){
                            this.statusList[2].second[l].title=sencond_arr.substring(index1)+'('+res.attachment.build_rcrs[m].step+')'
                          }
                          for (let k = 0; k < l; k++) { 
                            this.statusList[2].second[k].icon = 'el-icon-circle-check'
                            this.statusList[2].second[k].status = 'success'
                          }
                          
                        }else {
                          //如果底层执行完了，第一层build_rcrs要改状态
                          sencond_len++;
                          console.log(sencond_len )
                          if(sencond_len==res.attachment.build_rcrs.length){
                            this.statusList[2].icon = 'el-icon-circle-check'
                            this.statusList[2].status = 'success'
                          }
                          this.statusList[2].second[l].icon = 'el-icon-circle-check'
                          this.statusList[2].second[l].status = 'success'
                        }
                        //修改第四层的状态
                        let four_str=this.statusList[2].second[l].three;
                        for(let n=0;n<four_str.length;n++){
                            let four_len=0;
                          if (res.attachment.build_rcrs[m].step == this.statusList[2].second[l].three[n].title) {
                            if(res.attachment.build_rcrs[m].step !=='done'){
                              this.statusList[2].second[l].three[n].icon='el-icon-loading';
                              this.statusList[2].second[l].three[n].status='process';
                              for (let k = 0; k < n; k++) { 
                                this.statusList[2].second[l].three[k].icon = 'el-icon-circle-check'
                                this.statusList[2].second[l].three[k].status = 'success'
                              }
                            }else{
                              four_len++;
                              if(four_len==res.attachment.build_rcrs.length){
                                for (let k = 0; k < n; k++) { 
                                  this.statusList[2].second[l].three[k].icon = 'el-icon-circle-check'
                                  this.statusList[2].second[l].three[k].status = 'success'
                                }
                              }
                              this.statusList[2].second[l].three[n].icon = 'el-icon-circle-check'
                              this.statusList[2].second[l].three[n].status = 'success'  
                            }
                          }
                        }
                      }
                      //第五第六层改状态
                      if(res.attachment.build_rcrs[m].step=='rebuild_shard_data'){
                        let six_step=res.attachment.build_rcrs[m].sub_jobs;
                       
                        for(let p=0;p<six_step.length;p++){
                          let six_ip=Object.keys(six_step[p])[0];
                          let six_info=six_step[p][six_ip];
                          //后面再返的参数push进去
                          if(six_step.length>this.statusList[2].second[l].three[0].four.length){
                            if (this.statusList[2].second[l].three[0].four[0].title!==Object.keys(six_step[p])[0]) {
                              let five_add=[]
                              let five_add_steps=six_step[p][six_ip]['job_steps'];
                              five_add_steps = (five_add_steps).replace(/\s+/g, '').split(',');
                              for(let v=0;v<five_add_steps.length;v++){
                                let five_add_going={title:five_add_steps[v],status:'wait',description: ''}
                                five_add.push(five_add_going)
                              }
                              let fourgoing={title:Object.keys(six_step[p])[0],icon: '', status: 'wait', description: '',five:five_add};
                              this.statusList[2].second[l].three[0].four.push(fourgoing)
                            }
                          }
                          if(six_ip==this.statusList[2].second[l].three[0].four[p].title){
                            if(six_info.status=='done'){
                              console.log(1);
                              let five_len=this.statusList[2].second[l].three[0].four[p].five;
                              for(let r=0;r<five_len.length;r++){
                                this.statusList[2].second[l].three[0].four[p].five[r].icon = 'el-icon-circle-check'
                                this.statusList[2].second[l].three[0].four[p].five[r].status = 'success'
                                this.statusList[2].second[l].three[0].four[p].icon='el-icon-circle-check'
                                this.statusList[2].second[l].three[0].four[p].status='success'
                              }
                            }else if(six_info.status=='failed'){
                              let five_len=this.statusList[2].second[l].three[0].four[p].five;
                              for(let r=0;r<five_len.length;r++){
                                let this_index=0;
                                if(six_info.step== this.statusList[2].second[l].three[0].four[p].five[r].title){
                                  this_index=r;
                                  this.statusList[2].second[l].three[0].four[p].five[r].icon = 'el-icon-circle-close'
                                  this.statusList[2].second[l].three[0].four[p].five[r].status = 'error'
                                  this.statusList[2].second[l].three[0].four[p].five[r].description=six_info.error_info
                                  //第五层改状态
                                  this.statusList[2].second[l].three[0].four[p].icon='el-icon-circle-close'
                                  this.statusList[2].second[l].three[0].four[p].status='error'
                                  this.statusList[2].second[l].three[0].four[p].description=six_info.error_info
                                  //第四层改状态
                                  this.statusList[2].second[l].three[0].icon='el-icon-circle-close'
                                  this.statusList[2].second[l].three[0].status='error'
                                  //第三层改状态
                                  this.statusList[2].second[l].icon='el-icon-circle-close'
                                  this.statusList[2].second[l].status='error'
                                  //第二层改状态
                                  this.statusList[2].icon='el-icon-circle-close'
                                  this.statusList[2].status='error'
                                  this.statusList[2].description=res.error_info
                                }
                                for(let s=0;s<this_index;s++){
                                  this.statusList[2].second[l].three[0].four[p].five[s].icon = 'el-icon-circle-check'
                                  this.statusList[2].second[l].three[0].four[p].five[s].status = 'success'
                                }
                              }
                            }else{
                              let five_len=this.statusList[2].second[l].three[0].four[p].five;
                              for(let r=0;r<five_len.length;r++){
                                let this_index=0;
                                if(six_info.step== this.statusList[2].second[l].three[0].four[p].five[r].title){
                                  this_index=r;
                                  this.statusList[2].second[l].three[0].four[p].five[r].icon = 'el-icon-loading'
                                  this.statusList[2].second[l].three[0].four[p].five[r].status = 'process'
                                  this.statusList[2].second[l].three[0].four[p].icon='el-icon-loading'
                                  this.statusList[2].second[l].three[0].four[p].status='process'
                                }
                                for(let s=0;s<this_index;s++){
                                  this.statusList[2].second[l].three[0].four[p].five[s].icon = 'el-icon-circle-check'
                                  this.statusList[2].second[l].three[0].four[p].five[s].status = 'success'
                                }
                              }
                            }
                          }
                        }
                      }
                    }
                  }
                  }else if(info=='删除RCR'){
                    for (let l = 0;  l< this.statusList[1].second.length; l++) {
                      for(let m=0;m<res.attachment.delete_rcrs.length;m++){
                          if(res.attachment.delete_rcrs[m].step !=='done'){
                          this.statusList[1].second[l].icon = 'el-icon-loading'
                          this.statusList[1].second[l].status = 'process'
                          this.statusList[1].second[l].step=res.attachment.delete_rcrs[m].step
                          //修改第二层title括号内容
                          const sencond_arr=this.statusList[1].second[l].title;
                          let index1=sencond_arr.indexOf('(');
                          let index2=sencond_arr.indexOf(')');
                          let change_arr=sencond_arr.substring(index1+1,index2);
                          if(change_arr!=res.attachment.delete_rcrs[m].step){
                            this.statusList[2].second[l].title=sencond_arr.substring(index1)+'('+res.attachment.delete_rcrs[m].step+')'
                          }
                          for (let k = 0; k < l; k++) { 
                            this.statusList[1].second[k].icon = 'el-icon-circle-check'
                            this.statusList[1].second[k].status = 'success'
                          }
                        }else {
                          //如果底层执行完了，第一层build_rcrs要改状态
                          sencond_len++;
                          if(sencond_len==res.attachment.delete_rcrs.length){
                            this.statusList[1].icon = 'el-icon-circle-check'
                            this.statusList[1].status = 'success'
                          }
                          this.statusList[1].second[l].icon = 'el-icon-circle-check'
                          this.statusList[1].second[l].status = 'success'
                        }
                      }
                    }
                  }else if(info=='手动切换RCR'){
                    let rcrs_arrs=res.attachment.shard_sync.jobs;
                    
                    for(let m=0;m<rcrs_arrs.length;m++){
                      if(rcrs_arrs[m].step=='done'){
                        for (let l = 0;l< this.statusList[1].second.length; l++) {
                          this.statusList[1].second[l].icon='el-icon-circle-check'
                          this.statusList[1].second[l].status='success'
                          for (let v = 0;v< this.statusList[1].second[l].three.length; v++) {
                            this.statusList[1].second[l].three[v].icon='el-icon-circle-check'
                            this.statusList[1].second[l].three[v].status='success'
                          }
                          if(l==rcrs_arrs.length-1){
                            this.statusList[1].icon = 'el-icon-circle-check'
                            this.statusList[1].status = 'success'
                          }
                        }
                        
                      }else{
                        for (let l = 0;l< this.statusList[1].second.length; l++) {
                          for (let v = 0;v< this.statusList[1].second[l].three.length; v++) {
                            console.log(this.statusList[1].second[l].three[v].title);
                            console.log(res.attachment.shard_sync.jobs[m].step);
                            if(this.statusList[1].second[l].three[v].title==res.attachment.shard_sync.jobs[m].step){
                              this.statusList[1].second[l].icon = 'el-icon-loading'
                              this.statusList[1].second[l].status = 'process'
                              this.statusList[1].second[l].three[v].icon = 'el-icon-loading'
                              this.statusList[1].second[l].three[v].status = 'process'
                              this.statusList[1].second[l].step=res.attachment.shard_sync.jobs[m].step
                              //修改第二层title括号内容
                              const sencond_arr=this.statusList[1].second[l].title;
                              let index1=sencond_arr.indexOf('(');
                              let index2=sencond_arr.indexOf(')');
                              let change_arr=sencond_arr.substring(index1+1,index2);
                              if(change_arr!=res.attachment.shard_sync.jobs[m].step){
                                this.statusList[1].second[l].title=sencond_arr.substring(index1)+'('+res.attachment.shard_sync.jobs[m].step+')'
                              }
                              for (let k = 0; k < l; k++) { 
                                this.statusList[1].second[k].icon = 'el-icon-circle-check'
                                this.statusList[1].second[k].status = 'success'
                              }
                              for (let kk = 0; kk < v; kk++) { 
                                this.statusList[1].second[l].three[kk].icon = 'el-icon-circle-check'
                                this.statusList[1].second[l].three[kk].status = 'success'
                              }
                            }
                          }
                          
                        }
                      }
                    }
                    
                  }
                }
                }
              }
            } else if (res.attachment == null && res.error_code == '70001' && res.status == 'failed') {
              if (i > 5) {
                const statusgoing = {
                  title: info + '失败',
                  icon: 'el-icon-circle-close',
                  status: 'error',
                  description: res.error_info,
                  second: []
                }
                this.statusList.push(statusgoing)
                clearInterval(timer)
              }
            } else if (res.attachment == null && res.status == 'ongoing') {
              this.statusList = []
              let statusgoing = {}
              statusgoing = {
                title: '正在' + info,
                icon: 'el-icon-loading',
                status: 'process',
                description: '',
                second: []
              }
              this.statusList.push(statusgoing)
            } else if (res.attachment == null && res.status == 'done') {
              this.statusList = []
              let statusgoing = {}
              statusgoing = {
                title: info + '成功',
                icon: 'el-icon-circle-check',
                status: 'success',
                description: '',
                second: []
              }
              this.statusList.push(statusgoing)
              clearInterval(timer)
            } else {
              this.statusList = []
              let statusgoing = {}
              statusgoing = {
                title: info + '失败',
                icon: 'el-icon-circle-close',
                status: 'error',
                description: res.error_code,
                second: []
              }
              this.statusList.push(statusgoing)
              clearInterval(timer)
            }
          // }
        })
        if (i >= 86400) {
          clearInterval(timer)
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
.bold-text p{
  font-weight: bold;
}
.gtid{
  white-space: pre-wrap;
}
.red_clolor{
  color: red;
}
.line{
  text-decoration:underline;
}
.attion_fr{
  float: right;
}
.input_width{
  font-style:normal;
  margin-right: 10px;
   line-height: 30px;
}
.right_input{
  width: 50%;
}
.el-collapse{
  border-top: 1px solid #ffffff; 
  border-bottom: 1px solid #ffffff;
}
.el-collapse-item__header{
  height: 8px !important;
  line-height: 8px !important;
  border-bottom: 1px solid #ffffff ;
}
.el-collapse-item__arrow{
  margin: -36px 8px 0 auto ;
}
</style>