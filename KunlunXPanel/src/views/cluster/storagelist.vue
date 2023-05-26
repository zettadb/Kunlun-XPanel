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
          v-if="user_name=='super_dba'"
          class="filter-item"
          type="primary"
          icon="el-icon-plus"
          @click="handleCreate"
        >新增
        </el-button>
        <el-button
          v-if="user_name=='super_dba'"
          class="filter-item"
          type="primary"
          icon="el-icon-setting"
          @click="handleES"
        >es配置
        </el-button>
        <div v-show="installStatus===true" class="info" v-text="info" />
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
      <el-table-column
        type="index"
        align="center"
        label="序号"
        width="50"
      />

      <el-table-column label="目标名称" align="center">
        <template slot-scope="{row}">
          <span class="link-type" @click="handleDetail(row)">{{ row.name }}</span>
        </template>
      </el-table-column>

      <el-table-column
        prop="stype"
        align="center"
        label="类型"
      />
      <el-table-column
        prop="hostaddr"
        align="center"
        label="IP地址"
      />
      <el-table-column
        prop="port"
        align="center"
        label="端口号"
      />

      <el-table-column
        v-if="user_name=='super_dba'"
        label="操作"
        align="center"
        width="300"
        class-name="small-padding fixed-width"
      >
        <template slot-scope="{row,$index}">
          <el-button
            v-if="user_name=='super_dba'"
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

    <el-dialog :title="textMap[dialogStatus]" :visible.sync="dialogFormVisible" custom-class="single_dal_view">
      <el-form
        ref="dataForm"
        :rules="rules"
        :model="temp"
        label-position="left"
        label-width="140px"
      >

        <el-form-item label="目标名称:" prop="name">
          <el-input
            v-model="temp.name"
            placeholder="请输入目标名称"
            :disabled="dialogStatus==='detail'||dialogStatus==='update'"
          />
        </el-form-item>

        <el-form-item label="目标类型:" prop="stype">
          <el-select v-model="temp.stype" placeholder="请选择目标类型" @change="changeValue($event)">
            <el-option
              v-for="stype in stypelist"
              :key="stype.id"
              :label="stype.name"
              :value="stype.name"
            />
          </el-select>
        </el-form-item>

        <el-form-item label="IP地址:" prop="hostaddr">
          <el-input v-model="temp.hostaddr" placeholder="请输入IP地址" :disabled="dialogStatus==='detail'" />
        </el-form-item>
        <el-form-item v-if="temp.stype=='SSH'" label="SSH用户名:">
          <el-input v-model="temp.user_name" placeholder="请输入SSH 用户名" :disabled="dialogStatus==='detail'" />
        </el-form-item>
        <el-form-item label="端口号:" prop="port">
          <el-input v-model="temp.port" placeholder="请输入端口号" :disabled="dialogStatus==='detail'" />
        </el-form-item>

      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button v-show="!dialogDetail" @click="dialogFormVisible = false">关闭</el-button>
        <el-button v-show="!dialogDetail" type="primary" @click="dialogStatus==='create'?createData():updateData(row)">
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
    <el-dialog :title="textMap[dialogStatus]" :visible.sync="dialogESVisible" custom-class="single_dal_view">
      <el-form
            ref="esForm"
            :rules="rules"
            :model="estemp"
            label-position="left"
            label-width="140px"
        >
            <el-form-item label="IP地址:" prop="hostaddr">
            <el-input v-model="estemp.hostaddr" placeholder="请输入IP地址"/>
            </el-form-item>
            <el-form-item label="端口号:" prop="port">
                <el-input v-model="estemp.port"  placeholder="请输入端口号" />
            </el-form-item>
            <el-form-item label="是否安装:" prop="is_install">
            <el-radio v-model="estemp.is_install" label="yes">是</el-radio>
            <el-radio v-model="estemp.is_install" label="no">否</el-radio>
            </el-form-item>
        </el-form>
        <div slot="footer" class="dialog-footer" style="text-align:right">
            <el-button @click="dialogESVisible = false" >关闭</el-button>
            <el-button type="primary" @click="createESData(row)">确认</el-button>
        </div>
        <el-table
          :key="tableKey"
          v-loading="listESLoading"
          :data="eslist"
          border
          highlight-current-row
          style="width: 100%;margin-bottom: 20px;"
        >
          >
          <el-table-column
            type="index"
            align="center"
            label="序号"
            width="50"
          />
          <el-table-column
            prop="es_hostaddr"
            align="center"
            label="IP地址"
          />
          <el-table-column
            prop="es_port"
            align="center"
            label="端口号"
          />
          <el-table-column
            prop="is_install"
            align="center"
            label="是否安装"
          >
          <template slot-scope="scope">
            <span v-if="scope.row.is_install==='yes'">是</span>
            <span v-else-if="scope.row.is_install==='no'">否</span>
            <span v-else></span>
          </template>
          </el-table-column>

          <el-table-column
            label="操作"
            align="center"
            class-name="small-padding fixed-width"
          >
            <template slot-scope="{row,$index}">
              <el-button
                size="mini"
                type="danger"
                @click="handleESDelete(row,$index)"
              >删除
              </el-button>
            </template>
          </el-table-column>
        </el-table>

        <pagination
          v-show="total>0"
          :total="total"
          :page.sync="listESQuery.pageNo"
          :limit.sync="listESQuery.pageSize"
          @pagination="getESList"
        />
    </el-dialog>
  </div>
