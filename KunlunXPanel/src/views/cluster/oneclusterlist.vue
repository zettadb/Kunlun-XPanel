<template>
  <div class="all">
    <div class="leftmenu">
      <el-col :span="3">
        <el-menu
          default-active="1"
          class="el-menu-vertical-demo"
          @select="selected"
        >
          <el-menu-item index="1">
            <i class="el-icon-setting" />
            <span>基础设置</span>
          </el-menu-item>
          <el-menu-item index="2">
            <i class="el-icon-setting" />
            <span slot="title">全量备份</span>
          </el-menu-item>
          <el-menu-item index="3">
            <i class="el-icon-setting" />
            <span slot="title">全量备份记录</span>
          </el-menu-item>
          <el-menu-item index="4">
            <i class="el-icon-setting" />
            <span slot="title">集群回档</span>
          </el-menu-item>
          <el-menu-item index="5">
            <i class="el-icon-setting" />
            <span slot="title">集群扩容</span>
          </el-menu-item>
          <el-menu-item index="6">
            <i class="el-icon-setting" />
            <span slot="title">主备切换</span>
          </el-menu-item>
          <el-menu-item index="7">
            <i class="el-icon-setting" />
            <span slot="title">主备切换记录</span>
          </el-menu-item>
          <el-menu-item index="8">
            <i class="el-icon-setting" />
            <span slot="title">重做备机节点</span>
          </el-menu-item>
          <el-menu-item index="9">
            <i class="el-icon-setting" />
            <span slot="title">shard列表</span>
          </el-menu-item>
          <!-- <el-submenu index="6">
            <template slot="title">
              <i class="el-icon-menu"></i>
              <span>shard管理</span>
            </template>
            <el-menu-item-group>
              <el-menu-item index="6-1">shard列表</el-menu-item>
              <el-menu-item index="6-2">主备切换</el-menu-item>
              <el-menu-item index="6-2">重做备机节点</el-menu-item>
            </el-menu-item-group>
          </el-submenu> -->
          <el-menu-item index="10">
            <i class="el-icon-setting" />
            <span slot="title">计算节点列表</span>
          </el-menu-item>
          <el-menu-item index="11">
            <i class="el-icon-setting" />
            <span slot="title">设置延迟告警时间</span>
          </el-menu-item>
          <el-menu-item index="12">
            <i class="el-icon-setting" />
            <span slot="title">设置实例变量</span>
          </el-menu-item>
          <el-menu-item index="13">
            <i class="el-icon-setting" />
            <span slot="title">获取实例变量</span>
          </el-menu-item>
          <el-menu-item index="14">
            <i class="el-icon-setting" />
            <span slot="title">表重分布</span>
          </el-menu-item>
          <el-menu-item index="15">
            <i class="el-icon-setting" />
            <span slot="title">逻辑备份</span>
          </el-menu-item>
          <el-menu-item index="16">
            <i class="el-icon-setting" />
            <span slot="title">逻辑恢复</span>
          </el-menu-item>
        </el-menu>
      </el-col>
    </div>
    <div class="rightmenu">
      <component :is="componted" :listsent="listsent" @clusterId="clusterId" />
    </div>
  </div>
</template>

<script>
import CompList from '../onecluster/complist.vue'
import CommonSet from '../onecluster/commonset.vue'
import AllBackeUp from '../onecluster/allbackeup.vue'
import Expansion from '../onecluster/expansion.vue'
import Restore from '../onecluster/restore.vue'
import SwitchOver from '../onecluster/switchover.vue'
import BackUp from '../onecluster/backup.vue'
import StandbySwitch from '../onecluster/standby_switch.vue'
import ShardList from '../onecluster/shardlist.vue'
import RedoMachine from '../onecluster/redomachine.vue'
import SetAlarmTime from '../onecluster/set_alarm_time.vue'
import SetVariable from '../onecluster/setvariable.vue'
import GetVariable from '../onecluster/getvariable.vue'
import TableRepartition from '../onecluster/table_repartition.vue'
import LogicalBackup from '../onecluster/logical_backup.vue'
import Error from '../404.vue'

export default {
  components: {
    CompList,
    CommonSet,
    AllBackeUp,
    Expansion,
    Restore,
    SwitchOver,
    BackUp,
    StandbySwitch,
    ShardList,
    RedoMachine,
    SetAlarmTime,
    SetVariable,
    GetVariable,
    TableRepartition,
    LogicalBackup,
    Error
  },
  props: {
    oneList: { typeof: Object }
  },
  data() {
    return {
      listsent: [],
      componted: 'CommonSet'
    }
  },
  created() {
    this.listsent = this.oneList
  },
  methods: {
    selected(index) {
      if (index === '1') {
        this.componted = 'CommonSet'
      } else if (index === '2') {
        this.componted = 'AllBackeUp'
      } else if (index === '3') {
        this.componted = 'BackUp'
      } else if (index === '4') {
        this.componted = 'Restore'
      } else if (index === '5') {
        this.componted = 'Expansion'
      } else if (index === '6') {
        this.componted = 'StandbySwitch'
      } else if (index === '7') {
        this.componted = 'SwitchOver'
      } else if (index === '8') {
        this.componted = 'RedoMachine'
      } else if (index === '9') {
        this.componted = 'ShardList'
      } else if (index === '10') {
        this.componted = 'CompList'
      } else if (index === '11') {
        this.componted = 'SetAlarmTime'
      } else if (index === '12') {
        this.componted = 'SetVariable'
      } else if (index === '13') {
        this.componted = 'GetVariable'
      } else if (index === '14') {
        this.componted = 'TableRepartition'
      } else if (index === '15') {
        this.componted = 'LogicalBackup'
      } else if (index === '16') {
        this.componted = 'Expansion'
      } else {
        this.componted = 'Error'
      }
    },
    clusterId(value) {
      this.$emit('getId', value)
    }
  }
}

</script>

<style lang="less">
.el-tabs--border-card > .el-tabs__content {
  padding: 0px;
}

.all {
  height: 100%;
  position: absolute;
  width: 100%;
  display: flex;
}

.el-tabs__content {
  height: calc(100vh - 102px);
  overflow-y: auto;
}

.leftmenu {
  /*width: 13%;*/
  height: 100%;
  min-width: 145px;
  overflow: auto;
}

.rightmenu {
  width: 87%;
  height: 100%;
  overflow: auto;
}

.el-col-3 {
  width: 100%;
  height: 100%;
}

.el-menu {
  height: 100%;
}

/* 修改滚动条宽度 */
::-webkit-scrollbar {
  width: 5px;
  height: 5px;
}

::-webkit-scrollbar-thumb {
  border-radius: 5px;
  -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, .3);
  box-shadow: inset 0 0 6px rgba(0, 0, 0, .3);
  background-color: rgba(0, 0, 0, 0.1);
}
</style>
