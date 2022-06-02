<template>
  <div class="app-container">
    <div :style="{ height: screenHeight + 'px'}" class="online_table">
      <el-tree
        class="filter-tree"
        :data="data"
        :props="defaultProps"
        default-expand-all
        :filter-node-method="filterNode"
        ref="tree">
      </el-tree> 
    </div>
    <div style="float:right;width:80%;height:630px;">
      <textarea style="border:0;
            border-radius:5px;
            background-color:rgba(241,241,241,.98);
            width: 100%;height: 30%;
            padding: 10px;resize: none;font-size:14px;"
            placeholder="请输入sql语句..." maxlength="2000"  v-model="stmt"  @input="descInput" > 
            <!-- class="codesql"  ref="mycode" -->
      </textarea>
      <span>{{remnant}}/2000</span>
      <el-button  type="primary" size="mini" style="float:right" @click="handleResult()" v-show="flag">执行</el-button>
      <div style="width:100%;height:70%;;margin-top:15px;">
       
        <el-tabs v-model="activeName" type="card">
          <el-tab-pane label="执行信息" name="1" v-html="info">{{info}}</el-tab-pane>
          <el-tab-pane label="执行历史" name="2">
            <el-table  height="360" :data="history" border highlight-current-row style="width: 100%;margin-bottom: 20px;">
              <el-table-column type="index" align="center" label="序号" width="50"></el-table-column>
              <el-table-column  align="center" label="SQL(双击SQL粘贴至上方)"><template slot-scope="{row}"><span class="link-type" @dblclick="copyVale(row.sql)">{{row.sql}}</span></template></el-table-column>
              <el-table-column prop="result" align="center" label="状态"></el-table-column>
              <el-table-column prop="time" align="center" label="开始时间"></el-table-column>
              <el-table-column prop="ms" align="center" label="耗时（ms）"></el-table-column>
            </el-table>
          </el-tab-pane>
          
          <el-tab-pane
            :key="item.name"
            v-for="item in editableTabs"
            :label="item.title"
            :name="item.name" 
          >
            <el-table :data="item.selectList" height="360">
              <el-table-column
                v-for=" prop in item.selectListTitle"
                :key="prop"
                :prop="prop"
                :label="prop">
              </el-table-column>
            </el-table>
          </el-tab-pane>
        </el-tabs>
      </div>
    </div>
  </div>
</template>

<script>
import router from '@/router/index'
import {getEffectComp,getExperience} from '@/api/cluster/list'
import { messageTip} from "@/utils";
// import 'codemirror/theme/ambiance.css'
// import 'codemirror/lib/codemirror.css'
// import 'codemirror/addon/hint/show-hint.css'
 
