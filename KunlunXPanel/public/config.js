const config = {
    BASE_URL: "http://192.168.0.132:8081", //接口地址
    //API_URL: "http://192.168.0.127:55000",
  };
  sessionStorage.setItem('response', JSON.stringify(config));
// let nowTimes = new Date().getTime();
// module.exports = defineConfig({
// configureWebpack: { 
// 		output: { // 输出 添加时间戳到打包编译后的js文件名称
// 			filename: `static/js/js[name].${timeStamp}.js`,
// 			chunkFilename: `static/js/chunk.[id].${timeStamp}.js`,
// 		}
// 	},
// 	css: {
// 		extract: { // 添加时间戳到打包后css文件名称
// 			filename: `static/css/[name].${timeStamp}.css`,
// 			chunkFilename: `static/css/chunk.[id].${timeStamp}.css`,
// 		}
// 	},
// })