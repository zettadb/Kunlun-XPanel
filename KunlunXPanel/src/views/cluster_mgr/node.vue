
<template>
  <div>
    <!-- <div style="padding-top:6px;padding-left: 30px;padding-right:30px;border-bottom: #efefef solid 1px; width:200px;">
        <el-input  v-model="this.hostaddr" disabled></el-input>
    </div> -->
    <div style="height:calc(100vh - 150px);" ref="myPage">
      <SeeksRelationGraph ref="seeksRelationGraph" :options="graphOptions" :on-node-click="onNodeClick"
        :on-line-click="onLineClick">
        <div slot="node" slot-scope="{node}" @mouseover="showNodeTips(node, $event)"
          @mouseout="hideNodeTips(node, $event)">
          <div>
            <i :style="{ color: node.data.color, fontSize: 40 + 'px' }" :class="node.data.icon" />
            <!-- <img :src="compnode"  style="width:20%;"/>  -->
            <!-- <svg-icon :icon-class="node.data.icon" style="width:2rem;height:2rem;"/> -->
            <p v-text="node.text"></p>
          </div>
        </div>
      </SeeksRelationGraph>
    </div>
    <div v-show="isShowNodeMenuPanel"
      :style="{ left: nodeMenuPanelPosition.x + 'px', top: nodeMenuPanelPosition.y + 'px' }"
      style="z-index: 999;padding:10px;background-color: #ffffff;border:#eeeeee solid 1px;box-shadow: 0px 0px 8px #cccccc;position: absolute;">
      <div style="line-height: 25px;padding-left: 10px;color: #888888;font-size: 10px;">对{{ nodeName }}进行操作：</div>
      <div class="c-node-menu-item" @click.stop="doAction('主备切换')">主备切换</div>
    </div>
    <!--  状态框 -->
    <el-dialog :visible.sync="dialogStatusVisible" custom-class="single_dal_view" width="400px"
      :close-on-click-modal="false">
      <div class="block">
        <el-timeline>
          <el-timeline-item v-for="(activity, index) in activities" :key="index" :icon="activity.icon"
            :type="activity.type" :color="activity.color" :size="activity.size" :timestamp="activity.timestamp">
            {{ activity.content }}
          </el-timeline-item>
        </el-timeline>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import SeeksRelationGraph from 'relation-graph'
