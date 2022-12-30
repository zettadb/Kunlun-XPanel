<template>
  <div class="app-container">
    <el-form ref="form" :model="form" label-width="240px">
      <el-form-item label="集群ID:">
        <span>{{ form.id }}</span>
      </el-form-item>
      <el-form-item label="集群名称:">
        <span>{{ form.name }}</span>
      </el-form-item>
      <el-form-item label="业务名称:">
        <span>{{ form.nick_name }}</span>
      </el-form-item>
      <el-form-item label="高可用模式:">
        <span>{{ form.ha_mode }}</span>
      </el-form-item>
      <el-form-item label="shard分配:">
        <span>{{ form.shardtotal }}</span>
      </el-form-item>
      <el-form-item label="计算节点总个数:">
        <span>{{ form.comptotal + '个' }}</span>
      </el-form-item>
      <el-form-item label="缓冲池大小:">
        <span>{{ form.buffer_pool + 'MB' }}</span>
      </el-form-item>
      <el-form-item label="每shard中强同步备机应当个数:">
        <span>{{ form.fullsync_level + '个' }}</span>
      </el-form-item>
      <el-form-item>
        <el-button v-if="delflag" type="danger" @click="onSubmit(form)">删除集群</el-button>
        <!-- <el-button @click="onDel()">取消</el-button> -->
      </el-form-item>
    </el-form>
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
                  >
                    <!-- <template slot="description">
                  <el-button size="mini" type="primary"  @click="thisDetail(item.computer_id)">详情</el-button>
                </template> -->
                  </el-step>
                </el-steps>
              </div>
            </template>
          </el-step>
          <el-step
            v-if="shard_show"
            :title="shard_title"
            :status="storage_state"
            :icon="shard_icon"
            :description="shard_description"
          >
            <template slot="description">
              <span>{{ shard_description }}</span>
              <div style="padding:20px;">
                <el-steps direction="vertical" :active="shard_active">
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
  </div>
</template>

<script>
import { messageTip, createCode, gotoCofirm } from '@/utils'
import { delCluster, getEvStatus, uAssign } from '@/api/cluster/list'
import { version_arr, timestamp_arr } from '@/utils/global_variable'

