# 测试XPanel
# 测试内容包括XPanel基本的主要功能
# 导入需要的包
from selenium import webdriver
from selenium.webdriver.common.action_chains import ActionChains
from selenium.webdriver.common.keys import Keys
from selenium.webdriver.common.by import By
from selenium.webdriver.support.select import Select
import time

# 注意:需要与浏览器版本的一致,放置在python的安装路径里面
driver = webdriver.Chrome()
# driver.get("http://192.168.0.126/KunlunXPanel/")
driver.get("http://192.168.0.136:5555/KunlunXPanel/")
time.sleep(2)
# 最大化窗口
driver.maximize_window()
# 隐式等待
driver.implicitly_wait(20)

def new_login (user,pwd):
    driver.find_element(By.NAME, 'username').send_keys(user)
    driver.find_element(By.NAME, 'password').send_keys(pwd)
    driver.find_element(By.XPATH, '//*[@id="app"]/div/div/form/button').click()
# new_login(user='super_dba',pwd='Aa_12345')

def new_users(用户账号, 登录密码, 重复密码, 手机号码, 邮箱地址, 微信号):
    # 进入首页点击系统管理,点击用户管理,点击新建用户,创建创建用户test1,密码Aa_11111,电话13929558834,邮箱
    # 先是点击的系统管理
    driver.find_element(By.XPATH, "//span[contains(.,'系统管理')]").click()
    # 点击用户管理
    driver.find_element(By.XPATH, "//span[contains(.,'用户管理')]").click()
    # 进入用户管理的页面后点击新增用户
    # driver.find_element(By.XPATH, "//span[contains(.,'新增')]").click()
    driver.find_element(By.XPATH, '//*[@id="app"]/div/div[2]/section/div/div[1]/div/button[3]').click()
    time.sleep(2)
    driver.find_element(By.XPATH, "//input[@placeholder='请输入用户账号']").send_keys(用户账号)
    driver.find_element(By.XPATH, "//input[@autocomplete='new-password']").send_keys(登录密码)
    driver.find_element(By.XPATH, "//input[@placeholder='请再次输入登录密码']").send_keys(重复密码)
    driver.find_element(By.XPATH, "//input[@placeholder='请输入手机号码']").send_keys(手机号码)
    driver.find_element(By.XPATH, "//input[@placeholder='请输入邮箱地址']").send_keys(邮箱地址)
    driver.find_element(By.XPATH, "//input[@placeholder='请输入邮箱地址']").send_keys(微信号)
    driver.find_element(By.XPATH, "//span[contains(.,'确认')]").click()


# 点击建立角色
def new_role(role):
    # 点击角色管理
    driver.find_element(By.XPATH, "//span[contains(.,'角色管理')]").click()
    driver.find_element(By.XPATH, "//span[contains(.,'新增')]").click()
    driver.find_element(By.XPATH, "//input[@placeholder='请输入角色名称']").send_keys(role)
    driver.find_element(By.XPATH, '//*[@id="app"]/div/div[2]/section/div/div[4]/div/div[2]/form/div[2]/div/div/div[1]/div/label/span/span').click()
    driver.find_element(By.XPATH, '//*[@id="app"]/div/div[2]/section/div/div[4]/div/div[2]/form/div[2]/div/div/div[2]/div/label/span/span').click()
    driver.find_element(By.XPATH, '//*[@id="app"]/div/div[2]/section/div/div[4]/div/div[3]/div/button[2]').click()
