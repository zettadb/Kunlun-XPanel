(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-0168b42b"],{"0ccb":function(t,e,a){var r=a("50c4"),n=a("1148"),i=a("1d80"),o=Math.ceil,s=function(t){return function(e,a,s){var u,l,c=String(i(e)),d=c.length,p=void 0===s?" ":String(s),m=r(a);return m<=d||""==p?c:(u=m-d,l=n.call(p,o(u/p.length)),l.length>u&&(l=l.slice(0,u)),t?c+l:l+c)}};t.exports={start:s(!1),end:s(!0)}},"105c":function(t,e,a){},1148:function(t,e,a){"use strict";var r=a("a691"),n=a("1d80");t.exports="".repeat||function(t){var e=String(n(this)),a="",i=r(t);if(i<0||i==1/0)throw RangeError("Wrong number of repetitions");for(;i>0;(i>>>=1)&&(e+=e))1&i&&(a+=e);return a}},"333d":function(t,e,a){"use strict";var r=function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("div",{staticClass:"pagination-container",class:{hidden:t.hidden}},[a("el-pagination",t._b({attrs:{background:t.background,"current-page":t.currentPage,"page-size":t.pageSize,layout:t.layout,"page-sizes":t.pageSizes,total:t.total},on:{"update:currentPage":function(e){t.currentPage=e},"update:current-page":function(e){t.currentPage=e},"update:pageSize":function(e){t.pageSize=e},"update:page-size":function(e){t.pageSize=e},"size-change":t.handleSizeChange,"current-change":t.handleCurrentChange}},"el-pagination",t.$attrs,!1))],1)},n=[];a("a9e3");Math.easeInOutQuad=function(t,e,a,r){return t/=r/2,t<1?a/2*t*t+e:(t--,-a/2*(t*(t-2)-1)+e)};var i=function(){return window.requestAnimationFrame||window.webkitRequestAnimationFrame||window.mozRequestAnimationFrame||function(t){window.setTimeout(t,1e3/60)}}();function o(t){document.documentElement.scrollTop=t,document.body.parentNode.scrollTop=t,document.body.scrollTop=t}function s(){return document.documentElement.scrollTop||document.body.parentNode.scrollTop||document.body.scrollTop}function u(t,e,a){var r=s(),n=t-r,u=20,l=0;e="undefined"===typeof e?500:e;var c=function t(){l+=u;var s=Math.easeInOutQuad(l,r,n,e);o(s),l<e?i(t):a&&"function"===typeof a&&a()};c()}var l={name:"Pagination",props:{total:{required:!0,type:Number},page:{type:Number,default:1},limit:{type:Number,default:20},pageSizes:{type:Array,default:function(){return[10,20,30,50]}},layout:{type:String,default:"total, sizes, prev, pager, next, jumper"},background:{type:Boolean,default:!0},autoScroll:{type:Boolean,default:!0},hidden:{type:Boolean,default:!1}},computed:{currentPage:{get:function(){return this.page},set:function(t){this.$emit("update:page",t)}},pageSize:{get:function(){return this.limit},set:function(t){this.$emit("update:limit",t)}}},methods:{handleSizeChange:function(t){this.$emit("pagination",{page:this.currentPage,limit:t}),this.autoScroll&&u(0,800)},handleCurrentChange:function(t){this.$emit("pagination",{page:t,limit:this.pageSize}),this.autoScroll&&u(0,800)}}},c=l,d=(a("c3ac"),a("2877")),p=Object(d["a"])(c,r,n,!1,null,"65877abe",null);e["a"]=p.exports},"4d90":function(t,e,a){"use strict";var r=a("23e7"),n=a("0ccb").start,i=a("9a0c");r({target:"String",proto:!0,forced:i},{padStart:function(t){return n(this,t,arguments.length>1?arguments[1]:void 0)}})},"53ca":function(t,e,a){"use strict";a.d(e,"a",(function(){return r}));a("a4d3"),a("e01a"),a("d3b7"),a("d28b"),a("3ca3"),a("ddb0");function r(t){return r="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(t){return typeof t}:function(t){return t&&"function"==typeof Symbol&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t},r(t)}},5420:function(t,e,a){"use strict";a.r(e);var r=function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("div",{staticClass:"app-container"},[a("div",{staticClass:"filter-container"},[a("div",{staticClass:"table-list-search-wrap"},[a("el-input",{staticClass:"list_search_keyword",attrs:{placeholder:"可输入ip搜索"},nativeOn:{keyup:function(e){return!e.type.indexOf("key")&&t._k(e.keyCode,"enter",13,e.key,"Enter")?null:t.handleFilter(e)}},model:{value:t.listQuery.hostaddr,callback:function(e){t.$set(t.listQuery,"hostaddr",e)},expression:"listQuery.hostaddr"}}),a("el-button",{attrs:{icon:"el-icon-search"},on:{click:t.handleFilter}},[t._v(" 查询 ")]),a("el-button",{attrs:{icon:"el-icon-refresh-right"},on:{click:t.handleClear}},[t._v(" 重置 ")]),a("div",{directives:[{name:"show",rawName:"v-show",value:!0===t.installStatus,expression:"installStatus===true"}],staticClass:"info",domProps:{textContent:t._s(t.info)}})],1),a("div",{staticClass:"table-list-wrap"})]),a("el-table",{directives:[{name:"loading",rawName:"v-loading",value:t.listLoading,expression:"listLoading"}],key:t.tableKey,staticStyle:{width:"100%","margin-bottom":"20px"},attrs:{data:t.list,border:"","highlight-current-row":""}},[a("el-table-column",{attrs:{type:"index",align:"center",label:"序号",width:"50"}}),a("el-table-column",{attrs:{label:"IP地址",align:"center"},scopedSlots:t._u([{key:"default",fn:function(e){var r=e.row;return[a("span",{staticClass:"link-type",on:{click:function(e){return t.handleDetail(r)}}},[t._v(t._s(r.hostaddr))])]}}])}),a("el-table-column",{attrs:{prop:"rack_id",align:"center",label:"机架编号"}}),a("el-table-column",{attrs:{prop:"total_cpu_cores",align:"center",label:"cpu核数"}}),a("el-table-column",{attrs:{prop:"total_mem",align:"center",label:"总内存"},scopedSlots:t._u([{key:"default",fn:function(e){return[t._v(t._s(e.row.total_mem+"MB"))]}}])}),a("el-table-column",{attrs:{prop:"status",align:"center",label:"状态"},scopedSlots:t._u([{key:"default",fn:function(e){return["online"===e.row.status?a("span",{staticStyle:{color:"#00ed37"}},[t._v("在线")]):"offline"===e.row.status?a("span",{staticStyle:{color:"red"}},[t._v("离线")]):t._e()]}}])}),a("el-table-column",{attrs:{label:"操作",align:"center",width:"300","class-name":"small-padding fixed-width"},scopedSlots:t._u([{key:"default",fn:function(e){var r=e.row,n=e.$index;return[a("el-button",{attrs:{type:"primary",size:"mini"},on:{click:function(e){return t.gotolink(r.hostaddr)}}},[t._v("节点视图")]),"Y"===t.machine_priv?a("el-button",{attrs:{type:"primary",size:"mini"},on:{click:function(e){return t.handleUpdate(r)}}},[t._v("编辑")]):t._e(),"Y"===t.machine_drop_priv?a("el-button",{attrs:{size:"mini",type:"danger"},on:{click:function(e){return t.handleDelete(r,n)}}},[t._v("删除")]):t._e()]}}])})],1),a("pagination",{directives:[{name:"show",rawName:"v-show",value:t.total>0,expression:"total>0"}],attrs:{total:t.total,page:t.listQuery.pageNo,limit:t.listQuery.pageSize},on:{"update:page":function(e){return t.$set(t.listQuery,"pageNo",e)},"update:limit":function(e){return t.$set(t.listQuery,"pageSize",e)},pagination:t.getList}}),a("el-dialog",{attrs:{title:t.textMap[t.dialogStatus],visible:t.dialogFormVisible,"custom-class":"single_dal_view"},on:{"update:visible":function(e){t.dialogFormVisible=e}}},[a("el-form",{ref:"dataForm",attrs:{rules:t.rules,model:t.temp,"label-position":"left","label-width":"140px"}},[a("el-form-item",{attrs:{label:"IP地址:",prop:"hostaddr"}},[a("el-input",{attrs:{placeholder:"请输入IP地址",disabled:"detail"===t.dialogStatus||"update"===t.dialogStatus},model:{value:t.temp.hostaddr,callback:function(e){t.$set(t.temp,"hostaddr",e)},expression:"temp.hostaddr"}})],1),a("el-form-item",{attrs:{label:"机架编号:",prop:"rack_id"}},[a("el-input",{attrs:{placeholder:"请输入机架编号",autocomplete:"new-password",disabled:"detail"===t.dialogStatus},model:{value:t.temp.rack_id,callback:function(e){t.$set(t.temp,"rack_id",e)},expression:"temp.rack_id"}})],1),a("el-form-item",{attrs:{label:"日志目录:",prop:"logdir"}},[a("el-input",{attrs:{placeholder:"请输入日志目录",disabled:"detail"===t.dialogStatus},model:{value:t.temp.logdir,callback:function(e){t.$set(t.temp,"logdir",e)},expression:"temp.logdir"}})],1),a("el-form-item",{attrs:{label:"wal日志目录:",prop:"wal_log_dir"}},[a("el-input",{attrs:{placeholder:"请输入wal日志目录",disabled:"detail"===t.dialogStatus},model:{value:t.temp.wal_log_dir,callback:function(e){t.$set(t.temp,"wal_log_dir",e)},expression:"temp.wal_log_dir"}})],1),a("el-form-item",{attrs:{label:"存储节点数据目录:",prop:"datadir"}},[a("el-input",{attrs:{placeholder:"请输入存储节点数据目录",disabled:"detail"===t.dialogStatus},model:{value:t.temp.datadir,callback:function(e){t.$set(t.temp,"datadir",e)},expression:"temp.datadir"}})],1),a("el-form-item",{attrs:{label:"计算节点数据目录:",prop:"comp_datadir"}},[a("el-input",{attrs:{placeholder:"请输入计算节点数据目录",disabled:"detail"===t.dialogStatus},model:{value:t.temp.comp_datadir,callback:function(e){t.$set(t.temp,"comp_datadir",e)},expression:"temp.comp_datadir"}})],1),a("el-form-item",{attrs:{label:"总内存:",prop:"total_mem"}},[a("el-input",{attrs:{placeholder:"请输入总内存",disabled:"detail"===t.dialogStatus},model:{value:t.temp.total_mem,callback:function(e){t.$set(t.temp,"total_mem",e)},expression:"temp.total_mem"}},[a("i",{staticStyle:{"font-style":"normal","margin-right":"10px","line-height":"30px"},attrs:{slot:"suffix"},slot:"suffix"},[t._v("MB")])])],1),a("el-form-item",{attrs:{label:"cpu核数:",prop:"total_cpu_cores"}},[a("el-input",{attrs:{placeholder:"请输入cpu核数",disabled:"detail"===t.dialogStatus},model:{value:t.temp.total_cpu_cores,callback:function(e){t.$set(t.temp,"total_cpu_cores",e)},expression:"temp.total_cpu_cores"}})],1)],1),a("div",{staticClass:"dialog-footer",attrs:{slot:"footer"},slot:"footer"},[a("el-button",{directives:[{name:"show",rawName:"v-show",value:!t.dialogDetail,expression:"!dialogDetail"}],on:{click:function(e){t.dialogFormVisible=!1}}},[t._v("关闭")]),a("el-button",{directives:[{name:"show",rawName:"v-show",value:!t.dialogDetail,expression:"!dialogDetail"}],attrs:{type:"primary"},on:{click:function(e){"create"===t.dialogStatus?t.createData():t.updateData(t.row)}}},[t._v("确认")])],1)],1)],1)},n=[],i=(a("ac1f"),a("1276"),a("00b4"),a("ed08")),o=a("a467"),s=a("c2b2");function u(t){return Object(s["a"])({url:"/user/Machine/createMachine",method:"post",data:t})}function l(t){return Object(s["a"])({url:"/user/Machine/editMachine",method:"post",data:t})}function c(t){return Object(s["a"])({url:"/user/Machine/deleteMachine",method:"post",data:t})}function d(t){return Object(s["a"])({url:"/user/Machine/getStatus",method:"post",data:t})}function p(t){return Object(s["a"])({url:"/user/Machine/prometheusEnable",method:"post",data:t})}var m=a("a665"),f=a("578a"),g=a("333d"),h={name:"account",components:{Pagination:g["a"]},data:function(){var t=function(t,e,a){var r=/^((2(5[0-5]|[0-4]\d))|[0-1]?\d{1,2})(\.((2(5[0-5]|[0-4]\d))|[0-1]?\d{1,2})){3}$/,n=e.split(","),i=!0;if(n.length)for(var o=0;o<n.length;o++)0==r.test(n[o])&&(i=!1);if(""==e)return a(new Error("请输入IP地址"));i?a():a(new Error("请输入正确对IP地址"))},e=function(t,e,a){e?a():a(new Error("请输入存储节点数据目录"))},a=function(t,e,a){e?a():a(new Error("请输入日志目录"))},r=function(t,e,a){e?a():a(new Error("请输入wal日志目录"))},n=function(t,e,a){e?a():a(new Error("请输入计算节点数据目录"))},i=function(t,e,a){e?/^[0-9]+$/.test(e)?a():a(new Error("cpu核数只能输入数字")):a(new Error("请输入cpu核数"))},o=function(t,e,a){e?/^[0-9]+$/.test(e)?a():a(new Error("总内存只能输入数字")):a(new Error("请输入总内存"))};return{tableKey:0,list:null,listLoading:!0,searchLoading:!1,total:0,listQuery:{pageNo:1,pageSize:10,hostaddr:""},temp:{hostaddr:"",rack_id:"",datadir:"",logdir:"",wal_log_dir:"",comp_datadir:"",total_cpu_cores:"",total_mem:""},dialogFormVisible:!1,dialogEditVisible:!1,dialogStatus:"",textMap:{update:"编辑计算机",create:"新增计算机",detail:"详情"},dialogDetail:!1,message_tips:"",message_type:"",installStatus:!1,info:"",row:{},machine_add_priv:JSON.parse(sessionStorage.getItem("priv")).machine_add_priv,machine_drop_priv:JSON.parse(sessionStorage.getItem("priv")).machine_drop_priv,machine_priv:JSON.parse(sessionStorage.getItem("priv")).machine_priv,rules:{hostaddr:[{required:!0,trigger:"blur",validator:t}],datadir:[{required:!0,trigger:"blur",validator:e}],logdir:[{required:!0,trigger:"blur",validator:a}],wal_log_dir:[{required:!0,trigger:"blur",validator:r}],comp_datadir:[{required:!0,trigger:"blur",validator:n}],total_mem:[{required:!0,trigger:"blur",validator:o}],total_cpu_cores:[{required:!0,trigger:"blur",validator:i}]}}},created:function(){this.getList()},methods:{handleFilter:function(){this.listQuery.pageNo=1,this.getList()},handleClear:function(){this.listQuery.hostaddr="",this.listQuery.pageNo=1,this.getList()},getList:function(){var t=this;this.listLoading=!0;var e=Object.assign({},this.listQuery);Object(o["a"])(e).then((function(e){var a={job_id:"",job_type:"get_machine_summary"};a.version=f["m"][0].ver,a.timestamp=f["k"][0].time+"",a.paras={},Object(m["l"])(a).then((function(a){for(var r=a.attachment.list_machine,n=0;n<e.total;n++)for(var i=e.list[n],o=0;o<r.length;o++){var s=r[o].hostaddr;s==i.hostaddr&&t.$set(i,"status",r[o].status)}t.list=e.list,t.total=e.total,setTimeout((function(){t.listLoading=!1}),500)}))}))},resetTemp:function(){this.temp={hostaddr:"",rack_id:"",datadir:"",logdir:"",wal_log_dir:"",comp_datadir:"",total_cpu_cores:"",total_mem:""}},handleCreate:function(){var t=this;this.resetTemp(),this.dialogStatus="create",this.dialogFormVisible=!0,this.dialogDetail=!1,this.$nextTick((function(){t.$refs.dataForm.clearValidate()}))},createData:function(){var t=this;this.$refs["dataForm"].validate((function(e){if(e){var a={};a.user_name=sessionStorage.getItem("login_username"),a.job_id="",a.job_type="create_machine",a.version=f["m"][0].ver,a.timestamp=f["k"][0].time+"",a.paras=Object.assign({},t.temp),u(a).then((function(e){var a=e;if("accept"==a.status){t.dialogFormVisible=!1,t.message_tips="正在新增计算机...",t.message_type="success";var r=0,n=setInterval((function(){t.getStatus(n,a.job_id,r++)}),1e3),o={job_id:"",job_type:"update_prometheus"};o["version"]=f["m"][0].ver,o["timestamp"]=f["k"][0].time+"",o["user_name"]=sessionStorage.getItem("login_username"),o["paras"]={},p(o).then((function(t){}))}else t.message_tips=a.error_info,t.message_type="error";Object(i["e"])(t.message_tips,t.message_type)}))}}))},handleDetail:function(t){this.dialogStatus="detail",this.dialogFormVisible=!0,this.temp=Object.assign({},t),this.dialogDetail=!0},handleUpdate:function(t){var e=this;this.temp=Object.assign({},t),this.dialogStatus="update",this.dialogFormVisible=!0,this.dialogDetail=!1,this.temp.old_mobile=t.mobile,this.$nextTick((function(){e.$refs["dataForm"].clearValidate()}))},updateData:function(){var t=this;this.$refs["dataForm"].validate((function(e){if(e){var a={};a.user_name=sessionStorage.getItem("login_username"),a.job_id="",a.job_type="update_machine",a.version=f["m"][0].ver,a.timestamp=f["k"][0].time+"",a.paras=Object.assign({},t.temp),l(a).then((function(e){var a=e;if("accept"==a.status){t.dialogFormVisible=!1,t.message_tips="正在编辑计算机...",t.message_type="success";var r=0,n=setInterval((function(){t.getStatus(n,a.job_id,r++)}),1e3),o={job_id:"",job_type:"update_prometheus"};o["version"]=f["m"][0].ver,o["timestamp"]=f["k"][0].time+"",o["user_name"]=sessionStorage.getItem("login_username"),o["paras"]={},p(o).then((function(t){}))}else t.message_tips=a.error_info,t.message_type="error";Object(i["e"])(t.message_tips,t.message_type)}))}}))},handleDelete:function(t){var e=this;Object(i["d"])("此操作将永久删除该数据, 是否继续?").then((function(){var a={};a.user_name=sessionStorage.getItem("login_username"),a.job_id="",a.job_type="delete_machine",a.version=f["m"][0].ver,a.timestamp=f["k"][0].time+"",a.paras={hostaddr:t.hostaddr},c(a).then((function(t){var a=t;if("accept"==a.status){e.dialogFormVisible=!1,e.message_tips="正在删除计算机...",e.message_type="success";var r=0,n=setInterval((function(){e.getStatus(n,a.job_id,r++)}),1e3),o={job_id:"",job_type:"update_prometheus"};o["version"]=f["m"][0].ver,o["timestamp"]=f["k"][0].time+"",o["user_name"]=sessionStorage.getItem("login_username"),o["paras"]={},p(o).then((function(t){}))}else e.message_tips=a.error_info,e.message_type="error";Object(i["e"])(e.message_tips,e.message_type)}))})).catch((function(){console.log("quxiao"),Object(i["e"])("已取消删除","info")}))},gotolink:function(t){var e=this;Object(o["d"])(t).then((function(a){var r=a.total;0==r?Object(i["e"])("该计算机上还没有节点","error"):e.$router.push({path:"/machine/node",query:{hostaddr:t}})}))},getStatus:function(t,e,a){var r=this;setTimeout((function(){var n={job_type:"get_status"};n.version=f["m"][0].ver,n.job_id=e,n.timestamp=f["k"][0].time+"",n.paras={},d(n).then((function(e){"done"==e.status||"failed"==e.status?(clearInterval(t),r.info=e.error_info,"done"==e.status?r.getList():r.installStatus=!0):(r.info=e.error_info,r.installStatus=!0)})),a>=86400&&clearInterval(t)}),0)}}},b=h,_=a("2877"),v=Object(_["a"])(b,r,n,!1,null,null,null);e["default"]=v.exports},"9a0c":function(t,e,a){var r=a("342f");t.exports=/Version\/10\.\d+(\.\d+)?( Mobile\/\w+)? Safari\//.test(r)},a467:function(t,e,a){"use strict";a.d(e,"a",(function(){return n})),a.d(e,"b",(function(){return i})),a.d(e,"d",(function(){return o})),a.d(e,"c",(function(){return s})),a.d(e,"e",(function(){return u}));var r=a("b775");function n(t){return Object(r["a"])({url:"/user/Machine/getMachineList",method:"get",params:t})}function i(t){return Object(r["a"])({url:"/user/Machine/getMachineNodesList",method:"post",data:{ip:t}})}function o(t){return Object(r["a"])({url:"/user/Machine/getNodeCount",method:"post",data:{ip:t}})}function s(t){return Object(r["a"])({url:"/user/Machine/getNodeList",method:"post",data:t})}function u(t){return Object(r["a"])({url:"/user/Machine/getUsedList",method:"post",data:t})}},a665:function(t,e,a){"use strict";a.d(e,"l",(function(){return n})),a.d(e,"f",(function(){return i})),a.d(e,"c",(function(){return o})),a.d(e,"a",(function(){return s})),a.d(e,"b",(function(){return u})),a.d(e,"o",(function(){return l})),a.d(e,"g",(function(){return c})),a.d(e,"e",(function(){return d})),a.d(e,"v",(function(){return p})),a.d(e,"s",(function(){return m})),a.d(e,"r",(function(){return f})),a.d(e,"i",(function(){return g})),a.d(e,"h",(function(){return h})),a.d(e,"j",(function(){return b})),a.d(e,"w",(function(){return _})),a.d(e,"x",(function(){return v})),a.d(e,"y",(function(){return y})),a.d(e,"z",(function(){return S})),a.d(e,"t",(function(){return j})),a.d(e,"u",(function(){return w})),a.d(e,"m",(function(){return O})),a.d(e,"q",(function(){return k})),a.d(e,"d",(function(){return C})),a.d(e,"B",(function(){return I})),a.d(e,"k",(function(){return N})),a.d(e,"p",(function(){return x})),a.d(e,"n",(function(){return M})),a.d(e,"A",(function(){return E}));var r=a("c2b2");function n(t){return Object(r["a"])({url:"/user/allMachineStatus",method:"post",data:t})}function i(t){return Object(r["a"])({url:"/user/Cluster/createCluster",method:"post",data:t})}function o(t){return Object(r["a"])({url:"/user/Cluster/addShards",method:"post",data:t})}function s(t){return Object(r["a"])({url:"/user/Cluster/addComps",method:"post",data:t})}function u(t){return Object(r["a"])({url:"/user/Cluster/addNodes",method:"post",data:t})}function l(t){return Object(r["a"])({url:"/user/Cluster/getStatus",method:"post",data:t})}function c(t){return Object(r["a"])({url:"/user/Cluster/delCluster",method:"post",data:t})}function d(t){return Object(r["a"])({url:"/user/Cluster/backUpCluster",method:"post",data:t})}function p(t){return Object(r["a"])({url:"/user/Cluster/restoreCluster",method:"post",data:t})}function m(t){return Object(r["a"])({url:"/user/Cluster/postgresEnable",method:"post",data:t})}function f(t){return Object(r["a"])({url:"/user/Cluster/mysqlEnable",method:"post",data:t})}function g(t){return Object(r["a"])({url:"/user/Cluster/delShard",method:"post",data:t})}function h(t){return Object(r["a"])({url:"/user/Cluster/delComp",method:"post",data:t})}function b(t){return Object(r["a"])({url:"/user/Cluster/delSnode",method:"post",data:t})}function _(t){return Object(r["a"])({url:"/user/Cluster/controlInstance",method:"post",data:t})}function v(t){return Object(r["a"])({url:"/user/Cluster/controlInstance",method:"post",data:t})}function y(t){return Object(r["a"])({url:"/user/Cluster/controlInstance",method:"post",data:t})}function S(t){return Object(r["a"])({url:"/user/Cluster/controlInstance",method:"post",data:t})}function j(t){return Object(r["a"])({url:"/user/Cluster/controlInstance",method:"post",data:t})}function w(t){return Object(r["a"])({url:"/user/Cluster/controlInstance",method:"post",data:t})}function O(t){return Object(r["a"])({url:"/user/Cluster/getBackUpStorage",method:"post",data:t})}function k(t){return Object(r["a"])({url:"/user/Cluster/getStorageList",method:"post",data:t})}function C(t){return Object(r["a"])({url:"/user/Cluster/addStorage",method:"post",data:t})}function I(t){return Object(r["a"])({url:"/user/Cluster/updateStorage",method:"post",data:t})}function N(t){return Object(r["a"])({url:"/user/Cluster/delStorage",method:"post",data:t})}function x(t){return Object(r["a"])({url:"/Meta/getMetaPrimary",method:"POST",data:t})}function M(t){return Object(r["a"])({url:"/Meta/getClusterDetail",method:"post",data:t})}function E(t){return Object(r["a"])({url:"/Meta/switchShard",method:"post",data:t})}},a9e3:function(t,e,a){"use strict";var r=a("83ab"),n=a("da84"),i=a("94ca"),o=a("6eeb"),s=a("5135"),u=a("c6b6"),l=a("7156"),c=a("c04e"),d=a("d039"),p=a("7c73"),m=a("241c").f,f=a("06cf").f,g=a("9bf2").f,h=a("58a8").trim,b="Number",_=n[b],v=_.prototype,y=u(p(v))==b,S=function(t){var e,a,r,n,i,o,s,u,l=c(t,!1);if("string"==typeof l&&l.length>2)if(l=h(l),e=l.charCodeAt(0),43===e||45===e){if(a=l.charCodeAt(2),88===a||120===a)return NaN}else if(48===e){switch(l.charCodeAt(1)){case 66:case 98:r=2,n=49;break;case 79:case 111:r=8,n=55;break;default:return+l}for(i=l.slice(2),o=i.length,s=0;s<o;s++)if(u=i.charCodeAt(s),u<48||u>n)return NaN;return parseInt(i,r)}return+l};if(i(b,!_(" 0o1")||!_("0b1")||_("+0x1"))){for(var j,w=function(t){var e=arguments.length<1?0:t,a=this;return a instanceof w&&(y?d((function(){v.valueOf.call(a)})):u(a)!=b)?l(new _(S(e)),a,w):S(e)},O=r?m(_):"MAX_VALUE,MIN_VALUE,NaN,NEGATIVE_INFINITY,POSITIVE_INFINITY,EPSILON,isFinite,isInteger,isNaN,isSafeInteger,MAX_SAFE_INTEGER,MIN_SAFE_INTEGER,parseFloat,parseInt,isInteger".split(","),k=0;O.length>k;k++)s(_,j=O[k])&&!s(w,j)&&g(w,j,f(_,j));w.prototype=v,v.constructor=w,o(n,b,w)}},c2b2:function(t,e,a){"use strict";a("d3b7");var r=a("bc3a"),n=a.n(r),i=a("5c96"),o=(a("4360"),a("5f87"),JSON.parse(sessionStorage.getItem("response")).API_URL),s=n.a.create({baseURL:o+"/HttpService/Emit",timeout:6e4});s.interceptors.request.use((function(t){sessionStorage.getItem("zettadb_vue_token");return t}),(function(t){return console.log(t),Promise.reject(t)})),s.interceptors.response.use((function(t){var e=t.data;return e.code+="",e}),(function(t){return Object(i["Message"])({message:t.message,type:"error",duration:5e3}),Promise.reject(t)})),e["a"]=s},c3ac:function(t,e,a){"use strict";a("105c")},ed08:function(t,e,a){"use strict";a.d(e,"e",(function(){return n})),a.d(e,"d",(function(){return i})),a.d(e,"a",(function(){return o})),a.d(e,"c",(function(){return s})),a.d(e,"b",(function(){return u}));a("53ca"),a("ac1f"),a("00b4"),a("5319"),a("4d63"),a("2c3e"),a("25f0"),a("d3b7"),a("4d90"),a("1276"),a("159b");var r=a("5c96");function n(t,e){return Object(r["Message"])({message:t,type:e,duration:2e3})}function i(){var t=arguments.length>0&&void 0!==arguments[0]?arguments[0]:"确定执行此操作吗？",e=arguments.length>1&&void 0!==arguments[1]?arguments[1]:"warning";return r["MessageBox"].confirm(t,"提示",{confirmButtonText:"确定",cancelButtonText:"取消",type:e,center:!0})}function o(t){var e=t.split("T");return e[0]+" "+e[1]}function s(){var t=new Date,e="-",a=":",r=t.getFullYear(),n=t.getMonth()+1,i=t.getDate(),o=t.getHours(),s=t.getMinutes(),u=t.getSeconds(),l=["星期一","星期二","星期三","星期四","星期五","星期六","星期天"];l[t.getDay()];n>=1&&n<=9&&(n="0"+n),i>=0&&i<=9&&(i="0"+i),o>=0&&o<=9&&(o="0"+o),s>=0&&s<=9&&(s="0"+s),u>=0&&u<=9&&(u="0"+u);var c=r+e+n+e+i+" "+o+a+s+a+u;return c}function u(t){var e=t.split("-"),a=e[0],r=e[1],n=e[2],i=new Date(a,r,0);i=i.getDate();var o=a,s=parseInt(r)+1;13==s&&(o=parseInt(o)+1,s=1);var u=n,l=new Date(o,s,0);l=l.getDate(),u>l&&(u=l),s<10&&(s="0"+s);var c=o+"-"+s+"-"+u;return c}}}]);