<template>
  <div class="navbar">
    <hamburger
      :is-active="sidebar.opened"
      class="hamburger-container"
      @toggleClick="toggleSideBar"
    />

    <breadcrumb class="breadcrumb-container" />

    <div class="right-menu">
      <el-tooltip class="item" effect="dark" content="查看功能简介">
        <a class="right-menu-item" @click="showDocument"
          ><i class="el-icon-question"
        /></a>
      </el-tooltip>
      <span v-if="!login_auth" class="right-menu-item">|</span>
      <el-tooltip class="item" effect="dark" content="查看机器监控信息">
        <a class="right-menu-item" @click="showMachine"><i class="el-icon-monitor" /></a>
      </el-tooltip>
      <span v-if="!login_auth" class="right-menu-item">|</span>
      <a class="right-menu-item right-border" @click="changePaasword"
        >当前账户：{{ name }}</a
      >
      <span v-if="!login_auth" class="right-menu-item">|</span>

      <a v-if="!login_auth" class="right-menu-item" @click="logout">退出登录</a>
    </div>
  </div>
</template>

<script>
import { mapGetters } from "vuex";
import Breadcrumb from "@/components/Breadcrumb";
import Hamburger from "@/components/Hamburger";
import { ip_arr } from "@/utils/global_variable";
import { nodeDashboard } from "@/api/grafana/list";

export default {
  components: {
    Breadcrumb,
    Hamburger,
  },
  computed: {
    ...mapGetters(["sidebar"]),
  },
  data() {
    return {
      villageName: "",
      name: "",
      login_auth: "",
      websock: null,
    };
  },
  created: function () {
    this.getVillageName();
    this.login_auth = sessionStorage.getItem("login_auth");
  },
  methods: {
    getVillageName() {
      this.villageName = sessionStorage.getItem("villageName");
      this.name = sessionStorage.getItem("zettadb_vue_name");
    },
    toggleSideBar() {
      this.$store.dispatch("app/toggleSideBar");
    },
    async logout() {
      sessionStorage.clear();
      this.$router.push(`/login`);
    },
    changePaasword() {
      this.$router.push(`/alteration`);
    },
    showMachine() {
      nodeDashboard().then((pyres) => {
        if (pyres.code === 200) {
          window.open(ip_arr[0].ip + pyres.url + "?orgId=1&refresh=5s");
        }
      });
      // window.open('http://'+ip_arr[0].ip+'/d/c8W2b01Zz/node-exporter-for-prometheus-dashboard-zhong-wen-jian-rong-ban?orgId=1');
      // window.open(ip_arr[0].ip+'/d/c8W2b01Zz/node-exporter-for-prometheus-dashboard-zhong-wen-jian-rong-ban?orgId=1');
    },
    showDocument() {
      window.open("http://doc.kunlunbase.com/");
    },
  },
};
</script>

<style lang="scss">
.navbar {
  height: 50px;
  overflow: hidden;
  position: relative;
  background: #fff;
  box-shadow: 0 1px 4px rgba(0, 21, 41, 0.08);

  .hamburger-container {
    line-height: 46px;
    height: 100%;
    float: left;
    cursor: pointer;
    transition: background 0.3s;
    -webkit-tap-highlight-color: transparent;

    &:hover {
      background: rgba(0, 0, 0, 0.025);
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
        transition: background 0.3s;

        &:hover {
          background: rgba(0, 0, 0, 0.025);
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
        width: 20px;
        top: 40px;
        padding: 0;
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
