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
          <span class="link-type click_btn" @click="handleDetail(row)">{{ row.id }}</span>
        </template>
      </el-table-column>
      <el-table-column
            prop="job_type"
            align="center"
            label="任务名称">
      </el-table-column>
      <el-table-column
            prop="cluster_id"
            align="center"
            label="集群ID">
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
            <template slot-scope="scope">
          <span v-if="scope.row.job_type==='新增集群' && scope.row.status==='failed'"><el-button type="text" @click="failedDetail(scope.row.memo)">详情</el-button></span>
          <span v-else-if="scope.row.job_type==='表重分布' && scope.row.status==='done'"><el-button type="text" @click="handleDelete(scope.row)">删除源表</el-button></span>
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
import {getEvStatus} from "@/api/cluster/list";
 import { getOperationList,delTable} from '@/api/operation/record'
 import Pagination from '@/components/Pagination' 
 import JsonViewer from 'vue-json-viewer'
 import {handleCofirm,messageTip,getNowDate} from "@/utils";
 import { version_arr, timestamp_arr } from '@/utils/global_variable'

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
      timer: null,
      dialogStatusVisible: false,
      activities : [],
    };
  },
  created() {
    this.getList()
  },
  destroyed() {
    clearInterval(this.timer);
    this.timer = null;
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
    beforeRestoreDestory() {
    clearInterval(this.timer);
    this.dialogStatusVisible = false;
    this.timer = null;
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
    },
    handleDelete(row) {
      let info=eval('(' + row.job_info.replace("\\", "") + ')');; 
      handleCofirm("此操作将永久删除源表, 是否继续?").then( () =>{
        const data = {};
        data.user_name = sessionStorage.getItem("login_username");
        data.job_id = "";
        data.version = version_arr[0].ver;
        data.job_type = "del_table_partition_source_table";
        data.timestamp = timestamp_arr[0].time + "";
        const paras = {};
        paras.cluster_id = info.paras.src_cluster_id;
        paras.table_partition_id = row.id;
        data.paras = paras;
        delTable(data).then((response)=>{
          let res = response;
          if (res.code === 200) {
            console.log(res);
            this.$message.info("提交成功");
            this.$refs["form"].resetFields();
          }

          if (res.status === "accept") {
            this.dialogStatusVisible = true;
            this.activities = [];
            const newArr = {
              content: "删除源表中...",
              timestamp: getNowDate(),
              size: "large",
              type: "primary",
              icon: "el-icon-more",
            };
            this.activities.push(newArr);
            let i = 0;
            //this.timer = null;
            this.timer = setInterval(() => {
              this.getStatus(this.timer, res.job_id, i++);
            }, 1000);
          } else if (res.status === "ongoing") {
            this.message_tips = "系统正在操作中，请等待一会！";
            this.message_type = "error";
            messageTip(this.message_tips, this.message_type);
          } else {
            this.message_tips = res.error_info;
            this.message_type = "error";
            messageTip(this.message_tips, this.message_type);
          }
      })
      }).catch(() => {
          messageTip('已取消删除','info');
      }); 
    },
    getStatus(timer, data, i) {
      setTimeout(() => {
        const postarr = {};
        postarr.job_type = "get_status";
        postarr.version = version_arr[0].ver;
        postarr.job_id = data;
        postarr.timestamp = timestamp_arr[0].time + "";
        postarr.paras = {};
        getEvStatus(postarr).then((res) => {
          let error_info = res.error_info;
          if (res.status === "done" || res.status === "failed") {
            clearInterval(timer);
            if (res.status === "done") {
              const newArrdone = {
                content: "删除源表成功",
                timestamp: getNowDate(),
                color: "#0bbd87",
                icon: "el-icon-circle-check",
              };
              this.activities.push(newArrdone);

              this.getList();
            } else {
              console.log(error_info);
              const newArr = {
                content: error_info,
                timestamp: getNowDate(),
                color: "red",
                icon: "el-icon-circle-close",
              };
              this.activities.push(newArr);
            }
          } else {
            if (error_info) {
              // const newArrgoing = {
              //   content: error_info,
              //   timestamp: getNowDate(),
              //   color: '#0bbd87'
              // }
              // this.activities.push(newArrgoing)
            }
          }
        });
        if (i >= 86400) {
          clearInterval(timer);
        }
      }, 0);
    },
  },
};
</script>