(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-5d7dbc2c"],{"333d":function(t,e,r){"use strict";var n=function(){var t=this,e=t.$createElement,r=t._self._c||e;return r("div",{staticClass:"pagination-container",class:{hidden:t.hidden}},[r("el-pagination",t._b({attrs:{background:t.background,"current-page":t.currentPage,"page-size":t.pageSize,layout:t.layout,"page-sizes":t.pageSizes,total:t.total},on:{"update:currentPage":function(e){t.currentPage=e},"update:current-page":function(e){t.currentPage=e},"update:pageSize":function(e){t.pageSize=e},"update:page-size":function(e){t.pageSize=e},"size-change":t.handleSizeChange,"current-change":t.handleCurrentChange}},"el-pagination",t.$attrs,!1))],1)},u=[];r("a9e3");Math.easeInOutQuad=function(t,e,r,n){return t/=n/2,t<1?r/2*t*t+e:(t--,-r/2*(t*(t-2)-1)+e)};var a=function(){return window.requestAnimationFrame||window.webkitRequestAnimationFrame||window.mozRequestAnimationFrame||function(t){window.setTimeout(t,1e3/60)}}();function o(t){document.documentElement.scrollTop=t,document.body.parentNode.scrollTop=t,document.body.scrollTop=t}function s(){return document.documentElement.scrollTop||document.body.parentNode.scrollTop||document.body.scrollTop}function i(t,e,r){var n=s(),u=t-n,i=20,l=0;e="undefined"===typeof e?500:e;var c=function t(){l+=i;var s=Math.easeInOutQuad(l,n,u,e);o(s),l<e?a(t):r&&"function"===typeof r&&r()};c()}var l={name:"Pagination",props:{total:{required:!0,type:Number},page:{type:Number,default:1},limit:{type:Number,default:20},pageSizes:{type:Array,default:function(){return[10,20,30,50]}},layout:{type:String,default:"total, sizes, prev, pager, next, jumper"},background:{type:Boolean,default:!0},autoScroll:{type:Boolean,default:!0},hidden:{type:Boolean,default:!1}},computed:{currentPage:{get:function(){return this.page},set:function(t){this.$emit("update:page",t)}},pageSize:{get:function(){return this.limit},set:function(t){this.$emit("update:limit",t)}}},methods:{handleSizeChange:function(t){this.$emit("pagination",{page:this.currentPage,limit:t}),this.autoScroll&&i(0,800)},handleCurrentChange:function(t){this.$emit("pagination",{page:t,limit:this.pageSize}),this.autoScroll&&i(0,800)}}},c=l,d=(r("d059"),r("2877")),p=Object(d["a"])(c,n,u,!1,null,"65877abe",null);e["a"]=p.exports},"41a2":function(t,e,r){"use strict";r.r(e);var n=function(){var t=this,e=t.$createElement,r=t._self._c||e;return r("div",{staticClass:"app-container"},[r("div",{staticClass:"filter-container"},[r("div",{staticClass:"table-list-search-wrap"},[r("el-select",{staticClass:"list_search_select",attrs:{placeholder:"请选择备份类型"},model:{value:t.listQuery.backup_type,callback:function(e){t.$set(t.listQuery,"backup_type",e)},expression:"listQuery.backup_type"}},[r("el-option",{attrs:{label:"存储",value:"storage"}}),r("el-option",{attrs:{label:"计算",value:"compute"}})],1),r("el-select",{staticClass:"list_search_select",attrs:{placeholder:"请选择状态"},model:{value:t.listQuery.status,callback:function(e){t.$set(t.listQuery,"status",e)},expression:"listQuery.status"}},[r("el-option",{attrs:{label:"not_started",value:"not_started"}}),r("el-option",{attrs:{label:"ongoing",value:"ongoing"}}),r("el-option",{attrs:{label:"done",value:"done"}}),r("el-option",{attrs:{label:"failed",value:"failed"}})],1),r("el-button",{attrs:{icon:"el-icon-search"},on:{click:t.handleFilter}},[t._v("查询")]),r("el-button",{attrs:{icon:"el-icon-refresh-right"},on:{click:t.handleClear}},[t._v("重置")])],1)]),r("el-table",{directives:[{name:"loading",rawName:"v-loading",value:t.listLoading,expression:"listLoading"}],key:t.tableKey,staticStyle:{width:"100%","margin-bottom":"20px"},attrs:{data:t.list,border:"","highlight-current-row":""}},[r("el-table-column",{attrs:{type:"index",align:"center",label:"序号",width:"50"}}),r("el-table-column",{attrs:{label:"集群名称",align:"center"},scopedSlots:t._u([{key:"default",fn:function(e){var n=e.row;return[r("span",{staticClass:"link-type",on:{click:function(e){return t.handleDetail(n)}}},[t._v(t._s(n.cluster_name))])]}}])}),r("el-table-column",{attrs:{prop:"nick_name",align:"center",label:"业务名称"}}),r("el-table-column",{attrs:{prop:"shard_name",align:"center",label:"shard名称"}}),r("el-table-column",{attrs:{prop:"comp_name",align:"center",label:"计算节点名称"}}),r("el-table-column",{attrs:{prop:"backup_type",align:"center",label:"备份类型"},scopedSlots:t._u([{key:"default",fn:function(e){return["storage"===e.row.backup_type?r("span",[t._v("存储")]):"compute"===e.row.backup_type?r("span",[t._v("计算")]):r("span")]}}])}),r("el-table-column",{attrs:{prop:"status",align:"center",label:"状态"},scopedSlots:t._u([{key:"default",fn:function(e){return["failed"===e.row.status?r("span",{staticStyle:{color:"red"}},[t._v(t._s(e.row.status))]):r("span",[t._v(t._s(e.row.status))])]}}])}),r("el-table-column",{attrs:{prop:"info",align:"center","show-overflow-tooltip":!0,label:"结果信息"}}),r("el-table-column",{attrs:{prop:"start_ts",align:"center",label:"开始时间"}}),r("el-table-column",{attrs:{prop:"end_ts",align:"center",label:"结束时间"}})],1),r("pagination",{directives:[{name:"show",rawName:"v-show",value:t.total>0,expression:"total>0"}],attrs:{total:t.total,page:t.listQuery.pageNo,limit:t.listQuery.pageSize},on:{"update:page":function(e){return t.$set(t.listQuery,"pageNo",e)},"update:limit":function(e){return t.$set(t.listQuery,"pageSize",e)},pagination:t.getList}})],1)},u=[],a=r("82b0"),o=r("333d"),s={name:"Operation",components:{Pagination:o["a"]},data:function(){return{tableKey:0,list:null,listLoading:!0,searchLoading:!1,total:0,listQuery:{pageNo:1,pageSize:10,username:"",backup_type:"",status:""},dialogFormVisible:!1,dialogEditVisible:!1,dialogStatus:"",textMap:{detail:"详情"},dialogDetail:!1,message_tips:"",message_type:"",row:{}}},created:function(){this.getList()},methods:{handleFilter:function(){this.listQuery.pageNo=1,this.getList()},handleClear:function(){this.listQuery.username="",this.listQuery.backup_type="",this.listQuery.status="",this.listQuery.pageNo=1,this.getList()},getList:function(){var t=this;this.listLoading=!0;var e=Object.assign({},this.listQuery);Object(a["u"])(e).then((function(e){t.list=e.list,t.total=e.total,setTimeout((function(){t.listLoading=!1}),500)}))},handleDetail:function(t){this.dialogStatus="detail",this.dialogFormVisible=!0,this.temp=Object.assign({},t),this.dialogDetail=!0}}},i=s,l=r("2877"),c=Object(l["a"])(i,n,u,!1,null,null,null);e["default"]=c.exports},"82b0":function(t,e,r){"use strict";r.d(e,"x",(function(){return u})),r.d(e,"F",(function(){return a})),r.d(e,"I",(function(){return o})),r.d(e,"k",(function(){return s})),r.d(e,"l",(function(){return i})),r.d(e,"f",(function(){return l})),r.d(e,"u",(function(){return c})),r.d(e,"fb",(function(){return d})),r.d(e,"jb",(function(){return p})),r.d(e,"s",(function(){return f})),r.d(e,"U",(function(){return m})),r.d(e,"d",(function(){return b})),r.d(e,"b",(function(){return g})),r.d(e,"c",(function(){return h})),r.d(e,"G",(function(){return C})),r.d(e,"z",(function(){return O})),r.d(e,"n",(function(){return j})),r.d(e,"m",(function(){return y})),r.d(e,"o",(function(){return S})),r.d(e,"ob",(function(){return v})),r.d(e,"pb",(function(){return _})),r.d(e,"qb",(function(){return w})),r.d(e,"rb",(function(){return k})),r.d(e,"hb",(function(){return L})),r.d(e,"ib",(function(){return N})),r.d(e,"E",(function(){return z})),r.d(e,"ub",(function(){return Q})),r.d(e,"M",(function(){return x})),r.d(e,"Y",(function(){return T})),r.d(e,"ab",(function(){return B})),r.d(e,"bb",(function(){return M})),r.d(e,"S",(function(){return E})),r.d(e,"Z",(function(){return P})),r.d(e,"C",(function(){return $})),r.d(e,"v",(function(){return I})),r.d(e,"sb",(function(){return D})),r.d(e,"a",(function(){return F})),r.d(e,"e",(function(){return A})),r.d(e,"vb",(function(){return q})),r.d(e,"p",(function(){return U})),r.d(e,"gb",(function(){return V})),r.d(e,"W",(function(){return J})),r.d(e,"V",(function(){return R})),r.d(e,"D",(function(){return K})),r.d(e,"N",(function(){return G})),r.d(e,"O",(function(){return W})),r.d(e,"w",(function(){return H})),r.d(e,"g",(function(){return X})),r.d(e,"X",(function(){return Y})),r.d(e,"A",(function(){return Z})),r.d(e,"B",(function(){return tt})),r.d(e,"T",(function(){return et})),r.d(e,"Q",(function(){return rt})),r.d(e,"r",(function(){return nt})),r.d(e,"H",(function(){return ut})),r.d(e,"kb",(function(){return at})),r.d(e,"J",(function(){return ot})),r.d(e,"K",(function(){return st})),r.d(e,"t",(function(){return it})),r.d(e,"mb",(function(){return lt})),r.d(e,"db",(function(){return ct})),r.d(e,"eb",(function(){return dt})),r.d(e,"P",(function(){return pt})),r.d(e,"i",(function(){return ft})),r.d(e,"nb",(function(){return mt})),r.d(e,"L",(function(){return bt})),r.d(e,"lb",(function(){return gt})),r.d(e,"q",(function(){return ht})),r.d(e,"j",(function(){return Ct})),r.d(e,"cb",(function(){return Ot})),r.d(e,"y",(function(){return jt})),r.d(e,"tb",(function(){return yt})),r.d(e,"R",(function(){return St})),r.d(e,"h",(function(){return vt}));var n=r("b775");function u(t){return Object(n["a"])({url:"/user/Cluster/clusterList",method:"get",params:t})}function a(t){return Object(n["a"])({url:"/user/Cluster/getEffectComp",method:"post",data:t})}function o(t){return Object(n["a"])({url:"/user/Cluster/getExperience",method:"post",data:t})}function s(t){return Object(n["a"])({url:"/user/Cluster/createCluster",method:"post",data:t})}function i(t){return Object(n["a"])({url:"/user/Cluster/delCluster",method:"post",data:t})}function l(t){return Object(n["a"])({url:"/user/Cluster/backUpCluster",method:"post",data:t})}function c(t){return Object(n["a"])({url:"/user/Cluster/getBackUpList",method:"get",params:t})}function d(t){return Object(n["a"])({url:"/user/Cluster/ifBackUp",method:"post",data:{id:t}})}function p(t){return Object(n["a"])({url:"/user/Cluster/restoreCluster",method:"post",data:t})}function f(){return Object(n["a"])({url:"/user/Cluster/getAllMachine",method:"get",params:""})}function m(t){return Object(n["a"])({url:"/user/Cluster/getShards",method:"post",data:{id:t}})}function b(t){return Object(n["a"])({url:"/user/Cluster/addShards",method:"post",data:t})}function g(t){return Object(n["a"])({url:"/user/Cluster/addComps",method:"post",data:t})}function h(t){return Object(n["a"])({url:"/user/Cluster/addNodes",method:"post",data:t})}function C(t){return Object(n["a"])({url:"/user/Cluster/getStatus",method:"post",data:t})}function O(t){return Object(n["a"])({url:"/user/Cluster/getClusterNodesList",method:"post",data:t})}function j(t){return Object(n["a"])({url:"/user/Cluster/delShard",method:"post",data:t})}function y(t){return Object(n["a"])({url:"/user/Cluster/delComp",method:"post",data:t})}function S(t){return Object(n["a"])({url:"/user/Cluster/delSnode",method:"post",data:t})}function v(t){return Object(n["a"])({url:"/user/Cluster/controlInstance",method:"post",data:t})}function _(t){return Object(n["a"])({url:"/user/Cluster/controlInstance",method:"post",data:t})}function w(t){return Object(n["a"])({url:"/user/Cluster/controlInstance",method:"post",data:t})}function k(t){return Object(n["a"])({url:"/user/Cluster/controlInstance",method:"post",data:t})}function L(t){return Object(n["a"])({url:"/user/Cluster/controlInstance",method:"post",data:t})}function N(t){return Object(n["a"])({url:"/user/Cluster/controlInstance",method:"post",data:t})}function z(t){return Object(n["a"])({url:"/user/Cluster/getEffectCluster",method:"post",data:t})}function Q(t){return Object(n["a"])({url:"/user/Cluster/updateAssign",method:"post",data:t})}function x(t){return Object(n["a"])({url:"/user/Cluster/getNode",method:"post",data:t})}function T(t){return Object(n["a"])({url:"/user/Cluster/getStandbyNode",method:"post",data:t})}function B(t){return Object(n["a"])({url:"/user/Cluster/getSwitcheOverList",method:"get",params:t})}function M(t){return Object(n["a"])({url:"/user/Cluster/getTaskList",method:"get",params:t})}function E(t){return Object(n["a"])({url:"/user/Cluster/getShardPrimary",method:"post",data:t})}function P(){return Object(n["a"])({url:"/user/Cluster/getStroMachine",method:"get",params:""})}function $(){return Object(n["a"])({url:"/user/Cluster/getCompMachine",method:"get",params:""})}function I(t){return Object(n["a"])({url:"/user/Cluster/getBackUpStorage",method:"post",data:t})}function D(t){return Object(n["a"])({url:"/user/Cluster/switchShard",method:"post",data:t})}function F(t){return Object(n["a"])({url:"/user/Cluster/SetCpuCgroup",method:"post",data:t})}function A(t){return Object(n["a"])({url:"/user/Cluster/addStorage",method:"post",data:t})}function q(t){return Object(n["a"])({url:"/user/Cluster/updateStorage",method:"post",data:t})}function U(t){return Object(n["a"])({url:"/user/Cluster/delStorage",method:"post",data:t})}function V(t){return Object(n["a"])({url:"/user/Cluster/rebuildNode",method:"post",data:t})}function J(t){return Object(n["a"])({url:"/user/Cluster/getShardsJobLog",method:"post",data:t})}function R(t){return Object(n["a"])({url:"/user/Cluster/getShardsCount",method:"get",params:t})}function K(t){return Object(n["a"])({url:"/user/Cluster/getCompsCount",method:"get",params:t})}function G(t){return Object(n["a"])({url:"/user/Cluster/getNodesCount",method:"get",params:t})}function W(t){return Object(n["a"])({url:"/user/Cluster/getOldCluster",method:"get",params:t})}function H(){return Object(n["a"])({url:"/user/Cluster/getBackupStorageList",method:"get",params:""})}function X(t){return Object(n["a"])({url:"/user/Cluster/clusterListError",method:"get",params:t})}function Y(t){return Object(n["a"])({url:"/user/Cluster/getShardsName",method:"get",params:t})}function Z(t){return Object(n["a"])({url:"/user/Cluster/getCompDBName",method:"get",params:t})}function tt(t){return Object(n["a"])({url:"/user/Cluster/getCompDBTable",method:"get",params:t})}function et(t){return Object(n["a"])({url:"/user/Cluster/getShardTable",method:"get",params:t})}function rt(t){return Object(n["a"])({url:"/user/Cluster/getOtherShards",method:"get",params:t})}function nt(t){return Object(n["a"])({url:"/user/Cluster/expandCluster",method:"post",data:t})}function ut(t){return Object(n["a"])({url:"/user/Cluster/getExpandTableList",method:"post",data:t})}function at(t){return Object(n["a"])({url:"/user/Cluster/setMaxDalay",method:"post",data:t})}function ot(t){return Object(n["a"])({url:"/user/Cluster/getMaxDalay",method:"post",data:t})}function st(t){return Object(n["a"])({url:"/user/Cluster/getMetaCluster",method:"get",params:t})}function it(t){return Object(n["a"])({url:"/user/Cluster/getBackStorageList",method:"get",params:t})}function lt(t){return Object(n["a"])({url:"/user/Cluster/setVariable",method:"post",data:t})}function ct(t){return Object(n["a"])({url:"/user/Cluster/getVariable",method:"post",data:t})}function dt(t){return Object(n["a"])({url:"/user/Cluster/getWorkMode",method:"post",data:t})}function pt(t){return Object(n["a"])({url:"/user/Cluster/getOneBackUpList",method:"get",params:t})}function ft(t){return Object(n["a"])({url:"/user/Cluster/computeList",method:"get",params:t})}function mt(t){return Object(n["a"])({url:"/user/Cluster/shardList",method:"get",params:t})}function bt(t){return Object(n["a"])({url:"/user/Cluster/getNoSwitchList",method:"post",data:t})}function gt(t){return Object(n["a"])({url:"/user/Cluster/setNoSwitch",method:"post",data:t})}function ht(t){return Object(n["a"])({url:"/user/Cluster/delSwitch",method:"post",data:t})}function Ct(t){return Object(n["a"])({url:"/user/Cluster/controlInstance",method:"post",data:t})}function Ot(t){return Object(n["a"])({url:"/user/Cluster/getThisShardNodes",method:"post",data:t})}function jt(t){return Object(n["a"])({url:"/user/Cluster/getClusterMonitor",method:"get",params:t})}function yt(t){return Object(n["a"])({url:"/user/ClusterSetting/tableRepartition",method:"post",data:t})}function St(t){return Object(n["a"])({url:"/user/ClusterSetting/getPGTableList",method:"get",params:t})}function vt(t){return Object(n["a"])({url:"/user/ClusterSetting/clusterOptions",method:"get",params:t})}},bbec:function(t,e,r){},d059:function(t,e,r){"use strict";r("bbec")}}]);