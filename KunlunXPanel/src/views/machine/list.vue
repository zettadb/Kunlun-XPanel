<template>
  <div class="app-container">
    <div class="filter-container">
      <div class="table-list-search-wrap">
        <el-input
          class="list_search_keyword"
          v-model="listQuery.hostaddr"
          placeholder="可输入ip搜索"
          @keyup.enter.native="handleFilter"
        />
        <el-button icon="el-icon-search" @click="handleFilter">
          查询
        </el-button>
        <el-button icon="el-icon-refresh-right" @click="handleClear">
          重置
        </el-button>
        <el-button
          class="filter-item"
          type="primary"
          icon="el-icon-plus"
          @click="handleCreate"
          v-if="machine_add_priv==='Y'"
        >新增</el-button>
        <!-- <el-button style="padding:0px;">
          <Upload/> 
        </el-button> -->
        <!-- <el-button
          class="filter-item"
          type="primary"
          icon="el-icon-upload2"
          @click="handleUpload"
          v-if="machine_add_priv==='Y'"
        >批量导入</el-button> -->
        <div v-text="info" v-show="installStatus===true" class="info"></div>
      </div>
      <div class="table-list-wrap"></div>
    </div>
    <el-table
    ref="multipleTable"
      :key="tableKey"
      v-loading="listLoading"
      :data="list"
      border
      highlight-current-row
      :row-class-name="colorStyle"
      style="width: 100%;margin-bottom: 20px;">
      <el-table-column
        type="index"
        align="center"
        label="序号"
        width="50">
      </el-table-column>

      <el-table-column label="IP地址" align="center">
        <template slot-scope="{row}">
          <span class="link-type" @click="handleDetail(row)">{{ row.hostaddr }}</span>
        </template>
      </el-table-column>

      <el-table-column
        prop="machine_type"
        align="center"
        label="机器类型">
        <template slot-scope="scope">
          <span v-if="scope.row.machine_type==='storage'">存储</span>
          <span v-else-if="scope.row.machine_type==='computer'">计算</span>
          <span v-else></span>
        </template>
      </el-table-column>
      <el-table-column
        prop="port_range"
        align="center"
        label="端口范围">
      </el-table-column>
      <el-table-column
        prop="used_port"
        align="center"
        :show-overflow-tooltip="true"
        label="已使用端口">
      </el-table-column>
      <el-table-column
        prop="installing_port"
        align="center"
        :show-overflow-tooltip="true"
        label="安装中端口">
      </el-table-column>
      <el-table-column
        prop="node_stats"
        align="center"
        label="状态" width="170">
        <template slot-scope="scope">
          <!-- @focus="getDatalist(scope.row.node_stats)" -->
          <!-- <el-select  ref="node_stats" v-model="scope.row.node_stats"  placeholder="请选择"  @change="chageTextColor($event,'node_stats',scope.row,scope.$index)">
            <el-option v-for="item in nodeStatsList" :key="item.id" :label="item.label" :value="item.id" v-html="'<span style=color:'+item.color+'>'+item.label+'</span>'">
            </el-option>
          </el-select> -->
          <span v-if="scope.row.node_stats==='running'" style="color: #00ed37">在线</span>
          <span v-else-if="scope.row.node_stats==='idle'" style="color: #c7c9d1;">不允许再装实例</span>
          <span v-else-if="scope.row.node_stats==='dead'" style="color: red">离线</span>
          <span v-else></span>
        </template>
      </el-table-column>
      <el-table-column
        prop="total_cpu_cores"
        align="center"
        label="cpu核数">
      </el-table-column>
      <el-table-column
        prop="total_mem"
        align="center"
        label="总内存">
        <template slot-scope="scope">{{scope.row.total_mem+'MB'}}</template>
      </el-table-column>
      <el-table-column
          prop="rack_id"
          align="center"
          label="机架编号">
      </el-table-column>
      
      <el-table-column
        label="操作"
        align="center"
        width="180"
        class-name="small-padding fixed-width">
        <template slot-scope="{row,$index}">
          <el-button type="primary" size="mini" @click="gotolink(row.hostaddr)">节点视图</el-button>
          <!-- <el-button type="primary" size="mini" @click="handleUpdate(row)"  v-if="machine_priv==='Y'">编辑</el-button> -->
          <el-button
            size="mini"
            type="danger"
            @click="handleDelete(row,$index)"
             v-if="machine_drop_priv==='Y'"
          >删除</el-button>
        </template>
      </el-table-column>
    </el-table>
    <pagination v-show="total>0" :total="total" :page.sync="listQuery.pageNo" :limit.sync="listQuery.pageSize" @pagination="getList" />

    <el-dialog :title="textMap[dialogStatus]" :visible.sync="dialogFormVisible" custom-class="single_dal_view">
      <el-form
        ref="dataForm"
        :rules="rules"
        :model="temp"
        label-position="left"
        label-width="140px"
      >
      
        <el-form-item label="IP地址:" prop="hostaddr">
          <el-input v-model="temp.hostaddr" placeholder="请输入IP地址" :disabled="dialogStatus==='detail'||dialogStatus==='update'"/>
        </el-form-item>
        <el-form-item label="机器类型:" prop="machine_type">
          <el-select v-model="temp.machine_type" placeholder="请选择机器类型" @change="ChangeSaler" :disabled="dialogStatus==='detail'||dialogStatus==='update'">
            <el-option
              v-for="item in machine_types"
              :key="item.id"
              :label="item.label"
              :value="item.id">
            </el-option>
          </el-select>
        </el-form-item>
        <el-form-item label="端口范围:" prop="port_range">
          <el-input v-model="temp.start_port" placeholder="请输入起始端口号" :disabled="dialogStatus==='detail'" class="right_input_min" /> -
          <el-input v-model="temp.end_port" placeholder="请输入结束端口号" :disabled="dialogStatus==='detail'" class="right_input_min" />
        </el-form-item>

        <el-form-item label="日志目录:" prop="logdir" v-if="storage_status">
          <el-input  v-model="temp.logdir" placeholder="请输入日志目录"  :disabled="dialogStatus==='detail'"/>
        </el-form-item>

        <el-form-item label="wal日志目录:" prop="wal_log_dir" v-if="storage_status">
          <el-input  v-model="temp.wal_log_dir" placeholder="请输入wal日志目录"  :disabled="dialogStatus==='detail'"/>
        </el-form-item>

        <el-form-item label="存储节点数据目录:" prop="datadir" v-if="storage_status">
          <el-input v-model="temp.datadir" placeholder="请输入存储节点数据目录"  :disabled="dialogStatus==='detail' "/>
        </el-form-item>

        <el-form-item label="计算节点数据目录:" prop="comp_datadir" v-if="comp_status">
          <el-input  v-model="temp.comp_datadir" placeholder="请输入计算节点数据目录"  :disabled="dialogStatus==='detail'"/>
        </el-form-item>
        <el-form-item label="总内存:" prop="total_mem">
          <el-input  v-model="temp.total_mem" placeholder="请输入总内存"  :disabled="dialogStatus==='detail'">
            <i slot="suffix" style="font-style:normal;margin-right: 10px; line-height: 30px;">MB</i>
          </el-input>
        </el-form-item>
        <el-form-item label="cpu核数:" prop="total_cpu_cores">
          <el-input  v-model="temp.total_cpu_cores" placeholder="请输入cpu核数"  :disabled="dialogStatus==='detail'"/>
        </el-form-item>
        <el-form-item label="机架编号:" prop="rack_id">
          <el-input v-model="temp.rack_id" placeholder="请输入机架编号" autocomplete='new-password' :disabled="dialogStatus==='detail'"/>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="dialogFormVisible = false" v-show="!dialogDetail">关闭</el-button>
        <el-button type="primary" @click="dialogStatus==='create'?createData():updateData(row)"  v-show="!dialogDetail">确认</el-button>
      </div>
    </el-dialog>
    <el-dialog :title="textMap[dialogStatus]" :visible.sync="dialogUploadVisible" custom-class="single_dal_view">
      <el-form
        ref="dataForm"
        :rules="rules"
        :model="temp"
        label-position="left"
        label-width="110px"
      >
      <el-form-item label="模板:">
          <el-button
        type="text"
        icon="el-icon-download"
        @click="downloadTemplate()">下载模板</el-button>
        <!-- 下载模板a标签 -->
        <!-- <a ref="downloadTemplate" style="display: none" href="./machine.xlsx"></a> -->
        </el-form-item>
      <el-form-item label="选择导入文件:" >
        <el-row>
          <div class="el-form-item__content">
            <el-upload ref="upload"
                accept=".xls,.xlsx"
                action="doUpload"
                :limit="1"
                :file-list="fileList"
                :before-upload="beforeUpload"
                >
                <el-button size="small" type="primary">选择文件</el-button>
                <div slot="tip" class="el-upload__tip">一次只能上传一个xls/xlsx文件，且不超过5M</div>
                <div slot="tip" class="el-upload-list__item-name">{{ fileName }}</div>
            </el-upload>
          </div>
        </el-row>
      </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="dialogUploadVisible = false" v-show="!dialogDetail">关闭</el-button>
        <el-button type="primary" @click="uploadDate()"  v-show="!dialogDetail">上传</el-button>
      </div>
    </el-dialog>
     <!--状态框 -->
    <el-dialog :visible.sync="dialogStatusVisible" custom-class="single_dal_view" width="400px"  :close-on-click-modal="false">
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

  <el-dialog :title="job_id" :visible.sync="dialogStatusVisible" custom-class="single_dal_view" :close-on-click-modal="false" :before-close="beforeDestory">
      <div style="width: 100%;background: #fff;padding:0 20px;">
        <el-steps direction="vertical" :active="comp_active" >
          <el-step
              v-for="(item,index) of activities"
              :key="index"
              :title="item.title"
              :icon="item.icon"
              :status="item.status"
              :description="item.description"
          >
          </el-step>
        </el-steps>
      </div>
    </el-dialog>
  </div>
