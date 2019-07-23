/*
Navicat MySQL Data Transfer

Source Server         : 192.168.0.13_3306
Source Server Version : 50558
Source Host           : 192.168.0.13:3306
Source Database       : xiaobao

Target Server Type    : MYSQL
Target Server Version : 50558
File Encoding         : 65001

Date: 2019-07-23 16:19:25
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for bitauto_brand_index
-- ----------------------------
DROP TABLE IF EXISTS `bitauto_brand_index`;
CREATE TABLE `bitauto_brand_index` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `asnum` varchar(10) NOT NULL DEFAULT '' COMMENT '字母序号',
  `cname` varchar(50) NOT NULL DEFAULT '' COMMENT '名称',
  `img` varchar(100) NOT NULL DEFAULT '' COMMENT '图片地址',
  `link` varchar(100) NOT NULL DEFAULT '' COMMENT '链接',
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=161 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for bitauto_car_list
-- ----------------------------
DROP TABLE IF EXISTS `bitauto_car_list`;
CREATE TABLE `bitauto_car_list` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `brand_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '品牌id',
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '车型名称',
  `img` varchar(200) NOT NULL DEFAULT '' COMMENT '图片地址(车型小图)',
  `cover_img` varchar(200) NOT NULL DEFAULT '' COMMENT '封面图片',
  `link` varchar(100) NOT NULL DEFAULT '' COMMENT '链接',
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2702 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for card_action_logs
-- ----------------------------
DROP TABLE IF EXISTS `card_action_logs`;
CREATE TABLE `card_action_logs` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `user_id` int(10) NOT NULL COMMENT '登录账号id',
  `business` tinyint(3) NOT NULL DEFAULT '0' COMMENT '业务类型',
  `table` varchar(255) NOT NULL DEFAULT '' COMMENT '操作表对象',
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '操作类型：增1改2删3',
  `ip` varchar(25) NOT NULL DEFAULT '0' COMMENT 'ip地址',
  `comment` text NOT NULL COMMENT '操作说明',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `idx_userid` (`user_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=66 DEFAULT CHARSET=utf8 COMMENT='幸福卡操作日志表 auto colin';

-- ----------------------------
-- Table structure for ecs_account_log
-- ----------------------------
DROP TABLE IF EXISTS `ecs_account_log`;
CREATE TABLE `ecs_account_log` (
  `log_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(8) unsigned NOT NULL,
  `user_money` decimal(10,2) NOT NULL,
  `frozen_money` decimal(10,2) NOT NULL,
  `rank_points` mediumint(9) NOT NULL,
  `pay_points` mediumint(9) NOT NULL,
  `change_time` int(10) unsigned NOT NULL,
  `change_desc` varchar(255) NOT NULL,
  `change_type` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`log_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=20868 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_account_log_bonus
-- ----------------------------
DROP TABLE IF EXISTS `ecs_account_log_bonus`;
CREATE TABLE `ecs_account_log_bonus` (
  `logb_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(8) unsigned NOT NULL,
  `order_sn` varchar(100) NOT NULL DEFAULT '',
  `stores_id` int(10) NOT NULL,
  `bonus_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '礼品卡的id',
  `user_money` decimal(10,2) NOT NULL,
  `change_time` int(10) unsigned NOT NULL,
  `change_desc` varchar(255) NOT NULL,
  `is_refund` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '是否退款,1是',
  PRIMARY KEY (`logb_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=20473 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_ad
-- ----------------------------
DROP TABLE IF EXISTS `ecs_ad`;
CREATE TABLE `ecs_ad` (
  `ad_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `position_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `media_type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `ad_name` varchar(60) NOT NULL DEFAULT '',
  `ad_link` varchar(255) NOT NULL DEFAULT '',
  `wechat_link` varchar(100) NOT NULL DEFAULT '' COMMENT '小程序链接',
  `ad_code` text NOT NULL,
  `start_time` int(11) NOT NULL DEFAULT '0',
  `end_time` int(11) NOT NULL DEFAULT '0',
  `link_man` varchar(60) NOT NULL DEFAULT '',
  `link_email` varchar(60) NOT NULL DEFAULT '',
  `link_phone` varchar(60) NOT NULL DEFAULT '',
  `click_count` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `enabled` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `city` varchar(100) NOT NULL,
  `details` varchar(200) NOT NULL DEFAULT '' COMMENT '详情介绍',
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  PRIMARY KEY (`ad_id`),
  KEY `position_id` (`position_id`),
  KEY `enabled` (`enabled`)
) ENGINE=MyISAM AUTO_INCREMENT=86 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_ad_custom
-- ----------------------------
DROP TABLE IF EXISTS `ecs_ad_custom`;
CREATE TABLE `ecs_ad_custom` (
  `ad_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `ad_type` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `ad_name` varchar(60) DEFAULT NULL,
  `add_time` int(10) unsigned NOT NULL DEFAULT '0',
  `content` mediumtext,
  `url` varchar(255) DEFAULT NULL,
  `ad_status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`ad_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_ad_position
-- ----------------------------
DROP TABLE IF EXISTS `ecs_ad_position`;
CREATE TABLE `ecs_ad_position` (
  `position_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `position_name` varchar(60) NOT NULL DEFAULT '',
  `ad_width` smallint(5) unsigned NOT NULL DEFAULT '0',
  `ad_height` smallint(5) unsigned NOT NULL DEFAULT '0',
  `position_desc` varchar(255) NOT NULL DEFAULT '',
  `position_style` text NOT NULL,
  PRIMARY KEY (`position_id`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_admin_action
-- ----------------------------
DROP TABLE IF EXISTS `ecs_admin_action`;
CREATE TABLE `ecs_admin_action` (
  `action_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `action_code` varchar(20) NOT NULL DEFAULT '',
  `relevance` varchar(20) NOT NULL DEFAULT '',
  PRIMARY KEY (`action_id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=MyISAM AUTO_INCREMENT=157 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_admin_log
-- ----------------------------
DROP TABLE IF EXISTS `ecs_admin_log`;
CREATE TABLE `ecs_admin_log` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `log_time` int(10) unsigned NOT NULL DEFAULT '0',
  `user_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `log_info` varchar(255) NOT NULL DEFAULT '',
  `ip_address` varchar(15) NOT NULL DEFAULT '',
  `user_name` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`log_id`),
  KEY `log_time` (`log_time`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=25210 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_admin_message
-- ----------------------------
DROP TABLE IF EXISTS `ecs_admin_message`;
CREATE TABLE `ecs_admin_message` (
  `message_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `sender_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `receiver_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `sent_time` int(11) unsigned NOT NULL DEFAULT '0',
  `read_time` int(11) unsigned NOT NULL DEFAULT '0',
  `readed` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `deleted` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `title` varchar(150) NOT NULL DEFAULT '',
  `message` text NOT NULL,
  PRIMARY KEY (`message_id`),
  KEY `sender_id` (`sender_id`,`receiver_id`),
  KEY `receiver_id` (`receiver_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_admin_user
-- ----------------------------
DROP TABLE IF EXISTS `ecs_admin_user`;
CREATE TABLE `ecs_admin_user` (
  `user_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `user_name` varchar(60) NOT NULL DEFAULT '',
  `email` varchar(60) NOT NULL DEFAULT '',
  `password` varchar(32) NOT NULL DEFAULT '',
  `ec_salt` varchar(10) DEFAULT NULL,
  `add_time` int(11) NOT NULL DEFAULT '0',
  `last_login` int(11) NOT NULL DEFAULT '0',
  `last_ip` varchar(15) NOT NULL DEFAULT '',
  `action_list` text NOT NULL,
  `nav_list` text NOT NULL,
  `lang_type` varchar(50) NOT NULL DEFAULT '',
  `agency_id` smallint(5) unsigned NOT NULL,
  `suppliers_id` smallint(5) unsigned DEFAULT '0',
  `todolist` longtext,
  `role_id` smallint(5) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  KEY `user_name` (`user_name`),
  KEY `agency_id` (`agency_id`)
) ENGINE=MyISAM AUTO_INCREMENT=29 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_adsense
-- ----------------------------
DROP TABLE IF EXISTS `ecs_adsense`;
CREATE TABLE `ecs_adsense` (
  `from_ad` smallint(5) NOT NULL DEFAULT '0',
  `referer` varchar(255) NOT NULL DEFAULT '',
  `clicks` int(10) unsigned NOT NULL DEFAULT '0',
  KEY `from_ad` (`from_ad`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_affiliate_log
-- ----------------------------
DROP TABLE IF EXISTS `ecs_affiliate_log`;
CREATE TABLE `ecs_affiliate_log` (
  `log_id` mediumint(8) NOT NULL AUTO_INCREMENT,
  `order_id` mediumint(8) NOT NULL,
  `time` int(10) NOT NULL,
  `user_id` mediumint(8) NOT NULL,
  `user_name` varchar(60) DEFAULT NULL,
  `money` decimal(10,2) NOT NULL DEFAULT '0.00',
  `point` int(10) NOT NULL DEFAULT '0',
  `separate_type` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`log_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_agency
-- ----------------------------
DROP TABLE IF EXISTS `ecs_agency`;
CREATE TABLE `ecs_agency` (
  `agency_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `agency_name` varchar(255) NOT NULL,
  `agency_desc` text NOT NULL,
  PRIMARY KEY (`agency_id`),
  KEY `agency_name` (`agency_name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_area_region
-- ----------------------------
DROP TABLE IF EXISTS `ecs_area_region`;
CREATE TABLE `ecs_area_region` (
  `shipping_area_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `region_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`shipping_area_id`,`region_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_article
-- ----------------------------
DROP TABLE IF EXISTS `ecs_article`;
CREATE TABLE `ecs_article` (
  `article_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `cat_id` smallint(5) NOT NULL DEFAULT '0',
  `title` varchar(150) NOT NULL DEFAULT '',
  `content` longtext NOT NULL,
  `author` varchar(30) NOT NULL DEFAULT '',
  `author_email` varchar(60) NOT NULL DEFAULT '',
  `keywords` varchar(255) NOT NULL DEFAULT '',
  `article_type` tinyint(1) unsigned NOT NULL DEFAULT '2',
  `is_open` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `add_time` int(10) unsigned NOT NULL DEFAULT '0',
  `file_url` varchar(255) NOT NULL DEFAULT '',
  `open_type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `link` varchar(255) NOT NULL DEFAULT '',
  `description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`article_id`),
  KEY `cat_id` (`cat_id`)
) ENGINE=MyISAM AUTO_INCREMENT=42 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_article_cat
-- ----------------------------
DROP TABLE IF EXISTS `ecs_article_cat`;
CREATE TABLE `ecs_article_cat` (
  `cat_id` smallint(5) NOT NULL AUTO_INCREMENT,
  `cat_name` varchar(255) NOT NULL DEFAULT '',
  `cat_type` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `img` varchar(255) NOT NULL DEFAULT '' COMMENT '图标地址',
  `keywords` varchar(255) NOT NULL DEFAULT '',
  `cat_desc` varchar(255) NOT NULL DEFAULT '',
  `sort_order` tinyint(3) unsigned NOT NULL DEFAULT '50',
  `show_in_nav` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `parent_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`cat_id`),
  KEY `cat_type` (`cat_type`),
  KEY `sort_order` (`sort_order`),
  KEY `parent_id` (`parent_id`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_attribute
-- ----------------------------
DROP TABLE IF EXISTS `ecs_attribute`;
CREATE TABLE `ecs_attribute` (
  `attr_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `cat_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `attr_name` varchar(60) NOT NULL DEFAULT '',
  `attr_input_type` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `attr_type` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `attr_values` text NOT NULL,
  `attr_index` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `sort_order` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `is_linked` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `attr_group` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`attr_id`),
  KEY `cat_id` (`cat_id`)
) ENGINE=MyISAM AUTO_INCREMENT=223 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_auction_log
-- ----------------------------
DROP TABLE IF EXISTS `ecs_auction_log`;
CREATE TABLE `ecs_auction_log` (
  `log_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `act_id` mediumint(8) unsigned NOT NULL,
  `bid_user` mediumint(8) unsigned NOT NULL,
  `bid_price` decimal(10,2) unsigned NOT NULL,
  `bid_time` int(10) unsigned NOT NULL,
  PRIMARY KEY (`log_id`),
  KEY `act_id` (`act_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_auto_manage
-- ----------------------------
DROP TABLE IF EXISTS `ecs_auto_manage`;
CREATE TABLE `ecs_auto_manage` (
  `item_id` mediumint(8) NOT NULL,
  `type` varchar(10) NOT NULL,
  `starttime` int(10) NOT NULL,
  `endtime` int(10) NOT NULL,
  PRIMARY KEY (`item_id`,`type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_axprivate
-- ----------------------------
DROP TABLE IF EXISTS `ecs_axprivate`;
CREATE TABLE `ecs_axprivate` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `private_num` varchar(20) NOT NULL DEFAULT '' COMMENT '中间号码',
  `orig_num` varchar(20) NOT NULL DEFAULT '' COMMENT '原始号码',
  `subscription_id` varchar(50) NOT NULL DEFAULT '' COMMENT '绑定之后的id',
  `bind_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '绑定时间',
  `unbind_time` varchar(20) NOT NULL DEFAULT '' COMMENT '解绑时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_pnum` (`private_num`),
  KEY `idx_orignum` (`orig_num`),
  KEY `subscription_id` (`subscription_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='隐号功能表';

-- ----------------------------
-- Table structure for ecs_axprivate_log
-- ----------------------------
DROP TABLE IF EXISTS `ecs_axprivate_log`;
CREATE TABLE `ecs_axprivate_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `private_num` varchar(20) NOT NULL DEFAULT '' COMMENT '中间号码',
  `orig_num` varchar(20) NOT NULL DEFAULT '' COMMENT '原始号码',
  `subscription_id` varchar(50) NOT NULL DEFAULT '' COMMENT '绑定之后的id',
  `bind_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '绑定时间',
  `unbind_time` varchar(20) NOT NULL DEFAULT '' COMMENT '解绑时间',
  PRIMARY KEY (`id`),
  KEY `subscription_id` (`subscription_id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8 COMMENT='隐号功能表';

-- ----------------------------
-- Table structure for ecs_back_goods
-- ----------------------------
DROP TABLE IF EXISTS `ecs_back_goods`;
CREATE TABLE `ecs_back_goods` (
  `rec_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `back_id` mediumint(8) unsigned DEFAULT '0',
  `goods_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `product_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `product_sn` varchar(60) DEFAULT NULL,
  `goods_name` varchar(120) DEFAULT NULL,
  `brand_name` varchar(60) DEFAULT NULL,
  `goods_sn` varchar(60) DEFAULT NULL,
  `is_real` tinyint(1) unsigned DEFAULT '0',
  `send_number` smallint(5) unsigned DEFAULT '0',
  `goods_attr` text,
  PRIMARY KEY (`rec_id`),
  KEY `back_id` (`back_id`),
  KEY `goods_id` (`goods_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_back_order
-- ----------------------------
DROP TABLE IF EXISTS `ecs_back_order`;
CREATE TABLE `ecs_back_order` (
  `back_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `delivery_sn` varchar(20) NOT NULL,
  `order_sn` varchar(20) NOT NULL,
  `order_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `invoice_no` varchar(50) DEFAULT NULL,
  `add_time` int(10) unsigned DEFAULT '0',
  `shipping_id` tinyint(3) unsigned DEFAULT '0',
  `shipping_name` varchar(120) DEFAULT NULL,
  `user_id` mediumint(8) unsigned DEFAULT '0',
  `action_user` varchar(30) DEFAULT NULL,
  `consignee` varchar(60) DEFAULT NULL,
  `address` varchar(250) DEFAULT NULL,
  `country` smallint(5) unsigned DEFAULT '0',
  `province` smallint(5) unsigned DEFAULT '0',
  `city` smallint(5) unsigned DEFAULT '0',
  `district` smallint(5) unsigned DEFAULT '0',
  `sign_building` varchar(120) DEFAULT NULL,
  `email` varchar(60) DEFAULT NULL,
  `zipcode` varchar(60) DEFAULT NULL,
  `tel` varchar(60) DEFAULT NULL,
  `mobile` varchar(60) DEFAULT NULL,
  `best_time` varchar(120) DEFAULT NULL,
  `postscript` varchar(255) DEFAULT NULL,
  `how_oos` varchar(120) DEFAULT NULL,
  `insure_fee` decimal(10,2) unsigned DEFAULT '0.00',
  `shipping_fee` decimal(10,2) unsigned DEFAULT '0.00',
  `update_time` int(10) unsigned DEFAULT '0',
  `suppliers_id` smallint(5) DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `return_time` int(10) unsigned DEFAULT '0',
  `agency_id` smallint(5) unsigned DEFAULT '0',
  PRIMARY KEY (`back_id`),
  KEY `user_id` (`user_id`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_bonus_count
-- ----------------------------
DROP TABLE IF EXISTS `ecs_bonus_count`;
CREATE TABLE `ecs_bonus_count` (
  `bt_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `bt_num` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`bt_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_bonus_order
-- ----------------------------
DROP TABLE IF EXISTS `ecs_bonus_order`;
CREATE TABLE `ecs_bonus_order` (
  `bo_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `bonus_type_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '充值卡id',
  `bonus_order_sn` bigint(20) unsigned NOT NULL DEFAULT '0',
  `user_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `addtime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `bonus_amount` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '卡金额',
  `pay_id` int(10) NOT NULL DEFAULT '0' COMMENT '支付方式',
  `pay_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '支付状态 0未支付 1已支付',
  `ad_id` int(10) NOT NULL DEFAULT '0' COMMENT '卡样ID',
  `bo_randomNum` varchar(100) NOT NULL DEFAULT '',
  `bo_bouns_id` int(10) NOT NULL DEFAULT '0',
  PRIMARY KEY (`bo_id`),
  UNIQUE KEY `bosn` (`bonus_order_sn`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1878 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_bonus_type
-- ----------------------------
DROP TABLE IF EXISTS `ecs_bonus_type`;
CREATE TABLE `ecs_bonus_type` (
  `type_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `type_name` varchar(60) NOT NULL DEFAULT '',
  `type_money` decimal(10,2) NOT NULL DEFAULT '0.00',
  `send_type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `min_amount` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `max_amount` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `send_start_date` int(11) NOT NULL DEFAULT '0',
  `send_end_date` int(11) NOT NULL DEFAULT '0',
  `use_start_date` int(11) NOT NULL DEFAULT '0',
  `use_end_date` int(11) NOT NULL DEFAULT '0',
  `min_goods_amount` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `is_display` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`type_id`)
) ENGINE=MyISAM AUTO_INCREMENT=76 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_booking_goods
-- ----------------------------
DROP TABLE IF EXISTS `ecs_booking_goods`;
CREATE TABLE `ecs_booking_goods` (
  `rec_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `email` varchar(60) NOT NULL DEFAULT '',
  `link_man` varchar(60) NOT NULL DEFAULT '',
  `tel` varchar(60) NOT NULL DEFAULT '',
  `goods_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `goods_desc` varchar(255) NOT NULL DEFAULT '',
  `goods_number` smallint(5) unsigned NOT NULL DEFAULT '0',
  `booking_time` int(10) unsigned NOT NULL DEFAULT '0',
  `is_dispose` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `dispose_user` varchar(30) NOT NULL DEFAULT '',
  `dispose_time` int(10) unsigned NOT NULL DEFAULT '0',
  `dispose_note` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`rec_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_brand
-- ----------------------------
DROP TABLE IF EXISTS `ecs_brand`;
CREATE TABLE `ecs_brand` (
  `brand_id` int(5) unsigned NOT NULL AUTO_INCREMENT COMMENT '地区排序',
  `brand_name` varchar(60) NOT NULL DEFAULT '',
  `first_letter` varchar(20) NOT NULL DEFAULT '' COMMENT '首字母',
  `brand_logo` varchar(80) NOT NULL DEFAULT '',
  `brand_desc` text NOT NULL,
  `site_url` varchar(255) NOT NULL DEFAULT '',
  `sort_order` tinyint(3) unsigned NOT NULL DEFAULT '50',
  `is_show` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `region_id` mediumtext NOT NULL COMMENT '地区ID串',
  `gs_extract_16` text NOT NULL,
  `gs_desc_16` longtext NOT NULL,
  `gs_extract_17` text NOT NULL,
  `gs_desc_17` longtext NOT NULL,
  `gs_extract_18` text NOT NULL,
  `gs_desc_18` longtext NOT NULL,
  `gs_extract_19` text NOT NULL,
  `gs_desc_19` longtext NOT NULL,
  `gs_extract_24` text NOT NULL,
  `gs_desc_24` longtext NOT NULL,
  `reserve_type` tinyint(3) NOT NULL DEFAULT '0',
  `reserve_other` varchar(100) NOT NULL DEFAULT '' COMMENT '其它预定类型',
  `reserve_hours` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '提前预定小时数',
  `support_shipping` tinyint(2) NOT NULL DEFAULT '0',
  `region_sort` text COMMENT '地区(东莞)排序',
  PRIMARY KEY (`brand_id`),
  KEY `is_show` (`is_show`)
) ENGINE=MyISAM AUTO_INCREMENT=151 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_brand_extend
-- ----------------------------
DROP TABLE IF EXISTS `ecs_brand_extend`;
CREATE TABLE `ecs_brand_extend` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `brand_id` int(11) NOT NULL DEFAULT '0' COMMENT '品牌id',
  `cat_id` int(11) NOT NULL DEFAULT '0' COMMENT '分类id,ecs_category表cat_id',
  `gs_extract` text COMMENT '提取时间',
  `gs_desc` mediumtext COMMENT '详细描述',
  `update_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_bidcid` (`brand_id`,`cat_id`)
) ENGINE=InnoDB AUTO_INCREMENT=551 DEFAULT CHARSET=utf8 COMMENT='品牌扩展信息';

-- ----------------------------
-- Table structure for ecs_card
-- ----------------------------
DROP TABLE IF EXISTS `ecs_card`;
CREATE TABLE `ecs_card` (
  `card_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `card_name` varchar(120) NOT NULL DEFAULT '',
  `card_img` varchar(255) NOT NULL DEFAULT '',
  `card_fee` decimal(6,2) unsigned NOT NULL DEFAULT '0.00',
  `free_money` decimal(6,2) unsigned NOT NULL DEFAULT '0.00',
  `card_desc` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`card_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_cart
-- ----------------------------
DROP TABLE IF EXISTS `ecs_cart`;
CREATE TABLE `ecs_cart` (
  `rec_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `session_id` char(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `goods_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `goods_sn` varchar(60) NOT NULL DEFAULT '',
  `product_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `goods_name` varchar(120) NOT NULL DEFAULT '',
  `market_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `goods_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `goods_number` smallint(5) unsigned NOT NULL DEFAULT '0',
  `goods_attr` text NOT NULL,
  `is_real` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `extension_code` varchar(30) NOT NULL DEFAULT '',
  `parent_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `rec_type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_gift` smallint(5) unsigned NOT NULL DEFAULT '0',
  `is_shipping` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `can_handsel` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `goods_attr_id` varchar(255) NOT NULL DEFAULT '',
  `is_check` int(2) DEFAULT '0',
  `goods_brand_id` int(10) NOT NULL DEFAULT '0',
  `stores_user_id` int(10) NOT NULL DEFAULT '0',
  `cart_stores_id` int(10) NOT NULL DEFAULT '0' COMMENT '购物车环节选取门店ID',
  `exceed_promote_num` int(10) NOT NULL DEFAULT '0',
  `exceed_promote_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`rec_id`),
  KEY `session_id` (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_cat_recommend
-- ----------------------------
DROP TABLE IF EXISTS `ecs_cat_recommend`;
CREATE TABLE `ecs_cat_recommend` (
  `cat_id` smallint(5) NOT NULL,
  `recommend_type` tinyint(1) NOT NULL,
  PRIMARY KEY (`cat_id`,`recommend_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_category
-- ----------------------------
DROP TABLE IF EXISTS `ecs_category`;
CREATE TABLE `ecs_category` (
  `cat_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `cat_name` varchar(90) NOT NULL DEFAULT '',
  `keywords` varchar(255) NOT NULL DEFAULT '',
  `cat_desc` varchar(255) NOT NULL DEFAULT '',
  `parent_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `sort_order` tinyint(1) unsigned NOT NULL DEFAULT '50',
  `template_file` varchar(50) NOT NULL DEFAULT '',
  `measure_unit` varchar(15) NOT NULL DEFAULT '',
  `show_in_nav` tinyint(1) NOT NULL DEFAULT '0',
  `style` varchar(150) NOT NULL,
  `is_show` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `grade` tinyint(4) NOT NULL DEFAULT '0',
  `filter_attr` varchar(255) NOT NULL DEFAULT '0',
  `ads_img1` varchar(100) NOT NULL,
  `ads_img1_pc` varchar(100) NOT NULL,
  `ads_img1_pc2` varchar(100) NOT NULL,
  `city` varchar(100) NOT NULL,
  `is_send_msg` tinyint(2) DEFAULT '0' COMMENT '是否发送短信给门店及客服手机:1是,2否',
  `is_can_back` tinyint(2) NOT NULL DEFAULT '1' COMMENT '客户可自行退单:1是，2否',
  `sell_goods_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商品销售数量',
  PRIMARY KEY (`cat_id`),
  KEY `parent_id` (`parent_id`)
) ENGINE=MyISAM AUTO_INCREMENT=47 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_city_goods
-- ----------------------------
DROP TABLE IF EXISTS `ecs_city_goods`;
CREATE TABLE `ecs_city_goods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `city_id` int(11) NOT NULL,
  `good_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5287 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_collect_goods
-- ----------------------------
DROP TABLE IF EXISTS `ecs_collect_goods`;
CREATE TABLE `ecs_collect_goods` (
  `rec_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `goods_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `add_time` int(11) unsigned NOT NULL DEFAULT '0',
  `is_attention` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`rec_id`),
  KEY `user_id` (`user_id`),
  KEY `goods_id` (`goods_id`),
  KEY `is_attention` (`is_attention`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_collect_stores
-- ----------------------------
DROP TABLE IF EXISTS `ecs_collect_stores`;
CREATE TABLE `ecs_collect_stores` (
  `id` mediumint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL DEFAULT '0' COMMENT '用户id',
  `gs_id` int(10) NOT NULL DEFAULT '0' COMMENT '门店id,ecs_goods_stores表id',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '添加时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_userid_gsid` (`user_id`,`gs_id`)
) ENGINE=MyISAM AUTO_INCREMENT=84 DEFAULT CHARSET=utf8 COMMENT='门店收藏';

-- ----------------------------
-- Table structure for ecs_comment
-- ----------------------------
DROP TABLE IF EXISTS `ecs_comment`;
CREATE TABLE `ecs_comment` (
  `comment_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `comment_type` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `id_value` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `email` varchar(60) NOT NULL DEFAULT '',
  `user_name` varchar(60) NOT NULL DEFAULT '',
  `content` text NOT NULL,
  `comment_rank` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `add_time` int(10) unsigned NOT NULL DEFAULT '0',
  `ip_address` varchar(15) NOT NULL DEFAULT '',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `order_id` int(10) NOT NULL DEFAULT '0' COMMENT '针对该产品订单ID',
  `is_anonymous` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否匿名评价',
  `comment_rank_2` tinyint(1) NOT NULL DEFAULT '0',
  `comment_rank_3` tinyint(1) NOT NULL DEFAULT '0',
  `cmt_img_1` varchar(100) NOT NULL DEFAULT '',
  `cmt_img_2` varchar(100) NOT NULL DEFAULT '',
  `cmt_img_3` varchar(100) NOT NULL DEFAULT '',
  `cmt_img_4` varchar(100) NOT NULL DEFAULT '',
  `cmt_img_5` varchar(100) NOT NULL DEFAULT '',
  `is_img` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否有图',
  PRIMARY KEY (`comment_id`),
  KEY `parent_id` (`parent_id`),
  KEY `id_value` (`id_value`)
) ENGINE=MyISAM AUTO_INCREMENT=289 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_crons
-- ----------------------------
DROP TABLE IF EXISTS `ecs_crons`;
CREATE TABLE `ecs_crons` (
  `cron_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `cron_code` varchar(20) NOT NULL,
  `cron_name` varchar(120) NOT NULL,
  `cron_desc` text,
  `cron_order` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `cron_config` text NOT NULL,
  `thistime` int(10) NOT NULL DEFAULT '0',
  `nextime` int(10) NOT NULL,
  `day` tinyint(2) NOT NULL,
  `week` varchar(1) NOT NULL,
  `hour` varchar(2) NOT NULL,
  `minute` varchar(255) NOT NULL,
  `enable` tinyint(1) NOT NULL DEFAULT '1',
  `run_once` tinyint(1) NOT NULL DEFAULT '0',
  `allow_ip` varchar(100) NOT NULL DEFAULT '',
  `alow_files` varchar(255) NOT NULL,
  PRIMARY KEY (`cron_id`),
  KEY `nextime` (`nextime`),
  KEY `enable` (`enable`),
  KEY `cron_code` (`cron_code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_customer_service_tel
-- ----------------------------
DROP TABLE IF EXISTS `ecs_customer_service_tel`;
CREATE TABLE `ecs_customer_service_tel` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '处境id',
  `city_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '城市id',
  `tel` varchar(50) NOT NULL DEFAULT '' COMMENT '电话号码',
  `wx_openid` varchar(100) NOT NULL DEFAULT '' COMMENT '微信openid',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_cityid` (`city_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='客服电话';

-- ----------------------------
-- Table structure for ecs_delivery_goods
-- ----------------------------
DROP TABLE IF EXISTS `ecs_delivery_goods`;
CREATE TABLE `ecs_delivery_goods` (
  `rec_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `delivery_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `goods_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `product_id` mediumint(8) unsigned DEFAULT '0',
  `product_sn` varchar(60) DEFAULT NULL,
  `goods_name` varchar(120) DEFAULT NULL,
  `brand_name` varchar(60) DEFAULT NULL,
  `goods_sn` varchar(60) DEFAULT NULL,
  `is_real` tinyint(1) unsigned DEFAULT '0',
  `extension_code` varchar(30) DEFAULT NULL,
  `parent_id` mediumint(8) unsigned DEFAULT '0',
  `send_number` smallint(5) unsigned DEFAULT '0',
  `goods_attr` text,
  PRIMARY KEY (`rec_id`),
  KEY `delivery_id` (`delivery_id`,`goods_id`),
  KEY `goods_id` (`goods_id`)
) ENGINE=MyISAM AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_delivery_order
-- ----------------------------
DROP TABLE IF EXISTS `ecs_delivery_order`;
CREATE TABLE `ecs_delivery_order` (
  `delivery_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `delivery_sn` varchar(20) NOT NULL,
  `order_sn` varchar(20) NOT NULL,
  `order_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `invoice_no` varchar(50) DEFAULT NULL,
  `add_time` int(10) unsigned DEFAULT '0',
  `shipping_id` tinyint(3) unsigned DEFAULT '0',
  `shipping_name` varchar(120) DEFAULT NULL,
  `user_id` mediumint(8) unsigned DEFAULT '0',
  `action_user` varchar(30) DEFAULT NULL,
  `consignee` varchar(60) DEFAULT NULL,
  `address` varchar(250) DEFAULT NULL,
  `country` smallint(5) unsigned DEFAULT '0',
  `province` smallint(5) unsigned DEFAULT '0',
  `city` smallint(5) unsigned DEFAULT '0',
  `district` smallint(5) unsigned DEFAULT '0',
  `sign_building` varchar(120) DEFAULT NULL,
  `email` varchar(60) DEFAULT NULL,
  `zipcode` varchar(60) DEFAULT NULL,
  `tel` varchar(60) DEFAULT NULL,
  `mobile` varchar(60) DEFAULT NULL,
  `best_time` varchar(120) DEFAULT NULL,
  `postscript` varchar(255) DEFAULT NULL,
  `how_oos` varchar(120) DEFAULT NULL,
  `insure_fee` decimal(10,2) unsigned DEFAULT '0.00',
  `shipping_fee` decimal(10,2) unsigned DEFAULT '0.00',
  `update_time` int(10) unsigned DEFAULT '0',
  `suppliers_id` smallint(5) DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `agency_id` smallint(5) unsigned DEFAULT '0',
  PRIMARY KEY (`delivery_id`),
  KEY `user_id` (`user_id`),
  KEY `order_id` (`order_id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_email_list
-- ----------------------------
DROP TABLE IF EXISTS `ecs_email_list`;
CREATE TABLE `ecs_email_list` (
  `id` mediumint(8) NOT NULL AUTO_INCREMENT,
  `email` varchar(60) NOT NULL,
  `stat` tinyint(1) NOT NULL DEFAULT '0',
  `hash` varchar(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_email_sendlist
-- ----------------------------
DROP TABLE IF EXISTS `ecs_email_sendlist`;
CREATE TABLE `ecs_email_sendlist` (
  `id` mediumint(8) NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL,
  `template_id` mediumint(8) NOT NULL,
  `email_content` text NOT NULL,
  `error` tinyint(1) NOT NULL DEFAULT '0',
  `pri` tinyint(10) NOT NULL,
  `last_send` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_error_log
-- ----------------------------
DROP TABLE IF EXISTS `ecs_error_log`;
CREATE TABLE `ecs_error_log` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `info` varchar(255) NOT NULL,
  `file` varchar(100) NOT NULL,
  `time` int(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `time` (`time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_exchange_goods
-- ----------------------------
DROP TABLE IF EXISTS `ecs_exchange_goods`;
CREATE TABLE `ecs_exchange_goods` (
  `goods_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `exchange_integral` int(10) unsigned NOT NULL DEFAULT '0',
  `is_exchange` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_hot` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`goods_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_express_detail
-- ----------------------------
DROP TABLE IF EXISTS `ecs_express_detail`;
CREATE TABLE `ecs_express_detail` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_sn` varchar(20) NOT NULL DEFAULT '' COMMENT '订单号',
  `ex_num` varchar(20) NOT NULL DEFAULT '' COMMENT '快递单号',
  `ex_cnt` text COMMENT '物流详细信息',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '添加时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_snum` (`order_sn`,`ex_num`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COMMENT='物流详情信息';

-- ----------------------------
-- Table structure for ecs_express_info
-- ----------------------------
DROP TABLE IF EXISTS `ecs_express_info`;
CREATE TABLE `ecs_express_info` (
  `ex_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `ex_sn` varchar(50) NOT NULL DEFAULT '' COMMENT '快递公司编码',
  `ex_name` varchar(100) NOT NULL DEFAULT '' COMMENT '快递公司名称',
  `ex_tel` varchar(30) NOT NULL DEFAULT '' COMMENT '服务电话',
  `enable` tinyint(2) NOT NULL DEFAULT '0' COMMENT '是否启用：1启用',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '添加时间',
  PRIMARY KEY (`ex_id`),
  UNIQUE KEY `uq_exsn` (`ex_sn`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COMMENT='快递公司信息';

-- ----------------------------
-- Table structure for ecs_favourable_activity
-- ----------------------------
DROP TABLE IF EXISTS `ecs_favourable_activity`;
CREATE TABLE `ecs_favourable_activity` (
  `act_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `act_name` varchar(255) NOT NULL,
  `start_time` int(10) unsigned NOT NULL,
  `end_time` int(10) unsigned NOT NULL,
  `user_rank` varchar(255) NOT NULL,
  `act_range` tinyint(3) unsigned NOT NULL,
  `act_range_ext` varchar(255) NOT NULL,
  `min_amount` decimal(10,2) unsigned NOT NULL,
  `max_amount` decimal(10,2) unsigned NOT NULL,
  `act_type` tinyint(3) unsigned NOT NULL,
  `act_type_ext` decimal(10,2) unsigned NOT NULL,
  `gift` text NOT NULL,
  `sort_order` tinyint(3) unsigned NOT NULL DEFAULT '50',
  PRIMARY KEY (`act_id`),
  KEY `act_name` (`act_name`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_feedback
-- ----------------------------
DROP TABLE IF EXISTS `ecs_feedback`;
CREATE TABLE `ecs_feedback` (
  `msg_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `user_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `user_name` varchar(60) NOT NULL DEFAULT '',
  `user_email` varchar(60) NOT NULL DEFAULT '',
  `msg_title` varchar(200) NOT NULL DEFAULT '',
  `msg_type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `msg_status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `msg_content` text NOT NULL,
  `msg_time` int(10) unsigned NOT NULL DEFAULT '0',
  `message_img` varchar(255) NOT NULL DEFAULT '0',
  `order_id` int(11) unsigned NOT NULL DEFAULT '0',
  `msg_area` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `send_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0未生成会员消息;1已生成会员消息',
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '添加时间',
  PRIMARY KEY (`msg_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_friend_link
-- ----------------------------
DROP TABLE IF EXISTS `ecs_friend_link`;
CREATE TABLE `ecs_friend_link` (
  `link_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `link_name` varchar(255) NOT NULL DEFAULT '',
  `link_url` varchar(255) NOT NULL DEFAULT '',
  `link_logo` varchar(255) NOT NULL DEFAULT '',
  `show_order` tinyint(3) unsigned NOT NULL DEFAULT '50',
  PRIMARY KEY (`link_id`),
  KEY `show_order` (`show_order`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_goods
-- ----------------------------
DROP TABLE IF EXISTS `ecs_goods`;
CREATE TABLE `ecs_goods` (
  `goods_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT COMMENT '商品id',
  `cat_id` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '分类',
  `goods_sn` varchar(60) NOT NULL DEFAULT '' COMMENT '货号',
  `goods_name` varchar(120) NOT NULL DEFAULT '',
  `goods_name_style` varchar(60) NOT NULL DEFAULT '+',
  `click_count` int(10) unsigned NOT NULL DEFAULT '0',
  `brand_id` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '品牌id',
  `provider_name` varchar(100) NOT NULL DEFAULT '',
  `goods_number` smallint(5) unsigned NOT NULL DEFAULT '0',
  `goods_weight` decimal(10,3) unsigned NOT NULL DEFAULT '0.000',
  `market_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '市场价格',
  `settle_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '结算价格，auto(colin)',
  `shop_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '销售价格',
  `promote_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `promote_start_date` int(11) unsigned NOT NULL DEFAULT '0',
  `promote_end_date` int(11) unsigned NOT NULL DEFAULT '0',
  `warn_number` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `remind_msg` varchar(100) NOT NULL DEFAULT '' COMMENT '用户提示信息',
  `keywords` varchar(255) NOT NULL DEFAULT '',
  `goods_brief` varchar(255) NOT NULL DEFAULT '',
  `goods_desc` text NOT NULL,
  `goods_thumb` varchar(255) NOT NULL DEFAULT '',
  `goods_img` varchar(255) NOT NULL DEFAULT '',
  `original_img` varchar(255) NOT NULL DEFAULT '',
  `imgs` text COMMENT '产品图片(多个)',
  `is_real` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `extension_code` varchar(30) NOT NULL DEFAULT '',
  `is_on_sale` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否上架',
  `is_alone_sale` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `is_shipping` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `integral` int(10) unsigned NOT NULL DEFAULT '0',
  `add_time` int(10) unsigned NOT NULL DEFAULT '0',
  `sort_order` smallint(4) unsigned NOT NULL DEFAULT '100' COMMENT '推荐排序',
  `is_delete` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_best` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否精品',
  `is_new` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '新品',
  `is_hot` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '热销',
  `is_promote` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `bonus_type_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `last_update` int(10) unsigned NOT NULL DEFAULT '0',
  `goods_type` smallint(5) unsigned NOT NULL DEFAULT '0',
  `seller_note` varchar(255) NOT NULL DEFAULT '',
  `give_integral` int(11) NOT NULL DEFAULT '-1',
  `rank_integral` int(11) NOT NULL DEFAULT '-1',
  `suppliers_id` smallint(5) unsigned DEFAULT NULL,
  `is_check` tinyint(1) unsigned DEFAULT NULL,
  `aff_bid1` int(10) NOT NULL DEFAULT '0' COMMENT '口味',
  `aff_bid2` int(10) NOT NULL DEFAULT '0' COMMENT '人群',
  `shop_price_zy` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '直营店价格',
  `shop_price_jm` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '加盟店价格',
  `settle_discount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '结算折扣 auto(colin)',
  `saleqt` int(10) NOT NULL DEFAULT '0',
  `promote_num` int(10) NOT NULL DEFAULT '0',
  `pickup_mode` int(3) NOT NULL DEFAULT '1' COMMENT '提货方式：1门店自提；2商家配送；其他',
  `reserve_hours` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '提前预定小时数',
  `pay_type_limit` tinyint(2) NOT NULL DEFAULT '0' COMMENT '是否限定支付方式:1是，0否',
  `pay_types` varchar(20) NOT NULL DEFAULT '' COMMENT '支付方式id，多个逗号隔开：1幸福券，2余额，3微信,4支付宝',
  `free_post` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '是否包邮：1是',
  PRIMARY KEY (`goods_id`),
  KEY `goods_sn` (`goods_sn`),
  KEY `cat_id` (`cat_id`),
  KEY `last_update` (`last_update`),
  KEY `brand_id` (`brand_id`),
  KEY `goods_weight` (`goods_weight`),
  KEY `promote_end_date` (`promote_end_date`),
  KEY `promote_start_date` (`promote_start_date`),
  KEY `goods_number` (`goods_number`),
  KEY `sort_order` (`sort_order`)
) ENGINE=InnoDB AUTO_INCREMENT=6247 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_goods_activity
-- ----------------------------
DROP TABLE IF EXISTS `ecs_goods_activity`;
CREATE TABLE `ecs_goods_activity` (
  `act_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `act_name` varchar(255) NOT NULL,
  `act_desc` text NOT NULL,
  `act_type` tinyint(3) unsigned NOT NULL,
  `goods_id` mediumint(8) unsigned NOT NULL,
  `product_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `goods_name` varchar(255) NOT NULL,
  `start_time` int(10) unsigned NOT NULL,
  `end_time` int(10) unsigned NOT NULL,
  `is_finished` tinyint(3) unsigned NOT NULL,
  `ext_info` text NOT NULL,
  PRIMARY KEY (`act_id`),
  KEY `act_name` (`act_name`,`act_type`,`goods_id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_goods_article
-- ----------------------------
DROP TABLE IF EXISTS `ecs_goods_article`;
CREATE TABLE `ecs_goods_article` (
  `goods_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `article_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `admin_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`goods_id`,`article_id`,`admin_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_goods_attr
-- ----------------------------
DROP TABLE IF EXISTS `ecs_goods_attr`;
CREATE TABLE `ecs_goods_attr` (
  `goods_attr_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `goods_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `attr_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `attr_value` text NOT NULL,
  `attr_price` varchar(255) NOT NULL DEFAULT '',
  `original_price` varchar(10) NOT NULL DEFAULT '' COMMENT '原价',
  `settle_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '属性结算价 auto(colin)',
  `img` varchar(200) NOT NULL DEFAULT '' COMMENT '图片地址',
  `thumb_img` varchar(200) NOT NULL DEFAULT '' COMMENT '缩略图地址',
  PRIMARY KEY (`goods_attr_id`),
  KEY `goods_id` (`goods_id`),
  KEY `attr_id` (`attr_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15403 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_goods_attr_pre
-- ----------------------------
DROP TABLE IF EXISTS `ecs_goods_attr_pre`;
CREATE TABLE `ecs_goods_attr_pre` (
  `goods_attr_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `goods_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `attr_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `attr_value` text NOT NULL,
  `attr_price` varchar(255) NOT NULL DEFAULT '',
  `original_price` varchar(10) NOT NULL DEFAULT '' COMMENT '原价',
  `settle_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '属性结算价 auto(colin)',
  `img` varchar(200) NOT NULL DEFAULT '' COMMENT '图片地址',
  `thumb_img` varchar(200) NOT NULL DEFAULT '' COMMENT '缩略图地址',
  `pre_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'ecs_goods_pre关联id',
  PRIMARY KEY (`goods_attr_id`),
  KEY `goods_id` (`goods_id`),
  KEY `attr_id` (`attr_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15619 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_goods_cat
-- ----------------------------
DROP TABLE IF EXISTS `ecs_goods_cat`;
CREATE TABLE `ecs_goods_cat` (
  `goods_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `cat_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`goods_id`,`cat_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_goods_gallery
-- ----------------------------
DROP TABLE IF EXISTS `ecs_goods_gallery`;
CREATE TABLE `ecs_goods_gallery` (
  `img_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `goods_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `img_url` varchar(255) NOT NULL DEFAULT '',
  `img_desc` varchar(255) NOT NULL DEFAULT '',
  `thumb_url` varchar(255) NOT NULL DEFAULT '',
  `img_original` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`img_id`),
  KEY `goods_id` (`goods_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6592 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_goods_gallery_pre
-- ----------------------------
DROP TABLE IF EXISTS `ecs_goods_gallery_pre`;
CREATE TABLE `ecs_goods_gallery_pre` (
  `img_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `goods_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `img_url` varchar(255) NOT NULL DEFAULT '',
  `img_desc` varchar(255) NOT NULL DEFAULT '',
  `thumb_url` varchar(255) NOT NULL DEFAULT '',
  `img_original` varchar(255) NOT NULL DEFAULT '',
  `pre_id` int(10) NOT NULL DEFAULT '0' COMMENT 'ecs_goods_pre关联id',
  PRIMARY KEY (`img_id`),
  KEY `goods_id` (`goods_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6610 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_goods_kouwei
-- ----------------------------
DROP TABLE IF EXISTS `ecs_goods_kouwei`;
CREATE TABLE `ecs_goods_kouwei` (
  `goods_kw_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `goods_id` int(10) unsigned NOT NULL DEFAULT '0',
  `st_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`goods_kw_id`),
  KEY `goods_id` (`goods_id`),
  KEY `attr_id` (`st_id`)
) ENGINE=MyISAM AUTO_INCREMENT=10251 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_goods_kouwei_pre
-- ----------------------------
DROP TABLE IF EXISTS `ecs_goods_kouwei_pre`;
CREATE TABLE `ecs_goods_kouwei_pre` (
  `goods_kw_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `goods_id` int(10) unsigned NOT NULL DEFAULT '0',
  `st_id` int(10) unsigned NOT NULL DEFAULT '0',
  `pre_id` int(10) NOT NULL DEFAULT '0' COMMENT 'ecs_goods_pre关联id',
  PRIMARY KEY (`goods_kw_id`),
  KEY `goods_id` (`goods_id`),
  KEY `attr_id` (`st_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_goods_pre
-- ----------------------------
DROP TABLE IF EXISTS `ecs_goods_pre`;
CREATE TABLE `ecs_goods_pre` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `goods_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '商品id',
  `cat_id` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '分类',
  `goods_sn` varchar(60) NOT NULL DEFAULT '' COMMENT '货号',
  `goods_name` varchar(120) NOT NULL DEFAULT '',
  `goods_name_style` varchar(60) NOT NULL DEFAULT '+',
  `brand_id` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '品牌id',
  `market_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '市场价格',
  `shop_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '销售价格',
  `promote_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `promote_start_date` int(11) unsigned NOT NULL DEFAULT '0',
  `promote_end_date` int(11) unsigned NOT NULL DEFAULT '0',
  `remind_msg` varchar(100) NOT NULL DEFAULT '' COMMENT '用户提示信息',
  `goods_brief` varchar(255) NOT NULL DEFAULT '',
  `goods_desc` text NOT NULL,
  `goods_thumb` varchar(255) NOT NULL DEFAULT '',
  `goods_img` varchar(255) NOT NULL DEFAULT '',
  `original_img` varchar(255) NOT NULL DEFAULT '',
  `imgs` text COMMENT '产品图片(多个)',
  `is_promote` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `bonus_type_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `goods_type` smallint(5) unsigned NOT NULL DEFAULT '0',
  `settle_discount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '结算折扣 auto(colin)',
  `promote_num` int(10) NOT NULL DEFAULT '0',
  `pickup_mode` int(3) NOT NULL DEFAULT '1' COMMENT '提货方式：1门店自提；2商家配送；其他',
  `reserve_hours` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '提前预定小时数',
  `pay_type_limit` tinyint(2) NOT NULL DEFAULT '0' COMMENT '是否限定支付方式:1是，0否',
  `pay_types` varchar(20) NOT NULL DEFAULT '' COMMENT '支付方式id，多个逗号隔开：1幸福券，2余额，3微信,4支付宝',
  `free_post` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '是否包邮：1是',
  `pre_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '市场：0保存,1待审核,2审核通过3驳回',
  `pre_remake` varchar(255) NOT NULL DEFAULT '' COMMENT '产品审核备注信息',
  `finance_status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '财务：0保存1待审核,2审核通过3驳回',
  `finance_remake` varchar(255) NOT NULL DEFAULT '' COMMENT '市场审核备注信息',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '更新时间',
  `update_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '更新时间',
  `sub_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT '提交时间',
  PRIMARY KEY (`id`),
  KEY `goods_sn` (`goods_sn`)
) ENGINE=InnoDB AUTO_INCREMENT=6426 DEFAULT CHARSET=utf8 COMMENT='商品预编译表格 autor:colin';

-- ----------------------------
-- Table structure for ecs_goods_region
-- ----------------------------
DROP TABLE IF EXISTS `ecs_goods_region`;
CREATE TABLE `ecs_goods_region` (
  `gr_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `gr_name` varchar(100) NOT NULL DEFAULT '',
  `sort_order` int(11) unsigned NOT NULL DEFAULT '0',
  `parent_id` int(10) NOT NULL,
  `type` int(10) NOT NULL,
  `enable` tinyint(2) NOT NULL DEFAULT '1' COMMENT '是否启用:1启用,0不启用',
  `brand_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '品牌个数',
  PRIMARY KEY (`gr_id`)
) ENGINE=MyISAM AUTO_INCREMENT=148 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_goods_region_copy
-- ----------------------------
DROP TABLE IF EXISTS `ecs_goods_region_copy`;
CREATE TABLE `ecs_goods_region_copy` (
  `gr_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `gr_name` varchar(100) NOT NULL DEFAULT '',
  `sort_order` int(11) unsigned NOT NULL DEFAULT '0',
  `parent_id` int(10) NOT NULL,
  `type` int(10) NOT NULL,
  `enable` tinyint(2) NOT NULL DEFAULT '1' COMMENT '是否启用:1启用,0不启用',
  `brand_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '品牌个数',
  PRIMARY KEY (`gr_id`)
) ENGINE=MyISAM AUTO_INCREMENT=449 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_goods_sales_type
-- ----------------------------
DROP TABLE IF EXISTS `ecs_goods_sales_type`;
CREATE TABLE `ecs_goods_sales_type` (
  `goods_id` mediumint(8) unsigned NOT NULL,
  `sales_type` int(10) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_goods_stock
-- ----------------------------
DROP TABLE IF EXISTS `ecs_goods_stock`;
CREATE TABLE `ecs_goods_stock` (
  `st_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `gs_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商户id',
  `goods_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商品id',
  `attr_ids` varchar(100) NOT NULL DEFAULT '0' COMMENT '商品属性id，多个-连接',
  `num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '库存数量',
  `num_promotion` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '促销数量',
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`st_id`),
  UNIQUE KEY `uq_ids` (`gs_id`,`goods_id`,`attr_ids`)
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8 COMMENT='商品库存，商户定义';

-- ----------------------------
-- Table structure for ecs_goods_stores
-- ----------------------------
DROP TABLE IF EXISTS `ecs_goods_stores`;
CREATE TABLE `ecs_goods_stores` (
  `gs_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `gs_name` varchar(100) NOT NULL DEFAULT '' COMMENT '门店名称',
  `gs_login_name` varchar(100) NOT NULL COMMENT '门店登录用户名',
  `gs_login_pass` varchar(100) NOT NULL,
  `gs_login_salt` int(10) NOT NULL,
  `gs_brand_id` int(10) NOT NULL DEFAULT '0' COMMENT '品牌ID',
  `city_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '所在的城市id',
  `gs_region_id` int(10) NOT NULL DEFAULT '0' COMMENT '地区ID',
  `gs_region_sq` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商圈(地区)id',
  `show_citys` varchar(200) NOT NULL DEFAULT '' COMMENT '展示城市，多个逗号隔开',
  `gs_goods_id` longtext NOT NULL COMMENT '产品ID串(该店拥有的产品ID)',
  `full_free_post` tinyint(3) unsigned DEFAULT '0' COMMENT '是否满包邮:1是',
  `free_post_money` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '包邮条件：达到金额数包邮',
  `post_fee` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '省内邮费',
  `post_fee_2` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '省外邮费',
  `sort_order` int(11) unsigned NOT NULL DEFAULT '0',
  `gs_stats` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态，0关闭  1营业',
  `gs_address` varchar(255) NOT NULL DEFAULT '',
  `gs_notice` varchar(255) NOT NULL DEFAULT '' COMMENT '门店公告',
  `gs_lng` double(10,6) NOT NULL DEFAULT '0.000000' COMMENT '经度',
  `gs_lat` double(10,6) NOT NULL DEFAULT '0.000000' COMMENT '纬度',
  `gs_contacter` varchar(100) NOT NULL DEFAULT '' COMMENT '门店联系人',
  `gs_mobile` varchar(100) NOT NULL DEFAULT '' COMMENT '联系方式',
  `gs_auth` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否有权限操作商户中心，0无；1有',
  `is_manage` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否管理者',
  `pid` int(10) NOT NULL DEFAULT '0' COMMENT '父级ID',
  `gs_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '门店类型',
  `max_order_time` int(10) NOT NULL,
  `SaleAmount` int(10) NOT NULL,
  `store_pic` varchar(250) NOT NULL,
  `business_hours` varchar(250) NOT NULL,
  `pickup_mode` int(3) NOT NULL DEFAULT '1',
  `open_time` varchar(20) NOT NULL DEFAULT '' COMMENT '开门时间',
  `close_time` varchar(20) NOT NULL DEFAULT '' COMMENT '关门时间',
  `rec_goods_ids` varchar(200) NOT NULL DEFAULT '' COMMENT '推荐的产品id，多个逗号隔开',
  `wx_openid` varchar(100) NOT NULL DEFAULT '' COMMENT '微信openid',
  `picktime_start` tinyint(2) unsigned zerofill NOT NULL DEFAULT '07' COMMENT '取货最早时间',
  `picktime_end` tinyint(2) unsigned zerofill NOT NULL DEFAULT '22' COMMENT '取货最晚时间',
  `uptime_start` varchar(20) NOT NULL DEFAULT '' COMMENT '线下开店时间',
  `uptime_end` varchar(20) NOT NULL DEFAULT '' COMMENT '线下关店时间',
  PRIMARY KEY (`gs_id`),
  UNIQUE KEY `uq_name` (`gs_login_name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=499 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_goods_stores_attribute
-- ----------------------------
DROP TABLE IF EXISTS `ecs_goods_stores_attribute`;
CREATE TABLE `ecs_goods_stores_attribute` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '主键id',
  `gs_id` int(10) NOT NULL COMMENT '商户id',
  `goods_id` int(10) NOT NULL COMMENT '商品id',
  `sale_status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '销售状态：1上架，2下架',
  `sort` smallint(4) NOT NULL DEFAULT '0' COMMENT '排序权重值',
  `pickup_mode` tinyint(3) NOT NULL DEFAULT '1' COMMENT '提货方式：1门店自提；2商家配送；其他',
  `reserve_hours` tinyint(4) NOT NULL DEFAULT '0' COMMENT '提前预定小时数',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '添加时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_gs` (`gs_id`,`goods_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8 COMMENT='商户商品附属表 auto colin';

-- ----------------------------
-- Table structure for ecs_goods_type
-- ----------------------------
DROP TABLE IF EXISTS `ecs_goods_type`;
CREATE TABLE `ecs_goods_type` (
  `cat_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `cat_name` varchar(60) NOT NULL DEFAULT '',
  `enabled` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `attr_group` varchar(255) NOT NULL,
  PRIMARY KEY (`cat_id`)
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_group_goods
-- ----------------------------
DROP TABLE IF EXISTS `ecs_group_goods`;
CREATE TABLE `ecs_group_goods` (
  `parent_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `goods_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `goods_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `admin_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`parent_id`,`goods_id`,`admin_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_home_nav
-- ----------------------------
DROP TABLE IF EXISTS `ecs_home_nav`;
CREATE TABLE `ecs_home_nav` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '' COMMENT '名称',
  `img` varchar(255) NOT NULL DEFAULT '' COMMENT '图标地址',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '链接地址',
  `wechat_url` varchar(255) NOT NULL DEFAULT '' COMMENT '小程序链接地址',
  `sort` int(10) NOT NULL DEFAULT '0' COMMENT '排序:数字越大越靠前',
  `enbale` tinyint(2) NOT NULL DEFAULT '0' COMMENT '是否启用:1是，0否',
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COMMENT='首页菜单';

-- ----------------------------
-- Table structure for ecs_keywords
-- ----------------------------
DROP TABLE IF EXISTS `ecs_keywords`;
CREATE TABLE `ecs_keywords` (
  `date` date NOT NULL DEFAULT '0000-00-00',
  `searchengine` varchar(20) NOT NULL DEFAULT '',
  `keyword` varchar(90) NOT NULL DEFAULT '',
  `count` mediumint(8) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`date`,`searchengine`,`keyword`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_link_goods
-- ----------------------------
DROP TABLE IF EXISTS `ecs_link_goods`;
CREATE TABLE `ecs_link_goods` (
  `goods_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `link_goods_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `is_double` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `admin_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`goods_id`,`link_goods_id`,`admin_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_mail_templates
-- ----------------------------
DROP TABLE IF EXISTS `ecs_mail_templates`;
CREATE TABLE `ecs_mail_templates` (
  `template_id` tinyint(1) unsigned NOT NULL AUTO_INCREMENT,
  `template_code` varchar(30) NOT NULL DEFAULT '',
  `is_html` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `template_subject` varchar(200) NOT NULL DEFAULT '',
  `template_content` text NOT NULL,
  `last_modify` int(10) unsigned NOT NULL DEFAULT '0',
  `last_send` int(10) unsigned NOT NULL DEFAULT '0',
  `type` varchar(10) NOT NULL,
  PRIMARY KEY (`template_id`),
  UNIQUE KEY `template_code` (`template_code`),
  KEY `type` (`type`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_member_price
-- ----------------------------
DROP TABLE IF EXISTS `ecs_member_price`;
CREATE TABLE `ecs_member_price` (
  `price_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `goods_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `user_rank` tinyint(3) NOT NULL DEFAULT '0',
  `user_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`price_id`),
  KEY `goods_id` (`goods_id`,`user_rank`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_nav
-- ----------------------------
DROP TABLE IF EXISTS `ecs_nav`;
CREATE TABLE `ecs_nav` (
  `id` mediumint(8) NOT NULL AUTO_INCREMENT,
  `ctype` varchar(10) DEFAULT NULL,
  `cid` smallint(5) unsigned DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `ifshow` tinyint(1) NOT NULL,
  `vieworder` tinyint(1) NOT NULL,
  `opennew` tinyint(1) NOT NULL,
  `url` varchar(255) NOT NULL,
  `type` varchar(10) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `ifshow` (`ifshow`)
) ENGINE=MyISAM AUTO_INCREMENT=55 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_order_action
-- ----------------------------
DROP TABLE IF EXISTS `ecs_order_action`;
CREATE TABLE `ecs_order_action` (
  `action_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `rec_ids` varchar(200) NOT NULL DEFAULT '' COMMENT '表ecs_ordre_goods中的rec_id，多个逗号隔开',
  `action_user` varchar(30) NOT NULL DEFAULT '',
  `order_status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `shipping_status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `pay_status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `action_place` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `refund_amount` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '退款金额',
  `action_note` varchar(255) NOT NULL DEFAULT '',
  `log_time` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`action_id`),
  KEY `order_id` (`order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=20654 DEFAULT CHARSET=utf8 COMMENT='记录订单操作记录';

-- ----------------------------
-- Table structure for ecs_order_bonus_used
-- ----------------------------
DROP TABLE IF EXISTS `ecs_order_bonus_used`;
CREATE TABLE `ecs_order_bonus_used` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `order_sn` varchar(30) NOT NULL DEFAULT '' COMMENT '订单号',
  `bonus_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '幸福券id',
  `used_money` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '使用的金额',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '状态：0未完成,1完成,2已退回',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `change_time` varchar(255) NOT NULL DEFAULT '' COMMENT '最后改变时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_ordersnbid` (`order_sn`,`bonus_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=540 DEFAULT CHARSET=utf8 COMMENT='订单使用的幸福券';

-- ----------------------------
-- Table structure for ecs_order_express
-- ----------------------------
DROP TABLE IF EXISTS `ecs_order_express`;
CREATE TABLE `ecs_order_express` (
  `kid` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `order_sn` varchar(20) NOT NULL DEFAULT '' COMMENT '订单号',
  `ex_id` int(10) NOT NULL DEFAULT '0' COMMENT '快递表的id',
  `ex_num` varchar(50) NOT NULL DEFAULT '' COMMENT '快递单号',
  `ex_mess` varchar(100) NOT NULL DEFAULT '' COMMENT '其它快递信息',
  `up_times` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '修改次数',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '添加时间',
  `wx_send` tinyint(1) DEFAULT '0' COMMENT '微信消息发送状态',
  `send_time` varchar(20) NOT NULL DEFAULT '' COMMENT '发送消息时间',
  `remark` varchar(100) NOT NULL DEFAULT '' COMMENT '备注',
  `is_check` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '是否签收:1是',
  PRIMARY KEY (`kid`),
  UNIQUE KEY `uq_osn` (`order_sn`)
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8 COMMENT='订单物流信息';

-- ----------------------------
-- Table structure for ecs_order_goods
-- ----------------------------
DROP TABLE IF EXISTS `ecs_order_goods`;
CREATE TABLE `ecs_order_goods` (
  `rec_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '订单id',
  `goods_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '商品id',
  `goods_name` varchar(120) NOT NULL DEFAULT '' COMMENT '商品名称',
  `status` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '状态：0正常,1退款中,2已退款',
  `change_time` varchar(20) NOT NULL DEFAULT '' COMMENT '状态改变的时间',
  `goods_sn` varchar(60) NOT NULL DEFAULT '',
  `product_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `goods_number` smallint(5) unsigned NOT NULL DEFAULT '1',
  `market_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `goods_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `goods_attr` text NOT NULL,
  `send_number` smallint(5) unsigned NOT NULL DEFAULT '0',
  `is_real` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `extension_code` varchar(30) NOT NULL DEFAULT '',
  `parent_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `is_gift` smallint(5) unsigned NOT NULL DEFAULT '0',
  `goods_attr_id` varchar(255) NOT NULL DEFAULT '',
  `exceed_promote_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `exceed_promote_num` int(10) NOT NULL DEFAULT '0',
  `settle_discount` decimal(2,2) NOT NULL DEFAULT '0.00' COMMENT '当时的结算折扣',
  `settle_price` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '结算价格',
  PRIMARY KEY (`rec_id`),
  KEY `order_id` (`order_id`),
  KEY `goods_id` (`goods_id`)
) ENGINE=InnoDB AUTO_INCREMENT=35764 DEFAULT CHARSET=utf8 COMMENT='订单商品表';

-- ----------------------------
-- Table structure for ecs_order_info
-- ----------------------------
DROP TABLE IF EXISTS `ecs_order_info`;
CREATE TABLE `ecs_order_info` (
  `order_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `order_sn` varchar(20) NOT NULL DEFAULT '' COMMENT '订单编码',
  `user_id` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `order_status` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '订单状态：0待确认,1已确认,2已取消,3无效,4退货,5已分单，6部分分单',
  `shipping_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '配送状态:0未发货,1已发货,2已收货,3申请退货',
  `pay_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '支付状态:0未付款,1付款中,2已付款',
  `order_taking` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '接单状态:0无需接单，1待接单，2已接单',
  `taking_time` varchar(20) NOT NULL DEFAULT '' COMMENT '接单时间',
  `consignee` varchar(60) NOT NULL DEFAULT '' COMMENT '收货人',
  `country` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '国家',
  `province` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '省份',
  `city` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '城市',
  `district` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT '区域',
  `address` varchar(255) NOT NULL DEFAULT '' COMMENT '配送详细地址',
  `zipcode` varchar(60) NOT NULL DEFAULT '' COMMENT '邮编',
  `tel` varchar(60) NOT NULL DEFAULT '' COMMENT '电话',
  `mobile` varchar(60) NOT NULL DEFAULT '' COMMENT '手机',
  `email` varchar(60) NOT NULL DEFAULT '' COMMENT '邮箱',
  `best_time` varchar(120) NOT NULL DEFAULT '',
  `sign_building` varchar(120) NOT NULL DEFAULT '',
  `postscript` varchar(255) NOT NULL DEFAULT '',
  `is_shipping` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否配送：0否,1是',
  `shipping_id` tinyint(3) NOT NULL DEFAULT '0',
  `shipping_name` varchar(120) NOT NULL DEFAULT '',
  `pay_id` tinyint(3) NOT NULL DEFAULT '0' COMMENT '支付方式:表 ecs_payment 字段pay_id',
  `pay_name` varchar(120) NOT NULL DEFAULT '',
  `how_oos` varchar(120) NOT NULL DEFAULT '',
  `how_surplus` varchar(120) NOT NULL DEFAULT '',
  `pack_name` varchar(120) NOT NULL DEFAULT '',
  `card_name` varchar(120) NOT NULL DEFAULT '',
  `card_message` varchar(255) NOT NULL DEFAULT '',
  `inv_payee` varchar(120) NOT NULL DEFAULT '',
  `inv_content` varchar(120) NOT NULL DEFAULT '',
  `goods_amount` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '订单总金额',
  `shipping_fee` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '配送费',
  `insure_fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `pay_fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `pack_fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `card_fee` decimal(10,2) NOT NULL DEFAULT '0.00',
  `money_paid` decimal(10,2) NOT NULL DEFAULT '0.00',
  `surplus` decimal(10,2) NOT NULL DEFAULT '0.00',
  `integral` int(10) unsigned NOT NULL DEFAULT '0',
  `integral_money` decimal(10,2) NOT NULL DEFAULT '0.00',
  `bonus` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '使用幸福券的金额',
  `order_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `from_ad` smallint(5) NOT NULL DEFAULT '0',
  `referer` varchar(255) NOT NULL DEFAULT '',
  `add_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '订单添加时间',
  `confirm_time` int(10) unsigned NOT NULL DEFAULT '0',
  `pay_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '支付时间',
  `last_cfm_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '最后确认时间，针对还没确认收货状态的订单，到期后系统自动完成收货',
  `delay_cfm_count` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '收货延期次数',
  `shipping_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '配送时间',
  `pack_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `card_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `bonus_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `invoice_no` varchar(255) NOT NULL DEFAULT '',
  `extension_code` varchar(30) NOT NULL DEFAULT '',
  `extension_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `to_buyer` varchar(255) NOT NULL DEFAULT '',
  `pay_note` varchar(255) NOT NULL DEFAULT '',
  `agency_id` smallint(5) unsigned NOT NULL,
  `inv_type` varchar(60) NOT NULL,
  `tax` decimal(10,2) NOT NULL,
  `is_separate` tinyint(1) NOT NULL DEFAULT '0',
  `parent_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `discount` decimal(10,2) NOT NULL,
  `order_note` text NOT NULL COMMENT '备注',
  `order_lxr` varchar(100) NOT NULL DEFAULT '' COMMENT '联系人',
  `order_tel` varchar(100) NOT NULL DEFAULT '' COMMENT '联系电话',
  `order_pick_time` varchar(255) NOT NULL DEFAULT '' COMMENT '取货时间',
  `order_pick_stores` int(10) NOT NULL DEFAULT '0' COMMENT '取货门店ID',
  `is_evaluation` tinyint(1) NOT NULL DEFAULT '0' COMMENT '订单是否评价',
  `zhekou` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '线上支付折扣率',
  `order_amount_all` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '线上总金额，不打折总金额',
  `order_amount_zy` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '直营店总价',
  `stores_type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '门店类型',
  `bonus_company` varchar(100) NOT NULL DEFAULT '',
  `user_bonus_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '表ecs_user_bonus中的bonus_id,表示使用该卡支付',
  `user_bonus_money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '使用卡消费的金额',
  `user_money` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '使用余额消费的金额',
  `is_user_del` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `wx_prepay_id` varchar(60) DEFAULT NULL,
  `wx_transaction_id` varchar(60) DEFAULT NULL,
  `nonce_str` varchar(30) DEFAULT NULL,
  `isread` tinyint(4) DEFAULT NULL,
  `sys_remark` varchar(100) NOT NULL DEFAULT '' COMMENT '系统备注',
  `return_reason` varchar(200) NOT NULL DEFAULT '' COMMENT '退货原因',
  `dfrom` tinyint(2) DEFAULT '0' COMMENT '来源',
  `has_settled` tinyint(2) NOT NULL DEFAULT '0' COMMENT '是否完成结算:1完成',
  `settle_money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '结算金额',
  `is_group` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否为参团订单 0不是，1是',
  `is_show` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '团订单是否显示 0不显示 1显示',
  `group_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '开团表id（group_info）',
  PRIMARY KEY (`order_id`),
  UNIQUE KEY `order_sn` (`order_sn`),
  KEY `user_id` (`user_id`),
  KEY `order_status` (`order_status`),
  KEY `shipping_status` (`shipping_status`),
  KEY `pay_status` (`pay_status`),
  KEY `shipping_id` (`shipping_id`),
  KEY `pay_id` (`pay_id`),
  KEY `extension_code` (`extension_code`,`extension_id`),
  KEY `agency_id` (`agency_id`),
  KEY `idx_addtime` (`add_time`)
) ENGINE=InnoDB AUTO_INCREMENT=20493 DEFAULT CHARSET=utf8 COMMENT='订单信息表';

-- ----------------------------
-- Table structure for ecs_pack
-- ----------------------------
DROP TABLE IF EXISTS `ecs_pack`;
CREATE TABLE `ecs_pack` (
  `pack_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `pack_name` varchar(120) NOT NULL DEFAULT '',
  `pack_img` varchar(255) NOT NULL DEFAULT '',
  `pack_fee` decimal(6,2) unsigned NOT NULL DEFAULT '0.00',
  `free_money` smallint(5) unsigned NOT NULL DEFAULT '0',
  `pack_desc` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`pack_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_package_goods
-- ----------------------------
DROP TABLE IF EXISTS `ecs_package_goods`;
CREATE TABLE `ecs_package_goods` (
  `package_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `goods_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `product_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `goods_number` smallint(5) unsigned NOT NULL DEFAULT '1',
  `admin_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`package_id`,`goods_id`,`admin_id`,`product_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_pay_log
-- ----------------------------
DROP TABLE IF EXISTS `ecs_pay_log`;
CREATE TABLE `ecs_pay_log` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `order_amount` decimal(10,2) unsigned NOT NULL,
  `order_type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_paid` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`log_id`)
) ENGINE=InnoDB AUTO_INCREMENT=22561 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_payment
-- ----------------------------
DROP TABLE IF EXISTS `ecs_payment`;
CREATE TABLE `ecs_payment` (
  `pay_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `pay_code` varchar(20) NOT NULL DEFAULT '',
  `pay_name` varchar(120) NOT NULL DEFAULT '',
  `pay_fee` varchar(10) NOT NULL DEFAULT '0',
  `pay_desc` text NOT NULL,
  `pay_order` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `pay_config` text NOT NULL,
  `enabled` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_cod` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_online` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `pay_logo` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`pay_id`),
  UNIQUE KEY `pay_code` (`pay_code`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_plugins
-- ----------------------------
DROP TABLE IF EXISTS `ecs_plugins`;
CREATE TABLE `ecs_plugins` (
  `code` varchar(30) NOT NULL DEFAULT '',
  `version` varchar(10) NOT NULL DEFAULT '',
  `library` varchar(255) NOT NULL DEFAULT '',
  `assign` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `install_date` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_products
-- ----------------------------
DROP TABLE IF EXISTS `ecs_products`;
CREATE TABLE `ecs_products` (
  `product_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `goods_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `goods_attr` varchar(50) DEFAULT NULL,
  `product_sn` varchar(60) DEFAULT NULL,
  `product_number` smallint(5) unsigned DEFAULT '0',
  PRIMARY KEY (`product_id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_refund_apply
-- ----------------------------
DROP TABLE IF EXISTS `ecs_refund_apply`;
CREATE TABLE `ecs_refund_apply` (
  `apply_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(10) unsigned NOT NULL,
  `rec_ids` varchar(200) NOT NULL DEFAULT '' COMMENT '表ecs_ordre_goods中的rec_id，多个逗号隔开',
  `apply_user` varchar(30) NOT NULL,
  `apply_user_type` tinyint(4) NOT NULL COMMENT '1:客户;2:商户',
  `apply_time` int(10) unsigned NOT NULL,
  `apply_status` tinyint(4) NOT NULL COMMENT '0:未处理；1：已退货；2：已取消',
  `dispose_user` varchar(30) DEFAULT NULL,
  `dispose_time` int(10) unsigned DEFAULT NULL,
  `return_reason` varchar(200) NOT NULL DEFAULT '' COMMENT '退货原因',
  `wx_send` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '是否发送微信通知',
  `send_time` varchar(20) NOT NULL DEFAULT '' COMMENT '微信通知时间',
  `remark` varchar(100) NOT NULL DEFAULT '' COMMENT '备注',
  PRIMARY KEY (`apply_id`)
) ENGINE=InnoDB AUTO_INCREMENT=761 DEFAULT CHARSET=utf8 COMMENT='团购退货 auto colin';

-- ----------------------------
-- Table structure for ecs_reg_extend_info
-- ----------------------------
DROP TABLE IF EXISTS `ecs_reg_extend_info`;
CREATE TABLE `ecs_reg_extend_info` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(8) unsigned NOT NULL,
  `reg_field_id` int(10) unsigned NOT NULL,
  `content` text NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_reg_fields
-- ----------------------------
DROP TABLE IF EXISTS `ecs_reg_fields`;
CREATE TABLE `ecs_reg_fields` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `reg_field_name` varchar(60) NOT NULL,
  `dis_order` tinyint(3) unsigned NOT NULL DEFAULT '100',
  `display` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_need` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=100 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_region
-- ----------------------------
DROP TABLE IF EXISTS `ecs_region`;
CREATE TABLE `ecs_region` (
  `region_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `region_name` varchar(120) NOT NULL DEFAULT '',
  `region_type` tinyint(1) NOT NULL DEFAULT '2',
  `agency_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`region_id`),
  KEY `parent_id` (`parent_id`),
  KEY `region_type` (`region_type`),
  KEY `agency_id` (`agency_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3410 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_return_reason
-- ----------------------------
DROP TABLE IF EXISTS `ecs_return_reason`;
CREATE TABLE `ecs_return_reason` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `reason` varchar(100) NOT NULL DEFAULT '' COMMENT '原因',
  `other` varchar(200) NOT NULL DEFAULT '' COMMENT '其它原因',
  `enabled` tinyint(2) NOT NULL DEFAULT '0' COMMENT '是否启用:1启用,0不启用',
  `update_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='退货原因';

-- ----------------------------
-- Table structure for ecs_role
-- ----------------------------
DROP TABLE IF EXISTS `ecs_role`;
CREATE TABLE `ecs_role` (
  `role_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `role_name` varchar(60) NOT NULL DEFAULT '',
  `action_list` text NOT NULL,
  `role_describe` text,
  PRIMARY KEY (`role_id`),
  KEY `user_name` (`role_name`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_searchengine
-- ----------------------------
DROP TABLE IF EXISTS `ecs_searchengine`;
CREATE TABLE `ecs_searchengine` (
  `date` date NOT NULL DEFAULT '0000-00-00',
  `searchengine` varchar(20) NOT NULL DEFAULT '',
  `count` mediumint(8) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`date`,`searchengine`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_service_weixin
-- ----------------------------
DROP TABLE IF EXISTS `ecs_service_weixin`;
CREATE TABLE `ecs_service_weixin` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `nick_name` varchar(50) NOT NULL DEFAULT '' COMMENT '呢称',
  `openid` varchar(100) NOT NULL DEFAULT '' COMMENT '微信openid',
  `cityid` int(10) NOT NULL DEFAULT '0' COMMENT '城市id',
  `enable` tinyint(2) NOT NULL DEFAULT '0' COMMENT '是否启用：1启用',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COMMENT='微信客服';

-- ----------------------------
-- Table structure for ecs_sessions
-- ----------------------------
DROP TABLE IF EXISTS `ecs_sessions`;
CREATE TABLE `ecs_sessions` (
  `sesskey` char(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `expiry` int(10) unsigned NOT NULL DEFAULT '0',
  `userid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `adminid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `ip` char(15) NOT NULL DEFAULT '',
  `user_name` varchar(60) NOT NULL,
  `user_rank` tinyint(3) NOT NULL,
  `discount` decimal(3,2) NOT NULL,
  `email` varchar(60) NOT NULL,
  `data` char(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`sesskey`),
  KEY `expiry` (`expiry`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_sessions_data
-- ----------------------------
DROP TABLE IF EXISTS `ecs_sessions_data`;
CREATE TABLE `ecs_sessions_data` (
  `sesskey` varchar(32) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `expiry` int(10) unsigned NOT NULL DEFAULT '0',
  `data` longtext NOT NULL,
  PRIMARY KEY (`sesskey`),
  KEY `expiry` (`expiry`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_shipping
-- ----------------------------
DROP TABLE IF EXISTS `ecs_shipping`;
CREATE TABLE `ecs_shipping` (
  `shipping_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `shipping_code` varchar(20) NOT NULL DEFAULT '',
  `shipping_name` varchar(120) NOT NULL DEFAULT '',
  `shipping_desc` varchar(255) NOT NULL DEFAULT '',
  `insure` varchar(10) NOT NULL DEFAULT '0',
  `support_cod` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `enabled` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `shipping_print` text NOT NULL,
  `print_bg` varchar(255) DEFAULT NULL,
  `config_lable` text,
  `print_model` tinyint(1) DEFAULT '0',
  `shipping_order` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`shipping_id`),
  KEY `shipping_code` (`shipping_code`,`enabled`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_shipping_area
-- ----------------------------
DROP TABLE IF EXISTS `ecs_shipping_area`;
CREATE TABLE `ecs_shipping_area` (
  `shipping_area_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `shipping_area_name` varchar(150) NOT NULL DEFAULT '',
  `shipping_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `configure` text NOT NULL,
  PRIMARY KEY (`shipping_area_id`),
  KEY `shipping_id` (`shipping_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_shop_config
-- ----------------------------
DROP TABLE IF EXISTS `ecs_shop_config`;
CREATE TABLE `ecs_shop_config` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `code` varchar(30) NOT NULL DEFAULT '',
  `type` varchar(10) NOT NULL DEFAULT '',
  `store_range` varchar(255) NOT NULL DEFAULT '',
  `store_dir` varchar(255) NOT NULL DEFAULT '',
  `value` text NOT NULL,
  `sort_order` tinyint(3) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  KEY `parent_id` (`parent_id`)
) ENGINE=MyISAM AUTO_INCREMENT=907 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_showtype
-- ----------------------------
DROP TABLE IF EXISTS `ecs_showtype`;
CREATE TABLE `ecs_showtype` (
  `st_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `st_name` varchar(100) NOT NULL DEFAULT '',
  `st_pid` int(10) NOT NULL DEFAULT '0' COMMENT '品牌ID',
  `sort_order` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`st_id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_sms_send_log
-- ----------------------------
DROP TABLE IF EXISTS `ecs_sms_send_log`;
CREATE TABLE `ecs_sms_send_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '用户id',
  `mobile_number` varchar(200) NOT NULL DEFAULT '' COMMENT '手机号码',
  `content` varchar(200) NOT NULL DEFAULT '' COMMENT '短信内容',
  `order_sn` varchar(20) NOT NULL DEFAULT '' COMMENT '订单号',
  `result` tinyint(2) NOT NULL DEFAULT '0' COMMENT '发送结果:1成功',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `dfrom` tinyint(2) NOT NULL DEFAULT '0' COMMENT '来源:1旧版，0新版',
  `wx_send` tinyint(2) NOT NULL DEFAULT '0' COMMENT '是否已发送微信提醒',
  `send_time` datetime DEFAULT NULL COMMENT '微信发送时间',
  `remark` varchar(100) NOT NULL DEFAULT '' COMMENT '备注',
  PRIMARY KEY (`id`),
  KEY `idx_userid_mn` (`user_id`,`mobile_number`),
  KEY `idx_ordresn` (`order_sn`)
) ENGINE=InnoDB AUTO_INCREMENT=654 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_snatch_log
-- ----------------------------
DROP TABLE IF EXISTS `ecs_snatch_log`;
CREATE TABLE `ecs_snatch_log` (
  `log_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `snatch_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `user_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `bid_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `bid_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`log_id`),
  KEY `snatch_id` (`snatch_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_stats
-- ----------------------------
DROP TABLE IF EXISTS `ecs_stats`;
CREATE TABLE `ecs_stats` (
  `access_time` int(10) unsigned NOT NULL DEFAULT '0',
  `ip_address` varchar(15) NOT NULL DEFAULT '',
  `visit_times` smallint(5) unsigned NOT NULL DEFAULT '1',
  `browser` varchar(60) NOT NULL DEFAULT '',
  `system` varchar(20) NOT NULL DEFAULT '',
  `language` varchar(20) NOT NULL DEFAULT '',
  `area` varchar(30) NOT NULL DEFAULT '',
  `referer_domain` varchar(100) NOT NULL DEFAULT '',
  `referer_path` varchar(200) NOT NULL DEFAULT '',
  `access_url` varchar(255) NOT NULL DEFAULT '',
  KEY `access_time` (`access_time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_stores_weixin
-- ----------------------------
DROP TABLE IF EXISTS `ecs_stores_weixin`;
CREATE TABLE `ecs_stores_weixin` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `gs_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商户id',
  `openid` varchar(100) NOT NULL DEFAULT '' COMMENT '微信openid',
  `nickname` varchar(50) NOT NULL DEFAULT '' COMMENT '微信昵称',
  `headimgurl` varchar(200) NOT NULL DEFAULT '' COMMENT '微信用户头像地址',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '添加时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_gidpid` (`gs_id`,`openid`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COMMENT='商户绑定的微信';

-- ----------------------------
-- Table structure for ecs_suppliers
-- ----------------------------
DROP TABLE IF EXISTS `ecs_suppliers`;
CREATE TABLE `ecs_suppliers` (
  `suppliers_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `suppliers_name` varchar(255) DEFAULT NULL,
  `suppliers_desc` mediumtext,
  `is_check` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`suppliers_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_tag
-- ----------------------------
DROP TABLE IF EXISTS `ecs_tag`;
CREATE TABLE `ecs_tag` (
  `tag_id` mediumint(8) NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `goods_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `tag_words` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`tag_id`),
  KEY `user_id` (`user_id`),
  KEY `goods_id` (`goods_id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_template
-- ----------------------------
DROP TABLE IF EXISTS `ecs_template`;
CREATE TABLE `ecs_template` (
  `filename` varchar(30) NOT NULL DEFAULT '',
  `region` varchar(40) NOT NULL DEFAULT '',
  `library` varchar(40) NOT NULL DEFAULT '',
  `sort_order` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `number` tinyint(1) unsigned NOT NULL DEFAULT '5',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `theme` varchar(60) NOT NULL DEFAULT '',
  `remarks` varchar(30) NOT NULL DEFAULT '',
  KEY `filename` (`filename`,`region`),
  KEY `theme` (`theme`),
  KEY `remarks` (`remarks`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_topic
-- ----------------------------
DROP TABLE IF EXISTS `ecs_topic`;
CREATE TABLE `ecs_topic` (
  `topic_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '''''',
  `intro` text NOT NULL,
  `start_time` int(11) NOT NULL DEFAULT '0',
  `end_time` int(10) NOT NULL DEFAULT '0',
  `data` text NOT NULL,
  `template` varchar(255) NOT NULL DEFAULT '''''',
  `css` text NOT NULL,
  `topic_img` varchar(255) DEFAULT NULL,
  `title_pic` varchar(255) DEFAULT NULL,
  `base_style` char(6) DEFAULT NULL,
  `htmls` mediumtext,
  `keywords` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  KEY `topic_id` (`topic_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_user_account
-- ----------------------------
DROP TABLE IF EXISTS `ecs_user_account`;
CREATE TABLE `ecs_user_account` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `admin_user` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `add_time` int(10) NOT NULL DEFAULT '0',
  `paid_time` int(10) NOT NULL DEFAULT '0',
  `admin_note` varchar(255) NOT NULL,
  `user_note` varchar(255) NOT NULL,
  `process_type` tinyint(1) NOT NULL DEFAULT '0',
  `payment` varchar(90) NOT NULL,
  `is_paid` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `is_paid` (`is_paid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_user_address
-- ----------------------------
DROP TABLE IF EXISTS `ecs_user_address`;
CREATE TABLE `ecs_user_address` (
  `address_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `address_name` varchar(50) NOT NULL DEFAULT '',
  `user_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `consignee` varchar(60) NOT NULL DEFAULT '' COMMENT '收货人',
  `email` varchar(60) NOT NULL DEFAULT '',
  `country` smallint(5) NOT NULL DEFAULT '0' COMMENT '国家',
  `province` smallint(5) NOT NULL DEFAULT '0' COMMENT '省份id',
  `province_name` varchar(50) NOT NULL DEFAULT '' COMMENT '省份名称',
  `city` smallint(5) NOT NULL DEFAULT '0' COMMENT '城市id',
  `city_name` varchar(50) NOT NULL DEFAULT '' COMMENT '城市名称',
  `district` smallint(5) NOT NULL DEFAULT '0' COMMENT '区id',
  `district_name` varchar(50) NOT NULL DEFAULT '' COMMENT '地区名称',
  `address` varchar(120) NOT NULL DEFAULT '' COMMENT '详细地址',
  `zipcode` varchar(60) NOT NULL DEFAULT '',
  `tel` varchar(60) NOT NULL DEFAULT '' COMMENT '电话',
  `mobile` varchar(60) NOT NULL DEFAULT '' COMMENT '手机',
  `sex` tinyint(4) NOT NULL DEFAULT '0' COMMENT '性别:1男,2女',
  `is_default` tinyint(2) DEFAULT '0' COMMENT '是否默认址:1是,0否',
  `sign_building` varchar(120) NOT NULL DEFAULT '',
  `best_time` varchar(120) NOT NULL DEFAULT '',
  `update_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`address_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=8954 DEFAULT CHARSET=utf8 COMMENT='收货地址';

-- ----------------------------
-- Table structure for ecs_user_address_tmp
-- ----------------------------
DROP TABLE IF EXISTS `ecs_user_address_tmp`;
CREATE TABLE `ecs_user_address_tmp` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL COMMENT '用户id',
  `address` varchar(200) NOT NULL DEFAULT '' COMMENT '收货地址',
  `uname` varchar(20) NOT NULL DEFAULT '' COMMENT '收货人',
  `tel` varchar(20) NOT NULL DEFAULT '' COMMENT '联系电话',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '添加时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_uid` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='临时收货地址';

-- ----------------------------
-- Table structure for ecs_user_bonus
-- ----------------------------
DROP TABLE IF EXISTS `ecs_user_bonus`;
CREATE TABLE `ecs_user_bonus` (
  `bonus_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `bonus_type_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `bonus_sn` varchar(20) NOT NULL DEFAULT '0',
  `user_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `used_time` int(10) unsigned NOT NULL DEFAULT '0',
  `order_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `emailed` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `buy_user_id` int(10) NOT NULL DEFAULT '0' COMMENT '购买会员ID',
  `bonus_pass` varchar(100) NOT NULL,
  `bonus_salt` varchar(100) NOT NULL,
  `bonus_order_sn` varchar(100) NOT NULL,
  `bonus_pass_exp` varchar(100) NOT NULL COMMENT '明文',
  `ad_id` int(10) NOT NULL DEFAULT '0' COMMENT '卡样ID',
  `bonus_money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '幸福卡面值',
  `used_money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '已使用金额',
  `balance` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '余额',
  `delay_count` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '延期次数',
  `bonus_status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '状态：1正在使用，2已用完，3已过期，4已作废',
  `ban_stores_id` longtext NOT NULL COMMENT '禁止使用门店ID',
  `bonus_company` varchar(100) NOT NULL DEFAULT '',
  `bonus_start_date` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '使用开始日期',
  `bonus_end_date` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '使用结束日期',
  PRIMARY KEY (`bonus_id`),
  UNIQUE KEY `bonus_sn` (`bonus_sn`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=102050 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_user_feed
-- ----------------------------
DROP TABLE IF EXISTS `ecs_user_feed`;
CREATE TABLE `ecs_user_feed` (
  `feed_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `value_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `goods_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `feed_type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_feed` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`feed_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_user_message
-- ----------------------------
DROP TABLE IF EXISTS `ecs_user_message`;
CREATE TABLE `ecs_user_message` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `title` varchar(80) NOT NULL COMMENT '标题',
  `message` text NOT NULL COMMENT '系统消息内容',
  `nums` int(5) NOT NULL DEFAULT '0' COMMENT '推送人数',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '添加时间',
  PRIMARY KEY (`id`),
  KEY `idx_title` (`title`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=32114 DEFAULT CHARSET=utf8 COMMENT='系统消息表 colin';

-- ----------------------------
-- Table structure for ecs_user_message_records
-- ----------------------------
DROP TABLE IF EXISTS `ecs_user_message_records`;
CREATE TABLE `ecs_user_message_records` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `user_id` int(10) NOT NULL DEFAULT '0' COMMENT '用户id',
  `message_id` int(10) NOT NULL COMMENT '关联系统消息表id',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否已读：0未读：1已读；',
  `wx_notice` tinyint(2) NOT NULL DEFAULT '0' COMMENT '是否有微信通知',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_muid` (`message_id`,`user_id`) USING BTREE,
  KEY `idx_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=30383 DEFAULT CHARSET=utf8 COMMENT='用户消息表-附属关联表 colin';

-- ----------------------------
-- Table structure for ecs_user_payment_code
-- ----------------------------
DROP TABLE IF EXISTS `ecs_user_payment_code`;
CREATE TABLE `ecs_user_payment_code` (
  `pyid` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `pcode` varchar(50) NOT NULL DEFAULT '' COMMENT '二维码字符串',
  `status` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '状态: 1已使用 2已过期',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `create_time_int` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '添加时间，时间戳',
  `change_time` varchar(20) NOT NULL DEFAULT '' COMMENT '状态改变的时间',
  PRIMARY KEY (`pyid`),
  UNIQUE KEY `uq_pcode` (`pcode`),
  KEY `idx_uid` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1313 DEFAULT CHARSET=utf8 COMMENT='用户付款码';

-- ----------------------------
-- Table structure for ecs_user_rank
-- ----------------------------
DROP TABLE IF EXISTS `ecs_user_rank`;
CREATE TABLE `ecs_user_rank` (
  `rank_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `rank_name` varchar(30) NOT NULL DEFAULT '',
  `min_points` int(10) unsigned NOT NULL DEFAULT '0',
  `max_points` int(10) unsigned NOT NULL DEFAULT '0',
  `discount` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `show_price` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `special_rank` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`rank_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_users
-- ----------------------------
DROP TABLE IF EXISTS `ecs_users`;
CREATE TABLE `ecs_users` (
  `user_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(60) NOT NULL DEFAULT '',
  `user_name` varchar(60) NOT NULL DEFAULT '' COMMENT '用户名',
  `password` varchar(32) NOT NULL DEFAULT '',
  `question` varchar(255) NOT NULL DEFAULT '',
  `answer` varchar(255) NOT NULL DEFAULT '',
  `sex` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '性别:1男,2女',
  `birthday` date NOT NULL DEFAULT '0000-00-00',
  `user_money` decimal(10,2) NOT NULL DEFAULT '0.00',
  `frozen_money` decimal(10,2) NOT NULL DEFAULT '0.00',
  `pay_points` int(10) unsigned NOT NULL DEFAULT '0',
  `rank_points` int(10) unsigned NOT NULL DEFAULT '0',
  `address_id` mediumint(8) unsigned NOT NULL DEFAULT '1' COMMENT '现默认为1，如果开发配送地址 改为0',
  `reg_time` int(10) unsigned NOT NULL DEFAULT '0',
  `last_login` int(11) unsigned NOT NULL DEFAULT '0',
  `last_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_ip` varchar(15) NOT NULL DEFAULT '',
  `visit_count` smallint(5) unsigned NOT NULL DEFAULT '0',
  `user_rank` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `is_special` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `ec_salt` varchar(10) DEFAULT NULL,
  `salt` varchar(10) NOT NULL DEFAULT '0',
  `parent_id` mediumint(9) NOT NULL DEFAULT '0',
  `flag` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `alias` varchar(60) NOT NULL,
  `msn` varchar(60) NOT NULL,
  `qq` varchar(20) NOT NULL,
  `office_phone` varchar(20) NOT NULL,
  `home_phone` varchar(20) NOT NULL,
  `mobile_phone` varchar(20) NOT NULL,
  `is_validated` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `credit_line` decimal(10,2) unsigned NOT NULL,
  `passwd_question` varchar(50) DEFAULT NULL,
  `passwd_answer` varchar(255) DEFAULT NULL,
  `faces` varchar(100) NOT NULL DEFAULT '' COMMENT '头像图片地址',
  `user_money_date` int(11) NOT NULL DEFAULT '0' COMMENT '余额到期使用时间',
  `bonus_id` int(10) NOT NULL DEFAULT '0' COMMENT '幸福卡ID',
  `nickname` varchar(100) NOT NULL DEFAULT '',
  `bonus_company` varchar(100) NOT NULL DEFAULT '',
  `device_sn` varchar(100) NOT NULL,
  `device_type` varchar(100) NOT NULL,
  `order_count` int(10) NOT NULL DEFAULT '0',
  `login_device` varchar(50) DEFAULT '' COMMENT '登录设备md5字符',
  `login_token` varchar(50) NOT NULL DEFAULT '' COMMENT '登录token',
  `wx_openid` varchar(100) NOT NULL DEFAULT '' COMMENT '微信openid',
  `read_tips` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '是否已读提示标识: 1已读',
  `commission` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '用户获取佣金',
  `source` int(10) NOT NULL DEFAULT '0' COMMENT '注册来源',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_name` (`user_name`),
  UNIQUE KEY `uniq_token` (`login_token`,`user_name`) USING BTREE,
  KEY `email` (`email`),
  KEY `parent_id` (`parent_id`),
  KEY `flag` (`flag`),
  KEY `idx_openid` (`wx_openid`)
) ENGINE=InnoDB AUTO_INCREMENT=9676 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_virtual_card
-- ----------------------------
DROP TABLE IF EXISTS `ecs_virtual_card`;
CREATE TABLE `ecs_virtual_card` (
  `card_id` mediumint(8) NOT NULL AUTO_INCREMENT,
  `goods_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `card_sn` varchar(60) NOT NULL DEFAULT '',
  `card_password` varchar(60) NOT NULL DEFAULT '',
  `add_date` int(11) NOT NULL DEFAULT '0',
  `end_date` int(11) NOT NULL DEFAULT '0',
  `is_saled` tinyint(1) NOT NULL DEFAULT '0',
  `order_sn` varchar(20) NOT NULL DEFAULT '',
  `crc32` varchar(12) NOT NULL DEFAULT '0',
  PRIMARY KEY (`card_id`),
  KEY `goods_id` (`goods_id`),
  KEY `car_sn` (`card_sn`),
  KEY `is_saled` (`is_saled`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_volume_price
-- ----------------------------
DROP TABLE IF EXISTS `ecs_volume_price`;
CREATE TABLE `ecs_volume_price` (
  `price_type` tinyint(1) unsigned NOT NULL,
  `goods_id` mediumint(8) unsigned NOT NULL,
  `volume_number` smallint(5) unsigned NOT NULL DEFAULT '0',
  `volume_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`price_type`,`goods_id`,`volume_number`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_vote
-- ----------------------------
DROP TABLE IF EXISTS `ecs_vote`;
CREATE TABLE `ecs_vote` (
  `vote_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `vote_name` varchar(250) NOT NULL DEFAULT '',
  `start_time` int(11) unsigned NOT NULL DEFAULT '0',
  `end_time` int(11) unsigned NOT NULL DEFAULT '0',
  `can_multi` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `vote_count` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`vote_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_vote_log
-- ----------------------------
DROP TABLE IF EXISTS `ecs_vote_log`;
CREATE TABLE `ecs_vote_log` (
  `log_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `vote_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `ip_address` varchar(15) NOT NULL DEFAULT '',
  `vote_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`log_id`),
  KEY `vote_id` (`vote_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_vote_option
-- ----------------------------
DROP TABLE IF EXISTS `ecs_vote_option`;
CREATE TABLE `ecs_vote_option` (
  `option_id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `vote_id` smallint(5) unsigned NOT NULL DEFAULT '0',
  `option_name` varchar(250) NOT NULL DEFAULT '',
  `option_count` int(8) unsigned NOT NULL DEFAULT '0',
  `option_order` tinyint(3) unsigned NOT NULL DEFAULT '100',
  PRIMARY KEY (`option_id`),
  KEY `vote_id` (`vote_id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for ecs_wholesale
-- ----------------------------
DROP TABLE IF EXISTS `ecs_wholesale`;
CREATE TABLE `ecs_wholesale` (
  `act_id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `goods_id` mediumint(8) unsigned NOT NULL,
  `goods_name` varchar(255) NOT NULL,
  `rank_ids` varchar(255) NOT NULL,
  `prices` text NOT NULL,
  `enabled` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`act_id`),
  KEY `goods_id` (`goods_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for gift_good_cat
-- ----------------------------
DROP TABLE IF EXISTS `gift_good_cat`;
CREATE TABLE `gift_good_cat` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `name` int(10) NOT NULL,
  `parent_id` int(10) NOT NULL DEFAULT '0' COMMENT '上级id',
  `status` tinyint(1) NOT NULL COMMENT '分类状态1正常2删除',
  PRIMARY KEY (`id`),
  KEY `idx_parentid` (`parent_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='礼品商品类型 autor colin';

-- ----------------------------
-- Table structure for goods_store_token
-- ----------------------------
DROP TABLE IF EXISTS `goods_store_token`;
CREATE TABLE `goods_store_token` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `gs_id` int(10) NOT NULL COMMENT '商户id',
  `token` varchar(50) NOT NULL COMMENT 'token值',
  `updated_at` int(10) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `created_at` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_token` (`token`) USING BTREE,
  KEY `idx_gsid` (`gs_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=184 DEFAULT CHARSET=utf8 COMMENT='商户登录token记录表 auto colin';

-- ----------------------------
-- Table structure for group_base
-- ----------------------------
DROP TABLE IF EXISTS `group_base`;
CREATE TABLE `group_base` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `goods_id` int(10) unsigned NOT NULL COMMENT '商品id',
  `start_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '团购开始时间',
  `end_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '团购结束时间',
  `stock_num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '库存数量',
  `spell_num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '拼购数量',
  `discount_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '团购打折扣',
  `goods_price` varchar(50) NOT NULL DEFAULT '' COMMENT '商品价格',
  `original_price` varchar(50) NOT NULL DEFAULT '' COMMENT '原价',
  `people_num` smallint(3) unsigned NOT NULL DEFAULT '0' COMMENT '开团需要的人数',
  `delivery_model` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '配送方式：1自提，2配送，3其他',
  `city_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '城市id',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '1热门拼团，2团长免单',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态：1可开团，3不可开团',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `idx_goods_id` (`goods_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COMMENT='团购方案表 auto-colin';

-- ----------------------------
-- Table structure for group_crontab
-- ----------------------------
DROP TABLE IF EXISTS `group_crontab`;
CREATE TABLE `group_crontab` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `order_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '订单id',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态 0未处理，1已处理通过，3已处理废弃',
  `note` varchar(255) NOT NULL DEFAULT '' COMMENT '运行结果注释',
  `create_time` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间戳',
  PRIMARY KEY (`id`),
  KEY `idx_order` (`order_id`)
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8 COMMENT='团购订单处理表 auto colin';

-- ----------------------------
-- Table structure for group_info
-- ----------------------------
DROP TABLE IF EXISTS `group_info`;
CREATE TABLE `group_info` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `group_id` int(10) unsigned NOT NULL COMMENT '表（group_base的外键id）',
  `group_user_id` int(10) unsigned NOT NULL COMMENT '团长id',
  `spell_num` smallint(3) unsigned NOT NULL COMMENT '已经拼团人数',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0待成团，1已成团，3已撤销',
  `crontab_status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '团购退货处理状态：0初始化，1已处理，3处理异常',
  `crontab_note` varchar(255) NOT NULL DEFAULT '' COMMENT '团购退货处理说明',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `uni_group_user` (`group_id`,`group_user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8 COMMENT='开团明细表 auto colin';

-- ----------------------------
-- Table structure for group_info_detail
-- ----------------------------
DROP TABLE IF EXISTS `group_info_detail`;
CREATE TABLE `group_info_detail` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `order_id` int(10) unsigned NOT NULL COMMENT '订单 id',
  `group_info_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '开团表id（group_info）',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '状态 0未处理，1已处理通过，3已处理废弃',
  `creat_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `idx_order_id` (`order_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8 COMMENT='参团明细表 auto colin';

-- ----------------------------
-- Table structure for order_settle_base
-- ----------------------------
DROP TABLE IF EXISTS `order_settle_base`;
CREATE TABLE `order_settle_base` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `week` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '周1-52周',
  `year` year(4) NOT NULL COMMENT '年',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '确认状态 0：未确认，1已确认',
  `gs_id` int(10) NOT NULL COMMENT '商户门店id',
  `order_ids` text NOT NULL COMMENT '订单号 通过逗号分隔',
  `settle_money` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '需要结算的金额',
  `amount_money` decimal(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '订单总金额',
  `ip` varchar(255) NOT NULL DEFAULT '0' COMMENT '操作人ip',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '操作人，0为系统',
  `comment` varchar(255) NOT NULL DEFAULT '' COMMENT '操作说明',
  `upate_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`,`gs_id`),
  UNIQUE KEY `un_gyw` (`year`,`week`,`gs_id`) USING BTREE COMMENT '设置门店，年份，星期为唯一索引'
) ENGINE=InnoDB AUTO_INCREMENT=283 DEFAULT CHARSET=utf8 COMMENT='门店订单结算表 -author:colin';

-- ----------------------------
-- Table structure for sec_base
-- ----------------------------
DROP TABLE IF EXISTS `sec_base`;
CREATE TABLE `sec_base` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '秒杀分组id',
  `name` varchar(100) NOT NULL COMMENT '秒杀活动名称',
  `date` date NOT NULL COMMENT '日期',
  `start_time` varchar(20) NOT NULL COMMENT '秒杀开始时刻 09:00',
  `city_id` int(10) NOT NULL DEFAULT '0' COMMENT '城市id',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态:1启用，0不启用',
  `upate_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uni_dsc` (`date`,`start_time`,`city_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8 COMMENT='秒杀活动表 auto-colin';

-- ----------------------------
-- Table structure for sec_kill_goods
-- ----------------------------
DROP TABLE IF EXISTS `sec_kill_goods`;
CREATE TABLE `sec_kill_goods` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `sec_id` int(10) NOT NULL DEFAULT '0' COMMENT '秒杀id-(表sec_base的id)',
  `goods_id` int(10) unsigned NOT NULL COMMENT '商品id',
  `stores_id` varchar(255) DEFAULT NULL COMMENT '门店id',
  `city_id` int(10) NOT NULL DEFAULT '0' COMMENT '秒杀活动的城市id',
  `sort` int(10) NOT NULL DEFAULT '0' COMMENT '排序权重',
  `status` tinyint(1) NOT NULL DEFAULT '3' COMMENT '状态：1正常，3删除',
  `start_time` timestamp NULL DEFAULT NULL COMMENT '秒杀开始时间',
  `create_time` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `idx_goods_id` (`goods_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=70 DEFAULT CHARSET=utf8 COMMENT='秒杀产品表 auto-colin';

-- ----------------------------
-- Table structure for sec_pre
-- ----------------------------
DROP TABLE IF EXISTS `sec_pre`;
CREATE TABLE `sec_pre` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `user_id` int(10) unsigned NOT NULL COMMENT '用户id',
  `goods_id` int(10) unsigned NOT NULL COMMENT '商品id',
  `kill_id` int(10) unsigned NOT NULL COMMENT '秒杀id',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0未通知，1已通知',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uni_ugk` (`user_id`,`goods_id`,`kill_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='秒杀预约表 auto-colin';

-- ----------------------------
-- Table structure for special_base
-- ----------------------------
DROP TABLE IF EXISTS `special_base`;
CREATE TABLE `special_base` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '标题',
  `img` varchar(100) DEFAULT '' COMMENT '图片',
  `city_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '城市id',
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `status` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '状态:1启用，0不启用',
  `pro_id` int(10) unsigned DEFAULT '0' COMMENT '产品表归属id',
  `upate_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COMMENT='专题主表';

-- ----------------------------
-- Table structure for special_pro_list
-- ----------------------------
DROP TABLE IF EXISTS `special_pro_list`;
CREATE TABLE `special_pro_list` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `pro_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '商品id',
  `sort` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `base_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '表special_base的id',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '添加时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_pidbid` (`pro_id`,`base_id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COMMENT='专题商品表';

-- ----------------------------
-- Table structure for store_action_logs
-- ----------------------------
DROP TABLE IF EXISTS `store_action_logs`;
CREATE TABLE `store_action_logs` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `gs_id` int(10) NOT NULL DEFAULT '0' COMMENT '商户id',
  `business` tinyint(3) NOT NULL DEFAULT '0' COMMENT '业务类型',
  `table` varchar(255) NOT NULL DEFAULT '' COMMENT '操作表对象',
  `type` tinyint(1) NOT NULL DEFAULT '0' COMMENT '操作类型：增1改2删3',
  `ip` varchar(25) NOT NULL DEFAULT '0' COMMENT 'ip地址',
  `comment` text COMMENT '操作说明',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `idx_gs_id` (`gs_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=590 DEFAULT CHARSET=utf8 COMMENT='商户操作日志表 auto colin';

-- ----------------------------
-- Table structure for stores_settle_account
-- ----------------------------
DROP TABLE IF EXISTS `stores_settle_account`;
CREATE TABLE `stores_settle_account` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `gs_id` int(11) NOT NULL COMMENT '门店id',
  `real_name` varchar(25) NOT NULL DEFAULT '' COMMENT '开户名称',
  `bank_name` varchar(100) NOT NULL DEFAULT '' COMMENT '银行名称',
  `bank_account` varchar(25) NOT NULL DEFAULT '' COMMENT '银行账号',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '1正常3删除',
  `ip` varchar(255) NOT NULL DEFAULT '0' COMMENT '操作人ip',
  `user_id` int(10) NOT NULL DEFAULT '0' COMMENT '操作人，0为系统',
  `upate_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uni_gs` (`gs_id`) USING BTREE COMMENT '门店id唯一索引'
) ENGINE=InnoDB AUTO_INCREMENT=292 DEFAULT CHARSET=utf8 COMMENT='商户结算_银行账号信息表 -auto:colin';

-- ----------------------------
-- Table structure for user_red_packet
-- ----------------------------
DROP TABLE IF EXISTS `user_red_packet`;
CREATE TABLE `user_red_packet` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `user_id` int(10) NOT NULL COMMENT '用户id',
  `chird_id` int(10) NOT NULL DEFAULT '0' COMMENT '被推荐人id',
  `amount` decimal(10,2) NOT NULL COMMENT '红包金额',
  `used_money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '已用金额',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '状态：1正常，2已用，3过期',
  `type` tinyint(1) NOT NULL DEFAULT '1' COMMENT '获取红包的类型：1注册推荐',
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `use_time` int(10) NOT NULL DEFAULT '0' COMMENT '使用时间',
  `end_time` int(10) NOT NULL DEFAULT '0' COMMENT '有效时间，在结束时间之前',
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='用户红包表 colin';

-- ----------------------------
-- Table structure for user_token
-- ----------------------------
DROP TABLE IF EXISTS `user_token`;
CREATE TABLE `user_token` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `user_id` int(10) NOT NULL COMMENT '用户user_id',
  `token` varchar(50) NOT NULL COMMENT 'token值',
  `updated_at` int(10) NOT NULL DEFAULT '0' COMMENT '更新时间',
  `created_at` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_token` (`token`) USING BTREE,
  KEY `idx_uid` (`user_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=89 DEFAULT CHARSET=utf8 COMMENT='用户登录token记录表 auto colin';

-- ----------------------------
-- Table structure for user_weixin
-- ----------------------------
DROP TABLE IF EXISTS `user_weixin`;
CREATE TABLE `user_weixin` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '用户id',
  `openid` varchar(100) NOT NULL DEFAULT '' COMMENT '微信openid',
  `status` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '状态',
  `remark` varchar(200) NOT NULL DEFAULT '' COMMENT '备注',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_uid` (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='活动临时表';

-- ----------------------------
-- Table structure for wx_pay_refund
-- ----------------------------
DROP TABLE IF EXISTS `wx_pay_refund`;
CREATE TABLE `wx_pay_refund` (
  `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `out_trade_no` varchar(50) NOT NULL DEFAULT '' COMMENT '商户订单号',
  `out_refund_no` varchar(50) NOT NULL DEFAULT '' COMMENT '商户退款单号',
  `total_fee` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '订单总金额，单位为分，只能为整数',
  `refund_fee` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '退款总金额，单位为分，只能为整数',
  `refund_desc` varchar(100) NOT NULL DEFAULT '' COMMENT '退款原因',
  `status` tinyint(2) unsigned NOT NULL DEFAULT '0' COMMENT '状态：0待处理，1完成，2其它',
  `do_time` varchar(20) NOT NULL DEFAULT '' COMMENT '处理时间',
  `refund_id` varchar(30) NOT NULL DEFAULT '' COMMENT '微信退款单号',
  `return_msg` varchar(100) NOT NULL DEFAULT '' COMMENT '处理失败时的说明',
  `add_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '添加时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_sno` (`out_trade_no`,`out_refund_no`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='微信退款表';
