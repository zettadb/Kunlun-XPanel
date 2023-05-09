<template>
  <div class="app-container">
    <div class="filter-container">
      <div class="table-list-search-wrap">
        <el-select v-model="listQuery.status" placeholder="请选择状态" class="list_search_select">
          <el-option label="not_started" value="not_started" />
          <el-option label="ongoing" value="ongoing" />
          <el-option label="done" value="done" />
          <el-option label="failed" value="failed" />
        </el-select>
        <el-button icon="el-icon-search" @click="handleFilter">查询</el-button>
        <el-button icon="el-icon-refresh-right" @click="handleClear">重置</el-button>
      </div>
     
    </div>

    <el-table
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

      <el-table-column label="集群名称" align="center">
        <template slot-scope="{row}">
          <span class="link-type">{{ row.cluster_name }}</span>
        </template>
      </el-table-column>

      <el-table-column
        prop="nick_name"
        align="center"
        label="业务名称"
      />
      <el-table-column
        prop="job_id"
        align="center"
        label="任务ID"
      />
      <el-table-column
        prop="backup_state"
        align="center"
        label="状态"
      >
        <template slot-scope="scope">
          <span v-if="scope.row.backup_state==='failed'" style="color:red">{{ scope.row.backup_state }}</span>
          <span v-else>{{ scope.row.backup_state }}</span>
        </template>
      </el-table-column>
      <el-table-column
        prop="backup_info"
        align="center"
        :show-overflow-tooltip="true"
        label="结果信息"
      />
      <el-table-column
        prop="backup_time"
        align="center"
        label="备份时间"
      />
      <el-table-column
        label="操作"
        align="center"
        class-name="small-padding fixed-width"
        >
        <template slot-scope="{row,$index}">
          <el-button
          v-if="row.backup_state==='done'"
            size="mini"
            type="primary"
            @click="handleDelete(row,$index)"
          >逻辑恢复
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
  </div>
</template>
<script>
import { getLogicalBackUpList } from '@/api/cluster/list'
import Pagination from '@/components/Pagination'

export default {
  name: 'Operation',
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
        username: '',
        status: ''
      },
      dialogFormVisible: false,
      dialogEditVisible: false,
      dialogStatus: '',
      textMap: {
        detail: '详情'
      },
      dialogDetail: false,
      message_tips: '',
      message_type: '',
      row: {}
    }
  },
  created() {
    this.getList()
  },
  methods: {
    handleFilter() {
      this.listQuery.pageNo = 1
      this.getList()
    },
    handleClear() {
      this.listQuery.username = ''  
      this.listQuery.status = ''
      this.listQuery.pageNo = 1
      this.getList()
    },
    getList() {
      this.listLoading = true
      const queryParam = Object.assign({}, this.listQuery)
      // 模糊搜索
      getLogicalBackUpList(queryParam).then(response => {
        this.list = response.list
        this.total = response.total
        setTimeout(() => {
          this.listLoading = false
        }, 0.5 * 1000)
      })
    },

  }
}
</script>
