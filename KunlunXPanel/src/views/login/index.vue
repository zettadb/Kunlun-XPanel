<<<<<<< HEAD
<<<<<<< HEAD
<template>
  <div class="login-container">
     <Loginheader />
     <div class="login-bg">
    </div>
    <div class="login-body">
    <el-form
      ref="loginForm"
      :model="loginForm"
      :rules="loginRules"
      class="login-form"
      auto-complete="on"
      label-position="left"
    >
      <div class="title-container">
        <h3 class="title">统一管理用户登录</h3>
      </div>

      <el-form-item prop="username">
        <el-input
          ref="username"
          v-model="loginForm.username"
          placeholder="请输入账户"
          name="username"
          type="text"
          tabindex="1"
          auto-complete="on"
        />
      </el-form-item>

      <el-form-item prop="password">
        <el-input
          :key="passwordType"
          ref="password"
          v-model="loginForm.password"
          :type="passwordType"
          placeholder="请输入密码"
          name="password"
          tabindex="2"
          auto-complete="on"
          @keyup.enter.native="handleLogin"
        />
        <span class="show-pwd" @click="showPwd">
          <svg-icon :icon-class="passwordType === 'password' ? 'eye' : 'eye-open'" />
        </span>
      </el-form-item>

    
      <div class="oh change_password_wrap">
        <el-checkbox v-model="checked" class="remember">记住密码</el-checkbox>
        <router-link :to="{path: '/alteration'}" class="change_password">忘记密码</router-link>
      </div>
      <el-button
        :loading="loading"
        type="primary"
        style="width:100%;margin-bottom:20px;"
        @click.native.prevent="handleLogin"
      >登录</el-button>
    </el-form>
    </div>
    <div class="footer">
      <p>
        <span>© 2022 zettadb.com版权所有</span>
        <span>泽拓科技（深圳）有限责任公司</span>
      </p>
    </div>
  </div>
</template>

<script>
import { login} from "@/api/login/user";
//import { validUsername } from '@/utils/validate'
import Loginheader from '@/components/Loginheader';
import { messageTip } from "@/utils";

