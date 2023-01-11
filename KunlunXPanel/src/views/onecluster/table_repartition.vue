<template>
  <div class="app-container">
    <el-form id="form" ref="form" :model="form" :rules="rules" label-width="140px">
      <el-form-item label="原表集群:" prop="nick_name">
        <span>{{ form.name }}</span>
      </el-form-item>

      <el-form-item label="目标表集群:" prop="dst_cluster_id">
        <el-select
          v-model="form.dst_cluster_id"
          clearable
          placeholder="请选择目标表集群"
          style="width: 100%"
          @change="onDstClusterChange"
        >
          <el-option
            v-for="item in clusterOptions"
            :key="item.id"
            :label="item.name"
            :value="item.id"
          />
        </el-select>
      </el-form-item>
      <el-row v-for="(table, index) in form.repartition_tables" :key="table.key">
        <el-col :span="10">
          <el-form-item
            label="源表:"
            :prop="'repartition_tables.' + index + '.srcTable'"
            :rules="{
              required: true,
              message: '源表不能为空',
              trigger: 'blur',
            }"
          >
            <el-cascader
              :key="'srcTable' + index"
              v-model="form.repartition_tables[index].srcTable"
              style="width: 100%"
              clearable
              placeholder="请选择 库名/模式/表"
              :options="srcTableOptions"
              filterable
            />
          </el-form-item>
        </el-col>
        <el-col :span="10">
          <el-form-item
            label="目标表:"
            :prop="'repartition_tables.' + index + '.ditTable'"
            :rules="{
              required: true,
              message: '目标表不能为空',
              trigger: 'blur',
            }"
          >
            <el-cascader
              :key="'ditTable' + index"
              v-model="form.repartition_tables[index].ditTable"
              style="width: 100%"
              clearable
              placeholder="请选择 库名/模式/表"
              :options="ditTableOptions"
              filterable
            />
          </el-form-item>
        </el-col>
      </el-row>
      <el-form-item>
        <el-button type="primary" @click="onSubmit(form)">提交</el-button>
        <!-- <el-button>取消</el-button> -->
      </el-form-item>
    </el-form>

    <el-dialog
      :visible.sync="dialogStatusVisible"
      custom-class="single_dal_view"
      width="400px"
      :close-on-click-modal="false"
      :before-close="beforeRestoreDestory"
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
  </div>
</template>

<script>
import {
  tableRepartition,
  getPGTableList,
  getEvStatus,
  clusterOptions,
} from "@/api/cluster/list";
import { messageTip, handleCofirm, getNowDate } from "@/utils";
import { version_arr, timestamp_arr } from "@/utils/global_variable";
import { Loading } from "element-ui";

export default {
  name: "TableRepartition",
  props: {
    listsent: { typeof: Object },
  },
  data() {
    return {
      form: {
        src_cluster_id: "",
        dst_cluster_id: "",
        repartition_tables: [
          {
            srcTable: [],
            ditTable: [],
          },
        ],
        name: "",
        nick_name: "",
        id: "",
      },

      dialogStatusVisible: false,
      activities: [],
      clusterOptions: [],
      srcTable: [],
      srcTableOptions: [],
      ditTableOptions: [],
      rules: {
        dst_cluster_id: [
          { required: true, trigger: "blur", message: "目标集群不能为空" },
        ],
      },
    };
  },
  mounted() {
    this.form.src_cluster_id = this.listsent.id;
    this.form.name = this.listsent.name;
    this.form.nick_name = this.listsent.nick_name;
    const username = sessionStorage.getItem("login_username");
    // 获取原集群名称
    clusterOptions({}).then((res) => {
      this.clusterOptions = res.list;
    });
    getPGTableList({ name: username, cluster_id: this.form.src_cluster_id }).then(
      (res) => {
        this.srcTableOptions = res.list;
      }
    );
    this.$nextTick(() => {
      if (this.$refs["form"]) {
        this.$refs["form"].clearValidate();
      }
    });
  },

  destroyed() {
    clearInterval(this.timer);
    this.timer = null;
  },
  methods: {
    onPush(index) {
      this.form.repartition_tables.push({
        srcTable: [],
        ditTable: [],
      });
    },
    onRemove(index) {
      this.form.repartition_tables.splice(index, 1);
    },
    onDstClusterChange(val) {
      if (val === "") {
        this.ditTableOptions = [];
      } else {
        const loading = Loading.service({ target: "form" });
        getPGTableList({ cluster_id: val })
          .then((res) => {
            this.ditTableOptions = res.list;
          })
          .finally(function () {
            loading.close();
          });
      }

      this.form.repartition_tables.forEach((v, i) => {
        this.form.repartition_tables[i].ditTable = [];
      });
    },
    beforeRestoreDestory() {
      clearInterval(this.timer);
      this.dialogStatusVisible = false;
      this.timer = null;
    },
    onSubmit(row) {
      console.info(this.srcTable);
      this.$refs["form"].validate((valid) => {
        if (valid) {
          // const tempData = Object.assign({}, this.row);
          const data = {};
          data.user_name = sessionStorage.getItem("login_username");
          data.job_id = "";
          data.version = version_arr[0].ver;
          data.job_type = "table_repartition";
          data.timestamp = timestamp_arr[0].time + "";
          const paras = {};
          const repartition_tables = this.form.repartition_tables
            .map((v) => {
              return (
                v.srcTable[0] +
                "_$$_public." +
                v.srcTable[1] +
                "=>" +
                v.ditTable[0] +
                "_$$_public." +
                v.ditTable[1]
              );
            })
            .join(",");
          paras.src_cluster_id = this.form.src_cluster_id;
          paras.dst_cluster_id = this.form.dst_cluster_id;
          paras.repartition_tables = repartition_tables;
          data.paras = paras;
          tableRepartition(data).then((res) => {
            if (res.code === 200) {
              console.log(res);
              this.$message.info("提交成功");
              this.$refs["form"].resetFields();
            }

            if (res.status === "accept") {
              this.dialogRetreatedVisible = false;
              this.dialogStatusVisible = true;
              this.activities = [];
              const newArr = {
                content: "表重分中...",
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
    getStatus(timer, data, i) {
      setTimeout(() => {
        const postarr = {};
        postarr.job_type = "get_status";
        postarr.version = version_arr[0].ver;
        postarr.job_id = data;
        postarr.timestamp = timestamp_arr[0].time + "";
        postarr.paras = {};
        getEvStatus(postarr).then((res) => {
          const error_info = res.error_info;
          if (res.status === "done" || res.status === "failed") {
            clearInterval(timer);
            if (res.status === "done") {
              const newArrdone = {
                content: "表重分成功",
                timestamp: getNowDate(),
                color: "#0bbd87",
                icon: "el-icon-circle-check",
              };
              this.activities.push(newArrdone);

              this.getList();
            } else {
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
