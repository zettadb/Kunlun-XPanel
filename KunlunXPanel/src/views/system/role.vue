<template>
  <div class="app-container">
    <div class="filter-container">
      
      <div class="table-list-wrap" v-show="role_add_priv==='Y'">
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
    >
      <el-table-column
        type="index"
        align="center"
        label="序号"
        width="50">
      </el-table-column>

      <el-table-column label="角色名称" align="center">
        <template slot-scope="{row}">
          <span class="link-type" @click="handleDetail(row)">{{ row.role_name }}</span>
        </template>
      </el-table-column>

      <!-- <el-table-column
            prop="role_name"
            align="center"
            label="角色类型"
            :formatter='handleRoleType'>
      </el-table-column> -->

       <!-- <el-table-column
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
      </el-table-column> -->

      <el-table-column
            prop="update_time"
            align="center"
            label="更新时间">
            <!-- <template slot-scope="{row}">
              <span>{{formatDataTimeTotime(row.update_time)}}</span>
            </template> -->
      </el-table-column>

      <el-table-column
        label="操作"
        align="center"
        width="230"
        class-name="small-padding fixed-width"
        v-if="role_edit_priv==='Y'&&role_drop_priv==='Y'"
      >
        <template slot-scope="{row,$index}">
          <!-- <el-button type="primary" size="mini" @click="handleEdit(row)">授权</el-button> -->
          <el-button type="primary" size="mini" @click="handleUpdate(row)" v-show="role_edit_priv==='Y'&&row.role_name!=='super_dba'">编辑</el-button>
          <el-button size="mini" type="danger" @click="handleDelete(row,$index)" v-show="role_drop_priv==='Y'&&row.role_name!=='super_dba'" >删除</el-button>
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
        label-width="110px"
      >
        <el-form-item label="角色名称:" prop="role_name">
          <el-input v-model="temp.role_name" placeholder="请输入角色名称" :disabled="dialogStatus==='detail'||temp.role_name=='super_dba'"/>
        </el-form-item>

        <!-- <el-form-item label="角色类型:" prop="role_name" :disabled="dialogStatus==='detail' || dialogStatus==='update'||dialogStatus==='create'" v-if="dialogStatus==='grant'?false:true">
          <el-select v-model="temp.role_name" placeholder="请选择角色类型">
          <el-option
            v-for="item in role_type_data"
            :key="item.value"
            :label="item.label"
            :value="item.value">
          </el-option>
        </el-select>
        </el-form-item> -->

        <!-- <el-form-item label="有效期类型:" prop="valid_period"  :disabled="dialogStatus==='detail' || dialogStatus==='update'||dialogStatus==='create'" v-if="dialogStatus==='grant'?false:true">
          <el-select v-model="temp.valid_period" placeholder="请选择有效期类型">
            <el-option
              v-for="item in valid_period_data"
              :key="item.value"
              :label="item.label"
              :value="item.value">
            </el-option>
          </el-select>
        </el-form-item>

        <div v-if="temp.valid_period=='from_to'" :disabled="dialogStatus==='detail' || dialogStatus==='update'||dialogStatus==='create'">
        <el-form-item label="起止时间:" prop="beginDate" v-if="dialogStatus==='grant'?false:true" >
          <el-date-picker v-model="temp.start_ts" type="datetime" value-format="yyyy-MM-dd HH:mm:ss"  placeholder="请选择开始时间"></el-date-picker>
        至
        <el-date-picker v-model="temp.end_ts" type="datetime" value-format="yyyy-MM-dd HH:mm:ss" placeholder="请选择结束时间"></el-date-picker>
        </el-form-item>
        </div>

          <el-form-item label="角色分配:" v-if="dialogStatus==='grant'?true:false">
            <el-select v-model="temp.selectedroles" multiple placeholder="请选择用户角色">
            <el-option
              v-for="item in rolesData"
              :key="item.id"
              :label="item.name"
              :value="item.id">
            </el-option>
          </el-select>
          </el-form-item> -->

        <!-- 角色授权 -->
         <!-- <el-form-item label="角色授权:" prop="priv_type_arr" v-if="dialogStatus==='grant'?true:false">
         <el-checkbox-group 
            v-model="checkedGrant"
            :min="0"
            :max="26">
            <el-checkbox 
            v-for="priv in priv_type_arr" 
            :label="priv" 
            :key="priv">{{priv}}</el-checkbox>
          </el-checkbox-group>
          </el-form-item> -->

          <!-- 角色权限 -->
          <el-form-item label="拥有权限:" prop="checkkeys"  :disabled="dialogStatus==='detail'">
          <el-tree
            ref="tree"
            :data="datatree"
            show-checkbox
            node-key="id"
            :default-expanded-keys="[3]"
            :default-checked-keys="checkkeys"
            :props="defaultProps1"
            >
          </el-tree>
          </el-form-item>


      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="dialogFormVisible = false" v-show="!dialogDetail">关闭</el-button>
        <el-button type="primary" @click="dialogStatus==='create'?createData():updateData(row)" v-show="!dialogDetail">确认</el-button>
        <!-- <el-button type="primary" @click="dialogStatus==='create'?createData():(dialogStatus==='update'?updateData(row):grantData(row))" v-show="!dialogDetail">确认</el-button> -->
      </div>
    </el-dialog>

    <!-- <el-dialog title="编辑权限" :visible.sync="dialogEditVisible" custom-class="single_dal_view">
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
    </el-dialog> -->

  </div>
