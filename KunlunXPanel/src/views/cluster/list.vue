<template>
  <div class="app-container">
    <div class="filter-container">
      <div class="table-list-search-wrap">
        <el-input
          class="list_search_keyword"
          v-model="listQuery.name"
          placeholder="可输入集群名称搜索"
          @keyup.enter.native="handleFilter"
        />
        <el-button  icon="el-icon-search" @click="handleFilter">
          查询
        </el-button>
        <el-button  icon="el-icon-refresh-right" @click="handleClear">
          重置
        </el-button>
         <el-button
          class="filter-item"
          type="primary"
          icon="el-icon-plus"
          @click="handleCreate"
          v-if="cluster_creata_priv==='Y'"
        >新增</el-button>
        <el-popover placement="right" title="列筛选" trigger="click" width="420" style="float:right">            
            <el-checkbox-group v-model="checkedColumns" size="mini">
              <el-checkbox v-for="item in checkBoxGroup" :key="item" :label="item" :value="item"></el-checkbox>
            </el-checkbox-group>
            <el-button slot="reference" type="primary" size="small" plain><i class="el-icon-arrow-down el-icon-menu" />列筛选</el-button>
        </el-popover>
        <!-- <el-button
          class="filter-item"
          type="primary"
          icon="el-icon-plus"
          @click="handleStatus"
        >显示进度条</el-button> -->
        <div v-text="info" v-show="installStatus===true" class="info"></div>
      </div>
    </div>

    <el-table
      :key="tableKey"
      v-loading="listLoading"
      :data="list"
      border
      highlight-current-row
      style="width: 100%;margin-bottom: 20px;">
      <el-table-column
        type="index"
        align="center"
        label="序号"
        width="50">
      </el-table-column>
      <el-table-column v-if="colData[0].istrue" label="集群ID" align="center" width="70">
        <template slot-scope="{row}">
          <span class="link-type" @click="handleDetail(row)">{{ row.id }}</span>
        </template>
      </el-table-column>
      <el-table-column  
      v-if="colData[1].istrue"
       prop="nick_name" 
       label="业务名称" 
       align="center">
      </el-table-column>
       <el-table-column
       v-if="colData[2].istrue"
        prop="status"
        align="center"
        sortable
        :show-overflow-tooltip="true"
        label="状态">
        <template slot-scope="scope">
          <span v-if="scope.row.status==='运行中'" style="color: #00ed37">运行中</span>
          <span v-else style="color: red">{{scope.row.status}}</span>
        </template>
      </el-table-column>
      <el-table-column
            v-if="colData[3].istrue"
            align="center"
            label="计算节点" width="290">
           <template slot-scope="scope">
            <el-table border :data='scope.row.compList' max-height="150px">
              <el-table-column prop='ip' label="ip" align="center"></el-table-column>
              <el-table-column prop='port' label="端口" align="center" width="70"></el-table-column>
              <el-table-column prop='status' label="状态" align="center" width="80" sortable>
                <template slot-scope="scope">
                  <span v-if="scope.row.status==='active'" style="color: #00ed37">运行中</span>
                  <span v-else-if="scope.row.status==='creating'" style="color: #c7c9d1;">安装中</span>
                  <span v-else-if="scope.row.status==='manual_stop'" style="color: #c7c9d1;">停止</span>
                  <span v-else-if="scope.row.status==='inactive'" style="color: red">异常</span>
                  <span v-else></span>
                </template>
              </el-table-column>
            </el-table>
          </template>
      </el-table-column>
      <el-table-column
            v-if="colData[4].istrue"
            prop="shardtotal"
            align="center"
            label="shard分配"
             width="600">
            <template slot-scope="scope">
              <!-- :span-method="objectSpanMethod" -->
            <el-table border :data='scope.row.shardList' max-height="150px" >
              <el-table-column prop='name' label="名称" align="center" width="80"></el-table-column>
              <el-table-column prop='ip' label="ip" align="center"></el-table-column>
              <el-table-column prop='port' label="端口" align="center" width="70"></el-table-column>
              <el-table-column prop='member_state' label="主/备节点" align="center" sortable>
                <template slot-scope="scope">
                  <span v-if="scope.row.member_state==='source'" style="color: red">主</span>
                  <span v-else-if="scope.row.member_state==='replica'">备</span>
                  <span v-else></span>
                </template>
              </el-table-column>
              <el-table-column prop='status' label="状态" align="center" width="80" sortable>
                <template slot-scope="scope">
                  <span v-if="scope.row.status==='active'" style="color: #00ed37">运行中</span>
                  <span v-else-if="scope.row.status==='creating'" style="color: #c7c9d1;">安装中</span>
                  <span v-else-if="scope.row.status==='manual_stop'" style="color: #c7c9d1;">停止</span>
                  <span v-else-if="scope.row.status==='inactive'" style="color: red">异常</span>
                  <span v-else></span>
                </template>
              </el-table-column>
              <el-table-column prop='replica_delay' label="延迟时间" align="center" sortable>
                <template slot-scope="scope">
                  <span>{{scope.row.replica_delay+'s'}}</span>
                </template>
              </el-table-column>
            </el-table>
          </template>
      </el-table-column>
      
      <el-table-column
            v-if="colData[5].istrue"
            prop="back_up"
            align="center"
            label="最近备份时间">
            <template slot-scope="scope">
            <span v-if="scope.row.first_backup===''">无备份</span>
            <span v-else-if="scope.row.first_backup!==''">{{scope.row.first_backup}}</span>
        </template>
      </el-table-column>
      <el-table-column
            v-if="colData[6].istrue"
            prop="ha_mode"
            align="center"
            label="高可用模式">
      </el-table-column>
      <el-table-column   
        v-if="colData[7].istrue"
        prop="name" 
        label="集群名称"
        align="center">
      </el-table-column>
      <el-table-column
        label="操作"
        align="center"
        width="100"
        fixed="right"
        class-name="small-padding fixed-width"
        v-if="storage_node_create_priv==='Y'||shard_create_priv==='Y'||compute_node_create_priv==='Y'||restore_priv==='Y'||backup_priv==='Y'||cluster_drop_priv==='Y'||row.ha_mode==='rbr'"
      >
        <template slot-scope="{row}">
          <!-- <el-button type="primary" size="mini" @click="handleUpdate(row)" v-if="storage_node_create_priv==='Y'&&shard_create_priv==='Y'&&compute_node_create_priv==='Y'">+</el-button> -->
          <el-button type="primary" size="mini" @click="handleRetreated(row)" v-if="restore_priv==='Y'" style="margin-left: 10px;margin-bottom:2px;">回档</el-button>
          <el-button type="primary" size="mini" @click="handleExpand(row)" style="margin-bottom:2px;">扩容</el-button>
          <el-button type="primary" size="mini" @click="handleUpdate(row)" v-if="storage_node_create_priv==='Y'&&shard_create_priv==='Y'&&compute_node_create_priv==='Y'" style="margin-bottom:2px;">+</el-button>
          <!-- <el-button type="primary" size="mini" @click="handleRedo(row)"  style="margin-bottom:2px;">重做备机</el-button> -->
          <!-- <el-button type="primary" size="mini" @click="handleRestore(row)" v-if="restore_priv==='Y'">恢复</el-button> -->
          <!-- <el-button type="primary" size="mini" @click="handleBackUp(row,$index)" v-if="backup_priv==='Y'">全量备份</el-button> -->
          <!-- <el-button type="primary" size="mini" @click="handleSwitchOver(row,$index)" v-if="row.ha_mode==='rbr'">主备切换</el-button> -->
          <el-button type="primary" size="mini" @click="handleSetUp(row)">设置</el-button>
          <!-- <el-button
            size="mini"
            type="danger"
            @click="handleDelete(row,$index)"
            v-if="cluster_drop_priv==='Y'"
          >删除</el-button> -->
        </template>
      </el-table-column>
    </el-table>

    <pagination v-show="total>0" :total="total" :page.sync="listQuery.pageNo" :limit.sync="listQuery.pageSize" @pagination="getList" />
    <!-- 新增 -->
    <el-dialog :title="textMap[dialogStatus]" :visible.sync="dialogFormVisible" custom-class="single_dal_view"  :close-on-click-modal="false">
      <el-form
        ref="dataForm"
        :rules="rules"
        :model="temp"
        label-position="left"
        label-width="250px"
        >
      <el-form-item label="业务名称:" prop="nick_name"  v-show="dialogStatus==='create'||'detail'">
        <el-input  v-model="temp.nick_name" class="right_input" placeholder="请输入业务名称" :disabled="dialogStatus==='detail'"/>
      </el-form-item>
      <el-form-item label="选择计算机:" prop="machinelist"  v-if="dialogStatus==='create'">
        <div style="margin-bottom:10px;">存储：
        <!-- <el-checkbox-group 
        v-model="temp.machinelist"
        :min="minMachine"
        :max="machineTotal">
          <el-checkbox v-for="machine in machines" :label="machine.hostaddr" :key="machine.id">{{machine.hostaddr}}</el-checkbox>
        </el-checkbox-group> -->
          <el-select v-model="temp.machinelist" multiple placeholder="请选择">
            <el-option
              v-for="machine in machines"
              :key="machine.id"
              :label="machine.hostaddr"
              :value="machine.hostaddr">
            </el-option>
          </el-select>
        </div>
        <div>计算：
        <!-- <el-checkbox-group 
        v-model="temp.comp_machinelist"
        :min="comp_minMachine"
        :max="comp_machineTotal">
          <el-checkbox v-for="comp_machine in comp_machines" :label="comp_machine.hostaddr" :key="comp_machine.id">{{comp_machine.hostaddr}}</el-checkbox>
        </el-checkbox-group> -->
        <el-select v-model="temp.comp_machinelist" multiple placeholder="请选择">
            <el-option
              v-for="comp_machine in comp_machines"
              :key="comp_machine.id"
              :label="comp_machine.hostaddr"
              :value="comp_machine.hostaddr">
            </el-option>
          </el-select>
        </div>
      </el-form-item>
       <el-form-item label="高可用模式:" prop="ha_mode" v-show="dialogStatus==='create'||'detail'" >
          <el-select v-model="temp.ha_mode" placeholder="请选择高可用模式" :disabled="dialogStatus==='detail'">
            <el-option
              v-for="item in hamodeData"
              :key="item.id"
              :label="item.label"
              :value="item.id">
            </el-option>
          </el-select>
        </el-form-item>
  
        <div v-show="dialogStatus==='create'">
        <el-form-item label="shard个数:" prop="shards_count" >
          <el-input  v-model="temp.shards_count" class="right_input" placeholder="请输入shard个数" :disabled="dialogStatus==='detail'">
            <i slot="suffix" style="font-style:normal;margin-right: 10px; line-height: 30px;">个</i>
          </el-input>
        </el-form-item>
        </div>
        <el-form-item label="副本数:" prop="snode_count"  v-show="dialogStatus==='create'">
          <el-input  v-model="temp.snode_count" class="right_input"  placeholder="副本数至少是3，且不能大于256" :disabled="dialogStatus==='detail'">
           <i slot="suffix" style="font-style:normal;margin-right: 10px; line-height: 30px;">个</i>
          </el-input>
        </el-form-item>
        <el-form-item label="计算节点总个数:" prop="comp_count"  v-show="dialogStatus==='create'||'detail'">
          <el-input  v-model="temp.comp_count" class="right_input"  placeholder="输入计算节点总个数" :disabled="dialogStatus==='detail'">
           <i slot="suffix" style="font-style:normal;margin-right: 10px; line-height: 30px;">个</i>
          </el-input>
        </el-form-item>
        <el-form-item label="shard分配:" prop="shardtotal"  v-show="dialogStatus==='detail'">
          <span>{{temp.shardtotal}}</span>
        </el-form-item>
        <el-form-item label="缓冲池大小:" prop="buffer_pool"  v-show="dialogStatus==='create'||'detail'">
          <el-input  v-model="temp.buffer_pool" class="right_input"  placeholder="缓冲池大小单位为MB" :disabled="dialogStatus==='detail'">
            <i slot="suffix" style="font-style:normal;margin-right: 10px; line-height: 30px;">MB</i>
          </el-input>
        </el-form-item>
        <el-form-item label="每shard中强同步备机应当个数:" prop="fullsync_level"  v-show="dialogStatus==='create'||'detail'">
          <el-input  v-model="temp.fullsync_level" class="right_input"  placeholder="请输入每shard中强同步备机应当个数" :disabled="dialogStatus==='detail'">
            <i slot="suffix" style="font-style:normal;margin-right: 10px; line-height: 30px;">个</i>
          </el-input>
        </el-form-item>

        <div v-show="isShow"  v-if="dialogStatus==='create'?true:false">
          <el-form-item label="计算节点账号:" prop="computer_user"  v-show="dialogStatus==='create'||'detail'">
          <el-input  v-model="temp.computer_user" class="right_input"  placeholder="输入计算节点账号" :disabled="dialogStatus==='detail'">
          </el-input>
        </el-form-item>
        <el-form-item label="计算节点密码:" prop="computer_password"  v-show="dialogStatus==='create'||'detail'">
          <el-input  v-model="temp.computer_password" class="right_input"  placeholder="输入计算节点密码" :disabled="dialogStatus==='detail'">
          </el-input>
        </el-form-item>
          <el-form-item label="每个计算节点最大连接数:"  prop="max_connections">
            <el-input  v-model="temp.max_connections" class="right_input"  placeholder="请输入每个计算节点最大连接数" />
          </el-form-item>
          <el-form-item label="每个计算节点的cpu核数:" prop="per_computing_node_cpu_cores">
            <el-input  v-model="temp.per_computing_node_cpu_cores" class="right_input"  placeholder="请输入每个计算节点的cpu核数" />
          </el-form-item>
          <el-form-item label="每个计算节点最大的存储值:" prop="per_computing_node_max_mem_size">
            <el-input  v-model="temp.per_computing_node_max_mem_size" class="right_input"  placeholder="请输入每个计算节点最大的存储值">
              <i slot="suffix" style="font-style:normal;margin-right: 10px; line-height: 30px;">MB</i>
            </el-input>
          </el-form-item>
          <el-form-item label="每个存储节点的cpu核数:" prop="per_storage_node_cpu_cores">
            <el-input  v-model="temp.per_storage_node_cpu_cores" class="right_input"  placeholder="请输入每个存储节点的cpu核数"/>
          </el-form-item>
          <!-- <el-form-item label="每个存储节点innodb缓冲池大小:" prop="per_storage_node_innodb_buffer_pool_size">
            <el-input  v-model="temp.per_storage_node_innodb_buffer_pool_size" class="right_input"  placeholder="请输入每个存储节点innodb缓冲池大小">
              <i slot="suffix" style="font-style:normal;margin-right: 10px; line-height: 30px;">MB</i>
            </el-input>
          </el-form-item> -->
          <el-form-item label="每个存储节点rocksdb缓冲池大小:" prop="per_storage_node_rocksdb_buffer_pool_size">
            <el-input  v-model="temp.per_storage_node_rocksdb_buffer_pool_size" class="right_input"  placeholder="请输入每个存储节点rocksdb缓冲池大小">
              <i slot="suffix" style="font-style:normal;margin-right: 10px; line-height: 30px;">MB</i>
            </el-input>
          </el-form-item>
          <el-form-item label="每个存储节点初始化存储值:" prop="per_storage_node_initial_storage_size">
            <el-input  v-model="temp.per_storage_node_initial_storage_size" class="right_input"  placeholder="请输入每个存储节点初始化存储值">
              <i slot="suffix" style="font-style:normal;margin-right: 10px; line-height: 30px;">GB</i>
            </el-input>
          </el-form-item>
          <el-form-item label="每个存储节点最大存储值:" prop="per_storage_node_max_storage_size">
            <el-input  v-model="temp.per_storage_node_max_storage_size" class="right_input"  placeholder="请输入每个存储节点最大存储值">
              <i slot="suffix" style="font-style:normal;margin-right: 10px; line-height: 30px;">GB</i>
            </el-input>
          </el-form-item>
        </div>
        <div :class="isShow === false?'ro-90':'ro90'"  @click="toggle"  v-if="dialogStatus==='create'?true:false"><i class="el-icon-d-arrow-left" /></div>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="dialogFormVisible = false" v-show="!dialogDetail">关闭</el-button>
        <el-button type="primary" @click="dialogStatus==='create'?createData():updateData(row)"  v-show="!dialogDetail">确认</el-button>
      </div>
    </el-dialog>
    <!-- 添加 -->
    <el-dialog :title="textMap[dialogStatus]" :visible.sync="dialogNodeVisible" custom-class="single_dal_view"  :close-on-click-modal="false">
      <el-form
        ref="nodeForm"
        :model="nodetemp"
        :rules="rules"
        label-position="left"
        label-width="110px"
      >
        <el-form-item label="集群名称:" prop="name"  v-if="dialogStatus==='update'?true:false" >
          <el-input v-model="nodetemp.name" :disabled="true" />
        </el-form-item>
        <el-form-item label="类型:" prop="node_type" v-if="dialogStatus==='update'?true:false">
          <el-select v-model="nodetemp.node_type" placeholder="请选择类型"  v-if="dialogStatus=== 'update'?true:false" @change="ChangeSaler" >
            <el-option
              v-for="item in node_types"
              :key="item.id"
              :label="item.label"
              :value="item.id">
            </el-option>
          </el-select>
      </el-form-item>
      <el-form-item label="选择计算机:" prop="machinelist"  v-if="dialogStatus==='update'">
        <!-- <el-checkbox-group 
        v-model="nodetemp.machinelist"
        :min="minMachine"
        :max="machineTotal">
          <el-checkbox v-for="machine in machines" :label="machine.hostaddr" :key="machine.id">{{machine.hostaddr}}</el-checkbox>
        </el-checkbox-group> -->
        <el-select v-model="nodetemp.machinelist" multiple placeholder="请选择">
            <el-option
              v-for="machine in strogemachines"
              :key="machine.id"
              :label="machine.hostaddr"
              :value="machine.hostaddr">
            </el-option>
          </el-select>
      </el-form-item>
      <el-form-item label="shard名称:" prop="shard_name" v-if="nodetemp.node_type=='add_nodes' &&dialogStatus==='update'" >
          <el-select v-model="nodetemp.shard_name" placeholder="请选择shard名称"  v-if="dialogStatus=== 'update'?true:false">
          <el-option
            v-for="shard in shardList"
            :key="shard.id"
            :label="shard.name"
            :value="shard.id">
          </el-option>
        </el-select>
      </el-form-item>
      <el-form-item label="shard个数:" prop="shards" v-if="nodetemp.node_type=='add_shards'">
          <el-input  v-model="nodetemp.shards" class="right_input" placeholder="请输入shard个数" :disabled="dialogStatus==='detail'">
            <i slot="suffix" style="font-style:normal;margin-right: 10px; line-height: 30px;">个</i>
          </el-input>
        </el-form-item>
        <el-form-item label="节点个数:" prop="nodes" >
          <el-input  v-model="nodetemp.nodes" class="right_input" placeholder="请输入节点个数" :disabled="dialogStatus==='detail'">
            <i slot="suffix" style="font-style:normal;margin-right: 10px; line-height: 30px;">个</i>
          </el-input>
        </el-form-item>
       <!-- <el-form-item label="个数:" prop="shards" v-if="dialogStatus==='update'">
            <el-select v-model="nodetemp.shards" placeholder="请选择个数" :disabled="dialogStatus==='detail'">
            <el-option
              v-for="item in shardsDate"
              :key="item.id"
              :label="item.label"
              :value="item.id">
            </el-option>
          </el-select>
        </el-form-item> -->
      </el-form>
        <div slot="footer" class="dialog-footer">
          <el-button @click="dialogNodeVisible = false">关闭</el-button>
          <el-button type="primary" @click="updateData(row)">确认</el-button>
        </div>
    </el-dialog>
    <!-- 恢复-->
    <el-dialog :title="textMap[dialogStatus]" :visible.sync="dialogRestoreVisible" custom-class="single_dal_view"  :close-on-click-modal="false">
      <el-form
        ref="restoreForm"
        :model="restoretemp"
        :rules="rules"
        label-position="left"
        label-width="110px"
      >
      <el-form-item label="选择计算机:" prop="machinelist"  v-if="dialogStatus==='restore'">
        <el-checkbox-group 
        v-model="restoretemp.machinelist"
        :min="minMachine"
        :max="machineTotal">
          <el-checkbox v-for="machine in machines" :label="machine.hostaddr" :key="machine.id">{{machine.hostaddr}}</el-checkbox>
        </el-checkbox-group>
      </el-form-item>
        <el-form-item label="原集群名称:" prop="old_cluster_name">
          <el-input v-model="restoretemp.old_cluster_name" :disabled="true" />
        </el-form-item>
        <el-form-item label="新集群名称:" prop="nick_name">
          <el-input v-model="restoretemp.nick_name" placeholder="请输入新集群名称" />
        </el-form-item>
        <el-form-item label="回档时间:" prop="now">
          <el-date-picker v-model="restoretemp.now"  type="datetime" value-format="yyyy-MM-dd HH:mm:ss" placeholder="请选择回档时间"></el-date-picker>
        </el-form-item>
        <el-form-item label="最早备份时间:" prop="end_ts">
          <el-input  v-model="restoretemp.end_ts"  :disabled="true" />
        </el-form-item>
      </el-form>

        <div slot="footer" class="dialog-footer">
          <el-button @click="dialogRestoreVisible = false">关闭</el-button>
          <el-button type="primary" @click="restoreData(restoretemp)">确认</el-button>
        </div>
    </el-dialog>
    <!-- 回档-->
    <el-dialog :title="textMap[dialogStatus]" :visible.sync="dialogRetreatedVisible" custom-class="single_dal_view"  :close-on-click-modal="false">
      <el-form
        ref="retreatedForm"
        :model="retreatedtemp"
        :rules="rules"
        label-position="left"
        label-width="120px"
      >
        <el-form-item label="原集群名称:" prop="old_cluster_id">
          <!-- <el-input v-model="retreatedtemp.old_cluster_name" /> -->
          <el-select v-model="retreatedtemp.old_cluster_id" clearable placeholder="请选择原集群名称" style="width:100%;">
            <el-option
              v-for="item in oldClusterList"
              :key="item.id"
              :label="item.name"
              :value="item.id">
            </el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="回档集群名称:" prop="nick_name">
          <el-input v-model="retreatedtemp.nick_name"  :disabled="true" />
        </el-form-item>
        <el-form-item label="回档时间:" prop="retreated_time">
          <el-date-picker v-model="retreatedtemp.retreated_time"  type="datetime" value-format="yyyy-MM-dd HH:mm:ss" placeholder="请选择回档时间"></el-date-picker>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="dialogRetreatedVisible = false">关闭</el-button>
        <el-button type="primary" @click="retreatedData(retreatedtemp)">确认</el-button>
      </div>
    </el-dialog>
    <!-- 扩容-->
    <el-dialog :title="textMap[dialogStatus]" :visible.sync="dialogExpandVisible" custom-class="single_dal_view"  :close-on-click-modal="false" >
      <el-form
        ref="expandForm"
        :model="expandtemp"
        :rules="rules"
        label-position="left"
        label-width="130px"
      >
        <div class="icons-container">
          <el-tabs type="border-card" v-model="activeName" @tab-click="handleClick" >
            <el-tab-pane  v-for="(item,index) in shardNameList" :key="index" :label="item.name" :name="item.name" :value="item.id+'_'+item.cluster_id">
             <el-table
              :key="tableKey"
              v-loading="listLoading"
              :data="shardTable"
              max-height="240"
              @selection-change="selectionChangeHandle"
              border
              highlight-current-row
              style="width: 100%;margin-bottom: 20px;">
                <!-- :selectable='checkboxInit' -->
                <el-table-column type="selection" width="55"></el-table-column>
                <el-table-column
                    type="index"
                    align="center"
                    label="序号"
                    width="50">
                </el-table-column>
                <el-table-column   
                prop="TABLE_SCHEMA" 
                label="数据库名称"
                align="center">
                </el-table-column>
                <el-table-column  
                prop="TABLE_NAME" 
                label="表名称" 
                align="center">
                </el-table-column>
              </el-table>
              <el-form-item label="原shard:" prop="shard_name">
                <el-input v-model="expandtemp.shard_name"  :disabled="true" />
              </el-form-item>
              <el-form-item label="是否保留原表:" prop="if_save">
                <el-radio v-model="expandtemp.if_save" label="0">是</el-radio>
                <el-radio v-model="expandtemp.if_save" label="1">否</el-radio>
              </el-form-item>
              <el-form-item label="已选原shard表:" prop="table_list" v-if="expandtemp.table_list.length">
                <template>
                  <!-- v-infinite-scroll="load"  -->
                  <ul class="infinite-list" style="max-height:200px;overflow:auto">
                    <li v-for="(item,index) in expandtemp.table_list" :key="index" class="infinite-list-item">{{ item.TABLE_SCHEMA+'.'+item.TABLE_NAME }}</li>
                  </ul>
                </template>
              </el-form-item>
              <el-form-item label="目标shard:" prop="dst_shard_id" v-if="expandtemp.dsc_flag">
                <el-select v-model="expandtemp.dst_shard_id" clearable placeholder="请选择目标shard" style="width:100%;" @change="ChangeShardName">
                  <el-option
                    v-for="item in shardsList"
                    :key="item.id"
                    :label="item.name"
                    :value="item.id">
                  </el-option>
                </el-select>
              </el-form-item>
              
            </el-tab-pane>
          </el-tabs>
        </div>
      </el-form>

      <div slot="footer" class="dialog-footer">
        <el-button @click="dialogExpandVisible = false">关闭</el-button>
        <el-button type="primary" @click="showExpandInfo(expandtemp)">{{expandtemp.title}}</el-button>
      </div>
    </el-dialog>
    <!-- 扩容确认信息 -->
    <el-dialog :title="textMap[dialogStatus]" :visible.sync="dialogExpandInfoVisible" custom-class="single_dal_view"  :close-on-click-modal="false">
      <el-form
        ref="expandInfoForm"
        :model="expandInfoTemp"
        :rules="rules"
        label-position="left"
        label-width="130px"
      >
        <el-form-item label="原shard:" prop="shard_name">
          <el-input v-model="expandInfoTemp.shard_name"  :disabled="true" />
        </el-form-item>
        <el-form-item label="已选原shard表:" prop="table_list">
          <template>
            <ul class="infinite-list" style="max-height:200px;overflow:auto">
              <li v-for="(item,index) in expandInfoTemp.table_list" :key="index" class="infinite-list-item">{{ item.TABLE_SCHEMA+'.'+item.TABLE_NAME }}</li>
            </ul>
          </template>
        </el-form-item>
        <el-form-item label="排序方式:" prop="policy_name" v-if="expandInfoTemp.policy">
          <el-input v-model="expandInfoTemp.policy_name"  :disabled="true" />
        </el-form-item>
        <el-form-item label="目标shard:" prop="dst_shard_name">
          <el-input v-model="expandInfoTemp.dst_shard_name"  :disabled="true" />
        </el-form-item>
        <el-form-item label="是否保留原表:" prop="if_save">
          <template>
            <span v-if="expandInfoTemp.if_save=='0'">是</span>
            <span v-else>否</span>
        </template>
        </el-form-item>
      </el-form>

      <div slot="footer" class="dialog-footer">
        <el-button @click="expandClose">返回</el-button>
        <el-button type="primary" @click="expandData(expandInfoTemp)">确认</el-button>
      </div>
    </el-dialog>
    <!--自动扩容 -->
     <el-dialog :title="textMap[dialogStatus]" :visible.sync="outoExpandInfoVisible" custom-class="single_dal_view"  :close-on-click-modal="false">
      <el-form
        ref="autoExpandForm"
        :model="autoexpandtemp"
        :rules="rules"
        label-position="left"
        label-width="130px"
      >
        <el-form-item label="原shard:" prop="auto_shard_id">
          <el-select v-model="autoexpandtemp.auto_shard_id" clearable placeholder="请选择原shard" style="width:100%;" @change="autoChangeShardName">
            <el-option
              v-for="item in srcShardsList"
              :key="item.id"
              :label="item.name"
              :value="item.id">
            </el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="排序方式:" prop="policy">
          <el-select v-model="autoexpandtemp.policy" placeholder="请选择排序方式" @change="autoChangePolicy">
            <el-option
              v-for="item in policys"
              :key="item.id"
              :label="item.label"
              :value="item.id">
            </el-option>
          </el-select>
        </el-form-item>
        <el-table
        ref="dataTable"
        :key="tableKey"
        v-loading="listLoading"
        :data="autoexpandtemp.tables"
        max-height="240"
        @selection-change="selAutoChangeHandle"
        border
        highlight-current-row
        style="width: 100%;margin-bottom: 20px;">
          <!-- :selectable='checkboxInit' -->
          <el-table-column type="selection" width="55"></el-table-column>
          <el-table-column
              type="index"
              align="center"
              label="序号"
              width="50">
          </el-table-column>
          <el-table-column   
          prop="TABLE_SCHEMA" 
          label="数据库名称"
          align="center">
          </el-table-column>
          <el-table-column  
          prop="TABLE_NAME" 
          label="表名称" 
          align="center">
          </el-table-column>
        </el-table>
        <el-form-item label="已选原shard表:" prop="table_list" >
          <template>
            <ul class="infinite-list"  style="max-height:200px;overflow:auto">
              <li v-for="(item,index) in autoexpandtemp.table_list" :key="index" class="infinite-list-item">{{ item.TABLE_SCHEMA+'.'+item.TABLE_NAME }}</li>
            </ul>
          </template>
        </el-form-item>
        <el-form-item label="是否保留原表:" prop="if_save">
          <el-radio v-model="autoexpandtemp.if_save" label="0">是</el-radio>
          <el-radio v-model="autoexpandtemp.if_save" label="1">否</el-radio>
        </el-form-item>
        <el-form-item label="目标shard:" prop="dst_shard_id" v-if="autoexpandtemp.dsc_flag">
          <el-select v-model="autoexpandtemp.dst_shard_id" clearable placeholder="请选择目标shard" style="width:100%;" @change="autoChangeDscShardName">
            <el-option
              v-for="item in shardsList"
              :key="item.id"
              :label="item.name"
              :value="item.id">
            </el-option>
          </el-select>
        </el-form-item>
      </el-form>

      <div slot="footer" class="dialog-footer">
        <el-button @click="outoExpandInfoVisible = false">关闭</el-button>
        <el-button type="primary" @click="autoExpandInfo(autoexpandtemp)">提交</el-button>
      </div>
    </el-dialog>
    <!--状态框 -->
    <el-dialog :visible.sync="dialogStatusVisible" custom-class="single_dal_view" width="400px"  :close-on-click-modal="false" :before-close="beforeSyncDestory">
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
    <el-dialog :title="job_id" :visible.sync="dialogStatusShowVisible" custom-class="single_dal_view" :close-on-click-modal="false" :before-close="beforeDestory">
      <!-- <div style="height: 400px;">
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
      </div> -->
     <!-- <div class="stepComponent" >
    <div class="approvalProcess" >
        <el-steps :active="active" finish-status="success" direction="vertical" >
           <el-step :title="item.label"  v-for="item in approvalProcessProject" :id="item.id">
            <template slot="description" >
             <div class="step-row" v-for="item in approvalProcessProject">
               <table width="100%" border="0" cellspacing="0" cellpadding="0" class="processing_content">
                         <tr>
                            <td style="color:#98A6BE">
                                <div class="processing_content_detail" style="float:left;width:70%"><span >192.168.0.136_47001</span></div> 
                              <div class="processing_content_detail" style="float:right;"><span ><i class="el-icon-time"></i>&nbsp;&nbsp;昨天12:24</span> </div>
                            </td>
                          </tr>
                          <tr>
                            <td>
                                <div class="processing_content_detail" style="float:left;width:70%">
                                <div style="float:left;width: 2px;height: 20px; background:#C7D4E9;margin-left:10px;margin-right:10px"></div> 
                                <span style="color:#919FB8">ongoing</span></div> 
                            </td>
                          </tr>
                </table>
           </div>
         </template>
 
           </el-step>
        </el-steps>
         <el-button style="margin-top: 12px;" @click="next">下一步</el-button>
 
  </div>
 
