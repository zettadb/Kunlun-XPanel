<template>
  <div class="app-container">
    <div class="filter-container">
      <div class="table-list-search-wrap">
        <el-button class="filter-item" type="primary" icon="el-icon-plus" @click="handleCreate">添加Schema
        </el-button>
      </div>
    </div>
    <el-table ref="multipleTable" :key="tableKey" v-loading="listLoading" :data="list" border highlight-current-row
      style="width: 100%; margin-bottom: 20px">

      <el-table-column prop="nspname" align="center" label=" 名称" />
      <el-table-column prop="nspacl" align="center" width="400" label="权限" />

      <el-table-column label="操作" align="center" width="400" class-name="small-padding fixed-width">
        <template slot-scope="{ row, $index }">
          <el-button size="mini" type="primary" @click="handleSetCpu(row)">授权
          </el-button>
          <el-button size="mini" type="danger" @click="handleControlNode(row, 'stop')">删除
          </el-button>
        </template>
      </el-table-column>
    </el-table>
    <pagination v-show="total > 0" :total="total" :page.sync="listQuery.pageNo" :limit.sync="listQuery.pageSize"
      @pagination="getList" />
    <!-- 添加schema-->
    <el-dialog :title="textMap[dialogStatus]" :visible.sync="dialogNodeVisible" custom-class="single_dal_view"
      :close-on-click-modal="false">
      <el-form ref="dataForm" :model="temp" :rules="rules" label-position="left" label-width="130px">
        <el-form-item label="集群名称:" prop="name">
          <span>{{ temp.name }}</span>
        </el-form-item>
        <el-form-item label="业务名称:" prop="nick_name">
          <span>{{ temp.nick_name }}</span>
        </el-form-item>
        <el-form-item label="schmea 名称:" prop="schema">
          <el-input v-model="temp.schema" class="right_input" placeholder="请输入schmea 名称">
          </el-input>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="dialogNodeVisible = false">关闭</el-button>
        <el-button type="primary" @click="createData()">确认</el-button>
      </div>
    </el-dialog>
    <!-- 添加删除schema状态框 -->
    <el-dialog :title="job_id" :visible.sync="dialogStatusShowVisible" custom-class="single_dal_view"
      :close-on-click-modal="false" :before-close="beforeDestory">
      <div style="width: 100%; background: #fff; padding: 0 20px">
        <el-steps direction="vertical" :active="init_active">
          <el-step v-if="init_show" :title="init_title" icon="el-icon-more" />
          <el-step v-if="computer_show" :title="computer_title" :status="computer_state" :icon="computer_icon"
            :description="computer_description">
            <template slot="description">
              <span>{{ computer_description }}</span>
              <div style="padding: 20px">
                <el-steps direction="vertical" :active="comp_active">
                  <el-step v-for="(item, index) of computer" :key="index" :title="item.title" :icon="item.icon"
                    :status="item.status" :description="item.description" />
                </el-steps>
              </div>
            </template>
          </el-step>

          <el-step v-if="finish_show" :title="finish_title" :icon="finish_icon" :description="finish_description"
            :status="finish_state" />
        </el-steps>
      </div>
    </el-dialog>
    <!--  状态框 -->
    <el-dialog :visible.sync="dialogStatusVisible" custom-class="single_dal_view" width="400px"
      :close-on-click-modal="false" :before-close="beforeSyncDestory">
      <div class="block">
        <el-timeline>
          <el-timeline-item v-for="(activity, index) in activities" :key="index" :icon="activity.icon"
            :type="activity.type" :color="activity.color" :size="activity.size" :timestamp="activity.timestamp">
            {{ activity.content }}
          </el-timeline-item>
        </el-timeline>
      </div>
    </el-dialog>
    <el-dialog :title="textMap[dialogStatus]" :visible.sync="dialogFormVisible" custom-class="single_dal_view">
      <div class="box">
        <div title="">
          <el-form ref="dataForm" :rules="rules" :model="temp" label-position="left" label-width="120px">
            <el-form-item v-show="dialogStatus === 'detail'" label="schemaID:" prop="id">
              <span>{{ temp.id }}</span>
            </el-form-item>
            <el-form-item v-show="dialogStatus === 'detail'" label="集群ID:" prop="db_cluster_id">
              <span>{{ temp.db_cluster_id }}</span>
            </el-form-item>
            <el-form-item v-show="dialogStatus === 'detail'" label="IP地址:" prop="hostaddr">
              <span>{{ temp.hostaddr }}</span>
            </el-form-item>
            <el-form-item v-show="dialogStatus === 'detail'" label="端口:" prop="port">
              <span>{{ temp.port }}</span>
            </el-form-item>
            <el-form-item v-show="dialogStatus === 'detail'" label="cpu个数:" prop="cpu_cores">
              <span>{{ temp.cpu_cores }}</span>
            </el-form-item>
          </el-form>
        </div>
        <div title="">
          <el-form ref="dataForm" :rules="rules" :model="temp" label-position="left" label-width="150px">
            <el-form-item v-show="dialogStatus === 'detail'" label="状态:" prop="status">
              <span>{{ temp.status | statusString }}</span>
            </el-form-item>
            <el-form-item v-show="dialogStatus === 'detail'" label="schema名称:" prop="name">
              <span>{{ temp.name }}</span>
            </el-form-item>
            <el-form-item v-show="dialogStatus === 'detail'" label="最大内存（MB）:" prop="max_mem_MB">
              <span>{{ temp.max_mem_MB }}</span>
            </el-form-item>
            <el-form-item v-show="dialogStatus === 'detail'" label="最大连接数:" prop="max_conns">
              <span>{{ temp.max_conns }}</span>
            </el-form-item>
          </el-form>
        </div>
      </div>
    </el-dialog>

    <!-- scheame 授权-->
    <el-dialog title="Schema 授权" :visible.sync="dialogCpuVisible" custom-class="single_dal_view">
      <el-form ref="cpuForm" :model="cpu_paras" label-position="left" label-width="100px">
        <el-form-item label="Schema:" prop="nspname">
          <span>{{ cpu_paras.nspname }}</span>
        </el-form-item>
        <el-form-item label="当前权限:" prop="nspacl">
          <span>{{ cpu_paras.nspacl }}</span>
        </el-form-item>
        <el-form-item label="授权用户:" prop="user_name">
          <el-select v-model="cpu_paras.user_name" clearable multiple placeholder="授权用户">
            <el-option v-for="item in AccountList" :key="item.value" :label="item.username"
              :value="item.user_tag + '_' + item.username" />
          </el-select>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="dialogCpuVisible = false">关闭</el-button>
        <el-button type="primary" @click="SaveCpuSetData(cpu_paras)">确认</el-button>
      </div>
    </el-dialog>
  </div>
