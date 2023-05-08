<template>
  <div class="app-container">
    <div class="filter-container">
      <div v-show="role_add_priv === 'Y'" class="table-list-wrap">
        <el-button class="filter-item" type="primary" icon="el-icon-plus" @click="handleCreate">新增
        </el-button>
      </div>
    </div>

    <el-table :key="tableKey" v-loading="listLoading" :data="list" border highlight-current-row
      style="width: 100%;margin-bottom: 20px;">
      >
      <el-table-column type="index" align="center" label="序号" width="50" />

      <el-table-column label="角色名称" align="center">
        <template slot-scope="{row}">
          <span class="link-type" @click="handleDetail(row)">{{ row.role_name }}</span>
        </template>
      </el-table-column>
      <el-table-column prop="priv" align="center" :show-overflow-tooltip="true" label="拥有权限" />
      <el-table-column prop="update_time" align="center" label="更新时间" />

      <el-table-column v-if="role_edit_priv === 'Y' && role_drop_priv === 'Y'" label="操作" align="center" width="230"
        class-name="small-padding fixed-width">
        <template slot-scope="{row,$index}">
          <el-button v-show="role_edit_priv === 'Y' && row.role_name !== 'super_dba'" type="primary" size="mini"
            @click="handleUpdate(row)">编辑
          </el-button>
          <el-button v-show="role_drop_priv === 'Y' && row.role_name !== 'super_dba'" size="mini" type="danger"
            @click="handleDelete(row, $index)">删除
          </el-button>
        </template>
      </el-table-column>
    </el-table>

    <pagination v-show="total > 0" :total="total" :page.sync="listQuery.pageNo" :limit.sync="listQuery.pageSize"
      @pagination="getList" />

    <el-dialog :title="textMap[dialogStatus]" :visible.sync="dialogFormVisible" custom-class="single_dal_view">
      <el-form ref="dataForm" :rules="rules" :model="temp" label-position="left" label-width="110px">
        <el-form-item label="角色名称:" prop="role_name">
          <el-input v-model="temp.role_name" placeholder="请输入角色名称"
            :disabled="dialogStatus === 'detail' || temp.role_name == 'super_dba'" />
        </el-form-item>
        <!-- 角色权限 -->
        <el-form-item label="拥有权限:" prop="checkkeys" :disabled="dialogStatus === 'detail'">
          <el-tree ref="tree" :data="datatree" show-checkbox node-key="id" :default-expanded-keys="[3]"
            :default-checked-keys="checkkeys" :props="defaultProps1" />
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button v-show="!dialogDetail" @click="dialogFormVisible = false">关闭</el-button>
        <el-button v-show="!dialogDetail" type="primary" @click="dialogStatus === 'create' ? createData() : updateData(row)">
          确认
        </el-button>
      </div>
    </el-dialog>
  </div>
</template>
<script>

import { messageTip, handleCofirm, formatDataTimeTotime } from '@/utils'
// eslint-disable-next-line no-unused-vars
import { rolelist, addRole, update, delRole, getAllRole, rolePermission } from '@/api/system/role'
import Pagination from '@/components/Pagination'
import { role_type_arr, valid_period, priv_type_arr, datatree_arr } from '@/utils/global_variable'

