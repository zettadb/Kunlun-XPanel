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
          <el-input   :type="pwdType" v-model="temp.password" placeholder="登录密码，大小写字母/数字/特殊字符组合，长度8-12位" autocomplete='new-password' >
           <i slot="suffix" class="el-icon-view" @click="changeType"></i>
          </el-input>
        </el-form-item>
         <el-form-item label="重复密码:" prop="confirmPassword"  v-if="dialogStatus==='create'?true:false">
          <el-input v-model="temp.confirmPassword" :type="pwdconfirmType" placeholder="请再次输入登录密码"  >
            <i slot="suffix" class="el-icon-view" @click="changeconfirmType"></i>
          </el-input>
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
 import { getAccountList,addAccount,update,delAccount,findMobile,findUserName} from '@/api/system/account'
 import Pagination from '@/components/Pagination' 

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
      }else if(value==='internal_user'){
          callback(new Error("该账号为系统账号，不允许添加"));
      }
      else {
        if(!this.temp.old_username){
            findUserName(value).then((response) => {
              if(response.message){
                callback(new Error("用户账号重复"));
              }
              else{
                callback();return;
              }
            });
          }
          else if(this.temp.old_username && (this.temp.old_username!==value)){
            findUserName(value).then((response) => {
              if(response.message){
                callback(new Error("用户账号重复"));
              }
              else{
                callback();return;
              }
            });
          }
          else{
            callback();
          }
      }
    };
    const validatePassword = (rule, value, callback) => {
      //  var passwordreg = /(?=.*\d)(?=.*[a-zA-Z])(?=.*[^a-zA-Z0-9]).{8,12}/;
      var passwordreg = /^(?!.*\s)(?=.*?[a-z])(?=.*?[A-Z])(?=.*?[0-9])(?=.*?[-!@#$%^&*()_+|~=`{}\[\]:";'<>?,.\/]).{8,12}$/;
     if(!value){
        callback(new Error("请输入登陆密码"));
      }
      else if (!passwordreg.test(value)){
        callback(new Error("密码必须由大小写字母,数字,特殊字符(不含空格)组成,且长度为8到12位"));
      } else {
        callback();
      }
    };
    const validateconfirmPassword = (rule, value, callback) => {
      if(!value){
        callback(new Error("重复密码不能为空"));
      }
      else if(value != this.temp.password){
        callback(new Error('两次输入密码不一致'));
      }else {
        callback();
      }
    };
     const validateMobile = (rule, value, callback) => {
        if(this.search_person_has_m){
          callback();return;
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
                callback();return;
              }
            });
          }
          else if(this.temp.old_mobile && (this.temp.old_mobile!==value)){
            findMobile(value).then((response) => {
              if(response.message){
                callback(new Error("手机号重复"));
              }
              else{
                callback();return;
              }
            });
          }
          else{
            callback();
          }
        }
    };
    const validateEmail = (rule, value, callback) => {
      //  const regEmail = /^[A-Za-z0-9\u4e00-\u9fa5]+@[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)+$/;
      const regEmail=/^[a-zA-Z0-9]+([-_.][a-zA-Z0-9]+)*@[a-zA-Z0-9]+([-_.][a-zA-Z0-9]+)*\.[a-z]{2,}$/;
      if(!value){
        callback(new Error("请输入邮箱地址"));
      }
     else if(!regEmail.test(value)){
        callback(new Error('邮箱地址格式有误'));
      }else {
        callback();
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
        old_username:'',
        password:'',
        phone_number:'',
        email:'',
        wechat_number:'',
        old_mobile:'',
        confirmPassword: ''
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
      pwdType: 'password',
      pwdconfirmType:'password',
      rules: {
        username: [
          { required: true, trigger: "blur",validator: validateUsername },
        ],
        password: [
          { required: true, trigger: "blur",validator: validatePassword },
        ],
        phone_number: [
           { required: true, trigger: "blur",validator: validateMobile },
        ],
        confirmPassword: [
          { required: true, trigger: "blur",validator: validateconfirmPassword },
        ],
        email: [
          { required: true, trigger: "blur",validator: validateEmail },
        ]
      },
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
    handleClear(){
      this.listQuery.username = ''
      this.listQuery.pageNo = 1
      this.getList()
    },
    getList() {
        this.listLoading = true
        let queryParam = Object.assign({}, this.listQuery)
        queryParam.user_name=sessionStorage.getItem('login_username');
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
        old_username:'',
        password:'',
        phone_number:'',
        email:'',
        wechat_number:'',
        old_mobile:'',
        confirmPassword: ''
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
      this.resetTemp();
      this.temp = Object.assign({}, row); 
      this.dialogStatus = "update";
      this.dialogFormVisible = true;
      this.dialogDetail = false;
      this.temp.old_mobile = row.phone_number;
      this.temp.old_username = row.username;
      this.$nextTick(() => {
        this.$refs["dataForm"].clearValidate();
      });
    },
    updateData() {
      this.$refs["dataForm"].validate((valid) => {
        if (valid) {
          const tempData = Object.assign({}, this.temp);
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
    changeconfirmType(){
      this.pwdconfirmType = this.pwdconfirmType === 'password' ? 'text' : 'password';
      let e = document.getElementsByClassName('el-icon-view')[1];
      this.pwdconfirmType == 'text' ? e.setAttribute('style', 'color: #409EFF') : e.setAttribute('style', 'color: #c0c4cc');
    },
    changeType() {
      this.pwdType = this.pwdType === 'password' ? 'text' : 'password';
      let e = document.getElementsByClassName('el-icon-view')[0];
      this.pwdType == 'text' ? e.setAttribute('style', 'color: #409EFF') : e.setAttribute('style', 'color: #c0c4cc');
    },
  },
};
</script>