<template>
  <div class="app-container">
    <el-form ref="form" :model="form" :rules="rules" label-width="140px">
      <el-form-item label="原表集群:" prop="nick_name">
        <span>{{form.name}}</span>
      </el-form-item>

      <el-form-item label="目标表集群:" prop="old_cluster_id">
        <el-select v-model="form.dst_cluster_id" clearable placeholder="请选择目标表集群" style="width:100%;">
          <el-option
            v-for="item in oldClusterList"
            :key="item.id"
            :label="item.name"
            :value="item.id">
          </el-option>
        </el-select>
      </el-form-item>
      <el-form-item>
        <el-button type="primary" @click="onSubmit(form)">提交</el-button>
        <!-- <el-button>取消</el-button> -->
      </el-form-item>
    </el-form>
  </div>
</template>

<script>
  import {getClusterList, tableRepartition} from '@/api/cluster/list'
  import {messageTip, handleCofirm, getNowDate} from "@/utils";
  import {version_arr, timestamp_arr} from "@/utils/global_variable"

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
          repartition_tables: '',
          name: '',
          nick_name: '',
          id: '',
        },
        oldClusterList:[],
        rules: {
          dst_cluster_id: [
            {required: true, trigger: "blur"}
          ],
        }
      }
    },
    mounted() {
      this.form.src_cluster_id = this.listsent.id;
      this.form.name = this.listsent.name;
      this.form.nick_name = this.listsent.nick_name;
      let temp = {cluster_id: this.listsent.id}
      //获取原集群名称
      getClusterList(temp).then((res) => {
        this.oldClusterList = res.list;
      });
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
      beforeRestoreDestory() {
        clearInterval(this.timer)
        this.dialogStatusVisible = false;
        this.timer = null;
      },
      onSubmit(row) {
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
