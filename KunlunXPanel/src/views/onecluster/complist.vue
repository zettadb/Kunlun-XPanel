<template>
  <div class="app-container">
    <div class="filter-container">
      <div class="table-list-search-wrap">
        <el-input
          v-model="listQuery.hostaddr"
          class="list_search_keyword"
          placeholder="可输入ip搜索"
          @keyup.enter.native="handleFilter"
        />
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
        <el-button
          v-if="compute_node_create_priv==='Y'"
          class="filter-item"
          type="primary"
          icon="el-icon-plus"
          @click="handleCreate"
        >添加计算节点
        </el-button>
      </div>
    </div>
    <el-table
      ref="multipleTable"
      :key="tableKey"
      v-loading="listLoading"
      :data="list"
      border
      highlight-current-row
      style="width: 100%;margin-bottom: 20px;"
    >
      <el-table-column
        type="index"
        align="center"
        label="序号"
        width="50"
      />
      <!-- <el-table-column label="计算节点ID" align="center">
        <template slot-scope="{row}">
          <span class="link-type" @click="handleDetail(row)">{{ row.id }}</span>
        </template>
      </el-table-column> -->
      <!-- <el-table-column
        prop="db_cluster_id"
        align="center"
        label=" 集群ID">
      </el-table-column> -->
      <el-table-column
        prop="hostaddr"
        align="center"
        label="IP地址"
      >
        <template slot-scope="{row}">
          <span class="link-type click_btn" @click="handleDetail(row)">{{ row.hostaddr }}</span>
        </template>
      </el-table-column>
      <el-table-column
        prop="port"
        align="center"
        label=" 端口"
      />
      <el-table-column
        prop="name"
        align="center"
        label="计算节点名称"
      />
      <el-table-column
        prop="status"
        align="center"
        label="状态"
        width="170"
      >
        <template slot-scope="scope">
          <span v-if="scope.row.status==='active'" style="color: #00ed37">运行中</span>
          <span v-else-if="scope.row.status==='inactive'" style="color: red">异常</span>
          <span v-else-if="scope.row.status==='creating'">安装中</span>
          <span v-else-if="scope.row.status==='manual_stop'" style="color: #c7c9d1;">停止</span>
          <span v-else />
        </template>
      </el-table-column>
      <el-table-column
        prop="cpu_cores"
        align="center"
        label="cpu核数"
      />
      <!-- <el-table-column
        prop="max_mem_MB"
        align="center"
        label="最大内存（MB）">
      </el-table-column> -->
      <!--      <el-table-column-->
      <!--        prop="max_conns"-->
      <!--        align="center"-->
      <!--        label="最大连接数"-->
      <!--      />-->

      <el-table-column
        label="操作"
        align="center"
        width="400"
        class-name="small-padding fixed-width"
      >
        <template slot-scope="{row,$index}">
          <el-button v-if="row.status=='active'" size="mini" type="primary" @click="nodeMonitor(row)">节点监控
          </el-button>
          <el-button size="mini" type="primary" @click="handleSetCpu(row)">设置
          </el-button>
          <el-button v-if="row.status!=='active'" size="mini" type="primary" @click="handleControlNode(row,'start')">
            启用
          </el-button>
          <el-button
            v-if="row.status!=='manual_stop'"
            size="mini"
            type="primary"
            @click="handleControlNode(row,'stop')"
          >禁用
          </el-button>
          <el-button size="mini" type="primary" @click="handleControlNode(row,'restart')">重启</el-button>
          <el-button
            v-if="compute_node_drop_priv==='Y'"
            size="mini"
            type="danger"
            @click="handleDelete(row,$index)"
          >删除
          </el-button>
        </template>
      </el-table-column>
    </el-table>
    <pagination
      v-show="total>0"
      :total="total"
      :page.sync="listQuery.pageNo"
      :limit.sync="listQuery.pageSize"
      @pagination="getList"
    />
    <!-- 添加计算节点-->
    <el-dialog
      :title="textMap[dialogStatus]"
      :visible.sync="dialogNodeVisible"
      custom-class="single_dal_view"
      :close-on-click-modal="false"
    >
      <el-form
        ref="dataForm"
        :model="temp"
        :rules="rules"
        label-position="left"
        label-width="130px"
      >
        <el-form-item label="集群名称:" prop="name">
          <span>{{ temp.name }}</span>
        </el-form-item>
        <el-form-item label="业务名称:" prop="nick_name">
          <span>{{ temp.nick_name }}</span>
        </el-form-item>
        <el-form-item label="选择计算机:" prop="machinelist">
          <el-select v-model="temp.machinelist" multiple placeholder="请选择">
            <el-option
              v-for="machine in compmachines"
              :key="machine.id"
              :label="machine.hostaddr"
              :value="machine.hostaddr"
            />
          </el-select>
        </el-form-item>

        <el-form-item label="计算节点个数:" prop="nodes">
          <el-input v-model="temp.nodes" class="right_input" placeholder="请输入计算节点个数">
            <i slot="suffix" style="font-style:normal;margin-right: 10px; line-height: 30px;">个</i>
          </el-input>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="dialogNodeVisible = false">关闭</el-button>
        <el-button type="primary" @click="createData()">确认</el-button>
      </div>
    </el-dialog>
    <!-- 添加删除计算节点状态框 -->
    <el-dialog
      :title="job_id"
      :visible.sync="dialogStatusShowVisible"
      custom-class="single_dal_view"
      :close-on-click-modal="false"
      :before-close="beforeDestory"
    >
      <div style="width: 100%;background: #fff;padding:0 20px;">
        <el-steps direction="vertical" :active="init_active">
          <el-step v-if="init_show" :title="init_title" icon="el-icon-more" />
          <el-step
            v-if="computer_show"
            :title="computer_title"
            :status="computer_state"
            :icon="computer_icon"
            :description="computer_description"
          >
            <template slot="description">
              <span>{{ computer_description }}</span>
              <div style="padding:20px;">
                <el-steps direction="vertical" :active="comp_active">
                  <el-step
                    v-for="(item,index) of computer"
                    :key="index"
                    :title="item.title"
                    :icon="item.icon"
                    :status="item.status"
                    :description="item.description"
                  />
                </el-steps>
              </div>
            </template>
          </el-step>
          <!-- <el-step :title="shard_title"  :status="storage_state" :icon="shard_icon" :description="shard_description" v-if="shard_show">
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
          </el-step> -->
          <el-step
            v-if="finish_show"
            :title="finish_title"
            :icon="finish_icon"
            :description="finish_description"
            :status="finish_state"
          />
        </el-steps>
      </div>
    </el-dialog>
    <!--  状态框 -->
    <el-dialog
      :visible.sync="dialogStatusVisible"
      custom-class="single_dal_view"
      width="400px"
      :close-on-click-modal="false"
      :before-close="beforeSyncDestory"
    >
      <div class="block">
        <el-timeline>
          <el-timeline-item
            v-for="(activity, index) in activities"
            :key="index"
            :icon="activity.icon"
            :type="activity.type"
            :color="activity.color"
            :size="activity.size"
            :timestamp="activity.timestamp"
          >
            {{ activity.content }}
          </el-timeline-item>
        </el-timeline>
      </div>
    </el-dialog>
    <el-dialog :title="textMap[dialogStatus]" :visible.sync="dialogFormVisible" custom-class="single_dal_view">
      <div class="box">
        <div title="">
          <el-form
            ref="dataForm"
            :rules="rules"
            :model="temp"
            label-position="left"
            label-width="120px"
          >
            <el-form-item v-show="dialogStatus==='detail'" label="计算节点ID:" prop="id">
              <span>{{ temp.id }}</span>
            </el-form-item>
            <el-form-item v-show="dialogStatus==='detail'" label="集群ID:" prop="db_cluster_id">
              <span>{{ temp.db_cluster_id }}</span>
            </el-form-item>
            <el-form-item v-show="dialogStatus==='detail'" label="IP地址:" prop="hostaddr">
              <span>{{ temp.hostaddr }}</span>
            </el-form-item>
            <el-form-item v-show="dialogStatus==='detail'" label="端口:" prop="port">
              <span>{{ temp.port }}</span>
            </el-form-item>
            <el-form-item v-show="dialogStatus==='detail'" label="cpu核数:" prop="cpu_cores">
              <span>{{ temp.cpu_cores }}</span>
            </el-form-item>
          </el-form>
        </div>
        <div title="">
          <el-form
            ref="dataForm"
            :rules="rules"
            :model="temp"
            label-position="left"
            label-width="150px"
          >
            <el-form-item v-show="dialogStatus==='detail'" label="状态:" prop="status">
              <span>{{ temp.status|statusString }}</span>
            </el-form-item>
            <el-form-item v-show="dialogStatus==='detail'" label="计算节点名称:" prop="name">
              <span>{{ temp.name }}</span>
            </el-form-item>
            <el-form-item v-show="dialogStatus==='detail'" label="最大内存（MB）:" prop="max_mem_MB">
              <span>{{ temp.max_mem_MB }}</span>
            </el-form-item>
            <el-form-item v-show="dialogStatus==='detail'" label="最大连接数:" prop="max_conns">
              <span>{{ temp.max_conns }}</span>
            </el-form-item>
          </el-form>
        </div>
      </div>
    </el-dialog>

    <!--    CPU 隔离设置-->
    <el-dialog title="CPU资源设置" :visible.sync="dialogCpuVisible" custom-class="single_dal_view">
      <el-form
        ref="cpuForm"
        :model="cpu_paras"
        :rules="rules"
        label-position="left"
        label-width="100px"
      >
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
            <el-option
              v-for="item in cpu_paras_option"
              :key="item.value"
              :label="item.label"
              :value="item.label"
            />
          </el-select>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="dialogCpuVisible = false">关闭</el-button>
        <el-button type="primary" @click="SaveCpuSetData(cpu_paras)">确认</el-button>
      </div>
    </el-dialog>
  </div>
