<template>
  <div class="navbar">
    <hamburger :is-active="sidebar.opened" class="hamburger-container" @toggleClick="toggleSideBar" />

    <breadcrumb class="breadcrumb-container" />

    <div class="right-menu">
      <el-tooltip class="item" effect="dark" content="查看功能简介" >
        <a class="right-menu-item" @click="showDocument"><i class="el-icon-question"></i></a>
      </el-tooltip>
      <span class="right-menu-item" v-if="!login_auth">|</span>
      <el-tooltip class="item" effect="dark" content="查看机器监控信息">
        <a class="right-menu-item" @click="showMachine"><i class="el-icon-monitor"></i></a>
      </el-tooltip>
      <span class="right-menu-item" v-if="!login_auth">|</span>
      <a @click="changePaasword" class="right-menu-item right-border">当前账户：{{name}}</a>
      <span class="right-menu-item" v-if="!login_auth">|</span>

      <a @click="logout" class="right-menu-item" v-if="!login_auth">退出登录</a>

    </div>
  </div>
</template>

<script>
import { mapGetters } from 'vuex'
import Breadcrumb from '@/components/Breadcrumb'
import Hamburger from '@/components/Hamburger'
import {ip_arr} from "@/utils/global_variable"
export default {
  components: {
    Breadcrumb,
     Hamburger
  },
  computed: {
    ...mapGetters([
      'sidebar',
    ]),
    
  },
  data(){
    return {
      villageName:'',
      name:'',
      login_auth:'',
      websock: null,
    }
  },
  created:function(){
    this.getVillageName()
    this.login_auth = sessionStorage.getItem('login_auth');
  },
  methods: {
   
    getVillageName(){
        this.villageName = sessionStorage.getItem('villageName')
        this.name = sessionStorage.getItem('zettadb_vue_name');
    },
    toggleSideBar() {
      this.$store.dispatch('app/toggleSideBar')
    },
    async logout() { 
      sessionStorage.clear();
      this.$router.push(`/login`)
    },
    changePaasword(){
      this.$router.push(`/alteration`)
    },
    showMachine(){
      window.open('http://'+ip_arr[0].ip+'/d/c8W2b01Zz/node-exporter-for-prometheus-dashboard-zhong-wen-jian-rong-ban?orgId=1');
    },
    showDocument(){
       window.open('http://zettadb.com:8181/docs/kunlundb/kunlundb-1do3t7csai5a4#28uu2b');
    }
  }
}
</script>

<style lang="scss">
.navbar {
  height: 50px;
  overflow: hidden;
  position: relative;
  background: #fff;
  box-shadow: 0 1px 4px rgba(0,21,41,.08);

  .hamburger-container {
    line-height: 46px;
    height: 100%;
    float: left;
    cursor: pointer;
    transition: background .3s;
    -webkit-tap-highlight-color:transparent;

    &:hover {
      background: rgba(0, 0, 0, .025)
    }
  }

  .breadcrumb-container {
    float: left;
  }

  .right-menu {
    float: right;
    height: 100%;
    line-height: 50px;

    .el-icon-monitor {
      display: inline-block;
      cursor: pointer;
      font-size: 22px;
      vertical-align: -2px;
    }

    &:focus {
      outline: none;
    }

    .right-menu-item {
      display: inline-block;
      padding: 0 8px;
      height: 100%;
      font-size: 14px;
      color: #5a5e66;
      vertical-align: text-bottom;

      &.hover-effect {
        cursor: pointer;
        transition: background .3s;

        &:hover {
          background: rgba(0, 0, 0, .025)
        }
      }

      .el-icon {
        font-size: 22px;
        margin-top: 14px;
        display: inline-block;
      }

      .el-badge__content {
        height: 20px;
        line-height: 20px;
        width:20px;
        top: 40px;
        padding: 0 ;
        right: 6px;
      }

    }

    .avatar-container {
      margin-right: 30px;

      .avatar-wrapper {
        margin-top: 5px;
        position: relative;

        .user-avatar {
          cursor: pointer;
          width: 40px;
          height: 40px;
          border-radius: 10px;
        }

        .el-icon-caret-bottom {
          cursor: pointer;
          position: absolute;
          right: -20px;
          top: 25px;
          font-size: 12px;
        }
      }
    }
  }
}
</style>