</template>
<script>
import { messageTip, createCode, gotoCofirm, getNowDate } from "@/utils";
import { getAccountList } from '@/api/system/account'
import {
  computeList,
  getEvStatus,
  getCompMachine,
  addSchmea,
  createSchemaRole,
  deleteSchmea,
  getCompsCount,
  delComp,
} from "@/api/schema/list";
import { version_arr, timestamp_arr, ip_arr } from "@/utils/global_variable";
import Pagination from "@/components/Pagination";
import { pgsqlDashboard } from "@/api/grafana/list";

export default {
  name: "Complist",
  components: { Pagination },
  filters: {
    statusString: function (value) {
      if (value == "active") {
        return "运行中";
      } else if (value == "creating") {
        return "安装中";
      } else if (value == "inactive") {
        return "异常";
      } else if (value == "offline") {
        return "停止";
      }
    },
  },
  props: {
    listsent: { typeof: Object },
  },
  data() {
    const validateNodes = (rule, value, callback) => {
      if (!value) {
        callback(new Error("请输入schema"));
      } else if (/\d/.test(value)) {
        callback(new Error("schemam名称不能有数字"));
      } else {
        callback();
      }
    };

    return {
      AccountList: [],
      tableKey: 0,
      list: null,
      listLoading: true,
      searchLoading: false,
      total: 0,
      listQuery: {
        pageNo: 1,
        pageSize: 10,
        hostaddr: "",
        id: "",
        status: "",
      },
      temp: {
        nodes: "",
        name: "",
        nick_name: "",
        machinelist: "",
        cluster_id: "",
        dsn: "",
        uid: "",
        schema: "",
      },
      cpu_paras: {
        hostaddr: "",
        port: "",
        type: "pg",
        cpu_cores: "5",
        cgroup_mode: "quota",
        user_name: "",
        nspacl: "",
        nspname: "",
        dsn: "",
        uid: "",
        schema: "",

      },
      cpu_paras_option: [
        {
          value: "share",
          label: "share",
        },
        {
          value: "quota",
          label: "quota",
        },
      ],
      dialogCpuVisible: false,
      dialogFormVisible: false,
      dialogEditVisible: false,
      dialogUploadVisible: false,
      dialogStatus: "",
      textMap: {
        create: "添加Schema",
        detail: "详情",
      },
      dialogDetail: false,
      dialogNodeVisible: false,
      message_tips: "",
      message_type: "",
      installStatus: false,
      info: "",
      row: {},
      compute_node_create_priv: JSON.parse(sessionStorage.getItem("priv"))
        .compute_node_create_priv,
      compute_node_drop_priv: JSON.parse(sessionStorage.getItem("priv"))
        .compute_node_drop_priv,
      storage_status: false,
      comp_status: false,
      comp_active: 1,
      job_id: null,
      timer: null,
      compmachines: [],
      computer: [],
      computer_state: "",
      finish_state: "",
      computer_title: "",
      computer_icon: "",
      comp_active: 0,
      init_title: "",
      init_show: true,
      finish_show: false,
      computer_show: true,
      shard_show: true,
      init_active: 0,
      finish_title: "",
      finish_icon: "",
      finish_description: "",
      computer_description: "",
      job_id: "",
      dialogStatusShowVisible: false,
      dialogStatusVisible: false,
      activities: [],
      rules: {
        schema: [{ required: true, trigger: "blur", validator: validateNodes }],
      },
    };
  },
  created() {
    this.listQuery.id = this.listsent.id;
    this.getAccount();
  },
  destroyed() {
    clearInterval(this.timer);
    this.timer = null;
  },
  methods: {
    getAccount() {
      const queryParam = Object.assign({}, this.listQuery)
      queryParam.user_name = sessionStorage.getItem('login_username')
      getAccountList(queryParam).then(response => {
        this.AccountList = response.list
        this.getList();
      })
    },
    nodeMonitor(row) {
      const pparas = {};
      pparas["cluster_id"] = row.db_cluster_id;
      pparas["hostaddr"] = row.hostaddr;
      pparas["port"] = row.port;
      pparas["user_name"] = sessionStorage.getItem("login_username");
      pgsqlDashboard(pparas).then((pyres) => {
        if (pyres.code == "200") {
          window.open(ip_arr[0].ip + pyres.url + "?orgId=1&refresh=5s");
        }
      });
    },
    handleSetCpu(row) {
      console.log(row);
      this.dialogCpuVisible = true;
      // 获取主节点
      this.cpu_paras = Object.assign(this.cpu_paras, row);
      console.log(this.cpu_paras);
    },
    beforeSyncDestory() {
      clearInterval(this.timer);
      this.dialogStatusVisible = false;
      this.timer = null;
    },
    // 清除定时器
    beforeDestory() {
      clearInterval(this.timer);
      this.dialogStatusShowVisible = false;
      this.timer = null;
    },
    handleFilter() {
      this.listQuery.pageNo = 1;
      this.getList();
    },
    handleClear() {
      this.listQuery.hostaddr = "";
      this.listQuery.status = "";
      this.listQuery.pageNo = 1;
      this.listQuery.id = this.listsent.id;
      this.getList();
    },
    getList() {
      this.listLoading = true;
      console.log(this.AccountList);
      let queryParam = {
        dsn: this.AccountList[0].pg_dsn,
        uid: parseInt(sessionStorage.getItem('zettadb_vue_uid'))
      }
      queryParam = Object.assign(queryParam, this.listQuery);

      // 模糊搜索
      computeList(queryParam).then((response) => {
        this.list = response.data;
        setTimeout(() => {
          this.listLoading = false;
        }, 0.5 * 1000);
      });
    },
    resetTemp() {
      this.temp = {
        nodes: "1",
        name: "",
        nick_name: "",
        machinelist: "",
        cluster_id: "",
      };
    },
    handleCreate() {
      this.resetTemp();
      this.dialogStatus = "create";
      this.dialogNodeVisible = true;
      this.temp.name = this.listsent.name;
      this.temp.nick_name = this.listsent.nick_name;
      this.temp.cluster_id = this.listsent.id;
      // 获取计算机器
      getCompMachine().then((res) => {
        this.compmachines = res.list;
      });
      this.$nextTick(() => {
        this.$refs.dataForm.clearValidate();
      });
    },
    SaveCpuSetData(row) {
      this.$refs["cpuForm"].validate((valid) => {
        if (valid) {
          const tempData = this.cpu_paras
          tempData.dsn = this.AccountList[0].pg_dsn
          tempData.uid = parseInt(sessionStorage.getItem('zettadb_vue_uid'))
          tempData.user_tag = this.AccountList[0].user_tag
          //tempData.user_name = JSON.stringify(this.cpu_paras.user_name)
          createSchemaRole(tempData).then((response) => {
            // console.log(response)
            if (response.code == 200) {
              this.message_type = "success";
              this.message_tips = "修改成功";
              messageTip(this.message_tips, this.message_type);
              this.getList();
              this.dialogNodeVisible = false;
            }
          });
        }
      });
    },
    createData() {
      this.$refs["dataForm"].validate((valid) => {
        if (valid) {
          const tempData = this.temp
          tempData.dsn = this.AccountList[0].pg_dsn
          tempData.uid = parseInt(sessionStorage.getItem('zettadb_vue_uid'))
          // console.log(tempData); return;
          // 新增schema
          addSchmea(tempData).then((response) => {
            // console.log(response)
            if (response.code == 200) {
              this.message_type = "success";
              this.message_tips = "修改成功";
              messageTip(this.message_tips, this.message_type);
              this.getList();
              this.dialogNodeVisible = false;
            }
          });
        }
      });
    },
    handleDetail(row) {
      this.dialogStatus = "detail";
      this.dialogFormVisible = true;
      this.temp = Object.assign({}, row);
      this.dialogDetail = true;
    },
    handleDelete(row) {
      // 先判断该集群是否只有一个schema，如果只有一个schema不予许删除
      const temp = { id: row.db_cluster_id };
      getCompsCount(temp).then((res) => {
        if (res.total == 1) {
          messageTip("该集群当前有且仅有一个schema,不能进行删除操作", "error");
        } else if (res.total > 1) {
          const code = createCode();
          const string =
            "此操作将永久删除schema" +
            row.hostaddr +
            "(" +
            row.port +
            "), 是否继续?code=" +
            code;
          gotoCofirm(string)
            .then((res) => {
              // 先执行删权限
              if (!res.value) {
                this.message_tips = "code不能为空！";
                this.message_type = "error";
                messageTip(this.message_tips, this.message_type);
              } else if (res.value == code) {
                const tempData = {};
                tempData.user_name = sessionStorage.getItem("login_username");
                tempData.job_id = "";
                tempData.job_type = "delete_comp";
                tempData.version = version_arr[0].ver;
                tempData.timestamp = timestamp_arr[0].time + "";
                const paras = {};
                paras.cluster_id = row.db_cluster_id;
                paras.nick_name = this.listsent.nick_name;
                paras.comp_id = row.id;
                tempData.paras = paras;
                // console.log(tempData);return;
                delComp(tempData).then((response) => {
                  const res = response;
                  if (res.status == "accept") {
                    this.dialogStatusShowVisible = true;
                    // 调获取状态接口
                    let i = 0;
                    this.computer = [];
                    this.computer_state = "";
                    this.computer_title = "";
                    this.computer_icon = "";
                    this.comp_active = 0;
                    this.init_title = "";
                    this.init_active = 0;
                    this.finish_title = "";
                    this.finish_icon = "";
                    this.finish_description = "";
                    this.computer_description = "";
                    this.job_id = "";
                    this.timer = null;
                    const info = "删除schema";
                    this.getFStatus(this.timer, res.job_id, i++, info, "");
                    this.timer = setInterval(() => {
                      this.getFStatus(this.timer, res.job_id, i++, info, "");
                    }, 1000);
                  } else {
                    this.message_tips = res.error_info;
                    this.message_type = "error";
                    messageTip(this.message_tips, this.message_type);
                  }
                });
              } else {
                this.message_tips = "code输入有误";
                this.message_type = "error";
                messageTip(this.message_tips, this.message_type);
              }
            })
            .catch(() => {
              console.log("quxiao");
              messageTip("已取消删除", "info");
            });
        }
      });
    },
    handleControlNode(row, action) {
      console.log(row)
      let action_name = "删除";
      const code = createCode();
      const string = "将对该schema " + row.nspname + " 进行删除操作,是否继续?code=" + code;
      gotoCofirm(string)
        .then((res) => {
          // 先执行删权限
          if (!res.value) {
            this.message_tips = "code不能为空！";
            this.message_type = "error";
            messageTip(this.message_tips, this.message_type);
          } else if (res.value == code) {
            let tempData = {}
            tempData = Object.assign(tempData, row);
            tempData.dsn = this.AccountList[0].pg_dsn
            tempData.uid = parseInt(sessionStorage.getItem('zettadb_vue_uid'))
            // console.log(tempData); return;
            // 新增schema
            deleteSchmea(tempData).then((response) => {
              // console.log(response)
              if (response.code == 200) {
                this.message_type = "success";
                this.message_tips = "修改成功";
                messageTip(this.message_tips, this.message_type);
                this.getList();
                this.dialogNodeVisible = false;
              }
            });
          } else {
            this.message_tips = "code输入有误";
            this.message_type = "error";
            messageTip(this.message_tips, this.message_type);
          }
        }).catch((e) => {
          console.log(e);
          messageTip("已取消删除", "info");
        });
    },
    getFStatus(timer, data, i, info) {
      setTimeout(() => {
        const postarr = {};
        postarr.job_type = "get_status";
        postarr.version = version_arr[0].ver;
        postarr.job_id = data;
        postarr.timestamp = timestamp_arr[0].time + "";
        postarr.paras = {};
        this.job_id = "任务ID:" + data;
        getEvStatus(postarr).then((ress) => {
          // 新增schema
          if (ress.attachment !== null) {
            this.shard_show = false;
            this.init_show = false;
            this.computer_show = true;
            // 计算
            if (ress.attachment.hasOwnProperty("computer_state")) {
              if (ress.attachment.computer_state == "ongoing") {
                this.computer_state = "process";
                this.computer_icon = "el-icon-loading";
                this.computer_title = "正在" + info;
              } else if (ress.attachment.computer_state == "done") {
                this.computer_state = "success";
                this.computer_icon = "el-icon-circle-check";
                this.computer_title = info + "成功";
                // 遍历schema改状态
                if (this.computer.length > 0) {
                  for (let c = 0; c < this.computer.length; c++) {
                    const arr = ress.attachment.computer_hosts.substr(
                      0,
                      ress.attachment.computer_hosts.length - 1
                    );
                    const computer_hosts = arr.split(";");
                    for (let e = 0; e < computer_hosts.length; e++) {
                      if (this.computer[c].title == computer_hosts[e]) {
                        this.computer[c].icon = "el-icon-circle-check";
                        this.computer[c].status = "success";
                      }
                    }
                  }
                }
              } else if (ress.attachment.computer_state == "failed") {
                this.computer_state = "error";
                this.computer_icon = "el-icon-circle-close";
                this.computer_title = info + "失败";
                // 遍历schema改状态
                if (this.computer.length > 0) {
                  for (let c = 0; c < this.computer.length; c++) {
                    const arr = ress.attachment.computer_hosts.substr(
                      0,
                      ress.attachment.computer_hosts.length - 1
                    );
                    const computer_hosts = arr.split(";");
                    for (let e = 0; e < computer_hosts.length; e++) {
                      if (this.computer[c].title == computer_hosts[e]) {
                        this.computer[c].icon = "el-icon-circle-close";
                        this.computer[c].status = "error";
                      }
                    }
                  }
                  this.computer_description = ress.error_info;
                }
              } else {
                this.computer_state = "process";
                this.computer_icon = "el-icon-loading";
                this.computer_title = "正在" + info;
              }
            }
            this.init_title = "正在" + info;
            this.init_active = 1;
            if (this.computer.length == 0) {
              if (ress.status == "ongoing") {
                if (ress.attachment.hasOwnProperty("computer_hosts")) {
                  const arr = ress.attachment.computer_hosts.substr(
                    0,
                    ress.attachment.computer_hosts.length - 1
                  );
                  const computer_hosts = arr.split(";");
                  if (ress.attachment.computer_state == "done") {
                    for (let e = 0; e < computer_hosts.length; e++) {
                      const newArrgoing = {};
                      newArrgoing.title = computer_hosts[e];
                      newArrgoing.icon = "el-icon-circle-check";
                      newArrgoing.status = "success";
                      newArrgoing.description = "";
                      newArrgoing.computer_id = ress.attachment.computer_id;
                      this.computer.push(newArrgoing);
                    }
                  } else {
                    for (let e = 0; e < computer_hosts.length; e++) {
                      const newArrgoing = {};
                      newArrgoing.title = computer_hosts[e];
                      newArrgoing.icon = "el-icon-loading";
                      newArrgoing.status = "process";
                      newArrgoing.description = "";
                      newArrgoing.computer_id = ress.attachment.computer_id;
                      this.computer.push(newArrgoing);
                    }
                  }
                }
              } else if (ress.status == "failed") {
                this.computer_state = "error";
                this.computer_icon = "el-icon-circle-close";
                this.computer_title = info + "失败";

                this.finish_title = info + "失败";
                this.finish_icon = "el-icon-circle-close";
                this.finish_state = "error";
                this.init_active = 1;
                this.finish_description = ress.error_info;
                // 遍历schema改状态
                if (this.computer.length > 0) {
                  for (let c = 0; c < this.computer.length; c++) {
                    if (ress.attachment.hasOwnProperty("computer_hosts")) {
                      const arr = ress.attachment.computer_hosts.substr(
                        0,
                        ress.attachment.computer_hosts.length - 1
                      );
                      const computer_hosts = arr.split(";");
                      for (let e = 0; e < computer_hosts.length; e++) {
                        if (this.computer[c].title == computer_hosts[e]) {
                          this.computer[c].icon = "el-icon-circle-close";
                          this.computer[c].status = "error";
                        }
                      }
                    }
                  }
                  this.computer_description = ress.error_info;
                }
                clearInterval(timer);
              } else if (ress.status == "done") {
                this.computer_state = "success";
                this.computer_icon = "el-icon-circle-check";
                this.computer_title = info + "成功";

                this.finish_title = info + "成功";
                this.finish_icon = "el-icon-circle-check";
                this.finish_state = "success";
                this.init_active = 1;
                this.finish_description = ress.error_info;
                // 遍历schema改状态
                if (this.computer.length > 0) {
                  for (let c = 0; c < this.computer.length; c++) {
                    if (ress.attachment.hasOwnProperty("computer_hosts")) {
                      const arr = ress.attachment.computer_hosts.substr(
                        0,
                        ress.attachment.computer_hosts.length - 1
                      );
                      const computer_hosts = arr.split(";");
                      for (let e = 0; e < computer_hosts.length; e++) {
                        if (this.computer[c].title == computer_hosts[e]) {
                          this.computer[c].icon = "el-icon-circle-check";
                          this.computer[c].status = "success";
                        }
                      }
                    }
                  }
                }
                clearInterval(timer);
                this.getList();
              }
            } else {
              if (ress.status == "ongoing") {
                if (ress.attachment.hasOwnProperty("computer_state")) {
                  if (ress.attachment.computer_state == "done") {
                    this.computer_state = "success";
                    this.computer_icon = "el-icon-circle-check";
                    this.computer_title = info + "成功";
                    // 遍历schema改状态
                    if (this.computer.length > 0) {
                      for (let c = 0; c < this.computer.length; c++) {
                        const arr = ress.attachment.computer_hosts.substr(
                          0,
                          ress.attachment.computer_hosts.length - 1
                        );
                        const computer_hosts = arr.split(";");
                        for (let e = 0; e < computer_hosts.length; e++) {
                          if (this.computer[c].title == computer_hosts[e]) {
                            this.computer[c].icon = "el-icon-circle-check";
                            this.computer[c].status = "success";
                          }
                        }
                      }
                    }
                  } else if (ress.attachment.computer_state == "failed") {
                    this.computer_state = "error";
                    this.computer_icon = "el-icon-circle-close";
                    this.computer_title = info + "失败";
                    // 遍历schema改状态
                    if (this.computer.length > 0) {
                      for (let c = 0; c < this.computer.length; c++) {
                        const arr = ress.attachment.computer_hosts.substr(
                          0,
                          ress.attachment.computer_hosts.length - 1
                        );
                        const computer_hosts = arr.split(";");
                        for (let e = 0; e < computer_hosts.length; e++) {
                          if (this.computer[c].title == computer_hosts[e]) {
                            this.computer[c].icon = "el-icon-circle-close";
                            this.computer[c].status = "error";
                          }
                        }
                      }
                      this.computer_description = ress.error_info;
                    }
                  } else {
                    this.computer_state = "process";
                    this.computer_icon = "el-icon-loading";
                    this.computer_title = "正在" + info;
                  }
                }
              } else if (ress.status == "failed") {
                this.computer_state = "error";
                this.computer_icon = "el-icon-circle-close";
                this.computer_title = info + "失败";

                this.finish_title = info + "失败";
                this.finish_icon = "el-icon-circle-close";
                this.finish_state = "error";
                this.init_active = 1;
                this.finish_description = ress.error_info;
                // 遍历schema改状态
                if (this.computer.length > 0) {
                  for (let c = 0; c < this.computer.length; c++) {
                    if (ress.attachment.hasOwnProperty("computer_hosts")) {
                      const arr = ress.attachment.computer_hosts.substr(
                        0,
                        ress.attachment.computer_hosts.length - 1
                      );
                      const computer_hosts = arr.split(";");
                      for (let e = 0; e < computer_hosts.length; e++) {
                        if (this.computer[c].title == computer_hosts[e]) {
                          this.computer[c].icon = "el-icon-circle-close";
                          this.computer[c].status = "error";
                        }
                      }
                    }
                  }
                  this.computer_description = ress.error_info;
                }
                clearInterval(timer);
              } else if (ress.status == "done") {
                this.computer_state = "success";
                this.computer_icon = "el-icon-circle-check";
                this.computer_title = info + "成功";

                this.finish_title = info + "成功";
                this.finish_icon = "el-icon-circle-check";
                this.finish_state = "success";
                this.init_active = 1;
                this.finish_description = ress.error_info;
                // 遍历schema改状态
                if (this.computer.length > 0) {
                  for (let c = 0; c < this.computer.length; c++) {
                    if (ress.attachment.hasOwnProperty("computer_hosts")) {
                      const arr = ress.attachment.computer_hosts.substr(
                        0,
                        ress.attachment.computer_hosts.length - 1
                      );
                      const computer_hosts = arr.split(";");
                      for (let e = 0; e < computer_hosts.length; e++) {
                        if (this.computer[c].title == computer_hosts[e]) {
                          this.computer[c].icon = "el-icon-circle-check";
                          this.computer[c].status = "success";
                        }
                      }
                    }
                  }
                }
                clearInterval(timer);
                this.getList();
              }
            }
          } else if (
            ress.attachment == null &&
            ress.error_code == "70001" &&
            ress.status == "failed"
          ) {
            this.shard_show = false;
            this.init_show = false;
            this.computer_show = true;
            this.computer_state = "process";
            this.computer_icon = "el-icon-loading";
            this.computer_title = "正在" + info;
            if (i > 5) {
              this.computer_state = "error";
              this.computer_icon = "el-icon-circle-close";
              this.computer_title = info + "失败";
              clearInterval(timer);
            }
          } else if (ress.attachment == null && ress.status == "ongoing") {
            this.shard_show = false;
            this.init_show = false;
            this.computer_show = true;
            this.computer_state = "process";
            this.computer_icon = "el-icon-loading";
            this.computer_title = "正在" + info;
          } else if (ress.attachment == null && ress.status == "done") {
            this.shard_show = false;
            this.init_show = false;
            this.computer_show = true;
            this.computer_state = "success";
            this.computer_icon = "el-icon-circle-check";
            this.computer_title = info + "成功";
            clearInterval(timer);
          } else {
            this.shard_show = false;
            this.init_show = false;
            this.computer_show = true;
            this.computer_state = "error";
            this.computer_icon = "el-icon-circle-close";
            this.computer_title = info + "失败";
            clearInterval(timer);
          }
        });
        if (i >= 86400) {
          clearInterval(timer);
        }
      }, 0);
    },
    getStatus(timer, data, i, action_name) {
      setTimeout(() => {
        const postarr = {};
        postarr.job_type = "get_status";
        postarr.version = version_arr[0].ver;
        postarr.job_id = data;
        postarr.timestamp = timestamp_arr[0].time + "";
        postarr.paras = {};
        getEvStatus(postarr).then((res) => {
          if (res.status == "done" || res.status == "failed") {
            // clearInterval(timer);
            // this.info=res.error_info;
            if (res.status == "done") {
              const newArrdone = {
                content: action_name + "成功",
                timestamp: getNowDate(),
                color: "#0bbd87",
                icon: "el-icon-circle-check",
              };
              this.activities.push(newArrdone);
              this.getList();
              // this.dialogStatusVisible=false;
              clearInterval(timer);
            } else {
              if (
                res.attachment == null &&
                res.error_code == "70001" &&
                res.status == "failed"
              ) {
                if (i > 5) {
                  const newArr = {
                    content: res.error_info,
                    timestamp: getNowDate(),
                    color: "red",
                    icon: "el-icon-circle-close",
                  };
                  this.activities.push(newArr);
                  clearInterval(timer);
                }
              } else {
                const newArr = {
                  content: res.error_info,
                  timestamp: getNowDate(),
                  color: "red",
                  icon: "el-icon-circle-close",
                };
                this.activities.push(newArr);
                clearInterval(timer);
              }
              // this.installStatus = true;
            }
          }
          // else{
          //    const newArrgoing={
          //     content:res.error_info,
          //     timestamp: getNowDate(),
          //     color: '#0bbd87'
          //   };
          //   this.activities.push(newArrgoing)
          //   this.info=res.error_info;
          //   this.installStatus = true;
          // }
        });
        if (i >= 86400) {
          clearInterval(timer);
        }
      }, 0);
    },
  },
};
</script>
<style>
.right_input_min {
  width: 25%;
}
</style>
<style scoped>
.el-input {
  width: inherit;
}
</style>
<style lang="postcss">
.box {
  width: 100%;
  /* height: 22px; */
  display: flex;
  flex-direction: row;
}

.box>div {
  /* height: 22px; */
  flex: 1;
}
</style>
