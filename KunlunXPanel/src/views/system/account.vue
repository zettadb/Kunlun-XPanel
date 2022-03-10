<template>
  <div class="app-container">
    <div class="filter-container">
      <div class="table-list-search-wrap">
        <el-input
          class="list_search_keyword"
          v-model="listQuery.username"
          placeholder="可输入用户账号搜索"
          @keyup.enter.native="handleFilter"
        />
        <el-button  icon="el-icon-search" @click="handleFilter">
          查询
        </el-button>
        <el-button  icon="el-icon-refresh-right" @click="handleClear">
          重置
        </el-button>
        <el-button
          class="filter-item"
          type="primary"
          icon="el-icon-plus"
          @click="handleCreate"
          v-show="user_add_priv==='Y'"
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

      <el-table-column label="用户账号" align="center">
        <template slot-scope="{row}">
          <span class="link-type" @click="handleDetail(row)">{{ row.username }}</span>
        </template>
      </el-table-column>

      <el-table-column
            prop="email"
            align="center"
            label="邮箱地址">
      </el-table-column>

      <el-table-column
            prop="phone_number"
            align="center"
            label="手机号">
      </el-table-column>

      <el-table-column
            prop="wechat_number"
            align="center"
            label="微信号">
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
        v-if="user_edit_priv==='Y' && user_drop_priv==='Y'"
      >
        <template slot-scope="{row,$index}">
          <el-button type="primary" size="mini" @click="handleUpdate(row)" v-show="user_edit_priv==='Y'">编辑</el-button>
          <el-button
            size="mini"
            type="danger"
            @click="handleDelete(row,$index)"
            v-show="user_drop_priv==='Y'&&row.username!=='super_dba'"
          >删除</el-button>
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
      
        <el-form-item label="用户账号:" prop="username">
          <el-input v-model="temp.username" placeholder="请输入用户账号" :disabled="dialogStatus==='detail'||temp.username=='super_dba'"/>
        </el-form-item>

        <el-form-item label="登录密码:" prop="password" v-if="dialogStatus==='create'?true:false">
          <el-input type="password" v-model="temp.password" placeholder="请输入登录密码" autocomplete='new-password' :disabled="dialogStatus==='detail'" />
        </el-form-item>

        <el-form-item label="手机号码:" prop="phone_number" >
          <el-input v-model="temp.phone_number" placeholder="请输入手机号码" maxlength="11" show-word-limit clearable  :disabled="dialogStatus==='detail' || search_person_has_m "/>
        </el-form-item>

        <el-form-item label="邮箱地址:" prop="email">
          <el-input type="email" v-model="temp.email" placeholder="请输入邮箱地址"  :disabled="dialogStatus==='detail'"/>
        </el-form-item>

        <el-form-item label="微信号:" prop="wechat_number">
          <el-input type="wechat_number" v-model="temp.wechat_number" placeholder="请输入微信号"  :disabled="dialogStatus==='detail'"/>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="dialogFormVisible = false" v-show="!dialogDetail">关闭</el-button>
        <el-button type="primary" @click="dialogStatus==='create'?createData():updateData(row)"  v-show="!dialogDetail">确认</el-button>
      </div>
    </el-dialog>

  </div>
</template>

<script>
 import { messageTip,handleCofirm } from "@/utils";
 import { getAccountList,addAccount,update,delAccount,findMobile} from '@/api/system/account'
 import Pagination from '@/components/Pagination' 
//  import {getUserPremisson} from '@/api/system/priv'

