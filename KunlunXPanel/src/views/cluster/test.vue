<template>
    <el-table
                :key="tableKey"
                v-loading="listLoading"
                :data="list"
                border
                highlight-current-row
                style="width: 100%;margin-bottom: 20px;">
                <el-table-column type="selection" width="55" :selectable='checkboxInit'></el-table-column>
                <el-table-column
                  type="index"
                  align="center"
                  label="序号"
                  width="50">
                </el-table-column>
                <el-table-column label="集群ID" align="center">
                  <template slot-scope="{row}">
                    <span class="link-type" @click="handleDetail(row)">{{ row.id }}</span>
                  </template>
                </el-table-column>
                <el-table-column   
                prop="name" 
                label="集群名称"
                align="center">
                </el-table-column>
                <el-table-column  
                prop="nick_name" 
                label="业务名称" 
                align="center">
                </el-table-column>
                <el-table-column
                      prop="ha_mode"
                      align="center"
                      label="高可用模式">
                </el-table-column>
                <el-table-column
                      prop="comptotal"
                      align="center"
                      label="计算节点总数">
                </el-table-column>

                <el-table-column
                      prop="shardtotal"
                      align="center"
                      label="shard分配"
                      :show-overflow-tooltip="true"
                      >
                </el-table-column>
                <el-table-column
                      prop="back_up"
                      align="center"
                      label="最近备份时间">
                      <template slot-scope="scope">
                      <span v-if="scope.row.first_backup===''">无备份</span>
                      <span v-else-if="scope.row.first_backup!==''">{{scope.row.first_backup}}</span>
                  </template>
                </el-table-column>
              </el-table>
</template>