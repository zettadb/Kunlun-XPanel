<template>
    <div class="app-container">
        <el-form ref="form" :model="form" label-width="100px" :rules="rules">
            <el-form-item label="集群名称">
                <span>{{ form.name }}</span>
            </el-form-item>
            <el-form-item label="业务名称">
                <span>{{ form.nick_name }}</span>
            </el-form-item>
            <el-form-item label="设置变量:" prop="variable">
                <el-input v-model="form.variable" placeholder="set global fullsync_timeout = 1200" />
            </el-form-item>

            <el-form-item>
                <el-button type="primary" @click="onSubmit(form)">保存</el-button>
            </el-form-item>
        </el-form>
    </div>
</template>

<script>
import { getShards, setVariable, getThisShardNodes } from "@/api/cluster/list";
import { messageTip } from "@/utils";
import { version_arr, timestamp_arr } from "@/utils/global_variable";
export default {
    name: "list",
    props: {
        listsent: { typeof: Object },
    },
    data() {
        const validateShardName = (rule, value, callback) => {
            if (!value) {
                callback(new Error("请选择shard名称"));
            } else {
                callback();
            }
        };
        const validateReplica = (rule, value, callback) => {
            if (!value) {
                callback(new Error("请选择节点"));
            } else {
                callback();
            }
        };
        const validatevartype = (rule, value, callback) => {
            if (value.length === 0) {
                callback(new Error("请选择变量类型"));
            } else {
                callback();
            }
        };
        const variablename = (rule, value, callback) => {
            if (!value) {
                callback(new Error("变量名不能为空"));
            } else {
                callback();
            }
        };
        const variablevarvalue = (rule, value, callback) => {
            if (!value) {
                callback(new Error("变量值不能为空"));
            } else {
                callback();
            }
        };
        return {
            form: {
                name: "",
                nick_name: "",
                id: "",
                replica: "",
                shard_id: "",
                vartype: "",
                variable: "",
                varvalue: "",
            },
            shardList: [],
            options: [],
            dialogStatusFVisible: false,
            active: 0,
            // 已选步骤
            stepSuc: [0],
            stepParams: [],
            job_id: "",
            timer: null,
            typeOptions: [
                { value: "int", label: "int" },
                { value: "string", label: "string" },
            ],
            rules: {
                shard_id: [{ required: true, trigger: "blur", validator: validateShardName }],
                replica: [{ required: true, trigger: "blur", validator: validateReplica }],
                vartype: [{ required: true, trigger: "blur", validator: validatevartype }],
                variable: [{ required: true, trigger: "blur", validator: variablename }],
                varvalue: [{ required: true, trigger: "blur", validator: variablevarvalue }],
            },
        };
    },
    created() {
        //获取分片名称
        getShards(this.listsent.id).then((response) => {
            let res = response;
            if (res.code == 200) {
                this.shardList = res.list;
            }
        });
    },
    mounted() {
        this.form.name = this.listsent.name;
        this.form.nick_name = this.listsent.nick_name;
        this.form.id = this.listsent.id;
    },
    methods: {
        onSubmit(row) {
            this.$refs["form"].validate((valid) => {
                if (valid) {
                    let tempData = {};
                    tempData.user_name = sessionStorage.getItem("login_username");
                    tempData.job_id = "";
                    tempData.job_type = "set_variable";
                    tempData.version = version_arr[0].ver;
                    tempData.timestamp = timestamp_arr[0].time + "";
                    let paras = {};
                    let ip_arr = row.replica.substring(0, row.replica.length - 1).split("(");
                    paras.hostaddr = ip_arr[0];
                    paras.port = ip_arr[1];
                    paras.type = row.vartype;
                    paras.variable = row.variable;
                    paras.value = row.varvalue;
                    tempData.paras = paras;
                    //console.log(tempData);return;
                    //发送接口
                    setVariable(tempData).then((response) => {
                        this.isShowNodeMenuPanel = false;
                        let res = response;
                        this.message_tips = "正在设置实例变量...";
                        this.message_type = "success";
                        messageTip(this.message_tips, this.message_type);
                        if (res.error_code == "0" && res.status == "done") {
                            this.message_tips = "设置实例变量成功";
                            this.message_type = "success";
                            this.variableVisible = false;
                            messageTip(this.message_tips, this.message_type);
                        } else {
                            this.message_tips = res.error_info;
                            this.message_type = "error";
                            messageTip(this.message_tips, this.message_type);
                        }
                    });
                }
            });
        },
        ChangeSaler(value) {
            //获取节点
            const temps = {};
            temps.cluster_id = this.listsent.id;
            temps.shard_id = value;
            temps.ha_mode = this.listsent.ha_mode;
            getThisShardNodes(temps).then((res) => {
                if (res.code == 200) {
                    this.options = [];
                    if (res.list.length > 0) {
                        for (let i = 0; i < res.list.length; i++) {
                            const arr = {
                                value: i,
                                label: res.list[i].ip + "(" + res.list[i].port + ")",
                            };
                            this.options.push(arr);
                        }
                    }
                }
            });
        },
        resetform() {
            this.form = {
                name: "",
                nick_name: "",
                id: "",
                replica: "",
                shard_id: "",
                vartype: "",
                variable: "",
                varvalue: "",
            };
        },
    },
};
</script>
<style scoped></style>
