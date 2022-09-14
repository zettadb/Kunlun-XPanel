<<<<<<< HEAD
<template>
  <div class="app-container">
    <div class="filter-container">
      <!-- <div class="table-list-search-wrap">
        <el-input
          class="list_search_keyword"
          v-model="listQuery.username"
          placeholder="可输入用户账号搜索"
          @keyup.enter.native="handleFilter"
        />
        <el-button  icon="el-icon-search" @click="handleFilter">
          查询
        </el-button>
        <el-button  icon="el-icon-refresh-right" @click="handleClear">
          重置
        </el-button>
        <el-button
          class="filter-item"
          type="primary"
          icon="el-icon-plus"
          @click="handleCreate"
        >新增</el-button>
      </div> -->
      <!-- <div class="table-list-wrap" v-show="user_add_priv==='Y'">
        <el-button
          class="filter-item"
          type="primary"
          icon="el-icon-plus"
          @click="handleCreate"
        >新增</el-button>
      </div> -->
    </div>

    <el-table
      :key="tableKey"
      v-loading="listLoading"
      :data="list"
      border
      highlight-current-row
      style="width: 100%;margin-bottom: 20px;">
    >
      <el-table-column
        type="index"
        align="center"
        label="序号"
        width="50">
      </el-table-column>

      <el-table-column label="集群名称" align="center">
        <template slot-scope="{row}">
          <span class="link-type" @click="handleDetail(row)">{{ row.cluster_name }}</span>
        </template>
      </el-table-column>

      <el-table-column
            prop="start_ts"
            align="center"
            label="开始时间">
      </el-table-column>

      <el-table-column
            prop="end_ts"
            align="center"
            label="结束时间">
      </el-table-column>
      
    </el-table>

    <pagination v-show="total>0" :total="total" :page.sync="listQuery.pageNo" :limit.sync="listQuery.pageSize" @pagination="getList" />
  </div>
</template>

<script>
 import {getBackUpList} from '@/api/cluster/list'
 import Pagination from '@/components/Pagination' 

