<template>
  <div class="app-container">
    <el-form ref="expandForm" :model="expandtemp" label-width="120px" :rules="rules">
        <div class="icons-container">
          <el-tabs type="border-card" v-model="activeName" @tab-click="handleClick">
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
              <el-form-item>
                <el-button type="primary" @click="showExpandInfo(expandtemp)">{{expandtemp.title}}</el-button>
              </el-form-item> 
            </el-tab-pane>
          </el-tabs>
        </div>
    </el-form>
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
    <!--扩容状态框 -->
    <el-dialog :title="job_id" :visible.sync="dialogExpondInfo" custom-class="single_dal_view" :close-on-click-modal="false" :before-close="beforeExpandDestory">
      <span v-if="expondInit">{{expand_init}}</span>
      <json-viewer :value="expondInfo" v-if="expondSatus"></json-viewer>
      <span v-if="expondResult">{{expand_end}}</span>
    </el-dialog>
  </div>
</template>

<script>
import {messageTip} from "@/utils";
import {getShardTable,getOtherShards,expandCluster,getExpandTableList,getShardsName,getEvStatus  } from '@/api/cluster/list'
import {version_arr,timestamp_arr,policy_arr} from "@/utils/global_variable"
import JsonViewer from 'vue-json-viewer'
export default {
  name: "list",
  props:{
		listsent:{typeof:Object}
		},
  components: { JsonViewer }, 
  data() { 
    const validateShardName = (rule, value, callback) => {
      if(!value){
        callback(new Error("请选择shard名称"));
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
    return{
      form: {
        name: '',
        nick_name: '',
        id: ''
      },
      activeName:'',
      shardNameList:[],
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
      listLoading: true,
      tableKey: 0,
      shardTable:[],
      shardsList:[],
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
      dialogStatus: "",
      textMap: {
        expand:'集群扩容',
        expandInfo:'集群扩容确认信息',
        outoExpand:'自动扩容'
      },
      outoExpandInfoVisible:false,
      srcShardsList:[],
      policys:policy_arr,
      dialogExpandVisible:false,
      dialogExpandInfoVisible:false,
      dialogExpondInfo:false,
      expondSatus:true,
      expand_init:'',
      expand_end:'',
      job_id:'',
      timer:null,
      expondInit:true,
      expondResult:false,
      expondInfo:'',
      rules: {
        shard_name: [
          { required: true, trigger: "blur",validator: validateShardName},
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
      }
    }
  },
  mounted(){
    //console.log(this.listsent);
    this.form.name=this.listsent.name;
    this.form.nick_name=this.listsent.nick_name;
    this.form.id=this.listsent.id;
    //获取该集群下有多少个shard
    let temp={id:this.listsent.id};
    getShardsName(temp).then((res) => {
        if(res.list.length<2){
          this.message_tips = '集群扩容必须在同一集群不同shard下操作！';
          this.message_type = 'error';
          messageTip(this.message_tips,this.message_type);
        }else{
          // this.listLoading=false;
          this.shardNameList = res.list;
          this.activeName=res.list[0].name;
          this.expandtemp.list.cluster_id=this.listsent.id;
          this.expandtemp.list.shard_id=res.list[0].id;
          this.expandtemp.list.shard_name=res.list[0].name;
          //获取shard下的table
          this.shardTable=[]
          let table={cluster_id:res.list[0].cluster_id,id:res.list[0].id}
          getShardTable(table).then((ress) => {
            if(ress.code==200){
              this.shardTable = ress.list;
              this.dialogExpandVisible=true;
              setTimeout(() => {
                this.listLoading = false
              }, 0.5 * 1000)
            }else{
              this.message_tips = ress.message;
              this.message_type = 'error';
              messageTip(this.message_tips,this.message_type);
            }
          });
        }
      });
  },
  destroyed() {
    clearInterval(this.timer)
    this.timer = null
  },
  watch: {
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
  },
  methods:{
    onSubmit(row) {
        console.log(row);
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
    showExpandInfo(row){
      console.log(row);
      if(this.expandtemp.title=='自动扩容'){
        //console.log(11);
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
    autoChangeDscShardName(value){
      for(let i=0;i<this.shardsList.length;i++){
        if(this.shardsList[i].id==value){
          this.autoexpandtemp.dst_shard_name=this.shardsList[i].name;
        }
      }
    },
    ChangeShardName(value){
      for(let i=0;i<this.shardsList.length;i++){
        if(this.shardsList[i].id==value){
          this.expandtemp.dst_shard_name=this.shardsList[i].name;
        }
      }
    },
    beforeExpandDestory(){
      clearInterval(this.timer)
      this.dialogExpondInfo=false;
      this.timer = null;
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
          if(info=='扩容'){
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
  }
};
</script>
<style scoped>
.el-tabs__content{
  height: calc(100vh - 102px);
    /* overflow-y: auto; */
}
</style>
