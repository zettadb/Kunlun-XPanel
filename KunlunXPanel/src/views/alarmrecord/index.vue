<template>
  <div class="app-container">
    <div class="filter-container">
      <div class="table-list-search-wrap">
        <el-select
          v-model="listQuery.job_type"
          placeholder="请选择告警类型"
          class="list_search_select"
          style="width:150px;"
          clearable
        >
          <el-option
            v-for="item in alarmTypes"
            :key="item.id"
            :label="item.label"
            :value="item.id"
          />
        </el-select>
        <el-select
          v-model="listQuery.status"
          placeholder="请选择告警状态"
          class="list_search_select"
          style="width:150px;"
          clearable
        >
          <el-option label="未处理" value="unhandled" />
          <el-option label="已处理" value="handled" />
          <el-option label="忽略" value="ignore" />
        </el-select>
        <!-- <el-select v-model="listQuery.alarm_level" placeholder="请选择告警级别" class="list_search_select" style="width:150px;" clearable>
           <el-option
            v-for="item in alarmLevel"
            :key="item.id"
            :label="item.label"
            :value="item.id">
          </el-option>
        </el-select> -->
        <el-button icon="el-icon-search" @click="handleFilter">
          查询
        </el-button>
        <el-button icon="el-icon-refresh-right" @click="handleClear">
          重置
        </el-button>
        <!-- <el-button
         class="filter-item"
         type="primary"
         icon="el-icon-setting"
         @click="handleSet"
       >设置告警级别</el-button> -->
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
      style="width: 100%;margin-bottom: 20px;"
    >
      <el-table-column
        type="index"
        align="center"
        label="序号"
        width="50"
      />
      <el-table-column label="告警类型" align="center">
        <template slot-scope="{row}">
          <span class="link-type click_btn" @click="handleDetail(row)">{{ row.job_type }}</span>
        </template>
      </el-table-column>

      <el-table-column
        prop="object"
        align="center"
        label="告警对象"
      />
      <!-- <el-table-column
       prop="occur_timestamp"
       align="center"
       label="告警时间">
     </el-table-column> -->
      <el-table-column
        prop="alarm_status"
        align="center"
        label="告警状态"
      >
        <template slot-scope="scope">
          <span v-if="scope.row.status==='handled'">已处理</span>
          <span v-else-if="scope.row.status==='ignore'">忽略</span>
          <span v-else-if="scope.row.status==='unhandled'">未处理</span>
        </template>
      </el-table-column>

      <el-table-column
        prop="info"
        align="center"
        :show-overflow-tooltip="true"
        label="错误信息"
      />
      <div v-if="this.listQuery.status!=='unhandled'">
        <el-table-column
          prop="handle_time"
          align="center"
          label="处理时间"
        />
        <el-table-column
          prop="handler_name"
          align="center"
          label="处理人"
        />
      </div>

      <el-table-column
        v-if="this.listQuery.status=='unhandled'"
        label="操作"
        align="center"
        width="180"
        class-name="small-padding fixed-width"
      >
        <template slot-scope="{row}">
          <el-button type="primary" size="mini" @click="hanldeAlarm(row)">处理</el-button>
          <el-button type="warning" size="mini" @click="ignoreAlarm(row)">忽略</el-button>
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
  </div>
</template>
<script>
import { messageTip, createCode, gotoCofirm } from '@/utils'
import { getAlarmRecordList, update } from '@/api/alarmrecord/list'
import { alarm_type_arr, alarm_level_arr } from '@/utils/global_variable'
import Pagination from '@/components/Pagination'
import { getClusterMonitor } from '@/api/cluster/list'

export default {
  name: 'Alarmrecord',
  components: { Pagination },
  data() {
    return {
      tableKey: 0,
      list: null,
      listLoading: true,
      searchLoading: false,
      total: 0,
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
      // textMap: {
      //   update: "编辑计算机",
      //   create: "新增计算机",
      //   detail: "详情",
      //   upload:'批量导入'
      // },
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
      rules: {}

    }
  },
  created() {
    this.getList()
  },
  mounted() {
    let i = 0
    this.timer = setInterval(() => {
      this.getStatus(this.timer, i++)
      // this.getList()
      const queryParam = Object.assign({}, this.listQuery)
      // 模糊搜索
      getAlarmRecordList(queryParam).then(response => {
        this.list = response.list
        this.total = response.total
        this.listLoading = false
      })
    }, 60000)// 1min
  },
  destroyed() {
    clearInterval(this.timer)
    this.timer = null
  },
  methods: {
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
      this.listQuery.pageNo = 1
      this.listQuery.status = 'unhandled',
      this.alarm_level = ''
      this.getList()
    },
    getList() {
      this.listLoading = true
      const queryParam = Object.assign({}, this.listQuery)
      // 模糊搜索
      getAlarmRecordList(queryParam).then(response => {
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
      gotoCofirm(string).then((res) => {
        // 先执行删权限
        if (!res.value) {
          this.message_tips = 'code不能为空！'
          this.message_type = 'error'
          messageTip(this.message_tips, this.message_type)
        } else if (res.value == code) {
          const tempData = {}
          tempData.user_name = sessionStorage.getItem('login_username')
          tempData.status = 'handled'
          tempData.id = row.id
          update(tempData).then((response) => {
            const res = response
            if (res.code == 200) {
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
        if (res.code == 200) {
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
    }

  }
}
</script>
<style>
.right_input_min {
  width: 25%;
}
</style>