export default {
  name: "operation",
  components: { Pagination },
  data() {
    return {
      tableKey: 0,
      list: null,
      listLoading: true,
      searchLoading:false,
      total: 0,
      listQuery: {
        pageNo: 1,
        pageSize: 10,
        username: '',
      },
      dialogFormVisible: false,
      dialogEditVisible:false,
      dialogStatus: "",
      textMap: {
        detail: "详情"
      },
      dialogDetail: false,
      message_tips:'',
      message_type:'',
      row:{},
    };
  },
  created() {
    this.getList()
  },
  methods: {
    handleFilter() {
      this.listQuery.pageNo = 1
      this.getList()
    },
    handleClear(){
      this.listQuery.username = ''
      this.listQuery.pageNo = 1
      this.getList()
    },
    getList() {
        this.listLoading = true
        let queryParam = Object.assign({}, this.listQuery)
        //模糊搜索
        getBackUpList(queryParam).then(response => {
          this.list = response.list;
          this.total = response.total;
          setTimeout(() => {
            this.listLoading = false
          }, 0.5 * 1000)
        });
    },
    handleDetail(row){
      this.dialogStatus = "detail"
      this.dialogFormVisible = true
      this.temp = Object.assign({}, row);
      this.dialogDetail = true
    },
  
  },
};
=======
<template>
  <div class="app-container">
    <div class="filter-container">
      <div class="table-list-search-wrap">
        <el-select v-model="listQuery.backup_type" placeholder="请选择备份类型" class="list_search_select">
          <el-option label="存储" value="storage"></el-option>
          <el-option label="计算" value="compute"></el-option>
        </el-select>
        <el-select v-model="listQuery.status" placeholder="请选择状态" class="list_search_select">
          <el-option label="not_started" value="not_started"></el-option>
          <el-option label="ongoing" value="ongoing"></el-option>
          <el-option label="done" value="done"></el-option>
          <el-option label="failed" value="failed"></el-option>
        </el-select>
        <el-button  icon="el-icon-search" @click="handleFilter">查询</el-button>
        <el-button  icon="el-icon-refresh-right" @click="handleClear">重置</el-button>
      </div> 
      <!-- <div class="table-list-wrap" v-show="user_add_priv==='Y'">
        <el-button
          class="filter-item"
          type="primary"
          icon="el-icon-plus"
          @click="handleCreate"
        >新增</el-button>
      </div> -->
    </div>

    <el-table
      :key="tableKey"
      v-loading="listLoading"
      :data="list"
      border
      highlight-current-row
      style="width: 100%;margin-bottom: 20px;">
      <el-table-column
        type="index"
        align="center"
        label="序号"
        width="50">
      </el-table-column>

      <el-table-column label="集群名称" align="center">
        <template slot-scope="{row}">
          <span class="link-type" @click="handleDetail(row)">{{ row.cluster_name }}</span>
        </template>
      </el-table-column>

      <el-table-column
            prop="nick_name"
            align="center"
            label="业务名称">
      </el-table-column>
      <el-table-column
            prop="shard_name"
            align="center"
            label="shard名称">
      </el-table-column>
      <el-table-column
            prop="comp_name"
            align="center"
            label="计算节点名称">
      </el-table-column>
      <el-table-column
            prop="backup_type"
            align="center"
            label="备份类型">
            <template slot-scope="scope">
              <span v-if="scope.row.backup_type==='storage'">存储</span>
              <span v-else-if="scope.row.backup_type==='compute'">计算</span>
              <span v-else></span>
            </template>
      </el-table-column>
       <el-table-column
            prop="status"
            align="center"
            label="状态">
            <template slot-scope="scope">
              <span v-if="scope.row.status==='failed'" style="color:red">{{scope.row.status}}</span>
              <span v-else>{{scope.row.status}}</span>
            </template>
      </el-table-column>
      <el-table-column
            prop="info"
            align="center"
            :show-overflow-tooltip="true"
            label="结果信息">
      </el-table-column>
      <el-table-column
            prop="start_ts"
            align="center"
            label="开始时间">
      </el-table-column>

      <el-table-column
            prop="end_ts"
            align="center"
            label="结束时间">
      </el-table-column>
      
    </el-table>

    <pagination v-show="total>0" :total="total" :page.sync="listQuery.pageNo" :limit.sync="listQuery.pageSize" @pagination="getList" />
  </div>
</template>

<script>
 import {getBackUpList} from '@/api/cluster/list'
 import Pagination from '@/components/Pagination' 

export default {
  name: "operation",
  components: { Pagination },
  data() {
    return {
      tableKey: 0,
      list: null,
      listLoading: true,
      searchLoading:false,
      total: 0,
      listQuery: {
        pageNo: 1,
        pageSize: 10,
        username: '',
        backup_type:'',
        status:''
      },
      dialogFormVisible: false,
      dialogEditVisible:false,
      dialogStatus: "",
      textMap: {
        detail: "详情"
      },
      dialogDetail: false,
      message_tips:'',
      message_type:'',
      row:{},
    };
  },
  created() {
    this.getList()
  },
  methods: {
    handleFilter() {
      this.listQuery.pageNo = 1
      this.getList()
    },
    handleClear(){
      this.listQuery.username = ''
      this.listQuery.backup_type = ''
      this.listQuery.status = ''
      this.listQuery.pageNo = 1
      this.getList()
    },
    getList() {
        this.listLoading = true
        let queryParam = Object.assign({}, this.listQuery)
        //模糊搜索
        getBackUpList(queryParam).then(response => {
          this.list = response.list;
          this.total = response.total;
          setTimeout(() => {
            this.listLoading = false
          }, 0.5 * 1000)
        });
    },
    handleDetail(row){
      this.dialogStatus = "detail"
      this.dialogFormVisible = true
      this.temp = Object.assign({}, row);
      this.dialogDetail = true
    },
  
  },
};
>>>>>>> 1.0
</script>