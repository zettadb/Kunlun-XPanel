(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-2b7ad254"],{"0ccb":function(t,e,r){var n=r("50c4"),a=r("1148"),s=r("1d80"),u=Math.ceil,i=function(t){return function(e,r,i){var o,c,l=String(s(e)),d=l.length,m=void 0===i?" ":String(i),p=n(r);return p<=d||""==m?l:(o=p-d,c=a.call(m,u(o/m.length)),c.length>o&&(c=c.slice(0,o)),t?l+c:c+l)}};t.exports={start:i(!1),end:i(!0)}},1148:function(t,e,r){"use strict";var n=r("a691"),a=r("1d80");t.exports="".repeat||function(t){var e=String(a(this)),r="",s=n(t);if(s<0||s==1/0)throw RangeError("Wrong number of repetitions");for(;s>0;(s>>>=1)&&(e+=e))1&s&&(r+=e);return r}},"333d":function(t,e,r){"use strict";var n=function(){var t=this,e=t.$createElement,r=t._self._c||e;return r("div",{staticClass:"pagination-container",class:{hidden:t.hidden}},[r("el-pagination",t._b({attrs:{background:t.background,"current-page":t.currentPage,"page-size":t.pageSize,layout:t.layout,"page-sizes":t.pageSizes,total:t.total},on:{"update:currentPage":function(e){t.currentPage=e},"update:current-page":function(e){t.currentPage=e},"update:pageSize":function(e){t.pageSize=e},"update:page-size":function(e){t.pageSize=e},"size-change":t.handleSizeChange,"current-change":t.handleCurrentChange}},"el-pagination",t.$attrs,!1))],1)},a=[];r("a9e3");Math.easeInOutQuad=function(t,e,r,n){return t/=n/2,t<1?r/2*t*t+e:(t--,-r/2*(t*(t-2)-1)+e)};var s=function(){return window.requestAnimationFrame||window.webkitRequestAnimationFrame||window.mozRequestAnimationFrame||function(t){window.setTimeout(t,1e3/60)}}();function u(t){document.documentElement.scrollTop=t,document.body.parentNode.scrollTop=t,document.body.scrollTop=t}function i(){return document.documentElement.scrollTop||document.body.parentNode.scrollTop||document.body.scrollTop}function o(t,e,r){var n=i(),a=t-n,o=20,c=0;e="undefined"===typeof e?500:e;var l=function t(){c+=o;var i=Math.easeInOutQuad(c,n,a,e);u(i),c<e?s(t):r&&"function"===typeof r&&r()};l()}var c={name:"Pagination",props:{total:{required:!0,type:Number},page:{type:Number,default:1},limit:{type:Number,default:20},pageSizes:{type:Array,default:function(){return[10,20,30,50]}},layout:{type:String,default:"total, sizes, prev, pager, next, jumper"},background:{type:Boolean,default:!0},autoScroll:{type:Boolean,default:!0},hidden:{type:Boolean,default:!1}},computed:{currentPage:{get:function(){return this.page},set:function(t){this.$emit("update:page",t)}},pageSize:{get:function(){return this.limit},set:function(t){this.$emit("update:limit",t)}}},methods:{handleSizeChange:function(t){this.$emit("pagination",{page:this.currentPage,limit:t}),this.autoScroll&&o(0,800)},handleCurrentChange:function(t){this.$emit("pagination",{page:t,limit:this.pageSize}),this.autoScroll&&o(0,800)}}},l=c,d=(r("d059"),r("2877")),m=Object(d["a"])(l,n,a,!1,null,"65877abe",null);e["a"]=m.exports},"47a4":function(t,e,r){"use strict";r.r(e);var n=function(){var t=this,e=t.$createElement,r=t._self._c||e;return r("div",{staticClass:"app-container"},[r("div",{staticClass:"filter-container"},[r("div",{staticClass:"table-list-search-wrap"},["super_dba"==t.user_name?r("el-button",{staticClass:"filter-item",attrs:{type:"primary"},on:{click:t.handleCreate}},[t._v("免切设置 ")]):t._e(),r("div",{directives:[{name:"show",rawName:"v-show",value:!0===t.installStatus,expression:"installStatus===true"}],staticClass:"info",domProps:{textContent:t._s(t.info)}})],1),r("div",{staticClass:"table-list-wrap"})]),r("el-table",{directives:[{name:"loading",rawName:"v-loading",value:t.listLoading,expression:"listLoading"}],key:t.tableKey,staticStyle:{width:"100%","margin-bottom":"20px"},attrs:{data:t.list,border:"","highlight-current-row":""}},[t._v(" > "),r("el-table-column",{attrs:{type:"index",align:"center",label:"序号",width:"50"}}),r("el-table-column",{attrs:{label:"集群ID",align:"center"},scopedSlots:t._u([{key:"default",fn:function(e){return["all"===e.row.cluster_id?r("span",[t._v("所有集群")]):r("span",[t._v(t._s(e.row.cluster_id))])]}}])}),r("el-table-column",{attrs:{prop:"shard_id",align:"center",label:"shardID"},scopedSlots:t._u([{key:"default",fn:function(e){return[e.row.shard_id?r("span",[t._v(t._s(e.row.shard_id))]):r("span",[t._v("所有shard")])]}}])}),r("el-table-column",{attrs:{prop:"param.timestamp",align:"center",label:"超时时间"},scopedSlots:t._u([{key:"default",fn:function(e){var n=e.row;return[r("span",{staticClass:"link-type",on:{click:function(e){return t.handleDetail(n)}}},[t._v(t._s(t._f("formatDate")(n.param.timestamp)))])]}}])}),r("el-table-column",{attrs:{align:"center",label:"是否超时"},scopedSlots:t._u([{key:"default",fn:function(e){return[r("span",[t._v(t._s(t._f("compareTime")(e.row.param.timestamp)))])]}}])}),"super_dba"==t.user_name?r("el-table-column",{attrs:{label:"操作",align:"center",width:"300","class-name":"small-padding fixed-width"},scopedSlots:t._u([{key:"default",fn:function(e){var n=e.row,a=e.$index;return["super_dba"==t.user_name?r("el-button",{attrs:{size:"mini",type:"danger"},on:{click:function(e){return t.handleDelete(n,a)}}},[t._v("删除 ")]):t._e(),r("div",{directives:[{name:"show",rawName:"v-show",value:!0===t.installStatus,expression:"installStatus===true"}],staticClass:"info",domProps:{textContent:t._s(t.info)}})]}}],null,!1,2105715155)}):t._e()],1),r("pagination",{directives:[{name:"show",rawName:"v-show",value:t.total>0,expression:"total>0"}],attrs:{total:t.total,page:t.listQuery.pageNo,limit:t.listQuery.pageSize},on:{"update:page":function(e){return t.$set(t.listQuery,"pageNo",e)},"update:limit":function(e){return t.$set(t.listQuery,"pageSize",e)},pagination:t.getList}}),r("el-dialog",{attrs:{title:t.textMap[t.dialogStatus],visible:t.dialogFormVisible,"custom-class":"single_dal_view"},on:{"update:visible":function(e){t.dialogFormVisible=e}}},[r("el-form",{ref:"dataForm",attrs:{rules:t.rules,model:t.temp,"label-position":"left","label-width":"140px"}},[r("el-form-item",{attrs:{label:"集群名称:",prop:"nick_name"}},[r("div",{staticStyle:{"max-height":"200px","overflow-y":"auto"}},[r("el-checkbox",{attrs:{indeterminate:t.isIndeterminate},on:{change:t.handleCheckAllChange},model:{value:t.checkAll,callback:function(e){t.checkAll=e},expression:"checkAll"}},[t._v("all"),r("span",{staticStyle:{"font-size":"12px",color:"#F56C6C"}},[t._v("（勾选all表示所有的集群+所有shard都设置免切）")])]),r("div",{staticStyle:{margin:"15px 0"}}),r("el-checkbox-group",{attrs:{prop:"checkedCluster"},on:{change:t.handleCheckedCitiesChange},model:{value:t.checkedCluster,callback:function(e){t.checkedCluster=e},expression:"checkedCluster"}},t._l(t.clusters,(function(e){return r("el-checkbox",{key:e.id,attrs:{label:e.id}},[t._v(" "+t._s(e.nick_name+"("+e.name+")")+" ")])})),1)],1)]),1==t.checkedCluster.length?r("el-form-item",{attrs:{label:"shard名称:",prop:"shard_id"}},[r("el-select",{attrs:{clearable:"",placeholder:"请选择shard名称"},model:{value:t.temp.shard_id,callback:function(e){t.$set(t.temp,"shard_id",e)},expression:"temp.shard_id"}},t._l(t.shardList,(function(t){return r("el-option",{key:t.id,attrs:{label:t.name,value:t.id}})})),1)],1):t._e(),r("el-form-item",{attrs:{label:"超时时间:",prop:"timeout"}},[r("el-input",{attrs:{placeholder:"请输入超时时间"},model:{value:t.temp.timeout,callback:function(e){t.$set(t.temp,"timeout",e)},expression:"temp.timeout"}},[r("i",{staticStyle:{"font-style":"normal","margin-right":"10px","line-height":"30px"},attrs:{slot:"suffix"},slot:"suffix"},[t._v("s")])])],1)],1),r("div",{staticClass:"dialog-footer",attrs:{slot:"footer"},slot:"footer"},[r("el-button",{directives:[{name:"show",rawName:"v-show",value:!t.dialogDetail,expression:"!dialogDetail"}],on:{click:function(e){t.dialogFormVisible=!1}}},[t._v("关闭")]),r("el-button",{directives:[{name:"show",rawName:"v-show",value:!t.dialogDetail,expression:"!dialogDetail"}],attrs:{type:"primary"},on:{click:function(e){"create"===t.dialogStatus?t.createData():t.updateData(t.row)}}},[t._v(" 确认 ")])],1)],1)],1)},a=[],s=r("c7eb"),u=r("1da1"),i=(r("ac1f"),r("00b4"),r("d3b7"),r("159b"),r("b0c0"),r("ed08")),o=r("82b0"),c=r("578a"),l=r("333d"),d=r("8218"),m={name:"Account",components:{Pagination:l["a"]},filters:{formatDate:function(t){var e=new Date(1e3*t),r=e.getFullYear(),n=e.getMonth()+1;n=n<10?"0"+n:n;var a=e.getDate();a=a<10?"0"+a:a;var s=e.getHours();s=s<10?"0"+s:s;var u=e.getMinutes();u=u<10?"0"+u:u;var i=e.getSeconds();return i=i<10?"0"+i:i,r+"-"+n+"-"+a+" "+s+":"+u+":"+i},compareTime:function(t){var e=parseInt((new Date).getTime()/1e3);return t<e?"是":"否"}},data:function(){var t=this,e=function(e,r,n){t.checkedCluster.length<=0?n(new Error("请勾选集群名称")):n()},r=function(t,e,r){var n=/^[0-9]*$/;e?n.test(e)?r():r(new Error("超时时间只允许输入数字")):r(new Error("请输入超时时间"))};return{tableKey:0,list:null,listLoading:!0,searchLoading:!1,total:0,listQuery:{pageNo:1,pageSize:10,name:""},temp:{timeout:"",checkedCluster:[],shard_id:""},dialogFormVisible:!1,dialogEditVisible:!1,dialogStatus:"",textMap:{update:"编辑备份存储目标",create:"免切设置",detail:"详情"},dialogDetail:!1,message_tips:"",message_type:"",installStatus:!1,info:"",row:{},stypelist:c["r"],user_name:sessionStorage.getItem("login_username"),clusters:[],clusterTotal:0,checkedCluster:[],checkAll:!1,isIndeterminate:!1,shardList:[],rules:{nick_name:[{required:!0,trigger:"blur",validator:e}],timeout:[{required:!0,trigger:"blur",validator:r}]}}},watch:{checkedCluster:{handler:function(t,e){var r=this;this.temp.checkedCluster=[],this.checkedCluster.forEach((function(t){-1==r.temp.checkedCluster.indexOf(t)&&r.temp.checkedCluster.push(t),1==r.checkedCluster.length&&Object(o["U"])(r.checkedCluster[0]).then((function(t){var e=t;200==e.code&&(r.shardList=e.list)}))}))},immediate:!0}},created:function(){this.getList(),this.getCluster()},methods:{handleCheckAllChange:function(t){for(var e=[],r=0;r<this.clusters.length;r++)e.push(this.clusters[r].id);this.checkedCluster=t?e:[],this.isIndeterminate=!1},handleCheckedCitiesChange:function(t){var e=t.length;this.checkAll=e===this.clusters.length,this.isIndeterminate=e>0&&e<this.clusters.length},getCluster:function(){var t=this;return Object(u["a"])(Object(s["a"])().mark((function e(){var r;return Object(s["a"])().wrap((function(e){while(1)switch(e.prev=e.next){case 0:return e.next=2,Object(d["d"])();case 2:r=e.sent,t.clusters=r.list,t.clusterTotal=r.total;case 5:case"end":return e.stop()}}),e)})))()},handleFilter:function(){this.listQuery.pageNo=1,this.getList()},handleClear:function(){this.listQuery.name="",this.listQuery.pageNo=1,this.getList()},getList:function(){var t=this;this.listLoading=!0,this.installStatus=!1;Object.assign({},this.listQuery);var e={};e.version=c["u"][0].ver,e.job_id="",e.job_type="list_noswitch",e.timestamp=c["s"][0].time+"",e.paras={cluster_id:"all"},Object(o["L"])(e).then((function(e){0!==e.attachment.length?(t.list=e.attachment.value,t.total=e.attachment.value.length):(t.list=[],t.total=0),setTimeout((function(){t.listLoading=!1}),500)}))},resetTemp:function(){this.temp={timeout:"",checkedCluster:[],shard_id:""},this.checkAll=!1,this.isIndeterminate=!1,this.checkedCluster=[]},handleCreate:function(){var t=this;this.resetTemp(),this.dialogStatus="create",this.dialogFormVisible=!0,this.dialogDetail=!1,this.$nextTick((function(){t.$refs.dataForm.clearValidate()}))},createData:function(){var t=this;this.$refs["dataForm"].validate((function(e){if(e){var r={job_id:"",job_type:"set_noswitch"};r.version=c["u"][0].ver,r.timestamp=c["s"][0].time+"",r.user_name=sessionStorage.getItem("login_username");var n={};if(t.temp.checkedCluster.length==t.clusters.length&&1!==t.clusters.length)n.cluster_id="all",""!==t.temp.shard_id&&(n.shard_id=t.temp.shard_id),n.timeout=t.temp.timeout,n.type="1",r.paras=n,Object(o["lb"])(r).then((function(e){var r=e;t.message_tips="正在设置免切...",t.message_type="success",Object(i["g"])(t.message_tips,t.message_type),"0"==r.error_code?(t.dialogFormVisible=!1,t.message_tips="免切设置成功",t.message_type="success",Object(i["g"])(t.message_tips,t.message_type),t.getList()):(t.message_tips=r.error_info,t.message_type="error",Object(i["g"])(t.message_tips,t.message_type))}));else for(var a=0;a<t.temp.checkedCluster.length;a++)n.cluster_id=t.temp.checkedCluster[a],""!==t.temp.shard_id&&(n.shard_id=t.temp.shard_id),n.timeout=t.temp.timeout,n.type="1",r.paras=n,Object(o["lb"])(r).then((function(e){var r=e;t.message_tips="正在设置免切...",t.message_type="success",Object(i["g"])(t.message_tips,t.message_type),"0"==r.error_code?(t.dialogFormVisible=!1,t.message_tips="免切设置成功",t.message_type="success",Object(i["g"])(t.message_tips,t.message_type),t.getList()):(t.message_tips=r.error_info,t.message_type="error",Object(i["g"])(t.message_tips,t.message_type))}))}}))},handleDetail:function(t){this.dialogStatus="detail",this.dialogFormVisible=!0,this.temp=Object.assign({},t),this.dialogDetail=!0},handleUpdate:function(t){var e=this;this.temp=Object.assign({},t),this.dialogStatus="update",this.dialogFormVisible=!0,this.dialogDetail=!1,this.$nextTick((function(){e.$refs["dataForm"].clearValidate()}))},updateData:function(){var t=this;this.$refs["dataForm"].validate((function(e){if(e){var r={job_id:"",job_type:"update_backup_storage"};r.version=c["u"][0].ver,r.timestamp=c["s"][0].time+"",r.user_name=sessionStorage.getItem("login_username"),r.paras=Object.assign({},t.temp),Object(o["vb"])(r).then((function(e){var r=e;if("accept"==r.status){t.dialogFormVisible=!1,t.message_tips="正在编辑备份存储目标...",t.message_type="success";var n=0,a=setInterval((function(){t.getStatus(a,r.job_id,n++)}),1e3)}else"ongoing"==r.status?(t.message_tips="系统正在操作中，请等待一会！",t.message_type="error"):(t.message_tips=r.error_info,t.message_type="error");Object(i["g"])(t.message_tips,t.message_type)}))}}))},handleDelete:function(t){var e=this,r=Object(i["a"])(),n="此操作将永久删除集群ID为"+t.cluster_id+"的免切数据,是否继续?code="+r;Object(i["e"])(n).then((function(n){if(n.value)if(n.value==r){var a={};a.user_name=sessionStorage.getItem("login_username"),a.job_id="",a.job_type="del_noswitch",a.version=c["u"][0].ver,a.timestamp=c["s"][0].time+"";var s={};s.cluster_id=t.cluster_id,t.shard_id&&(s.shard_id=t.shard_id),a.paras=s,Object(o["q"])(a).then((function(t){var r=t;e.message_tips="正在删除",e.message_type="success",Object(i["g"])(e.message_tips,e.message_type),"0"==r.error_code?(e.message_tips="删除成功",e.message_type="success",Object(i["g"])(e.message_tips,e.message_type),e.getList()):(e.message_tips=r.error_info,e.message_type="error",Object(i["g"])(e.message_tips,e.message_type))}))}else e.message_tips="code输入有误",e.message_type="error",Object(i["g"])(e.message_tips,e.message_type);else e.message_tips="code不能为空！",e.message_type="error",Object(i["g"])(e.message_tips,e.message_type)})).catch((function(){console.log("quxiao"),Object(i["g"])("已取消删除","info")}))},getStatus:function(t,e,r){var n=this;setTimeout((function(){var a={job_type:"get_status"};a.version=c["u"][0].ver,a.job_id=e,a.timestamp=c["s"][0].time+"",a.paras={},Object(o["G"])(a).then((function(e){"done"==e.status||"failed"==e.status?(clearInterval(t),n.info=e.error_info,"done"==e.status?n.getList():n.installStatus=!0):(n.info=e.error_info,n.installStatus=!0)})),r>=86400&&clearInterval(t)}),0)}}},p=m,f=r("2877"),g=Object(f["a"])(p,n,a,!1,null,null,null);e["default"]=g.exports},"4d90":function(t,e,r){"use strict";var n=r("23e7"),a=r("0ccb").start,s=r("9a0c");n({target:"String",proto:!0,forced:s},{padStart:function(t){return a(this,t,arguments.length>1?arguments[1]:void 0)}})},8218:function(t,e,r){"use strict";r.d(e,"a",(function(){return a})),r.d(e,"b",(function(){return s})),r.d(e,"e",(function(){return u})),r.d(e,"c",(function(){return i})),r.d(e,"d",(function(){return o}));var n=r("b775");function a(t){return Object(n["a"])({url:"/user/Access/accessList",method:"get",params:t})}function s(t){return Object(n["a"])({url:"/user/Access/add",method:"post",data:t})}function u(t){return Object(n["a"])({url:"/user/Access/edit",method:"post",data:t})}function i(t,e){return Object(n["a"])({url:"/user/Access/delete",method:"post",data:{user_id:t,role_id:e}})}function o(){return Object(n["a"])({url:"/user/Access/getCluster",method:"get",param:""})}},"82b0":function(t,e,r){"use strict";r.d(e,"x",(function(){return a})),r.d(e,"F",(function(){return s})),r.d(e,"I",(function(){return u})),r.d(e,"k",(function(){return i})),r.d(e,"l",(function(){return o})),r.d(e,"f",(function(){return c})),r.d(e,"u",(function(){return l})),r.d(e,"fb",(function(){return d})),r.d(e,"jb",(function(){return m})),r.d(e,"s",(function(){return p})),r.d(e,"U",(function(){return f})),r.d(e,"d",(function(){return g})),r.d(e,"b",(function(){return h})),r.d(e,"c",(function(){return b})),r.d(e,"G",(function(){return _})),r.d(e,"z",(function(){return C})),r.d(e,"n",(function(){return v})),r.d(e,"m",(function(){return j})),r.d(e,"o",(function(){return O})),r.d(e,"ob",(function(){return y})),r.d(e,"pb",(function(){return S})),r.d(e,"qb",(function(){return k})),r.d(e,"rb",(function(){return w})),r.d(e,"hb",(function(){return x})),r.d(e,"ib",(function(){return D})),r.d(e,"E",(function(){return L})),r.d(e,"ub",(function(){return T})),r.d(e,"M",(function(){return I})),r.d(e,"Y",(function(){return N})),r.d(e,"ab",(function(){return M})),r.d(e,"bb",(function(){return F})),r.d(e,"S",(function(){return z})),r.d(e,"Z",(function(){return A})),r.d(e,"C",(function(){return $})),r.d(e,"v",(function(){return B})),r.d(e,"sb",(function(){return E})),r.d(e,"a",(function(){return V})),r.d(e,"e",(function(){return P})),r.d(e,"vb",(function(){return Q})),r.d(e,"p",(function(){return q})),r.d(e,"gb",(function(){return U})),r.d(e,"W",(function(){return R})),r.d(e,"V",(function(){return J})),r.d(e,"D",(function(){return G})),r.d(e,"N",(function(){return H})),r.d(e,"O",(function(){return K})),r.d(e,"w",(function(){return W})),r.d(e,"g",(function(){return Y})),r.d(e,"X",(function(){return X})),r.d(e,"A",(function(){return Z})),r.d(e,"B",(function(){return tt})),r.d(e,"T",(function(){return et})),r.d(e,"Q",(function(){return rt})),r.d(e,"r",(function(){return nt})),r.d(e,"H",(function(){return at})),r.d(e,"kb",(function(){return st})),r.d(e,"J",(function(){return ut})),r.d(e,"K",(function(){return it})),r.d(e,"t",(function(){return ot})),r.d(e,"mb",(function(){return ct})),r.d(e,"db",(function(){return lt})),r.d(e,"eb",(function(){return dt})),r.d(e,"P",(function(){return mt})),r.d(e,"i",(function(){return pt})),r.d(e,"nb",(function(){return ft})),r.d(e,"L",(function(){return gt})),r.d(e,"lb",(function(){return ht})),r.d(e,"q",(function(){return bt})),r.d(e,"j",(function(){return _t})),r.d(e,"cb",(function(){return Ct})),r.d(e,"y",(function(){return vt})),r.d(e,"tb",(function(){return jt})),r.d(e,"R",(function(){return Ot})),r.d(e,"h",(function(){return yt}));var n=r("b775");function a(t){return Object(n["a"])({url:"/user/Cluster/clusterList",method:"get",params:t})}function s(t){return Object(n["a"])({url:"/user/Cluster/getEffectComp",method:"post",data:t})}function u(t){return Object(n["a"])({url:"/user/Cluster/getExperience",method:"post",data:t})}function i(t){return Object(n["a"])({url:"/user/Cluster/createCluster",method:"post",data:t})}function o(t){return Object(n["a"])({url:"/user/Cluster/delCluster",method:"post",data:t})}function c(t){return Object(n["a"])({url:"/user/Cluster/backUpCluster",method:"post",data:t})}function l(t){return Object(n["a"])({url:"/user/Cluster/getBackUpList",method:"get",params:t})}function d(t){return Object(n["a"])({url:"/user/Cluster/ifBackUp",method:"post",data:{id:t}})}function m(t){return Object(n["a"])({url:"/user/Cluster/restoreCluster",method:"post",data:t})}function p(){return Object(n["a"])({url:"/user/Cluster/getAllMachine",method:"get",params:""})}function f(t){return Object(n["a"])({url:"/user/Cluster/getShards",method:"post",data:{id:t}})}function g(t){return Object(n["a"])({url:"/user/Cluster/addShards",method:"post",data:t})}function h(t){return Object(n["a"])({url:"/user/Cluster/addComps",method:"post",data:t})}function b(t){return Object(n["a"])({url:"/user/Cluster/addNodes",method:"post",data:t})}function _(t){return Object(n["a"])({url:"/user/Cluster/getStatus",method:"post",data:t})}function C(t){return Object(n["a"])({url:"/user/Cluster/getClusterNodesList",method:"post",data:t})}function v(t){return Object(n["a"])({url:"/user/Cluster/delShard",method:"post",data:t})}function j(t){return Object(n["a"])({url:"/user/Cluster/delComp",method:"post",data:t})}function O(t){return Object(n["a"])({url:"/user/Cluster/delSnode",method:"post",data:t})}function y(t){return Object(n["a"])({url:"/user/Cluster/controlInstance",method:"post",data:t})}function S(t){return Object(n["a"])({url:"/user/Cluster/controlInstance",method:"post",data:t})}function k(t){return Object(n["a"])({url:"/user/Cluster/controlInstance",method:"post",data:t})}function w(t){return Object(n["a"])({url:"/user/Cluster/controlInstance",method:"post",data:t})}function x(t){return Object(n["a"])({url:"/user/Cluster/controlInstance",method:"post",data:t})}function D(t){return Object(n["a"])({url:"/user/Cluster/controlInstance",method:"post",data:t})}function L(t){return Object(n["a"])({url:"/user/Cluster/getEffectCluster",method:"post",data:t})}function T(t){return Object(n["a"])({url:"/user/Cluster/updateAssign",method:"post",data:t})}function I(t){return Object(n["a"])({url:"/user/Cluster/getNode",method:"post",data:t})}function N(t){return Object(n["a"])({url:"/user/Cluster/getStandbyNode",method:"post",data:t})}function M(t){return Object(n["a"])({url:"/user/Cluster/getSwitcheOverList",method:"get",params:t})}function F(t){return Object(n["a"])({url:"/user/Cluster/getTaskList",method:"get",params:t})}function z(t){return Object(n["a"])({url:"/user/Cluster/getShardPrimary",method:"post",data:t})}function A(){return Object(n["a"])({url:"/user/Cluster/getStroMachine",method:"get",params:""})}function $(){return Object(n["a"])({url:"/user/Cluster/getCompMachine",method:"get",params:""})}function B(t){return Object(n["a"])({url:"/user/Cluster/getBackUpStorage",method:"post",data:t})}function E(t){return Object(n["a"])({url:"/user/Cluster/switchShard",method:"post",data:t})}function V(t){return Object(n["a"])({url:"/user/Cluster/SetCpuCgroup",method:"post",data:t})}function P(t){return Object(n["a"])({url:"/user/Cluster/addStorage",method:"post",data:t})}function Q(t){return Object(n["a"])({url:"/user/Cluster/updateStorage",method:"post",data:t})}function q(t){return Object(n["a"])({url:"/user/Cluster/delStorage",method:"post",data:t})}function U(t){return Object(n["a"])({url:"/user/Cluster/rebuildNode",method:"post",data:t})}function R(t){return Object(n["a"])({url:"/user/Cluster/getShardsJobLog",method:"post",data:t})}function J(t){return Object(n["a"])({url:"/user/Cluster/getShardsCount",method:"get",params:t})}function G(t){return Object(n["a"])({url:"/user/Cluster/getCompsCount",method:"get",params:t})}function H(t){return Object(n["a"])({url:"/user/Cluster/getNodesCount",method:"get",params:t})}function K(t){return Object(n["a"])({url:"/user/Cluster/getOldCluster",method:"get",params:t})}function W(){return Object(n["a"])({url:"/user/Cluster/getBackupStorageList",method:"get",params:""})}function Y(t){return Object(n["a"])({url:"/user/Cluster/clusterListError",method:"get",params:t})}function X(t){return Object(n["a"])({url:"/user/Cluster/getShardsName",method:"get",params:t})}function Z(t){return Object(n["a"])({url:"/user/Cluster/getCompDBName",method:"get",params:t})}function tt(t){return Object(n["a"])({url:"/user/Cluster/getCompDBTable",method:"get",params:t})}function et(t){return Object(n["a"])({url:"/user/Cluster/getShardTable",method:"get",params:t})}function rt(t){return Object(n["a"])({url:"/user/Cluster/getOtherShards",method:"get",params:t})}function nt(t){return Object(n["a"])({url:"/user/Cluster/expandCluster",method:"post",data:t})}function at(t){return Object(n["a"])({url:"/user/Cluster/getExpandTableList",method:"post",data:t})}function st(t){return Object(n["a"])({url:"/user/Cluster/setMaxDalay",method:"post",data:t})}function ut(t){return Object(n["a"])({url:"/user/Cluster/getMaxDalay",method:"post",data:t})}function it(t){return Object(n["a"])({url:"/user/Cluster/getMetaCluster",method:"get",params:t})}function ot(t){return Object(n["a"])({url:"/user/Cluster/getBackStorageList",method:"get",params:t})}function ct(t){return Object(n["a"])({url:"/user/Cluster/setVariable",method:"post",data:t})}function lt(t){return Object(n["a"])({url:"/user/Cluster/getVariable",method:"post",data:t})}function dt(t){return Object(n["a"])({url:"/user/Cluster/getWorkMode",method:"post",data:t})}function mt(t){return Object(n["a"])({url:"/user/Cluster/getOneBackUpList",method:"get",params:t})}function pt(t){return Object(n["a"])({url:"/user/Cluster/computeList",method:"get",params:t})}function ft(t){return Object(n["a"])({url:"/user/Cluster/shardList",method:"get",params:t})}function gt(t){return Object(n["a"])({url:"/user/Cluster/getNoSwitchList",method:"post",data:t})}function ht(t){return Object(n["a"])({url:"/user/Cluster/setNoSwitch",method:"post",data:t})}function bt(t){return Object(n["a"])({url:"/user/Cluster/delSwitch",method:"post",data:t})}function _t(t){return Object(n["a"])({url:"/user/Cluster/controlInstance",method:"post",data:t})}function Ct(t){return Object(n["a"])({url:"/user/Cluster/getThisShardNodes",method:"post",data:t})}function vt(t){return Object(n["a"])({url:"/user/Cluster/getClusterMonitor",method:"get",params:t})}function jt(t){return Object(n["a"])({url:"/user/ClusterSetting/tableRepartition",method:"post",data:t})}function Ot(t){return Object(n["a"])({url:"/user/ClusterSetting/getPGTableList",method:"get",params:t})}function yt(t){return Object(n["a"])({url:"/user/ClusterSetting/clusterOptions",method:"get",params:t})}},"9a0c":function(t,e,r){var n=r("342f");t.exports=/Version\/10\.\d+(\.\d+)?( Mobile\/\w+)? Safari\//.test(n)},bbec:function(t,e,r){},d059:function(t,e,r){"use strict";r("bbec")},ed08:function(t,e,r){"use strict";r.d(e,"g",(function(){return a})),r.d(e,"f",(function(){return s})),r.d(e,"b",(function(){return u})),r.d(e,"d",(function(){return i})),r.d(e,"c",(function(){return o})),r.d(e,"a",(function(){return c})),r.d(e,"e",(function(){return l}));r("53ca"),r("ac1f"),r("00b4"),r("5319"),r("4d63"),r("2c3e"),r("25f0"),r("d3b7"),r("4d90"),r("159b");var n=r("5c96");function a(t,e){return Object(n["Message"])({message:t,type:e,duration:2e3})}function s(){var t=arguments.length>0&&void 0!==arguments[0]?arguments[0]:"确定执行此操作吗？",e=arguments.length>1&&void 0!==arguments[1]?arguments[1]:"warning";return n["MessageBox"].confirm(t,"提示",{confirmButtonText:"确定",cancelButtonText:"取消",type:e,center:!0})}function u(t){var e=t.split("T");return e[0]+" "+e[1]}function i(){var t=new Date,e="-",r=":",n=t.getFullYear(),a=t.getMonth()+1,s=t.getDate(),u=t.getHours(),i=t.getMinutes(),o=t.getSeconds(),c=["星期一","星期二","星期三","星期四","星期五","星期六","星期天"];c[t.getDay()];a>=1&&a<=9&&(a="0"+a),s>=0&&s<=9&&(s="0"+s),u>=0&&u<=9&&(u="0"+u),i>=0&&i<=9&&(i="0"+i),o>=0&&o<=9&&(o="0"+o);var l=n+e+a+e+s+" "+u+r+i+r+o;return l}function o(t){var e=t.split("-"),r=e[0],n=e[1],a=e[2],s=new Date(r,n,0);s=s.getDate();var u=r,i=parseInt(n)+1;13==i&&(u=parseInt(u)+1,i=1);var o=a,c=new Date(u,i,0);c=c.getDate(),o>c&&(o=c),i<10&&(i="0"+i);var l=u+"-"+i+"-"+o;return l}function c(){for(var t="",e=4,r=new Array(0,1,2,3,4,5,6,7,8,9),n=0;n<e;n++){var a=Math.floor(9*Math.random());t+=r[a]}return t}function l(){var t=arguments.length>0&&void 0!==arguments[0]?arguments[0]:"确定执行此操作吗？",e=arguments.length>1&&void 0!==arguments[1]?arguments[1]:"warning";return n["MessageBox"].prompt(t,"提示",{confirmButtonText:"确定",cancelButtonText:"取消",inputPlaceholder:"请输入code",inputPattern:/^[0-9]\d*$/,inputErrorMessage:"code格式不正确",type:e})}}}]);