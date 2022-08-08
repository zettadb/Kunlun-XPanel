# Kunlun-XPanel
The fantastic tools used to manager  
## 说明
开发环境 Windows 10 nodejs 14.14.0 Apache 2.4.48 x64 PHP Version 7.3.30

## 技术栈
vue2 + vuex + vue-router + webpack + ES6/7 + less + element-ui+PHP

## 运行前准备
由于此项目是基于nodejs+php的前后端结合项目，你需要进行nodejs的相关准备工作。项目运行之前，请确保系统已经安装以下应用：  
(1)、node (6.0 及以上版本)。使用细节，请参考：[node的下载及安装](https://nodejs.org/en/download/)。  
(2)、Apache 。使用细节，请参考：[apache的下载及安装](https://httpd.apache.org/download)。  
(3)、PHP(7.3及以上版本)。使用细节，请参考：[PHP的下载及安装](https://www.php.net/downloads.php)。  
(4)、安装PHP的mysql和pgsql扩展。请参考：[PHP官方的扩展库](http://pecl.php.net/)。  
注意：安装完成PHP后，记得重启Apache。

## 项目运行
#克隆到本地   
git clone -b 0.9.3 https://github.com/zettadb/Kunlun-XPanel.git  
#进入文件夹  
cd Kunlun-XPanel/KunlunXPanel
#安装依赖  
npm install  
#修改成自己的ip，port  
生产环境：vi Kunlun-XPanel/KunlunXPanel/.env.production  
开发环境：vi Kunlun-XPanel/KunlunXPanel/.env.development  
VUE_APP_BASE_API为apache的环境路径  
VUE_APP_INTERFACE_API为cluste_mgr路径  
#开启本地服务器http://localhost:9528  
npm run dev   
#发布生产环境  
npm run build:prod  
#生产环境部署  
把KunlunMonitor、dist(dist文件在Kunlun-XPanel/KunlunXPanel目录下)和index.php文件都放到apache的/var/www/html/路径下  
mv dist KunlunXPanel  
http://ip:port/KunlunXPanel访问即可  
默认账号密码均为super_dba 

#安装grafana  
(1).docker pull grafana/grafana  
(2).docker run -d -p 3000:3000 --name=grafana  grafana/grafana  
(3).初始账号和密码均是admin    
(4).绑定cluster_mgr数据源：  
页面上左边框找到设置-》Data sources-》搜Prometheus，然后点击-》url改成自己的cluster_mgr上的prometheus地址（例如：http://127.0.0.1:1000）-》Save&test保存即可。     
(5).导入json模板  
在文件的json目录下有三个json模板分别导入到grafana中。进入到grafana下，点+号-》点import-》Upload Json file-》import即可   
（json模板，从github上获取，执行语句：git clone -b 0.9.3.3 https://github.com/zettadb/Kunlun-XPanel.git）


# docker安装  
(1).sudo docker run --pull always -itd --name xpanel1 -p 17000:80 \
registry.cn-hangzhou.aliyuncs.com/kunlundb/kunlun-xpanel bash -c '/bin/bash /kunlun/start.sh'
(2).使用谷歌浏览器打开XPanel网站: http://192.168.0.111:17000/KunlunXPanel
(3).XPanel的初始账号密码均为：super_dba

 


