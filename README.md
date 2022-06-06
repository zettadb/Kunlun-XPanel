# Kunlun-XPanel
The fantastic tools used to manager 
# docker安装  
docker pull registry.cn-hangzhou.aliyuncs.com/kunlundb/xpanel  
docker load --input web20220107.tar  
git clone -b 0.9.2.1 https://github.com/zettadb/Kunlun-XPanel.git    
unzip platform  
cd platform  
docker run -id --name xpanel -v  $(PWD):/var/www/html -p 8081:80  -d web:v20220107  
http://ip:8081/KunlunXPanel  
