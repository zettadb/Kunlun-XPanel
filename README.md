# Kunlun-XPanel
The fantastic tools used to manager 
# docker安装  
docker pull registry.cn-hangzhou.aliyuncs.com/kunlundb/xpanel  
git clone -b 0.9.2.1 https://github.com/zettadb/Kunlun-XPanel.git     
unzip platform   
cd Kunlun-XPanel/platform/KunlunXPanel目录下找到config.js文件，修改配置文件config.js为相应的环境地址   
（注意：BASE_URL：放xpanel的ip和docker映射的端口；API_URL：元数据集群的ip和端口）  
cd platform  
docker run -id --name xpanel -v  $(PWD):/var/www/html -p 8081:80  -d registry.cn-hangzhou.aliyuncs.com/kunlundb/xpanel   
http://ip:8081/KunlunXPanel  

（注意：
1.遇到404报错时，登录元数据查看是否有dba_tools_db数据库，没有则导入该数据库：https://github.com/zettadb/Kunlun-XPanel/blob/0.9.2/dba_tools_db.sql
2.遇到Permission denied的问题：   
(1).docker exec -it 容器id /bin/bash   
(2).cd /var/www/html/monitor/application/config   
(3).chmod 777 database.php  )  
