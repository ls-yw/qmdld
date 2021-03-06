/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50633
Source Host           : 127.0.0.1:3306
Source Database       : qmdld

Target Server Type    : MYSQL
Target Server Version : 50633
File Encoding         : 65001

Date: 2019-02-16 18:14:18
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for goods
-- ----------------------------
DROP TABLE IF EXISTS `goods`;
CREATE TABLE `goods` (
  `id` int(10) unsigned NOT NULL,
  `name` varchar(20) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of goods
-- ----------------------------
INSERT INTO `goods` VALUES ('100001', '小体力药水');
INSERT INTO `goods` VALUES ('100002', '中体力药水');
INSERT INTO `goods` VALUES ('100003', '大体力药水');
INSERT INTO `goods` VALUES ('100006', '黄金卷轴');
INSERT INTO `goods` VALUES ('100012', '小威望旗');
INSERT INTO `goods` VALUES ('100013', '中威望旗');
INSERT INTO `goods` VALUES ('100014', '大威望旗');
INSERT INTO `goods` VALUES ('100015', '江湖令礼包');
INSERT INTO `goods` VALUES ('100016', '武林帖');
INSERT INTO `goods` VALUES ('100021', '大力丸');
INSERT INTO `goods` VALUES ('100022', '强健丸');
INSERT INTO `goods` VALUES ('100023', '迅捷丸');
INSERT INTO `goods` VALUES ('100024', '活血丸');
INSERT INTO `goods` VALUES ('100025', '经验药水');
INSERT INTO `goods` VALUES ('100026', '1级剑君攻魂');
INSERT INTO `goods` VALUES ('100036', '1级剑君守魂');
INSERT INTO `goods` VALUES ('100046', '1级帅帅暴魂');
INSERT INTO `goods` VALUES ('100056', '1级帅帅命魂');
INSERT INTO `goods` VALUES ('100066', '1级月敏反魂');
INSERT INTO `goods` VALUES ('100076', '1级月敏愈魂');
INSERT INTO `goods` VALUES ('100086', '1级聚魂之盒');
INSERT INTO `goods` VALUES ('100087', '中奖瓶盖');
INSERT INTO `goods` VALUES ('100088', '灵药葫芦');
INSERT INTO `goods` VALUES ('100096', '怒火精华');
INSERT INTO `goods` VALUES ('100097', '禅修精华');
INSERT INTO `goods` VALUES ('100098', '鹰眼精华');
INSERT INTO `goods` VALUES ('100099', '原水精华');
INSERT INTO `goods` VALUES ('100100', '葵花宝典');
INSERT INTO `goods` VALUES ('100114', '燃烧精华');
INSERT INTO `goods` VALUES ('100115', '高级燃烧精华');
INSERT INTO `goods` VALUES ('100116', '山岳精华');
INSERT INTO `goods` VALUES ('100117', '高级山岳精华');
INSERT INTO `goods` VALUES ('100121', '召唤水晶');
INSERT INTO `goods` VALUES ('100122', '风神之息');
INSERT INTO `goods` VALUES ('100123', '英雄江湖令礼包');
INSERT INTO `goods` VALUES ('100134', '致死精华');
INSERT INTO `goods` VALUES ('100135', '高级致死精华');
INSERT INTO `goods` VALUES ('100136', '极忍精华');
INSERT INTO `goods` VALUES ('100137', '高级极忍精华');
INSERT INTO `goods` VALUES ('100148', '大地结晶');
INSERT INTO `goods` VALUES ('100161', '天师精华');
INSERT INTO `goods` VALUES ('100163', '生命精华');

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
-- Table structure for lilian_reward
-- ----------------------------
DROP TABLE IF EXISTS `lilian_reward`;
CREATE TABLE `lilian_reward` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `dup` int(10) unsigned NOT NULL DEFAULT '0',
  `good_id` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(30) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of lilian_reward
-- ----------------------------
INSERT INTO `lilian_reward` VALUES ('1', '10001', '100124', '剑君攻魂碎片');
INSERT INTO `lilian_reward` VALUES ('2', '10001', '100128', '月敏反魂碎片');
INSERT INTO `lilian_reward` VALUES ('3', '10001', '100126', '帅帅暴魂碎片');
INSERT INTO `lilian_reward` VALUES ('4', '10001', '100131', '潜能果实碎片');
INSERT INTO `lilian_reward` VALUES ('5', '10001', '100125', '剑君守魂碎片');
INSERT INTO `lilian_reward` VALUES ('6', '10001', '100129', '月敏愈魂碎片');
INSERT INTO `lilian_reward` VALUES ('7', '10001', '100127', '帅帅命魂碎片');
INSERT INTO `lilian_reward` VALUES ('8', '10001', '100130', '召唤水晶碎片');

-- ----------------------------
-- Table structure for shops
-- ----------------------------
DROP TABLE IF EXISTS `shops`;
CREATE TABLE `shops` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `mark` varchar(20) NOT NULL DEFAULT '',
  `goods` text NOT NULL,
  `update_date` date NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=186 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of shops
-- ----------------------------
INSERT INTO `shops` VALUES ('1', 'pvp', '[{\"id\":100023,\"goods_id\":100006,\"name\":\"黄金卷轴\",\"price\":20,\"default_num\":1,\"max_num\":6,\"seq\":1,\"vip_level\":0,\"remain\":6,\"iconId\":100006,\"goods_des\":\"金光闪闪的卷轴，似乎隐藏着什么修炼秘诀…\",\"goods_effect\":\"可强化技能和武器，增强战斗实力。\"},{\"id\":100024,\"goods_id\":100021,\"name\":\"大力丸\",\"price\":10,\"default_num\":1,\"max_num\":5,\"seq\":1,\"vip_level\":0,\"remain\":5,\"iconId\":100021,\"goods_des\":\"江湖秘药，食之力大无穷！\",\"goods_effect\":\"使用获得5次大力状态(力量增加5+等级*0.4)；\"},{\"id\":100025,\"goods_id\":100022,\"name\":\"强健丸\",\"price\":10,\"default_num\":1,\"max_num\":5,\"seq\":1,\"vip_level\":0,\"remain\":5,\"iconId\":100022,\"goods_des\":\"江湖秘药，食之强身健体！\",\"goods_effect\":\"使用获得5次强健状态(筋骨增加5+等级*0.4)；\"},{\"id\":100026,\"goods_id\":100023,\"name\":\"迅捷丸\",\"price\":10,\"default_num\":1,\"max_num\":5,\"seq\":1,\"vip_level\":0,\"remain\":5,\"iconId\":100023,\"goods_des\":\"江湖秘药，食之快步如飞！\",\"goods_effect\":\"使用获得5次迅捷状态(速度增加5+等级*0.4)；\"},{\"id\":100027,\"goods_id\":100024,\"name\":\"活血丸\",\"price\":10,\"default_num\":1,\"max_num\":5,\"seq\":1,\"vip_level\":0,\"remain\":5,\"iconId\":100024,\"goods_des\":\"江湖秘药，食之活血化瘀！\",\"goods_effect\":\"使用获得5次活血状态(生命增加500+等级*30)；\"},{\"id\":100028,\"goods_id\":100087,\"name\":\"中奖瓶盖\",\"price\":300,\"default_num\":1,\"max_num\":1,\"seq\":1,\"vip_level\":0,\"remain\":1,\"iconId\":100087,\"goods_des\":\"盖子里面的小字写着“再来一次”。\",\"goods_effect\":\"可以重置所有的自由点数重新分配。\"}]', '2018-02-23');
INSERT INTO `shops` VALUES ('172', 'servant', '[{\"id\":100029,\"goods_id\":100012,\"name\":\"小威望旗\",\"price\":200,\"default_num\":1,\"max_num\":3,\"seq\":5,\"vip_level\":0,\"remain\":3,\"iconId\":100012,\"goods_des\":\"小门派的旗子，有一定的号召力…\",\"goods_effect\":\"使用获得600点威望。（每日最多可购买40个）\"},{\"id\":100030,\"goods_id\":100088,\"name\":\"灵药葫芦\",\"price\":100,\"default_num\":1,\"max_num\":5,\"seq\":6,\"vip_level\":0,\"remain\":5,\"iconId\":100088,\"goods_des\":\"葫芦里到底卖的什么药？\",\"goods_effect\":\"可以随机获得一枚增强属性的丹药。\"},{\"id\":100031,\"goods_id\":100006,\"name\":\"黄金卷轴\",\"price\":100,\"default_num\":1,\"max_num\":4,\"seq\":1,\"vip_level\":0,\"remain\":4,\"iconId\":100006,\"goods_des\":\"金光闪闪的卷轴，似乎隐藏着什么修炼秘诀…\",\"goods_effect\":\"可强化技能和武器，增强战斗实力。\"},{\"id\":100032,\"goods_id\":100096,\"name\":\"怒火精华\",\"price\":200,\"default_num\":1,\"max_num\":4,\"seq\":3,\"vip_level\":0,\"remain\":4,\"iconId\":100096,\"goods_des\":\"一团实体化的怒火。学会控制愤怒，不要被愤怒支配。\",\"goods_effect\":\"可用于强化怒吼密卷中的连锁和技能。\"},{\"id\":100033,\"goods_id\":100097,\"name\":\"禅修精华\",\"price\":200,\"default_num\":1,\"max_num\":4,\"seq\":4,\"vip_level\":0,\"remain\":4,\"iconId\":100097,\"goods_des\":\"空即是色，色即是空。无欲无求的心境让你更加强大。\",\"goods_effect\":\"可用于强化禅定密卷中的连锁和技能。\"},{\"id\":100044,\"goods_id\":100025,\"name\":\"经验药水\",\"price\":500,\"default_num\":1,\"max_num\":1,\"seq\":7,\"vip_level\":0,\"remain\":1,\"iconId\":100025,\"goods_des\":\"江湖神药，服用后切磋练功事半功倍！\",\"goods_effect\":\"使用后24小时内获得聪慧状态(好友或斗友的切磋经验增加10%)；\"},{\"id\":100112,\"goods_id\":100120,\"name\":\"潜能果实\",\"price\":100,\"default_num\":1,\"max_num\":5,\"seq\":2,\"vip_level\":0,\"remain\":5,\"iconId\":100120,\"goods_des\":\"召唤兽有强大的潜力等待发觉，食用潜能果实就是最好的方式。\",\"goods_effect\":\"可以激活所有召唤兽的潜能。\"}]', '2018-02-23');
INSERT INTO `shops` VALUES ('184', 'qualifying', '[{\"id\":100049,\"goods_id\":100114,\"name\":\"燃烧精华\",\"price\":200,\"default_num\":1,\"max_num\":20,\"seq\":3,\"vip_level\":0,\"remain\":20,\"iconId\":100114,\"goods_des\":\"被包裹在琥珀中的火焰之花，表面还可以感受到它的热量。\",\"goods_effect\":\"可用于强化烈焰密卷中的连锁和技能。\"},{\"id\":100050,\"goods_id\":100116,\"name\":\"山岳精华\",\"price\":200,\"default_num\":1,\"max_num\":20,\"seq\":4,\"vip_level\":0,\"remain\":20,\"iconId\":100116,\"goods_des\":\"坚硬无比的玉石，被誉为山之脊髓。\",\"goods_effect\":\"可用于强化屏障密卷中的连锁和技能。\"},{\"id\":100051,\"goods_id\":100115,\"name\":\"高级燃烧精华\",\"price\":400,\"default_num\":1,\"max_num\":8,\"seq\":7,\"vip_level\":0,\"remain\":8,\"iconId\":100115,\"goods_des\":\"燃烧精华中难得一遇的精品，是深造密卷的绝佳之物。\",\"goods_effect\":\"可用于强化烈焰密卷中的10级以上的连锁和技能。\"},{\"id\":100052,\"goods_id\":100117,\"name\":\"高级山岳精华\",\"price\":400,\"default_num\":1,\"max_num\":8,\"seq\":8,\"vip_level\":0,\"remain\":8,\"iconId\":100117,\"goods_des\":\"山岳精华中难得一遇的精品，是深造密卷的绝佳之物。\",\"goods_effect\":\"可用于强化屏障密卷中的10级以上的连锁和技能。\"},{\"id\":100053,\"goods_id\":100015,\"name\":\"江湖令礼包\",\"price\":50,\"default_num\":1,\"max_num\":99,\"seq\":11,\"vip_level\":0,\"remain\":20,\"iconId\":100007,\"goods_des\":\"江湖信物，走南闯北必备。\",\"goods_effect\":\"使用获得1个江湖令，用于江湖历练消耗。\"},{\"id\":100054,\"goods_id\":100012,\"name\":\"小威望旗\",\"price\":200,\"default_num\":1,\"max_num\":99,\"seq\":12,\"vip_level\":0,\"remain\":3,\"iconId\":100012,\"goods_des\":\"小门派的旗子，有一定的号召力…\",\"goods_effect\":\"使用获得600点威望。（每日最多可购买40个）\"},{\"id\":100055,\"goods_id\":100088,\"name\":\"灵药葫芦\",\"price\":100,\"default_num\":1,\"max_num\":99,\"seq\":13,\"vip_level\":0,\"remain\":40,\"iconId\":100088,\"goods_des\":\"葫芦里到底卖的什么药？\",\"goods_effect\":\"可以随机获得一枚增强属性的丹药。\"},{\"id\":100056,\"goods_id\":100086,\"name\":\"1级聚魂之盒\",\"price\":500,\"default_num\":1,\"max_num\":1,\"seq\":14,\"vip_level\":0,\"remain\":1,\"iconId\":100011,\"goods_des\":\"万物之魂灵，随缘而聚！\",\"goods_effect\":\"打开随机获得一颗1级魂珠……\"},{\"id\":100076,\"goods_id\":100122,\"name\":\"风神之息\",\"price\":500,\"default_num\":1,\"max_num\":8,\"seq\":1,\"vip_level\":0,\"remain\":0,\"iconId\":100122,\"goods_des\":\"一团缥缈的旋转的气流，蕴藏着神秘的未知力量。\",\"goods_effect\":\"可以强化召唤兽苍龙的圣物。\"},{\"id\":100077,\"goods_id\":100134,\"name\":\"致死精华\",\"price\":200,\"default_num\":1,\"max_num\":20,\"seq\":5,\"vip_level\":0,\"remain\":20,\"iconId\":100134,\"goods_des\":\"传说中是巫毒一族用来与死神沟通的灵媒。\",\"goods_effect\":\"可用于强化恶疾密卷中的连锁和技能。\"},{\"id\":100078,\"goods_id\":100136,\"name\":\"极忍精华\",\"price\":200,\"default_num\":1,\"max_num\":20,\"seq\":6,\"vip_level\":0,\"remain\":20,\"iconId\":100136,\"goods_des\":\"具有极强的意志，才不会被轻易击倒！\",\"goods_effect\":\"可用于强化根性密卷中的连锁和技能。\"},{\"id\":100079,\"goods_id\":100135,\"name\":\"高级致死精华\",\"price\":400,\"default_num\":1,\"max_num\":8,\"seq\":9,\"vip_level\":0,\"remain\":8,\"iconId\":100135,\"goods_des\":\"致死精华中难得一遇的精品，是深造密卷的绝佳之物。\",\"goods_effect\":\"可用于强化恶疾密卷中的10级以上的连锁和技能。\"},{\"id\":100080,\"goods_id\":100137,\"name\":\"高级极忍精华\",\"price\":400,\"default_num\":1,\"max_num\":8,\"seq\":10,\"vip_level\":0,\"remain\":8,\"iconId\":100137,\"goods_des\":\"极忍精华中难得一遇的精品，是深造密卷的绝佳之物。\",\"goods_effect\":\"可用于强化根性密卷中的10级以上的连锁和技能。\"},{\"id\":100093,\"goods_id\":100148,\"name\":\"大地结晶\",\"price\":500,\"default_num\":1,\"max_num\":8,\"seq\":2,\"vip_level\":0,\"remain\":0,\"iconId\":100148,\"goods_des\":\"表面看来是平庸无奇的石块，蕴藏其内的却是大地母亲馈赠的厚礼。\",\"goods_effect\":\"可以强化召唤兽白虎的圣物。\"}]', '2018-02-23');
INSERT INTO `shops` VALUES ('185', 'doushen', '[{\"id\":100105,\"goods_id\":100173,\"name\":\"地煞符\",\"price\":100,\"default_num\":1,\"max_num\":30,\"seq\":1,\"vip_level\":0,\"remain\":30,\"iconId\":100173,\"goods_des\":\"召唤地煞冥火，阅后即焚！\",\"goods_effect\":\"可精炼普通武器或技能。\"},{\"id\":100106,\"goods_id\":100174,\"name\":\"天罡符\",\"price\":200,\"default_num\":1,\"max_num\":6,\"seq\":2,\"vip_level\":0,\"remain\":6,\"iconId\":100174,\"goods_des\":\"召唤天罡玄火，阅后即焚！\",\"goods_effect\":\"可精炼优良武器或技能。\"},{\"id\":100107,\"goods_id\":100159,\"name\":\"不灭火种\",\"price\":500,\"default_num\":1,\"max_num\":8,\"seq\":3,\"vip_level\":0,\"remain\":0,\"iconId\":100159,\"goods_des\":\"风吹不灭的火种，在任何恶劣的绝境中都给人带来希望和光明。\",\"goods_effect\":\"可以强化召唤兽朱雀的圣物。\"},{\"id\":100108,\"goods_id\":100015,\"name\":\"江湖令礼包\",\"price\":50,\"default_num\":1,\"max_num\":20,\"seq\":4,\"vip_level\":0,\"remain\":20,\"iconId\":100007,\"goods_des\":\"江湖信物，走南闯北必备。\",\"goods_effect\":\"使用获得1个江湖令，用于江湖历练消耗。\"},{\"id\":100109,\"goods_id\":100012,\"name\":\"小威望旗\",\"price\":200,\"default_num\":1,\"max_num\":3,\"seq\":5,\"vip_level\":0,\"remain\":3,\"iconId\":100012,\"goods_des\":\"小门派的旗子，有一定的号召力…\",\"goods_effect\":\"使用获得600点威望。（每日最多可购买40个）\"},{\"id\":100110,\"goods_id\":100088,\"name\":\"灵药葫芦\",\"price\":100,\"default_num\":1,\"max_num\":40,\"seq\":6,\"vip_level\":0,\"remain\":40,\"iconId\":100088,\"goods_des\":\"葫芦里到底卖的什么药？\",\"goods_effect\":\"可以随机获得一枚增强属性的丹药。\"},{\"id\":100111,\"goods_id\":100086,\"name\":\"1级聚魂之盒\",\"price\":500,\"default_num\":1,\"max_num\":1,\"seq\":7,\"vip_level\":0,\"remain\":1,\"iconId\":100011,\"goods_des\":\"万物之魂灵，随缘而聚！\",\"goods_effect\":\"打开随机获得一颗1级魂珠……\"}]', '2018-02-23');

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
  `project` varchar(20) NOT NULL DEFAULT '',
  `auto` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of user
-- ----------------------------
INSERT INTO `user` VALUES ('1', 'yls', '6084512', 'o8FFywJcAMLAEAo-1Xh7YkIlR0dI', 'oKIwA0eHZyXEDaUICvhtyE8EJuts', '178c07a6117d5c7d7139a46db7456f4c', 'dld', '1');
INSERT INTO `user` VALUES ('2', 'yj', '769448', '', 'oKIwA0aGacUIRZjEHNXgzQvT65CA', '', 'dld', '1');
INSERT INTO `user` VALUES ('3', 'ylsxh', '636428', '', 'oKIwA0eHZyXEDaUICvhtyE8EJuts', '', 'dld', '1');
INSERT INTO `user` VALUES ('4', 'ylszyhx', '6460484098682207206', '', 'f40b80b85b7ec31b0c3d4486b02e18cd', 'f40b80b85b7ec31b0c3d4486b02e18cd', 'zyhx', '1');

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
  `pvp_shop` varchar(200) NOT NULL DEFAULT '',
  `master_auto` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '自动敬茶/解惑 0 不使用 1使用',
  `servant_auto` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '收家财 0 不使用 1使用',
  `servant_rob` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '捣乱 0 不自动 1自动',
  `servant_train` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '训练 0 不自动 1自动',
  `servant_catch` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '捕捉 0 不自动 1自动',
  `servant_release` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '释放 0 不自动 1自动',
  `servant_shop` varchar(200) NOT NULL DEFAULT '',
  `faction_auto` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '捐献 0 不使用 1使用',
  `faction_club` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '武馆 0 不使用 1使用',
  `qualifying_person` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '个人王者争霸赛 0 不使用 1使用',
  `qualifying_team` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '团队王者争霸赛 0 不使用 1使用',
  `qualifying_doushen` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '自动斗神 0 不自动 1自动',
  `qualifying_doushen_type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '斗神方式 0 爬榜 1胜利',
  `qualifying_shop` varchar(200) NOT NULL DEFAULT '',
  `doushen_shop` varchar(200) NOT NULL DEFAULT '',
  `hangup_equip` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '游历装备 0 不自动 1自动',
  `hangup_box` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '游历使用宝箱 0 不自动 1自动',
  `hangup_encourage` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '游历互动 0 不自动 1自动',
  `hangup_fight` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '游历挑战 0 不自动 1自动',
  `lilian_ordinary` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '普通历练 0 不自动 1自动',
  `lilian_ordinary_type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '普通历练类型 1爬楼 2经验 3满星',
  `lilian_used` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '使用令牌礼包',
  `lilian_hero_ordinary` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '英雄历练 0 不自动 1自动',
  `lilian_hero_ordinary_type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '英雄历练类型 1爬楼 2物品 3满星',
  `lilian_hero_ordinary_goods` varchar(200) NOT NULL DEFAULT '' COMMENT '英雄历练物品',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of user_config
-- ----------------------------
INSERT INTO `user_config` VALUES ('1', '1', '1', '1', '1', '1', '1', '1', '1', '100023', '1', '1', '1', '1', '1', '0', '100029,100031,100112', '1', '1', '1', '1', '1', '0', '100054,100056,100076,100093', '100105,100107', '1', '1', '1', '1', '1', '1', '1', '1', '2', '100130');
INSERT INTO `user_config` VALUES ('2', '2', '1', '1', '1', '1', '1', '1', '1', '100023', '1', '1', '1', '1', '1', '0', '100031,100112', '1', '1', '1', '1', '1', '0', '100054,100056,100076,100093', '100105,100107', '1', '1', '1', '1', '1', '1', '1', '1', '2', '100130');
INSERT INTO `user_config` VALUES ('3', '3', '1', '1', '1', '2', '1', '1', '1', '100023', '1', '1', '1', '1', '1', '0', '100029,100031,100112', '1', '1', '1', '1', '1', '0', '100054,100056,100076,100093', '100105,100107', '1', '1', '1', '1', '1', '2', '1', '1', '2', '100130,100126,100124');

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
  `fac_cave` varchar(20) NOT NULL DEFAULT '' COMMENT '洞穴',
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
  `doushen` int(8) unsigned NOT NULL DEFAULT '0' COMMENT '斗神',
  `lilian_num` smallint(4) unsigned NOT NULL DEFAULT '0' COMMENT '历练',
  `hero_lilian_num` smallint(4) unsigned NOT NULL DEFAULT '0' COMMENT '英雄历练',
  `tower` varchar(10) NOT NULL DEFAULT '' COMMENT '千层塔',
  `marry_hangup` varchar(10) NOT NULL DEFAULT '' COMMENT '游历',
  `unlock_page` varchar(10) NOT NULL DEFAULT '' COMMENT '72变',
  `unlock_scene` varchar(10) NOT NULL DEFAULT '' COMMENT '场景解锁',
  `unlock_weapon` varchar(10) NOT NULL DEFAULT '' COMMENT '武器',
  `unlock_skill` varchar(10) NOT NULL DEFAULT '' COMMENT '技能',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of user_info
-- ----------------------------
INSERT INTO `user_info` VALUES ('11', '3', 'BACKPA…', '44', 'https://wx.qlogo.cn/mmopen/vi_32/DYAIOgq83ertuLYicnsiaekB4Libibvt2kYBKFicmOyAVQqmZUutCFnWGC0wRNMxiaQFN51icAfkZ2CAsAWR7tVsG34gA/0', '557', '25600', '0', '1', '2', '636428', '984841', '青天外', '3/3', '39846', '2', '0', '4760', '0', '2177', '6355', '284', '634780', '300', '42', '0', '铂金斗尊Ⅱ 0星', '0', '黄金斗师Ⅳ 4星', '0', '0', '0', '0', '5-8', '', '120/120', '17/17', '14/22/90', '4/5/62');
INSERT INTO `user_info` VALUES ('12', '1', 'BACKPA…', '50', 'https://wx.qlogo.cn/mmopen/vi_32/DYAIOgq83ertuLYicnsiaekB4Libibvt2kYBKFicmOyAVQqmZUutCFnWGC0wRNMxiaQFN51icAfkZ2CAsAWR7tVsG34gA/0', '46185', '48350', '0', '31', '1', '6084512', '984841', '青天外', '3/3', '53935', '3', '8', '3816', '0', '1008', '1736', '231', '140320', '700', '52', '0', '钻石斗皇Ⅳ 0星', '0', '钻石斗皇V 7星', '0', '5429', '2', '0', '7-5', '', '120/120', '17/17', '27/28/90', '10/10/62');
INSERT INTO `user_info` VALUES ('13', '2', 'YuJun阳…', '50', 'https://wx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTJnv4h4j5tWy3y9l0vp2KMt96LxHbyibOWE5sdFbskDeyqOF22icC4stH2hcRichsWw5SWo0nVPTbspA/0', '39659', '48350', '0', '8', '2', '769448', '984841', '青天外', '0/3', '53505', '2', '11', '2355', '0', '899', '9959', '216', '226840', '40', '52', '0', '钻石斗皇V 4星', '0', '钻石斗皇V 7星', '0', '6210', '0', '0', '6-20', '', '120/120', '17/17', '30/33/90', '7/7/62');
