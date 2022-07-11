import { MessageBox, Message } from 'element-ui'
/**
 * Created by PanJiaChen on 16/11/18.
 */

/**
 * Parse the time to string
 * @param {(Object|string|number)} time
 * @param {string} cFormat
 * @returns {string | null}
 */
export function parseTime(time, cFormat) {
  if (arguments.length === 0 || !time) {
    return null
  }
  const format = cFormat || '{y}-{m}-{d} {h}:{i}:{s}'
  let date
  if (typeof time === 'object') {
    date = time
  } else {
    if ((typeof time === 'string')) {
      if ((/^[0-9]+$/.test(time))) {
        // support "1548221490638"
        time = parseInt(time)
      } else {
        // support safari
        // https://stackoverflow.com/questions/4310953/invalid-date-in-safari
        time = time.replace(new RegExp(/-/gm), '/')
      }
    }

    if ((typeof time === 'number') && (time.toString().length === 10)) {
      time = time * 1000
    }
    date = new Date(time)
  }
  const formatObj = {
    y: date.getFullYear(),
    m: date.getMonth() + 1,
    d: date.getDate(),
    h: date.getHours(),
    i: date.getMinutes(),
    s: date.getSeconds(),
    a: date.getDay()
  }
  const time_str = format.replace(/{([ymdhisa])+}/g, (result, key) => {
    const value = formatObj[key]
    // Note: getDay() returns 0 on Sunday
    if (key === 'a') { return ['日', '一', '二', '三', '四', '五', '六'][value ] }
    return value.toString().padStart(2, '0')
  })
  return time_str
}

/**
 * @param {number} time
 * @param {string} option
 * @returns {string}
 */
export function formatTime(time, option) {
  if (('' + time).length === 10) {
    time = parseInt(time) * 1000
  } else {
    time = +time
  }
  const d = new Date(time)
  const now = Date.now()

  const diff = (now - d) / 1000

  if (diff < 30) {
    return '刚刚'
  } else if (diff < 3600) {
    // less 1 hour
    return Math.ceil(diff / 60) + '分钟前'
  } else if (diff < 3600 * 24) {
    return Math.ceil(diff / 3600) + '小时前'
  } else if (diff < 3600 * 24 * 2) {
    return '1天前'
  }
  if (option) {
    return parseTime(time, option)
  } else {
    return (
      d.getMonth() +
      1 +
      '月' +
      d.getDate() +
      '日' +
      d.getHours() +
      '时' +
      d.getMinutes() +
      '分'
    )
  }
}

/**
 * @param {string} url
 * @returns {Object}
 */
export function param2Obj(url) {
  const search = decodeURIComponent(url.split('?')[1]).replace(/\+/g, ' ')
  if (!search) {
    return {}
  }
  const obj = {}
  const searchArr = search.split('&')
  searchArr.forEach(v => {
    const index = v.indexOf('=')
    if (index !== -1) {
      const name = v.substring(0, index)
      const val = v.substring(index + 1, v.length)
      obj[name] = val
    }
  })
  return obj
}

export function messageTip(tips, type) {
  return Message({
    message: tips,
    type: type,
    duration: 2*1000
  })
}

export function handleCofirm(text='确定执行此操作吗？',type='warning'){
  return MessageBox.confirm(text,'提示',{
    confirmButtonText: '确定',
    cancelButtonText: '取消',
    type: type,
    center: true
  })
}
export function getUrlVars() {
  var vars = {};
  var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi,
      function (m, key, value) {
          vars[key] = value;
      }
  );
  return vars;
}
export function isJSON(str) {
  if (typeof str == 'string') {
      try {
          var obj=JSON.parse(str);
          if(typeof obj == 'object' && obj ){
              return true;
          }else{
              return false;
          }

      } catch(e) {
          console.log('error：'+str+'!!!'+e);
          return false;
      }
  }
}
//格式化后台传回的time格式为日期格式2020-08-19T01:59:32.000+0000->2020-08-19
export function formatDataTime(time){
  let arr = time.split('T');
  return arr[0];
}

export function formatDataTimeTotime(time){
  let arr = time.split('T');
  return arr[0]+' '+arr[1];
}
//获取当前时间,得到2017-03-29 11:41:10
export function getNowDate() {
  var date = new Date();
  var sign1 = "-";
  var sign2 = ":";
  var year = date.getFullYear() // 年
  var month = date.getMonth() + 1; // 月
  var day  = date.getDate(); // 日
  var hour = date.getHours(); // 时
  var minutes = date.getMinutes(); // 分
  var seconds = date.getSeconds() //秒
  var weekArr = ['星期一', '星期二', '星期三', '星期四', '星期五', '星期六', '星期天'];
  var week = weekArr[date.getDay()];
  // 给一位数数据前面加 “0”
  if (month >= 1 && month <= 9) {
    month = "0" + month;
  }
  if (day >= 0 && day <= 9) {
    day = "0" + day;
  }
  if (hour >= 0 && hour <= 9) {
    hour = "0" + hour;
  }
  if (minutes >= 0 && minutes <= 9) {
    minutes = "0" + minutes;
  }
  if (seconds >= 0 && seconds <= 9) {
    seconds = "0" + seconds;
  }
  var currentdate = year + sign1 + month + sign1 + day + " " + hour + sign2 + minutes + sign2 + seconds;
  return currentdate;
 }
//  获取下一个月          
export function getNextMonth(date) {  
   var arr = date.split('-');  
   var year = arr[0]; //获取当前日期的年份  
   var month = arr[1]; //获取当前日期的月份  
   var day = arr[2]; //获取当前日期的日  
   var days = new Date(year, month, 0);  
   days = days.getDate(); //获取当前日期中的月的天数  
   var year2 = year;  
   var month2 = parseInt(month) + 1;  
   if (month2 == 13) {  
       year2 = parseInt(year2) + 1;  
       month2 = 1;  
   }  
   var day2 = day;  
   var days2 = new Date(year2, month2, 0);  
   days2 = days2.getDate();  
   if (day2 > days2) {  
       day2 = days2;  
   }  
   if (month2 < 10) {  
       month2 = '0' + month2;  
   }  
   var t2 = year2 + '-' + month2 + '-' + day2;  
   return t2;  
}  
//获取4位随机数
export function createCode(){
  var code = '';
  var codeLength = 4;
  var random = new Array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9);
  for (var i = 0; i < codeLength; i++) {
    var index = Math.floor(Math.random() * 9);
    code += random[index];
  }
  return code;
}
export function gotoCofirm(text='确定执行此操作吗？',type='warning'){
  return MessageBox.prompt(text,'提示',{
    confirmButtonText: '确定',
    cancelButtonText: '取消',
    inputPlaceholder:'请输入code',
    inputPattern: /^[0-9]\d*$/,
    inputErrorMessage: 'code格式不正确',
    type: type,
    // center: true
  })
}