def accredit():
    # 点击新增授权
    time.sleep(2)
    driver.find_element(By.XPATH, "//span[contains(.,'新增')]").click()
    time.sleep(1)
    # 点击用户账户
    driver.find_element(By.XPATH, "//input[@placeholder='请选择用户账户']").click()
    # 选择需要的账户 后期测试把test1设置成全局变量(所有测试中需要保证唯一性)
    driver.find_element(By.XPATH, "//li[contains(.,'test1')]").click()
    #  点击角色名称
    driver.find_element(By.XPATH, "//input[@placeholder='请选择用户角色']").click()
    # 选择角色
    driver.find_element(By.XPATH, "//div[4]/div/div/ul/li").click()
    # driver.find_element(By.XPATH, "//span[contains(.,'角色名称')]").click()
    # 点击有效的时间类型 ,类型
    driver.find_element(By.XPATH, "(//input[@type='text'])[4]").click()
    # 选择永久或者时间段
    driver.find_element(By.XPATH, "//span[contains(.,'永久')]").click()
    # driver.find_element(By.XPATH, "//span[contains(.,\'时间段\')]").click()
    time.sleep(1)
    # 选择应用使用与所有的集群
    driver.find_element(By.XPATH, '//*[@id="app"]/div/div[2]/section/div/div[4]/div/div[2]/form/div[4]/div/label[1]/span[1]').click()
    # 点击确定
    driver.find_element(By.XPATH, "//span[contains(.,'确认')]").click()

### 计算机管理 中的删除与新增.存在有相关的计算机的集群不能进行删除

# 进行删除计算机
def del_server(计算机地址):
    # 点击元数据集群管理
    driver.find_element(By.XPATH, "//span[contains(.,'计算机管理')]").click()
    driver.find_element(By.XPATH, "//span[contains(.,'计算机列表')]").click()
    time.sleep(1)
    driver.find_element(By.XPATH, '//*[@id="app"]/div/div[2]/section/div/div[1]/div[1]/div[1]/input').send_keys(计算机地址)
    driver.find_element(By.XPATH, "//span[contains(.,'查询')]").click()
    driver.find_element(By.XPATH, "//span[contains(.,'删除')]").click()

# 进行登录 这里的"类型"只能填写"计算"或者"储存" 1为存储,2为计算
def kl_server1(ip地址,
                  起始端口,
                  结束端口,
                  日志目录,
               wal日志目录,
               存储节点数据目录,
                  总内存,
                  cpu核数,
                  机架编号):
    driver.find_element(By.XPATH, "//span[contains(.,'计算机管理')]").click()
    driver.find_element(By.XPATH, "//span[contains(.,'计算机列表')]").click()
    time.sleep(1)
    # 点击新增计算机
    driver.find_element(By.XPATH, "//span[contains(.,'新增')]").click()
    # driver.find_element(By.XPATH, '//*[@id="app"]/div/div[2]/section/div/div[1]/div[1]/button[3]').click()
    # 填写ip地址
    driver.find_element(By.XPATH, "//input[@placeholder='请输入IP地址']").send_keys(ip地址)

    driver.find_element(By.XPATH, "//input[@placeholder='请选择机器类型']").click()
    # 进行选择机器的类型1为存储,2为计算类型
    # driver.find_element(By.XPATH, "/html/body/div[3]/div[1]/div[1]/ul/li[1]").click()
    driver.find_element(By.XPATH, "//span[contains(.,'储存')]").click()
    driver.find_element(By.XPATH, "//input[@placeholder='请输入起始端口号']").send_keys(起始端口)
    driver.find_element(By.XPATH, "//input[@placeholder='请输入结束端口号']").send_keys(结束端口)
    driver.find_element(By.XPATH, "//input[@placeholder='请输入日志目录']").send_keys(日志目录)
    driver.find_element(By.XPATH, "//input[@placeholder='请输入wal日志目录']").send_keys(wal日志目录)
    driver.find_element(By.XPATH, "//input[@placeholder='请输入存储节点数据目录']").send_keys(存储节点数据目录)
    driver.find_element(By.XPATH, "//input[@placeholder='请输入总内存']").send_keys(总内存)
    driver.find_element(By.XPATH, "//input[@placeholder='请输入cpu核数']").send_keys(cpu核数)
    driver.find_element(By.XPATH, "//input[@placeholder='请输入机架编号']").send_keys(机架编号)
    # 点击确定
    driver.find_element(By.XPATH, "//span[contains(.,'确定')]").click()
    # driver.find_element(By.XPATH, '//*[@id="app"]/div/div[2]/section/div/div[4]/div/div[3]/div/button[2]').click()
    #driver.find_element(By.XPATH, '').click()

    # kl_server1(
    #     ip地址='192.168.0.132',
    #     机器类型='计算',
    #     起始端口='47000',
    #     结束端口='48000',
    #     日志目录='/home/kunlun/vito5555/debug/server_datadir',
    #      wal日志目录='/home/kunlun/vito5555/debug/,
    #   存储节点数据目录='/home/kunlun/vito5555/debug/,
    #     总内存='8192',
    #     cpu核数='32',
    #     机架编号=''
    # )
