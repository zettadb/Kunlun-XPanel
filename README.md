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
git clone -b 0.9.2 https://github.com/zettadb/Kunlun-XPanel.git  
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
把monitor、dist(dist文件在Kunlun-XPanel/KunlunXPanel目录下)和index.php文件都放到apache的/var/www/html/路径下  
mv dist KunlunXPanel  
http://ip:port/KunlunXPanel访问即可  
默认账号密码均为super_dba 


# docker安装  
docker pull registry.cn-hangzhou.aliyuncs.com/kunlundb/xpanel   
git clone -b 0.9.2.1 https://github.com/zettadb/Kunlun-XPanel.git  
unzip platform  
cd Kunlun-XPanel/platform/KunlunXPanel目录下找到config.js文件，修改配置文件config.js为相应的环境地址  
（注意：BASE_URL：放xpanel的ip和docker映射的端口；API_URL：元数据集群的ip和端口）   
cd platform    
docker run -id --name xpanel -v $(PWD):/var/www/html -p 8081:80 -d registry.cn-hangzhou.aliyuncs.com/kunlundb/xpanel    
http://ip:8081/KunlunXPanel 

（注意：  
1.遇到404报错时，登录元数据查看是否有dba_tools_db数据库，没有则导入该数据库：https://github.com/zettadb/Kunlun-XPanel/blob/0.9.2/dba_tools_db.sql  
2.遇到Permission denied的问题：   
(1).docker exec -it 容器id /bin/bash   
(2).cd /var/www/html/monitor/application/config   
(3).chmod 777 database.php )  
