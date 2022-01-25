<template>
  <div class="app-container">
    <div class="filter-container">

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
      row-key="id"
      border
      highlight-current-row
      style="width: 100%;margin-bottom: 20px;"
      :tree-props="{children: 'children', hasChildren: 'hasChildren'}">
    >
      <el-table-column
        type="index"
        align="center"
        label="序号"
        width="50">
      </el-table-column>

      <el-table-column label="菜单名称" width="180px" align="center">
        <template slot-scope="{row}">
          <span class="link-type" @click="handleDetail(row)">{{ row.name }}</span>
        </template>
      </el-table-column>

      <el-table-column
            min-width="200px"
            prop="icon"
            align="center"
            label="菜单图标">
      </el-table-column>

      <el-table-column
            width="240px"
            prop="component"
            align="center"
            label="组件">
      </el-table-column>

      <el-table-column
            width="200px"
            prop="url"
            align="center"
            label="路径">
      </el-table-column>

      <!-- <el-table-column
            width="100px"
            prop="sortNo"
            align="center"
            label="排序">
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

    <el-dialog :title="textMap[dialogStatus]" :visible.sync="dialogFormVisible" custom-class="single_dal_view">
      <el-form
        ref="dataForm"
        :rules="rules"
        :model="temp"
        label-position="left"
        label-width="110px"
      >
        <el-form-item label="菜单类型:">
          <el-radio-group v-model="temp.menuType">
            <el-radio :label="0" :disabled="dialogStatus==='detail'">一级菜单</el-radio>
            <el-radio :label="1" :disabled="dialogStatus==='detail'">子菜单</el-radio>
          </el-radio-group>
        </el-form-item>

        <el-form-item label="菜单名称:" prop="name">
          <el-input v-model="temp.name" placeholder="请输入菜单名称" :disabled="dialogStatus==='detail'"/>
        </el-form-item>

        <el-form-item label="上级菜单:" prop="menuType" v-if="temp.menuType">
          <el-cascader :options="menuLists" :show-all-levels="false" placeholder="请选择上级菜单"
          :props="{ value: 'id', label: 'name',checkStrictly: true}" v-model="temp.parentId" :disabled="dialogStatus==='detail'"></el-cascader>
        </el-form-item>

        <el-form-item label="菜单路径:" prop="url">
          <el-input v-model="temp.url" placeholder="请输入菜单路径"  :disabled="dialogStatus==='detail'"/>
        </el-form-item>

        <el-form-item label="前端组件:" prop="component">
          <el-input v-model="temp.component" placeholder="请输入前端组件" :disabled="dialogStatus==='detail'" />
        </el-form-item>
        

        <el-form-item label="菜单图标:">
          <el-input v-model="temp.icon" placeholder="请输入菜单图标" :disabled="dialogStatus==='detail'"/>
        </el-form-item>

        <el-form-item label="排序:" prop="sortNo">
          <el-input v-model="temp.sortNo" placeholder="请输入菜单的显示顺序" :disabled="dialogStatus==='detail'"/>
        </el-form-item>

        <el-form-item label="是否路由菜单:">
          <el-switch
            v-model="temp.route"
            @change="changeroute(temp.route)"
            :width="50"
            :active-value="true"
            :inactive-value="false"
            active-text="是"
            inactive-text="否"
            :disabled="dialogStatus==='detail'"
            >
          </el-switch>
        </el-form-item>

        <el-form-item label="隐藏路由:">
          <el-switch
            v-model="temp.hidden"
            :width="50"
            :active-value="true"
            :inactive-value="false"
            active-text="是"
            inactive-text="否"
            :disabled="dialogStatus==='detail'"
            >
          </el-switch>
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
// import { messageTip,getTreeData,handleCofirm,getTreeDeepArr } from "@/utils";
// import { permissionlist,addMenu,updateMenu,delMenu } from '@/api/system/menu'

export default {
  name: "menulist",
  data() {
    const validatemenuType = (rule, value, callback) => {
      if(this.temp.menuType && !value){
        callback(new Error("请选择上级菜单"));
      }else {
        callback();
      }
    };

    return {
      menuLists:[],
      tableKey: 0,
      list: [],
      listLoading: true,
      listQuery: {
        page: 1,
        limit: 20
      },
      temp: {
        status: "1",
        permsType:'1',
        route:true,
        icon:'',
        alwaysShow:false,
        hidden:false,
        keepAlive: false,
        internalOrExternal:false,
        menuType:0,
        name: "",
        parentId:"",
        url:"",
        component:"",
        sortNo:1
      },
      dialogFormVisible: false,
      dialogStatus: "",
      textMap: {
        update: "编辑",
        create: "新增",
        detail: "详情"
      },
      dialogDetail: false,
      message_tips:'',
      message_type:'',
      row:{},
      delId:'',
      rules: {
        url: [
          { required: true, message: "请输入菜单路径!", trigger: "blur" },
        ],
        name: [
          { required: true, message: "请输入菜单名称!", trigger: "blur" },
        ],
        component:[
          { required: true, message: "请输入前端组件名称!", trigger: "blur" },
        ],
        sortNo:[
          { required: true, message: "请输入菜单的显示顺序!", trigger: "blur" },
        ],
        menuType:[
          { required: true, trigger: "blur", validator: validatemenuType},
        ]
      },
    };
  },
  created() {
    // messageTip('测试弹窗','success');
    this.getList();
  },
  methods: {
    getList() {
        this.listLoading = true
        permissionlist().then(response => {
          this.menuLists = getTreeData(response.result);
          this.list = response.result;
          // console.log(this.list);
          setTimeout(() => {
            this.listLoading = false
          }, 0.5 * 1000)
        });
    },
    changeroute(params){
      this.temp.route = params;
    },
    resetTemp() {
      this.temp = {
        status: "1",
        permsType:'1',
        route:true,
        icon:'',
        alwaysShow:false,
        hidden:false,
        keepAlive: false,
        internalOrExternal:false,
        menuType:0,
        name: "",
        parentId:"",
        url:"",
        component:"",
        sortNo:1
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
          let createData = Object.assign({}, this.temp);
          createData.parentId = createData.parentId[createData.parentId.length-1];
          //发送接口
          addMenu(createData).then(response=>{
            let res = response;
            if(res.code==200){
              // this.list.unshift(this.temp)
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
      this.dialogDetail = true
    },
    handleUpdate(row) {
      // console.log(row);
      this.temp = Object.assign({}, row); // copy obj,***很重要,不需要一步步赋值了***
      this.temp.id = row.id;
      //给级联选择器赋值初始值
      let idArr = getTreeDeepArr(row.parentId,this.list);
      // console.log(row.parentId);
      // console.log(idArr);
      this.temp.parentId = idArr;
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
          tempData.parentId = tempData.parentId[tempData.parentId.length-1];
          updateMenu(tempData).then((response) => {
            let res = response;
            if(res.code==200){
              // const index = this.list.findIndex(v => v.id === this.temp.id)
              // this.list.splice(index, 1, this.temp)
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
    handleDelete(row, index) {
      console.log(index);
      handleCofirm("此操作将永久删除该数据, 是否继续?").then( () =>{
          delMenu(row.id).then((response)=>{
            console.log(response);
            let res = response;
            if(res.code==200){
              this.dialogFormVisible = false;
              this.message_tips = '删除成功';
              this.message_type = 'success';
              // this.list.splice(index, 1)
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
          messageTip('已取消删除','info');
      }); 
    },
  },
};
</script>