# 计算机类型
def kl_server2(ip地址,起始端口,结束端口,计算节点数据目录,总内存,cpu核数,机架编号):
    driver.find_element(By.XPATH, "//span[contains(.,'计算机管理')]").click()
    driver.find_element(By.XPATH, "//span[contains(.,'计算机列表')]").click()
    time.sleep(1)
    driver.find_element(By.XPATH, "//span[contains(.,'新增')]").click()
    driver.find_element(By.XPATH, "//input[@placeholder='请输入IP地址']").send_keys(ip地址)
    driver.find_element(By.XPATH, "//input[@placeholder='请选择机器类型']").click()
    # 进行选择机器的类型1为存储,2为计算类型
    driver.find_element(By.XPATH, "//span[contains(.,'计算')]").click()
    #driver.find_element(By.XPATH, "/html/body/div[3]/div[1]/div[1]/ul/li[2]").click()

    driver.find_element(By.XPATH, "//input[@placeholder='请输入起始端口号']").send_keys(起始端口)
    driver.find_element(By.XPATH, "//input[@placeholder='请输入结束端口号']").send_keys(结束端口)
    driver.find_element(By.XPATH, "//input[@placeholder='请输入计算节点数据目录']").send_keys(计算节点数据目录)
    driver.find_element(By.XPATH, "//input[@placeholder='请输入总内存']").send_keys(总内存)
    driver.find_element(By.XPATH, "//input[@placeholder='请输入cpu核数']").send_keys(cpu核数)
    driver.find_element(By.XPATH, "//input[@placeholder='请输入机架编号']").send_keys(机架编号)
    driver.find_element(By.XPATH, "//span[contains(.,'确定')]").click()
    # driver.find_element(By.XPATH, '//*[@id="app"]/div/div[2]/section/div/div[4]/div/div[3]/div/button[2]').click()
    # kl_server(
    #     ip地址='192.168.0.132',
    #     起始端口='47000',
    #     结束端口='48000',
    #     计算节点数据目录='/home/kunlun/vito5555/debug/server_datadir',
    #     总内存='8192',
    #     cpu核数='32',
    #     机架编号=''
    # )

### 元数据集群管理
def meta_cluster(查询ip):
    driver.find_element(By.XPATH, "//span[contains(.,'元数据集群管理')]").click()
    driver.find_element(By.XPATH, "//span[contains(.,'元数据节点列表')]").click()
    time.sleep(1)
    driver.find_element(By.XPATH, "//input[@placeholder='可输入IP搜索']").send_keys(查询ip)
### 集群管理

