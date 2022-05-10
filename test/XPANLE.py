# 测试XPanel
# 导入需要的包
import unittest
from selenium import webdriver
from selenium.webdriver.common.action_chains import ActionChains
from selenium.webdriver.common.keys import Keys
from selenium.webdriver.common.by import By
from selenium.webdriver.support.select import Select
import time

# 打开浏览器进入网页
driver = webdriver.Chrome()
driver.get("http://192.168.0.126/KunlunXPanel/")
# 最大化窗口
driver.maximize_window()
# 隐式等待
driver.implicitly_wait(20)

# 进行登录 会多次使用进行def进行封装 账户默认super_dba,密码默认:Aa_11111

def login (user,pwd):
    driver.find_element(By.NAME, 'username').send_keys(user)
    driver.find_element(By.NAME, 'password').send_keys(pwd)
    driver.find_element(By.XPATH, '//*[@id="app"]/div/div[3]/form/div[4]/div/div/div/input').click()
    driver.find_element(By.XPATH, "//li/span").click()
    driver.find_element(By.XPATH, '//*[@id="app"]/div/div[3]/form/button').click()

login(user='super_dba',pwd='12345678')



# 新增用户
def new_users(用户账号,登录密码,重复密码,手机号码,邮箱地址,微信号):
    # 进入首页点击系统管理,点击用户管理,点击新建用户,创建创建用户test1,密码Aa_11111,电话13929558834,邮箱
    # 先是点击的系统管理
    driver.find_element(By.XPATH, '//*[@id="app"]/div/div[1]/div/div[1]/div/ul/div[2]/li/div').click()
    # 点击用户管理
    driver.find_element(By.XPATH, '//*[@id="app"]/div/div[1]/div/div[1]/div/ul/div[2]/li/ul/div[1]').click()
    # 进入用户管理的页面后点击新增用户
    driver.find_element(By.XPATH, '//*[@id="app"]/div/div[2]/section/div/div[1]/div/button[3]').click()
    driver.find_element(By.XPATH, '//*[@id="app"]/div/div[2]/section/div/div[4]/div/div[2]/form/div[1]/div/div/input').send_keys(用户账号)
    driver.find_element(By.XPATH, '//*[@id="app"]/div/div[2]/section/div/div[4]/div/div[2]/form/div[2]/div/div/input').send_keys(登录密码)
    driver.find_element(By.XPATH, '//*[@id="app"]/div/div[2]/section/div/div[4]/div/div[2]/form/div[3]/div/div[1]/input').send_keys(重复密码)
    driver.find_element(By.XPATH, '//*[@id="app"]/div/div[2]/section/div/div[4]/div/div[2]/form/div[4]/div/div[1]/input').send_keys(手机号码)
    driver.find_element(By.XPATH, '//*[@id="app"]/div/div[2]/section/div/div[4]/div/div[2]/form/div[5]/div/div[1]/input').send_keys(邮箱地址)
    driver.find_element(By.XPATH, '//*[@id="app"]/div/div[2]/section/div/div[4]/div/div[2]/form/div[6]/div/div/input').send_keys(微信号)

new_users(用户账号='test1',登录密码='Aa_11111',重复密码='Aa_11111',手机号码='123929558834',邮箱地址='123@qq.com',微信号='')
time.sleep(2)
driver.find_element(By.XPATH, '//*[@id="app"]/div/div[2]/section/div/div[4]/div/div[3]/div/button[2]').click()

# 增加角色,名称a 拥有集群权限和计算机权限

# 点击角色管理
driver.find_element(By.XPATH, "//span[contains(.,'角色管理')]").click()

