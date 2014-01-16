<?php
---province   city
--
-- ��Ľṹ `asf_member`
--

CREATE TABLE IF NOT EXISTS `asf_member` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `username` char(40) NOT NULL,
  `password` char(32) NOT NULL,
  `salt` char(32) DEFAULT NULL,
  `name` char(20) NOT NULL,
  `sex` tinyint(1) DEFAULT NULL COMMENT '�Ա�0Ůʿ��1����',
  `birthday` varchar(10) DEFAULT NULL,
  `mobile` char(14) DEFAULT NULL,
  `telephone` char(14) DEFAULT NULL,
  `email` varchar(50) DEFAULT NULL,
  `zip_code` char(6) DEFAULT NULL,
  `last_login_ip` char(15) DEFAULT '',
  `login_count` mediumint(8) unsigned DEFAULT '0' COMMENT '��¼����',
  `create_time` int(10) unsigned NOT NULL COMMENT 'ע��ʱ��',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '��Ա״̬',
  `referee_id` mediumint(8) unsigned DEFAULT NULL COMMENT '�Ƽ���',
  `user_id` smallint(5) unsigned NOT NULL COMMENT '�ͷ�id',
  `address` varchar(50) DEFAULT NULL,
  `points` mediumint(8) unsigned DEFAULT '0' COMMENT '����',
  `rebate` decimal(5,1) DEFAULT '0.0' COMMENT '�û�����',
  `rank_id` smallint(5) unsigned DEFAULT '1',
  `last_login_time` int(10) unsigned DEFAULT NULL,
  `province` varchar(50) NOT NULL DEFAULT '0',
  `city` varchar(50) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `user_id` (`user_id`),
  KEY `referee_id` (`referee_id`),
  KEY `rank_id` (`rank_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='��վ��Ա' AUTO_INCREMENT=1230 ;

--
-- ���Ƶ����ı�
--

--
-- ���Ʊ� `asf_member`
--
ALTER TABLE `asf_member`
  ADD CONSTRAINT `asf_member_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `asf_user` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `asf_member_ibfk_2` FOREIGN KEY (`rank_id`) REFERENCES `asf_member_rank` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `asf_member_ibfk_3` FOREIGN KEY (`referee_id`) REFERENCES `asf_member` (`id`) ON UPDATE CASCADE;
  
--
-- ��Ľṹ `asf_complaint`
--

CREATE TABLE IF NOT EXISTS `asf_complaint` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` int(11) NOT NULL COMMENT '���� 1Ͷ��  2����',
  `title` varchar(50) NOT NULL COMMENT '����',
  `contents` text COMMENT '��������',
  `jietu` varchar(100) NOT NULL,
  `member_id` int(11) NOT NULL COMMENT '�û�ID',
  `mobile` varchar(20) DEFAULT NULL,
  `email` varchar(20) DEFAULT NULL,
  `ip` varchar(20) DEFAULT NULL,
  `create_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=21 ;

--
-- ��Ľṹ `asf_evaluat`
--

CREATE TABLE IF NOT EXISTS `asf_evaluat` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL COMMENT '�û�id',
  `customerid` int(11) NOT NULL COMMENT '�ͷ�id',
  `order_id` int(11) NOT NULL COMMENT '����id',
  `from_city` int(11) NOT NULL COMMENT '��������',
  `to_city` int(11) NOT NULL COMMENT '�������',
  `total` tinyint(1) NOT NULL DEFAULT '1' COMMENT '��������',
  `manner` tinyint(1) NOT NULL DEFAULT '1' COMMENT '����̬��',
  `specialty` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'רҵ��',
  `price` tinyint(1) NOT NULL DEFAULT '1' COMMENT '�۸�',
  `contents` text COMMENT '��������',
  `create_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `asf_points` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL COMMENT '�û�id',
  `points` int(11) DEFAULT '0.0' COMMENT '������',
  `type` int DEFAULT '0' COMMENT '��������  0 ע�� ',
  `description` varchar(100) COMMENT '����',
  `create_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;