# 创建集群
def new_cluster(业务名称,
                选择模式,
                副本数,
                shard个数,
                计算节点总个数,
                缓冲池大小):
    # 点击集群管理
    driver.find_element(By.XPATH, '//*[@id="app"]/div/div[1]/div/div[1]/div/ul/div[5]/li/div').click()
    driver.find_element(By.XPATH, "//span[contains(.,'集群列表')]").click()
    # 点击新增集群
    driver.find_element(By.XPATH, '//*[@id="pane-second"]/div/div[1]/div/button[3]').click()

    time.sleep(2)
    #  业务名称
    driver.find_element(By.XPATH, "//input[@placeholder='请输入业务名称']").send_keys(业务名称)
    # 选择计算机
    driver.find_element(By.XPATH, '//*[@id="pane-second"]/div/div[4]/div/div[2]/form/div[2]/div/div[1]/div/div[2]/input').click()
    time.sleep(1)
    driver.find_element(By.XPATH, '/html/body/div[4]/div[1]/div[1]/ul/li/span').click()
    driver.find_element(By.XPATH, '//*[@id="pane-second"]/div/div[4]/div/div[2]/form/div[2]/div/div[1]/div/div[2]/span/span/i').click()

    driver.find_element(By.XPATH, '//*[@id="pane-second"]/div/div[4]/div/div[2]/form/div[2]/div/div[2]/div/div[2]/input').click()
    time.sleep(1)
    driver.find_element(By.XPATH, '/html/body/div[5]/div[1]/div[1]/ul/li/span').click()
    driver.find_element(By.XPATH, '//*[@id="pane-second"]/div/div[4]/div/div[2]/form/div[2]/div/div[2]/div/div[2]/span/span/i').click()
    # 高可用模式
    driver.find_element(By.XPATH, "//input[@placeholder='请选择高可用模式']").click()
    time.sleep(1)
    # mgr的模式 可以设置变量 1为mgr 2为rbr, 3为no_rep
    driver.find_element(By.XPATH, "/html/body/div[6]/div[1]/div[1]/ul/li['"+选择模式+"']").click()
    # rbr的模式
    # shard个数
    n = driver.find_element(By.XPATH, "//input[@placeholder='请输入shard个数']")
    n.clear()
    n.send_keys(shard个数)
    # 副本数  副本数至少是3，且不能大于所选计算机数
    c = driver.find_element(By.XPATH, "//input[@placeholder='副本数至少是3，且不能大于256']")
    c.clear()
    c.send_keys(副本数)
    # 计算节点个数
    a = driver.find_element(By.XPATH, "//input[@placeholder='输入计算节点总个数']")
    a.clear()
    a.send_keys(计算节点总个数)
    # 缓冲池大小
    a = driver.find_element(By.XPATH, "//input[@placeholder='缓冲池大小单位为MB']")
    a.clear()
    a.send_keys(缓冲池大小)
    # 点击关闭
    # driver.find_element(By.XPATH, "//input[@placeholder='']")
    # driver.find_element(By.XPATH, "//span[contains(.,'关闭')]").click()
    # driver.find_element(By.XPATH, '').click()
    # 点击确定
    driver.find_element(By.XPATH, '//*[@id="pane-second"]/div/div[4]/div/div[3]/div/button[2]').click()
    # 安装集群的等待时间
    time.sleep(300)
    # 集群安装完成进行退出
    driver.find_element(By.XPATH, '//*[@id="pane-second"]/div/div[12]/div/div[1]/button/i').click()
def del_cluster(集群名称):
    # 点击集群管理
    driver.find_element(By.XPATH, '//*[@id="app"]/div/div[1]/div/div[1]/div/ul/div[5]/li/div').click()
    driver.find_element(By.XPATH, "//span[contains(.,'集群列表')]").click()
    driver.find_element(By.XPATH, "//span[contains(.,'重置')]").click()
    driver.find_element(By.XPATH, "//input[@placeholder='可输入集群名称搜索']").send_keys(集群名称)
    driver.find_element(By.XPATH, "//span[contains(.,'查询')]").click()
    driver.find_element(By.XPATH, '//*[@id="pane-second"]/div/div[2]/div[4]/div[2]/table/tbody/tr/td[7]/div/button[4]').click()
    time.sleep(2)
    driver.find_element(By.XPATH, "//span[contains(.,'删除集群')]").click()
    time.sleep(2)
    a = driver.find_element(By.XPATH, '/html/body/div[2]/div/div[2]/div[1]/div[2]').text
    time.sleep(2)
    b = int(a[-4:])
    driver.find_element(By.XPATH, "//input[@placeholder='请输入code']").send_keys(b)
    driver.find_element(By.XPATH, "//span[contains(.,'确定')]").click()


def add_mq(shard选择,超时时间):
    #点击免切设置
    driver.find_element(By.XPATH, '//*[@id="app"]/div/div[2]/section/div/div[1]/div[1]/button').click()
    time.sleep(1)
    driver.find_element(By.XPATH, "//span [contains(.,'勾选all表示所有的集群+所有shard都设置免切')]").click()
    # driver.find_element(By.XPATH, "//input[@placeholder='请选择shard名称']").click()
    # time.sleep(1)
    # driver.find_element(By.XPATH, "/html/body/div[3]/div[1]/div[1]/ul/li['"+shard选择+"']").click()
    driver.find_element(By.XPATH, "//input[@placeholder='请输入超时时间']").send_keys(超时时间)
    driver.find_element(By.XPATH, '//*[@id="app"]/div/div[2]/section/div/div[4]/div/div[3]/div/button[2]/span').click()

