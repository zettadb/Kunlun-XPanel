<template>
  <div class="app-container">
    <div class="filter-container">
      <div class="table-list-search-wrap">
        <el-input
          v-model="listQuery.name"
          class="list_search_keyword"
          placeholder="可输入目标名称搜索"
          @keyup.enter.native="handleFilter"
        />
        <el-button icon="el-icon-search" @click="handleFilter"> 查询</el-button>
        <el-button icon="el-icon-refresh-right" @click="handleClear"> 重置</el-button>
        <el-button
          class="filter-item"
          type="primary"
          icon="el-icon-plus"
          @click="handleCreate"
        >新增
        </el-button>
        <div v-show="installStatus === true" class="info" v-text="info" />
      </div>
      <div class="table-list-wrap" />
    </div>

    <el-table
      :key="tableKey"
      v-loading="listLoading"
      :data="list"
      border
      highlight-current-row
      style="width: 100%; margin-bottom: 20px"
    >
      >
      <el-table-column type="index" align="center" label="序号" width="50" />

      <el-table-column prop="host_addr" align="center" label="IP地址" />
      <el-table-column prop="port" align="center" label="端口号" />
      <el-table-column prop="master" align="center" label="主节点" />
      <el-table-column prop="status" align="center" label="状态" />

      <el-table-column
        label="操作"
        align="center"
        width="300"
        class-name="small-padding fixed-width"
      >
        <template slot-scope="{ row, $index }">
          <el-button size="mini" type="danger" @click="handleDelete(row, $index)">删除
          </el-button>
        </template>
      </el-table-column>
    </el-table>

    <pagination
      v-show="total > 0"
      :total="total"
      :page.sync="listQuery.pageNo"
      :limit.sync="listQuery.pageSize"
      @pagination="getList"
    />

    <el-dialog :title="textMap[dialogStatus]" :visible.sync="dialogFormVisible" custom-class="single_dal_view">
      <el-form ref="dataForm" :rules="rules" :model="temp" label-position="left" label-width="140px">

        <el-form-item label="元数据:" prop="meta_db">
          <el-select v-model="temp.meta_db" clearable placeholder="请选择目标表集群" multiple style="width: 100%">
            <el-option
              v-for="item in MetaClusterList"
              :key="item.id"
              :label="item.hostaddr + ':' + item.port"
              :value="item.hostaddr + ':' + item.port"
            />
          </el-select>
        </el-form-item>
        <el-form-item label="元数据账号:" prop="meta_user">
          <el-input v-model="temp.meta_user" clearable placeholder="元数据账号" />
        </el-form-item>

        <el-form-item label="元数据密码:" prop="meta_passwd">
          <el-input v-model="temp.meta_passwd" clearable placeholder="元数据密码" />
        </el-form-item>

        <el-form-item label="目标表集群:" prop="cluster_name">
          <el-select
            v-model="temp.cluster_name"
            clearable
            placeholder="请选择目标表集群"
            @change="handleChangeCluster($event)"
          >
            <el-option v-for="item in clusterOptions" :key="item.id" :label="item.name" :value="item.id" />
          </el-select>
        </el-form-item>

        <el-form-item label="数据表:" prop="dump_tables">
          <el-cascader
            ref="tableOptions"
            v-model="temp.dump_tables"
            clearable
            placeholder="请选择 库名/模式/表"
            :options="srcTableOptions"
            filterable
            :props="props"
            @change="handleTableChange"
          />
        </el-form-item>

        <el-form-item label="plugin_name:" prop="plugin_name">
          <el-select v-model="temp.plugin_name" clearable placeholder="请选择目标表集群">
            <el-option
              v-for="item in plugin_name_list"
              :key="item.id"
              :label="item.plugin_name"
              :value="item.plugin_name"
            />
          </el-select>
        </el-form-item>

        <div
          v-if="temp.plugin_name === 'event_file'"
          style="border:1px solid #ddd;width:80%;padding:32px;border-radius: 10px;"
        >
          <el-form-item label="plugin_param:" prop="plugin_param">
            <el-input v-model="temp.plugin_param" clearable placeholder="/home/barney/kunlun_cdc/temp/event.log" />
          </el-form-item>
          <el-form-item label="udf_name:" prop="udf_name">
            <el-input v-model="temp.udf_name" clearable placeholder="test1" />
          </el-form-item>
        </div>

        <div v-else style="border:1px solid #ddd;width:80%;padding:32px;border-radius: 10px;">
          <el-form-item label="ip地址:" prop="kunlunsql_plugin_param.hostaddr">
            <el-input v-model="temp.kunlunsql_plugin_param.hostaddr" clearable placeholder="ip地址" />
          </el-form-item>
          <el-form-item label="端口:" prop="kunlunsql_plugin_param.port">
            <el-input v-model="temp.kunlunsql_plugin_param.port" clearable placeholder="端口" />
          </el-form-item>
          <el-form-item label="用户名:" prop="kunlunsql_plugin_param.user">
            <el-input v-model="temp.kunlunsql_plugin_param.user" clearable placeholder="用户名" />
          </el-form-item>
          <el-form-item label="密码:" prop="kunlunsql_plugin_param.password">
            <el-input v-model="temp.kunlunsql_plugin_param.password" clearable placeholder="密码" />
          </el-form-item>
          <el-form-item label="日志地址:" prop="kunlunsql_plugin_param.log_path">
            <el-input v-model="temp.kunlunsql_plugin_param.log_path" clearable placeholder="日志地址" />
          </el-form-item>
          <el-form-item label="udf_name:" prop="udf_name">
            <el-input v-model="temp.udf_name" clearable placeholder="日志地址" />
          </el-form-item>
        </div>

      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button v-show="!dialogDetail" @click="dialogFormVisible = false">关闭</el-button>
        <el-button
          v-show="!dialogDetail"
          type="primary"
          @click="dialogStatus === 'create' ? createData() : updateData(row)"
        >
          确认
        </el-button>
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
  </div>