# 点击进入建立新的角色a ,权限包括集群权限,和计算机权限
time.sleep(2)
# 点击建立角色
def new_role(role):
    driver.find_element(By.XPATH, '//*[@id="app"]/div/div[2]/section/div/div[1]/div/button/i').click()
    driver.find_element(By.XPATH, '//*[@id="app"]/div/div[2]/section/div/div[4]/div/div[2]/form/div[1]/div/div[1]/input').send_keys(role)
    driver.find_element(By.XPATH, '//*[@id="app"]/div/div[2]/section/div/div[4]/div/div[2]/form/div[2]/div/div/div[1]/div/label/span/span').click()
    driver.find_element(By.XPATH, '//*[@id="app"]/div/div[2]/section/div/div[4]/div/div[2]/form/div[2]/div/div/div[2]/div/label/span/span').click()
    driver.find_element(By.XPATH, '//*[@id="app"]/div/div[2]/section/div/div[4]/div/div[3]/div/button[2]').click()
new_role(role='a')
# 为账户test1添加角色
# 点击授权管理
driver.find_element(By.XPATH, '//*[@id="app"]/div/div[1]/div/div[1]/div/ul/div[2]/li/ul/div[3]/a').click()
time.sleep(1)

# 点击新增授权
driver.find_element(By.XPATH, "//span[contains(.,'新增')]").click()
time.sleep(1)
# 选择账户
def accredit():
    # 点击用户账户
    driver.find_element(By.XPATH,
                        '//*[@id="app"]/div/div[2]/section/div/div[4]/div/div[2]/form/div[1]/div/div/div[1]/input').click()
    # 选择需要的账户 后期测试把test1设置成全局变量(所有测试中需要保证唯一性)
    driver.find_element(By.XPATH, "//li[contains(.,'test1')]").click()
    #  点击角色名称
    driver.find_element(By.XPATH,
                        '//*[@id="app"]/div/div[2]/section/div/div[4]/div/div[2]/form/div[2]/div/div/div[1]/input').click()
    # 选择角色
    driver.find_element(By.XPATH, "//div[4]/div/div/ul/li[2]").click()
    # driver.find_element(By.XPATH, "//span[contains(.,'角色名称')]").click()
    # 点击有效的时间类型 ,类型
    driver.find_element(By.XPATH, "(//input[@type='text'])[4]").click()
    # 选择永久或者时间段
    driver.find_element(By.XPATH, "//span[contains(.,'永久')]").click()
    # driver.find_element(By.XPATH, "//span[contains(.,\'时间段\')]").click()
    time.sleep(1)
    # 选择应用使用与所有的集群
    driver.find_element(By.XPATH,
                        '//*[@id="app"]/div/div[2]/section/div/div[4]/div/div[2]/form/div[4]/div/label[1]/span[1]').click()
    driver.find_element(By.CSS_SELECTOR, ".el-radio:nth-child(1) .el-radio__inner").click()
    # 点击确定
    driver.find_element(By.XPATH, "//span[contains(.,'确认')]").click()
accredit()
# 退出登录
driver.find_element(By.XPATH, '//*[@id="app"]/div/div[2]/div/div/div[3]/a[3]').click()

# 进行test1的登录
login(user='test1',pwd='Aa_11111')
# 需要断言 当前账户:test1 之后退出登录
driver.find_element(By.XPATH, '//*[@id="app"]/div/div[2]/div/div/div[3]/a[3]').click()
# 进入到主账号进行删除test1的信息
login(user='super_dba',pwd='12345678')

# 先是点击的系统管理
driver.find_element(By.XPATH, '//*[@id="app"]/div/div[1]/div/div[1]/div/ul/div[2]/li/div').click()
# 点击用户管理
driver.find_element(By.XPATH, '//*[@id="app"]/div/div[1]/div/div[1]/div/ul/div[2]/li/ul/div[1]').click()
# 删除账号
time.sleep(2)
driver.find_element(By.XPATH, '//*[@id="app"]/div/div[2]/section/div/div[2]/div[3]/table/tbody/tr[1]/td[7]/div/button[2]').click()
driver.find_element(By.XPATH, '/html/body/div[2]/div/div[3]/button[2]').click()
# 点击角色管理
driver.find_element(By.XPATH, '//*[@id="app"]/div/div[1]/div/div[1]/div/ul/div[2]/li/ul/div[2]/a').click()
time.sleep(1)
# 删除角色
driver.find_element(By.XPATH, '//*[@id="app"]/div/div[2]/section/div/div[2]/div[3]/table/tbody/tr[1]/td[5]/div/button[2]').click()
time.sleep(1)
driver.find_element(By.XPATH, '/html/body/div[2]/div/div[3]/button[2]').click()

