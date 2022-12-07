 <template>
  <div>
    <el-radio-group  v-model="currentCase" size="small" @change="agreeChange" v-show="g_loading">
    <el-radio v-for="cluster in clusters" :label="cluster.id" :key="cluster.id">{{cluster.nick_name+'('+cluster.id+')'}}</el-radio>
    </el-radio-group>
    <div class="nodata" v-show="nodataShow">暂无数据</div>
    <!-- style="margin-top:0px;width: calc(100(100% - 10px);height:calc(100vh - 160px);" -->
    <div ref="myPage" style="margin-top:0px;width: calc(100(100% - 10px);height:calc(100vh - 160px);" v-show="g_loading" @click="isShowNodeMenuPanel = false">
      <SeeksRelationGraph
        ref="seeksRelationGraph"
        :options="graphOptions"
        :on-node-click="onNodeClick"
        :on-line-click="onLineClick" :on-node-expand="onNodeExpand" :on-node-collapse="onNodeCollapse"
      >
        <div slot="node" slot-scope="{node}"   @click="showNodeMenus(node, $event)">
          <div>
            <i :style="{ color: node.data.color, fontSize: 40 + 'px' }" :class="node.data.icon"/>
            <p v-text="node.text" style="word-wrap: break-word;word-break: break-all;"></p>
          </div>
        </div>
      </SeeksRelationGraph>
    </div>
    <div v-show="isShowNodeMenuPanel&&g_loading" :style="{left: nodeMenuPanelPosition.x + 'px', top: nodeMenuPanelPosition.y + 'px' }" style="z-index: 999;padding:10px;background-color: #ffffff;border:#eeeeee solid 1px;box-shadow: 0px 0px 8px #cccccc;position: absolute;">
      <div style="line-height: 25px;padding-left: 10px;color: #888888;font-size: 10px;">对{{nodeName}}进行操作：</div>
      <!-- <div class="c-node-menu-item"  v-if="user_name=='super_dba'&&(type==='snode')" @click.stop="doAction('设置实例变量')">设置实例变量</div> -->
      <!-- <div class="c-node-menu-item"  v-if="user_name=='super_dba'&&(type==='snode')" @click.stop="doAction('获取实例变量')">获取实例变量</div> -->
      <!-- <div class="c-node-menu-item" v-if="type==='shard'&& (storage_node_create_priv==='Y')" @click.stop="doAction('新增存储节点')">新增存储节点</div>--> 
      <!-- <div class="c-node-menu-item" v-if="type==='shard'&&ha_mode==='rbr'" @click.stop="doAction('主备切换')">主备切换</div> -->
      <!-- <div class="c-node-menu-item" v-if="type==='shard'&&ha_mode==='rbr'&& (storage_node_create_priv==='Y')" @click.stop="doAction('重做备机节点')">重做备机节点</div> -->
      <!-- <div class="c-node-menu-item" v-if="type==='shard'&&ha_mode==='rbr'" @click.stop="doAction('设置延迟告警时间')">设置延迟告警时间</div> -->
      <div class="c-node-menu-item" v-if="type==='cluster'&& (shard_create_priv==='Y')" @click.stop="doAction('新增存储集群')">新增存储集群</div>
      <div class="c-node-menu-item" v-if="type==='cluster'&& (compute_node_create_priv==='Y')" @click.stop="doAction('新增计算节点')">新增计算节点</div>
      <div class="c-node-menu-item"  v-if="type==='snode'" @click.stop="doAction('详情')">详情</div>
      <!-- <div class="c-node-menu-item" v-if="(type==='cnode'||type==='snode')&& (user_name=='super_dba')&&(node_status!=='active')" @click.stop="doAction('启用')">启用</div>
      <div class="c-node-menu-item"  v-if="(type==='cnode'||type==='snode')&& (user_name=='super_dba')&& (master!=='true')&&(node_status!=='manual_stop')" @click.stop="doAction('禁用')">禁用</div>
      <div class="c-node-menu-item"  v-if="(type==='cnode'||type==='snode')&& (user_name=='super_dba')&& (master!=='true')" @click.stop="doAction('重启')">重启</div>
      <div class="c-node-menu-item"  v-if="type==='shard'&& shard_drop_priv==='Y'" @click.stop="doAction('删除')">删除</div>
      <div class="c-node-menu-item"  v-if="type==='cnode'&& compute_node_drop_priv==='Y'" @click.stop="doAction('删除')">删除</div>
      <div class="c-node-menu-item"  v-else-if="type==='snode'&& storage_node_drop_priv==='Y'" @click.stop="doAction('删除')">删除</div>-->
      <div class="c-node-menu-item"  v-if="(type==='cnode'||type==='snode')&&(node_status=='active')" @click.stop="doAction('节点监控')">节点监控</div> 
    </div>

    <div v-if="isShowNodeTipsPanel" :style="{left: nodeMenuPanelPosition.x + 'px', top: nodeMenuPanelPosition.y + 'px' }" style="z-index: 999;padding:10px;background-color: #ffffff;border:#eeeeee solid 1px;box-shadow: 0px 0px 8px #cccccc;position: absolute;">
      <div class="c-node-menu-item">ip:{{currentNode.data.ip}}</div>
      <div class="c-node-menu-item">port:{{currentNode.text}}</div>
      <div class="c-node-menu-item" v-if="shardName">shard名称:{{currentNode.data.shard_name}}</div>
      <div class="c-node-menu-item">集群名称:{{currentNode.data.cluster_name}}</div>
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
        <el-select v-model="redotemp.redolist" multiple placeholder="请选择" @change="change">
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
          <el-button type="primary" @click="redoData(redotemp)">确认</el-button>
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
    <!--设置延迟告警时间-->
    <el-dialog title="设置延迟告警时间" :visible.sync="dialogDalayVisible" custom-class="single_dal_view">
      <el-form
        ref="dalayForm"
        :model="dalaytemp"
        :rules="rules"
        label-position="left"
        label-width="120px"
      >
      <el-form-item label="shard名称:" prop="shard_name">
        <span>{{dalaytemp.shard_name}}</span>
      </el-form-item>
      <el-form-item label="最大延迟时间:" prop="maxtime" >
         <el-input  v-model="dalaytemp.maxtime" placeholder="请输入最大延迟时间">
         <i slot="suffix" style="font-style:normal;margin-right: 10px; line-height: 30px;">s</i>
         </el-input>
      </el-form-item>
      </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button @click="dialogDalayVisible = false">关闭</el-button>
          <el-button type="primary" @click="dalayData(dalaytemp)">确认</el-button>
        </div>
    </el-dialog>
    <!--查看节点详情-->
    <el-dialog title="存储节点详情" :visible.sync="dialogDetailVisible" custom-class="single_dal_view">
      <div class="box">
        <div title="" >
          <el-form
          ref="detailForm"
          :model="detailtemp"
          :rules="rules"
          label-position="left"
          label-width="120px"
          >
            <el-form-item label="集群id:" prop="cluster_id">
              <span>{{detailtemp.cluster_id}}</span>
            </el-form-item>
            <el-form-item label="shard_id:" prop="shard_id">
              <span>{{detailtemp.shard_id}}</span>
            </el-form-item>
            <el-form-item label="shard名称:" prop="shard_name">
              <span>{{detailtemp.shard_name}}</span>
            </el-form-item>
            <el-form-item label="ip:" prop="ip">
              <span>{{detailtemp.ip}}</span>
            </el-form-item>
            <el-form-item label="port:" prop="port">
              <span>{{detailtemp.port}}</span>
            </el-form-item>
            <el-form-item label="状态:" prop="master">
              <span v-if="detailtemp.master=='true'">{{detailtemp|nodeStatus}}</span>
              <span v-else-if="detailtemp.master=='false'">{{detailtemp|nodeStatus}}</span>
            </el-form-item>
            <el-form-item label="节点类型:" prop="sync_state">
              <span v-if="detailtemp.sync_state=='async'">异步</span>
              <span v-else-if="detailtemp.sync_state=='fsync'">强同步</span>
              <span v-else>{{detailtemp.sync_state}}</span>
            </el-form-item>
          </el-form>
        </div>
        <div title="">
          <el-form
          ref="detailForm"
          :model="detailtemp"
          :rules="rules"
          label-position="left"
          label-width="150px"
          >
            <el-form-item label="延迟时间:" prop="replica_delay">
              <span>{{detailtemp.replica_delay+'s'}}</span>
            </el-form-item>
            <el-form-item label="cpu核数:" prop="cpu_cores">
              <span>{{detailtemp.cpu_cores}}</span>
            </el-form-item>
            <el-form-item label="初始化存储值:" prop="initial_storage_GB">
              <span>{{detailtemp.initial_storage_GB+'GB'}}</span>
            </el-form-item>
            <el-form-item label="最大存储值:" prop="max_storage_GB">
              <span>{{detailtemp.max_storage_GB+'GB'}}</span>
            </el-form-item>
            <el-form-item label="innodb缓冲池大小:" prop="innodb_buffer_pool_MB">
              <span>{{detailtemp.innodb_buffer_pool_MB+'MB'}}</span>
            </el-form-item>
            <el-form-item label="rocksdb缓冲池大小:" prop="rocksdb_buffer_pool_MB">
              <span>{{detailtemp.rocksdb_buffer_pool_MB+'MB'}}</span>
            </el-form-item>
             <!-- <button type="primary" @click="getVariable(detailtemp.ip,detailtemp.port)">查看实例变量</button> -->
          </el-form>
        </div>
      </div>
      
    </el-dialog>
    <!--设置实例变量-->
    <el-dialog :title="textMap[dialogStatus]" :visible.sync="variableVisible" custom-class="single_dal_view">
      <el-form
        ref="variableForm"
        :model="variabletemp"
        :rules="rules"
        label-position="left"
        label-width="120px"
      >
      <el-form-item label="IP:" prop="ip">
        <span>{{variabletemp.ip}}</span>
      </el-form-item>
      <el-form-item label="端口:" prop="port">
        <span>{{variabletemp.port}}</span>
      </el-form-item>
      <el-form-item label="变量类型:" prop="vartype"  v-if="dialogStatus==='setVariable'">
         <el-select v-model="variabletemp.vartype" placeholder="请选择">
          <el-option
            v-for="item in typeOptions"
            :key="item.value"
            :label="item.label"
            :value="item.value">
          </el-option>
        </el-select>
      </el-form-item>
      <el-form-item label="变量名:" prop="variable" >
         <el-input  v-model="variabletemp.variable" placeholder="请输入变量名" />
      </el-form-item>
      <el-form-item label="变量值:" prop="varvalue" v-if="dialogStatus==='setVariable'">
         <el-input  v-model="variabletemp.varvalue" placeholder="请输入变量值" />
      </el-form-item>
      </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button @click="variableVisible = false">关闭</el-button>
          <el-button type="primary" @click="dialogStatus==='setVariable'?setVariable(variabletemp):getVariable(variabletemp)">确认</el-button>
        </div>
    </el-dialog>

    <!-- 状态框 -->
    <el-dialog :visible.sync="dialogStatusVisible" custom-class="single_dal_view" width="400px" :close-on-click-modal="false" :before-close="beforeSyncDestory">
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
     <el-dialog :title="job_id" :visible.sync="dialogStatusRedoVisible" custom-class="single_dal_view" :close-on-click-modal="false" :before-close="beforeRedoDestory">
      <div style="width: 100%;background: #fff;padding:0 20px;" class="block">
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
                    v-for="(item,index) of statusList[key].second"
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
    <el-dialog :title="job_id" :visible.sync="dialogStatusShowVisible" custom-class="single_dal_view" :close-on-click-modal="false" :before-close="beforeDelDestory">
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
import {getClusterNodesList,getEffectCluster,getNodes,getAllMachine, getShards,getSnodeTotal,getStandbyNode,getShardPrimary,pgEnable,myEnable,delShard,delComp,delSnode,startComp,startSnode,stopComp,stopSnode,restartComp,restartSnode,getClusterDetail,switchShard,getStorageList,rebuildNode,getEvStatus,getShardsCount,getCompsCount,getNodesCount,getBackupStorageList,setMaxDalay,getMetaCluster,setVariable,getVariable} from '@/api/cluster/list'
import {mysqlDashboard,pgsqlDashboard} from '@/api/grafana/list'
import SeeksRelationGraph from 'relation-graph'
import {version_arr,ip_arr,timestamp_arr} from "@/utils/global_variable"
export default {
  name: 'Demo',
  components: { SeeksRelationGraph },
  data() {
    const validateredolist = (rule, value, callback) => {
     if(value.length === 0){
        callback(new Error("请选择需重做的备机节点"));
      }else if(value.length==this.options.length){
        this.redotemp.allow_pull_from_master='1';
        callback();
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
    const validatemaxtime = (rule, value, callback) => {
     if(!value){
        callback(new Error("最大延迟时间不能为空"));
      }else if(!(/^[0-9]+$/.test(value))){
        callback(new Error("最大延迟时间只能输入数字"));
      }
      else {
        callback();
      }
    };
    const validatevartype = (rule, value, callback) => {
      if(value.length === 0){
        callback(new Error("请选择变量类型"));
      }
      else {
        callback();
      }
    };
    const variablename = (rule, value, callback) => {
     if(!value){
        callback(new Error("变量名不能为空"));
      }
      else {
        callback();
      }
    };
    const variablevarvalue = (rule, value, callback) => {
     if(!value){
        callback(new Error("变量值不能为空"));
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
      master:'',
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
      dialogDalayVisible:false,
      dialogDetailVisible:false,
      variableVisible:false,
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
      dalaytemp:{
        shard_name:'',
        maxtime:'100',
        shard_id:'',
        cluster_id:'',
      },
      variabletemp:{
        ip:'',
        port:'',
        vartype:'',
        variable:'',
        varvalue:''
      },
      detailtemp:{
        shard_name:'',
        ip:'',
        port:'',
        shard_id:'',
        cluster_id:'',
        sync_state:'',
        status:'',
        master:'',
        replica_delay:'',
        cpu_cores:'',
        initial_storage_GB:'',
        max_storage_GB:'',
        innodb_buffer_pool_MB:'',
        rocksdb_buffer_pool_MB:''
      },
      machinelist:[],
      machines:[],
      minMachine:0,
      machineTotal:0,
      rules: {},
      options: [],
      hdfs_options:[],
      typeOptions:[{ value: 'int',label: 'int'}, {value: 'string',label: 'string'}],
      status: [],
      dialogStatus: "",
      textMap: {
        update: "新增计算节点",
        create: "新增存储集群",
        detail: "新增存储节点",
        restore:'重做备机节点',
        setVariable:'设置实例变量',
        getVariable:'获取实例变量'
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
        //   description:'11',
        //   second:[
        //     {
        //     title: '1',
        //     icon: 'el-icon-loading',
        //     status: 'process',
        //     description:''
        //     },
        //     {
        //     title: '2',
        //     icon: 'el-icon-loading',
        //     status: 'process',
        //     description:''
        //     },
        //     {
        //     title: '3',
        //     icon: 'el-icon-loading',
        //     status: 'process',
        //     description:''
        //     },
        //     {
        //     title: '4',
        //     icon: 'el-icon-loading',
        //     status: 'process',
        //     description:''
        //     },
        //     {
        //     title: '5',
        //     icon: 'el-icon-loading',
        //     status: 'process',
        //     description:''
        //     },
        
        // ]
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
      // secondStatus:[
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
      // ],
      ipList:[],
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
      node_status:'',
      temp_cluster:{},

      demoname: '---',
      range_horizontal: [ 100, 300 ],
      range_vertical: [ 20, 100 ],
      activities:[],
      graphOptions: {
        debug: true,
        'disableDragNode':true,//是否禁用图谱中节点的拖动
        'backgrounImageNoRepeat': true,
        'allowShowZoomMenu':true,//是否在右侧菜单栏显示放大缩小的按钮
        // 'allowShowMiniView':true,//是否显示缩略图
          'layouts': [
            {
              'label': '中心',
              'layoutName': 'tree',
              'layoutClassName': 'seeks-layout-center',
              'defaultJunctionPoint': 'border',
              'defaultNodeShape': 0,
              'defaultLineShape': 1,
              'from': 'left',
              // 通过这4个属性来调整 tree-层级距离&节点距离
              'min_per_width': '300',
              'max_per_width': '320',
              'min_per_height': '120',
              'max_per_height': '140',
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
          'defaultNodeWidth': '125',//默认节点的宽度
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
      },
      // avltimer:null,
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
        maxtime:[
          { required: true, trigger: "blur",validator: validatemaxtime },
        ],
        vartype:[
          { required: true, trigger: "blur",validator: validatevartype },
        ],
        variable:[
          { required: true, trigger: "blur",validator: variablename },
        ],
        varvalue:[
          { required: true, trigger: "blur",validator: variablevarvalue },
        ],
      },
    }
  },
  created(){
    this.getCluster();
  },
  mounted(){
   const timer = setInterval(()=>{
     this.getAllMetaCluster();
    },20000)
    this.$once('hook:beforeDestroy', () => {                     
      clearInterval(timer);                                     
    })
  },
  destroyed() {
    clearInterval(this.timer)
    this.timer = null
  },
  filters: {
      nodeStatus:function(value){
        let status='';
        let master='';
        if(value.status=='active'){
          status='在线';
          if(value.master=='true'){
            master='主节点';
          }else if(value.master=='false'){
            master='备机节点';
          }
          return master+'('+status+')'; 
        }else if(value.status=='inactive'){
          status='异常';
          if(value.master=='true'){
            master='主节点';
          }else if(value.master=='false'){
            master='备机节点';
          }
          return master+'('+status+')';
        }else if(value.status=='manual_stop'){
          status='停止';
          if(value.master=='true'){
            master='主节点';
          }else if(value.master=='false'){
            master='备机节点';
          }
          return master+'('+status+')';
        }else if(value.status=='creating'){
          status='安装中';
          if(value.master=='true'){
            master='主节点';
          }else if(value.master=='false'){
            master='备机节点';
          }
          return master+'('+status+')';
        }
      }
    },
  methods: {
    getAllMetaCluster(){
      let queryParam = {user_name:sessionStorage.getItem('login_username')}
      //模糊搜索
      getMetaCluster(queryParam).then(response => {
       if(response.length>0){
        for (let j = 0; j < response.length; j++) {
          this.open4(response[j]);
        }
       }
      });
    },
     dalayData(row) {
      this.$refs["dalayForm"].validate((valid) => {
        if (valid) {
          let tempData=row;
          tempData.user_name = sessionStorage.getItem('login_username');
          //console.log(tempData);return;
          //发送接口
          setMaxDalay(tempData).then(response=>{
            let res = response;
            if(res.code==200){
              this.dialogDalayVisible = false;
              this.message_tips = res.message;
              this.message_type = 'success';
              messageTip(this.message_tips,this.message_type);
            }
            else{
              this.message_tips = res.message;
              this.message_type = 'error';
              messageTip(this.message_tips,this.message_type);
            }
          })
        }
      });
    },
    change(){
      if(this.options.length==this.redotemp.redolist.length){
        this.redotemp.allow_pull_from_master='1'; 
      }else{
        this.redotemp.allow_pull_from_master='0'; 
      }
    },
    beforeSyncDestory(){
      clearInterval(this.timer)
      this.dialogStatusVisible=false;
      this.timer=null;
    },
    beforeSwitchDestory(){
      //console.log('00:00');
      clearInterval(this.timer)
      this.dialogStatusFVisible=false;
      this.timer = null;

    },
    beforeRedoDestory(){
      clearInterval(this.timer)
      this.dialogStatusRedoVisible=false;
      this.timer = null;
    },
    //清除定时器
    beforeDelDestory(){
      //console.log('10:00');
      clearInterval(this.timer)
      this.dialogStatusShowVisible=false;
    },
    resetredotemp(){
      this.redotemp={
        redolist:[],
        allow_pull_from_master:'0',
        allow_replica_delay:'30',
        need_backup:'0',
        pv_limit:'10',
        hdfs_host:''   
      }
    },
    resetVariableTemp(){
      this.variabletemp={
        ip:'',
        port:'',
        vartype:'',
        variable:'',
        varvalue:''
      }
    },
    getVariable(row) {
      this.$refs["variableForm"].validate((valid) => {
        if (valid) {
          let tempData = {};
          tempData.user_name = sessionStorage.getItem('login_username');
          tempData.job_id ='';
          tempData.job_type ='get_variable';
          tempData.version=version_arr[0].ver;
          tempData.timestamp=timestamp_arr[0].time+'';
          let paras={}
          paras.hostaddr=row.ip;
          paras.port=row.port;
          paras.variable=row.variable;
          tempData.paras=paras;
          //console.log(tempData);return;
          //发送接口
          getVariable(tempData).then(response=>{
            this.isShowNodeMenuPanel=false;
            let res = response;
            this.message_tips = '正在获取实例变量...';
            this.message_type = 'success';
            messageTip(this.message_tips,this.message_type);
            if(res.error_code=='0'&&res.status=='done'){
              this.message_tips = '获取实例变量成功,value='+res.attachment.value;
              this.message_type = 'success';
              this.variableVisible=false;
              messageTip(this.message_tips,this.message_type);
            }else{
              this.message_tips = res.error_info;
              this.message_type = 'error';
              messageTip(this.message_tips,this.message_type);
            }
          })
        }
      });
    },
    setVariable(row) {
      this.$refs["variableForm"].validate((valid) => {
        if (valid) {
          let tempData = {};
          tempData.user_name = sessionStorage.getItem('login_username');
          tempData.job_id ='';
          tempData.job_type ='set_variable';
          tempData.version=version_arr[0].ver;
          tempData.timestamp=timestamp_arr[0].time+'';
          let paras={}
          paras.hostaddr=row.ip;
          paras.port=row.port;
          paras.type=row.vartype;
          paras.variable=row.variable;
          paras.value=row.varvalue;
          tempData.paras=paras;
          //console.log(tempData);return;
          //发送接口
          setVariable(tempData).then(response=>{
            this.isShowNodeMenuPanel=false;
            let res = response;
            this.message_tips = '正在设置实例变量...';
            this.message_type = 'success';
            messageTip(this.message_tips,this.message_type);
            if(res.error_code=='0'&&res.status=='done'){
              this.message_tips = '设置实例变量成功';
              this.message_type = 'success';
              this.variableVisible=false;
              messageTip(this.message_tips,this.message_type);
            }else{
              this.message_tips = res.error_info;
              this.message_type = 'error';
              messageTip(this.message_tips,this.message_type);
            }
          })
        }
      });
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
          let ipList=[]
          //console.log(this.redotemp.redolist.length);return;
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
            ipList.push({title:ip_arr[0]+'_'+ip_arr[1],error_code: '0',error_info: '',status: 'no_start',step: ''})
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
          //console.log(tempData);return;
          //发送接口
          rebuildNode(tempData).then(response=>{
            let res = response;
            if(res.status=='accept'){
              this.dialogRedoVisible = false;
              this.stepParams=[];
              let i=0;
              const info='重做备机节点';this.statusList=[];this.timer=null;this.init_active=0;this.second_active=0;
              //传重做备机节点的ipList
              this.getFStatus(this.timer,res.job_id,i++,info,ipList)
              this.timer= setInterval(() => {
                this.getFStatus(this.timer,res.job_id,i++,info,ipList)
              }, 5000)
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
                this.getFStatus(this.timer,res.job_id,i++,info,'')
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
      node.data.childrenLoaded = true;
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
              //this.$set(param.data, 'color', '#e98f36')
              if(param.data.master=='true'){
                if(param.data.status=='active'){
                  this.$set(param.data, 'color', '#1196db')
                  this.$set(param, 'text', param.data.port+'(主-正常)')
                }else if(param.data.status=='inactive'){
                  this.$set(param.data, 'color', '#e15050')
                  this.$set(param, 'text', param.data.port+'(主-异常)')
                }else if(param.data.status=='creating'){
                  this.$set(param.data, 'color', '#2c2d2d')
                  this.$set(param, 'text', param.data.port+'(主-安装中)')
                }else if(param.data.status=='manual_stop'){
                  this.$set(param.data, 'color', '#acacac')
                  this.$set(param, 'text', param.data.port+'(主-停止)')
                }else{
                  this.$set(param.data, 'color', '#e15050')
                  this.$set(param, 'text', param.data.port+'(主-异常)')
                }
              }else if(param.data.master=='false'){
                if(param.data.status=='active'){
                  this.$set(param.data, 'color', '#e98f36')
                  this.$set(param, 'text', param.data.port+'(备-正常)')
                }else if(param.data.status=='inactive'){
                  this.$set(param.data, 'color', '#e15050')
                  this.$set(param, 'text', param.data.port+'(备-异常)')
                }else if(param.data.status=='creating'){
                  this.$set(param.data, 'color', '#2c2d2d')
                  this.$set(param, 'text', param.data.port+'(备-安装中)')
                }else if(param.data.status=='manual_stop'){
                  this.$set(param.data, 'color', '#acacac')
                  this.$set(param, 'text', param.data.port+'(备-停止)')
                }else{
                  this.$set(param.data, 'color', '#e15050')
                  this.$set(param, 'text', param.data.port+'(备-异常)')
                }
                //this.$set(param, 'text', param.data.port+'(备-正常)')
                //this.$set(param.data, 'color', '#e98f36')
              }else {
                this.$set(param.data, 'color', '#e15050')
              }
              // if(param.data.status=='manual_stop'){
              //   this.$set(param.data, 'color', '#acacac')
              // }
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
      //console.log(val);
      let temp={id:val}
      this.temp_cluster={id:val};
      this.getOneCluster(temp);
    },
    async getCluster() {
      const temp={};
      temp.effectCluster=sessionStorage.getItem('affected_clusters');
      temp.apply_all_cluster= sessionStorage.getItem('apply_all_cluster');
      const res = await getEffectCluster(temp)
      if(res.total>0){
        this.currentCase=res.list[0].id
        this.clusters = res.list;
        let temp={id:res.list[0].id}
        this.temp_cluster={id:res.list[0].id};
        this.getOneCluster(temp);
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
            }
            if(param.id.indexOf('cnode')!=-1){
              this.$set(param.data, 'icon', 'iconfont icon-compnode')
              if(param.data.status=='manual_stop'){
                this.$set(param.data, 'color', '#acacac')
                this.$set(param, 'text', param.data.port+'(停止)')
              }else if(param.data.status=='creating'){
                this.$set(param.data, 'color', '#2c2d2d')
                this.$set(param, 'text', param.data.port+'(安装中)')
              }else if(param.data.status=='active'){
                this.$set(param.data, 'color', '#1196db')
                this.$set(param, 'text', param.data.port+'(正常)')
              }else if(param.data.status=='inactive'){
                this.$set(param.data, 'color', '#e15050')
                this.$set(param, 'text', param.data.port+'(异常)')
              }else{
                  this.$set(param.data, 'color', '#e15050')
              }
            }
            // if(param.id.indexOf('snode')!=-1){
            //    this.$set(param.data, 'icon', 'iconfont icon-snode')
            //    this.$set(param.data, 'color', '#e98f36')
            // }
          }
          this.list = arr;
          this.showSeeksGraph( this.list )
          setTimeout(() => {
            this.listLoading = false
          }, 0.5 * 1000)
      });
    },
    open4(message) {
      this.$notify({
        title: '告警',
        message: message,
        // duration: 0,
        type: 'warning'
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
        else if(this.currentNode.id=='cluster'||this.currentNode.text.indexOf('shard') !=-1){//cluster not display
          this.isShowNodeMenuPanel = false
        }
        else{
          this.isShowNodeMenuPanel = true
          this.nodeName=this.currentNode.text;
          this.node_status=this.currentNode.data.status;
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
      this.isShowNodeMenuPanel = false
    },
    showNodeMenus(nodeObject, $event) {
      this.currentNode = nodeObject
      var _base_position = this.$refs.myPage.getBoundingClientRect()
       if(this.currentNode.id.indexOf('cnode') !=-1||this.currentNode.id.indexOf('snode') !=-1){
          this.isShowNodeMenuPanel = true
          this.nodeName=this.currentNode.text;
          this.node_status=this.currentNode.data.status;
           if(this.currentNode.id.indexOf('cnode') !=-1) {
            this.type='cnode'
          }else if(this.currentNode.id.indexOf('snode') !=-1){
            this.type='snode'
            this.master=this.currentNode.data.master;
          }
       }
       this.nodeMenuPanelPosition.x = $event.clientX - _base_position.x
       this.nodeMenuPanelPosition.y = $event.clientY - _base_position.y
    },
    doAction(actionName) {
      if(actionName==='节点监控'){
          if(this.currentNode.data.name==='pgsql'){
            const pparas={};
            pparas['cluster_id']=this.currentNode.data.cluster_id;
            pparas['hostaddr']=this.currentNode.data.hostaddr;
            pparas['port']=this.currentNode.data.port;
            pparas['user_name']=sessionStorage.getItem('login_username');
            pgsqlDashboard(pparas).then((pyres) => {
              if(pyres.code=='200'){
                window.open(ip_arr[0].ip+pyres.url+'?orgId=1&refresh=5s');
              }
            })
          //  window.open(ip_arr[0].ip+'/d/postgresql/postgresql?orgId=1&refresh=5s');
          }else if(this.currentNode.data.name==='mysql'){
            const mparas={};
            mparas['cluster_id']=this.currentNode.data.cluster_id;
            mparas['hostaddr']=this.currentNode.data.hostaddr;
            mparas['port']=this.currentNode.data.port;
            mparas['user_name']=sessionStorage.getItem('login_username');
            mysqlDashboard(mparas).then((myres) => {
              if(myres.code=='200'){
                window.open(ip_arr[0].ip+myres.url+'?orgId=1&refresh=5s');
              }
            })
            //跳转新页面mysql
            // window.open(ip_arr[0].ip+'/d/mysql/mysql?orgId=1&refresh=5s');
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
                      this.isShowNodeMenuPanel = false
                      this.dialogStatusShowVisible=true;
                      //调获取状态接口
                      let i=0;
                      this.computer=[];this.shard=[];this.computer_state='';this.storage_state='';this.computer_title='';this.computer_icon='';this.shard_icon='';this.shard_title='';this.comp_active=0;this.shard_active=0;this.strogemachines=[];this.init_title='';this.init_active=0;this.finish_title='';this.finish_icon='';this.finish_description='';this.computer_description='';this.shard_description='';this.job_id='';this.timer=null;
                      const info='删除shard'
                      this.getFStatus(this.timer,res.job_id,i++,info,'')
                      this.timer = setInterval(() => {
                        this.getFStatus(this.timer,res.job_id,i++,info,'')
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
                      this.getFStatus(this.timer,res.job_id,i++,info,'')
                      this.timer = setInterval(() => {
                        this.getFStatus(this.timer,res.job_id,i++,info,'')
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
                  delSnode(tempData).then((response)=>{
                    let res = response;
                    if(res.status=='accept'){
                      this.isShowNodeMenuPanel = false
                      this.dialogStatusShowVisible=true;
                      //调获取状态接口
                      let i=0;
                      this.computer=[];this.shard=[];this.computer_state='';this.storage_state='';this.computer_title='';this.computer_icon='';this.shard_icon='';this.shard_title='';this.comp_active=0;this.shard_active=0;this.strogemachines=[];this.init_title='';this.init_active=0;this.finish_title='';this.finish_icon='';this.finish_description='';this.computer_description='';this.shard_description='';this.job_id='';this.timer=null;
                      const info='删除存储节点'
                      this.getFStatus(this.timer,res.job_id,i++,info,'')
                      this.timer = setInterval(() => {
                        this.getFStatus(this.timer,res.job_id,i++,info,'')
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
          })
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
            paras.cluster_id=this.currentNode.data.cluster_id;
            paras.machine_type='computer';
            tempData.paras=paras;
            let arr={
              list:tempData,
              type:'pgsql'
            }
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
                //调获取状态接口
                let i=0;
                this.timer = setInterval(() => {
                  this.getStatus(this.timer,res.job_id,i++)
                }, 5000)
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
              paras.cluster_id=this.currentNode.data.cluster_id;
              paras.shard_id=this.currentNode.data.shard_id;
              paras.machine_type='storage';
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
                  this.timer = setInterval(() => {
                    this.getStatus(this.timer,res.job_id,i++)
                  }, 5000)
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
              paras.cluster_id=this.currentNode.data.cluster_id;
              paras.machine_type='computer';
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
                  //调获取状态接口
                  let i=0;
                  this.timer = setInterval(() => {
                    this.getStatus(this.timer,res.job_id,i++)
                  }, 5000)
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
              paras.cluster_id=this.currentNode.data.cluster_id;
              paras.shard_id=this.currentNode.data.shard_id;
              paras.machine_type='storage';
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
                  this.isShowNodeMenuPanel = false
                  //调获取状态接口
                  let i=0;
                  this.timer = setInterval(() => {
                    this.getStatus(this.timer,res.job_id,i++)
                  }, 5000)
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
              paras.cluster_id=this.currentNode.data.cluster_id;
              paras.machine_type='computer';
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
                  //调获取状态接口
                  let i=0;
                  this.timer = setInterval(() => {
                    this.getStatus(this.timer,res.job_id,i++)
                  }, 5000)
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
              paras.cluster_id=this.currentNode.data.cluster_id;
              paras.shard_id=this.currentNode.data.shard_id;
              paras.machine_type='storage';
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
                  this.isShowNodeMenuPanel = false
                  //调获取状态接口
                  let i=0;
                  this.timer = setInterval(() => {
                    this.getStatus(this.timer,res.job_id,i++)
                  }, 5000)
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
        this.resetredotemp();
        this.redotemp.shard_id=this.currentNode.data.shard_id;
        this.redotemp.cluster_id=this.currentNode.data.cluster_id;
        this.dialogRedoVisible=true;
        //获取计算机名称
        // getAllMachine().then((res) => {
        // this.machines = res.list;
        //   this.minMachine=0;
        //   this.machineTotal=res.total;
        // });
        // const temp={};
        // temp.version=version_arr[0].ver;
        // temp.job_id='';
        // temp.job_type='get_backup_storage';
        // temp.timestamp=timestamp_arr[0].time+'';
        // temp.paras={};
        // getStorageList(temp).then(response => {
        //   this.hdfs_options=[];
        //   if(response.attachment.list_backup_storage!==null){
        //     for (let i = 0; i < response.attachment.list_backup_storage.length; i++) {
        //        const arr={'value':i,'label':response.attachment.list_backup_storage[i].hostaddr+'('+response.attachment.list_backup_storage[i].port+')'};
        //        this.hdfs_options.push(arr);
        //     }
        //   }
        // });
        getBackupStorageList().then(response => {
          this.hdfs_options=[];
          if(response.list!==null){
            for (let i = 0; i < response.list.length; i++) {
               const arr={'value':i,'label':response.list[i].hostaddr+'('+response.list[i].port+')'};
               this.hdfs_options.push(arr);
            }
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
      if(actionName==='设置延迟告警时间'){
        this.dalaytemp.shard_name=this.currentNode.text;
        this.dalaytemp.cluster_id=this.currentNode.data.cluster_id;
        this.dalaytemp.shard_id=this.currentNode.data.shard_id;
        this.dialogDalayVisible=true;
      }
      if(actionName==='详情'){
        this.isShowNodeMenuPanel=false;
        this.detailtemp.cluster_id=this.currentNode.data.cluster_id;
        this.detailtemp.cpu_cores=this.currentNode.data.cpu_cores;
        this.detailtemp.replica_delay=this.currentNode.data.delay;
        this.detailtemp.ip=this.currentNode.data.hostaddr;
        this.detailtemp.port=this.currentNode.data.port;
        this.detailtemp.initial_storage_GB=this.currentNode.data.initial_storage_GB;
        this.detailtemp.innodb_buffer_pool_MB=this.currentNode.data.innodb_buffer_pool_MB;
        this.detailtemp.master=this.currentNode.data.master;
        this.detailtemp.max_storage_GB=this.currentNode.data.max_storage_GB;
        this.detailtemp.rocksdb_buffer_pool_MB=this.currentNode.data.rocksdb_buffer_pool_MB;
        this.detailtemp.shard_id=this.currentNode.data.shard_id;
        this.detailtemp.shard_name=this.currentNode.data.shard_name;
        this.detailtemp.sync_state=this.currentNode.data.sync_status;
        this.detailtemp.status=this.currentNode.data.status;
        this.dialogDetailVisible=true;
      }
      if(actionName==='设置实例变量'){
        this.resetVariableTemp();
        this.dialogStatus = "setVariable";
        this.variabletemp.ip=this.currentNode.data.hostaddr;
        this.variabletemp.port=this.currentNode.data.port;
        this.variableVisible=true;
      }
      if(actionName==='获取实例变量'){
        this.resetVariableTemp();
         this.dialogStatus = "getVariable";
        this.variabletemp.ip=this.currentNode.data.hostaddr;
        this.variabletemp.port=this.currentNode.data.port;
        this.variableVisible=true;
      }
      
      // this.$notify({
      //   title: '提示',
      //   message: '对节点【' + this.currentNode.text + '】进行了：' + actionName,
      //   type: 'success'
      // })
      // this.isShowNodeMenuPanel = false
    },
    getFStatus (timer,data,i,info,iparr) {
      setTimeout(()=>{
        const postarr={}
        postarr.job_type='get_status';
        postarr.version=version_arr[0].ver;
        postarr.timestamp=timestamp_arr[0].time+'';
        postarr.job_id=data;
        postarr.paras={};
        this.job_id='任务ID:'+data;
        getEvStatus(postarr).then((res) => {
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
                for(let k=0;k<this.stepParams.length;k++){
                  if(res.attachment.step==this.stepParams[k].title){
                    this.active=k;
                    this.stepParams[k].icon='el-icon-loading';
                    this.stepParams[k].status='process';
                    if(k>0&&k<(this.active+1)){
                      //小于i的情况
                      for(let j=0;j<k;j++){
                        this.stepParams[j].icon='el-icon-circle-check';
                        this.stepParams[j].status='success';
                      }
                    }
                  }
                }
              }else if(res.status=='done'){
                for(let k=0;k<this.stepParams.length;k++){
                  this.active=this.stepParams.length;
                  this.stepParams[k].icon='el-icon-circle-check';
                  this.stepParams[k].status='success';
                }
                clearInterval(timer);
                //this.getCluster();
                this.getOneCluster(this.temp_cluster);
              }else if(res.status=='failed'){
                for(let k=0;k<this.stepParams.length;k++){
                    if(this.stepParams[k].status=='process'){
                      this.active=k;
                      this.stepParams[k].icon='el-icon-circle-close';
                      this.stepParams[k].status='error';
                      this.stepParams[k].description=res.error_info;
                    }
                }
                clearInterval(timer);
              }
            }
            }else if(res.attachment==null&&res.error_code=='70001'&&res.status=='failed'){
              if(i>5){
                clearInterval(timer);
              }
            }
          }else if(info=='重做备机节点'){
            if(res.attachment!==null){
              if(this.statusList.length==0){
                let statusgoing={}
                if(res.status=='failed'){
                  statusgoing={title:info+'失败',icon:'el-icon-circle-close',status:'error',description:res.error_info,second:[]}
                  this.statusList.push(statusgoing)
                  clearInterval(timer);
                }else{
                  //let statusgoing={}
                  // let iparr=[];
                  // for(let item in res.attachment){
                  //   if(item!=='job_step'){
                  //   let key=res.attachment[item];
                  //   this.$set(key, 'title', item)
                  //   iparr.push(key);
                  //   }
                  // }
                  for(let a=0;a<iparr.length;a++){
                    const steps=(res.attachment.job_step).replace(/\s+/g, '').split(',');
                    let secondgoing={}
                    let secondlist=[];
                    for(let b=0;b<steps.length;b++){
                      secondgoing={title:steps[b],icon:'',status: 'wait',description:''}
                      secondlist.push(secondgoing)
                    }
                    if(a==0){
                      statusgoing={title:iparr[a].title,icon:'el-icon-loading',status:'process',description:'',second:secondlist}
                      this.statusList.push(statusgoing)
                      if(iparr[a].status=='done'){
                        this.init_active=a+1;
                        this.statusList[a].icon='el-icon-circle-check';
                        this.statusList[a].status='success';
                        for(let c=0;c<this.statusList[a].second.length;c++){
                          this.statusList[a].second[c].icon='el-icon-circle-check';
                          this.statusList[a].second[c].status='success';
                        }
                      }else if(iparr[a].status=='ongoing'){
                        this.init_active=a;
                        this.statusList[a].icon='el-icon-loading';
                        this.statusList[a].status='process';
                        for(let c=0;c<this.statusList[a].second.length;c++){
                          if(iparr[a].step==this.statusList[a].second[c].title){
                            this.statusList[a].second[c].icon='el-icon-loading';
                            this.statusList[a].second[c].status='process';
                            for(let j=0;j<c;j++){
                              this.statusList[a].second[j].icon='el-icon-circle-check';
                              this.statusList[a].second[j].status='success';
                            }
                          }
                        }
                      }else if(iparr[a].status=='failed'){
                        this.statusList[a].icon='el-icon-circle-close';
                        this.statusList[a].status='error';
                        for(let c=0;c<this.statusList[a].second.length;c++){
                          if(iparr[a].step==this.statusList[a].second[c].title){
                            this.statusList[a].second[c].icon='el-icon-circle-close';
                            this.statusList[a].second[c].status='error';
                            for(let j=0;j<c;j++){
                              this.statusList[a].second[j].icon='el-icon-circle-check';
                              this.statusList[a].second[j].status='success';
                            }
                          }
                        }
                        this.statusList[a].description=iparr[a].error_info;
                      }
                    }
                    else{
                      const steps=(res.attachment.job_step).replace(/\s+/g, '').split(',');
                      let secondgoing={}
                      let secondlist=[];
                      for(let b=0;b<steps.length;b++){
                        secondgoing={title:steps[b],icon:'',status: 'wait',description:''}
                        secondlist.push(secondgoing)
                      }
                      if(this.init_active==0){
                        statusgoing={title:iparr[a].title,icon:'',status:'wait',description:'',second:secondlist}
                        this.statusList.push(statusgoing)
                      }else{
                        statusgoing={title:iparr[a].title,icon:'el-icon-loading',status:'process',description:'',second:secondlist}
                        this.statusList.push(statusgoing)
                      }
                      //第a个修改里边的状态
                      if(iparr[a].status=='done'){
                        this.init_active=a+1;
                        this.statusList[a].icon='el-icon-circle-check';
                        this.statusList[a].status='success';
                        for(let c=0;c<this.statusList[a].second.length;c++){
                          this.statusList[a].second[c].icon='el-icon-circle-check';
                          this.statusList[a].second[c].status='success';
                        }
                      }else if(iparr[a].status=='ongoing'){
                        this.init_active=a;
                        this.statusList[a].icon='el-icon-loading';
                        this.statusList[a].status='process';
                        for(let c=0;c<this.statusList[a].second.length;c++){
                          if(iparr[a].step==this.statusList[a].second[c].title){
                            this.statusList[a].second[c].icon='el-icon-loading';
                            this.statusList[a].second[c].status='process';
                            for(let j=0;j<c;j++){
                              this.statusList[a].second[j].icon='el-icon-circle-check';
                              this.statusList[a].second[j].status='success';
                            }
                          }
                        }
                      }else if(iparr[a].status=='failed'){
                        this.statusList[a].icon='el-icon-circle-close';
                        this.statusList[a].status='error';
                        for(let c=0;c<this.statusList[a].second.length;c++){
                          if(iparr[a].step==this.statusList[a].second[c].title){
                            this.statusList[a].second[c].icon='el-icon-circle-close';
                            this.statusList[a].second[c].status='error';
                            for(let j=0;j<c;j++){
                              this.statusList[a].second[j].icon='el-icon-circle-check';
                              this.statusList[a].second[j].status='success';
                            }
                          }
                        }
                        this.statusList[a].description=iparr[a].error_info;
                      }
                    }
                  }
                }
              }else{
                //statusList不为0的情况
                if(res.status=='ongoing'){
                  //let statusgoing={}
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
                      if(iparr[a].status=='done'){
                        this.init_active=a+1;
                        this.statusList[a].icon='el-icon-circle-check';
                        this.statusList[a].status='success';
                        for(let c=0;c<this.statusList[a].second.length;c++){
                          this.statusList[a].second[c].icon='el-icon-circle-check';
                          this.statusList[a].second[c].status='success';
                        }
                      }else if(iparr[a].status=='ongoing'){
                        this.init_active=a;
                        this.statusList[a].icon='el-icon-loading';
                        this.statusList[a].status='process';
                        for(let c=0;c<this.statusList[a].second.length;c++){
                          if(iparr[a].step==this.statusList[a].second[c].title){
                            this.statusList[a].second[c].icon='el-icon-loading';
                            this.statusList[a].second[c].status='process';
                            for(let j=0;j<c;j++){
                              this.statusList[a].second[j].icon='el-icon-circle-check';
                              this.statusList[a].second[j].status='success';
                            }
                          }
                        }
                      }else if(iparr[a].status=='failed'){
                        this.statusList[a].icon='el-icon-circle-close';
                        this.statusList[a].status='error';
                        for(let c=0;c<this.statusList[a].second.length;c++){
                          if(iparr[a].step==this.statusList[a].second[c].title){
                            this.statusList[a].second[c].icon='el-icon-circle-close';
                            this.statusList[a].second[c].status='error';
                            for(let j=0;j<c;j++){
                              this.statusList[a].second[j].icon='el-icon-circle-check';
                              this.statusList[a].second[j].status='success';
                            }
                          }
                        }
                        this.statusList[a].description=iparr[a].error_info;
                      }
                    }
                    else{
                      //第a个修改里边的状态
                      if(iparr[a].status=='done'){
                        this.init_active=a+1;
                        this.statusList[a].icon='el-icon-circle-check';
                        this.statusList[a].status='success';
                        for(let c=0;c<this.statusList[a].second.length;c++){
                          this.statusList[a].second[c].icon='el-icon-circle-check';
                          this.statusList[a].second[c].status='success';
                        }
                      }else if(iparr[a].status=='ongoing'){
                        this.init_active=a;
                        this.statusList[a].icon='el-icon-loading';
                        this.statusList[a].status='process';
                        for(let c=0;c<this.statusList[a].second.length;c++){
                          if(iparr[a].step==this.statusList[a].second[c].title){
                            this.statusList[a].second[c].icon='el-icon-loading';
                            this.statusList[a].second[c].status='process';
                            for(let j=0;j<c;j++){
                              this.statusList[a].second[j].icon='el-icon-circle-check';
                              this.statusList[a].second[j].status='success';
                            }
                          }
                        }
                      }else if(iparr[a].status=='failed'){
                        this.statusList[a].icon='el-icon-circle-close';
                        this.statusList[a].status='error';
                        for(let c=0;c<this.statusList[a].second.length;c++){
                          if(iparr[a].step==this.statusList[a].second[c].title){
                            this.statusList[a].second[c].icon='el-icon-circle-close';
                            this.statusList[a].second[c].status='error';
                            for(let j=0;j<c;j++){
                              this.statusList[a].second[j].icon='el-icon-circle-check';
                              this.statusList[a].second[j].status='success';
                            }
                          }
                        }
                        this.statusList[a].description=iparr[a].error_info;
                      }
                    }
                  }
                }else if(res.status=='failed'){
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
                      if(iparr[a].status=='done'){
                        this.init_active=a+1;
                        this.statusList[a].icon='el-icon-circle-check';
                        this.statusList[a].status='success';
                        for(let c=0;c<this.statusList[a].second.length;c++){
                          this.statusList[a].second[c].icon='el-icon-circle-check';
                          this.statusList[a].second[c].status='success';
                        }
                      }else if(iparr[a].status=='ongoing'){
                        this.init_active=a;
                        this.statusList[a].icon='el-icon-loading';
                        this.statusList[a].status='process';
                        for(let c=0;c<this.statusList[a].second.length;c++){
                          if(iparr[a].step==this.statusList[a].second[c].title){
                            this.statusList[a].second[c].icon='el-icon-loading';
                            this.statusList[a].second[c].status='process';
                            for(let j=0;j<c;j++){
                              this.statusList[a].second[j].icon='el-icon-circle-check';
                              this.statusList[a].second[j].status='success';
                            }
                          }
                        }
                      }else if(iparr[a].status=='failed'){
                        this.statusList[a].icon='el-icon-circle-close';
                        this.statusList[a].status='error';
                        for(let c=0;c<this.statusList[a].second.length;c++){
                          if(iparr[a].step==this.statusList[a].second[c].title){
                            this.statusList[a].second[c].icon='el-icon-circle-close';
                            this.statusList[a].second[c].status='error';
                            for(let j=0;j<c;j++){
                              this.statusList[a].second[j].icon='el-icon-circle-check';
                              this.statusList[a].second[j].status='success';
                            }
                          }
                        }
                        this.statusList[a].description=iparr[a].error_info;
                      }
                    }
                    else{
                      //第a个修改里边的状态
                      if(iparr[a].status=='done'){
                        this.init_active=a+1;
                        this.statusList[a].icon='el-icon-circle-check';
                        this.statusList[a].status='success';
                        for(let c=0;c<this.statusList[a].second.length;c++){
                          this.statusList[a].second[c].icon='el-icon-circle-check';
                          this.statusList[a].second[c].status='success';
                        }
                      }else if(iparr[a].status=='ongoing'){
                        this.init_active=a;
                        this.statusList[a].icon='el-icon-loading';
                        this.statusList[a].status='process';
                        for(let c=0;c<this.statusList[a].second.length;c++){
                          if(iparr[a].step==this.statusList[a].second[c].title){
                            this.statusList[a].second[c].icon='el-icon-loading';
                            this.statusList[a].second[c].status='process';
                            for(let j=0;j<c;j++){
                              this.statusList[a].second[j].icon='el-icon-circle-check';
                              this.statusList[a].second[j].status='success';
                            }
                          }
                        }
                      }else if(iparr[a].status=='failed'){
                        this.statusList[a].icon='el-icon-circle-close';
                        this.statusList[a].status='error';
                        for(let c=0;c<this.statusList[a].second.length;c++){
                          if(iparr[a].step==this.statusList[a].second[c].title){
                            this.statusList[a].second[c].icon='el-icon-circle-close';
                            this.statusList[a].second[c].status='error';
                            for(let j=0;j<c;j++){
                              this.statusList[a].second[j].icon='el-icon-circle-check';
                              this.statusList[a].second[j].status='success';
                            }
                          }
                        }
                        this.statusList[a].description=iparr[a].error_info;
                      }
                    }
                  }
                  let statusgoing={title:info+'失败',icon:'el-icon-circle-close',status:'error',description:res.error_info,second:''}
                  this.statusList.push(statusgoing)
                  clearInterval(timer);
                }else if(res.status=='done'){
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
                      if(iparr[a].status=='done'){
                        this.init_active=a+1;
                        this.statusList[a].icon='el-icon-circle-check';
                        this.statusList[a].status='success';
                        for(let c=0;c<this.statusList[a].second.length;c++){
                          this.statusList[a].second[c].icon='el-icon-circle-check';
                          this.statusList[a].second[c].status='success';
                        }
                      }else if(iparr[a].status=='ongoing'){
                        this.init_active=a;
                        this.statusList[a].icon='el-icon-loading';
                        this.statusList[a].status='process';
                        for(let c=0;c<this.statusList[a].second.length;c++){
                          if(iparr[a].step==this.statusList[a].second[c].title){
                            this.statusList[a].second[c].icon='el-icon-loading';
                            this.statusList[a].second[c].status='process';
                            for(let j=0;j<c;j++){
                              this.statusList[a].second[j].icon='el-icon-circle-check';
                              this.statusList[a].second[j].status='success';
                            }
                          }
                        }
                      }else if(iparr[a].status=='failed'){
                        this.statusList[a].icon='el-icon-circle-close';
                        this.statusList[a].status='error';
                        for(let c=0;c<this.statusList[a].second.length;c++){
                          if(iparr[a].step==this.statusList[a].second[c].title){
                            this.statusList[a].second[c].icon='el-icon-circle-close';
                            this.statusList[a].second[c].status='error';
                            for(let j=0;j<c;j++){
                              this.statusList[a].second[j].icon='el-icon-circle-check';
                              this.statusList[a].second[j].status='success';
                            }
                          }
                        }
                        this.statusList[a].description=iparr[a].error_info;
                      }
                    }
                    else{
                      //第a个修改里边的状态
                      if(iparr[a].status=='done'){
                        this.init_active=a+1;
                        this.statusList[a].icon='el-icon-circle-check';
                        this.statusList[a].status='success';
                        for(let c=0;c<this.statusList[a].second.length;c++){
                          this.statusList[a].second[c].icon='el-icon-circle-check';
                          this.statusList[a].second[c].status='success';
                        }
                      }else if(iparr[a].status=='ongoing'){
                        this.init_active=a;
                        this.statusList[a].icon='el-icon-loading';
                        this.statusList[a].status='process';
                        for(let c=0;c<this.statusList[a].second.length;c++){
                          if(iparr[a].step==this.statusList[a].second[c].title){
                            this.statusList[a].second[c].icon='el-icon-loading';
                            this.statusList[a].second[c].status='process';
                            for(let j=0;j<c;j++){
                              this.statusList[a].second[j].icon='el-icon-circle-check';
                              this.statusList[a].second[j].status='success';
                            }
                          }
                        }
                      }else if(iparr[a].status=='failed'){
                        this.statusList[a].icon='el-icon-circle-close';
                        this.statusList[a].status='error';
                        for(let c=0;c<this.statusList[a].second.length;c++){
                          if(iparr[a].step==this.statusList[a].second[c].title){
                            this.statusList[a].second[c].icon='el-icon-circle-close';
                            this.statusList[a].second[c].status='error';
                            for(let j=0;j<c;j++){
                              this.statusList[a].second[j].icon='el-icon-circle-check';
                              this.statusList[a].second[j].status='success';
                            }
                          }
                        }
                        this.statusList[a].description=iparr[a].error_info;
                      }
                    }
                  }
                  let statusgoing={title:info+'成功',icon:'el-icon-circle-check',status:'success',description:'',second:''}
                  this.statusList.push(statusgoing)
                  clearInterval(timer);
                }else{
                  this.message_tips = res.error_info;
                  this.message_type = 'error';
                  messageTip(this.message_tips,this.message_type);
                }
              }
            }else if(res.attachment==null&&res.error_code=='70001'&&res.status=='failed'){
              // if(i==0){
              //   this.statusList=[];
              //   let statusgoing={}
              //   statusgoing={title:'正在'+info,icon:'el-icon-loading',status:'process',description:'',second:[]}
              //   this.statusList.push(statusgoing)
              // }
              if(i>5){
                let statusgoing={title:info+'失败',icon:'el-icon-circle-close',status:'error',description:res.error_code,second:[]}
                this.statusList.push(statusgoing)
                // this.statusList.title=info+'失败';
                // this.statusList.icon='el-icon-circle-close';
                // this.statusList.status='error';
                // this.statusList.description=res.error_code;
                clearInterval(timer);
              }
            }else if(res.attachment==null&&res.status=='ongoing'){
              this.statusList=[];
              let statusgoing={}
              statusgoing={title:'正在'+info,icon:'el-icon-loading',status:'process',description:'',second:[]}
              this.statusList.push(statusgoing)
            }else if(res.attachment==null&&res.status=='done'){
              this.statusList=[];
              let statusgoing={}
              statusgoing={title:info+'成功',icon:'el-icon-circle-check',status:'success',description:'',second:[]}
              this.statusList.push(statusgoing)
              clearInterval(timer);
            }else{
              this.statusList=[];
              let statusgoing={}
              statusgoing={title:info+'失败',icon:'el-icon-circle-close',status:'error',description:res.error_code,second:[]}
              this.statusList.push(statusgoing)
              clearInterval(timer);
            }
          }else if(info=='删除shard'||info=='删除计算节点'||info=='删除存储节点'){
          //删除shard,计算节点，存储节点
          if(res.attachment!==null){
            if(info=='删除shard'||info=='删除存储节点'){
              this.shard_show=true;
              this.init_show=false;
              this.computer_show=false;
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
                //this.getCluster();
                this.getOneCluster(this.temp_cluster);
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
          //clearInterval(timer);
          //this.info=res.error_info;
          if(res.status=='done'){
            const newArrdone={
            content:res.error_info,
              timestamp: getNowDate(),
              color: '#0bbd87',
              icon: 'el-icon-circle-check'
            };
            this.activities.push(newArrdone)
            //this.getCluster();
            this.getOneCluster(this.temp_cluster);
            //this.dialogStatusVisible=false;
            clearInterval(timer);
          }else{
            if(res.attachment==null&&res.error_code=='70001'&&res.status=='failed'){
              if(i>5){
                const newArr={
                  content:res.error_info,
                  timestamp: getNowDate(),
                  color: 'red',
                  icon: 'el-icon-circle-close'
                };
                this.activities.push(newArr);
                clearInterval(timer);
              }
            }else{
              const newArr={
                content:res.error_info,
                timestamp: getNowDate(),
                color: 'red',
                icon: 'el-icon-circle-close'
              };
              this.activities.push(newArr);
              clearInterval(timer);
            }
            //this.installStatus = true;
          }
        }else{
           const newArrgoing={
            content:res.error_info,
            timestamp: getNowDate(),
            color: '#0bbd87'
          };
          this.activities.push(newArrgoing)
          this.info=res.error_info;
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
.block{
  margin-left: 20px;
  max-height: 600px;
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
  box-shadow: inset 0 0 6px rgba(0,0,0,.3);
  background-color: rgba(0,0,0,0.1);
}
</style>
<style lang='postcss'>
.box {
  width: 100%;
  /* height: 22px; */
  display: flex;
  flex-direction: row;
}
.box > div {
  /* height: 22px; */
  flex: 1;
}
/* 全屏展示 */
/* .el-tabs__content{
		height: calc(100vh - 110px);
		overflow-y: auto;
	} */
</style>