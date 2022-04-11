 <template>
  <div>
    <el-radio-group  v-model="currentCase" size="small" @change="agreeChange" v-show="g_loading">
    <el-radio v-for="cluster in clusters" :label="cluster.id" :key="cluster.id">{{cluster.name}}</el-radio>
    </el-radio-group>
    <div v-text="info" v-show="installStatus===true" class="info"></div>
    <div class="nodata" v-show="nodataShow">暂无数据</div>
    <!--  @mouseover="showNodeTips(node, $event)" @mouseout="hideNodeTips(node, $event)" -->
    <div ref="myPage" style="height:calc(100vh - 200px);" v-show="g_loading">
      <SeeksRelationGraph
        ref="seeksRelationGraph"
        :options="graphOptions"
        :on-node-click="onNodeClick"
        :on-line-click="onLineClick">
        <div slot="node" slot-scope="{node}"  @click="showNodeMenus(node, $event)" @contextmenu.prevent.stop="showNodeMenus(node, $event)">
          <div>
            <div v-text="node.text"></div>
            <!-- <i style="font-size: 30px;" :class="node.data.myicon" /> -->
          </div>
          <!-- <div style="color: forestgreen;font-size: 16px;position: absolute;width: 160px;height:25px;line-height: 25px;margin-top:5px;margin-left:-48px;text-align: center;background-color: rgba(66,187,66,0.2);">
            {{ node.data.myicon }}
          </div> -->
        </div>
        <!-- <div slot="bottomPanel" style="border-top:#efefef solid 1px;height:60px;line-height: 60px;text-align: center;font-size: 18px;background-color: #ffffff;">
          这里是底部插槽 slot="bottomPanel",可以自定义这里的内容
        </div> -->
      </SeeksRelationGraph>
    </div>
    <!-- <div v-if="isShowNodeTipsPanel" :style="{left: nodeMenuPanelPosition.x + 'px', top: nodeMenuPanelPosition.y + 'px' }" style="z-index: 999;padding:10px;background-color: #ffffff;border:#eeeeee solid 1px;box-shadow: 0px 0px 8px #cccccc;position: absolute;">
      <div style="line-height: 25px;padding-left: 10px;color: #888888;font-size: 12px;">节点名称：{{currentNode.text}}</div>
      <div class="c-node-menu-item">id:{{currentNode.text}}</div>
      <div class="c-node-menu-item">图标:{{currentNode.data.myicon}}</div>
    </div> -->
    <div v-show="isShowNodeMenuPanel&&g_loading" :style="{left: nodeMenuPanelPosition.x + 'px', top: nodeMenuPanelPosition.y + 'px' }" style="z-index: 999;padding:10px;background-color: #ffffff;border:#eeeeee solid 1px;box-shadow: 0px 0px 8px #cccccc;position: absolute;">
      <div style="line-height: 25px;padding-left: 10px;color: #888888;font-size: 12px;">对{{nodeName}}进行操作：</div>
      <!-- <div class="c-node-menu-item"  v-if="(type==='shards'&& shard_create_priv==='Y')||(type==='comps'&& compute_node_create_priv==='Y')||(type==='shard'&& storage_node_create_priv==='Y')" @click.stop="doAction('新增')">新增</div> -->
      <div class="c-node-menu-item" v-if="type==='node'&& (storage_enable_priv==='Y'||compute_enable_priv==='Y')" @click.stop="doAction('启用')">启用</div>
      <div class="c-node-menu-item"  v-if="type==='node'&& (storage_disable_priv==='Y'||compute_disable_priv==='Y')" @click.stop="doAction('禁用')">禁用</div>
      <div class="c-node-menu-item"  v-if="(type==='shard'&& shard_drop_priv==='Y')||(type==='node'&& compute_node_drop_priv==='Y')||(type==='node'&& storage_node_drop_priv==='Y')" @click.stop="doAction('删除')">删除</div>
      <div class="c-node-menu-item"  v-if="type==='node'" @click.stop="doAction('进入')">进入</div>
    </div>
  </div>
</template>

<script>
 import { messageTip,handleCofirm} from "@/utils";