# 点击计算机管理,点击计算机列表
time.sleep(2)
driver.find_element(By.XPATH, '//*[@id="app"]/div/div[1]/div/div[1]/div/ul/div[3]/li/div').click()
driver.find_element(By.XPATH, '//*[@id="app"]/div/div[1]/div/div[1]/div/ul/div[3]/li/ul/div[1]/a/li').click()
# 点击新增计算机
def new_computers(ip地址,机架编号,数据目录,日志目录,wal日志目录,计算节点数据目录,总内存,cpu核数):
    # 点击新增计算机
    driver.find_element(By.XPATH, '//*[@id="app"]/div/div[2]/section/div/div[1]/div[1]/button[3]').click()
    # ip地址
    driver.find_element(By.XPATH, '//*[@id="app"]/div/div[2]/section/div/div[4]/div/div[2]/form/div[1]/div/div/input').send_keys(ip地址)
    # 机架编号
    driver.find_element(By.XPATH, '//*[@id="app"]/div/div[2]/section/div/div[4]/div/div[2]/form/div[2]/div/div/input').send_keys(机架编号)
    # 数据目录
    driver.find_element(By.XPATH, '//*[@id="app"]/div/div[2]/section/div/div[4]/div/div[2]/form/div[3]/div/div/input').send_keys(数据目录)
    # 日志目录
    driver.find_element(By.XPATH, '//*[@id="app"]/div/div[2]/section/div/div[4]/div/div[2]/form/div[4]/div/div/input').send_keys(日志目录)
    # wal日志目录
    driver.find_element(By.XPATH, '//*[@id="app"]/div/div[2]/section/div/div[4]/div/div[2]/form/div[5]/div/div/input').send_keys(wal日志目录)
    # 计算节点数据目录
    driver.find_element(By.XPATH, '//*[@id="app"]/div/div[2]/section/div/div[4]/div/div[2]/form/div[6]/div/div/input').send_keys(计算节点数据目录)
    # 总内存
    driver.find_element(By.XPATH, '//*[@id="app"]/div/div[2]/section/div/div[4]/div/div[2]/form/div[7]/div/div/input').send_keys(总内存)
    # cpu核数
    driver.find_element(By.XPATH, '//*[@id="app"]/div/div[2]/section/div/div[4]/div/div[2]/form/div[8]/div/div/input').send_keys(cpu核数)
    # 点击确定
    driver.find_element(By.XPATH, '//*[@id="app"]/div/div[2]/section/div/div[4]/div/div[3]/div/button[2]').click()

new_computers(ip地址='172.17.0.4',
              机架编号='1',
              数据目录='/home/kunlun;',
              日志目录='/home/kunlun',
              wal日志目录='/home/kunlun',
              计算节点数据目录='/home/kunlun;;;;',
              总内存='1024',
              cpu核数='8')