// const CodeMirror = require('codemirror/lib/codemirror')
// require('codemirror/addon/edit/matchbrackets')
// require('codemirror/addon/selection/active-line')
// require('codemirror/mode/sql/sql')
// require('codemirror/addon/hint/show-hint')
// require('codemirror/addon/hint/sql-hint')
export default {
  name: "experience",
  data() {
    return {
      filterText: '',
      data:[{}],
      defaultProps: {
        children: 'children',
        label: 'label'
      },
      remnant: 2000,
      stmt:'',
      editableTabs: [],
      flag:true,
      selectList:[],
      selectListTitle:[],
      history:[],
      info:'',
      activeName: '1',
      screenHeight: document.body.clientHeight - 80
    };
  },
  watch: {
    filterText(val) {
      this.$refs.tree.filter(val);
    },
    screenHeight (val) {
      // 为了避免频繁触发resize函数导致页面卡顿，使用定时器
      if (!this.timer) {
        // 一旦监听到的screenWidth值改变，就将其重新赋给data里的screenWidth
        this.screenHeight = val
        this.timer = true
        let that = this
        setTimeout(function () {
          // 打印screenWidth变化的值
          console.log(that.screenHeight)
          that.timer = false
        }, 400)
      }
    },
  },
  mounted(){
    this.getComp();
    const that = this
    window.onresize = () => {
      return (() => {
          // 可以限制最小高度
          // if (document.body.clientHeight - 240 < 450) {
          //   return
          // }
          window.screenHeight = document.body.clientHeight-80
          that.screenHeight = window.screenHeight
      })()
    };


    // const mime = 'text/x-mariadb'
    // // let theme = 'ambiance'//设置主题，不设置的会使用默认主题
    // this.editor = CodeMirror.fromTextArea(this.$refs.mycode, {
    //   mode: mime, // 选择对应代码编辑器的语言，我这边选的是数据库，根据个人情况自行设置即可
    //   indentWithTabs: true,
    //   smartIndent: true,
    //   lineNumbers: true,
    //   matchBrackets: true,
    //   // theme: 'base16-light',
    //   // autofocus: true,
    //   extraKeys: { 'Ctrl': 'autocomplete' }, // 自定义快捷键
    //   // hintOptions: {// 自定义提示选项
    //   //   tables: {
    //   //     users: ['1112', '123123', '124124'],
    //   //     countries: ['124', '124124', '1']
    //   //   }
    //   // }
    // })
    // this.editor.on('cursorActivity', () => {
    //  this.editor.showHint()
    // })

  },
  methods: {
    filterNode(value, data) {
      if (!value) return true;
      return data.label.indexOf(value) !== -1;
    },
    getComp(){
      //作用集群
      const apply_all_cluster=sessionStorage.getItem('apply_all_cluster');
      const effectCluster=sessionStorage.getItem('affected_clusters');
      const user_name=sessionStorage.getItem('login_username');
      //作用于部分集群
      if(apply_all_cluster==2&&effectCluster.length===0){
        router.push('/404');
         //messageTip('请先创建集群再体验！','error');
      }else{
        //集群是否有计算节点
        const temp={
          "effectCluster":effectCluster,
          "user_name":user_name
        }
        getEffectComp(temp).then((res) => {
          if(res.code==200){
            //对象转数组
            for (const i in res.res) {
              this.$set(this.data[0], i, res.res[i])
            }
            //session
            sessionStorage.setItem('ip',res.ip);
            sessionStorage.setItem('port',res.port);
            sessionStorage.setItem('db',res.db);
          }else if(res.code==501){
            router.push('/404');
          }
          else{
            messageTip(res.message,'error');
          }
        });
      }
    },
    descInput(){
      var txtVal = this.stmt.length;
      this.remnant = 2000 - txtVal;
    },
    handleResult(){
      this.flag=false;
      //console.log(this.$refs.mycode);
      if(!this.stmt){
        this.flag=true;
        messageTip("sql语句不能为空！","error");return;
      }
      if(this.stmt.length>2000){
        this.flag=true;
        messageTip("请输入2000字节以内的sql语句！","error");return;
      }
      const temp={
        "text":this.stmt,
        "ip":sessionStorage.getItem('ip'),
        "port":sessionStorage.getItem('port'),
        "db":sessionStorage.getItem('db'),
      }
      getExperience(temp).then((res) => {
        if(res.code==200){
          this.flag=true;
          this.info=res.len;
          //加载结果
          if(res.res_date.length>0){
            const newArr = [];
            const arr=res.res_date;
            arr.forEach((item,index) => {
              const list={'title': '结果'+(index+1),'name': "'"+(index+3)+"'",'selectList':item.arr,'selectListTitle':item.title};
              newArr.push(list)
            })
            this.editableTabs=newArr;
          }
          this.history=res.history;
          this.getComp();
        }
      })

    },
    copyVale(value){
      // let oInput = document.createElement("input"); //创建一个input标签
      // oInput.setAttribute("value",value);   // 设置改input的value值
      // document.body.appendChild(oInput); // 将input添加到body
      // oInput.select(); // 获取input的文本内容
      // document.execCommand("copy"); // 执行copy指令
      // document.body.removeChild(oInput); // 删除input标签
      // this.$message({message: '复制成功',type: 'success'});
      this.stmt=this.stmt+value;
    },
  },
};
</script>
<style lang="scss" scoped> 
.el-button--info{
    color: #000000;
    background-color: rgb(198, 198, 199);
    border-color: rgb(198, 198, 199);
    font-size: 12px;
  &:hover {
    color: rgb(0, 0, 0);
    background-color: rgb(255, 255, 255);
    border-color: rgb(198, 198, 199);
  }
  &:active {
    color: rgb(0, 0, 0);
    background-color: rgb(255, 255, 255);
    border-color: rgb(198, 198, 199);
  }
}
.el-menu-item{
  background-color: #ffffff !important;
}

.el-table__body-wrapper::-webkit-scrollbar {
  width: 10px;
}
 .el-table__body-wrapper::-webkit-scrollbar-thumb  {
  background-color: #c1c1c1;
  border-radius: 8px;
}
.online_table{
//width:18%;
// height:630px;
//border-right: 2px solid #dbdcdc;
//float:left
height: 100%;
   width: 18%;
   float:left;
  //  overflow-x: hidden;
   overflow-x: auto;
   overflow-y: auto;
   border-right: 2px solid #ececec;
}
::-webkit-scrollbar { 
  width: 5px; 
}
::-webkit-scrollbar-thumb{
  border-radius: 5px;
  -webkit-box-shadow: inset 0 0 6px rgba(0,0,0,.3);
  background-color: rgba(0,0,0,0.1);
}
// ::-webkit-scrollbar-track {
//   border-radius: 5px;
//   background-color: rgba(0,0,0,0.1);
// }
</style>