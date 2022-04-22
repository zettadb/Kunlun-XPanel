<template>
  <div style="text-align: center">

    <el-transfer
      style="text-align: left; display: inline-block"
      v-model="value"
      filterable
      :render-content="renderFunc"
      :titles="['库存物资', '出库物资']"
      
      :format="{
        noChecked: '${total}',
        hasChecked: '${checked}/${total}'
      }"
      @change="handleChange"
      :data="data">
      <el-button class="transfer-footer" slot="left-footer" size="small">操作</el-button>
      <el-button class="transfer-footer" slot="right-footer" size="small">操作</el-button>
    </el-transfer>
  </div>
</template>

<style>
  .transfer-footer {
    margin-left: 20px;
    padding: 6px 5px;
  }
</style>

<script>
import {getClusterList} from '@/api/cluster/list'
  export default {
    data() {
      const generateData = _ => {
        const data = [];
        for (let i = 1; i <= 15; i++) {
          data.push({
            key: i,
            label: `备选项 ${ i }`,
            // disabled: i % 4 === 0
          });
        }
        return data;
      };
      return {
        data: [],
        value: [1],
        value4: [1],
         listQuery: {
          pageNo: 1,
          pageSize: 10,
          name: ''
        },
        renderFunc(h, option) {
          return <span>{ option.key } - { option.label }</span>;
        }
      };
    },
    created(){
      this.getList()
    },
    methods: {
      handleChange(value, direction, movedKeys) {
        console.log(value, direction, movedKeys);
      },
      getList() {
        this.listLoading = true
        let queryParam = Object.assign({}, this.listQuery)
        queryParam.effectCluster= sessionStorage.getItem('affected_clusters');
        queryParam.apply_all_cluster= sessionStorage.getItem('apply_all_cluster');
        //模糊搜索
        getClusterList(queryParam).then(response => {
          this.data = response.list;
          this.total = response.total;
          setTimeout(() => {
            this.listLoading = false
          }, 0.5 * 1000)
        });
    },
    }
  };
</script>