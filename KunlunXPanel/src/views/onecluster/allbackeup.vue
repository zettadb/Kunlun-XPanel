<template>
    <div class="app-container">
        <el-form ref="form" :model="form" label-width="100px">
            <el-form-item label="集群名称">
                <span>{{ form.name }}</span>
            </el-form-item>
            <el-form-item label="业务名称">
                <span>{{ form.nick_name }}</span>
            </el-form-item>
            <el-form-item>
                <el-button type="primary" @click="onSubmit(form)">发起全量备份</el-button>
                <!-- <el-button>取消</el-button> -->
            </el-form-item>
        </el-form>
    </div>
</template>

<script>
import { backeUpCluster } from '@/api/cluster/list'
import { messageTip, handleCofirm } from "@/utils";
import { version_arr, timestamp_arr } from "@/utils/global_variable"
export default {
    name: "list",
    props: {
        listsent: { typeof: Object }
    },
    data() {
        return {
            form: {
                name: '',
                nick_name: '',
                id: ''
            }
        }
    },
    mounted() {
        this.form.name = this.listsent.name;
        this.form.nick_name = this.listsent.nick_name;
        this.form.id = this.listsent.id;
    },
    methods: {
        onSubmit(row) {
            handleCofirm("确定要对集群" + row.nick_name + "进行全量备份么?").then(() => {
                const tempData = Object.assign({}, row);
                const backupData = {};
                backupData.user_name = sessionStorage.getItem('login_username');
                backupData.job_id = '';
                backupData.paras = { 'cluster_id': tempData.id, 'nick_name': tempData.nick_name };
                backupData.version = version_arr[0].ver;
                backupData.timestamp = timestamp_arr[0].time + '';
                backupData.job_type = 'manual_backup_cluster';
                //console.log(backupData);return;
                backeUpCluster(backupData).then((response) => {
                    let res = response;
                    if (res.status == 'accept') {
                        this.dialogFormVisible = false;
                        this.message_tips = '全量备份下发成功';
                        this.message_type = 'success';
                        messageTip(this.message_tips, this.message_type);
                    }
                    else if (res.status == 'ongoing') {
                        this.message_tips = '系统正在操作中，请等待一会！';
                        this.message_type = 'error';
                        messageTip(this.message_tips, this.message_type);
                    } else {
                        this.message_tips = res.error_info;
                        this.message_type = 'error';
                        messageTip(this.message_tips, this.message_type);
                    }
                })
            }).catch(() => {
                messageTip('已取消全量备份', 'info');
            });
        }
    }
};
</script>
<style scoped>

</style>