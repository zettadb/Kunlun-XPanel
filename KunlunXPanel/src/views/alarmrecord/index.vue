<template>
  <div class="app-container">
    <el-dialog
      title="告警配置"
      :visible.sync="dialogRestoreVisible"
      custom-class="single_dal_view"
      :close-on-click-modal="false"
    >
      <el-form ref="restoreForm" :model="restoretemp" :rules="rules" label-position="left">
        <el-form-item label="告警类型:" prop="alrmType">
          <el-select v-model="restoretemp.alrmType" placeholder="请选择告警类型" class="list_search_select" clearable>
            <el-option v-for="item in alarmTypes" :key="item.id" :label="item.label" :value="item.id" />
          </el-select>
        </el-form-item>
        <el-form-item label="用户名称:" prop="user">
          <el-select v-model="restoretemp.user" placeholder="请选择告警类型" class="list_search_select" clearable>
            <el-option v-for="item in userList" :key="item.id" :label="item.username" :value="item.id" />
          </el-select>
        </el-form-item>
        <el-form-item label="提醒方式:" prop="alrm_to_user">
          <el-select
            v-model="restoretemp.alrm_to_user"
            placeholder="提醒方式"
            multiple
            class="list_search_select"
            clearable
          >
            <el-option key="0" label="系统提醒" value="system" />
            <el-option key="1" label="短信提醒" value="phone_message" />
            <el-option key="2" label="邮件提醒" value="mail" />
            <!-- <el-option key="3" label="微信推送" value="wechat" /> -->
          </el-select>
        </el-form-item>
        <el-form-item label="是否生效:" prop="status">
          <el-select v-model="restoretemp.status" placeholder="是否生效" class="list_search_select" clearable>
            <el-option key="0" label="有效" value="1" />
            <el-option key="1" label="禁用" value="0" />
          </el-select>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="dialogRestoreVisible = false">关闭</el-button>
        <el-button type="primary" @click="createData(restoretemp)">确认</el-button>
      </div>
    </el-dialog>

    <el-drawer title="告警配置" :visible.sync="table" direction="rtl" size="70%">

      <el-tabs v-model="activeName">
        <el-tab-pane label="告警管理" name="first">
          <div class="alarm" style="height: 800px;overflow-y: scroll">
            <el-table :data="alarmTypes">
              <el-table-column property="id" prop="id" label="告警ID" />
              <el-table-column property="label" prop="label" label="告警名称" />
              <el-table-column property="level" prop="level" label="告警级别">
                <template slot-scope="scope">
                  <el-select v-model="scope.row.level" placeholder="告警级别">
                    <el-option v-for="item in alarmLevel" :key="item.id" :label="item.id" :value="item.id" />
                  </el-select>
                </template>
              </el-table-column>
              <el-table-column property="threshold" prop="threshold" label="告警阈值">
                <template slot-scope="scope">
                  <span>告警{{ scope.row.threshold }} 次触发</span>
                </template>
              </el-table-column>
              <el-table-column property="accept_user" prop="accept_user" label="推送用户">
                <template slot-scope="{ row }">
                  <el-select v-model="row.accept_user" multiple clearable placeholder="推送用户">
                    <el-option v-for="item in userList" :key="item.id" :label="item.username" :value="item.id" />
                  </el-select>
                </template>
              </el-table-column>
              <el-table-column property="accept_user" prop="accept_user" label="推送方式">
                <template slot-scope="{ row }">
                  <el-select v-model="row.push_type" multiple clearable placeholder="推送方式">
                    <el-option key="0" label="系统提醒" value="system" />
                    <el-option key="1" label="短信提醒" value="phone_message" />
                    <el-option key="2" label="邮件提醒" value="mail" />
                  </el-select>
                </template>
              </el-table-column>
            </el-table>
          </div>

        </el-tab-pane>

        <el-tab-pane label="推送管理" name="second">
          <el-tabs :tab-position="tabPosition">
            <el-tab-pane label="短信配置">
              <el-form ref="phone_message_config" :model="phone_message_config" label-width="120px" :rules="rules">
                <el-form-item label="AccessKeyId:" prop="AccessKeyId">
                  <el-input v-model="phone_message_config.AccessKeyId" placeholder="请输入AccessKeyId" />
                </el-form-item>
                <el-form-item label="SecretKey:" prop="SecretKey">
                  <el-input v-model="phone_message_config.SecretKey" placeholder="请输入SecretKey" />
                </el-form-item>
                <el-form-item label="短信模版ID:" prop="TemplateId">
                  <el-input v-model="phone_message_config.TemplateId" placeholder="请输入短信模版ID" />
                </el-form-item>
                <el-form-item label="短信签名ID:" prop="SigId">
                  <el-input v-model="phone_message_config.SigId" placeholder="请输入短信签名ID" />
                </el-form-item>
                <el-form-item>
                  <el-button type="primary" @click="SaveMessageConfig()">保存</el-button>
                </el-form-item>
              </el-form>
            </el-tab-pane>

            <el-tab-pane label="阿里云邮件">
              <el-form ref="mail_config" :model="mail_config" label-width="120px" :rules="rules">
                <el-form-item label="AccessKeyId:" prop="AccessKeyId">
                  <el-input v-model="mail_config.AccessKeyId" placeholder="请输入AccessKeyId" />
                </el-form-item>
                <el-form-item label="SecretKey:" prop="SecretKey">
                  <el-input v-model="mail_config.SecretKey" placeholder="请输入SecretKey" />
                </el-form-item>
                <el-form-item label="发件邮箱:" prop="AccountName">
                  <el-input v-model="mail_config.AccountName" placeholder="请输入发件邮箱" />
                </el-form-item>
                <el-form-item>
                  <el-button type="primary" @click="SaveMailConfig()">保存</el-button>
                </el-form-item>
              </el-form>
            </el-tab-pane>
            <!-- <el-tab-pane label="服务号推送">
            </el-tab-pane> -->
          </el-tabs>
        </el-tab-pane>
      </el-tabs>
    </el-drawer>
    <div class="filter-container">
      <div class="table-list-search-wrap">
        <el-select
          v-model="listQuery.job_type"
          placeholder="请选择告警类型"
          class="list_search_select"
          style="width: 150px"
          clearable
        >
          <el-option v-for="item in alarmTypes" :key="item.id" :label="item.label" :value="item.id" />
        </el-select>
        <el-select
          v-model="listQuery.status"
          placeholder="请选择告警状态"
          class="list_search_select"
          style="width: 150px"
          clearable
        >
          <el-option label="未处理" value="unhandled" />
          <el-option label="已处理" value="handled" />
          <el-option label="忽略" value="ignore" />
        </el-select>
        <el-button icon="el-icon-search" @click="handleFilter"> 查询</el-button>
        <el-button icon="el-icon-refresh-right" @click="handleClear"> 重置</el-button>
        <el-button type="primary" icon="el-icon-plus" @click="addAlarm()"> 新增</el-button>
        <el-button type="primary" icon="el-icon-plus" @click="table = true"> 告警管理</el-button>
      </div>
      <div class="table-list-wrap" />
    </div>
    <el-table
      ref="multipleTable"
      :key="tableKey"
      v-loading="listLoading"
      :data="list"
      border
      highlight-current-row
      style="width: 100%; margin-bottom: 20px"
    >
      <el-table-column type="index" align="center" label="序号" width="50" />
      <el-table-column label="告警类型" align="center">
        <template slot-scope="{ row }">
          <span class="link-type click_btn" @click="handleDetail(row)">{{
            row.job_type
          }}</span>
        </template>
      </el-table-column>

      <el-table-column prop="object" align="center" label="告警对象" />
      <el-table-column prop="alarm_status" align="center" label="告警状态">
        <template slot-scope="scope">
          <span v-if="scope.row.status === 'handled'">已处理</span>
          <span v-else-if="scope.row.status === 'ignore'">忽略</span>
          <span v-else-if="scope.row.status === 'unhandled'">未处理</span>
        </template>
      </el-table-column>

      <el-table-column prop="info" align="center" :show-overflow-tooltip="true" label="错误信息" />
      <div v-if="this.listQuery.status !== 'unhandled'">
        <el-table-column prop="handle_time" align="center" label="处理时间" />
        <el-table-column prop="handler_name" align="center" label="处理人" />
      </div>

      <el-table-column
        v-if="this.listQuery.status == 'unhandled'"
        label="操作"
        align="center"
        width="180"
        class-name="small-padding fixed-width"
      >
        <template slot-scope="{ row }">
          <el-button type="primary" size="mini" @click="hanldeAlarm(row)">处理</el-button>
          <el-button type="warning" size="mini" @click="ignoreAlarm(row)">忽略</el-button>
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
  </div>
