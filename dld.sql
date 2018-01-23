/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50633
Source Host           : 127.0.0.1:3336
Source Database       : dld

Target Server Type    : MYSQL
Target Server Version : 50633
File Encoding         : 65001

Date: 2018-01-23 20:44:34
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
INSERT INTO `user` VALUES ('1', 'yls', '6084512', 'o8FFywJcAMLAEAo-1Xh7YkIlR0dI', 'oKIwA0eHZyXEDaUICvhtyE8EJuts', '0e0ee562b40bccb84818c817816ed7ba', '1');
INSERT INTO `user` VALUES ('2', 'yj', '769448', '', 'oKIwA0aGacUIRZjEHNXgzQvT65CA', '', '1');
INSERT INTO `user` VALUES ('3', 'ylsxh', '636428', '', 'oKIwA0eHZyXEDaUICvhtyE8EJuts', '', '1');

-- ----------------------------
-- Table structure for user_config
-- ----------------------------
DROP TABLE IF EXISTS `user_config`;
CREATE TABLE `user_config` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `meridian` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `lilian` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '历练 1要经验 2爬',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of user_config
-- ----------------------------
INSERT INTO `user_config` VALUES ('1', '1', '1', '2');

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
  `qualifying` varchar(30) NOT NULL DEFAULT '' COMMENT '排位',
  `qualifying_num` smallint(4) unsigned NOT NULL DEFAULT '0' COMMENT '次数',
  `teamqua` varchar(30) NOT NULL DEFAULT '' COMMENT '团队排位',
  `teamqua_num` smallint(4) unsigned NOT NULL DEFAULT '0',
  `lilian` varchar(10) NOT NULL DEFAULT '' COMMENT '历练',
  `hero_lilian` varchar(10) NOT NULL DEFAULT '' COMMENT '英雄历练',
  `tower` varchar(10) NOT NULL DEFAULT '' COMMENT '千层塔',
  `marry_hangup` varchar(10) NOT NULL DEFAULT '' COMMENT '游历',
  `unlock_page` varchar(10) NOT NULL DEFAULT '' COMMENT '72变',
  `unlock_scene` varchar(10) NOT NULL DEFAULT '' COMMENT '场景解锁',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of user_info
-- ----------------------------
INSERT INTO `user_info` VALUES ('11', '3', 'BACKPA…', '34', 'https://wx.qlogo.cn/mmopen/vi_32/DYAIOgq83ertuLYicnsiaekB4Libibvt2kYBKFicmOyAVQqmZUutCFnWGC0wRNMxiaQFN51icAfkZ2CAsAWR7tVsG34gA/0', '887', '8000', '0', '82', '2', '636428', '984841', '青天外', '16780', '0', '0', '5010', '0', '110', '12235', '15', '72880', '660', '13', '6084512', '白银斗士Ⅲ 4星', '0', '', '0', '', '', '', '', '120/120', '17/17');
INSERT INTO `user_info` VALUES ('12', '1', 'BACKPA…', '41', 'https://wx.qlogo.cn/mmopen/vi_32/DYAIOgq83ertuLYicnsiaekB4Libibvt2kYBKFicmOyAVQqmZUutCFnWGC0wRNMxiaQFN51icAfkZ2CAsAWR7tVsG34gA/0', '16325', '18250', '0', '3', '1', '6084512', '984841', '青天外', '30105', '3', '6', '751', '0', '751', '6659', '244', '141280', '720', '21', '0', '铂金斗尊Ⅲ 2星', '0', '黄金斗师Ⅳ 1星', '0', '', '', '', '', '120/120', '17/17');
INSERT INTO `user_info` VALUES ('13', '2', 'YuJun阳…', '42', 'https://wx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTJnv4h4j5tWy3y9l0vp2KMt96LxHbyibOWE5sdFbskDeyqOF22icC4stH2hcRichsWw5SWo0nVPTbspA/0', '3397', '20500', '0', '7', '2', '769448', '984841', '青天外', '31088', '2', '11', '1195', '0', '1586', '12159', '288', '88840', '160', '21', '0', '铂金斗尊Ⅲ 1星', '0', '', '0', '', '', '', '', '120/120', '17/17');