</div> -->
      
      <!-- <div style="width: 100%;background: #fff;padding:20px; ">
      <el-steps class="hoverSteps" direction="vertical" :space="80" :active="2">
        <el-step v-for="(value, key) in lists" :key="key">
          <template slot="description">
              <div @mouseover="mouseOver(key)"
                   @mouseleave="mouseLeave(key)" :id="key">
                   <div class="stepNoBtn" :class="current===key? 'stepBtn':''">
                      <div class="step-title-font">{{ value.title }}</div>
                    <div>{{value.description}}</div>
                    <div class="btnPosition">
                      <el-button v-if="current===key" size="mini" type="primary">详情</el-button>
                    </div>
                   </div>
            </div>
          </template>
        </el-step>
      </el-steps>
    </div> -->

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
          <el-step :title="finish_title" :icon="finish_icon" :description="finish_description" :status="finish_state" v-if="finish_show"></el-step>
        </el-steps>
      </div>
    </el-dialog>
    <!--shard信息框 -->
    <el-dialog :title="dialogStatus" :visible.sync="dialogShardInfo" custom-class="single_dal_view" :close-on-click-modal="false">
      <json-viewer :value="shardInfo"></json-viewer>
    </el-dialog>
    <!--扩容状态框 -->
    <el-dialog :title="job_id" :visible.sync="dialogExpondInfo" custom-class="single_dal_view" :close-on-click-modal="false" :before-close="beforeExpandDestory">
      <span v-if="expondInit">{{expand_init}}</span>
      <json-viewer :value="expondInfo" v-if="expondSatus"></json-viewer>
      <span v-if="expondResult">{{expand_end}}</span>
    </el-dialog>
  </div>
</template>

<script>
 import { messageTip,handleCofirm,getNowDate,getNextMonth,createCode,gotoCofirm } from "@/utils";
 import Pagination from '@/components/Pagination' 
 import {ha_mode_arr,c_ha_mode_arr,shards_arr,per_shard_arr,norepshards_arr,node_type_arr,c_node_type_arr,version_arr,storage_type_arr,timestamp_arr,policy_arr} from "@/utils/global_variable"
 import {getClusterList,ifBackUp,getAllMachine,getShards,uAssign,getStroMachine,getCompMachine,createCluster,delCluster,backeUpCluster,addShards,addComps,addNodes,restoreCluster,getBackUpStorage,getEvStatus,getShardsJobLog,getOldCluster, getShardsCount,getShardsName,getShardTable,getOtherShards,expandCluster,getExpandTableList } from '@/api/cluster/list'
 //import {addShards,createCluster,addComps,addNodes,getEvStatus,delCluster,backeUpCluster,restoreCluster,getMetaMode,getBackUpStorage} from '@/api/cluster/listInterface'
 import {getMetaMode} from '@/api/cluster/listInterface'
 import JsonViewer from 'vue-json-viewer'
