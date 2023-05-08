<template>
  <div class="app-container">
    <el-form ref="form" :model="form" label-width="180px" :rules="rules">
      <el-form-item label="集群名称">
        <span>{{ form.name }}</span>
      </el-form-item>
      <el-form-item label="业务名称">
        <span>{{ form.nick_name }}</span>
      </el-form-item>
      <el-form-item label="shard名称:" prop="shard_id">
        <el-select v-model="form.shard_id" clearable placeholder="请选择shard名称">
          <el-option
            v-for="shard in shardList"
            :key="shard.id"
            :label="shard.name"
            :value="shard.id"
            @click.native="ChangeSaler(shard)"
          />
        </el-select>
      </el-form-item>
      <el-form-item label="需重做的备机节点:" prop="redolist">
        <el-select v-model="form.redolist" multiple placeholder="请选择需重做的备机节点" @change="change">
          <el-option
            v-for="item in options"
            :key="item.value"
            :label="item.label"
            :value="item.label"
          />
        </el-select>
      </el-form-item>
      <el-form-item label="是否从主节点上拉取数据:" prop="allow_pull_from_master">
        <el-radio v-model="form.allow_pull_from_master" label="1">是</el-radio>
        <el-radio v-model="form.allow_pull_from_master" label="0">否</el-radio>
      </el-form-item>
      <el-form-item v-if="form.allow_pull_from_master=='0'" label="主备延迟:" prop="allow_replica_delay">
        <el-input v-model="form.allow_replica_delay" class="right_input" placeholder="主备延迟">
          <i slot="suffix" style="font-style:normal;margin-right: 10px; line-height: 30px;">s</i>
        </el-input>
      </el-form-item>
      <el-form-item label="是否备份:" prop="need_backup">
        <el-radio v-model="form.need_backup" label="1">是</el-radio>
        <el-radio v-model="form.need_backup" label="0">否</el-radio>
      </el-form-item>
      <el-form-item v-if="form.need_backup=='1'" label="备份存储目标:" prop="hdfs_host">
        <el-select v-model="form.hdfs_host" placeholder="请选择">
          <el-option
            v-for="item in hdfs_options"
            :key="item.value"
            :label="item.label"
            :value="item.label"
          />
        </el-select>
      </el-form-item>
      <el-form-item label="限速:" prop="pv_limit">
        <el-input v-model="form.pv_limit" class="right_input" placeholder="限速">
          <i slot="suffix" style="font-style:normal;margin-right: 10px; line-height: 30px;">KB/s</i>
        </el-input>
      </el-form-item>

      <el-form-item>
        <el-button type="primary" @click="onSubmit(form)">保存</el-button>
      </el-form-item>
    </el-form>
    <!-- 重做备机状态框 -->
    <el-dialog
      :title="job_id"
      :visible.sync="dialogStatusRedoVisible"
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
              <span>{{ items.description }}</span>
              <div style="padding:20px;">
                <el-steps direction="vertical" :active="second_active">
                  <el-step
                    v-for="(item,index) of statusList[key].second"
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
        </el-steps>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import { getShards, getStandbyNode, getEvStatus, getBackupStorageList, rebuildNode } from '@/api/cluster/list'
import { messageTip, createCode, gotoCofirm } from '@/utils'
import { version_arr, timestamp_arr } from '@/utils/global_variable'

