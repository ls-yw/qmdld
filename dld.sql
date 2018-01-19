/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50633
Source Host           : 127.0.0.1:3336
Source Database       : dld

Target Server Type    : MYSQL
Target Server Version : 50633
File Encoding         : 65001

Date: 2018-01-19 17:47:47
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for lilian_config
-- ----------------------------
DROP TABLE IF EXISTS `lilian_config`;
CREATE TABLE `lilian_config` (
  `id` int(11) unsigned NOT NULL DEFAULT '0',
  `name` varchar(20) NOT NULL DEFAULT '',
  `gift` varchar(500) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of lilian_config
-- ----------------------------

-- ----------------------------
-- Table structure for user
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_name` varchar(20) NOT NULL DEFAULT '',
  `uid` bigint(20) unsigned NOT NULL DEFAULT '0',
  `openid` varchar(50) NOT NULL DEFAULT '',
  `h5openid` varchar(50) NOT NULL DEFAULT '',
  `h5token` varchar(50) NOT NULL DEFAULT '',
  `auto` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of user
-- ----------------------------
INSERT INTO `user` VALUES ('1', 'yls', '6084512', 'o8FFywJcAMLAEAo-1Xh7YkIlR0dI', 'oKIwA0eHZyXEDaUICvhtyE8EJuts', 'd69ce2842f7ea6d231986dee4fd1e1a7', '1');
INSERT INTO `user` VALUES ('2', 'yj', '769448', '', 'oKIwA0aGacUIRZjEHNXgzQvT65CA', 'a26eb997e435e95220ad86b62ba25ab7', '0');
INSERT INTO `user` VALUES ('3', 'ylsxh', '636428', '', 'oKIwA0eHZyXEDaUICvhtyE8EJuts', 'fefe48f9a5c240cd6904cc23c67d5578', '1');

-- ----------------------------
-- Table structure for user_config
-- ----------------------------
DROP TABLE IF EXISTS `user_config`;
CREATE TABLE `user_config` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `meridian` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of user_config
-- ----------------------------
INSERT INTO `user_config` VALUES ('1', '1', '1');

-- ----------------------------
-- Table structure for user_info
-- ----------------------------
DROP TABLE IF EXISTS `user_info`;
CREATE TABLE `user_info` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL,
  `name` varchar(100) NOT NULL COMMENT '游戏名',
  `lvl` smallint(4) unsigned NOT NULL DEFAULT '0' COMMENT '等级',
  `headimgurl` varchar(255) NOT NULL COMMENT '头像',
  `exp` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '经验',
  `max_exp` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '满级经验',
  `vip_lvl` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT 'VIP等级',
  `vit` smallint(4) unsigned NOT NULL DEFAULT '0' COMMENT '能量',
  `sex` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '性别 1男 2女',
  `uid` int(11) unsigned NOT NULL DEFAULT '0',
  `fac_id` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '帮派ID',
  `fac_name` varchar(20) NOT NULL DEFAULT '' COMMENT '帮派名字',
  `attack_power` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '战斗力',
  `marry_status` smallint(4) unsigned NOT NULL DEFAULT '0' COMMENT '结婚状态 3 已婚',
  `invite_num` smallint(4) unsigned NOT NULL DEFAULT '0' COMMENT '邀请人数',
  `douyu_num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '斗鱼币',
  `doubi_num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '斗币',
  `servant_cash` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '家财币',
  `king_medal` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '荣耀币',
  `prestige` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '威望',
  `spirit` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '修为池',
  `winpoint` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '胜点',
  `login_days` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '登录天数',
  `mentor_uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '师傅UID',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of user_info
-- ----------------------------
INSERT INTO `user_info` VALUES ('11', '3', 'BACKPA…', '30', 'https://wx.qlogo.cn/mmopen/vi_32/DYAIOgq83ertuLYicnsiaekB4Libibvt2kYBKFicmOyAVQqmZUutCFnWGC0wRNMxiaQFN51icAfkZ2CAsAWR7tVsG34gA/0', '378', '3400', '0', '53', '2', '636428', '984841', '', '13769', '0', '0', '3270', '0', '700', '630', '255', '33820', '400', '9', '6084512');
INSERT INTO `user_info` VALUES ('12', '1', 'BACKPA…', '39', 'https://wx.qlogo.cn/mmopen/vi_32/DYAIOgq83ertuLYicnsiaekB4Libibvt2kYBKFicmOyAVQqmZUutCFnWGC0wRNMxiaQFN51icAfkZ2CAsAWR7tVsG34gA/0', '4236', '14400', '0', '12', '1', '6084512', '984841', '', '26350', '3', '6', '41', '0', '541', '9632', '241', '128740', '600', '17', '0');
