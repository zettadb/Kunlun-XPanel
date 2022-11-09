<template>
  <div class="app-container">
    <el-form ref="form" :model="form" id="form" :rules="rules" label-width="140px">
      <el-form-item label="原表集群:" prop="nick_name">
        <span>{{form.name}}</span>
      </el-form-item>

      <el-form-item label="目标表集群:" prop="dst_cluster_id">
        <el-select
          v-model="form.dst_cluster_id"
          @change="onDstClusterChange"
          clearable
          placeholder="请选择目标表集群"
          style="width:100%;"
        >
          <el-option
            v-for="item in oldClusterList"
            :key="item.id"
            :label="item.name"
            :value="item.id">
          </el-option>
        </el-select>
      </el-form-item>
      <el-row v-for="(table, index) in form.repartition_tables"
              :key="table.key">
        <el-col :span="10">
          <el-form-item label="源表:" :prop="'repartition_tables.' + index + '.srcTable'">
            <el-cascader
              style="width: 100%"
              v-model="form.repartition_tables[index].srcTable"
              clearable
              placeholder="请选择 库名/模式/表"
              :options="srcTableOptions"
              filterable></el-cascader>
          </el-form-item>
        </el-col>
        <el-col :span="10">
          <el-form-item label="目标表:" :prop="'repartition_tables.' + index + '.ditTable'">
            <el-cascader
              style="width: 100%"
              clearable
              v-model="form.repartition_tables[index].ditTable"
              placeholder="请选择 库名/模式/表"
              :options="ditTableOptions"
              filterable></el-cascader>
          </el-form-item>
        </el-col>
        <el-col :span="4" style="margin-top: 4px;">
          <div style="margin-left: 3px">
            <el-button icon="el-icon-plus" @click="onPush(index)" size="small"></el-button>
            <el-button icon="el-icon-minus" @click="onRemove(index)" size="small"></el-button>
          </div>
        </el-col>
      </el-row>
      <el-form-item>
        <el-button type="primary" @click="onSubmit(form)">提交</el-button>
        <!-- <el-button>取消</el-button> -->
      </el-form-item>
    </el-form>
  </div>
</template>

<script>
  import {getClusterList, tableRepartition, getPGTableList} from '@/api/cluster/list'
  import {messageTip, handleCofirm, getNowDate} from "@/utils";
  import {version_arr, timestamp_arr} from "@/utils/global_variable"
  import {Loading} from "element-ui";

  export default {
    name: "table_repartition",
    props: {
      listsent: {typeof: Object}
    },
    data() {
      return {
        form: {
          src_cluster_id: '',
          dst_cluster_id: '',
          repartition_tables: [
            {
              srcTable: [],
              ditTable: [],
            }
          ],
          name: '',
          nick_name: '',
          id: '',
        },
        oldClusterList: [],
        srcTable: [],
        srcTableOptions: [],
        ditTableOptions: [],
        rules: {
          dst_cluster_id: [
            {required: true, trigger: "blur", message: '目标集群不能为空'}
          ],
        }
      }
    },
    mounted() {
      this.form.src_cluster_id = this.listsent.id;
      this.form.name = this.listsent.name;
      this.form.nick_name = this.listsent.nick_name;
      let temp = {cluster_id: this.listsent.id}
      let username = sessionStorage.getItem('login_username');
      //获取原集群名称
      getClusterList(temp).then((res) => {
        this.oldClusterList = res.list;
      });
      getPGTableList({name: username, cluster_id: this.form.src_cluster_id}).then(res => {
        this.srcTableOptions = res.list;
      })
      this.$nextTick(() => {
        if (this.$refs["form"]) {
          this.$refs["form"].clearValidate();
        }
      });
    },
    destroyed() {
      clearInterval(this.timer)
      this.timer = null
    },
    methods: {
      onPush(index) {
        this.form.repartition_tables.push({
          srcTable: [],
          ditTable: [],
        })
      },
      onRemove(index) {
        this.form.repartition_tables.splice(index, 1)
      },
      onDstClusterChange(val) {
        if (val === '') {
          this.ditTableOptions = []
        }
        let loading = Loading.service({target: 'form'});
        getPGTableList({cluster_id: val}).then(res => {
          this.ditTableOptions = res.list;
        }).finally(function () {
          loading.close()
        })
      },
      beforeRestoreDestory() {
        clearInterval(this.timer)
        this.dialogStatusVisible = false;
        this.timer = null;
      },
      onSubmit(row) {
        console.info(this.srcTable)
        this.$refs["form"].validate((valid) => {
          if (valid) {
            //const tempData = Object.assign({}, this.row);
            const restoreData = {};
            restoreData.user_name = sessionStorage.getItem('login_username');
            restoreData.job_id = '';
            restoreData.version = version_arr[0].ver;
            restoreData.job_type = 'cluster_restore';
            restoreData.timestamp = timestamp_arr[0].time + '';
            const paras = {}
            paras.src_cluster_id = row.old_cluster_id;
            paras.dst_cluster_id = row.id;
            paras.restore_time = row.retreated_time;
            restoreData.paras = paras;
          }
        })
      },
    }
  };
</script>