def del_mq():
    driver.find_element(By.XPATH, '//*[@id="app"]/div/div[1]/div/div[1]/div/ul/div[5]/li/div').click()
    driver.find_element(By.XPATH, "//span[contains(.,'集群免切设置')]").click()
    driver.find_element(By.XPATH, '//*[@id="app"]/div/div[2]/section/div/div[2]/div[3]/table/tbody/tr/td[6]/div/button').click()
    c = driver.find_element(By.XPATH, '/html/body/div[2]/div/div[2]/div[1]/div[2]').text
    a = int(c[-4:])
    time.sleep(1)
    driver.find_element(By.XPATH, "//input[@placeholder='请输入code']").send_keys(a)
    driver.find_element(By.XPATH, "//span[contains(.,'确定')]").click()


def hdfs_backup(目标名称,IP地址,端口号):
    # 点击进入集群管理
    driver.find_element(By.XPATH, '//*[@id="app"]/div/div[1]/div/div[1]/div/ul/div[5]/li/div').click()
    # 点击进入 备份储存目标管理
    driver.find_element(By.XPATH, "//span[contains(.,'备份存储目标管理')]").click()
    # 点击新增储存目标
    time.sleep(2)
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
def hdfs_backup_del(选择集群):
    # 点击集群管理
    driver.find_element(By.XPATH, '//*[@id="app"]/div/div[1]/div/div[1]/div/ul/div[5]/li/div').click()
    driver.find_element(By.XPATH, "//span[contains(.,'备份存储目标管理')]").click()
    # 点击搜索框
    c = driver.find_element(By.XPATH, '//*[@id="app"]/div/div[2]/section/div/div[1]/div[1]/div[1]/input')
    c.clear()
    c.send_keys(选择集群)
    # 点击查询
    driver.find_element(By.XPATH, "//span[contains(.,'查询')]").click()
    # 点击删除
    time.sleep(2)
    driver.find_element(By.XPATH, '//*[@id="app"]/div/div[2]/section/div/div[2]/div[3]/table/tbody/tr/td[6]/div/button[2]').click()
    # driver.find_element(By.XPATH, "//span[contains(.,'删除')]").click()
    driver.find_element(By.XPATH, "//span[contains(.,'确定')]").click()


def hdfs_backup_alter(选择集群, ip地址,端口号):
    # 点击集群管理
    driver.find_element(By.XPATH, '//*[@id="app"]/div/div[1]/div/div[1]/div/ul/div[5]/li/div').click()
    driver.find_element(By.XPATH, "//span[contains(.,'备份存储目标管理')]").click()
    # 点击搜索框
    c = driver.find_element(By.XPATH, '//*[@id="app"]/div/div[2]/section/div/div[1]/div[1]/div[1]/input')
    c.clear()
    c.send_keys(选择集群)
    driver.find_element(By.XPATH, "//span[contains(.,'查询')]").click()
    driver.find_element(By.XPATH, '//*[@id="app"]/div/div[2]/section/div/div[2]/div[3]/table/tbody/tr/td[6]/div/button[1]').click()
    b = driver.find_element(By.XPATH, "//input[@placeholder='请输入IP地址']")
    b.clear()
    b.send_keys(ip地址)
    e = driver.find_element(By.XPATH, "//input[@placeholder='请输入端口号']")
    e.clear()
    e.send_keys(端口号)

    driver.find_element(By.XPATH, '//*[@id="app"]/div/div[2]/section/div/div[4]/div/div[3]/div/button[2]').click()

