<template>
  <div class="app-container">
    <el-form ref="form" :model="form" id="form" :rules="rules" label-width="140px">
      <el-form-item label="集群名称">
        <span>{{form.name}}</span>
      </el-form-item>
      <el-form-item label="业务名称">
        <span>{{form.nick_name}}</span>
      </el-form-item>
      <el-row v-for="(table, index) in form.backup"
              :key="table.key">
        <el-col :span="10">
          <el-form-item label="备份表:" :prop="'backup.' + index + '.db_table'"
                        :rules="{
                                    required: true, message: '备份表不能为空', trigger: 'blur'}"
          >
            <el-cascader
              :key="'srcTable' + index"
              style="width: 100%"
              v-model="form.backup[index].db_table"
              clearable
              placeholder="请选择 库名/模式/表"
              :options="tableOptions"
              filterable></el-cascader>
          </el-form-item>
        </el-col>
        <el-col :span="10">
          <el-form-item label="备份时间范围:" :prop="'backup.' + index" :rules="rules.time"
          >
            <el-time-select
              style="width: 48%"
              :key="'startTime' + index"
              placeholder="起始时间"
              :arrow-control="true"
              v-model="form.backup[index].startTime"
              :picker-options="{
              start: '00:00',
              step: '00:30',
              end: '24:00'
    }">
            </el-time-select>
            <span>-</span>
            <el-time-select
              style="width: 48%"
              :key="'endTime' + index"
              placeholder="结束时间"
              v-model="form.backup[index].endTime"
              :picker-options="{
              start: '00:00',
              step: '00:30',
              end: '24:00',
            minTime: form.backup[index].startTime
    }">
            </el-time-select>
          </el-form-item>
        </el-col>
        <el-col :span="4" style="margin-top: 4px;">
          <div style="margin-left: 3px">
            <el-button icon="el-icon-plus" v-if="index === 0" @click="onPush(index)" size="small"></el-button>
            <el-button icon="el-icon-minus" v-else @click="onRemove(index)" size="small"></el-button>
          </div>
        </el-col>
      </el-row>
      <el-form-item>
        <el-button type="primary" @click="onSubmit(form)">保存</el-button>
        <!-- <el-button>取消</el-button> -->
      </el-form-item>
    </el-form>
  </div>
</template>

<script>
  import {getPGTableList, tableRepartition} from "@/api/cluster/list";
  import {timestamp_arr, version_arr} from "@/utils/global_variable";

  export default {
    name: "logical_backup",
    props: {
      listsent: {typeof: Object}
    },
    data() {
      const checkTimeRange = (rule, value, cb) => {
        if (value.startTime === '' || value.endTime === '') {
          return cb(new Error('请选择备份时间范围'))
        }

        cb()
      }
      return {
        form: {
          name: '',
          nick_name: '',
          backup: [
            {db_table: [], backup_time: '', startTime: '', endTime: ''}
          ],
          id: ''
        },
        rules: {
          time: [
            {validator: checkTimeRange, trigger: 'blur'},
          ],
        },
        tableOptions: [],
      }
    },
    mounted() {
      this.form.name = this.listsent.name;
      this.form.nick_name = this.listsent.nick_name;
      this.form.id = this.listsent.id;
      getPGTableList({cluster_id: this.form.id, all: '1'}).then(res => {
        this.tableOptions = res.list;
      })
    },
    methods: {
      onPush(index) {
        this.form.backup.push({db_table: [], backup_time: '', startTime: '', endTime: ''})
      },
      onRemove(index) {
        this.form.backup.splice(index, 1)
      },
      onSubmit(row) {
        console.info(this.form)

        this.$refs["form"].validate((valid) => {
          if (valid) {
            const data = {};
            data.user_name = sessionStorage.getItem('login_username');
            data.job_id = '';
            data.version = version_arr[0].ver;
            data.job_type = 'logical_backup';
            data.timestamp = timestamp_arr[0].time + '';
            const paras = {}
            let backup = this.form.backup.map(v => {
              return {
                db_table: v.db_table[0] + '_$$_' + v.db_table[1] + '.' + v.db_table[2],
                backup_time: v.startTime + ':00' + '-' + v.endTime + ':00'
              }
            })
            paras.cluster_id = this.form.id;
            paras.backup = backup;
            data.paras = paras
            console.info(data)
            tableRepartition(data).then(res => {
              if (res.code === 200) {
                this.$message.info('保存')
                this.form.backup = [
                  {db_table: [], backup_time: '', startTime: '', endTime: ''}
                ]
              }

            })
          }
        })
      },
    }
  }
</script>
