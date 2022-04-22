<template>
  <div class="app-container">
    <div class="filter-container">
      <div class="table-list-search-wrap">
        <el-input
          class="list_search_keyword"
          v-model="listQuery.name"
          placeholder="可输入目标名称搜索"
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
          v-if="user_name=='super_dba'"
        >新增</el-button>
        <div v-text="info" v-show="installStatus===true" class="info"></div>
      </div>
      <div class="table-list-wrap">

      </div>
    </div>

    <el-table
      :key="tableKey"
      v-loading="listLoading"
      :data="list"
      border
      highlight-current-row
      style="width: 100%;margin-bottom: 20px;">
    >
      <el-table-column
        type="index"
        align="center"
        label="序号"
        width="50">
      </el-table-column>

      <el-table-column label="目标名称" align="center">
        <template slot-scope="{row}">
          <span class="link-type" @click="handleDetail(row)">{{ row.name }}</span>
        </template>
      </el-table-column>

      <el-table-column
            prop="stype"
            align="center"
            label="类型">
      </el-table-column>
      <el-table-column
            prop="hostaddr"
            align="center"
            label="IP地址">
      </el-table-column>
      <el-table-column
            prop="port"
            align="center"
            label="端口号">
      </el-table-column>
      
      <el-table-column
        label="操作"
        align="center"
        width="300"
        class-name="small-padding fixed-width"
        v-if="user_name=='super_dba'"
      >
        <template slot-scope="{row,$index}">
          <el-button type="primary" size="mini" @click="handleUpdate(row)" v-if="user_name=='super_dba'">编辑</el-button>
          <el-button
            size="mini"
            type="danger"
            @click="handleDelete(row,$index)"
            v-if="user_name=='super_dba'"
          >删除</el-button>
          <div v-text="info" v-show="installStatus===true" class="info"></div>
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
      
        <el-form-item label="目标名称:" prop="name">
          <el-input v-model="temp.name" placeholder="请输入目标名称" :disabled="dialogStatus==='detail'||dialogStatus==='update'"/>
        </el-form-item>

        <el-form-item label="目标类型:" prop="stype" >
          <el-select v-model="temp.stype" placeholder="请选择目标类型">
            <el-option
              v-for="stype in stypelist"
              :key="stype.id"
              :label="stype.name"
              :value="stype.name">
            </el-option>
          </el-select>
        </el-form-item>

        <el-form-item label="IP地址:" prop="hostaddr">
          <el-input v-model="temp.hostaddr" placeholder="请输入IP地址" :disabled="dialogStatus==='detail'"/>
        </el-form-item>

        <el-form-item label="端口号:" prop="port">
          <el-input  v-model="temp.port" placeholder="请输入端口号"  :disabled="dialogStatus==='detail'"/>
        </el-form-item>

      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click="dialogFormVisible = false" v-show="!dialogDetail">关闭</el-button>
        <el-button type="primary" @click="dialogStatus==='create'?createData():updateData(row)"  v-show="!dialogDetail">确认</el-button>
      </div>
    </el-dialog>

  </div>
</template>

<script>
 import { messageTip,handleCofirm } from "@/utils";
//  import { getMachineList,getNodes} from '@/api/machine/list'
 import {getStorageList,addStorage,updateStorage,delStorage} from '@/api/cluster/listInterface'
 //import {pEnable} from '@/api/cluster/list'
 import {version_arr,storage_type_arr} from "@/utils/global_variable"
 import Pagination from '@/components/Pagination' 
 import { v4 as uuidv4 } from 'uuid';


