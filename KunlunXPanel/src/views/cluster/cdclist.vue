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
      style="width: 100%;margin-bottom: 20px;"
    >
      >
      <el-table-column type="index" align="center" label="序号" width="50" />

      <el-table-column prop="host_addr" align="center" label="IP地址" />
      <el-table-column prop="port" align="center" label="端口号" />
      <el-table-column prop="master" align="center" label="主节点" />
      <el-table-column prop="status" align="center" label="状态" />

      <el-table-column
        v-if="user_name === 'super_dba'"
        label="操作"
        align="center"
        width="300"
        class-name="small-padding fixed-width"
      >
        <template slot-scope="{row,$index}">
          <el-button v-if="user_name === 'super_dba'" size="mini" type="danger" @click="handleDelete(row, $index)">删除
          </el-button>
          <!-- <div v-text="info" v-show="installStatus===true" class="info"></div> -->
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
      <el-form ref="form" :rules="rules" :model="form" label-position="left">

        <el-row v-for="(table, index) in form.temp" :key="index">
          <el-col :span="10">
            <el-form-item
              :label="'IP地址('+index+'):'"
              label-width="140px"
              :prop="'temp.' + index + '.hostaddr'"
              :rules="{
                required: true,
                message: 'IP地址不能空',
                trigger: 'blur',
              }"
            >
              <el-input
                :key="index"
                v-model="form.temp[index].hostaddr"
                placeholder="请输入IP地址"
              />
            </el-form-item>
          </el-col>
          <el-col :span="8">
            <el-form-item
              label="端口号:"
              label-width="80px"
              :prop="'temp.' + index + '.port'"
              :rules="{
                required: true,
                message: '端口不能空',
                trigger: 'blur',
              }"
            >
              <el-input v-model="form.temp[index].port" placeholder="请输入端口号" />
            </el-form-item>
          </el-col>
          <el-col :span="4" style="margin-top: 4px">
            <div style="margin-left: 3px">
              <el-button v-if="index === 0" icon="el-icon-plus" size="small" @click="onPush(index)" />
              <el-button v-else icon="el-icon-minus" size="small" @click="onRemove(index)" />
            </div>
          </el-col>
        </el-row>

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
import { messageTip, handleCofirm, getNowDate } from '@/utils'
import {
  updateStorage,
  getEvStatus,
  editCdc,
  getCdcList,
  DeleteCdc

} from '@/api/cluster/list'
import { version_arr, storage_type_arr, timestamp_arr } from '@/utils/global_variable'
import Pagination from '@/components/Pagination'

export default {
  name: 'Account',
  components: { Pagination },
  data() {
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
    const validatePort = (rule, value, callback) => {
      console.log(value)
      const number = /^[0-9]*$/
      if (!value) {
        callback(new Error('请输入端口号'))
      } else if (!number.test(value)) {
        callback(new Error('端口号只允许输入数字'))
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
        name: ''
      },
      form: {
        temp: [{
          hostaddr: '',
          name: '',
          stype: '',
          port: '',
          user_name: ''
        }]
      },
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
        // hostaddr: [
        //   { required: true, trigger: 'blur', validator: validateName }
        // ],
        // name: [
        //   { required: true, trigger: 'blur', validator: validateName }
        // ],
        // stype: [
        //   { required: true, trigger: 'blur', validator: validateStype }
        // ],
        // port: [
        //   { required: true, trigger: 'blur', validator: validatePort }
        // ]
      }
    }
  },
  created() {
    this.getList()
  },
  destroyed() {
    clearInterval(this.timer)
    this.timer = null
  },
  methods: {
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
      getCdcList(queryParam).then(response => {
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
      this.form.temp = [{
        hostaddr: '',
        name: '',
        stype: 'HDFS',
        port: '',
        user_name: ''
      }]
    },
    onPush(index) {
      this.form.temp.push({
        hostaddr: '',
        name: '',
        stype: 'HDFS',
        port: '',
        user_name: ''
      })
    },
    onRemove(index) {
      this.form.temp.splice(index, 1)
    },
    handleCreate() {
      this.resetTemp()
      this.dialogStatus = 'create'
      this.dialogFormVisible = true
      this.dialogDetail = false
    },
    createData() {
      this.$refs['form'].validate((valid) => {
        if (valid) {
          const tempData = {}
          tempData.job_id = ''
          tempData.job_type = 'get_leader'
          tempData.version = '1.0'
          tempData.timestamp = timestamp_arr[0].time + ''
          tempData.user_name = sessionStorage.getItem('login_username')
          tempData.paras = this.form
          // tempData.host_addr = tempData.paras.hostaddr
          // tempData.port = tempData.paras.port
          // 发送接口
          editCdc(tempData).then(response => {
            const res = response
            // eslint-disable-next-line eqeqeq
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
      handleCofirm('此操作将永久删除该数据, 是否继续?').then(() => {
        const tempData = {}
        tempData.paras = { 'name': row.id }
        DeleteCdc(tempData).then((response) => {
          console.log(response)
        })
      }).catch(() => {
        console.log('quxiao')
        messageTip('已取消删除', 'info')
      })
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
          if (res.status === 'done' || res.status === 'failed') {
            if (res.status === 'done') {
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
              if (res.attachment == null && res.error_code === '70001' && res.status === 'failed') {
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
            }
          }
        })
        if (i >= 86400) {
          clearInterval(timer)
        }
      }, 0)
    }

  }
}
</script>
