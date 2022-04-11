
<template>
  <div>
    <div style="padding-top:6px;padding-left: 30px;padding-right:30px;border-bottom: #efefef solid 1px; width:200px;">
        <el-input  v-model="this.hostaddr" disabled></el-input>
    </div>
    <div style="height:calc(100vh - 150px);" ref="myPage"> 
      <SeeksRelationGraph
        ref="seeksRelationGraph"
        :options="graphOptions"
        :on-node-click="onNodeClick"
        :on-line-click="onLineClick">
        <div slot="node" slot-scope="{node}" @mouseover="showNodeTips(node, $event)" @mouseout="hideNodeTips(node, $event)">
          <div>
            <i :style="{ color: node.data.color, fontSize: 40 + 'px' }" :class="node.data.icon"/>
            <!-- <img :src="compnode"  style="width:20%;"/>  -->
            <!-- <svg-icon :icon-class="node.data.icon" style="width:2rem;height:2rem;"/> -->
            <p v-text="node.text"></p>
          </div>
        </div>
        
      </SeeksRelationGraph>
    </div>
   <div v-if="isShowNodeTipsPanel" :style="{left: nodeMenuPanelPosition.x + 'px', top: nodeMenuPanelPosition.y + 'px' }" style="z-index: 999;padding:10px;background-color: #ffffff;border:#eeeeee solid 1px;box-shadow: 0px 0px 8px #cccccc;position: absolute;">
      <div class="c-node-menu-item">ip:{{currentNode.data.ip}}</div>
      <div class="c-node-menu-item">port:{{currentNode.text}}</div>
      <div class="c-node-menu-item" v-if="shardName">shard名称:{{currentNode.data.shard_name}}</div>
      <div class="c-node-menu-item">集群名称:{{currentNode.data.cluster_name}}</div>
    </div>
  </div>
</template>

<script>
import SeeksRelationGraph from 'relation-graph'
import {getMachineNodesList} from '@/api/machine/list'
// import {imgUrl} from '@/assets/images/compnode.png'
export default {
  name: 'Node',
  components: { SeeksRelationGraph },
  data() {
    return {
      compnode:require('@/assets/images/compnode.png'),
      comps:require('@/assets/images/compnode.png'),
      isShowNodeTipsPanel: false,
       nodeMenuPanelPosition: { x: 0, y: 0 },
       shardName:false,
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
            // 'defaultJunctionPoint': 'border',
            'defaultNodeShape': 0,
            // 'defaultLineShape': 4,
            'min_per_width': 40,
            'max_per_width': 70,
            'min_per_height':180
          }
        ],
        'defaultLineMarker': {
          'markerWidth': 10,
          'markerHeight': 10,
          'refX': 6,
          'refY': 6,
          'data': 'M2,2 L10,6 L2,10 L6,6 L2,2'
        },
        'defaultNodeShape': 1,
        //'defaultNodeWidth': '100',
        'defaultLineShape': 4,
        'defaultJunctionPoint': 'tb',
        'defaultNodeBorderWidth': 0,
        'defaultLineColor': 'rgba(149, 195, 243, 1)',
        'defaultNodeColor': 'rgba(255, 255, 255, 1)',
        'defaultNodeFontColor':'rgba(0, 0, 0, 1)',
        'defaultNodeBorderColor':'rgba(255, 255, 255, 1)',
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
    showNodeTips(nodeObject, $event) {
      this.currentNode = nodeObject
      var _base_position = this.$refs.myPage.getBoundingClientRect()
      //console.log('showNodeMenus:', $event, _base_position)
      if(this.currentNode.id=='ip'||this.currentNode.id=='storage'||this.currentNode.id=='comps'){
        this.isShowNodeTipsPanel = false
      }else{
        if(this.currentNode.id.indexOf('snode')!=-1){this.shardName=true;}else{this.shardName=false;}
        this.isShowNodeTipsPanel = true
        this.nodeMenuPanelPosition.x = $event.clientX - _base_position.x + 10
        this.nodeMenuPanelPosition.y = $event.clientY - _base_position.y + 10
      }
    },
    hideNodeTips(nodeObject, $event) {
      this.isShowNodeTipsPanel = false
    },
    showNodeMenus(nodeObject, $event) {
      this.currentNode = nodeObject
      var _base_position = this.$refs.myPage.getBoundingClientRect()
      this.isShowNodeMenuPanel = true
      // if((this.currentNode.data.name=='shard'&&this.shard_drop_priv!=='Y')||this.currentNode.id=='shards'||this.currentNode.id=='cluster'||this.currentNode.id=='comps_name'||this.currentNode.id=='comps'){
      //   this.isShowNodeMenuPanel = false
      // }else{
      //   this.isShowNodeMenuPanel = true
      //   this.nodeName=this.currentNode.text;
      //   if(this.nodeName.indexOf('shard') !=-1) {
      //     this.type='shard'
      //   }else {
      //     this.type='node'
      //   }
        this.nodeMenuPanelPosition.x = $event.clientX - _base_position.x
        this.nodeMenuPanelPosition.y = $event.clientY - _base_position.y
      // }
    },
    getParams() {
      this.hostaddr = this.$route.query.hostaddr
    },
    showSeeksGraph(query) {
      getMachineNodesList(this.hostaddr).then(response => {
          let arr = response.list;
          for (let i = 0; i < arr.nodes.length; i++) {
            let param = arr.nodes[i];
            if(param.id=='ip'){
               this.$set(param, 'data', {'icon':'iconfont  icon-computer','color':'#533ade'})
            }
            if(param.id=='comps'){
               this.$set(param, 'data', {'icon':'iconfont icon-comps','color':'#1196db'})
            }
            if(param.id=='storage'){
               this.$set(param, 'data', {'icon':'iconfont icon-storage','color':'#e98f36'})
            }
            if(param.id.indexOf('cnode')!=-1){
               this.$set(param.data, 'icon', 'iconfont icon-compnode')
               this.$set(param.data, 'color', '#1196db')
            }
            if(param.id.indexOf('snode')!=-1){
               this.$set(param.data, 'icon', 'iconfont icon-snode')
               this.$set(param.data, 'color', '#e98f36')
            }
          }
          //console.log(arr);return
          this.list = arr;
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
.rel-node-checked{
  box-shadow: 0px 0px 10px #ffffff !important;
}
.c-mini-namefilter{
  height: 0px !important;
  margin-top:0px !important;
}

</style>