</template>
<script>
 import { messageTip,handleCofirm,getNowDate } from "@/utils";
 import { getMachineList,getNodes,addMachine,delMachine,importData,setMachineStatus} from '@/api/machine/list'
 import { pgEnable,getEvStatus } from '@/api/cluster/list'
 //import {addMachine,delMachine,getEvStatus,pEnable,update} from '@/api/machine/listInterface'
 import {getAllMachineStatus} from '@/api/cluster/listInterface'
 import {version_arr,timestamp_arr,machine_type_arr,node_stats_arr} from "@/utils/global_variable"
 import Pagination from '@/components/Pagination' 
 //import { v4 as uuidv4 } from 'uuid';
 // { color } from 'echarts/lib/export';
 import Upload from '../machine/upload' 
export default {
  name: "account",
  components: { Pagination,Upload },
  data() {
   const validateIPAddress=(rule, value, callback)=>{
      let regexp = /^((2(5[0-5]|[0-4]\d))|[0-1]?\d{1,2})(\.((2(5[0-5]|[0-4]\d))|[0-1]?\d{1,2})){3}$/;
      let valdata = value.split(',');
      let isCorrect = true;
      if (valdata.length) {
        for (let i = 0; i < valdata.length; i++) {
          if (regexp.test(valdata[i]) == false) {
              isCorrect = false;
          }
        }
      }
      if (value == '') {
            return callback(new Error('请输入IP地址'));
      } else if (!isCorrect) {
            callback(new Error('请输入正确对IP地址'));
      } else {
            callback()
      }
    };
    const validateMachineType = (rule, value, callback) => {
     if(!value){
        callback(new Error("请选择机器类型"));
      }else {
        callback();
      }
    };
    const validateStartPort = (rule, value, callback) => {
     if(!value){
        callback(new Error("请输入起始端口号"));
      }else if(!(/^[0-9]+$/.test(value)) ) {
        callback(new Error("端口号只能输入数字"));
      }else if(value&&this.temp.end_port){
        if(this.temp.end_port<=value){
          callback(new Error("起始端口号不能大于等于结束端口号"));
        }else{
          callback();
        }
      }else {
        callback();
      }
    };
    const validateEndPort = (rule, value, callback) => {
     if(!value){
        callback(new Error("请输入结束端口号"));
      }else if(!(/^[0-9]+$/.test(value)) ) {
        callback(new Error("端口号只能输入数字"));
      }else if(value&&this.temp.start_port){
        if(this.temp.start_port>value){
          callback(new Error("起始端口号不能大于等于结束端口号"));
        }else{
          callback();
        }
      }else {
        callback();
      }
    };
    const validateDataDir = (rule, value, callback) => {
      if(!value){
        callback(new Error("请输入存储节点数据目录"));
      }else {
        callback();
      }
    };
    const validateLogDir = (rule, value, callback) => {
      if(!value){
        callback(new Error("请输入日志目录"));
      }else {
        callback();
      }
    };
    const validateWalLogDir = (rule, value, callback) => {
      if (!value) {
        callback(new Error("请输入wal日志目录"));
      } else {
        callback();
      }
    };
     const validateCompDataDir = (rule, value, callback) => {
      if (!value) {
        callback(new Error("请输入计算节点数据目录"));
      } else {
        callback();
      }
    };
    const validateCPUCores = (rule, value, callback) => {
      if (!value) {
        callback(new Error("请输入cpu核数"));
      }else if(!(/^[0-9]+$/.test(value)) ) {
        callback(new Error("cpu核数只能输入数字"));
      } else {
        callback();
      }
    };
    const validateTotalMem = (rule, value, callback) => {
      if (!value) {
        callback(new Error("请输入总内存"));
      }else if(!(/^[0-9]+$/.test(value)) ) {
        callback(new Error("总内存只能输入数字"));
      } else {
        callback();
      }
    };
    const validatePortRange = (rule, value, callback) => {
     if(!this.temp.start_port&&!this.temp.end_port){
        callback(new Error("端口范围不能为空"));
      }else {
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
        hostaddr: '',
      },
      machine_types:machine_type_arr,
      nodeStatsList:node_stats_arr,
      temp: {
        hostaddr: '',
        rack_id:'',
        datadir:'',
        logdir:'',
        wal_log_dir:'',
        comp_datadir:'',
        total_cpu_cores:'',
        total_mem:'',
        machine_type:'',
        start_port:'',
        end_port:'',
        port_range:''
      },
      dialogFormVisible: false,
      dialogEditVisible:false,
      dialogUploadVisible:false,
      dialogStatus: "",
      textMap: {
        update: "编辑计算机",
        create: "新增计算机",
        detail: "详情",
        upload:'批量导入'
      },
      dialogDetail: false,
      message_tips:'',
      message_type:'',
      installStatus:false,
      info:'',
      row:{},
      machine_add_priv:JSON.parse(sessionStorage.getItem('priv')).machine_add_priv,
      machine_drop_priv:JSON.parse(sessionStorage.getItem('priv')).machine_drop_priv,
      machine_priv:JSON.parse(sessionStorage.getItem('priv')).machine_priv,
      storage_status:false,
      comp_status:false,
      comp_active:1,
      job_id:null,

      file: {},
      files: {
        name: "",
        size: "",
      },
      fileName: "",
      fileList: [],
      activities: [],
      dialogStatusVisible:false,
      timer:null,
      rules: {
        hostaddr: [
          { required: true, trigger: "blur",validator: validateIPAddress },
        ],
        datadir: [
          { required: true, trigger: "blur",validator: validateDataDir },
        ],
        logdir: [
          { required: true, trigger: "blur",validator: validateLogDir },
        ],
        wal_log_dir: [
          { required: true, trigger: "blur",validator: validateWalLogDir },
        ],
        comp_datadir: [
          { required: true, trigger: "blur",validator: validateCompDataDir },
        ],
        total_mem: [
          { required: true, trigger: "blur",validator: validateTotalMem },
        ],
        total_cpu_cores: [
          { required: true, trigger: "blur",validator: validateCPUCores },
        ],
        machine_type:[
          { required: true, trigger: "blur",validator: validateMachineType },
        ],
        port_range:[
          { required: true, trigger: "blur",validator: validatePortRange },
        ],
        start_port:[
          { required: true, trigger: "blur",validator: validateStartPort },
        ],
        end_port:[
          { required: true, trigger: "blur",validator: validateEndPort },
        ],
      },
    };
  },
  created() {
    this.getList()
  },
  methods:{
    colorStyle({row,rowIndex}){
      this.$nextTick(()=>{ 
        // this.$refs.multipleTable.$el.children[2].children[0].children[1].children[rowIndex].children[6].children[0].children[0].children[0].children[0].style.color=''+ row.node_stats_color+'';
      });
    },
    chageTextColor($event, selectedRef,row,index) {
       //设置机器的状态
        // const temp={};
        // const paras={};
        // temp.user_name=sessionStorage.getItem('login_username');
        // temp.job_id='';
        // temp.version=version_arr[0].ver;
        // temp.timestamp=timestamp_arr[0].time+'';
        // temp.job_type='set_machine';
        // //必填项
        // paras.hostaddr=row.hostaddr;
        // paras.node_stats=row.node_stats;
        // temp.paras=paras;
        // setMachineStatus(temp).then(response=>{
        //   let res = response;
        //   if(res.status=='accept'){
        //   }else if(res.status=='ongoing'){
        //     this.message_tips = '系统正在操作中，请等待一会！';
        //     this.message_type = 'error';
        //     messageTip(this.message_tips,this.message_type);
        //   }else{
        //     this.message_tips = res.error_info;
        //     this.message_type = 'error';
        //     messageTip(this.message_tips,this.message_type);
        //   }
        // })
      let color = this.getColor($event)
      // 改变下拉框颜色值
      //console.log(this.$refs.multipleTable.$el.children[2].children[0].children[1].children[index].children[6].children[0].children[0].children[0].children[0].style.color)
      //this.$refs[selectedRef].$el.children[0].children[0].style.color = '' + color + ''
      //this.$nextTick(()=>{ 
        //console.log(this.$refs.multipleTable.$el.children[2].children[0].children[1].children[index].children[6].children[0].children[0].children[0].children[0].style.color);
        this.$refs.multipleTable.$el.children[2].children[0].children[1].children[index].children[6].children[0].children[0].children[0].children[0].style.color=''+color+'';
        //console.log(this.$refs.multipleTable.$el.children[2].children[0].children[1].children[index].children[6].children[0].children[0].children[0].children[0].style.color);
      //});
      
    },
    getColor(value) {
      for (const i in this.nodeStatsList) {
        if (this.nodeStatsList[i].id === value) {
          return this.nodeStatsList[i].color
        }
      }
    },
  //清除定时器
  beforeDestory(){
    clearInterval(this.timer)
    this.dialogStatusVisible=false;
  },
  beforeUpload(file){
      console.log(file, "文件");
      this.files = file;
      const extension = file.name.split(".")[1] === "xls";
      const extension2 = file.name.split(".")[1] === "xlsx";
      const isLt2M = file.size / 1024 / 1024 < 5;
      if (!extension && !extension2) {
        this.$message.warning("上传模板只能是xls、xlsx格式!");
        return;
      }
      if (!isLt2M) {
        this.$message.warning("上传模板大小不能超过5MB!");
        return;
      }
      this.fileName = file.name;
      return false; // 返回false不会自动上传
    },

    uploadDate() {
      console.log("上传" + this.files.name);
      if (this.fileName == "") {
        this.$message.warning("请选择要上传的文件！");
        return false;
      }
      let fileFormData = new FormData();
      fileFormData.append("fileName", this.fileName);
      fileFormData.append("file", this.files);
      //也可以使用axios插件直接请求
      // this.$http.post(`dev-api/school/base/importData`, fileFormData)
      this.message_tips = '正在导入数据';
      this.message_type = 'success';
      messageTip(this.message_tips ,this.message_type);
      importData(fileFormData).then((response) => {
        this.dialogUploadVisible=false;
        this.message_tips = '正在导入...';
        this.message_type = 'success';
        if(response.code==200){
          this.message_tips = response.message;
          this.message_type = 'success';
        }
        messageTip(this.message_tips ,this.message_type);
        //this.$modal.msgSuccess("导入成功");
        this.getList();
      });
    },
    //下载模板
    downloadTemplate(){
      var a = document.createElement("a"); //创建一个<a></a>标签
      a.href = "./machine.xlsx"; // 给a标签的href属性值加上地址，注意，这里是绝对路径，不用加 点.
      a.download = "machine.xlsx"; //设置下载文件文件名，这里加上.xlsx指定文件类型，pdf文件就指定.fpd即可
      a.style.display = "none"; // 障眼法藏起来a标签
      document.body.appendChild(a); // 将a标签追加到文档对象中
      a.click(); // 模拟点击了a标签，会触发a标签的href的读取，浏览器就会自动下载了
      a.remove(); // 一次性的，用完就删除a标签
      //this.$refs.downloadTemplate.dispatchEvent(new MouseEvent('click')) 
    },
    ChangeSaler(value){
      if(value=='storage'){
          this.temp.start_port='57000';
          this.temp.end_port='58000';
          this.storage_status=true;
          this.comp_status=false;
        }else if(value=='computer'){
          this.temp.start_port='47000';
          this.temp.end_port='48000';
          this.storage_status=false;
          this.comp_status=true;
        }
    },
    handleFilter() {
      this.listQuery.pageNo = 1
      this.getList()
    },
    handleClear(){
      this.listQuery.hostaddr = ''
      this.listQuery.pageNo = 1
      this.getList()
    },
    getList() {
      this.listLoading = true
      let queryParam = Object.assign({}, this.listQuery)
      //模糊搜索
      getMachineList(queryParam).then(response => {
        //机器的状态
        const tempData = {};
        tempData.job_id ='';
        tempData.job_type ='get_machine_summary';
        tempData.version=version_arr[0].ver;
        tempData.timestamp =timestamp_arr[0].time+'';
        tempData.paras={};
        // getAllMachineStatus(tempData).then(res => {
        //   const res_status=res.attachment.list_machine;
        //   for(let i=0;i<response.total;i++){
        //     const arr=response.list[i];
        //     for (let j = 0; j < res_status.length; j++) {
        //       const ip=res_status[j].hostaddr;
        //       if(ip==arr.hostaddr){
        //         this.$set(arr, 'status', res_status[j].status)
        //       }
        //     }
        //   }
        // })
        this.list = response.list;
        this.total = response.total;
        setTimeout(() => {
          this.listLoading = false
        }, 0.5 * 1000)
      });
    },
    resetTemp() {
      this.temp = {
        hostaddr: '',
        rack_id:'',
        datadir:'',
        logdir:'',
        wal_log_dir:'',
        comp_datadir:'',
        total_cpu_cores:'',
        total_mem:'',
        machine_type:'',
        start_port:'',
        end_port:'',
        port_range:''
      };
    },
    handleCreate() {
      this.resetTemp();
      this.dialogStatus = "create";
      this.dialogFormVisible = true;
      this.dialogDetail = false;
      this.storage_status=false,
      this.comp_status=false,
      this.$nextTick(() => {
        this.$refs.dataForm.clearValidate();
      });
    },
    createData() {
      this.$refs["dataForm"].validate((valid) => {
        if (valid) {
          const tempData = {};
          tempData.user_name = sessionStorage.getItem('login_username');
          tempData.job_id ='';
          tempData.job_type ='create_machine';
          tempData.version=version_arr[0].ver;
          tempData.timestamp=timestamp_arr[0].time+'';
          this.temp.port_range=this.temp.start_port+'-'+this.temp.end_port;
          if(!this.temp.rack_id){
            this.temp.rack_id='0'
          }
          this.$delete(this.temp,'start_port');
          this.$delete(this.temp,'end_port');
          tempData.paras=Object.assign({}, this.temp);
          //发送接口
          addMachine(tempData).then(response=>{
            let res = response;
            //同步接口
            // this.message_tips = '正在新增计算机...';
            // this.message_type = 'success';
            // if(res.error_code=='0'){
            //   this.dialogFormVisible = false;
            //   this.message_tips = '新增计算机成功';
            //   this.message_type = 'success';
            //   this.dialogFormVisible = false;
            //   this.getList();
            if(res.status=='accept'){
              this.dialogFormVisible = false;
              this.job_id='新增计算机('+res.job_id+')';
              this.dialogStatusVisible=true;
              this.activities=[];
              const newArr={
                title:'正在新增计算机',
                status: 'process',
                icon: 'el-icon-loading',
                description:''
              };
              this.activities.push(newArr)
              let i=0;
              let info='新增';
              let timer = setInterval(() => {
                this.getStatus(timer,res.job_id,i++,info)
              }, 1000)

              //重启promentheus
              // const prometheus = {};
              // prometheus['job_id'] = '';
              // prometheus['job_type']='update_prometheus';
              // prometheus['version']=version_arr[0].ver;
              // prometheus['timestamp']=timestamp_arr[0].time+'';
              // prometheus['user_name']=sessionStorage.getItem('login_username');
              // prometheus['paras']={};
              // pgEnable(prometheus).then((resp) => {
              //   // if(res.result=='accept'){
              //   //   let j=0;
              //   //   let timerp = setInterval(() => {
              //   //   this.getStatus(timerp,prometheus['job_id'],j++)
              //   //   }, 1000)
              //   // }
              // })
            }
            else{
              this.message_tips = res.error_info;
              this.message_type = 'error';
              messageTip(this.message_tips,this.message_type);
            }
            //messageTip(this.message_tips,this.message_type);
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
      this.temp = Object.assign({}, row); 
      this.dialogStatus = "update";
      this.dialogFormVisible = true;
      this.dialogDetail = false;
      this.temp.start_port = row.port_range.split('-')[0];
      this.temp.end_port = row.port_range.split('-')[1];
      this.$nextTick(() => {
        this.$refs["dataForm"].clearValidate();
      });
    },
    updateData() {
      this.$refs["dataForm"].validate((valid) => {
        if (valid) {
          const tempData = {};
          tempData.user_name = sessionStorage.getItem('login_username');
          tempData.job_id ='';
          tempData.job_type ='update_machine';
          tempData.version=version_arr[0].ver;
          tempData.timestamp=timestamp_arr[0].time+'';
          this.temp.port_range=this.temp.start_port+'-'+this.temp.end_port;
          if(!this.temp.rack_id){
            this.temp.rack_id='0';
          }
          this.$delete(this.temp,'start_port');
          this.$delete(this.temp,'end_port');
          tempData.paras=Object.assign({}, this.temp);
          update(tempData).then((response) => {
            let res = response;
            this.message_tips = '正在编辑计算机...';
            this.message_type = 'success';
            if(res.error_code=='0'){
              this.dialogFormVisible = false;
              this.message_tips = '编辑计算机成功';
              this.message_type = 'success';
              // let i=0;
              // let timer = setInterval(() => {
              //   this.getStatus(timer,res.job_id,i++)
              // }, 1000)
              //重启promentheus
              // const prometheus = {};
              // prometheus['job_id'] = '';
              // prometheus['job_type']='update_prometheus';
              // prometheus['version']=version_arr[0].ver;
              // prometheus['timestamp']=timestamp_arr[0].time+'';
              // prometheus['user_name']=sessionStorage.getItem('login_username');
              // prometheus['paras']={};
              // pgEnable(prometheus).then((resp) => {
              //   // if(resp.code==200){
              //   //   let j=0;
              //   //   let timerp = setInterval(() => {
              //   //   this.getStatus(timerp,prometheus['job_id'],j++)
              //   //   }, 1000)
              //   // }
              // })

              //this.dialogFormVisible = false
              //this.getList();
            }
            else{
              this.message_tips = res.error_info;
              this.message_type = 'error';
              messageTip(this.message_tips,this.message_type);
            }

          });
        }
      });
    },
    handleDelete(row) {
      if(!row.used_port||row.used_port==='NULL'||row.used_port.length==0||row.used_port==='null'){
        handleCofirm("此操作将永久删除该数据, 是否继续?").then( () =>{
         const tempData = {};
          tempData.user_name = sessionStorage.getItem('login_username');
          tempData.job_id ='';
          tempData.job_type ='delete_machine';
          tempData.version=version_arr[0].ver;
          tempData.timestamp=timestamp_arr[0].time+'';
          tempData.paras={"hostaddr":row.hostaddr,"machine_type":row.machine_type};
          //console.log(tempData);return;
          delMachine(tempData).then((response)=>{
            let res = response;
            //同步接口
            // this.message_tips = '正在删除计算机...';
            // this.message_type = 'success';
            // if(res.error_code=='0'){
            //   this.dialogFormVisible = false;
            //   this.message_tips = '删除计算机成功';
            //   this.message_type = 'success';
            //   this.getList();
            //异步接口
            if(res.status=='accept'){
              this.dialogFormVisible = false;
              this.job_id='删除计算机('+res.job_id+')';
              this.dialogStatusVisible=true;
              this.activities=[];
             const newArr={
                title:'正在删除计算机',
                status: 'process',
                icon: 'el-icon-loading',
                description:''
              };
              this.activities.push(newArr)
              let info='删除';
              let i=0;
              let timer = setInterval(() => {
                this.getStatus(timer,res.job_id,i++,info)
              }, 1000)
                //重启promentheus
              // const prometheus = {};
              // prometheus['job_id'] = '';
              // prometheus['job_type']='update_prometheus';
              // prometheus['version']=version_arr[0].ver;
              // prometheus['timestamp']=timestamp_arr[0].time+'';
              // prometheus['user_name']=sessionStorage.getItem('login_username');
              // prometheus['paras']={};
              // pgEnable(prometheus).then((resp) => {
              //   // if(resp.code==200){
              //   //   let j=0;
              //   //   let timerp = setInterval(() => {
              //   //   this.getStatus(timerp,prometheus['job_id'],j++)
              //   //   }, 1000)
              //   // }
              // })
              
            }else{
              this.message_tips = res.error_info;
              this.message_type = 'error';
              messageTip(this.message_tips,this.message_type);
            }
            //messageTip(this.message_tips,this.message_type);
        })
        }).catch(() => {
            console.log('quxiao')
            messageTip('已取消删除','info');
        }); 
      }else{
        this.message_tips = '请先清空该计算机已经存在的实例，再进行删除操作';
        this.message_type = 'error';
        messageTip(this.message_tips,this.message_type);
      }
      
    },
    gotolink(hostaddr){
      //先查看该计算机机上是否存在节点信息
        getNodes(hostaddr).then(response => {
          let total = response.total;
          if(total==0){
            messageTip('该计算机上还没有节点','error');
          }else{
            this.$router.push({
              path: '/machine/node',
              query: {
                  hostaddr: hostaddr
              }
            })
          }
        });
    },
    handleUpload() {
      this.dialogStatus = "upload";
      this.dialogUploadVisible = true;
      this.dialogDetail = false;
      this.$nextTick(() => {
        this.$refs.dataForm.clearValidate();
      });
    },
    getStatus (timer,data,i,info) {
      setTimeout(()=>{
        const postarr={};
        postarr.job_type='get_status';
        postarr.version=version_arr[0].ver;
        postarr.job_id=data;
        postarr.timestamp=timestamp_arr[0].time+'';
        postarr.paras={}
        getEvStatus(postarr).then((res) => {
        if(res.status=='done'||res.status=='failed'){
          clearInterval(timer);
          if(res.status=='done'){
            this.activities[0].title=info+'计算机成功'
            this.activities[0].icon='el-icon-circle-check'
            this.activities[0].status='success'
            this.getList();
          }else{
            this.activities[0].title=info+'计算机失败'
            this.activities[0].icon='el-icon-circle-close'
            this.activities[0].status='error'
          }
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
<style >
.right_input_min{
  width:25%;
}
/* .el-table .el-input__inner{
  color: #00ed37;
} */
/* .el-table .el-input__inner .cell-green{
  color: #00ed37;
}
 .el-table .el-input__inner .cell-red{
  color: red;
}
 .el-table .el-input__inner .cell-grey{
  color: #c7c9d1;
} */
</style>