(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-62b90b89"],{"0ccb":function(e,t,a){var r=a("50c4"),n=a("1148"),i=a("1d80"),s=Math.ceil,o=function(e){return function(t,a,o){var l,u,c=String(i(t)),d=c.length,p=void 0===o?" ":String(o),m=r(a);return m<=d||""==p?c:(l=m-d,u=n.call(p,s(l/p.length)),u.length>l&&(u=u.slice(0,l)),e?c+u:u+c)}};e.exports={start:o(!1),end:o(!0)}},1148:function(e,t,a){"use strict";var r=a("a691"),n=a("1d80");e.exports="".repeat||function(e){var t=String(n(this)),a="",i=r(e);if(i<0||i==1/0)throw RangeError("Wrong number of repetitions");for(;i>0;(i>>>=1)&&(t+=t))1&i&&(a+=t);return a}},"333d":function(e,t,a){"use strict";var r=function(){var e=this,t=e.$createElement,a=e._self._c||t;return a("div",{staticClass:"pagination-container",class:{hidden:e.hidden}},[a("el-pagination",e._b({attrs:{background:e.background,"current-page":e.currentPage,"page-size":e.pageSize,layout:e.layout,"page-sizes":e.pageSizes,total:e.total},on:{"update:currentPage":function(t){e.currentPage=t},"update:current-page":function(t){e.currentPage=t},"update:pageSize":function(t){e.pageSize=t},"update:page-size":function(t){e.pageSize=t},"size-change":e.handleSizeChange,"current-change":e.handleCurrentChange}},"el-pagination",e.$attrs,!1))],1)},n=[];a("a9e3");Math.easeInOutQuad=function(e,t,a,r){return e/=r/2,e<1?a/2*e*e+t:(e--,-a/2*(e*(e-2)-1)+t)};var i=function(){return window.requestAnimationFrame||window.webkitRequestAnimationFrame||window.mozRequestAnimationFrame||function(e){window.setTimeout(e,1e3/60)}}();function s(e){document.documentElement.scrollTop=e,document.body.parentNode.scrollTop=e,document.body.scrollTop=e}function o(){return document.documentElement.scrollTop||document.body.parentNode.scrollTop||document.body.scrollTop}function l(e,t,a){var r=o(),n=e-r,l=20,u=0;t="undefined"===typeof t?500:t;var c=function e(){u+=l;var o=Math.easeInOutQuad(u,r,n,t);s(o),u<t?i(e):a&&"function"===typeof a&&a()};c()}var u={name:"Pagination",props:{total:{required:!0,type:Number},page:{type:Number,default:1},limit:{type:Number,default:20},pageSizes:{type:Array,default:function(){return[10,20,30,50]}},layout:{type:String,default:"total, sizes, prev, pager, next, jumper"},background:{type:Boolean,default:!0},autoScroll:{type:Boolean,default:!0},hidden:{type:Boolean,default:!1}},computed:{currentPage:{get:function(){return this.page},set:function(e){this.$emit("update:page",e)}},pageSize:{get:function(){return this.limit},set:function(e){this.$emit("update:limit",e)}}},methods:{handleSizeChange:function(e){this.$emit("pagination",{page:this.currentPage,limit:e}),this.autoScroll&&l(0,800)},handleCurrentChange:function(e){this.$emit("pagination",{page:e,limit:this.pageSize}),this.autoScroll&&l(0,800)}}},c=u,d=(a("d059"),a("2877")),p=Object(d["a"])(c,r,n,!1,null,"65877abe",null);t["a"]=p.exports},"4d90":function(e,t,a){"use strict";var r=a("23e7"),n=a("0ccb").start,i=a("9a0c");r({target:"String",proto:!0,forced:i},{padStart:function(e){return n(this,e,arguments.length>1?arguments[1]:void 0)}})},9763:function(e,t,a){"use strict";a.r(t);var r=function(){var e=this,t=e.$createElement,a=e._self._c||t;return a("div",{staticClass:"app-container"},[a("div",{staticClass:"filter-container"},[a("div",{staticClass:"table-list-search-wrap"},[a("el-input",{staticClass:"list_search_keyword",attrs:{placeholder:"可输入用户账号搜索"},nativeOn:{keyup:function(t){return!t.type.indexOf("key")&&e._k(t.keyCode,"enter",13,t.key,"Enter")?null:e.handleFilter(t)}},model:{value:e.listQuery.username,callback:function(t){e.$set(e.listQuery,"username",t)},expression:"listQuery.username"}}),a("el-button",{attrs:{icon:"el-icon-search"},on:{click:e.handleFilter}},[e._v(" 查询 ")]),a("el-button",{attrs:{icon:"el-icon-refresh-right"},on:{click:e.handleClear}},[e._v(" 重置 ")]),a("el-button",{directives:[{name:"show",rawName:"v-show",value:"1"===e.main_user,expression:"main_user === '1'"}],staticClass:"filter-item",attrs:{type:"primary",icon:"el-icon-plus"},on:{click:e.handleCreate}},[e._v("新增 ")])],1)]),a("el-table",{directives:[{name:"loading",rawName:"v-loading",value:e.listLoading,expression:"listLoading"}],key:e.tableKey,staticStyle:{width:"100%","margin-bottom":"20px"},attrs:{data:e.list,border:"","highlight-current-row":""}},[e._v(" > "),a("el-table-column",{attrs:{type:"index",align:"center",label:"序号",width:"50"}}),a("el-table-column",{attrs:{label:"用户账号",align:"center"},scopedSlots:e._u([{key:"default",fn:function(t){var r=t.row;return[a("span",{staticClass:"link-type",on:{click:function(t){return e.handleDetail(r)}}},[e._v(e._s(r.username))])]}}])}),a("el-table-column",{attrs:{prop:"email",align:"center",label:"邮箱地址"}}),a("el-table-column",{attrs:{prop:"main_user",align:"center",label:"管理员"},scopedSlots:e._u([{key:"default",fn:function(t){return[e._v(" "+e._s(1==t.row.main_user?"是":"否")+" ")]}}])}),a("el-table-column",{attrs:{prop:"pg_dsn",align:"center",width:"500",label:"DSN"}}),"1"===e.main_user?a("el-table-column",{attrs:{label:"操作",align:"center",width:"230","class-name":"small-padding fixed-width"},scopedSlots:e._u([{key:"default",fn:function(t){var r=t.row,n=t.$index;return[a("el-button",{directives:[{name:"show",rawName:"v-show",value:"1"===e.main_user,expression:"main_user === '1'"}],attrs:{type:"primary",size:"mini"},on:{click:function(t){return e.handleUpdate(r)}}},[e._v("编辑 ")]),a("el-button",{directives:[{name:"show",rawName:"v-show",value:"1"===e.main_user,expression:"main_user === '1'"}],attrs:{size:"mini",type:"danger"},on:{click:function(t){return e.handleDelete(r,n)}}},[e._v("禁用 ")])]}}],null,!1,232429667)}):e._e()],1),a("pagination",{directives:[{name:"show",rawName:"v-show",value:e.total>0,expression:"total > 0"}],attrs:{total:e.total,page:e.listQuery.pageNo,limit:e.listQuery.pageSize},on:{"update:page":function(t){return e.$set(e.listQuery,"pageNo",t)},"update:limit":function(t){return e.$set(e.listQuery,"pageSize",t)},pagination:e.getList}}),a("el-dialog",{attrs:{title:e.textMap[e.dialogStatus],visible:e.dialogFormVisible,"custom-class":"single_dal_view"},on:{"update:visible":function(t){e.dialogFormVisible=t}}},[a("el-form",{ref:"dataForm",attrs:{rules:e.rules,model:e.temp,"label-position":"left","label-width":"110px"}},[a("el-form-item",{attrs:{label:"用户账号:",prop:"username"}},[a("el-input",{attrs:{placeholder:"请输入用户账号",disabled:"detail"===e.dialogStatus||"super_dba"==e.temp.username},model:{value:e.temp.username,callback:function(t){e.$set(e.temp,"username",t)},expression:"temp.username"}})],1),"create"===e.dialogStatus?a("el-form-item",{attrs:{label:"登录密码:",prop:"password"}},[a("el-input",{attrs:{type:e.pwdType,placeholder:"登录密码，大小写字母/数字/特殊字符组合，长度8-12位",autocomplete:"new-password"},model:{value:e.temp.password,callback:function(t){e.$set(e.temp,"password",t)},expression:"temp.password"}},[a("i",{staticClass:"el-icon-view",attrs:{slot:"suffix"},on:{click:e.changeType},slot:"suffix"})])],1):e._e(),"create"===e.dialogStatus?a("el-form-item",{attrs:{label:"重复密码:",prop:"confirmPassword"}},[a("el-input",{attrs:{type:e.pwdconfirmType,placeholder:"请再次输入登录密码"},model:{value:e.temp.confirmPassword,callback:function(t){e.$set(e.temp,"confirmPassword",t)},expression:"temp.confirmPassword"}},[a("i",{staticClass:"el-icon-view",attrs:{slot:"suffix"},on:{click:e.changeconfirmType},slot:"suffix"})])],1):e._e(),a("el-form-item",{attrs:{label:"邮箱地址:",prop:"email"}},[a("el-input",{attrs:{type:"email",placeholder:"请输入邮箱地址",disabled:"detail"===e.dialogStatus},model:{value:e.temp.email,callback:function(t){e.$set(e.temp,"email",t)},expression:"temp.email"}})],1)],1),a("div",{staticClass:"dialog-footer",attrs:{slot:"footer"},slot:"footer"},[a("el-button",{directives:[{name:"show",rawName:"v-show",value:!e.dialogDetail,expression:"!dialogDetail"}],on:{click:function(t){e.dialogFormVisible=!1}}},[e._v("关闭")]),a("el-button",{directives:[{name:"show",rawName:"v-show",value:!e.dialogDetail,expression:"!dialogDetail"}],attrs:{type:"primary"},on:{click:function(t){"create"===e.dialogStatus?e.createData():e.updateData(e.row)}}},[e._v(" 确认 ")])],1)],1)],1)},n=[],i=(a("ac1f"),a("00b4"),a("ed08")),s=a("a287"),o=a("333d"),l={name:"Account",components:{Pagination:o["a"]},data:function(){var e=this,t=function(t,a,r){a?/^[0-9a-zA-Z_]+$/.test(a)?"internal_user"===a?r(new Error("该账号为系统账号，不允许添加")):e.temp.old_username?e.temp.old_username&&e.temp.old_username!==a?Object(s["d"])(a).then((function(e){e.message?r(new Error("用户账号重复")):r()})):r():Object(s["d"])(a).then((function(e){e.message?r(new Error("用户账号重复")):r()})):r(new Error("用户账号只能输入英文字母、数字、下划线")):r(new Error("请输入用户账号"))},a=function(e,t,a){var r=/^(?!.*\s)(?=.*?[a-z])(?=.*?[A-Z])(?=.*?[0-9])(?=.*?[-!@#$%^&*()_+|~=`{}\[\]:";'<>?,.\/]).{8,12}$/;t?r.test(t)?a():a(new Error("密码必须由大小写字母,数字,特殊字符(不含空格)组成,且长度为8到12位")):a(new Error("请输入登陆密码"))},r=function(t,a,r){a?a!=e.temp.password?r(new Error("两次输入密码不一致")):r():r(new Error("重复密码不能为空"))},n=function(t,a,r){e.search_person_has_m?r():a?/^1[0-9]\d{9}$/.test(a)?e.temp.old_mobile?e.temp.old_mobile&&e.temp.old_mobile!==a?Object(s["c"])(a).then((function(e){e.message?r(new Error("手机号重复")):r()})):r():Object(s["c"])(a).then((function(e){e.message?r(new Error("手机号重复")):r()})):r(new Error("手机号格式不对")):r(new Error("请输入手机号"))},i=function(e,t,a){var r=/^[a-zA-Z0-9]+([-_.][a-zA-Z0-9]+)*@[a-zA-Z0-9]+([-_.][a-zA-Z0-9]+)*\.[a-z]{2,}$/;t?r.test(t)?a():a(new Error("邮箱地址格式有误")):a(new Error("请输入邮箱地址"))};return{tableKey:0,list:null,listLoading:!0,searchLoading:!1,total:0,listQuery:{pageNo:1,pageSize:10,username:""},search_person_has_m:!1,temp:{username:"",old_username:"",password:"",phone_number:"",email:"",wechat_number:"",old_mobile:"",confirmPassword:""},dialogFormVisible:!1,dialogEditVisible:!1,dialogStatus:"",textMap:{update:"编辑用户",create:"新增用户",detail:"详情"},dialogDetail:!1,message_tips:"",message_type:"",user_add_priv:JSON.parse(sessionStorage.getItem("priv")).user_add_priv,user_drop_priv:JSON.parse(sessionStorage.getItem("priv")).user_drop_priv,user_edit_priv:JSON.parse(sessionStorage.getItem("priv")).user_edit_priv,row:{},pwdType:"text",pwdconfirmType:"text",main_user:"0",uid:1,user_tag:"",rules:{username:[{required:!0,trigger:"blur",validator:t}],password:[{required:!0,trigger:"blur",validator:a}],phone_number:[{required:!0,trigger:"blur",validator:n}],confirmPassword:[{required:!0,trigger:"blur",validator:r}],email:[{required:!0,trigger:"blur",validator:i}]}}},created:function(){this.getList()},methods:{handleFilter:function(){this.listQuery.pageNo=1,this.getList()},handleClear:function(){this.listQuery.username="",this.listQuery.pageNo=1,this.getList()},getList:function(){var e=this;this.listLoading=!0;var t=Object.assign({},this.listQuery);t.user_name=sessionStorage.getItem("login_username"),Object(s["e"])(t).then((function(t){e.list=t.list,e.total=t.total,e.main_user=t.main_user,e.uid=t.uid,e.user_tag=t.user_tag,setTimeout((function(){e.listLoading=!1}),500)}))},resetTemp:function(){this.search_person_has_m=!1,this.temp={username:"",old_username:"",password:"",phone_number:"",email:"",wechat_number:"",old_mobile:"",confirmPassword:"",pid:this.uid,user_tag:this.user_tag,MainUser:"0"}},handleCreate:function(){var e=this;this.resetTemp(),this.dialogStatus="create",this.dialogFormVisible=!0,this.dialogDetail=!1,this.$nextTick((function(){e.$refs.dataForm.clearValidate()}))},createData:function(){var e=this;this.$refs["dataForm"].validate((function(t){if(t){var a=Object.assign({},e.temp);Object(s["a"])(a).then((function(t){var a=t;200===a.code?(e.getList(),e.dialogFormVisible=!1,e.message_tips="新增成功",e.message_type="success"):(e.message_tips=a.message,e.message_type="error"),Object(i["g"])(e.message_tips,e.message_type)}))}}))},handleDetail:function(e){this.dialogStatus="detail",this.dialogFormVisible=!0,this.temp=Object.assign({},e),this.dialogDetail=!0},handleUpdate:function(e){var t=this;this.resetTemp(),this.temp=Object.assign({},e),this.dialogStatus="update",this.dialogFormVisible=!0,this.dialogDetail=!1,this.temp.old_mobile=e.phone_number,this.temp.old_username=e.username,this.$nextTick((function(){t.$refs["dataForm"].clearValidate()}))},updateData:function(){var e=this;this.$refs["dataForm"].validate((function(t){if(t){var a=Object.assign({},e.temp);Object(s["g"])(a).then((function(t){var a=t;200===a.code?(e.dialogFormVisible=!1,e.message_tips="编辑成功",e.message_type="success",e.getList()):(e.message_tips=a.message,e.message_type="error"),Object(i["g"])(e.message_tips,e.message_type)}))}}))},handleDelete:function(e){var t=this;Object(i["f"])("禁止账号使用, 是否继续?").then((function(){Object(s["b"])(e.id).then((function(e){console.log(e);var a=e;200===a.code?(t.dialogFormVisible=!1,t.message_tips="删除成功",t.message_type="success",t.getList()):(t.message_tips=a.message,t.message_type="error"),Object(i["g"])(t.message_tips,t.message_type)}))})).catch((function(){console.log("quxiao"),Object(i["g"])("已取消删除","info")}))},changeconfirmType:function(){this.pwdconfirmType="password"===this.pwdconfirmType?"text":"password";var e=document.getElementsByClassName("el-icon-view")[1];"text"===this.pwdconfirmType?e.setAttribute("style","color: #409EFF"):e.setAttribute("style","color: #c0c4cc")},changeType:function(){this.pwdType="password"===this.pwdType?"text":"password";var e=document.getElementsByClassName("el-icon-view")[0];"text"===this.pwdType?e.setAttribute("style","color: #409EFF"):e.setAttribute("style","color: #c0c4cc")}}},u=l,c=a("2877"),d=Object(c["a"])(u,r,n,!1,null,null,null);t["default"]=d.exports},"9a0c":function(e,t,a){var r=a("342f");e.exports=/Version\/10\.\d+(\.\d+)?( Mobile\/\w+)? Safari\//.test(r)},a287:function(e,t,a){"use strict";a.d(t,"e",(function(){return n})),a.d(t,"a",(function(){return i})),a.d(t,"f",(function(){return s})),a.d(t,"g",(function(){return o})),a.d(t,"b",(function(){return l})),a.d(t,"c",(function(){return u})),a.d(t,"d",(function(){return c}));var r=a("b775");function n(e){return Object(r["a"])({url:"/user/User/userList",method:"get",params:e})}function i(e){return Object(r["a"])({url:"/user/User/add",method:"post",data:e})}function s(e){return Object(r["a"])({url:"/user/User/getVcode",method:"post",data:e})}function o(e){return Object(r["a"])({url:"/user/User/edit",method:"post",data:e})}function l(e){return Object(r["a"])({url:"/user/User/delete",method:"post",data:{id:e}})}function u(e){return Object(r["a"])({url:"/user/User/checkMobile",method:"post",data:{phone_number:e}})}function c(e){return Object(r["a"])({url:"/user/User/checkUserName",method:"post",data:{username:e}})}},bbec:function(e,t,a){},d059:function(e,t,a){"use strict";a("bbec")},ed08:function(e,t,a){"use strict";a.d(t,"g",(function(){return n})),a.d(t,"f",(function(){return i})),a.d(t,"b",(function(){return s})),a.d(t,"d",(function(){return o})),a.d(t,"c",(function(){return l})),a.d(t,"a",(function(){return u})),a.d(t,"e",(function(){return c}));a("53ca"),a("ac1f"),a("00b4"),a("5319"),a("4d63"),a("2c3e"),a("25f0"),a("d3b7"),a("4d90"),a("159b");var r=a("5c96");function n(e,t){return Object(r["Message"])({message:e,type:t,duration:2e3})}function i(){var e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:"确定执行此操作吗？",t=arguments.length>1&&void 0!==arguments[1]?arguments[1]:"warning";return r["MessageBox"].confirm(e,"提示",{confirmButtonText:"确定",cancelButtonText:"取消",type:t,center:!0})}function s(e){var t=e.split("T");return t[0]+" "+t[1]}function o(){var e=new Date,t="-",a=":",r=e.getFullYear(),n=e.getMonth()+1,i=e.getDate(),s=e.getHours(),o=e.getMinutes(),l=e.getSeconds(),u=["星期一","星期二","星期三","星期四","星期五","星期六","星期天"];u[e.getDay()];n>=1&&n<=9&&(n="0"+n),i>=0&&i<=9&&(i="0"+i),s>=0&&s<=9&&(s="0"+s),o>=0&&o<=9&&(o="0"+o),l>=0&&l<=9&&(l="0"+l);var c=r+t+n+t+i+" "+s+a+o+a+l;return c}function l(e){var t=e.split("-"),a=t[0],r=t[1],n=t[2],i=new Date(a,r,0);i=i.getDate();var s=a,o=parseInt(r)+1;13==o&&(s=parseInt(s)+1,o=1);var l=n,u=new Date(s,o,0);u=u.getDate(),l>u&&(l=u),o<10&&(o="0"+o);var c=s+"-"+o+"-"+l;return c}function u(){for(var e="",t=4,a=new Array(0,1,2,3,4,5,6,7,8,9),r=0;r<t;r++){var n=Math.floor(9*Math.random());e+=a[n]}return e}function c(){var e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:"确定执行此操作吗？",t=arguments.length>1&&void 0!==arguments[1]?arguments[1]:"warning";return r["MessageBox"].prompt(e,"提示",{confirmButtonText:"确定",cancelButtonText:"取消",inputPlaceholder:"请输入code",inputPattern:/^[0-9]\d*$/,inputErrorMessage:"code格式不正确",type:t})}}}]);