</template>
<script>
import { messageTip, createCode, gotoCofirm, getNowDate } from '@/utils'
import {
  computeList,
  getEvStatus,
  getCompMachine,
  addComps,
  getCompsCount,
  delComp,
  controlInstance, SetCpuCgroup
} from '@/api/cluster/list'
import { version_arr, timestamp_arr, ip_arr } from '@/utils/global_variable'
import Pagination from '@/components/Pagination'
import { pgsqlDashboard } from '@/api/grafana/list'

export default {
  name: 'Complist',
  components: { Pagination },
  filters: {
    statusString: function(value) {
      if (value == 'active') {
        return '运行中'
      } else if (value == 'creating') {
        return '安装中'
      } else if (value == 'inactive') {
        return '异常'
      } else if (value == 'offline') {
        return '停止'
      }
    }
  },
  props: {
    listsent: { typeof: Object }
  },
  data() {
    const validateNodes = (rule, value, callback) => {
      if (!value) {
        callback(new Error('请输入计算节点个数'))
      } else if (!(/^[0-9]+$/.test(value))) {
        callback(new Error('计算节点个数只能输入数字'))
      } else if (value > 256) {
        callback(new Error('计算节点个数不能大于256'))
      } else {
        callback()
      }
    }

    return {
      tableKey: 0,
      list: null,
      listLoading: true,
      searchLoading: false,
      total: 0,
      listQuery: {
        pageNo: 1,
        pageSize: 10,
        hostaddr: '',
        id: '',
        status: ''
      },
      temp: {
        nodes: '',
        name: '',
        nick_name: '',
        machinelist: '',
        cluster_id: ''
      },
      cpu_paras: {
        hostaddr: '',
        port: '',
        type: 'pg',
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
      dialogCpuVisible: false,
      dialogFormVisible: false,
      dialogEditVisible: false,
      dialogUploadVisible: false,
      dialogStatus: '',
      textMap: {
        create: '添加计算节点',
        detail: '详情'
      },
      dialogDetail: false,
      dialogNodeVisible: false,
      message_tips: '',
      message_type: '',
      installStatus: false,
      info: '',
      row: {},
      compute_node_create_priv: JSON.parse(sessionStorage.getItem('priv')).compute_node_create_priv,
      compute_node_drop_priv: JSON.parse(sessionStorage.getItem('priv')).compute_node_drop_priv,
      storage_status: false,
      comp_status: false,
      comp_active: 1,
      job_id: null,
      timer: null,
      compmachines: [],
      computer: [],
      computer_state: '',
      finish_state: '',
      computer_title: '',
      computer_icon: '',
      comp_active: 0,
      init_title: '',
      init_show: true,
      finish_show: false,
      computer_show: true,
      shard_show: true,
      init_active: 0,
      finish_title: '',
      finish_icon: '',
      finish_description: '',
      computer_description: '',
      job_id: '',
      dialogStatusShowVisible: false,
      dialogStatusVisible: false,
      activities: [],
      rules: {
        nodes: [
          { required: true, trigger: 'blur', validator: validateNodes }
        ]
      }
    }
  },
  created() {
    this.listQuery.id = this.listsent.id
    this.getList()
  },
  destroyed() {
    clearInterval(this.timer)
    this.timer = null
  },
  methods: {
    nodeMonitor(row) {
      const pparas = {}
      pparas['cluster_id'] = row.db_cluster_id
      pparas['hostaddr'] = row.hostaddr
      pparas['port'] = row.port
      pparas['user_name'] = sessionStorage.getItem('login_username')
      pgsqlDashboard(pparas).then((pyres) => {
        if (pyres.code == '200') {
          window.open(ip_arr[0].ip + pyres.url + '?orgId=1&refresh=5s')
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
    beforeSyncDestory() {
      clearInterval(this.timer)
      this.dialogStatusVisible = false
      this.timer = null
    },
    // 清除定时器
    beforeDestory() {
      clearInterval(this.timer)
      this.dialogStatusShowVisible = false
      this.timer = null
    },
    handleFilter() {
      this.listQuery.pageNo = 1
      this.getList()
    },
    handleClear() {
      this.listQuery.hostaddr = ''
      this.listQuery.status = ''
      this.listQuery.pageNo = 1
      this.listQuery.id = this.listsent.id
      this.getList()
    },
    getList() {
      this.listLoading = true
      const queryParam = Object.assign({}, this.listQuery)
      // 模糊搜索
      computeList(queryParam).then(response => {
        this.list = response.list
        this.total = response.total
        setTimeout(() => {
          this.listLoading = false
        }, 0.5 * 1000)
      })
    },
    resetTemp() {
      this.temp = {
        nodes: '1',
        name: '',
        nick_name: '',
        machinelist: '',
        cluster_id: ''
      }
    },
    handleCreate() {
      this.resetTemp()
      this.dialogStatus = 'create'
      this.dialogNodeVisible = true
      this.temp.name = this.listsent.name
      this.temp.nick_name = this.listsent.nick_name
      this.temp.cluster_id = this.listsent.id
      // 获取计算机器
      getCompMachine().then((res) => {
        this.compmachines = res.list
      })
      this.$nextTick(() => {
        this.$refs.dataForm.clearValidate()
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
              type: 'pg',
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
    createData() {
      this.$refs['dataForm'].validate((valid) => {
        if (valid) {
          const tempData = {}
          tempData.user_name = sessionStorage.getItem('login_username')
          tempData.job_id = ''
          tempData.job_type = 'add_comps'
          tempData.version = version_arr[0].ver
          tempData.timestamp = timestamp_arr[0].time + ''
          const machinelist = []
          this.temp.machinelist.forEach(item => {
            machinelist.push(item)
          })
          const paras = {}
          paras.comps = this.temp.nodes
          paras.cluster_id = this.temp.cluster_id
          paras.nick_name = this.listsent.nick_name
          if (machinelist.length > 0) {
            paras.computer_iplists = machinelist
          }
          tempData.paras = paras
          // console.log(tempData);return;
          // 新增计算节点
          addComps(tempData).then((response) => {
            const res = response
            if (res.status == 'accept') {
              this.dialogNodeVisible = false
              this.dialogStatusShowVisible = true
              // 把之前的数据清空
              this.computer = []
              this.computer_state = ''
              this.computer_title = ''
              this.computer_icon = ''
              this.comp_active = 0
              this.init_title = ''
              this.init_active = 0
              this.finish_title = ''
              this.finish_icon = ''
              this.finish_description = ''
              this.computer_description = ''
              this.job_id = ''
              this.timer = null
              this.init_show = true
              this.finish_show = false
              this.finish_state = ''
              const info = '添加计算节点'
              let i = 0
              this.getFStatus(this.timer, res.job_id, i++, info)
              this.timer = setInterval(() => {
                this.getFStatus(this.timer, res.job_id, i++, info)
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
      // 先判断该集群是否只有一个计算节点，如果只有一个计算节点不予许删除
      const temp = { id: row.db_cluster_id }
      getCompsCount(temp).then((res) => {
        if (res.total == 1) {
          messageTip('该集群当前有且仅有一个计算节点,不能进行删除操作', 'error')
        } else if (res.total > 1) {
          const code = createCode()
          const string = '此操作将永久删除计算节点' + row.hostaddr + '(' + row.port + '), 是否继续?code=' + code
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
              tempData.job_type = 'delete_comp'
              tempData.version = version_arr[0].ver
              tempData.timestamp = timestamp_arr[0].time + ''
              const paras = {}
              paras.cluster_id = row.db_cluster_id
              paras.nick_name = this.listsent.nick_name
              paras.comp_id = row.id
              tempData.paras = paras
              // console.log(tempData);return;
              delComp(tempData).then((response) => {
                const res = response
                if (res.status == 'accept') {
                  this.dialogStatusShowVisible = true
                  // 调获取状态接口
                  let i = 0
                  this.computer = []
                  this.computer_state = ''
                  this.computer_title = ''
                  this.computer_icon = ''
                  this.comp_active = 0
                  this.init_title = ''
                  this.init_active = 0
                  this.finish_title = ''
                  this.finish_icon = ''
                  this.finish_description = ''
                  this.computer_description = ''
                  this.job_id = ''
                  this.timer = null
                  const info = '删除计算节点'
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
      const string = '将对该计算节点' + row.hostaddr + '(' + row.port + ')进行' + action_name + '操作,是否继续?code=' + code
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
          paras.machine_type = 'computer'
          tempData.paras = paras
          const arr = {
            list: tempData,
            type: 'pgsql'
          }
          // console.log(arr);return

          controlInstance(arr).then((response) => {
            const res = response
            if (res.status == 'accept') {
              this.dialogStatusVisible = true
              this.activities = []
              const newArr = {
                content: '正在' + action_name + '计算节点' + row.hostaddr + '(' + row.port + ')',
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
    getFStatus(timer, data, i, info) {
      setTimeout(() => {
        const postarr = {}
        postarr.job_type = 'get_status'
        postarr.version = version_arr[0].ver
        postarr.job_id = data
        postarr.timestamp = timestamp_arr[0].time + ''
        postarr.paras = {}
        this.job_id = '任务ID:' + data
        getEvStatus(postarr).then((ress) => {
          // 新增计算节点
          if (ress.attachment !== null) {
            this.shard_show = false
            this.init_show = false
            this.computer_show = true
            // 计算
            if (ress.attachment.hasOwnProperty('computer_state')) {
              if (ress.attachment.computer_state == 'ongoing') {
                this.computer_state = 'process'
                this.computer_icon = 'el-icon-loading'
                this.computer_title = '正在' + info
              } else if (ress.attachment.computer_state == 'done') {
                this.computer_state = 'success'
                this.computer_icon = 'el-icon-circle-check'
                this.computer_title = info + '成功'
                // 遍历计算节点改状态
                if (this.computer.length > 0) {
                  for (let c = 0; c < this.computer.length; c++) {
                    const arr = ress.attachment.computer_hosts.substr(0, ress.attachment.computer_hosts.length - 1)
                    const computer_hosts = arr.split(';')
                    for (let e = 0; e < computer_hosts.length; e++) {
                      if (this.computer[c].title == computer_hosts[e]) {
                        this.computer[c].icon = 'el-icon-circle-check'
                        this.computer[c].status = 'success'
                      }
                    }
                  }
                }
              } else if (ress.attachment.computer_state == 'failed') {
                this.computer_state = 'error'
                this.computer_icon = 'el-icon-circle-close'
                this.computer_title = info + '失败'
                // 遍历计算节点改状态
                if (this.computer.length > 0) {
                  for (let c = 0; c < this.computer.length; c++) {
                    const arr = ress.attachment.computer_hosts.substr(0, ress.attachment.computer_hosts.length - 1)
                    const computer_hosts = arr.split(';')
                    for (let e = 0; e < computer_hosts.length; e++) {
                      if (this.computer[c].title == computer_hosts[e]) {
                        this.computer[c].icon = 'el-icon-circle-close'
                        this.computer[c].status = 'error'
                      }
                    }
                  }
                  this.computer_description = ress.error_info
                }
              } else {
                this.computer_state = 'process'
                this.computer_icon = 'el-icon-loading'
                this.computer_title = '正在' + info
              }
            }
            this.init_title = '正在' + info
            this.init_active = 1
            if (this.computer.length == 0) {
              if (ress.status == 'ongoing') {
                if (ress.attachment.hasOwnProperty('computer_hosts')) {
                  const arr = ress.attachment.computer_hosts.substr(0, ress.attachment.computer_hosts.length - 1)
                  const computer_hosts = arr.split(';')
                  if (ress.attachment.computer_state == 'done') {
                    for (let e = 0; e < computer_hosts.length; e++) {
                      const newArrgoing = {}
                      newArrgoing.title = computer_hosts[e]
                      newArrgoing.icon = 'el-icon-circle-check'
                      newArrgoing.status = 'success'
                      newArrgoing.description = ''
                      newArrgoing.computer_id = ress.attachment.computer_id
                      this.computer.push(newArrgoing)
                    }
                  } else {
                    for (let e = 0; e < computer_hosts.length; e++) {
                      const newArrgoing = {}
                      newArrgoing.title = computer_hosts[e]
                      newArrgoing.icon = 'el-icon-loading'
                      newArrgoing.status = 'process'
                      newArrgoing.description = ''
                      newArrgoing.computer_id = ress.attachment.computer_id
                      this.computer.push(newArrgoing)
                    }
                  }
                }
              } else if (ress.status == 'failed') {
                this.computer_state = 'error'
                this.computer_icon = 'el-icon-circle-close'
                this.computer_title = info + '失败'

                this.finish_title = info + '失败'
                this.finish_icon = 'el-icon-circle-close'
                this.finish_state = 'error'
                this.init_active = 1
                this.finish_description = ress.error_info
                // 遍历计算节点改状态
                if (this.computer.length > 0) {
                  for (let c = 0; c < this.computer.length; c++) {
                    if (ress.attachment.hasOwnProperty('computer_hosts')) {
                      const arr = ress.attachment.computer_hosts.substr(0, ress.attachment.computer_hosts.length - 1)
                      const computer_hosts = arr.split(';')
                      for (let e = 0; e < computer_hosts.length; e++) {
                        if (this.computer[c].title == computer_hosts[e]) {
                          this.computer[c].icon = 'el-icon-circle-close'
                          this.computer[c].status = 'error'
                        }
                      }
                    }
                  }
                  this.computer_description = ress.error_info
                }
                clearInterval(timer)
              } else if (ress.status == 'done') {
                this.computer_state = 'success'
                this.computer_icon = 'el-icon-circle-check'
                this.computer_title = info + '成功'

                this.finish_title = info + '成功'
                this.finish_icon = 'el-icon-circle-check'
                this.finish_state = 'success'
                this.init_active = 1
                this.finish_description = ress.error_info
                // 遍历计算节点改状态
                if (this.computer.length > 0) {
                  for (let c = 0; c < this.computer.length; c++) {
                    if (ress.attachment.hasOwnProperty('computer_hosts')) {
                      const arr = ress.attachment.computer_hosts.substr(0, ress.attachment.computer_hosts.length - 1)
                      const computer_hosts = arr.split(';')
                      for (let e = 0; e < computer_hosts.length; e++) {
                        if (this.computer[c].title == computer_hosts[e]) {
                          this.computer[c].icon = 'el-icon-circle-check'
                          this.computer[c].status = 'success'
                        }
                      }
                    }
                  }
                }
                clearInterval(timer)
                this.getList()
              }
            } else {
              if (ress.status == 'ongoing') {
                if (ress.attachment.hasOwnProperty('computer_state')) {
                  if (ress.attachment.computer_state == 'done') {
                    this.computer_state = 'success'
                    this.computer_icon = 'el-icon-circle-check'
                    this.computer_title = info + '成功'
                    // 遍历计算节点改状态
                    if (this.computer.length > 0) {
                      for (let c = 0; c < this.computer.length; c++) {
                        const arr = ress.attachment.computer_hosts.substr(0, ress.attachment.computer_hosts.length - 1)
                        const computer_hosts = arr.split(';')
                        for (let e = 0; e < computer_hosts.length; e++) {
                          if (this.computer[c].title == computer_hosts[e]) {
                            this.computer[c].icon = 'el-icon-circle-check'
                            this.computer[c].status = 'success'
                          }
                        }
                      }
                    }
                  } else if (ress.attachment.computer_state == 'failed') {
                    this.computer_state = 'error'
                    this.computer_icon = 'el-icon-circle-close'
                    this.computer_title = info + '失败'
                    // 遍历计算节点改状态
                    if (this.computer.length > 0) {
                      for (let c = 0; c < this.computer.length; c++) {
                        const arr = ress.attachment.computer_hosts.substr(0, ress.attachment.computer_hosts.length - 1)
                        const computer_hosts = arr.split(';')
                        for (let e = 0; e < computer_hosts.length; e++) {
                          if (this.computer[c].title == computer_hosts[e]) {
                            this.computer[c].icon = 'el-icon-circle-close'
                            this.computer[c].status = 'error'
                          }
                        }
                      }
                      this.computer_description = ress.error_info
                    }
                  } else {
                    this.computer_state = 'process'
                    this.computer_icon = 'el-icon-loading'
                    this.computer_title = '正在' + info
                  }
                }
              } else if (ress.status == 'failed') {
                this.computer_state = 'error'
                this.computer_icon = 'el-icon-circle-close'
                this.computer_title = info + '失败'

                this.finish_title = info + '失败'
                this.finish_icon = 'el-icon-circle-close'
                this.finish_state = 'error'
                this.init_active = 1
                this.finish_description = ress.error_info
                // 遍历计算节点改状态
                if (this.computer.length > 0) {
                  for (let c = 0; c < this.computer.length; c++) {
                    if (ress.attachment.hasOwnProperty('computer_hosts')) {
                      const arr = ress.attachment.computer_hosts.substr(0, ress.attachment.computer_hosts.length - 1)
                      const computer_hosts = arr.split(';')
                      for (let e = 0; e < computer_hosts.length; e++) {
                        if (this.computer[c].title == computer_hosts[e]) {
                          this.computer[c].icon = 'el-icon-circle-close'
                          this.computer[c].status = 'error'
                        }
                      }
                    }
                  }
                  this.computer_description = ress.error_info
                }
                clearInterval(timer)
              } else if (ress.status == 'done') {
                this.computer_state = 'success'
                this.computer_icon = 'el-icon-circle-check'
                this.computer_title = info + '成功'

                this.finish_title = info + '成功'
                this.finish_icon = 'el-icon-circle-check'
                this.finish_state = 'success'
                this.init_active = 1
                this.finish_description = ress.error_info
                // 遍历计算节点改状态
                if (this.computer.length > 0) {
                  for (let c = 0; c < this.computer.length; c++) {
                    if (ress.attachment.hasOwnProperty('computer_hosts')) {
                      const arr = ress.attachment.computer_hosts.substr(0, ress.attachment.computer_hosts.length - 1)
                      const computer_hosts = arr.split(';')
                      for (let e = 0; e < computer_hosts.length; e++) {
                        if (this.computer[c].title == computer_hosts[e]) {
                          this.computer[c].icon = 'el-icon-circle-check'
                          this.computer[c].status = 'success'
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
            this.shard_show = false
            this.init_show = false
            this.computer_show = true
            this.computer_state = 'process'
            this.computer_icon = 'el-icon-loading'
            this.computer_title = '正在' + info
            if (i > 5) {
              this.computer_state = 'error'
              this.computer_icon = 'el-icon-circle-close'
              this.computer_title = info + '失败'
              clearInterval(timer)
            }
          } else if (ress.attachment == null && ress.status == 'ongoing') {
            this.shard_show = false
            this.init_show = false
            this.computer_show = true
            this.computer_state = 'process'
            this.computer_icon = 'el-icon-loading'
            this.computer_title = '正在' + info
          } else if (ress.attachment == null && ress.status == 'done') {
            this.shard_show = false
            this.init_show = false
            this.computer_show = true
            this.computer_state = 'success'
            this.computer_icon = 'el-icon-circle-check'
            this.computer_title = info + '成功'
            clearInterval(timer)
          } else {
            this.shard_show = false
            this.init_show = false
            this.computer_show = true
            this.computer_state = 'error'
            this.computer_icon = 'el-icon-circle-close'
            this.computer_title = info + '失败'
            clearInterval(timer)
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
    }
  }
}
</script>
<style>
.right_input_min {
  width: 25%;
}

</style>
<style scoped>
.el-input {
  width: inherit;
}
</style>
<style lang='postcss'>
.box {
  width: 100%;
  /* height: 22px; */
  display: flex;
  flex-direction: row;
}

.box > div {
  /* height: 22px; */
  flex: 1;
}
</style>
