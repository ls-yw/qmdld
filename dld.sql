/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50633
Source Host           : 127.0.0.1:3336
Source Database       : dld

Target Server Type    : MYSQL
Target Server Version : 50633
File Encoding         : 65001

Date: 2018-01-25 19:13:41
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
INSERT INTO `user` VALUES ('1', 'yls', '6084512', 'o8FFywJcAMLAEAo-1Xh7YkIlR0dI', 'oKIwA0eHZyXEDaUICvhtyE8EJuts', '', '1');
INSERT INTO `user` VALUES ('2', 'yj', '769448', '', 'oKIwA0aGacUIRZjEHNXgzQvT65CA', 'b56609b69eb3938f7f70279b18207660', '1');
INSERT INTO `user` VALUES ('3', 'ylsxh', '636428', '', 'oKIwA0eHZyXEDaUICvhtyE8EJuts', 'e0f01e77802711928e1eaf1d808fab1f', '1');

-- ----------------------------
-- Table structure for user_config
-- ----------------------------
DROP TABLE IF EXISTS `user_config`;
CREATE TABLE `user_config` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `meridian_auto` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '经脉自动造访 0不自动 1自动',
  `meridian_flag` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '自动使用威望旗 0不自动 1自动',
  `meridian_reward` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '自动使用雪莲  0不自动 1自动',
  `lilian` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '历练 1要经验 2爬',
  `pvp_auto` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '自动战斗 0不战斗 1战斗',
  `pvp_potion` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '自动使用体力药水 0 不使用 1使用',
  `pvp_friend_vit` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '自动赠送和领取好友体力 0 不使用 1使用',
  `master_auto` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '自动敬茶/解惑 0 不使用 1使用',
  `servant_auto` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '收家财 0 不使用 1使用',
  `servant_rob` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '捣乱 0 不自动 1自动',
  `servant_train` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '训练 0 不自动 1自动',
  `servant_catch` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '捕捉 0 不自动 1自动',
  `servant_release` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '释放 0 不自动 1自动',
  `faction_auto` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '捐献 0 不使用 1使用',
  `faction_club` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '武馆 0 不使用 1使用',
  `qualifying_person` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '个人王者争霸赛 0 不使用 1使用',
  `qualifying_team` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '团队王者争霸赛 0 不使用 1使用',
  `hangup_equip` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '游历装备 0 不自动 1自动',
  `hangup_box` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '游历使用宝箱 0 不自动 1自动',
  `hangup_encourage` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '游历互动 0 不自动 1自动',
  `hangup_fight` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '游历挑战 0 不自动 1自动',
  `lilian_ordinary` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '普通历练 0 不自动 1自动',
  `lilian_ordinary_type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '普通历练类型 1爬楼 2经验 3满星',
  `lilian_used` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '使用令牌礼包',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of user_config
-- ----------------------------
INSERT INTO `user_config` VALUES ('1', '1', '1', '1', '1', '1', '0', '1', '1', '1', '1', '1', '1', '1', '0', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1');
INSERT INTO `user_config` VALUES ('2', '2', '1', '1', '1', '1', '0', '1', '1', '1', '1', '1', '1', '1', '0', '1', '1', '1', '1', '1', '1', '1', '1', '0', '2', '1');
INSERT INTO `user_config` VALUES ('3', '3', '1', '1', '1', '2', '0', '0', '0', '1', '1', '1', '1', '1', '0', '1', '1', '1', '0', '0', '0', '0', '0', '1', '1', '1');

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
INSERT INTO `user_info` VALUES ('11', '3', 'BACKPA…', '35', 'https://wx.qlogo.cn/mmopen/vi_32/DYAIOgq83ertuLYicnsiaekB4Libibvt2kYBKFicmOyAVQqmZUutCFnWGC0wRNMxiaQFN51icAfkZ2CAsAWR7tVsG34gA/0', '3572', '9050', '0', '150', '2', '636428', '984841', '青天外', '19680', '0', '0', '5210', '0', '258', '15485', '167', '127870', '780', '15', '0', '黄金斗师Ⅲ 2星', '0', '', '0', '', '', '', '', '120/120', '17/17');
INSERT INTO `user_info` VALUES ('12', '1', 'BACKPA…', '42', 'https://wx.qlogo.cn/mmopen/vi_32/DYAIOgq83ertuLYicnsiaekB4Libibvt2kYBKFicmOyAVQqmZUutCFnWGC0wRNMxiaQFN51icAfkZ2CAsAWR7tVsG34gA/0', '15139', '20500', '0', '4', '1', '6084512', '984841', '青天外', '31297', '3', '6', '1261', '0', '1361', '9579', '296', '91420', '600', '23', '0', '铂金斗尊Ⅱ 3星', '0', '黄金斗师Ⅲ 1星', '0', '', '', '', '', '120/120', '17/17');
INSERT INTO `user_info` VALUES ('13', '2', 'YuJun阳…', '42', 'https://wx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTJnv4h4j5tWy3y9l0vp2KMt96LxHbyibOWE5sdFbskDeyqOF22icC4stH2hcRichsWw5SWo0nVPTbspA/0', '19942', '20500', '0', '9', '2', '769448', '984841', '青天外', '32362', '2', '11', '1315', '0', '1954', '13589', '43595', '61180', '160', '23', '0', '铂金斗尊Ⅲ 6星', '0', '黄金斗师Ⅲ 1星', '0', '', '', '', '', '120/120', '17/17');