# def new_cluster(shard个数,
#                 副本数,
#                 集群名称,
#                 缓冲池大小,
#                 每个计算节点最大连接数,
#                 每个计算节点的cpu核数,
#                 每个计算节点最大的存储值,
#                 每个存储节点的cpu核数,
#                 每个存储节点innodb缓冲池大小,
#                 每个存储节点rocksdb缓冲池大小,
#                 每个存储节点初始化存储值,
#                 每个存储节点最大存储值):
#     # 点击新增集群
#     driver.find_element(By.XPATH, '//*[@id="pane-second"]/div/div[1]/div/button[3]').click()
#     time.sleep(2)
#     # 选择计算机
#     driver.find_element(By.XPATH, '//*[@id="pane-second"]/div/div[4]/div/div[2]/form/div[1]/div/div/label[1]/span[1]/span').click()
#     driver.find_element(By.XPATH, '//*[@id="pane-1"]/div/div[4]/div/div[2]/form/div[1]/div/div/label[1]/span[1]/span').click()
#     driver.find_element(By.XPATH, '//*[@id="pane-1"]/div/div[4]/div/div[2]/form/div[1]/div/div/label[3]/span[1]/span').click()
#     # 高可用模式
#     driver.find_element(By.XPATH, '//*[@id="pane-1"]/div/div[4]/div/div[2]/form/div[2]/div/div/div/input').click()
#     driver.find_element(By.XPATH, '/html/body/div[3]/div[1]/div[1]/ul/li').click()
#     # driver.find_element_by_class_name('el-select-dropdown__item hover').cllck()
#     # shard个数
#     n = driver.find_element(By.XPATH, '//*[@id="pane-1"]/div/div[4]/div/div[2]/form/div[3]/div/div/div/input')
#     n.clear()
#     n.send_keys(shard个数)
#     # 副本数  副本数至少是3，且不能大于所选计算机数
#     c = driver.find_element(By.XPATH, '//*[@id="pane-1"]/div/div[4]/div/div[2]/form/div[4]/div/div/input')
#     c.clear()
#     c.send_keys(副本数)
#     #  集群名称
#     driver.find_element(By.XPATH, '//*[@id="pane-1"]/div/div[4]/div/div[2]/form/div[5]/div/div/input').send_keys(集群名称)
#     # 缓冲池大小
#     a = driver.find_element(By.XPATH, '//*[@id="pane-1"]/div/div[4]/div/div[2]/form/div[6]/div/div/input')
#     a.clear()
#     a.send_keys(缓冲池大小)
#     # 打开下拉框
#     driver.find_element(By.XPATH, '//*[@id="pane-1"]/div/div[4]/div/div[2]/form/div[8]/i').click()
#     # 向下滑
#     # 每个计算节点最大连接数
#     driver.find_element(By.XPATH, '//*[@id="pane-1"]/div/div[4]/div/div[2]/form/div[7]/div[1]/div/div/input').send_keys(每个计算节点最大连接数)
#     # 每个计算节点的cpu核数
#     driver.find_element(By.XPATH, '//*[@id="pane-1"]/div/div[4]/div/div[2]/form/div[7]/div[2]/div/div/input').send_keys(每个计算节点的cpu核数)
#     # 每个计算节点最大的存储值
#     driver.find_element(By.XPATH, '//*[@id="pane-1"]/div/div[4]/div/div[2]/form/div[7]/div[3]/div/div/input').send_keys(每个计算节点最大的存储值)
#     # 每个存储节点的cpu核数
#     driver.find_element(By.XPATH, '//*[@id="pane-1"]/div/div[4]/div/div[2]/form/div[7]/div[4]/div/div/input').send_keys(每个存储节点的cpu核数)
#     # 每个存储节点innodb缓冲池大
#     # driver.find_element(By.XPATH, '//*[@id="pane-1"]/div/div[4]/div/div[2]/form/div[7]/div[5]/div/div/input').send_keys(每个存储节点innodb缓冲池大)
#
#     # 每个存储节点rocksdb缓冲池大小
#     driver.find_element(By.XPATH, '//*[@id="pane-1"]/div/div[4]/div/div[2]/form/div[7]/div[6]/div/div/input').send_keys(每个存储节点rocksdb缓冲池大小)
#     # 每个存储节点初始化存储值
#     driver.find_element(By.XPATH, '//*[@id="pane-1"]/div/div[4]/div/div[2]/form/div[7]/div[7]/div/div/input').send_keys(每个存储节点初始化存储值)
#     # 每个存储节点最大存储值
#     driver.find_element(By.XPATH, '//*[@id="pane-1"]/div/div[4]/div/div[2]/form/div[7]/div[8]/div/div/input').send_keys(每个存储节点最大存储值)
#     driver.find_element(By.XPATH, '//*[@id="pane-1"]/div/div[4]/div/div[3]/div/button[2]').send_keys()
#     driver.find_element(By.XPATH, '//*[@id="pane-1"]/div/div[4]/div/div[3]/div/button[2]').click()

