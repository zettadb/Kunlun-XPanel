CREATE DATABASE IF NOT EXISTS `kunlun_dba_tools_db`;
CREATE TABLE IF NOT EXISTS `kunlun_dba_tools_db`.`kunlun_user`(
  `id` int unsigned NOT NULL AUTO_INCREMENT comment "User unique id",
  `name` varchar(512) NOT NULL DEFAULT "anonymous_user" comment "Readable user name string",
  `password` varchar(512) NOT NULL DEFAULT "anonymous_pwd" comment "User related password",
  `email` varchar(256) DEFAULT NULL comment "email address belongs to current user",
  `phone_number` varchar(256) DEFAULT NULL comment "Phone number belongs to current user",
  `wechat_number` varchar(256) DEFAULT NULL comment "Wechat id belongs to current user",
  `update_time` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY(`id`)
)ENGINE= InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO `kunlun_dba_tools_db`.`kunlun_user` SET `name` = 'super_dba',`password` = 'super_dba';
SELECT `id` INTO @user_id FROM `kunlun_dba_tools_db`.`kunlun_user` where `name` = 'super_dba';

CREATE TABLE IF NOT EXISTS `kunlun_dba_tools_db`.`kunlun_role_privilege`(
  `id` int unsigned NOT NULL AUTO_INCREMENT comment "Priviliege Unique id",
  `role_name` varchar(256) NOT NULL ,
  `user_add_priv` enum('N','Y') NOT NULL DEFAULT 'N' ,
  `user_drop_priv` enum('N','Y') NOT NULL DEFAULT 'N' ,
  `user_grant_priv` enum('N','Y') NOT NULL DEFAULT 'N' ,
  `user_edit_priv` enum('N','Y') NOT NULL DEFAULT 'N' ,
  `role_add_priv` enum('N','Y') NOT NULL DEFAULT 'N' ,
  `role_drop_priv` enum('N','Y') NOT NULL DEFAULT 'N' ,
  `role_edit_priv` enum('N','Y') NOT NULL DEFAULT 'N' ,
  `machine_priv` enum('N','Y') NOT NULL DEFAULT 'N' ,
  `cluster_creata_priv` enum('N','Y') NOT NULL DEFAULT 'N' ,
  `cluster_drop_priv` enum('N','Y') NOT NULL DEFAULT 'N' ,
  `shard_create_priv` enum('N','Y') NOT NULL DEFAULT 'N' ,
  `shard_drop_priv` enum('N','Y') NOT NULL DEFAULT 'N' ,
  `storage_node_create_priv` enum('N','Y') NOT NULL DEFAULT 'N' ,
  `storage_node_drop_priv` enum('N','Y') NOT NULL DEFAULT 'N' ,
  `compute_node_create_priv` enum('N','Y') NOT NULL DEFAULT 'N' ,
  `compute_node_drop_priv` enum('N','Y') NOT NULL DEFAULT 'N' ,
  `machine_add_priv` enum('N','Y') NOT NULL DEFAULT 'N' ,
  `machine_drop_priv` enum('N','Y') NOT NULL DEFAULT 'N' ,
  `backup_service_enable_priv` enum('N','Y') NOT NULL DEFAULT 'N' ,
  `backup_service_disable_priv` enum('N','Y') NOT NULL DEFAULT 'N' ,
  `storage_enable_priv` enum('N','Y') NOT NULL DEFAULT 'N' ,
  `storage_disable_priv` enum('N','Y') NOT NULL DEFAULT 'N' ,
  `compute_enable_priv` enum('N','Y') NOT NULL DEFAULT 'N' ,
  `compute_disable_priv` enum('N','Y') NOT NULL DEFAULT 'N' ,
  `backup_priv` enum('N','Y') NOT NULL DEFAULT 'N' ,
  `restore_priv` enum('N','Y') NOT NULL DEFAULT 'N' ,
  `expand_cluster_priv` enum('N','Y') NOT NULL DEFAULT 'N' ,
  `shrink_cluster_priv` enum('N','Y') NOT NULL DEFAULT 'N' ,
  `update_time` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY(`id`)
)ENGINE= InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO `kunlun_dba_tools_db`.`kunlun_role_privilege` SET
  `role_name` = 'super_dba',
  `user_add_priv` = 'Y',
  `user_drop_priv` = 'Y',
  `user_grant_priv` = 'Y',
  `user_edit_priv` = 'Y',
  `role_add_priv` = 'Y',
  `role_drop_priv` = 'Y',
  `role_edit_priv` = 'Y',
  `machine_priv` = 'Y',
  `cluster_creata_priv` = 'Y',
  `cluster_drop_priv` = 'Y',
  `shard_create_priv` = 'Y',
  `shard_drop_priv` = 'Y',
  `storage_node_create_priv` = 'Y',
  `storage_node_drop_priv` = 'Y',
  `compute_node_create_priv` = 'Y',
  `compute_node_drop_priv` = 'Y',
  `machine_add_priv` = 'Y',
  `machine_drop_priv` = 'Y',
  `backup_service_enable_priv` = 'Y',
  `backup_service_disable_priv` = 'Y',
  `storage_enable_priv` = 'Y',
  `storage_disable_priv` = 'Y',
  `compute_enable_priv` = 'Y',
  `compute_disable_priv` = 'Y',
  `backup_priv` = 'Y',
  `restore_priv` = 'Y',
  `expand_cluster_priv` = 'Y',
  `shrink_cluster_priv` = 'Y';
SELECT `id` INTO @role_id FROM `kunlun_dba_tools_db`.`kunlun_role_privilege` where `role_name` = 'super_dba';


CREATE TABLE IF NOT EXISTS `kunlun_dba_tools_db`.`kunlun_role_assign`(
  `user_id` int unsigned NOT NULL,
  `role_id` int unsigned NOT NULL,
  `valid_period` enum('permanent','from_to') NOT NULL DEFAULT 'permanent' comment "Period of validity of roles" ,
  `start_ts` timestamp NULL DEFAULT NULL comment "Start time of the valid period of current role",
  `end_ts` timestamp NULL DEFAULT NULL comment "End time of the valid period of current role",
  `apply_all_cluster` bool NOT NULL DEFAULT 1,
  `affected_clusters` text DEFAULT NULL comment "The list consisted by cluster_id sperated by comma",
  `update_time` timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY(`user_id`,`role_id`)
)ENGINE = InnoDB DEFAULT CHARSET = utf8mb4;

INSERT IGNORE INTO `kunlun_dba_tools_db`.`kunlun_role_assign` set `role_id` = @role_id,`user_id` =@user_id, `valid_period` = 'permanent';

