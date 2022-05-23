<template>
  <div class="app-container">
    <div class="filter-container">
      <div class="table-list-search-wrap">
        <el-date-picker
          v-model="listQuery.startTime"
          type="datetime"
          value-format="yyyy-MM-dd HH:mm:ss"
          placeholder="起始时间" style="width:200px;margin-right:5px" >
        </el-date-picker>-

        <el-date-picker
          v-model="listQuery.endTime"
          type="datetime"
          value-format="yyyy-MM-dd HH:mm:ss"
          placeholder="结束时间" style="width:200px;margin-right:10px">
        </el-date-picker>
        <el-input
          class="list_search_keyword"
          v-model="listQuery.hostaddr"
          placeholder="可输入原IP地址搜索"
          @keyup.enter.native="handleFilter"
        />
        <el-button icon="el-icon-search" @click="handleFilter">
          查询
        </el-button>
        <el-button icon="el-icon-refresh-right" @click="handleClear">
          重置
        </el-button>
        <!-- <el-button
          class="filter-item"
          type="primary"
          icon="el-icon-plus"
          @click="handleCreate"
          v-if="machine_add_priv==='Y'"
        >新增</el-button> -->
        <div v-text="info" v-show="installStatus===true" class="info"></div>
      </div>
      <div class="table-list-wrap"></div>
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

      <el-table-column label="原IP地址" align="center">
        <template slot-scope="{row}">
          <span class="link-type" @click="handleDetail(row)">{{ row.host }}</span>
        </template>
      </el-table-column>

      <el-table-column
        prop="new_master_host"
        align="center"
        label="新IP地址">
      </el-table-column>
      <el-table-column
        prop="start_time"
        align="center"
        label="开始时间">
      </el-table-column>
      <!-- <el-table-column
        prop="end_time"
        align="center"
        label="结束时间">
      </el-table-column> -->
      <el-table-column
        prop="count_time"
        align="center"
        label="耗时">
      </el-table-column>
      <el-table-column
        prop="status"
        align="center"
        label="状态">
         <template scope="scope">
            <span v-if="scope.row.status==='成功'" style="color: #00ed37">成功</span>
            <span v-else-if="scope.row.status==='失败'" style="color: red">失败</span>
        </template>
      </el-table-column>
      <el-table-column
        prop="err_msg"
        align="center"
        :show-overflow-tooltip="true"
        label="结果信息">
      </el-table-column>
      <!-- <el-table-column
        label="操作"
        align="center"
        width="300"
        class-name="small-padding fixed-width"
      >
        <template slot-scope="{row,$index}">
          <el-button type="primary" size="mini" @click="handleUpdate(row)"  v-if="machine_priv==='Y'">编辑</el-button>
          <el-button
            size="mini"
            type="danger"
            @click="handleDelete(row,$index)"
             v-if="machine_drop_priv==='Y'"
          >删除</el-button>
        </template>
      </el-table-column> -->
    </el-table>

    <pagination v-show="total>0" :total="total" :page.sync="listQuery.pageNo" :limit.sync="listQuery.pageSize" @pagination="getList" :page-sizes="[10, 20, 30, 40, 50]" />
<!-- 
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

        <el-form-item label="机架编号:" prop="rack_id">
          <el-input v-model="temp.rack_id" placeholder="请输入机架编号" autocomplete='new-password' :disabled="dialogStatus==='detail'"/>
        </el-form-item>

        <el-form-item label="数据目录:" prop="datadir" >
          <el-input v-model="temp.datadir" placeholder="请输入数据目录"  :disabled="dialogStatus==='detail' "/>
        </el-form-item>

        <el-form-item label="日志目录:" prop="logdir">
          <el-input  v-model="temp.logdir" placeholder="请输入日志目录"  :disabled="dialogStatus==='detail'"/>
        </el-form-item>

        <el-form-item label="wal日志目录:" prop="wal_log_dir">
          <el-input  v-model="temp.wal_log_dir" placeholder="请输入wal日志目录"  :disabled="dialogStatus==='detail'"/>
        </el-form-item>

        <el-form-item label="计算节点数据目录:" prop="comp_datadir">
          <el-input  v-model="temp.comp_datadir" placeholder="请输入计算节点数据目录"  :disabled="dialogStatus==='detail'"/>
        </el-form-item>
        <el-form-item label="总内存:" prop="total_mem">
          <el-input  v-model="temp.total_mem" placeholder="请输入总内存"  :disabled="dialogStatus==='detail'"/>
        </el-form-item>
        <el-form-item label="cpu核数:" prop="total_cpu_cores">
          <el-input  v-model="temp.total_cpu_cores" placeholder="请输入cpu核数"  :disabled="dialogStatus==='detail'"/>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="dialogFormVisible = false" v-show="!dialogDetail">关闭</el-button>
        <el-button type="primary" @click="dialogStatus==='create'?createData():updateData(row)"  v-show="!dialogDetail">确认</el-button>
      </div>
    </el-dialog> -->
  </div>
