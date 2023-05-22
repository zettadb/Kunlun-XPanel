<template>
  <div class="app-container">
    <div class="filter-container">
      <div class="table-list-search-wrap">
        <el-input v-model="listQuery.hostaddr" class="list_search_keyword" placeholder="可输入ip搜索"
          @keyup.enter.native="handleFilter" />
        <el-select v-model="listQuery.master" placeholder="请选择主/备节点" class="list_search_select" style="width:150px;">
          <el-option label="主节点" value="source" />
          <el-option label="备节点" value="replica" />
        </el-select>
        <el-select v-model="listQuery.status" placeholder="请选择状态" class="list_search_select" style="width:150px;">
          <el-option label="运行中" value="active" />
          <el-option label="安装中" value="creating" />
          <el-option label="停止" value="manual_stop" />
          <el-option label="异常" value="inactive" />
        </el-select>
        <el-button icon="el-icon-search" @click="handleFilter">
          查询
        </el-button>
        <el-button icon="el-icon-refresh-right" @click="handleClear">
          重置
        </el-button>
        <el-button v-if="shard_create_priv === 'Y'" class="filter-item" type="primary" icon="el-icon-plus"
          @click="handleCreate">添加shard
        </el-button>
        <el-button v-if="storage_node_create_priv === 'Y'" class="filter-item" type="primary" icon="el-icon-plus"
          @click="handleCreateStorage">添加储存节点
        </el-button>
      </div>
    </div>
    <el-table ref="multipleTable" :key="tableKey" v-loading="listLoading" :data="list" border highlight-current-row
      default-expand-all style="width: 100%;margin-bottom: 20px;">
      <el-table-column type="expand" align="center" label="" width="50">
        <template slot-scope="scope">
          <el-table class="sectable" :data="scope.row.shardList" stripe style="width: 100%">
            <el-table-column type="index" label="序号" align="center" width="50" />
            <el-table-column prop="hostaddr" label="IP" align="center" width="120" />
            <el-table-column prop="port" label="端口" align="center" />
            <el-table-column prop="cpu_cores" label="CPU个数" align="center" />
            <el-table-column prop="master" label="主/备节点" align="center">
              <template slot-scope="scope">
                <span v-if="scope.row.master === 'true'" style="color: #409eff;">主</span>
                <span v-else-if="scope.row.master === 'false'">备</span>
                <span v-else />
              </template>
            </el-table-column>
            <el-table-column prop="delay" label="延迟时间" align="center">
              <template slot-scope="scope">
                <span>{{ scope.row.delay + 's' }}</span>
              </template>
            </el-table-column>
            <el-table-column prop="status" label="状态" align="center">
              <template slot-scope="scope">
                <span v-if="scope.row.status === 'online'" style="color: #00ed37">运行中</span>
                <span v-if="scope.row.status === 'creating'">安装中</span>
                <span v-if="scope.row.status === 'inactive'" style="color: red">异常</span>
                <span v-else-if="scope.row.status === 'offline'" style="color: #c7c9d1;">停止</span>
                <span v-else />
              </template>
            </el-table-column>
            <el-table-column prop="install_proxysql_addr" label="Proxy Address" align="center" width="120" />
            <el-table-column prop="install_proxysql_port" label="Proxy PORT" align="center" width="120" />
            <el-table-column label="操作" align="center" width="380" class-name="small-padding fixed-width">
              <template slot-scope="{row,$index}">
                <el-button v-if="row.status == 'online'" size="mini" type="primary" @click="nodeMonitor(row)">节点监控
                </el-button>
                <el-button v-if="row.status == 'online'" size="mini" type="primary" @click="handleSetCpu(row)">设置
                </el-button>
                <el-button v-if="row.status !== 'online'" size="mini" type="primary"
                  @click="handleControlNode(row, 'start')">启用
                </el-button>
                <el-button v-if="row.master !== 'true' && row.status !== 'offline'" size="mini" type="primary"
                  @click="handleControlNode(row, 'stop')">禁用
                </el-button>
                <el-button v-if="row.master !== 'true'" size="mini" type="primary"
                  @click="handleControlNode(row, 'restart')">重启
                </el-button>
                <el-button v-if="row.master == 'true'" size="mini" type="primary" @click="handleSwitch(row)">主备切换
                </el-button>
                <el-button v-if="row.master == 'true'" size="mini" type="primary" @click="handleReDo(row)">重做备机节点
                </el-button>
                <el-button v-if="storage_node_drop_priv === 'Y' && row.master !== 'true'" size="mini" type="danger"
                  @click="handleDeleteStorage(row, $index)">删除
                </el-button>
              </template>
            </el-table-column>
          </el-table>
        </template>
      </el-table-column>

      <el-table-column type="index" align="center" label="序号" width="50" />
      <el-table-column prop="shardID" label="shardID" align="center">
        <template slot-scope="{row}">
          <span class="link-type" @click="handleDetail(row)">{{ row.id }}</span>
        </template>
      </el-table-column>
      <el-table-column prop="db_cluster_id" align="center" label=" 集群ID" />
      <el-table-column prop="name" align="center" label="shard名称" />
      <el-table-column prop="num_nodes" align="center" label="副本数" />
      <el-table-column prop="degrade_conf_state" align="center" label="是否退化">
        <template slot-scope="scope">
          <span v-if="scope.row.degrade_conf_state === 'ON'">是</span>
          <span v-else-if="scope.row.degrade_conf_state === 'OFF'">否</span>
          <span v-else />
        </template>
      </el-table-column>
       <el-table-column prop="degrade_conf_time" align="center" label="退化时间" >
          <template slot-scope="scope">
            <span v-if="scope.row.degrade_conf_time === '0'"></span>
            <span v-else >{{scope.row.degrade_conf_time}}</span>
          </template>
       </el-table-column>
      <el-table-column label="操作" align="center" width="150" class-name="small-padding fixed-width">
        <template slot-scope="{row,$index}">
          <el-button v-if="shard_drop_priv === 'Y'" size="mini" type="primary" @click="handleUpdate(row)">编辑</el-button>
          <el-button v-if="shard_drop_priv === 'Y'" size="mini" type="danger" @click="handleDelete(row, $index)">删除</el-button>
        </template>
      </el-table-column>
    </el-table>

    <pagination v-show="total > 0" :total="total" :page.sync="listQuery.pageNo" :limit.sync="listQuery.pageSize"
      @pagination="getList" />
    <!--添加shard -->
    <el-dialog :title="textMap[dialogStatus]" :visible.sync="dialogNodeVisible" custom-class="single_dal_view"
      :close-on-click-modal="false">
      <el-form ref="dataForm" :model="temp" :rules="rules" label-position="left" label-width="130px">
        <el-form-item label="集群名称:" prop="name">
          <span>{{ temp.name }}</span>
        </el-form-item>
        <el-form-item label="业务名称:" prop="nick_name">
          <span>{{ temp.nick_name }}</span>
        </el-form-item>
        <el-form-item label="选择计算机:" prop="machinelist">
          <el-select v-model="temp.machinelist" multiple placeholder="请选择">
            <el-option v-for="machine in strogemachines" :key="machine.id" :label="machine.hostaddr"
              :value="machine.hostaddr" />
          </el-select>
        </el-form-item>
        <el-form-item v-if="dialogStatus === 'createstorage'" label="shard名称:" prop="shard_name">
          <el-select v-if="dialogStatus === 'createstorage' ? true : false" v-model="temp.shard_name"
            placeholder="请选择shard名称">
            <el-option v-for="shard in shard_arr" :key="shard.id" :label="shard.name" :value="shard.id" />
          </el-select>
        </el-form-item>
        <el-form-item v-if="dialogStatus === 'create'" label="shard个数:" prop="shards_count">
          <el-input v-model="temp.shards_count" class="right_input" placeholder="请输入shard个数">
            <i slot="suffix" style="font-style:normal;margin-right: 10px; line-height: 30px;">个</i>
          </el-input>
        </el-form-item>
        <el-form-item :label="label_node" prop="nodes">
          <el-input v-model="temp.nodes" class="right_input" :placeholder="placeholder_node">
            <i slot="suffix" style="font-style:normal;margin-right: 10px; line-height: 30px;">个</i>
          </el-input>
        </el-form-item>
        <el-form-item style="color:red;" v-show="if_show==='true'"><p>该集群已建立RCR关系</p></el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="dialogNodeVisible = false">关闭</el-button>
        <el-button type="primary" @click="dialogStatus === 'create' ? createData() : createStorageData()">确认</el-button>
      </div>
    </el-dialog>
    <!--添加删除状态框 -->
    <el-dialog :title="job_id" :visible.sync="dialogStatusShowVisible" custom-class="single_dal_view"
      :close-on-click-modal="false" :before-close="beforeDestory">
      <div style="width: 100%;background: #fff;padding:0 20px;">
        <el-steps direction="vertical" :active="init_active">
          <el-step v-if="init_show" :title="init_title" icon="el-icon-more" />
          <el-step v-if="shard_show" :title="shard_title" :status="storage_state" :icon="shard_icon"
            :description="shard_description">
            <template slot="description">
              <span>{{ shard_description }}</span>
              <div style="padding:20px;">
                <el-steps direction="vertical" :active="shard_active">
                  <el-step v-for="(item, index) of shard" :key="index" :title="item.title" :icon="item.icon"
                    :status="item.status" :description="item.description" @click.native="thisDetail(item.shard_id)" />
                </el-steps>
              </div>
            </template>
          </el-step>
          <el-step v-if="finish_show" :title="finish_title" :icon="finish_icon" :description="finish_description"
            :status="finish_state" />
        </el-steps>
      </div>
    </el-dialog>
    <!--  状态框 -->
    <el-dialog :visible.sync="dialogStatusVisible" custom-class="single_dal_view" width="400px"
      :close-on-click-modal="false" :before-close="beforeSyncDestory">
      <div class="block">
        <el-timeline>
          <el-timeline-item v-for="(activity, index) in activities" :key="index" :icon="activity.icon"
            :type="activity.type" :color="activity.color" :size="activity.size" :timestamp="activity.timestamp">
            {{ activity.content }}
          </el-timeline-item>
        </el-timeline>
      </div>
    </el-dialog>
    <!--shard信息框 -->
    <el-dialog :title="dialogStatus" :visible.sync="dialogShardInfo" custom-class="single_dal_view"
      :close-on-click-modal="false">
      <json-viewer :value="shardInfo" />
    </el-dialog>
    <!--主备切换-->
    <el-dialog title="主备切换" :visible.sync="dialogSwitchOVisible" custom-class="single_dal_view">
      <el-form ref="switchForm" :model="switchtemp" :rules="rules" label-position="left" label-width="100px">
        <el-form-item label="主节点:" prop="primary_node">
          <span>{{ switchtemp.primary_node }}</span>
        </el-form-item>
        <el-form-item label="备机节点:" prop="replica">
          <el-select v-model="switchtemp.replica" clearable placeholder="请选择备机节点">
            <el-option v-for="item in options" :key="item.value" :label="item.label" :value="item.label" />
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
      <el-form ref="redoForm" :model="redotemp" :rules="rules" label-position="left" label-width="180px">
        <el-form-item label="需重做的备机节点:" prop="redolist">
          <el-select v-model="redotemp.redolist" multiple placeholder="请选择" @change="change">
            <el-option v-for="item in options" :key="item.value" :label="item.label" :value="item.label" />
          </el-select>
        </el-form-item>

        <el-form-item label="是否从主节点上拉取数据:" prop="allow_pull_from_master">
          <el-radio v-model="redotemp.allow_pull_from_master" label="1">是</el-radio>
          <el-radio v-model="redotemp.allow_pull_from_master" label="0">否</el-radio>
        </el-form-item>
        <el-form-item v-if="redotemp.allow_pull_from_master == '0'" label="主备延迟:" prop="allow_replica_delay">
          <el-input v-model="redotemp.allow_replica_delay" class="right_input" placeholder="主备延迟">
            <i slot="suffix" style="font-style:normal;margin-right: 10px; line-height: 30px;">s</i>
          </el-input>
        </el-form-item>
        <el-form-item label="是否备份:" prop="need_backup">
          <el-radio v-model="redotemp.need_backup" label="1">是</el-radio>
          <el-radio v-model="redotemp.need_backup" label="0">否</el-radio>
        </el-form-item>
        <el-form-item v-if="redotemp.need_backup == '1'" label="备份存储目标:" prop="hdfs_host">
          <el-select v-model="redotemp.hdfs_host" placeholder="请选择">
            <el-option v-for="item in hdfs_options" :key="item.value" :label="item.label" :value="item.label" />
          </el-select>
        </el-form-item>
        <el-form-item label="限速:" prop="pv_limit">
          <el-input v-model="redotemp.pv_limit" class="right_input" placeholder="限速">
            <i slot="suffix" style="font-style:normal;margin-right: 10px; line-height: 30px;">KB/s</i>
          </el-input>
        </el-form-item>

      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="dialogRedoVisible = false">关闭</el-button>
        <el-button type="primary" @click="redoData(redotemp)">确认</el-button>
      </div>
    </el-dialog>
    <!-- 主备切换状态框 -->
    <el-dialog :title="job_id" :visible.sync="dialogStatusFVisible" custom-class="single_dal_view"
      :close-on-click-modal="false" :before-close="beforeSwitchDestory">
      <div style="height: 400px;">
        <el-steps direction="vertical" :active="active" finish-status="success">
          <el-step v-for="(item, index) of stepParams" :key="index" :title="item.title" :icon="item.icon"
            :status="item.status" :description="item.description" :class="stepSuc.includes(index) ? 'stepSuc' : 'stepErr'"
            @click.native="handleStep(index)" />
        </el-steps>
      </div>

    </el-dialog>
    <!-- 重做备机状态框 -->
    <el-dialog :title="job_id" :visible.sync="dialogStatusRedoVisible" custom-class="single_dal_view"
      :close-on-click-modal="false" :before-close="beforeRedoDestory">
      <div style="width: 100%;background: #fff;padding:0 20px;" class="block">
        <el-steps direction="vertical" :active="init_active">
          <el-step v-for="(items, key) in statusList" :key="key" :title="items.title" :icon="items.icon"
            :status="items.status" :description="items.description">
            <template slot="description">
              <span>{{ items.description }}</span>
              <div style="padding:20px;">
                <el-steps direction="vertical" :active="second_active">
                  <el-step v-for="(item, index) of statusList[key].second" :key="index" :title="item.title"
                    :icon="item.icon" :status="item.status" :description="item.description" />
                </el-steps>
              </div>
            </template>
          </el-step>
        </el-steps>
      </div>
    </el-dialog>
    <!--  cpu 隔离设置  -->
    <el-dialog title="CPU资源设置" :visible.sync="dialogCpuVisible" custom-class="single_dal_view">
      <el-form ref="cpuForm" :model="cpu_paras" :rules="rules" label-position="left" label-width="100px">
        <el-form-item label="节点IP:" prop="cpu_paras.hostaddr">
          <span>{{ cpu_paras.hostaddr }}</span>
        </el-form-item>
        <el-form-item label="端口:" prop="cpu_paras.port">
          <span>{{ cpu_paras.port }}</span>
        </el-form-item>
        <el-form-item label="cpu 个数:" prop="cpu_paras.cpu_cores">
          <el-input v-model="cpu_paras.cpu_cores" class="right_input" placeholder="请输入cpu个数">
            <i slot="suffix" style="font-style:normal;margin-right: 10px; line-height: 30px;">个</i>
          </el-input>
        </el-form-item>
        <el-form-item label="cpu模式:" prop="cpu_paras.cgroup_mode">
          <el-select v-model="cpu_paras.cgroup_mode" clearable placeholder="资源限制模式">
            <el-option v-for="item in cpu_paras_option" :key="item.value" :label="item.label" :value="item.label" />
          </el-select>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="dialogCpuVisible = false">关闭</el-button>
        <el-button type="primary" @click="SaveCpuSetData(cpu_paras)">确认</el-button>
      </div>
    </el-dialog>
    <el-dialog :title="textMap[dialogStatus]" :visible.sync="dialogChangeVisible" custom-class="single_dal_view"
      :close-on-click-modal="false">
      <el-form ref="editForm" :model="shardEdit" :rules="rules" label-position="left" label-width="130px">
        <el-form-item label="集群ID:" prop="db_cluster_id">
          <span>{{ shardEdit.db_cluster_id }}</span>
        </el-form-item>
        <el-form-item label="shard名称:" prop="shard_name">
          <span>{{ shardEdit.shard_name }}</span>
        </el-form-item>
        <el-form-item  label="shard是否退化:" prop="conf_degrade_state">
          <el-radio v-model="shardEdit.conf_degrade_state" label="ON">是</el-radio>
          <el-radio v-model="shardEdit.conf_degrade_state" label="OFF">否</el-radio>
        </el-form-item>
        <el-form-item label="退化时间:" prop="degrade_conf_time" v-if="shardEdit.conf_degrade_state=='ON'">
          <el-input v-model="shardEdit.degrade_conf_time" class="right_input" placeholder="退化时间范围为5-500s">
            <i slot="suffix" style="font-style: normal; margin-right: 10px; line-height: 30px">s</i>
          </el-input>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="dialogChangeVisible = false">关闭</el-button>
        <el-button type="primary" @click=" updateData()">确认</el-button>
      </div>
    </el-dialog>
  </div>
