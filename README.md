# docker安装  
(1).在可使用docker的linux机器（比如机器ip为：192.168.0.111）上执行如下语句：   
sudo docker run --pull always -itd --name xpanel1 -p 17000:80 \    
  registry.cn-hangzhou.aliyuncs.com/kunlundb/kunlun-xpanel bash -c '/bin/bash /kunlun/start.sh'    
(2).使用谷歌浏览器打开XPanel网站: http://192.168.0.111:17000/KunlunXPanel     
(3).XPanel的初始账号密码均为：super_dba

#grafana  
(1).初始账号和密码均是admin  