# new_cluster(    集群名称='vito',
#                 副本数=3,
#                 shard个数=1,
#                 缓冲池大小=1,
#                 每个计算节点最大连接数='',
#                 每个计算节点的cpu核数='',
#                 每个计算节点最大的存储值='',
#                 每个存储节点的cpu核数='',
#                 每个存储节点innodb缓冲池大小='',
#                 每个存储节点rocksdb缓冲池大小='',
#                 每个存储节点初始化存储值='',
#                 每个存储节点最大存储值='')

def new_cluster(集群名称,
                副本数,
                shard个数,
                缓冲池大小):
    # 点击集群管理,集群列表,集群列表信息,点击新增
    driver.find_element(By.XPATH, '//*[@id="app"]/div/div[1]/div/div[1]/div/ul/div[4]/li/div').click()
    driver.find_element(By.XPATH, '//*[@id="app"]/div/div[1]/div/div[1]/div/ul/div[4]/li/ul/div[1]/a/li').click()
    driver.find_element(By.XPATH, '//*[@id="tab-second"]').click()

    # 点击新增集群
    driver.find_element(By.XPATH, '//*[@id="pane-second"]/div/div[1]/div/button[3]').click()
    time.sleep(2)
    #  集群名称
    driver.find_element(By.XPATH, '//*[@id="pane-second"]/div/div[4]/div/div[2]/form/div[1]/div/div/input').send_keys(集群名称)
    # 选择计算机
    driver.find_element(By.XPATH, '//*[@id="pane-second"]/div/div[4]/div/div[2]/form/div[2]/div/div/label/span[1]/span').click()
    # 高可用模式
    driver.find_element(By.XPATH, '//*[@id="pane-second"]/div/div[4]/div/div[2]/form/div[3]/div/div/div/input').click()
    driver.find_element(By.XPATH, '/html/body/div[4]/div[1]/div[1]/ul/li').click()
    # shard个数
    n = driver.find_element(By.XPATH, '//*[@id="pane-second"]/div/div[4]/div/div[2]/form/div[4]/div/div/div/input')
    n.clear()
    n.send_keys(shard个数)
    # 副本数  副本数至少是3，且不能大于所选计算机数
    c = driver.find_element(By.XPATH, '//*[@id="pane-second"]/div/div[4]/div/div[2]/form/div[5]/div/div/input')
    c.clear()
    c.send_keys(副本数)
    # 缓冲池大小
    a = driver.find_element(By.XPATH, '//*[@id="pane-second"]/div/div[4]/div/div[2]/form/div[6]/div/div/input')
    a.clear()
    a.send_keys(缓冲池大小)
    driver.find_element(By.XPATH, '//*[@id="pane-second"]/div/div[4]/div/div[3]/div/button[2]').send_keys()
    driver.find_element(By.XPATH, '//*[@id="pane-second"]/div/div[4]/div/div[3]/div/button[2]').click()

new_cluster(    集群名称='test',
                副本数=3,
                shard个数=1,
                缓冲池大小=1)

time.sleep(180)
# 新增备份储存目标
def copyid(目标名称,IP地址,端口号):
    # 点击集群管理
    driver.find_element(By.XPATH, '//*[@id="app"]/div/div[1]/div/div[1]/div/ul/div[4]/li/div').click()
    # 点击备份储存目标
    driver.find_element(By.XPATH, '//*[@id="app"]/div/div[1]/div/div[1]/div/ul/div[4]/li/ul/div[4]/a/li').click()
    # 点击新增储存目标
    time.sleep(5)
    driver.find_element(By.XPATH, '//*[@id="app"]/div/div[2]/section/div/div[1]/div[1]/button[3]').click()
    # 输入目标名称
    driver.find_element(By.XPATH, '//*[@id="app"]/div/div[2]/section/div/div[4]/div/div[2]/form/div[1]/div/div[1]/input').send_keys(目标名称)
    #  选择目标类型
    c = driver.find_element(By.XPATH, '//*[@id="app"]/div/div[2]/section/div/div[4]/div/div[2]/form/div[2]/div/div/div/input')
    c.click()
    # 选择HDFS类型
    driver.find_element(By.XPATH, "//li[contains(.,'HDFS')]").click()
    # 输入ip地址
    driver.find_element(By.XPATH, '//*[@id="app"]/div/div[2]/section/div/div[4]/div/div[2]/form/div[3]/div/div[1]/input').send_keys(IP地址)
    # 输入端口号
    driver.find_element(By.XPATH, '//*[@id="app"]/div/div[2]/section/div/div[4]/div/div[2]/form/div[4]/div/div/input').send_keys(端口号)
    driver.find_element(By.XPATH, '//*[@id="app"]/div/div[2]/section/div/div[4]/div/div[3]/div/button[2]').click()
