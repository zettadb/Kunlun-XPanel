<template>
  <div class="app-container">
    <div class="filter-container">
      
      <div class="table-list-wrap" v-if="user_grant==='Y'?true:false">
        <el-button
          class="filter-item"
          type="primary"
          icon="el-icon-plus"
          @click="handleCreate"
        >新增</el-button>
      </div>
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

      <el-table-column label="用户账户" align="center">
        <template slot-scope="{row}">
          <span class="link-type" @click="handleDetail(row)">{{ row.username }}</span>
        </template>
      </el-table-column>
      <el-table-column 
        label="角色名称"
        align="center"
        prop="role_name"
        >
      </el-table-column>

       <el-table-column
            prop="valid_period"
            align="center"
            label="有效期类型"
            :formatter='handleEffectTime'>
      </el-table-column>

      <el-table-column
            prop="start_ts"
            align="center"
            label="开始时间"
             :formatter='handleStartTime'>
      </el-table-column>

      <el-table-column
            prop="end_ts"
            align="center"
            label="结束时间"
            :formatter='handleEndTime'>
      </el-table-column>

      <el-table-column
            prop="update_time"
            align="center"
            label="更新时间">
      </el-table-column>
      <el-table-column
        label="操作"
        align="center"
        width="230"
        class-name="small-padding fixed-width"
        v-if="user_grant==='Y'"
      >
        <template slot-scope="{row,$index}">
          <el-button type="primary" size="mini" @click="handleUpdate(row)" v-show="user_grant==='Y'&&row.username!=='super_dba'">编辑</el-button>
          <el-button size="mini" type="danger" @click="handleDelete(row,$index)" v-show="user_grant==='Y'&&row.username!=='super_dba'">删除</el-button>
        </template>
      </el-table-column>
    </el-table>

    <pagination v-show="total>0" :total="total" :page.sync="listQuery.pageNo" :limit.sync="listQuery.pageSize" @pagination="getList" />

    <el-dialog :title="textMap[dialogStatus]" :visible.sync="dialogFormVisible" custom-class="single_dal_view">
      <el-form 
        ref="dataForm"
        :rules="rules"
        :model="temp"
        label-position="left"
        label-width="155px"
      >
       <el-form-item label="用户账户:" prop="username">
        <el-select v-model="temp.username" placeholder="请选择用户账户" :disabled="dialogStatus==='detail'|| dialogStatus==='update'">
        <el-option
          v-for="item in userData"
          :key="item.id"
          :label="item.name"
          :value="item.id">
        </el-option>
      </el-select>
      </el-form-item>

      <el-form-item label="角色名称:" prop="role_name" >
        <el-select v-model="temp.role_name" placeholder="请选择用户角色" :disabled="dialogStatus==='detail'|| dialogStatus==='update'">
        <el-option
          v-for="item in rolesData"
          :key="item.id"
          :label="item.name"
          :value="item.id">
        </el-option>
      </el-select>
      </el-form-item>
      <el-form-item label="有效期类型:" prop="valid_period"  :disabled="dialogStatus==='detail'">
        <el-select v-model="temp.valid_period" placeholder="请选择有效期类型" :disabled="dialogStatus==='detail'">
          <el-option
            v-for="item in valid_period_data"
            :key="item.value"
            :label="item.label"
            :value="item.value">
          </el-option>
        </el-select>
      </el-form-item>

      <div v-if="temp.valid_period=='from_to'" :disabled="dialogStatus==='detail'">
      <el-form-item label="起止时间:" prop="beginDate">
        <el-date-picker v-model="temp.start_ts" type="datetime" value-format="yyyy-MM-dd HH:mm:ss"  placeholder="请选择开始时间" :disabled="dialogStatus==='detail'"></el-date-picker>
      至
      <el-date-picker v-model="temp.end_ts" type="datetime" value-format="yyyy-MM-dd HH:mm:ss" placeholder="请选择结束时间" :disabled="dialogStatus==='detail'"></el-date-picker>
      </el-form-item>
      </div>

      <el-form-item label="是否应用于所有集群:" prop="radio" :disabled="dialogStatus==='detail'">
        <el-radio v-model="temp.radio" label="1" :disabled="dialogStatus==='detail'">是</el-radio>
        <el-radio v-model="temp.radio" label="2" :disabled="dialogStatus==='detail'">否</el-radio>
      </el-form-item>
      <div v-if="temp.radio=='2'">
      <el-checkbox-group 
      v-model="checkedCluster"
      :min="minCluster"
      :max="clusterTotal" prop="checkedCluster">
      <el-checkbox v-for="cluster in clusters" :label="cluster.id" :key="cluster.id">{{cluster.name}}</el-checkbox>
    </el-checkbox-group>
      </div>
        
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="dialogFormVisible = false" v-show="!dialogDetail">关闭</el-button>
        <el-button type="primary" @click="dialogStatus==='create'?createData():updateData(row)" v-show="!dialogDetail">确认</el-button>
      </div>
    </el-dialog>

    <el-dialog title="编辑授权" :visible.sync="dialogEditVisible" custom-class="single_dal_view">
      <el-form
        ref="rolesForm"
        :model="temp"
        label-position="left"
        label-width="110px"
      >
        <el-form-item>
          <el-tree
            ref="tree"
            :data="routesData"
            :props="defaultProps"
            :default-checked-keys="checkedNodes"
            show-checkbox
            :check-strictly="true"
            node-key="key"
          />
        </el-form-item>
      </el-form>

        <div slot="footer" class="dialog-footer">
          <el-button @click="dialogEditVisible = false">关闭</el-button>
          <el-button type="primary" @click="savePermission()">确认</el-button>
        </div>
    </el-dialog>

  </div>
