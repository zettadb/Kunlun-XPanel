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
      <!-- <div class="c-node-menu-item" v-if="type==='shard'&& (storage_node_create_priv==='Y')" @click.stop="doAction('新增存储节点')">新增存储节点</div>-->
      <div class="c-node-menu-item" v-if="type==='shard'&&ha_mode==='rbr'&& (storage_node_create_priv==='Y')" @click.stop="doAction('重做备机节点')">重做备机节点</div> 
      <div class="c-node-menu-item" v-if="type==='shard'&&ha_mode==='rbr'" @click.stop="doAction('主备切换')">主备切换</div>
      <div class="c-node-menu-item" v-if="type==='cluster'&& (shard_create_priv==='Y')" @click.stop="doAction('新增存储集群')">新增存储集群</div>
      <div class="c-node-menu-item" v-if="type==='cluster'&& (compute_node_create_priv==='Y')" @click.stop="doAction('新增计算节点')">新增计算节点</div>
      <div class="c-node-menu-item" v-if="(type==='cnode'||type==='snode')&& (user_name=='super_dba')" @click.stop="doAction('启用')">启用</div>
      <div class="c-node-menu-item"  v-if="(type==='cnode'||type==='snode')&& (user_name=='super_dba'&&ha_mode==='mgr')" @click.stop="doAction('禁用')">禁用</div>
      <div class="c-node-menu-item"  v-if="(type==='cnode'||type==='snode')&& (user_name=='super_dba')" @click.stop="doAction('重启')">重启</div>
      <!-- <div class="c-node-menu-item"  v-if="(type==='shard'&& shard_drop_priv==='Y')&&(type==='cnode'&& compute_node_drop_priv==='Y')&&(type==='snode'&& storage_node_drop_priv==='Y')" @click.stop="doAction('删除')">删除</div> -->
      <div class="c-node-menu-item"  v-if="type==='shard'&& shard_drop_priv==='Y'" @click.stop="doAction('删除')">删除</div>
      <div class="c-node-menu-item"  v-if="type==='cnode'&& compute_node_drop_priv==='Y'" @click.stop="doAction('删除')">删除</div>
      <div class="c-node-menu-item"  v-else-if="type==='snode'&& storage_node_drop_priv==='Y'" @click.stop="doAction('删除')">删除</div>
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
        <el-form-item label="副本数:" prop="snode_count">
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
    <!--重做备机节点-->
    <el-dialog title="重做备机节点" :visible.sync="dialogRedoVisible" custom-class="single_dal_view">
      <el-form
        ref="redoForm"
        :model="redotemp"
        :rules="rules"
        label-position="left"
        label-width="180px"
      >
      <el-form-item label="需重做的备机节点:" prop="redolist" >
        <el-select v-model="redotemp.redolist" multiple placeholder="请选择">
          <el-option
            v-for="item in options"
            :key="item.value"
            :label="item.label"
            :value="item.label">
          </el-option>
        </el-select>
       </el-form-item>
       
       <el-form-item label="是否从主节点上拉取数据:" prop="allow_pull_from_master" >
        <el-radio v-model="redotemp.allow_pull_from_master" label="1">是</el-radio>
        <el-radio v-model="redotemp.allow_pull_from_master" label="0">否</el-radio>
      </el-form-item>
      <el-form-item label="主备延迟:" prop="allow_replica_delay"  v-if="redotemp.allow_pull_from_master=='0'">
        <el-input  v-model="redotemp.allow_replica_delay" class="right_input"  placeholder="主备延迟">
          <i slot="suffix" style="font-style:normal;margin-right: 10px; line-height: 30px;">s</i>
        </el-input>
      </el-form-item>
       <el-form-item label="是否备份:" prop="need_backup" >
        <el-radio v-model="redotemp.need_backup" label="1">是</el-radio>
        <el-radio v-model="redotemp.need_backup" label="0">否</el-radio>
      </el-form-item>
      <el-form-item label="备份存储目标:" prop="hdfs_host"  v-if="redotemp.need_backup=='1'">
        <el-select v-model="redotemp.hdfs_host" placeholder="请选择">
          <el-option
            v-for="item in hdfs_options"
            :key="item.value"
            :label="item.label"
            :value="item.label">
          </el-option>
        </el-select>
       </el-form-item>
      <el-form-item label="限速:" prop="pv_limit" >
        <el-input  v-model="redotemp.pv_limit" class="right_input"  placeholder="限速">
          <i slot="suffix" style="font-style:normal;margin-right: 10px; line-height: 30px;">KB/s</i>
        </el-input>
      </el-form-item>

      </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button @click="dialogRedoVisible = false">关闭</el-button>
          <el-button type="primary" @click="redoData(snodetemp)">确认</el-button>
        </div>
    </el-dialog>

     <!--主备切换-->
    <el-dialog title="主备切换" :visible.sync="dialogSwitchOVisible" custom-class="single_dal_view">
      <el-form
        ref="switchForm"
        :model="switchtemp"
        :rules="rules"
        label-position="left"
        label-width="100px"
      >
      <el-form-item label="主节点:" prop="primary_node">
        <span>{{switchtemp.primary_node}}</span>
      </el-form-item>
      <el-form-item label="备机节点:" prop="replica" >
        <el-select v-model="switchtemp.replica" clearable placeholder="请选择备机节点">
          <el-option
            v-for="item in options"
            :key="item.value"
            :label="item.label"
            :value="item.label">
          </el-option>
        </el-select>
      </el-form-item>
      </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button @click="dialogSwitchOVisible = false">关闭</el-button>
          <el-button type="primary" @click="switchData(switchtemp)">确认</el-button>
        </div>
    </el-dialog>
    <!--  状态框 -->
    <el-dialog :visible.sync="dialogStatusVisible" custom-class="single_dal_view" width="400px">
      <div class="block">
        <el-timeline>
          <el-timeline-item
            v-for="(activity, index) in activities"
            :key="index"
            :icon="activity.icon"
            :type="activity.type"
            :color="activity.color"
            :size="activity.size"
            :timestamp="activity.timestamp">
            {{activity.content}}
          </el-timeline-item>
        </el-timeline>
      </div>
    </el-dialog>
    <!-- 主备切换状态框 -->
    <el-dialog :title="job_id" :visible.sync="dialogStatusFVisible" custom-class="single_dal_view" :close-on-click-modal="false" :before-close="beforeSwitchDestory">
      <div style="height: 400px;">
        <el-steps direction="vertical" :active="active" finish-status="success">
          <el-step
              v-for="(item,index) of stepParams"
              :key="index"
              :title="item.title"
              :icon="item.icon"
              :status="item.status"
              :description="item.description"
              :class="stepSuc.includes(index) ? 'stepSuc':'stepErr'"
              @click.native="handleStep(index)"
          />
        </el-steps>
      </div>
      
    </el-dialog>
    <!-- 重做备机状态框 -->
     <el-dialog :title="job_id" :visible.sync="dialogStatusRedoVisible" custom-class="single_dal_view" :close-on-click-modal="false">
      <div style="width: 100%;background: #fff;padding:0 20px;">
        <el-steps direction="vertical" :active="init_active">
          <el-step 
          :title="items.title"  
          v-for="(items, key) in statusList" 
          :key="key" 
          :icon="items.icon" 
          :status="items.status"
          :description="items.description">
            <template slot="description">
              <span>{{items.description}}</span>
            <div style="padding:20px;">
              <el-steps direction="vertical" :active="second_active" >
                <el-step
                    v-for="(item,index) of secondStatus"
                    :key="index"
                    :title="item.title"
                    :icon="item.icon"
                    :status="item.status"
                    :description="item.description"
                >
                </el-step>
              </el-steps>
            </div>
            </template>
          </el-step>
          </el-steps>
      </div>
    </el-dialog>
    <!-- 删除状态框 -->
    <el-dialog :title="job_id" :visible.sync="dialogStatusShowVisible" custom-class="single_dal_view" :close-on-click-modal="false" :before-close="beforeDestory">
      <div style="width: 100%;background: #fff;padding:0 20px;">
        <el-steps direction="vertical" :active="init_active">
          <el-step :title="init_title" icon="el-icon-more" v-if="init_show"></el-step>
          <el-step :title="computer_title" :status="computer_state" :icon="computer_icon" :description="computer_description" v-if="computer_show">
            <template slot="description">
              <span>{{computer_description}}</span>
            <div style="padding:20px;">
              <el-steps direction="vertical" :active="comp_active" >
                <el-step
                    v-for="(item,index) of computer"
                    :key="index"
                    :title="item.title"
                    :icon="item.icon"
                    :status="item.status"
                    :description="item.description"
                >
                <!-- <template slot="description">
                  <el-button size="mini" type="primary"  @click="thisDetail(item.computer_id)">详情</el-button>
                </template> -->
                </el-step>
              </el-steps>
            </div>
            </template>
          </el-step>
          <el-step :title="shard_title"  :status="storage_state" :icon="shard_icon" :description="shard_description" v-if="shard_show">
            <template slot="description">
              <span>{{shard_description}}</span>
            <div style="padding:20px;">
              <el-steps direction="vertical" :active="shard_active" >
                <el-step
                    v-for="(item,index) of shard"
                    :key="index"
                    :title="item.title"
                    :icon="item.icon"
                    :status="item.status"
                    :description="item.description"
                    @click.native="thisDetail(item.shard_id)"
                >
                <!-- <template slot="description">
                  <el-button size="mini" type="primary" @click="thisDetail(item.shard_id)">详情</el-button>
                </template> -->
                </el-step>
              </el-steps>
            </div>
            </template>
          </el-step>
          <!-- <el-step :title="finish_title" :icon="finish_icon" :description="finish_description" color='red'></el-step> -->
        </el-steps>
      </div>
    </el-dialog>
  </div>
