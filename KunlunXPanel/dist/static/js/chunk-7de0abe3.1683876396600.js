(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-7de0abe3"],{"0ccb":function(t,e,r){var n=r("50c4"),u=r("1148"),o=r("1d80"),s=Math.ceil,a=function(t){return function(e,r,a){var c,i,d=String(o(e)),l=d.length,f=void 0===a?" ":String(a),m=n(r);return m<=l||""==f?d:(c=m-l,i=u.call(f,s(c/f.length)),i.length>c&&(i=i.slice(0,c)),t?d+i:i+d)}};t.exports={start:a(!1),end:a(!0)}},1148:function(t,e,r){"use strict";var n=r("a691"),u=r("1d80");t.exports="".repeat||function(t){var e=String(u(this)),r="",o=n(t);if(o<0||o==1/0)throw RangeError("Wrong number of repetitions");for(;o>0;(o>>>=1)&&(e+=e))1&o&&(r+=e);return r}},"1cb4":function(t,e,r){},"44a1":function(t,e,r){},"4d90":function(t,e,r){"use strict";var n=r("23e7"),u=r("0ccb").start,o=r("9a0c");n({target:"String",proto:!0,forced:o},{padStart:function(t){return u(this,t,arguments.length>1?arguments[1]:void 0)}})},"589a":function(t,e,r){"use strict";r("1cb4")},"82b0":function(t,e,r){"use strict";r.d(e,"x",(function(){return u})),r.d(e,"F",(function(){return o})),r.d(e,"I",(function(){return s})),r.d(e,"k",(function(){return a})),r.d(e,"l",(function(){return c})),r.d(e,"f",(function(){return i})),r.d(e,"u",(function(){return d})),r.d(e,"hb",(function(){return l})),r.d(e,"lb",(function(){return f})),r.d(e,"s",(function(){return m})),r.d(e,"W",(function(){return p})),r.d(e,"d",(function(){return g})),r.d(e,"b",(function(){return h})),r.d(e,"c",(function(){return b})),r.d(e,"G",(function(){return C})),r.d(e,"z",(function(){return O})),r.d(e,"n",(function(){return j})),r.d(e,"m",(function(){return v})),r.d(e,"o",(function(){return w})),r.d(e,"qb",(function(){return _})),r.d(e,"rb",(function(){return S})),r.d(e,"sb",(function(){return k})),r.d(e,"tb",(function(){return y})),r.d(e,"jb",(function(){return x})),r.d(e,"kb",(function(){return F})),r.d(e,"E",(function(){return I})),r.d(e,"wb",(function(){return L})),r.d(e,"O",(function(){return T})),r.d(e,"ab",(function(){return M})),r.d(e,"cb",(function(){return D})),r.d(e,"db",(function(){return B})),r.d(e,"U",(function(){return N})),r.d(e,"bb",(function(){return E})),r.d(e,"C",(function(){return $})),r.d(e,"v",(function(){return P})),r.d(e,"ub",(function(){return A})),r.d(e,"a",(function(){return q})),r.d(e,"e",(function(){return J})),r.d(e,"yb",(function(){return U})),r.d(e,"p",(function(){return R})),r.d(e,"ib",(function(){return z})),r.d(e,"Y",(function(){return V})),r.d(e,"X",(function(){return W})),r.d(e,"D",(function(){return G})),r.d(e,"P",(function(){return H})),r.d(e,"Q",(function(){return X})),r.d(e,"w",(function(){return Y})),r.d(e,"g",(function(){return Z})),r.d(e,"Z",(function(){return K})),r.d(e,"A",(function(){return Q})),r.d(e,"B",(function(){return tt})),r.d(e,"V",(function(){return et})),r.d(e,"S",(function(){return rt})),r.d(e,"r",(function(){return nt})),r.d(e,"H",(function(){return ut})),r.d(e,"mb",(function(){return ot})),r.d(e,"L",(function(){return st})),r.d(e,"M",(function(){return at})),r.d(e,"t",(function(){return ct})),r.d(e,"ob",(function(){return it})),r.d(e,"fb",(function(){return dt})),r.d(e,"gb",(function(){return lt})),r.d(e,"R",(function(){return ft})),r.d(e,"i",(function(){return mt})),r.d(e,"pb",(function(){return pt})),r.d(e,"N",(function(){return gt})),r.d(e,"nb",(function(){return ht})),r.d(e,"q",(function(){return bt})),r.d(e,"j",(function(){return Ct})),r.d(e,"eb",(function(){return Ot})),r.d(e,"y",(function(){return jt})),r.d(e,"vb",(function(){return vt})),r.d(e,"T",(function(){return wt})),r.d(e,"h",(function(){return _t})),r.d(e,"J",(function(){return St})),r.d(e,"K",(function(){return kt})),r.d(e,"xb",(function(){return yt}));var n=r("b775");function u(t){return Object(n["a"])({url:"/user/Cluster/clusterList",method:"get",params:t})}function o(t){return Object(n["a"])({url:"/user/Cluster/getEffectComp",method:"post",data:t})}function s(t){return Object(n["a"])({url:"/user/Cluster/getExperience",method:"post",data:t})}function a(t){return Object(n["a"])({url:"/user/Cluster/createCluster",method:"post",data:t})}function c(t){return Object(n["a"])({url:"/user/Cluster/delCluster",method:"post",data:t})}function i(t){return Object(n["a"])({url:"/user/Cluster/backUpCluster",method:"post",data:t})}function d(t){return Object(n["a"])({url:"/user/Cluster/getBackUpList",method:"get",params:t})}function l(t){return Object(n["a"])({url:"/user/Cluster/ifBackUp",method:"post",data:{id:t}})}function f(t){return Object(n["a"])({url:"/user/Cluster/restoreCluster",method:"post",data:t})}function m(){return Object(n["a"])({url:"/user/Cluster/getAllMachine",method:"get",params:""})}function p(t){return Object(n["a"])({url:"/user/Cluster/getShards",method:"post",data:{id:t}})}function g(t){return Object(n["a"])({url:"/user/Cluster/addShards",method:"post",data:t})}function h(t){return Object(n["a"])({url:"/user/Cluster/addComps",method:"post",data:t})}function b(t){return Object(n["a"])({url:"/user/Cluster/addNodes",method:"post",data:t})}function C(t){return Object(n["a"])({url:"/user/Cluster/getStatus",method:"post",data:t})}function O(t){return Object(n["a"])({url:"/user/Cluster/getClusterNodesList",method:"post",data:t})}function j(t){return Object(n["a"])({url:"/user/Cluster/delShard",method:"post",data:t})}function v(t){return Object(n["a"])({url:"/user/Cluster/delComp",method:"post",data:t})}function w(t){return Object(n["a"])({url:"/user/Cluster/delSnode",method:"post",data:t})}function _(t){return Object(n["a"])({url:"/user/Cluster/controlInstance",method:"post",data:t})}function S(t){return Object(n["a"])({url:"/user/Cluster/controlInstance",method:"post",data:t})}function k(t){return Object(n["a"])({url:"/user/Cluster/controlInstance",method:"post",data:t})}function y(t){return Object(n["a"])({url:"/user/Cluster/controlInstance",method:"post",data:t})}function x(t){return Object(n["a"])({url:"/user/Cluster/controlInstance",method:"post",data:t})}function F(t){return Object(n["a"])({url:"/user/Cluster/controlInstance",method:"post",data:t})}function I(t){return Object(n["a"])({url:"/user/Cluster/getEffectCluster",method:"post",data:t})}function L(t){return Object(n["a"])({url:"/user/Cluster/updateAssign",method:"post",data:t})}function T(t){return Object(n["a"])({url:"/user/Cluster/getNode",method:"post",data:t})}function M(t){return Object(n["a"])({url:"/user/Cluster/getStandbyNode",method:"post",data:t})}function D(t){return Object(n["a"])({url:"/user/Cluster/getSwitcheOverList",method:"get",params:t})}function B(t){return Object(n["a"])({url:"/user/Cluster/getTaskList",method:"get",params:t})}function N(t){return Object(n["a"])({url:"/user/Cluster/getShardPrimary",method:"post",data:t})}function E(){return Object(n["a"])({url:"/user/Cluster/getStroMachine",method:"get",params:""})}function $(){return Object(n["a"])({url:"/user/Cluster/getCompMachine",method:"get",params:""})}function P(t){return Object(n["a"])({url:"/user/Cluster/getBackUpStorage",method:"post",data:t})}function A(t){return Object(n["a"])({url:"/user/Cluster/switchShard",method:"post",data:t})}function q(t){return Object(n["a"])({url:"/user/Cluster/SetCpuCgroup",method:"post",data:t})}function J(t){return Object(n["a"])({url:"/user/Cluster/addStorage",method:"post",data:t})}function U(t){return Object(n["a"])({url:"/user/Cluster/updateStorage",method:"post",data:t})}function R(t){return Object(n["a"])({url:"/user/Cluster/delStorage",method:"post",data:t})}function z(t){return Object(n["a"])({url:"/user/Cluster/rebuildNode",method:"post",data:t})}function V(t){return Object(n["a"])({url:"/user/Cluster/getShardsJobLog",method:"post",data:t})}function W(t){return Object(n["a"])({url:"/user/Cluster/getShardsCount",method:"get",params:t})}function G(t){return Object(n["a"])({url:"/user/Cluster/getCompsCount",method:"get",params:t})}function H(t){return Object(n["a"])({url:"/user/Cluster/getNodesCount",method:"get",params:t})}function X(t){return Object(n["a"])({url:"/user/Cluster/getOldCluster",method:"get",params:t})}function Y(){return Object(n["a"])({url:"/user/Cluster/getBackupStorageList",method:"get",params:""})}function Z(t){return Object(n["a"])({url:"/user/Cluster/clusterListError",method:"get",params:t})}function K(t){return Object(n["a"])({url:"/user/Cluster/getShardsName",method:"get",params:t})}function Q(t){return Object(n["a"])({url:"/user/Cluster/getCompDBName",method:"get",params:t})}function tt(t){return Object(n["a"])({url:"/user/Cluster/getCompDBTable",method:"get",params:t})}function et(t){return Object(n["a"])({url:"/user/Cluster/getShardTable",method:"get",params:t})}function rt(t){return Object(n["a"])({url:"/user/Cluster/getOtherShards",method:"get",params:t})}function nt(t){return Object(n["a"])({url:"/user/Cluster/expandCluster",method:"post",data:t})}function ut(t){return Object(n["a"])({url:"/user/Cluster/getExpandTableList",method:"post",data:t})}function ot(t){return Object(n["a"])({url:"/user/Cluster/setMaxDalay",method:"post",data:t})}function st(t){return Object(n["a"])({url:"/user/Cluster/getMaxDalay",method:"post",data:t})}function at(t){return Object(n["a"])({url:"/user/Cluster/getMetaCluster",method:"get",params:t})}function ct(t){return Object(n["a"])({url:"/user/Cluster/getBackStorageList",method:"get",params:t})}function it(t){return Object(n["a"])({url:"/user/Cluster/setVariable",method:"post",data:t})}function dt(t){return Object(n["a"])({url:"/user/Cluster/getVariable",method:"post",data:t})}function lt(t){return Object(n["a"])({url:"/user/Cluster/getWorkMode",method:"post",data:t})}function ft(t){return Object(n["a"])({url:"/user/Cluster/getOneBackUpList",method:"get",params:t})}function mt(t){return Object(n["a"])({url:"/user/Cluster/computeList",method:"get",params:t})}function pt(t){return Object(n["a"])({url:"/user/Cluster/shardList",method:"get",params:t})}function gt(t){return Object(n["a"])({url:"/user/Cluster/getNoSwitchList",method:"post",data:t})}function ht(t){return Object(n["a"])({url:"/user/Cluster/setNoSwitch",method:"post",data:t})}function bt(t){return Object(n["a"])({url:"/user/Cluster/delSwitch",method:"post",data:t})}function Ct(t){return Object(n["a"])({url:"/user/Cluster/controlInstance",method:"post",data:t})}function Ot(t){return Object(n["a"])({url:"/user/Cluster/getThisShardNodes",method:"post",data:t})}function jt(t){return Object(n["a"])({url:"/user/Cluster/getClusterMonitor",method:"get",params:t})}function vt(t){return Object(n["a"])({url:"/user/ClusterSetting/tableRepartition",method:"post",data:t})}function wt(t){return Object(n["a"])({url:"/user/ClusterSetting/getPGTableList",method:"get",params:t})}function _t(t){return Object(n["a"])({url:"/user/ClusterSetting/clusterOptions",method:"get",params:t})}function St(t){return Object(n["a"])({url:"/user/Cluster/getLogicalBackUpList",method:"get",params:t})}function kt(t){return Object(n["a"])({url:"/user/ClusterSetting/getRecordLogicalBackup",method:"get",params:t})}function yt(t){return Object(n["a"])({url:"/user/Cluster/updateShard",method:"post",data:t})}},"82d1":function(t,e,r){"use strict";r("44a1")},"9a0c":function(t,e,r){var n=r("342f");t.exports=/Version\/10\.\d+(\.\d+)?( Mobile\/\w+)? Safari\//.test(n)},"9ed6":function(t,e,r){"use strict";r.r(e);var n=function(){var t=this,e=t.$createElement,r=t._self._c||e;return r("div",{staticClass:"page-module login-container backgroundCover flex width_100 height_100"},[r("div",{staticClass:"login-panel"},[r("Loginheader"),r("el-form",{ref:"loginForm",staticClass:"login-form",attrs:{model:t.loginForm,rules:t.loginRules,"auto-complete":"on","label-position":"left"}},[r("div",{staticClass:"title-container"},[r("h3",{staticClass:"title"},[t._v("统一管理用户登录")])]),r("el-form-item",{attrs:{prop:"username"}},[r("el-input",{ref:"username",attrs:{placeholder:"请输入账户",name:"username",type:"text",tabindex:"1","auto-complete":"on"},model:{value:t.loginForm.username,callback:function(e){t.$set(t.loginForm,"username",e)},expression:"loginForm.username"}})],1),r("el-form-item",{attrs:{prop:"password"}},[r("el-input",{key:t.passwordType,ref:"password",attrs:{type:t.passwordType,placeholder:"请输入密码",name:"password",tabindex:"2","auto-complete":"on"},nativeOn:{keyup:function(e){return!e.type.indexOf("key")&&t._k(e.keyCode,"enter",13,e.key,"Enter")?null:t.handleLogin(e)}},model:{value:t.loginForm.password,callback:function(e){t.$set(t.loginForm,"password",e)},expression:"loginForm.password"}}),r("span",{staticClass:"show-pwd",on:{click:t.showPwd}},[r("svg-icon",{attrs:{"icon-class":"password"===t.passwordType?"eye":"eye-open"}})],1)],1),r("div",{staticClass:"oh change_password_wrap"},[r("el-checkbox",{staticClass:"remember",model:{value:t.checked,callback:function(e){t.checked=e},expression:"checked"}},[t._v("记住密码")]),r("router-link",{staticClass:"change_password",attrs:{to:{path:"/alteration"}}},[t._v("忘记密码")])],1),r("el-button",{staticStyle:{width:"100%","margin-bottom":"20px"},attrs:{loading:t.loading,type:"primary"},nativeOn:{click:function(e){return e.preventDefault(),t.handleLogin(e)}}},[t._v("登录")])],1),t._m(0)],1)])},u=[function(){var t=this,e=t.$createElement,r=t._self._c||e;return r("div",{staticClass:"footer"},[r("p",[r("span",[t._v("© 2023 v1.2 版权所有")]),r("span",[t._v("泽拓科技（深圳）有限责任公司")])])])}],o=r("c7eb"),s=r("1da1"),a=(r("ac1f"),r("00b4"),r("e9c4"),r("abfb")),c=function(){var t=this,e=t.$createElement,r=t._self._c||e;return r("div",{staticClass:"oh login-header"},[r("p",{staticClass:"header fl"},[t._v(t._s(t.company)+"-XPanel管理系统")])])},i=[],d={name:"Loginheader",data:function(){return{company:""}},created:function(){this.company="泽拓科技"}},l=d,f=(r("589a"),r("2877")),m=Object(f["a"])(l,c,i,!1,null,null,null),p=m.exports,g=r("ed08"),h=r("82b0"),b=r("578a"),C={name:"Login",components:{Loginheader:p},data:function(){var t=function(t,e,r){e?/^[A-Za-z0-9_]+$/.test(e)?r():r(new Error("用户名只能输入英文字母、数字、下划线")):r(new Error("请输入用户名"))},e=function(t,e,r){e.length<8?r(new Error("密码不少于8位")):r()};return{loginForm:{username:"",password:""},loginRules:{username:[{required:!0,trigger:"blur",validator:t}],password:[{required:!0,trigger:"blur",validator:e}]},loading:!1,passwordType:"password",redirect:void 0,checked:!0,randCodeImage:"",requestCodeSuccess:!1,verifiedCode:"",options:[],meta:"",msg:"例:192.168.0.1:10001"}},watch:{$route:{handler:function(t){this.redirect=t.query&&t.query.redirect},immediate:!0}},methods:{showPwd:function(){var t=this;"password"===this.passwordType?this.passwordType="":this.passwordType="password",this.$nextTick((function(){t.$refs.password.focus()}))},handleLogin:function(){var t=this;this.checked?(sessionStorage.setItem("login_username",this.loginForm.username),sessionStorage.setItem("login_password",this.loginForm.password)):(sessionStorage.setItem("login_username",""),sessionStorage.setItem("login_password","")),this.$refs.loginForm.validate(function(){var e=Object(s["a"])(Object(o["a"])().mark((function e(r){var n,u,s;return Object(o["a"])().wrap((function(e){while(1)switch(e.prev=e.next){case 0:if(!r){e.next=16;break}return e.next=3,Object(a["a"])();case 3:if(n=e.sent,200!=n.code){e.next=13;break}return t.loading=!0,u={username:t.loginForm.username,password:t.loginForm.password},e.next=9,Object(a["c"])(u);case 9:s=e.sent,200==s.code?t.goto(s,n.meta_ha_mode):(Object(g["g"])(s.message,"error"),t.loading=!1),e.next=14;break;case 13:Object(g["g"])(n.message,"error");case 14:e.next=18;break;case 16:return console.log("error submit!!"),e.abrupt("return",!1);case 18:case"end":return e.stop()}}),e)})));return function(t){return e.apply(this,arguments)}}())},goto:function(t,e){var r=this;if(this.loading=!1,t.Token){sessionStorage.setItem("zettadb_vue_name",t.userName);var n=t.num;if(1==n){var u={};u.user_name=sessionStorage.getItem("login_username"),u.job_id="0",u.job_type="get_cluster_detail",u.version=b["u"][0].ver,u.timestamp=b["s"][0].time+"";var o={};u.paras=o,Object(h["gb"])(u).then((function(n){r.isShowNodeMenuPanel=!1;var u=n;if(u.hasOwnProperty("attachment")){if(null!==u.attachment)sessionStorage.setItem("work_mode",u.attachment.work_mode);else{if(null!=u.attachment)return r.message_tips=u.error_info,r.message_type="error",void Object(g["g"])(r.message_tips,r.message_type);sessionStorage.setItem("work_mode","")}sessionStorage.setItem("zettadb_vue_token",t.Token),sessionStorage.setItem("meta_ha_mode",e),sessionStorage.setItem("apply_all_cluster",t.apply_all_cluster),sessionStorage.setItem("affected_clusters",t.affected_clusters),sessionStorage.setItem("priv",JSON.stringify(t.priv)),r.$router.push({path:"/cluster"})}else r.message_tips=u.error_info,r.message_type="error",Object(g["g"])(r.message_tips,r.message_type)}))}else sessionStorage.setItem("zettadb_vue_token",t.Token),this.$router.push({path:"/alteration"})}else Object(g["g"])("登录接口没有返回token","error")}},created:function(){this.checked?(this.loginForm.username=sessionStorage.getItem("login_username"),this.loginForm.password=sessionStorage.getItem("login_password")):(this.loginForm.username="",this.loginForm.password="")}},O=C,j=(r("82d1"),Object(f["a"])(O,n,u,!1,null,null,null));e["default"]=j.exports},e9c4:function(t,e,r){var n=r("23e7"),u=r("d066"),o=r("d039"),s=u("JSON","stringify"),a=/[\uD800-\uDFFF]/g,c=/^[\uD800-\uDBFF]$/,i=/^[\uDC00-\uDFFF]$/,d=function(t,e,r){var n=r.charAt(e-1),u=r.charAt(e+1);return c.test(t)&&!i.test(u)||i.test(t)&&!c.test(n)?"\\u"+t.charCodeAt(0).toString(16):t},l=o((function(){return'"\\udf06\\ud834"'!==s("\udf06\ud834")||'"\\udead"'!==s("\udead")}));s&&n({target:"JSON",stat:!0,forced:l},{stringify:function(t,e,r){var n=s.apply(null,arguments);return"string"==typeof n?n.replace(a,d):n}})},ed08:function(t,e,r){"use strict";r.d(e,"g",(function(){return u})),r.d(e,"f",(function(){return o})),r.d(e,"b",(function(){return s})),r.d(e,"d",(function(){return a})),r.d(e,"c",(function(){return c})),r.d(e,"a",(function(){return i})),r.d(e,"e",(function(){return d}));r("53ca"),r("ac1f"),r("00b4"),r("5319"),r("4d63"),r("2c3e"),r("25f0"),r("d3b7"),r("4d90"),r("159b");var n=r("5c96");function u(t,e){return Object(n["Message"])({message:t,type:e,duration:2e3})}function o(){var t=arguments.length>0&&void 0!==arguments[0]?arguments[0]:"确定执行此操作吗？",e=arguments.length>1&&void 0!==arguments[1]?arguments[1]:"warning";return n["MessageBox"].confirm(t,"提示",{confirmButtonText:"确定",cancelButtonText:"取消",type:e,center:!0})}function s(t){var e=t.split("T");return e[0]+" "+e[1]}function a(){var t=new Date,e="-",r=":",n=t.getFullYear(),u=t.getMonth()+1,o=t.getDate(),s=t.getHours(),a=t.getMinutes(),c=t.getSeconds(),i=["星期一","星期二","星期三","星期四","星期五","星期六","星期天"];i[t.getDay()];u>=1&&u<=9&&(u="0"+u),o>=0&&o<=9&&(o="0"+o),s>=0&&s<=9&&(s="0"+s),a>=0&&a<=9&&(a="0"+a),c>=0&&c<=9&&(c="0"+c);var d=n+e+u+e+o+" "+s+r+a+r+c;return d}function c(t){var e=t.split("-"),r=e[0],n=e[1],u=e[2],o=new Date(r,n,0);o=o.getDate();var s=r,a=parseInt(n)+1;13==a&&(s=parseInt(s)+1,a=1);var c=u,i=new Date(s,a,0);i=i.getDate(),c>i&&(c=i),a<10&&(a="0"+a);var d=s+"-"+a+"-"+c;return d}function i(){for(var t="",e=4,r=new Array(0,1,2,3,4,5,6,7,8,9),n=0;n<e;n++){var u=Math.floor(9*Math.random());t+=r[u]}return t}function d(){var t=arguments.length>0&&void 0!==arguments[0]?arguments[0]:"确定执行此操作吗？",e=arguments.length>1&&void 0!==arguments[1]?arguments[1]:"warning";return n["MessageBox"].prompt(t,"提示",{confirmButtonText:"确定",cancelButtonText:"取消",inputPlaceholder:"请输入code",inputPattern:/^[0-9]\d*$/,inputErrorMessage:"code格式不正确",type:e})}}}]);