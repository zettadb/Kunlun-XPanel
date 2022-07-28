# docker安装  
(1).在可使用docker的linux机器（比如机器ip为：192.168.0.111）上执行如下语句：   
sudo docker run --pull always -itd --name xpanel1 -p 17000:80 \    
  registry.cn-hangzhou.aliyuncs.com/kunlundb/kunlun-xpanel bash -c '/bin/bash /kunlun/start.sh'    
(2).使用谷歌浏览器打开XPanel网站: http://192.168.0.111:17000/KunlunXPanel     
(3).XPanel的初始账号密码均为：super_dba

#grafana  
(1).初始账号和密码均是admin    
(2).绑定cluster_mgr数据源：  
页面上左边框找到设置-》Data sources-》搜Prometheus，然后点击-》url改成自己的cluster_mgr上的prometheus地址（例如：http://127.0.0.1:1000）-》Save&test保存即可。    
(3).导入json模板  
在文件的json目录下有三个json模板分别导入到grafana中。进入到grafana下，点+号-》点import-》Upload Json file-》import即可   
（json模板，从github上获取，执行语句：git clone -b 0.9.3.3 https://github.com/zettadb/Kunlun-XPanel.git）