</template>

<script>
 import { messageTip,handleCofirm,formatDataTimeTotime} from "@/utils"
 import { rolelist,addRole,update,delRole,getAllRole,rolePermission} from '@/api/system/role'
 import Pagination from '@/components/Pagination' 
 import { role_type_arr,valid_period,priv_type_arr,datatree_arr } from "@/utils/global_variable"



export default {
  name: "role",
  components: { Pagination },
  data() {
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
        role_name: '',
        priv:{}
      },
      dialogFormVisible: false,
      dialogEditVisible:false,
      dialogStatus: "",
      textMap: {
        update: "编辑角色",
        create: "新增角色",
        detail: "详情",
        grant:'授权'
      },
      dialogDetail: false,
      message_tips:'',
      message_type:'',
      role_type_data:role_type_arr,
      valid_period_data:valid_period,
      priv_type_arr:priv_type_arr,
      checkedGrant:[],
      rolesData:[],
      role_add_priv:JSON.parse(sessionStorage.getItem('priv')).role_add_priv,
      role_drop_priv:JSON.parse(sessionStorage.getItem('priv')).role_drop_priv,
      role_edit_priv:JSON.parse(sessionStorage.getItem('priv')).role_edit_priv,
      row:{},
       datatree:datatree_arr,
        defaultProps1: {
          children: 'children',
          label: 'label'
        },
      rules: {
        role_name: [
          { required: true, message: "请输入角色名称!", trigger: "blur" },
        ]
      },
      rolId:'',
      routesData:[],
      checkedNodes:[],
      defaultProps: {
        children: 'children',
        label: 'slotTitle'
      },
      routes:[],
      checkkeys:[]
    };
  },
  created() {
    this.getList()
    
  },
  methods: {
    getList() {
        this.listLoading = true
        let queryParam = Object.assign({}, this.listQuery)
        //模糊搜索
        queryParam.roleName = `*${this.listQuery.roleName}*`
        rolelist(queryParam).then(response => {
          this.list = response.list;
          this.total = response.total;
          setTimeout(() => {
            this.listLoading = false
          }, 0.5 * 1000)
        });
    },
    resetTemp() {
      this.temp = {
        role_name: '',
        priv:{}
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
       let checkedNodes =  this.$refs.tree.getCheckedKeys();
       let priv = {permissionIds:checkedNodes.join(",")};
       this.temp.priv=priv;
      this.$refs["dataForm"].validate((valid) => {
        if (valid) {
          //发送接口
          addRole(this.temp).then(response=>{
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
      //设置默认选中节点(即当前角色所拥有的菜单权限)
      rolePermission(row.id).then(res => {
          this.checkkeys = res.list[0].checkkeys;
          this.dialogEditVisible = true;
          console.log( this.checkkeys);
          //不会重新渲染dom树,所以要手动设置tree的选中节点
          //this.$nextTick(() => {
          //  this.$refs.tree.setCheckedKeys(this.checkkeys)
          //})
      });
      this.temp = Object.assign({}, row); // copy obj,***很重要,不需要一步步赋值了***
      this.temp.id = row.id;
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
          let checkedNodes =  this.$refs.tree.getCheckedKeys();
          let priv = {permissionIds:checkedNodes.join(",")};
          this.temp.priv=priv;
          const tempData = Object.assign({}, this.temp);
          console.log(tempData);
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
          delRole(row.id).then((response)=>{
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
      // this.rolId = row.id;
      // //设置默认选中节点(即当前角色所拥有的菜单权限)
      // queryRolePermission(row.id).then(res => {
      //     this.checkedNodes = res.result;
      //     this.dialogEditVisible = true;
      //     //不会重新渲染dom树,所以要手动设置tree的选中节点
      //     this.$nextTick(() => {
      //       this.$refs.tree.setCheckedKeys(this.checkedNodes)
      //     })
      // });
    },
    savePermission(){
        let checkedNodes =  this.$refs.tree.getCheckedKeys();
        let addData = {roleId:this.rolId,permissionIds:checkedNodes.join(","),lastpermissionIds:this.checkedNodes.join(",")};
         console.log(addData);
        //发送接口
        saveRolePermission(addData).then(response=>{
          let res = response;
          if(res.code==200){
            this.getList();
            this.dialogEditVisible = false;
            this.message_tips = '编辑成功';
            this.message_type = 'success';
          }
          else{
            this.message_tips = res.message;
            this.message_type = 'error';
          }
          messageTip(this.message_tips,this.message_type);
        })
    },
    async getRoutes() {
      const res = await getRoutes()
      this.routesData = getTreeData(res.result.treeList);
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