# 备份储存目标的删除
def copyid_delete():
    # 点击集群管理
    driver.find_element(By.XPATH, '//*[@id="app"]/div/div[1]/div/div[1]/div/ul/div[4]/li/div').click()
    # 点击备份储存目标
    driver.find_element(By.XPATH, '//*[@id="app"]/div/div[2]/div/div[1]/div/ul/div[4]/li/ul/div[4]/a/li').click()
    # 点击删除
    driver.find_element(By.XPATH, '//*[@id="app"]/div/div[2]/section/div/div[2]/div[3]/table/tbody/tr/td[6]/div/button[2]').click()
    # 点击确定
    driver.find_element(By.XPATH, '/html/body/div[2]/div/div[3]/button[2]').click()
    # driver.find_element(By.XPATH, '').click()

# 进行备份数据
def backup():
    # 点击集群管理,集群列表,集群列表信息
    driver.find_element(By.XPATH, '//*[@id="app"]/div/div[1]/div/div[1]/div/ul/div[4]/li/div').click()
    driver.find_element(By.XPATH, '//*[@id="app"]/div/div[1]/div/div[1]/div/ul/div[4]/li/ul/div[1]/a/li').click()
    driver.find_element(By.XPATH, '//*[@id="tab-second"]').click()
    # 点击备份
    driver.find_element(By.XPATH, '//*[@id="pane-second"]/div/div[2]/div[3]/table/tbody/tr[1]/td[7]/div/button[3]').click()
    # 点击确定
    driver.find_element(By.XPATH, '//body/div[2]/div/div[3]/button[2]').click()

# 进行集群的恢复
def restore(新集群名称):
    # 点击恢复按键
    driver.find_element(By.XPATH, '//*[@id="pane-second"]/div/div[2]/div[3]/table/tbody/tr/td[7]/div/button[2]').click()
    # 点击选择的计算机
    driver.find_element(By.XPATH, '//*[@id="pane-second"]/div/div[6]/div/div[2]/form/div[1]/div/div/label/span[1]/span').click()
    # 点击并且输入新的集群名称
    driver.find_element(By.XPATH, '//*[@id="pane-second"]/div/div[6]/div/div[2]/form/div[3]/div/div/input').click(新集群名称)
    # 点击选择的回档时间
    # driver.find_element(By.XPATH, '').click()
    # 点击确定恢
    driver.find_element(By.XPATH, '//*[@id="pane-second"]/div/div[6]/div/div[3]/div/button[2]').click()



# 对集群进行增加shard
def new_shard(选择shard个数):
    # 点击增加的按键:
    driver.find_element(By.XPATH, '//tbody/tr/td[7]/div/button').click()
    # 选择计算机
    driver.find_element(By.XPATH, '//label/span/span').click()
    # 点击类型
    driver.find_element(By.XPATH, "//input[@placeholder='请选择类型']").click()
    # 从中选择shard或者是计算节点
    driver.find_element(By.XPATH, "//span[contains(.,'shard')]").click()
    # 点击个数
    driver.find_element(By.XPATH, "//input[@placeholder='请选择个数']").click()
    # 点击选择数字
    driver.find_element(By.XPATH, "//body/div[4]/div[1]/div[1]/ul/li['"+选择shard个数+"']").click()
    # 点击确定
    driver.find_element(By.XPATH, '//*[@id="pane-second"]/div/div[5]/div/div[3]/div/button[2]').click()
    # driver.find_element(By.XPATH, '').click()


