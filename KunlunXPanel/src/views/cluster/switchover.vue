<template>
  <div class="app-container">
    <div class="filter-container">
      <div class="table-list-search-wrap">
        <el-date-picker
          v-model="listQuery.startTime"
          type="datetime"
          value-format="yyyy-MM-dd HH:mm:ss"
          placeholder="起始时间" style="width:200px;margin-right:5px" >
        </el-date-picker>-

        <el-date-picker
          v-model="listQuery.endTime"
          type="datetime"
          value-format="yyyy-MM-dd HH:mm:ss"
          placeholder="结束时间" style="width:200px;margin-right:10px">
        </el-date-picker>
        <el-input
          class="list_search_keyword"
          v-model="listQuery.hostaddr"
          placeholder="可输入原IP地址搜索"
          @keyup.enter.native="handleFilter"
        />
        <el-button icon="el-icon-search" @click="handleFilter">
          查询
        </el-button>
        <el-button icon="el-icon-refresh-right" @click="handleClear">
          重置
        </el-button>
        <div v-text="info" v-show="installStatus===true" class="info"></div>
      </div>
      <div class="table-list-wrap"></div>
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

      <el-table-column label="taskid" align="center">
        <template slot-scope="{row}">
          <span class="link-type" @click="handleDetail(row)">{{ row.taskid }}</span>
        </template>
      </el-table-column>
      <el-table-column
        prop="cluster_id"
        align="center"
        label="cluster_id">
      </el-table-column>
      <el-table-column
        prop="shard_id"
        align="center"
        label="shard_id">
      </el-table-column>
      <el-table-column
        prop="shard_name"
        align="center"
        label="shard_name">
      </el-table-column>

      <el-table-column
        prop="host"
        align="center"
        label="原IP地址">
      </el-table-column>

      <el-table-column
        prop="new_master_host"
        align="center"
        label="新IP地址">
      </el-table-column>
      <el-table-column
        prop="start_time"
        align="center"
        label="开始时间">
      </el-table-column>
      <!-- <el-table-column
        prop="end_time"
        align="center"
        label="结束时间">
      </el-table-column> -->
      <el-table-column
        prop="count_time"
        align="center"
        label="耗时">
      </el-table-column>
      <el-table-column
        prop="status"
        align="center"
        label="状态">
         <template slot-scope="scope">
            <span v-if="scope.row.status==='成功'" style="color: #00ed37">成功</span>
            <span v-else-if="scope.row.status==='失败'" style="color: red">失败</span>
        </template>
      </el-table-column>
      <el-table-column
        prop="err_msg"
        align="center"
        :show-overflow-tooltip="true"
        label="结果信息">
      </el-table-column>
    </el-table>

    <pagination v-show="total>0" :total="total" :page.sync="listQuery.pageNo" :limit.sync="listQuery.pageSize" @pagination="getList" :page-sizes="[10, 20, 30, 40, 50]" />

    <el-dialog :title="textMap[dialogStatus]" :visible.sync="dialogFormVisible" custom-class="single_dal_view" width="90%">
      <el-form
        ref="dataForm"
        :rules="rules"
        :model="temp"
        label-position="left"
        label-width="1200px"
      >
        <el-table  height="360" :data="history" border highlight-current-row style="width: 100%;margin-bottom: 20px;" :disabled="dialogStatus==='detail'" >
          <el-table-column type="index" align="center" label="序号" width="50"></el-table-column>
          <!-- <el-table-column prop="id" align="center" label="id"></el-table-column> -->
          <el-table-column prop="host" align="center" label="host"></el-table-column>
          <el-table-column prop="shard_id" align="center" label="shard_id"></el-table-column>
          <!-- <el-table-column prop="cluster_id" align="center" label="cluster_id"></el-table-column> -->
          <!-- <el-table-column prop="taskid" align="center" label="taskid"></el-table-column> -->
          <el-table-column prop="step" align="center" label="step"></el-table-column>
          <el-table-column prop="new_master_host" align="center" label="new_master_host"></el-table-column>
          <!-- <el-table-column prop="description" align="center" label="description"></el-table-column> -->
          <el-table-column prop="consfailover_msg" align="center" label="consfailover_msg" :show-overflow-tooltip="true"></el-table-column>
          <el-table-column prop="timestamp" align="center" label="timestamp"></el-table-column>
          <!-- <el-table-column prop="err_code" align="center" label="err_code"></el-table-column> -->
          <el-table-column prop="err_msg" align="center" label="err_msg"></el-table-column>
        </el-table>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="dialogFormVisible = false" v-show="!dialogDetail">关闭</el-button>
        <el-button type="primary" @click="dialogStatus==='create'?createData():updateData(row)"  v-show="!dialogDetail">确认</el-button>
      </div>
    </el-dialog>
  </div>
</template>
<script>
 import { getSwitcheOverList,getTaskList} from '@/api/cluster/list'
 import Pagination from '@/components/Pagination' 
export default {
  name: "account",
  components: { Pagination },
  data() {
    return {
      tableKey: 0,
      list: null,
      history:null,
      listLoading: true,
      searchLoading:false,
      total: 0,
      
      listQuery: {
        pageNo: 1,
        pageSize: 10,
        hostaddr: '',
        startTime:'',
        endTime:'',
        id:''

      },
      temp: {
        hostaddr: '',
        rack_id:'',
        datadir:'',
        logdir:'',
        wal_log_dir:'',
        comp_datadir:'',
        total_cpu_cores:'',
        total_mem:''
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
      installStatus:false,
      info:'',
      row:{},
      rules: {
      },
      //id: null, 
    };
  },
  created() {
    this.getParams(); 
    this.getList()
  },
  methods: {
    getParams() {
      this.listQuery.id = this.$route.query.id
    },
    handleFilter() {
      this.listQuery.pageNo = 1
      this.getList()
    },
    handleClear(){
      this.listQuery.hostaddr = '';
      this.listQuery.startTime = '';
      this.listQuery.endTime = '';
      this.listQuery.pageNo = 1;
      this.listQuery.id=this.listQuery.id;
      this.getList()
    },
    getList() {
        this.listLoading = true
        let queryParam = Object.assign({}, this.listQuery)
        //console.log(queryParam);
        //模糊搜索
        getSwitcheOverList(queryParam).then(response => {
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
      this.dialogDetail = true
      let tempn={};
      tempn.taskid=row.taskid;
      getTaskList(tempn).then(response => {
          this.history = response.list;
          setTimeout(() => {
            this.listLoading = false
          }, 0.5 * 1000)
        });
    },

  },
};
</script>