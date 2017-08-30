-- phpMyAdmin SQL Dump
-- version 4.1.14
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 2017-08-30 12:41:03
-- 服务器版本： 5.6.17
-- PHP Version: 5.5.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `twoasystem`
--

-- --------------------------------------------------------

--
-- 表的结构 `oa_archives`
--

CREATE TABLE IF NOT EXISTS `oa_archives` (
  `archives_id` int(9) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `archives_usercode` bigint(10) NOT NULL COMMENT '关联oa_user的user_code',
  `archives_degree` varchar(20) NOT NULL DEFAULT '' COMMENT '学历',
  `archives_idn` bigint(18) NOT NULL COMMENT '身份证号码',
  `archives_idexp` date DEFAULT NULL COMMENT '身份证到期日期',
  `archives_bank` varchar(100) DEFAULT '' COMMENT '银行信息',
  `archives_enformp` varchar(200) DEFAULT '' COMMENT '入职表扫描件',
  `archives_cvp` varchar(200) DEFAULT '' COMMENT '简历扫描件',
  `archives_degreep` varchar(200) DEFAULT '' COMMENT '学历证书扫描件',
  `archives_idp` varchar(200) DEFAULT '' COMMENT '身份证扫描件',
  `archives_horp` varchar(200) DEFAULT '' COMMENT '户口本扫描件',
  `archives_bankp` varchar(200) DEFAULT '' COMMENT '银行卡扫描件',
  `archives_physicalp` varchar(200) DEFAULT '' COMMENT '体检报告扫描件',
  `archives_cultivatep` varchar(200) DEFAULT '' COMMENT '培训记录扫描件',
  `archives_receivep` varchar(200) DEFAULT '' COMMENT '领取物品扫描件',
  `archives_agreement` varchar(200) DEFAULT '' COMMENT '劳动合同扫描件',
  `archives_secretp` varchar(200) DEFAULT '' COMMENT '保密合同扫描件',
  `archives_quitp` varchar(200) DEFAULT '' COMMENT '离职扫描件',
  PRIMARY KEY (`archives_id`),
  UNIQUE KEY `archives_usercode` (`archives_usercode`),
  UNIQUE KEY `archives_idn` (`archives_idn`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- 转存表中的数据 `oa_archives`
--

INSERT INTO `oa_archives` (`archives_id`, `archives_usercode`, `archives_degree`, `archives_idn`, `archives_idexp`, `archives_bank`, `archives_enformp`, `archives_cvp`, `archives_degreep`, `archives_idp`, `archives_horp`, `archives_bankp`, `archives_physicalp`, `archives_cultivatep`, `archives_receivep`, `archives_agreement`, `archives_secretp`, `archives_quitp`) VALUES
(1, 1000000114, '硕士', 100000011410000001, '2035-11-14', '10000001141000000114', '/twoa/Public/images/upload/archives/enformp/1000000114.pdf', '/twoa/Public/images/upload/archives/cvp/1000000114.pdf', '/twoa/Public/images/upload/archives/degreep/1000000114.pdf', '/twoa/Public/images/upload/archives/idp/1000000114.pdf', '/twoa/Public/images/upload/archives/horp/1000000114.pdf', '', '', '', '/twoa/Public/images/upload/archives/receivep/1000000114.png', '', '', ''),
(2, 1000000115, '硕士', 100000011510000001, '2017-08-31', '10000001151000000115', '/twoa/Public/images/upload/archives/enformp/1000000115.pdf', '/twoa/Public/images/upload/archives/cvp/1000000115.pdf', '/twoa/Public/images/upload/archives/degreep/1000000115.pdf', '', '/twoa/Public/images/upload/archives/horp/1000000115.jpeg', '', '', '', '', '/twoa/Public/images/upload/archives/agreement/1000000115.pdf', '', ''),
(3, 1000000117, '博士', 100000011710000001, '2017-08-17', '10000001171000000117', '/twoa/Public/images/upload/archives/enformp/1000000117.png', '', '', '', '', '', '', '', '/twoa/Public/images/upload/archives/receivep/1000000117.png', '', '', ''),
(4, 1000000121, '初中', 1000000121, '2017-08-30', '1000000121', '/twoa/Public/images/upload/archives/enformp/1000000121.png', '', '', '', '', '', '', '', '', '', '', ''),
(5, 1000000125, '博士', 100000012510000001, '2017-10-01', '10000001251000000125', '/twoa/Public/images/upload/archives/enformp/1000000125.pdf', '', '', '', '', '', '', '', '', '', '', '');

-- --------------------------------------------------------

--
-- 表的结构 `oa_attend_apply`
--

CREATE TABLE IF NOT EXISTS `oa_attend_apply` (
  `aapply_id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '打卡记录id',
  `aapply_code` bigint(20) DEFAULT NULL,
  `aapply_type` int(3) DEFAULT '0' COMMENT '申请类型，加班、外请等',
  `aapply_inday` int(3) DEFAULT '0',
  `aapply_addtime` datetime DEFAULT NULL COMMENT '申请时间',
  `aapply_schedule` date DEFAULT NULL COMMENT '计划时间',
  `aapply_days` int(3) DEFAULT '0',
  `aapply_hours` float(4,2) DEFAULT '0.00',
  `aapply_reason` varchar(200) DEFAULT '',
  `aapply_project` varchar(300) DEFAULT '' COMMENT '涉及项目',
  `aapply_proof` varchar(300) DEFAULT '' COMMENT '证明文件',
  `aapply_approve` varchar(200) DEFAULT '',
  `aapply_state` int(1) DEFAULT '0',
  `aapply_operation` varchar(100) DEFAULT '',
  `aapply_remark` varchar(200) DEFAULT '',
  PRIMARY KEY (`aapply_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=35 ;

--
-- 转存表中的数据 `oa_attend_apply`
--

INSERT INTO `oa_attend_apply` (`aapply_id`, `aapply_code`, `aapply_type`, `aapply_inday`, `aapply_addtime`, `aapply_schedule`, `aapply_days`, `aapply_hours`, `aapply_reason`, `aapply_project`, `aapply_proof`, `aapply_approve`, `aapply_state`, `aapply_operation`, `aapply_remark`) VALUES
(1, 1000000107, 2, 0, '2017-08-07 12:00:00', '2017-08-08', 0, 3.00, '外出啊', '', '', '1111111', 0, '', ''),
(5, 1000000107, 2, 1, '2017-08-14 08:00:00', '2017-08-14', 0, 3.00, '外勤', '', '', '123456789', 1, '', ''),
(6, 1000000107, 3, 1, '2017-08-14 08:00:00', '2017-08-14', 0, 0.00, '加班啊', '', '', '123456789', 1, '', ''),
(8, 1000000107, 3, 1, '2017-08-18 18:30:00', '2017-08-18', 2, 0.00, '周六日加班', '', '', '123456789', 1, '', ''),
(9, 1000000107, 2, 1, '2017-08-20 18:30:00', '2017-08-21', 0, 0.00, '外勤', '', '', '123456789', 1, '', ''),
(10, 1000000107, 3, 1, '2017-08-20 18:30:00', '2017-08-21', 0, 0.00, '加班', '', '', '123456789', 1, '', ''),
(16, 1000000107, 3, 0, '2017-08-25 00:00:00', '2017-08-25', 0, 0.00, 'print_r($applyArray);', '', '', '1000000116', 0, '', ''),
(27, 1000000107, 7, 1, '2017-08-25 16:28:38', '2017-08-25', 0, 1.00, ',$aapply_inday', '', '', '1000000116', 0, '', ''),
(28, 1000000107, 2, 2, '2017-08-25 16:32:05', '2017-08-25', 0, 0.00, '&lt;input type=&quot;hidden&quot; class=&quot;apply-info&quot; name=&quot;aapply_type&quot; value=&quot;7&quot;&gt;', '', '', '1000000116', 0, '', ''),
(29, 1000000107, 2, 3, '2017-08-25 16:32:11', '2017-08-25', 0, 0.00, '&lt;input type=&quot;hidden&quot; class=&quot;apply-info&quot; name=&quot;aapply_type&quot; value=&quot;7&quot;&gt;', '', '', '1000000116', 0, '', ''),
(30, 1000000107, 3, 0, '2017-08-29 10:54:10', '2017-08-29', 0, 0.00, '一般2天左右', '', '', '1000000116', 0, '', ''),
(31, 1000000107, 5, 0, '2017-08-29 11:06:02', '2017-08-29', 0, 0.00, '一般2天左右', '', '', '1000000116', 0, '', ''),
(32, 1000000107, 6, 0, '2017-08-29 11:06:10', '2017-08-29', 0, 1.00, '一般2天左右', '', '', '1000000116', 0, '', ''),
(33, 1000000107, 3, 3, '2017-08-30 16:20:50', '2017-08-30', 0, 0.00, '3，普通加班', '', '', '1000000116', 0, '', ''),
(34, 1000000107, 7, 2, '2017-08-30 17:23:33', '2017-09-01', 0, 0.00, '|| I(&quot;type&quot;)==7', '', '', '1000000116', 0, '', '');

-- --------------------------------------------------------

--
-- 表的结构 `oa_attend_checkin`
--

CREATE TABLE IF NOT EXISTS `oa_attend_checkin` (
  `acheckin_id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '打卡记录id',
  `acheckin_code` bigint(10) DEFAULT NULL COMMENT '对应的用户code',
  `acheckin_checkinway` int(3) DEFAULT '0' COMMENT '打卡的方法，1定位，2拍照',
  `acheckin_type` int(3) DEFAULT '0' COMMENT '打卡的类型，1正常上下班，2外勤，3加班',
  `acheckin_timetype` int(3) DEFAULT '0' COMMENT '打卡时间类型，1开始，2结束',
  `acheckin_addtime` datetime DEFAULT NULL,
  `acheckin_checkintime` datetime DEFAULT NULL COMMENT '打开产生的时间',
  `acheckin_location` varchar(300) DEFAULT '' COMMENT '位置文本',
  `acheckin_longlat` varchar(50) DEFAULT '' COMMENT '位置经纬度',
  `acheckin_picture` varchar(300) DEFAULT '' COMMENT '拍照的图片路径',
  `acheckin_state` int(2) DEFAULT '0',
  `acheckin_tempstorage` varchar(200) DEFAULT '',
  `acheckin_applyid` bigint(20) DEFAULT '0',
  PRIMARY KEY (`acheckin_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=22 ;

--
-- 转存表中的数据 `oa_attend_checkin`
--

INSERT INTO `oa_attend_checkin` (`acheckin_id`, `acheckin_code`, `acheckin_checkinway`, `acheckin_type`, `acheckin_timetype`, `acheckin_addtime`, `acheckin_checkintime`, `acheckin_location`, `acheckin_longlat`, `acheckin_picture`, `acheckin_state`, `acheckin_tempstorage`, `acheckin_applyid`) VALUES
(1, 1000000107, 1, 1, 1, NULL, '2017-08-07 09:30:23', '13422154', '12333', '1111', 0, '0.00', 0),
(2, 1000000107, 1, 1, 1, NULL, '2017-08-06 09:32:55', '13422154', '12333', '1111', 0, '0.00', 0),
(3, 1000000107, 1, 1, 1, NULL, '2017-08-05 09:36:55', '13422154', '12333', '1111', 0, '0.00', 0),
(4, 1000000107, 1, 1, 2, NULL, '2017-08-07 19:36:55', '13422154', '12333', '1111', 0, '0.00', 0),
(5, 1000000107, 1, 1, 1, NULL, '2017-08-08 09:36:55', '13422154', '12333', '1111', 0, '0.00', 0),
(6, 1000000107, 1, 2, 1, '2017-08-14 09:10:12', '2017-08-14 09:10:12', '123.12-141.2', 'abc', '', 1, '{"forenoon":{"type":2,"worktime":2},"afternoon":{"type":1,"worktime":5.17}}', 0),
(7, 1000000107, 1, 2, 2, '2017-08-14 11:10:12', '2017-08-14 11:10:12', '123.12-141.2', 'abc', '', 1, '{"forenoon":{"type":2,"worktime":2},"afternoon":{"type":1,"worktime":5.17}}', 0),
(8, 1000000107, 1, 1, 1, '2017-08-14 11:10:12', '2017-08-14 11:10:12', '123.12-141.2', 'abc', '', 1, '0.00', 0),
(9, 1000000107, 1, 1, 2, '2017-08-14 18:10:12', '2017-08-14 18:10:12', '123.12-141.2', 'abc', '', 1, '0.00', 0),
(10, 1000000107, 1, 3, 1, '2017-08-14 18:10:12', '2017-08-14 18:10:12', '123.12-141.2', 'abc', '', 1, '{"14":{"forenoon":{"type":2,"worktime":3},"afternoon":{"type":3,"worktime":9}}}', 6),
(11, 1000000107, 1, 3, 2, '2017-08-14 22:10:12', '2017-08-14 22:10:12', '123.12-141.2', 'abc', '', 1, '{"14":{"forenoon":{"type":2,"worktime":3},"afternoon":{"type":3,"worktime":9}}}', 6),
(12, 1000000107, 1, 3, 1, '2017-08-16 18:10:12', '2017-08-16 18:10:12', '123.12-141.2', 'abc', '', 0, '', 7),
(13, 1000000107, 1, 3, 2, '2017-08-18 03:10:12', '2017-08-18 03:10:12', '123.12-141.2', 'abc', '', 0, '', 7),
(14, 1000000107, 1, 3, 1, '2017-08-18 18:30:12', '2017-08-18 18:30:12', '08-18', 'abc', '', 0, '', 8),
(15, 1000000107, 1, 3, 2, '2017-08-20 18:30:12', '2017-08-20 18:30:12', '08-18', 'abc', '', 0, '', 8),
(16, 1000000107, 1, 1, 1, '2017-08-21 10:10:12', '2017-08-21 10:10:12', '123.12-141.2', 'abc', '', 1, '', 0),
(17, 1000000107, 1, 1, 2, '2017-08-21 18:20:12', '2017-08-21 18:20:12', '123.12-141.2', 'abc', '', 1, '', 0),
(18, 1000000107, 1, 2, 1, '2017-08-21 09:10:12', '2017-08-21 09:10:12', '123.12-141.2', 'abc', '', 1, '{"2017":{"08":{"21":{"forenoon":{"type":2,"worktime":1},"afternoon":{"type":2,"worktime":0}}}}}', 0),
(19, 1000000107, 1, 2, 2, '2017-08-21 10:10:12', '2017-08-21 10:10:12', '123.12-141.2', 'abc', '', 1, '{"2017":{"08":{"21":{"forenoon":{"type":2,"worktime":1},"afternoon":{"type":2,"worktime":0}}}}}', 0),
(20, 1000000107, 1, 3, 1, '2017-08-21 18:10:12', '2017-08-21 18:20:12', '123.12-141.2', 'abc', '', 0, '', 10),
(21, 1000000107, 1, 3, 2, '2017-08-21 23:10:12', '2017-08-21 23:10:12', '123.12-141.2', 'abc', '', 0, '', 10);

-- --------------------------------------------------------

--
-- 表的结构 `oa_attend_record`
--

CREATE TABLE IF NOT EXISTS `oa_attend_record` (
  `arecord_id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '打卡记录id',
  `arecord_code` bigint(10) NOT NULL DEFAULT '0' COMMENT '对应的用户code',
  `arecord_year` int(4) NOT NULL COMMENT '年',
  `arecord_month` int(2) NOT NULL COMMENT '月',
  `arecord_json` text COMMENT '数据json格式',
  `arecord_count` float(10,2) DEFAULT '0.00' COMMENT '本月统计',
  `arecord_remedy` int(2) DEFAULT '0' COMMENT '后补次数',
  PRIMARY KEY (`arecord_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=19 ;

--
-- 转存表中的数据 `oa_attend_record`
--

INSERT INTO `oa_attend_record` (`arecord_id`, `arecord_code`, `arecord_year`, `arecord_month`, `arecord_json`, `arecord_count`, `arecord_remedy`) VALUES
(1, 0, 2017, 8, '1, 2, 3, 4, 7, 8, 9, 10, 11, 14, 15, 16, 17, 18, 21, 22, 23, 24, 25, 28, 29, 30, 31', 0.00, 0),
(15, 1000000107, 2017, 8, '{"1":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"2":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"3":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"4":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"5":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"6":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"7":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"8":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"9":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"10":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"11":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"12":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"13":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"14":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"15":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"16":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"17":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"18":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"19":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"20":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"21":{"forenoon":{"type":2,"worktime":2.83},"afternoon":{"type":2,"worktime":4.84}},"22":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"23":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"24":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"25":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"26":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"27":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"28":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"29":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"30":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"31":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}}}', 5.67, 2),
(16, 1000000107, 2017, 9, '{"1":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"2":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"3":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"4":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"5":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"6":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"7":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"8":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"9":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"10":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"11":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"12":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"13":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"14":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"15":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"16":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"17":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"18":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"19":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"20":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"21":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"22":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"23":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"24":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"25":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"26":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"27":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"28":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"29":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"30":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}}}', 0.00, 3),
(17, 0, 2017, 9, '1, 4, 5, 6, 7, 8, 11, 12, 13, 14, 15, 18, 19, 20, 21, 22, 25, 26, 27, 28, 29, 30', 0.00, 0),
(18, 1000000107, 2017, 7, '{"1":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"2":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"3":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"4":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"5":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"6":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"7":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"8":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"9":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"10":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"11":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"12":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"13":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"14":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"15":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"16":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"17":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"18":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"19":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"20":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"21":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"22":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"23":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"24":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"25":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"26":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"27":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"28":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"29":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"30":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}},"31":{"forenoon":{"type":"","worktime":""},"afternoon":{"type":"","worktime":""}}}', 0.00, 3);

-- --------------------------------------------------------

--
-- 表的结构 `oa_attend_user`
--

CREATE TABLE IF NOT EXISTS `oa_attend_user` (
  `auser_id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '人员记录id',
  `auser_code` bigint(10) NOT NULL COMMENT '对应的用户code',
  `auser_eachday` int(2) DEFAULT '0' COMMENT '每天工时',
  `auser_worktime` float(10,2) DEFAULT NULL COMMENT '动作时长',
  `auser_annual` int(3) DEFAULT '0' COMMENT '年假',
  PRIMARY KEY (`auser_id`),
  UNIQUE KEY `auser_code` (`auser_code`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- 转存表中的数据 `oa_attend_user`
--

INSERT INTO `oa_attend_user` (`auser_id`, `auser_code`, `auser_eachday`, `auser_worktime`, `auser_annual`) VALUES
(1, 1000000107, 8, 0.00, 0);

-- --------------------------------------------------------

--
-- 表的结构 `oa_company`
--

CREATE TABLE IF NOT EXISTS `oa_company` (
  `company_id` int(9) NOT NULL AUTO_INCREMENT,
  `company_name` varchar(20) NOT NULL,
  PRIMARY KEY (`company_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- 转存表中的数据 `oa_company`
--

INSERT INTO `oa_company` (`company_id`, `company_name`) VALUES
(1, '睿基'),
(2, 'UPT'),
(3, '睿速'),
(4, 'Dpower');

-- --------------------------------------------------------

--
-- 表的结构 `oa_config`
--

CREATE TABLE IF NOT EXISTS `oa_config` (
  `config_id` int(10) NOT NULL AUTO_INCREMENT,
  `config_class` varchar(100) NOT NULL,
  `config_key` varchar(20) NOT NULL,
  `config_value` varchar(500) NOT NULL,
  `config_upper` int(10) NOT NULL,
  PRIMARY KEY (`config_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=14 ;

--
-- 转存表中的数据 `oa_config`
--

INSERT INTO `oa_config` (`config_id`, `config_class`, `config_key`, `config_value`, `config_upper`) VALUES
(1, 'company', '1', 'TWOWAY', 0),
(2, 'company', '2', 'UPT', 0),
(3, 'option', '1', 'TWOWAY', 0),
(4, 'company', '3', 'DPOWER', 0),
(5, 'company', '4', '睿色', 0),
(6, 'company', '5', '睿诚', 0),
(7, 'acheckin_checkinway', '1', '定位', 0),
(8, 'acheckin_checkinway', '2', '拍照', 0),
(9, 'acheckin_type', '1', '上下班', 0),
(10, 'acheckin_type', '2', '外勤', 0),
(11, 'acheckin_type', '3', '加班', 0),
(12, 'acheckin_timetype', '1', '开始', 0),
(13, 'acheckin_timetype', '2', '结束', 0);

-- --------------------------------------------------------

--
-- 表的结构 `oa_department`
--

CREATE TABLE IF NOT EXISTS `oa_department` (
  `department_id` int(9) NOT NULL AUTO_INCREMENT,
  `department_name` varchar(20) NOT NULL,
  `department_leader` int(1) DEFAULT '0',
  PRIMARY KEY (`department_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

--
-- 转存表中的数据 `oa_department`
--

INSERT INTO `oa_department` (`department_id`, `department_name`, `department_leader`) VALUES
(1, '行政部', 0),
(2, '财务部', 0),
(4, '项目部', 0),
(5, '总经办', 1),
(8, '高层管理', 1);

-- --------------------------------------------------------

--
-- 表的结构 `oa_group`
--

CREATE TABLE IF NOT EXISTS `oa_group` (
  `group_id` int(9) NOT NULL AUTO_INCREMENT,
  `group_name` varchar(20) NOT NULL,
  `group_department` int(9) NOT NULL DEFAULT '0',
  PRIMARY KEY (`group_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=20 ;

--
-- 转存表中的数据 `oa_group`
--

INSERT INTO `oa_group` (`group_id`, `group_name`, `group_department`) VALUES
(1, '行政部', 0),
(2, '财务部', 0),
(3, '设计部', 0),
(4, '策划部', 0),
(6, '项目部', 0),
(7, '营业部', 0),
(8, '演出组', 0),
(9, 'IT组', 1),
(10, '人事', 1),
(12, '', 0),
(13, '4', 0),
(14, 'TC组', 4),
(17, 'A组', 4),
(18, '财务小分组', 2),
(19, '财务二分组', 2);

-- --------------------------------------------------------

--
-- 表的结构 `oa_place`
--

CREATE TABLE IF NOT EXISTS `oa_place` (
  `place_id` int(9) NOT NULL AUTO_INCREMENT,
  `place_name` varchar(20) NOT NULL,
  `place_department` int(9) NOT NULL,
  `place_group` int(9) NOT NULL,
  `place_manager` int(2) DEFAULT '0',
  `place_extent` varchar(200) NOT NULL DEFAULT '',
  `place_role` int(9) NOT NULL DEFAULT '0',
  PRIMARY KEY (`place_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=28 ;

--
-- 转存表中的数据 `oa_place`
--

INSERT INTO `oa_place` (`place_id`, `place_name`, `place_department`, `place_group`, `place_manager`, `place_extent`, `place_role`) VALUES
(1, '文员', 1, 0, 0, '', 0),
(3, '程序员', 1, 1, 0, '', 0),
(4, '人事助理', 1, 3, 0, '', 0),
(5, '财务经理', 2, 0, 1, '', 0),
(6, '小组组长', 6, 4, 1, '', 0),
(7, '小组组长', 6, 2, 1, '', 0),
(9, '小小前天台', 9, 5, 0, '', 0),
(10, 'TW总经理', 8, 0, 1, '4,', 0),
(11, '前台', 1, 0, 0, '', 0),
(16, '程序员', 1, 9, 0, '', 33),
(18, '会计', 2, 0, 0, '', 0),
(19, '人事经理', 1, 10, 0, '', 0),
(20, '项目部经理', 4, 0, 1, '', 0),
(21, '小组组长', 4, 14, 0, '', 0),
(22, '行政总监', 1, 0, 1, '', 0),
(23, 'IT主管', 1, 9, 1, '', 1),
(24, 'UPT总经理', 5, 0, 1, '2,4,', 0),
(25, '副总经理', 5, 0, 0, '', 0),
(26, '出纳', 2, 19, 0, '', 0),
(27, '会计', 2, 19, 0, '', 0);

-- --------------------------------------------------------

--
-- 表的结构 `oa_rauth`
--

CREATE TABLE IF NOT EXISTS `oa_rauth` (
  `rauth_id` int(9) NOT NULL AUTO_INCREMENT,
  `rauth_role` int(9) NOT NULL,
  `rauth_auth` text,
  `rauth_table` text,
  PRIMARY KEY (`rauth_id`),
  UNIQUE KEY `rauth_role` (`rauth_role`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=24 ;

--
-- 转存表中的数据 `oa_rauth`
--

INSERT INTO `oa_rauth` (`rauth_id`, `rauth_role`, `rauth_auth`, `rauth_table`) VALUES
(1, 2, '{"Admin":{"Auth":{"name":"权限管理","menus":[{"name":"页面权限","menus":"authlist","icon":"fa fa-eye"},{"name":"数据表权限","menus":"authtable","icon":"fa fa-database"}],"icon":"glyphicon glyphicon-user"}},"Home":{"Attend":{"name":"考勤管理","menus":[{"name":"考勤配置","menus":"config","icon":"fa fa-gear"},{"name":"考勤申请","menus":"apply","icon":"fa fa-list-alt"},{"name":"申请管理","menus":"archives","icon":"fa fa-pencil-square"},{"name":"打卡","menus":"checkin","icon":"fa fa-square"}],"icon":"fa fa-calendar"},"Menu":{"name":"主菜单","menus":[{"name":"企业信息","menus":"company","icon":"fa fa-tachometer"},{"name":"个人信息","menus":"profile","icon":"fa fa-user"}],"icon":"fa fa-tachometer"},"User":{"name":"人事管理","menus":[{"name":"新建用户","menus":"create","icon":"glyphicon glyphicon-user"},{"name":"用户列表","menus":"userlist","icon":"fa fa-users"},{"name":"档案管理","menus":"archives","icon":"fa fa-file-archive-o"},{"name":"基础信息","menus":"ubase","icon":"glyphicon glyphicon-send"},{"name":"图表统计","menus":"charts","icon":"fa fa-bar-chart-o"}],"icon":"fa fa-users"}}}', '{"oa_company":["select","insert","update","delete"],"oa_department":["select","insert","update","delete"],"oa_group":["select","insert","update","delete"],"oa_place":["select","insert","update","delete"],"oa_rauth":["select","insert","update","delete"],"oa_role":["select","insert","update","delete"],"oa_user":["select","insert","update","delete"],"oa_archives":["select","insert","update","delete"],"oa_attend_apply":["select","insert","update","delete"],"oa_attend_checkin":["select","insert","update","delete"],"oa_attend_record":["select","insert","update","delete"],"oa_auser":["select","insert","update","delete"],"oa_config":["select","insert","update","delete"],"oa_attend_user":["select","insert","update","delete"]}'),
(10, 31, '{"Admin":{"Auth":{"name":"权限管理","menus":[],"icon":"glyphicon glyphicon-user"}},"Home":{"Menu":{"name":"主菜单","menus":[{"name":"企业信息","menus":"company","icon":"fa fa-tachometer"},{"name":"个人信息","menus":"profile","icon":"fa fa-user"}],"icon":"fa fa-tachometer"},"User":{"name":"用户功能","menus":[{"name":"用户列表","menus":"userlist","icon":"fa fa-users"}],"icon":"fa fa-users"}}}', '{"oa_archives":["select"],"oa_attend_apply":["select"],"oa_attend_checkin":["select"],"oa_attend_record":["select"],"oa_auser":["select"],"oa_company":["select"],"oa_config":["select"],"oa_department":["select"],"oa_group":["select"],"oa_place":["select"],"oa_rauth":["select"],"oa_role":["select"],"oa_user":["select"],"oa_attend_user":["select"]}'),
(11, 1, '{"Admin":{"Auth":{"name":"权限管理","menus":[{"name":"权限列表","menus":"authlist","icon":"fa fa-lock"}],"icon":"glyphicon glyphicon-user"}},"Home":{"Menu":{"name":"主菜单","menus":[{"name":"企业信息","menus":"company","icon":"fa fa-tachometer"},{"name":"个人信息","menus":"profile","icon":"fa fa-user"}],"icon":"fa fa-tachometer"},"User":{"name":"用户功能","menus":[{"name":"用户列表","menus":"userlist","icon":"fa fa-users"},{"name":"新建用户","menus":"create","icon":"glyphicon glyphicon-user"},{"name":"基础信息","menus":"ubase","icon":"glyphicon glyphicon-send"}],"icon":"fa fa-users"}}}', '{"oa_company":["select","insert","update","delete"],"oa_department":["select","insert","update","delete"],"oa_group":["select","insert","update","delete"],"oa_place":["select","insert","update","delete"],"oa_rauth":["select","insert","update","delete"],"oa_role":["select","insert","update","delete"],"oa_user":["select","insert","update","delete"],"oa_archives":["select"],"oa_attend_apply":["select"],"oa_attend_checkin":["select"],"oa_attend_record":["select"],"oa_auser":["select"],"oa_config":["select"],"oa_attend_user":["select"]}'),
(12, 4, '{"Admin":{"Auth":{"name":"权限管理","menus":[],"icon":"glyphicon glyphicon-user"}},"Home":{"Menu":{"name":"主菜单","menus":[{"name":"企业信息","menus":"company","icon":"fa fa-tachometer"},{"name":"个人信息","menus":"profile","icon":"fa fa-user"}],"icon":"fa fa-tachometer"},"User":{"name":"用户功能","menus":[{"name":"用户列表","menus":"userlist","icon":"fa fa-users"},{"name":"新建用户","menus":"create","icon":"glyphicon glyphicon-user"},{"name":"基础信息","menus":"ubase","icon":"glyphicon glyphicon-send"}],"icon":"fa fa-users"}}}', '{"oa_company":["select","insert"],"oa_department":["select","insert"],"oa_group":["select","insert"],"oa_place":["select","insert"],"oa_rauth":["select","insert"],"oa_role":["select","insert"],"oa_user":["select","insert"],"oa_archives":["select"],"oa_attend_apply":["select"],"oa_attend_checkin":["select"],"oa_attend_record":["select"],"oa_auser":["select"],"oa_config":["select"],"oa_attend_user":["select"]}'),
(13, 7, '{"Admin":{},"Home":{"Menu":{"name":"主菜单","menus":[{"name":"企业信息","menus":"company","icon":"fa fa-tachometer"},{"name":"个人信息","menus":"profile","icon":"fa fa-user"}],"icon":"fa fa-tachometer"}}}', '{"oa_company":["select"],"oa_department":["select"],"oa_group":["select"],"oa_place":["select"],"oa_rauth":["select"],"oa_role":["select"],"oa_user":["select"],"oa_archives":["select"],"oa_attend_apply":["select"],"oa_attend_checkin":["select"],"oa_attend_record":["select"],"oa_auser":["select"],"oa_config":["select"],"oa_attend_user":["select"]}'),
(14, 28, '{"Admin":{},"Home":{"Menu":{"name":"主菜单","menus":[{"name":"企业信息","menus":"company","icon":"fa fa-tachometer"}],"icon":"fa fa-tachometer"},},}', '{"oa_company":["select","insert"],"oa_department":["select","insert"],"oa_group":["select","insert"],"oa_place":["select","insert"],"oa_rauth":["select","insert"],"oa_role":["select","insert"],"oa_user":["select","insert"],"oa_archives":["select"],"oa_attend_apply":["select"],"oa_attend_checkin":["select"],"oa_attend_record":["select"],"oa_auser":["select"],"oa_config":["select"],"oa_attend_user":["select"]}'),
(15, 5, '{"Admin":{},"Home":{"Menu":{"name":"主菜单","menus":[{"name":"企业信息","menus":"company","icon":"fa fa-tachometer"},{"name":"个人信息","menus":"profile","icon":"fa fa-user"}],"icon":"fa fa-tachometer"},"User":{"name":"用户功能","menus":[{"name":"用户列表","menus":"userlist","icon":"fa fa-users"},{"name":"新建用户","menus":"create","icon":"glyphicon glyphicon-user"}],"icon":"fa fa-users"}}}', '{"oa_company":["select"],"oa_department":["select","insert"],"oa_group":["select","insert"],"oa_place":["select","insert"],"oa_rauth":["select","insert"],"oa_role":["select","insert"],"oa_user":["select","insert"],"oa_archives":["select"],"oa_attend_apply":["select"],"oa_attend_checkin":["select"],"oa_attend_record":["select"],"oa_auser":["select"],"oa_config":["select"],"oa_attend_user":["select"]}'),
(16, 29, '', '{"oa_company":["select"],"oa_department":["select"],"oa_group":["select"],"oa_place":["select"],"oa_rauth":["select"],"oa_role":["select"],"oa_user":["select"],"oa_archives":["select"],"oa_attend_apply":["select"],"oa_attend_checkin":["select"],"oa_attend_record":["select"],"oa_auser":["select"],"oa_config":["select"],"oa_attend_user":["select"]}'),
(17, 6, '{"Admin":{},"Home":{"Menu":{"name":"主菜单","menus":[{"name":"企业信息","menus":"company","icon":"fa fa-tachometer"},{"name":"个人信息","menus":"profile","icon":"fa fa-user"}],"icon":"fa fa-tachometer"}}}', '{"oa_company":["select"],"oa_department":["select"],"oa_group":["select"],"oa_place":["select"],"oa_rauth":["select"],"oa_role":["select"],"oa_user":["select"],"oa_archives":["select"],"oa_attend_apply":["select"],"oa_attend_checkin":["select"],"oa_attend_record":["select"],"oa_auser":["select"],"oa_config":["select"],"oa_attend_user":["select"]}'),
(18, 33, '{"Admin":{"Auth":{"name":"权限管理","menus":[{"name":"页面权限","menus":"authlist","icon":"fa fa-eye"}],"icon":"glyphicon glyphicon-user"}},"Home":{"Menu":{"name":"主菜单","menus":[{"name":"企业信息","menus":"company","icon":"fa fa-tachometer"},{"name":"个人信息","menus":"profile","icon":"fa fa-user"}],"icon":"fa fa-tachometer"},"User":{"name":"用户功能","menus":[{"name":"新建用户","menus":"create","icon":"glyphicon glyphicon-user"},{"name":"用户列表","menus":"userlist","icon":"fa fa-users"},{"name":"基础信息","menus":"ubase","icon":"glyphicon glyphicon-send"}],"icon":"fa fa-users"}}}', '{"oa_company":["select","insert","update","delete"],"oa_department":["select","insert","update","delete"],"oa_group":["select","insert","update","delete"],"oa_place":["select","insert","update","delete"],"oa_rauth":["select","insert","update","delete"],"oa_role":["select","insert","update","delete"],"oa_user":["select","insert","update","delete"],"oa_archives":["select"],"oa_attend_apply":["select"],"oa_attend_checkin":["select"],"oa_attend_record":["select"],"oa_auser":["select"],"oa_config":["select"],"oa_attend_user":["select"]}'),
(19, 8, '{"Admin":{"Auth":{"name":"权限管理","menus":[],"icon":"glyphicon glyphicon-user"}},"Home":{"Menu":{"name":"主菜单","menus":[{"name":"企业信息","menus":"company","icon":"fa fa-tachometer"},{"name":"个人信息","menus":"profile","icon":"fa fa-user"}],"icon":"fa fa-tachometer"},"User":{"name":"用户功能","menus":[],"icon":"fa fa-users"}}}', '{"oa_archives":["select"],"oa_attend_apply":["select"],"oa_attend_checkin":["select"],"oa_attend_record":["select"],"oa_auser":["select"],"oa_company":["select"],"oa_config":["select"],"oa_department":["select"],"oa_group":["select"],"oa_place":["select"],"oa_rauth":["select"],"oa_role":["select"],"oa_user":["select"],"oa_attend_user":["select"]}'),
(20, 9, '{"Admin":{"Auth":{"name":"权限管理","menus":[],"icon":"glyphicon glyphicon-user"}},"Home":{"Menu":{"name":"主菜单","menus":[{"name":"企业信息","menus":"company","icon":"fa fa-tachometer"},{"name":"个人信息","menus":"profile","icon":"fa fa-user"}],"icon":"fa fa-tachometer"},"User":{"name":"用户功能","menus":[],"icon":"fa fa-users"}}}', '{"oa_archives":["select"],"oa_attend_apply":["select"],"oa_attend_checkin":["select"],"oa_attend_record":["select"],"oa_auser":["select"],"oa_company":["select"],"oa_config":["select"],"oa_department":["select"],"oa_group":["select"],"oa_place":["select"],"oa_rauth":["select"],"oa_role":["select"],"oa_user":["select"],"oa_attend_user":["select"]}'),
(21, 10, '{"Admin":{"Auth":{"name":"权限管理","menus":[],"icon":"glyphicon glyphicon-user"}},"Home":{"Menu":{"name":"主菜单","menus":[{"name":"企业信息","menus":"company","icon":"fa fa-tachometer"},{"name":"个人信息","menus":"profile","icon":"fa fa-user"}],"icon":"fa fa-tachometer"},"User":{"name":"用户功能","menus":[],"icon":"fa fa-users"}}}', '{"oa_archives":["select"],"oa_attend_apply":["select"],"oa_attend_checkin":["select"],"oa_attend_record":["select"],"oa_auser":["select"],"oa_company":["select"],"oa_config":["select"],"oa_department":["select"],"oa_group":["select"],"oa_place":["select"],"oa_rauth":["select"],"oa_role":["select"],"oa_user":["select"],"oa_attend_user":["select"]}'),
(22, 30, '{"Admin":{"Auth":{"name":"权限管理","menus":[],"icon":"glyphicon glyphicon-user"}},"Home":{"Menu":{"name":"主菜单","menus":[{"name":"企业信息","menus":"company","icon":"fa fa-tachometer"},{"name":"个人信息","menus":"profile","icon":"fa fa-user"}],"icon":"fa fa-tachometer"},"User":{"name":"用户功能","menus":[{"name":"用户列表","menus":"userlist","icon":"fa fa-users"},{"name":"基础信息","menus":"ubase","icon":"glyphicon glyphicon-send"}],"icon":"fa fa-users"}}}', '{"oa_archives":["select"],"oa_attend_apply":["select"],"oa_attend_checkin":["select"],"oa_attend_record":["select"],"oa_auser":["select"],"oa_company":["select"],"oa_config":["select"],"oa_department":["select"],"oa_group":["select"],"oa_place":["select"],"oa_rauth":["select"],"oa_role":["select"],"oa_user":["select"],"oa_attend_user":["select"]}'),
(23, 35, '', '{"oa_company":["select"],"oa_department":["select"],"oa_group":["select"],"oa_place":["select"],"oa_rauth":["select"],"oa_role":["select"],"oa_user":["select","update"],"oa_archives":["select"],"oa_attend_apply":["select"],"oa_attend_checkin":["select"],"oa_attend_record":["select"],"oa_auser":["select"],"oa_config":["select"],"oa_attend_user":["select"]}');

-- --------------------------------------------------------

--
-- 表的结构 `oa_role`
--

CREATE TABLE IF NOT EXISTS `oa_role` (
  `role_id` int(9) NOT NULL AUTO_INCREMENT,
  `role_name` varchar(50) NOT NULL,
  `role_upper` int(9) NOT NULL,
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=36 ;

--
-- 转存表中的数据 `oa_role`
--

INSERT INTO `oa_role` (`role_id`, `role_name`, `role_upper`) VALUES
(1, '管理员', 0),
(2, '超级管理员', 1),
(4, '行政人员', 0),
(5, '文员', 4),
(6, '项目人员', 0),
(7, '营业人员', 0),
(8, '策划人员', 0),
(9, '设计人员', 0),
(10, '财务人员', 0),
(28, '前台', 4),
(29, '后勤', 4),
(30, '出纳', 10),
(31, '会计', 10),
(33, '普通管理员', 1),
(34, '待激活组', 0),
(35, '未激活用户', 34);

-- --------------------------------------------------------

--
-- 表的结构 `oa_user`
--

CREATE TABLE IF NOT EXISTS `oa_user` (
  `user_id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '?û?id',
  `user_username` varchar(50) NOT NULL,
  `user_name` varchar(50) NOT NULL COMMENT '?û?????',
  `user_code` bigint(10) NOT NULL DEFAULT '0',
  `user_passwd` varchar(40) DEFAULT NULL,
  `user_company` int(9) NOT NULL DEFAULT '0',
  `user_department` int(9) NOT NULL DEFAULT '0',
  `user_group` int(9) NOT NULL DEFAULT '0',
  `user_place` int(9) NOT NULL DEFAULT '0',
  `user_roles` int(5) NOT NULL DEFAULT '0',
  `user_role` int(5) NOT NULL DEFAULT '0',
  `user_director` bigint(10) NOT NULL DEFAULT '0',
  `user_phone` bigint(11) DEFAULT NULL,
  `user_avatar` varchar(200) DEFAULT NULL,
  `user_sex` char(2) DEFAULT NULL,
  `user_born` date NOT NULL,
  `user_lastlogin` datetime NOT NULL,
  `user_entry` date NOT NULL COMMENT '??ְʱ??',
  `user_quit` date DEFAULT NULL COMMENT '??ְʱ??',
  `user_login` int(10) NOT NULL,
  `user_state` int(1) NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_code` (`user_code`),
  UNIQUE KEY `user_code_2` (`user_code`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=20 ;

--
-- 转存表中的数据 `oa_user`
--

INSERT INTO `oa_user` (`user_id`, `user_username`, `user_name`, `user_code`, `user_passwd`, `user_company`, `user_department`, `user_group`, `user_place`, `user_roles`, `user_role`, `user_director`, `user_phone`, `user_avatar`, `user_sex`, `user_born`, `user_lastlogin`, `user_entry`, `user_quit`, `user_login`, `user_state`) VALUES
(1, 'vition', '郭伟升', 1000000107, 'd13149de00848eb013cad318d27829db64b965d7', 2, 1, 9, 23, 1, 2, 1000000116, 13430310719, '/assets/avatars/man.png', '男', '1988-03-23', '2017-05-16 13:00:00', '2015-01-19', '0000-00-00', 1, 1),
(2, 'test', 'test1', 1000000110, '32e5cca1b3f7f914d34390baa02bade311858484', 2, 2, 19, 27, 10, 31, 1000000115, 3333333, '/assets/avatars/man.png', '男', '2017-05-16', '0000-00-00 00:00:00', '2017-07-19', '0000-00-00', 0, 1),
(3, '', '罗贯中', 1000000111, '32e5cca1b3f7f914d34390baa02bade311858484', 3, 4, 0, 20, 6, 0, 0, 0, '/assets/avatars/man.png', '男', '2017-07-18', '0000-00-00 00:00:00', '2017-07-26', '0000-00-00', 0, 1),
(4, '', '施耐庵', 1000000112, '32e5cca1b3f7f914d34390baa02bade311858484', 4, 1, 9, 16, 1, 5, 0, 0, '/assets/avatars/man.png', '男', '2017-07-18', '0000-00-00 00:00:00', '2017-07-19', '0000-00-00', 0, 1),
(5, '', '曹雪芹', 1000000113, '32e5cca1b3f7f914d34390baa02bade311858484', 3, 1, 9, 0, 1, 5, 0, 0, '/assets/avatars/man.png', '男', '2017-07-19', '0000-00-00 00:00:00', '2017-07-19', '0000-00-00', 0, 1),
(6, '', '贾宝玉', 1000000114, '32e5cca1b3f7f914d34390baa02bade311858484', 1, 1, 9, 16, 4, 29, 1000000111, 0, '/assets/avatars/man.png', '男', '2017-07-18', '0000-00-00 00:00:00', '2017-07-19', '0000-00-00', 0, 1),
(7, '', '许冠杰', 1000000115, '32e5cca1b3f7f914d34390baa02bade311858484', 1, 0, 0, 0, 0, 0, 0, 0, '/assets/avatars/man.png', '男', '2017-07-27', '0000-00-00 00:00:00', '2017-07-19', '0000-00-00', 0, 1),
(8, '', '谭咏麟', 1000000116, '32e5cca1b3f7f914d34390baa02bade311858484', 2, 0, 0, 0, 0, 0, 0, 0, '/assets/avatars/man.png', '男', '2017-07-27', '0000-00-00 00:00:00', '2017-07-19', '0000-00-00', 0, 1),
(9, '', '袁崇焕', 1000000117, '32e5cca1b3f7f914d34390baa02bade311858484', 1, 1, 9, 0, 1, 0, 1000000107, 0, '/assets/avatars/man.png', '男', '1988-01-05', '0000-00-00 00:00:00', '2017-07-19', '0000-00-00', 0, 1),
(12, 'daiyu', '林黛玉', 1000000118, '32e5cca1b3f7f914d34390baa02bade311858484', 1, 2, 0, 5, 10, 31, 0, 0, '/assets/avatars/lady.png', '女', '2003-02-20', '0000-00-00 00:00:00', '2017-07-19', '0000-00-00', 0, 1),
(13, '', '林黛玉', 1000000119, '32e5cca1b3f7f914d34390baa02bade311858484', 1, 1, 0, 0, 0, 0, 0, 0, '/assets/avatars/lady.png', '女', '2003-02-20', '0000-00-00 00:00:00', '2017-08-03', '0000-00-00', 0, 1),
(14, 'ming', '钟冠民', 1000000120, '32e5cca1b3f7f914d34390baa02bade311858484', 1, 1, 9, 16, 4, 29, 0, 0, '/assets/avatars/man.png', '男', '2017-07-18', '0000-00-00 00:00:00', '2017-07-18', '0000-00-00', 0, 1),
(15, '', '小明同学', 1000000121, '32e5cca1b3f7f914d34390baa02bade311858484', 1, 1, 9, 3, 1, 1, 1000000107, 9223372036854775807, '/assets/avatars/man.png', '男', '1990-07-12', '0000-00-00 00:00:00', '2017-07-19', '0000-00-00', 0, 1),
(16, '', '吴奇隆', 1000000122, '32e5cca1b3f7f914d34390baa02bade311858484', 4, 0, 0, 0, 0, 0, 0, 0, '/assets/avatars/man.png', '男', '2017-07-27', '0000-00-00 00:00:00', '2017-07-27', NULL, 0, 0),
(17, '', '1', 1000000123, '32e5cca1b3f7f914d34390baa02bade311858484', 2, 0, 0, 0, 0, 0, 0, 0, '/assets/avatars/man.png', '男', '2017-07-27', '0000-00-00 00:00:00', '2017-07-27', '2017-07-26', 0, 2),
(18, 'zhang', '朱元璋', 1000000124, '32e5cca1b3f7f914d34390baa02bade311858484', 2, 4, 14, 21, 6, 0, 1000000115, 13455555555, '/assets/avatars/man.png', '男', '1975-07-23', '0000-00-00 00:00:00', '2017-07-24', '0000-00-00', 0, 1),
(19, 'xuefeng', '廖雪峰', 1000000125, '7c4a8d09ca3762af61e59520943dc26494f8941b', 1, 1, 9, 16, 1, 33, 1000000111, 13441144111, '/assets/avatars/man.png', '男', '1984-06-05', '0000-00-00 00:00:00', '2017-07-26', '0000-00-00', 0, 1);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