</template>
<script>
import { messageTip, createCode, gotoCofirm } from '@/utils'
import {
  getAlarmRecordList,
  updateAlarmConfig,
  update,
  getAlarmConfig,
  updateAlarmSet,
  getAlarmSetList,
  DeleteAlarmItem
} from '@/api/alarmrecord/list'
import { alarm_type_arr, alarm_level_arr } from '@/utils/global_variable'
import { getAccountList } from '@/api/system/account'
import Pagination from '@/components/Pagination'
import { getClusterMonitor } from '@/api/cluster/list'

export default {
  name: 'Alarmrecord',
  components: { Pagination },
  data() {
    const validateAlrmType = (rule, value, callback) => {
      console.log(value)
      if (!value) {
        callback(new Error('请输入，不能为空'))
      } else {
        callback()
      }
    }
    return {
      activeName: 'first',
      tabPosition: 'left',
      dialogRestoreVisible: false,
      tableKey: 0,
      list: null,
      listLoading: true,
      searchLoading: false,
      total: 0,
      phone_message_config: {
        AccessKeyId: '',
        SecretKey: '',
        TemplateId: '',
        SigId: '',
        Type: 'phone_message',
        id: 0
      },
      mail_config: {
        AccessKeyId: '',
        SecretKey: '',
        AccountName: '',
        Type: 'email',
        id: 0
      },
      listQuery: {
        pageNo: 1,
        pageSize: 10,
        job_type: '',
        status: 'unhandled',
        alarm_level: ''
      },
      alarmTypes: alarm_type_arr,
      alarmLevel: alarm_level_arr,
      dialogFormVisible: false,
      dialogEditVisible: false,
      dialogUploadVisible: false,
      dialogStatus: '',
      dialogDetail: false,
      message_tips: '',
      message_type: '',
      installStatus: false,
      dialogInfo: false,
      alarmJobInfo: '',
      info: '',
      row: {},
      machine_add_priv: JSON.parse(sessionStorage.getItem('priv')).machine_add_priv,
      machine_drop_priv: JSON.parse(sessionStorage.getItem('priv')).machine_drop_priv,
      machine_priv: JSON.parse(sessionStorage.getItem('priv')).machine_priv,
      timer: null,
      gridData: [],
      table: false,
      dialog: false,
      loading: false,
      userList: [],
      restoretemp: {
        id: 0,
        alrmType: '',
        user: '',
        alrm_to_user: '',
        status: ''
      },
      rules: {
        alrmType: [
          { required: true, trigger: 'blur', validator: validateAlrmType }
        ],
        user: [
          { required: true, trigger: 'blur', validator: validateAlrmType }
        ],
        alrm_to_user: [
          { required: true, trigger: 'blur', validator: validateAlrmType }
        ],
        status: [
          { required: true, trigger: 'blur', validator: validateAlrmType }
        ],
        AccessKeyId: [
          { required: true, trigger: 'blur', validator: validateAlrmType }
        ],
        SecretKey: [
          { required: true, trigger: 'blur', validator: validateAlrmType }
        ],
        AccountName: [
          { required: true, trigger: 'blur', validator: validateAlrmType }
        ],
        TemplateId: [
          { required: true, trigger: 'blur', validator: validateAlrmType }
        ],
        SigId: [
          { required: true, trigger: 'blur', validator: validateAlrmType }
        ]
      }
    }
  },

  created() {
    this.getList()
    this.getUserList()
    this.getAlarmSetList()
    this.getAlarmConfig()
  },
  mounted() {
    let i = 0
    this.timer = setInterval(() => {
      this.getStatus(this.timer, i++)
      // this.getList()
      const queryParam = Object.assign({}, this.listQuery)
      // 模糊搜索
      getAlarmRecordList(queryParam).then((response) => {
        this.list = response.list
        this.total = response.total
        this.listLoading = false
      })
    }, 60000) // 1min
  },
  destroyed() {
    clearInterval(this.timer)
    this.timer = null
  },
  methods: {
    SaveMailConfig() {
      // 保存邮件推送配置
      this.$refs['mail_config'].validate((valid) => {
        if (valid) {
          const tempData = this.mail_config
          console.log(tempData)
          updateAlarmConfig(tempData).then((response) => {
            const res = response
            if (res.code == 200) {
              this.message_tips = '成功'
              this.message_type = 'success'
              this.getList()
              this.getUserList()
              this.getAlarmSetList()
              this.getAlarmConfig()
              this.dialogRestoreVisible = false
            } else {
              this.message_tips = '忽略失败'
              this.message_type = 'error'
            }
            messageTip(this.message_tips, this.message_type)
          })
        }
      })
    },
    SaveMessageConfig() {
      // 保存短信推送配置
      this.$refs['phone_message_config'].validate((valid) => {
        if (valid) {
          const tempData = this.phone_message_config
          console.log(tempData)
          updateAlarmConfig(tempData).then((response) => {
            const res = response
            if (res.code === 200) {
              this.message_tips = '成功'
              this.message_type = 'success'
              this.getList()
              this.getUserList()
              this.getAlarmSetList()
              this.getAlarmConfig()
              this.dialogRestoreVisible = false
            } else {
              this.message_tips = '忽略失败'
              this.message_type = 'error'
            }
            messageTip(this.message_tips, this.message_type)
          })
        }
      })
    },
    addAlarm() {
      this.restoretemp = {
        id: 0,
        alrmType: '',
        user: '',
        alrm_to_user: '',
        status: ''
      }
      this.dialogRestoreVisible = true
    },

    handleDeleteAlram(row) {
      const code = createCode()
      const string = '将对告警规则进行处理操作,是否继续?code=' + code
      gotoCofirm(string)
        .then((res) => {
          // 先执行删权限
          if (!res.value) {
            this.message_tips = 'code不能为空！'
            this.message_type = 'error'
            messageTip(this.message_tips, this.message_type)
          } else if (res.value === code) {
            const tempData = {}
            tempData.id = row.id
            DeleteAlarmItem(tempData).then((response) => {
              const res = response
              if (res.code === 200) {
                this.dialogFormVisible = false
                this.message_tips = '处理成功'
                this.message_type = 'success'
                this.getUserList()
                this.getAlarmSetList()
                this.getAlarmConfig()
              } else {
                this.message_tips = '处理失败'
                this.message_type = 'error'
              }
              messageTip(this.message_tips, this.message_type)
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
    handleEditAlram(row) {
      console.log(row)
      this.dialogRestoreVisible = true
      this.restoretemp.id = row.id
      this.restoretemp.alrmType = row.alarm_type
      this.restoretemp.user = row.uid
      this.restoretemp.alrm_to_user = row.alarm_to_user.split(',')
      this.restoretemp.stauts = row.status
    },
    handleAlramTypeName(name) {
      const nameArr = name.split(',')
      const type_name = []
      for (let i = 0; i < nameArr.length; i++) {
        switch (nameArr[i]) {
          case 'system':
            type_name.push('系统消息')
            break
          case 'phone_message':
            type_name.push('短信提醒')
            break
          case 'mail':
            type_name.push('邮件提醒')
            break
          case 'wechat':
            type_name.push('微信推送')
            break
        }
      }
      return type_name.join(',')
    },

    handleUserTypeName(name) {
      let type_name = ''
      for (let i = 0; i < this.alarmTypes.length; i++) {
        if (this.alarmTypes[i].id === name) {
          type_name = this.alarmTypes[i].label
          break
        }
      }
      return type_name
    },
    handleUserName(uid) {
      if (this.userList.length === 0) {
        this.getUserList()
      }
      let user_name = ''
      for (let i = 0; i < this.userList.length; i++) {
        if (this.userList[i].id === uid) {
          user_name = this.userList[i].username
          break
        }
      }
      return user_name
    },
    handleDetail(row) {
      this.$alert(row.list, '详细信息', {
        dangerouslyUseHTMLString: true
      })
    },
    handleFilter() {
      this.listQuery.pageNo = 1
      this.getList()
    },
    handleClear() {
      this.listQuery.job_type = ''
      this.listQuery.pageNo = 1;
      (this.listQuery.status = 'unhandled'), (this.alarm_level = '')
      this.getList()
    },

    getAlarmConfig() {
      const queryParam = {}
      getAlarmConfig(queryParam).then(response => {
        console.log(response)
        const configItem = response.list
        const _this = this
        for (let i = 0; i < configItem.length; i++) {
          switch (configItem[i].type) {
            case 'email':
              const email = JSON.parse(configItem[i].message)
              _this.mail_config.AccessKeyId = email.AccessKeyId
              _this.mail_config.SecretKey = email.SecretKey
              _this.mail_config.AccountName = email.AccountName
              _this.mail_config.id = configItem[i].id
              break
            case 'phone_message':
              const phone_message = JSON.parse(configItem[i].message)
              _this.phone_message_config.AccessKeyId = phone_message.AccessKeyId
              _this.phone_message_config.SecretKey = phone_message.SecretKey
              _this.phone_message_config.TemplateId = phone_message.TemplateId
              const str = phone_message.SigId.replace(/u/gi, '\\u')
              _this.phone_message_config.SigId = eval("'" + str + "'")
              _this.phone_message_config.id = configItem[i].id
              break
          }
        }
      })
    },
    getUserList() {
      const queryParam = Object.assign({}, this.listQuery)
      queryParam.user_name = sessionStorage.getItem('login_username')
      // 模糊搜索
      getAccountList(queryParam).then(response => {
        this.userList = response.list
      })
    },

    getAlarmSetList() {
      const queryParam = {
        pageNo: 1,
        pageSize: 50,
        user_name: sessionStorage.getItem('login_username')
      }
      getAlarmSetList(queryParam).then((response) => {
        console.log(response)
        this.gridData = response.list
      })
    },

    getList() {
      this.listLoading = true
      const queryParam = Object.assign({}, this.listQuery)
      // 模糊搜索
      getAlarmRecordList(queryParam).then((response) => {
        this.list = response.list
        this.total = response.total
        setTimeout(() => {
          this.listLoading = false
        }, 0.5 * 1000)
      })
    },
    getStatus(timer, i) {
      setTimeout(() => {
        const queryParam = { user_name: sessionStorage.getItem('login_username') }
        getClusterMonitor(queryParam).then((res) => {
        })
        if (i >= 86400) {
          clearInterval(timer)
        }
      }, 0)
    },
    hanldeAlarm(row) {
      const code = createCode()
      const string = '将对' + row.object + row.job_type + '告警进行处理操作,是否继续?code=' + code
      gotoCofirm(string)
        .then((res) => {
          // 先执行删权限
          if (!res.value) {
            this.message_tips = 'code不能为空！'
            this.message_type = 'error'
            messageTip(this.message_tips, this.message_type)
          } else if (res.value === code) {
            const tempData = {}
            tempData.user_name = sessionStorage.getItem('login_username')
            tempData.status = 'handled'
            tempData.id = row.id
            update(tempData).then((response) => {
              const res = response
              if (res.code === 200) {
                this.dialogFormVisible = false
                this.message_tips = '处理成功'
                this.message_type = 'success'
                this.getList()
              } else {
                this.message_tips = '处理失败'
                this.message_type = 'error'
              }
              messageTip(this.message_tips, this.message_type)
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
    ignoreAlarm(row) {
      const tempData = {}
      tempData.user_name = sessionStorage.getItem('login_username')
      tempData.status = 'ignore'
      tempData.id = row.id
      update(tempData).then((response) => {
        const res = response
        if (res.code === 200) {
          this.dialogFormVisible = false
          this.message_tips = '忽略成功'
          this.message_type = 'success'
          this.getList()
        } else {
          this.message_tips = '忽略失败'
          this.message_type = 'error'
        }
        messageTip(this.message_tips, this.message_type)
      })
    },

    createData() {
      this.$refs['restoreForm'].validate((valid) => {
        if (valid) {
          const tempData = this.restoretemp
          console.log(valid)
          updateAlarmSet(tempData).then((response) => {
            const res = response
            if (res.code === 200) {
              this.message_tips = '成功'
              this.message_type = 'success'
              this.getList()
              this.getUserList()
              this.getAlarmSetList()
              this.dialogRestoreVisible = false
            } else {
              this.message_tips = '忽略失败'
              this.message_type = 'error'
            }
            messageTip(this.message_tips, this.message_type)
          })
        }
      })
    }
  }
}
</script>
<style>
.right_input_min {
  width: 25%;
}

#el-drawer__title {
  font-size: large;
}
</style>