</template>

<script>
 import { messageTip,handleCofirm,formatDataTimeTotime} from "@/utils"
 import {getAllRole,getAllUser} from '@/api/system/role'
 import {accesslist,getAllCluster,addAccess,delAccess,update} from '@/api/system/access'
 import Pagination from '@/components/Pagination' 
 import { role_type_arr,valid_period,priv_type_arr } from "@/utils/global_variable"

export default {
  name: "access",
  components: { Pagination },
  data() {
    const validateUsername = (rule, value, callback) => {
     if(!value){
        callback(new Error("请选择用户账号"));
      }
      else {
        callback();
      }
    };
    const validRolename = (rule, value, callback) => {
     if(!value){
        callback(new Error("请选择角色名称"));
      }
      else {
        callback();
      }
    };
    const validdataperiod = (rule, value, callback) => {
     if(!value){
        callback(new Error("请选择有效期类型"));
      }
      else {
        callback();
      }
    };
     const validradio = (rule, value, callback) => {
     if(!value){
        callback(new Error("请选择是否应用于所有集群"));
      }
      else {
        callback();
      }
    };
    return {
      tableKey: 0,
      list: null,
      listLoading: true,
      total: 0,
      listQuery: {
        pageNo: 1,
        pageSize: 10,
      },
      temp: {
        username: '',
        role_name: '',
        valid_period:'',
        start_ts:'',
        end_ts:'',
        radio: '',
        checkedCluster: [],
      },
      dialogFormVisible: false,
      dialogEditVisible:false,
      dialogStatus: "",
      textMap: {
        update: "编辑授权",
        create: "新增授权",
        detail: "详情"
      },
      dialogDetail: false,
      message_tips:'',
      message_type:'',
      role_type_data:role_type_arr,
      valid_period_data:valid_period,
      priv_type_arr:priv_type_arr,
      checkedGrant:[],
      rolesData:[],
      userData:[],
      checkedCluster: [],
      clusters: [],
      clusterTotal:0,
      minCluster:0,
      radio:'',
      user_grant:JSON.parse(sessionStorage.getItem('priv')).user_grant_priv,
      row:{},
      rules: {
        username: [
           { required: true, trigger: "blur",validator: validateUsername },
        ],
        role_name: [
          { required: true, trigger: "blur",validator: validRolename },
        ],
        valid_period: [
          { required: true, trigger: "blur",validator: validdataperiod },
        ],
        radio: [
          { required: true, trigger: "blur",validator: validradio },
        ]
      },
      rolId:'',
      routesData:[],
      checkedNodes:[],
      defaultProps: {
        children: 'children',
        label: 'slotTitle'
      },
      routes:[]
    };
  },
  created() {
    this.getList()
    this.getRole()
    this.getUser()
    this.getCluster()
  },
  watch: {
    'checkedCluster': {
      handler: function(val,oldVal) {
        this.temp.checkedCluster=[];
        this.checkedCluster.forEach(item => {
        if (this.temp.checkedCluster.indexOf(item) == -1) {
          this.temp.checkedCluster.push(item)
        }
      })
      },
      immediate: true
    }
  },
  methods: {
    async getRole() {
      const res = await getAllRole()
      this.rolesData = res.list;
    },
    async getCluster() {
      const res = await getAllCluster()
      this.clusters = res.list;
      if(res.total<=1){
        this.minCluster=0;
        this.clusterTotal=0;
        this.radio=1;
      }else{
        this.minCluster=1;
        this.clusterTotal=res.total-1;
      }
    },
    async getUser() {
      const res = await getAllUser()
      this.userData = res.list;
    },
    getList() {
        this.listLoading = true
        let queryParam = Object.assign({}, this.listQuery)
        //模糊搜索
        queryParam.roleName = `*${this.listQuery.roleName}*`
        accesslist(queryParam).then(response => {
          this.list = response.list;
          this.total = response.total;
          setTimeout(() => {
            this.listLoading = false
          }, 0.5 * 1000)
        });
    },
    resetTemp() {
      this.temp = {
       username: '',
        role_name: '',
        valid_period:'',
        start_ts:'',
        end_ts:'',
        radio: '',
        checkedCluster: [],
      };
    },
    handleCreate() {
      this.resetTemp();
      this.dialogStatus = "create";
      this.dialogFormVisible = true;
      this.dialogDetail = false;
      this.$nextTick(() => {
        this.$refs.dataForm.clearValidate();
      });
    },
    createData() {
      this.$refs["dataForm"].validate((valid) => {
        if (valid) {
          // 时间段判断
          if(this.temp.valid_period=='from_to'){
            if(!this.temp.start_ts&&!this.temp.end_ts){
              messageTip(`起止时间起止时间必须有一个不为空`,'info');return;
            }
            if(this.temp.start_ts&&this.temp.end_ts){
              if(this.temp.start_ts>=this.temp.end_ts){
                messageTip(`开始时间不能大于等于结束时间`,'info');return;
              }
            }
          }else{
            this.temp.start_ts='';
            this.temp.end_ts='';
          } 
          // 是否应用于所有集群判断
          if(this.temp.radio=='2'){
            if(this.temp.checkedCluster.length<= 0){
                messageTip(`请勾选集群`,'info');return;
              }
          }
          //发送接口
          addAccess(this.temp).then(response=>{
            let res = response;
            if(res.code==200){
              this.getList();
              this.dialogFormVisible = false;
              this.message_tips = '新增成功';
              this.message_type = 'success';
            }
            else{
              this.message_tips = res.message;
              this.message_type = 'error';
            }
            messageTip(this.message_tips,this.message_type);
          })
        }
      });
    },
    handleDetail(row){
      this.dialogStatus = "detail"
      this.dialogFormVisible = true
      this.temp = Object.assign({}, row);
      this.dialogDetail = true
    },
    handleUpdate(row) {
      this.temp = Object.assign({}, row); // copy obj,***很重要,不需要一步步赋值了***
      this.dialogStatus = "update";
      this.dialogFormVisible = true;
      this.dialogDetail = false;
      this.$nextTick(() => {
        this.$refs["dataForm"].clearValidate();
      });
    },
    updateData(row) {
      this.$refs["dataForm"].validate((valid) => {
        if (valid) {
          if(this.temp.valid_period=='from_to'){
            if(!this.temp.start_ts&&!this.temp.end_ts){
              messageTip(`起止时间起止时间必须有一个不为空`,'info');return;
            }
            if(this.temp.start_ts&&this.temp.end_ts){
              if(this.temp.start_ts>=this.temp.end_ts){
                messageTip(`开始时间不能大于等于结束时间`,'info');return;
              }
            }
          }else{
            this.temp.start_ts='';
            this.temp.end_ts='';
          }
          const tempData = Object.assign({}, this.temp);
          update(tempData).then((response) => {
            let res = response;
            if(res.code==200){
              this.dialogFormVisible = false;
              this.message_tips = '编辑成功';
              this.message_type = 'success';
              this.getList();
            }
            else{
              this.message_tips = res.message;
              this.message_type = 'error';
            }
            messageTip(this.message_tips,this.message_type);

          });
        }
      });
    },
    handleDelete(row) {
      handleCofirm("此操作将永久删除该数据, 是否继续?").then( () =>{
          delAccess(row.user_id,row.role_id).then((response)=>{
            let res = response;
            if(res.code==200){
              this.dialogFormVisible = false;
              this.message_tips = '删除成功';
              this.message_type = 'success';
              //成功后重新设置数据
              this.getList();
            }
            else{
              this.message_tips = res.message;
              this.message_type = 'error';
            }
            messageTip(this.message_tips,this.message_type);
      })
      }).catch(() => {
        console.log('quxiao')
          messageTip('已取消删除','info');
      }); 
    },
    handleEdit(row){ 
      this.resetTemp();
      this.dialogStatus = "grant";
      this.dialogFormVisible = true;
      this.dialogDetail = false;
      this.$nextTick(() => {
        this.$refs.dataForm.clearValidate();
      });
    },
    handleRoleType(row){
      let res = '';
        for(let v of role_type_arr){
          if(row.role_name == v['value']){
            res = v['label'];break;
          }
          else {
            res = row.role_name
          }
        }
        return res;
    },
     handleEffectTime(row){
      let res = '';
        for(let v of valid_period){
          if(row.valid_period == v['value']){
            res = v['label'];break;
          }
          else {
            res = row.valid_period
          }
        }
        return res;
    },
    handleStartTime(row){
      let res = '';
      if(row.start_ts == '0000-00-00 00:00:00'){
        res = '';
      }else {
        res = row.start_ts
      }
      return res;
    },
    handleEndTime(row){
      let res = '';
     if(row.end_ts == '0000-00-00 00:00:00'){
        res = '';
      }else{
        res = row.end_ts
      }
      return res;
    },
    formatDataTimeTotime(value){
      return formatDataTimeTotime(value)
    }
  },
};
</script>