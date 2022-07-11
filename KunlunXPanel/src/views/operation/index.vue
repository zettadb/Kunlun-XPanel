<template>
  <div class="app-container">
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
      <el-table-column
            align="center"
            label="任务ID"  >
             <template slot-scope="{row}">
          <span class="link-type" @click="handleDetail(row)">{{ row.id }}</span>
        </template>
      </el-table-column>
      <el-table-column
            prop="job_type"
            align="center"
            label="任务名称">
      </el-table-column>
      <el-table-column
            prop="object"
            align="center"
            label="操作对象">
      </el-table-column>
      <el-table-column
            prop="when_started"
            align="center"
            label="开始时间">
      </el-table-column>

      <el-table-column
            prop="when_ended"
            align="center"
            label="结束时间">
      </el-table-column>

      <el-table-column
            prop="status"
            align="center"
            label="状态">
      </el-table-column>
       <el-table-column
            prop="info"
            align="center"
            :show-overflow-tooltip="true"
            label="结果信息">
            <template slot-scope="scope">
          <span v-if="scope.row.job_type==='新增集群' && scope.row.status==='failed'"><el-button type="text" @click="failedDetail(scope.row.memo)">详情</el-button></span>
          <span v-else>{{scope.row.info}}</span>
        </template>
      </el-table-column>
       <el-table-column
            prop="user_name"
            align="center"
            label="操作账号"
             v-if="user_name==='super_dba'"
            >
      </el-table-column>
      <!-- <el-table-column
        label="查看信息"
        align="center"
        width="230"
        class-name="small-padding fixed-width"
      >
        <template slot-scope="{row}">
          <el-button type="primary" size="mini" @click="handleDetail(row)">查看</el-button>
        </template>
      </el-table-column> -->
    </el-table>
    <pagination v-show="total>0" :total="total" :page.sync="listQuery.pageNo" :limit.sync="listQuery.pageSize" @pagination="getList" :page-sizes="[10, 20, 30, 40, 50]" />
    
    <el-dialog title="错误信息" :visible.sync="dialogFailedInfo" custom-class="single_dal_view" :close-on-click-modal="false">
      <json-viewer :value="createClusterErr"></json-viewer>
    </el-dialog>
  </div>
</template>

<script>
 import { getOperationList} from '@/api/operation/record'
 import Pagination from '@/components/Pagination' 
 import JsonViewer from 'vue-json-viewer'

export default {
  name: "operation",
  components: { Pagination,JsonViewer },
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
        username: ''
      }, 
      temp:{},
      row:{},
      user_name:sessionStorage.getItem('login_username'),
      dialogStatus: "",
      textMap: {
        detail: "详情",
      },
      dialogDetail: false,
      dialogFormVisible: false,
       dialogFailedInfo:false,
      createClusterErr:'',
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
      queryParam.username=sessionStorage.getItem('login_username');
      //模糊搜索
      getOperationList(queryParam).then(response => {
        this.list = response.list;
        this.total = response.total;
        setTimeout(() => {
          this.listLoading = false
        }, 0.5 * 1000)
      });
    },
    handleDetail(row){
        this.$alert(row.list, '详细信息', {
          dangerouslyUseHTMLString: true
        });
    },
    failedDetail(info){
      let info1=info.replace("\\", ""); 
      this.createClusterErr=  JSON.parse(info1.replace(/\r\n/g,'').replace(/\n/g,''));
      this.dialogFailedInfo=true;
      //console.log(this.createClusterErr);
    },
    
  },
};
</script>