</template>
<script>
import { messageTip,handleCofirm,getNowDate,createCode,gotoCofirm} from "@/utils";
import {getClusterNodesList,getEffectCluster,getNodes,getAllMachine, getShards,getSnodeTotal,getStandbyNode,getShardPrimary,pgEnable,myEnable,delShard,delComp,delSnode,startComp,startSnode,stopComp,stopSnode,restartComp,restartSnode,getClusterDetail,switchShard,getStorageList,rebuildNode,getEvStatus,getShardsCount,getCompsCount,getNodesCount} from '@/api/cluster/list'
import SeeksRelationGraph from 'relation-graph'
//import {v4 as uuidv4 } from 'uuid';
import {version_arr,ip_arr,timestamp_arr} from "@/utils/global_variable"
//import {getEvStatus,pgEnable,myEnable,delShard,delComp,delSnode,startComp,startSnode,stopComp,stopSnode,restartComp,restartSnode,getClusterDetail,switchShard,getStorageList,rebuildNode} from '@/api/cluster/listInterface'
// import { Steps } from 'element-ui';
export default {
  name: 'Demo',
  components: { SeeksRelationGraph },
  data() {
    const validateredolist = (rule, value, callback) => {
     if(value.length === 0){
        callback(new Error("请选择需重做的备机节点"));
      }
      else {
        callback();
      }
    };
    const validatereplicadelay = (rule, value, callback) => {
     if(!value){
        callback(new Error("主备延迟不能为空"));
      }else if(!(/^[0-9]+$/.test(value))){
        callback(new Error("主备延迟只能输入数字"));
      }
      else {
        callback();
      }
    };
    const validatehdfshost = (rule, value, callback) => {
      if(value.length === 0){
        callback(new Error("请选择备份存储目标"));
      }
      else {
        callback();
      }
    };
    return {
      currentCase:1,
      clusters:[],
      nodeName:'',
      type:'',
      ha_mode:'',
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
      user_name:sessionStorage.getItem('login_username'),
      isShowCodePanel: false,
      isShowNodeTipsPanel: false,
      isShowNodeMenuPanel: false,
      nodeMenuPanelPosition: { x: 0, y: 0 },
      currentNode: {},
      dialogShardVisible:false,
      dialogCompVisible:false,
      dialogSnodeVisible:false,
      dialogRedoVisible:false,
      dialogSwitchOVisible:false,
      dialogStatusFVisible:false,
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
        redolist:[],
        allow_pull_from_master:'0',
        allow_replica_delay:'30',
        need_backup:'0',
        pv_limit:'10',
        hdfs_host:''   
      },
      switchtemp:{
          replica:'',
          primary_node:'',
          shard_id:'',
          cluster_id:'',
      },
      machinelist:[],
      machines:[],
      minMachine:0,
      machineTotal:0,
      rules: {},
      options: [],
      hdfs_options:[],
      status: [],
      dialogStatus: "",
      textMap: {
        update: "新增计算节点",
        create: "新增存储集群",
        detail: "新增存储节点",
        restore:'重做备机节点'
      },
      delpriv:'',
      user_name:sessionStorage.getItem('login_username'),
      g_loading: false,
      dialogStatusVisible:false,
      active: 0,
      // 已选步骤
      stepSuc: [0],
      // 步骤参数
      stepParams: [
        // {
        //   title: 'submit',
        //   icon: 'el-icon-circle-check',
        //   status: 'success',
        //   description:""
        // },
        // {
        //   title: 'degrade_master',
        //   icon: 'el-icon-circle-check',
        //   status: 'success',
        //   description:""
        // },
        // {
        //   title: 'report_relaying',
        //   icon: 'el-icon-circle-check',
        //   status: 'success',
        //   description:""
        // },
        // {
        //   title: 'apply_relaylog',
        //   icon: 'el-icon-circle-check',
        //   status: 'success',
        //   description:""
        // },
        // {
        //   title: 'select_new_master',
        //   icon: 'el-icon-circle-check',
        //   status: 'success',
        //   description:""
        // },
        // {
        //   title: 'change_slave_sync',
        //   icon: 'el-icon-circle-check',
        //   status: 'success',
        //   description:""
        // },
        // {
        //   title: 'Done',
        //   icon: 'el-icon-circle-check',
        //   status: 'success',
        //   description:""
        // },
      ],
      job_id:'',
      init_active:0,
      second_active:0,
      statusList:[
        // {
        //   title: '192.168.0.136_47001;',
        //   icon: 'el-icon-loading',
        //   status: 'process',
        //   description:'11'
        // },
        // {
        //   title: '192.168.0.136_37001;',
        //   icon: 'el-icon-loading',
        //   status: 'process',
        //   computer_id:'101',
        //   description:'22'
        // },
        // {
        //   title: '192.168.0.125_37001;',
        //   icon: 'el-icon-loading',
        //   status: 'process',
        //   computer_id:'101',
        //   description:''
        // },
      ],
      secondStatus:[
        // {
        //   title: '192.168.0.136_47001;',
        //   icon: 'el-icon-loading',
        //   status: 'process',
        //   computer_id:'101',
        //   description:''
        // },
        // {
        //   title: '192.168.0.136_37001;',
        //   icon: 'el-icon-loading',
        //   status: 'process',
        //   computer_id:'101',
        //   description:''
        // },
      ],
      dialogStatusRedoVisible:false,
      dialogStatusShowVisible:false,
      computer:[
      ],
       shard:[
      ],
      computer_state:'',
      storage_state:'',
      computer_title:'',
      computer_icon:'',
      shard_icon:'',
      shard_title:'',
      comp_active:0,
      shard_active:0,
      strogemachines:[],
      init_title:'',
      init_show:true,
      computer_show:true,
      shard_show:true,
      init_active:0,
      finish_title:'',
      finish_icon:'',
      finish_description:'',
      computer_description:'',
      shard_description:'',
      shardInfo:'',
      dialogShardInfo:false,

      demoname: '---',
      range_horizontal: [ 100, 300 ],
      range_vertical: [ 20, 100 ],
      activities:[],
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
          //'defaultNodeColor': 'rgba(255, 255, 255, 1)',//节点颜色
          'defaultNodeColor': 'rgba(176, 210, 237, 1)',//节点颜色
          'defaultNodeFontColor':'rgba(0, 0, 0, 1)',//字体的颜色
          'defaultNodeBorderColor':'rgba(255, 255, 255, 1)'
        // allowSwitchLineShape: true,//显示切换线条形状的按钮
        // allowSwitchJunctionPoint: true,//切换连接点位置的按钮
        //defaultJunctionPoint: 'border'
        // 这里可以参考"Graph 图谱"中的参数进行设置
      },
      rules: {
        redolist: [
          { required: true, trigger: "blur",validator: validateredolist },
        ],
        allow_replica_delay: [
          { required: true, trigger: "blur",validator: validatereplicadelay },
        ],
        hdfs_host: [
          { required: true, trigger: "blur",validator: validatehdfshost },
        ],
      },
    }
  },
  created(){
    this.getCluster();
  },
  methods: {
    beforeSwitchDestory(){
      //console.log('00:00');
      clearInterval(this.timer)
      this.dialogStatusFVisible=false;
    },
    //清除定时器
    beforeDestory(){
      //console.log('10:00');
      clearInterval(this.timer)
      this.dialogStatusShowVisible=false;
    },
    redoData(row) {
      this.$refs["redoForm"].validate((valid) => {
        if (valid) {
          let tempData = {};
          tempData.user_name = sessionStorage.getItem('login_username');
          tempData.job_id ='';
          tempData.job_type ='rebuild_node';
          tempData.version=version_arr[0].ver;
          tempData.timestamp=timestamp_arr[0].time+'';
          let rebuild=[];
          for(let i=0;i<this.redotemp.redolist.length;i++){
            let ip_arr=(this.redotemp.redolist[i].substring(0,this.redotemp.redolist[i].length-1)).split('(');
            let pv_limit='';
            if(!this.redotemp.pv_limit){
              pv_limit='10'
            }else{
              pv_limit=this.redotemp.pv_limit
            }
            let rb_paras={}
            if(this.redotemp.need_backup=='1'){
              const hdfs_host=(this.redotemp.hdfs_host.substring(0,this.redotemp.hdfs_host.length-1)).replace('(',':');
              rb_paras.hostaddr=ip_arr[0];
              rb_paras.port=ip_arr[1];
              rb_paras.need_backup=this.redotemp.need_backup;
              rb_paras.hdfs_host=hdfs_host;
              rb_paras.pv_limit=pv_limit
            }else{
              rb_paras.hostaddr=ip_arr[0];
              rb_paras.port=ip_arr[1];
              rb_paras.need_backup=this.redotemp.need_backup;
              rb_paras.pv_limit=pv_limit
            }
            rebuild.push(rb_paras)
          }
           let paras={}
          if(this.redotemp.allow_pull_from_master=='1'){
            paras.shard_id=this.redotemp.shard_id,
            paras.cluster_id=this.redotemp.cluster_id,
            paras.allow_pull_from_master=this.redotemp.allow_pull_from_master,
            paras.rb_nodes= rebuild
          }else{
            paras.shard_id=this.redotemp.shard_id;
            paras.cluster_id=this.redotemp.cluster_id;
            paras.allow_pull_from_master=this.redotemp.allow_pull_from_master;
            paras.allow_replica_delay=this.redotemp.allow_replica_delay;
            paras.rb_nodes= rebuild
          }
          tempData.paras=paras;
          this.dialogStatusRedoVisible=true;
          this.dialogRedoVisible=false;
          let i=0;
          const info='重做备机'
          this.secondStatus=[];this.timer=null;
          this.timer = setInterval(() => {
            this.getFStatus(this.timer,'1706',i++,info)
          }, 1000)
          console.log(tempData);return;
          //发送接口
          rebuildNode(tempData).then(response=>{
            let res = response;
            if(res.status=='accept'){
              this.dialogRedoVisible = false;
              this.stepParams=[];
              let i=0;
              const info='重做备机';this.timer=null;
              this.timer= setInterval(() => {
                this.getFStatus(this.timer,res.job_id,i++,info)
              }, 1000)
            }
            else{
              this.message_tips = res.error_info;
              this.message_type = 'error';
              messageTip(this.message_tips,this.message_type);
            }
          })
        }
      });
    },
    switchData(row) {
      this.$refs["switchForm"].validate((valid) => {
        if (valid) {
          //this.dialogStatusVisible=true;
          const tempData = {};
          tempData.user_name = sessionStorage.getItem('login_username');
          tempData.job_id ='';
          tempData.job_type ='manual_switch';
          tempData.version=version_arr[0].ver;
          tempData.timestamp=timestamp_arr[0].time+'';
          let master_ip=(this.switchtemp.primary_node.substring(0,this.switchtemp.primary_node.length-1)).replace('(','_');
          let paras={};
          if(!this.switchtemp.replica.length){
            paras.shard_id=this.switchtemp.shard_id;
            paras.cluster_id=this.switchtemp.cluster_id;
            paras.master_hostaddr=master_ip;
          }else{
            let assign_ip=(this.switchtemp.replica.substring(0,this.switchtemp.replica.length-1)).replace('(','_');
            paras.shard_id=this.switchtemp.shard_id;
            paras.cluster_id=this.switchtemp.cluster_id;
            paras.master_hostaddr=master_ip;
            paras.assign_hostaddr=assign_ip;
          }
          tempData.paras=paras;
          //console.log(tempData);return;
          //发送接口
          switchShard(tempData).then(response=>{
            let res = response;
            if(res.status=='accept'){
              this.dialogSwitchOVisible = false;
              // this.activities=[];
              // const newArr={
              //   content:'正在进行主备切换...',
              //   timestamp: getNowDate(),
              //   size: 'large',
              //   type: 'primary',
              //   icon: 'el-icon-more'
              // };
              // this.activities.push(newArr);
              // this.message_tips = '正在进行主备切换...';
              // this.message_type = 'success';
              this.stepParams=[];
              let i=0;
              const info='主备切换';
              this.computer=[];this.shard=[];this.computer_state='';this.storage_state='';this.computer_title='';this.computer_icon='';this.shard_icon='';this.shard_title='';this.comp_active=0;this.shard_active=0;this.strogemachines=[];this.init_title='';this.init_active=0;this.finish_title='';this.finish_icon='';this.finish_description='';this.computer_description='';this.shard_description='';this.job_id='';this.timer=null;
              this.timer = setInterval(() => {
                this.getFStatus(this.timer,res.job_id,i++,info)
              }, 1000)
            }
            else{
              this.message_tips = res.error_info;
              this.message_type = 'error';
              messageTip(this.message_tips,this.message_type);
            }
          })
        }
      });
    },
    matchState(state = '', reg) {
     return !!String(state).match(reg)//返回true/false
    },
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
      //console.log(node.data);return;
      const tempData = {};
      tempData.shard_id =node.data.shard_id;
      tempData.cluster_id =node.data.cluster_id;
      tempData.tree_id =node.id;
      tempData.ha_mode=node.data.ha_mode;
      getNodes(tempData).then((response)=>{
        let res = response;
        if(res.code==200){
          let status=[];
          let arr = response.list;
          //调接口获取主备且在线情况
          // const temp={
          //   job_id:'',
          //   paras:{'cluster_name':arr.nodes[0].data.cluster_name},
          //   version:version_arr[0].ver,
          //   timestamp:timestamp_arr[0].time+'',
          //   job_type:'get_cluster_detail'
          // }
          // getClusterDetail(temp).then((response) => {
          //     status = response.attachment.list_shard;
          //     for (let i = 0; i < arr.nodes.length; i++) {
          //     let param = arr.nodes[i];
          //     if(param.id.indexOf('snode')!=-1){
          //       this.$set(param.data, 'icon', 'iconfont icon-snode')
          //       //this.$set(param.data, 'color', '#e98f36')
          //        for (let j = 0; j < status.length; j++) {
          //           const shard_name=status[j].shard_name;
          //           for (let k = 0; k < status[j].list_node.length; k++) {
          //             const ip=status[j].list_node[k].hostaddr;
          //             const port=status[j].list_node[k].port;
          //             if(shard_name===param.data.shard_name&&ip===param.data.hostaddr&&port===param.text){
          //               this.$set(param.data, 'status', status[j].list_node[k].status)
          //               this.$set(param.data, 'master', status[j].list_node[k].master)
          //             }
          //           }
          //        }
          //       if(param.data.master=='true'){
          //         this.$set(param.data, 'color', '#ff0000')
          //       }else if(param.data.master=='false'){
          //         this.$set(param.data, 'color', '#e98f36')
          //       }else {
          //         this.$set(param.data, 'color', '#acacac')
          //       }
          //     }
          //     console.log(param);
          //   }
          // })
          
          for (let i = 0; i < arr.nodes.length; i++) {
            let param = arr.nodes[i];
            if(param.id.indexOf('snode')!=-1){
              this.$set(param.data, 'icon', 'iconfont icon-snode')
              this.$set(param.data, 'color', '#e98f36')
              if(param.data.master=='true'){
                this.$set(param.data, 'color', '#ff0000')
              }else if(param.data.master=='false'){
                this.$set(param.data, 'color', '#e98f36')
              }else {
                this.$set(param.data, 'color', '#acacac')
              }
            }
          }
          
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
      this.currentNode = nodeObject
      var _base_position = this.$refs.myPage.getBoundingClientRect()
      if(this.currentNode.id=='cluster' ||this.currentNode.text.indexOf('shard') !=-1){
        if((this.currentNode.data.name=='shard'&&this.shard_drop_priv!=='Y'&&this.currentNode.data.ha_mode)){
          this.isShowNodeMenuPanel = false
        }
        else if(this.currentNode.id=='cluster'){//cluster not display
          this.isShowNodeMenuPanel = false
        }
        else{
          this.isShowNodeMenuPanel = true
          this.nodeName=this.currentNode.text;
          if(this.currentNode.text.indexOf('shard') !=-1) {
            this.type='shard'
            this.ha_mode=this.currentNode.data.ha_mode;
          }else if(this.currentNode.id=='cluster'){
            this.type='cluster'
          }
          this.nodeMenuPanelPosition.x = $event.clientX - _base_position.x
          this.nodeMenuPanelPosition.y = $event.clientY - _base_position.y
        }
      }
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
          tempData['job_id'] = '';
          tempData['job_type'] ='postgres_exporter';
          tempData['version']=version_arr[0].ver;
          tempData['timestamp']=timestamp_arr[0].time+'';
          tempData['user_name']=sessionStorage.getItem('login_username');
          const paras={};
          paras['hostaddr']=this.currentNode.data.hostaddr;
          paras['port']=this.currentNode.data.port;
          tempData['paras']=paras;
          const mysqlData = {};//mysql
          mysqlData['job_id'] = '';
          mysqlData['job_type'] ='mysqld_exporter';
          mysqlData['version']=version_arr[0].ver;
          mysqlData['timestamp']=timestamp_arr[0].time+'';
          mysqlData['user_name']=sessionStorage.getItem('login_username');
          const mparas={};
          mparas['hostaddr']=this.currentNode.data.hostaddr;
          mparas['port']=this.currentNode.data.port;
          mysqlData['paras'] =mparas;
          const prometheus = {};//prometheus
          prometheus['job_id'] = '';
          prometheus['job_type']='update_prometheus';
          prometheus['version']=version_arr[0].ver;
          prometheus['timestamp']=timestamp_arr[0].time+'';
          prometheus['user_name']=sessionStorage.getItem('login_username');
          prometheus['paras']={};

          if(this.currentNode.data.name==='pgsql'){
            pgEnable(tempData).then((pgres) => {
              if(pgres.status=='accept'){
                 //调获取状态接口
                  let i=0;
                  let timer = setInterval(() => {
                      setTimeout(()=>{
                        const postarr={};
                        postarr.job_type='get_status';
                        postarr.version=version_arr[0].ver;
                        postarr.job_id=pgres.job_id;
                        postarr.timestamp=timestamp_arr[0].time+'';
                        postarr.paras={};
                        getEvStatus(postarr).then((res) => {
                        if(res.status=='done'||res.status=='failed'){
                          clearInterval(timer);
                          this.dialogStatusVisible=true;
                          this.activities=[];
                          //this.info=res.error_info;
                          if(res.status=='done'){
                            const newArr={
                              content:res.error_info,
                              timestamp: getNowDate(),
                              color: '#0bbd87',
                              icon: 'el-icon-circle-check'
                            };
                            this.activities.push(newArr);
                            this.dialogStatusVisible=false;
                            //跳转新页面pgsql
                            window.open('http://'+ip_arr[0].ip+'/d/postgresql/postgresql?orgId=1&refresh=5s');
                          }else{
                             const newArr={
                              content:res.error_info,
                              timestamp: getNowDate(),
                              color: 'red',
                              icon: 'el-icon-circle-close'
                            };
                            this.activities.push(newArr);
                            //this.installStatus = true;
                          }
                        }else{
                          const newArrgoing={
                            content:res.error_info,
                            timestamp: getNowDate(),
                            color: '#0bbd87'
                          };
                          this.activities.push(newArrgoing)
                          //this.info=res.error_info;
                          //this.installStatus = true;
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
              if(myres.status='accept'){
                 //调获取状态接口
                  let i=0;
                  let timer = setInterval(() => {
                     setTimeout(()=>{
                        const postarr={};
                        postarr.job_type='get_status';
                        postarr.version=version_arr[0].ver;
                        postarr.job_id=myres.job_id;
                        postarr.timestamp=timestamp_arr[0].time+'';
                        postarr.paras={};
                        getEvStatus(postarr).then((res) => {
                        if(res.status=='done'||res.status=='failed'){
                          clearInterval(timer);
                          this.dialogStatusVisible=true;
                          this.activities=[];
                          //this.info=res.error_info;
                          if(res.status=='done'){
                            const newArr={
                              content:res.error_info,
                              timestamp: getNowDate(),
                              color: '#0bbd87',
                              icon: 'el-icon-circle-check'
                            };
                            this.activities.push(newArr);
                            this.dialogStatusVisible=false;
                            //跳转新页面mysql
                            window.open('http://'+ip_arr[0].ip+'/d/mysql/mysql?orgId=1&refresh=5s');
                          }else{
                            const newArr={
                              content:res.error_info,
                              timestamp: getNowDate(),
                              color: 'red',
                              icon: 'el-icon-circle-close'
                            };
                            this.activities.push(newArr);
                            //this.installStatus = true;
                          }
                        }else{
                          const newArrgoing={
                            content:res.error_info,
                            timestamp: getNowDate(),
                            color: '#0bbd87'
                          };
                          this.activities.push(newArrgoing)
                          //this.info=res.error_info;
                          //this.installStatus = true;
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
          //先判断该集群是否只有一个shard，如果只有一个shard不予许删除
          let temp={id:this.currentNode.data.cluster_id};
          getShardsCount(temp).then((res) => {
            if(res.total==1){
              messageTip('该集群当前有且仅有一个shard,不能进行删除操作','error');
            }else if(res.total>1){
              const code=createCode();
              let string="此操作将永久删除"+this.currentNode.text+",是否继续?code="+code;
              gotoCofirm(string).then( (res) =>{
              //先执行删权限
                if(!res.value){
                  this.message_tips = 'code不能为空！';
                  this.message_type = 'error';
                  messageTip(this.message_tips,this.message_type);
                }else if(res.value==code){
              //handleCofirm("此操作将永久删除"+this.currentNode.text+", 是否继续?").then( () =>{
                  const tempData = {};
                  tempData.user_name = sessionStorage.getItem('login_username');
                  tempData.job_id ='';
                  tempData.job_type ='delete_shard';
                  tempData.version=version_arr[0].ver;
                  tempData.timestamp=timestamp_arr[0].time+'';
                  const paras={}
                  paras.cluster_id=this.currentNode.data.cluster_id;
                  paras.shard_id=this.currentNode.data.shard_id;
                  tempData.paras=paras;
                  //console.log(tempData);return;
                  delShard(tempData).then((response)=>{
                    let res = response;
                    if(res.status='accept'){
                      // this.dialogStatusVisible=true;
                      // this.activities=[];
                      // const newArr={
                      //   content:'正在删除'+this.currentNode.text,
                      //   timestamp: getNowDate(),
                      //   size: 'large',
                      //   type: 'primary',
                      //   icon: 'el-icon-more'
                      // };
                      // this.activities.push(newArr);
                      //this.message_tips = '正在删除'+this.currentNode.text;
                      //this.message_type = 'success';
                      this.isShowNodeMenuPanel = false
                      this.dialogStatusShowVisible=true;
                      //调获取状态接口
                      let i=0;
                      this.computer=[];this.shard=[];this.computer_state='';this.storage_state='';this.computer_title='';this.computer_icon='';this.shard_icon='';this.shard_title='';this.comp_active=0;this.shard_active=0;this.strogemachines=[];this.init_title='';this.init_active=0;this.finish_title='';this.finish_icon='';this.finish_description='';this.computer_description='';this.shard_description='';this.job_id='';this.timer=null;
                      const info='删除shard'
                      this.getFStatus(this.timer,res.job_id,i++,info)
                      this.timer = setInterval(() => {
                        this.getFStatus(this.timer,res.job_id,i++,info)
                      }, 1000)
                    }
                    else{
                      this.message_tips = res.error_info;
                      this.message_type = 'error';
                      messageTip(this.message_tips,this.message_type);
                    }
                  })
                }else{
                  this.message_tips = 'code输入有误';
                  this.message_type = 'error';
                  messageTip(this.message_tips,this.message_type);
                }
              }).catch(() => {
                  console.log('quxiao')
                  messageTip('已取消删除','info');
              }); 
            }
          });
        }else if(this.currentNode.data.name==='pgsql'){
          //先判断该集群是否只有一个计算节点，如果只有一个计算节点不予许删除
          let temp={id:this.currentNode.data.cluster_id};
          getCompsCount(temp).then((res) => {
            if(res.total==1){
              messageTip('该集群当前有且仅有一个计算节点,不能进行删除操作','error');
            }else if(res.total>1){
              const code=createCode();
              let string="此操作将永久删除计算节点"+this.currentNode.data.hostaddr+"("+this.currentNode.text+"), 是否继续?code="+code;
              gotoCofirm(string).then( (res) =>{
                //先执行删权限
                if(!res.value){
                  this.message_tips = 'code不能为空！';
                  this.message_type = 'error';
                  messageTip(this.message_tips,this.message_type);
                }else if(res.value==code){
                  const tempData = {};
                  tempData.user_name = sessionStorage.getItem('login_username');
                  tempData.job_id ='';
                  tempData.job_type ='delete_comp';
                  tempData.version=version_arr[0].ver;
                  tempData.timestamp=timestamp_arr[0].time+'';
                  const paras={};
                  paras.cluster_id=this.currentNode.data.cluster_id;
                  paras.nick_name=this.currentNode.data.nick_name;
                  paras.comp_id=this.currentNode.data.comp_id;
                  tempData.paras=paras;
                  //console.log(tempData);return;
                  delComp(tempData).then((response)=>{
                    let res = response;
                    if(res.status=='accept'){
                      // this.dialogStatusVisible=true;
                      // this.activities=[];
                      // const newArr={
                      //   content:'正在删除计算节点'+this.currentNode.text,
                      //   timestamp: getNowDate(),
                      //   size: 'large',
                      //   type: 'primary',
                      //   icon: 'el-icon-more'
                      // };
                      // this.activities.push(newArr);
                      //this.message_tips = '正在删除计算节点'+this.currentNode.text;
                      //this.message_type = 'success';
                      this.isShowNodeMenuPanel = false
                      this.dialogStatusShowVisible=true;
                      //调获取状态接口
                      let i=0;
                      this.computer=[];this.shard=[];this.computer_state='';this.storage_state='';this.computer_title='';this.computer_icon='';this.shard_icon='';this.shard_title='';this.comp_active=0;this.shard_active=0;this.strogemachines=[];this.init_title='';this.init_active=0;this.finish_title='';this.finish_icon='';this.finish_description='';this.computer_description='';this.shard_description='';this.job_id='';this.timer=null;
                      const info='删除计算节点'
                      this.getFStatus(this.timer,res.job_id,i++,info)
                      this.timer = setInterval(() => {
                        this.getFStatus(this.timer,res.job_id,i++,info)
                      }, 1000)
                    }
                    else{
                      this.message_tips = res.error_info;
                      this.message_type = 'error';
                      messageTip(this.message_tips,this.message_type);
                    }
                  })
                }else{
                  this.message_tips = 'code输入有误';
                  this.message_type = 'error';
                  messageTip(this.message_tips,this.message_type);
                }
              }).catch(() => {
              console.log('quxiao')
              messageTip('已取消删除','info');
              });
            }
          });
          //     handleCofirm("此操作将永久删除计算节点"+this.currentNode.text+", 是否继续?").then( () =>{
          //     const tempData = {};
          //     tempData.user_name = sessionStorage.getItem('login_username');
          //     tempData.job_id ='';
          //     tempData.job_type ='delete_comp';
          //     tempData.version=version_arr[0].ver;
          //     tempData.timestamp=timestamp_arr[0].time+'';
          //     const paras={};
          //     paras.cluster_id=this.currentNode.data.cluster_id;
          //     paras.nick_name=this.currentNode.data.nick_name;
          //     paras.comp_id=this.currentNode.data.comp_id;
          //     tempData.paras=paras;
          //     //console.log(tempData);return;
          //     delComp(tempData).then((response)=>{
          //       let res = response;
          //       if(res.status=='accept'){
          //         this.dialogStatusVisible=true;
          //         this.activities=[];
          //         const newArr={
          //           content:'正在删除计算节点'+this.currentNode.text,
          //           timestamp: getNowDate(),
          //           size: 'large',
          //           type: 'primary',
          //           icon: 'el-icon-more'
          //         };
          //         this.activities.push(newArr);
          //         //this.message_tips = '正在删除计算节点'+this.currentNode.text;
          //         //this.message_type = 'success';
          //         this.isShowNodeMenuPanel = false
          //         //调获取状态接口
          //         let i=0;
          //         let timer = setInterval(() => {
          //           this.getStatus(timer,res.job_id,i++)
          //         }, 1000)
          //       }
          //       else{
          //         this.message_tips = res.error_info;
          //         this.message_type = 'error';
          //       }
          //       messageTip(this.message_tips,this.message_type);
          //   })
          // }).catch(() => {
          //     console.log('quxiao')
          //     messageTip('已取消删除','info');
          // }); 

        }else if(this.currentNode.data.name==='mysql'){
          //先判断该集群是否只有一个存储节点，如果只有一个存储节点不予许删除
          let temp={id:this.currentNode.data.cluster_id,shard_id:this.currentNode.data.shard_id};
          getNodesCount(temp).then((res) => {
            if(res.total==1){
              messageTip('该集群当前有且仅有一个存储节点,不能进行删除操作','error');
            }else if(res.total>1){
              const code=createCode();
              let string="此操作将永久删除存储节点"+this.currentNode.data.hostaddr+"("+this.currentNode.text+"),是否继续?code="+code;
              gotoCofirm(string).then( (res) =>{
                //先执行删权限
                if(!res.value){
                  this.message_tips = 'code不能为空！';
                  this.message_type = 'error';
                  messageTip(this.message_tips,this.message_type);
                }else if(res.value==code){
                  const tempData = {};
                  tempData.user_name = sessionStorage.getItem('login_username');
                  tempData.job_id ='';
                  tempData.job_type ='delete_node';
                  tempData.version=version_arr[0].ver;
                  tempData.timestamp=timestamp_arr[0].time+'';
                  const paras={};
                  paras.cluster_id=this.currentNode.data.cluster_id;
                  paras.shard_id=this.currentNode.data.shard_id;
                  paras.hostaddr=this.currentNode.data.hostaddr;
                  paras.port=this.currentNode.data.port;
                  paras.nick_name=this.currentNode.data.nick_name;
                  tempData.paras=paras;
                  //console.log(tempData);return;
                  delSnode(tempData).then((response)=>{
                    let res = response;
                    if(res.status=='accept'){
                      // this.dialogStatusVisible=true;
                      // this.activities=[];
                      // const newArr={
                      //   content:'正在删除存储节点'+this.currentNode.text,
                      //   timestamp: getNowDate(),
                      //   size: 'large',
                      //   type: 'primary',
                      //   icon: 'el-icon-more'
                      // };
                      // this.activities.push(newArr);
                      //this.message_tips = '正在删除存储节点'+this.currentNode.text;
                      //this.message_type = 'success';
                      this.isShowNodeMenuPanel = false
                      this.dialogStatusShowVisible=true;
                      //调获取状态接口
                      let i=0;
                      this.computer=[];this.shard=[];this.computer_state='';this.storage_state='';this.computer_title='';this.computer_icon='';this.shard_icon='';this.shard_title='';this.comp_active=0;this.shard_active=0;this.strogemachines=[];this.init_title='';this.init_active=0;this.finish_title='';this.finish_icon='';this.finish_description='';this.computer_description='';this.shard_description='';this.job_id='';this.timer=null;
                      const info='删除存储节点'
                      this.getFStatus(this.timer,res.job_id,i++,info)
                      this.timer = setInterval(() => {
                        this.getFStatus(this.timer,res.job_id,i++,info)
                      }, 1000)
                    }
                    else{
                      this.message_tips = res.error_info;
                      this.message_type = 'error';
                      messageTip(this.message_tips,this.message_type);
                    }
                  })
                }else{
                  this.message_tips = 'code输入有误';
                  this.message_type = 'error';
                  messageTip(this.message_tips,this.message_type);
                }
              }).catch(() => {
              console.log('quxiao')
              messageTip('已取消删除','info');
              });
            }
          });

          // handleCofirm("此操作将永久删除存储节点"+this.currentNode.text+", 是否继续?").then( () =>{
          //   const tempData = {};
          //     tempData.user_name = sessionStorage.getItem('login_username');
          //     tempData.job_id ='';
          //     tempData.job_type ='delete_node';
          //     tempData.version=version_arr[0].ver;
          //     tempData.timestamp=timestamp_arr[0].time+'';
          //     const paras={};
          //     paras.cluster_id=this.currentNode.data.cluster_id;
          //     paras.shard_id=this.currentNode.data.shard_id;
          //     paras.hostaddr=this.currentNode.data.hostaddr;
          //     paras.port=this.currentNode.data.port;
          //     paras.nick_name=this.currentNode.data.nick_name;
          //     tempData.paras=paras;
          //     //console.log(tempData);return;
          //     delSnode(tempData).then((response)=>{
          //       let res = response;
          //       if(res.status=='accept'){
          //         this.dialogStatusVisible=true;
          //         this.activities=[];
          //         const newArr={
          //           content:'正在删除存储节点'+this.currentNode.text,
          //           timestamp: getNowDate(),
          //           size: 'large',
          //           type: 'primary',
          //           icon: 'el-icon-more'
          //         };
          //         this.activities.push(newArr);
          //         //this.message_tips = '正在删除存储节点'+this.currentNode.text;
          //         //this.message_type = 'success';
          //         this.isShowNodeMenuPanel = false
          //         //调获取状态接口
          //         let i=0;
          //         let timer = setInterval(() => {
          //           this.getStatus(timer,res.job_id,i++)
          //         }, 2000)
          //       }
          //       else{
          //         this.message_tips = res.error_info;
          //         this.message_type = 'error';
          //       }
          //       messageTip(this.message_tips,this.message_type);
          //   })
          // }).catch(() => {
          //     console.log('quxiao')
          //     messageTip('已取消删除','info');
          // }); 
        }
      }
      if(actionName==='启用'){
         if(this.currentNode.data.name==='pgsql'){
          handleCofirm("是否继续启用"+this.currentNode.text+"该计算节点?").then( () =>{
            const tempData = {};
              tempData.user_name = sessionStorage.getItem('login_username');
              tempData.job_id ='';
              tempData.job_type ='control_instance';
              tempData.version=version_arr[0].ver;
              tempData.timestamp=timestamp_arr[0].time+'';
              const paras={};
              paras.control='start';
              paras.hostaddr=this.currentNode.data.hostaddr;
              paras.port=this.currentNode.data.port;
              //paras.type=this.currentNode.data.name;
              tempData.paras=paras;
              let arr={
                list:tempData,
                type:'pgsql'
              }
              //console.log(arr);return;
              startComp(arr).then((response)=>{
                let res = response;
                if(res.status=='accept'){
                  this.dialogStatusVisible=true;
                  this.activities=[];
                  const newArr={
                    content:'正在启动计算节点'+this.currentNode.text,
                    timestamp: getNowDate(),
                    size: 'large',
                    type: 'primary',
                    icon: 'el-icon-more'
                  };
                  this.activities.push(newArr);
                  //this.message_tips = '正在启动计算节点'+this.currentNode.text;
                  //this.message_type = 'success';
                  //调获取状态接口
                  let i=0;
                  let timer = setInterval(() => {
                    this.getStatus(timer,res.job_id,i++)
                  }, 1000)
                  this.isShowNodeMenuPanel = false
                }
                else{
                  this.message_tips = res.error_info;
                  this.message_type = 'error';
                  messageTip(this.message_tips,this.message_type);
                }
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
              tempData.job_id ='';
              tempData.job_type ='control_instance';
              tempData.version=version_arr[0].ver;
              tempData.timestamp=timestamp_arr[0].time+'';
              const paras={};
              paras.control='start';
              paras.hostaddr=this.currentNode.data.hostaddr;
              paras.port=this.currentNode.data.port;
              //paras.type=this.currentNode.data.name;
              tempData.paras=paras;
              let arr={
                list:tempData,
                type:'mysql'
              }
              startSnode(arr).then((response)=>{
                let res = response;
                if(res.status=='accept'){
                  this.dialogStatusVisible=true;
                  this.activities=[];
                  const newArr={
                    content:'正在启动存储节点'+this.currentNode.text,
                    timestamp: getNowDate(),
                    size: 'large',
                    type: 'primary',
                    icon: 'el-icon-more'
                  };
                  this.activities.push(newArr);
                  //this.message_tips = '正在启动存储节点'+this.currentNode.text;
                  //this.message_type = 'success';
                  //调获取状态接口
                  let i=0;
                  let timer = setInterval(() => {
                    this.getStatus(timer,res.job_id,i++)
                  }, 1000)
                  this.isShowNodeMenuPanel = false
                }
                else{
                  this.message_tips = res.error_info;
                  this.message_type = 'error';
                  messageTip(this.message_tips,this.message_type);
                }
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
              tempData.job_id ='';
              tempData.job_type ='control_instance';
              tempData.version=version_arr[0].ver;
              tempData.timestamp=timestamp_arr[0].time+'';
              const paras={};
              paras.control='stop';
              paras.hostaddr=this.currentNode.data.hostaddr;
              paras.port=this.currentNode.data.port;
              //paras.type=this.currentNode.data.name;
              tempData.paras=paras;
              let arr={
                list:tempData,
                type:'pgsql'
              }
              stopComp(arr).then((response)=>{
                let res = response;
                if(res.status=='accept'){
                  this.dialogStatusVisible=true;
                  this.activities=[];
                  const newArr={
                    content:'正在禁用计算节点'+this.currentNode.text,
                    timestamp: getNowDate(),
                    size: 'large',
                    type: 'primary',
                    icon: 'el-icon-more'
                  };
                  this.activities.push(newArr);
                  //this.message_tips = '正在禁用计算节点'+this.currentNode.text;
                  //this.message_type = 'success';
                  //调获取状态接口
                  let i=0;
                  let timer = setInterval(() => {
                    this.getStatus(timer,res.job_id,i++)
                  }, 1000)
                  //成功后重新设置数据
                  //this.getCluster();
                  this.isShowNodeMenuPanel = false
                }
                else{
                  this.message_tips = res.error_info;
                  this.message_type = 'error';
                  messageTip(this.message_tips,this.message_type);
                }
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
              tempData.job_id ='';
              tempData.job_type ='control_instance';
              tempData.version=version_arr[0].ver;
              tempData.timestamp=timestamp_arr[0].time+'';
              const paras={};
              paras.control='stop';
              paras.hostaddr=this.currentNode.data.hostaddr;
              paras.port=this.currentNode.data.port;
              //paras.type=this.currentNode.data.name;
              tempData.paras=paras;
              let arr={
                list:tempData,
                type:'mysql'
              }
              stopSnode(arr).then((response)=>{
                let res = response;
                if(res.status=='accept'){
                  this.dialogStatusVisible=true;
                  this.activities=[];
                  const newArr={
                    content:'正在禁用存储节点'+this.currentNode.text,
                    timestamp: getNowDate(),
                    size: 'large',
                    type: 'primary',
                    icon: 'el-icon-more'
                  };
                  this.activities.push(newArr);
                  //this.message_tips = '正在禁用存储节点'+this.currentNode.text;
                  //this.message_type = 'success';
                  this.isShowNodeMenuPanel = false
                  //调获取状态接口
                  let i=0;
                  let timer = setInterval(() => {
                    this.getStatus(timer,res.job_id,i++)
                  }, 1000)
                }
                else{
                  this.message_tips = res.error_info;
                  this.message_type = 'error';
                  messageTip(this.message_tips,this.message_type);
                }
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
              tempData.job_id ='';
              tempData.job_type ='control_instance';
              tempData.version=version_arr[0].ver;
              tempData.timestamp=timestamp_arr[0].time+'';
              const paras={};
              paras.control='restart';
              paras.hostaddr=this.currentNode.data.hostaddr;
              paras.port=this.currentNode.data.port;
              //paras.type=this.currentNode.data.name;
              tempData.paras=paras;
              let arr={
                list:tempData,
                type:'pgsql'
              }
              restartComp(arr).then((response)=>{
                let res = response;
                if(res.status=='accept'){
                  this.dialogStatusVisible=true;
                  this.activities=[];
                  const newArr={
                    content:'正在重启计算节点'+this.currentNode.text,
                    timestamp: getNowDate(),
                    size: 'large',
                    type: 'primary',
                    icon: 'el-icon-more'
                  };
                  this.activities.push(newArr);
                  //this.message_tips = '正在重启计算节点'+this.currentNode.text;
                  //this.message_type = 'success';
                  //调获取状态接口
                  let i=0;
                  let timer = setInterval(() => {
                    this.getStatus(timer,res.job_id,i++)
                  }, 1000)
                  this.isShowNodeMenuPanel = false
                }
                else{
                  this.message_tips = res.error_info;
                  this.message_type = 'error';
                  messageTip(this.message_tips,this.message_type);
                }
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
              tempData.job_id ='';
              tempData.job_type ='control_instance';
              tempData.version=version_arr[0].ver;
              tempData.timestamp=timestamp_arr[0].time+'';
              const paras={};
              paras.control='restart';
              paras.hostaddr=this.currentNode.data.hostaddr;
              paras.port=this.currentNode.data.port;
              //paras.type=this.currentNode.data.name;
              tempData.paras=paras;
              let arr={
                list:tempData,
                type:'mysql'
              }
              restartSnode(arr).then((response)=>{
                let res = response;
                if(res.status=='accept'){
                  this.dialogStatusVisible=true;
                  this.activities=[];
                  const newArr={
                    content:'正在重启存储节点'+this.currentNode.text,
                    timestamp: getNowDate(),
                    size: 'large',
                    type: 'primary',
                    icon: 'el-icon-more'
                  };
                  this.activities.push(newArr);
                  //this.message_tips = '正在重启存储节点'+this.currentNode.text;
                  //this.message_type = 'success';
                  this.isShowNodeMenuPanel = false
                  //调获取状态接口
                  let i=0;
                  let timer = setInterval(() => {
                    this.getStatus(timer,res.job_id,i++)
                  }, 1000)
                }
                else{
                  this.message_tips = res.error_info;
                  this.message_type = 'error';
                  messageTip(this.message_tips,this.message_type);
                }
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
      if(actionName==='重做备机节点'){
        //this.redotemp.shard_name=this.currentNode.text;
        this.redotemp.shard_id=this.currentNode.data.shard_id;
        this.redotemp.cluster_id=this.currentNode.data.cluster_id;
        this.dialogRedoVisible=true;
        //获取计算机名称
        // getAllMachine().then((res) => {
        // this.machines = res.list;
        //   this.minMachine=0;
        //   this.machineTotal=res.total;
        // });
        const temp={};
        temp.version=version_arr[0].ver;
        temp.job_id='';
        temp.job_type='get_backup_storage';
        temp.timestamp=timestamp_arr[0].time+'';
        temp.paras={};
      //   getStorageList(temp).then(response => {
      //     this.hdfs_options=[];
      //     if(response.attachment.list_backup_storage!==null){
      //       for (let i = 0; i < response.attachment.list_backup_storage.length; i++) {
      //          const arr={'value':i,'label':response.attachment.list_backup_storage[i].hostaddr+'('+response.attachment.list_backup_storage[i].port+')'};
      //          this.hdfs_options.push(arr);
      //       }
      //     }
      // });
        //获取备机节点
        const temps={};
        temps.cluster_id=this.currentNode.data.cluster_id;
        temps.shard_id=this.currentNode.data.shard_id;
        temps.ha_mode=this.currentNode.data.ha_mode;
        getStandbyNode(temps).then((res) => {
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
      }
      if(actionName==='主备切换'){
        this.redotemp.shard_name=this.currentNode.text;
        this.dialogSwitchOVisible=true;
        //获取主节点
        const temp={};
        temp.cluster_id=this.currentNode.data.cluster_id;
        temp.shard_id=this.currentNode.data.shard_id;
        temp.ha_mode=this.currentNode.data.ha_mode;
        this.switchtemp.shard_id=this.currentNode.data.shard_id;
        this.switchtemp.cluster_id=this.currentNode.data.cluster_id;
        getShardPrimary(temp).then((res) => {
          if(res.list.length>0){
             this.switchtemp.primary_node=res.list[0].ip+'('+res.list[0].port+')';
          }
        });
        //获取备机节点
        const temps={};
        temps.cluster_id=this.currentNode.data.cluster_id;
        temps.shard_id=this.currentNode.data.shard_id;
        temps.ha_mode=this.currentNode.data.ha_mode;
        getStandbyNode(temps).then((res) => {
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
      }
      
      // this.$notify({
      //   title: '提示',
      //   message: '对节点【' + this.currentNode.text + '】进行了：' + actionName,
      //   type: 'success'
      // })
      // this.isShowNodeMenuPanel = false
    },
    getFStatus (timer,data,i,info) {
      setTimeout(()=>{
        const postarr={}
        postarr.job_type='get_status';
        postarr.version=version_arr[0].ver;
        postarr.timestamp=timestamp_arr[0].time+'';
        postarr.job_id=data;
        postarr.paras={};
        this.job_id='任务ID:'+data;
        getEvStatus(postarr).then((res) => {
//           const res={
//     "attachment":{
//         "192.168.0.136_31003":{
//             "error_code":"0",
//             "error_info":"OK",
//             "status":"done",
//             "step":"done"
//         },
//         "192.168.0.125_41002":{
//             "error_code":"0",
//             "error_info":"OK",
//             "status":"ongoing",
//             "step":"check_param"
//         },
//         "job_step":"check_param, xtracback_data, checksum_data, backup_old_data, clear_old_data, recover_data, rebuild_sync, done"
//     },
//     "error_code":"0",
//     "error_info":"OK",
//     "job_id":"1706",
//     "job_type":"rebuild_node",
//     "status":"ongoing",
//     "version":"1.0"
// };
          if(info=='主备切换'){
            if(res.attachment!==null){
            this.dialogStatusFVisible=true;
            const steps=(res.attachment.job_step).replace(/\s+/g, '').split(',');
            if(this.stepParams.length==0){
              for(let a=0;a<steps.length;a++){
                let newArrgoing={}
                if(a==0){
                  newArrgoing.title=steps[a];
                  newArrgoing.icon='el-icon-loading';
                  newArrgoing.status= 'process';
                  newArrgoing.description='';
                }else{
                  newArrgoing.title=steps[a];
                  newArrgoing.icon= '';
                  newArrgoing.status= 'wait';
                  newArrgoing.description='';
                }
                if(res.attachment.step==steps[a]){
                  if(a>(this.active)){
                    for(let k=0;k<this.stepParams.length;k++){
                      if(k==a){
                        if(this.stepParams[k].status=='wait'){
                          this.stepParams[k].icon='el-icon-loading';
                          this.stepParams[k].status='process';
                        }
                      }
                      if(k>0&&k<a){
                        //小于i的情况
                        for(let j=0;j<k;j++){
                          this.stepParams[j].icon='el-icon-circle-check';
                          this.stepParams[j].status='success';
                        }
                      }
                    }
                  }
                  this.active=a;
                }
                this.stepParams.push(newArrgoing)
              }
            }else{
              if(res.status=='ongoing'){
                //console.log(11);
                for(let k=0;k<this.stepParams.length;k++){
                  //console.log(this.stepParams.length);
                  //console.log(typeof this.stepParams[k].title);
                  //console.log(this.stepParams[k].title);
                  //console.log(typeof res.attachment.step);
                  //console.log(res.attachment.step);
                  if(res.attachment.step==this.stepParams[k].title){
                    //console.log(44);
                    this.active=k;
                    this.stepParams[k].icon='el-icon-loading';
                    this.stepParams[k].status='process';
                    if(k>0&&k<(this.active+1)){
                      //小于i的情况
                      for(let j=0;j<k;j++){
                        this.stepParams[j].icon='el-icon-circle-check';
                        this.stepParams[j].status='success';
                        //console.log(55);
                      }
                    }
                  }
                }
                 //console.log(this.active);
              }else if(res.status=='done'){
                for(let k=0;k<this.stepParams.length;k++){
                  this.active=this.stepParams.length;
                  this.stepParams[k].icon='el-icon-circle-check';
                  this.stepParams[k].status='success';
                }
                clearInterval(timer);
                //this.getCluster();
              }else if(res.status=='failed'){
                //console.log(22);
                for(let k=0;k<this.stepParams.length;k++){

                  //if(res.attachment.step==this.stepParams[k].title){
                    if(this.stepParams[k].status=='process'){
                      this.active=k;
                      this.stepParams[k].icon='el-icon-circle-close';
                      this.stepParams[k].status='error';
                      this.stepParams[k].description=res.error_info;
                    }
                  //}
                }
                clearInterval(timer);
              }
            }
            }else if(res.attachment==null&&res.error_code=='70001'&&res.status=='failed'){
              if(i>5){
                clearInterval(timer);
              }
            }
          }else if(info=='重做备机'){
            this.dialogStatusRedoVisible=true;
            if(res.status=='ongoing'){
              if(res.attachment!==null){
                if(this.secondStatus.length==0){
                  const steps=(res.attachment.job_step).replace(/\s+/g, '').split(',');
                  let secondgoing={}
                  for(let a=0;a<steps.length;a++){
                    secondgoing={title:steps[a],icon:'',status: 'wait',description:''}
                    this.secondStatus.push(secondgoing)
                  }
                }
                if(this.statusList.length==0){
                  let statusgoing={}
                  let iparr=[];
                  for(let item in res.attachment){
                    if(item!=='job_step'){
                    let key=res.attachment[item];
                    this.$set(key, 'title', item)
                    iparr.push(key);
                    }
                  }
                  for(let a=0;a<iparr.length;a++){
                    if(a==0){
                      statusgoing={title:iparr[a].title,icon:'el-icon-loading',status:'process',description:''}
                      this.statusList.push(statusgoing)
                      if(iparr[a].status=='done'){
                        console.log(1);
                        this.init_active=a+1;
                        this.statusList[a].icon='el-icon-circle-check';
                        this.statusList[a].status='success';
                        for(let b=0;b<this.secondStatus.length;b++){
                          this.secondStatus[b].icon='el-icon-circle-check';
                          this.secondStatus[b].status='success';
                        }
                      }else if(iparr[a].status=='ongoing'){
                        this.init_active=a;

                      }
                    }
                    else{
                      statusgoing={title:iparr[a].title,icon:'',status:'wait',description:''}
                      this.statusList.push(statusgoing)
                    }
                  }
                }else{
                  //statusList不为0的情况

                }
                clearInterval(timer);
                console.log(this.statusList);
              }
            }else if(res.status=='failed'){
              clearInterval(timer);
            }else if(res.status=='done'){
              clearInterval(timer);
            }else{
              this.message_tips = res.error_info;
              this.message_type = 'error';
              messageTip(this.message_tips,this.message_type);
            }
          }else if(info=='删除shard'||info=='删除计算节点'||info=='删除存储节点'){
          //删除shard,计算节点，存储节点
          if(res.attachment!==null){
            if(info=='删除shard'||info=='删除存储节点'){
              this.shard_show=true;
              this.init_show=false;
              this.computer_show=false;
              console.log(10)
            }else if(info=='删除计算节点'){
              this.shard_show=false; 
              this.init_show=false;
              this.computer_show=true;
            }
            //计算
            if(res.attachment.computer_state=='ongoing'){
               console.log(11)
              this.computer_state='process';
              this.computer_icon='el-icon-loading'
              this.computer_title='正在'+info;
            }else if(res.attachment.computer_state=='done'){
              this.computer_state='success';
              this.computer_icon='el-icon-circle-check'
              this.computer_title=info+'成功';
               console.log(12)
             
            }else if(res.attachment.computer_state=='failed'){
              this.computer_state='error';
              this.computer_icon='el-icon-circle-close'
              this.computer_title=info+'失败';
               console.log(13)
            }else{
              this.computer_state='process';
              this.computer_icon='el-icon-loading'
              this.computer_title='正在'+info;
              console.log(14)
            }
            //存储
            if(res.attachment.storage_state=='ongoing'){
              this.storage_state='process';
              this.shard_icon='el-icon-loading'
              this.shard_title='正在'+info;
            }else if(res.attachment.storage_state=='done'){
               console.log(4);
              this.storage_state='success';
              this.shard_icon='el-icon-circle-check'
              this.shard_title=info+'成功';
              
            }else if(res.attachment.storage_state=='failed'){
               console.log(5);
              this.storage_state='error';
              this.shard_icon='el-icon-circle-close'
              this.shard_title=info+'失败';
              
            }else{
              console.log(6);
              this.storage_state='process';
              this.shard_icon='el-icon-loading'
              this.shard_title='正在'+info;
            }
            this.init_title='正在'+info;
            //this.finish_title=info+'集群成功'
            this.init_active=1;
            if(this.computer.length==0||this.shard.length==0){
              if(this.computer_show==true){
                if(this.computer.length==0){
                  console.log(1);
                  let newArrgoing={}
                  if(res.attachment.hasOwnProperty('computer_hosts')){
                    const arr=res.attachment.computer_hosts.substr(0,res.attachment.computer_hosts.length-1);
                    const computer_hosts=arr.split(';');
                    for(let e=0;e<computer_hosts.length;e++){
                      if(res.attachment.computer_state=='done'){
                        newArrgoing.title=res.attachment.hasOwnProperty('computer_hosts')===true?computer_hosts[e]:'正在'+info;
                        newArrgoing.icon='el-icon-circle-check';
                        newArrgoing.status= 'success';
                        newArrgoing.description='';
                        newArrgoing.computer_id=res.attachment.computer_id;
                        this.computer.push(newArrgoing)
                      }else if(res.attachment.computer_state=='failed'){
                        newArrgoing.title=res.attachment.hasOwnProperty('computer_hosts')===true?computer_hosts[e]:'正在'+info;
                        newArrgoing.icon='el-icon-circle-close';
                        newArrgoing.status= 'error';
                        newArrgoing.description=res.error_info;
                        newArrgoing.computer_id=res.attachment.computer_id;
                        this.computer.push(newArrgoing)
                      }else{
                        newArrgoing.title=res.attachment.hasOwnProperty('computer_hosts')===true?computer_hosts[e]:'正在'+info;
                        newArrgoing.icon='el-icon-loading';
                        newArrgoing.status= 'process';
                        newArrgoing.description='';
                        newArrgoing.computer_id=res.attachment.computer_id;
                        this.computer.push(newArrgoing)
                      }
                    }
                  }
                }
              }
              if(this.shard_show==true){
                if(this.shard.length==0){
                  console.log(2);
                  if(res.attachment.hasOwnProperty('storage_state')){
                    if(res.attachment.storage_state=='done'){
                      let shard_ids='';
                      if(info=='新增shard'){
                        shard_ids=res.attachment.shard_ids;
                        let shardgoing={}
                        for(let e=0;e<shard_ids.length;e++){
                          for(var item in shard_ids[e]){
                            var shard_idsValue=shard_ids[e][item];
                            console.log(shard_idsValue);
                            const shard_text=item+':'+shard_idsValue;
                            shardgoing.title=shard_idsValue!==''?shard_text:'正在'+info;
                            shardgoing.icon='el-icon-circle-check';
                            shardgoing.status= 'success';
                            shardgoing.description='';
                            shardgoing.shard_id=shard_idsValue;
                            this.shard.push(shardgoing)
                          }
                        }
                      }else{
                        console.log(3);
                        shard_ids=res.attachment.shard_id;
                        let shardgoing={}
                        shardgoing.title=shard_ids!==''?shard_ids:'正在'+info;
                        shardgoing.icon='el-icon-circle-check';
                        shardgoing.status= 'success';
                        shardgoing.description='';
                        shardgoing.shard_id=shard_idsValue;
                        this.shard.push(shardgoing)
                      }

                    }else if(res.attachment.storage_state=='failed'){
                      let shard_ids='';
                      if(info=='新增shard'){
                        shard_ids=res.attachment.shard_ids;
                        let shardgoing={}
                        for(let e=0;e<shard_ids.length;e++){
                          for(var item in shard_ids[e]){
                            var shard_idsValue=shard_ids[e][item];
                            console.log(shard_idsValue);
                            const shard_text=item+':'+shard_idsValue;
                            shardgoing.title=shard_idsValue!==''?shard_text:'正在'+info;
                            shardgoing.icon='el-icon-circle-close';
                            shardgoing.status= 'error';
                            shardgoing.description=res.error_info;
                            shardgoing.shard_id=shard_idsValue;
                            this.shard.push(shardgoing)
                          }
                        }
                      }else{
                        shard_ids=res.attachment.shard_id;
                        let shardgoing={}
                        shardgoing.title=shard_ids!==''?shard_ids:'正在'+info;
                        shardgoing.icon='el-icon-circle-close';
                        shardgoing.status= 'error';
                        shardgoing.description=res.error_info;
                        shardgoing.shard_id=shard_idsValue;
                        this.shard.push(shardgoing)
                      }
                    }else{
                      let shard_ids='';
                      if(info=='新增shard'){
                        shard_ids=res.attachment.shard_ids;
                        let shardgoing={}
                        for(let e=0;e<shard_ids.length;e++){
                          for(var item in shard_ids[e]){
                            var shard_idsValue=shard_ids[e][item];
                            console.log(shard_idsValue);
                            const shard_text=item+':'+shard_idsValue;
                            shardgoing.title=shard_idsValue!==''?shard_text:'正在'+info;
                            shardgoing.icon='el-icon-loading';
                            shardgoing.status= 'process';
                            shardgoing.description='';
                            shardgoing.shard_id=shard_idsValue;
                            this.shard.push(shardgoing)
                          }
                        }
                      }else{
                        shard_ids=res.attachment.shard_id;
                        let shardgoing={}
                        shardgoing.title=shard_ids!==''?shard_ids:'正在'+info;
                        shardgoing.icon='el-icon-loading';
                        shardgoing.status= 'process';
                        shardgoing.description='';
                        shardgoing.shard_id=shard_ids;
                        this.shard.push(shardgoing)
                      }
                    }
                  }
                }
              }
              if(res.status=='failed'){
                console.log(7);
                this.storage_state='error';
                this.shard_icon='el-icon-circle-close'
                this.shard_title=info+'失败';
                this.init_show=false;

                this.computer_state='error';
                this.computer_icon='el-icon-circle-close'
                this.computer_title=info+'失败';

                this.finish_title=info+'失败'
                this.finish_icon='el-icon-circle-close'
                this.init_active=1
                this.finish_description=res.error_info;
                //遍历计算节点改状态
                if(this.computer.length>0){
                  for(let c=0;c<this.computer.length;c++){
                    const arr=res.attachment.computer_hosts.substr(0,res.attachment.computer_hosts.length-1);
                    const computer_hosts=arr.split(';');
                    for(let e=0;e<computer_hosts.length;e++){
                      if(this.computer[c].title==computer_hosts[e]){
                        this.computer[c].icon='el-icon-circle-close';
                        this.computer[c].status='error';
                      }
                    }
                  }
                this.computer_description=res.error_info;
                }
                //遍历存储节点改状态
                if(this.shard.length>0){
                  for(let c=0;c<this.shard.length;c++){
                    let shard_ids='';
                    if(info=='新增shard'){
                      shard_ids=res.attachment.shard_ids;
                      for(let e=0;e<shard_ids.length;e++){
                        for(var item in shard_ids[e]){
                          var shard_idsValue=shard_ids[e][item];
                          if(this.shard[c].shard_id==shard_idsValue){
                            this.shard[c].icon='el-icon-circle-close';
                            this.shard[c].status='error';
                          }
                        }
                      }
                    }else{
                      shard_ids=res.attachment.shard_id;
                      if(this.shard[c].shard_id==shard_ids){
                        this.shard[c].icon='el-icon-circle-close';
                        this.shard[c].status='error';
                      }
                    }
                  }
                  this.shard_description=res.error_info;
                }
                clearInterval(timer);
              }else if(res.status=='done'){
                console.log(8);
                this.storage_state='success';
                this.shard_icon='el-icon-circle-check'
                this.shard_title=info+'成功';

                this.computer_state='success';
                this.computer_icon='el-icon-circle-check'
                this.computer_title=info+'成功';

                this.finish_title=info+'成功'
                this.finish_icon='el-icon-circle-check'
                this.init_active=1
                this.finish_description=res.error_info;
                //遍历计算节点改状态
                if(this.computer.length>0){
                  for(let c=0;c<this.computer.length;c++){
                    const arr=res.attachment.computer_hosts.substr(0,res.attachment.computer_hosts.length-1);
                    const computer_hosts=arr.split(';');
                    for(let e=0;e<computer_hosts.length;e++){
                      if(this.computer[c].title==computer_hosts[e]){
                        this.computer[c].icon='el-icon-circle-check';
                        this.computer[c].status='success';
                      }
                    }
                  }
                }
                //遍历存储节点改状态
                if(this.shard.length>0){
                  console.log(9);
                  for(let c=0;c<this.shard.length;c++){
                    let shard_ids='';
                    if(info=='新增shard'){
                      shard_ids=res.attachment.shard_ids;
                      for(let e=0;e<shard_ids.length;e++){
                        for(var item in shard_ids[e]){
                          var shard_idsValue=shard_ids[e][item];
                          if(this.shard[c].shard_id==shard_idsValue){
                            this.shard[c].icon='el-icon-circle-check';
                            this.shard[c].status='success';
                          }
                        }
                      }
                    }else{
                      shard_ids=res.attachment.shard_id;
                      if(this.shard[c].shard_id==shard_ids){
                        this.shard[c].icon='el-icon-circle-check';
                        this.shard[c].status='success';
                      }
                    }
                  }
                }
                clearInterval(timer);
                this.getCluster();
              }
            }
          }else if(res.attachment==null&&res.error_code=='70001'&&res.status=='failed'){
            if(info=='删除shard'||info=='删除存储节点'){
              this.shard_show=true;
              this.init_show=false;
              this.computer_show=false;
              this.storage_state='process';
              this.shard_icon='el-icon-loading'
              this.shard_title='正在'+info;
              if(i>5){
                this.storage_state='error';
                this.shard_icon='el-icon-circle-close'
                this.shard_title=info+'失败';
                clearInterval(timer);
              }
            }else if(info=='删除计算节点'){
              this.shard_show=false; 
              this.init_show=false;
              this.computer_show=true;
              this.computer_state='process';
              this.computer_icon='el-icon-loading'
              this.computer_title='正在'+info;
              if(i>5){
                this.computer_state='error';
                this.computer_icon='el-icon-circle-close'
                this.computer_title=info+'失败';
                clearInterval(timer);
              }
            }
          }else if(res.attachment==null&&res.status=='ongoing'){
            if(info=='删除shard'||info=='删除存储节点'){
              this.shard_show=true;
              this.init_show=false;
              this.computer_show=false;
              this.storage_state='process';
              this.shard_icon='el-icon-loading'
              this.shard_title='正在'+info;
            }else if(info=='删除计算节点'){
              this.shard_show=false; 
              this.init_show=false;
              this.computer_show=true;
              this.computer_state='process';
              this.computer_icon='el-icon-loading'
              this.computer_title='正在'+info;
            }
          }else if(res.attachment==null&&res.status=='done'){
            if(info=='删除shard'||info=='删除存储节点'){
              this.shard_show=true;
              this.init_show=false;
              this.computer_show=false;
              this.storage_state='success';
              this.shard_icon='el-icon-circle-check'
              this.shard_title=info+'成功';
            }else if(info=='删除计算节点'){
              this.shard_show=false; 
              this.init_show=false;
              this.computer_show=true;
              this.computer_state='success';
              this.computer_icon='el-icon-circle-check'
              this.computer_title=info+'成功';
            }
            clearInterval(timer);
          }else{
            if(info=='删除shard'||info=='删除存储节点'){
              this.shard_show=true;
              this.init_show=false;
              this.computer_show=false;
              this.storage_state='error';
              this.shard_icon='el-icon-circle-close'
              this.shard_title=info+'失败';
            }else if(info=='删除计算节点'){
              this.shard_show=false; 
              this.init_show=false;
              this.computer_show=true;
              this.computer_state='error';
              this.computer_icon='el-icon-circle-close'
              this.computer_title=info+'失败';
            }
            clearInterval(timer);
          }
        }
      });
        if(i>=86400){
            clearInterval(timer);
        }
      }, 0)
    },
     getStatus (timer,data,i) {
      setTimeout(()=>{
        const postarr={};
        postarr.job_type='get_status';
        postarr.version=version_arr[0].ver;
        postarr.job_id=data;
        postarr.timestamp=timestamp_arr[0].time+'';
        postarr.paras={};
        getEvStatus(postarr).then((res) => {
        if(res.status=='done'||res.status=='failed'){
          clearInterval(timer);
          //this.info=res.error_info;
          if(res.status=='done'){
            const newArrdone={
            content:res.error_info,
              timestamp: getNowDate(),
              color: '#0bbd87',
              icon: 'el-icon-circle-check'
            };
            this.activities.push(newArrdone)
            this.getList();
            this.dialogStatusVisible=false;
          }else{
            const newArr={
              content:res.error_info,
              timestamp: getNowDate(),
              color: 'red',
              icon: 'el-icon-circle-close'
            };
            this.activities.push(newArr);
            //this.installStatus = true;
          }
        }else{
           const newArrgoing={
            content:res.error_info,
            timestamp: getNowDate(),
            color: '#0bbd87'
          };
          this.activities.push(newArrgoing)
          //this.info=res.error_info;
          //this.installStatus = true;
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
.block{
  margin-left: 20px;
  max-height: 400px;
  overflow: auto;
}
.el-timeline{
  margin-left: 2px;
}
/* 修改滚动条宽度 */
::-webkit-scrollbar {
	width: 5px;
}
::-webkit-scrollbar-thumb{
  border-radius: 5px;
  -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,.3);
  background-color: rgba(0,0,0,0.1);
}
</style>