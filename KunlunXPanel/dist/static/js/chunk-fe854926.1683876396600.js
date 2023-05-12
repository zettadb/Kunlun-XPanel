(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-fe854926"],{"333d":function(t,e,n){"use strict";var r=function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",{staticClass:"pagination-container",class:{hidden:t.hidden}},[n("el-pagination",t._b({attrs:{background:t.background,"current-page":t.currentPage,"page-size":t.pageSize,layout:t.layout,"page-sizes":t.pageSizes,total:t.total},on:{"update:currentPage":function(e){t.currentPage=e},"update:current-page":function(e){t.currentPage=e},"update:pageSize":function(e){t.pageSize=e},"update:page-size":function(e){t.pageSize=e},"size-change":t.handleSizeChange,"current-change":t.handleCurrentChange}},"el-pagination",t.$attrs,!1))],1)},a=[];n("a9e3");Math.easeInOutQuad=function(t,e,n,r){return t/=r/2,t<1?n/2*t*t+e:(t--,-n/2*(t*(t-2)-1)+e)};var u=function(){return window.requestAnimationFrame||window.webkitRequestAnimationFrame||window.mozRequestAnimationFrame||function(t){window.setTimeout(t,1e3/60)}}();function o(t){document.documentElement.scrollTop=t,document.body.parentNode.scrollTop=t,document.body.scrollTop=t}function s(){return document.documentElement.scrollTop||document.body.parentNode.scrollTop||document.body.scrollTop}function l(t,e,n){var r=s(),a=t-r,l=20,i=0;e="undefined"===typeof e?500:e;var c=function t(){i+=l;var s=Math.easeInOutQuad(i,r,a,e);o(s),i<e?u(t):n&&"function"===typeof n&&n()};c()}var i={name:"Pagination",props:{total:{required:!0,type:Number},page:{type:Number,default:1},limit:{type:Number,default:20},pageSizes:{type:Array,default:function(){return[10,20,30,50]}},layout:{type:String,default:"total, sizes, prev, pager, next, jumper"},background:{type:Boolean,default:!0},autoScroll:{type:Boolean,default:!0},hidden:{type:Boolean,default:!1}},computed:{currentPage:{get:function(){return this.page},set:function(t){this.$emit("update:page",t)}},pageSize:{get:function(){return this.limit},set:function(t){this.$emit("update:limit",t)}}},methods:{handleSizeChange:function(t){this.$emit("pagination",{page:this.currentPage,limit:t}),this.autoScroll&&l(0,800)},handleCurrentChange:function(t){this.$emit("pagination",{page:t,limit:this.pageSize}),this.autoScroll&&l(0,800)}}},c=i,d=(n("d059"),n("2877")),p=Object(d["a"])(c,r,a,!1,null,"65877abe",null);e["a"]=p.exports},"7b87":function(t,e,n){"use strict";n.r(e);var r=function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",{staticClass:"icons-container"},[n("el-tabs",{attrs:{type:"border-card"},on:{"tab-click":t.handleClick},model:{value:t.activeName,callback:function(e){t.activeName=e},expression:"activeName"}},[n("el-tab-pane",{attrs:{label:"逻辑备份",name:"first"}},[n("LogicalBackup")],1),n("el-tab-pane",{attrs:{label:"物理备份",name:"second"}},[n("BackUp")],1)],1)],1)},a=[],u=function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",{staticClass:"app-container"},[n("div",{staticClass:"filter-container"},[n("div",{staticClass:"table-list-search-wrap"},[n("el-select",{staticClass:"list_search_select",attrs:{placeholder:"请选择备份类型"},model:{value:t.listQuery.backup_type,callback:function(e){t.$set(t.listQuery,"backup_type",e)},expression:"listQuery.backup_type"}},[n("el-option",{attrs:{label:"存储",value:"storage"}}),n("el-option",{attrs:{label:"计算",value:"compute"}})],1),n("el-select",{staticClass:"list_search_select",attrs:{placeholder:"请选择状态"},model:{value:t.listQuery.status,callback:function(e){t.$set(t.listQuery,"status",e)},expression:"listQuery.status"}},[n("el-option",{attrs:{label:"not_started",value:"not_started"}}),n("el-option",{attrs:{label:"ongoing",value:"ongoing"}}),n("el-option",{attrs:{label:"done",value:"done"}}),n("el-option",{attrs:{label:"failed",value:"failed"}})],1),n("el-button",{attrs:{icon:"el-icon-search"},on:{click:t.handleFilter}},[t._v("查询")]),n("el-button",{attrs:{icon:"el-icon-refresh-right"},on:{click:t.handleClear}},[t._v("重置")])],1)]),n("el-table",{directives:[{name:"loading",rawName:"v-loading",value:t.listLoading,expression:"listLoading"}],key:t.tableKey,staticStyle:{width:"100%","margin-bottom":"20px"},attrs:{data:t.list,border:"","highlight-current-row":""}},[n("el-table-column",{attrs:{type:"index",align:"center",label:"序号",width:"50"}}),n("el-table-column",{attrs:{label:"集群名称",align:"center"},scopedSlots:t._u([{key:"default",fn:function(e){var r=e.row;return[n("span",{staticClass:"link-type",on:{click:function(e){return t.handleDetail(r)}}},[t._v(t._s(r.cluster_name))])]}}])}),n("el-table-column",{attrs:{prop:"nick_name",align:"center",label:"业务名称"}}),n("el-table-column",{attrs:{prop:"shard_name",align:"center",label:"shard名称"}}),n("el-table-column",{attrs:{prop:"comp_name",align:"center",label:"计算节点名称"}}),n("el-table-column",{attrs:{prop:"backup_type",align:"center",label:"备份类型"},scopedSlots:t._u([{key:"default",fn:function(e){return["storage"===e.row.backup_type?n("span",[t._v("存储")]):"compute"===e.row.backup_type?n("span",[t._v("计算")]):n("span")]}}])}),n("el-table-column",{attrs:{prop:"status",align:"center",label:"状态"},scopedSlots:t._u([{key:"default",fn:function(e){return["failed"===e.row.status?n("span",{staticStyle:{color:"red"}},[t._v(t._s(e.row.status))]):n("span",[t._v(t._s(e.row.status))])]}}])}),n("el-table-column",{attrs:{prop:"info",align:"center","show-overflow-tooltip":!0,label:"结果信息"}}),n("el-table-column",{attrs:{prop:"start_ts",align:"center",label:"开始时间"}}),n("el-table-column",{attrs:{prop:"end_ts",align:"center",label:"结束时间"}})],1),n("pagination",{directives:[{name:"show",rawName:"v-show",value:t.total>0,expression:"total>0"}],attrs:{total:t.total,page:t.listQuery.pageNo,limit:t.listQuery.pageSize},on:{"update:page":function(e){return t.$set(t.listQuery,"pageNo",e)},"update:limit":function(e){return t.$set(t.listQuery,"pageSize",e)},pagination:t.getList}})],1)},o=[],s=n("82b0"),l=n("333d"),i={name:"Operation",components:{Pagination:l["a"]},data:function(){return{tableKey:0,list:null,listLoading:!0,searchLoading:!1,total:0,listQuery:{pageNo:1,pageSize:10,username:"",backup_type:"",status:""},dialogFormVisible:!1,dialogEditVisible:!1,dialogStatus:"",textMap:{detail:"详情"},dialogDetail:!1,message_tips:"",message_type:"",row:{}}},created:function(){this.getList()},methods:{handleFilter:function(){this.listQuery.pageNo=1,this.getList()},handleClear:function(){this.listQuery.username="",this.listQuery.backup_type="",this.listQuery.status="",this.listQuery.pageNo=1,this.getList()},getList:function(){var t=this;this.listLoading=!0;var e=Object.assign({},this.listQuery);Object(s["u"])(e).then((function(e){t.list=e.list,t.total=e.total,setTimeout((function(){t.listLoading=!1}),500)}))},handleDetail:function(t){this.dialogStatus="detail",this.dialogFormVisible=!0,this.temp=Object.assign({},t),this.dialogDetail=!0}}},c=i,d=n("2877"),p=Object(d["a"])(c,u,o,!1,null,null,null),f=p.exports,b=function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",{staticClass:"app-container"},[n("div",{staticClass:"filter-container"},[n("div",{staticClass:"table-list-search-wrap"},[n("el-select",{staticClass:"list_search_select",attrs:{placeholder:"请选择状态"},model:{value:t.listQuery.status,callback:function(e){t.$set(t.listQuery,"status",e)},expression:"listQuery.status"}},[n("el-option",{attrs:{label:"not_started",value:"not_started"}}),n("el-option",{attrs:{label:"ongoing",value:"ongoing"}}),n("el-option",{attrs:{label:"done",value:"done"}}),n("el-option",{attrs:{label:"failed",value:"failed"}})],1),n("el-button",{attrs:{icon:"el-icon-search"},on:{click:t.handleFilter}},[t._v("查询")]),n("el-button",{attrs:{icon:"el-icon-refresh-right"},on:{click:t.handleClear}},[t._v("重置")])],1)]),n("el-table",{directives:[{name:"loading",rawName:"v-loading",value:t.listLoading,expression:"listLoading"}],key:t.tableKey,staticStyle:{width:"100%","margin-bottom":"20px"},attrs:{data:t.list,border:"","highlight-current-row":""}},[n("el-table-column",{attrs:{type:"index",align:"center",label:"序号",width:"50"}}),n("el-table-column",{attrs:{label:"集群名称",align:"center"},scopedSlots:t._u([{key:"default",fn:function(e){var r=e.row;return[n("span",{staticClass:"link-type"},[t._v(t._s(r.cluster_name))])]}}])}),n("el-table-column",{attrs:{prop:"nick_name",align:"center",label:"业务名称"}}),n("el-table-column",{attrs:{prop:"job_id",align:"center",label:"任务ID"}}),n("el-table-column",{attrs:{prop:"backup_state",align:"center",label:"状态"},scopedSlots:t._u([{key:"default",fn:function(e){return["failed"===e.row.backup_state?n("span",{staticStyle:{color:"red"}},[t._v(t._s(e.row.backup_state))]):n("span",[t._v(t._s(e.row.backup_state))])]}}])}),n("el-table-column",{attrs:{prop:"backup_info",align:"center","show-overflow-tooltip":!0,label:"结果信息"}}),n("el-table-column",{attrs:{prop:"backup_time",align:"center",label:"备份时间"}}),n("el-table-column",{attrs:{label:"操作",align:"center","class-name":"small-padding fixed-width"},scopedSlots:t._u([{key:"default",fn:function(e){var r=e.row,a=e.$index;return["done"===r.backup_state?n("el-button",{attrs:{size:"mini",type:"primary"},on:{click:function(e){return t.handleDelete(r,a)}}},[t._v("逻辑恢复 ")]):t._e()]}}])})],1),n("pagination",{directives:[{name:"show",rawName:"v-show",value:t.total>0,expression:"total>0"}],attrs:{total:t.total,page:t.listQuery.pageNo,limit:t.listQuery.pageSize},on:{"update:page":function(e){return t.$set(t.listQuery,"pageNo",e)},"update:limit":function(e){return t.$set(t.listQuery,"pageSize",e)},pagination:t.getList}})],1)},g=[],m={name:"Operation",components:{Pagination:l["a"]},data:function(){return{tableKey:0,list:null,listLoading:!0,searchLoading:!1,total:0,listQuery:{pageNo:1,pageSize:10,username:"",status:""},dialogFormVisible:!1,dialogEditVisible:!1,dialogStatus:"",textMap:{detail:"详情"},dialogDetail:!1,message_tips:"",message_type:"",row:{}}},created:function(){this.getList()},methods:{handleFilter:function(){this.listQuery.pageNo=1,this.getList()},handleClear:function(){this.listQuery.username="",this.listQuery.status="",this.listQuery.pageNo=1,this.getList()},getList:function(){var t=this;this.listLoading=!0;var e=Object.assign({},this.listQuery);Object(s["J"])(e).then((function(e){t.list=e.list,t.total=e.total,setTimeout((function(){t.listLoading=!1}),500)}))}}},h=m,C=Object(d["a"])(h,b,g,!1,null,null,null),O=C.exports,j={components:{BackUp:f,LogicalBackup:O},data:function(){return{activeName:"first"}},methods:{handleClick:function(t,e){console.log(t,e)}}},y=j,v=(n("d465"),Object(d["a"])(y,r,a,!1,null,"04097001",null));e["default"]=v.exports},"82b0":function(t,e,n){"use strict";n.d(e,"x",(function(){return a})),n.d(e,"F",(function(){return u})),n.d(e,"I",(function(){return o})),n.d(e,"k",(function(){return s})),n.d(e,"l",(function(){return l})),n.d(e,"f",(function(){return i})),n.d(e,"u",(function(){return c})),n.d(e,"hb",(function(){return d})),n.d(e,"lb",(function(){return p})),n.d(e,"s",(function(){return f})),n.d(e,"W",(function(){return b})),n.d(e,"d",(function(){return g})),n.d(e,"b",(function(){return m})),n.d(e,"c",(function(){return h})),n.d(e,"G",(function(){return C})),n.d(e,"z",(function(){return O})),n.d(e,"n",(function(){return j})),n.d(e,"m",(function(){return y})),n.d(e,"o",(function(){return v})),n.d(e,"qb",(function(){return _})),n.d(e,"rb",(function(){return S})),n.d(e,"sb",(function(){return k})),n.d(e,"tb",(function(){return w})),n.d(e,"jb",(function(){return L})),n.d(e,"kb",(function(){return N})),n.d(e,"E",(function(){return Q})),n.d(e,"wb",(function(){return x})),n.d(e,"O",(function(){return z})),n.d(e,"ab",(function(){return B})),n.d(e,"cb",(function(){return $})),n.d(e,"db",(function(){return T})),n.d(e,"U",(function(){return E})),n.d(e,"bb",(function(){return M})),n.d(e,"C",(function(){return D})),n.d(e,"v",(function(){return P})),n.d(e,"ub",(function(){return F})),n.d(e,"a",(function(){return I})),n.d(e,"e",(function(){return U})),n.d(e,"yb",(function(){return V})),n.d(e,"p",(function(){return A})),n.d(e,"ib",(function(){return q})),n.d(e,"Y",(function(){return J})),n.d(e,"X",(function(){return K})),n.d(e,"D",(function(){return R})),n.d(e,"P",(function(){return G})),n.d(e,"Q",(function(){return W})),n.d(e,"w",(function(){return H})),n.d(e,"g",(function(){return X})),n.d(e,"Z",(function(){return Y})),n.d(e,"A",(function(){return Z})),n.d(e,"B",(function(){return tt})),n.d(e,"V",(function(){return et})),n.d(e,"S",(function(){return nt})),n.d(e,"r",(function(){return rt})),n.d(e,"H",(function(){return at})),n.d(e,"mb",(function(){return ut})),n.d(e,"L",(function(){return ot})),n.d(e,"M",(function(){return st})),n.d(e,"t",(function(){return lt})),n.d(e,"ob",(function(){return it})),n.d(e,"fb",(function(){return ct})),n.d(e,"gb",(function(){return dt})),n.d(e,"R",(function(){return pt})),n.d(e,"i",(function(){return ft})),n.d(e,"pb",(function(){return bt})),n.d(e,"N",(function(){return gt})),n.d(e,"nb",(function(){return mt})),n.d(e,"q",(function(){return ht})),n.d(e,"j",(function(){return Ct})),n.d(e,"eb",(function(){return Ot})),n.d(e,"y",(function(){return jt})),n.d(e,"vb",(function(){return yt})),n.d(e,"T",(function(){return vt})),n.d(e,"h",(function(){return _t})),n.d(e,"J",(function(){return St})),n.d(e,"K",(function(){return kt})),n.d(e,"xb",(function(){return wt}));var r=n("b775");function a(t){return Object(r["a"])({url:"/user/Cluster/clusterList",method:"get",params:t})}function u(t){return Object(r["a"])({url:"/user/Cluster/getEffectComp",method:"post",data:t})}function o(t){return Object(r["a"])({url:"/user/Cluster/getExperience",method:"post",data:t})}function s(t){return Object(r["a"])({url:"/user/Cluster/createCluster",method:"post",data:t})}function l(t){return Object(r["a"])({url:"/user/Cluster/delCluster",method:"post",data:t})}function i(t){return Object(r["a"])({url:"/user/Cluster/backUpCluster",method:"post",data:t})}function c(t){return Object(r["a"])({url:"/user/Cluster/getBackUpList",method:"get",params:t})}function d(t){return Object(r["a"])({url:"/user/Cluster/ifBackUp",method:"post",data:{id:t}})}function p(t){return Object(r["a"])({url:"/user/Cluster/restoreCluster",method:"post",data:t})}function f(){return Object(r["a"])({url:"/user/Cluster/getAllMachine",method:"get",params:""})}function b(t){return Object(r["a"])({url:"/user/Cluster/getShards",method:"post",data:{id:t}})}function g(t){return Object(r["a"])({url:"/user/Cluster/addShards",method:"post",data:t})}function m(t){return Object(r["a"])({url:"/user/Cluster/addComps",method:"post",data:t})}function h(t){return Object(r["a"])({url:"/user/Cluster/addNodes",method:"post",data:t})}function C(t){return Object(r["a"])({url:"/user/Cluster/getStatus",method:"post",data:t})}function O(t){return Object(r["a"])({url:"/user/Cluster/getClusterNodesList",method:"post",data:t})}function j(t){return Object(r["a"])({url:"/user/Cluster/delShard",method:"post",data:t})}function y(t){return Object(r["a"])({url:"/user/Cluster/delComp",method:"post",data:t})}function v(t){return Object(r["a"])({url:"/user/Cluster/delSnode",method:"post",data:t})}function _(t){return Object(r["a"])({url:"/user/Cluster/controlInstance",method:"post",data:t})}function S(t){return Object(r["a"])({url:"/user/Cluster/controlInstance",method:"post",data:t})}function k(t){return Object(r["a"])({url:"/user/Cluster/controlInstance",method:"post",data:t})}function w(t){return Object(r["a"])({url:"/user/Cluster/controlInstance",method:"post",data:t})}function L(t){return Object(r["a"])({url:"/user/Cluster/controlInstance",method:"post",data:t})}function N(t){return Object(r["a"])({url:"/user/Cluster/controlInstance",method:"post",data:t})}function Q(t){return Object(r["a"])({url:"/user/Cluster/getEffectCluster",method:"post",data:t})}function x(t){return Object(r["a"])({url:"/user/Cluster/updateAssign",method:"post",data:t})}function z(t){return Object(r["a"])({url:"/user/Cluster/getNode",method:"post",data:t})}function B(t){return Object(r["a"])({url:"/user/Cluster/getStandbyNode",method:"post",data:t})}function $(t){return Object(r["a"])({url:"/user/Cluster/getSwitcheOverList",method:"get",params:t})}function T(t){return Object(r["a"])({url:"/user/Cluster/getTaskList",method:"get",params:t})}function E(t){return Object(r["a"])({url:"/user/Cluster/getShardPrimary",method:"post",data:t})}function M(){return Object(r["a"])({url:"/user/Cluster/getStroMachine",method:"get",params:""})}function D(){return Object(r["a"])({url:"/user/Cluster/getCompMachine",method:"get",params:""})}function P(t){return Object(r["a"])({url:"/user/Cluster/getBackUpStorage",method:"post",data:t})}function F(t){return Object(r["a"])({url:"/user/Cluster/switchShard",method:"post",data:t})}function I(t){return Object(r["a"])({url:"/user/Cluster/SetCpuCgroup",method:"post",data:t})}function U(t){return Object(r["a"])({url:"/user/Cluster/addStorage",method:"post",data:t})}function V(t){return Object(r["a"])({url:"/user/Cluster/updateStorage",method:"post",data:t})}function A(t){return Object(r["a"])({url:"/user/Cluster/delStorage",method:"post",data:t})}function q(t){return Object(r["a"])({url:"/user/Cluster/rebuildNode",method:"post",data:t})}function J(t){return Object(r["a"])({url:"/user/Cluster/getShardsJobLog",method:"post",data:t})}function K(t){return Object(r["a"])({url:"/user/Cluster/getShardsCount",method:"get",params:t})}function R(t){return Object(r["a"])({url:"/user/Cluster/getCompsCount",method:"get",params:t})}function G(t){return Object(r["a"])({url:"/user/Cluster/getNodesCount",method:"get",params:t})}function W(t){return Object(r["a"])({url:"/user/Cluster/getOldCluster",method:"get",params:t})}function H(){return Object(r["a"])({url:"/user/Cluster/getBackupStorageList",method:"get",params:""})}function X(t){return Object(r["a"])({url:"/user/Cluster/clusterListError",method:"get",params:t})}function Y(t){return Object(r["a"])({url:"/user/Cluster/getShardsName",method:"get",params:t})}function Z(t){return Object(r["a"])({url:"/user/Cluster/getCompDBName",method:"get",params:t})}function tt(t){return Object(r["a"])({url:"/user/Cluster/getCompDBTable",method:"get",params:t})}function et(t){return Object(r["a"])({url:"/user/Cluster/getShardTable",method:"get",params:t})}function nt(t){return Object(r["a"])({url:"/user/Cluster/getOtherShards",method:"get",params:t})}function rt(t){return Object(r["a"])({url:"/user/Cluster/expandCluster",method:"post",data:t})}function at(t){return Object(r["a"])({url:"/user/Cluster/getExpandTableList",method:"post",data:t})}function ut(t){return Object(r["a"])({url:"/user/Cluster/setMaxDalay",method:"post",data:t})}function ot(t){return Object(r["a"])({url:"/user/Cluster/getMaxDalay",method:"post",data:t})}function st(t){return Object(r["a"])({url:"/user/Cluster/getMetaCluster",method:"get",params:t})}function lt(t){return Object(r["a"])({url:"/user/Cluster/getBackStorageList",method:"get",params:t})}function it(t){return Object(r["a"])({url:"/user/Cluster/setVariable",method:"post",data:t})}function ct(t){return Object(r["a"])({url:"/user/Cluster/getVariable",method:"post",data:t})}function dt(t){return Object(r["a"])({url:"/user/Cluster/getWorkMode",method:"post",data:t})}function pt(t){return Object(r["a"])({url:"/user/Cluster/getOneBackUpList",method:"get",params:t})}function ft(t){return Object(r["a"])({url:"/user/Cluster/computeList",method:"get",params:t})}function bt(t){return Object(r["a"])({url:"/user/Cluster/shardList",method:"get",params:t})}function gt(t){return Object(r["a"])({url:"/user/Cluster/getNoSwitchList",method:"post",data:t})}function mt(t){return Object(r["a"])({url:"/user/Cluster/setNoSwitch",method:"post",data:t})}function ht(t){return Object(r["a"])({url:"/user/Cluster/delSwitch",method:"post",data:t})}function Ct(t){return Object(r["a"])({url:"/user/Cluster/controlInstance",method:"post",data:t})}function Ot(t){return Object(r["a"])({url:"/user/Cluster/getThisShardNodes",method:"post",data:t})}function jt(t){return Object(r["a"])({url:"/user/Cluster/getClusterMonitor",method:"get",params:t})}function yt(t){return Object(r["a"])({url:"/user/ClusterSetting/tableRepartition",method:"post",data:t})}function vt(t){return Object(r["a"])({url:"/user/ClusterSetting/getPGTableList",method:"get",params:t})}function _t(t){return Object(r["a"])({url:"/user/ClusterSetting/clusterOptions",method:"get",params:t})}function St(t){return Object(r["a"])({url:"/user/Cluster/getLogicalBackUpList",method:"get",params:t})}function kt(t){return Object(r["a"])({url:"/user/ClusterSetting/getRecordLogicalBackup",method:"get",params:t})}function wt(t){return Object(r["a"])({url:"/user/Cluster/updateShard",method:"post",data:t})}},b80a:function(t,e,n){},bbec:function(t,e,n){},d059:function(t,e,n){"use strict";n("bbec")},d465:function(t,e,n){"use strict";n("b80a")}}]);