</template>
<script>
 import { messageTip,handleCofirm } from "@/utils";
 import { getSwitcheOverList,getNodes} from '@/api/cluster/list'
 import {addMachine,delMachine,getEvStatus,pEnable,update} from '@/api/machine/listInterface'
 import {getAllMachineStatus} from '@/api/cluster/listInterface'
 import {version_arr} from "@/utils/global_variable"
 import Pagination from '@/components/Pagination' 
 import { v4 as uuidv4 } from 'uuid';
export default {
  name: "account",
  components: { Pagination },
  data() {
  //  const validateIPAddress=(rule, value, callback)=>{
  //     let regexp = /^((2(5[0-5]|[0-4]\d))|[0-1]?\d{1,2})(\.((2(5[0-5]|[0-4]\d))|[0-1]?\d{1,2})){3}$/;
  //     let valdata = value.split(',');
  //     let isCorrect = true;
  //     if (valdata.length) {
  //       for (let i = 0; i < valdata.length; i++) {
  //         if (regexp.test(valdata[i]) == false) {
  //             isCorrect = false;
  //         }
  //       }
  //     }
  //     if (value == '') {
  //           return callback(new Error('请输入IP地址'));
  //     } else if (!isCorrect) {
  //           callback(new Error('请输入正确对IP地址'));
  //     } else {
  //           callback()
  //     }
  //   };
  //   const validateDataDir = (rule, value, callback) => {
  //     if(!value){
  //       callback(new Error("请输入数据目录"));
  //     }else {
  //       callback();
  //     }
  //   };
  //   const validateLogDir = (rule, value, callback) => {
  //     if(!value){
  //       callback(new Error("请输入日志目录"));
  //     }else {
  //       callback();
  //     }
  //   };
  //   const validateWalLogDir = (rule, value, callback) => {
  //     if (!value) {
  //       callback(new Error("请输入wal日志目录"));
  //     } else {
  //       callback();
  //     }
  //   };
  //    const validateCompDataDir = (rule, value, callback) => {
  //     if (!value) {
  //       callback(new Error("请输入计算节点数据目录"));
  //     } else {
  //       callback();
  //     }
  //   };
  //   const validateCPUCores = (rule, value, callback) => {
  //     if (!value) {
  //       callback(new Error("请输入cpu核数"));
  //     }else if(!(/^[0-9]+$/.test(value)) ) {
  //       callback(new Error("cpu核数只能输入数字"));

  //     } else {
  //       callback();
  //     }
  //   };
  //   const validateTotalMem = (rule, value, callback) => {
  //     if (!value) {
  //       callback(new Error("请输入总内存"));
  //     }else if(!(/^[0-9]+$/.test(value)) ) {
  //       callback(new Error("总内存只能输入数字"));

  //     } else {
  //       callback();
  //     }
  //   };
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
        startTime:'',
        endTime:''

      },
      temp: {
        hostaddr: '',
        rack_id:'',
        datadir:'',
        logdir:'',
        wal_log_dir:'',
        comp_datadir:'',
        total_cpu_cores:'',
        total_mem:''
      },
      dialogFormVisible: false,
      dialogEditVisible:false,
      dialogStatus: "",
      textMap: {
        detail: "详情"
      },
      dialogDetail: false,
      message_tips:'',
      message_type:'',
      installStatus:false,
      info:'',
      row:{},
      // machine_add_priv:JSON.parse(sessionStorage.getItem('priv')).machine_add_priv,
      // machine_drop_priv:JSON.parse(sessionStorage.getItem('priv')).machine_drop_priv,
      // machine_priv:JSON.parse(sessionStorage.getItem('priv')).machine_priv,
      
      rules: {
      //   hostaddr: [
      //     { required: true, trigger: "blur",validator: validateIPAddress },
      //   ],
      //   datadir: [
      //     { required: true, trigger: "blur",validator: validateDataDir },
      //   ],
      //   logdir: [
      //     { required: true, trigger: "blur",validator: validateLogDir },
      //   ],
      //   wal_log_dir: [
      //     { required: true, trigger: "blur",validator: validateWalLogDir },
      //   ],
      //   comp_datadir: [
      //     { required: true, trigger: "blur",validator: validateCompDataDir },
      //   ],
      //   total_mem: [
      //     { required: true, trigger: "blur",validator: validateTotalMem },
      //   ],
      //   total_cpu_cores: [
      //     { required: true, trigger: "blur",validator: validateCPUCores },
      //   ],
      },
    };
  },
  created() {
    this.getList()
  },
  methods: {
    handleFilter() {
      this.listQuery.pageNo = 1
      this.getList()
    },
    handleClear(){
      this.listQuery.hostaddr = ''
      this.listQuery.startTime = ''
      this.listQuery.endTime = ''
      this.listQuery.pageNo = 1
      this.getList()
    },
    getList() {
        this.listLoading = true
        let queryParam = Object.assign({}, this.listQuery)
        //模糊搜索
        getSwitcheOverList(queryParam).then(response => {
          this.list = response.list;
          this.total = response.total;
           setTimeout(() => {
            this.listLoading = false
          }, 0.5 * 1000)
        });
    },
    // resetTemp() {
    //   this.temp = {
    //     hostaddr: '',
    //     rack_id:'',
    //     datadir:'',
    //     logdir:'',
    //     wal_log_dir:'',
    //     comp_datadir:'',
    //     total_cpu_cores:'',
    //     total_mem:''
    //   };
    // },
    // handleCreate() {
    //   this.resetTemp();
    //   this.dialogStatus = "create";
    //   this.dialogFormVisible = true;
    //   this.dialogDetail = false;
    //   this.$nextTick(() => {
    //     this.$refs.dataForm.clearValidate();
    //   });
    // },
    // createData() {
    //   this.$refs["dataForm"].validate((valid) => {
    //     if (valid) {
    //       const tempData = Object.assign({}, this.temp);
    //       tempData.user_name = sessionStorage.getItem('login_username');
    //       tempData.job_id =uuidv4();
    //       tempData.job_type ='create_machine';
    //       tempData.ver=version_arr[0].ver;
    //       //发送接口
    //       addMachine(tempData).then(response=>{
    //         let res = response;
    //         if(res.result=='accept'){
    //           this.dialogFormVisible = false;
    //           this.message_tips = '正在新增计算机...';
    //           this.message_type = 'success';
    //           let i=0;
    //           let timer = setInterval(() => {
    //             this.getStatus(timer,tempData.job_id,i++)
    //           }, 1000)

    //           //重启promentheus
    //           const prometheus = {};
    //           prometheus['job_id'] = uuidv4();
    //           prometheus['job_type']='update_prometheus';
    //           prometheus['ver']=version_arr[0].ver;
    //           prometheus['user_name']=sessionStorage.getItem('login_username');
    //           pEnable(prometheus).then((resp) => {
    //             // if(res.result=='accept'){
    //             //   let j=0;
    //             //   let timerp = setInterval(() => {
    //             //   this.getStatus(timerp,prometheus['job_id'],j++)
    //             //   }, 1000)
    //             // }
    //           })
    //         }
    //         else{
    //           this.message_tips = res.result;
    //           this.message_type = 'error';
    //         }
    //         messageTip(this.message_tips,this.message_type);
    //       })
    //     }
    //   });
    // },
    handleDetail(row){
      this.dialogStatus = "detail"
      this.dialogFormVisible = true
      this.temp = Object.assign({}, row);
      this.dialogDetail = true
    },
    // handleUpdate(row) {
    //   this.temp = Object.assign({}, row); 
    //   this.dialogStatus = "update";
    //   this.dialogFormVisible = true;
    //   this.dialogDetail = false;
    //   this.temp.old_mobile = row.mobile;
    //   this.$nextTick(() => {
    //     this.$refs["dataForm"].clearValidate();
    //   });
    // },
    // updateData() {
    //   this.$refs["dataForm"].validate((valid) => {
    //     if (valid) {
    //       const tempData = Object.assign({}, this.temp);
    //       tempData.user_name = sessionStorage.getItem('login_username');
    //       tempData.job_id =uuidv4();
    //       tempData.job_type ='update_machine';
    //       tempData.ver=version_arr[0].ver;
    //       update(tempData).then((response) => {
    //         let res = response;
    //         if(res.result=='accept'){
    //           this.dialogFormVisible = false;
    //           this.message_tips = '正在编辑计算机...';
    //           this.message_type = 'success';
    //           let i=0;
    //           let timer = setInterval(() => {
    //             this.getStatus(timer,tempData.job_id,i++)
    //           }, 1000)
    //           //重启promentheus
    //           const prometheus = {};
    //           prometheus['job_id'] = uuidv4();
    //           prometheus['job_type']='update_prometheus';
    //           prometheus['ver']=version_arr[0].ver;
    //           prometheus['user_name']=sessionStorage.getItem('login_username');
    //           pEnable(prometheus).then((resp) => {
    //             if(resp.code==200){
    //               let j=0;
    //               let timerp = setInterval(() => {
    //               this.getStatus(timerp,prometheus['job_id'],j++)
    //               }, 1000)
    //             }
    //           })
    //         }
    //         else{
    //           this.message_tips = res.result;
    //           this.message_type = 'error';
    //         }
    //         messageTip(this.message_tips,this.message_type);

    //       });
    //     }
    //   });
    // },
    // handleDelete(row) {
    //   handleCofirm("此操作将永久删除该数据, 是否继续?").then( () =>{
    //      const tempData = {};
    //       tempData.user_name = sessionStorage.getItem('login_username');
    //       tempData.job_id =uuidv4();
    //       tempData.job_type ='delete_machine';
    //       tempData.ver=version_arr[0].ver;
    //       tempData.hostaddr=row.hostaddr;
    //       delMachine(tempData).then((response)=>{
    //         let res = response;
    //         if(res.result=='accept'){
    //           this.dialogFormVisible = false;
    //           this.message_tips = '正在删除计算机...';
    //           this.message_type = 'success';
    //           let i=0;
    //           let timer = setInterval(() => {
    //             this.getStatus(timer, tempData.job_id,i++)
    //           }, 1000)
    //           //重启promentheus
    //           const prometheus = {};
    //           prometheus['job_id'] = uuidv4();
    //           prometheus['job_type']='update_prometheus';
    //           prometheus['ver']=version_arr[0].ver;
    //           prometheus['user_name']=sessionStorage.getItem('login_username');
    //           pEnable(prometheus).then((resp) => {
    //             if(resp.code==200){
    //               let j=0;
    //               let timerp = setInterval(() => {
    //               this.getStatus(timerp,prometheus['job_id'],j++)
    //               }, 1000)
    //             }
    //           })
    //         }
    //         else{
    //           this.message_tips = res.result;
    //           this.message_type = 'error';
    //         }
    //         messageTip(this.message_tips,this.message_type);
    //   })
    //   }).catch(() => {
    //       console.log('quxiao')
    //       messageTip('已取消删除','info');
    //   }); 
    // },
    // getStatus (timer,data,i) {
    //   setTimeout(()=>{
    //     const postarr={};
    //     postarr.job_type='get_status';
    //     postarr.ver=version_arr[0].ver;
    //     postarr.job_id=data;
    //     getEvStatus(postarr).then((res) => {
    //     if(res.result=='done'||res.result=='failed'){
    //       clearInterval(timer);
    //       this.info=res.info;
    //       if(res.result=='done'){
    //         this.getList();
    //       }else{
    //         this.installStatus = true;
    //       }
    //     }else{
    //       this.info=res.info;
    //       this.installStatus = true;
    //     }
    //   });
    //     if(i>=86400){
    //         clearInterval(timer);
    //     }
    //   }, 0)
    // }

  },
};
</script>