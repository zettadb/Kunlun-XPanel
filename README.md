# Kunlun-XPanel
The fantastic tools used to manager 
# docker安装  
docker pull registry.cn-hangzhou.aliyuncs.com/kunlundb/xpanel  
docker load --input web20220107.tar  
git clone -b 0.9.2.1 https://github.com/zettadb/Kunlun-XPanel.git    
unzip platform  
cd Kunlun-XPanel/platform/KunlunXPanel目录下找到config.js文件，修改配置文件config.js为相应的环境地址
（注意：BASE_URL：放xpanel的ip和docker映射的端口；API_URL：元数据集群的ip和端口）
cd platform 
docker run -id --name xpanel -v  $(PWD):/var/www/html -p 8081:80  -d web:v20220107  
http://ip:8081/KunlunXPanel  