export default {
  name: "account",
  components: { Pagination },
  data() {
    const validateUsername = (rule, value, callback) => {
      if(!value){
        callback(new Error("请输入用户账号"));
      }
      else if (!(/^[0-9a-zA-Z_]+$/.test(value)) ) {
        callback(new Error("用户账号只能输入英文字母、数字、下划线"));
      }
      else {
        callback();
      }
    };
    const validatePassword = (rule, value, callback) => {
      if (value.length < 6) {
        callback(new Error("密码不少于6位"));
      } else {
        callback();
      }
    };
     const validateMobile = (rule, value, callback) => {
        if(this.search_person_has_m){
          callback();return
        }
        if(!value){
            callback(new Error("请输入手机号"));
        }
        else if (!(/^1[0-9]\d{9}$/.test(value)) ) {
            callback(new Error("手机号格式不对"));
        } 
        //通过验证之后,还要调取后台的一个接口,来验证手机号是否重复
        else {
          if(!this.temp.old_mobile){
            findMobile(value).then((response) => {
              if(response.message){
                callback(new Error("手机号重复"));
              }
              else{
                callback();
              }
            });
          }
          else if(this.temp.old_mobile && (this.temp.old_mobile!==value)){
            findMobile(value).then((response) => {
              if(response.message){
                callback(new Error("手机号重复"));
              }
              else{
                callback();
              }
            });
          }
          else{
            callback();
          }
        }
    };
    return {
      tableKey: 0,
      list: null,
      listLoading: true,
      searchLoading:false,
      total: 0,
      listQuery: {
        pageNo: 1,
        pageSize: 10,
        username: '',
      },
      search_person_has_m:false,
      temp: {
        username: '',
        password:'',
        phone_number:'',
        email:'',
        wechat_number:'',
        old_mobile:''
        //confirmpassword:'',
      },
      dialogFormVisible: false,
      dialogEditVisible:false,
      dialogStatus: "",
      textMap: {
        update: "编辑用户",
        create: "新增用户",
        detail: "详情"
      },
      dialogDetail: false,
      message_tips:'',
      message_type:'',
      user_add_priv:JSON.parse(sessionStorage.getItem('priv')).user_add_priv,
      user_drop_priv:JSON.parse(sessionStorage.getItem('priv')).user_drop_priv,
      user_edit_priv:JSON.parse(sessionStorage.getItem('priv')).user_edit_priv,
      row:{},
      rules: {
        username: [
          { required: true, trigger: "blur",validator: validateUsername },
        ],
        password: [
          { required: true, trigger: "blur",validator: validatePassword },
        ],
        phone_number: [
           { required: true, trigger: "blur",validator: validateMobile },
        ]
      },
    };
  },
  created() {
    this.getList()
    //this.getPremission()
  },
  methods: {
    //  async getPremission() {
    //   const res = await getUserPremisson()
    //   this.user_add_priv = res.list[0].user_add_priv;
    //   this.user_drop_priv = res.list[0].user_drop_priv;
    //   this.user_edit_priv = res.list[0].user_edit_priv;
    // },
    handleFilter() {
      this.listQuery.pageNo = 1
      this.getList()
    },
    handleClear(){
      this.listQuery.username = ''
      this.listQuery.pageNo = 1
      this.getList()
    },
    getList() {
        this.listLoading = true
        let queryParam = Object.assign({}, this.listQuery)
        //模糊搜索
        getAccountList(queryParam).then(response => {
          this.list = response.list;
          this.total = response.total;
          setTimeout(() => {
            this.listLoading = false
          }, 0.5 * 1000)
        });
    },
    resetTemp() {
      this.search_person_has_m = false;
      this.temp = {
        username: '',
        password:'',
        phone_number:'',
        email:'',
        wechat_number:'',
        old_mobile:''
       // confirmpassword:''
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
          const tempData = Object.assign({}, this.temp);
          //tempData.confirmpassword = this.temp.password
          //console.log(tempData);return;
          //发送接口
          addAccount(tempData).then(response=>{
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
      this.temp = Object.assign({}, row); 
       console.log(row);
      this.dialogStatus = "update";
      this.dialogFormVisible = true;
      this.dialogDetail = false;
      this.temp.old_mobile = row.phone_number;
      this.$nextTick(() => {
        this.$refs["dataForm"].clearValidate();
      });
    },
    updateData() {
      this.$refs["dataForm"].validate((valid) => {
        if (valid) {
          const tempData = Object.assign({}, this.temp);
          //tempData.confirmpassword = this.temp.password
          update(tempData).then((response) => {
            let res = response;
            if(res.code==200){
              this.dialogFormVisible = false;
              this.message_tips = '编辑成功';
              this.message_type = 'success';
              //编辑成功后重新设置数据
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
          delAccount(row.id).then((response)=>{
            console.log(response);
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
  },
};
</script>