def Online(sql):
    driver.find_element(By.XPATH, "//span[contains(.,'在线体验')]").click()
    driver.find_element(By.XPATH, "//span[contains(.,'一键清空')]").click()
    driver.find_element(By.XPATH, "//div[@id='app']/div/div[2]/section/div/div[2]/div/div[6]").click()
    driver.find_element(By.XPATH, "//div[@id='app']/div/div[2]/section/div/div[2]/div/div/textarea").send_keys(sql)
    driver.find_element(By.XPATH, "//span[contains(.,'运行')]").click()
    a = driver.find_element(By.XPATH, '//*[@id="app"]/div/div[2]/section/div/div[2]/div[2]/div/div[2]').text
    print(a)

# 进行登录
new_login(user='super_dba', pwd='Aa_12345')
# 创建账号
new_users(用户账号='test1', 登录密码='Aa_11111', 重复密码='Aa_11111', 手机号码='123929558834', 邮箱地址='123@qq.com', 微信号='')
time.sleep(1)
# 点击角色管理
driver.find_element(By.XPATH, "//span[contains(.,'角色管理')]").click()
# 点击进入建立新的角色a ,权限包括集群权限,和计算机权限
time.sleep(1)
# 创建角色
new_role(role='a')

# 点击授权管理
driver.find_element(By.XPATH, "//span[contains(.,'授权管理')]").click()
time.sleep(1)

# 进行授权
accredit()

# 退出登录进行创建新账号的验证
time.sleep(1)
driver.find_element(By.XPATH, "//a[contains(.,'退出登录')]").click()
time.sleep(2)
# 进行test1的登录
new_login(user='test1', pwd='Aa_11111')
# 登录后可以进行一些操作,这里先省略
# 退出账号test1
driver.find_element(By.XPATH, "//a[contains(.,'退出登录')]").click()
# 登录管理员账号
new_login(user='super_dba', pwd='Aa_12345')
# 先是点击的系统管理
driver.find_element(By.XPATH, "//span[contains(.,'系统管理')]").click()
# 点击用户管理
driver.find_element(By.XPATH, "//span[contains(.,'用户管理')]").click()
# 删除账号
driver.find_element(By.XPATH, "//input[@placeholder='可输入用户账号搜索']").send_keys('test1')
driver.find_element(By.XPATH, "//span[contains(.,'查询')]").click()
time.sleep(2)
driver.find_element(By.XPATH, '//*[@id="app"]/div/div[2]/section/div/div[2]/div[3]/table/tbody/tr[1]/td[7]/div/button[2]').click()
driver.find_element(By.XPATH, '/html/body/div[2]/div/div[3]/button[2]').click()
# 点击角色管理
driver.find_element(By.XPATH, "//span[contains(.,'角色管理')]").click()
time.sleep(1)
# 删除角色
driver.find_element(By.XPATH, '//*[@id="app"]/div/div[2]/section/div/div[2]/div[3]/table/tbody/tr[1]/td[5]/div/button[2]').click()
time.sleep(1)
driver.find_element(By.XPATH, '/html/body/div[2]/div/div[3]/button[2]').click()

time.sleep(3)

# 创建一个新的集群
new_cluster(业务名称='vito', 选择模式='1', 副本数='3', shard个数='1', 计算节点总个数='1', 缓冲池大小='1')
# 进行免切设置
driver.find_element(By.XPATH, '//*[@id="app"]/div/div[1]/div/div[1]/div/ul/div[5]/li/div').click()
driver.find_element(By.XPATH, "//span[contains(.,'集群免切设置')]").click()
add_mq(shard选择='1', 超时时间='20')
# 删除免切设置
del_mq()
time.sleep(2)
driver.find_element(By.XPATH, '//*[@id="app"]/div/div[1]/div/div[1]/div/ul/div[5]/li/div').click()
driver.find_element(By.XPATH, "//span[contains(.,'备份存储目标管理')]").click()
hdfs_backup(目标名称='vito2', IP地址='192.168.0.128', 端口号='57303')
hdfs_backup_alter(选择集群='vito2', ip地址='192.168.0.129', 端口号='23456')
hdfs_backup_del(选择集群='vito2')
Online(sql='table t1;')
# 删除已有的集群
del_cluster(集群名称='vito')
# del_server(计算机地址='192.168.0.136')