def new_server(选择计算节点个数):
    # 点击增加的按键:
    driver.find_element(By.XPATH, '//tbody/tr/td[7]/div/button').click()
    # 选择计算机
    driver.find_element(By.XPATH, '//label/span/span').click()
    # 点击类型
    driver.find_element(By.XPATH, "//input[@placeholder='请选择类型']").click()
    # 从中选择shard或者是计算节点
    driver.find_element(By.XPATH, "//span[contains(.,'计算节点')]").click()
    # 点击个数
    driver.find_element(By.XPATH, "//input[@placeholder='请选择个数']").click()
    # 点击选择数字/html/body/div[4]/div[1]/div[1]/ul
    driver.find_element(By.XPATH, "//body/div[4]/div[1]/div[1]/ul/li['"+选择计算节点个数+"']").click()
    # 点击确定
    driver.find_element(By.XPATH, '//*[@id="pane-second"]/div/div[5]/div/div[3]/div/button[2]').click()
    # driver.find_element(By.XPATH, '').click()

def new_storage(shard名称,选择存储节点个数):
    # 点击增加的按键:
    driver.find_element(By.XPATH, '//tbody/tr/td[7]/div/button').click()
    # 选择计算机
    driver.find_element(By.XPATH, '//label/span/span').click()
    # 点击类型
    driver.find_element(By.XPATH, "//input[@placeholder='请选择类型']").click()
    # 从中选择shard或者是计算节点
    driver.find_element(By.XPATH, "//span[contains(.,'存储节点')]").click()
    # 点击shard名称
    driver.find_element(By.XPATH, "//input[@placeholder='请选择shard名称']").click()
    # 进行选择
    driver.find_element(By.XPATH, "//span[contains(.,'"+shard名称+"')]").click()
    # 点击个数
    driver.find_element(By.XPATH, "//input[@placeholder='请选择个数']").click()
    # 点击选择数字
    driver.find_element(By.XPATH, "//body/div[5]/div[1]/div[1]/ul/li['"+选择存储节点个数+"']").click()
    # 点击确定
    driver.find_element(By.XPATH, '//*[@id="pane-second"]/div/div[5]/div/div[3]/div/button[2]').click()
    # driver.find_element(By.XPATH, '').click()



# 新增备份存储目标
copyid(目标名称='test',IP地址='192.168.0.128',端口号='57002')
# 进行备份
backup()
restore(新集群名称='test111')
# 进入到集群管理
driver.find_element(By.XPATH, "//span[contains(.,'集群管理')]").click()
# 进入到集群列表
driver.find_element(By.XPATH, "//span[contains(.,'集群列表')]").click()
time.sleep(1)
# 进入到集群列表信息
driver.find_element(By.XPATH, "//*[@id='tab-second']").click()
new_shard(选择shard个数='1')
time.sleep(120)
new_server(选择计算节点个数='1')
time.sleep(120)
new_storage(shard名称='shard1',选择存储节点个数='1')
time.sleep(120)


# 计算节点的删除
# 存储节点的删除

# 在线体验的测试
def new_kunlundb():
    # 进入在线体验
    driver.find_element(By.XPATH, "//span[contains(.,'在线体验')]").click()
    # 点击输入框
    c = driver.find_element(By.XPATH, '//*[@id="app"]/div/div[2]/section/div/div[2]/textarea')
    c.send_keys('create table t1(a int);')
    # 点击执行
    a = driver.find_element(By.XPATH, "//span[contains(.,'在线体验')]")
    a.click()
    c.send_keys('insert into t1 values (1); ')
    a.click()
    c.send_keys('select * from t1;')
    a.click()
    c.send_keys('drop table t1;')
    a.click()


