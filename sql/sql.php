<?php
---province   city
--
-- 表的结构 `asf_member`
--

CREATE TABLE IF NOT EXISTS `asf_member` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `username` char(40) NOT NULL,
  `password` char(32) NOT NULL,
  `salt` char(32) DEFAULT NULL,
  `name` char(20) NOT NULL,
  `sex` tinyint(1) DEFAULT NULL COMMENT '性别：0女士；1先生',
  `birthday` varchar(10) DEFAULT NULL,
  `mobile` char(14) DEFAULT NULL,
  `telephone` char(14) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `zip_code` char(6) DEFAULT NULL,
  `last_login_ip` char(15) DEFAULT '',
  `login_count` mediumint(8) unsigned DEFAULT '0' COMMENT '登录次数',
  `create_time` int(10) unsigned NOT NULL COMMENT '注册时间',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '会员状态',
  `referee_id` mediumint(8) unsigned DEFAULT NULL COMMENT '推荐人',
  `user_id` smallint(5) unsigned NOT NULL COMMENT '客服id',
  `address` varchar(50) DEFAULT NULL,
  `points` mediumint(8) unsigned DEFAULT '0' COMMENT '积分',
  `rebate` decimal(5,1) DEFAULT '0.0' COMMENT '用户返利',
  `rank_id` smallint(5) unsigned DEFAULT '1',
  `last_login_time` int(10) unsigned DEFAULT NULL,
  `province` varchar(50) NOT NULL DEFAULT '0',
  `city` varchar(50) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `user_id` (`user_id`),
  KEY `referee_id` (`referee_id`),
  KEY `rank_id` (`rank_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='网站会员' AUTO_INCREMENT=1230 ;

--
-- 限制导出的表
--

--
-- 限制表 `asf_member`
--
ALTER TABLE `asf_member`
  ADD CONSTRAINT `asf_member_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `asf_user` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `asf_member_ibfk_2` FOREIGN KEY (`rank_id`) REFERENCES `asf_member_rank` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `asf_member_ibfk_3` FOREIGN KEY (`referee_id`) REFERENCES `asf_member` (`id`) ON UPDATE CASCADE;
  
--
-- 表的结构 `asf_complaint`
--

CREATE TABLE IF NOT EXISTS `asf_complaint` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` int(11) NOT NULL COMMENT '类型 1投诉  2建议',
  `title` varchar(50) NOT NULL COMMENT '主题',
  `contents` text COMMENT '评价内容',
  `jietu` varchar(100) NOT NULL,
  `member_id` int(11) NOT NULL COMMENT '用户ID',
  `mobile` varchar(20) DEFAULT NULL,
  `email` varchar(20) DEFAULT NULL,
  `ip` varchar(20) DEFAULT NULL,
  `create_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=21 ;

--
-- 表的结构 `asf_evaluat`
--

CREATE TABLE IF NOT EXISTS `asf_evaluat` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL COMMENT '用户id',
  `customerid` int(11) NOT NULL COMMENT '客服id',
  `order_id` int(11) NOT NULL COMMENT '订单id',
  `from_city` int(11) NOT NULL COMMENT '出发城市',
  `to_city` int(11) NOT NULL COMMENT '到达城市',
  `total` tinyint(1) NOT NULL DEFAULT '1' COMMENT '总体评价',
  `manner` tinyint(1) NOT NULL DEFAULT '1' COMMENT '服务态度',
  `specialty` tinyint(1) NOT NULL DEFAULT '1' COMMENT '专业性',
  `price` tinyint(1) NOT NULL DEFAULT '1' COMMENT '价格',
  `contents` text COMMENT '评价内容',
  `create_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `asf_points` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL COMMENT '用户id',
  `points` int(11) DEFAULT '0.0' COMMENT '返积分',
  `type` int DEFAULT '0' COMMENT '积分类型  0 注册 ',
  `description` varchar(100) COMMENT '描述',
  `create_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;