# docker安装  
选择一台机器安装XPanel，并确定给XPanel使用的端口， 本处假设该机器为192.168.0.113，给XPanel的端口为17000:  
sudo docker run --pull always -itd --name xpanel1 -p 17000:80 \  
  registry.cn-hangzhou.aliyuncs.com/kunlundb/kunlun-xpanel bash -c '/bin/bash /kunlun/start.sh'  
一分钟后，使用浏览器打开XPanel网站: ​http://192.168.0.113:17000/KunlunXPanel  

#grafana  
(1).初始账号和密码均是admin    
(2).绑定cluster_mgr数据源：  
页面上左边框找到设置-》Data sources-》搜Prometheus，然后点击-》url改成自己的cluster_mgr上的prometheus地址（例如：http://127.0.0.1:1000）-》Save&test保存即可。    
(3).导入json模板  
在文件的json目录下有三个json模板分别导入到grafana中。进入到grafana下，点+号-》点import-》Upload Json file-》import即可   
（json模板下载：git clone -b 0.9.3.3 https://github.com/zettadb/Kunlun-XPanel.git）
