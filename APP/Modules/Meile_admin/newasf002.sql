-- phpMyAdmin SQL Dump
-- version 4.0.4
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2013 年 12 月 18 日 10:22
-- 服务器版本: 5.6.12-log
-- PHP 版本: 5.4.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `newasf002`
--
CREATE DATABASE IF NOT EXISTS `newasf002` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `newasf002`;

-- --------------------------------------------------------

--
-- 表的结构 `asf_asms_member`
--

CREATE TABLE IF NOT EXISTS `asf_asms_member` (
  `hyid` bigint(20) unsigned NOT NULL,
  `hylx` varchar(10) DEFAULT NULL COMMENT '会员类型',
  `hyzcm` varchar(30) DEFAULT NULL COMMENT '注册名',
  `xm` varchar(30) DEFAULT NULL COMMENT '会员姓名',
  `hykh` varchar(30) DEFAULT NULL COMMENT '会员卡号',
  `zjhm` varchar(30) DEFAULT NULL COMMENT '证件号码',
  `sj` varchar(15) DEFAULT NULL COMMENT '联系电话',
  `lxdz` varchar(100) DEFAULT NULL COMMENT '联系地址',
  `dqjf` float(10,2) DEFAULT NULL COMMENT '累积积分',
  `kyjf` float(10,2) DEFAULT NULL COMMENT '可用积分',
  `jfyxrq` varchar(20) DEFAULT NULL COMMENT '积分有效期',
  `zjlx` varchar(20) DEFAULT NULL COMMENT '证件类型',
  `zhcprq` varchar(20) DEFAULT NULL COMMENT '最后出票时间',
  `cjrq` varchar(20) DEFAULT NULL COMMENT '注册时间',
  `ywy` varchar(20) NOT NULL COMMENT '业务员',
  `update_time` int(11) DEFAULT NULL,
  `xb` varchar(5) DEFAULT NULL COMMENT '性别',
  `sshy` varchar(5) DEFAULT NULL COMMENT '"NI" >身份证 "PP" >护照 "ID" >其它',
  `zjlx2` varchar(20) DEFAULT NULL COMMENT '护照类型',
  `zjhm2` varchar(20) DEFAULT NULL COMMENT '护照号码',
  `zjyxq2` varchar(30) DEFAULT NULL COMMENT '护照有效期',
  `csrq` varchar(30) DEFAULT NULL COMMENT '出生日期',
  `hydj` varchar(30) DEFAULT NULL COMMENT '会员等级',
  `ywyid` varchar(30) DEFAULT NULL COMMENT '拓展业务员id',
  `bzbz1` varchar(20) DEFAULT NULL COMMENT '会员QQ',
  `bzbz2` varchar(30) DEFAULT NULL COMMENT '会员MSN',
  `bzbz3` varchar(30) DEFAULT NULL COMMENT '昵称',
  `ywmc` varchar(30) DEFAULT NULL COMMENT '英文名称',
  `yzbm` varchar(10) DEFAULT NULL COMMENT '邮政编码',
  `jtdh` varchar(20) DEFAULT NULL COMMENT '住址电话',
  `email` varchar(30) DEFAULT NULL COMMENT '电子邮箱',
  `gzdw` varchar(50) DEFAULT NULL COMMENT '公司名称',
  `swzh` varchar(20) DEFAULT NULL COMMENT '工作职务',
  `gsdh` varchar(10) DEFAULT NULL COMMENT '公司电话',
  `gscz` varchar(10) DEFAULT NULL COMMENT '公司传真',
  `szsf` varchar(20) DEFAULT NULL COMMENT '所在国家或省份',
  `szdqmc` varchar(30) DEFAULT NULL COMMENT '所在地区',
  `www` varchar(100) DEFAULT NULL COMMENT '公司网址',
  `gsdz` varchar(100) DEFAULT NULL COMMENT '公司地址',
  `zj_khbh` varchar(100) DEFAULT NULL COMMENT '凭证代码',
  `jthmc` varchar(50) DEFAULT NULL COMMENT '集团号',
  `tsyq` text COMMENT '特殊要求',
  `bzbz` text COMMENT '备注',
  `userid` varchar(20) DEFAULT NULL,
  `zt` tinyint(1) DEFAULT NULL COMMENT '状态',
  PRIMARY KEY (`hyid`),
  KEY `hyzcm` (`hyzcm`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `asf_asms_order`
--

CREATE TABLE IF NOT EXISTS `asf_asms_order` (
  `ddbh` bigint(20) NOT NULL,
  `ddzt` int(11) DEFAULT NULL,
  `lx` varchar(20) DEFAULT NULL COMMENT '机票类型',
  `qfsj` varchar(30) DEFAULT NULL COMMENT '起飞时间',
  `cw` varchar(5) DEFAULT NULL COMMENT '舱位等级',
  `hc` varchar(20) DEFAULT NULL COMMENT '航班行程',
  `xm` varchar(30) DEFAULT NULL COMMENT '姓名',
  `cklx` varchar(20) DEFAULT NULL COMMENT '乘客类型',
  `rs` int(11) DEFAULT NULL COMMENT '人数',
  `sshy` varchar(10) DEFAULT NULL COMMENT '证件类型',
  `zjhm` varchar(30) DEFAULT NULL COMMENT '证件号码',
  `sf` float(10,2) DEFAULT NULL COMMENT '参考税',
  `ysje` float(10,2) DEFAULT NULL COMMENT '应付金额',
  `dprq` varchar(30) DEFAULT NULL COMMENT '下单时间',
  `hykh` varchar(30) DEFAULT NULL COMMENT '会员卡号',
  `hyid` bigint(20) DEFAULT NULL COMMENT '会员id',
  `pnr` varchar(20) DEFAULT NULL,
  `pnr_zt` varchar(10) DEFAULT NULL,
  `hbh` varchar(10) DEFAULT NULL COMMENT '行班号',
  `pay` varchar(20) DEFAULT NULL COMMENT '支付',
  `yf` float DEFAULT NULL COMMENT '已付金额',
  `email` varchar(50) DEFAULT NULL,
  `nklxr` varchar(30) DEFAULT NULL COMMENT '旅客联系人 ',
  `lxdh` varchar(20) DEFAULT NULL COMMENT '联系电话',
  `update_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`ddbh`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `asf_asms_user`
--

CREATE TABLE IF NOT EXISTS `asf_asms_user` (
  `ywyid` varchar(20) NOT NULL COMMENT '编号',
  `name` varchar(30) NOT NULL COMMENT '姓名',
  `status` tinyint(1) NOT NULL COMMENT '状态',
  `tel` varchar(20) DEFAULT NULL COMMENT '联系电话',
  `phone` varchar(12) DEFAULT NULL COMMENT '手机',
  `qxjb` varchar(30) NOT NULL COMMENT '权限 级别',
  `yhlx` varchar(50) NOT NULL COMMENT '用户类型',
  `fjzt` tinyint(1) NOT NULL COMMENT '发送 短信',
  `wscx` varchar(30) NOT NULL COMMENT '呼叫 中心',
  `email` varchar(100) NOT NULL COMMENT '电子信箱',
  `address` varchar(100) NOT NULL COMMENT '联系地址',
  `department` varchar(30) NOT NULL COMMENT '所属营业部',
  `company` varchar(30) NOT NULL COMMENT '所属公司',
  `create_date` varchar(20) NOT NULL COMMENT '创建时间',
  `update_time` int(11) NOT NULL,
  PRIMARY KEY (`ywyid`),
  KEY `status` (`status`),
  KEY `create_date` (`create_date`),
  KEY `update_time` (`update_time`),
  KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
