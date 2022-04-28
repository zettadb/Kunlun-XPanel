 <template>
  <div>
    <el-radio-group  v-model="currentCase" size="small" @change="agreeChange" v-show="g_loading">
    <el-radio v-for="cluster in clusters" :label="cluster.id" :key="cluster.id">{{cluster.nick_name}}</el-radio>
    </el-radio-group>
    <div v-text="info" v-show="installStatus===true" class="info"></div>
    <div class="nodata" v-show="nodataShow">暂无数据</div>
    <div ref="myPage" style="margin-top:0px;width: calc(100(100% - 10px);height:calc(100vh - 160px);" v-show="g_loading" @click="isShowNodeMenuPanel = false">
      <SeeksRelationGraph
        ref="seeksRelationGraph"
        :options="graphOptions"
        :on-node-click="onNodeClick"
        :on-line-click="onLineClick" :on-node-expand="onNodeExpand" :on-node-collapse="onNodeCollapse">
        <div slot="node" slot-scope="{node}"  @click="showNodeMenus(node, $event)" @contextmenu.prevent.stop="showNodeMenus(node, $event)" >
          <div>
            <i :style="{ color: node.data.color, fontSize: 40 + 'px' }" :class="node.data.icon"/>
            <p v-text="node.text" style="word-wrap: break-word;word-break: break-all;"></p>
          </div>
        </div>
      </SeeksRelationGraph>
    </div>
    <div v-show="isShowNodeMenuPanel&&g_loading" :style="{left: nodeMenuPanelPosition.x + 'px', top: nodeMenuPanelPosition.y + 'px' }" style="z-index: 999;padding:10px;background-color: #ffffff;border:#eeeeee solid 1px;box-shadow: 0px 0px 8px #cccccc;position: absolute;">
      <div style="line-height: 25px;padding-left: 10px;color: #888888;font-size: 10px;">对{{nodeName}}进行操作：</div>
      <!-- <div class="c-node-menu-item" v-if="type==='shard'&& (storage_node_create_priv==='Y')" @click.stop="doAction('新增存储节点')">新增存储节点</div> -->
      <!-- <div class="c-node-menu-item" v-if="type==='shard'&& (storage_node_create_priv==='Y')" @click.stop="doAction('重做备机存储节点')">重做备机存储节点</div> -->
      <!-- <div class="c-node-menu-item" v-if="type==='cluster'&& (shard_create_priv==='Y')" @click.stop="doAction('新增存储集群')">新增存储集群</div> -->
      <!-- <div class="c-node-menu-item" v-if="type==='cluster'&& (compute_node_create_priv==='Y')" @click.stop="doAction('新增计算节点')">新增计算节点</div> -->
      <div class="c-node-menu-item" v-if="(type==='cnode'||type==='snode')&& (storage_enable_priv==='Y'||compute_enable_priv==='Y')" @click.stop="doAction('启用')">启用</div>
      <div class="c-node-menu-item"  v-if="(type==='cnode'||type==='snode')&& (storage_disable_priv==='Y'||compute_disable_priv==='Y')" @click.stop="doAction('禁用')">禁用</div>
      <div class="c-node-menu-item"  v-if="(type==='cnode'||type==='snode')&& (storage_disable_priv==='Y'||compute_disable_priv==='Y')" @click.stop="doAction('重启')">重启</div>
      <div class="c-node-menu-item"  v-if="(type==='shard'&& shard_drop_priv==='Y')||((type==='cnode'||type==='snode')&& compute_node_drop_priv==='Y')||((type==='cnode'||type==='snode')&& storage_node_drop_priv==='Y')" @click.stop="doAction('删除')">删除</div>
      <div class="c-node-menu-item"  v-if="(type==='cnode'||type==='snode')" @click.stop="doAction('进入')">进入</div>
      <!-- <div class="c-node-menu-item"  v-if="user_name=='super_dba'&&(type==='cnode'||type==='snode')" @click.stop="doAction('设置全局变量')">设置全局变量</div> -->
    </div>
    <!--新增存储集群-->
    <el-dialog title="新增存储集群" :visible.sync="dialogShardVisible" custom-class="single_dal_view">
      <el-form
        ref="restoreForm"
        :model="shardtemp"
        :rules="rules"
        label-position="left"
        label-width="110px"
      >
      <el-form-item label="选择计算机:" prop="machinelist" >
        <el-checkbox-group 
        v-model="shardtemp.machinelist"
        :min="minMachine"
        :max="machineTotal" >
          <el-checkbox v-for="machine in machines" :label="machine.hostaddr" :key="machine.id">{{machine.hostaddr}}</el-checkbox>
        </el-checkbox-group>
      </el-form-item>
        <el-form-item label="集群名称:" prop="old_cluster_name">
          <el-input v-model="shardtemp.old_cluster_name" :disabled="true" />
        </el-form-item>
        <el-form-item label="副本数:" prop="snode_count" >
           <el-input  v-model="shardtemp.snode_count" placeholder="副本数至少是3，且不能大于所选计算机数"  :disabled="dialogStatus==='detail'"/>
        </el-form-item>
      </el-form>

        <div slot="footer" class="dialog-footer">
          <el-button @click="dialogShardVisible = false">关闭</el-button>
          <el-button type="primary" @click="shardData(shardtemp)">确认</el-button>
        </div>
    </el-dialog>

    <!--新增计算节点-->
    <el-dialog title="新增计算节点" :visible.sync="dialogCompVisible" custom-class="single_dal_view">
      <el-form
        ref="compForm"
        :model="comptemp"
        :rules="rules"
        label-position="left"
        label-width="110px"
      >
      <el-form-item label="选择计算机:" prop="machinelist" >
        <el-checkbox-group 
        v-model="comptemp.machinelist"
        :min="minMachine"
        :max="machineTotal" >
          <el-checkbox v-for="machine in machines" :label="machine.hostaddr" :key="machine.id">{{machine.hostaddr}}</el-checkbox>
        </el-checkbox-group>
      </el-form-item>
        <el-form-item label="集群名称:" prop="old_cluster_name">
          <el-input v-model="comptemp.old_cluster_name" :disabled="true" />
        </el-form-item>
      </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button @click="dialogCompVisible = false">关闭</el-button>
          <el-button type="primary" @click="compData(comptemp)">确认</el-button>
        </div>
    </el-dialog>
     <!--新增存储节点-->
    <el-dialog title="新增存储节点" :visible.sync="dialogSnodeVisible" custom-class="single_dal_view">
      <el-form
        ref="compForm"
        :model="comptemp"
        :rules="rules"
        label-position="left"
        label-width="110px"
      >
      <el-form-item label="选择计算机:" prop="machinelist" >
        <el-checkbox-group 
        v-model="snodetemp.machinelist"
        :min="minMachine"
        :max="machineTotal" >
          <el-checkbox v-for="machine in machines" :label="machine.hostaddr" :key="machine.id">{{machine.hostaddr}}</el-checkbox>
        </el-checkbox-group>
      </el-form-item>
        <el-form-item label="集群名称:" prop="old_cluster_name">
          <el-input v-model="snodetemp.old_cluster_name" :disabled="true" />
        </el-form-item>
         <el-form-item label="shard名称:" prop="shard_name">
          <el-input v-model="snodetemp.shard_name" :disabled="true" />
        </el-form-item>
      </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button @click="dialogSnodeVisible = false">关闭</el-button>
          <el-button type="primary" @click="snodeData(snodetemp)">确认</el-button>
        </div>
    </el-dialog>
    <!--重做备机存储节点-->
    <el-dialog title="重做备机存储节点" :visible.sync="dialogRedoVisible" custom-class="single_dal_view">
      <el-form
        ref="redoForm"
        :model="redotemp"
        :rules="rules"
        label-position="left"
        label-width="130px"
      >
      <el-form-item label="选择计算机:" prop="machinelist" >
        <el-checkbox-group 
        v-model="snodetemp.machinelist"
        :min="minMachine"
        :max="machineTotal" >
          <el-checkbox v-for="machine in machines" :label="machine.hostaddr" :key="machine.id">{{machine.hostaddr}}</el-checkbox>
        </el-checkbox-group>
      </el-form-item>
      <el-form-item label="需重做的备机节点:" prop="redoList" >
        <el-select v-model="redotemp.redoList" multiple placeholder="请选择">
          <el-option
            v-for="item in options"
            :key="item.value"
            :label="item.label"
            :value="item.value">
          </el-option>
        </el-select>
       </el-form-item>
      </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button @click="dialogRedoVisible = false">关闭</el-button>
          <el-button type="primary" @click="snodeData(snodetemp)">确认</el-button>
        </div>
    </el-dialog>
  </div>
