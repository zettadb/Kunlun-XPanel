(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-1e4a4c83"],{2327:function(t,e,r){},"333d":function(t,e,r){"use strict";var a=function(){var t=this,e=t.$createElement,r=t._self._c||e;return r("div",{staticClass:"pagination-container",class:{hidden:t.hidden}},[r("el-pagination",t._b({attrs:{background:t.background,"current-page":t.currentPage,"page-size":t.pageSize,layout:t.layout,"page-sizes":t.pageSizes,total:t.total},on:{"update:currentPage":function(e){t.currentPage=e},"update:current-page":function(e){t.currentPage=e},"update:pageSize":function(e){t.pageSize=e},"update:page-size":function(e){t.pageSize=e},"size-change":t.handleSizeChange,"current-change":t.handleCurrentChange}},"el-pagination",t.$attrs,!1))],1)},n=[];r("a9e3");Math.easeInOutQuad=function(t,e,r,a){return t/=a/2,t<1?r/2*t*t+e:(t--,-r/2*(t*(t-2)-1)+e)};var i=function(){return window.requestAnimationFrame||window.webkitRequestAnimationFrame||window.mozRequestAnimationFrame||function(t){window.setTimeout(t,1e3/60)}}();function o(t){document.documentElement.scrollTop=t,document.body.parentNode.scrollTop=t,document.body.scrollTop=t}function s(){return document.documentElement.scrollTop||document.body.parentNode.scrollTop||document.body.scrollTop}function u(t,e,r){var a=s(),n=t-a,u=20,l=0;e="undefined"===typeof e?500:e;var c=function t(){l+=u;var s=Math.easeInOutQuad(l,a,n,e);o(s),l<e?i(t):r&&"function"===typeof r&&r()};c()}var l={name:"Pagination",props:{total:{required:!0,type:Number},page:{type:Number,default:1},limit:{type:Number,default:20},pageSizes:{type:Array,default:function(){return[10,20,30,50]}},layout:{type:String,default:"total, sizes, prev, pager, next, jumper"},background:{type:Boolean,default:!0},autoScroll:{type:Boolean,default:!0},hidden:{type:Boolean,default:!1}},computed:{currentPage:{get:function(){return this.page},set:function(t){this.$emit("update:page",t)}},pageSize:{get:function(){return this.limit},set:function(t){this.$emit("update:limit",t)}}},methods:{handleSizeChange:function(t){this.$emit("pagination",{page:this.currentPage,limit:t}),this.autoScroll&&u(0,800)},handleCurrentChange:function(t){this.$emit("pagination",{page:t,limit:this.pageSize}),this.autoScroll&&u(0,800)}}},c=l,d=(r("d059"),r("2877")),p=Object(d["a"])(c,a,n,!1,null,"65877abe",null);e["a"]=p.exports},"47ea":function(t,e,r){"use strict";r("2327")},5420:function(t,e,r){"use strict";r.r(e);var a=function(){var t=this,e=t.$createElement,r=t._self._c||e;return r("div",{staticClass:"app-container"},[r("div",{staticClass:"filter-container"},[r("div",{staticClass:"table-list-search-wrap"},[r("el-input",{staticClass:"list_search_keyword",attrs:{placeholder:"可输入ip搜索"},nativeOn:{keyup:function(e){return!e.type.indexOf("key")&&t._k(e.keyCode,"enter",13,e.key,"Enter")?null:t.handleFilter(e)}},model:{value:t.listQuery.hostaddr,callback:function(e){t.$set(t.listQuery,"hostaddr",e)},expression:"listQuery.hostaddr"}}),r("el-button",{attrs:{icon:"el-icon-search"},on:{click:t.handleFilter}},[t._v(" 查询 ")]),r("el-button",{attrs:{icon:"el-icon-refresh-right"},on:{click:t.handleClear}},[t._v(" 重置 ")]),"Y"===t.machine_add_priv?r("el-button",{staticClass:"filter-item",attrs:{type:"primary",icon:"el-icon-plus"},on:{click:t.handleCreate}},[t._v("新增")]):t._e(),r("div",{directives:[{name:"show",rawName:"v-show",value:!0===t.installStatus,expression:"installStatus===true"}],staticClass:"info",domProps:{textContent:t._s(t.info)}})],1),r("div",{staticClass:"table-list-wrap"})]),r("el-table",{directives:[{name:"loading",rawName:"v-loading",value:t.listLoading,expression:"listLoading"}],key:t.tableKey,ref:"multipleTable",staticStyle:{width:"100%","margin-bottom":"20px"},attrs:{data:t.list,border:"","highlight-current-row":"","row-class-name":t.colorStyle}},[r("el-table-column",{attrs:{type:"index",align:"center",label:"序号",width:"50"}}),r("el-table-column",{attrs:{label:"IP地址",align:"center"},scopedSlots:t._u([{key:"default",fn:function(e){var a=e.row;return[r("span",{staticClass:"link-type",on:{click:function(e){return t.handleDetail(a)}}},[t._v(t._s(a.hostaddr))])]}}])}),r("el-table-column",{attrs:{prop:"machine_type",align:"center",label:"机器类型"},scopedSlots:t._u([{key:"default",fn:function(e){return["storage"===e.row.machine_type?r("span",[t._v("存储")]):"computer"===e.row.machine_type?r("span",[t._v("计算")]):r("span")]}}])}),r("el-table-column",{attrs:{prop:"port_range",align:"center",label:"端口范围"}}),r("el-table-column",{attrs:{prop:"used_port",align:"center","show-overflow-tooltip":!0,label:"已使用端口"}}),r("el-table-column",{attrs:{prop:"installing_port",align:"center","show-overflow-tooltip":!0,label:"安装中端口"}}),r("el-table-column",{attrs:{prop:"node_stats",align:"center",label:"状态",width:"170"},scopedSlots:t._u([{key:"default",fn:function(e){return["running"===e.row.node_stats?r("span",{staticStyle:{color:"#00ed37"}},[t._v("在线")]):"idle"===e.row.node_stats?r("span",{staticStyle:{color:"#c7c9d1"}},[t._v("不允许再装实例")]):"dead"===e.row.node_stats?r("span",{staticStyle:{color:"red"}},[t._v("离线")]):r("span")]}}])}),r("el-table-column",{attrs:{prop:"total_cpu_cores",align:"center",label:"cpu核数"}}),r("el-table-column",{attrs:{prop:"total_mem",align:"center",label:"总内存"},scopedSlots:t._u([{key:"default",fn:function(e){return[t._v(t._s(e.row.total_mem+"MB"))]}}])}),r("el-table-column",{attrs:{prop:"rack_id",align:"center",label:"机架编号"}}),r("el-table-column",{attrs:{label:"操作",align:"center",width:"250","class-name":"small-padding fixed-width"},scopedSlots:t._u([{key:"default",fn:function(e){var a=e.row,n=e.$index;return[r("el-button",{attrs:{type:"primary",size:"mini"},on:{click:function(e){return t.gotolink(a.hostaddr)}}},[t._v("节点视图")]),"Y"===t.machine_priv?r("el-button",{attrs:{type:"primary",size:"mini"},on:{click:function(e){return t.handleUpdate(a)}}},[t._v("编辑")]):t._e(),"Y"===t.machine_drop_priv?r("el-button",{attrs:{size:"mini",type:"danger"},on:{click:function(e){return t.handleDelete(a,n)}}},[t._v("删除")]):t._e()]}}])})],1),r("pagination",{directives:[{name:"show",rawName:"v-show",value:t.total>0,expression:"total>0"}],attrs:{total:t.total,page:t.listQuery.pageNo,limit:t.listQuery.pageSize},on:{"update:page":function(e){return t.$set(t.listQuery,"pageNo",e)},"update:limit":function(e){return t.$set(t.listQuery,"pageSize",e)},pagination:t.getList}}),r("el-dialog",{attrs:{title:t.textMap[t.dialogStatus],visible:t.dialogFormVisible,"custom-class":"single_dal_view"},on:{"update:visible":function(e){t.dialogFormVisible=e}}},[r("el-form",{ref:"dataForm",attrs:{rules:t.rules,model:t.temp,"label-position":"left","label-width":"140px"}},[r("el-form-item",{attrs:{label:"IP地址:",prop:"hostaddr"}},[r("el-input",{attrs:{placeholder:"请输入IP地址",disabled:"detail"===t.dialogStatus||"update"===t.dialogStatus},model:{value:t.temp.hostaddr,callback:function(e){t.$set(t.temp,"hostaddr",e)},expression:"temp.hostaddr"}})],1),r("el-form-item",{attrs:{label:"机器类型:",prop:"machine_type"}},[r("el-select",{attrs:{placeholder:"请选择机器类型",disabled:"detail"===t.dialogStatus||"update"===t.dialogStatus},on:{change:t.ChangeSaler},model:{value:t.temp.machine_type,callback:function(e){t.$set(t.temp,"machine_type",e)},expression:"temp.machine_type"}},t._l(t.machine_types,(function(t){return r("el-option",{key:t.id,attrs:{label:t.label,value:t.id}})})),1)],1),r("el-form-item",{attrs:{label:"端口范围:",prop:"port_range"}},[r("el-input",{staticClass:"right_input_min",attrs:{placeholder:"请输入起始端口号",disabled:"detail"===t.dialogStatus},model:{value:t.temp.start_port,callback:function(e){t.$set(t.temp,"start_port",e)},expression:"temp.start_port"}}),t._v(" - "),r("el-input",{staticClass:"right_input_min",attrs:{placeholder:"请输入结束端口号",disabled:"detail"===t.dialogStatus},model:{value:t.temp.end_port,callback:function(e){t.$set(t.temp,"end_port",e)},expression:"temp.end_port"}})],1),"storage"==t.temp.machine_type?r("div",[r("el-form-item",{attrs:{label:"日志目录:",prop:"logdir"}},[r("el-input",{attrs:{placeholder:"请输入日志目录",disabled:"detail"===t.dialogStatus},model:{value:t.temp.logdir,callback:function(e){t.$set(t.temp,"logdir",e)},expression:"temp.logdir"}})],1),r("el-form-item",{attrs:{label:"wal日志目录:",prop:"wal_log_dir"}},[r("el-input",{attrs:{placeholder:"请输入wal日志目录",disabled:"detail"===t.dialogStatus},model:{value:t.temp.wal_log_dir,callback:function(e){t.$set(t.temp,"wal_log_dir",e)},expression:"temp.wal_log_dir"}})],1),r("el-form-item",{attrs:{label:"存储节点数据目录:",prop:"datadir"}},[r("el-input",{attrs:{placeholder:"请输入存储节点数据目录",disabled:"detail"===t.dialogStatus},model:{value:t.temp.datadir,callback:function(e){t.$set(t.temp,"datadir",e)},expression:"temp.datadir"}})],1)],1):t._e(),"computer"==t.temp.machine_type?r("el-form-item",{attrs:{label:"计算节点数据目录:",prop:"comp_datadir"}},[r("el-input",{attrs:{placeholder:"请输入计算节点数据目录",disabled:"detail"===t.dialogStatus},model:{value:t.temp.comp_datadir,callback:function(e){t.$set(t.temp,"comp_datadir",e)},expression:"temp.comp_datadir"}})],1):t._e(),r("el-form-item",{attrs:{label:"总内存:",prop:"total_mem"}},[r("el-input",{attrs:{placeholder:"请输入总内存",disabled:"detail"===t.dialogStatus},model:{value:t.temp.total_mem,callback:function(e){t.$set(t.temp,"total_mem",e)},expression:"temp.total_mem"}},[r("i",{staticStyle:{"font-style":"normal","margin-right":"10px","line-height":"30px"},attrs:{slot:"suffix"},slot:"suffix"},[t._v("MB")])])],1),r("el-form-item",{attrs:{label:"cpu核数:",prop:"total_cpu_cores"}},[r("el-input",{attrs:{placeholder:"请输入cpu核数",disabled:"detail"===t.dialogStatus},model:{value:t.temp.total_cpu_cores,callback:function(e){t.$set(t.temp,"total_cpu_cores",e)},expression:"temp.total_cpu_cores"}})],1),r("el-form-item",{attrs:{label:"机架编号:",prop:"rack_id"}},[r("el-input",{attrs:{placeholder:"请输入机架编号",autocomplete:"new-password",disabled:"detail"===t.dialogStatus||"update"===t.dialogStatus},model:{value:t.temp.rack_id,callback:function(e){t.$set(t.temp,"rack_id",e)},expression:"temp.rack_id"}})],1)],1),r("div",{staticClass:"dialog-footer",attrs:{slot:"footer"},slot:"footer"},[r("el-button",{directives:[{name:"show",rawName:"v-show",value:!t.dialogDetail,expression:"!dialogDetail"}],on:{click:function(e){t.dialogFormVisible=!1}}},[t._v("关闭")]),r("el-button",{directives:[{name:"show",rawName:"v-show",value:!t.dialogDetail,expression:"!dialogDetail"}],attrs:{type:"primary"},on:{click:function(e){"create"===t.dialogStatus?t.createData():t.updateData(t.row)}}},[t._v("确认")])],1)],1),r("el-dialog",{attrs:{title:t.textMap[t.dialogStatus],visible:t.dialogUploadVisible,"custom-class":"single_dal_view"},on:{"update:visible":function(e){t.dialogUploadVisible=e}}},[r("el-form",{ref:"dataForm",attrs:{rules:t.rules,model:t.temp,"label-position":"left","label-width":"110px"}},[r("el-form-item",{attrs:{label:"模板:"}},[r("el-button",{attrs:{type:"text",icon:"el-icon-download"},on:{click:function(e){return t.downloadTemplate()}}},[t._v("下载模板")])],1),r("el-form-item",{attrs:{label:"选择导入文件:"}},[r("el-row",[r("div",{staticClass:"el-form-item__content"},[r("el-upload",{ref:"upload",attrs:{accept:".xls,.xlsx",action:"doUpload",limit:1,"file-list":t.fileList,"before-upload":t.beforeUpload}},[r("el-button",{attrs:{size:"small",type:"primary"}},[t._v("选择文件")]),r("div",{staticClass:"el-upload__tip",attrs:{slot:"tip"},slot:"tip"},[t._v("一次只能上传一个xls/xlsx文件，且不超过5M")]),r("div",{staticClass:"el-upload-list__item-name",attrs:{slot:"tip"},slot:"tip"},[t._v(t._s(t.fileName))])],1)],1)])],1)],1),r("div",{staticClass:"dialog-footer",attrs:{slot:"footer"},slot:"footer"},[r("el-button",{directives:[{name:"show",rawName:"v-show",value:!t.dialogDetail,expression:"!dialogDetail"}],on:{click:function(e){t.dialogUploadVisible=!1}}},[t._v("关闭")]),r("el-button",{directives:[{name:"show",rawName:"v-show",value:!t.dialogDetail,expression:"!dialogDetail"}],attrs:{type:"primary"},on:{click:function(e){return t.uploadDate()}}},[t._v("上传")])],1)],1),r("el-dialog",{attrs:{visible:t.dialogStatusVisible,"custom-class":"single_dal_view",width:"400px","close-on-click-modal":!1},on:{"update:visible":function(e){t.dialogStatusVisible=e}}},[r("div",{staticClass:"block"},[r("el-timeline",t._l(t.activities,(function(e,a){return r("el-timeline-item",{key:a,attrs:{icon:e.icon,type:e.type,color:e.color,size:e.size,timestamp:e.timestamp}},[t._v(" "+t._s(e.content)+" ")])})),1)],1)]),r("el-dialog",{attrs:{title:t.job_id,visible:t.dialogStatusVisible,"custom-class":"single_dal_view","close-on-click-modal":!1,"before-close":t.beforeDestory},on:{"update:visible":function(e){t.dialogStatusVisible=e}}},[r("div",{staticStyle:{width:"100%",background:"#fff",padding:"0 20px"}},[r("el-steps",{attrs:{direction:"vertical",active:t.comp_active}},t._l(t.activities,(function(t,e){return r("el-step",{key:e,attrs:{title:t.title,icon:t.icon,status:t.status,description:t.description}})})),1)],1)])],1)},n=[],i=(r("ac1f"),r("00b4"),r("b64b"),r("b0c0"),r("a4d3"),r("e01a"),r("ed08")),o=r("a467"),s=r("82b0"),u=r("578a"),l=r("333d"),c=function(){var t=this,e=t.$createElement,r=t._self._c||e;return r("div",[r("el-upload",{attrs:{action:"https://jsonplaceholder.typicode.com/posts/","on-success":t.handleChange,"file-list":t.fileList}},[r("el-button",{attrs:{type:"primary",icon:"el-icon-upload2"}},[t._v("批量导入")])],1),t.tableHead.length?r("el-table",{staticStyle:{width:"100%"},attrs:{data:t.tableData[0]}},t._l(t.tableHead,(function(t,e){return r("el-table-column",{key:e,attrs:{prop:t,label:t,width:"180"}})})),1):t._e()],1)},d=[],p=(r("fb6a"),r("caad"),r("d3b7"),r("159b"),r("2b0e")),m=r("5c96"),f=r.n(m),g=(r("0fae"),r("25ca"));p["default"].use(f.a);var h={data:function(){return{fileList:[],tableHead:[],tableData:[]}},methods:{handleBefore:function(t){var e=["xlsx","xls"],r=t.name.split(".").slice(-1)[0].toLowerCase();if(!e.includes(r))return this.$message.error("上传文件只能是xls/xlsx格式!"),!1;var a=this.readExcel(t);if(console.log(a),a){var n=t.size/1024/1024<2;if(!n)return this.$message.error("文件大小不能超过2MB!"),!1}return!0},handleChange:function(t,e,r){for(var a=0;a<r.length;a++)e.name!=r[a].name&&this.fileList.push({name:e.name,url:"",uid:e.uid});var n={0:e};this.readExcel(n)},readExcel:function(t){var e=this,r=new FileReader;r.onload=function(t){try{var r=t.target.result,a=Object(g["a"])(r,{type:"binary"}),n=[];if(a.SheetNames.forEach((function(t){if(n.push({name:t,dataList:g["b"].sheet_to_json(a.Sheets[t])}),g["b"].sheet_to_json(a.Sheets[t].length>0))for(var r=0;r<g["b"].sheet_to_json(a.Sheets[t]).length;r++);e.tableData.push(g["b"].sheet_to_json(a.Sheets[t]))})),e.tableData.length>0)for(var i in e.tableData[0][0])e.tableHead.push(i);return n}catch(o){return console.log("error:"+o),!1}},r.readAsBinaryString(t[0].raw)}}},b=h,_=(r("47ea"),r("2877")),v=Object(_["a"])(b,c,d,!1,null,"7c5a674e",null),C=v.exports,j={name:"account",components:{Pagination:l["a"],Upload:C},data:function(){var t=this,e=function(t,e,r){var a=/^((2(5[0-5]|[0-4]\d))|[0-1]?\d{1,2})(\.((2(5[0-5]|[0-4]\d))|[0-1]?\d{1,2})){3}$/,n=e.split(","),i=!0;if(n.length)for(var o=0;o<n.length;o++)0==a.test(n[o])&&(i=!1);if(""==e)return r(new Error("请输入IP地址"));i?r():r(new Error("请输入正确对IP地址"))},r=function(t,e,r){e?r():r(new Error("请选择机器类型"))},a=function(e,r,a){r?/^[0-9]+$/.test(r)?r&&t.temp.end_port&&t.temp.end_port<=r?a(new Error("起始端口号不能大于等于结束端口号")):a():a(new Error("端口号只能输入数字")):a(new Error("请输入起始端口号"))},n=function(e,r,a){r?/^[0-9]+$/.test(r)?r&&t.temp.start_port&&t.temp.start_port>r?a(new Error("起始端口号不能大于等于结束端口号")):a():a(new Error("端口号只能输入数字")):a(new Error("请输入结束端口号"))},i=function(t,e,r){e?r():r(new Error("请输入存储节点数据目录"))},o=function(t,e,r){e?r():r(new Error("请输入日志目录"))},s=function(t,e,r){e?r():r(new Error("请输入wal日志目录"))},l=function(t,e,r){e?r():r(new Error("请输入计算节点数据目录"))},c=function(t,e,r){e?/^[0-9]+$/.test(e)?r():r(new Error("cpu核数只能输入数字")):r(new Error("请输入cpu核数"))},d=function(t,e,r){e?/^[0-9]+$/.test(e)?r():r(new Error("总内存只能输入数字")):r(new Error("请输入总内存"))},p=function(e,r,a){t.temp.start_port||t.temp.end_port?a():a(new Error("端口范围不能为空"))};return{tableKey:0,list:null,listLoading:!0,searchLoading:!1,total:0,listQuery:{pageNo:1,pageSize:10,hostaddr:""},machine_types:u["i"],nodeStatsList:u["j"],temp:{hostaddr:"",rack_id:"",datadir:"",logdir:"",wal_log_dir:"",comp_datadir:"",total_cpu_cores:"",total_mem:"",machine_type:"",start_port:"",end_port:"",port_range:""},dialogFormVisible:!1,dialogEditVisible:!1,dialogUploadVisible:!1,dialogStatus:"",textMap:{update:"编辑计算机",create:"新增计算机",detail:"详情",upload:"批量导入"},dialogDetail:!1,message_tips:"",message_type:"",installStatus:!1,info:"",row:{},machine_add_priv:JSON.parse(sessionStorage.getItem("priv")).machine_add_priv,machine_drop_priv:JSON.parse(sessionStorage.getItem("priv")).machine_drop_priv,machine_priv:JSON.parse(sessionStorage.getItem("priv")).machine_priv,storage_status:!1,comp_status:!1,comp_active:1,job_id:null,file:{},files:{name:"",size:""},fileName:"",fileList:[],activities:[],dialogStatusVisible:!1,timer:null,rules:{hostaddr:[{required:!0,trigger:"blur",validator:e}],datadir:[{required:!0,trigger:"blur",validator:i}],logdir:[{required:!0,trigger:"blur",validator:o}],wal_log_dir:[{required:!0,trigger:"blur",validator:s}],comp_datadir:[{required:!0,trigger:"blur",validator:l}],total_mem:[{required:!0,trigger:"blur",validator:d}],total_cpu_cores:[{required:!0,trigger:"blur",validator:c}],machine_type:[{required:!0,trigger:"blur",validator:r}],port_range:[{required:!0,trigger:"blur",validator:p}],start_port:[{required:!0,trigger:"blur",validator:a}],end_port:[{required:!0,trigger:"blur",validator:n}]}}},created:function(){this.getList()},methods:{colorStyle:function(t){t.row,t.rowIndex;this.$nextTick((function(){}))},chageTextColor:function(t,e,r,a){var n=this.getColor(t);this.$refs.multipleTable.$el.children[2].children[0].children[1].children[a].children[6].children[0].children[0].children[0].children[0].style.color=""+n},getColor:function(t){for(var e in this.nodeStatsList)if(this.nodeStatsList[e].id===t)return this.nodeStatsList[e].color},beforeDestory:function(){clearInterval(this.timer),this.dialogStatusVisible=!1},beforeUpload:function(t){console.log(t,"文件"),this.files=t;var e="xls"===t.name.split(".")[1],r="xlsx"===t.name.split(".")[1],a=t.size/1024/1024<5;if(e||r){if(a)return this.fileName=t.name,!1;this.$message.warning("上传模板大小不能超过5MB!")}else this.$message.warning("上传模板只能是xls、xlsx格式!")},uploadDate:function(){var t=this;if(console.log("上传"+this.files.name),""==this.fileName)return this.$message.warning("请选择要上传的文件！"),!1;var e=new FormData;e.append("fileName",this.fileName),e.append("file",this.files),this.message_tips="正在导入数据",this.message_type="success",Object(i["g"])(this.message_tips,this.message_type),Object(o["f"])(e).then((function(e){t.dialogUploadVisible=!1,t.message_tips="正在导入...",t.message_type="success",200==e.code&&(t.message_tips=e.message,t.message_type="success"),Object(i["g"])(t.message_tips,t.message_type),t.getList()}))},downloadTemplate:function(){var t=document.createElement("a");t.href="./machine.xlsx",t.download="machine.xlsx",t.style.display="none",document.body.appendChild(t),t.click(),t.remove()},ChangeSaler:function(t){"storage"==t?(this.temp.start_port="57000",this.temp.end_port="58000",this.storage_status=!0,this.comp_status=!1):"computer"==t&&(this.temp.start_port="47000",this.temp.end_port="48000",this.storage_status=!1,this.comp_status=!0)},handleFilter:function(){this.listQuery.pageNo=1,this.getList()},handleClear:function(){this.listQuery.hostaddr="",this.listQuery.pageNo=1,this.getList()},getList:function(){var t=this;this.listLoading=!0;var e=Object.assign({},this.listQuery);Object(o["c"])(e).then((function(e){t.list=e.list,t.total=e.total,setTimeout((function(){t.listLoading=!1}),500)}))},resetTemp:function(){this.temp={hostaddr:"",rack_id:"",datadir:"",logdir:"",wal_log_dir:"",comp_datadir:"",total_cpu_cores:"",total_mem:"",machine_type:"",start_port:"",end_port:"",port_range:""}},handleCreate:function(){var t=this;this.resetTemp(),this.dialogStatus="create",this.dialogFormVisible=!0,this.dialogDetail=!1,this.storage_status=!1,this.comp_status=!1,this.$nextTick((function(){t.$refs.dataForm.clearValidate()}))},createData:function(){var t=this;this.$refs["dataForm"].validate((function(e){if(e){var r={};r.user_name=sessionStorage.getItem("login_username"),r.job_id="",r.job_type="create_machine",r.version=u["u"][0].ver,r.timestamp=u["s"][0].time+"",t.temp.port_range=t.temp.start_port+"-"+t.temp.end_port,t.temp.rack_id||(t.temp.rack_id="0"),t.$delete(t.temp,"start_port"),t.$delete(t.temp,"end_port"),r.paras=Object.assign({},t.temp),Object(o["a"])(r).then((function(e){var r=e;if("accept"==r.status){t.dialogFormVisible=!1,t.job_id="新增计算机("+r.job_id+")",t.dialogStatusVisible=!0,t.activities=[];var a={title:"正在新增计算机",status:"process",icon:"el-icon-loading",description:""};t.activities.push(a);var n=0,o="新增";t.timer=setInterval((function(){t.getStatus(t.timer,r.job_id,n++,o)}),1e3)}else t.message_tips=r.error_info,t.message_type="error",Object(i["g"])(t.message_tips,t.message_type)}))}}))},handleDetail:function(t){this.dialogStatus="detail",this.dialogFormVisible=!0,this.temp=Object.assign({},t),this.dialogDetail=!0},handleUpdate:function(t){var e=this;this.temp=Object.assign({},t),this.dialogStatus="update",this.dialogFormVisible=!0,this.dialogDetail=!1,this.temp.start_port=t.port_range.split("-")[0],this.temp.end_port=t.port_range.split("-")[1],this.$nextTick((function(){e.$refs["dataForm"].clearValidate()}))},updateData:function(){var t=this;this.$refs["dataForm"].validate((function(e){if(e){var r={};r.user_name=sessionStorage.getItem("login_username"),r.job_id="",r.job_type="update_machine",r.version=u["u"][0].ver,r.timestamp=u["s"][0].time+"";var a={};a.port_range=t.temp.start_port+"-"+t.temp.end_port,a.hostaddr=t.temp.hostaddr,a.machine_type=t.temp.machine_type,a.total_cpu_cores=t.temp.total_cpu_cores,a.total_mem=t.temp.total_mem,"storage"==t.temp.machine_type?(a.datadir=t.temp.datadir,a.logdir=t.temp.logdir,a.wal_log_dir=t.temp.wal_log_dir):"computer"==t.temp.machine_type&&(a.comp_datadir=t.temp.comp_datadir),r.paras=a,Object(o["g"])(r).then((function(e){var r=e;if("accept"==r.status){t.dialogFormVisible=!1,t.job_id="编辑计算机("+r.job_id+")",t.dialogStatusVisible=!0,t.activities=[];var a={title:"正在编辑计算机",status:"process",icon:"el-icon-loading",description:""};t.activities.push(a);var n=0,o="编辑";t.timer=setInterval((function(){t.getStatus(t.timer,r.job_id,n++,o)}),1e3)}else t.message_tips=r.error_info,t.message_type="error",Object(i["g"])(t.message_tips,t.message_type)}))}}))},handleDelete:function(t){var e=this;t.used_port&&"NULL"!==t.used_port&&0!=t.used_port.length&&"null"!==t.used_port?(this.message_tips="请先清空该计算机已经存在的实例，再进行删除操作",this.message_type="error",Object(i["g"])(this.message_tips,this.message_type)):Object(i["f"])("此操作将永久删除该数据, 是否继续?").then((function(){var r={};r.user_name=sessionStorage.getItem("login_username"),r.job_id="",r.job_type="delete_machine",r.version=u["u"][0].ver,r.timestamp=u["s"][0].time+"",r.paras={hostaddr:t.hostaddr,machine_type:t.machine_type},Object(o["b"])(r).then((function(t){var r=t;if("accept"==r.status){e.dialogFormVisible=!1,e.job_id="删除计算机("+r.job_id+")",e.dialogStatusVisible=!0,e.activities=[];var a={title:"正在删除计算机",status:"process",icon:"el-icon-loading",description:""};e.activities.push(a);var n="删除",o=0;e.timer=setInterval((function(){e.getStatus(e.timer,r.job_id,o++,n)}),1e3)}else e.message_tips=r.error_info,e.message_type="error",Object(i["g"])(e.message_tips,e.message_type)}))})).catch((function(){console.log("quxiao"),Object(i["g"])("已取消删除","info")}))},gotolink:function(t){var e=this;Object(o["e"])(t).then((function(r){var a=r.total;0==a?Object(i["g"])("该计算机上还没有节点","error"):e.$router.push({path:"/machine/node",query:{hostaddr:t}})}))},handleUpload:function(){var t=this;this.dialogStatus="upload",this.dialogUploadVisible=!0,this.dialogDetail=!1,this.$nextTick((function(){t.$refs.dataForm.clearValidate()}))},getStatus:function(t,e,r,a){var n=this;setTimeout((function(){var i={job_type:"get_status"};i.version=u["u"][0].ver,i.job_id=e,i.timestamp=u["s"][0].time+"",i.paras={},Object(s["Q"])(i).then((function(e){"done"!=e.status&&"failed"!=e.status||(clearInterval(t),"done"==e.status?(n.activities[0].title=a+"计算机成功",n.activities[0].icon="el-icon-circle-check",n.activities[0].status="success",n.getList()):(n.activities[0].title=a+"计算机失败",n.activities[0].icon="el-icon-circle-close",n.activities[0].status="error",n.activities[0].description=e.error_info))})),r>=86400&&clearInterval(t)}),0)}}},y=j,O=(r("657c"),Object(_["a"])(y,a,n,!1,null,null,null));e["default"]=O.exports},"657c":function(t,e,r){"use strict";r("a525")},"82b0":function(t,e,r){"use strict";r.d(e,"F",(function(){return n})),r.d(e,"P",(function(){return i})),r.d(e,"S",(function(){return o})),r.d(e,"o",(function(){return s})),r.d(e,"p",(function(){return u})),r.d(e,"j",(function(){return l})),r.d(e,"B",(function(){return c})),r.d(e,"sb",(function(){return d})),r.d(e,"wb",(function(){return p})),r.d(e,"z",(function(){return m})),r.d(e,"hb",(function(){return f})),r.d(e,"h",(function(){return g})),r.d(e,"e",(function(){return h})),r.d(e,"g",(function(){return b})),r.d(e,"Q",(function(){return _})),r.d(e,"H",(function(){return v})),r.d(e,"s",(function(){return C})),r.d(e,"q",(function(){return j})),r.d(e,"t",(function(){return y})),r.d(e,"Bb",(function(){return O})),r.d(e,"Cb",(function(){return w})),r.d(e,"Db",(function(){return S})),r.d(e,"Eb",(function(){return x})),r.d(e,"ub",(function(){return k})),r.d(e,"vb",(function(){return L})),r.d(e,"O",(function(){return D})),r.d(e,"Hb",(function(){return $})),r.d(e,"Z",(function(){return E})),r.d(e,"lb",(function(){return N})),r.d(e,"nb",(function(){return M})),r.d(e,"ob",(function(){return T})),r.d(e,"fb",(function(){return I})),r.d(e,"mb",(function(){return V})),r.d(e,"K",(function(){return z})),r.d(e,"C",(function(){return F})),r.d(e,"Fb",(function(){return B})),r.d(e,"d",(function(){return U})),r.d(e,"i",(function(){return q})),r.d(e,"Jb",(function(){return P})),r.d(e,"u",(function(){return Q})),r.d(e,"tb",(function(){return A})),r.d(e,"jb",(function(){return J})),r.d(e,"ib",(function(){return H})),r.d(e,"L",(function(){return R})),r.d(e,"ab",(function(){return W})),r.d(e,"bb",(function(){return Y})),r.d(e,"D",(function(){return G})),r.d(e,"k",(function(){return K})),r.d(e,"kb",(function(){return X})),r.d(e,"I",(function(){return Z})),r.d(e,"J",(function(){return tt})),r.d(e,"gb",(function(){return et})),r.d(e,"db",(function(){return rt})),r.d(e,"y",(function(){return at})),r.d(e,"R",(function(){return nt})),r.d(e,"xb",(function(){return it})),r.d(e,"V",(function(){return ot})),r.d(e,"W",(function(){return st})),r.d(e,"A",(function(){return ut})),r.d(e,"zb",(function(){return lt})),r.d(e,"qb",(function(){return ct})),r.d(e,"rb",(function(){return dt})),r.d(e,"cb",(function(){return pt})),r.d(e,"m",(function(){return mt})),r.d(e,"Ab",(function(){return ft})),r.d(e,"Y",(function(){return gt})),r.d(e,"yb",(function(){return ht})),r.d(e,"v",(function(){return bt})),r.d(e,"n",(function(){return _t})),r.d(e,"pb",(function(){return vt})),r.d(e,"G",(function(){return Ct})),r.d(e,"Gb",(function(){return jt})),r.d(e,"eb",(function(){return yt})),r.d(e,"l",(function(){return Ot})),r.d(e,"M",(function(){return wt})),r.d(e,"T",(function(){return St})),r.d(e,"U",(function(){return xt})),r.d(e,"Ib",(function(){return kt})),r.d(e,"w",(function(){return Lt})),r.d(e,"x",(function(){return Dt})),r.d(e,"b",(function(){return $t})),r.d(e,"E",(function(){return Et})),r.d(e,"a",(function(){return Nt})),r.d(e,"c",(function(){return Mt})),r.d(e,"X",(function(){return Tt})),r.d(e,"f",(function(){return It})),r.d(e,"N",(function(){return Vt})),r.d(e,"r",(function(){return zt}));var a=r("b775");function n(t){return Object(a["a"])({url:"/user/Cluster/clusterList",method:"get",params:t})}function i(t){return Object(a["a"])({url:"/user/Cluster/getEffectComp",method:"post",data:t})}function o(t){return Object(a["a"])({url:"/user/Cluster/getExperience",method:"post",data:t})}function s(t){return Object(a["a"])({url:"/user/Cluster/createCluster",method:"post",data:t})}function u(t){return Object(a["a"])({url:"/user/Cluster/delCluster",method:"post",data:t})}function l(t){return Object(a["a"])({url:"/user/Cluster/backUpCluster",method:"post",data:t})}function c(t){return Object(a["a"])({url:"/user/Cluster/getBackUpList",method:"get",params:t})}function d(t){return Object(a["a"])({url:"/user/Cluster/ifBackUp",method:"post",data:{id:t}})}function p(t){return Object(a["a"])({url:"/user/Cluster/restoreCluster",method:"post",data:t})}function m(){return Object(a["a"])({url:"/user/Cluster/getAllMachine",method:"get",params:""})}function f(t){return Object(a["a"])({url:"/user/Cluster/getShards",method:"post",data:{id:t}})}function g(t){return Object(a["a"])({url:"/user/Cluster/addShards",method:"post",data:t})}function h(t){return Object(a["a"])({url:"/user/Cluster/addComps",method:"post",data:t})}function b(t){return Object(a["a"])({url:"/user/Cluster/addNodes",method:"post",data:t})}function _(t){return Object(a["a"])({url:"/user/Cluster/getStatus",method:"post",data:t})}function v(t){return Object(a["a"])({url:"/user/Cluster/getClusterNodesList",method:"post",data:t})}function C(t){return Object(a["a"])({url:"/user/Cluster/delShard",method:"post",data:t})}function j(t){return Object(a["a"])({url:"/user/Cluster/delComp",method:"post",data:t})}function y(t){return Object(a["a"])({url:"/user/Cluster/delSnode",method:"post",data:t})}function O(t){return Object(a["a"])({url:"/user/Cluster/controlInstance",method:"post",data:t})}function w(t){return Object(a["a"])({url:"/user/Cluster/controlInstance",method:"post",data:t})}function S(t){return Object(a["a"])({url:"/user/Cluster/controlInstance",method:"post",data:t})}function x(t){return Object(a["a"])({url:"/user/Cluster/controlInstance",method:"post",data:t})}function k(t){return Object(a["a"])({url:"/user/Cluster/controlInstance",method:"post",data:t})}function L(t){return Object(a["a"])({url:"/user/Cluster/controlInstance",method:"post",data:t})}function D(t){return Object(a["a"])({url:"/user/Cluster/getEffectCluster",method:"post",data:t})}function $(t){return Object(a["a"])({url:"/user/Cluster/updateAssign",method:"post",data:t})}function E(t){return Object(a["a"])({url:"/user/Cluster/getNode",method:"post",data:t})}function N(t){return Object(a["a"])({url:"/user/Cluster/getStandbyNode",method:"post",data:t})}function M(t){return Object(a["a"])({url:"/user/Cluster/getSwitcheOverList",method:"get",params:t})}function T(t){return Object(a["a"])({url:"/user/Cluster/getTaskList",method:"get",params:t})}function I(t){return Object(a["a"])({url:"/user/Cluster/getShardPrimary",method:"post",data:t})}function V(){return Object(a["a"])({url:"/user/Cluster/getStroMachine",method:"get",params:""})}function z(){return Object(a["a"])({url:"/user/Cluster/getCompMachine",method:"get",params:""})}function F(t){return Object(a["a"])({url:"/user/Cluster/getBackUpStorage",method:"post",data:t})}function B(t){return Object(a["a"])({url:"/user/Cluster/switchShard",method:"post",data:t})}function U(t){return Object(a["a"])({url:"/user/Cluster/SetCpuCgroup",method:"post",data:t})}function q(t){return Object(a["a"])({url:"/user/Cluster/addStorage",method:"post",data:t})}function P(t){return Object(a["a"])({url:"/user/Cluster/updateStorage",method:"post",data:t})}function Q(t){return Object(a["a"])({url:"/user/Cluster/delStorage",method:"post",data:t})}function A(t){return Object(a["a"])({url:"/user/Cluster/rebuildNode",method:"post",data:t})}function J(t){return Object(a["a"])({url:"/user/Cluster/getShardsJobLog",method:"post",data:t})}function H(t){return Object(a["a"])({url:"/user/Cluster/getShardsCount",method:"get",params:t})}function R(t){return Object(a["a"])({url:"/user/Cluster/getCompsCount",method:"get",params:t})}function W(t){return Object(a["a"])({url:"/user/Cluster/getNodesCount",method:"get",params:t})}function Y(t){return Object(a["a"])({url:"/user/Cluster/getOldCluster",method:"get",params:t})}function G(){return Object(a["a"])({url:"/user/Cluster/getBackupStorageList",method:"get",params:""})}function K(t){return Object(a["a"])({url:"/user/Cluster/clusterListError",method:"get",params:t})}function X(t){return Object(a["a"])({url:"/user/Cluster/getShardsName",method:"get",params:t})}function Z(t){return Object(a["a"])({url:"/user/Cluster/getCompDBName",method:"get",params:t})}function tt(t){return Object(a["a"])({url:"/user/Cluster/getCompDBTable",method:"get",params:t})}function et(t){return Object(a["a"])({url:"/user/Cluster/getShardTable",method:"get",params:t})}function rt(t){return Object(a["a"])({url:"/user/Cluster/getOtherShards",method:"get",params:t})}function at(t){return Object(a["a"])({url:"/user/Cluster/expandCluster",method:"post",data:t})}function nt(t){return Object(a["a"])({url:"/user/Cluster/getExpandTableList",method:"post",data:t})}function it(t){return Object(a["a"])({url:"/user/Cluster/setMaxDalay",method:"post",data:t})}function ot(t){return Object(a["a"])({url:"/user/Cluster/getMaxDalay",method:"post",data:t})}function st(t){return Object(a["a"])({url:"/user/Cluster/getMetaCluster",method:"get",params:t})}function ut(t){return Object(a["a"])({url:"/user/Cluster/getBackStorageList",method:"get",params:t})}function lt(t){return Object(a["a"])({url:"/user/Cluster/setVariable",method:"post",data:t})}function ct(t){return Object(a["a"])({url:"/user/Cluster/getVariable",method:"post",data:t})}function dt(t){return Object(a["a"])({url:"/user/Cluster/getWorkMode",method:"post",data:t})}function pt(t){return Object(a["a"])({url:"/user/Cluster/getOneBackUpList",method:"get",params:t})}function mt(t){return Object(a["a"])({url:"/user/Cluster/computeList",method:"get",params:t})}function ft(t){return Object(a["a"])({url:"/user/Cluster/shardList",method:"get",params:t})}function gt(t){return Object(a["a"])({url:"/user/Cluster/getNoSwitchList",method:"post",data:t})}function ht(t){return Object(a["a"])({url:"/user/Cluster/setNoSwitch",method:"post",data:t})}function bt(t){return Object(a["a"])({url:"/user/Cluster/delSwitch",method:"post",data:t})}function _t(t){return Object(a["a"])({url:"/user/Cluster/controlInstance",method:"post",data:t})}function vt(t){return Object(a["a"])({url:"/user/Cluster/getThisShardNodes",method:"post",data:t})}function Ct(t){return Object(a["a"])({url:"/user/Cluster/getClusterMonitor",method:"get",params:t})}function jt(t){return Object(a["a"])({url:"/user/ClusterSetting/tableRepartition",method:"post",data:t})}function yt(t){return Object(a["a"])({url:"/user/ClusterSetting/getPGTableList",method:"get",params:t})}function Ot(t){return Object(a["a"])({url:"/user/ClusterSetting/clusterOptions",method:"get",params:t})}function wt(t){return Object(a["a"])({url:"/user/ClusterSetting/getDataCenters",method:"get",params:t})}function St(t){return Object(a["a"])({url:"/user/Cluster/getLogicalBackUpList",method:"get",params:t})}function xt(t){return Object(a["a"])({url:"/user/ClusterSetting/getRecordLogicalBackup",method:"get",params:t})}function kt(t){return Object(a["a"])({url:"/user/Cluster/updateShard",method:"post",data:t})}function Lt(t){return Object(a["a"])({url:"/user/Cluster/cdcEdit",method:"post",data:t})}function Dt(t){return Object(a["a"])({url:"/user/Cluster/cdcEditWork",method:"post",data:t})}function $t(t){return Object(a["a"])({url:"/user/Cluster/CdcDelete",method:"post",data:t})}function Et(t){return Object(a["a"])({url:"/user/Cluster/getCdcList",method:"post",data:t})}function Nt(t){return Object(a["a"])({url:"/user/Cluster/getCdcWorkerList",method:"post",data:t})}function Mt(t){return Object(a["a"])({url:"/user/Cluster/DeleteCdcWorker",method:"post",data:t})}function Tt(t){return Object(a["a"])({url:"/user/MetaCluster/getMetaClusterList",method:"get",params:t})}function It(t){return Object(a["a"])({url:"/user/Cluster/addESList",method:"post",data:t})}function Vt(t){return Object(a["a"])({url:"/user/Cluster/getESList",method:"get",params:t})}function zt(t){return Object(a["a"])({url:"/user/Cluster/delESList",method:"post",data:t})}},a467:function(t,e,r){"use strict";r.d(e,"c",(function(){return n})),r.d(e,"a",(function(){return i})),r.d(e,"g",(function(){return o})),r.d(e,"b",(function(){return s})),r.d(e,"d",(function(){return u})),r.d(e,"e",(function(){return l})),r.d(e,"f",(function(){return c}));var a=r("b775");function n(t){return Object(a["a"])({url:"/user/Machine/getMachineList",method:"get",params:t})}function i(t){return Object(a["a"])({url:"/user/Machine/createMachine",method:"post",data:t})}function o(t){return Object(a["a"])({url:"/user/Machine/editMachine",method:"post",data:t})}function s(t){return Object(a["a"])({url:"/user/Machine/deleteMachine",method:"post",data:t})}function u(t){return Object(a["a"])({url:"/user/Machine/getMachineNodesList",method:"post",data:{ip:t}})}function l(t){return Object(a["a"])({url:"/user/Machine/getNodeCount",method:"post",data:{ip:t}})}function c(t){return Object(a["a"])({url:"/user/Import/importData",method:"post",headers:{"Content-Type":"multipart/form-data"},data:t})}},a525:function(t,e,r){},bbec:function(t,e,r){},d059:function(t,e,r){"use strict";r("bbec")},ed08:function(t,e,r){"use strict";r.d(e,"g",(function(){return n})),r.d(e,"f",(function(){return i})),r.d(e,"b",(function(){return o})),r.d(e,"d",(function(){return s})),r.d(e,"c",(function(){return u})),r.d(e,"a",(function(){return l})),r.d(e,"e",(function(){return c}));r("53ca"),r("ac1f"),r("00b4"),r("5319"),r("4d63"),r("2c3e"),r("25f0"),r("d3b7"),r("4d90"),r("159b"),r("b64b");var a=r("5c96");function n(t,e){return Object(a["Message"])({message:t,type:e,duration:2e3})}function i(){var t=arguments.length>0&&void 0!==arguments[0]?arguments[0]:"确定执行此操作吗？",e=arguments.length>1&&void 0!==arguments[1]?arguments[1]:"warning";return a["MessageBox"].confirm(t,"提示",{confirmButtonText:"确定",cancelButtonText:"取消",type:e,center:!0})}function o(t){var e=t.split("T");return e[0]+" "+e[1]}function s(){var t=new Date,e="-",r=":",a=t.getFullYear(),n=t.getMonth()+1,i=t.getDate(),o=t.getHours(),s=t.getMinutes(),u=t.getSeconds(),l=["星期一","星期二","星期三","星期四","星期五","星期六","星期天"];l[t.getDay()];n>=1&&n<=9&&(n="0"+n),i>=0&&i<=9&&(i="0"+i),o>=0&&o<=9&&(o="0"+o),s>=0&&s<=9&&(s="0"+s),u>=0&&u<=9&&(u="0"+u);var c=a+e+n+e+i+" "+o+r+s+r+u;return c}function u(t){var e=t.split("-"),r=e[0],a=e[1],n=e[2],i=new Date(r,a,0);i=i.getDate();var o=r,s=parseInt(a)+1;13==s&&(o=parseInt(o)+1,s=1);var u=n,l=new Date(o,s,0);l=l.getDate(),u>l&&(u=l),s<10&&(s="0"+s);var c=o+"-"+s+"-"+u;return c}function l(){for(var t="",e=4,r=new Array(0,1,2,3,4,5,6,7,8,9),a=0;a<e;a++){var n=Math.floor(9*Math.random());t+=r[n]}return t}function c(){var t=arguments.length>0&&void 0!==arguments[0]?arguments[0]:"确定执行此操作吗？",e=arguments.length>1&&void 0!==arguments[1]?arguments[1]:"warning";return a["MessageBox"].prompt(t,"提示",{confirmButtonText:"确定",cancelButtonText:"取消",inputPlaceholder:"请输入code",inputPattern:/^[0-9]\d*$/,inputErrorMessage:"code格式不正确",type:e})}}}]);