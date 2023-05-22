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
            @click="handleRestore(row,$index)"
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
    <el-dialog :title="textMap[dialogStatus]" :visible.sync="dialogRestoreVisible" custom-class="single_dal_view" >
     <el-form id="form" ref="form" :model="form" :rules="rules" label-width="140px">
      <el-form-item label="集群名称">
        <span>{{ form.name }}</span>
      </el-form-item>
      <el-form-item label="业务名称">
        <span>{{ form.nick_name }}</span>
      </el-form-item>
      <el-form-item label="恢复类型">
        <el-select v-model="backup_type" placeholder="选择恢复类型" @change="setbackupType($event)">
          <el-option v-for="item in backup_type_items" :key="item.value" :label="item.label" :value="item.value">
          </el-option>
        </el-select>
      </el-form-item>
      <el-form-item label="目标表集群:" prop="dst_cluster_id">
        <el-select v-model="form.dst_cluster_id" clearable placeholder="请选择目标表集群" style="width: 100%"
          @change="onDstClusterChange">
          <el-option v-for="item in clusterOptions" :key="item.id" :label="item.name" :value="item.id" />
        </el-select>
      </el-form-item>
      <el-row v-for="(table, index) in form.backup" :key="table.key" :gutter="0">
        <el-col :span="8">
          <el-form-item label="备份记录:" :prop="'backup.' + index + '.db_table'" :rules="{required: true,message: '请选择备份记录',trigger: 'blur'}">
            <el-cascader :key="'srcTable' + index" v-model="form.backup[index].db_table" style="width: 100%" clearable
              placeholder="请选择 库名/模式/表" :options="backupList" filterable  />
          </el-form-item>
        </el-col>

        <el-col :span="8">
          <el-form-item label="开始时间:" :prop="'backup.' + index" :rules="rules.time">
            <el-date-picker v-model="form.backup[index].startTime" type="datetime" value-format="yyyy-MM-dd HH:mm:ss"
              placeholder="开始时间" />
          </el-form-item>
        </el-col>
        <el-col :span="2" :offset="6" v-if="backup_type == 'table' || backup_type == 'schema'">
          <el-button v-if="index === 0"  icon="el-icon-plus" size="small" @click="onPush(index)" />
          <el-button v-else icon="el-icon-minus" size="small" @click="onRemove(index)" />
        </el-col>
      </el-row>
      <el-form-item style="color:red;" label="温馨提示:" ><p>1.开始时间不能大于当前系统时间。</p><p>2.目标表集群不存在和备份集群相同的table和schema。</p></el-form-item>
    </el-form>
    <div slot="footer" class="dialog-footer">
        <el-button @click="dialogRestoreVisible = false" v-show="!dialogDetail">关闭</el-button>
        <el-button type="primary" @click="onSubmit(form)"  v-show="!dialogDetail">确认</el-button>
      </div>
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
import { getLogicalBackUpList,clusterOptions,getEvStatus,getPGTableList,tableRepartition,getLogicalBackUpRecordList } from '@/api/cluster/list'
import Pagination from '@/components/Pagination'
import { timestamp_arr, version_arr } from "@/utils/global_variable";
import { getNowDate, messageTip } from "@/utils";