import { getClusterMgrList, raftClusterMgr } from '@/api/cluster_mgr/list'
import { version_arr, timestamp_arr } from "@/utils/global_variable"
import { getNowDate } from "@/utils";
import { getEvStatus } from '@/api/cluster/list'
// import {imgUrl} from '@/assets/images/compnode.png'
export default {
  name: 'ClusterMgr',
  components: { SeeksRelationGraph },
  data() {

    return {
      compnode: require('@/assets/images/compnode.png'),
      comps: require('@/assets/images/compnode.png'),
      isShowNodeTipsPanel: false,
      nodeMenuPanelPosition: { x: 0, y: 0 },
      shardName: false,
      currentCase: '横向树状图谱',
      isShowCodePanel: false,
      graphOptions: {
        debug: true,
        'backgrounImageNoRepeat': true,
        'layouts': [
          {
            'label': '树',
            'layoutName': 'tree',
            'layoutClassName': 'seeks-layout-center',
            'defaultNodeShape': 0,
            'defaultLineShape': 1,
            'from': 'left',
            // 通过这4个属性来调整 tree-层级距离&节点距离
            'min_per_width': '200',
            'max_per_width': '400',
            'min_per_height': '200',
            'max_per_height': '200',
            'levelDistance': '' // 如果此选项有值，则优先级高于上面那4个选项
          }
        ],
        'defaultLineMarker': {//默认的线条箭头样式，
          'markerWidth': 12,
          'markerHeight': 12,
          'refX': 6,
          'refY': 6,
          'data': 'M2,2 L10,6 L2,10 L6,6 L2,2'
        },
        // "defaultExpandHolderPosition": "right",//右边展开关闭
        'defaultNodeShape': 1,//	默认的节点形状，0:圆形；1:矩形
        // 'defaultNodeWidth': '100',//默认节点的宽度
        'defaultLineShape': 4,//默认的线条样式（1:直线/2:样式2/3:样式3/4:折线/5:样式5/6:样式6)
        'defaultJunctionPoint': 'lr',//连接与节点的接触方式，lr为左右
        'defaultNodeBorderWidth': 0,//默认的节点边框粗细（像素）
        'defaultLineColor': 'rgba(149, 195, 243, 1)',//线条颜色
        //'defaultNodeColor': 'rgba(255, 255, 255, 1)',//节点颜色
        'defaultNodeColor': 'rgba(176, 210, 237, 1)',//节点颜色
        'defaultNodeFontColor': 'rgba(0, 0, 0, 1)',//字体的颜色
        'defaultNodeBorderColor': 'rgba(255, 255, 255, 1)'
        // allowSwitchLineShape: true,//显示切换线条形状的按钮
        // allowSwitchJunctionPoint: true,//切换连接点位置的按钮
        //defaultJunctionPoint: 'border'
        // 这里可以参考"Graph 图谱"中的参数进行设置
      },
      //hostaddr: "", //存放list页面传过来的值
      list: [],
      isShowNodeMenuPanel: false,
      nodeName: '',
      dialogStatusVisible: false,
      timer: null,
      activities: []
    }
  },
  created() {
    //this.getParams(); 
  },
  mounted() {
    this.showSeeksGraph()
  },
  methods: {
    showNodeTips(nodeObject, $event) {
      this.currentNode = nodeObject
      var _base_position = this.$refs.myPage.getBoundingClientRect()
      //console.log('showNodeMenus:', $event, _base_position)
      // if(this.currentNode.id=='ip'||this.currentNode.id=='storage'||this.currentNode.id=='comps'){
      //   this.isShowNodeTipsPanel = false
      // }else{
      //   if(this.currentNode.id.indexOf('snode')!=-1){this.shardName=true;}else{this.shardName=false;}
      //   this.isShowNodeTipsPanel = true
      //   this.nodeMenuPanelPosition.x = $event.clientX - _base_position.x + 10
      //   this.nodeMenuPanelPosition.y = $event.clientY - _base_position.y + 10
      // }
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
    // getParams() {
    //   this.hostaddr = this.$route.query.hostaddr
    // },
    showSeeksGraph(query) {
      getClusterMgrList().then(response => {
        let arr = response.list;
        for (let i = 0; i < arr.nodes.length; i++) {
          let param = arr.nodes[i];
          //console.log(param);
          if (param.id == 'cluster_mgr') {
            this.$set(param, 'data', { 'icon': 'iconfont  icon-computer', 'color': '#533ade' })
          }
          if (param.id.indexOf('cnode') != -1) {
            this.$set(param.data, 'icon', 'iconfont icon-snode')
            this.$set(param.data, 'color', '#e98f36')
            if (param.data.member_state == 'source') {
              this.$set(param.data, 'color', '#ff0000')
            } else if (param.data.member_state == 'replica') {
              this.$set(param.data, 'color', '#e98f36')
            } else {
              this.$set(param.data, 'color', '#acacac')
            }
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
      this.currentNode = nodeObject
      var _base_position = this.$refs.myPage.getBoundingClientRect()
      if (this.currentNode.data.member_state == 'replica') {
        this.isShowNodeMenuPanel = true
        this.nodeName = this.currentNode.text;
        this.nodeMenuPanelPosition.x = $event.clientX - _base_position.x
        this.nodeMenuPanelPosition.y = $event.clientY - _base_position.y
      } else {
        this.isShowNodeMenuPanel = false
      }
    },
    onLineClick(lineObject, $event) {
      console.log('onLineClick:', lineObject)
    },
    doAction(actionName) {
      if (actionName == '主备切换') {
        const temp = {};
        temp.user_name = sessionStorage.getItem('login_username');
        temp.job_id = '';
        temp.version = version_arr[0].ver;
        temp.job_type = 'raft_mission';
        temp.timestamp = timestamp_arr[0].time + '';
        const paras = {}
        paras.task_type = 'transfer_leader';
        paras.target_leader = this.currentNode.data.ip + ':' + this.currentNode.data.port;
        temp.paras = paras;
        //console.log(temp);return;
        raftClusterMgr(temp).then((response) => {
          let res = response;
          if (res.status == 'accept') {
            this.isShowNodeMenuPanel = false
            this.dialogStatusVisible = true;
            this.activities = [];
            const newArr = {
              content: '正在进行主备切换',
              timestamp: getNowDate(),
              size: 'large',
              type: 'primary',
              icon: 'el-icon-more'
            };
            this.activities.push(newArr);
            //调获取状态接口
            let i = 0; this.timer = null;
            this.timer = setInterval(() => {
              this.getStatus(this.timer, res.job_id, i++)
            }, 1000)
          }
        });
      }
    },
    getStatus(timer, data, i) {
      setTimeout(() => {
        const postarr = {};
        postarr.job_type = 'get_status';
        postarr.version = version_arr[0].ver;
        postarr.job_id = data;
        postarr.timestamp = timestamp_arr[0].time + '';
        postarr.paras = {};
        getEvStatus(postarr).then((res) => {
          if (res.status == 'done' || res.status == 'failed') {
            clearInterval(timer);
            //this.info=res.error_info;
            if (res.status == 'done') {
              const newArrdone = {
                content: res.error_info,
                timestamp: getNowDate(),
                color: '#0bbd87',
                icon: 'el-icon-circle-check'
              };
              this.activities.push(newArrdone)
              this.getList();
              this.dialogStatusVisible = false;
            } else {
              const newArr = {
                content: res.error_info,
                timestamp: getNowDate(),
                color: 'red',
                icon: 'el-icon-circle-close'
              };
              this.activities.push(newArr);
              //this.installStatus = true;
            }
          } else {
            const newArrgoing = {
              content: res.error_info,
              timestamp: getNowDate(),
              color: '#0bbd87'
            };
            this.activities.push(newArrgoing)
            //this.info=res.error_info;
            //this.installStatus = true;
          }
        });
        if (i >= 86400) {
          clearInterval(timer);
        }
      }, 0)
    }
  }
}
</script>

<style lang="scss">
.rel-node-checked {
  box-shadow: 0px 0px 10px #ffffff !important;
}

.c-mini-namefilter {
  height: 0px !important;
  margin-top: 0px !important;
}

.c-node-menu-item {
  line-height: 30px;
  padding-left: 10px;
  cursor: pointer;
  color: #444444;
  font-size: 14px;
  border-top: #efefef solid 1px;
}

.c-node-menu-item:hover {
  background-color: rgba(66, 153, 187, 0.2);
}
</style>
