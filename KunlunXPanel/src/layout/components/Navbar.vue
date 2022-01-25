<template>
  <div class="navbar">
    <hamburger :is-active="sidebar.opened" class="hamburger-container" @toggleClick="toggleSideBar" />

    <breadcrumb class="breadcrumb-container" />

    <div class="right-menu">

      <a @click="clearEventList" class="right-menu-item" style="margin-right:10px;" >
        <el-popover
          placement="top-start"
          width="260"
          trigger="hover"
          :content="mes_tip">
          <el-badge :value="eventNumber" slot="reference" v-show="eventNumber">
            <i class="el-icon el-icon-alarm-clock"></i>
          </el-badge>
        </el-popover>
      </a>

      <!-- <a class="right-menu-item" @click="accessshow"><i class="el-icon-monitor"></i></a> -->
      
      <!-- <screenfull id="screenfull" class="right-menu-item hover-effect" /> -->

      <!-- <a @click="changeVillage" class="right-menu-item">当前社区：{{villageName}}</a> 
      <span class="right-menu-item">|</span> -->
    
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
//import Screenfull from '@/components/Screenfull'
import { isJSON } from "@/utils"

export default {
  components: {
    Breadcrumb,
     Hamburger
    // Screenfull
  },
  computed: {
    ...mapGetters([
      'sidebar',
      'eventNumber',  //未查看事件数目
    ]),
    //计算属性,可以实时获取数据
    mes_tip(){
      return this.$store.getters.eventNumber + '条报警事件未查看,请及时处理'
    }
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
    this.initWebSocket();
  },
  methods: {
    clearEventList(){
      //先把未读事件清空为0,然后跳转到事件中心管理页面
      this.$store.commit('event/clear')
      this.$router.push(`/event/eventlist`)
    },
    getVillageName(){
        this.villageName = sessionStorage.getItem('villageName')
        this.name = sessionStorage.getItem('zettadb_vue_name');
    },
    toggleSideBar() {
      this.$store.dispatch('app/toggleSideBar')
    },
    async logout() {
      //退出登录,清除所有的cookie以及vuex中的state
      sessionStorage.removeItem('zettadb_vue_token');
      //sessionStorage.removeItem('villageName');
      //sessionStorage.removeItem('villageCode');
      sessionStorage.removeItem('zettadb_vue_name');
      //这里表示调用vuex中的uers模块下的RESET_STATE这个mutation(vuex下同步方法的别称)
      this.$store.commit('user/RESET_STATE');
      //清除权限菜单列表
      this.$store.commit('permission/RESET_STATE');
      // this.$router.push(`/login?redirect=${this.$route.fullPath}`)
      this.$router.push(`/login`)
      console.log(sessionStorage.getItem('villageCode'));
    },
    changeVillage(){
      //先清空sessionStorage中的villageCode和villageName
      sessionStorage.removeItem('villageCode');
      sessionStorage.removeItem('villageName');
      this.$router.push(`/selectvillage`)
    },
    changePaasword(){
      this.$router.push(`/alteration`)
    },
    initWebSocket(){ //初始化weosocket
      let timestamp = (new Date()).getTime();
      let userId = sessionStorage.getItem('zettadb_vue_token')+'_'+timestamp;
      const wsuri = process.env.VUE_APP_BASE_API2+userId;
      // console.log(wsuri);
      this.websock = new WebSocket(wsuri);
      //开始接收数据
      this.websock.onmessage = this.websocketonmessage;
      // this.websock.onopen = this.websocketonopen;
      this.websock.onerror = this.websocketonerror;
      this.websock.onclose = this.websocketclose;
    },
    websocketonopen(){ //连接建立之后执行send方法发送数据
      let actions = {"test":"12345"};
      this.websocketsend(JSON.stringify(actions));
    },
    websocketonerror(){//连接建立失败重连
      this.initWebSocket();
    },
    websocketonmessage(e){ //数据接收
      if(isJSON(e.data)){
        const redata = JSON.parse(e.data);
        let title = redata.sceneName
        let alarmTime = redata.alarmTime
        //未查看事件数量加1
        this.$store.commit('event/add')
        //新事件提醒
        this.$notify({
          title: '事件中心',
          dangerouslyUseHTMLString: true,
          message: alarmTime+'<br />'+title,
          position: 'bottom-right',
          duration: 6000,
        });
      }
    },
    websocketsend(Data){//数据发送
      this.websock.send(Data);
    },
    websocketclose(e){  //关闭
      console.log('websocket断开连接',e);
    },
    accessshow(){
      //this.$router.push(`/accessshow`)
      var customerCode=sessionStorage.getItem('cCode')
      if(customerCode==10005){
          this.$router.push(`/accessshow`)
      }else{
         this.$router.push(`/smartshow`)
      }
    },
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