export default {
  name: 'Login',
  components:{Loginheader},
  data() {
    const validateUsername = (rule, value, callback) => {
      if(!value){
        callback(new Error("请输入用户名"));
      }
      else if (!(/^[A-Za-z0-9_]+$/.test(value)) ) {
        callback(new Error("用户名只能输入英文字母、数字、下划线"));
      } else {
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
    return {
      loginForm: {
        username: '',
        password: '',
        //inputCodeContent:''
      },
      loginRules: {
        username: [
          { required: true, trigger: "blur", validator: validateUsername }
        ],
        password: [
          { required: true, trigger: "blur", validator: validatePassword }
        ] //,
        // inputCodeContent: [
        //   { required: true, trigger: 'blur',validator: validateInputCode}
        // ]
      },
      loading: false,
      passwordType: 'password',
      redirect: undefined,
      checked:true,
      randCodeImage:'',
      requestCodeSuccess:false,
      verifiedCode:'',
      //currdatetime:''
    };
  },
  watch: {
    $route: {
      handler: function(route) {
        this.redirect = route.query && route.query.redirect;
      },
      immediate: true
    }
  },
  methods: {
    showPwd() {
      if (this.passwordType === "password") {
        this.passwordType = "";
      } else {
        this.passwordType = "password";
      }
      this.$nextTick(() => {
        this.$refs.password.focus();
      });
    },
    // inputCodeChange(e){
    //     this.loginForm.inputCodeContent = e
    // },
    handleLogin() {
      if(this.checked){
        sessionStorage.setItem('login_username',this.loginForm.username);
        sessionStorage.setItem('login_password',this.loginForm.password);
      }
      else{
        sessionStorage.setItem('login_username','');
        sessionStorage.setItem('login_password','');
      }
      //修改登录方式不需要加cCode参数后需要去掉注释
      // if(!this.cCode && this.loginForm.username=="admin"){
      //   messageTip('admin账号登录时url中必须有cCode参数','error');return;
      // }
      this.$refs.loginForm.validate(async valid => {
        if (valid) {
          this.loading = true;
          let post_data = {
            username: this.loginForm.username,
            password: this.loginForm.password,
            //checkKey:this.currdatetime,
            //remember_me:true,
            // captcha:this.loginForm.inputCodeContent
          };
          let loginRes = await login(post_data);
          if (loginRes.code == 200) {
            this.goto(loginRes);
          }
          else{
            messageTip(loginRes.message,'error');
            this.loading = false;
          }
        } else {
          console.log("error submit!!");
          return false;
        }
      });
    },
    goto(loginRes){
      this.loading = false;
      console.log(loginRes.result);
      if(!loginRes.accessToken){
        messageTip('登录接口没有返回token','error');return;
      }
      sessionStorage.setItem('zettadb_vue_token',loginRes.accessToken);
      sessionStorage.setItem('zettadb_vue_name',loginRes.userName);
      let num =loginRes.num;
      if(num==1){
        sessionStorage.setItem('apply_all_cluster',loginRes.apply_all_cluster);
        sessionStorage.setItem('affected_clusters',loginRes.affected_clusters);
        sessionStorage.setItem('priv',JSON.stringify(loginRes.priv));
        //console.log(JSON.parse(sessionStorage.getItem('priv')).backup_priv);
        this.$router.push({ path: '/dashboard'})
      }
      else{
        this.$router.push({ path:'/alteration'})
      }
    }
  },
  created: function() {
    //进入这个页面表示不是单点登录,所以需要去掉单点登录缓存信息(url中带的token)
    //sessionStorage.removeItem('login_auth');
   // const params = getUrlVars('cCode');
    //this.cCode = params.cCode;
    //this.handlegetRandomImage();
    if(this.checked){
      this.loginForm.username = sessionStorage.getItem('login_username');
      this.loginForm.password = sessionStorage.getItem('login_password');
    }
    else{
      this.loginForm.username = '';
      this.loginForm.password = '';
    }
  }
};
</script>

 <style lang="scss">

$bg: #283443;
$light_gray: #fff;
$cursor: #333;

.oh {
    overflow: hidden;
}
.fl {
  float: left;
}
.fr {
  float: right;
}

.el-checkbox__input.is-checked+.el-checkbox__label {
  color:#333;
}
.el-checkbox__inner:hover,.el-checkbox__inner,.el-checkbox__inner.is-focus .el-checkbox__inner {
  border-color: #333 !important;
}
.el-checkbox__input.is-checked .el-checkbox__inner, .el-checkbox__input.is-indeterminate .el-checkbox__inner{
  background-color:#333;
  border-color: #333;
}

@supports (-webkit-mask: none) and (not (cater-color: $cursor)) {
  .login-container .el-input input {
    color: $cursor;
  }
}

/* reset element-ui css */
.login-container {

  .el-input {
    display: inline-block;
    width: 100%;
    
    input {
      border: 0px;
      -webkit-appearance: none;
      border:1px solid #d7d7d7;
      border-radius: 5px;
      padding-left: 20px;
      color: #333;
      height: 38px;
      line-height: 38px;
      box-sizing: border-box;
      caret-color: $cursor;
    }
  }

}
</style>

<style lang="scss">
$bg: #e4f4fb;
$dark_gray: #889aa4;
$border_radius:5px;
.el-form-item {
  margin-bottom: 18px;
}
.login-container {
  height: 100%;
  width: 100%;
  // width: 1440px;
  // height: 820px;
  margin:0 auto;
  background-color: $bg;
  overflow: hidden;
  color: #333333;

  .login-bg {
    position: absolute;
    top: 0;
    left: 0;
    z-index: 10;
    height: 100%;
    width: 100%;
    background-image: url('~@/assets/images/login_bg.jpg');
    background-repeat: no-repeat;
    background-position: center center;
    background-size: 100% 100%;
  }
  .login-body {
    position: absolute;
    top: 0;
    left: 0;
    z-index: 11;
    width: 100%;
    // padding-top: 270px;
    padding-top:230px;
  }

  .header {
    font-size: 18px;
    font-weight: bold;
    padding-left: 60px;
    height: 50px;
    line-height: 50px;
    margin:0 auto;
  }

  .select_lang {
    font-size: 14px;
    padding-right: 20px;

    span {
      margin-right: 20px;
    }

  }

  .footer {
    overflow: hidden;
    font-size: 13px;
    line-height: 1.4;
    padding-right: 60px;
    padding-top: 10px;
    height: 70px;
    position: absolute;
    width: 100%;
    bottom: 0;
    z-index:11;
    background-color: #e4f4fb;
    p {
      width: 190px;
      float: right;
      overflow: hidden;

      span {
        display: block;
      }

    }

  }

  .login-form {
    position: relative;
    width: 396px;
    max-width: 100%;
    padding: 30px 64px 0;
    margin: 0 auto;
    overflow: hidden;
    background-color: #fff;
    border-radius: $border_radius;
  }
  
  .change_password_wrap {
    margin-bottom:26px;
    padding: 0 4px;
  }

  .change_password {
    float: right;
    text-decoration: underline;
    font-size: 13px;
  }

  .remember {
    float: left;
    
  }
  .el-checkbox__input.is-checked+.el-checkbox__label {
    color:#333 !important;
    font-size: 13px !important;
  }

  .tips {
    font-size: 14px;
    color: #fff;
    margin-bottom: 10px;

    span {
      &:first-of-type {
        margin-right: 16px;
      }
    }
  }

  .svg-container {
    padding: 6px 5px 6px 15px;
    color: $dark_gray;
    vertical-align: middle;
    width: 30px;
    display: inline-block;
  }

  .title-container {
    position: relative;
    height: 20px;

    .title {
      font-size: 16px;
      margin: 0px auto 30px auto;
      text-align: center;
      background: #fff;
      color: #000;
    }
  }

  .show-pwd {
    position: absolute;
    right: 10px;
    top: 3px;
    font-size: 16px;
    color: $dark_gray;
    cursor: pointer;
    user-select: none;
  }
}
</style>
=======
=======
>>>>>>> 1.1

<template>
  <div class="page-module login-container backgroundCover flex width_100 height_100">
    <div class="login-panel">
      <!-- <div class="head-text align_center">
        <img src="../../assets/images/login_bg.jpg">
      </div> -->
      <!-- <el-form class="login-form" size="medium" :model="form" :rules="rules" ref="form">
        <el-form-item prop="username">
          <el-input placeholder="请输入账户名：" autofocus v-model="form.username"/>
        </el-form-item>
        <el-form-item prop="password">
          <el-input type="password" placeholder="请输入账户密码：" v-model="form.password"/>
        </el-form-item>
        <el-form-item>
          <el-button type="primary" class="width_100" icon="el-icon-check" :loading="submitLoad"/>
        </el-form-item>
      </el-form> -->
      <Loginheader />
      <el-form
        ref="loginForm"
        :model="loginForm"
        :rules="loginRules"
        class="login-form"
        auto-complete="on"
        label-position="left"
      >
        <div class="title-container">
          <h3 class="title">统一管理用户登录</h3>
        </div>

        <el-form-item prop="username">
          <el-input
            ref="username"
            v-model="loginForm.username"
            placeholder="请输入账户"
            name="username"
            type="text"
            tabindex="1"
            auto-complete="on"
          />
        </el-form-item>

        <el-form-item prop="password">
          <el-input
            :key="passwordType"
            ref="password"
            v-model="loginForm.password"
            :type="passwordType"
            placeholder="请输入密码"
            name="password"
            tabindex="2"
            auto-complete="on"
            @keyup.enter.native="handleLogin"
          />
          <span class="show-pwd" @click="showPwd">
            <svg-icon :icon-class="passwordType === 'password' ? 'eye' : 'eye-open'" />
          </span>
        </el-form-item>
        <!-- <el-form-item prop="metaDate">
          <el-input
            ref="metaDate"
            v-model="loginForm.metaDate"
            type="text"
            placeholder="请输入元数据的IP:端口"
            name="metaDate"
            tabindex="3"
            auto-complete="on"
            @keyup.enter.native="handleLogin"
            :title="msg"
          />
        </el-form-item> -->
        <!-- <el-form-item prop="meta">
          <el-select v-model="loginForm.meta" placeholder="请选择元数据">
            <el-option
              v-for="item in options"
              :key="item.value"
              :label="item.label"
              :value="item.label">
            </el-option>
          </el-select>
          <i class="el-icon-plus" style="margin-left: 15px;font-size: 20px;"></i>
        </el-form-item> -->
        <div class="oh change_password_wrap">
          <el-checkbox v-model="checked" class="remember">记住密码</el-checkbox>
          <router-link :to="{path: '/alteration'}" class="change_password">忘记密码</router-link>
        </div>
        <el-button
          :loading="loading"
          type="primary"
          style="width:100%;margin-bottom:20px;"
          @click.native.prevent="handleLogin"
        >登录</el-button>
      </el-form>
      <div class="footer">
        <p>
          <span>© 2022 zettadb.com版权所有</span>
          <span>泽拓科技（深圳）有限责任公司</span>
        </p>
      </div>
    </div>
  </div>
</template>
<script>
import {login,change} from "@/api/login/user";
import Loginheader from '@/components/Loginheader';
import { messageTip } from "@/utils";
import { getMetaPrimary,getWorkMode } from "@/api/cluster/list";
import { v4 as uuidv4 } from 'uuid';
import {version_arr,timestamp_arr} from "@/utils/global_variable"

export default {
  name: 'Login',
  components:{Loginheader},
  data() {
    const validateUsername = (rule, value, callback) => {
      if(!value){
        callback(new Error("请输入用户名"));
      }
      else if (!(/^[A-Za-z0-9_]+$/.test(value)) ) {
        callback(new Error("用户名只能输入英文字母、数字、下划线"));
      } else {
        callback();
      }
    };
    const validatePassword = (rule, value, callback) => {
      if (value.length < 8) {
        callback(new Error("密码不少于8位"));
      } else {
        callback();
      }
    };
    // const validateMetaDate = (rule, value, callback) => {
    //   if (!value) {
    //     callback(new Error("请输入元数据的IP端口,以英文冒号分割"));
    //   } else if(value){
    //     if(value.indexOf(":") != -1 ){
    //       const arr=value.split(':');
    //       console.log(arr[0]);
    //       let regexp = /^((2(5[0-5]|[0-4]\d))|[0-1]?\d{1,2})(\.((2(5[0-5]|[0-4]\d))|[0-1]?\d{1,2})){3}$/;
    //       let valdata = arr[0].split(',');
    //       let isCorrect = true;
    //       if (valdata.length) {
    //         for (let i = 0; i < valdata.length; i++) {
    //           if (regexp.test(valdata[i]) == false) {
    //               isCorrect = false;
    //           }
    //         }
    //       }
    //       if(!arr[0]){
    //         callback(new Error('请输入正确的IP'));
    //       }else if (arr[0]&&!isCorrect) {
    //         callback(new Error('请输入正确的IP'));
    //       }else{
    //         if(!arr[1]){
    //           callback(new Error("请输入正确的端口"));
    //         }else if(!(/^[0-9]+$/.test(arr[1])) ) {
    //           callback(new Error("端口只能输入数字"));
    //         }else{
    //           callback();
    //         }
    //       }
    //     }else{
    //       callback(new Error("请输入正确的元数据IP端口,以英文冒号分割"));
    //     }
    //   }else{
    //     callback();
    //   }
    // };
    // const validateMeta= (rule, value, callback) => {
    //   if (!value) {
    //     callback(new Error("请选择元数据"));
    //   } else {
    //     callback();
    //   }
    // };
    return {
      loginForm: {
        username: '',
        password: '',
        // meta:'',
        // metaDate:''
      },
      loginRules: {
        username: [
          { required: true, trigger: "blur", validator: validateUsername }
        ],
        password: [
          { required: true, trigger: "blur", validator: validatePassword }
        ],
        // meta: [
        //   { required: true, trigger: "blur", validator: validateMeta }
        // ], 
        // metaDate: [
        //   { required: true, trigger: "blur", validator: validateMetaDate }
        // ],
      },
      loading: false,
      passwordType: 'password',
      redirect: undefined,
      checked:true,
      randCodeImage:'',
      requestCodeSuccess:false,
      verifiedCode:'',
      options: [],
      meta:'',
      msg:'例:192.168.0.1:10001',
    };
  },
  watch: {
    $route: {
      handler: function(route) {
        this.redirect = route.query && route.query.redirect;
      },
      immediate: true
    }
  },
  methods: {
    //  getMetaList() {
    //    const temp={};
    //   //  temp.job_type='get_meta';
    //   //  temp.version=version_arr[0].ver;
    //   //  temp.job_id=uuidv4();
       
    //    temp.job_type='get_meta_summary';
    //    temp.version=version_arr[0].ver;
    //    temp.job_id='';
    //    temp.timestamp=timestamp_arr[0].time+'';
    //    temp.paras={};
    //    //console.log(temp);return;
    //    getMetaPrimary(temp).then(response => {
    //       const res=response.attachment.list_meta;
    //       //const res=response;
    //       if(res.length>0){
    //         for(let i=0;i<res.length;i++){
    //           //取主节点
    //           if(res[i].master=='true'){
    //             const arr={'value':i+1,'label':res[i].hostaddr+'('+res[i].port+')'};
    //             this.options.push(arr);
    //           }
    //         }
    //       }
    //     });
    // },
    showPwd() {
      if (this.passwordType === "password") {
        this.passwordType = "";
      } else {
        this.passwordType = "password";
      }
      this.$nextTick(() => {
        this.$refs.password.focus();
      });
    },
    handleLogin() {
      if(this.checked){
        sessionStorage.setItem('login_username',this.loginForm.username);
        sessionStorage.setItem('login_password',this.loginForm.password);
      }
      else{
        sessionStorage.setItem('login_username','');
        sessionStorage.setItem('login_password','');
      }
      this.$refs.loginForm.validate(async valid => {
        if (valid) {
          //let arr=this.loginForm.meta.substring(0,this.loginForm.meta.length-1).split('(');
          //let arr=this.loginForm.metaDate.split(':');
          //console.log(arr);return;
          // let post_ip={
          //   ip:arr[0],
          //   port:arr[1]
          // }
          // let res = await change(post_ip);
          let res = await change();
          if(res.code==200){
            this.loading = true;
            let post_data = {
              username: this.loginForm.username,
              password: this.loginForm.password,
            };
            let loginRes = await login(post_data);
            if (loginRes.code == 200) {
              this.goto(loginRes,res.meta_ha_mode);
            }
            else{
              messageTip(loginRes.message,'error');
              this.loading = false;
            }
          }else{
            messageTip(res.message,'error');
          }
          //console.log(res);return;
        } else {
          console.log("error submit!!");
          return false;
        }
      });
    },
    goto(loginRes,meta_ha_mode){
      this.loading = false;
      //console.log(loginRes);
      if(!loginRes.Token){
        messageTip('登录接口没有返回token','error');return;
      }
      // sessionStorage.setItem('zettadb_vue_token',loginRes.Token);
       sessionStorage.setItem('zettadb_vue_name',loginRes.userName);
      // sessionStorage.setItem('meta_ha_mode',meta_ha_mode);
      let num =loginRes.num;
      if(num==1){
        //console.log(num);
        // sessionStorage.setItem('apply_all_cluster',loginRes.apply_all_cluster);
        // sessionStorage.setItem('affected_clusters',loginRes.affected_clusters);
        // sessionStorage.setItem('priv',JSON.stringify(loginRes.priv));
        //区分企业版还是社区版
        let tempData = {};
        tempData.user_name = sessionStorage.getItem('login_username');
        tempData.job_id ='0';
        tempData.job_type ='get_cluster_detail';
        tempData.version=version_arr[0].ver;
        tempData.timestamp=timestamp_arr[0].time+'';
        let paras={}
        tempData.paras=paras;
        //console.log(tempData);
        //发送接口
        getWorkMode(tempData).then(response=>{
          this.isShowNodeMenuPanel=false;
          let res = response;
          //console.log(res);
          if(res.hasOwnProperty('attachment')){
            if(res.attachment!==null){
              sessionStorage.setItem('work_mode',res.attachment.work_mode);
            }else if(res.attachment==null){
              sessionStorage.setItem('work_mode','');
            }else{
              this.message_tips = res.error_info;
              this.message_type = 'error';
              messageTip(this.message_tips,this.message_type);return;
            }
            sessionStorage.setItem('zettadb_vue_token',loginRes.Token);
            sessionStorage.setItem('meta_ha_mode',meta_ha_mode);
            sessionStorage.setItem('apply_all_cluster',loginRes.apply_all_cluster);
            sessionStorage.setItem('affected_clusters',loginRes.affected_clusters);
            sessionStorage.setItem('priv',JSON.stringify(loginRes.priv));
            // this.$router.push({ path: '/dashboard'})
            this.$router.push({ path: '/cluster'})
          }else{
            this.message_tips = res.error_info;
            this.message_type = 'error';
            messageTip(this.message_tips,this.message_type);
          }
        })
      }
      else{
        sessionStorage.setItem('zettadb_vue_token',loginRes.Token);
        this.$router.push({ path:'/alteration'})
      }
    }
  },
  created: function() {
    if(this.checked){
      this.loginForm.username = sessionStorage.getItem('login_username');
      this.loginForm.password = sessionStorage.getItem('login_password');
    }
    else{
      this.loginForm.username = '';
      this.loginForm.password = '';
    }
    //this.getMetaList();
  }
};
</script>
<style lang="scss" type="text/scss" rel="stylesheet/scss">
 $dark_gray: #889aa4;
 $border_radius:5px;
html {
    line-height: 1.15;
    -webkit-text-size-adjust: 100%;
}
* {
      word-break: break-all;
      word-wrap: break-word;
      padding: 0;
      margin: 0;
      outline: 0;
      -webkit-tap-highlight-color: rgba(0,0,0,0);
      -webkit-box-sizing: border-box;
      box-sizing: border-box;
    }
.flex {
    display: -webkit-box;
    display: -ms-flexbox;
    display: flex;
  }
.width_100 {
    width: 100%;
  }
.height_100 {
    height: 100%;
  }
 .backgroundCover {
    background-repeat: no-repeat;
    background-position: center;
    background-size: cover;
  }
  .login-container {
    min-height: 100vh;
    align-items: center;
    justify-content: center;
    background-image: url(~@/assets/images/login_bg.jpg);

    .login-panel {
      // padding: 25px 15px;
      width: 350px;
      border-radius: 3px;
      background-color: #fff;
      max-width: 100%;
    }

    .head-text {
      margin-bottom: 25px;
    } 
  }
  .show-pwd {
    position: absolute;
    right: 10px;
    top: 3px;
    font-size: 16px;
    color: $dark_gray;
    cursor: pointer;
    user-select: none;
  }
   .title-container {
    position: relative;
    height: 20px;

    .title {
      font-size: 16px;
      margin: 0px auto 30px auto;
      text-align: center;
      background: #fff;
      color: #000;
    }
  }
   .login-form {
    position: relative;
    // width: 396px;
    max-width: 100%;
    padding: 30px 30px 0;
    margin: 0 auto;
    overflow: hidden;
    background-color: #fff;
    border-radius: $border_radius;
  }
  .el-form-item {
    margin-bottom: 18px;
  }
  .change_password_wrap {
    margin-bottom:26px;
    padding: 0 4px;
  }
  .change_password {
    float: right;
    text-decoration: underline;
    font-size: 13px;
  }
  .remember {
    float: left;
  }
  .el-checkbox__input.is-checked+.el-checkbox__label {
    color:#333 !important;
    font-size: 13px !important;
  }
  .footer {
    overflow: hidden;
    font-size: 13px;
    line-height: 1.4;
    padding-right: 60px;
    padding-top: 10px;
    height: 70px;
    position: absolute;
    width: 100%;
    bottom: 0;
    left: 0;
    z-index:11;
    background-color: #e4f4fb;
    p {
      width: 190px;
      float: right;
      overflow: hidden;
      span {
        display: block;
      }
    }
  }

</style>
>>>>>>> 1.0
