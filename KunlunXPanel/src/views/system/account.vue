<template>
  <div class="app-container">
    <div class="filter-container">
      <div class="table-list-search-wrap">
        <el-input
          class="list_search_keyword"
          v-model="listQuery.username"
          placeholder="可输入用户账号搜索"
          @keyup.enter.native="handleFilter"
        />
        <el-button  type="primary" icon="el-icon-search" @click="handleFilter">
          查询
        </el-button>
        <el-button  type="primary" icon="el-icon-refresh-right" @click="handleClear">
          重置
        </el-button>
      </div>
      <div class="table-list-wrap">
        <el-button
          class="filter-item"
          type="primary"
          icon="el-icon-plus"
          @click="handleCreate"
        >新增</el-button>
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

      <el-table-column label="用户账号" align="center">
        <template slot-scope="{row}">
          <span class="link-type" @click="handleDetail(row)">{{ row.username }}</span>
        </template>
      </el-table-column>

      <el-table-column
            prop="email"
            align="center"
            label="邮箱地址">
      </el-table-column>

      <el-table-column
            prop="phone_number"
            align="center"
            label="手机号">
      </el-table-column>

      <el-table-column
            prop="wechat_number"
            align="center"
            label="微信号">
      </el-table-column>
       <el-table-column
            prop="update_time"
            align="center"
            label="更新时间">
      </el-table-column>
      <!-- <el-table-column
            align="center"
            label="状态">
            <template slot-scope="{row}">
              <span>{{row.status==1?"正常":"冻结"}}</span>
            </template>
      </el-table-column> -->

      <el-table-column
        label="操作"
        align="center"
        width="230"
        class-name="small-padding fixed-width"
      >
        <template slot-scope="{row,$index}">
          <el-button type="primary" size="mini" @click="handleUpdate(row)">编辑</el-button>
          <el-button
            size="mini"
            type="danger"
            @click="handleDelete(row,$index)"
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
        label-width="110px"
      >
      
        <el-form-item label="用户账号:" prop="username">
          <el-input v-model="temp.username" placeholder="请输入用户账号" :disabled="dialogStatus==='detail'"/>
        </el-form-item>

        <el-form-item label="登录密码:" prop="password" v-if="dialogStatus==='create'?true:false">
          <el-input type="password" v-model="temp.password" placeholder="请输入登录密码" autocomplete='new-password' :disabled="dialogStatus==='detail'"/>
        </el-form-item>

        <el-form-item label="手机号码:" prop="phone_number" >
          <el-input v-model="temp.phone_number" placeholder="请输入手机号码" maxlength="11" show-word-limit clearable :disabled="dialogStatus==='detail' "/>
        </el-form-item>

        <el-form-item label="邮箱地址:" prop="email">
          <el-input type="email" v-model="temp.email" placeholder="请输入邮箱地址"  :disabled="dialogStatus==='detail'"/>
        </el-form-item>

        <el-form-item label="微信号:" prop="wechat_number">
          <el-input type="wechat_number" v-model="temp.wechat_number" placeholder="请输入微信号"  :disabled="dialogStatus==='detail'"/>
        </el-form-item>
        <!-- <el-form-item label="用户姓名:" prop="personCode" v-show="dialogStatus==='create'">
            <el-select
            filterable
            remote
            reserve-keyword
            @change="selectPerson"
            :remote-method="querySearchPersonAsync"
            :loading="searchLoading"
            v-model="temp.personCode"
            placeholder="请输入姓名搜索"
            :disabled="dialogStatus==='detail'" 
            >
              <el-option
                v-for="item in temp.personCodeList"
                :key="item.value"
                :label="item.label"
                :value="item.value">
              </el-option>
            </el-select>
        </el-form-item> -->

        <el-form-item label="角色分配:">
          <el-select v-model="temp.selectedroles" placeholder="请选择用户角色" :disabled="dialogStatus==='detail'">
          <el-option
            v-for="item in rolesData"
            :key="item.id"
            :label="item.roleName"
            :value="item.id">
          </el-option>
        </el-select>
        </el-form-item>

        <!-- <el-form-item label="绑定小区:">
          <el-select
            multiple
            filterable
            remote
            reserve-keyword
            :remote-method="querySearchVillageAsync"
            :loading="searchLoading"
            v-model="temp.villageCodeList"
            placeholder="请选择小区"
            :disabled="dialogStatus==='detail'"
            >
              <el-option
                v-for="item in tempVillageList"
                :key="item.villageCode"
                :label="item.name"
                :value="item.villageCode">
              </el-option>
          </el-select>

        </el-form-item> -->
        

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
 import { getAccountList,addAccount,update,delAccount} from '@/api/system/account'
 import { getAllRole } from '@/api/system/role'
