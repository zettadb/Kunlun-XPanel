<template>
  <div class="login-container">
    <p class="header fl">泽拓科技-DBA管理平台</p>
    <div class="login-wrap">
      <el-form
        ref="loginForm"
        :model="loginForm"
        :rules="loginRules"
        class="login-form"
        label-position="left"
      >
        <div class="title-container">
          <h3 class="title">修改密码</h3>
        </div>

        <el-form-item prop="username">
          <el-input
            ref="username"
            v-model="loginForm.username"
            placeholder="请输入账户(手机号)"
            name="username"
            type="text"
            tabindex="1"
             :disabled="true"
          />
        </el-form-item>

        <el-form-item prop="password">
          <el-input
            :key="passwordType"
            ref="password"
            v-model="loginForm.password"
            :type="passwordType"
            placeholder="请输入新密码"
            autocomplete='new-password'
            name="password"
            tabindex="4"
          />
        </el-form-item>

        <el-form-item prop="confirmPassword">
          <el-input
            :key="passwordType"
            ref="confirmPassword"
            v-model="loginForm.confirmPassword"
            :type="passwordType"
            placeholder="请再次输入新密码"
            name="confirmPassword"
            tabindex="5"
          />
        </el-form-item>

        <el-button
          :loading="loading"
          type="primary"
          style="width:100%;margin-bottom:20px;"
          @click.native.prevent="handleChangePassword"
        >修改密码</el-button>
      </el-form>
    </div>
  </div>
</template>

<style scoped>
.header {
  font-size: 18px;
  font-weight: bold;
  padding-left: 60px;
  height: 50px;
  line-height: 50px;
  margin: 0 auto;
  background-color: #e4f4fb;
}
.login-container .login-wrap {
  padding: 170px 0;
  padding: 200px 0;
  width: 100%;
  background: #e4f4fb;
  position: absolute;
    /* top: 0; */
    top: 50px;
    left: 0;
    z-index: 11;
    /* padding-top: 230px; */
    padding-top: 180px;
}
.title-container{
  height: 20px;
}
.title-container .title {
  font-size: 16px;
  margin: 0px auto 30px auto;
  text-align: center;
}
.login-container .login-form {
  position: relative;
  width: 330px;
  max-width: 100%;
  padding: 22px 64px 0;
  margin: 0 auto;
  overflow: hidden;
  background-color: #fff;
  border-radius: 5px;
}

</style>

<script>
import { changePassword } from "@/api/login/user";
import { messageTip } from "@/utils";
export default {
  name: "changePasswordView",
  data() {
    const validateUsername = (rule, value, callback) => {
      if (!value) {
        callback(new Error("请输入用户名!"));
      // } else if (!/^1[3456789]\d{9}$/.test(value)) {
      //   callback(new Error("手机号格式不对!"));
      // } else {
      //   callback();
      // }
      }else if (!(/^[A-Za-z0-9_]+$/.test(value)) ) {
        callback(new Error("用户名只能输入英文字母、数字、下划线"));
      } else {
        callback();
      }
    };
    const validatePassword = (rule, value, callback) => {
      if (value.length < 6) {
        callback(new Error("密码不少于6位!"));
      } else if (!/^[A-Za-z0-9_]+$/.test(value)) {
        callback(new Error("密码只能输入英文字母、数字、下划线!"));
      } else {
        callback();
      }
    };
    const validateConfirmPassword = (rule, value, callback) => {
      if (value.length < 6) {
        callback(new Error("密码不少于6位!"));
      } else if (!/^[A-Za-z0-9_]+$/.test(value)) {
        callback(new Error("密码只能输入英文字母、数字、下划线!"));
      } else if(value!==this.loginForm.password) {
         callback(new Error("密码不一致!")); 
      } 
      else {
        callback();
      }
    };
    return {
      loginForm: {
        username: sessionStorage.getItem('zettadb_vue_name'),
        password: "",
        confirmPassword: "",
        inputCodeContent: "",
      },
      loginRules: {
        username: [
          { required: true, trigger: "blur", validator: validateUsername },
        ],
        password: [
          { required: true, trigger: "blur", validator: validatePassword },
        ],
        confirmPassword: [
          {
            required: true,
            trigger: "blur",
            validator: validateConfirmPassword,
          },
        ]
        // inputCodeContent: [
        //   { required: true, trigger: "blur", validator: validateInputCode },
        // ],
      },
      loading: false,
      loadingCode:false,
      passwordType: "password",
      verifiedCode: "",
      currdatetime: "",
    };
  },
  methods: {
    handleChangePassword() {
      this.$refs.loginForm.validate(async (valid) => {
        if (valid) {
          this.loading = true;
          //发送修改密码接口
          let post_data = {
            username: this.loginForm.username,
            password: this.loginForm.password
          };
          let res = await changePassword(post_data);
          let that = this;
          if(res.code=='200'){
            this.loading = false;
            that.$message({ 
              message:res.message, 
              type: 'success', 
              onClose:function(){
                setTimeout(function(){
                  sessionStorage.clear();
                  that.$router.push({path:'/login'})
                },0)
              }
            });
          }else{
            messageTip(res.message,'error');
             this.loading = false;
          }
        } else {
          console.log("error submit!!");
          return false;
        }
      });
    },
    getCode() {
      if(!/^1[3456789]\d{9}$/.test(this.loginForm.username)){
          this.$message({ message:'手机号格式不对!', type: 'error'})
      }
      else {
          this.loadingCode = true;
      }
      console.log('获取验证码');
    },
  },
};
</script>