export default {
  name: 'List',
  props: {
    listsent: { typeof: Object }
  },
  data() {
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
        this.form.allow_pull_from_master = '1'
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
    return {
      form: {
        name: '',
        nick_name: '',
        id: '',
        shard_id: '',
        shard_name: '',
        redolist: [],
        allow_pull_from_master: '0',
        allow_replica_delay: '30',
        need_backup: '0',
        pv_limit: '10',
        hdfs_host: ''
      },
      shardList: [],
      options: [],
      hdfs_options: [],
      dialogStatusRedoVisible: false,
      active: 0,
      job_id: '',
      timer: null,
      statusList: [],
      init_active: 0,
      second_active: 0,
      rules: {
        shard_id: [
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
        ]
      }
    }
  },
  created() {
    // 获取分片名称
    getShards(this.listsent.id).then((response) => {
      const res = response
      if (res.code == 200) {
        this.shardList = res.list
      }
    })
    // 获取备份存储目标
    getBackupStorageList().then(response => {
      this.hdfs_options = []
      if (response.list !== null) {
        for (let i = 0; i < response.list.length; i++) {
          const arr = { 'value': i, 'label': response.list[i].hostaddr + '(' + response.list[i].port + ')' }
          this.hdfs_options.push(arr)
        }
      }
    })
  },
  mounted() {
    this.form.name = this.listsent.name
    this.form.nick_name = this.listsent.nick_name
    this.form.id = this.listsent.id
  },
  destroyed() {
    clearInterval(this.timer)
    this.timer = null
  },
  methods: {
    change() {
      if (this.options.length == this.form.redolist.length) {
        this.form.allow_pull_from_master = '1'
      } else {
        this.form.allow_pull_from_master = '0'
      }
    },
    onSubmit(row) {
      this.$refs['form'].validate((valid) => {
        if (valid) {
          const code = createCode()
          const string = '将对' + this.form.shard_name + '进行重做备机节点,是否继续?code=' + code
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
              tempData.job_type = 'rebuild_node'
              tempData.version = version_arr[0].ver
              tempData.timestamp = timestamp_arr[0].time + ''
              const rebuild = []
              const ipList = []
              for (let i = 0; i < row.redolist.length; i++) {
                const ip_arr = (row.redolist[i].substring(0, row.redolist[i].length - 1)).split('(')
                let pv_limit = ''
                if (!row.pv_limit) {
                  pv_limit = '10'
                } else {
                  pv_limit = row.pv_limit
                }
                const rb_paras = {}
                if (row.need_backup == '1') {
                  const hdfs_host = (row.hdfs_host.substring(0, row.hdfs_host.length - 1)).replace('(', ':')
                  rb_paras.hostaddr = ip_arr[0]
                  rb_paras.port = ip_arr[1]
                  rb_paras.need_backup = row.need_backup
                  rb_paras.hdfs_host = hdfs_host
                  rb_paras.pv_limit = pv_limit
                } else {
                  rb_paras.hostaddr = ip_arr[0]
                  rb_paras.port = ip_arr[1]
                  rb_paras.need_backup = row.need_backup
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
              if (row.allow_pull_from_master == '1') {
                paras.shard_id = row.shard_id,
                paras.cluster_id = this.form.id,
                paras.allow_pull_from_master = row.allow_pull_from_master,
                paras.rb_nodes = rebuild
              } else {
                paras.shard_id = row.shard_id
                paras.cluster_id = this.form.id
                paras.allow_pull_from_master = row.allow_pull_from_master
                paras.allow_replica_delay = row.allow_replica_delay
                paras.rb_nodes = rebuild
              }
              tempData.paras = paras
              this.dialogStatusRedoVisible = true
              // this.dialogRedoVisible=false;
              // console.log(tempData);return;
              // 发送接口
              rebuildNode(tempData).then(response => {
                const res = response
                if (res.status == 'accept') {
                  // this.dialogRedoVisible = false;
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
            } else {
              this.message_tips = 'code输入有误'
              this.message_type = 'error'
              messageTip(this.message_tips, this.message_type)
            }
          })
        }
      })
    },
    ChangeSaler(value) {
      if (!value) {
        this.form.primary_node = ''
      }
      // 获取主节点
      // const temp={};
      // temp.cluster_id=this.listsent.id;
      // temp.shard_id=value;
      // temp.ha_mode=this.listsent.ha_mode;
      // getShardPrimary(temp).then((res) => {
      //     if(res.list.length>0){
      //         this.form.primary_node=res.list[0].ip+'('+res.list[0].port+')';
      //     }
      // });
      this.form.shard_name = value.name
      // 获取备机节点
      const temps = {}
      temps.cluster_id = this.listsent.id
      temps.shard_id = value.id
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
    beforeRedoDestory() {
      clearInterval(this.timer)
      this.dialogStatusRedoVisible = false
      this.timer = null
    },
    resetform() {
      this.form = {
        name: '',
        nick_name: '',
        id: '',
        shard_id: '',
        shard_name: '',
        redolist: [],
        allow_pull_from_master: '0',
        allow_replica_delay: '30',
        need_backup: '0',
        pv_limit: '10',
        hdfs_host: ''
      }
    },
    getFStatus(timer, data, i, info, iparr) {
      setTimeout(() => {
        const postarr = {}
        postarr.job_type = 'get_status'
        postarr.version = version_arr[0].ver
        postarr.timestamp = timestamp_arr[0].time + ''
        postarr.job_id = data
        postarr.paras = {}
        this.job_id = '任务ID:' + data
        getEvStatus(postarr).then((res) => {
          if (info == '重做备机节点') {
            if (res.attachment !== null) {
              if (this.statusList.length == 0) {
                let statusgoing = {}
                if (res.status == 'failed') {
                  statusgoing = {
                    title: info + '失败',
                    icon: 'el-icon-circle-close',
                    status: 'error',
                    description: res.error_code,
                    second: []
                  }
                  this.statusList.push(statusgoing)
                  clearInterval(timer)
                } else {
                  // let statusgoing={}
                  // let iparr=[];
                  // for(let item in res.attachment){
                  //   if(item!=='job_step'){
                  //   let key=res.attachment[item];
                  //   this.$set(key, 'title', item)
                  //   iparr.push(key);
                  //   }
                  // }
                  for (let a = 0; a < iparr.length; a++) {
                    const steps = (res.attachment.job_step).replace(/\s+/g, '').split(',')
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
                      const steps = (res.attachment.job_step).replace(/\s+/g, '').split(',')
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
                if (res.status == 'ongoing') {
                  // let statusgoing={}
                  const iparr = []
                  for (const item in res.attachment) {
                    if (item !== 'job_step') {
                      const key = res.attachment[item]
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
                } else if (res.status == 'failed') {
                  const iparr = []
                  for (const item in res.attachment) {
                    if (item !== 'job_step') {
                      const key = res.attachment[item]
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
                    description: res.error_info,
                    second: ''
                  }
                  this.statusList.push(statusgoing)
                  clearInterval(timer)
                } else if (res.status == 'done') {
                  const iparr = []
                  for (const item in res.attachment) {
                    if (item !== 'job_step') {
                      const key = res.attachment[item]
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
                } else {
                  this.message_tips = res.error_info
                  this.message_type = 'error'
                  messageTip(this.message_tips, this.message_type)
                }
              }
            } else if (res.attachment == null && res.error_code == '70001' && res.status == 'failed') {
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
                  description: res.error_info,
                  second: []
                }
                this.statusList.push(statusgoing)
                // this.statusList.title=info+'失败';
                // this.statusList.icon='el-icon-circle-close';
                // this.statusList.status='error';
                // this.statusList.description=res.error_code;
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
<style scoped>
.el-input {
  width: inherit;
}
</style>
