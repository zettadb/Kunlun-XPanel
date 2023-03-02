<template>
  <div class="login-container">
    <p class="header fl">泽拓科技-XPanel管理系统 用户注册</p>
    <div class="login-wrap">
      <el-form ref="loginForm" :model="loginForm" :rules="loginRules" class="login-form" label-position="left">

        <el-form-item prop="username" label="请输入名称">
          <el-col :span="12">
            <el-input ref="username" v-model="loginForm.username" placeholder="请输入名称" name="username" type="text"
              tabindex="1" />
          </el-col>
        </el-form-item>
        <el-form-item prop="email" label="请输入邮箱">
          <el-col :span="12">
            <el-input v-model="loginForm.email" type="email" placeholder="请输入邮箱地址" />
          </el-col>
          <el-col :span="5">
            <el-button type="primary" :loading="false" @click="getCodeV()">{{ codeText }}</el-button>
          </el-col>
        </el-form-item>

        <el-form-item prop="email" label="输入验证码">
          <el-col :span="12">
            <el-input v-model="loginForm.code" type="email" placeholder="输入验证码" />
          </el-col>
        </el-form-item>

        <el-form-item prop="password" label="请输入密码">
          <el-col :span="12">
            <el-input :key="passwordType" ref="password" v-model="loginForm.password" :type="passwordType"
              placeholder="请输入密码" autocomplete='new-password' name="password" tabindex="4" />
          </el-col>
        </el-form-item>

        <el-form-item prop="confirmPassword" label="请确认密码">
          <el-col :span="12">
            <el-input :key="passwordType" ref="confirmPassword" v-model="loginForm.confirmPassword" :type="passwordType"
              placeholder="请再次输入密码" name="confirmPassword" tabindex="5" />
          </el-col>
        </el-form-item>
        <el-form-item label="客户验证ID">
          <el-col :span="12">
            <el-input v-model="loginForm.user_tag" :disabled="true" tabindex="5" />
          </el-col>
        </el-form-item>

        <el-button :loading="loading" type="primary" style="width:100%;margin-bottom:20px;"
          @click.native.prevent="createData()">立即注册</el-button>
      </el-form>
    </div>
  </div>
</template>

