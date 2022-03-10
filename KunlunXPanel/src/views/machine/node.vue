
<template>
  <div>
    <div style="height:50px;padding-top:6px;padding-left: 30px;padding-right:30px;border-bottom: #efefef solid 1px; width:200px;">
      <!-- <el-radio-group v-model="currentCase" size="small" @change="$router.push('/demo/layout-tree2')"> -->
        <el-input  v-model="this.hostaddr" disabled></el-input>
        <!-- <el-radio-button label="纵向树状图谱" ></el-radio-button> -->
      <!-- </el-radio-group> -->
      <!-- <el-button type="success" class="c-show-code-button"><el-link href="https://github.com/seeksdream/relation-graph/blob/master/doc/demo/Demo4LayoutTree.vue" target="_blank" style="color: #ffffff;">查看代码</el-link></el-button> -->
    </div>
    <div style="height:calc(100vh - 150px);">
      <SeeksRelationGraph
        ref="seeksRelationGraph"
        :options="graphOptions"
        :on-node-click="onNodeClick"
        :on-line-click="onLineClick">
        
      </SeeksRelationGraph>
    </div>
  
  </div>
</template>

<script>
import SeeksRelationGraph from 'relation-graph'
import {getMachineNodesList} from '@/api/machine/list'
export default {
  name: 'Node',
  components: { SeeksRelationGraph },
  data() {
    return {
      currentCase: '横向树状图谱',
      isShowCodePanel: false,
      graphOptions: {
        //'backgrounImage': 'http://ai-mark.cn/images/ai-mark-desc.png',
        'backgrounImageNoRepeat': true,
        'layouts': [
          {
            'label': '中心',
            'layoutName': 'tree',
            'layoutClassName': 'seeks-layout-center',
            'defaultJunctionPoint': 'border',
            'defaultNodeShape': 0,
            'defaultLineShape': 1,
            'min_per_width': 40,
            'max_per_width': 70,
            'min_per_height': 100
          }
        ],
        'defaultLineMarker': {
          'markerWidth': 12,
          'markerHeight': 12,
          'refX': 6,
          'refY': 6,
          'data': 'M2,2 L10,6 L2,10 L6,6 L2,2'
        },
        'defaultNodeShape': 1,
        //'defaultNodeWidth': '100',
        'defaultLineShape': 2,
        'defaultJunctionPoint': 'tb',
        'defaultNodeBorderWidth': 0,
        'defaultLineColor': 'rgba(149, 195, 243, 1)',
        'defaultNodeColor': 'rgba(64, 158, 255, 1)',
        //  'defaultNodeHeight': '90'
      },
      hostaddr: "", //存放list页面传过来的值
      list:[]
    }
  },
  created(){
    this.getParams(); 
  },
  mounted() {
    this.showSeeksGraph()
  },
  methods: {
    getParams() {
            this.hostaddr = this.$route.query.hostaddr
    },
    showSeeksGraph(query) {
      getMachineNodesList(this.hostaddr).then(response => {
          this.list = response.list;
           var __graph_json_data = this.list;
          this.$refs.seeksRelationGraph.setJsonData(__graph_json_data, (seeksRGGraph) => {
          // 这些写上当图谱初始化完成后需要执行的代码
          })
          setTimeout(() => {
            this.listLoading = false
          }, 0.5 * 1000)
      });
          // var __graph_json_data = {"rootId": "1","nodes": [{ "id": "1", "text": "192.168.0.127" }, { "id": "b1", "text": "存储节点" }, { "id": "b1-1", "text": "b1-1" }, { "id": "b1-2", "text": "b1-2" }, { "id": "b1-3", "text": "b1-3" }, { "id": "b1-4", "text": "b1-4" }, { "id": "b1-5", "text": "b1-5" }, { "id": "b1-6", "text": "b1-6" }, { "id": "b2", "text": "计算节点" }, { "id": "b2-1", "text": "b2-1" }, { "id": "b2-2", "text": "b2-2" }, { "id": "b2-3", "text": "b2-3" }, { "id": "b2-4", "text": "b2-4" }, { "id": "b3-1", "text": "b3-1" }, { "id": "b3-2", "text": "b3-2" }, { "id": "b4-1", "text": "b4-1" }, { "id": "b4-2", "text": "b4-2" }], "links": [{ "from": "1", "to": "b1" },{ "from": "1", "to": "b2" },  { "from": "b1", "to": "b1-1" }, { "from": "b1", "to": "b1-2" }, { "from": "b1", "to": "b1-3" }, { "from": "b1", "to": "b1-4" }, { "from": "b1", "to": "b1-5" }, { "from": "b1", "to": "b1-6" }, { "from": "b2", "to": "b2-1" }, { "from": "b2", "to": "b2-2" }, { "from": "b2", "to": "b2-3" }, { "from": "b2", "to": "b2-4" },{ "from": "b1-1", "to": "b3-1" },{ "from": "b1-2", "to": "b3-1" },{ "from": "b1-3", "to": "b3-1" },{ "from": "b1-4", "to": "b3-2" },{ "from": "b1-5", "to": "b3-2" },{ "from": "b1-6", "to": "b3-2" },{ "from": "b3-2", "to": "b4-2" },{ "from": "b3-1", "to": "b4-1" }]};
      //      var __graph_json_data = this.list;
      //     this.$refs.seeksRelationGraph.setJsonData(__graph_json_data, (seeksRGGraph) => {
      //   // 这些写上当图谱初始化完成后需要执行的代码
      // })
    },
    onNodeClick(nodeObject, $event) {
      console.log('onNodeClick:', nodeObject)
    },
    onLineClick(lineObject, $event) {
      console.log('onLineClick:', lineObject)
    },
  
  }
}
</script>

<style lang="scss">
</style>