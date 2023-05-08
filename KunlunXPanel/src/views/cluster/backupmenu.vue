<template>
 <div class="icons-container">
  <el-tabs v-model="activeName" @tab-click="handleClick" type="border-card" >
    <el-tab-pane label="逻辑备份" name="first">
      <LogicalBackup  />
    </el-tab-pane>
    <el-tab-pane label="物理备份" name="second">
      <BackUp  />
    </el-tab-pane>
  </el-tabs>
 </div>
</template>
<script>
  import BackUp from "../cluster/backup.vue";
  import LogicalBackup from  "../cluster/logicalbp.vue";
  export default {
    components: { BackUp ,LogicalBackup},
    data() {
      return {
        activeName: 'first'
      };
    },
    methods: {
      handleClick(tab, event) {
        console.log(tab, event);
      }
    }
  };
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