<script>
import { changePassword } from "@/api/login/user";
import { messageTip } from "@/utils";
import { addAccount, getVcode } from '@/api/system/account'
import { nullLiteral } from "@babel/types";
export default {
  name: "changePasswordView",
  data() {

    const validateUsername = (rule, value, callback) => {
      if (!value) {
        callback(new Error("请输入用户名!"));
      } else if (!(/^[A-Za-z0-9_]+$/.test(value))) {
        callback(new Error("用户名只能输入英文字母、数字、下划线"));
      } else {
        callback();
      }
    };
    const validateEmail = (rule, value, callback) => {
      //  const regEmail = /^[A-Za-z0-9\u4e00-\u9fa5]+@[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)+$/;
      const regEmail = /^[a-zA-Z0-9]+([-_.][a-zA-Z0-9]+)*@[a-zA-Z0-9]+([-_.][a-zA-Z0-9]+)*\.[a-z]{2,}$/
      if (!value) {
        callback(new Error('请输入邮箱地址'))
      } else if (!regEmail.test(value)) {
        callback(new Error('邮箱地址格式有误'))
      } else {
        callback()
      }
    }
    const validatePassword = (rule, value, callback) => {
      //var passwordreg = /(?=.*\d)(?=.*[a-zA-Z])(?=.*[^a-zA-Z0-9]).{8,12}/;
      var passwordreg = /^(?!.*\s)(?=.*?[a-z])(?=.*?[A-Z])(?=.*?[0-9])(?=.*?[-!@#$%^&*()_+|~=`{}\[\]:";'<>?,.\/]).{8,12}$/;
      if (!value) {
        callback(new Error("请输入密码"));
      }
      else if (!passwordreg.test(value)) {
        callback(new Error("密码必须由大小写字母,数字,特殊字符(不含空格)组成,且长度为8到12位"));
      } else {
        callback();
      }
    };
    const validateConfirmPassword = (rule, value, callback) => {
      //var passwordreg = /(?=.*\d)(?=.*[a-zA-Z])(?=.*[^a-zA-Z0-9]).{8,12}/;
      var passwordreg = /^(?!.*\s)(?=.*?[a-z])(?=.*?[A-Z])(?=.*?[0-9])(?=.*?[-!@#$%^&*()_+|~=`{}\[\]:";'<>?,.\/]).{8,12}$/;
      if (!value) {
        callback(new Error("请再次输入密码"));
      } else if (!passwordreg.test(value)) {
        callback(new Error("密码必须由大小写字母,数字,特殊字符(不含空格)组成,且长度为8到12位"));
      } else if (value !== this.loginForm.password) {
        callback(new Error("密码不一致!"));
      }
      else {
        callback();
      }
    };
    return {
      loginForm: {
        username: "",
        password: "",
        confirmPassword: "",
        inputCodeContent: "",
        email: "",
        old_username: '',
        phone_number: '',
        wechat_number: '',
        old_mobile: '',
        confirmPassword: '',
        pid: "0",
        user_tag: "",
        main_user: "1",
        code: "",
      },
      codeText: "获取验证码",
      mailTime: 300,
      l: null,
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
        ],
        email: [
          { required: true, trigger: 'blur', validator: validateEmail }
        ]
      },
      loading: false,
      loadingCode: false,
      passwordType: "text",
      verifiedCode: "",
      currdatetime: "",
      user_tag: "",
    };
  },
  watch: {
    $route: {
      handler: function (route) {
        this.loginForm.user_tag = route.query.token
        this.redirect = route.query && route.query.redirect;
      },
      immediate: true
    }
  },
  created() {

  },
  methods: {

    resetTemp() {

    },
    handleCreate() {

      this.dialogStatus = 'create'
      this.dialogFormVisible = true
      this.dialogDetail = false
      this.$nextTick(() => {
        this.$refs.dataForm.clearValidate()
      })
    },

    getCodeV() {
      if (this.loginForm.email == "") {
        this.message_tips = "请输入邮箱"
        this.message_type = 'error'
        messageTip(this.message_tips, this.message_type)
        return false
      }

      if (this.l != null) {
        return false
      }

      getVcode({ email: this.loginForm.email }).then(response => {
        const res = response
        if (res.code === 200) {
          this.dialogFormVisible = false
          this.message_tips = '邮件发送成功'
          this.message_type = 'success'
          const _this = this;
          _this.l = setInterval(() => {
            _this.mailTime -= 1
            if (_this.mailTime <= 0) {
              clearInterval(_this.l)
            }
            _this.codeText = _this.mailTime + " 后重新获取"
          }, 1000)
          messageTip(this.message_tips, this.message_type)
          return false
        } else if (res.code === 505) {
          this.message_tips = "邮箱已经注册，去登录"
          this.message_type = 'error'
          messageTip(this.message_tips, this.message_type)
          return false
        } else {
          this.message_tips = res.msg
          this.message_type = 'error'
          messageTip(this.message_tips, this.message_type)
          return false
        }
      })

    },
    createData() {
      this.$refs.loginForm.validate(async (valid) => {
        if (valid) {
          const tempData = Object.assign({}, this.loginForm)

          // 发送接口
          addAccount(tempData).then(response => {
            const res = response
            if (res.code === 200) {
              this.dialogFormVisible = false
              this.message_tips = '注册成功'
              this.message_type = 'success'
            } else {
              this.message_tips = res.msg
              this.message_type = 'error'
            }
            messageTip(this.message_tips, this.message_type)
          })
        }
      })
    },
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
          if (res.code == 200) {
            this.loading = false;
            that.$message({
              message: res.message,
              type: 'success',
              onClose: function () {
                setTimeout(function () {
                  sessionStorage.clear();
                  that.$router.push({ path: '/login' })
                }, 0)
              }
            });
          } else if (res.code == 200) {

          } else {
            messageTip(res.message, 'error');
            this.loading = false;
          }
        } else {
          console.log("error submit!!");
          return false;
        }
      });
    },
    getCode() {
      if (!/^1[3456789]\d{9}$/.test(this.loginForm.username)) {
        this.$message({ message: '手机号格式不对!', type: 'error' })
      }
      else {
        this.loadingCode = true;
      }
      console.log('获取验证码');
    },
  },
};
</script>

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
  width: 100%;
  background: #e4f4fb;
  position: absolute;
  top: 50px;
  left: 0;
  z-index: 11;
  padding-top: 70px;
}

.title-container {
  height: 20px;
}

.title-container .title {
  font-size: 16px;
  margin: 0px auto 30px auto;
  text-align: center;
}

.login-container .login-form {
  position: relative;
  width: 550px;
  max-width: 100%;
  padding: 64px;
  margin: 0 auto;
  overflow: hidden;
  background-color: #fff;
  border-radius: 5px;
}

.el-form-item {
  margin-bottom: 33px;
}
</style>