export default {
  name: 'Operation',
  components: { Pagination },
   props: {
    listsent: { typeof: Object },
  },
  data() {
    const checkTimeRange = (rule, value, cb) => {
      if (value.startTime === "") {
        return cb(new Error("请选择开始时间"));
      }else if(value.startTime >getNowDate()){
        return cb(new Error("开始时间不能大于当前系统时间"));
      }
      cb();
    };
   const checkDstCluster = (rule, value, callback) => {
      if (!value) {
        callback(new Error('请选择目标表集群'))
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
      row: {},
      dialogRestoreVisible:false,
      backup_type: "table",
      backup_type_items: [
        {
          item: "db",
          label: "db",
          value: 'db',
        },
        {
          item: "schema",
          label: "schema",
          value: 'schema',
        }, {
          item: "table",
          label: "table",
          value: 'table',
        }],
      form: {
        src_cluster_id: "",
        dst_cluster_id: "",
        name: "",
        nick_name: "",
        backup: [{ db_table: [], backup_time: "", new_db_table: "", startTime: "", endTime: "" }],
        id: "",
        //clusterOptions: [],
      },
      clusterOptions: [],
      // dst_cluster_id: 0,
      dialogStatusVisible: false,
      activities: [],
      backupIndex:0,
      rules: {
        time: [{ validator: checkTimeRange, trigger: "blur" }],
        dst_cluster_id:[{required: true, validator: checkDstCluster, trigger: "blur" }]
      },
      tableOptions: [],
      backupList: [],
      backupListStr: null,
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
    handleRestore(row){
      this.resetTemp()
      this.dialogRestoreVisible=true;
      this.form.name=row.cluster_name;
      this.form.nick_name=row.nick_name;
      this.form.id=row.cluster_id;
      // 获取原集群名称
      let temp={'cluster_id':row.cluster_id}
      this.clusterOptions =[];
      this.clusterOptionsList(temp);
      this.toGetLogicalBackUpRecordList(this.backup_type)

    },
    async clusterOptionsList(temp){
      clusterOptions(temp).then((res) => {
        this.clusterOptions = res.list;
      });
    },
    toGetLogicalBackUpRecordList(type) {
      getLogicalBackUpRecordList({cluster_id: this.form.id,type:type }).then((res) => {
        this.backupListStr = JSON.stringify(res.list);
        try {
          if(type=='db'){
            this.backupList = res.list['db'];
          }else if(type=='schema'){
            this.backupList = res.list['schema'];
          }else{
            this.backupList = res.list['table'];
          }
        } catch (error) { }
      });
    },
    setbackupType(value) {
      this.toGetLogicalBackUpRecordList(value)
    },
    beforeRestoreDestory() {
      clearInterval(this.timer);
      this.dialogStatusVisible = false;
      this.timer = null;
    },
    onDstClusterChange(e) {
      this.dst_cluster_id = e;
    },
    onPush(index) {
      this.form.backup.push({
        db_table: [],
        backup_time: "",
        startTime: "",
        endTime: "",
      });
    },
    onRemove(index) {
      this.form.backup.splice(index, 1);
    },
    onSubmit(row) {
      const _this = this;
      this.$refs["form"].validate((valid) => {
        if (valid) {
          const data = {};
          data.user_name = sessionStorage.getItem("login_username");
          data.job_id = "";
          data.version = version_arr[0].ver;
          data.job_type = "logical_restore";
          data.timestamp = timestamp_arr[0].time + "";
          const paras = {};
          let repeat=this.repeatObject(this.form.backup,'db_table');
          if(repeat!==null){
            this.message_tips = '备份记录不能重复选择';
            this.message_type = 'error';
            messageTip(this.message_tips,this.message_type);
            return;
          }
          const restore = this.form.backup.map((v) => {
            if (_this.backup_type == "table") {
              return {
                db_table: v.db_table[0].substr(0, v.db_table[0].indexOf("(")),
                restore_time: v.startTime
              };
            }

            if (_this.backup_type == "db") {
              return {
                db_table: v.db_table[0].substr(0, v.db_table[0].indexOf("(")),
                restore_time: v.startTime
              };
            }

            if (_this.backup_type == "schema") {
              return {
                db_table: v.db_table[0].substr(0, v.db_table[0].indexOf("(")),
                restore_time: v.startTime,
              };
            }
          });
          paras.cluster_id = this.form.id;
          paras.restore_type = _this.backup_type;
          paras.src_cluster_id = this.form.id;
          paras.dst_cluster_id = _this.form.dst_cluster_id;
          paras.restore = restore;

          data.paras = paras;
          tableRepartition(data).then((res) => {
            if (res.code === 200) {
              this.form.backup = [
                { db_table: [], backup_time: "", startTime: "", endTime: "" },
              ];
            }
            if (res.status === "accept") {
              this.dialogRestoreVisible = false;
              this.dialogStatusVisible = true;
              this.activities = [];
              const newArr = {
                content: "逻辑恢复中...",
                timestamp: getNowDate(),
                size: "large",
                type: "primary",
                icon: "el-icon-more",
              };
              this.activities.push(newArr);
              let i = 0;
              this.timer = null;
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
          });
        }
      });
    },
    repeatObject(obj,field) {
      var count = 0;
      var len = obj.length, result = new Array(), resultList = new Array();
      for (var i = 0, x = 0; i < len; i++) {
          var id = obj[i][field];
          if (result[id]) {
              resultList[x] = id;
              count = 1, x++; 
          } else {
              result[id] = 1;
          }
      }
      if (count == 1) {
          return resultList;
      }
      return null;
    },
     resetTemp() {
      this.form = {
        src_cluster_id: "",
        dst_cluster_id: "",
        name: "",
        nick_name: "",
        backup: [{ db_table: [], backup_time: "", new_db_table: "", startTime: "", endTime: "" }],
        id: "",
        clusterOptions: [],
      }
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
                content: "逻辑恢复成功",
                timestamp: getNowDate(),
                color: "#0bbd87",
                icon: "el-icon-circle-check",
              };
              this.activities.push(newArrdone);
              this.getList();
            } else {
              //获取attachment里的fail_dts错误数据全部显示出来
              let errors='';
              if(res.attachment.fail_dts){
                //遍历取出数据
                for (let i = 0; i<res.attachment.fail_dts.length;i++) {
                    errors+=res.attachment.fail_dts[i].host+res.attachment.fail_dts[i].db_table+res.attachment.fail_dts[i].errmsg+';'
                }
                error_info=errors
              }
              const newArr = {
                content: error_info,
                timestamp: getNowDate(),
                color: "red",
                icon: "el-icon-circle-close",
              };
              this.activities.push(newArr);
            }
          } else {
          }
        });
        if (i >= 86400) {
          clearInterval(timer);
        }
      }, 0);
    },
    
  }
}
</script>
