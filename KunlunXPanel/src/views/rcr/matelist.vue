<template>
  <div class="app-container">
      <el-tabs v-model="activeName">
        <el-tab-pane label="元数据列表" name="first">
          <el-table :data="list">
            <el-table-column property="name" label="元数据名称" width="150">
            </el-table-column>
            <el-table-column property="rcr_meta" label="元数据ip端口" width="200">
              <template slot-scope="{row}">
                <span class="link-type" @click="handleDetail(row)">{{ row.rcr_meta| masterMetaWrap(row.rcr_meta)}}</span>
              </template>
            </el-table-column>
            <el-table-column property="" label="操作">
              <template slot-scope="{ row }">
                <el-button type="danger" size="mini" @click="handleDeleteMeta(row)">删除</el-button>
              </template>
            </el-table-column>
          </el-table>
          <pagination v-show="total > 0" :total="total" :page.sync="listQuery.pageNo" :limit.sync="listQuery.pageSize"
      @pagination="getList" />
        </el-tab-pane>
        <el-tab-pane label="新增元数据" name="second">
              <el-form :model="from" label-width="120px" ref="from" :rules="rules">
                <el-form-item label="元数据名称:" prop="meta_name" >
                  <el-input v-model="from.meta_name" placeholder="请输入元数据名称" />
                </el-form-item>
                <el-row v-for="(table, index) in from.ips" :key="table.key">
                  <el-col :span="10">
                    <el-form-item label="ip:" :prop="'ips.' + index + '.ip'" :rules="{
                      required: true,
                      message: 'ip不能为空',
                      trigger: 'blur',
                    }">
                    <el-input v-model="from.ips[index].ip"  placeholder="输入元数据ip" />
                    </el-form-item>
                  </el-col>
                  <el-col :span="10">
                    <el-form-item label="端口号:" :prop="'ips.' + index + '.port'" :rules="{
                      required: true,
                      message: '端口号不能为空',
                      trigger: 'blur',
                    }">
                    <el-input v-model="from.ips[index].port"  placeholder="输入元数据端口号" />
                    </el-form-item>
                  </el-col>
                  <el-col :span="2">
                    <el-button v-if="index === 0" icon="el-icon-plus" size="small" @click="onPush(from.ips[index])" />
                    <el-button v-else icon="el-icon-minus" size="small" @click="onRemove(index)" />
                  </el-col>
                </el-row>
                <el-form-item>
                  <el-button type="primary" @click="submitForm('from')">保存</el-button>
                </el-form-item>
              </el-form>
        </el-tab-pane>
      </el-tabs>
  </div>
</template>
<script>
import { messageTip, createCode, gotoCofirm } from "@/utils";
import { getMetaList} from '@/api/rcr/list';
import { alarm_type_arr, alarm_level_arr } from "@/utils/global_variable";
import { getAccountList } from '@/api/system/account'
import Pagination from "@/components/Pagination";
import { findName,addMetaList,deleteMeta,findRcrRelat } from "@/api/rcr/list";