// import {getAllCluster} from '@/api/system/access'
import {getClusterNodesList,pgEnable,pEnable,myEnable,delShard,delComp,delSnode,startComp,startSnode,stopComp,stopSnode,getEffectCluster,getEvStatus} from '@/api/cluster/list'
import SeeksRelationGraph from 'relation-graph'
import {v4 as uuidv4 } from 'uuid';
import {version_arr,ip_arr} from "@/utils/global_variable"
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

      g_loading: false,
      demoname: '---',
      // activeTabName: 'case2',
      range_horizontal: [ 100, 300 ],
      range_vertical: [ 20, 100 ],
      levelDistance: '300,260,250,500',//线条长度设置
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
          // "defaultExpandHolderPosition": "right",//右边展开关闭
          'defaultNodeShape': 1,//	默认的节点形状，0:圆形；1:矩形
          // 'defaultNodeWidth': '100',//默认节点的宽度
          'defaultLineShape': 4,//默认的线条样式（1:直线/2:样式2/3:样式3/4:折线/5:样式5/6:样式6)
          'defaultJunctionPoint': 'lr',//连接与节点的接触方式，lr为左右
          'defaultNodeBorderWidth': 0,//默认的节点边框粗细（像素）
          'defaultLineColor': 'rgba(149, 195, 243, 1)',//线条颜色
          'defaultNodeColor': 'rgba(64, 158, 255, 1)',//节点颜色
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
  mounted() {
    //this.showSeeksGraph()
  },
  methods: {
    agreeChange:function(val){
      //let that = this 
      //console.log(val);
      this.getOneCluster(val);
      //that.btnstatus=(val==='1')?true:false;
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
          this.list = response.list;
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
      console.log('onNodeClick:', nodeObject)
    },
    onLineClick(lineObject, $event) {
      console.log('onLineClick:', lineObject)
    },
    showNodeTips(nodeObject, $event) {
      this.currentNode = nodeObject
      var _base_position = this.$refs.myPage.getBoundingClientRect()
      //console.log('showNodeMenus:', $event, _base_position)
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
      if((this.currentNode.data.name=='shard'&&this.shard_drop_priv!=='Y')||this.currentNode.id=='shards'||this.currentNode.id=='cluster'||this.currentNode.id=='comps_name'||this.currentNode.id=='comps'){
        this.isShowNodeMenuPanel = false
      }else{
        this.isShowNodeMenuPanel = true
        this.nodeName=this.currentNode.text;
        if(this.nodeName.indexOf('shard') !=-1) {
          this.type='shard'
        }else {
          this.type='node'
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
          tempData['username']=sessionStorage.getItem('login_username');
          tempData['ip']=this.currentNode.data.hostaddr;
          tempData['port']=this.currentNode.data.port;
          const mysqlData = {};//mysql
          mysqlData['job_id'] = uuidv4();
          mysqlData['job_type'] ='mysqld_exporter';
          mysqlData['ver']=version_arr[0].ver;
          mysqlData['username']=sessionStorage.getItem('login_username');
          mysqlData['ip']=this.currentNode.data.hostaddr;
          mysqlData['port']=this.currentNode.data.port;
          const prometheus = {};//prometheus
          prometheus['job_id'] = uuidv4();
          prometheus['job_type']='update_prometheus';
          prometheus['ver']=version_arr[0].ver;
          prometheus['username']=sessionStorage.getItem('login_username');

          //先启动promentheus
          // pEnable(prometheus).then((response) => {
          //    let res = response;
          //   if(res.code==200){
          //     if(this.currentNode.data.name==='pgsql'){
          //       pgEnable(tempData).then((pgres) => {
          //         if(pgres.code=='200'){
          //           //跳转新页面
          //           window.open(ip_arr[0].ip+'/d/wGgaPlciz/postgresql-exporter-quickstart-and-dashboard?orgId=1');
          //         }
          //       })
          //     }else if(this.currentNode.data.name==='mysql'){
          //       myEnable(mysqlData).then((myres) => {
          //         if(myres.code=='200'){
          //           //跳转新页面
          //           window.open(ip_arr[0].ip+'/d/mysql/mysql?orgId=1&refresh=10s');
          //         }
          //       })
          //     }
            
          //   }else{
          //     this.message_tips = res.message;
          //     this.message_type = 'error';
          //     messageTip(this.message_tips,this.message_type);
          //   }
          // })
          if(this.currentNode.data.name==='pgsql'){
            pgEnable(tempData).then((pgres) => {
              if(pgres.code=='200'){
                 //调获取状态接口
                  let i=0;
                  let timer = setInterval(() => {
                    //this.getStatus(timer,res.uuid,i++)
                      setTimeout(()=>{
                        getEvStatus(pgres.uuid).then((res) => {
                        if(res.res.result=='done'||res.res.result=='failed'){
                          clearInterval(timer);
                          this.info=res.res.info;
                          if(res.res.result=='done'){
                            //跳转新页面pgsql
                            window.open(ip_arr[0].ip+'/d/postgresql/postgresql?orgId=1&refresh=5s');
                          }else{
                            this.installStatus = true;
                          }
                        }else{
                          this.info=res.res.info;
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
              if(myres.code=='200'){
                 //调获取状态接口
                  let i=0;
                  let timer = setInterval(() => {
                    //this.getStatus(timer,res.uuid,i++)
                     setTimeout(()=>{
                        getEvStatus(myres.uuid).then((res) => {
                        if(res.res.result=='done'||res.res.result=='failed'){
                          clearInterval(timer);
                          this.info=res.res.info;
                          if(res.res.result=='done'){
                            //跳转新页面mysql
                            window.open(ip_arr[0].ip+'/d/mysql/mysql?orgId=1&refresh=5s');
                          }else{
                            this.installStatus = true;
                          }
                        }else{
                          this.info=res.res.info;
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
              tempData.username = sessionStorage.getItem('login_username');
              tempData.job_id =uuidv4();
              tempData.job_type ='delete_shard';
              tempData.ver=version_arr[0].ver;
              tempData.cluster_name=this.currentNode.data.cluster_name;
              tempData.shard_name=this.currentNode.text;
              delShard(tempData).then((response)=>{
                let res = response;
                if(res.code==200){
                  this.message_tips = '正在删除'+this.currentNode.text;
                  this.message_type = 'success';
                  this.isShowNodeMenuPanel = false
                  //调获取状态接口
                  let i=0;
                  let timer = setInterval(() => {
                    this.getStatus(timer,res.uuid,i++)
                  }, 1000)
                  //成功后重新设置数据
                  //this.getCluster();
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
              tempData.username = sessionStorage.getItem('login_username');
              tempData.job_id =uuidv4();
              tempData.job_type ='delete_comp';
              tempData.ver=version_arr[0].ver;
              tempData.cluster_name=this.currentNode.data.cluster_name;
              tempData.comp_name=this.currentNode.data.comp;
              delComp(tempData).then((response)=>{
                let res = response;
                if(res.code==200){
                  this.message_tips = '正在删除计算节点'+this.currentNode.text;
                  this.message_type = 'success';
                  this.isShowNodeMenuPanel = false
                  //调获取状态接口
                  let i=0;
                  let timer = setInterval(() => {
                    this.getStatus(timer,res.uuid,i++)
                  }, 1000)
                  //成功后重新设置数据
                  //this.getCluster();
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
              tempData.username = sessionStorage.getItem('login_username');
              tempData.job_id =uuidv4();
              tempData.job_type ='delete_node';
              tempData.ver=version_arr[0].ver;
              tempData.cluster_name=this.currentNode.data.cluster_name;
              tempData.shard_name=this.currentNode.data.shard_name;
              tempData.ip=this.currentNode.data.hostaddr;
              tempData.port=this.currentNode.data.port;
              delSnode(tempData).then((response)=>{
                let res = response;
                if(res.code==200){
                  this.message_tips = '正在删除存储节点'+this.currentNode.text;
                  this.message_type = 'success';
                  this.isShowNodeMenuPanel = false
                  //调获取状态接口
                  let i=0;
                  let timer = setInterval(() => {
                    this.getStatus(timer,res.uuid,i++)
                  }, 1000)
                  //成功后重新设置数据
                  //this.getCluster();
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
              tempData.username = sessionStorage.getItem('login_username');
              tempData.job_id =uuidv4();
              tempData.job_type ='control_instance';
              tempData.ver=version_arr[0].ver;
              tempData.control='start';
              tempData.ip=this.currentNode.data.hostaddr;
              tempData.port=this.currentNode.data.port;
              tempData.type=this.currentNode.data.name;
              startComp(tempData).then((response)=>{
                let res = response;
                if(res.code==200){
                  this.message_tips = '正在启动计算节点'+this.currentNode.text;
                  this.message_type = 'success';
                  //调获取状态接口
                  let i=0;
                  let timer = setInterval(() => {
                    this.getStatus(timer,res.uuid,i++)
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
          handleCofirm("是否继续启用"+this.currentNode.text+"该存储节点?").then( () =>{
            const tempData = {};
              tempData.username = sessionStorage.getItem('login_username');
              tempData.job_id =uuidv4();
              tempData.job_type ='control_instance';
              tempData.ver=version_arr[0].ver;
              tempData.control='start';
              tempData.ip=this.currentNode.data.hostaddr;
              tempData.port=this.currentNode.data.port;
              tempData.type=this.currentNode.data.name;
              startSnode(tempData).then((response)=>{
                let res = response;
                if(res.code==200){
                  this.message_tips = '正在启动存储节点'+this.currentNode.text;
                  this.message_type = 'success';
                  //调获取状态接口
                  let i=0;
                  let timer = setInterval(() => {
                    this.getStatus(timer,res.uuid,i++)
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
      }
      if(actionName==='禁用'){
        if(this.currentNode.data.name==='pgsql'){
          handleCofirm("是否继续禁用"+this.currentNode.text+"该计算节点?").then( () =>{
            const tempData = {};
              tempData.username = sessionStorage.getItem('login_username');
              tempData.job_id =uuidv4();
              tempData.job_type ='control_instance';
              tempData.ver=version_arr[0].ver;
              tempData.control='stop';
              tempData.ip=this.currentNode.data.hostaddr;
              tempData.port=this.currentNode.data.port;
              tempData.type=this.currentNode.data.name;
              stopComp(tempData).then((response)=>{
                let res = response;
                if(res.code==200){
                  this.message_tips = '正在禁用计算节点'+this.currentNode.text;
                  this.message_type = 'success';
                  //调获取状态接口
                  let i=0;
                  let timer = setInterval(() => {
                    this.getStatus(timer,res.uuid,i++)
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
              tempData.username = sessionStorage.getItem('login_username');
              tempData.job_id =uuidv4();
              tempData.job_type ='control_instance';
              tempData.ver=version_arr[0].ver;
              tempData.control='stop';
              tempData.ip=this.currentNode.data.hostaddr;
              tempData.port=this.currentNode.data.port;
              tempData.type=this.currentNode.data.name;
              stopSnode(tempData).then((response)=>{
                let res = response;
                if(res.code==200){
                  this.message_tips = '正在禁用存储节点'+this.currentNode.text;
                  this.message_type = 'success';
                  this.isShowNodeMenuPanel = false
                  //调获取状态接口
                  let i=0;
                  let timer = setInterval(() => {
                    this.getStatus(timer,res.uuid,i++)
                  }, 1000)
                  //成功后重新设置数据
                  //this.getCluster();
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
      // this.$notify({
      //   title: '提示',
      //   message: '对节点【' + this.currentNode.text + '】进行了：' + actionName,
      //   type: 'success'
      // })
      // this.isShowNodeMenuPanel = false
    },
    getStatus (timer,data,i) {
      setTimeout(()=>{
        getEvStatus(data).then((res) => {
        if(res.res.result=='done'||res.res.result=='failed'){
          clearInterval(timer);
          this.info=res.res.info;
          if(res.res.result=='done'){
            this.getCluster();
          }else{
            this.installStatus = true;
          }
        }else{
          this.info=res.res.info;
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
</style>