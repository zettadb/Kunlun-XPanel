# Kunlun-XPanel
The fantastic tools used to manager 
## 说明
开发环境 Windows 10 nodejs 14.14.0 Apache 2.4.48 x64 PHP Version 7.3.30

## 技术栈
vue2 + vuex + vue-router + webpack + ES6/7 + less + element-ui+PHP

## 运行前准备
由于此项目是基于nodejs+php的前后端结合项目，你需要进行nodejs的相关准备工作。项目运行之前，请确保系统已经安装以下应用：  
(1)、node (6.0 及以上版本)。使用细节，请参考：[node的下载及安装](https://nodejs.org/en/download/)。  
(2)、Apache。使用细节，请参考：[apache的下载及安装](https://httpd.apache.org/download)。  
(3)、PHP。使用细节，请参考：[PHP的下载及安装](https://www.php.net/downloads.php)。  
(4)、安装PHP的mysql和pgsql扩展。请参考：[PHP官方的扩展库](http://pecl.php.net/)。  

## 项目运行
#克隆到本地  
git clone https://github.com/zettadb/Kunlun-XPanel.git  
#进入文件夹  
cd Kunlun-XPanel/KunlunXPanel  
#安装依赖  
npm install  
#修改ip，port  
vi Kunlun-XPanel/KunlunXPanel/.env.production  
VUE_APP_BASE_API为apache的环境路径  
VUE_APP_INTERFACE_API为cluste_mgr路径  
#开启本地服务器http://localhost:9528  
npm run dev   
#发布生产环境  
npm run build:prod  
#生产环境部署  
把monitor和dist文件放到apache的html路径下  
mv dist KunlunXPanel  
http://ip:port/KunlunXPanel访问即可  
