# Kunlun-XPanel
The fantastic tools used to manager 
# docker安装  
docker pull registry.cn-hangzhou.aliyuncs.com/kunlundb/xpanel  
git clone -b 0.9.3.1 https://github.com/zettadb/Kunlun-XPanel.git     
unzip platform   
cd Kunlun-XPanel/platform/KunlunXPanel目录下找到config.js文件，修改配置文件config.js为相应的环境地址   
（注意：BASE_URL：放xpanel的ip和docker映射的端口）  
cd platform  
docker run -id --name xpanel -v  $PWD:/var/www/html -p 8081:80  -d registry.cn-hangzhou.aliyuncs.com/kunlundb/xpanel   
http://ip:8081/KunlunXPanel  

（注意：  
1.遇到404报错时，登录元数据查看是否有dba_tools_db数据库，没有则导入该数据库：https://github.com/zettadb/Kunlun-XPanel/blob/0.9.2/dba_tools_db.sql  
2.遇到Permission denied的问题：   
(1).docker exec -it 容器id /bin/bash   
(2).cd /var/www/html/monitor/application/config   
(3).chmod 777 database.php和chmod 777 myconfig.php)  

#安装grafana  
(1).docker pull grafana/grafana  
(2).docker run -d -p 3000:3000 --name=grafana  grafana/grafana  
(3).初始账号和密码均是admin  
(4).grafana设置免密登录：  
docker exec -it grafana的镜像id /bin/bash  
vi /etc/grafana/grafaba.ini   
找到 [auth.anonymous],enabled 设置为 true   
(5).绑定cluster_mgr数据源：  
页面上左边框找到设置-》Data sources-》搜Prometheus，然后点击-》url改成自己的cluster_mgr上的prometheus地址即可。  