</template>

<script>
 import { messageTip,handleCofirm} from "@/utils";
import {getClusterNodesList,getEffectCluster,getNodes,getAllMachine, getShards,getSnodeTotal,getStandbyNode} from '@/api/cluster/list'
import SeeksRelationGraph from 'relation-graph'
import {v4 as uuidv4 } from 'uuid';
import {version_arr,ip_arr} from "@/utils/global_variable"
import {getEvStatus,pgEnable,myEnable,delShard,delComp,delSnode,startComp,startSnode,stopComp,stopSnode,restartComp,restartSnode,getClusterDetail} from '@/api/cluster/listInterface'
export default {
  name: 'Demo',
  components: { SeeksRelationGraph },
  data() {
    return {
      currentCase:1,
      clusters:[],
      nodeName:'',
      type:'',
      message_tips:'',
      message_type :'',
      nodataShow:false,
      timer:null,
      installStatus:false,
      info:'',
      row:{},
      snode:{},
      shard_create_priv:JSON.parse(sessionStorage.getItem('priv')).shard_create_priv,
      shard_drop_priv:JSON.parse(sessionStorage.getItem('priv')).shard_drop_priv,
      storage_node_create_priv:JSON.parse(sessionStorage.getItem('priv')).storage_node_create_priv,
      storage_node_drop_priv:JSON.parse(sessionStorage.getItem('priv')).storage_node_drop_priv,
      compute_node_create_priv:JSON.parse(sessionStorage.getItem('priv')).compute_node_create_priv,
      compute_node_drop_priv:JSON.parse(sessionStorage.getItem('priv')).compute_node_drop_priv,
      storage_enable_priv:JSON.parse(sessionStorage.getItem('priv')).storage_enable_priv,
      storage_disable_priv:JSON.parse(sessionStorage.getItem('priv')).storage_disable_priv,
      compute_enable_priv:JSON.parse(sessionStorage.getItem('priv')).compute_enable_priv,
      compute_disable_priv:JSON.parse(sessionStorage.getItem('priv')).compute_disable_priv,
      isShowCodePanel: false,
      isShowNodeTipsPanel: false,
      isShowNodeMenuPanel: false,
      nodeMenuPanelPosition: { x: 0, y: 0 },
      currentNode: {},
      dialogShardVisible:false,
      dialogCompVisible:false,
      dialogSnodeVisible:false,
      dialogRedoVisible:false,
      shardtemp:{
        old_cluster_name:'',
        cluster_name:'',
        snode_count:'',
        machinelist:[]
      },
      comptemp:{
        old_cluster_name:'',
        machinelist:[]
      },
      snodetemp:{
        old_cluster_name:'',
        shard_name:'',
        machinelist:[]
      },
      redotemp:{
        redolist:[]
      },
      machinelist:[],
      machines:[],
      minMachine:0,
      machineTotal:0,
      rules: {},
      options: [],
      status: [],
      dialogStatus: "",
      textMap: {
        update: "新增计算节点",
        create: "新增存储集群",
        detail: "新增存储节点",
        restore:'重做备机存储节点'
      },
      user_name:sessionStorage.getItem('login_username'),
      g_loading: false,
      demoname: '---',
      range_horizontal: [ 100, 300 ],
      range_vertical: [ 20, 100 ],
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
              'min_per_width': undefined,
              'max_per_width': '300',
              'min_per_height': '40',
              'max_per_height': undefined,
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
          "defaultExpandHolderPosition": "right",//右边展开关闭
          'defaultNodeShape': 1,//	默认的节点形状，0:圆形；1:矩形
          'defaultNodeWidth': '100',//默认节点的宽度
          'defaultLineShape': 4,//默认的线条样式（1:直线/2:样式2/3:样式3/4:折线/5:样式5/6:样式6)
          'defaultJunctionPoint': 'lr',//连接与节点的接触方式，lr为左右
          'defaultNodeBorderWidth': 0,//默认的节点边框粗细（像素）
          'defaultLineColor': 'rgba(149, 195, 243, 1)',//线条颜色
          // 'defaultNodeColor': 'rgba(255, 255, 255, 1)',//节点颜色
          'defaultNodeColor': 'rgba(176, 210, 237, 1)',//节点颜色
          'defaultNodeFontColor':'rgba(0, 0, 0, 1)',//字体的颜色
          'defaultNodeBorderColor':'rgba(255, 255, 255, 1)'
        // allowSwitchLineShape: true,//显示切换线条形状的按钮
        // allowSwitchJunctionPoint: true,//切换连接点位置的按钮
        //defaultJunctionPoint: 'border'
        // 这里可以参考"Graph 图谱"中的参数进行设置
      }
    }
  },
  created(){
    this.getCluster();
  },
  methods: {
    onNodeCollapse(node, e) {
      this.$refs.seeksRelationGraph.refresh()
    },
    onNodeExpand(node, e) {
      // 根据具体的业务需要决定是否需要从后台加载数据
      if (!node.data.isNeedLoadDataFromRemoteServer) {
        console.log('这个节点的子节点已经加载过了')
        this.$refs.seeksRelationGraph.refresh()
        return
      }
      //判断是否已经动态加载数据了
      if (node.data.childrenLoaded) {
        console.log('这个节点的子节点已经加载过了')
        this.$refs.seeksRelationGraph.refresh()
        return
      }
      node.data.childrenLoaded = true
      const tempData = {};
      tempData.shard_id =node.data.shard_id;
      tempData.cluster_id =node.data.cluster_id;
      tempData.tree_id =node.id;
      getNodes(tempData).then((response)=>{
        let res = response;
        if(res.code==200){
          let status=[];
          let arr = response.list;
          //调接口获取主备且在线情况
          const temp={
            job_id:uuidv4(),
            cluster_name:arr.nodes[0].data.cluster_name,
            version:version_arr[0].ver,
            job_type:'get_cluster_detail'
          }
          getClusterDetail(temp).then((response) => {
              status = response;
              for (let i = 0; i < arr.nodes.length; i++) {
              let param = arr.nodes[i];
              if(param.id.indexOf('snode')!=-1){
                this.$set(param.data, 'icon', 'iconfont icon-snode')
                //this.$set(param.data, 'color', '#e98f36')
                 for (let j = 0; j < status.length; j++) {
                    const shard_name=status[j].shard_name;
                    const ip=status[j].ip;
                    const port=status[j].port;
                    if(shard_name===param.data.shard_name&&ip===param.data.hostaddr&&port===param.text){
                      this.$set(param.data, 'status', status[j].status)
                      this.$set(param.data, 'master', status[j].master)
                    }
                 }
                if(param.data.master=='true'){
                  this.$set(param.data, 'color', '#ff0000')
                }else if(param.data.master=='false'){
                  this.$set(param.data, 'color', '#e98f36')
                }else {
                  this.$set(param.data, 'color', '#acacac')
                }
              }
              //console.log(param);
            }
          })
          
          // for (let i = 0; i < arr.nodes.length; i++) {
          //   let param = arr.nodes[i];
          //   if(param.id.indexOf('snode')!=-1){
          //     this.$set(param.data, 'icon', 'iconfont icon-snode')
          //     this.$set(param.data, 'color', '#e98f36')
          //     //  for (let j = 0; j < status.length; j++) {
                 
          //     //  }
          //     //  if(param.data.status=='PRIMARY'){
          //     //   this.$set(param.data, 'color', '#ff0000')
          //     //  }else if(param.data.status=='SECONDARY'){
          //     //   this.$set(param.data, 'color', '#e98f36')
          //     //  }else{
          //     //    this.$set(param.data, 'color', '#acacac')
          //     //  }
          //   }
          // }
          
          this.snode=arr;
          this.g_loading=true;
          this.$refs.seeksRelationGraph.appendJsonData(this.snode, (seeksRGGraph) => {
        })
        }
        else{
          this.message_tips = res.message;
          this.message_type = 'error';
          messageTip(this.message_tips,this.message_type);
        }
        
      })
    },
    agreeChange:function(val){
      this.getOneCluster(val);
    },
    async getCluster() {
      const temp={};
      temp.effectCluster=sessionStorage.getItem('affected_clusters');
      temp.apply_all_cluster= sessionStorage.getItem('apply_all_cluster');
      const res = await getEffectCluster(temp)
      if(res.total>0){
        this.currentCase=res.list[0].id
        this.clusters = res.list;
        this.getOneCluster(this.currentCase);
        this.nodataShow=false;
        this.g_loading=true;
      }else{
        this.g_loading=false;
        this.nodataShow=true;
      }
    },
    getOneCluster(val){
      getClusterNodesList(val).then(response => {
        let arr = response.list;
          for (let i = 0; i < arr.nodes.length; i++) {
            let param = arr.nodes[i];
            if(param.id=='cluster'){
               this.$set(param, 'data', {'icon':'iconfont  icon-cluster','color':'#533ade'})
            }
            if(param.id.indexOf('shard')!=-1){
              this.$set(param, 'expandHolderPosition', 'right')
              this.$set(param, 'expanded', false)
              this.$set(param.data, 'isNeedLoadDataFromRemoteServer', true)
              this.$set(param.data, 'childrenLoaded', false)
              this.$set(param.data, 'icon', 'iconfont icon-shard')
              this.$set(param.data, 'color', '#e98f36')
             
               //this.$set(param, 'data', {'icon':'iconfont icon-shard','color':'#e98f36'})
            }
            if(param.id.indexOf('cnode')!=-1){
               this.$set(param.data, 'icon', 'iconfont icon-compnode')
               if(param.data.status=='inactive'){
                this.$set(param.data, 'color', '#acacac')
               }else{
                 this.$set(param.data, 'color', '#1196db')
               }
            }
            if(param.id.indexOf('snode')!=-1){
               this.$set(param.data, 'icon', 'iconfont icon-snode')
               this.$set(param.data, 'color', '#e98f36')
            }
          }
          this.list = arr;
          this.showSeeksGraph( this.list )
          setTimeout(() => {
            this.listLoading = false
          }, 0.5 * 1000)
      });
    },
    showSeeksGraph(query) {
       var __graph_json_data =query;
      this.$refs.seeksRelationGraph.setJsonData(__graph_json_data, (seeksRGGraph) => {
        // 这些写上当图谱初始化完成后需要执行的代码
        this.graphOptions.layouts[0].levelDistance = this.levelDistance
        this.$refs.seeksRelationGraph.setOptions(this.graphOptions, (seeksRGGraph) => {
          // 这些写上当图谱初始化完成后需要执行的代码
          console.log('setOptions:callback:', seeksRGGraph)
        })
      })
    },
    onNodeClick(nodeObject, $event) {
    //  this.currentNode = nodeObject
    //   var _base_position = this.$refs.myPage.getBoundingClientRect()
    //   if(this.currentNode.id=='cluster' ||this.currentNode.text.indexOf('shard') !=-1){
    //      if((this.currentNode.data.name=='shard'&&this.shard_drop_priv!=='Y')){
    //     this.isShowNodeMenuPanel = false
    //   }
    //   else if(this.currentNode.id=='cluster'){//cluster not display
    //     this.isShowNodeMenuPanel = false
    //   }
    //   else{
    //     this.isShowNodeMenuPanel = true
    //     this.nodeName=this.currentNode.text;
    //     if(this.currentNode.text.indexOf('shard') !=-1) {
    //       this.type='shard'
    //     }else if(this.currentNode.id=='cluster'){
    //       this.type='cluster'
    //     }
    //     this.nodeMenuPanelPosition.x = $event.clientX - _base_position.x
    //     this.nodeMenuPanelPosition.y = $event.clientY - _base_position.y
    //   }
    //   }
    },
    onLineClick(lineObject, $event) {
      console.log('onLineClick:', lineObject)
    },
    showNodeTips(nodeObject, $event) {
      this.currentNode = nodeObject
      var _base_position = this.$refs.myPage.getBoundingClientRect()
      this.isShowNodeTipsPanel = true
      this.nodeMenuPanelPosition.x = $event.clientX - _base_position.x + 10
      this.nodeMenuPanelPosition.y = $event.clientY - _base_position.y + 10
    },
    hideNodeTips(nodeObject, $event) {
      this.isShowNodeTipsPanel = false
    },
    showNodeMenus(nodeObject, $event) {
      this.currentNode = nodeObject
      var _base_position = this.$refs.myPage.getBoundingClientRect()
       if(this.currentNode.id.indexOf('cnode') !=-1||this.currentNode.id.indexOf('snode') !=-1){
          this.isShowNodeMenuPanel = true
          this.nodeName=this.currentNode.text;
           if(this.currentNode.id.indexOf('cnode') !=-1) {
            this.type='cnode'
          }else if(this.currentNode.id.indexOf('snode') !=-1){
            this.type='snode'
          }
          this.nodeMenuPanelPosition.x = $event.clientX - _base_position.x
          this.nodeMenuPanelPosition.y = $event.clientY - _base_position.y
       }
    },
    doAction(actionName) {
      if(actionName==='进入'){
          const tempData = {};//pgsql
          tempData['job_id'] = uuidv4();
          tempData['job_type'] ='postgres_exporter';
          tempData['ver']=version_arr[0].ver;
          tempData['user_name']=sessionStorage.getItem('login_username');
          tempData['ip']=this.currentNode.data.hostaddr;
          tempData['port']=this.currentNode.data.port;
          const mysqlData = {};//mysql
          mysqlData['job_id'] = uuidv4();
          mysqlData['job_type'] ='mysqld_exporter';
          mysqlData['ver']=version_arr[0].ver;
          mysqlData['user_name']=sessionStorage.getItem('login_username');
          mysqlData['ip']=this.currentNode.data.hostaddr;
          mysqlData['port']=this.currentNode.data.port;
          const prometheus = {};//prometheus
          prometheus['job_id'] = uuidv4();
          prometheus['job_type']='update_prometheus';
          prometheus['ver']=version_arr[0].ver;
          prometheus['user_name']=sessionStorage.getItem('login_username');

          if(this.currentNode.data.name==='pgsql'){
            pgEnable(tempData).then((pgres) => {
              if(pgres.result=='accept'){
                 //调获取状态接口
                  let i=0;
                  let timer = setInterval(() => {
                      setTimeout(()=>{
                        const postarr={};
                        postarr.job_type='get_status';
                        postarr.ver=version_arr[0].ver;
                        postarr.job_id=tempData['job_id'];
                        getEvStatus(postarr).then((res) => {
                        if(res.result=='done'||res.result=='failed'){
                          clearInterval(timer);
                          this.info=res.info;
                          if(res.result=='done'){
                            //跳转新页面pgsql
                            window.open('http://'+ip_arr[0].ip+'/d/postgresql/postgresql?orgId=1&refresh=5s');
                          }else{
                            this.installStatus = true;
                          }
                        }else{
                          this.info=res.info;
                          this.installStatus = true;
                        }
                      });
                        if(i>=86400){
                            clearInterval(timer);
                        }
                        i++;
                      }, 0)
                  }, 1000)
              
              }
            })
          }else if(this.currentNode.data.name==='mysql'){
            myEnable(mysqlData).then((myres) => {
              if(myres.result='accept'){
                 //调获取状态接口
                  let i=0;
                  let timer = setInterval(() => {
                     setTimeout(()=>{
                        const postarr={};
                        postarr.job_type='get_status';
                        postarr.ver=version_arr[0].ver;
                        postarr.job_id=mysqlData['job_id'];
                        getEvStatus(postarr).then((res) => {
                        if(res.result=='done'||res.result=='failed'){
                          clearInterval(timer);
                          this.info=res.info;
                          if(res.result=='done'){
                            //跳转新页面mysql
                            window.open('http://'+ip_arr[0].ip+'/d/mysql/mysql?orgId=1&refresh=5s');
                          }else{
                            this.installStatus = true;
                          }
                        }else{
                          this.info=res.info;
                          this.installStatus = true;
                        }
                      });
                        if(i>=86400){
                            clearInterval(timer);
                        }
                        i++;
                      }, 0)
                  }, 1000)
                
              }
            })
          }
      }
      if(actionName==='删除'){
        if(this.currentNode.data.name==='shard'){
          handleCofirm("此操作将永久删除"+this.currentNode.text+", 是否继续?").then( () =>{
            const tempData = {};
              tempData.user_name = sessionStorage.getItem('login_username');
              tempData.job_id =uuidv4();
              tempData.job_type ='delete_shard';
              tempData.ver=version_arr[0].ver;
              tempData.cluster_name=this.currentNode.data.cluster_name;
              tempData.shard_name=this.currentNode.text;
              //console.log(tempData);return;
              delShard(tempData).then((response)=>{
                let res = response;
                if(res.result='accept'){
                  this.message_tips = '正在删除'+this.currentNode.text;
                  this.message_type = 'success';
                  this.isShowNodeMenuPanel = false
                  //调获取状态接口
                  let i=0;
                  let timer = setInterval(() => {
                    this.getStatus(timer,tempData.job_id,i++)
                  }, 1000)
                }
                else{
                  this.message_tips = res.message;
                  this.message_type = 'error';
                }
                messageTip(this.message_tips,this.message_type);
          })
          }).catch(() => {
              console.log('quxiao')
              messageTip('已取消删除','info');
          }); 
        }else if(this.currentNode.data.name==='pgsql'){
          handleCofirm("此操作将永久删除计算节点"+this.currentNode.text+", 是否继续?").then( () =>{
            const tempData = {};
              tempData.user_name = sessionStorage.getItem('login_username');
              tempData.job_id =uuidv4();
              tempData.job_type ='delete_comp';
              tempData.ver=version_arr[0].ver;
              tempData.cluster_name=this.currentNode.data.cluster_name;
              tempData.comp_name=this.currentNode.data.comp;
              delComp(tempData).then((response)=>{
                let res = response;
                if(res.result=='accept'){
                  this.message_tips = '正在删除计算节点'+this.currentNode.text;
                  this.message_type = 'success';
                  this.isShowNodeMenuPanel = false
                  //调获取状态接口
                  let i=0;
                  let timer = setInterval(() => {
                    this.getStatus(timer,tempData.job_id,i++)
                  }, 1000)
                }
                else{
                  this.message_tips = res.message;
                  this.message_type = 'error';
                }
                messageTip(this.message_tips,this.message_type);
            })
          }).catch(() => {
              console.log('quxiao')
              messageTip('已取消删除','info');
          }); 

        }else if(this.currentNode.data.name==='mysql'){
          handleCofirm("此操作将永久删除存储节点"+this.currentNode.text+", 是否继续?").then( () =>{
            const tempData = {};
              tempData.user_name = sessionStorage.getItem('login_username');
              tempData.job_id =uuidv4();
              tempData.job_type ='delete_node';
              tempData.ver=version_arr[0].ver;
              tempData.cluster_name=this.currentNode.data.cluster_name;
              tempData.shard_name=this.currentNode.data.shard_name;
              tempData.ip=this.currentNode.data.hostaddr;
              tempData.port=this.currentNode.data.port;
              delSnode(tempData).then((response)=>{
                let res = response;
                if(res.result=='accept'){
                  this.message_tips = '正在删除存储节点'+this.currentNode.text;
                  this.message_type = 'success';
                  this.isShowNodeMenuPanel = false
                  //调获取状态接口
                  let i=0;
                  let timer = setInterval(() => {
                    this.getStatus(timer,tempData.job_id,i++)
                  }, 1000)
                }
                else{
                  this.message_tips = res.message;
                  this.message_type = 'error';
                }
                messageTip(this.message_tips,this.message_type);
            })
          }).catch(() => {
              console.log('quxiao')
              messageTip('已取消删除','info');
          }); 
        }
      }
      if(actionName==='启用'){
         if(this.currentNode.data.name==='pgsql'){
          handleCofirm("是否继续启用"+this.currentNode.text+"该计算节点?").then( () =>{
            const tempData = {};
              tempData.user_name = sessionStorage.getItem('login_username');
              tempData.job_id =uuidv4();
              tempData.job_type ='control_instance';
              tempData.ver=version_arr[0].ver;
              tempData.control='start';
              tempData.ip=this.currentNode.data.hostaddr;
              tempData.port=this.currentNode.data.port;
              tempData.type=this.currentNode.data.name;
              startComp(tempData).then((response)=>{
                let res = response;
                if(res.result=='accept'){
                  this.message_tips = '正在启动计算节点'+this.currentNode.text;
                  this.message_type = 'success';
                  //调获取状态接口
                  let i=0;
                  let timer = setInterval(() => {
                    this.getStatus(timer,tempData.job_id,i++)
                  }, 1000)
                  this.isShowNodeMenuPanel = false
                }
                else{
                  this.message_tips = res.message;
                  this.message_type = 'error';
                }
                messageTip(this.message_tips,this.message_type);
            })
          }).catch(() => {
              console.log('quxiao')
              messageTip('已取消删除','info');
          }); 
        }
        if(this.currentNode.data.name==='mysql'){
          handleCofirm("是否继续启用"+this.currentNode.text+"该存储节点?").then( () =>{
            const tempData = {};
              tempData.user_name = sessionStorage.getItem('login_username');
              tempData.job_id =uuidv4();
              tempData.job_type ='control_instance';
              tempData.ver=version_arr[0].ver;
              tempData.control='start';
              tempData.ip=this.currentNode.data.hostaddr;
              tempData.port=this.currentNode.data.port;
              tempData.type=this.currentNode.data.name;
              startSnode(tempData).then((response)=>{
                let res = response;
                if(res.result=='accept'){
                  this.message_tips = '正在启动存储节点'+this.currentNode.text;
                  this.message_type = 'success';
                  //调获取状态接口
                  let i=0;
                  let timer = setInterval(() => {
                    this.getStatus(timer,tempData.job_id,i++)
                  }, 1000)
                  this.isShowNodeMenuPanel = false
                }
                else{
                  this.message_tips = res.message;
                  this.message_type = 'error';
                }
                messageTip(this.message_tips,this.message_type);
            })
          }).catch(() => {
              console.log('quxiao')
              messageTip('已取消删除','info');
          }); 
        }
      }
      if(actionName==='禁用'){
        if(this.currentNode.data.name==='pgsql'){
          handleCofirm("是否继续禁用"+this.currentNode.text+"该计算节点?").then( () =>{
            const tempData = {};
              tempData.user_name = sessionStorage.getItem('login_username');
              tempData.job_id =uuidv4();
              tempData.job_type ='control_instance';
              tempData.ver=version_arr[0].ver;
              tempData.control='stop';
              tempData.ip=this.currentNode.data.hostaddr;
              tempData.port=this.currentNode.data.port;
              tempData.type=this.currentNode.data.name;
              stopComp(tempData).then((response)=>{
                let res = response;
                if(res.result=='accept'){
                  this.message_tips = '正在禁用计算节点'+this.currentNode.text;
                  this.message_type = 'success';
                  //调获取状态接口
                  let i=0;
                  let timer = setInterval(() => {
                    this.getStatus(timer,tempData.job_id,i++)
                  }, 1000)
                  //成功后重新设置数据
                  //this.getCluster();
                  this.isShowNodeMenuPanel = false
                }
                else{
                  this.message_tips = res.message;
                  this.message_type = 'error';
                }
                messageTip(this.message_tips,this.message_type);
            })
          }).catch(() => {
              console.log('quxiao')
              messageTip('已取消删除','info');
          }); 
        }
        if(this.currentNode.data.name==='mysql'){
          handleCofirm("是否继续禁用"+this.currentNode.text+"该存储节点?").then( () =>{
            const tempData = {};
              tempData.user_name = sessionStorage.getItem('login_username');
              tempData.job_id =uuidv4();
              tempData.job_type ='control_instance';
              tempData.ver=version_arr[0].ver;
              tempData.control='stop';
              tempData.ip=this.currentNode.data.hostaddr;
              tempData.port=this.currentNode.data.port;
              tempData.type=this.currentNode.data.name;
              stopSnode(tempData).then((response)=>{
                let res = response;
                if(res.result=='accept'){
                  this.message_tips = '正在禁用存储节点'+this.currentNode.text;
                  this.message_type = 'success';
                  this.isShowNodeMenuPanel = false
                  //调获取状态接口
                  let i=0;
                  let timer = setInterval(() => {
                    this.getStatus(timer,tempData.job_id,i++)
                  }, 1000)
                }
                else{
                  this.message_tips = res.message;
                  this.message_type = 'error';
                }
                messageTip(this.message_tips,this.message_type);
            })
          }).catch(() => {
              console.log('quxiao')
              messageTip('已取消删除','info');
          }); 
        }
      }
      if(actionName==='重启'){
        if(this.currentNode.data.name==='pgsql'){
          handleCofirm("是否继续重启"+this.currentNode.text+"该计算节点?").then( () =>{
            const tempData = {};
              tempData.user_name = sessionStorage.getItem('login_username');
              tempData.job_id =uuidv4();
              tempData.job_type ='control_instance';
              tempData.ver=version_arr[0].ver;
              tempData.control='restart';
              tempData.ip=this.currentNode.data.hostaddr;
              tempData.port=this.currentNode.data.port;
              tempData.type=this.currentNode.data.name;
              restartComp(tempData).then((response)=>{
                let res = response;
                if(res.result=='accept'){
                  this.message_tips = '正在重启计算节点'+this.currentNode.text;
                  this.message_type = 'success';
                  //调获取状态接口
                  let i=0;
                  let timer = setInterval(() => {
                    this.getStatus(timer,tempData.job_id,i++)
                  }, 1000)
                  this.isShowNodeMenuPanel = false
                }
                else{
                  this.message_tips = res.message;
                  this.message_type = 'error';
                }
                messageTip(this.message_tips,this.message_type);
            })
          }).catch(() => {
              console.log('quxiao')
              messageTip('已取消删除','info');
          }); 
        }
        if(this.currentNode.data.name==='mysql'){
          handleCofirm("是否继续重启"+this.currentNode.text+"该存储节点?").then( () =>{
            const tempData = {};
              tempData.user_name = sessionStorage.getItem('login_username');
              tempData.job_id =uuidv4();
              tempData.job_type ='control_instance';
              tempData.ver=version_arr[0].ver;
              tempData.control='restart';
              tempData.ip=this.currentNode.data.hostaddr;
              tempData.port=this.currentNode.data.port;
              tempData.type=this.currentNode.data.name;
              restartSnode(tempData).then((response)=>{
                let res = response;
                if(res.result=='accept'){
                  this.message_tips = '正在重启存储节点'+this.currentNode.text;
                  this.message_type = 'success';
                  this.isShowNodeMenuPanel = false
                  //调获取状态接口
                  let i=0;
                  let timer = setInterval(() => {
                    this.getStatus(timer,tempData.job_id,i++)
                  }, 1000)
                }
                else{
                  this.message_tips = res.message;
                  this.message_type = 'error';
                }
                messageTip(this.message_tips,this.message_type);
            })
          }).catch(() => {
              console.log('quxiao')
              messageTip('已取消删除','info');
          }); 
        }
      }
      if(actionName==='新增存储集群'){
        this.shardtemp.old_cluster_name=this.currentNode.text;
        this.shardtemp.cluster_name=this.currentNode.data.cluster_name;
        this.dialogShardVisible=true;
        //获取计算机名称
        getAllMachine().then((res) => {
        this.machines = res.list;
          this.minMachine=0;
          this.machineTotal=res.total;
        });
        // //获取副本数
        // const tempData = {};
        // tempData.shard_id =this.currentNode.data.shard_id;
        // tempData.cluster_id =this.currentNode.data.cluster_id;
        // console.log(tempData);return;
        // getSnodeTotal(tempData).then((res) => {
        // this.shardtemp.snode_count = res.total;
        // });
      }
      if(actionName==='新增计算节点'){
        this.comptemp.old_cluster_name=this.currentNode.text;
        this.dialogCompVisible=true;
        //获取计算机名称
        getAllMachine().then((res) => {
        this.machines = res.list;
          this.minMachine=0;
          this.machineTotal=res.total;
        });
      }
      if(actionName==='新增存储节点'){
        this.snodetemp.shard_name=this.currentNode.text;
        this.snodetemp.old_cluster_name=this.currentNode.data.cluster_name;
        this.dialogSnodeVisible=true;
        //获取计算机名称
        getAllMachine().then((res) => {
        this.machines = res.list;
          this.minMachine=0;
          this.machineTotal=res.total;
        });
      }
      if(actionName==='重做备机存储节点'){
        this.redotemp.shard_name=this.currentNode.text;
        this.dialogRedoVisible=true;
        //获取计算机名称
        getAllMachine().then((res) => {
        this.machines = res.list;
          this.minMachine=0;
          this.machineTotal=res.total;
        });
      }
      // if(actionName==='设置全局变量'){
       
      // }
      //获取备机节点
      const temp={};
      temp.cluster_id=this.currentNode.data.cluster_id;
      temp.shard_id=this.currentNode.data.shard_id;
      getStandbyNode(temp).then((res) => {
        if(res.code==200){
          this.options=[];
          if(res.list.length>0){
             for (let i = 0; i < res.list.length; i++) {
               const arr={'value':i,'label':res.list[i].ip+'('+res.list[i].port+')'};
               this.options.push(arr);
             }

          }
        }
        
      });
      
      // this.$notify({
      //   title: '提示',
      //   message: '对节点【' + this.currentNode.text + '】进行了：' + actionName,
      //   type: 'success'
      // })
      // this.isShowNodeMenuPanel = false
    },
    getStatus (timer,data,i) {
      setTimeout(()=>{
        const postarr={}
        postarr.job_type='get_status';
        postarr.ver=version_arr[0].ver;
        postarr.job_id=data;
        getEvStatus(postarr).then((res) => {
        if(res.result=='done'||res.result=='failed'){
          clearInterval(timer);
          this.info=res.info;
          if(res.result=='done'){
            this.getCluster();
          }else{
            this.installStatus = true;
          }
        }else{
          this.info=res.info;
          this.installStatus = true;
        }
      });
        if(i>=86400){
            clearInterval(timer);
        }
      }, 0)
    }
  }
}
</script>

<style lang="scss" scoped>
.c-node-menu-item{
  line-height: 30px;padding-left: 10px;cursor: pointer;color: #444444;font-size: 14px;border-top:#efefef solid 1px;
}
.c-node-menu-item:hover{
  background-color: rgba(66,153,187,0.2);
}
.nodata{
    text-align: center;
    font-size: 16px;
}
.c-expanded{
  background-color: #add1f5 !important;
}
</style>