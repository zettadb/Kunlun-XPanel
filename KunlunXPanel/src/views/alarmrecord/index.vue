<template>
  <div class="app-container">
    <div class="filter-container">
      <div class="table-list-search-wrap">
        <el-select v-model="listQuery.job_type" placeholder="请选择告警类型" class="list_search_select" style="width:150px;">
           <el-option
            v-for="item in alarmTypes"
            :key="item.id"
            :label="item.label"
            :value="item.id">
          </el-option>
        </el-select>
        <el-button icon="el-icon-search" @click="handleFilter">
          查询
        </el-button>
        <el-button icon="el-icon-refresh-right" @click="handleClear">
          重置
        </el-button>
      </div>
      <div class="table-list-wrap"></div>
    </div>
    <el-table
    ref="multipleTable"
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

      <el-table-column label="告警类别" align="center">
        <template slot-scope="{row}">
          <span class="link-type" @click="handleDetail(row)">{{ row.job_type }}</span>
        </template>
      </el-table-column>

      <el-table-column
        prop="user_name"
        align="center"
        label="操作账户">
      </el-table-column>
      <el-table-column
        prop="alarm_status"
        align="center"
        label="告警状态">
        <template slot-scope="scope">
          <span v-if="scope.row.alarm_status==='1'" >已处理</span>
          <span v-else-if="scope.row.alarm_status==='2'">忽略</span>
          <span v-else>未处理</span>
        </template>
      </el-table-column>
      
      <el-table-column
        prop="info"
        align="center"
        :show-overflow-tooltip="true"
        label="错误信息">
      </el-table-column>
      
      <el-table-column
        label="操作"
        align="center"
        width="180"
        class-name="small-padding fixed-width">
        <template slot-scope="{row}">
          <el-button type="primary" size="mini" @click="hanldeAlarm(row)">处理</el-button>
          <el-button type="warning" size="mini" @click="hanldeAlarm(row)">忽略</el-button>
        </template>
      </el-table-column>
    </el-table>
    <pagination v-show="total>0" :total="total" :page.sync="listQuery.pageNo" :limit.sync="listQuery.pageSize" @pagination="getList" />
  </div>
</template>
<script>
 import {getAlarmRecordList} from '@/api/alarmrecord/list'
 import {alarm_type_arr} from "@/utils/global_variable"
 import Pagination from '@/components/Pagination' 
export default {
  name: "alarmrecord",
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
        job_type: '',
      },
      alarmTypes:alarm_type_arr,
      dialogFormVisible: false,
      dialogEditVisible:false,
      dialogUploadVisible:false,
      dialogStatus: "",
      // textMap: {
      //   update: "编辑计算机",
      //   create: "新增计算机",
      //   detail: "详情",
      //   upload:'批量导入'
      // },
      dialogDetail: false,
      message_tips:'',
      message_type:'',
      installStatus:false,
      info:'',
      row:{},
      machine_add_priv:JSON.parse(sessionStorage.getItem('priv')).machine_add_priv,
      machine_drop_priv:JSON.parse(sessionStorage.getItem('priv')).machine_drop_priv,
      machine_priv:JSON.parse(sessionStorage.getItem('priv')).machine_priv,
      rules: {
      },
    };
  },
  created() {
    this.getList()
  },
  methods:{
    handleFilter() {
      this.listQuery.pageNo = 1
      this.getList()
    },
    handleClear(){
      this.listQuery.job_type = ''
      this.listQuery.pageNo = 1
      this.getList()
    },
    getList() {
      this.listLoading = true
      let queryParam = Object.assign({}, this.listQuery)
      //模糊搜索
      getAlarmRecordList(queryParam).then(response => {
        this.list = response.list;
        this.total = response.total;
        setTimeout(() => {
          this.listLoading = false
        }, 0.5 * 1000)
      });
    },

  },
};
</script>
<style >
.right_input_min{
  width:25%;
}
</style>