export default {
  name: "account",
  components: { Pagination },
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
    const validateName = (rule, value, callback) => {
      if(!value){
        callback(new Error("请输入目标名称"));
      }else {
        callback();
      }
    };
    const validateStype = (rule, value, callback) => {
      if(!value){
        callback(new Error("请选择目标类型"));
      }else {
        callback();
      }
    };
    const validatePort = (rule, value, callback) => {
       const number= /^[0-9]*$/
      if (!value) {
        callback(new Error("请输入端口号"));
      } else if(!number.test(value)){
       callback(new Error("端口号只允许输入数字"));
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
        name: '',
      },
      temp: {
        hostaddr: '',
        name:'',
        stype:'',
        port:''
      },
      dialogFormVisible: false,
      dialogEditVisible:false,
      dialogStatus: "",
      textMap: {
        update: "编辑备份存储目标",
        create: "新增备份存储目标",
        detail: "详情"
      },
      dialogDetail: false,
      message_tips:'',
      message_type:'',
      installStatus:false,
      info:'',
      row:{},
      stypelist:storage_type_arr,
      user_name:sessionStorage.getItem('login_username'),
      rules: {
        hostaddr: [
          { required: true, trigger: "blur",validator: validateIPAddress },
        ],
        name: [
          { required: true, trigger: "blur",validator: validateName },
        ],
        stype: [
          { required: true, trigger: "blur",validator: validateStype },
        ],
        port: [
          { required: true, trigger: "blur",validator: validatePort },
        ]
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
      this.listQuery.pageNo = 1
      this.getList()
    },
    getList() {
        this.listLoading = true
        this.installStatus = false
        let queryParam = Object.assign({}, this.listQuery)
        const temp={};
        temp.version=version_arr[0].ver;
        temp.job_id=uuidv4();
        temp.job_type='get_backup_storage';
        //模糊搜索
        getStorageList(temp).then(response => {
          //console.log(response);return;
          this.list = response;
          this.total = response.length;
          setTimeout(() => {
            this.listLoading = false
          }, 0.5 * 1000)
        });
    },
    resetTemp() {
      this.temp = {
        hostaddr: '',
        name:'',
        stype:'',
        port:''
      };
    },
    handleCreate() {
      this.resetTemp();
      this.dialogStatus = "create";
      this.dialogFormVisible = true;
      this.dialogDetail = false;
      this.$nextTick(() => {
        this.$refs.dataForm.clearValidate();
      });
    },
    createData() {
      this.$refs["dataForm"].validate((valid) => {
        if (valid) {
          const tempData = Object.assign({}, this.temp);
          tempData.job_id =uuidv4();
          tempData.job_type ='create_backup_storage';
          tempData.version=version_arr[0].ver;
          //发送接口
          addStorage(tempData).then(response=>{
            let res = response;
            if(res.result=='done'){
              this.dialogFormVisible = false;
              this.message_tips = res.info;
              this.message_type = 'success';
              this.getList();
            }
            else{
              this.message_tips = res.info;
              this.message_type = 'error';
            }
            messageTip(this.message_tips,this.message_type);
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
      this.$nextTick(() => {
        this.$refs["dataForm"].clearValidate();
      });
    },
    updateData() {
      this.$refs["dataForm"].validate((valid) => {
        if (valid) {
          const tempData = Object.assign({}, this.temp);
          tempData.job_id =uuidv4();
          tempData.job_type ='update_backup_storage';
          tempData.version=version_arr[0].ver;
          updateStorage(tempData).then((response) => {
            let res = response;
            if(res.result=='done'){
              this.dialogFormVisible = false;
              this.message_tips = res.info;
              this.message_type = 'success';
              this.getList();
            }
            else{
              this.message_tips = res.info;
              this.message_type = 'error';
            }
            messageTip(this.message_tips,this.message_type);

          });
        }
      });
    },
    handleDelete(row) {
      handleCofirm("此操作将永久删除该数据, 是否继续?").then( () =>{
         const tempData = {};
          tempData.job_id =uuidv4();
          tempData.job_type ='delete_backup_storage';
          tempData.version=version_arr[0].ver;
          tempData.name=row.name;
          delStorage(tempData).then((response)=>{
            let res = response;
            if(res.result=='done'){
              this.dialogFormVisible = false;
              this.message_tips = res.info;
              this.message_type = 'success';
              this.getList();
            }
            else{
              this.message_tips = res.info;
              this.message_type = 'error';
            }
            messageTip(this.message_tips,this.message_type);
      })
      }).catch(() => {
          console.log('quxiao')
          messageTip('已取消删除','info');
      }); 
    },
  },
};
</script>