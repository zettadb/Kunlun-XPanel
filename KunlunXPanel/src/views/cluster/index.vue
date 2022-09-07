<template>
  <div class="icons-container">
    <el-tabs type="border-card" v-model="activeName" @tab-click="handleClick">
      <el-tab-pane label="集群列表信息" name="second" >
          <List v-if="tabs.second" @updateActiveName="updateActiveName"/>
      </el-tab-pane>
      <el-tab-pane label="集群展示" name="first">
        <Cshow v-if="tabs.first"/> 
      </el-tab-pane>
      <el-tab-pane label="异常集群列表" name="three">
          <ErrorList v-if="tabs.three"/>
      </el-tab-pane>
      <el-tab-pane :label="cluster_id" name="four" v-if="tabs.four">
          <OneClusterList v-if="tabs.four" :oneList="InfoList"/>
      </el-tab-pane>
    </el-tabs>
  </div>
</template>

<script>
import List from '../cluster/list.vue'
import Cshow from './cshow.vue'
import ErrorList from '../cluster/errorlist.vue'
import OneClusterList from '../cluster/oneclusterlist.vue'
export default {
   components: {List,Cshow,ErrorList,OneClusterList},
  data() {
    return {
      activeName: "second",
      tabs: {
        first: false,
        second:true,
        three:false,
        four:false
      },
      InfoList:[],
      cluster_id:''
    }
  },
  methods: {
     handleClick(tab) {
      this.activeName = tab.name;
      switch (this.activeName) {
        case "first":
          this.switchTab("first");
          break;
        case "second":
          this.switchTab("second");
          break;
        case "three":
          this.switchTab("three");
          break;
        case "four":
          this.switchTab("four");
          break;
      }

    },
    switchTab(tab) {
      for (let key in this.tabs) {
        if (key === tab) {
         this.tabs[key] = true;
        } else {
          this.tabs[key] = false;
        }
      }
    },
    updateActiveName(data) {
      // 修改activeName的名称
      this.activeName = data.activeName
      this.InfoList= data.list;
      this.cluster_id=data.list.id+'集群设置'
      if(this.activeName=='four'){
        this.tabs.four=true;
        this.tabs.second=false;
      }
    },
  }
}
</script>

<style lang="scss" scoped>
.icons-container {
  margin: 10px 20px 0;
  overflow: hidden;

  .grid {
    position: relative;
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
  }

  .icon-item {
    margin: 20px;
    height: 85px;
    text-align: center;
    width: 100px;
    float: left;
    font-size: 30px;
    color: #24292e;
    cursor: pointer;
  }

  span {
    display: block;
    font-size: 16px;
    margin-top: 10px;
  }

  .disabled {
    pointer-events: none;
  }
}
</style>