</template>

<script>
import { messageTip, handleCofirm, getNowDate } from '@/utils'
import {
  // eslint-disable-next-line no-unused-vars
  getStorageList,
  addStorage,
  updateStorage,
  delStorage,
  getEvStatus,
  getBackStorageList,
  addESList,
  getESList,
  delESList
} from '@/api/cluster/list'
import { version_arr, storage_type_arr, timestamp_arr } from '@/utils/global_variable'
import Pagination from '@/components/Pagination'

export default {
  name: 'Account',
  components: { Pagination },
  data() {
    const validateIPAddress = (rule, value, callback) => {
      const regexp = /^((2(5[0-5]|[0-4]\d))|[0-1]?\d{1,2})(\.((2(5[0-5]|[0-4]\d))|[0-1]?\d{1,2})){3}$/
      const valdata = value.split(',')
      let isCorrect = true
      if (valdata.length) {
        for (let i = 0; i < valdata.length; i++) {
          if (regexp.test(valdata[i]) == false) {
            isCorrect = false
          }
        }
      }
      if (value == '') {
        return callback(new Error('请输入IP地址'))
      } else if (!isCorrect) {
        callback(new Error('请输入正确对IP地址'))
      } else {
        callback()
      }
    }
    const validateName = (rule, value, callback) => {
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
      listESLoading:true,
      eslist:null,
      estotal: 0,
      listESQuery: {
        pageNo: 1,
        pageSize: 10,
      },
      temp: {
        hostaddr: '',
        name: '',
        stype: '',
        port: '',
        user_name: ''
      },
      estemp:{
        port:'',
        hostaddr:'',
        is_install:'no'
      },
      dialogFormVisible: false,
      dialogEditVisible: false,
      dialogESVisible:false,
      dialogStatus: '',
      textMap: {
        update: '编辑备份存储目标',
        create: '新增备份存储目标',
        detail: '详情',
        setES:'es配置'
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
        hostaddr: [
          { required: true, trigger: 'blur', validator: validateIPAddress }
        ],
        name: [
          { required: true, trigger: 'blur', validator: validateName }
        ],
        stype: [
          { required: true, trigger: 'blur', validator: validateStype }
        ],
        port: [
          { required: true, trigger: 'blur', validator: validatePort }
        ]
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
    handleESDelete(row) {
      handleCofirm('此操作将永久删除该数据, 是否继续?').then(() => {
        let tempdata={id:row.id}
        delESList(tempdata).then((response) => {
          const res = response
          if (res.code == 200) {
            this.message_tips = '删除成功'
            this.message_type = 'success'
            // 成功后重新设置数据
            this.getESList()
          } else {
            this.message_tips = res.message
            this.message_type = 'error'
          }
          messageTip(this.message_tips, this.message_type)
        })
      }).catch(() => {
        console.log('quxiao')
        messageTip('已取消删除', 'info')
      })
    },
    getESList(){
      this.listESLoading = true
      let queryParam = Object.assign({}, this.listESQuery)
      getESList(queryParam).then(response => {
        this.eslist = response.list;
        this.estotal = response.total;
        setTimeout(() => {
          this.listESLoading = false
        }, 0.5 * 1000)
      });
    },
    createESData(row) {
      this.$refs['esForm'].validate((valid) => {
        if (valid) {
          const tempData = Object.assign({}, this.estemp)
          addESList(tempData).then(response => {
            const res = response
            if (res.code === 200) {
              this.getESList()
              this.dialogESVisible = false
              this.message_tips = '新增成功'
              this.message_type = 'success'
            } else {
              this.message_tips = res.message
              this.message_type = 'error'
            }
            messageTip(this.message_tips, this.message_type)
          })
        } else {
          console.log('error submit!!');
          return false;
        }
      });
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
      // const temp={};
      // temp.version=version_arr[0].ver;
      // temp.job_id='';
      // temp.job_type='get_backup_storage';
      // temp.timestamp=timestamp_arr[0].time+'';
      // temp.paras={};
      // //模糊搜索
      // getStorageList(temp).then(response => {
      //   if(response.attachment.list_backup_storage!==null){
      //     this.list = response.attachment.list_backup_storage;
      //     this.total = response.attachment.list_backup_storage.length;
      //   }else{
      //     this.list =[];
      //     this.total=[];
      //   }
      //   setTimeout(() => {
      //     this.listLoading = false
      //   }, 0.5 * 1000)
      // });
      getBackStorageList(queryParam).then(response => {
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
        name: '',
        stype: 'HDFS',
        port: '',
        user_name: ''
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
    handleES() {
      this.dialogStatus = 'setES'
      this.dialogESVisible = true
      this.dialogDetail = false
      this.getESList();
      this.$nextTick(() => {
        this.$refs.esForm.clearValidate()
      })
    },
    createData() {
      this.$refs['dataForm'].validate((valid) => {
        if (valid) {
          const tempData = {}
          tempData.job_id = ''
          tempData.job_type = 'create_backup_storage'
          tempData.version = version_arr[0].ver
          tempData.timestamp = timestamp_arr[0].time + ''
          tempData.user_name = sessionStorage.getItem('login_username')
          tempData.paras = Object.assign({}, this.temp)
          // 发送接口
          addStorage(tempData).then(response => {
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
        tempData.job_id = ''
        tempData.job_type = 'delete_backup_storage'
        tempData.version = version_arr[0].ver
        tempData.timestamp = timestamp_arr[0].time + ''
        tempData.user_name = sessionStorage.getItem('login_username')
        tempData.paras = { 'name': row.name }
        delStorage(tempData).then((response) => {
          const res = response
          if (res.status == 'accept') {
            this.dialogFormVisible = false
            this.dialogStatusVisible = true
            this.activities = []
            const newArr = {
              content: '正在删除备份存储目标',
              timestamp: getNowDate(),
              size: 'large',
              type: 'primary',
              icon: 'el-icon-more'
            }
            this.activities.push(newArr)
            // 调获取状态接口
            let i = 0
            const action_name = '删除备份存储目标'
            this.timer = setInterval(() => {
              this.getStatus(this.timer, res.job_id, i++, action_name)
            }, 1000)
          } else {
            this.message_tips = res.error_info
            this.message_type = 'error'
            messageTip(this.message_tips, this.message_type)
          }
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
          if (res.status == 'done' || res.status == 'failed') {
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