export default {
  name: "MetaList",
  components: { Pagination },
  data() {
     const validateMetaName = (rule, value, callback) => {
      if(!value){
          callback(new Error('元数据名称不能为空'))
      }else	if(!(/^[a-zA-Z0-9\u4e00-\u9fa5]+$/.test(value))){
        callback(new Error("元数据名称只能输入汉字、字母和数字"));
        return;
      }else {//验证元数据名称是否重复
        if(value){
          let temp={'name':value,'user_name': sessionStorage.getItem('login_username')}
          findName(temp).then((response) => {
            if(response.total>0){
              callback(new Error("元数据名称不能重复"));
            }
            else{
              callback();
            }
          });
        }
      }
    }
    const validateIp = (rule, value, callback) => {
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
      if(value){
        if (this.ips.ip=='') {
          callback(new Error('请填写ip'))
        } else if (!isCorrect) {
          callback(new Error('请输入正确的ip'));
        } else {
          callback()
        }
      }
    }
    const validatePort = (rule, value, callback) => {
      if(value){
        if (value.length <= 0) {
          callback(new Error('请填写端口号'))
        }else if(!(/^[0-9]+$/.test(value)) ) {
          callback(new Error("端口号只能输入数字"));
        } else {
          callback()
        }
      }
    }
    return {
      activeName: "first",
      tabPosition: 'left',
      tableKey: 0,
      list: null,
      listLoading: true,
      searchLoading: false,
      total: 0,
      listQuery: {
        pageNo: 1,
        pageSize: 10,
        user_name :sessionStorage.getItem('login_username')
      },
      alarmTypes: alarm_type_arr,
      alarmLevel: alarm_level_arr,
      dialogFormVisible: false,
      dialogEditVisible: false,
      dialogUploadVisible: false,
      dialogStatus: "",
      dialogDetail: false,
      message_tips: "",
      message_type: "",
      installStatus: false,
      dialogInfo: false,
      alarmJobInfo: "",
      info: "",
      row: {},
      timer: null,
      table: false,
      loading: false,
      from: {
        ips: [{
          ip: '',
          port:''
        }],
        meta_name: ''
      },
      rules: {
        meta_name: [
          { required: true,  trigger: 'blur' ,validator: validateMetaName}
        ],
        ip: [
          { required: true, message: '元数据ip不能为空', trigger: 'blur',validator: validateIp }
        ],
        port: [
          { required: true, message: '元数据port不能为空', trigger: 'blur',validator: validatePort }
        ]
      }
    };
  },

  created() {
    this.getList();
  },
  mounted() {
  },
  destroyed() {
    clearInterval(this.timer);
    this.timer = null;
  },
  filters: {
    masterMetaWrap(value) {
      return value.replace(eval(`/${','}/g`), `\n`)
    }
  },
  methods: {
  submitForm(formName) {
      this.$refs[formName].validate((valid) => {
        if (valid) {
          let ips ='';
           this.from.ips.map((v) => {
            ips+= v.ip+":"+v.port+',';
          });
          ips = ips.substring(0, ips.length - 1);
          const tempData={};
          tempData.user_name=sessionStorage.getItem('login_username');
          tempData.ips=ips;
          tempData.meta_name=this.from.meta_name;
          addMetaList(tempData).then(response => {
            const res = response
            if (res.code === 200) {
              this.getList()
              this.dialogFormVisible = false
              this.message_tips = '新增成功'
              this.message_type = 'success'
              this.activeName='first' 
            } else {
              this.message_tips = res.message
              this.message_type = 'error'
            }
            messageTip(this.message_tips, this.message_type)
          })
        } else {
          console.log('error submit!!');
          return false;
        }
      });
    },
    repeatObject(obj,field) {
      var count = 0;
      var len = obj.length, result = new Array(), resultList = new Array();
      for (var i = 0, x = 0; i < len; i++) {
          var id = obj[i][field];
          if (result[id]) {
              resultList[x] = id;
              count = 1, x++; 
          } else {
              result[id] = 1;
          }
      }
      if (count == 1) {
          return resultList;
      }
      return null;
    },
    handleDeleteMeta(row) {
      //先判断该元数据是否存在rcr关系，解除rcr关系才能删除
      let temp={};
      temp.meta_db = row.rcr_meta;
      findRcrRelat(temp).then((response) => {
        if(response.total>0){
          this.message_tips = "该元数据建立有RCR关系，请先解除RCR关系再删除";
          this.message_type = "error";
          messageTip(this.message_tips, this.message_type);
          return
        }else{
          const code = createCode();
          const string = "将删除该RCR的元数据信息,是否继续?code=" + code;
          gotoCofirm(string).then((res) => {
            // 先执行删权限
            if (!res.value) {
              this.message_tips = "code不能为空！";
              this.message_type = "error";
              messageTip(this.message_tips, this.message_type);
            } else if (res.value == code) {
              const tempData = {};
              tempData.id = row.id;
              deleteMeta(tempData).then((response) => {
                const res = response;
                if (res.code == 200) {
                  this.dialogFormVisible = false;
                  this.message_tips = "删除成功";
                  this.message_type = "success";
                  this.getList();
                } else {
                  this.message_tips = "删除失败";
                  this.message_type = "error";
                }
                messageTip(this.message_tips, this.message_type);
              });
            } else {
              this.message_tips = "code输入有误";
              this.message_type = "error";
              messageTip(this.message_tips, this.message_type);
            }
          }).catch(() => {
            console.log("quxiao");
            messageTip("已取消删除", "info");
          });
        }
      });
    },
    getList() {
      this.listLoading = true;
      const queryParam = Object.assign({}, this.listQuery);
      // 模糊搜索
      getMetaList(queryParam).then((response) => {
        this.list = response.list;
        this.total = response.total;
        setTimeout(() => {
          this.listLoading = false;
        }, 0.5 * 1000);
      });
    },
    onPush(row) {
      // let row=row.trim();
      // let ips=row.ip+row.port;
      this.from.ips.push({
        ip:"",
        port: "",
      });
    },
    onRemove(index) {
      this.from.ips.splice(index, 1);
    },

  },
};
</script>
<style>
.right_input {
  width: 35%;
}

#el-drawer__title {
  font-size: large;
}
</style>