// import { findPersonByName,findMobile } from "@/api/person";
 import Pagination from '@/components/Pagination' 

export default {
  name: "account",
  components: { Pagination },
  data() {
    const validateUsername = (rule, value, callback) => {
      if(!value){
        callback(new Error("请输入用户账号"));
      }
      else if (!(/^[0-9a-zA-Z_]+$/.test(value)) ) {
        callback(new Error("用户账号只能输入英文字母、数字、下划线"));
      }
      else {
        callback();
      }
    };
    const validatePassword = (rule, value, callback) => {
      if (value.length < 6) {
        callback(new Error("密码不少于6位"));
      } else {
        callback();
      }
    };
    const validateMobile = (rule, value, callback) => {
        if(this.search_person_has_m){
          callback();return
        }
        if(!value){
            callback(new Error("请输入手机号"));
        }
        else if (!(/^1[0-9]\d{9}$/.test(value)) ) {
            callback(new Error("手机号格式不对"));
        } 
        //通过验证之后,还要调取后台的一个接口,来验证手机号是否重复
        else {
          if(!this.temp.old_mobile){
            findMobile(value).then((response) => {
              if(response.result){
                callback(new Error("手机号重复"));
              }
              else{
                callback();
              }
            });
          }
          else if(this.temp.old_mobile && (this.temp.old_mobile!==value)){
            findMobile(value).then((response) => {
              if(response.result){
                callback(new Error("手机号重复"));
              }
              else{
                callback();
              }
            });
          }
          else{
            callback();
          }
        }
    };
    return {
      tableKey: 0,
      list: null,
      listLoading: true,
      searchLoading:false,
      villageList:[],
      tempVillageList:[],
      total: 0,
      listQuery: {
        pageNo: 1,
        pageSize: 10,
        username: '',
      },
      search_person_has_m:false,
      temp: {
        username: '',
        password:'',
        confirmpassword:'',
        old_mobile:'',
        mobile:'',
        personCode:'',
        avatar:'',
        selectedroles:'',
        villageCodeList:[],
        personCodeList:[],
        finalPersonArr:[]
      },
      dialogFormVisible: false,
      dialogEditVisible:false,
      dialogStatus: "",
      textMap: {
        update: "编辑用户",
        create: "新增用户",
        detail: "详情"
      },
      dialogDetail: false,
      message_tips:'',
      message_type:'',
      row:{},
      rolesData:[],
      villagesData:[],
      rules: {
        username: [
          { required: true, trigger: "blur",validator: validateUsername },
        ],
        password: [
          { required: true, trigger: "blur",validator: validatePassword },
        ],
        mobile: [
          { required: true, trigger: "blur",validator: validateMobile },
        ],
        personCode: [
          { required: true, message: "请选择姓名",trigger: "blur"},
        ],
        selectedroles:[
          { required: true, message: "请选择角色",trigger: "blur"},
        ]
      },
    };
  },
  created() {
    this.getList()
    //this.getRole()
    //this.getVillageList();
  },
  methods: {
    getVillageList(){
      this.villageList = this.$store.getters.villageList
      this.tempVillageList = this.villageList
    },
    handleFilter() {
      this.listQuery.pageNo = 1
      this.getList()
    },
    handleClear(){
      this.listQuery.username = ''
      this.listQuery.pageNo = 1
      this.getList()
    },
    getList() {
        this.listLoading = true
        let queryParam = Object.assign({}, this.listQuery)
        //模糊搜索
        getAccountList(queryParam).then(response => {
          this.list = response.list;
          this.total = response.total;
          setTimeout(() => {
            this.listLoading = false
          }, 0.5 * 1000)
        });
    },
    resetTemp() {
      this.search_person_has_m = false;
      this.temp = {
        username: '',
        password:'',
        confirmpassword:'',
        old_mobile:'',
        mobile:'',
        personCode:'',
        avatar:'',
        selectedroles:'',
        villageCodeList:[],
        personCodeList:[],
        finalPersonArr:[]
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
      // console.log(this.temp);return;
      this.$refs["dataForm"].validate((valid) => {
        if (valid) {
          const tempData = Object.assign({}, this.temp);
          tempData.confirmpassword = this.temp.password
          tempData.personCode = this.temp.personCode.split(',')[0];
          // console.log(this.temp.villageCodeList)
          // tempData.selectedroles = this.temp.selectedroles?this.temp.selectedroles.join(','):null;
          // if(this.temp.selectedroles.length==0){
          //   tempData.selectedroles = null
          // }
          // else{
          //   tempData.selectedroles = this.temp.selectedroles.join(',')
          // }
          //现在是单选框,所以是字符串,不用再转换一次
          tempData.selectedroles = this.temp.selectedroles?this.temp.selectedroles:null;
          // if(this.temp.villageCodeList.length==0){
          //   tempData.villageCodeList = null
          // }
          // else{
          //   tempData.villageCodeList = this.temp.villageCodeList.join(',')
          // }
           console.log(tempData);
          //发送接口
          addAccount(tempData).then(response=>{
            let res = response;
            if(res.code==200){
              this.getList();
              this.dialogFormVisible = false;
              this.message_tips = '新增成功';
              this.message_type = 'success';
            }
            else{
              this.message_tips = res.message;
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
      if(typeof row.selectedroles == 'object' && row.selectedroles){
        this.temp.selectedroles = row.selectedroles.join(",")
      }
      this.dialogDetail = true
    },
    handleUpdate(row) {
      this.temp = Object.assign({}, row); 
      if(typeof row.selectedroles == 'object' && row.selectedroles){
        this.temp.selectedroles = row.selectedroles.join(",")
      }
       console.log(row);
      this.dialogStatus = "update";
      this.dialogFormVisible = true;
      this.dialogDetail = false;
      this.temp.old_mobile = row.mobile;
      this.$nextTick(() => {
        this.$refs["dataForm"].clearValidate();
      });
    },
    updateData() {
      this.$refs["dataForm"].validate((valid) => {
        if (valid) {
          // const tempData = Object.assign({}, this.temp);
          // console.log(tempData);
          // tempData.selectedroles = tempData.selectedroles.join(',')
          // tempData.villageCodeList = tempData.villageCodeList.join(',')
          const tempData = Object.assign({}, this.temp);
          tempData.confirmpassword = this.temp.password
          // tempData.selectedroles = this.temp.selectedroles?this.temp.selectedroles.join(','):null;
          // console.log(this.temp.villageCodeList)
          // if(this.temp.selectedroles.length==0){
          //   tempData.selectedroles = null
          // }
          // else{
          //   tempData.selectedroles = this.temp.selectedroles.join(',')
          // }
          //现在是单选框,所以是字符串,不用再转换一次
          tempData.selectedroles = this.temp.selectedroles?this.temp.selectedroles:null;
          // if(this.temp.villageCodeList.length==0){
          //   tempData.villageCodeList = null
          // }
          // else{
          //   tempData.villageCodeList = this.temp.villageCodeList.join(',')
          // }
          update(tempData).then((response) => {
            let res = response;
            if(res.code==200){
              this.dialogFormVisible = false;
              this.message_tips = '编辑成功';
              this.message_type = 'success';
              //编辑成功后重新设置数据
              this.getList();
            }
            else{
              this.message_tips = res.message;
              this.message_type = 'error';
            }
            messageTip(this.message_tips,this.message_type);

          });
        }
      });
    },
    handleDelete(row) {
      handleCofirm("此操作将永久删除该数据, 是否继续?").then( () =>{
          delAccount(row.id).then((response)=>{
            console.log(response);
            let res = response;
            if(res.code==200){
              this.dialogFormVisible = false;
              this.message_tips = '删除成功';
              this.message_type = 'success';
              //成功后重新设置数据
              this.getList();
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
    },
    async getRole() {
      const res = await getAllRole()
      this.rolesData = res.result;
    },
    querySearchPersonAsync(queryString){
      if(queryString){
        //限制输入框的输入内容
				if(!(/^[a-zA-Z\u4e00-\u9fa5]+$/.test(queryString))){
          messageTip('姓名只能输入汉字、字母!','info');
          return;
        }
        else{
          findPersonByName(queryString).then(response => {
              let list = [{}];
              list = response.result.map(item=>{
                //组装，只需要code和name
                return {value: item.personCode+','+item.mobile, label: item.mobile+" "+item.personName};
              })
              // console.log(list);
              this.searchLoading = true;
              setTimeout(() => {
                  this.searchLoading = false;
                  this.temp.personCodeList = list.filter(item => {
                    return item.label;
                  });
              }, 200);
          })

        }
      }
    },
    selectPerson(v){
      let person_mobile_arr = v.split(',');
      if(person_mobile_arr[1]){
        this.temp.mobile = person_mobile_arr[1];
        this.search_person_has_m = true;
      }
    },
    querySearchVillageAsync(queryString){
      if(queryString){
        //限制输入框的输入内容
				if(!(/^[a-zA-Z0-9\u4e00-\u9fa5]+$/.test(queryString))){
          messageTip('小区名称只能输入汉字、字母和数字!','info');
          return;
        }
        else{
          setTimeout(() => {
              this.searchLoading = false;
              this.tempVillageList = this.villageList.filter(item => {
                return item.name.toLowerCase()
                .indexOf(queryString.toLowerCase()) > -1;
              });
          }, 200);
        }
      }
    },
  },
};
</script>