<template>
  <div class="icons-container">
    <el-tabs type="border-card" v-model="activeName" @tab-click="handleClick"  @tab-remove="removeTab">
      <el-tab-pane label="集群列表信息" name="second" >
          <List v-if="tabs.second" @updateActiveName="updateActiveName"  :comment="clusterTab"/>
      </el-tab-pane>
      <el-tab-pane label="集群展示" name="first">
        <Cshow v-if="tabs.first"/> 
      </el-tab-pane>
      <el-tab-pane label="异常集群列表" name="three">
        <ErrorList v-if="tabs.three"/>
      </el-tab-pane>
      <!-- <el-tab-pane :label="cluster_id" name="four" v-if="tabs.four">
          <OneClusterList v-if="tabs.four" :oneList="InfoList"/>
      </el-tab-pane> -->
      <!-- <el-tabs v-model="editableTabsValue" type="card" closable @tab-remove="removeTab"> -->
        <!-- v-for="(item,index) in editableTabs" -->
      <el-tab-pane
        v-for="(item) in editableTabs"
        :key="item.name"
        :label="item.title"
        :name="item.name"
        closable
      >
        <OneClusterList v-if="item.name" :oneList="InfoList" @getId="clusterId"/>
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
      cluster_id:'',
      editableTabs:[],
      active:'',
      clusterTab:{
        editableTabs:[],
        active:'',
      }
    }
  },
  methods: {
    removeTab(targetName) {
      let tabs = this.editableTabs;
      let activeName = this.activeName;
      if (activeName === targetName) {
        tabs.forEach((tab, index) => {
          if (tab.name === targetName) {
            let nextTab = tabs[index + 1] || tabs[index - 1];
            if (nextTab) {
              activeName = nextTab.name;
            }else{
              activeName='second';
            }
          }
        });
      }
      this.activeName = activeName;
      this.editableTabs = tabs.filter(tab => tab.name !== targetName);
      if(this.activeName=='second'){
        this.tabs.second=true;
      }
      this.clusterTab={
        active:this.active,
        editableTabs:this.editableTabs
      }
    },
     handleClick(tab) {
      //console.log(tab);
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
        default: this.switchTab(this.activeName);
        // case "four":
        //   this.switchTab("four");
        //   break;
      }

    },
    switchTab(tab) {
      for (let key in this.tabs) {
        console.log(key);
        if (key === tab) {
         this.tabs[key] = true;
        } else {
          this.tabs[key] = false;
        }
      }
    },
    updateActiveName(data) {
      // 修改activeName的名称
      //this.activeName = data.activeName
      this.InfoList= data.list;
      this.cluster_id=data.list.id+'集群设置'
      if(this.activeName=='four'){
        this.tabs.four=true;
        this.tabs.second=false;
      }
      let aticeindex=0;
      data.tab.forEach((tab,index) => {
        if(tab.name===data.list.id+''){
          aticeindex=index;
        }
      })
      this.activeName = data.tab[aticeindex].name
      this.editableTabs=data.tab;
      this.clusterTab={
        active:this.active,
        editableTabs:this.editableTabs
      }
    },
    clusterId(value){
      this.removeTab(value);
      this.tabs.second=true;
      this.active='delcluster';
      //this.activeName='second'
    }
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