</template>

<script>
import { messageTip, getNowDate } from '@/utils'
import {
  updateStorage,
  editCdc,
  getCdcList,
  getPGTableList,
  clusterOptions,
  getMetaClusterList
} from '@/api/cluster/list'
import { version_arr, storage_type_arr, timestamp_arr } from '@/utils/global_variable'
import Pagination from '@/components/Pagination'
import { Loading } from 'element-ui'

export default {
  name: 'Account',
  components: { Pagination },
  data() {
    const validateIPAddress = (rule, value, callback) => {
      console.log(value)
      const regexp = /^((2(5[0-5]|[0-4]\d))|[0-1]?\d{1,2})(\.((2(5[0-5]|[0-4]\d))|[0-1]?\d{1,2})){3}$/
      const valdata = value.split(',')
      let isCorrect = true
      if (valdata.length) {
        for (let i = 0; i < valdata.length; i++) {
          if (regexp.test(valdata[i]) === false) {
            isCorrect = false
          }
        }
      }
      // eslint-disable-next-line eqeqeq
      if (value === '') {
        return callback(new Error('请输入IP地址'))
      } else if (!isCorrect) {
        callback(new Error('请输入正确对IP地址'))
      } else {
        callback()
      }
    }
    const validateName = (rule, value, callback) => {
      console.log(value)
      if (!value) {
        callback(new Error('请输入目标名称'))
      } else {
        callback()
      }
    }
    const validateStype = (rule, value, callback) => {
      if (!value) {
        callback(new Error('请选择目标类型'))
      } else {
        callback()
      }
    }
    return {
      props: { multiple: true },
      tableKey: 0,

      list: null,
      MetaClusterList: null,
      listLoading: true,
      searchLoading: false,
      total: 0,

      clusterOptions: [],
      repartition_tables: [],
      srcTableOptions: [],
      listQuery: {
        pageNo: 1,
        pageSize: 10,
        name: ''
      },
      temp: {
        hostaddr: '',
        name: 'name',
        stype: '',
        port: '',
        user: 'ss',
        user_name: '',
        cluster_name: '',
        meta_db: '',
        meta_user: '',
        meta_passwd: '',
        dump_tables: '',
        kunlunsql_plugin_param: {
          hostaddr: '',
          port: '',
          user: '',
          password: '',
          log_path: ''
        },
        plugin_name: 'event_file',
        udf_name: '',
        plugin_param: ''
      },

      plugin_name_list: [
        {
          plugin_name: 'event_file',
          plugin_param: '',
          udf_name: ''
        },
        {
          plugin_name: 'event_kunlunsql',
          plugin_param: '',
          udf_name: ''
        }
      ],
      dialogFormVisible: false,
      dialogEditVisible: false,
      dialogStatus: '',
      textMap: {
        update: 'cdc 服务',
        create: 'cdc 服务',
        detail: '详情'
      },
      dialogDetail: false,
      message_tips: '',
      message_type: '',
      installStatus: false,
      info: '',
      row: {},
      stypelist: storage_type_arr,
      user_name: sessionStorage.getItem('login_username'),
      timer: null,
      dialogStatusVisible: false,
      activities: [],
      rules: {
        hostaddr: [{ required: true, trigger: 'blur', validator: validateIPAddress }],
        name: [{ required: true, trigger: 'blur', validator: validateName }],
        stype: [{ required: true, trigger: 'blur', validator: validateStype }],
        port: [{ required: true, trigger: 'blur', validator: validateName }],
        user: [{ required: true, trigger: 'blur', validator: validateName }],
        password: [{ required: true, trigger: 'blur', validator: validateName }],
        log_path: [{ required: true, trigger: 'blur', validator: validateName }],
        udf_name: [{ required: true, trigger: 'blur', validator: validateName }],
        plugin_param: [{ required: true, trigger: 'blur', validator: validateName }],
        meta_db: [{ required: true, trigger: 'blur', validator: validateName }],
        meta_user: [{ required: true, trigger: 'blur', validator: validateName }],
        meta_passwd: [{ required: true, trigger: 'blur', validator: validateName }],
        cluster_name: [{ required: true, trigger: 'blur', validator: validateName }],
        dump_tables: [{ required: true, trigger: 'blur', validator: validateName }],
        plugin_name: [{ required: true, trigger: 'blur', validator: validateName }]

      }
    }
  },

  mounted() {
    // 获取原集群名称
    clusterOptions({}).then((res) => {
      this.clusterOptions = res.list
      this.src_cluster_id = this.clusterOptions[0].id
      this.getPGTable()
    })

    getMetaClusterList({
      pageNo: 1,
      pageSize: 10
    }).then((response) => {
      console.log(response)
      this.MetaClusterList = response.list
    })
  },
  created() {
    this.getList()
  },
  destroyed() {
    clearInterval(this.timer)
    this.timer = null
  },
  methods: {

    handleChangeCluster(e) {
      console.log(e)
      this.src_cluster_id = e
      this.getPGTable()
    },

    getPGTable() {
      const username = sessionStorage.getItem('login_username')
      getPGTableList({ name: username, cluster_id: this.src_cluster_id }).then((res) => {
        this.srcTableOptions = res.list
      })
    },
    handleTableChange() {
      const nodeInfos = this.$refs['tableOptions'].getCheckedNodes()
      console.log(nodeInfos)
    },
    onDstClusterChange(val) {
      if (val === '') {
        this.ditTableOptions = []
      } else {
        const loading = Loading.service({ target: 'form' })
        getPGTableList({ cluster_id: val })
          .then((res) => {
            this.ditTableOptions = res.list
          })
          .finally(function() {
            loading.close()
          })
      }
      this.form.repartition_tables.forEach((v, i) => {
        this.form.repartition_tables[i].ditTable = []
      })
    },
    changeValue(value) {
      console.log(value)
      this.temp.stype = value
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
      this.listQuery.name = ''
      this.listQuery.pageNo = 1
      this.getList()
    },
    getList() {
      this.listLoading = true
      this.installStatus = false
      const queryParam = Object.assign({}, this.listQuery)
      getCdcList(queryParam).then((response) => {
        if (response.list !== false) {
          this.list = response.list
          this.total = response.total
        } else {
          this.list = []
          this.total = 0
        }
        setTimeout(() => {
          this.listLoading = false
        }, 0.5 * 1000)
      })
    },
    resetTemp() {
      this.temp = {
        hostaddr: '',
        name: 'name',
        stype: '',
        port: '',
        user: '',
        user_name: '',
        cluster_name: '',
        meta_db: '',
        meta_user: '',
        meta_passwd: '',
        dump_tables: '',
        kunlunsql_plugin_param: {
          hostaddr: '',
          port: '',
          user: '',
          password: '',
          log_path: ''
        },
        plugin_name: 'event_file',
        udf_name: '',
        plugin_param: ''
      }
    },
    handleCreate() {
      this.resetTemp()
      this.dialogStatus = 'create'
      this.dialogFormVisible = true
      this.dialogDetail = false
      this.$nextTick(() => {
        this.$refs.dataForm.clearValidate()
      })
    },
    createData() {
      const _this = this
      this.$refs['dataForm'].validate((valid) => {
        if (valid) {
          const tempData = {}
          tempData.job_id = ''
          tempData.job_type = 'add_dump_table'
          tempData.version = '1.0'
          tempData.timestamp = timestamp_arr[0].time + ''
          tempData.user_name = sessionStorage.getItem('login_username')

          let cluster_name = ''
          this.clusterOptions.forEach((v) => {
            if (v.id === _this.temp.cluster_name) {
              cluster_name = v.name
            }
          })

          const dump_tables = []
          this.temp.dump_tables.forEach((v) => {
            dump_tables.push(v[0] + '_$$_' + v[1] + '.' + v[2])
          })

          let output_plugins = []
          if (this.temp.plugin_name === 'event_file') {
            output_plugins = [{
              plugin_name: 'event_file',
              plugin_param: this.temp.plugin_param,
              udf_name: this.temp.udf_name
            }]
          } else {
            output_plugins = [{
              plugin_name: 'event_kunlunsql',
              plugin_param: JSON.stringify(this.temp.kunlunsql_plugin_param),
              udf_name: this.temp.udf_name
            }]
          }
          const param = {
            meta_db: this.temp.meta_db.join(','),
            meta_user: this.temp.meta_user,
            meta_passwd: this.temp.meta_passwd,
            cluster_name: cluster_name,
            dump_tables: dump_tables.join(','),
            output_plugins: output_plugins
          }
          console.log(param)
          tempData.paras = param
          // 发送接口
          editCdc(tempData).then((response) => {
            const res = response
            if (res.status === 'accept') {
              this.dialogFormVisible = false
              this.dialogStatusVisible = true
              this.activities = []
              const newArr = {
                content: '正在新增备份存储目标',
                timestamp: getNowDate(),
                size: 'large',
                type: 'primary',
                icon: 'el-icon-more'
              }
              this.activities.push(newArr)
              // 调获取状态接口
              let i = 0
              const action_name = '新增备份存储目标'
              this.timer = setInterval(() => {
                this.getStatus(this.timer, res.job_id, i++, action_name)
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
    handleDetail(row) {
      this.dialogStatus = 'detail'
      this.dialogFormVisible = true
      this.temp = Object.assign({}, row)
      this.dialogDetail = true
    },
    handleUpdate(row) {
      this.temp = Object.assign({}, row)
      this.dialogStatus = 'update'
      this.dialogFormVisible = true
      this.dialogDetail = false
      this.$nextTick(() => {
        this.$refs['dataForm'].clearValidate()
      })
    },
    updateData() {
      this.$refs['dataForm'].validate((valid) => {
        if (valid) {
          const tempData = {}
          tempData.job_id = ''
          tempData.job_type = 'update_backup_storage'
          tempData.version = version_arr[0].ver
          tempData.timestamp = timestamp_arr[0].time + ''
          tempData.user_name = sessionStorage.getItem('login_username')
          tempData.paras = Object.assign({}, this.temp)
          updateStorage(tempData).then((response) => {
            const res = response
            // eslint-disable-next-line eqeqeq
            if (res.status === 'accept') {
              this.dialogFormVisible = false
              this.dialogStatusVisible = true
              this.activities = []
              const newArr = {
                content: '正在编辑备份存储目标',
                timestamp: getNowDate(),
                size: 'large',
                type: 'primary',
                icon: 'el-icon-more'
              }
              this.activities.push(newArr)
              // 调获取状态接口
              let i = 0
              const action_name = '编辑备份存储目标'
              this.timer = setInterval(() => {
                this.getStatus(this.timer, res.job_id, i++, action_name)
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
    handleDelete(row) {

    },

    getStatus(timer, data, i, action_name) {

    }
  }
}
</script>