export default {
  name: 'Role',
  components: { Pagination },
  data() {
    return {
      tableKey: 0,
      list: null,
      listLoading: true,
      total: 0,
      listQuery: {
        pageNo: 1,
        pageSize: 10
      },
      temp: {
        role_name: '',
        priv: {}
      },
      dialogFormVisible: false,
      dialogEditVisible: false,
      dialogStatus: '',
      textMap: {
        update: '编辑角色',
        create: '新增角色',
        detail: '详情',
        grant: '授权'
      },
      dialogDetail: false,
      message_tips: '',
      message_type: '',
      role_type_data: role_type_arr,
      valid_period_data: valid_period,
      priv_type_arr: priv_type_arr,
      checkedGrant: [],
      rolesData: [],
      role_add_priv: JSON.parse(sessionStorage.getItem('priv')).role_add_priv,
      role_drop_priv: JSON.parse(sessionStorage.getItem('priv')).role_drop_priv,
      role_edit_priv: JSON.parse(sessionStorage.getItem('priv')).role_edit_priv,
      row: {},
      datatree: datatree_arr,
      defaultProps1: {
        children: 'children',
        label: 'label'
      },
      rules: {
        role_name: [
          { required: true, message: '请输入角色名称!', trigger: 'blur' }
        ]
      },
      rolId: '',
      routesData: [],
      checkedNodes: [],
      defaultProps: {
        children: 'children',
        label: 'slotTitle'
      },
      routes: [],
      checkkeys: []
    }
  },
  created() {
    this.getList()
  },
  methods: {
    getList() {
      this.listLoading = true
      const queryParam = Object.assign({}, this.listQuery)
      queryParam.user_name = sessionStorage.getItem('login_username')
      // 模糊搜索
      queryParam.roleName = `*${this.listQuery.roleName}*`
      rolelist(queryParam).then(response => {
        // this.list = response.list;
        const arr = response.list
        for (let i = 0; i < arr.length; i++) {
          let priv = ''
          if (arr[i].backup_priv == 'Y') {
            priv += '备份集群,'
          }
          if (arr[i].cluster_creata_priv == 'Y') {
            priv += '新增集群,'
          }
          if (arr[i].cluster_drop_priv == 'Y') {
            priv += '删除集群,'
          }
          if (arr[i].compute_disable_priv == 'Y' && arr[i].compute_enable_priv == 'Y') {
            priv += '启/禁用计算节点,'
          }
          if (arr[i].compute_node_create_priv == 'Y') {
            priv += '新增计算节点,'
          }
          if (arr[i].compute_node_drop_priv == 'Y') {
            priv += '删除计算节点,'
          }
          if (arr[i].machine_add_priv == 'Y') {
            priv += '新增计算机,'
          }
          if (arr[i].machine_drop_priv == 'Y') {
            priv += '删除计算机,'
          }
          if (arr[i].machine_priv == 'Y') {
            priv += '编辑计算机,'
          }
          if (arr[i].restore_priv == 'Y') {
            priv += '恢复集群,'
          }
          if (arr[i].role_add_priv == 'Y') {
            priv += '新增角色,'
          }
          if (arr[i].role_drop_priv == 'Y') {
            priv += '删除角色,'
          }
          if (arr[i].role_edit_priv == 'Y') {
            priv += '编辑角色,'
          }
          if (arr[i].shard_create_priv == 'Y') {
            priv += '新增存储shard,'
          }
          if (arr[i].shard_drop_priv == 'Y') {
            priv += '删除存储shard,'
          }
          if (arr[i].shrink_cluster_priv == 'Y') {
            priv += '集群缩容,'
          }
          if (arr[i].expand_cluster_priv == 'Y') {
            priv += '集群扩容,'
          }
          if (arr[i].backup_service_disable_priv == 'Y' && arr[i].backup_service_enable_priv == 'Y') {
            priv += '启/禁用备份服务,'
          }
          if (arr[i].storage_disable_priv == 'Y' && arr[i].storage_enable_priv == 'Y') {
            priv += '启/禁用存储节点,'
          }
          if (arr[i].storage_node_create_priv == 'Y') {
            priv += '新增存储节点,'
          }
          if (arr[i].storage_node_drop_priv == 'Y') {
            priv += '删除存储节点,'
          }
          if (arr[i].user_add_priv == 'Y') {
            priv += '新增用户,'
          }
          if (arr[i].user_drop_priv == 'Y') {
            priv += '删除用户,'
          }
          if (arr[i].user_edit_priv == 'Y') {
            priv += '编辑用户,'
          }
          if (arr[i].user_grant_priv == 'Y') {
            priv += '用户授权,'
          }
          priv = priv.substring(0, priv.length - 1)
          this.$set(arr[i], 'priv', priv)
        }
        this.list = arr
        this.total = response.total
        setTimeout(() => {
          this.listLoading = false
        }, 0.5 * 1000)
      })
    },
    resetTemp() {
      this.temp = {
        role_name: '',
        priv: {}
      }
    },
    handleCreate() {
      this.resetTemp()
      this.dialogStatus = 'create'
      this.dialogFormVisible = true
      this.dialogDetail = false
      this.$nextTick(() => {
        this.$refs.dataForm.clearValidate()
        this.$refs.tree.setCheckedKeys([])
      })
    },
    createData() {
      const checkedNodes = this.$refs.tree.getCheckedKeys()
      const priv = { permissionIds: checkedNodes.join(',') }
      this.temp.priv = priv
      this.$refs['dataForm'].validate((valid) => {
        if (valid) {
          // 发送接口
          addRole(this.temp).then(response => {
            const res = response
            if (res.code == 200) {
              this.getList()
              this.dialogFormVisible = false
              this.message_tips = '新增成功'
              this.message_type = 'success'
            } else {
              this.message_tips = res.message
              this.message_type = 'error'
            }
            messageTip(this.message_tips, this.message_type)
          })
        }
      })
    },
    handleDetail(row) {
      this.dialogStatus = 'detail'
      this.dialogFormVisible = true
      this.temp = Object.assign({}, row)
      this.dialogDetail = true
      rolePermission(row.id).then(res => {
        this.checkkeys = res.list[0].checkkeys
        this.$nextTick(() => {
          this.$refs.tree.setCheckedKeys(this.checkkeys)
        })
      })
    },
    handleUpdate(row) {
      // 设置默认选中节点(即当前角色所拥有的菜单权限)
      rolePermission(row.id).then(res => {
        this.checkkeys = res.list[0].checkkeys
        this.dialogEditVisible = true
        this.$nextTick(() => {
          this.$refs.tree.setCheckedKeys(this.checkkeys)
        })
      })
      this.temp = Object.assign({}, row) // copy obj,***很重要,不需要一步步赋值了***
      this.temp.id = row.id
      this.dialogStatus = 'update'
      this.dialogFormVisible = true
      this.dialogDetail = false
      this.$nextTick(() => {
        this.$refs['dataForm'].clearValidate()
      })
    },

    updateData(row) {
      this.$refs['dataForm'].validate((valid) => {
        if (valid) {
          const checkedNodes = this.$refs.tree.getCheckedKeys()
          const priv = { permissionIds: checkedNodes.join(',') }
          this.temp.priv = priv
          const tempData = Object.assign({}, this.temp)
          console.log(tempData)
          update(tempData).then((response) => {
            const res = response
            if (res.code == 200) {
              this.dialogFormVisible = false
              this.message_tips = '编辑成功'
              this.message_type = 'success'
              this.getList()
            } else {
              this.message_tips = res.message
              this.message_type = 'error'
            }
            messageTip(this.message_tips, this.message_type)
          })
        }
      })
    },
    handleDelete(row) {
      handleCofirm('此操作将永久删除该数据, 是否继续?').then(() => {
        delRole(row.id).then((response) => {
          const res = response
          if (res.code == 200) {
            this.dialogFormVisible = false
            this.message_tips = '删除成功'
            this.message_type = 'success'
            // 成功后重新设置数据
            this.getList()
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
    handleEdit(row) {
      this.resetTemp()
      this.dialogStatus = 'grant'
      this.dialogFormVisible = true
      this.dialogDetail = false
      this.$nextTick(() => {
        this.$refs.dataForm.clearValidate()
      })
    },
    savePermission() {
      const checkedNodes = this.$refs.tree.getCheckedKeys()
      const addData = {
        roleId: this.rolId,
        permissionIds: checkedNodes.join(','),
        lastpermissionIds: this.checkedNodes.join(',')
      }
      console.log(addData)
      // 发送接口
      saveRolePermission(addData).then(response => {
        const res = response
        if (res.code == 200) {
          this.getList()
          this.dialogEditVisible = false
          this.message_tips = '编辑成功'
          this.message_type = 'success'
        } else {
          this.message_tips = res.message
          this.message_type = 'error'
        }
        messageTip(this.message_tips, this.message_type)
      })
    },
    async getRoutes() {
      const res = await getRoutes()
      this.routesData = getTreeData(res.result.treeList)
    },
    handleRoleType(row) {
      let res = ''
      for (const v of role_type_arr) {
        if (row.role_name == v['value']) {
          res = v['label']
          break
        } else {
          res = row.role_name
        }
      }
      return res
    },
    handleEffectTime(row) {
      let res = ''
      for (const v of valid_period) {
        if (row.valid_period == v['value']) {
          res = v['label']
          break
        } else {
          res = row.valid_period
        }
      }
      return res
    },
    handleStartTime(row) {
      let res = ''
      if (row.start_ts == '0000-00-00 00:00:00') {
        res = ''
      } else {
        res = row.start_ts
      }
      return res
    },
    handleEndTime(row) {
      let res = ''
      if (row.end_ts == '0000-00-00 00:00:00') {
        res = ''
      } else {
        res = row.end_ts
      }
      return res
    },
    formatDataTimeTotime(value) {
      return formatDataTimeTotime(value)
    }
  }
}
</script>
