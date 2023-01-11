<template>
  <div class="app-container">
    <el-form id="form" ref="form" :model="form" :rules="rules" label-width="140px">
      <el-form-item label="集群名称">
        <span>{{ form.name }}</span>
      </el-form-item>
      <el-form-item label="业务名称">
        <span>{{ form.nick_name }}</span>
      </el-form-item>
      <el-form-item label="目标表集群:" prop="dst_cluster_id">
        <el-select v-model="form.dst_cluster_id" clearable placeholder="请选择目标表集群" style="width:100%;"
          @change="onDstClusterChange">
          <el-option v-for="item in clusterOptions" :key="item.id" :label="item.name" :value="item.id" />
        </el-select>
      </el-form-item>
      <el-row v-for="(table, index) in form.backup" :key="table.key">
        <el-col :span="10">
          <el-form-item label="备份表:" :prop="'backup.' + index + '.db_table'" :rules="{
            required: true, message: '备份表不能为空', trigger: 'blur'
          }">
            <el-cascader :key="'srcTable' + index" v-model="form.backup[index].db_table" style="width: 100%" clearable
              placeholder="请选择 库名/模式/表" :options="tableOptions" filterable />
          </el-form-item>
        </el-col>
        <el-col :span="10">
          <el-form-item label="恢复开始时间:" :prop="'backup.' + index" :rules="rules.time">

            <el-date-picker v-model="form.backup[index].startTime" type="datetime" value-format="yyyy-MM-dd HH:mm:ss"
              placeholder="起始时间" />
            <!--            <el-time-select-->
            <!--              :key="'startTime' + index"-->
            <!--              v-model="form.backup[index].startTime"-->
            <!--              style="width: 48%"-->
            <!--              placeholder="起始时间"-->
            <!--              :arrow-control="true"-->
            <!--              :picker-options="{-->
            <!--                start: '00:00',-->
            <!--                step: '00:30',-->
            <!--                end: '24:00'-->
            <!--              }"-->
            <!--            />-->
          </el-form-item>
        </el-col>

      </el-row>
      <el-form-item>
        <el-button type="primary" @click="onSubmit(form)">保存</el-button>
        <!-- <el-button>取消</el-button> -->
      </el-form-item>
    </el-form>

    <el-dialog :visible.sync="dialogStatusVisible" custom-class="single_dal_view" width="400px"
      :close-on-click-modal="false" :before-close="beforeRestoreDestory">
      <div class="block">
        <el-timeline>
          <el-timeline-item v-for="(activity, index) in activities" :key="index" :icon="activity.icon"
            :type="activity.type" :color="activity.color" :size="activity.size" :timestamp="activity.timestamp">
            {{ activity.content }}
          </el-timeline-item>
        </el-timeline>
      </div>
    </el-dialog>
  </div>

</template>

<script>
import { clusterOptions, getEvStatus, getPGTableList, tableRepartition } from '@/api/cluster/list'
import { timestamp_arr, version_arr } from '@/utils/global_variable'
import { getNowDate, messageTip } from '@/utils'

export default {
  name: 'LogicalBackup',
  props: {
    listsent: { typeof: Object }
  },
  data() {
    const checkTimeRange = (rule, value, cb) => {
      if (value.startTime === '') {
        return cb(new Error('请选择备份时间范围'))
      }
      cb()
    }
    return {
      form: {
        src_cluster_id: '',
        dst_cluster_id: '',
        name: '',
        nick_name: '',
        backup: [
          { db_table: [], backup_time: '', startTime: '', endTime: '' }
        ],
        id: '',
        clusterOptions: []

      },
      dst_cluster_id: 0,
      dialogStatusVisible: false,
      activities: [],
      rules: {
        time: [
          { validator: checkTimeRange, trigger: 'blur' }
        ]
      },
      tableOptions: []
    }
  },
  mounted() {
    this.form.name = this.listsent.name
    this.form.nick_name = this.listsent.nick_name
    this.form.id = this.listsent.id
    getPGTableList({ cluster_id: this.form.id, all: '1' }).then(res => {
      this.tableOptions = res.list
    })
    // 获取原集群名称
    clusterOptions({}).then((res) => {
      this.clusterOptions = res.list
    })
  },

  methods: {
    beforeRestoreDestory() {
      clearInterval(this.timer)
      this.dialogStatusVisible = false
      this.timer = null
    },
    clusterOptions,
    onDstClusterChange(e) {
      console.log(e)
      this.dst_cluster_id = e
    },
    onPush(index) {
      this.form.backup.push({ db_table: [], backup_time: '', startTime: '', endTime: '' })
    },
    onRemove(index) {
      this.form.backup.splice(index, 1)
    },
    onSubmit(row) {
      console.info(this.form)

      this.$refs['form'].validate((valid) => {
        if (valid) {
          const data = {}
          data.user_name = sessionStorage.getItem('login_username')
          data.job_id = ''
          data.version = version_arr[0].ver
          data.job_type = 'logical_restore'
          data.timestamp = timestamp_arr[0].time + ''
          const paras = {}
          const backup = this.form.backup.map(v => {
            return {
              db_table: v.db_table[0] + '_$$_public.' + v.db_table[1],
              backup_time: v.startTime
            }
          })
          console.log(backup)
          paras.src_cluster_id = this.form.id
          // paras.backup = backup[0]
          paras.db_table = backup[0].db_table
          paras.restore_time = backup[0].backup_time

          paras.dst_cluster_id = this.dst_cluster_id
          data.paras = paras
          console.info(data)
          tableRepartition(data).then(res => {
            if (res.code === 200) {
              this.form.backup = [
                { db_table: [], backup_time: '', startTime: '', endTime: '' }
              ]
            }
            if (res.status === 'accept') {
              this.dialogRetreatedVisible = false
              this.dialogStatusVisible = true
              this.activities = []
              const newArr = {
                content: '逻辑恢复中...',
                timestamp: getNowDate(),
                size: 'large',
                type: 'primary',
                icon: 'el-icon-more'
              }
              this.activities.push(newArr)
              let i = 0
              this.timer = null
              this.timer = setInterval(() => {
                this.getStatus(this.timer, res.job_id, i++)
              }, 1000)
            } else if (res.status === 'ongoing') {
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
    getStatus(timer, data, i) {
      setTimeout(() => {
        const postarr = {}
        postarr.job_type = 'get_status'
        postarr.version = version_arr[0].ver
        postarr.job_id = data
        postarr.timestamp = timestamp_arr[0].time + ''
        postarr.paras = {}
        getEvStatus(postarr).then((res) => {
          const error_info = res.error_info
          if (res.status === 'done' || res.status === 'failed') {
            clearInterval(timer)
            // this.info=res.error_info;
            if (res.status === 'done') {
              const newArrdone = {
                content: '集群回档成功',
                timestamp: getNowDate(),
                color: '#0bbd87',
                icon: 'el-icon-circle-check'
              }
              this.activities.push(newArrdone)
              // }
              this.getList()
              // this.dialogStatusVisible=false;
            } else {
              const newArr = {
                content: error_info,
                timestamp: getNowDate(),
                color: 'red',
                icon: 'el-icon-circle-close'
              }
              this.activities.push(newArr)
              // this.installStatus = true;
            }
          } else {
            // if (error_info) {
            //   const newArrgoing = {
            //     content: error_info,
            //     timestamp: getNowDate(),
            //     color: '#0bbd87'
            //   }
            //   this.activities.push(newArrgoing)
            // }
            // this.info=res.error_info;
            // this.installStatus = true;
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