</template>
<script>
import { messageTip, createCode, gotoCofirm, getNowDate } from '@/utils'
import {
  shardList,
  getEvStatus,
  getStroMachine,
  addShards,
  getShardsCount,
  delShard,
  getShardsJobLog,
  getShards,
  addNodes,
  getNodesCount,
  delSnode,
  controlInstance,
  getStandbyNode,
  getShardPrimary,
  switchShard,
  getBackupStorageList,
  rebuildNode, SetCpuCgroup,updateShard
} from '@/api/cluster/list'
import { version_arr, timestamp_arr, ip_arr } from '@/utils/global_variable'
import Pagination from '@/components/Pagination'
import JsonViewer from 'vue-json-viewer'
import { mysqlDashboard } from '@/api/grafana/list'
import { getRCRRelater } from '@/api/rcr/list'

export default {
  name: 'Complist',
  components: { Pagination, JsonViewer },
  props: {
    listsent: { typeof: Object }
  },
  data() {
    const validateNodes = (rule, value, callback) => {
      if (!value) {
        const string = this.label_node.substr(0, this.label_node.length - 1)
        callback(new Error('请输入' + string))
      } else if (!(/^[0-9]+$/.test(value))) {
        callback(new Error(string + '只能输入数字'))
      } else if (value > 256) {
        callback(new Error(string + '不能大于256'))
      } else {
        callback()
      }
    }
    const validateShardsCount = (rule, value, callback) => {
      if (!value) {
        callback(new Error('请输入shard个数'))
      } else if (!(/^[0-9]+$/.test(value))) {
        callback(new Error('shard个数只能输入数字'))
      } else if (value > 256) {
        callback(new Error('shard个数不能大于256'))
      } else {
        callback()
      }
    }
    const validateShardName = (rule, value, callback) => {
      if (!value) {
        callback(new Error('请选择shard名称'))
      } else {
        callback()
      }
    }
    const validateredolist = (rule, value, callback) => {
      if (value.length === 0) {
        callback(new Error('请选择需重做的备机节点'))
      } else if (value.length == this.options.length) {
        this.redotemp.allow_pull_from_master = '1'
        callback()
      } else {
        callback()
      }
    }
    const validatereplicadelay = (rule, value, callback) => {
      if (!value) {
        callback(new Error('主备延迟不能为空'))
      } else if (!(/^[0-9]+$/.test(value))) {
        callback(new Error('主备延迟只能输入数字'))
      } else {
        callback()
      }
    }
    const validatehdfshost = (rule, value, callback) => {
      if (value.length === 0) {
        callback(new Error('请选择备份存储目标'))
      } else {
        callback()
      }
    }

    const validatemaxtime = (rule, value, callback) => {
      if (!value) {
        callback(new Error('最大延迟时间不能为空'))
      } else if (!(/^[0-9]+$/.test(value))) {
        callback(new Error('最大延迟时间只能输入数字'))
      } else {
        callback()
      }
    }
    const validateDegradeTime = (rule, value, callback) => {
      if (value.length == 0) {
        callback(new Error('请输入退化时间'))
      } else if (!/^[0-9]+$/.test(value)) {
        callback(new Error('退化时间只能输入数字'))
      } else if (value > 500) {
        callback(new Error('退化时间不能大于500'))
      } else if (value < 5) {
        callback(new Error('退化时间不能小于5'))
      } else {
        callback()
      }
    }

    return {
      tableKey: 0,
      // list: null,
      list: [],
      listLoading: true,
      searchLoading: false,
      total: 0,
      listQuery: {
        pageNo: 1,
        pageSize: 10,
        hostaddr: '',
        id: '',
        ha_mode: '',
        master: '',
        status: ''
      },
      temp: {
        nodes: '',
        name: '',
        nick_name: '',
        machinelist: '',
        cluster_id: '',
        shards_count: '',
        shard_name: ''
      },
      redotemp: {
        redolist: [],
        allow_pull_from_master: '0',
        allow_replica_delay: '30',
        need_backup: '0',
        pv_limit: '10',
        hdfs_host: ''
      },
      switchtemp: {
        replica: '',
        primary_node: '',
        shard_id: '',
        cluster_id: ''
      },
      cpu_paras: {
        hostaddr: '',
        port: '',
        type: 'mysql',
        cpu_cores: '5',
        cgroup_mode: 'quota'
      },
      cpu_paras_option: [
        {
          value: 'share',
          label: 'share'
        },
        {
          value: 'quota',
          label: 'quota'
        }
      ],
      options: [],
      hdfs_options: [],
      active: 0,
      // 已选步骤
      stepSuc: [0],
      // 步骤参数
      stepParams: [],
      init_active: 0,
      second_active: 0,
      statusList: [],
      ipList: [],
      dialogFormVisible: false,
      dialogEditVisible: false,
      dialogUploadVisible: false,
      dialogSwitchOVisible: false,
      dialogCpuVisible: false,
      dialogRedoVisible: false,
      dialogStatusFVisible: false,
      dialogStatusRedoVisible: false,
      dialogStatus: '',
      textMap: {
        create: '添加shard',
        createstorage: '添加存储节点',
        detail: '详情',
        shardupdate: '编辑shard'
      },
      dialogDetail: false,
      dialogNodeVisible: false,
      dialogChangeVisible: false,
      message_tips: '',
      message_type: '',
      installStatus: false,
      info: '',
      row: {},
      shard_create_priv: JSON.parse(sessionStorage.getItem('priv')).shard_create_priv,
      shard_drop_priv: JSON.parse(sessionStorage.getItem('priv')).shard_drop_priv,
      storage_node_create_priv: JSON.parse(sessionStorage.getItem('priv')).storage_node_create_priv,
      storage_node_drop_priv: JSON.parse(sessionStorage.getItem('priv')).storage_node_drop_priv,
      storage_status: false,
      comp_status: false,
      comp_active: 1,
      job_id: null,
      timer: null,
      strogemachines: [],
      shard: [],
      storage_state: '',
      finish_state: '',
      shard_icon: '',
      shard_title: '',
      shard_active: 0,
      init_title: '',
      init_show: true,
      finish_show: false,
      computer_show: true,
      shard_show: true,
      finish_title: '',
      finish_icon: '',
      finish_description: '',
      shard_description: '',
      shardInfo: '',
      dialogShardInfo: false,
      dialogStatusShowVisible: false,
      shard_arr: [],
      label_node: '',
      placeholder_node: '',
      dialogStatusVisible: false,
      activities: [],
      //suppliers:[],   //保存数据    
      showInput: "", //用来判断是否显示哪个单元格
      oldData: {}, //用来存修改前的数据
      currentData: {},  //用来保存新的数据  
      suppliers:[{
          value: 'ON',
          label: '是'
        }, {
          value: 'OFF',
          label: '否'
        }],
      shardEdit:{
        shardid:'',
        db_cluster_id:'',
        shard_name:'',
        conf_degrade_state:'',
        degrade_conf_time:''
      },
      if_show:'false',
      rules: {
        nodes: [
          { required: true, trigger: 'blur', validator: validateNodes }
        ],
        shards_count: [
          { required: true, trigger: 'blur', validator: validateShardsCount }
        ],
        shard_name: [
          { required: true, trigger: 'blur', validator: validateShardName }
        ],
        redolist: [
          { required: true, trigger: 'blur', validator: validateredolist }
        ],
        allow_replica_delay: [
          { required: true, trigger: 'blur', validator: validatereplicadelay }
        ],
        hdfs_host: [
          { required: true, trigger: 'blur', validator: validatehdfshost }
        ],
        maxtime: [
          { required: true, trigger: 'blur', validator: validatemaxtime }
        ],
        conf_degrade_state: [
          { required: true, trigger: 'blur', message: 'shard是否退化必选'  }
        ],
        conf_degrade_time: [
          { required: true, trigger: 'blur', validator: validateDegradeTime}
        ],
      }
    }
  },
  created() {
    this.listQuery.id = this.listsent.id
    this.listQuery.ha_mode = this.listsent.ha_mode
    this.getList()
  },
  destroyed() {
    clearInterval(this.timer)
    this.timer = null
  },
  methods: {
    handleUpdate(row) {
      this.shardEdit.shardid = row.id; 
      this.shardEdit.db_cluster_id=row.db_cluster_id;
      this.shardEdit.shard_name=row.name;
      this.shardEdit.conf_degrade_state=row.degrade_conf_state;
      this.shardEdit.degrade_conf_time=row.degrade_conf_time;
      if(row.degrade_conf_time=='0'){
        this.shardEdit.degrade_conf_time='25';
      }
      this.dialogStatus = "shardupdate";
      this.dialogChangeVisible = true;
      this.$nextTick(() => {
        this.$refs["editForm"].clearValidate();
      });
    },
    updateData() {
      this.$refs["editForm"].validate((valid) => {
        if (valid) {
          const tempData = {}
          tempData.user_name = sessionStorage.getItem('login_username')
          tempData.job_id = ''
          tempData.job_type = 'update_shard_degrade_conf'
          tempData.version = version_arr[0].ver
          tempData.timestamp = timestamp_arr[0].time + ''
          const paras = {}
          paras.cluster_id = this.shardEdit.db_cluster_id
          paras.shard_id =  this.shardEdit.shardid
          paras.conf_degrade_state =  this.shardEdit.conf_degrade_state
          if(this.shardEdit.conf_degrade_state=='OFF'){
            this.shardEdit.degrade_conf_time='0'
          }
          paras.conf_degrade_time =  this.shardEdit.degrade_conf_time
          tempData.paras = paras
          //console.log(tempData);return;
          updateShard(tempData).then((response) => {
            let res = response;
            if(res.status=='done'){
              this.dialogChangeVisible = false;
              this.message_tips = '编辑成功';
              this.message_type = 'success';
              messageTip(this.message_tips,this.message_type);
              this.getList()
            }
            else{
              this.message_tips = res.error_info;
              this.message_type = 'error';
              messageTip(this.message_tips,this.message_type);
            }

          });
        }
      });
    },
    tableDbEdit(row, column, event) {
      this.showInput = column.property + row.inboundId;
      this.oldData[column.property] = row[column.property];
    },
    
    beforeSwitchDestory() {
      // console.log('00:00');
      clearInterval(this.timer)
      this.dialogStatusFVisible = false
      this.timer = null
    },
    beforeRedoDestory() {
      clearInterval(this.timer)
      this.dialogStatusRedoVisible = false
      this.timer = null
    },
    redoData(row) {
      this.$refs['redoForm'].validate((valid) => {
        if (valid) {
          const tempData = {}
          tempData.user_name = sessionStorage.getItem('login_username')
          tempData.job_id = ''
          tempData.job_type = 'rebuild_node'
          tempData.version = version_arr[0].ver
          tempData.timestamp = timestamp_arr[0].time + ''
          const rebuild = []
          const ipList = []
          // console.log(this.redotemp.redolist.length);return;
          for (let i = 0; i < this.redotemp.redolist.length; i++) {
            const ip_arr = (this.redotemp.redolist[i].substring(0, this.redotemp.redolist[i].length - 1)).split('(')
            let pv_limit = ''
            if (!this.redotemp.pv_limit) {
              pv_limit = '10'
            } else {
              pv_limit = this.redotemp.pv_limit
            }
            const rb_paras = {}
            if (this.redotemp.need_backup == '1') {
              const hdfs_host = (this.redotemp.hdfs_host.substring(0, this.redotemp.hdfs_host.length - 1)).replace('(', ':')
              rb_paras.hostaddr = ip_arr[0]
              rb_paras.port = ip_arr[1]
              rb_paras.need_backup = this.redotemp.need_backup
              rb_paras.hdfs_host = hdfs_host
              rb_paras.pv_limit = pv_limit
            } else {
              rb_paras.hostaddr = ip_arr[0]
              rb_paras.port = ip_arr[1]
              rb_paras.need_backup = this.redotemp.need_backup
              rb_paras.pv_limit = pv_limit
            }
            rebuild.push(rb_paras)
            ipList.push({
              title: ip_arr[0] + '_' + ip_arr[1],
              error_code: '0',
              error_info: '',
              status: 'no_start',
              step: ''
            })
          }
          const paras = {}
          if (this.redotemp.allow_pull_from_master == '1') {
            paras.shard_id = this.redotemp.shard_id,
              paras.cluster_id = this.redotemp.cluster_id,
              paras.allow_pull_from_master = this.redotemp.allow_pull_from_master,
              paras.rb_nodes = rebuild
          } else {
            paras.shard_id = this.redotemp.shard_id
            paras.cluster_id = this.redotemp.cluster_id
            paras.allow_pull_from_master = this.redotemp.allow_pull_from_master
            paras.allow_replica_delay = this.redotemp.allow_replica_delay
            paras.rb_nodes = rebuild
          }
          tempData.paras = paras
          this.dialogStatusRedoVisible = true
          this.dialogRedoVisible = false
          // console.log(tempData);return;
          // 发送接口
          rebuildNode(tempData).then(response => {
            const res = response
            if (res.status == 'accept') {
              this.dialogRedoVisible = false
              this.stepParams = []
              let i = 0
              const info = '重做备机节点'
              this.statusList = []
              this.timer = null
              this.init_active = 0
              this.second_active = 0
              // 传重做备机节点的ipList
              this.getFStatus(this.timer, res.job_id, i++, info, ipList)
              this.timer = setInterval(() => {
                this.getFStatus(this.timer, res.job_id, i++, info, ipList)
              }, 5000)
            } else {
              this.message_tips = res.error_info
              this.message_type = 'error'
              messageTip(this.message_tips, this.message_type)
            }
          })
        }
      })
    },
    SaveCpuSetData(row) {
      this.$refs['cpuForm'].validate((valid) => {
        if (valid) {
          console.log(valid)
          const tempData = {
            version: '1.0',
            job_id: '',
            job_type: 'update_instance_cgroup',
            timestamp: timestamp_arr[0].time + '',
            user_name: sessionStorage.getItem('login_username'),
            paras: {
              ip: row.hostaddr,
              port: row.port,
              type: 'mysql',
              cpu_cores: row.cpu_cores,
              cgroup_mode: row.cgroup_mode
            }
          }
          // 发送接口
          SetCpuCgroup(tempData).then(response => {
            const res = response
            if (res.status == 'accept') {
              let i = 0
              this.dialogSwitchOVisible = false
              this.job_id = ''
              this.timer = null
              const info = '重做备机节点'
              this.timer = setInterval(() => {
                this.getFStatus(this.timer, res.job_id, i++, info, '')
              }, 1000)

              setTimeout(() => {
                this.dialogCpuVisible = false
                this.message_type = 'success'
                this.message_tips = '修改成功'
                messageTip(this.message_tips, this.message_type)
                this.getList()
              }, 1000)
            } else {
              this.message_tips = res.error_info
              this.message_type = 'error'
              messageTip(this.message_tips, this.message_type)
            }
          })
        }
      })
    },
    switchData(row) {
      this.$refs['switchForm'].validate((valid) => {
        if (valid) {
          // this.dialogStatusVisible=true;
          const tempData = {}
          tempData.user_name = sessionStorage.getItem('login_username')
          tempData.job_id = ''
          tempData.job_type = 'manual_switch'
          tempData.version = version_arr[0].ver
          tempData.timestamp = timestamp_arr[0].time + ''
          const master_ip = (this.switchtemp.primary_node.substring(0, this.switchtemp.primary_node.length - 1)).replace('(', '_')
          const paras = {}
          if (!this.switchtemp.replica.length) {
            paras.shard_id = this.switchtemp.shard_id
            paras.cluster_id = this.switchtemp.cluster_id
            paras.master_hostaddr = master_ip
          } else {
            const assign_ip = (this.switchtemp.replica.substring(0, this.switchtemp.replica.length - 1)).replace('(', '_')
            paras.shard_id = this.switchtemp.shard_id
            paras.cluster_id = this.switchtemp.cluster_id
            paras.master_hostaddr = master_ip
            paras.assign_hostaddr = assign_ip
          }
          tempData.paras = paras
          // console.log(tempData);return;
          // 发送接口
          switchShard(tempData).then(response => {
            const res = response
            if (res.status == 'accept') {
              this.dialogSwitchOVisible = false
              this.stepParams = []
              let i = 0
              const info = '主备切换'
              this.computer = []
              this.shard = []
              this.computer_state = ''
              this.storage_state = ''
              this.computer_title = ''
              this.computer_icon = ''
              this.shard_icon = ''
              this.shard_title = ''
              this.comp_active = 0
              this.shard_active = 0
              this.strogemachines = []
              this.init_title = ''
              this.init_active = 0
              this.finish_title = ''
              this.finish_icon = ''
              this.finish_description = ''
              this.computer_description = ''
              this.shard_description = ''
              this.job_id = ''
              this.timer = null
              this.timer = setInterval(() => {
                this.getFStatus(this.timer, res.job_id, i++, info, '')
              }, 1000)
            } else {
              this.message_tips = res.error_info
              this.message_type = 'error'
              messageTip(this.message_tips, this.message_type)
            }
          })
        }
      })
    },
    change() {
      if (this.options.length == this.redotemp.redolist.length) {
        this.redotemp.allow_pull_from_master = '1'
      } else {
        this.redotemp.allow_pull_from_master = '0'
      }
    },
    handleReDo(row) {
      this.resetReDoTemp()
      this.dialogRedoVisible = true
      this.redotemp.shard_id = row.shard_id
      this.redotemp.cluster_id = row.cluster_id
      getBackupStorageList().then(response => {
        this.hdfs_options = []
        if (response.list !== null) {
          for (let i = 0; i < response.list.length; i++) {
            const arr = { 'value': i, 'label': response.list[i].hostaddr + '(' + response.list[i].port + ')' }
            this.hdfs_options.push(arr)
          }
        }
      })
      // 获取备机节点
      const temps = {}
      temps.cluster_id = row.cluster_id
      temps.shard_id = row.shard_id
      temps.ha_mode = this.listsent.ha_mode
      getStandbyNode(temps).then((res) => {
        if (res.code == 200) {
          this.options = []
          if (res.list.length > 0) {
            for (let i = 0; i < res.list.length; i++) {
              const arr = { 'value': i, 'label': res.list[i].ip + '(' + res.list[i].port + ')' }
              this.options.push(arr)
            }
          }
        }
      })
    },
    handleSwitch(row) {
      this.resetSwitchTemp()
      this.dialogSwitchOVisible = true
      // 获取主节点
      const temp = {}
      temp.cluster_id = row.cluster_id
      temp.shard_id = row.shard_id
      temp.ha_mode = this.listsent.ha_mode
      this.switchtemp.shard_id = row.shard_id
      this.switchtemp.cluster_id = row.cluster_id
      getShardPrimary(temp).then((res) => {
        if (res.list.length > 0) {
          this.switchtemp.primary_node = res.list[0].ip + '(' + res.list[0].port + ')'
        }
      })
      // 获取备机节点
      const temps = {}
      temps.cluster_id = row.cluster_id
      temps.shard_id = row.shard_id
      temps.ha_mode = this.listsent.ha_mode
      getStandbyNode(temps).then((res) => {
        if (res.code == 200) {
          this.options = []
          if (res.list.length > 0) {
            for (let i = 0; i < res.list.length; i++) {
              const arr = { 'value': i, 'label': res.list[i].ip + '(' + res.list[i].port + ')' }
              this.options.push(arr)
            }
          }
        }
      })
    },
    handleSetCpu(row) {
      console.log(row)
      this.dialogCpuVisible = true
      // 获取主节点
      this.cpu_paras = Object.assign(this.cpu_paras, row)
      console.log(this.cpu_paras)
    },
    nodeMonitor(row) {
      const mparas = {}
      mparas['cluster_id'] = row.cluster_id
      mparas['hostaddr'] = row.hostaddr
      mparas['port'] = row.port
      mparas['user_name'] = sessionStorage.getItem('login_username')
      mysqlDashboard(mparas).then((myres) => {
        if (myres.code == '200') {
          window.open(ip_arr[0].ip + myres.url + '?orgId=1&refresh=5s')
        }
      })
    },
    thisDetail(id) {
      const temp = { id: id }
      getShardsJobLog(temp).then((response) => {
        this.dialogStatus = 'shard_' + id + '详细信息'
        this.dialogShardInfo = true
        this.shardInfo = response.shard_nodes
      })
    },
    // 清除定时器
    beforeDestory() {
      clearInterval(this.timer)
      this.dialogStatusShowVisible = false
      this.timer = null
    },
    beforeSyncDestory() {
      clearInterval(this.timer)
      this.dialogStatusVisible = false
      this.timer = null
    },
    handleFilter() {
      this.listQuery.pageNo = 1
      this.getList()
    },
    handleClear() {
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
      const queryParam = Object.assign({}, this.listQuery)
      // 模糊搜索
      shardList(queryParam).then(response => {
        this.list = response.list
        this.total = response.total
        setTimeout(() => {
          this.listLoading = false
        }, 0.5 * 1000)
      })
    },
    resetTemp() {
      this.temp = {
        nodes: '',
        name: '',
        nick_name: '',
        machinelist: '',
        cluster_id: '',
        shards_count: '1',
        shard_name: ''
      }
    },
    resetSwitchTemp() {
      this.switchtemp = {
        replica: '',
        primary_node: '',
        shard_id: '',
        cluster_id: ''
      }
    },
    resetReDoTemp() {
      this.redotemp = {
        redolist: [],
        allow_pull_from_master: '0',
        allow_replica_delay: '30',
        need_backup: '0',
        pv_limit: '10',
        hdfs_host: ''
      }
    },
    handleCreate() {
      this.resetTemp()
      this.dialogStatus = 'create'
      this.label_node = '副本数:'
      this.placeholder_node = '请输入副本数'
      this.temp.nodes = '3'
      this.dialogNodeVisible = true
      this.temp.name = this.listsent.name
      this.temp.nick_name = this.listsent.nick_name
      this.temp.cluster_id = this.listsent.id
      // 获取存储机器
      getStroMachine().then((res) => {
        this.strogemachines = []
        this.strogemachines = res.list
      })
      //判断是否为rcr关系
      let tempdate={cluater_id:this.listsent.id};
      getRCRRelater(tempdate).then((response) => {
        if(response.total==0){
          this.if_show='false';
        }else{
          this.if_show='true';
        }
      })
      this.$nextTick(() => {
        this.$refs.dataForm.clearValidate()
      })
    },
    createData() {
      this.$refs['dataForm'].validate((valid) => {
        if (valid) {
          const tempData = {}
          tempData.user_name = sessionStorage.getItem('login_username')
          tempData.job_id = ''
          tempData.job_type = 'add_shards'
          tempData.version = version_arr[0].ver
          tempData.timestamp = timestamp_arr[0].time + ''
          const machinelist = []
          this.temp.machinelist.forEach(item => {
            machinelist.push(item)
          })
          const paras = {}
          paras.nodes = this.temp.nodes
          paras.cluster_id = this.temp.cluster_id
          paras.nick_name = this.temp.nick_name
          paras.shards = this.temp.shards_count
          if (machinelist.length > 0) {
            paras.storage_iplists = machinelist
          }
          tempData.paras = paras
          // console.log(tempData);return;
          // 新增shard
          addShards(tempData).then((response) => {
            const res = response
            if (res.status == 'accept') {
              this.dialogNodeVisible = false
              this.dialogStatusShowVisible = true
              // 把之前的数据清空
              this.shard = []
              this.storage_state = ''
              this.shard_icon = ''
              this.shard_title = ''
              this.shard_active = 0
              this.strogemachines = []
              this.init_title = ''
              this.init_active = 0
              this.finish_title = ''
              this.finish_icon = ''
              this.finish_description = ''
              this.shard_description = ''
              this.job_id = ''
              this.timer = null
              this.init_show = true
              this.finish_show = false
              this.finish_state = ''
              const info = '添加shard'
              let i = 0
              this.getFStatus(this.timer, res.job_id, i++, info, '')
              this.timer = setInterval(() => {
                this.getFStatus(this.timer, res.job_id, i++, info, '')
              }, 5000)
            } else if (res.status == 'ongoing') {
              this.message_tips = '系统正在操作中，请等待一会！'
              this.message_type = 'error'
              messageTip(this.message_tips, this.message_type)
            } else {
              this.message_tips = res.error_info
              this.message_type = 'error'
              messageTip(this.message_tips, this.message_type)
            }
          })
        }
      })
    },
    handleCreateStorage() {
      this.resetTemp()
      this.dialogStatus = 'createstorage'
      this.label_node = '存储节点个数:'
      this.placeholder_node = '请输入存储节点个数'
      this.temp.nodes = '1'
      this.dialogNodeVisible = true
      this.temp.name = this.listsent.name
      this.temp.nick_name = this.listsent.nick_name
      this.temp.cluster_id = this.listsent.id
      // 获取存储机器
      getStroMachine().then((res) => {
        this.strogemachines = []
        this.strogemachines = res.list
      })
      // 获取分片名称
      getShards(this.listsent.id).then((response) => {
        const res = response
        if (res.code == 200) {
          this.shard_arr = res.list
        }
      })
      this.$nextTick(() => {
        this.$refs.dataForm.clearValidate()
      })
    },
    createStorageData() {
      this.$refs['dataForm'].validate((valid) => {
        if (valid) {
          const tempData = {}
          tempData.user_name = sessionStorage.getItem('login_username')
          tempData.job_id = ''
          tempData.job_type = 'add_nodes'
          tempData.version = version_arr[0].ver
          tempData.timestamp = timestamp_arr[0].time + ''
          const machinelist = []
          this.temp.machinelist.forEach(item => {
            machinelist.push(item)
          })
          const paras = {}
          paras.nodes = this.temp.nodes
          paras.cluster_id = this.temp.cluster_id
          paras.nick_name = this.temp.nick_name
          paras.shard_id = this.temp.shard_name
          if (machinelist.length > 0) {
            paras.storage_iplists = machinelist
          }
          tempData.paras = paras
          // console.log(tempData);return;
          // 新增shard
          addNodes(tempData).then((response) => {
            const res = response
            if (res.status == 'accept') {
              this.dialogNodeVisible = false
              this.dialogStatusShowVisible = true
              // 把之前的数据清空
              this.shard = []
              this.storage_state = ''
              this.shard_icon = ''
              this.shard_title = ''
              this.shard_active = 0
              this.strogemachines = []
              this.init_title = ''
              this.init_active = 0
              this.finish_title = ''
              this.finish_icon = ''
              this.finish_description = ''
              this.shard_description = ''
              this.job_id = ''
              this.timer = null
              this.init_show = true
              this.finish_show = false
              this.finish_state = ''
              const info = '添加存储节点'
              let i = 0
              this.getFStatus(this.timer, res.job_id, i++, info, '')
              this.timer = setInterval(() => {
                this.getFStatus(this.timer, res.job_id, i++, info, '')
              }, 5000)
            } else if (res.status == 'ongoing') {
              this.message_tips = '系统正在操作中，请等待一会！'
              this.message_type = 'error'
              messageTip(this.message_tips, this.message_type)
            } else {
              this.message_tips = res.error_info
              this.message_type = 'error'
              messageTip(this.message_tips, this.message_type)
            }
          })
        }
      })
    },
    handleDetail(row) {
      this.dialogStatus = 'detail'
      this.dialogFormVisible = true
      this.temp = Object.assign({}, row)
      this.dialogDetail = true
    },
    handleDelete(row) {
      // 先判断该集群是否只有一个shard，如果只有一个shard不予许删除
      const temp = { id: row.db_cluster_id }
      getShardsCount(temp).then((res) => {
        if (res.total == 1) {
          messageTip('该集群当前有且仅有一个shard,不能进行删除操作', 'error')
        } else if (res.total > 1) {
          const code = createCode()
          let rcrRelation='';
          //判断是否为rcr关系
          let tempdate={cluater_id:this.listsent.id};
          getRCRRelater(tempdate).then((response) => {
            if(response.total>0){
              rcrRelation='该集群已建立rcr关系,';
            }
          })
          const string = rcrRelation+'此操作将永久删除' + row.name + ',是否继续?code=' + code
          gotoCofirm(string).then((res) => {
            // 先执行删权限
            if (!res.value) {
              this.message_tips = 'code不能为空！'
              this.message_type = 'error'
              messageTip(this.message_tips, this.message_type)
            } else if (res.value == code) {
              const tempData = {}
              tempData.user_name = sessionStorage.getItem('login_username')
              tempData.job_id = ''
              tempData.job_type = 'delete_shard'
              tempData.version = version_arr[0].ver
              tempData.timestamp = timestamp_arr[0].time + ''
              const paras = {}
              paras.cluster_id = row.db_cluster_id
              paras.nick_name = this.listsent.nick_name
              paras.shard_id = row.id
              tempData.paras = paras
              // console.log(tempData);return;
              delShard(tempData).then((response) => {
                const res = response
                if (res.status == 'accept') {
                  this.dialogStatusShowVisible = true
                  // 调获取状态接口
                  let i = 0
                  this.shard = []
                  this.storage_state = ''
                  this.shard_icon = ''
                  this.shard_title = ''
                  this.shard_active = 0
                  this.strogemachines = []
                  this.init_title = ''
                  this.init_active = 0
                  this.finish_title = ''
                  this.finish_icon = ''
                  this.finish_description = ''
                  this.shard_description = ''
                  this.job_id = ''
                  this.timer = null
                  this.init_show = true
                  this.finish_show = false
                  this.finish_state = ''
                  const info = '删除shard'
                  this.getFStatus(this.timer, res.job_id, i++, info, '')
                  this.timer = setInterval(() => {
                    this.getFStatus(this.timer, res.job_id, i++, info, '')
                  }, 1000)
                } else {
                  this.message_tips = res.error_info
                  this.message_type = 'error'
                  messageTip(this.message_tips, this.message_type)
                }
              })
            } else {
              this.message_tips = 'code输入有误'
              this.message_type = 'error'
              messageTip(this.message_tips, this.message_type)
            }
          }).catch(() => {
            console.log('quxiao')
            messageTip('已取消删除', 'info')
          })
        }
      })
    },
    handleDeleteStorage(row) {
      // 先判断该集群是否只有一个存储节点，如果只有一个存储节点不予许删除
      const temp = { id: row.cluster_id, shard_id: row.shard_id }
      getNodesCount(temp).then((res) => {
        if (res.total == 1) {
          messageTip('该集群当前有且仅有一个储存节点,不能进行删除操作', 'error')
        } else if (res.total > 1) {
          const code = createCode()
          const string = '此操作将永久删除存储节点' + row.hostaddr + '(' + row.port + '),是否继续?code=' + code
          gotoCofirm(string).then((res) => {
            // 先执行删权限
            if (!res.value) {
              this.message_tips = 'code不能为空！'
              this.message_type = 'error'
              messageTip(this.message_tips, this.message_type)
            } else if (res.value == code) {
              const tempData = {}
              tempData.user_name = sessionStorage.getItem('login_username')
              tempData.job_id = ''
              tempData.job_type = 'delete_node'
              tempData.version = version_arr[0].ver
              tempData.timestamp = timestamp_arr[0].time + ''
              const paras = {}
              paras.cluster_id = row.cluster_id
              paras.nick_name = this.listsent.nick_name
              paras.shard_id = row.shard_id
              paras.hostaddr = row.hostaddr
              paras.port = row.port
              tempData.paras = paras
              // console.log(tempData);return;
              delSnode(tempData).then((response) => {
                const res = response
                if (res.status == 'accept') {
                  this.dialogStatusShowVisible = true
                  // 调获取状态接口
                  let i = 0
                  this.shard = []
                  this.storage_state = ''
                  this.shard_icon = ''
                  this.shard_title = ''
                  this.shard_active = 0
                  this.strogemachines = []
                  this.init_title = ''
                  this.init_active = 0
                  this.finish_title = ''
                  this.finish_icon = ''
                  this.finish_description = ''
                  this.shard_description = ''
                  this.job_id = ''
                  this.timer = null
                  this.init_show = true
                  this.finish_show = false
                  this.finish_state = ''
                  const info = '删除存储节点'
                  const ip_port = row.hostaddr + '_' + row.port
                  this.getFStatus(this.timer, res.job_id, i++, info, ip_port)
                  this.timer = setInterval(() => {
                    this.getFStatus(this.timer, res.job_id, i++, info, ip_port)
                  }, 3000)
                } else {
                  this.message_tips = res.error_info
                  this.message_type = 'error'
                  messageTip(this.message_tips, this.message_type)
                }
              })
            } else {
              this.message_tips = 'code输入有误'
              this.message_type = 'error'
              messageTip(this.message_tips, this.message_type)
            }
          }).catch(() => {
            console.log('quxiao')
            messageTip('已取消删除', 'info')
          })
        }
      })
    },
    handleControlNode(row, action) {
      let action_name = ''
      if (action == 'start') {
        action_name = '启用'
      } else if (action == 'stop') {
        action_name = '禁用'
      } else if (action == 'restart') {
        action_name = '重启'
      }
      const code = createCode()
      const string = '将对该存储节点' + row.hostaddr + '(' + row.port + ')进行' + action_name + '操作,是否继续?code=' + code
      gotoCofirm(string).then((res) => {
        // 先执行删权限
        if (!res.value) {
          this.message_tips = 'code不能为空！'
          this.message_type = 'error'
          messageTip(this.message_tips, this.message_type)
        } else if (res.value == code) {
          const tempData = {}
          tempData.user_name = sessionStorage.getItem('login_username')
          tempData.job_id = ''
          tempData.job_type = 'control_instance'
          tempData.version = version_arr[0].ver
          tempData.timestamp = timestamp_arr[0].time + ''
          const paras = {}
          paras.control = action
          paras.hostaddr = row.hostaddr
          paras.port = row.port
          paras.cluster_id = this.listsent.id
          paras.machine_type = 'storage'
          tempData.paras = paras
          const arr = {
            list: tempData,
            type: 'mysql'
          }
          controlInstance(arr).then((response) => {
            const res = response
            if (res.status == 'accept') {
              this.dialogStatusVisible = true
              this.activities = []
              const newArr = {
                content: '正在' + action_name + '储存节点' + row.hostaddr + '(' + row.port + ')',
                timestamp: getNowDate(),
                size: 'large',
                type: 'primary',
                icon: 'el-icon-more'
              }
              this.activities.push(newArr)
              // 调获取状态接口
              let i = 0
              this.timer = setInterval(() => {
                this.getStatus(this.timer, res.job_id, i++, action_name)
              }, 5000)
              // this.isShowNodeMenuPanel = false
            } else {
              this.message_tips = res.error_info
              this.message_type = 'error'
              messageTip(this.message_tips, this.message_type)
            }
          })
        } else {
          this.message_tips = 'code输入有误'
          this.message_type = 'error'
          messageTip(this.message_tips, this.message_type)
        }
      }).catch(() => {
        console.log('quxiao')
        messageTip('已取消删除', 'info')
      })
    },
    getFStatus(timer, data, i, info, iparr) {
      setTimeout(() => {
        const postarr = {}
        postarr.job_type = 'get_status'
        postarr.version = version_arr[0].ver
        postarr.job_id = data
        postarr.timestamp = timestamp_arr[0].time + ''
        postarr.paras = {}
        this.job_id = '任务ID:' + data
        getEvStatus(postarr).then((ress) => {
          // 新增shard,存储节点
          if (info == '主备切换') {
            if (ress.attachment !== null) {
              this.dialogStatusFVisible = true
              const steps = (ress.attachment.job_step).replace(/\s+/g, '').split(',')
              if (this.stepParams.length == 0) {
                for (let a = 0; a < steps.length; a++) {
                  const newArrgoing = {}
                  if (a == 0) {
                    newArrgoing.title = steps[a]
                    newArrgoing.icon = 'el-icon-loading'
                    newArrgoing.status = 'process'
                    newArrgoing.description = ''
                  } else {
                    newArrgoing.title = steps[a]
                    newArrgoing.icon = ''
                    newArrgoing.status = 'wait'
                    newArrgoing.description = ''
                  }
                  if (ress.attachment.step == steps[a]) {
                    if (a > (this.active)) {
                      for (let k = 0; k < this.stepParams.length; k++) {
                        if (k == a) {
                          if (this.stepParams[k].status == 'wait') {
                            this.stepParams[k].icon = 'el-icon-loading'
                            this.stepParams[k].status = 'process'
                          }
                        }
                        if (k > 0 && k < a) {
                          // 小于i的情况
                          for (let j = 0; j < k; j++) {
                            this.stepParams[j].icon = 'el-icon-circle-check'
                            this.stepParams[j].status = 'success'
                          }
                        }
                      }
                    }
                    this.active = a
                  }
                  this.stepParams.push(newArrgoing)
                }
              } else {
                if (ress.status == 'ongoing') {
                  for (let k = 0; k < this.stepParams.length; k++) {
                    if (ress.attachment.step == this.stepParams[k].title) {
                      this.active = k
                      this.stepParams[k].icon = 'el-icon-loading'
                      this.stepParams[k].status = 'process'
                      if (k > 0 && k < (this.active + 1)) {
                        // 小于i的情况
                        for (let j = 0; j < k; j++) {
                          this.stepParams[j].icon = 'el-icon-circle-check'
                          this.stepParams[j].status = 'success'
                        }
                      }
                    }
                  }
                } else if (ress.status == 'done') {
                  for (let k = 0; k < this.stepParams.length; k++) {
                    this.active = this.stepParams.length
                    this.stepParams[k].icon = 'el-icon-circle-check'
                    this.stepParams[k].status = 'success'
                  }
                  clearInterval(timer)
                  this.getList()
                  this.getOneCluster(this.temp_cluster)
                } else if (ress.status == 'failed') {
                  for (let k = 0; k < this.stepParams.length; k++) {
                    if (this.stepParams[k].status == 'process') {
                      this.active = k
                      this.stepParams[k].icon = 'el-icon-circle-close'
                      this.stepParams[k].status = 'error'
                      this.stepParams[k].description = ress.error_info
                    }
                  }
                  clearInterval(timer)
                }
              }
            } else if (ress.attachment == null && ress.error_code == '70001' && ress.status == 'failed') {
              if (i > 5) {
                clearInterval(timer)
              }
            }
          } else if (info == '重做备机节点') {
            if (ress.attachment !== null) {
              if (this.statusList.length == 0) {
                let statusgoing = {}
                if (ress.status == 'failed') {
                  statusgoing = {
                    title: info + '失败',
                    icon: 'el-icon-circle-close',
                    status: 'error',
                    description: ress.error_info,
                    second: []
                  }
                  this.statusList.push(statusgoing)
                  clearInterval(timer)
                } else {
                  // let statusgoing={}
                  // let iparr=[];
                  // for(let item in ress.attachment){
                  //   if(item!=='job_step'){
                  //   let key=ress.attachment[item];
                  //   this.$set(key, 'title', item)
                  //   iparr.push(key);
                  //   }
                  // }
                  for (let a = 0; a < iparr.length; a++) {
                    const steps = (ress.attachment.job_step).replace(/\s+/g, '').split(',')
                    let secondgoing = {}
                    const secondlist = []
                    for (let b = 0; b < steps.length; b++) {
                      secondgoing = { title: steps[b], icon: '', status: 'wait', description: '' }
                      secondlist.push(secondgoing)
                    }
                    if (a == 0) {
                      statusgoing = {
                        title: iparr[a].title,
                        icon: 'el-icon-loading',
                        status: 'process',
                        description: '',
                        second: secondlist
                      }
                      this.statusList.push(statusgoing)
                      if (iparr[a].status == 'done') {
                        this.init_active = a + 1
                        this.statusList[a].icon = 'el-icon-circle-check'
                        this.statusList[a].status = 'success'
                        for (let c = 0; c < this.statusList[a].second.length; c++) {
                          this.statusList[a].second[c].icon = 'el-icon-circle-check'
                          this.statusList[a].second[c].status = 'success'
                        }
                      } else if (iparr[a].status == 'ongoing') {
                        this.init_active = a
                        this.statusList[a].icon = 'el-icon-loading'
                        this.statusList[a].status = 'process'
                        for (let c = 0; c < this.statusList[a].second.length; c++) {
                          if (iparr[a].step == this.statusList[a].second[c].title) {
                            this.statusList[a].second[c].icon = 'el-icon-loading'
                            this.statusList[a].second[c].status = 'process'
                            for (let j = 0; j < c; j++) {
                              this.statusList[a].second[j].icon = 'el-icon-circle-check'
                              this.statusList[a].second[j].status = 'success'
                            }
                          }
                        }
                      } else if (iparr[a].status == 'failed') {
                        this.statusList[a].icon = 'el-icon-circle-close'
                        this.statusList[a].status = 'error'
                        for (let c = 0; c < this.statusList[a].second.length; c++) {
                          if (iparr[a].step == this.statusList[a].second[c].title) {
                            this.statusList[a].second[c].icon = 'el-icon-circle-close'
                            this.statusList[a].second[c].status = 'error'
                            for (let j = 0; j < c; j++) {
                              this.statusList[a].second[j].icon = 'el-icon-circle-check'
                              this.statusList[a].second[j].status = 'success'
                            }
                          }
                        }
                        this.statusList[a].description = iparr[a].error_info
                      }
                    } else {
                      const steps = (ress.attachment.job_step).replace(/\s+/g, '').split(',')
                      let secondgoing = {}
                      const secondlist = []
                      for (let b = 0; b < steps.length; b++) {
                        secondgoing = { title: steps[b], icon: '', status: 'wait', description: '' }
                        secondlist.push(secondgoing)
                      }
                      if (this.init_active == 0) {
                        statusgoing = {
                          title: iparr[a].title,
                          icon: '',
                          status: 'wait',
                          description: '',
                          second: secondlist
                        }
                        this.statusList.push(statusgoing)
                      } else {
                        statusgoing = {
                          title: iparr[a].title,
                          icon: 'el-icon-loading',
                          status: 'process',
                          description: '',
                          second: secondlist
                        }
                        this.statusList.push(statusgoing)
                      }
                      // 第a个修改里边的状态
                      if (iparr[a].status == 'done') {
                        this.init_active = a + 1
                        this.statusList[a].icon = 'el-icon-circle-check'
                        this.statusList[a].status = 'success'
                        for (let c = 0; c < this.statusList[a].second.length; c++) {
                          this.statusList[a].second[c].icon = 'el-icon-circle-check'
                          this.statusList[a].second[c].status = 'success'
                        }
                      } else if (iparr[a].status == 'ongoing') {
                        this.init_active = a
                        this.statusList[a].icon = 'el-icon-loading'
                        this.statusList[a].status = 'process'
                        for (let c = 0; c < this.statusList[a].second.length; c++) {
                          if (iparr[a].step == this.statusList[a].second[c].title) {
                            this.statusList[a].second[c].icon = 'el-icon-loading'
                            this.statusList[a].second[c].status = 'process'
                            for (let j = 0; j < c; j++) {
                              this.statusList[a].second[j].icon = 'el-icon-circle-check'
                              this.statusList[a].second[j].status = 'success'
                            }
                          }
                        }
                      } else if (iparr[a].status == 'failed') {
                        this.statusList[a].icon = 'el-icon-circle-close'
                        this.statusList[a].status = 'error'
                        for (let c = 0; c < this.statusList[a].second.length; c++) {
                          if (iparr[a].step == this.statusList[a].second[c].title) {
                            this.statusList[a].second[c].icon = 'el-icon-circle-close'
                            this.statusList[a].second[c].status = 'error'
                            for (let j = 0; j < c; j++) {
                              this.statusList[a].second[j].icon = 'el-icon-circle-check'
                              this.statusList[a].second[j].status = 'success'
                            }
                          }
                        }
                        this.statusList[a].description = iparr[a].error_info
                      }
                    }
                  }
                }
              } else {
                // statusList不为0的情况
                if (ress.status == 'ongoing') {
                  // let statusgoing={}
                  const iparr = []
                  for (const item in ress.attachment) {
                    if (item !== 'job_step') {
                      const key = ress.attachment[item]
                      this.$set(key, 'title', item)
                      iparr.push(key)
                    }
                  }
                  for (let a = 0; a < iparr.length; a++) {
                    if (a == 0) {
                      if (iparr[a].status == 'done') {
                        this.init_active = a + 1
                        this.statusList[a].icon = 'el-icon-circle-check'
                        this.statusList[a].status = 'success'
                        for (let c = 0; c < this.statusList[a].second.length; c++) {
                          this.statusList[a].second[c].icon = 'el-icon-circle-check'
                          this.statusList[a].second[c].status = 'success'
                        }
                      } else if (iparr[a].status == 'ongoing') {
                        this.init_active = a
                        this.statusList[a].icon = 'el-icon-loading'
                        this.statusList[a].status = 'process'
                        for (let c = 0; c < this.statusList[a].second.length; c++) {
                          if (iparr[a].step == this.statusList[a].second[c].title) {
                            this.statusList[a].second[c].icon = 'el-icon-loading'
                            this.statusList[a].second[c].status = 'process'
                            for (let j = 0; j < c; j++) {
                              this.statusList[a].second[j].icon = 'el-icon-circle-check'
                              this.statusList[a].second[j].status = 'success'
                            }
                          }
                        }
                      } else if (iparr[a].status == 'failed') {
                        this.statusList[a].icon = 'el-icon-circle-close'
                        this.statusList[a].status = 'error'
                        for (let c = 0; c < this.statusList[a].second.length; c++) {
                          if (iparr[a].step == this.statusList[a].second[c].title) {
                            this.statusList[a].second[c].icon = 'el-icon-circle-close'
                            this.statusList[a].second[c].status = 'error'
                            for (let j = 0; j < c; j++) {
                              this.statusList[a].second[j].icon = 'el-icon-circle-check'
                              this.statusList[a].second[j].status = 'success'
                            }
                          }
                        }
                        this.statusList[a].description = iparr[a].error_info
                      }
                    } else {
                      // 第a个修改里边的状态
                      if (iparr[a].status == 'done') {
                        this.init_active = a + 1
                        this.statusList[a].icon = 'el-icon-circle-check'
                        this.statusList[a].status = 'success'
                        for (let c = 0; c < this.statusList[a].second.length; c++) {
                          this.statusList[a].second[c].icon = 'el-icon-circle-check'
                          this.statusList[a].second[c].status = 'success'
                        }
                      } else if (iparr[a].status == 'ongoing') {
                        this.init_active = a
                        this.statusList[a].icon = 'el-icon-loading'
                        this.statusList[a].status = 'process'
                        for (let c = 0; c < this.statusList[a].second.length; c++) {
                          if (iparr[a].step == this.statusList[a].second[c].title) {
                            this.statusList[a].second[c].icon = 'el-icon-loading'
                            this.statusList[a].second[c].status = 'process'
                            for (let j = 0; j < c; j++) {
                              this.statusList[a].second[j].icon = 'el-icon-circle-check'
                              this.statusList[a].second[j].status = 'success'
                            }
                          }
                        }
                      } else if (iparr[a].status == 'failed') {
                        this.statusList[a].icon = 'el-icon-circle-close'
                        this.statusList[a].status = 'error'
                        for (let c = 0; c < this.statusList[a].second.length; c++) {
                          if (iparr[a].step == this.statusList[a].second[c].title) {
                            this.statusList[a].second[c].icon = 'el-icon-circle-close'
                            this.statusList[a].second[c].status = 'error'
                            for (let j = 0; j < c; j++) {
                              this.statusList[a].second[j].icon = 'el-icon-circle-check'
                              this.statusList[a].second[j].status = 'success'
                            }
                          }
                        }
                        this.statusList[a].description = iparr[a].error_info
                      }
                    }
                  }
                } else if (ress.status == 'failed') {
                  const iparr = []
                  for (const item in ress.attachment) {
                    if (item !== 'job_step') {
                      const key = ress.attachment[item]
                      this.$set(key, 'title', item)
                      iparr.push(key)
                    }
                  }
                  for (let a = 0; a < iparr.length; a++) {
                    if (a == 0) {
                      if (iparr[a].status == 'done') {
                        this.init_active = a + 1
                        this.statusList[a].icon = 'el-icon-circle-check'
                        this.statusList[a].status = 'success'
                        for (let c = 0; c < this.statusList[a].second.length; c++) {
                          this.statusList[a].second[c].icon = 'el-icon-circle-check'
                          this.statusList[a].second[c].status = 'success'
                        }
                      } else if (iparr[a].status == 'ongoing') {
                        this.init_active = a
                        this.statusList[a].icon = 'el-icon-loading'
                        this.statusList[a].status = 'process'
                        for (let c = 0; c < this.statusList[a].second.length; c++) {
                          if (iparr[a].step == this.statusList[a].second[c].title) {
                            this.statusList[a].second[c].icon = 'el-icon-loading'
                            this.statusList[a].second[c].status = 'process'
                            for (let j = 0; j < c; j++) {
                              this.statusList[a].second[j].icon = 'el-icon-circle-check'
                              this.statusList[a].second[j].status = 'success'
                            }
                          }
                        }
                      } else if (iparr[a].status == 'failed') {
                        this.statusList[a].icon = 'el-icon-circle-close'
                        this.statusList[a].status = 'error'
                        for (let c = 0; c < this.statusList[a].second.length; c++) {
                          if (iparr[a].step == this.statusList[a].second[c].title) {
                            this.statusList[a].second[c].icon = 'el-icon-circle-close'
                            this.statusList[a].second[c].status = 'error'
                            for (let j = 0; j < c; j++) {
                              this.statusList[a].second[j].icon = 'el-icon-circle-check'
                              this.statusList[a].second[j].status = 'success'
                            }
                          }
                        }
                        this.statusList[a].description = iparr[a].error_info
                      }
                    } else {
                      // 第a个修改里边的状态
                      if (iparr[a].status == 'done') {
                        this.init_active = a + 1
                        this.statusList[a].icon = 'el-icon-circle-check'
                        this.statusList[a].status = 'success'
                        for (let c = 0; c < this.statusList[a].second.length; c++) {
                          this.statusList[a].second[c].icon = 'el-icon-circle-check'
                          this.statusList[a].second[c].status = 'success'
                        }
                      } else if (iparr[a].status == 'ongoing') {
                        this.init_active = a
                        this.statusList[a].icon = 'el-icon-loading'
                        this.statusList[a].status = 'process'
                        for (let c = 0; c < this.statusList[a].second.length; c++) {
                          if (iparr[a].step == this.statusList[a].second[c].title) {
                            this.statusList[a].second[c].icon = 'el-icon-loading'
                            this.statusList[a].second[c].status = 'process'
                            for (let j = 0; j < c; j++) {
                              this.statusList[a].second[j].icon = 'el-icon-circle-check'
                              this.statusList[a].second[j].status = 'success'
                            }
                          }
                        }
                      } else if (iparr[a].status == 'failed') {
                        this.statusList[a].icon = 'el-icon-circle-close'
                        this.statusList[a].status = 'error'
                        for (let c = 0; c < this.statusList[a].second.length; c++) {
                          if (iparr[a].step == this.statusList[a].second[c].title) {
                            this.statusList[a].second[c].icon = 'el-icon-circle-close'
                            this.statusList[a].second[c].status = 'error'
                            for (let j = 0; j < c; j++) {
                              this.statusList[a].second[j].icon = 'el-icon-circle-check'
                              this.statusList[a].second[j].status = 'success'
                            }
                          }
                        }
                        this.statusList[a].description = iparr[a].error_info
                      }
                    }
                  }
                  const statusgoing = {
                    title: info + '失败',
                    icon: 'el-icon-circle-close',
                    status: 'error',
                    description: ress.error_info,
                    second: ''
                  }
                  this.statusList.push(statusgoing)
                  clearInterval(timer)
                } else if (ress.status == 'done') {
                  const iparr = []
                  for (const item in ress.attachment) {
                    if (item !== 'job_step') {
                      const key = ress.attachment[item]
                      this.$set(key, 'title', item)
                      iparr.push(key)
                    }
                  }
                  for (let a = 0; a < iparr.length; a++) {
                    if (a == 0) {
                      if (iparr[a].status == 'done') {
                        this.init_active = a + 1
                        this.statusList[a].icon = 'el-icon-circle-check'
                        this.statusList[a].status = 'success'
                        for (let c = 0; c < this.statusList[a].second.length; c++) {
                          this.statusList[a].second[c].icon = 'el-icon-circle-check'
                          this.statusList[a].second[c].status = 'success'
                        }
                      } else if (iparr[a].status == 'ongoing') {
                        this.init_active = a
                        this.statusList[a].icon = 'el-icon-loading'
                        this.statusList[a].status = 'process'
                        for (let c = 0; c < this.statusList[a].second.length; c++) {
                          if (iparr[a].step == this.statusList[a].second[c].title) {
                            this.statusList[a].second[c].icon = 'el-icon-loading'
                            this.statusList[a].second[c].status = 'process'
                            for (let j = 0; j < c; j++) {
                              this.statusList[a].second[j].icon = 'el-icon-circle-check'
                              this.statusList[a].second[j].status = 'success'
                            }
                          }
                        }
                      } else if (iparr[a].status == 'failed') {
                        this.statusList[a].icon = 'el-icon-circle-close'
                        this.statusList[a].status = 'error'
                        for (let c = 0; c < this.statusList[a].second.length; c++) {
                          if (iparr[a].step == this.statusList[a].second[c].title) {
                            this.statusList[a].second[c].icon = 'el-icon-circle-close'
                            this.statusList[a].second[c].status = 'error'
                            for (let j = 0; j < c; j++) {
                              this.statusList[a].second[j].icon = 'el-icon-circle-check'
                              this.statusList[a].second[j].status = 'success'
                            }
                          }
                        }
                        this.statusList[a].description = iparr[a].error_info
                      }
                    } else {
                      // 第a个修改里边的状态
                      if (iparr[a].status == 'done') {
                        this.init_active = a + 1
                        this.statusList[a].icon = 'el-icon-circle-check'
                        this.statusList[a].status = 'success'
                        for (let c = 0; c < this.statusList[a].second.length; c++) {
                          this.statusList[a].second[c].icon = 'el-icon-circle-check'
                          this.statusList[a].second[c].status = 'success'
                        }
                      } else if (iparr[a].status == 'ongoing') {
                        this.init_active = a
                        this.statusList[a].icon = 'el-icon-loading'
                        this.statusList[a].status = 'process'
                        for (let c = 0; c < this.statusList[a].second.length; c++) {
                          if (iparr[a].step == this.statusList[a].second[c].title) {
                            this.statusList[a].second[c].icon = 'el-icon-loading'
                            this.statusList[a].second[c].status = 'process'
                            for (let j = 0; j < c; j++) {
                              this.statusList[a].second[j].icon = 'el-icon-circle-check'
                              this.statusList[a].second[j].status = 'success'
                            }
                          }
                        }
                      } else if (iparr[a].status == 'failed') {
                        this.statusList[a].icon = 'el-icon-circle-close'
                        this.statusList[a].status = 'error'
                        for (let c = 0; c < this.statusList[a].second.length; c++) {
                          if (iparr[a].step == this.statusList[a].second[c].title) {
                            this.statusList[a].second[c].icon = 'el-icon-circle-close'
                            this.statusList[a].second[c].status = 'error'
                            for (let j = 0; j < c; j++) {
                              this.statusList[a].second[j].icon = 'el-icon-circle-check'
                              this.statusList[a].second[j].status = 'success'
                            }
                          }
                        }
                        this.statusList[a].description = iparr[a].error_info
                      }
                    }
                  }
                  const statusgoing = {
                    title: info + '成功',
                    icon: 'el-icon-circle-check',
                    status: 'success',
                    description: '',
                    second: ''
                  }
                  this.statusList.push(statusgoing)
                  clearInterval(timer)
                  this.getList()
                } else {
                  this.message_tips = ress.error_info
                  this.message_type = 'error'
                  messageTip(this.message_tips, this.message_type)
                }
              }
            } else if (ress.attachment == null && ress.error_code == '70001' && ress.status == 'failed') {
              // if(i==0){
              //   this.statusList=[];
              //   let statusgoing={}
              //   statusgoing={title:'正在'+info,icon:'el-icon-loading',status:'process',description:'',second:[]}
              //   this.statusList.push(statusgoing)
              // }
              if (i > 5) {
                const statusgoing = {
                  title: info + '失败',
                  icon: 'el-icon-circle-close',
                  status: 'error',
                  description: ress.error_code,
                  second: []
                }
                this.statusList.push(statusgoing)
                // this.statusList.title=info+'失败';
                // this.statusList.icon='el-icon-circle-close';
                // this.statusList.status='error';
                // this.statusList.description=ress.error_code;
                clearInterval(timer)
              }
            } else if (ress.attachment == null && ress.status == 'ongoing') {
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
            } else if (ress.attachment == null && ress.status == 'done') {
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
                description: ress.error_code,
                second: []
              }
              this.statusList.push(statusgoing)
              clearInterval(timer)
            }
          } else if (info == '添加shard' || info == '添加存储节点' || info == '删除shard' || info == '删除存储节点') {
            // 新增shard,存储节点
            if (ress.attachment !== null) {
              console.log(5)
              if (info == '添加shard' || info == '添加存储节点' || info == '删除shard' || info == '删除存储节点') {
                this.shard_show = true
                this.init_show = false
                this.computer_show = false
              }
              // 存储
              if (ress.attachment.hasOwnProperty('storage_state')) {
                if (ress.attachment.storage_state == 'ongoing') {
                  this.storage_state = 'process'
                  this.shard_icon = 'el-icon-loading'
                  this.shard_title = '正在' + info
                } else if (ress.attachment.storage_state == 'done') {
                  this.storage_state = 'success'
                  this.shard_icon = 'el-icon-circle-check'
                  this.shard_title = info + '成功'
                  // 遍历存储节点改状态
                  if (this.shard.length > 0) {
                    for (let c = 0; c < this.shard.length; c++) {
                      let shard_ids = ''
                      if (info == '添加shard') {
                        shard_ids = ress.attachment.shard_ids
                        for (let e = 0; e < shard_ids.length; e++) {
                          for (var item in shard_ids[e]) {
                            var shard_idsValue = shard_ids[e][item]
                            if (this.shard[c].shard_id == shard_idsValue) {
                              this.shard[c].icon = 'el-icon-circle-check'
                              this.shard[c].status = 'success'
                            }
                          }
                        }
                      } else if (info == '添加存储节点') {
                        shard_ids = ress.attachment.shard_hosts
                        for (let e = 0; e < shard_ids.length; e++) {
                          for (var item in shard_ids[e]) {
                            var shard_idsValue = shard_ids[e][item]
                            if (this.shard[c].shard_id == shard_idsValue) {
                              this.shard[c].icon = 'el-icon-circle-check'
                              this.shard[c].status = 'success'
                            }
                          }
                        }
                      } else if (info == '删除shard') {
                        const arr = ress.attachment.storage_hosts.substr(0, ress.attachment.storage_hosts.length - 1)
                        shard_ids = arr.split(';')
                        for (let e = 0; e < shard_ids.length; e++) {
                          // for(var item in shard_ids[e]){
                          //   var shard_idsValue=shard_ids[e][item];
                          if (this.shard[c].shard_id == shard_ids[e]) {
                            this.shard[c].icon = 'el-icon-circle-check'
                            this.shard[c].status = 'success'
                          }
                          // }
                        }
                      } else {
                        shard_ids = ip
                        if (this.shard[c].shard_id == shard_ids) {
                          this.shard[c].icon = 'el-icon-circle-check'
                          this.shard[c].status = 'success'
                        }
                      }
                    }
                  }
                } else if (ress.attachment.storage_state == 'failed') {
                  console.log(5)
                  this.storage_state = 'error'
                  this.shard_icon = 'el-icon-circle-close'
                  this.shard_title = info + '失败'
                  // 遍历存储节点改状态
                  if (this.shard.length > 0) {
                    for (let c = 0; c < this.shard.length; c++) {
                      let shard_ids = ''
                      if (info == '添加shard') {
                        shard_ids = ress.attachment.shard_ids
                        for (let e = 0; e < shard_ids.length; e++) {
                          for (var item in shard_ids[e]) {
                            var shard_idsValue = shard_ids[e][item]
                            if (this.shard[c].shard_id == shard_idsValue) {
                              this.shard[c].icon = 'el-icon-circle-close'
                              this.shard[c].status = 'error'
                            }
                          }
                        }
                      } else if (info == '添加存储节点') {
                        shard_ids = ress.attachment.shard_hosts
                        for (let e = 0; e < shard_ids.length; e++) {
                          for (var item in shard_ids[e]) {
                            var shard_idsValue = shard_ids[e][item]
                            if (this.shard[c].shard_id == shard_idsValue) {
                              this.shard[c].icon = 'el-icon-circle-close'
                              this.shard[c].status = 'error'
                            }
                          }
                        }
                      } else if (info == '删除shard') {
                        const arr = ress.attachment.storage_hosts.substr(0, ress.attachment.storage_hosts.length - 1)
                        shard_ids = arr.split(';')
                        for (let e = 0; e < shard_ids.length; e++) {
                          // for(var item in shard_ids[e]){
                          //   var shard_idsValue=shard_ids[e][item];
                          if (this.shard[c].shard_id == shard_ids[e]) {
                            this.shard[c].icon = 'el-icon-circle-close'
                            this.shard[c].status = 'error'
                          }
                          // }
                        }
                      } else {
                        shard_ids = ip
                        if (this.shard[c].shard_id == shard_ids) {
                          this.shard[c].icon = 'el-icon-circle-close'
                          this.shard[c].status = 'error'
                        }
                      }
                    }
                    this.shard_description = ress.error_info
                  }
                  // clearInterval(timer);
                } else {
                  this.storage_state = 'process'
                  this.shard_icon = 'el-icon-loading'
                  this.shard_title = '正在' + info
                }
              }
              this.init_title = '正在' + info
              this.init_active = 1
              if (this.shard.length == 0) {
                console.log(1)
                if (ress.status == 'ongoing') {
                  this.storage_state = 'process'
                  this.shard_icon = 'el-icon-loading'
                  this.shard_title = '正在' + info
                  if (ress.attachment.hasOwnProperty('storage_state')) {
                    let shard_ids = ''
                    if (info == '添加shard') {
                      shard_ids = ress.attachment.shard_ids
                      for (let e = 0; e < shard_ids.length; e++) {
                        for (var item in shard_ids[e]) {
                          const shardgoing = {}
                          var shard_idsValue = shard_ids[e][item]
                          console.log(shard_idsValue)
                          const shard_text = item + ':' + shard_idsValue
                          console.log(shard_text)
                          shardgoing.title = shard_idsValue !== '' ? shard_text : '正在' + info
                          shardgoing.icon = 'el-icon-loading'
                          shardgoing.status = 'process'
                          shardgoing.description = ''
                          shardgoing.shard_id = shard_idsValue
                          this.shard.push(shardgoing)
                        }
                      }
                    } else if (info == '添加存储节点') {
                      shard_ids = ress.attachment.shard_hosts
                      for (let e = 0; e < shard_ids.length; e++) {
                        for (var item in shard_ids[e]) {
                          const shardgoing = {}
                          var shard_idsValue = shard_ids[e][item]
                          console.log(shard_idsValue)
                          const shard_text = item + ':' + shard_idsValue
                          console.log(shard_text)
                          shardgoing.title = shard_idsValue !== '' ? shard_text : '正在' + info
                          shardgoing.icon = 'el-icon-loading'
                          shardgoing.status = 'process'
                          shardgoing.description = ''
                          shardgoing.shard_id = shard_idsValue
                          this.shard.push(shardgoing)
                        }
                      }
                    } else if (info == '删除shard') {
                      const arr = ress.attachment.storage_hosts.substr(0, ress.attachment.storage_hosts.length - 1)
                      shard_ids = arr.split(';')
                      for (let e = 0; e < shard_ids.length; e++) {
                        // for(var item in shard_ids[e]){
                        //   let shardgoing={}
                        //   var shard_idsValue=shard_ids[e][item];
                        //   console.log(shard_idsValue);
                        //   const shard_text=item+':'+shard_idsValue;
                        //   console.log(shard_text);
                        const shardgoing = {}
                        shardgoing.title = shard_ids[e] !== '' ? shard_ids[e] : '正在' + info
                        shardgoing.icon = 'el-icon-loading'
                        shardgoing.status = 'process'
                        shardgoing.description = ''
                        shardgoing.shard_id = shard_ids[e]
                        this.shard.push(shardgoing)
                        // }
                      }
                    } else {
                      shard_ids = ip
                      const shardgoing = {}
                      shardgoing.title = shard_ids
                      shardgoing.icon = 'el-icon-loading'
                      shardgoing.status = 'process'
                      shardgoing.description = ''
                      shardgoing.shard_id = shard_ids
                      this.shard.push(shardgoing)
                    }
                  }
                } else if (ress.status == 'failed') {
                  console.log(2)
                  this.storage_state = 'error'
                  this.shard_icon = 'el-icon-circle-close'
                  this.shard_title = info + '失败'
                  this.init_show = false
                  this.shard_description = ress.error_info

                  this.finish_title = info + '失败'
                  this.finish_icon = 'el-icon-circle-close'
                  this.finish_state = 'error'
                  this.init_active = 1
                  this.finish_description = ress.error_info

                  // 遍历存储节点改状态
                  if (this.shard.length > 0) {
                    for (let c = 0; c < this.shard.length; c++) {
                      let shard_ids = ''
                      if (ress.attachment.hasOwnProperty('shard_hosts')) {
                        if (info == '添加shard') {
                          shard_ids = ress.attachment.shard_ids
                          for (let e = 0; e < shard_ids.length; e++) {
                            for (var item in shard_ids[e]) {
                              var shard_idsValue = shard_ids[e][item]
                              if (this.shard[c].shard_id == shard_idsValue) {
                                this.shard[c].icon = 'el-icon-circle-close'
                                this.shard[c].status = 'error'
                              }
                            }
                          }
                        } else if (info == '添加存储节点') {
                          shard_ids = ress.attachment.shard_hosts
                          for (let e = 0; e < shard_ids.length; e++) {
                            for (var item in shard_ids[e]) {
                              var shard_idsValue = shard_ids[e][item]
                              if (this.shard[c].shard_id == shard_idsValue) {
                                this.shard[c].icon = 'el-icon-circle-close'
                                this.shard[c].status = 'error'
                              }
                            }
                          }
                        } else if (info == '删除shard') {
                          const arr = ress.attachment.storage_hosts.substr(0, ress.attachment.storage_hosts.length - 1)
                          shard_ids = arr.split(';')
                          for (let e = 0; e < shard_ids.length; e++) {
                            // for(var item in shard_ids[e]){
                            //   var shard_idsValue=shard_ids[e][item];
                            if (this.shard[c].shard_id == shard_ids[e]) {
                              this.shard[c].icon = 'el-icon-circle-close'
                              this.shard[c].status = 'error'
                            }
                            // }
                          }
                        } else {
                          shard_ids = ip
                          if (this.shard[c].shard_id == shard_ids) {
                            this.shard[c].icon = 'el-icon-circle-close'
                            this.shard[c].status = 'error'
                          }
                        }
                      }
                    }
                    this.shard_description = ress.error_info
                  } else {
                    if (ress.attachment.hasOwnProperty('storage_state')) {
                      let shard_ids = ''
                      if (info == '添加shard') {
                        shard_ids = ress.attachment.shard_ids
                        for (let e = 0; e < shard_ids.length; e++) {
                          for (var item in shard_ids[e]) {
                            const shardgoing = {}
                            var shard_idsValue = shard_ids[e][item]
                            console.log(shard_idsValue)
                            const shard_text = item + ':' + shard_idsValue
                            console.log(shard_text)
                            shardgoing.title = shard_idsValue !== '' ? shard_text : '正在' + info
                            shardgoing.icon = 'el-icon-circle-close'
                            shardgoing.status = 'error'
                            shardgoing.description = ''
                            shardgoing.shard_id = shard_idsValue
                            this.shard.push(shardgoing)
                          }
                        }
                      } else if (info == '添加存储节点') {
                        shard_ids = ress.attachment.shard_hosts
                        for (let e = 0; e < shard_ids.length; e++) {
                          for (var item in shard_ids[e]) {
                            const shardgoing = {}
                            var shard_idsValue = shard_ids[e][item]
                            console.log(shard_idsValue)
                            const shard_text = item + ':' + shard_idsValue
                            console.log(shard_text)
                            shardgoing.title = shard_idsValue !== '' ? shard_text : '正在' + info
                            shardgoing.icon = 'el-icon-circle-close'
                            shardgoing.status = 'error'
                            shardgoing.description = ''
                            shardgoing.shard_id = shard_idsValue
                            this.shard.push(shardgoing)
                          }
                        }
                      } else if (info == '删除shard') {
                        const arr = ress.attachment.storage_hosts.substr(0, ress.attachment.storage_hosts.length - 1)
                        shard_ids = arr.split(';')
                        for (let e = 0; e < shard_ids.length; e++) {
                          // for(var item in shard_ids[e]){
                          //   let shardgoing={}
                          //   var shard_idsValue=shard_ids[e][item];
                          //   console.log(shard_idsValue);
                          //   const shard_text=item+':'+shard_idsValue;
                          //   console.log(shard_text);
                          const shardgoing = {}
                          shardgoing.title = shard_ids[e] !== '' ? shard_ids[e] : '正在' + info
                          shardgoing.icon = 'el-icon-circle-close'
                          shardgoing.status = 'error'
                          shardgoing.description = ''
                          shardgoing.shard_id = shard_ids[e]
                          this.shard.push(shardgoing)
                          // }
                        }
                      } else {
                        shard_ids = ip
                        const shardgoing = {}
                        shardgoing.title = shard_ids !== '' ? shard_ids : '正在' + info
                        shardgoing.icon = 'el-icon-circle-close'
                        shardgoing.status = 'error'
                        shardgoing.description = ''
                        shardgoing.shard_id = shard_ids
                        this.shard.push(shardgoing)
                      }
                    }
                  }
                  clearInterval(timer)
                  console.log(7)
                } else if (ress.status == 'done') {
                  console.log(3)
                  this.storage_state = 'success'
                  this.shard_icon = 'el-icon-circle-check'
                  this.shard_title = info + '成功'

                  this.finish_title = info + '成功'
                  this.finish_icon = 'el-icon-circle-check'
                  this.finish_state = 'success'
                  this.init_active = 1
                  this.finish_description = ress.error_info
                  // 遍历存储节点改状态
                  if (this.shard.length > 0) {
                    for (let c = 0; c < this.shard.length; c++) {
                      let shard_ids = ''
                      if (ress.attachment.hasOwnProperty('shard_hosts')) {
                        if (info == '添加shard') {
                          shard_ids = ress.attachment.shard_ids
                          for (let e = 0; e < shard_ids.length; e++) {
                            for (var item in shard_ids[e]) {
                              var shard_idsValue = shard_ids[e][item]
                              if (this.shard[c].shard_id == shard_idsValue) {
                                this.shard[c].icon = 'el-icon-circle-check'
                                this.shard[c].status = 'success'
                              }
                            }
                          }
                        } else if (info == '添加存储节点') {
                          shard_ids = ress.attachment.shard_hosts
                          for (let e = 0; e < shard_ids.length; e++) {
                            for (var item in shard_ids[e]) {
                              var shard_idsValue = shard_ids[e][item]
                              if (this.shard[c].shard_id == shard_idsValue) {
                                this.shard[c].icon = 'el-icon-circle-check'
                                this.shard[c].status = 'success'
                              }
                            }
                          }
                        } else if (info == '删除shard') {
                          const arr = ress.attachment.storage_hosts.substr(0, ress.attachment.storage_hosts.length - 1)
                          shard_ids = arr.split(';')
                          for (let e = 0; e < shard_ids.length; e++) {
                            // for(var item in shard_ids[e]){
                            //   var shard_idsValue=shard_ids[e][item];
                            if (this.shard[c].shard_id == shard_ids[e]) {
                              this.shard[c].icon = 'el-icon-circle-check'
                              this.shard[c].status = 'success'
                            }
                            // }
                          }
                        } else {
                          shard_ids = ip
                          if (this.shard[c].shard_id == shard_ids) {
                            this.shard[c].icon = 'el-icon-circle-check'
                            this.shard[c].status = 'success'
                          }
                        }
                      }
                    }
                  } else {
                    if (ress.attachment.hasOwnProperty('storage_state')) {
                      let shard_ids = ''
                      if (info == '添加shard') {
                        shard_ids = ress.attachment.shard_ids
                        for (let e = 0; e < shard_ids.length; e++) {
                          for (var item in shard_ids[e]) {
                            const shardgoing = {}
                            var shard_idsValue = shard_ids[e][item]
                            console.log(shard_idsValue)
                            const shard_text = item + ':' + shard_idsValue
                            console.log(shard_text)
                            shardgoing.title = shard_idsValue !== '' ? shard_text : '正在' + info
                            shardgoing.icon = 'el-icon-circle-check'
                            shardgoing.status = 'success'
                            shardgoing.description = ''
                            shardgoing.shard_id = shard_idsValue
                            this.shard.push(shardgoing)
                          }
                        }
                      } else if (info == '添加存储节点') {
                        shard_ids = ress.attachment.shard_hosts
                        for (let e = 0; e < shard_ids.length; e++) {
                          for (var item in shard_ids[e]) {
                            const shardgoing = {}
                            var shard_idsValue = shard_ids[e][item]
                            console.log(shard_idsValue)
                            const shard_text = item + ':' + shard_idsValue
                            console.log(shard_text)
                            shardgoing.title = shard_idsValue !== '' ? shard_text : '正在' + info
                            shardgoing.icon = 'el-icon-circle-check'
                            shardgoing.status = 'success'
                            shardgoing.description = ''
                            shardgoing.shard_id = shard_idsValue
                            this.shard.push(shardgoing)
                          }
                        }
                      } else if (info == '删除shard') {
                        const arr = ress.attachment.storage_hosts.substr(0, ress.attachment.storage_hosts.length - 1)
                        shard_ids = arr.split(';')
                        for (let e = 0; e < shard_ids.length; e++) {
                          // for(var item in shard_ids[e]){
                          //   let shardgoing={}
                          //   var shard_idsValue=shard_ids[e][item];
                          //   console.log(shard_idsValue);
                          //   const shard_text=item+':'+shard_idsValue;
                          //   console.log(shard_text);
                          const shardgoing = {}
                          shardgoing.title = shard_ids[e] !== '' ? shard_ids[e] : '正在' + info
                          shardgoing.icon = 'el-icon-circle-check'
                          shardgoing.status = 'success'
                          shardgoing.description = ''
                          shardgoing.shard_id = shard_ids[e]
                          this.shard.push(shardgoing)
                          // }
                        }
                      } else {
                        shard_ids = ip
                        const shardgoing = {}
                        shardgoing.title = shard_ids !== '' ? shard_ids : '正在' + info
                        shardgoing.icon = 'el-icon-circle-check'
                        shardgoing.status = 'success'
                        shardgoing.description = ''
                        shardgoing.shard_id = shard_ids
                        this.shard.push(shardgoing)
                      }
                    }
                  }
                  clearInterval(timer)
                  this.getList()
                }
              } else {
                console.log(4)
                if (ress.status == 'ongoing') {
                  if (ress.attachment.hasOwnProperty('storage_state')) {
                    if (ress.attachment.storage_state == 'done') {
                      this.storage_state = 'success'
                      this.shard_icon = 'el-icon-circle-check'
                      this.shard_title = info + '成功'
                      // 遍历存储节点改状态
                      if (this.shard.length > 0) {
                        for (let c = 0; c < this.shard.length; c++) {
                          let shard_ids = ''
                          if (info == '添加shard') {
                            shard_ids = ress.attachment.shard_ids
                            for (let e = 0; e < shard_ids.length; e++) {
                              for (var item in shard_ids[e]) {
                                var shard_idsValue = shard_ids[e][item]
                                if (this.shard[c].shard_id == shard_idsValue) {
                                  this.shard[c].icon = 'el-icon-circle-check'
                                  this.shard[c].status = 'success'
                                }
                              }
                            }
                          } else if (info == '添加存储节点') {
                            shard_ids = ress.attachment.shard_hosts
                            for (let e = 0; e < shard_ids.length; e++) {
                              for (var item in shard_ids[e]) {
                                var shard_idsValue = shard_ids[e][item]
                                if (this.shard[c].shard_id == shard_idsValue) {
                                  this.shard[c].icon = 'el-icon-circle-check'
                                  this.shard[c].status = 'success'
                                }
                              }
                            }
                          } else if (info == '删除shard') {
                            const arr = ress.attachment.storage_hosts.substr(0, ress.attachment.storage_hosts.length - 1)
                            shard_ids = arr.split(';')
                            for (let e = 0; e < shard_ids.length; e++) {
                              // for(var item in shard_ids[e]){
                              //   var shard_idsValue=shard_ids[e][item];
                              if (this.shard[c].shard_id == shard_ids[e]) {
                                this.shard[c].icon = 'el-icon-circle-check'
                                this.shard[c].status = 'success'
                              }
                              // }
                            }
                          } else {
                            shard_ids = ip
                            if (this.shard[c].shard_id == shard_ids) {
                              this.shard[c].icon = 'el-icon-circle-check'
                              this.shard[c].status = 'success'
                            }
                          }
                        }
                      }
                    } else if (ress.attachment.storage_state == 'failed') {
                      console.log(6)
                      this.storage_state = 'error'
                      this.shard_icon = 'el-icon-circle-close'
                      this.shard_title = info + '失败'
                      // 遍历存储节点改状态
                      if (this.shard.length > 0) {
                        for (let c = 0; c < this.shard.length; c++) {
                          let shard_ids = ''
                          if (info == '添加shard') {
                            shard_ids = ress.attachment.shard_ids
                            for (let e = 0; e < shard_ids.length; e++) {
                              for (var item in shard_ids[e]) {
                                var shard_idsValue = shard_ids[e][item]
                                if (this.shard[c].shard_id == shard_idsValue) {
                                  this.shard[c].icon = 'el-icon-circle-close'
                                  this.shard[c].status = 'error'
                                }
                              }
                            }
                          } else if (info == '添加存储节点') {
                            shard_ids = ress.attachment.shard_hosts
                            for (let e = 0; e < shard_ids.length; e++) {
                              for (var item in shard_ids[e]) {
                                var shard_idsValue = shard_ids[e][item]
                                if (this.shard[c].shard_id == shard_idsValue) {
                                  this.shard[c].icon = 'el-icon-circle-close'
                                  this.shard[c].status = 'error'
                                }
                              }
                            }
                          } else if (info == '删除shard') {
                            const arr = ress.attachment.storage_hosts.substr(0, ress.attachment.storage_hosts.length - 1)
                            shard_ids = arr.split(';')
                            for (let e = 0; e < shard_ids.length; e++) {
                              // for(var item in shard_ids[e]){
                              //   var shard_idsValue=shard_ids[e][item];
                              if (this.shard[c].shard_id == shard_ids[e]) {
                                this.shard[c].icon = 'el-icon-circle-close'
                                this.shard[c].status = 'error'
                              }
                              // }
                            }
                          } else {
                            shard_ids = ip
                            if (this.shard[c].shard_id == shard_ids) {
                              this.shard[c].icon = 'el-icon-circle-close'
                              this.shard[c].status = 'error'
                            }
                          }
                        }
                        this.shard_description = ress.error_info
                      }
                      // clearInterval(timer);
                    } else {
                      this.storage_state = 'process'
                      this.shard_icon = 'el-icon-loading'
                      this.shard_title = '正在' + info
                    }
                  }
                } else if (ress.status == 'failed') {
                  console.log(7)
                  this.storage_state = 'error'
                  this.shard_icon = 'el-icon-circle-close'
                  this.shard_title = info + '失败'
                  this.init_show = false
                  this.finish_title = info + '失败'
                  this.finish_icon = 'el-icon-circle-close'
                  this.finish_state = 'error'
                  this.init_active = 1
                  this.finish_description = ress.error_info

                  // 遍历存储节点改状态
                  if (this.shard.length > 0) {
                    for (let c = 0; c < this.shard.length; c++) {
                      let shard_ids = ''
                      if (ress.attachment.hasOwnProperty('shard_hosts')) {
                        if (info == '添加shard') {
                          shard_ids = ress.attachment.shard_ids
                          for (let e = 0; e < shard_ids.length; e++) {
                            for (var item in shard_ids[e]) {
                              var shard_idsValue = shard_ids[e][item]
                              if (this.shard[c].shard_id == shard_idsValue) {
                                this.shard[c].icon = 'el-icon-circle-close'
                                this.shard[c].status = 'error'
                              }
                            }
                          }
                        } else if (info == '添加存储节点') {
                          shard_ids = ress.attachment.shard_hosts
                          for (let e = 0; e < shard_ids.length; e++) {
                            for (var item in shard_ids[e]) {
                              var shard_idsValue = shard_ids[e][item]
                              if (this.shard[c].shard_id == shard_idsValue) {
                                this.shard[c].icon = 'el-icon-circle-close'
                                this.shard[c].status = 'error'
                              }
                            }
                          }
                        } else if (info == '删除shard') {
                          const arr = ress.attachment.storage_hosts.substr(0, ress.attachment.storage_hosts.length - 1)
                          shard_ids = arr.split(';')
                          for (let e = 0; e < shard_ids.length; e++) {
                            // for(var item in shard_ids[e]){
                            //   var shard_idsValue=shard_ids[e][item];
                            if (this.shard[c].shard_id == shard_ids[e]) {
                              this.shard[c].icon = 'el-icon-circle-close'
                              this.shard[c].status = 'error'
                            }
                            // }
                          }
                        } else {
                          shard_ids = ip
                          if (this.shard[c].shard_id == shard_ids) {
                            this.shard[c].icon = 'el-icon-circle-close'
                            this.shard[c].status = 'error'
                          }
                        }
                      }
                    }
                    this.shard_description = ress.error_info
                  }
                  clearInterval(timer)
                } else if (ress.status == 'done') {
                  this.storage_state = 'success'
                  this.shard_icon = 'el-icon-circle-check'
                  this.shard_title = info + '成功'

                  this.finish_title = info + '成功'
                  this.finish_icon = 'el-icon-circle-check'
                  this.finish_state = 'success'
                  this.init_active = 1
                  this.finish_description = ress.error_info
                  // 遍历存储节点改状态
                  if (this.shard.length > 0) {
                    for (let c = 0; c < this.shard.length; c++) {
                      let shard_ids = ''
                      if (ress.attachment.hasOwnProperty('shard_hosts')) {
                        if (info == '添加shard') {
                          shard_ids = ress.attachment.shard_ids
                          for (let e = 0; e < shard_ids.length; e++) {
                            for (var item in shard_ids[e]) {
                              var shard_idsValue = shard_ids[e][item]
                              if (this.shard[c].shard_id == shard_idsValue) {
                                this.shard[c].icon = 'el-icon-circle-check'
                                this.shard[c].status = 'success'
                              }
                            }
                          }
                        } else if (info == '添加存储节点') {
                          shard_ids = ress.attachment.shard_hosts
                          for (let e = 0; e < shard_ids.length; e++) {
                            for (var item in shard_ids[e]) {
                              var shard_idsValue = shard_ids[e][item]
                              if (this.shard[c].shard_id == shard_idsValue) {
                                this.shard[c].icon = 'el-icon-circle-check'
                                this.shard[c].status = 'success'
                              }
                            }
                          }
                        } else if (info == '删除shard') {
                          const arr = ress.attachment.storage_hosts.substr(0, ress.attachment.storage_hosts.length - 1)
                          shard_ids = arr.split(';')
                          for (let e = 0; e < shard_ids.length; e++) {
                            // for(var item in shard_ids[e]){
                            //   var shard_idsValue=shard_ids[e][item];
                            if (this.shard[c].shard_id == shard_ids[e]) {
                              this.shard[c].icon = 'el-icon-circle-check'
                              this.shard[c].status = 'success'
                            }
                            // }
                          }
                        } else {
                          shard_ids = ip
                          if (this.shard[c].shard_id == shard_ids) {
                            this.shard[c].icon = 'el-icon-circle-check'
                            this.shard[c].status = 'success'
                          }
                        }
                      }
                    }
                  }
                  clearInterval(timer)
                  this.getList()
                }
              }
            } else if (ress.attachment == null && ress.error_code == '70001' && ress.status == 'failed') {
              if (info == '添加shard' || info == '添加存储节点' || info == '删除shard' || info == '删除存储节点') {
                this.shard_show = true
                this.init_show = false
                this.computer_show = false
                this.storage_state = 'process'
                this.shard_icon = 'el-icon-loading'
                this.shard_title = '正在' + info
                if (i > 5) {
                  this.storage_state = 'error'
                  this.shard_icon = 'el-icon-circle-close'
                  this.shard_title = info + '失败'
                  clearInterval(timer)
                }
              }
            } else if (ress.attachment == null && ress.status == 'ongoing') {
              if (info == '添加shard' || info == '添加存储节点' || info == '删除shard' || info == '删除存储节点') {
                this.shard_show = true
                this.init_show = false
                this.computer_show = false
                this.storage_state = 'process'
                this.shard_icon = 'el-icon-loading'
                this.shard_title = '正在' + info
              }
            } else if (ress.attachment == null && ress.status == 'done') {
              if (info == '添加shard' || info == '添加存储节点') {
                this.shard_show = true
                this.init_show = false
                this.computer_show = false
                this.storage_state = 'success'
                this.shard_icon = 'el-icon-circle-check'
                this.shard_title = info + '成功'
              }
              clearInterval(timer)
            } else {
              if (info == '添加shard' || info == '添加存储节点' || info == '删除shard' || info == '删除存储节点') {
                this.shard_show = true
                this.init_show = false
                this.computer_show = false
                this.storage_state = 'error'
                this.shard_icon = 'el-icon-circle-close'
                this.shard_title = info + '失败'
              }
              clearInterval(timer)
            }
          }
        })
        if (i >= 86400) {
          clearInterval(timer)
        }
      }, 0)
    },
    getStatus(timer, data, i, action_name) {
      setTimeout(() => {
        const postarr = {}
        postarr.job_type = 'get_status'
        postarr.version = version_arr[0].ver
        postarr.job_id = data
        postarr.timestamp = timestamp_arr[0].time + ''
        postarr.paras = {}
        getEvStatus(postarr).then((res) => {
          if (res.status == 'done' || res.status == 'failed') {
            // clearInterval(timer);
            // this.info=res.error_info;
            if (res.status == 'done') {
              const newArrdone = {
                content: action_name + '成功',
                timestamp: getNowDate(),
                color: '#0bbd87',
                icon: 'el-icon-circle-check'
              }
              this.activities.push(newArrdone)
              this.getList()
              // this.dialogStatusVisible=false;
              clearInterval(timer)
            } else {
              if (res.attachment == null && res.error_code == '70001' && res.status == 'failed') {
                if (i > 5) {
                  const newArr = {
                    content: res.error_info,
                    timestamp: getNowDate(),
                    color: 'red',
                    icon: 'el-icon-circle-close'
                  }
                  this.activities.push(newArr)
                  clearInterval(timer)
                }
              } else {
                const newArr = {
                  content: res.error_info,
                  timestamp: getNowDate(),
                  color: 'red',
                  icon: 'el-icon-circle-close'
                }
                this.activities.push(newArr)
                clearInterval(timer)
              }
              // this.installStatus = true;
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
        })
        if (i >= 86400) {
          clearInterval(timer)
        }
      }, 0)
    },
    beforeDestroy() {
      clearInterval(this.timer)
      this.timer = null
    }
  }
}
</script>
<style>
.right_input_min {
  width: 25%;
}

.el-table__expanded-cell[class*=cell] {
  padding: 0px 0px;
}
</style>
<style scoped>
.el-input {
  width: inherit;
}
</style>
