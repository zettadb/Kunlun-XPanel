<template>
  <div class="icons-container">
    <el-tabs type="border-card" v-model="activeName" @tab-click="handleClick">
      <el-tab-pane label="集群展示" name="first">
        <Cshow v-if="tabs.first"/> 
      </el-tab-pane>
      <el-tab-pane label="集群列表信息" name="second">
          <List v-if="tabs.second"/>
          <!-- <Cshow/>  -->
      </el-tab-pane>
    </el-tabs>
  </div>
</template>

<script>
import List from '../cluster/list.vue'
import Cshow from './cshow.vue'
export default {
   components: {List,Cshow},
  data() {
    return {
      activeName: "first",
      tabs: {
        first: true,
        second: false,
      },
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
