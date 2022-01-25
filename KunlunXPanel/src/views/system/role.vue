<template>
  <div class="app-container">
    <div class="filter-container">
      
      <div class="table-list-wrap">
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
            <!-- <template slot-scope="{row}">
              <span>{{formatDataTimeTotime(row.update_time)}}</span>
            </template> -->
      </el-table-column>

      <el-table-column
        label="操作"
        align="center"
        width="230"
        class-name="small-padding fixed-width"
      >
        <template slot-scope="{row,$index}">
          <!-- <el-button type="primary" size="mini" @click="handleEdit(row)">授权</el-button> -->
          <el-button type="primary" size="mini" @click="handleUpdate(row)">编辑</el-button>
          <el-button size="mini" type="danger" @click="handleDelete(row,$index)" v-show="row.id!=='admin'" >删除</el-button>
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
          <el-input v-model="temp.roleName" placeholder="请输入角色名称" :disabled="dialogStatus==='detail'"/>
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
          <el-form-item label="拥有权限:" prop="priv_type_arr"  :disabled="dialogStatus==='detail'">
          <el-tree
            ref="tree"
            :data="datatree"
            show-checkbox
            node-key="id"
            :default-expanded-keys="[3]"
            :default-checked-keys="[5]"
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

    <el-dialog title="编辑权限" :visible.sync="dialogEditVisible" custom-class="single_dal_view">
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
 import { rolelist,addRole,update,delRole,getAllRole} from '@/api/system/role'
 import Pagination from '@/components/Pagination' 
 import { role_type_arr,valid_period,priv_type_arr } from "@/utils/global_variable"
 //import { asyncRoutes,router } from '@/router'
//import store from '@/store'

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
        //roleName: '',
        //role_name: '',
      },
      temp: {
        //roleName: '',
        role_name: '',
        valid_period:'',
        start_ts:'',
        end_ts:''
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
      row:{},
       datatree:[{
          id: 'user',
          label: '用户权限',
          children: [{
            id: 'add',
            label: '新增用户'
          },{
            id: 'edit',
            label: '编辑用户'
          },{
            id: 'del',
            label: '删除用户'
          },{
            id: 'grant',
            label: '用户授权'
          }]
        }, {
          id: 'role',
          label: '角色权限',
          children: [{
            id: 'add',
            label: '新增角色'
          }, {
            id: 'edit',
            label: '编辑角色'
          }, {
            id: 'del',
            label: '删除角色'
          }]
        }, {
          id: 'cluster',
          label: '集群权限',
          children: [{
            id: 'add',
            label: '新增集群'
          }, {
            id: 'del',
            label: '删除集群'
          }, {
            id: 'backeup',
            label: '备份集群'
          }, {
            id: 'restore',
            label: '恢复集群'
          }, {
            id: 'expand',
            label: '集群扩容'
          }, {
            id: 'shrink',
            label: '集群缩容'
          }, {
            id: 'storage',
            label: '存储节点权限',
            children: [
              {
               id:'node_create',
               label:'新增存储节点'
              },{
               id:'node_drop',
               label:'删除存储节点'
              },{
               id:'en',
               label:'启/禁用存储节点'
              }]
          },{
            id: 'comp',
            label: '计算节点权限',
            children: [
              {
               id:'add',
               label:'新增计算节点'
              },{
               id:'del',
               label:'删除计算节点'
              },{
               id:'en',
               label:'启/禁用计算节点'
              }]
          },{
            id: 'shard',
            label: '存储shard权限',
            children: [
              {
               id:'add',
               label:'新增存储shard'
              },{
               id:'del',
               label:'删除存储shard'
              }]
          }]
        }, {
          id: 'equip',
          label: '计算机权限',
          children: [{
            id: 'add',
            label: '新增计算机'
          }, {
            id: 'edit',
            label: '编辑计算机'
          }, {
            id: 'del',
            label: '删除计算机'
          }, {
            id: 'en',
            label: '启/禁用计算机'
          }]
        }],
        defaultProps1: {
          children: 'children',
          label: 'label'
        },
      rules: {
        // roleName: [
        //   { required: true, message: "请输入角色名称!", trigger: "blur" },
        // ],
        role_name: [
          { required: true, message: "请输入角色名称!", trigger: "blur" },
        ],
        // valid_period: [
        //   { required: true, message: "请选择角色类型!", trigger: "blur" },
        // ]
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
    //this.getRoutes()
    //this.getRoleTypes()
  },
  methods: {
    async getRole() {
      const res = await getAllRole()
      this.rolesData = res.list;
    },
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
        //roleName: '',
        role_name: '',
        valid_period:'',
        start_ts:'',
        end_ts:''
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
        let addData = {roleId:this.id,permissionIds:checkedNodes.join(","),lastpermissionIds:this.checkedNodes.join(",")};
         console.log(addData);
      this.$refs["dataForm"].validate((valid) => {
        if (valid) {
          if(this.temp.valid_period=='from_to'){
            if(!this.temp.start_ts||!this.temp.end_ts){
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
      // console.log(row);
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
          if(this.temp.valid_period=='from_to'){
            if(!this.temp.start_ts||!this.temp.end_ts){
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
            // //编辑权限后应该刷新左侧菜单栏,todo
            // let constRoutes = [];
            // constRoutes = asyncRoutes;
            // store.dispatch('permission/UpdateAppRouter',  { constRoutes }).then(() => {
            //   // 根据roles权限生成可访问的路由表
            //   // 动态添加可访问路由表
            //   router.addRoutes(store.getters.addRoutes)
            // })
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
      // console.log(this.routesData);
    },
    // getRoleTypes(){
    //   //这里根据用户的role来给定能分配的角色类型
    //   console.log('登录用户所属角色编码:',sessionStorage.getItem('userRolesCodeArr'))
    //   let userRolesCodeArr = sessionStorage.getItem('userRolesCodeArr')
    //   userRolesCodeArr = JSON.parse(userRolesCodeArr)
    //   let role_type_arr = []
    //   //有系统管理员,最高权限,可配置:系统管理员-项目管理员-普通操作员
    //   if(userRolesCodeArr.includes('admin')){
    //     role_type_arr = [{value:'system_admin',label:'系统管理员'},{value:'system_manager',label:'项目管理员'},{value:'system_operator',label:'普通操作员'}]
    //   }
    //   //系统管理员,可配置:项目管理员-普通操作员
    //   else if(userRolesCodeArr.includes('system_admin')){
    //     role_type_arr = [{value:'system_manager',label:'项目管理员'},{value:'system_operator',label:'普通操作员'}]
    //   }
    //   //项目管理员,可配置:普通操作员
    //   else{
    //     role_type_arr = [{value:'system_operator',label:'普通操作员'}]
    //   }
    //   this.role_type_data = role_type_arr;
    //   // console.log(role_type_arr);
    // },
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
        // console.log(row);
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