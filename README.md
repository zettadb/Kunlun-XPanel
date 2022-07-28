# Kunlun-XPanel
The fantastic tools used to manager 
# docker安装  
docker pull registry.cn-hangzhou.aliyuncs.com/kunlundb/xpanel  
git clone -b 0.9.3.2 https://github.com/zettadb/Kunlun-XPanel.git     
unzip platform   
cd platform  
docker run -id --name xpanel -v  $PWD:/var/www/html -p 8081:80  -d registry.cn-hangzhou.aliyuncs.com/kunlundb/xpanel   
http://ip:8081/KunlunXPanel  

（注意：  
1.遇到Permission denied的问题：   
(1).docker exec -it 容器id /bin/bash   
(2).cd /var/www/html/KunlunMonitor/application/config   
(3).chmod 777 database.php和chmod 777 myconfig.php)  

#安装grafana  
(1).docker pull grafana/grafana  
(2).docker run -d -p 3000:3000 --name=grafana  grafana/grafana  
(3).初始账号和密码均是admin    
(4).绑定cluster_mgr数据源：  
页面上左边框找到设置-》Data sources-》搜Prometheus，然后点击-》url改成自己的cluster_mgr上的prometheus地址（例如：http://127.0.0.1:1000）-》Save&test保存即可。    
(5).导入json模板  
在文件的json目录下有三个json模板分别导入到grafana中。进入到grafana下，点+号-》点import-》Upload Json file-》import即可     