export default {
  name: 'Commonset',
  props: {
    listsent: { typeof: Object }
  },
  data() {
    return {
      form: {
        name: '',
        nick_name: '',
        id: '',
        ha_mode: ''
      },
      dialogStatusShowVisible: false,
      timer: null,
      installStatus: false,
      info: '',
      activities: [],
      dialogStatusShowVisible: false,
      active: 0,
      // 已选步骤
      stepSuc: [0],
      // 步骤参数
      stepParams: [],
      computer: [],
      shard: [],
      computer_state: '',
      storage_state: '',
      finish_state: '',
      computer_title: '',
      computer_icon: '',
      shard_icon: '',
      shard_title: '',
      comp_active: 0,
      shard_active: 0,
      strogemachines: [],
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
      shard_description: '',
      shardInfo: '',
      dialogShardInfo: false,
      job_id: '',
      delflag: true
    }
  },

  mounted() {
    // console.log(this.listsent);
    this.form.name = this.listsent.name
    this.form.nick_name = this.listsent.nick_name
    this.form.id = this.listsent.id
    this.form.ha_mode = this.listsent.ha_mode
    this.form.fullsync_level = this.listsent.fullsync_level
    this.form.buffer_pool = this.listsent.buffer_pool
    this.form.comptotal = this.listsent.comp_count
    // this.form.shards_count=this.listsent.shards_count;
    this.form.shardtotal = this.listsent.shardtotal
  },
  methods: {
    onSubmit(row) {
      const code = createCode()
      const string = '此操作将永久删除该数据, 是否继续?code=' + code
      gotoCofirm(string).then((res) => {
        // 先执行删权限
        if (!res.value) {
          this.message_tips = 'code不能为空！'
          this.message_type = 'error'
          messageTip(this.message_tips, this.message_type)
        } else if (res.value == code) {
          const apply_all_cluster = sessionStorage.getItem('apply_all_cluster')
          if (apply_all_cluster == 2) {
            const arrs = {}
            arrs.effectCluster = sessionStorage.getItem('affected_clusters')
            arrs.cluster_name = row.name
            arrs.username = sessionStorage.getItem('login_username')
            arrs.type = 'del'
            uAssign(arrs).then((responses) => {
              const res_update = responses
              if (res_update.code == 200) {
                // this.dialogFormVisible = false;
                sessionStorage.setItem('affected_clusters', res_update.effectCluster)
              }
            })
          }
          // 调接口删集群
          const tempData = {}
          tempData.user_name = sessionStorage.getItem('login_username')
          tempData.job_id = ''
          tempData.version = version_arr[0].ver
          tempData.job_type = 'delete_cluster'
          tempData.timestamp = timestamp_arr[0].time + ''
          tempData.paras = { 'cluster_id': row.id, 'nick_name': row.nick_name }
          delCluster(tempData).then((response) => {
            const res = response
            if (res.status == 'accept') {
              // this.dialogFormVisible = false;
              this.dialogStatusShowVisible = true
              // 把之前的数据清空
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
              this.shard_show = true
              this.finish_show = false
              this.computer_show = true
              this.finish_state = ''
              const info = '删除'
              this.init_show = true
              // 调获取状态接口
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
    onDel() {
      this.$emit('clusterId', this.listsent.id)
      // this.$listeners.getId(this.listsent.id)
    },
    // 清除定时器
    beforeDestory() {
      clearInterval(this.timer)
      this.dialogStatusShowVisible = false
      // 把集群id传给父组件，更新数据
      this.delflag = false
      this.$emit('clusterId', this.listsent.id)
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
          // 新增集群、删除集群
          if (info == '新增' || info == '删除') {
            if (ress.attachment !== null) {
              const steps = (ress.attachment.job_steps).split(',')
              // 计算
              if (ress.attachment.computer_state == 'ongoing') {
                this.computer_state = 'process'
                this.computer_icon = 'el-icon-loading'
                this.computer_title = '正在' + info + steps[1]
              } else if (ress.attachment.computer_state == 'done') {
                this.computer_state = 'success'
                this.computer_icon = 'el-icon-circle-check'
                this.computer_title = info + steps[1] + '成功'
                // 遍历计算节点改状态
                if (this.computer.length > 0) {
                  for (let b = 0; b < ress.attachment.computer_step.length; b++) {
                    if (ress.attachment.computer_step[b].computer_state == 'done') {
                      for (let c = 0; c < this.computer.length; c++) {
                        const arr = ress.attachment.computer_step[b].computer_hosts.substr(0, ress.attachment.computer_step[b].computer_hosts.length - 1)
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
                }
                // 如果存储节点done，计算节点failed,ress.status='ongoing' 需要停止请求
              } else if (ress.attachment.computer_state == 'failed') {
                this.computer_state = 'error'
                this.computer_icon = 'el-icon-circle-close'
                this.computer_title = info + steps[1] + '失败'
                // 遍历计算节点改状态
                if (this.computer.length > 0) {
                  for (let b = 0; b < ress.attachment.computer_step.length; b++) {
                    if (ress.attachment.computer_step[b].computer_state == 'failed') {
                      for (let c = 0; c < this.computer.length; c++) {
                        const arr = ress.attachment.computer_step[b].computer_hosts.substr(0, ress.attachment.computer_step[b].computer_hosts.length - 1)
                        const computer_hosts = arr.split(';')
                        for (let e = 0; e < computer_hosts.length; e++) {
                          if (this.computer[c].title == computer_hosts[e]) {
                            this.computer[c].icon = 'el-icon-circle-close'
                            this.computer[c].status = 'error'
                          }
                        }
                      }
                      this.computer_description = ress.attachment.computer_step[b].error_info
                    }
                  }
                }
              } else {
                this.computer_state = 'process'
                this.computer_icon = 'el-icon-loading'
                this.computer_title = '正在' + info + steps[1]
              }
              // 存储
              if (ress.attachment.storage_state == 'ongoing') {
                this.storage_state = 'process'
                this.shard_icon = 'el-icon-loading'
                this.shard_title = '正在' + info + steps[0]
              } else if (ress.attachment.storage_state == 'done') {
                this.storage_state = 'success'
                this.shard_icon = 'el-icon-circle-check'
                this.shard_title = info + steps[0] + '成功'
                // 遍历存储节点改状态
                if (this.shard.length > 0) {
                  for (let c = 0; c < this.shard.length; c++) {
                    if (ress.attachment.hasOwnProperty('shard_step')) {
                      for (let b = 0; b < ress.attachment.shard_step.length; b++) {
                        if (info == '新增') {
                          const shard_ids = ress.attachment.shard_step[b].shard_ids
                          for (let e = 0; e < shard_ids.length; e++) {
                            for (var item in shard_ids[e]) {
                              var shard_idsValue = shard_ids[e][item]
                              if (this.shard[c].shard_id == shard_idsValue) {
                                this.shard[c].icon = 'el-icon-circle-check'
                                this.shard[c].status = 'success'
                              }
                            }
                          }
                        } else if (info == '删除') {
                          const arr = ress.attachment.shard_step[b].storage_hosts.substr(0, ress.attachment.shard_step[b].storage_hosts.length - 1)
                          const shard_ids = arr.split(',')
                          for (let e = 0; e < shard_ids.length; e++) {
                            if (this.shard[c].shard_id == shard_ids[e]) {
                              this.shard[c].icon = 'el-icon-circle-check'
                              this.shard[c].status = 'success'
                            }
                          }
                        }
                      }
                    } else {
                      this.shard[c].icon = 'el-icon-circle-check'
                      this.shard[c].status = 'success'
                    }
                  }
                }
              } else if (ress.attachment.storage_state == 'failed') {
                this.storage_state = 'error'
                this.shard_icon = 'el-icon-circle-close'
                this.shard_title = info + steps[0] + '失败'
                // 遍历存储节点改状态
                if (this.shard.length > 0) {
                  for (let c = 0; c < this.shard.length; c++) {
                    if (ress.attachment.hasOwnProperty('shard_step')) {
                      for (let b = 0; b < ress.attachment.shard_step.length; b++) {
                        if (info == '新增') {
                          const shard_ids = ress.attachment.shard_step[b].shard_ids
                          for (let e = 0; e < shard_ids.length; e++) {
                            for (var item in shard_ids[e]) {
                              var shard_idsValue = shard_ids[e][item]
                              if (this.shard[c].shard_id == shard_idsValue) {
                                this.shard[c].icon = 'el-icon-circle-close'
                                this.shard[c].status = 'error'
                              }
                            }
                          }
                        } else if (info == '删除') {
                          const arr = ress.attachment.shard_step[b].storage_hosts.substr(0, ress.attachment.shard_step[b].storage_hosts.length - 1)
                          const shard_ids = arr.split(',')
                          for (let e = 0; e < shard_ids.length; e++) {
                            if (this.shard[c].shard_id == shard_ids[e]) {
                              this.shard[c].icon = 'el-icon-circle-close'
                              this.shard[c].status = 'error'
                            }
                          }
                        }
                        this.shard_description = ress.attachment.shard_step[b].error_info
                      }
                    } else {
                      this.shard[c].icon = 'el-icon-circle-check'
                      this.shard[c].status = 'success'
                      this.shard_description = ress.error_info
                    }
                  }
                }
              } else {
                this.storage_state = 'process'
                this.shard_icon = 'el-icon-loading'
                this.shard_title = '正在' + info + steps[0]
              }
              this.init_title = '正在' + info + '集群'
              // this.finish_title=info+'集群成功'

              this.init_active = 3
              if (this.computer.length == 0 && this.shard.length == 0) {
                if (ress.attachment.hasOwnProperty('computer_step')) {
                  for (let a = 0; a < ress.attachment.computer_step.length; a++) {
                    if (ress.attachment.computer_step[a].hasOwnProperty('computer_hosts')) {
                      const arr = ress.attachment.computer_step[a].computer_hosts.substr(0, ress.attachment.computer_step[a].computer_hosts.length - 1)
                      const computer_hosts = arr.split(';')
                      // console.log(computer_hosts[e]);
                      if (ress.attachment.computer_state == 'done') {
                        for (let e = 0; e < computer_hosts.length; e++) {
                          const newArrgoing = {}
                          newArrgoing.title = computer_hosts[e]
                          newArrgoing.icon = 'el-icon-circle-check'
                          newArrgoing.status = 'success'
                          newArrgoing.description = ''
                          newArrgoing.computer_id = ress.attachment.computer_step[a].computer_id
                          this.computer.push(newArrgoing)
                        }
                        // console.log(this.computer);
                      } else if (ress.attachment.computer_state == 'failed') {
                        for (let e = 0; e < computer_hosts.length; e++) {
                          const newArrgoing = {}
                          newArrgoing.title = computer_hosts[e]
                          newArrgoing.icon = 'el-icon-circle-close'
                          newArrgoing.status = 'error'
                          newArrgoing.description = ress.attachment.comp_error_info
                          newArrgoing.computer_id = ress.attachment.computer_step[a].computer_id
                          this.computer.push(newArrgoing)
                        }
                      } else {
                        // console.log(11);
                        for (let e = 0; e < computer_hosts.length; e++) {
                          const newArrgoing = {}
                          newArrgoing.title = computer_hosts[e]
                          newArrgoing.icon = 'el-icon-loading'
                          newArrgoing.status = 'process'
                          newArrgoing.description = ''
                          newArrgoing.computer_id = ress.attachment.computer_step[a].computer_id
                          this.computer.push(newArrgoing)
                        }
                      }
                      // }
                      // console.log(this.computer);
                    }
                  }
                }
                if (ress.attachment.hasOwnProperty('shard_step')) {
                  for (let b = 0; b < ress.attachment.shard_step.length; b++) {
                    if (ress.attachment.storage_state == 'done') {
                      if (info == '删除') {
                        const arr = ress.attachment.shard_step[b].storage_hosts.substr(0, ress.attachment.shard_step[b].storage_hosts.length - 1)
                        const shard_ids = arr.split(';')
                        for (let e = 0; e < shard_ids.length; e++) {
                          const shardgoing = {}
                          shardgoing.title = shard_ids !== '' ? shard_ids[e] : '正在' + info + steps[0]
                          shardgoing.icon = 'el-icon-circle-check'
                          shardgoing.status = 'success'
                          shardgoing.description = ''
                          shardgoing.shard_id = shard_ids[e]
                          this.shard.push(shardgoing)
                        }
                      }
                      if (info == '新增') {
                        const shard_ids = ress.attachment.shard_step[b].shard_ids
                        for (let e = 0; e < shard_ids.length; e++) {
                          for (var item in shard_ids[e]) {
                            const shardgoing = {}
                            var shard_idsValue = shard_ids[e][item]
                            const shard_text = item + ':' + shard_idsValue
                            shardgoing.title = shard_idsValue !== '' ? shard_text : '正在' + info + steps[0]
                            shardgoing.icon = 'el-icon-circle-check'
                            shardgoing.status = 'success'
                            shardgoing.description = ''
                            shardgoing.shard_id = shard_idsValue
                            this.shard.push(shardgoing)
                          }
                        }
                      }
                    } else if (ress.attachment.storage_state == 'failed') {
                      if (info == '删除') {
                        const arr = ress.attachment.shard_step[b].storage_hosts.substr(0, ress.attachment.shard_step[b].storage_hosts.length - 1)
                        const shard_ids = arr.split(';')
                        for (let e = 0; e < shard_ids.length; e++) {
                          const shardgoing = {}
                          shardgoing.title = shard_ids !== '' ? shard_ids[e] : '正在' + info + steps[0]
                          shardgoing.icon = 'el-icon-circle-close'
                          shardgoing.status = 'error'
                          shardgoing.description = ress.attachment.shard_error_info
                          shardgoing.shard_id = shard_ids[e]
                          this.shard.push(shardgoing)
                        }
                      }
                      if (info == '新增') {
                        const shard_ids = ress.attachment.shard_step[b].shard_ids
                        for (let e = 0; e < shard_ids.length; e++) {
                          for (var item in shard_ids[e]) {
                            const shardgoing = {}
                            var shard_idsValue = shard_ids[e][item]
                            const shard_text = item + ':' + shard_idsValue
                            shardgoing.title = shard_idsValue !== '' ? shard_text : '正在' + info + steps[0]
                            shardgoing.icon = 'el-icon-circle-close'
                            shardgoing.status = 'error'
                            shardgoing.description = ress.attachment.shard_error_info
                            shardgoing.shard_id = shard_idsValue
                            this.shard.push(shardgoing)
                          }
                        }
                      }
                    } else {
                      if (info == '删除') {
                        if (ress.attachment.shard_step[b].hasOwnProperty('storage_hosts')) {
                          const arr = ress.attachment.shard_step[b].storage_hosts.substr(0, ress.attachment.shard_step[b].storage_hosts.length - 1)
                          const shard_ids = arr.split(';')
                          for (let e = 0; e < shard_ids.length; e++) {
                            const shardgoing = {}
                            shardgoing.title = shard_ids !== '' ? shard_ids[e] : '正在' + info + steps[0]
                            shardgoing.icon = 'el-icon-loading'
                            shardgoing.status = 'process'
                            shardgoing.description = ''
                            shardgoing.shard_id = shard_ids[e]
                            this.shard.push(shardgoing)
                          }
                        }
                      }
                      if (info == '新增') {
                        const shard_ids = ress.attachment.shard_step[b].shard_ids
                        for (let e = 0; e < shard_ids.length; e++) {
                          for (var item in shard_ids[e]) {
                            const shardgoing = {}
                            var shard_idsValue = shard_ids[e][item]
                            const shard_text = item + ':' + shard_idsValue
                            shardgoing.title = shard_idsValue !== '' ? shard_text : '正在' + info + steps[0]
                            shardgoing.icon = 'el-icon-loading'
                            shardgoing.status = 'process'
                            shardgoing.description = ''
                            shardgoing.shard_id = shard_idsValue
                            this.shard.push(shardgoing)
                          }
                        }
                      }
                    }
                  }
                }
                if (ress.status == 'failed') {
                  this.storage_state = 'error'
                  this.shard_icon = 'el-icon-circle-close'
                  this.shard_title = info + steps[0] + '失败'
                  this.init_show = false

                  this.computer_state = 'error'
                  this.computer_icon = 'el-icon-circle-close'
                  this.computer_title = info + steps[1] + '失败'

                  this.finish_title = info + '集群失败'
                  this.finish_icon = 'el-icon-circle-close'
                  this.finish_state = 'error'
                  this.finish_show = true
                  this.init_active = 4
                  this.finish_description = ress.error_info
                  // 遍历计算节点改状态
                  if (this.computer.length > 0) {
                    for (let b = 0; b < ress.attachment.computer_step.length; b++) {
                      if (ress.attachment.computer_step[b].computer_state == 'failed') {
                        for (let c = 0; c < this.computer.length; c++) {
                          const arr = ress.attachment.computer_step[b].computer_hosts.substr(0, ress.attachment.computer_step[b].computer_hosts.length - 1)
                          const computer_hosts = arr.split(';')
                          for (let e = 0; e < computer_hosts.length; e++) {
                            if (this.computer[c].title == computer_hosts[e]) {
                              this.computer[c].icon = 'el-icon-circle-close'
                              this.computer[c].status = 'error'
                            }
                          }
                        }
                        this.computer_description = ress.attachment.computer_step[b].error_info
                      }
                    }
                  }
                  // 遍历存储节点改状态
                  if (this.shard.length > 0) {
                    for (let b = 0; b < ress.attachment.shard_step.length; b++) {
                      for (let c = 0; c < this.shard.length; c++) {
                        if (info == '新增') {
                          const shard_ids = ress.attachment.shard_step[b].shard_ids
                          for (let e = 0; e < shard_ids.length; e++) {
                            for (var item in shard_ids[e]) {
                              var shard_idsValue = shard_ids[e][item]
                              if (this.shard[c].shard_id == shard_idsValue) {
                                this.shard[c].icon = 'el-icon-circle-close'
                                this.shard[c].status = 'error'
                              }
                            }
                          }
                        } else if (info == '删除') {
                          const arr = ress.attachment.shard_step[b].storage_hosts.substr(0, ress.attachment.shard_step[b].storage_hosts.length - 1)
                          const shard_ids = arr.split(';')
                          for (let e = 0; e < shard_ids.length; e++) {
                            if (this.shard[c].shard_id == shard_ids[e]) {
                              this.shard[c].icon = 'el-icon-circle-close'
                              this.shard[c].status = 'error'
                            }
                          }
                        }
                      }
                      this.shard_description = ress.attachment.shard_step[b].error_info
                    }
                  }
                  clearInterval(timer)
                } else if (ress.status == 'done') {
                  this.init_active = 4
                  this.finish_icon = 'el-icon-circle-check'
                  this.finish_state = 'success'
                  this.init_show = false
                  this.finish_show = true
                  clearInterval(timer)
                }
              } else {
                if (ress.status == 'ongoing') {
                  // 计算
                  // if(ress.attachment.computer_state=='ongoing'){
                  for (let k = 0; k < this.computer.length; k++) {
                    this.comp_active = k
                    for (let j = 0; j < ress.attachment.computer_step.length; j++) {
                      if (ress.attachment.computer_step[j].computer_state == 'ongoing') {
                        // console.log(22);
                        const arr = ress.attachment.computer_step[j].computer_hosts.substr(0, ress.attachment.computer_step[j].computer_hosts.length - 1)
                        const computer_hosts = arr.split(';')
                        for (let e = 0; e < computer_hosts.length; e++) {
                          console.log(22)
                          if (computer_hosts[e] == this.computer[k].title) {
                            this.comp_active = k - 1
                            this.computer[k].icon = 'el-icon-loading'
                            this.computer[k].status = 'process'
                            if (k > 0 && k < (this.comp_active + 1)) {
                              // 小于k的情况
                              for (let p = 0; p < k; p++) {
                                this.computer[p].icon = 'el-icon-circle-check'
                                this.computer[p].status = 'success'
                              }
                            }
                          }
                        }
                      } else if (ress.attachment.computer_step[j].computer_state == 'failed') {
                        console.log(23)
                        const arr = ress.attachment.computer_step[j].computer_hosts.substr(0, ress.attachment.computer_step[j].computer_hosts.length - 1)
                        const computer_hosts = arr.split(';')
                        for (let e = 0; e < computer_hosts.length; e++) {
                          if (computer_hosts[e] == this.computer[k].title) {
                            this.computer[k].icon = 'el-icon-circle-close'
                            this.computer[k].status = 'error'
                          }
                        }
                        this.computer_description = ress.attachment.computer_step[j].error_info
                      } else if (ress.attachment.computer_step[j].computer_state == 'done') {
                        console.log(24)
                        const arr = ress.attachment.computer_step[j].computer_hosts.substr(0, ress.attachment.computer_step[j].computer_hosts.length - 1)
                        const computer_hosts = arr.split(';')
                        for (let e = 0; e < computer_hosts.length; e++) {
                          if (computer_hosts[e] == this.computer[k].title) {
                            // this.comp_active=k;
                            this.computer[k].status = 'success'
                            this.computer[k].icon = 'el-icon-circle-check'
                          }
                        }
                      }
                    }
                  }
                  // }
                  // 存储
                  if (this.shard.length > 0) {
                    for (let k = 0; k < this.shard.length; k++) {
                      for (let j = 0; j < ress.attachment.shard_step.length; j++) {
                        if (ress.attachment.shard_step[j].storage_state == 'ongoing') {
                          if (ress.attachment.shard_step[j].shard_hosts == this.shard[k].title) {
                            this.shard_active = k - 1
                            this.shard[k].icon = 'el-icon-loading'
                            this.shard[k].status = 'process'
                            if (k > 0 && k < (this.shard_active + 1)) {
                              // 小于k的情况
                              for (let p = 0; p < k; p++) {
                                this.shard[p].icon = 'el-icon-circle-check'
                                this.shard[p].status = 'success'
                              }
                            }
                          }
                        } else if (ress.attachment.shard_step[j].storage_state == 'failed') {
                          this.shard_active = k
                          this.shard[k].icon = 'el-icon-circle-close'
                          this.shard[k].status = 'error'
                          this.shard_description = ress.attachment.shard_step[j].error_info
                        } else if (ress.attachment.shard_step[j].storage_state == 'done') {
                          this.shard_active = k
                          this.shard[k].status = 'success'
                          this.shard[k].icon = 'el-icon-circle-check'
                        }
                      }
                    }
                  } else {
                    if (ress.attachment.shard_step[0].hasOwnProperty('storage_hosts')) {
                      if (info == '删除') {
                        if (ress.attachment.storage_state == 'ongoing') {
                          const arr = ress.attachment.shard_step[0].storage_hosts.substr(0, ress.attachment.shard_step[0].storage_hosts.length - 1)
                          const shard_ids = arr.split(';')
                          for (let e = 0; e < shard_ids.length; e++) {
                            const shardgoing = {}
                            shardgoing.title = shard_ids !== '' ? shard_ids[e] : '正在' + info + steps[0]
                            shardgoing.icon = 'el-icon-loading'
                            shardgoing.status = 'process'
                            shardgoing.description = ''
                            shardgoing.shard_id = shard_ids[e]
                            this.shard.push(shardgoing)
                          }
                        } else if (ress.attachment.storage_state == 'failed') {
                          const arr = ress.attachment.shard_step[0].storage_hosts.substr(0, ress.attachment.shard_step[0].storage_hosts.length - 1)
                          const shard_ids = arr.split(';')
                          for (let e = 0; e < shard_ids.length; e++) {
                            const shardgoing = {}
                            shardgoing.title = shard_ids !== '' ? shard_ids[e] : '正在' + info + steps[0]
                            shardgoing.icon = 'el-icon-circle-close'
                            shardgoing.status = 'error'
                            shardgoing.description = ''
                            shardgoing.shard_id = shard_ids[e]
                            this.shard.push(shardgoing)
                          }
                        } else if (ress.attachment.storage_state == 'done') {
                          const arr = ress.attachment.shard_step[0].storage_hosts.substr(0, ress.attachment.shard_step[0].storage_hosts.length - 1)
                          const shard_ids = arr.split(';')
                          for (let e = 0; e < shard_ids.length; e++) {
                            const shardgoing = {}
                            shardgoing.title = shard_ids !== '' ? shard_ids[e] : '正在' + info + steps[0]
                            shardgoing.icon = 'el-icon-circle-check'
                            shardgoing.status = 'success'
                            shardgoing.description = ''
                            shardgoing.shard_id = shard_ids[e]
                            this.shard.push(shardgoing)
                          }
                        }
                      }
                    }
                  }
                } else if (ress.status == 'done') {
                  // console.log(3);
                  this.init_show = false
                  if (ress.attachment.computer_state == 'done') {
                    console.log(51)
                    this.computer_title = info + steps[1] + '成功'
                    this.computer_state = 'success'
                    this.computer_icon = 'el-icon-circle-check'
                    this.comp_active = this.computer.length - 1
                    for (let d = 0; d < this.computer.length; d++) {
                      // console.log(this.computer)
                      this.computer[d].status = 'success'
                      this.computer[d].icon = 'el-icon-circle-check'
                    }
                  }
                  if (ress.attachment.storage_state == 'done') {
                    console.log(52)
                    this.shard_title = info + steps[0] + '成功'
                    this.storage_state = 'success'
                    this.shard_icon = 'el-icon-circle-check'
                    this.shard_active = this.shard.length - 1
                    if (this.shard.length > 0) {
                      for (let d = 0; d < this.shard.length; d++) {
                        console.log(53)
                        this.shard[d].status = 'success'
                        this.shard[d].icon = 'el-icon-circle-check'
                      }
                    } else {
                      if (ress.attachment.shard_step[0].hasOwnProperty('storage_hosts')) {
                        if (info == '删除') {
                          const arr = ress.attachment.shard_step[0].storage_hosts.substr(0, ress.attachment.shard_step[0].storage_hosts.length - 1)
                          const shard_ids = arr.split(';')
                          for (let e = 0; e < shard_ids.length; e++) {
                            const shardgoing = {}
                            shardgoing.title = shard_ids !== '' ? shard_ids[e] : '正在' + info + steps[0]
                            shardgoing.icon = 'el-icon-circle-check'
                            shardgoing.status = 'success'
                            shardgoing.description = ''
                            shardgoing.shard_id = shard_ids[e]
                            this.shard.push(shardgoing)
                          }
                        }
                      }
                    }
                  }

                  this.init_active = 4
                  this.finish_title = info + '集群成功'
                  this.finish_icon = 'el-icon-circle-check'
                  this.finish_state = 'success'
                  this.finish_show = true
                  clearInterval(timer)
                  if (info == '新增') {
                    const apply_all_cluster = sessionStorage.getItem('apply_all_cluster')
                    if (apply_all_cluster == 2) {
                      const arrs = {}
                      arrs.effectCluster = sessionStorage.getItem('affected_clusters')
                      arrs.cluster_name = ress.attachment.cluster_name
                      arrs.username = sessionStorage.getItem('login_username')
                      arrs.type = 'add'
                      uAssign(arrs).then((responses) => {
                        const res_update = responses
                        if (res_update.code == 200) {
                          // this.dialogFormVisible = false;
                          sessionStorage.setItem('affected_clusters', res_update.effectCluster)
                        }
                      })
                    }
                    setTimeout(() => {
                      // this.getList();
                      // this.dialogStatusShowVisible=false;
                    }, 3000)
                  } else {
                    // this.getList();
                  }
                } else if (ress.status == 'failed') {
                  this.finish_title = info + '集群失败'
                  this.finish_icon = 'el-icon-circle-close'
                  this.finish_state = 'error'
                  this.init_active = 4
                  this.finish_description = ress.error_info
                  this.finish_show = true
                  this.init_show = false
                  clearInterval(timer)
                  if (ress.attachment.hasOwnProperty('computer_state')) {
                    if (ress.attachment.computer_state == 'failed') {
                      this.computer_state = 'error'
                      this.computer_icon = 'el-icon-circle-close'
                      for (let b = 0; b < ress.attachment.computer_step.length; b++) {
                        const arr = ress.attachment.computer_step[b].computer_hosts.substr(0, ress.attachment.computer_step[b].computer_hosts.length - 1)
                        const computer_hosts = arr.split(';')
                        if (ress.attachment.computer_step[b].computer_state == 'done') {
                          this.comp_active = b
                          for (let c = 0; c < this.computer.length; c++) {
                            for (let e = 0; e < computer_hosts.length; e++) {
                              if (this.computer[c].title == computer_hosts[e]) {
                                this.computer[c].icon = 'el-icon-circle-check'
                                this.computer[c].status = 'success'
                              }
                            }
                          }
                        } else if (ress.attachment.computer_step[b].computer_state == 'failed') {
                          for (let c = 0; c < this.computer.length; c++) {
                            for (let e = 0; e < computer_hosts.length; e++) {
                              if (this.computer[c].title == computer_hosts[e]) {
                                this.computer[c].icon = 'el-icon-circle-close'
                                this.computer[c].status = 'error'
                              }
                            }
                          }
                        }
                        this.computer_description = ress.attachment.computer_step[b].error_info
                      }
                    } else if (ress.attachment.computer_state == 'done') {
                      // let current_id=0;
                      for (let b = 0; b < ress.attachment.computer_step.length; b++) {
                        const arr = ress.attachment.computer_step[b].computer_hosts.substr(0, ress.attachment.computer_step[b].computer_hosts.length - 1)
                        const computer_hosts = arr.split(';')
                        if (ress.attachment.computer_step[b].computer_state == 'done') {
                          this.comp_active = b
                          for (let c = 0; c < this.computer.length; c++) {
                            for (let e = 0; e < computer_hosts.length; e++) {
                              if (this.computer[c].title = computer_hosts[e]) {
                                this.computer[c].icon = 'el-icon-circle-check'
                                this.computer[c].status = 'success'
                                // current_id=c;
                              }
                            }
                          }
                        } else if (ress.attachment.computer_step[b].computer_state == 'failed') {
                          for (let c = 0; c < this.computer.length; c++) {
                            for (let e = 0; e < computer_hosts.length; e++) {
                              if (this.computer[c].title == computer_hosts[e]) {
                                this.computer[c].icon = 'el-icon-circle-close'
                                this.computer[c].status = 'error'
                                // current_id=c;
                              }
                            }
                          }
                          this.computer_description = ress.attachment.computer_step[b].error_info
                        }
                      }
                      // 小于b的status为success
                      // if(this.computer.length>1&&this.comp_active>0){
                      //   if(current_id){

                      //   }
                      // }
                    }
                  } else {
                    this.computer_state = 'error'
                    this.computer_icon = 'el-icon-circle-close'
                    this.computer_title = info + steps[0] + '失败'
                  }
                  if (ress.attachment.hasOwnProperty('storage_state')) {
                    if (ress.attachment.storage_state == 'failed') {
                      this.storage_state = 'error'
                      this.shard_icon = 'el-icon-circle-close'
                      for (let b = 0; b < ress.attachment.shard_step.length; b++) {
                        if (ress.attachment.shard_step[b].storage_state == 'done') {
                          this.shard_active = b
                          for (let c = 0; c < this.shard.length; c++) {
                            if (info == '新增') {
                              const shard_ids = ress.attachment.shard_step[b].shard_ids
                              for (let e = 0; e < shard_ids.length; e++) {
                                for (var item in shard_ids[e]) {
                                  var shard_idsValue = shard_ids[e][item]
                                  if (this.shard[c].shard_id == shard_idsValue) {
                                    this.shard[c].icon = 'el-icon-circle-check'
                                    this.shard[c].status = 'success'
                                  }
                                }
                              }
                            } else if (info == '删除') {
                              const arr = ress.attachment.shard_step[b].storage_hosts.substr(0, ress.attachment.shard_step[b].storage_hosts.length - 1)
                              const shard_ids = arr.split(',')
                              for (let e = 0; e < shard_ids.length; e++) {
                                if (this.shard[c].shard_id == shard_ids[e]) {
                                  this.shard[c].icon = 'el-icon-circle-check'
                                  this.shard[c].status = 'success'
                                }
                              }
                            }
                          }
                        } else if (ress.attachment.shard_step[b].storage_state == 'failed') {
                          for (let c = 0; c < this.shard.length; c++) {
                            if (info == '新增') {
                              const shard_ids = ress.attachment.shard_step[b].shard_ids
                              for (let e = 0; e < shard_ids.length; e++) {
                                for (var item in shard_ids[e]) {
                                  var shard_idsValue = shard_ids[e][item]
                                  if (this.shard[c].shard_id == shard_idsValue) {
                                    this.shard[c].icon = 'el-icon-circle-close'
                                    this.shard[c].status = 'error'
                                  }
                                }
                              }
                            } else if (info == '删除') {
                              const arr = ress.attachment.shard_step[b].storage_hosts.substr(0, ress.attachment.shard_step[b].storage_hosts.length - 1)
                              const shard_ids = arr.split(',')
                              for (let e = 0; e < shard_ids.length; e++) {
                                if (this.shard[c].shard_id == shard_ids[e]) {
                                  this.shard[c].icon = 'el-icon-circle-close'
                                  this.shard[c].status = 'error'
                                }
                              }
                            }
                          }
                          this.shard_description = ress.attachment.shard_step[b].error_info
                        }
                      }
                    } else if (ress.attachment.storage_state == 'done') {
                      // let current_id=0;
                      for (let b = 0; b < ress.attachment.shard_step.length; b++) {
                        if (ress.attachment.shard_step[b].storage_state == 'done') {
                          this.shard_active = b
                          for (let c = 0; c < this.shard.length; c++) {
                            if (this.shard[c].title == ress.attachment.shard_step[b].shard_hosts) {
                              this.shard[c].icon = 'el-icon-circle-check'
                              this.shard[c].status = 'success'
                              // current_id=c;
                            }
                          }
                        } else if (ress.attachment.shard_step[b].storage_state == 'failed') {
                          for (let c = 0; c < this.shard.length; c++) {
                            if (this.shard[c].title == ress.attachment.shard_step[b].shard_hosts) {
                              this.shard[c].icon = 'el-icon-circle-close'
                              this.shard[c].status = 'error'
                              // current_id=c;
                            }
                          }
                        }
                      }
                    } else {
                      for (let c = 0; c < this.shard.length; c++) {
                        if (info == '新增') {
                          const shard_ids = ress.attachment.shard_step[b].shard_ids
                          for (let e = 0; e < shard_ids.length; e++) {
                            for (var item in shard_ids[e]) {
                              var shard_idsValue = shard_ids[e][item]
                              if (this.shard[c].shard_id == shard_idsValue) {
                                this.shard[c].icon = 'el-icon-circle-close'
                                this.shard[c].status = 'error'
                              }
                            }
                          }
                        } else if (info == '删除') {
                          const arr = ress.attachment.shard_step[b].storage_hosts.substr(0, ress.attachment.shard_step[b].storage_hosts.length - 1)
                          const shard_ids = arr.split(',')
                          for (let e = 0; e < shard_ids.length; e++) {
                            if (this.shard[c].shard_id == shard_ids[e]) {
                              this.shard[c].icon = 'el-icon-circle-close'
                              this.shard[c].status = 'error'
                            }
                          }
                        }
                      }
                      this.shard_description = ress.error_info
                    }
                  } else {
                    this.storage_state = 'error'
                    this.shard_icon = 'el-icon-circle-close'
                    this.shard_title = info + steps[0] + '失败'
                  }
                }
              }
            } else if (ress.attachment == null && ress.error_code == '70001' && ress.status == 'failed') {
              this.init_title = '正在' + info + '集群'
              // 计算节点
              this.computer_state = 'process'
              this.computer_icon = 'el-icon-loading'
              this.computer_title = '正在' + info + 'computer'
              // 储存节点
              this.storage_state = 'process'
              this.shard_icon = 'el-icon-loading'
              this.shard_title = '正在' + info + 'shard'
              if (i > 5) {
                this.init_show = false
                // 计算节点
                this.computer_state = 'error'
                this.computer_icon = 'el-icon-circle-close'
                this.computer_title = info + 'computer失败'
                // 储存节点
                this.storage_state = 'error'
                this.shard_icon = 'el-icon-circle-close'
                this.shard_title = info + 'shard失败'
                clearInterval(timer)
              }
            } else if (ress.attachment == null && ress.status == 'ongoing') {
              this.init_title = '正在' + info + '集群'
              // 计算节点
              this.computer_state = 'process'
              this.computer_icon = 'el-icon-loading'
              this.computer_title = '正在' + info + 'computer'
              // 储存节点
              this.storage_state = 'process'
              this.shard_icon = 'el-icon-loading'
              this.shard_title = '正在' + info + 'shard'
            } else if (ress.attachment == null && ress.status == 'done') {
              this.init_show = false
              // 计算节点
              this.computer_state = 'success'
              this.computer_icon = 'el-icon-circle-check'
              this.computer_title = info + 'computer成功'
              // 储存节点
              this.storage_state = 'success'
              this.shard_icon = 'el-icon-circle-check'
              this.shard_title = info + 'shard成功'
              clearInterval(timer)
            } else {
              this.init_show = false
              // 计算节点
              this.computer_state = 'error'
              this.computer_icon = 'el-icon-circle-close'
              this.computer_title = info + 'computer失败'
              // 储存节点
              this.storage_state = 'error'
              this.shard_icon = 'el-icon-circle-close'
              this.shard_title = info + 'shard失败'
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
</style>