export default {
  name: "list",
  components: { Pagination,JsonViewer }, 
  // props: ['data', 'defaultActive','comment'],
  props:{comment:{type:Array}},
  data() {  
    // const validatemachine = (rule, value, callback) => {
    //  if(value.length === 0){
    //     callback(new Error("请选择计算机"));
    //   }
    //   // else if(value.length <3){
    //   //   callback(new Error("所选计算机数不能小于3"));
    //   // }
    //   else {
    //     callback();
    //   }
    // };
    const validateHaMode = (rule, value, callback) => {
     if(!value){
        callback(new Error("请选择高可用模式"));
      }
      else {
        callback();
      }
    };
     const validateShardsCount = (rule, value, callback) => {
     if(!value){
        callback(new Error("请输入shard个数"));
      }else if(!(/^[0-9]+$/.test(value))){
        callback(new Error("shard个数只能输入数字"));
      }
      else if(value>256){
        callback(new Error("shard个数不能大于256"));
      }
      else {
        callback();
      }
    };
    const validateSnodeCount = (rule, value, callback) => {
     if(!value){
        callback(new Error("请输入副本数"));
      }else if(!(/^[0-9]+$/.test(value))){
        callback(new Error("副本数只能输入数字"));
      }else if(value<3){
        callback(new Error("副本数不能小于3"));
      }else if(value>256){
        callback(new Error("副本数不能大于256"));
      }
      else {
        callback();
      }
    };
    const validateCompCount = (rule, value, callback) => {
     if(!value){
        callback(new Error("请输入计算节点总个数"));
      }else if (!(/^[0-9]+$/.test(value)) ) {
        callback(new Error("计算节点总个数只能输入数字"));
      }
      else {
        callback();
      }
    };
    const validateBufferPool = (rule, value, callback) => {
     if(!value){
        callback(new Error("请输入缓冲池大小"));
      }else if (!(/^[0-9]+$/.test(value)) ) {
        callback(new Error("缓冲池大小只能输入数字"));
      }
      else {
        callback();
      }
    };
    const validateRestoreTime = (rule, value, callback) => {
      const time=getNowDate();
      const netxMonth=getNextMonth(time);
      if(!this.restoretemp.now){
        callback(new Error("请选择回档时间"));
      }else if(this.restoretemp.now<this.restoretemp.end_ts){
        callback(new Error("回档时间不能早于最早备份时间"));
      }else if(this.restoretemp.now>netxMonth){
        callback(new Error("回档时间选择范围应在当前系统时间开始一个月之内"));
      }else {
      callback();
      }
    };
    const validateretreatedTime = (rule, value, callback) => {
      const time=getNowDate();
      if(!value){
        callback(new Error("请选择回档时间"));
      }else if(value>time){
        callback(new Error("回档时间不能晚于当前时间"));
      }else {
      callback();
      }
    };
    const validateNodeType = (rule, value, callback) => {
     if(!value){
        callback(new Error("请选择类型"));
      }
      else {
        callback();
      }
    };
    const validateShardName = (rule, value, callback) => {
      if(this.nodetemp.node_type==='add_nodes'){
        if(!value){
          callback(new Error("请选择shard名称"));
        }
        else {
          callback();
        }
      }else{
         callback();
      }
    };
     const validateNodeTotal = (rule, value, callback) => {
     if(!value){
        callback(new Error("请输入shard个数"));
      }else if(!(/^[0-9]+$/.test(value))){
        callback(new Error("shard个数只能输入数字"));
      }else if(value>256){
        callback(new Error("shard个数不能大于256"));
      }
      else {
        callback();
      }
    };
    const validateNickName = (rule, value, callback) => {
      if(!value){
        callback(new Error("请输入业务名称"));
      }
      else {
      callback();
      }
    };
     const validateFullsyncLevel = (rule, value, callback) => {
      if(!value){
        callback(new Error("请输入每shard中强同步备机应当个数"));
      }else if (!(/^[0-9]+$/.test(value)) ) {
        callback(new Error("每shard中强同步备机应当个数只能输入数字"));
      }else if(value>=this.temp.snode_count){
        callback(new Error("每shard中强同步备机应当个数应小于副本数"));
      }
      else {
      callback();
      }
    };
    const validateNodes = (rule, value, callback) => {
      if(!value){
        callback(new Error("请输入节点个数"));
      }else if(!(/^[0-9]+$/.test(value))){
        callback(new Error("节点个数只能输入数字"));
      }else if(value>256){
        callback(new Error("节点个数不能大于256"));
      }
      else {
        callback();
      }
    };
    const validateOldClusterId = (rule, value, callback) => {
     if(!value){
        callback(new Error("请选择原集群名称"));
      }
      else {
        callback();
      }
    };
    const validateDstShardName = (rule, value, callback) => {
     if(!value){
        callback(new Error("请选择目标shard"));
      }
      else {
        callback();
      }
    };
    const validatePolicy = (rule, value, callback) => {
     if(!value){
        callback(new Error("请选择排序方式"));
      }
      else {
        callback();
      }
    };
    const validateAutoShardId = (rule, value, callback) => {
     if(!value){
        callback(new Error("请选择原shard"));
      }
      else {
        callback();
      }
    };
    const validateTableList = (rule, value, callback) => {
     if(value.length==0){
        callback(new Error("请选择需要扩容的表"));
      }
      else {
        callback();
      }
    };
    return {
      tableKey: 0,
      list: null,
      listLoading: true,
      searchLoading:false,
      total: 0,
      listQuery: {
        pageNo: 1,
        pageSize: 10,
        name: '',
        user_name:sessionStorage.getItem('login_username')
      },
      temp: {
        ha_mode:sessionStorage.getItem('work_mode')=='enterprise'?'rbr':'mgr',
        shards_count:'1',
        snode_count: '3',
        comp_count:'1',
        buffer_pool:'1024',
        max_connections:'6',
        per_computing_node_cpu_cores:'8',
        per_computing_node_max_mem_size:'',
        per_storage_node_cpu_cores:'8',
        per_storage_node_innodb_buffer_pool_size:'',
        per_storage_node_rocksdb_buffer_pool_size:'',
        per_storage_node_initial_storage_size:'20',
        per_storage_node_max_storage_size:'20',
        nick_name:'',
        machinelist:[],
        comp_machinelist:[],
        node_type:'',
        shard_name:'',
        shards:'',
        computer_user:'',
        computer_password:'',
        fullsync_level:'1'
      },
      restoretemp:{
        old_cluster_name:'',
        now:'',
        end_ts:'',
        machinelist:[],
        backup_cluster_name:'',
        nick_name:''
      },
      retreatedtemp:{
        old_cluster_name:'',
        retreated_time:'',
        nick_name:'',
        old_cluster_id:'',
        new_cluster_id:''
      },
      expandtemp:{
        shard_name:'',
        list:{cluster_id:'',shard_id:'',shard_name:''},
        table_list:[],
        dst_shard_id:'',
        dst_shard_name:'',
        title:'自动扩容',
        dsc_flag:false,
        if_save:'0'
      },
      autoexpandtemp:{
        shard_name:'',
        list:{cluster_id:'',shard_id:'',shard_name:''},
        table_list:[],
        policy:'',
        auto_shard_id:'',
        tables:[],
        policy_name:'',
        dst_shard_id:'',
        dst_shard_name:'',
        dsc_flag:false,
        if_save:'0'

      },
      expandInfoTemp:{
        shard_name:'',
        list:{cluster_id:'',shard_id:'',shard_name:''},
        table_list:[],
        dst_shard_id:'',
        dst_shard_name:'',
        policy:'',
        policy_name:''
      },
      nodetemp:{
        machinelist:[],
        node_type:'',
        shard_name:'',
        shards:'',
        name:'',
        nick_name:'',
        nodes:''
      },
      dialogFormVisible: false,
      dialogRestoreVisible:false,
      dialogRetreatedVisible:false,
      dialogNodeVisible:false,
      dialogStatusVisible:false,
      dialogExpandVisible:false,
      dialogExpandInfoVisible:false,
      dialogExpondInfo:false,
      expondSatus:true,
      expondResult:false,
      dialogStatus: "",
      textMap: {
        update: "添加",
        create: "新增集群",
        detail: "详情",
        restore:'恢复集群',
        retreated:'回档集群',
        expand:'集群扩容',
        expandInfo:'集群扩容确认信息',
        outoExpand:'自动扩容'
      },
      dialogDetail: false,
      message_tips:'',
      message_type:'',
      user_add_priv:'',
      user_drop_priv:'',
      user_edit_priv:'',
      row:{},
      hamodeData:sessionStorage.getItem('work_mode')=='enterprise'?ha_mode_arr:c_ha_mode_arr,
      shardsDate:shards_arr,
      norepshardsDate:norepshards_arr,
      pershardDate:per_shard_arr,
      isShow:false,
      machinelist:[],
      machines:[],
      minMachine:0,
      machineTotal:0,
      comp_machines:[],
      comp_minMachine:0,
      comp_machineTotal:0,
      node_types:sessionStorage.getItem('work_mode')=='enterprise'?node_type_arr:c_node_type_arr,
      shardList:[],
      backup_priv:JSON.parse(sessionStorage.getItem('priv')).backup_priv,
      restore_priv:JSON.parse(sessionStorage.getItem('priv')).restore_priv,
      cluster_creata_priv:JSON.parse(sessionStorage.getItem('priv')).cluster_creata_priv,
      cluster_drop_priv:JSON.parse(sessionStorage.getItem('priv')).cluster_drop_priv,
      shard_create_priv:JSON.parse(sessionStorage.getItem('priv')).shard_create_priv,
      storage_node_create_priv:JSON.parse(sessionStorage.getItem('priv')).storage_node_create_priv,
      compute_node_create_priv:JSON.parse(sessionStorage.getItem('priv')).compute_node_create_priv,
      timer:null,
      installStatus:false,
      info:'',
      activities: [],
      dialogStatusShowVisible:false,
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
      computer:[
        // {
        //   title: '192.168.0.136_47001;',
        //   icon: 'el-icon-loading',
        //   status: 'process',
        //   computer_id:'101',
        //   description:''
        // },
      ],
       shard:[
        // {
        //   title: '104',
        //   icon: 'el-icon-circle-check',
        //   status: 'success',
        //   shard_id:'104',
        //   description:''
        // },
      ],
      computer_state:'',
      storage_state:'',
      finish_state:'',
      computer_title:'',
      computer_icon:'',
      shard_icon:'',
      shard_title:'',
      comp_active:0,
      shard_active:0,
      strogemachines:[],
      init_title:'',
      init_show:true,
      finish_show:false,
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
      job_id:'',
      oldClusterList:[],
      shardNameList:[],
      activeName:'',
      shardTable:[],
      shardsList:[],
      srcShardsList:[],
      expondInfo:'',
      expondSatus:true,
      expand_init:'',
      expand_end:'',
      expondInit:true,
      expondResult:false,
      outoExpandInfoVisible:false,
      policys:policy_arr,
      editableTabs:[],
      //列表动态隐藏
      colData: [
        { title: "集群ID", istrue: true },
        { title: "业务名称", istrue: true },
        { title: "状态", istrue: true },
        { title: "计算节点", istrue: true },
        { title: "shard分配", istrue: true },  
        { title: "最近备份时间", istrue: false },  
        { title: "高可用模式", istrue: false },
        { title: "集群名称", istrue: false },  
      ],
      checkBoxGroup: [],
      checkedColumns: ['集群ID','业务名称','状态','计算节点','shard分配'],
      // active: 0,
      //  approvalProcessProject:[
      //      {id:'0',label: "computer_step"},
      //      {id:'1',label: "shard_step"},
      //     //  { id:'2',label: "割接审批"},
      //     //  { id:'3',label: "审批成功"},
      //  ],
      // current :0,
      // lists:[
      //     {
      //       title:'shard_step',
      //       description:'192.168.0.125_57001;192.168.0.136_57001;192.168.0.125_570',
      //       status:0 // 判断是否有按钮及盒子凸显，例子中0代表不凸显无按钮。 具体情况可自行判断
      //     },
      //     {
      //       title:'computer_step',
      //       description:'192.168.0.136_47001;',
      //       status:1
      //     },
      //     {
      //       title:'完成中低先级需求及问题的研发、测试及上线',
      //       description:'2020-05-31',
      //       status:0
      //     }
      //   ],
      //dialogBackUpStorageVisible:false,
      //stypelist:storage_type_arr,
      rules: {
        // machinelist: [
        //   { required: true, trigger: "blur",validator: validatemachine },
        // ],
        ha_mode: [
          { required: true, trigger: "blur",validator: validateHaMode },
        ],
         shards_count: [
          { required: true, trigger: "blur",validator: validateShardsCount },
        ],
         snode_count: [
          { required: true, trigger: "blur",validator: validateSnodeCount },
        ],
        comp_count: [
          { required: true, trigger: "blur",validator: validateCompCount },
        ],
        buffer_pool: [
          { required: true, trigger: "blur",validator: validateBufferPool },
        ],
        now: [
           { required: true, trigger: "blur",validator: validateRestoreTime },
        ],
        node_type: [
            { required: true, trigger: "blur",validator: validateNodeType },
        ],
        shard_name: [
            { required: true, trigger: "blur",validator: validateShardName},
        ],
        shards: [
            { required: true, trigger: "blur",validator: validateNodeTotal },
        ],
        nodes: [
            { required: true, trigger: "blur",validator: validateNodes },
        ],
        // cluster_name: [
        //     { required: true, trigger: "blur",validator: validateClusterName },
        // ],
        nick_name: [
            { required: true, trigger: "blur",validator: validateNickName },
        ],
        fullsync_level:[
            { required: true, trigger: "blur",validator: validateFullsyncLevel },
        ],
        retreated_time:[
          {required: true, trigger: "blur",validator: validateretreatedTime }
        ],
        old_cluster_id:[
          {required: true, trigger: "blur",validator: validateOldClusterId }
        ],
        dst_shard_id:[
          {required: true, trigger: "blur",validator: validateDstShardName }
        ],
        policy:[
          {required: true, trigger: "blur",validator: validatePolicy }
        ],
        auto_shard_id:[
          {required: true, trigger: "blur",validator: validateAutoShardId }
        ],
        table_list:[
          {required: true, trigger: "blur",validator: validateTableList }
        ]
      },
  
    };
  },
  created() {
    // sessionStorage.setItem('oneClusterList',false);
    // sessionStorage.setItem('cshow',true);
    this.getList();
    //this.getMode();
      // 列筛选
      this.colData.forEach((item, index) => {
        this.checkBoxGroup.push(item.title);
        //this.checkedColumns.push(item.title);
      })
      this.checkedColumns = this.checkedColumns
      let UnData = localStorage.getItem(this.colTable)
      UnData = JSON.parse(UnData)
      if (UnData != null) {
        this.checkedColumns = this.checkedColumns.filter((item) => {
          return !UnData.includes(item)
        })
      }
      //console.log(this.checkedColumns);
  },
  watch: {
    'temp.machinelist': {
      handler: function(val,oldVal) {
       //旧数据中包含时无需push
      //   this.temp.machinelist=[];
      //   this.temp.machinelist.forEach(item => {
      //   if (this.temp.machinelist.indexOf(item) == -1) {
      //     const newArr={"hostaddr":item};
      //     this.temp.machinelist.push(newArr)
      //   }
      // })
      //this.temp.shards_count=this.temp.machinelist.length;
      },
    },
    'expandtemp.table_list': {
      handler: function(val,oldVal) {
        if(val.length>0){
          this.expandtemp.dsc_flag=true;
          this.expandtemp.title='提交'
        }else{
          this.expandtemp.dsc_flag=false;
          this.expandtemp.title='自动扩容'
          this.expandtemp.shard_name='';  
        }
      },
    },
    'autoexpandtemp.table_list': {
      handler: function(val,oldVal) {
        if(val.length>0){
          this.autoexpandtemp.dsc_flag=true;
        }else{
          this.autoexpandtemp.dsc_flag=false;
        }
      },
    },
    checkedColumns(val,value) {
     let arr = this.checkBoxGroup.filter(i => !val.includes(i)); // 未选中
     localStorage.setItem(this.colTable, JSON.stringify(arr))
     this.colData.filter(i => {
       if (arr.indexOf(i.title) != -1) {
         i.istrue = false;
       } else {
         i.istrue = true;
       }
     });
   }
  },
  destroyed() {
    clearInterval(this.timer)
    this.timer = null
  },
  methods: {
    // objectSpanMethod({ row, column, rowIndex, columnIndex}) {
    //   console.log(this.list[rowIndex]['shardList']);
    //   const fields = ['name']
    //   if (fields.includes(column.property)) {
    //     const cellValue = row['name']
    //     //console.log(column.property);
    //     if (cellValue && fields.includes(column.property)) {
    //       const prevRow = this.list[rowIndex]['shardList'][rowIndex - 1]  
    //       let nextRow = this.list[rowIndex]['shardList'][rowIndex + 1]
    //       if (prevRow && prevRow['name'] === cellValue) {
    //         return { rowspan: 0, colspan: 0 }
    //       } else {
    //         // return { rowspan: row.rowspan, colspan: 1 }
    //         let countRowspan = 1
    //         while (nextRow && nextRow['name'] === cellValue) {
    //           nextRow = this.list[rowIndex]['shardList'][++countRowspan + rowIndex]
    //         }
    //         if (countRowspan > 1) {
    //           return { rowspan: countRowspan, colspan: 1 }
    //         }
    //       }
    //     }
    //   }
    // },
    beforeSyncDestory(){
      clearInterval(this.timer)
      this.dialogStatusVisible=false;
      this.timer=null;
    },
    beforeExpandDestory(){
      clearInterval(this.timer)
      this.dialogExpondInfo=false;
      this.timer = null;
    },
    autoChangeDscShardName(value){
      for(let i=0;i<this.shardsList.length;i++){
        if(this.shardsList[i].id==value){
          this.autoexpandtemp.dst_shard_name=this.shardsList[i].name;
        }
      }
    },
    selAutoChangeHandle(val){
      this.autoexpandtemp.table_list=val;
    },
    autoChangeShardName(value){
      for(let i=0;i<this.srcShardsList.length;i++){
        if(this.srcShardsList[i].id==value){
          this.autoexpandtemp.shard_name=this.srcShardsList[i].name;
          this.autoexpandtemp.list.shard_id=this.srcShardsList[i].id;
          this.autoexpandtemp.list.shard_name=this.srcShardsList[i].name;
        }
      }
      if(this.autoexpandtemp.shard_name){
        //获取shards名称
        this.shardsList=[];
        let temp={cluster_id:this.autoexpandtemp.list.cluster_id,shard_id:this.autoexpandtemp.list.shard_id};
        getOtherShards(temp).then((res) => {
          this.shardsList = res.list;
        });
      }
      const tempData = {};
      tempData.job_id = '';
      tempData.version=version_arr[0].ver;
      tempData.user_name=sessionStorage.getItem('login_username');
      tempData.job_type='get_expand_table_list';
      tempData.timestamp=timestamp_arr[0].time+'';
      const paras={}
      paras.cluster_id = this.autoexpandtemp.list.cluster_id;
      paras.shard_id=value;
      paras.policy=this.autoexpandtemp.policy;
      tempData.paras=paras;
      if(this.autoexpandtemp.policy){
        this.autoexpandtemp.tables=[];
        getExpandTableList(tempData).then((ress) => {
          if(ress.error_code=='0'){
            if(ress.attachment.table_list!==''){
              const ss=ress.attachment.table_list.split(',');
              let table=[];
              for(let a=0;a<ss.length;a++){
                let arr=ss[a].split('.');
                let table_arr={TABLE_SCHEMA:arr[1],TABLE_NAME:arr[2]}
                table.push(table_arr);
              }
              this.autoexpandtemp.tables = table;
              if(this.autoexpandtemp.tables){
                this.$nextTick(()=>{
                  this.autoexpandtemp.tables.forEach((row) => {
                    this.$refs.dataTable.toggleAllSelection(row, true)
                  })
                })
              }
            }else{
              this.autoexpandtemp.tables = false;
            }
            
            //this.autoexpandtemp.table_list = ress.attachment.table_list;
          }else{
            this.message_tips = ress.error_info;
            this.message_type = 'error';
            messageTip(this.message_tips,this.message_type);
          }
        });
      }
    },
    autoChangePolicy(value){
      for(let i=0;i<this.policys.length;i++){
        if(this.policys[i].id==value){
          this.autoexpandtemp.policy_name=this.policys[i].label;
          this.autoexpandtemp.list.policy=this.policys[i].id;
        }
      }
      const tempData = {};
      tempData.job_id = '';
      tempData.version=version_arr[0].ver;
      tempData.user_name=sessionStorage.getItem('login_username');
      tempData.job_type='get_expand_table_list';
      tempData.timestamp=timestamp_arr[0].time+'';
      const paras={}
      paras.cluster_id = this.autoexpandtemp.list.cluster_id;
      paras.shard_id=this.autoexpandtemp.auto_shard_id;
      paras.policy=value;
      tempData.paras=paras;
      if(this.autoexpandtemp.auto_shard_id){
        this.autoexpandtemp.tables=[];
        getExpandTableList(tempData).then((ress) => {
          if(ress.error_code=='0'){
            if(ress.attachment.table_list!==''){
              const ss=ress.attachment.table_list.split(',');
              let table=[];
              for(let a=0;a<ss.length;a++){
                let arr=ss[a].split('.');
                let table_arr={TABLE_SCHEMA:arr[1],TABLE_NAME:arr[2]}
                table.push(table_arr);
              }
              this.autoexpandtemp.tables = table;
              if(this.autoexpandtemp.tables){
                this.$nextTick(()=>{
                  this.autoexpandtemp.tables.forEach((row) => {
                    this.$refs.dataTable.toggleAllSelection(row, true)
                  })
                })
              }
            }else{
              this.autoexpandtemp.tables = false;
            }
          }else{
            this.message_tips = ress.error_info;
            this.message_type = 'error';
            messageTip(this.message_tips,this.message_type);
          }
        });
      }
    },
    ChangeShardName(value){
      for(let i=0;i<this.shardsList.length;i++){
        if(this.shardsList[i].id==value){
          this.expandtemp.dst_shard_name=this.shardsList[i].name;
        }
      }
    },
    selectionChangeHandle(val){
      this.expandtemp.shard_name=this.expandtemp.list.shard_name;
      // if(this.expandtemp.table_list.length>0){
      //   //return 1;
      //   this.message_tips = '只允许勾选同一个shard的表数据';
      //   this.message_type = 'error';
      //   messageTip(this.message_tips,this.message_type);
      // }
      this.expandtemp.table_list=val;
      // console.log(this.expandtemp.table_list);
      if(this.expandtemp.shard_name){
        //获取shards名称
        let temp={cluster_id:this.expandtemp.list.cluster_id,shard_id:this.expandtemp.list.shard_id};
        getOtherShards(temp).then((res) => {
          this.shardsList = res.list;
        });
        this.$nextTick(() => {
          if(this.$refs["dataForm"]){
            this.$refs["dataForm"].clearValidate();
          }
        });
      }
    },
    handleClick(tab) {
      //console.log(tab);
      if(tab.$attrs.value){
        let ids=tab.$attrs.value.split('_');
        this.expandtemp.list.cluster_id=ids[1];
        this.expandtemp.list.shard_id=ids[0];
        this.expandtemp.list.shard_name=tab.name;
        //this.expandtemp.shard_name=tab.name;
        this.expandtemp.dst_shard_id='';
        //console.log(this.expandtemp.list);
        //获取shard下的table
        this.shardTable=[];
        let table={cluster_id:ids[1],id:ids[0]}
        getShardTable(table).then((res) => {
         if(res.code==200){
            this.shardTable = res.list;
          }else{
            this.message_tips = res.message;
            this.message_type = 'error';
            messageTip(this.message_tips,this.message_type);
          }
        });
        if(this.expandtemp.shard_name){
        //获取shards名称
        this.shardsList=[];
        let temp={cluster_id:this.expandtemp.list.cluster_id,shard_id:this.expandtemp.list.shard_id};
        getOtherShards(temp).then((res) => {
          this.shardsList = res.list;
        });
        this.$nextTick(() => {
          if(this.$refs["dataForm"]){
            this.$refs["dataForm"].clearValidate();
          }
        });
      }
      }
      // this.activeName = tab.name;
    },
    //清除定时器
    beforeDestory(){
      clearInterval(this.timer)
      this.dialogStatusShowVisible=false;
    },
    thisDetail(id){
      const temp ={id:id};
      getShardsJobLog(temp).then((response) => {
        this.dialogStatus = 'shard_'+id+'详细信息';
        this.dialogShardInfo=true;
        this.shardInfo=response.shard_nodes

      });
    },
    handleStatus(){
      this.dialogStatusShowVisible=true;
    },
    ChangeSaler(value){
      //console.log(value);
      if(value=='add_shards'){
        //获取存储机器
        getStroMachine().then((res) => {
          this.strogemachines=[];
          this.strogemachines = res.list;
        });
        this.nodetemp.nodes='3';
        this.nodetemp.shards='1'
        
      }else if(value=='add_nodes'){
        //获取存储机器
         getStroMachine().then((res) => {
          this.strogemachines=[];
          this.strogemachines = res.list;
        });
         this.nodetemp.nodes='1'
      }else if(value=='add_comps'){
        //获取计算机器
        getCompMachine().then((res) => {
          this.strogemachines=[];
          this.strogemachines = res.list;
        });
        this.nodetemp.nodes='1'
      }
    },
    toggle(){
       this.isShow = !this.isShow //取反
    },
    handleFilter() {
      this.listQuery.pageNo = 1
      this.listQuery.user_name=sessionStorage.getItem('login_username')
      this.getList()
    },
    handleClear(){
      this.listQuery.name = ''
      this.listQuery.pageNo = 1
      this.listQuery.user_name = sessionStorage.getItem('login_username');
      this.getList()
    },
    getList() {
        this.listLoading = true
        let queryParam = Object.assign({}, this.listQuery)
        queryParam.effectCluster= sessionStorage.getItem('affected_clusters');
        queryParam.apply_all_cluster= sessionStorage.getItem('apply_all_cluster');
        //模糊搜索
        getClusterList(queryParam).then(response => {
          this.list = response.list;
          this.total = response.total;
          setTimeout(() => {
            this.listLoading = false
          }, 0.5 * 1000)
        });
    },
    resetTemp() {
      this.temp = {
        ha_mode:sessionStorage.getItem('work_mode')=='enterprise'?'rbr':'mgr',
        shards_count:'1',
        snode_count: '3',
        comp_count:'1',
        buffer_pool:'1024',
        max_connections:'6',
        per_computing_node_cpu_cores:'8',
        per_computing_node_max_mem_size:'',
        per_storage_node_cpu_cores:'8',
        per_storage_node_innodb_buffer_pool_size:'',
        per_storage_node_rocksdb_buffer_pool_size:'',
        per_storage_node_initial_storage_size:'20',
        per_storage_node_max_storage_size:'20',
        nick_name:'',
        machinelist:[],
        comp_machinelist:[],
        node_type:'',
        shard_name:'',
        shards:'',
        computer_user:'',
        computer_password:'',
        fullsync_level:'1'
      };
    },
    autoExpandTemp(){
      this.autoexpandtemp={
        shard_name:'',
        list:{cluster_id:'',shard_id:'',shard_name:''},
        table_list:[],
        policy:'',
        auto_shard_id:'',
        tables:[],
        policy_name:'',
        dst_shard_id:'',
        dst_shard_name:'',
        dsc_flag:false,
        if_save:'0'
      }
    },
    resetNodeTemp(){
      this.nodetemp = {
        machinelist:[],
        node_type:'',
        shard_name:'',
        shards:'',
        name:'',
        nick_name:'',
        nodes:''
      };
    },
    handleCreate() {
      this.resetTemp();
      this.dialogStatus = "create";
      getStroMachine().then((res) => {
        this.machines = res.list;
        this.minMachine=0;
        this.machineTotal=res.total;
        let if_machines=false;
        let if_comp_machine=false;
        if(this.machines==null||this.machines.length==0||this.machines==false){
          messageTip('请先添加计算机再新增集群!','error');
        }else{
          if_machines=true;
          getCompMachine().then((res) => {
            this.comp_machines = res.list;
            this.comp_minMachine=0;
            this.comp_machineTotal=res.total;
            if(this.comp_machines==null||this.comp_machines.length==0||this.comp_machines==false){
            messageTip('请先添加计算机再新增集群!','error');
            }else{
              if_comp_machine=true;
              if(if_machines===true&&if_comp_machine===true){
                this.dialogFormVisible = true;
                this.dialogDetail = false;
              }else{
                this.dialogFormVisible = false;
                this.dialogDetail = false;
              }
            }
          });
        }
      });
     
      // const temp={};
      // temp.job_type='get_meta_mode';
      // temp.version=version_arr[0].ver;
      // temp.job_id='';
      // temp.timestamp=timestamp_arr[0].time+'';
      // temp.paras={}
      // getMetaMode(temp).then((res) => {
      //   if(res){
      //     const ha_mode=[{"id":res.attachment.mode,"label":res.attachment.mode}];
      //     this.hamodeData=ha_mode;
      //   };
      // });
      // this.$nextTick(() => {
      //   this.$refs.dataForm.clearValidate();
      // });
    },
    createData() {
      this.$refs["dataForm"].validate((valid) => {
         if (valid) {
          const tempData = Object.assign({}, this.temp);
          //处理储存机器的格式
          let machinelist=[];
          tempData.machinelist.forEach(item => {
            machinelist.push(item)
          })
          //处理计算机器的格式
          let comp_machinelist=[];
          tempData.comp_machinelist.forEach(item => {
            comp_machinelist.push(item)
          })
          const clusterData={};
          const paras={};
          clusterData.user_name=sessionStorage.getItem('login_username');
          clusterData.job_id='';
          clusterData.version=version_arr[0].ver;
          clusterData.timestamp=timestamp_arr[0].time+'';
          clusterData.job_type='create_cluster';
          //必填项
          paras.comps=tempData.comp_count+'';
          paras.shards=tempData.shards_count+'';
          paras.nodes=tempData.snode_count;
          paras.innodb_size=tempData.buffer_pool;
          paras.ha_mode=tempData.ha_mode;
          paras.nick_name=tempData.nick_name;
          paras.fullsync_level=tempData.fullsync_level;
          //可选项
          if(tempData.per_storage_node_max_storage_size){
             paras.max_storage_size=tempData.per_storage_node_max_storage_size;
          }
          if(tempData.max_connections){
            paras.max_connections=tempData.max_connections;
          }
          if(tempData.per_computing_node_cpu_cores){
            paras.cpu_cores=tempData.per_computing_node_cpu_cores;
          }
          if(machinelist.length>0){
            paras.storage_iplists=machinelist;
          }
           if(comp_machinelist.length>0){
             paras.computer_iplists=comp_machinelist;
          }
          if(tempData.computer_user){
            paras.computer_user=tempData.computer_user;
          }
          if(tempData.computer_password){
             paras.computer_password=tempData.computer_password;
          }
          //0和没有该字段为正常模式，1为小内存模式
          paras.dbcfg='0';
          clusterData.paras=paras;
          //console.log(clusterData);return;
          //发送接口
          createCluster(clusterData).then(response=>{
            let res = response;
            if(res.status=='accept'){
              this.dialogFormVisible = false;
              this.dialogStatusShowVisible=true;
              //把之前的数据清空
              this.computer=[];this.shard=[];this.computer_state='';this.storage_state='';this.computer_title='';this.computer_icon='';this.shard_icon='';this.shard_title='';this.comp_active=0;this.shard_active=0;this.strogemachines=[];this.init_title='';this.init_active=0;this.finish_title='';this.finish_icon='';this.finish_description='';this.computer_description='';this.shard_description='';this.job_id='';this.timer=null;
              let info='新增'
              this.init_show=true;this.finish_show=false;this.finish_state='';
              //调获取状态接口
              let i=0;
              this.getFStatus(this.timer,res.job_id,i++,info)
              this.timer = setInterval(() => {
                this.getFStatus(this.timer,res.job_id,i++,info)
              }, 5000)

            }else if(res.status=='ongoing'){
              this.message_tips = '系统正在操作中，请等待一会！';
              this.message_type = 'error';
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
    handleDetail(row){
      this.dialogStatus = "detail"
      this.dialogFormVisible = true
      this.temp = Object.assign({}, row);
      this.dialogDetail = true
    },
    handleUpdate(row) {
      this.resetNodeTemp();
      this.temp = Object.assign({}, row); 
      this.nodetemp.nick_name=this.temp.nick_name;
      this.nodetemp.name=this.temp.name;
      this.nodetemp.id=this.temp.id;
      this.dialogStatus = "update";
      this.dialogNodeVisible = true;
      this.dialogDetail = false;
      //获取计算机名称
      // getAllMachine().then((res) => {
      // this.machines = res.list;
      //   this.minMachine=0;
      //   this.machineTotal=res.total;
      //  });
      //获取分片名称
       getShards(row.id).then((response) => {
          let res = response;
          if(res.code==200){
          this.shardList=res.list;
          }
        });
      this.$nextTick(() => {
        this.$refs["nodeForm"].clearValidate();
      });
      
    },
    updateData() {
      this.$refs["nodeForm"].validate((valid) => {
        if (valid) {
          const tempData = Object.assign({}, this.nodetemp);
          //处理machinelist的格式
          let machinelist=[];
          tempData.machinelist.forEach(item => {
            //const newArr={"hostaddr":item};
            machinelist.push(item)
          })
          tempData.job_id = '';
          tempData.version=version_arr[0].ver;
          tempData.user_name=sessionStorage.getItem('login_username');
          tempData.job_type=tempData.node_type;
          tempData.timestamp=timestamp_arr[0].time+'';
          const paras={}
          paras.cluster_id = tempData.id;
          paras.nick_name=tempData.nick_name;
          //删除参数
          this.$delete(tempData,'node_type');
          this.$delete(tempData,'name');
          this.$delete(tempData,'nick_name');
          this.$delete(tempData,'machinelist');
          // this.$delete(tempData,'shardtotal');
          if(tempData.job_type=='add_shards'){
            //新增shard
            paras.shards = tempData.shards;
            paras.nodes = tempData.nodes;
            if(machinelist.length>0){
              paras.storage_iplists=machinelist;
            }
            this.$delete(tempData,'shard_name');
            this.$delete(tempData,'shards');
            this.$delete(tempData,'nodes');
            this.$delete(tempData,'id');
            tempData.paras=paras;
            //console.log(tempData);return;
            addShards(tempData).then((response) => {
            let res = response;
            if(res.status=='accept'){
              // this.dialogNodeVisible = false;
              // this.dialogStatusVisible=true;
              // this.activities=[];
              // const newArr={
              //   content:'正在新增shard...',
              //   timestamp: getNowDate(),
              //   size: 'large',
              //   type: 'primary',
              //   icon: 'el-icon-more'
              // };
              // this.activities.push(newArr)
              //this.message_tips = '正在新增shard...';
              //this.message_type = 'success';
              //调获取状态接口
              this.dialogNodeVisible = false;
              this.dialogStatusShowVisible=true;
              //把之前的数据清空
              this.computer=[];this.shard=[];this.computer_state='';this.storage_state='';this.computer_title='';this.computer_icon='';this.shard_icon='';this.shard_title='';this.comp_active=0;this.shard_active=0;this.strogemachines=[];this.init_title='';this.init_active=0;this.finish_title='';this.finish_icon='';this.finish_description='';this.computer_description='';this.shard_description='';this.job_id='';this.timer=null;
              this.init_show=true;this.finish_show=false;this.finish_state='';
              const info='添加shard'
              let i=0;
              this.getFStatus(this.timer,res.job_id,i++,info)
              this.timer = setInterval(() => {
                this.getFStatus(this.timer,res.job_id,i++,info)
              }, 5000)
            }else if(res.status=='ongoing'){
              this.message_tips = '系统正在操作中，请等待一会！';
              this.message_type = 'error';
              messageTip(this.message_tips,this.message_type);
            }else{
              this.message_tips = res.error_info;
              this.message_type = 'error';
              messageTip(this.message_tips,this.message_type);
            }
          });
          }else if(tempData.job_type=='add_comps'){
            paras.comps=tempData.nodes;
            if(machinelist.length>0){
              paras.computer_iplists=machinelist;
            }
            tempData.paras=paras;
            this.$delete(tempData,'shards');
            this.$delete(tempData,'shard_name');
            this.$delete(tempData,'nodes');
            this.$delete(tempData,'id');
            //console.log(tempData);return;
            //新增计算节点
            addComps(tempData).then((response) => {
            let res = response;
            if(res.status=='accept'){
            //  this.dialogNodeVisible = false;
            //  this.dialogStatusVisible=true;
            //  this.activities=[];
            //  const newArr={
            //     content:'正在新增计算节点...',
            //     timestamp: getNowDate(),
            //     size: 'large',
            //     type: 'primary',
            //     icon: 'el-icon-more'
            //  };
            //  this.activities.push(newArr)
              //this.message_tips = '正在新增计算节点...';
              //this.message_type = 'success';
              //调获取状态接口
              this.dialogNodeVisible = false;
              this.dialogStatusShowVisible=true;
              //把之前的数据清空
              this.computer=[];this.shard=[];this.computer_state='';this.storage_state='';this.computer_title='';this.computer_icon='';this.shard_icon='';this.shard_title='';this.comp_active=0;this.shard_active=0;this.strogemachines=[];this.init_title='';this.init_active=0;this.finish_title='';this.finish_icon='';this.finish_description='';this.computer_description='';this.shard_description='';this.job_id='';this.timer=null;
              this.init_show=true;this.finish_show=false;this.finish_state='';
              const info='添加计算节点'
              let i=0;
              this.getFStatus(this.timer,res.job_id,i++,info)
              this.timer = setInterval(() => {
                this.getFStatus(this.timer,res.job_id,i++,info)
              }, 5000)
            }
            else if(res.status=='ongoing'){
              this.message_tips = '系统正在操作中，请等待一会！';
              this.message_type = 'error';
               messageTip(this.message_tips,this.message_type);
            }else{
              this.message_tips = res.error_info;
              this.message_type = 'error';
               messageTip(this.message_tips,this.message_type);
            }
          });
          }else if(tempData.job_type=='add_nodes'){
             //console.log(tempData);return;
             paras.nodes=tempData.nodes;
             paras.shard_id=tempData.shard_name;
             if(machinelist.length>0){
              paras.storage_iplists=machinelist;
            }
             tempData.paras=paras;
             this.$delete(tempData,'shards');
             this.$delete(tempData,'shard_name');
             this.$delete(tempData,'nodes');
             this.$delete(tempData,'id');
             //console.log(tempData);return;
            //新增存储节点
            addNodes(tempData).then((response) => {
            let res = response;
            if(res.status=='accept'){
              // this.dialogNodeVisible = false;
              // this.dialogStatusVisible=true;
              // this.activities=[];
              // const newArr={
              //     content:'正在新增存储节点...',
              //     timestamp: getNowDate(),
              //     size: 'large',
              //     type: 'primary',
              //     icon: 'el-icon-more'
              // };
              // this.activities.push(newArr)
              //this.message_tips =  '正在新增存储节点...';
              //this.message_type = 'success';
              this.dialogNodeVisible = false;
              this.dialogStatusShowVisible=true;
              //把之前的数据清空
              this.computer=[];this.shard=[];this.computer_state='';this.storage_state='';this.computer_title='';this.computer_icon='';this.shard_icon='';this.shard_title='';this.comp_active=0;this.shard_active=0;this.strogemachines=[];this.init_title='';this.init_active=0;this.finish_title='';this.finish_icon='';this.finish_description='';this.computer_description='';this.shard_description='';this.job_id='';this.timer=null;
              this.init_show=true;this.finish_show=false;this.finish_state='';
              const info='添加存储节点'
              //调获取状态接口
              let i=0;
               this.getFStatus(this.timer,res.job_id,i++,info)
              this.timer= setInterval(() => {
                this.getFStatus(this.timer,res.job_id,i++,info)
              }, 5000)
            }else if(res.status=='ongoing'){
              this.message_tips = '系统正在操作中，请等待一会！';
              this.message_type = 'error';
               messageTip(this.message_tips,this.message_type);
            }else{
              this.message_tips = res.error_info;
              this.message_type = 'error';
               messageTip(this.message_tips,this.message_type);
            }
          });
          }
          
        }
      });
    },
    handleDelete(row) {
      const code=createCode();
      let string="此操作将永久删除该数据, 是否继续?code="+code;
      gotoCofirm(string).then( (res) =>{
        //先执行删权限
        if(!res.value){
          this.message_tips = 'code不能为空！';
          this.message_type = 'error';
          messageTip(this.message_tips,this.message_type);
        }else if(res.value==code){
          const apply_all_cluster=sessionStorage.getItem('apply_all_cluster');
          if(apply_all_cluster==2){
            const arrs= {};
            arrs.effectCluster=sessionStorage.getItem('affected_clusters');
            arrs.cluster_name=row.name;
            arrs.username=sessionStorage.getItem('login_username');
            arrs.type='del';
            uAssign(arrs).then((responses) => {
              let res_update = responses;
              if(res_update.code==200){
                this.dialogFormVisible = false;
                sessionStorage.setItem('affected_clusters',res_update.effectCluster);
              }
            });
          }
          //调接口删集群
          const tempData={};
          tempData.user_name=sessionStorage.getItem('login_username');
          tempData.job_id = '';
          tempData.version=version_arr[0].ver;
          tempData.job_type='delete_cluster';
          tempData.timestamp=timestamp_arr[0].time+'';
          tempData.paras={'cluster_id':row.id,'nick_name':row.nick_name}
          delCluster(tempData).then((response)=>{
            let res = response;
            if(res.status=='accept'){
              // this.dialogFormVisible = false;
              // this.dialogStatusVisible=true;
              // this.activities=[];
              // const newArr={
              //   content:'正在删除集群...',
              //   timestamp: getNowDate(),
              //   size: 'large',
              //   type: 'primary',
              //   icon: 'el-icon-more'
              // };
              // this.activities.push(newArr);
              //this.message_tips = '正在删除...';
              //this.message_type = 'success';

              this.dialogFormVisible = false;
              this.dialogStatusShowVisible=true;
              //把之前的数据清空
              this.computer=[];this.shard=[];this.computer_state='';this.storage_state='';this.computer_title='';this.computer_icon='';this.shard_icon='';this.shard_title='';this.comp_active=0;this.shard_active=0;this.strogemachines=[];this.init_title='';this.init_active=0;this.finish_title='';this.finish_icon='';this.finish_description='';this.computer_description='';this.shard_description='';this.job_id='';this.timer=null;this.shard_show=true;this.finish_show=false;
              this.computer_show=true;this.finish_state='';
              let info='删除' ;
              this.init_show=true;
              //调获取状态接口
              let i=0;
              this.getFStatus(this.timer,res.job_id,i++,info)
              this.timer = setInterval(() => {
                this.getFStatus(this.timer,res.job_id,i++,info)
              }, 5000)
            }
            else if(res.status=='ongoing'){
              this.message_tips = '系统正在操作中，请等待一会！';
              this.message_type = 'error';
              messageTip(this.message_tips,this.message_type);
            }else{
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
       
    },
    handleBackUp(row) {
      //先验证是否备份存储介质
      // const arrs= {};
      // arrs.version=version_arr[0].ver;
      // arrs.job_id='';
      // arrs.job_type='get_backup_storage';
      // arrs.timestamp=timestamp_arr[0].time+'';
      // arrs.paras={}
      // getBackUpStorage(arrs).then((res) => {
      //   if(res.attachment.list_backup_storage!==null){
      //     handleCofirm("确定要备份"+row.nick_name+"这个集群么?").then( () =>{
      //     const tempData = Object.assign({}, row);
      //     const backupData={};
      //     backupData.user_name=sessionStorage.getItem('login_username');
      //     backupData.job_id = '';
      //     backupData.paras = {'backup_cluster_name':tempData.name,'nick_name':tempData.nick_name};
      //     backupData.version=version_arr[0].ver;
      //     backupData.timestamp=timestamp_arr[0].time+'';
      //     backupData.job_type='backup_cluster';
      //     backeUpCluster(backupData).then((response)=>{
      //       let res = response;
      //       if(res.status=='accept'){
      //         this.dialogFormVisible = false;
      //         this.dialogStatusVisible=true;
      //         this.activities=[];
      //         const newArr={
      //           content:'正在备份集群...',
      //           timestamp: getNowDate(),
      //           size: 'large',
      //           type: 'primary',
      //           icon: 'el-icon-more'
      //         };
      //         this.activities.push(newArr);
      //         //this.message_tips = '正在备份...';
      //         //this.message_type = 'success';
      //         //调获取状态接口
      //         let i=0;
      //         this.timer = setInterval(() => {
      //           this.getStatus(this.timer,res.job_id,i++)
      //         }, 1000)
      //       }
      //       else if(res.status=='ongoing'){
      //           this.message_tips = '系统正在操作中，请等待一会！';
      //           this.message_type = 'error';
      //           messageTip(this.message_tips,this.message_type);
      //         }else{
      //           this.message_tips = res.error_info;
      //           this.message_type = 'error';
      //           messageTip(this.message_tips,this.message_type);
      //         }
      //     })
      //   }).catch(() => {
      //       messageTip('已取消备份','info');
      //   }); 
      //   }else{  
      //     messageTip('请先添加备份存储目标!','error');
      //   }
      // });
        handleCofirm("确定要对集群"+row.nick_name+"进行全量备份么?").then( () =>{
          const tempData = Object.assign({}, row);
          const backupData={};
          backupData.user_name=sessionStorage.getItem('login_username');
          backupData.job_id = '';
          backupData.paras = {'cluster_id':tempData.id,'nick_name':tempData.nick_name};
          backupData.version=version_arr[0].ver;
          backupData.timestamp=timestamp_arr[0].time+'';
          backupData.job_type='manual_backup_cluster';
          backeUpCluster(backupData).then((response)=>{
            let res = response;
            if(res.status=='accept'){
              this.dialogFormVisible = false;
              // this.dialogStatusVisible=true;
              // this.activities=[];
              // const newArr={
              //   content:'正在备份集群...',
              //   timestamp: getNowDate(),
              //   size: 'large',
              //   type: 'primary',
              //   icon: 'el-icon-more'
              // };
              // this.activities.push(newArr);
              this.message_tips = '全量备份下发成功';
              this.message_type = 'success';
              messageTip(this.message_tips,this.message_type);
              //调获取状态接口
              // let i=0;
              // this.timer = setInterval(() => {
              //   this.getStatus(this.timer,res.job_id,i++)
              // }, 1000)
            }
            else if(res.status=='ongoing'){
                this.message_tips = '系统正在操作中，请等待一会！';
                this.message_type = 'error';
                messageTip(this.message_tips,this.message_type);
              }else{
                this.message_tips = res.error_info;
                this.message_type = 'error';
                messageTip(this.message_tips,this.message_type);
              }
          })
        }).catch(() => {
            messageTip('已取消全量备份','info');
        }); 
      
     
    },
    handleRestore(row) {
      this.restoretempDate = Object.assign({}, row); 
      this.dialogStatus = "restore";
      this.dialogFormVisible = false;
      this.dialogDetail = false;
      //判断是否存在备份文件
       ifBackUp(row.id).then((response) => {
        let res = response;
        if(res.code==200){
          this.dialogRestoreVisible=true;
          this.restoretemp.end_ts=response.list[0].end_ts;
          this.restoretemp.old_cluster_name=row.nick_name;
          this.restoretemp.backup_cluster_name = row.name;
          this.restoretemp.now=getNowDate();
          getAllMachine().then((res) => {
            this.machines = res.list;
            this.minMachine=0;
            this.machineTotal=res.total;
          });
        }
        else{
          this.message_tips = res.message;
          this.message_type = 'error';
          messageTip(this.message_tips,this.message_type);
        }
      });
      this.$nextTick(() => {
        if(this.$refs["dataForm"]){
          this.$refs["dataForm"].clearValidate();
        }
      });
    },
    restoreData() {
      this.$refs["restoreForm"].validate((valid) => {
        if (valid) {
      const tempData = Object.assign({}, this.restoretemp);
      //处理machinelist的格式
      let machinelist=[];
      tempData.machinelist.forEach(item => {
        const newArr={"hostaddr":item};
        machinelist.push(newArr)
      })
      const restoreData={};
      restoreData.user_name=sessionStorage.getItem('login_username');
      restoreData.job_id = '';
      restoreData.version=version_arr[0].ver;
      restoreData.job_type='restore_new_cluster';
      restoreData.timestamp=timestamp_arr[0].time+'';
      const paras={}
      paras.nick_name = tempData.nick_name;
      paras.backup_cluster_name = tempData.backup_cluster_name;
      paras.timestamp=tempData.now;
      paras.machinelist=machinelist;
      restoreData.paras=paras;
      restoreCluster(restoreData).then((response) => {
        let res = response;
        if(res.status=='accept'){
          this.dialogRestoreVisible = false;
          this.dialogStatusVisible=true;
          this.activities=[];
          const newArr={
            content:'正在恢复集群...',
            timestamp: getNowDate(),
            size: 'large',
            type: 'primary',
            icon: 'el-icon-more'
          };
          this.activities.push(newArr);
          //this.message_tips = '正在恢复...';
          //this.message_type = 'success';
          //调获取状态接口
          let i=0;
          this.timer = setInterval(() => {
            this.getStatus(this.timer,res.job_id,i++)
          }, 1000)
        }
        else if(res.status=='ongoing'){
          this.message_tips = '系统正在操作中，请等待一会！';
          this.message_type = 'error';
          messageTip(this.message_tips,this.message_type);
        }else{
          this.message_tips = res.error_info;
          this.message_type = 'error';
           messageTip(this.message_tips,this.message_type);
        }
      });
        }
      })
    },
    handleRetreated(row) {
      this.dialogStatus = "retreated";
      this.dialogFormVisible = false;
      this.dialogDetail = false;
      this.dialogRetreatedVisible=true;
      //this.retreatedtemp.nick_name=row.nick_name+'('+row.id+')';
      this.retreatedtemp.old_cluster_id='';
      this.retreatedtemp.retreated_time='';
      this.retreatedtemp.nick_name=row.name;
      this.retreatedtemp.old_cluster_id=this.retreatedtemp.old_cluster_id;
      this.retreatedtemp.new_cluster_id=row.id;
      let temp={cluster_id:row.id}
      //获取原集群名称
      getOldCluster(temp).then((res) => {
        this.oldClusterList = res.list;
      });
      this.$nextTick(() => {
        if(this.$refs["retreatedForm"]){
          this.$refs["retreatedForm"].clearValidate();
        }
      });
    },
    retreatedData() {
      this.$refs["retreatedForm"].validate((valid) => {
      if (valid) {
      const tempData = Object.assign({}, this.retreatedtemp);
      const restoreData={};
      restoreData.user_name=sessionStorage.getItem('login_username');
      restoreData.job_id = '';
      restoreData.version=version_arr[0].ver;
      restoreData.job_type='cluster_restore';
      restoreData.timestamp=timestamp_arr[0].time+'';
      const paras={}
      paras.src_cluster_id = tempData.old_cluster_id;
      paras.dst_cluster_id = tempData.new_cluster_id;
      paras.restore_time=tempData.retreated_time;
      // paras.machinelist=machinelist;
      restoreData.paras=paras;
      //console.log(restoreData);return;
      handleCofirm(tempData.nick_name+"集群的数据将被覆盖，确定要进行回档操作么?").then( () =>{
      restoreCluster(restoreData).then((response) => {
        let res = response;
        if(res.status=='accept'){
          this.dialogRetreatedVisible = false;
          this.dialogStatusVisible=true;
          this.activities=[];
          const newArr={
            content:'正在回档集群...',
            timestamp: getNowDate(),
            size: 'large',
            type: 'primary',
            icon: 'el-icon-more'
          };
          this.activities.push(newArr);
          //this.message_tips = '正在恢复...';
          //this.message_type = 'success';
          //调获取状态接口
          let i=0;this.timer=null;
          this.getStatus(this.timer,res.job_id,i++)
          this.timer = setInterval(() => {
            this.getStatus(this.timer,res.job_id,i++)
          }, 5000)
        }
        else if(res.status=='ongoing'){
          this.message_tips = '系统正在操作中，请等待一会！';
          this.message_type = 'error';
          messageTip(this.message_tips,this.message_type);
        }else{
          this.message_tips = res.error_info;
          this.message_type = 'error';
           messageTip(this.message_tips,this.message_type);
        }
      });
      }).catch(() => {
            messageTip('已取消回档','info');
        }); 
      }
      })
    },
    handleSwitchOver(row){
      this.$router.push({
        path: '/cluster/switchover',
        query: {
          id: row.id
        }
        })
    },
    handleSetUp(row){
      //this.editableTabs=this.comment;
       if(this.comment.length==0){
        this.editableTabs=this.comment;
       }
      let newTabName = row.id+ '';
      let tabs = this.editableTabs;
      if(tabs.length>0){
        let exist = false;
        tabs.forEach((tab) => {
          if (tab.name === newTabName) {
            exist = true;
          }
        });
        if(!exist){
          this.editableTabs.push({
            title: row.id+'集群设置',
            name: newTabName
          });
        }
      }else{
        this.editableTabs.push({
          title: row.id+'集群设置',
          name: newTabName
        });
      }
      const temp={
        list:row,
        activeName:'four',
        tab:this.editableTabs
      }
      this.$emit('updateActiveName', temp)
    },
    handleExpand(row) {
      this.dialogStatus = "expand";
      this.dialogFormVisible = false;
      this.dialogDetail = false;
      this.expandtemp.list.cluster_id=row.id;
      //获取该集群下有多少个shard
      let temp={id:row.id};
      getShardsName(temp).then((res) => {
        if(res.list.length<2){
          this.message_tips = '集群扩容必须在同一集群不同shard下操作！';
          this.message_type = 'error';
          messageTip(this.message_tips,this.message_type);
        }else{
          this.shardNameList = res.list;
          this.activeName=res.list[0].name;
          //this.expandtemp.list.cluster_id=res.list[0].cluster_id;
          this.expandtemp.list.shard_id=res.list[0].id;
          this.expandtemp.list.shard_name=res.list[0].name;
          //this.expandtemp.shard_name=res.list[0].name;
          //获取shard下的table
          this.shardTable=[]
          let table={cluster_id:res.list[0].cluster_id,id:res.list[0].id}
          getShardTable(table).then((ress) => {
            if(ress.code==200){
              this.shardTable = ress.list;
              this.dialogExpandVisible=true;
            }else{
              this.message_tips = ress.message;
              this.message_type = 'error';
              messageTip(this.message_tips,this.message_type);
            }
          });
        }
      });
    },
    showExpandInfo(row){
      console.log(row);
      if(this.expandtemp.title=='自动扩容'){
        console.log(11);
        this.autoExpandTemp();
        this.dialogExpandVisible = false;
        this.dialogDetail = false;
        this.dialogStatus = "outoExpand";
        this.dialogExpandInfoVisible=false;
        this.outoExpandInfoVisible=true;
        this.autoexpandtemp.list.cluster_id=row.list.cluster_id;
        //查看该集群下有多少个shard
        let temp={id:row.list.cluster_id};
        getShardsName(temp).then((res) => {
          if(res.list.length<2){
            this.message_tips = '集群扩容必须在同一集群不同shard下操作！';
            this.message_type = 'error';
            messageTip(this.message_tips,this.message_type);
          }else{
            this.srcShardsList = res.list;
          }
        });
      }else{
        this.$refs["expandForm"].validate((valid) => {
        if (valid) {
          this.dialogExpandVisible = false;
          this.dialogDetail = false;
          this.dialogStatus = "expandInfo";
          this.dialogExpandInfoVisible=true;
          //参数赋值
          this.expandInfoTemp.shard_name=row.shard_name;
          this.expandInfoTemp.list=row.list;
          this.expandInfoTemp.table_list=row.table_list;
          this.expandInfoTemp.dst_shard_id=row.dst_shard_id;
          this.expandInfoTemp.dst_shard_name=row.dst_shard_name;
          this.expandInfoTemp.if_save=row.if_save;
        }
      });
      }
    },
    expandClose(){
      this.dialogExpandVisible = true;
      this.dialogExpandInfoVisible=false;
    },
    autoExpandInfo(row){
      this.$refs["autoExpandForm"].validate((valid) => {
        if (valid) {
          this.dialogStatus = "expandInfo";
          this.outoExpandInfoVisible = false;
          this.dialogExpandInfoVisible=true;
          //参数赋值
          this.expandInfoTemp.shard_name=row.shard_name;
          this.expandInfoTemp.table_list=row.table_list;
          this.expandInfoTemp.list=row.list;
          this.expandInfoTemp.policy=row.policy;
          this.expandInfoTemp.policy_name=row.policy_name;
          this.expandInfoTemp.dst_shard_id=row.dst_shard_id;
          this.expandInfoTemp.dst_shard_name=row.dst_shard_name;
          this.expandInfoTemp.if_save=row.if_save;
        }
      });
    },
    expandData(row) {
      let table_list=[];
      for(let i=0;i<row.table_list.length;i++){
        let a=(row.table_list[i].TABLE_SCHEMA).replace('_$$_','.')+'.'+row.table_list[i].TABLE_NAME;
        table_list.push(a);
      }
      const tempData = {};
      tempData.user_name=sessionStorage.getItem('login_username');
      tempData.job_id = '';
      tempData.version=version_arr[0].ver;
      tempData.job_type='expand_cluster';
      tempData.timestamp=timestamp_arr[0].time+'';
      const paras={}
      paras.cluster_id = row.list.cluster_id;
      paras.src_shard_id=row.list.shard_id;
      paras.table_list=table_list;
      paras.dst_shard_id = row.dst_shard_id;
      paras.drop_old_table = row.if_save;
      if(row.policy){
        paras.policy = row.policy;
      }
      tempData.paras=paras;
      //console.log(tempData);return;
      expandCluster(tempData).then((response) => {
        let res = response;
        if(res.status=='accept'){
          //调获取状态接口
          this.dialogExpondInfo=true;
          this.dialogExpandVisible=false;
          this.dialogExpandInfoVisible=false;
          this.computer=[];this.shard=[];this.computer_state='';this.storage_state='';this.computer_title='';this.computer_icon='';this.shard_icon='';this.shard_title='';this.comp_active=0;this.shard_active=0;this.strogemachines=[];this.init_title='';this.init_active=0;this.finish_title='';this.finish_icon='';this.finish_description='';this.computer_description='';this.shard_description='';this.job_id='';this.timer=null;
          this.expondSatus=false; this.expondResult=false;this.expondInit=true;this.expand_init='';this.expand_end='';this.expondInfo=[];this.finish_show=false;this.finish_state='';
          let info='扩容'
          let i=0;
          this.getFStatus(this.timer,res.job_id,i++,info)
          this.timer = setInterval(() => {
            this.getFStatus(this.timer,res.job_id,i++,info)
          }, 2000)
        }
        else if(res.status=='ongoing'){
          this.message_tips = '系统正在操作中，请等待一会！';
          this.message_type = 'error';
          messageTip(this.message_tips,this.message_type);
        }else{
          this.message_tips = res.error_info;
          this.message_type = 'error';
           messageTip(this.message_tips,this.message_type);
        }
      });
    },
    getFStatus (timer,data,i,info) {
      setTimeout(()=>{
        const postarr={};
        postarr.job_type='get_status';
        postarr.version=version_arr[0].ver;
        postarr.job_id=data;
        postarr.timestamp=timestamp_arr[0].time+'';
        postarr.paras={};
        this.job_id='任务ID:'+data;
        getEvStatus(postarr).then((ress) => {
          //新增集群、删除集群
          if(info=='新增'||info=='删除'){
            if(ress.attachment!==null){
              const steps=(ress.attachment.job_steps).split(',');
              //计算
              if(ress.attachment.computer_state=='ongoing'){
                this.computer_state='process';
                this.computer_icon='el-icon-loading'
                this.computer_title='正在'+info+steps[1];
              }else if(ress.attachment.computer_state=='done'){
                this.computer_state='success';
                this.computer_icon='el-icon-circle-check'
                this.computer_title=info+steps[1]+'成功';
                //遍历计算节点改状态
                if(this.computer.length>0){
                  for(let b=0;b<ress.attachment.computer_step.length;b++){
                    if(ress.attachment.computer_step[b].computer_state=='done'){
                      for(let c=0;c<this.computer.length;c++){
                        const arr=ress.attachment.computer_step[b].computer_hosts.substr(0,ress.attachment.computer_step[b].computer_hosts.length-1);
                        const computer_hosts=arr.split(';');
                        for(let e=0;e<computer_hosts.length;e++){
                          if(this.computer[c].title==computer_hosts[e]){
                            this.computer[c].icon='el-icon-circle-check';
                            this.computer[c].status='success';
                          }
                        }
                      }
                    }
                  }
                }
                //如果存储节点done，计算节点failed,ress.status='ongoing' 需要停止请求
              }else if(ress.attachment.computer_state=='failed'){
                this.computer_state='error';
                this.computer_icon='el-icon-circle-close'
                this.computer_title=info+steps[1]+'失败';
                //遍历计算节点改状态
                if(this.computer.length>0){
                  for(let b=0;b<ress.attachment.computer_step.length;b++){
                    if(ress.attachment.computer_step[b].computer_state=='failed'){
                      for(let c=0;c<this.computer.length;c++){
                        const arr=ress.attachment.computer_step[b].computer_hosts.substr(0,ress.attachment.computer_step[b].computer_hosts.length-1);
                        const computer_hosts=arr.split(';');
                        for(let e=0;e<computer_hosts.length;e++){
                          if(this.computer[c].title==computer_hosts[e]){
                            this.computer[c].icon='el-icon-circle-close';
                            this.computer[c].status='error';
                          }
                        }
                      }
                      this.computer_description=ress.attachment.computer_step[b].error_info;
                    }
                  }
                }
              }else{
                this.computer_state='process';
                this.computer_icon='el-icon-loading'
                this.computer_title='正在'+info+steps[1];
              }
              //存储
              if(ress.attachment.storage_state=='ongoing'){
                this.storage_state='process';
                this.shard_icon='el-icon-loading'
                this.shard_title='正在'+info+steps[0];
              }else if(ress.attachment.storage_state=='done'){
                this.storage_state='success';
                this.shard_icon='el-icon-circle-check'
                this.shard_title=info+steps[0]+'成功';
                //遍历存储节点改状态
                if(this.shard.length>0){
                  for(let c=0;c<this.shard.length;c++){
                    if(ress.attachment.hasOwnProperty('shard_step')){
                      for(let b=0;b<ress.attachment.shard_step.length;b++){
                        if(info=='新增'){
                          const shard_ids=ress.attachment.shard_step[b].shard_ids;
                          for(let e=0;e<shard_ids.length;e++){
                            for(var item in shard_ids[e]){
                              var shard_idsValue=shard_ids[e][item];
                              if(this.shard[c].shard_id==shard_idsValue){
                                this.shard[c].icon='el-icon-circle-check';
                                this.shard[c].status='success';
                              }
                            }
                          }
                        }else if(info=='删除'){
                          const arr=ress.attachment.shard_step[b].storage_hosts.substr(0,ress.attachment.shard_step[b].storage_hosts.length-1);
                          const shard_ids=arr.split(',');
                          for(let e=0;e<shard_ids.length;e++){
                            if(this.shard[c].shard_id==shard_ids[e]){
                              this.shard[c].icon='el-icon-circle-check';
                              this.shard[c].status='success';
                            }
                          }
                          
                        }
                      }
                    }else{
                      this.shard[c].icon='el-icon-circle-check';
                      this.shard[c].status='success';
                    }
                  }
                }
              }else if(ress.attachment.storage_state=='failed'){
                this.storage_state='error';
                this.shard_icon='el-icon-circle-close'
                this.shard_title=info+steps[0]+'失败';
                //遍历存储节点改状态
                if(this.shard.length>0){
                  for(let c=0;c<this.shard.length;c++){
                    if(ress.attachment.hasOwnProperty('shard_step')){
                      for(let b=0;b<ress.attachment.shard_step.length;b++){
                        if(info=='新增'){
                          const shard_ids=ress.attachment.shard_step[b].shard_ids;
                          for(let e=0;e<shard_ids.length;e++){
                            for(var item in shard_ids[e]){
                              var shard_idsValue=shard_ids[e][item];
                              if(this.shard[c].shard_id==shard_idsValue){
                                this.shard[c].icon='el-icon-circle-close';
                                this.shard[c].status='error';
                              }
                            }
                          }
                        }else if(info=='删除'){
                          const arr=ress.attachment.shard_step[b].storage_hosts.substr(0,ress.attachment.shard_step[b].storage_hosts.length-1);
                          const shard_ids=arr.split(',');
                          for(let e=0;e<shard_ids.length;e++){
                            if(this.shard[c].shard_id==shard_ids[e]){
                              this.shard[c].icon='el-icon-circle-close';
                              this.shard[c].status='error';
                            }
                          }
                        }
                        this.shard_description=ress.attachment.shard_step[b].error_info;
                      }
                    }else{
                      this.shard[c].icon='el-icon-circle-check';
                      this.shard[c].status='success';
                      this.shard_description=ress.error_info;
                    }
                  }
                }
              }else{
                this.storage_state='process';
                this.shard_icon='el-icon-loading'
                this.shard_title='正在'+info+steps[0];
              }
              this.init_title='正在'+info+'集群';
              //this.finish_title=info+'集群成功'
              
              this.init_active=3;
              if(this.computer.length==0&&this.shard.length==0){
                if(ress.attachment.hasOwnProperty('computer_step')){
                  for(let a=0;a<ress.attachment.computer_step.length;a++){
                    if(ress.attachment.computer_step[a].hasOwnProperty('computer_hosts')){
                      const arr=ress.attachment.computer_step[a].computer_hosts.substr(0,ress.attachment.computer_step[a].computer_hosts.length-1);
                      const computer_hosts=arr.split(';');
                        //console.log(computer_hosts[e]);
                        if(ress.attachment.computer_state=='done'){
                          for(let e=0;e<computer_hosts.length;e++){
                            let newArrgoing={}
                            newArrgoing.title=computer_hosts[e];
                            newArrgoing.icon='el-icon-circle-check';
                            newArrgoing.status= 'success';
                            newArrgoing.description='';
                            newArrgoing.computer_id=ress.attachment.computer_step[a].computer_id;
                            this.computer.push(newArrgoing)
                          }
                          //console.log(this.computer);
                        }else if(ress.attachment.computer_state=='failed'){
                          for(let e=0;e<computer_hosts.length;e++){
                            let newArrgoing={}
                            newArrgoing.title=computer_hosts[e];
                            newArrgoing.icon='el-icon-circle-close';
                            newArrgoing.status= 'error';
                            newArrgoing.description=ress.attachment.comp_error_info;
                            newArrgoing.computer_id=ress.attachment.computer_step[a].computer_id;
                            this.computer.push(newArrgoing)
                          }
                        }else{
                          //console.log(11);
                          for(let e=0;e<computer_hosts.length;e++){
                            let newArrgoing={}
                            newArrgoing.title=computer_hosts[e];
                            newArrgoing.icon='el-icon-loading';
                            newArrgoing.status= 'process';
                            newArrgoing.description='';
                            newArrgoing.computer_id=ress.attachment.computer_step[a].computer_id;
                            this.computer.push(newArrgoing)
                          }
                        }
                      //}
                      //console.log(this.computer);
                    }
                  }
                }
                if(ress.attachment.hasOwnProperty('shard_step')){
                  for(let b=0;b<ress.attachment.shard_step.length;b++){
                    if(ress.attachment.storage_state=='done'){
                      if(info=='删除'){
                        const arr=ress.attachment.shard_step[b].storage_hosts.substr(0,ress.attachment.shard_step[b].storage_hosts.length-1);
                        const shard_ids=arr.split(';');
                        for(let e=0;e<shard_ids.length;e++){
                          let shardgoing={}
                          shardgoing.title=shard_ids!==''?shard_ids[e]:'正在'+info+steps[0];
                          shardgoing.icon='el-icon-circle-check';
                          shardgoing.status= 'success';
                          shardgoing.description='';
                          shardgoing.shard_id=shard_ids[e];
                          this.shard.push(shardgoing)
                        }
                      }
                      if(info=='新增'){
                        let shard_ids=ress.attachment.shard_step[b].shard_ids;
                        for(let e=0;e<shard_ids.length;e++){
                          for(var item in shard_ids[e]){
                            let shardgoing={}
                            var shard_idsValue=shard_ids[e][item];
                            const shard_text=item+':'+shard_idsValue;
                            shardgoing.title=shard_idsValue!==''?shard_text:'正在'+info+steps[0];
                            shardgoing.icon='el-icon-circle-check';
                            shardgoing.status= 'success';
                            shardgoing.description='';
                            shardgoing.shard_id=shard_idsValue;
                            this.shard.push(shardgoing)
                          }
                        }
                      }
                    }else if(ress.attachment.storage_state=='failed'){
                      if(info=='删除'){
                        const arr=ress.attachment.shard_step[b].storage_hosts.substr(0,ress.attachment.shard_step[b].storage_hosts.length-1);
                        const shard_ids=arr.split(';');
                        for(let e=0;e<shard_ids.length;e++){
                          let shardgoing={}
                          shardgoing.title=shard_ids!==''?shard_ids[e]:'正在'+info+steps[0];
                          shardgoing.icon='el-icon-circle-close';
                          shardgoing.status= 'error';
                          shardgoing.description=ress.attachment.shard_error_info;
                          shardgoing.shard_id=shard_ids[e];
                          this.shard.push(shardgoing)
                        }
                      }
                      if(info=='新增'){
                        let shard_ids=ress.attachment.shard_step[b].shard_ids;
                        for(let e=0;e<shard_ids.length;e++){
                          for(var item in shard_ids[e]){
                            let shardgoing={}
                            var shard_idsValue=shard_ids[e][item];
                            const shard_text=item+':'+shard_idsValue;
                            shardgoing.title=shard_idsValue!==''?shard_text:'正在'+info+steps[0];
                            shardgoing.icon='el-icon-circle-close';
                            shardgoing.status= 'error';
                            shardgoing.description=ress.attachment.shard_error_info;
                            shardgoing.shard_id=shard_idsValue;
                            this.shard.push(shardgoing)
                          }
                        }
                      }
                    }else {
                      if(info=='删除'){
                        if(ress.attachment.shard_step[b].hasOwnProperty('storage_hosts')){
                          const arr=ress.attachment.shard_step[b].storage_hosts.substr(0,ress.attachment.shard_step[b].storage_hosts.length-1);
                          const shard_ids=arr.split(';');
                          for(let e=0;e<shard_ids.length;e++){
                            let shardgoing={}
                            shardgoing.title=shard_ids!==''?shard_ids[e]:'正在'+info+steps[0];
                            shardgoing.icon='el-icon-loading';
                            shardgoing.status= 'process';
                            shardgoing.description='';
                            shardgoing.shard_id=shard_ids[e];
                            this.shard.push(shardgoing)
                          }
                        }
                      }
                      if(info=='新增'){
                        let shard_ids=ress.attachment.shard_step[b].shard_ids;
                        for(let e=0;e<shard_ids.length;e++){
                          for(var item in shard_ids[e]){
                            let shardgoing={}
                            var shard_idsValue=shard_ids[e][item];
                            const shard_text=item+':'+shard_idsValue;
                            shardgoing.title=shard_idsValue!==''?shard_text:'正在'+info+steps[0];
                            shardgoing.icon='el-icon-loading';
                            shardgoing.status= 'process';
                            shardgoing.description='';
                            shardgoing.shard_id=shard_idsValue;
                            this.shard.push(shardgoing)
                          }
                        }
                      }
                    }
                  }
                }
                if(ress.status=='failed'){
                  this.storage_state='error';
                  this.shard_icon='el-icon-circle-close'
                  this.shard_title=info+steps[0]+'失败';
                  this.init_show=false;

                  this.computer_state='error';
                  this.computer_icon='el-icon-circle-close'
                  this.computer_title=info+steps[1]+'失败';

                  this.finish_title=info+'集群失败'
                  this.finish_icon='el-icon-circle-close'
                  this.finish_state='error';
                  this.finish_show=true;
                  this.init_active=4
                  this.finish_description=ress.error_info;
                  //遍历计算节点改状态
                  if(this.computer.length>0){
                    for(let b=0;b<ress.attachment.computer_step.length;b++){
                      if(ress.attachment.computer_step[b].computer_state=='failed'){
                        for(let c=0;c<this.computer.length;c++){
                          const arr=ress.attachment.computer_step[b].computer_hosts.substr(0,ress.attachment.computer_step[b].computer_hosts.length-1);
                          const computer_hosts=arr.split(';');
                          for(let e=0;e<computer_hosts.length;e++){
                            if(this.computer[c].title==computer_hosts[e]){
                              this.computer[c].icon='el-icon-circle-close';
                              this.computer[c].status='error';
                            }
                          }
                        }
                        this.computer_description=ress.attachment.computer_step[b].error_info;
                      }
                    }
                  }
                  //遍历存储节点改状态
                  if(this.shard.length>0){
                    for(let b=0;b<ress.attachment.shard_step.length;b++){
                      for(let c=0;c<this.shard.length;c++){
                        if(info=='新增'){
                          const shard_ids=ress.attachment.shard_step[b].shard_ids;
                          for(let e=0;e<shard_ids.length;e++){
                            for(var item in shard_ids[e]){
                              var shard_idsValue=shard_ids[e][item];
                              if(this.shard[c].shard_id==shard_idsValue){
                                this.shard[c].icon='el-icon-circle-close';
                                this.shard[c].status='error';
                              }
                            }
                          }
                        }else if(info=='删除'){
                          const arr=ress.attachment.shard_step[b].storage_hosts.substr(0,ress.attachment.shard_step[b].storage_hosts.length-1);
                          const shard_ids=arr.split(';');
                          for(let e=0;e<shard_ids.length;e++){
                            if(this.shard[c].shard_id==shard_ids[e]){
                              this.shard[c].icon='el-icon-circle-close';
                              this.shard[c].status='error';
                            }
                          }
                        }
                      }
                      this.shard_description=ress.attachment.shard_step[b].error_info;
                    }
                  }
                  clearInterval(timer);
                }else if(ress.status=='done'){
                  this.init_active=4;
                  this.finish_icon='el-icon-circle-check';
                  this.finish_state='success';
                  this.init_show=false;
                  this.finish_show=true;
                  clearInterval(timer);
                }
              }else{
                if(ress.status=='ongoing'){
                  //计算
                  //if(ress.attachment.computer_state=='ongoing'){
                    for(let k=0;k<this.computer.length;k++){
                      this.comp_active=k;
                      for(let j=0;j<ress.attachment.computer_step.length;j++){
                        if(ress.attachment.computer_step[j].computer_state=='ongoing'){
                          //console.log(22);
                          const arr=ress.attachment.computer_step[j].computer_hosts.substr(0,ress.attachment.computer_step[j].computer_hosts.length-1);
                          const computer_hosts=arr.split(';');
                          for(let e=0;e<computer_hosts.length;e++){
                            console.log(22);
                            if(computer_hosts[e]==this.computer[k].title){
                              this.comp_active=k-1;
                              this.computer[k].icon='el-icon-loading';
                              this.computer[k].status='process';
                              if(k>0&&k<(this.comp_active+1)){
                                //小于k的情况
                                for(let p=0;p<k;p++){
                                  this.computer[p].icon='el-icon-circle-check';
                                  this.computer[p].status='success';
                                }
                              }
                            }
                          }
                        }else if(ress.attachment.computer_step[j].computer_state=='failed'){
                          console.log(23);
                          const arr=ress.attachment.computer_step[j].computer_hosts.substr(0,ress.attachment.computer_step[j].computer_hosts.length-1);
                          const computer_hosts=arr.split(';');
                          for(let e=0;e<computer_hosts.length;e++){
                            if(computer_hosts[e]==this.computer[k].title){
                              this.computer[k].icon='el-icon-circle-close';
                              this.computer[k].status='error';
                            }
                          }
                          this.computer_description=ress.attachment.computer_step[j].error_info;
                        }else if(ress.attachment.computer_step[j].computer_state=='done'){
                          console.log(24);
                          const arr=ress.attachment.computer_step[j].computer_hosts.substr(0,ress.attachment.computer_step[j].computer_hosts.length-1);
                          const computer_hosts=arr.split(';');
                          for(let e=0;e<computer_hosts.length;e++){
                            if(computer_hosts[e]==this.computer[k].title){
                              //this.comp_active=k;
                              this.computer[k].status='success';
                              this.computer[k].icon='el-icon-circle-check';
                            }
                          }
                        }
                      }
                    }
                  //}
                  //存储
                  if(this.shard.length>0){
                    for(let k=0;k<this.shard.length;k++){
                      for(let j=0;j<ress.attachment.shard_step.length;j++){
                        if(ress.attachment.shard_step[j].storage_state=='ongoing'){
                          if(ress.attachment.shard_step[j].shard_hosts==this.shard[k].title){
                            this.shard_active=k-1;
                            this.shard[k].icon='el-icon-loading';
                            this.shard[k].status='process';
                            if(k>0&&k<(this.shard_active+1)){
                              //小于k的情况
                              for(let p=0;p<k;p++){
                                this.shard[p].icon='el-icon-circle-check';
                                this.shard[p].status='success';
                              }
                            }
                          }
                        }else if(ress.attachment.shard_step[j].storage_state=='failed'){
                          this.shard_active=k;
                          this.shard[k].icon='el-icon-circle-close';
                          this.shard[k].status='error';
                          this.shard_description=ress.attachment.shard_step[j].error_info;
                        }else if(ress.attachment.shard_step[j].storage_state=='done'){
                          this.shard_active=k;
                          this.shard[k].status='success';
                          this.shard[k].icon='el-icon-circle-check';
                        }
                      }
                    }
                  }else{
                    if(ress.attachment.shard_step[0].hasOwnProperty('storage_hosts')){
                      if(info=='删除'){
                        if(ress.attachment.storage_state=='ongoing'){
                          const arr=ress.attachment.shard_step[0].storage_hosts.substr(0,ress.attachment.shard_step[0].storage_hosts.length-1);
                          const shard_ids=arr.split(';');
                          for(let e=0;e<shard_ids.length;e++){
                            let shardgoing={}
                            shardgoing.title=shard_ids!==''?shard_ids[e]:'正在'+info+steps[0];
                            shardgoing.icon='el-icon-loading';
                            shardgoing.status= 'process';
                            shardgoing.description='';
                            shardgoing.shard_id=shard_ids[e];
                            this.shard.push(shardgoing)
                          }
                        }else if(ress.attachment.storage_state=='failed'){
                          const arr=ress.attachment.shard_step[0].storage_hosts.substr(0,ress.attachment.shard_step[0].storage_hosts.length-1);
                          const shard_ids=arr.split(';');
                          for(let e=0;e<shard_ids.length;e++){
                            let shardgoing={}
                            shardgoing.title=shard_ids!==''?shard_ids[e]:'正在'+info+steps[0];
                            shardgoing.icon='el-icon-circle-close';
                            shardgoing.status= 'error';
                            shardgoing.description='';
                            shardgoing.shard_id=shard_ids[e];
                            this.shard.push(shardgoing)
                          }
                        }else if(ress.attachment.storage_state=='done'){
                          const arr=ress.attachment.shard_step[0].storage_hosts.substr(0,ress.attachment.shard_step[0].storage_hosts.length-1);
                          const shard_ids=arr.split(';');
                          for(let e=0;e<shard_ids.length;e++){
                            let shardgoing={}
                            shardgoing.title=shard_ids!==''?shard_ids[e]:'正在'+info+steps[0];
                            shardgoing.icon='el-icon-circle-check';
                            shardgoing.status= 'success';
                            shardgoing.description='';
                            shardgoing.shard_id=shard_ids[e];
                            this.shard.push(shardgoing)
                          }
                        }
                      }
                    }
                  }
                }else if(ress.status=='done'){
                  //console.log(3);
                  this.init_show=false;
                  if(ress.attachment.computer_state=='done'){
                    console.log(51)
                    this.computer_title=info+steps[1]+'成功';
                    this.computer_state='success';
                    this.computer_icon='el-icon-circle-check'
                    this.comp_active=this.computer.length-1;
                  for(let d=0;d<this.computer.length;d++){
                    //console.log(this.computer)
                    this.computer[d].status='success';
                    this.computer[d].icon='el-icon-circle-check';
                  }
                  }
                  if(ress.attachment.storage_state=='done'){
                    console.log(52)
                    this.shard_title=info+steps[0]+'成功';
                    this.storage_state='success';
                    this.shard_icon='el-icon-circle-check'
                    this.shard_active=this.shard.length-1;
                    if(this.shard.length>0){
                      for(let d=0;d<this.shard.length;d++){
                        console.log(53)
                        this.shard[d].status='success';
                        this.shard[d].icon='el-icon-circle-check';
                      }
                    }else{
                      if(ress.attachment.shard_step[0].hasOwnProperty('storage_hosts')){
                        if(info=='删除'){
                          const arr=ress.attachment.shard_step[0].storage_hosts.substr(0,ress.attachment.shard_step[0].storage_hosts.length-1);
                          const shard_ids=arr.split(';');
                          for(let e=0;e<shard_ids.length;e++){
                            let shardgoing={}
                            shardgoing.title=shard_ids!==''?shard_ids[e]:'正在'+info+steps[0];
                            shardgoing.icon='el-icon-circle-check';
                            shardgoing.status= 'success';
                            shardgoing.description='';
                            shardgoing.shard_id=shard_ids[e];
                            this.shard.push(shardgoing)
                          }
                        }
                      }
                    }
                  }
                  
                  this.init_active=4;
                  this.finish_title=info+'集群成功'
                  this.finish_icon='el-icon-circle-check'
                  this.finish_state='success';
                  this.finish_show=true;
                  clearInterval(timer);
                  if(info=='新增'){
                    const  apply_all_cluster=sessionStorage.getItem('apply_all_cluster');
                    if(apply_all_cluster==2){
                        const arrs= {};
                        arrs.effectCluster=sessionStorage.getItem('affected_clusters');
                        arrs.cluster_name=ress.attachment.cluster_name;
                        arrs.username=sessionStorage.getItem('login_username');
                        arrs.type='add';
                        uAssign(arrs).then((responses) => {
                          let res_update = responses;
                          if(res_update.code==200){
                            this.dialogFormVisible = false;
                            sessionStorage.setItem('affected_clusters',res_update.effectCluster);
                          }
                        });
                    }
                    setTimeout(() => {
                      this.getList();
                      //this.dialogStatusShowVisible=false;
                    }, 3000);
                  }else{
                    this.getList();
                  }
                }else if(ress.status=='failed'){
                  this.finish_title=info+'集群失败'
                  this.finish_icon='el-icon-circle-close'
                  this.finish_state='error';
                  this.init_active=4
                  this.finish_description=ress.error_info;
                  this.finish_show=true;
                  this.init_show=false;
                  clearInterval(timer);
                  if(ress.attachment.hasOwnProperty('computer_state')){
                    if(ress.attachment.computer_state=='failed'){
                      this.computer_state='error';
                      this.computer_icon='el-icon-circle-close';
                      for(let b=0;b<ress.attachment.computer_step.length;b++){
                        const arr=ress.attachment.computer_step[b].computer_hosts.substr(0,ress.attachment.computer_step[b].computer_hosts.length-1);
                        const computer_hosts=arr.split(';');
                        if(ress.attachment.computer_step[b].computer_state=='done'){
                          this.comp_active=b;
                          for(let c=0;c<this.computer.length;c++){
                            for(let e=0;e<computer_hosts.length;e++){
                              if(this.computer[c].title==computer_hosts[e]){
                                this.computer[c].icon='el-icon-circle-check';
                                this.computer[c].status='success';
                              }
                            }
                          }
                        }else if(ress.attachment.computer_step[b].computer_state=='failed'){
                            for(let c=0;c<this.computer.length;c++){
                              for(let e=0;e<computer_hosts.length;e++){
                                if(this.computer[c].title==computer_hosts[e]){
                                  this.computer[c].icon='el-icon-circle-close';
                                  this.computer[c].status='error';
                                }
                              }
                          }
                        }
                        this.computer_description=ress.attachment.computer_step[b].error_info;
                      }
                    }else if(ress.attachment.computer_state=='done'){
                      //let current_id=0;
                      for(let b=0;b<ress.attachment.computer_step.length;b++){
                        const arr=ress.attachment.computer_step[b].computer_hosts.substr(0,ress.attachment.computer_step[b].computer_hosts.length-1);
                        const computer_hosts=arr.split(';');
                        if(ress.attachment.computer_step[b].computer_state=='done'){
                          this.comp_active=b;
                          for(let c=0;c<this.computer.length;c++){
                            for(let e=0;e<computer_hosts.length;e++){
                              if(this.computer[c].title=computer_hosts[e]){
                                this.computer[c].icon='el-icon-circle-check';
                                this.computer[c].status='success';
                                //current_id=c;
                              }
                            }
                          }
                        }else if(ress.attachment.computer_step[b].computer_state=='failed'){
                          for(let c=0;c<this.computer.length;c++){
                            for(let e=0;e<computer_hosts.length;e++){
                              if(this.computer[c].title==computer_hosts[e]){
                                this.computer[c].icon='el-icon-circle-close';
                                this.computer[c].status='error';
                                //current_id=c;
                              }
                            }
                          }
                          this.computer_description=ress.attachment.computer_step[b].error_info;
                        }
                      }
                      //小于b的status为success
                      // if(this.computer.length>1&&this.comp_active>0){
                      //   if(current_id){

                      //   }
                      // }
                    }
                  }else{
                    this.computer_state='error';
                    this.computer_icon='el-icon-circle-close'
                    this.computer_title=info+steps[0]+'失败';
                  }
                  if(ress.attachment.hasOwnProperty('storage_state')){
                    if(ress.attachment.storage_state=='failed'){
                      this.storage_state='error';
                      this.shard_icon='el-icon-circle-close'
                      for(let b=0;b<ress.attachment.shard_step.length;b++){
                        if(ress.attachment.shard_step[b].storage_state=='done'){
                          this.shard_active=b;
                          for(let c=0;c<this.shard.length;c++){
                            if(info=='新增'){
                            const shard_ids=ress.attachment.shard_step[b].shard_ids;
                            for(let e=0;e<shard_ids.length;e++){
                              for(var item in shard_ids[e]){
                                var shard_idsValue=shard_ids[e][item];
                                if(this.shard[c].shard_id==shard_idsValue){
                                  this.shard[c].icon='el-icon-circle-check';
                                  this.shard[c].status='success';
                                }
                              }
                            }
                            }else if(info=='删除'){
                              const arr=ress.attachment.shard_step[b].storage_hosts.substr(0,ress.attachment.shard_step[b].storage_hosts.length-1);
                              const shard_ids=arr.split(',');
                              for(let e=0;e<shard_ids.length;e++){
                                if(this.shard[c].shard_id==shard_ids[e]){
                                  this.shard[c].icon='el-icon-circle-check';
                                  this.shard[c].status='success';
                                }
                              }
                            }
                          }
                        }else if(ress.attachment.shard_step[b].storage_state=='failed'){
                          for(let c=0;c<this.shard.length;c++){
                            if(info=='新增'){
                            const shard_ids=ress.attachment.shard_step[b].shard_ids;
                              for(let e=0;e<shard_ids.length;e++){
                                for(var item in shard_ids[e]){
                                  var shard_idsValue=shard_ids[e][item];
                                  if(this.shard[c].shard_id==shard_idsValue){
                                    this.shard[c].icon='el-icon-circle-close';
                                    this.shard[c].status='error';
                                  }
                                }
                              }
                            }else if(info=='删除'){
                              const arr=ress.attachment.shard_step[b].storage_hosts.substr(0,ress.attachment.shard_step[b].storage_hosts.length-1);
                              const shard_ids=arr.split(',');
                              for(let e=0;e<shard_ids.length;e++){
                                if(this.shard[c].shard_id==shard_ids[e]){
                                  this.shard[c].icon='el-icon-circle-close';
                                  this.shard[c].status='error';
                                }
                              }
                            }
                          }
                          this.shard_description=ress.attachment.shard_step[b].error_info;
                        }
                      }
                    }else if(ress.attachment.storage_state=='done'){
                      //let current_id=0;
                      for(let b=0;b<ress.attachment.shard_step.length;b++){
                        if(ress.attachment.shard_step[b].storage_state=='done'){
                          this.shard_active=b;
                          for(let c=0;c<this.shard.length;c++){
                            if(this.shard[c].title==ress.attachment.shard_step[b].shard_hosts){
                              this.shard[c].icon='el-icon-circle-check';
                              this.shard[c].status='success';
                              //current_id=c;
                            }
                          }
                        }else if(ress.attachment.shard_step[b].storage_state=='failed'){
                            for(let c=0;c<this.shard.length;c++){
                            if(this.shard[c].title==ress.attachment.shard_step[b].shard_hosts){
                              this.shard[c].icon='el-icon-circle-close';
                              this.shard[c].status='error';
                              //current_id=c;
                            }
                          }
                        }
                      }
                    }
                    else{
                      for(let c=0;c<this.shard.length;c++){
                        if(info=='新增'){
                          const shard_ids=ress.attachment.shard_step[b].shard_ids;
                          for(let e=0;e<shard_ids.length;e++){
                            for(var item in shard_ids[e]){
                              var shard_idsValue=shard_ids[e][item];
                              if(this.shard[c].shard_id==shard_idsValue){
                                this.shard[c].icon='el-icon-circle-close';
                                this.shard[c].status='error';
                              }
                            }
                          }
                        }else if(info=='删除'){
                          const arr=ress.attachment.shard_step[b].storage_hosts.substr(0,ress.attachment.shard_step[b].storage_hosts.length-1);
                          const shard_ids=arr.split(',');
                          for(let e=0;e<shard_ids.length;e++){
                            if(this.shard[c].shard_id==shard_ids[e]){
                              this.shard[c].icon='el-icon-circle-close';
                              this.shard[c].status='error';
                            }
                          }
                        }
                      }
                      this.shard_description=ress.error_info;
                    }
                  }else{
                    this.storage_state='error';
                    this.shard_icon='el-icon-circle-close'
                    this.shard_title=info+steps[0]+'失败';
                  }
                  
                }
              }
            }else if(ress.attachment==null&&ress.error_code=='70001'&&ress.status=='failed'){
              this.init_title='正在'+info+'集群';
              //计算节点
              this.computer_state='process';
              this.computer_icon='el-icon-loading'
              this.computer_title='正在'+info+'computer';
              //储存节点
              this.storage_state='process';
              this.shard_icon='el-icon-loading';
              this.shard_title='正在'+info+'shard';
              if(i>5){
                this.init_show=false
                //计算节点
                this.computer_state='error';
                this.computer_icon='el-icon-circle-close'
                this.computer_title=info+'computer失败';
                //储存节点
                this.storage_state='error';
                this.shard_icon='el-icon-circle-close';
                this.shard_title=info+'shard失败';
                clearInterval(timer);
              }
            }else if(ress.attachment==null&&ress.status=='ongoing'){
              this.init_title='正在'+info+'集群';
              //计算节点
              this.computer_state='process';
              this.computer_icon='el-icon-loading'
              this.computer_title='正在'+info+'computer';
              //储存节点
              this.storage_state='process';
              this.shard_icon='el-icon-loading';
              this.shard_title='正在'+info+'shard';
            }else if(ress.attachment==null&&ress.status=='done'){
              this.init_show=false
              //计算节点
              this.computer_state='success';
              this.computer_icon='el-icon-circle-check'
              this.computer_title=info+'computer成功';
              //储存节点
              this.storage_state='success';
              this.shard_icon='el-icon-circle-check';
              this.shard_title=info+'shard成功';
              clearInterval(timer);
            }else{
              this.init_show=false
              //计算节点
              this.computer_state='error';
              this.computer_icon='el-icon-circle-close'
              this.computer_title=info+'computer失败';
              //储存节点
              this.storage_state='error';
              this.shard_icon='el-icon-circle-close';
              this.shard_title=info+'shard失败';
              clearInterval(timer);
            }
          }else if(info=='添加shard'||info=='添加计算节点'||info=='添加存储节点'){
            //新增shard,计算节点，存储节点
            if(ress.attachment!==null){
              if(info=='添加shard'||info=='添加存储节点'){
                this.shard_show=true;
                this.init_show=false;
                this.computer_show=false;
              }else if(info=='添加计算节点'){
                this.shard_show=false;
                this.init_show=false;
                this.computer_show=true;
              }
              //计算
              if(ress.attachment.hasOwnProperty('computer_state')){
                if(ress.attachment.computer_state=='ongoing'){
                  this.computer_state='process';
                  this.computer_icon='el-icon-loading'
                  this.computer_title='正在'+info;
                }else if(ress.attachment.computer_state=='done'){
                  this.computer_state='success';
                  this.computer_icon='el-icon-circle-check'
                  this.computer_title=info+'成功';
                  //遍历计算节点改状态
                  if(this.computer.length>0){
                    for(let c=0;c<this.computer.length;c++){
                      const arr=ress.attachment.computer_hosts.substr(0,ress.attachment.computer_hosts.length-1);
                      const computer_hosts=arr.split(';');
                      for(let e=0;e<computer_hosts.length;e++){
                        if(this.computer[c].title==computer_hosts[e]){
                          this.computer[c].icon='el-icon-circle-check';
                          this.computer[c].status='success';
                        }
                      }
                    }
                  }
                }else if(ress.attachment.computer_state=='failed'){
                  this.computer_state='error';
                  this.computer_icon='el-icon-circle-close'
                  this.computer_title=info+'失败';
                  //遍历计算节点改状态
                  if(this.computer.length>0){
                    for(let c=0;c<this.computer.length;c++){
                      const arr=ress.attachment.computer_hosts.substr(0,ress.attachment.computer_hosts.length-1);
                      const computer_hosts=arr.split(';');
                      for(let e=0;e<computer_hosts.length;e++){
                        if(this.computer[c].title==computer_hosts[e]){
                          this.computer[c].icon='el-icon-circle-close';
                          this.computer[c].status='error';
                        }
                      }
                    }
                  this.computer_description=ress.error_info;
                  }
                }else{
                  this.computer_state='process';
                  this.computer_icon='el-icon-loading'
                  this.computer_title='正在'+info;
                }
              }
              //存储
              if(ress.attachment.hasOwnProperty('storage_state')){
                if(ress.attachment.storage_state=='ongoing'){
                  this.storage_state='process';
                  this.shard_icon='el-icon-loading'
                  this.shard_title='正在'+info;
                }else if(ress.attachment.storage_state=='done'){
                  this.storage_state='success';
                  this.shard_icon='el-icon-circle-check'
                  this.shard_title=info+'成功';
                  //遍历存储节点改状态
                  if(this.shard.length>0){
                    for(let c=0;c<this.shard.length;c++){
                      let shard_ids='';
                      if(info=='添加shard'){
                        shard_ids=ress.attachment.shard_ids;
                      }else{
                        shard_ids=ress.attachment.shard_hosts;
                      }
                      //const shard_ids=ress.attachment.shard_ids;
                      for(let e=0;e<shard_ids.length;e++){
                        for(var item in shard_ids[e]){
                          var shard_idsValue=shard_ids[e][item];
                          if(this.shard[c].shard_id==shard_idsValue){
                            this.shard[c].icon='el-icon-circle-check';
                            this.shard[c].status='success';
                          }
                        }
                      }
                    }
                  }
                }else if(ress.attachment.storage_state=='failed'){
                  this.storage_state='error';
                  this.shard_icon='el-icon-circle-close'
                  this.shard_title=info+'失败';
                  //遍历存储节点改状态
                  if(this.shard.length>0){
                    for(let c=0;c<this.shard.length;c++){
                      let shard_ids='';
                      if(info=='添加shard'){
                        shard_ids=ress.attachment.shard_ids;
                      }else{
                        shard_ids=ress.attachment.shard_hosts;
                      }
                      for(let e=0;e<shard_ids.length;e++){
                        for(var item in shard_ids[e]){
                          var shard_idsValue=shard_ids[e][item];
                          if(this.shard[c].shard_id==shard_idsValue){
                            this.shard[c].icon='el-icon-circle-close';
                            this.shard[c].status='error';
                          }
                        }
                      }
                    }
                    this.shard_description=ress.error_info;
                  }
                  //clearInterval(timer);
                }else{
                  this.storage_state='process';
                  this.shard_icon='el-icon-loading'
                  this.shard_title='正在'+info;
                }
              }
              this.init_title='正在'+info;
              //this.finish_title=info+'集群成功'
              this.init_active=1;
              if(this.computer.length==0||this.shard.length==0){
                if(info=='添加计算节点'){
                  if(this.computer.length==0){
                    if(ress.attachment.hasOwnProperty('computer_hosts')){
                      const arr=ress.attachment.computer_hosts.substr(0,ress.attachment.computer_hosts.length-1);
                      const computer_hosts=arr.split(';');
                      if(ress.attachment.computer_state=='done'){
                        for(let e=0;e<computer_hosts.length;e++){
                          let newArrgoing={}
                          newArrgoing.title=computer_hosts[e];
                          newArrgoing.icon='el-icon-circle-check';
                          newArrgoing.status= 'success';
                          newArrgoing.description='';
                          newArrgoing.computer_id=ress.attachment.computer_id;
                          this.computer.push(newArrgoing)
                        }
                      }else{
                        for(let e=0;e<computer_hosts.length;e++){
                          let newArrgoing={}
                          newArrgoing.title=computer_hosts[e];
                          newArrgoing.icon='el-icon-loading';
                          newArrgoing.status= 'process';
                          newArrgoing.description='';
                          newArrgoing.computer_id=ress.attachment.computer_id;
                          this.computer.push(newArrgoing)
                        }
                      }
                    }
                  }else{
                    if(ress.attachment.hasOwnProperty('computer_state')){
                      if(ress.attachment.computer_state=='done'){
                        this.computer_state='success';
                        this.computer_icon='el-icon-circle-check'
                        this.computer_title=info+'成功';
                        //遍历计算节点改状态
                        if(this.computer.length>0){
                          for(let c=0;c<this.computer.length;c++){
                            const arr=ress.attachment.computer_hosts.substr(0,ress.attachment.computer_hosts.length-1);
                            const computer_hosts=arr.split(';');
                            for(let e=0;e<computer_hosts.length;e++){
                              if(this.computer[c].title==computer_hosts[e]){
                                this.computer[c].icon='el-icon-circle-check';
                                this.computer[c].status='success';
                              }
                            }
                          }
                        }
                      }else if(ress.attachment.computer_state=='failed'){
                        this.computer_state='error';
                        this.computer_icon='el-icon-circle-close'
                        this.computer_title=info+'失败';
                        //遍历计算节点改状态
                        if(this.computer.length>0){
                          for(let c=0;c<this.computer.length;c++){
                            const arr=ress.attachment.computer_hosts.substr(0,ress.attachment.computer_hosts.length-1);
                            const computer_hosts=arr.split(';');
                            for(let e=0;e<computer_hosts.length;e++){
                              if(this.computer[c].title==computer_hosts[e]){
                                this.computer[c].icon='el-icon-circle-close';
                                this.computer[c].status='error';
                              }
                            }
                          }
                        this.computer_description=ress.error_info;
                        }
                      }else{
                        this.computer_state='process';
                        this.computer_icon='el-icon-loading'
                        this.computer_title='正在'+info;
                      }
                    }
                  }
                }else{
                  if(this.shard.length==0){
                    if(ress.attachment.hasOwnProperty('shard_hosts')){
                      let shard_ids='';
                      if(info=='添加shard'){
                        shard_ids=ress.attachment.shard_ids;
                      }else{
                        shard_ids=ress.attachment.shard_hosts;
                      }
                      for(let e=0;e<shard_ids.length;e++){
                        for(var item in shard_ids[e]){
                          let shardgoing={}
                          var shard_idsValue=shard_ids[e][item];
                          console.log(shard_idsValue);
                          const shard_text=item+':'+shard_idsValue;
                          console.log(shard_text);
                          shardgoing.title=shard_idsValue!==''?shard_text:'正在'+info;
                          shardgoing.icon='el-icon-loading';
                          shardgoing.status= 'process';
                          shardgoing.description='';
                          shardgoing.shard_id=shard_idsValue;
                          this.shard.push(shardgoing)
                        }
                      }
                    }
                  }else{
                    if(ress.attachment.hasOwnProperty('storage_state')){
                      if(ress.attachment.storage_state=='done'){
                        this.storage_state='success';
                        this.shard_icon='el-icon-circle-check'
                        this.shard_title=info+'成功';
                        //遍历存储节点改状态
                        if(this.shard.length>0){
                          for(let c=0;c<this.shard.length;c++){
                            let shard_ids='';
                            if(info=='添加shard'){
                              shard_ids=ress.attachment.shard_ids;
                            }else{
                              shard_ids=ress.attachment.shard_hosts;
                            }
                            //const shard_ids=ress.attachment.shard_ids;
                            for(let e=0;e<shard_ids.length;e++){
                              for(var item in shard_ids[e]){
                                var shard_idsValue=shard_ids[e][item];
                                if(this.shard[c].shard_id==shard_idsValue){
                                  this.shard[c].icon='el-icon-circle-check';
                                  this.shard[c].status='success';
                                }
                              }
                            }
                          }
                        }
                      }else if(ress.attachment.storage_state=='failed'){
                        this.storage_state='error';
                        this.shard_icon='el-icon-circle-close'
                        this.shard_title=info+'失败';
                        //遍历存储节点改状态
                        if(this.shard.length>0){
                          for(let c=0;c<this.shard.length;c++){
                            let shard_ids='';
                            if(info=='添加shard'){
                              shard_ids=ress.attachment.shard_ids;
                            }else{
                              shard_ids=ress.attachment.shard_hosts;
                            }
                            for(let e=0;e<shard_ids.length;e++){
                              for(var item in shard_ids[e]){
                                var shard_idsValue=shard_ids[e][item];
                                if(this.shard[c].shard_id==shard_idsValue){
                                  this.shard[c].icon='el-icon-circle-close';
                                  this.shard[c].status='error';
                                }
                              }
                            }
                          }
                          this.shard_description=ress.error_info;
                        }
                        //clearInterval(timer);
                      }else{
                        this.storage_state='process';
                        this.shard_icon='el-icon-loading'
                        this.shard_title='正在'+info;
                      }
                    }
                  }
                }
                if(ress.status=='failed'){
                  this.storage_state='error';
                  this.shard_icon='el-icon-circle-close'
                  this.shard_title=info+'失败';
                  this.init_show=false;

                  this.computer_state='error';
                  this.computer_icon='el-icon-circle-close'
                  this.computer_title=info+'失败';

                  this.finish_title=info+'失败'
                  this.finish_icon='el-icon-circle-close'
                  this.finish_state='error';
                  this.init_active=1
                  this.finish_description=ress.error_info;
                  //遍历计算节点改状态
                  if(this.computer.length>0){
                    for(let c=0;c<this.computer.length;c++){
                      if(ress.attachment.hasOwnProperty('computer_hosts')){
                        const arr=ress.attachment.computer_hosts.substr(0,ress.attachment.computer_hosts.length-1);
                        const computer_hosts=arr.split(';');
                        for(let e=0;e<computer_hosts.length;e++){
                          if(this.computer[c].title==computer_hosts[e]){
                            this.computer[c].icon='el-icon-circle-close';
                            this.computer[c].status='error';
                          }
                        }
                      }
                    }
                  this.computer_description=ress.error_info;
                  }
                  //遍历存储节点改状态
                  if(this.shard.length>0){
                    for(let c=0;c<this.shard.length;c++){
                      let shard_ids='';
                      if(ress.attachment.hasOwnProperty('shard_hosts')){
                        if(info=='添加shard'){
                          shard_ids=ress.attachment.shard_ids;
                        }else{
                          shard_ids=ress.attachment.shard_hosts;
                        }
                        for(let e=0;e<shard_ids.length;e++){
                          for(var item in shard_ids[e]){
                            var shard_idsValue=shard_ids[e][item];
                            if(this.shard[c].shard_id==shard_idsValue){
                              this.shard[c].icon='el-icon-circle-close';
                              this.shard[c].status='error';
                            }
                          }
                        }
                      }
                    }
                    this.shard_description=ress.error_info;
                  }
                  clearInterval(timer);
                }else if(ress.status=='done'){
                  this.storage_state='success';
                  this.shard_icon='el-icon-circle-check'
                  this.shard_title=info+'成功';

                  this.computer_state='success';
                  this.computer_icon='el-icon-circle-check'
                  this.computer_title=info+'成功';

                  this.finish_title=info+'成功'
                  this.finish_icon='el-icon-circle-check'
                  this.finish_state='success';
                  this.init_active=1
                  this.finish_description=ress.error_info;
                  //遍历计算节点改状态
                  if(this.computer.length>0){
                    for(let c=0;c<this.computer.length;c++){
                      if(ress.attachment.hasOwnProperty('computer_hosts')){
                        const arr=ress.attachment.computer_hosts.substr(0,ress.attachment.computer_hosts.length-1);
                        const computer_hosts=arr.split(';');
                        for(let e=0;e<computer_hosts.length;e++){
                          if(this.computer[c].title==computer_hosts[e]){
                            this.computer[c].icon='el-icon-circle-check';
                            this.computer[c].status='success';
                          }
                        }
                      }
                    }
                  }
                  //遍历存储节点改状态
                  if(this.shard.length>0){
                    for(let c=0;c<this.shard.length;c++){
                      let shard_ids='';
                      if(ress.attachment.hasOwnProperty('shard_hosts')){
                        if(info=='添加shard'){
                          shard_ids=ress.attachment.shard_ids;
                        }else{
                          shard_ids=ress.attachment.shard_hosts;
                        }
                        //const shard_ids=ress.attachment.shard_ids;
                        for(let e=0;e<shard_ids.length;e++){
                          for(var item in shard_ids[e]){
                            var shard_idsValue=shard_ids[e][item];
                            if(this.shard[c].shard_id==shard_idsValue){
                              this.shard[c].icon='el-icon-circle-check';
                              this.shard[c].status='success';
                            }
                          }
                        }
                      }
                    }
                  }
                  clearInterval(timer);
                  this.getList();
                }
              }
            }else if(ress.attachment==null&&ress.error_code=='70001'&&ress.status=='failed'){
              if(info=='添加shard'||info=='添加存储节点'){
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
              }else if(info=='添加计算节点'){
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
            }else if(ress.attachment==null&&ress.status=='ongoing'){
               if(info=='添加shard'||info=='添加存储节点'){
                this.shard_show=true;
                this.init_show=false;
                this.computer_show=false;
                this.storage_state='process';
                this.shard_icon='el-icon-loading'
                this.shard_title='正在'+info;
              }else if(info=='添加计算节点'){
                this.shard_show=false;
                this.init_show=false;
                this.computer_show=true;
                this.computer_state='process';
                this.computer_icon='el-icon-loading'
                this.computer_title='正在'+info;
              }
            }else if(ress.attachment==null&&ress.status=='done'){
              if(info=='添加shard'||info=='添加存储节点'){
                this.shard_show=true;
                this.init_show=false;
                this.computer_show=false;
                this.storage_state='success';
                this.shard_icon='el-icon-circle-check'
                this.shard_title=info+'成功';
              }else if(info=='添加计算节点'){
                this.shard_show=false;
                this.init_show=false;
                this.computer_show=true;
                this.computer_state='success';
                this.computer_icon='el-icon-circle-check'
                this.computer_title=info+'成功';
              }
              clearInterval(timer);
            }else{
              if(info=='添加shard'||info=='添加存储节点'){
                this.shard_show=true;
                this.init_show=false;
                this.computer_show=false;
                this.storage_state='error';
                this.shard_icon='el-icon-circle-close'
                this.shard_title=info+'失败';
              }else if(info=='添加计算节点'){
                this.shard_show=false;
                this.init_show=false;
                this.computer_show=true;
                this.computer_state='error';
                this.computer_icon='el-icon-circle-close'
                this.computer_title=info+'失败';
              }
              clearInterval(timer);
            }
          }else if(info=='扩容'){
            if(ress.attachment!==null){
              if(ress.status=='failed'){
                this.expand_end="集群扩容失败"
                this.expondInit=false;
                this.expondSatus=true;
                this.expondResult=true;
                this.expondInfo=ress.attachment.memo_info;
                console.log(ress.attachment.memo_info);
                clearInterval(timer);
              }else if(ress.status=='done'){
                this.expand_end="集群扩容成功"
                this.expondInit=false;
                this.expondSatus=true;
                this.expondResult=true;
                this.expondInfo=ress.attachment.memo_info;
                //console.log(typeof ress.attachment.memo_info);
                clearInterval(timer);
              }else{
                this.expand_init="正在进行集群扩容"
                this.expondInit=true;
                this.expondSatus=false;
                this.expondResult=false;
                this.expondInfo=ress.attachment.memo_info;
              }
            }else if(ress.attachment==null&&ress.status=='failed'){
              this.expondInit=false;
              this.expondSatus=false;
              this.expondResult=true;
              this.expand_end="集群扩容失败"
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
          let error_info='';
          if(res.attachment!==null){
            if(res.attachment.memo_info.error_info!==''){
              error_info=res.attachment.memo_info.error_info
            }else{
              error_info=res.error_info
            }
          }
        if(res.status=='done'||res.status=='failed'){
          clearInterval(timer);
          //this.info=res.error_info;
          if(res.status=='done'){
            // if(error_info){
            //   const newArrdone={
            //     content:error_info,
            //     timestamp: getNowDate(),
            //     color: '#0bbd87',
            //     icon: 'el-icon-circle-check'
            //   };
            //   this.activities.push(newArrdone)
            // }else{
              const newArrdone={
                content:'集群回档成功',
                timestamp: getNowDate(),
                color: '#0bbd87',
                icon: 'el-icon-circle-check'
              };
              this.activities.push(newArrdone)
            // }
            this.getList();
            //this.dialogStatusVisible=false;
          }else{
            const newArr={
              content:error_info,
              timestamp: getNowDate(),
              color: 'red',
              icon: 'el-icon-circle-close'
            };
            this.activities.push(newArr);
            //this.installStatus = true;
          }
        }else{
          if(error_info){
            const newArrgoing={
              content:error_info,
              timestamp: getNowDate(),
              color: '#0bbd87'
            };
            this.activities.push(newArrgoing)
          }
          //this.info=res.error_info;
          //this.installStatus = true;
        }
      });
        if(i>=86400){
            clearInterval(timer);
        }
      }, 0)
    }

  },
};
</script>
<style scoped>
/* .right_input{
  width:8%;
} */
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

.el-step.is-vertical .el-step__title{
  width: 80%;
  display: table-cell !important;
}
.el-step__description.is-success{
  display: table-cell !important;
}


/* 
.el-step__description.is-finish span{
  color: red !important;
}  */



 /* .stepComponent{
      background-color:#DFEBFF;
      width: 100%-20px;
      padding: 10px 10px 10px 10px;
      margin: 10px 10px 10px 10px;
  }
  .stepsTitle{
      margin: 10px 0px  10px  10px ;
  }
  .approvalProcess{
      color: #9EADC4;
      font-size: 14px;
      
      background:#DFEBFF;
      margin-left:20px;
      margin-right:0px;
      margin-top:10px;
  }
  .processing_content{
    background-color: #D9E5F9;
  }
  .processing_content_detail{
     margin-left: 10px;
     margin-top: 3.5px;
     margin-bottom: 3.5px;
       width:150px;
     display:inline-block;
  }
  .step-row{
     min-width:500px;
     margin-bottom:12px;
     margin-top:12px;
  } */
  

</style>
<style lang="less" scoped>


//  .hoverSteps{
//     /deep/ .ai-step__description{
//       padding-right:0 !important;
//     }
//   }
//   .stepNoBtn{
//     padding:12px 12px;
//     box-sizing: border-box;
//     margin-bottom:10px;
//     .step-title-font{
//       font-size:14px; font-weight: bold;;
//     }
//   }
//   .stepBtn{
//     box-sizing: border-box;
//     background: #fff;
//     /*width: 90%;*/
//     border-radius: 4px;
//     border: 1px solid #ebeef5;
//     line-height: 1.4;
//     box-shadow: 0 2px 12px 0 rgba(0,0,0,.1);
//     word-break: break-all;
//     .btnPosition{
//       text-align: right;
//     }
//   }
</style>