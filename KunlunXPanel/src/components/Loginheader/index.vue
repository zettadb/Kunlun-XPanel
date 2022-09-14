<<<<<<< HEAD
<template>
  <div class="oh login-header">
      <p class="header fl">{{company}}-DBA管理平台</p>
      <!-- <p class="select_lang fr"><span>English</span><span>繁體中文</span><span>简体中文</span></p> -->
  </div>
</template>

<script>

//import { getCustomer } from "@/api/login/user";

export default {
  name: 'Loginheader',
  data() {
    return {
      //company由本地存储中的客户id(cCode)查询后台接口得到
      company:''
    }
  },
  created() {
      this.company = "泽拓科技";
  },
  methods: {
    // getCustomer(){
    //   getCustomer().then(response => {
    //       this.company = response.result.company;
    //     });
    // }
  }
}
</script>

<style lang="scss">
.login-header {
  position:absolute;
  top:0;
  left: 0;
  width: 100%;
  z-index: 11;
  background-color:#e4f4fb;
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
      cursor: pointer;
      margin-right: 20px;
    }
}
=======
<template>
  <div class="oh login-header">
      <p class="header fl">{{company}}-XPanel管理系统</p>
      <!-- <p class="select_lang fr"><span>English</span><span>繁體中文</span><span>简体中文</span></p> -->
  </div>
</template>

<script>

export default {
  name: 'Loginheader',
  data() {
    return {
      company:''
    }
  },
  created() {
      this.company = "泽拓科技";
  },
}
</script>

<style lang="scss">
.login-header {
  position:absolute;
  top:0;
  left: 0;
  width: 100%;
  z-index: 11;
  background-color:#e4f4fb;
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
      cursor: pointer;
      margin-right: 20px;
    }
}
>>>>>>> 1.0
</style>