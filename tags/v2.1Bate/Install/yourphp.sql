-- Yourphp SQL Backup
-- Time:2011-08-05 14:57:32
-- http://www.yourphp.cn 

--
-- 表的结构 `yourphp_access`
-- 

CREATE TABLE `yourphp_access` (
  `role_id` smallint(6) unsigned NOT NULL DEFAULT '0',
  `node_id` smallint(6) unsigned NOT NULL DEFAULT '0',
  `level` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `pid` smallint(6) unsigned NOT NULL DEFAULT '0',
  `model` varchar(50) NOT NULL DEFAULT '',
  KEY `groupId` (`role_id`),
  KEY `nodeId` (`node_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
--
-- 表的结构 `yourphp_article`
-- 

CREATE TABLE `yourphp_article` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `catid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `userid` int(11) unsigned NOT NULL DEFAULT '0',
  `username` varchar(40) NOT NULL DEFAULT '',
  `title` varchar(120) NOT NULL DEFAULT '',
  `title_style` varchar(40) NOT NULL DEFAULT '',
  `keywords` varchar(120) NOT NULL DEFAULT '',
  `copyfrom` varchar(255) NOT NULL DEFAULT '',
  `fromlink` varchar(80) NOT NULL DEFAULT '0',
  `description` mediumtext NOT NULL,
  `content` text NOT NULL,
  `template` varchar(30) NOT NULL DEFAULT '',
  `thumb` varchar(100) NOT NULL DEFAULT '',
  `posid` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `recommend` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `readgroup` varchar(255) NOT NULL DEFAULT '',
  `readpoint` int(10) unsigned NOT NULL DEFAULT '0',
  `listorder` int(10) unsigned NOT NULL DEFAULT '0',
  `url` varchar(50) NOT NULL DEFAULT '',
  `hits` int(11) unsigned NOT NULL DEFAULT '0',
  `createtime` int(11) unsigned NOT NULL DEFAULT '0',
  `updatetime` int(11) unsigned NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `lang` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `status` (`id`,`status`,`listorder`),
  KEY `catid` (`id`,`catid`,`status`),
  KEY `lang` (`id`,`lang`,`status`),
  KEY `listorder` (`id`,`catid`,`status`,`listorder`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
--
-- 表的结构 `yourphp_attachment`
-- 

CREATE TABLE `yourphp_attachment` (
  `aid` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `moduleid` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `catid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `id` int(8) unsigned NOT NULL DEFAULT '0',
  `filename` varchar(50) NOT NULL DEFAULT '',
  `filepath` varchar(80) NOT NULL DEFAULT '',
  `filesize` int(10) unsigned NOT NULL DEFAULT '0',
  `fileext` char(10) NOT NULL DEFAULT '',
  `isimage` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `isthumb` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `userid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `createtime` int(10) unsigned NOT NULL DEFAULT '0',
  `uploadip` char(15) NOT NULL DEFAULT '',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`aid`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
--
-- 表的结构 `yourphp_category`
-- 

CREATE TABLE `yourphp_category` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `catname` varchar(30) NOT NULL DEFAULT '',
  `catdir` varchar(30) NOT NULL DEFAULT '',
  `parentdir` varchar(50) NOT NULL DEFAULT '',
  `parentid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `moduleid` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `module` char(24) NOT NULL DEFAULT '',
  `arrparentid` varchar(255) NOT NULL DEFAULT '',
  `arrchildid` varchar(255) NOT NULL DEFAULT '',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `title` varchar(150) NOT NULL DEFAULT '',
  `keywords` varchar(100) NOT NULL DEFAULT '',
  `description` varchar(255) NOT NULL DEFAULT '',
  `listorder` smallint(5) unsigned NOT NULL DEFAULT '0',
  `ishtml` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `ismenu` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `hits` int(10) unsigned NOT NULL DEFAULT '0',
  `image` varchar(100) NOT NULL DEFAULT '',
  `child` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `url` varchar(100) NOT NULL DEFAULT '',
  `template_list` varchar(20) NOT NULL DEFAULT '',
  `template_show` varchar(20) NOT NULL DEFAULT '',
  `pagesize` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `readgroup` varchar(100) NOT NULL DEFAULT '',
  `listtype` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `lang` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `urlruleid` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `presentpoint` tinyint(3) NOT NULL DEFAULT '0',
  `chargepoint` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `paytype` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `repeatchargedays` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `postgroup` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `parentid` (`parentid`),
  KEY `listorder` (`listorder`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;

--
-- 表的结构 `yourphp_config`
-- 

CREATE TABLE `yourphp_config` (
  `id` smallint(8) unsigned NOT NULL AUTO_INCREMENT,
  `varname` varchar(20) NOT NULL DEFAULT '',
  `info` varchar(100) NOT NULL DEFAULT '',
  `groupid` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `value` text NOT NULL,
  `lang` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `varname` (`varname`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
--
-- 表的结构 `yourphp_dbsource`
-- 

CREATE TABLE `yourphp_dbsource` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `host` varchar(20) NOT NULL DEFAULT '',
  `port` int(5) NOT NULL DEFAULT '3306',
  `username` varchar(50) NOT NULL DEFAULT '',
  `password` varchar(50) NOT NULL  DEFAULT '',
  `dbname` varchar(50) NOT NULL  DEFAULT '',
  `dbtablepre` varchar(30) NOT NULL  DEFAULT '',
  PRIMARY KEY (`id`)
) TYPE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
--
-- 表的结构 `yourphp_download`
-- 

CREATE TABLE `yourphp_download` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `catid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `userid` int(8) unsigned NOT NULL DEFAULT '0',
  `username` varchar(40) NOT NULL DEFAULT '',
  `title` varchar(120) NOT NULL DEFAULT '',
  `title_style` varchar(40) NOT NULL DEFAULT '',
  `thumb` varchar(100) NOT NULL DEFAULT '',
  `keywords` varchar(120) NOT NULL DEFAULT '',
  `description` mediumtext NOT NULL,
  `content` mediumtext NOT NULL,
  `template` varchar(40) NOT NULL DEFAULT '',
  `posid` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `recommend` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `readgroup` varchar(100) NOT NULL DEFAULT '',
  `readpoint` smallint(5) unsigned NOT NULL,
  `listorder` int(10) unsigned NOT NULL DEFAULT '0',
  `hits` int(11) unsigned NOT NULL DEFAULT '0',
  `createtime` int(11) unsigned NOT NULL DEFAULT '0',
  `updatetime` int(11) unsigned NOT NULL DEFAULT '0',
  `url` varchar(60) NOT NULL DEFAULT '',
  `file` varchar(80) NOT NULL DEFAULT '',
  `ext` varchar(10) NOT NULL DEFAULT '',
  `size` varchar(10) NOT NULL DEFAULT '',
  `downs` int(10) unsigned NOT NULL DEFAULT '0',
  `lang` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `status` (`id`,`status`,`listorder`),
  KEY `catid` (`id`,`catid`,`status`),
  KEY `lang` (`id`,`status`,`lang`),
  KEY `listorder` (`id`,`catid`,`status`,`listorder`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
--
-- 表的结构 `yourphp_feedback`
-- 

CREATE TABLE `yourphp_feedback` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `listorder` int(10) unsigned NOT NULL DEFAULT '0',
  `username` varchar(20) NOT NULL DEFAULT '',
  `telephone` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(50) NOT NULL DEFAULT '',
  `content` mediumtext NOT NULL,
  `ip` varchar(255) NOT NULL DEFAULT '',
  `title` varchar(50) NOT NULL DEFAULT '',
  `createtime` int(11) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `typeid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `userid` int(11) unsigned NOT NULL DEFAULT '0',
  `lang` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
--
-- 表的结构 `yourphp_field`
-- 

CREATE TABLE `yourphp_field` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `moduleid` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `field` varchar(20) NOT NULL DEFAULT '',
  `name` varchar(30) NOT NULL DEFAULT '',
  `tips` varchar(150) NOT NULL DEFAULT '',
  `required` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `minlength` int(10) unsigned NOT NULL DEFAULT '0',
  `maxlength` int(10) unsigned NOT NULL DEFAULT '0',
  `pattern` varchar(255) NOT NULL DEFAULT '',
  `errormsg` varchar(255) NOT NULL DEFAULT '',
  `class` varchar(20) NOT NULL DEFAULT '',
  `type` varchar(20) NOT NULL DEFAULT '',
  `setup` mediumtext NOT NULL,
  `ispost` tinyint(1) NOT NULL DEFAULT '0',
  `unpostgroup` varchar(60) NOT NULL DEFAULT '',
  `listorder` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `issystem` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=103 DEFAULT CHARSET=utf8;
--
-- 表的结构 `yourphp_guestbook`
-- 

CREATE TABLE `yourphp_guestbook` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL DEFAULT '',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `listorder` int(10) unsigned NOT NULL DEFAULT '0',
  `createtime` int(11) unsigned NOT NULL DEFAULT '0',
  `username` varchar(20) NOT NULL DEFAULT '',
  `telephone` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(40) NOT NULL DEFAULT '',
  `content` mediumtext NOT NULL,
  `reply_content` mediumtext NOT NULL,
  `ip` varchar(15) NOT NULL DEFAULT '',
  `userid` int(11) unsigned NOT NULL DEFAULT '0',
  `lang` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
--
-- 表的结构 `yourphp_kefu`
-- 

CREATE TABLE `yourphp_kefu` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `listorder` int(10) unsigned NOT NULL DEFAULT '0',
  `createtime` int(11) unsigned NOT NULL DEFAULT '0',
  `name` varchar(20) NOT NULL DEFAULT '',
  `type` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `code` mediumtext NOT NULL,
  `skin` varchar(3) NOT NULL DEFAULT '0',
  `lang` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

--
-- 表的结构 `yourphp_link`
-- 

CREATE TABLE `yourphp_link` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `listorder` int(10) unsigned NOT NULL DEFAULT '0',
  `createtime` int(11) unsigned NOT NULL DEFAULT '0',
  `name` varchar(50) NOT NULL DEFAULT '',
  `logo` varchar(80) NOT NULL DEFAULT '',
  `siteurl` varchar(150) NOT NULL DEFAULT '',
  `typeid` smallint(5) unsigned NOT NULL,
  `linktype` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `siteinfo` mediumtext NOT NULL,
  `lang` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY  `lang`  (`lang`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

--
-- 表的结构 `yourphp_menu`
-- 

CREATE TABLE `yourphp_menu` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `parentid` smallint(6) unsigned NOT NULL DEFAULT '0',
  `model` char(20) NOT NULL DEFAULT '',
  `action` char(20) NOT NULL DEFAULT '',
  `data` char(50) NOT NULL DEFAULT '',
  `type` tinyint(1) NOT NULL DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `group_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `name` varchar(50) NOT NULL DEFAULT '',
  `remark` varchar(255) NOT NULL DEFAULT '',
  `listorder` smallint(6) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `parentid` (`parentid`),
  KEY `model` (`model`)
) ENGINE=MyISAM AUTO_INCREMENT=71 DEFAULT CHARSET=utf8;
--
-- 表的结构 `yourphp_module`
-- 

CREATE TABLE `yourphp_module` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '',
  `name` varchar(50) NOT NULL DEFAULT '',
  `description` varchar(200) NOT NULL DEFAULT '',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `issystem` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `issearch` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `listfields` varchar(255) NOT NULL DEFAULT '',
  `setup` mediumtext NOT NULL,
  `listorder` smallint(3) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `postgroup` varchar(100) NOT NULL DEFAULT '',
  `ispost` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

--
-- 表的结构 `yourphp_node`
-- 

CREATE TABLE `yourphp_node` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL DEFAULT '',
  `title` varchar(50) NOT NULL DEFAULT '',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `remark` varchar(255) NOT NULL DEFAULT '',
  `listorder` smallint(5) unsigned NOT NULL DEFAULT '0',
  `pid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `level` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `name` (`name`,`status`,`pid`)
) ENGINE=MyISAM AUTO_INCREMENT=30 DEFAULT CHARSET=utf8;


--
-- 表的结构 `yourphp_order`
-- 

CREATE TABLE `yourphp_order` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `sn` char(22) NOT NULL DEFAULT '',
  `password` varchar(40) NOT NULL DEFAULT '',
  `module` varchar(20) NOT NULL DEFAULT '',
  `userid` int(8) unsigned NOT NULL DEFAULT '0',
  `username` varchar(40) NOT NULL DEFAULT '',
  `price` decimal(10,0) unsigned NOT NULL DEFAULT '0',
  `productlist` mediumtext NOT NULL,
  `note` mediumtext NOT NULL,
  `realname` varchar(40) NOT NULL DEFAULT '',
  `email` varchar(50) NOT NULL DEFAULT '',
  `tel` varchar(50) NOT NULL DEFAULT '',
  `mobile` varchar(18) NOT NULL DEFAULT '',
  `fax` varchar(50) NOT NULL DEFAULT '',
  `address` varchar(80) NOT NULL DEFAULT '',
  `zipcode` varchar(10) NOT NULL DEFAULT '',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `ip` char(15) NOT NULL DEFAULT '',
  `createtime` int(11) unsigned NOT NULL DEFAULT '0',
  `updatetime` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `sn` (`sn`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
--
-- 表的结构 `yourphp_page`
-- 

CREATE TABLE `yourphp_page` (
  `id` smallint(5) NOT NULL,
  `title` varchar(100) NOT NULL DEFAULT '',
  `thumb` varchar(100) NOT NULL DEFAULT '',
  `keywords` varchar(250) NOT NULL DEFAULT '',
  `description` mediumtext NOT NULL,
  `content` mediumtext NOT NULL,
  `template` varchar(30) NOT NULL DEFAULT '',
  `listorder` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 表的结构 `yourphp_picture`
-- 

CREATE TABLE `yourphp_picture` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `catid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `userid` int(11) unsigned NOT NULL DEFAULT '0',
  `username` varchar(40) NOT NULL DEFAULT '',
  `title` varchar(120) NOT NULL DEFAULT '',
  `title_style` varchar(40) NOT NULL DEFAULT '',
  `thumb` varchar(100) NOT NULL DEFAULT '',
  `keywords` varchar(120) NOT NULL DEFAULT '',
  `description` mediumtext NOT NULL,
  `content` mediumtext NOT NULL,
  `url` varchar(60) NOT NULL DEFAULT '',
  `template` varchar(40) NOT NULL DEFAULT '',
  `posid` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `recommend` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `readgroup` varchar(100) NOT NULL DEFAULT '',
  `readpoint` smallint(5) NOT NULL DEFAULT '0',
  `listorder` int(10) unsigned NOT NULL DEFAULT '0',
  `hits` int(11) unsigned NOT NULL DEFAULT '0',
  `createtime` int(11) unsigned NOT NULL DEFAULT '0',
  `updatetime` int(11) unsigned NOT NULL DEFAULT '0',
  `pics` mediumtext NOT NULL,
  `lang` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `status` (`id`,`status`,`listorder`),
  KEY `catid` (`id`,`catid`,`status`),
  KEY `lang` (`id`,`status`,`lang`),
  KEY `listorder` (`id`,`catid`,`status`,`listorder`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
--
-- 表的结构 `yourphp_posid`
-- 

CREATE TABLE `yourphp_posid` (
  `id` tinyint(2) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(40) NOT NULL DEFAULT '',
  `listorder` tinyint(2) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;


--
-- 表的结构 `yourphp_product`
-- 

CREATE TABLE `yourphp_product` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `catid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `userid` int(11) unsigned NOT NULL DEFAULT '0',
  `username` varchar(40) NOT NULL DEFAULT '',
  `title` varchar(120) NOT NULL DEFAULT '',
  `title_style` varchar(40) NOT NULL DEFAULT '',
  `keywords` varchar(80) NOT NULL DEFAULT '',
  `description` mediumtext NOT NULL,
  `content` mediumtext NOT NULL,
  `template` varchar(40) NOT NULL DEFAULT '',
  `thumb` varchar(100) NOT NULL DEFAULT '',
  `posid` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `recommend` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `readgroup` varchar(100) NOT NULL DEFAULT '',
  `readpoint` smallint(5) NOT NULL DEFAULT '0',
  `listorder` int(10) unsigned NOT NULL DEFAULT '0',
  `hits` int(11) unsigned NOT NULL DEFAULT '0',
  `createtime` int(11) unsigned NOT NULL DEFAULT '0',
  `updatetime` int(11) unsigned NOT NULL DEFAULT '0',
  `price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `url` varchar(60) NOT NULL DEFAULT '',
  `xinghao` varchar(30) NOT NULL DEFAULT '',
  `pics` mediumtext NOT NULL,
  `lang` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `status` (`id`,`status`,`listorder`),
  KEY `catid` (`id`,`catid`,`status`),
  KEY `lang` (`id`,`status`,`lang`),
  KEY `listorder` (`id`,`catid`,`status`,`listorder`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
--
-- 表的结构 `yourphp_role`
-- 

CREATE TABLE `yourphp_role` (
  `id` smallint(6) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL DEFAULT '',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `remark` varchar(255) NOT NULL DEFAULT '',
  `pid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `listorder` smallint(6) unsigned NOT NULL DEFAULT '0',
  `allowpost` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `allowpostverify` tinyint(1) unsigned NOT NULL,
  `allowsearch` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `allowupgrade` tinyint(1) unsigned NOT NULL DEFAULT '1',
  `allowsendmessage` tinyint(1) unsigned NOT NULL,
  `allowattachment` tinyint(1) NOT NULL,
  `maxpostnum` smallint(5) unsigned NOT NULL DEFAULT '0',
  `maxmessagenum` smallint(5) unsigned NOT NULL DEFAULT '0',
  `price_y` decimal(8,2) unsigned NOT NULL DEFAULT '0.00',
  `price_m` decimal(8,2) unsigned NOT NULL DEFAULT '0.00',
  `price_d` decimal(8,2) unsigned NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id`),
  KEY `status` (`status`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
--
-- 表的结构 `yourphp_role_user`
-- 

CREATE TABLE `yourphp_role_user` (
  `role_id` mediumint(9) unsigned DEFAULT '0',
  `user_id` int(11) unsigned NOT NULL DEFAULT '0',
  KEY `group_id` (`role_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
--
-- 表的结构 `yourphp_type`
-- 

CREATE TABLE `yourphp_type` (
  `typeid` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL DEFAULT '',
  `parentid` smallint(5) unsigned NOT NULL DEFAULT '0',
  `description` varchar(200) NOT NULL DEFAULT '',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `listorder` smallint(5) unsigned NOT NULL DEFAULT '0',
  `keyid` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`typeid`),
  KEY `parentid` (`parentid`,`listorder`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

--
-- 表的结构 `yourphp_urlrule`
-- 

CREATE TABLE `yourphp_urlrule` (
  `urlruleid` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `ishtml` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `showurlrule` varchar(255) NOT NULL DEFAULT '',
  `showexample` varchar(255) NOT NULL DEFAULT '',
  `listurlrule` varchar(255) NOT NULL DEFAULT '',
  `listexample` varchar(255) NOT NULL DEFAULT '',
  `listorder` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`urlruleid`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

--
-- 表的结构 `yourphp_user`
-- 

CREATE TABLE `yourphp_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `groupid` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `username` varchar(50) NOT NULL DEFAULT '',
  `password` varchar(50) NOT NULL DEFAULT '',
  `email` varchar(50) NOT NULL DEFAULT '',
  `realname` varchar(50) NOT NULL DEFAULT '',
  `question` varchar(50) NOT NULL DEFAULT '',
  `answer` varchar(50) NOT NULL DEFAULT '',
  `sex` tinyint(1) NOT NULL DEFAULT '0',
  `tel` varchar(50) NOT NULL DEFAULT '',
  `mobile` varchar(50) NOT NULL DEFAULT '',
  `fax` varchar(50) NOT NULL DEFAULT '',
  `web_url` varchar(100) NOT NULL DEFAULT '',
  `address` varchar(100) NOT NULL DEFAULT '',
  `login_count` int(11) unsigned NOT NULL DEFAULT '0',
  `createtime` int(11) unsigned NOT NULL DEFAULT '0',
  `updatetime` int(11) unsigned NOT NULL DEFAULT '0',
  `last_logintime` int(11) unsigned NOT NULL DEFAULT '0',
  `reg_ip` char(15) NOT NULL DEFAULT '',
  `last_ip` char(15) NOT NULL DEFAULT '',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `amount` decimal(8,2) unsigned NOT NULL DEFAULT '0.00',
  `point` smallint(5) unsigned NOT NULL DEFAULT '0',
  `avatar` varchar(120) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


CREATE TABLE `yourphp_lang` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL DEFAULT '',
  `mark` varchar(30) NOT NULL DEFAULT '',
  `flag` varchar(100) NOT NULL DEFAULT '',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `path` varchar(30) NOT NULL DEFAULT '',
  `domain` varchar(30) NOT NULL DEFAULT '',
  `listorder` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

CREATE TABLE `yourphp_block` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `pos` char(30)  NOT NULL DEFAULT '',
  `name` varchar(30)  NOT NULL  DEFAULT '',
  `lang` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `content` text,
  PRIMARY KEY (`id`),
  KEY `pos` (`pos`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `yourphp_slide` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL  DEFAULT '',
  `flashfile` varchar(150) NOT NULL  DEFAULT '',
  `xmlfile` varchar(150) NOT NULL  DEFAULT '',
  `tpl` varchar(30) NOT NULL  DEFAULT '',
  `width` smallint(5) unsigned NOT NULL  DEFAULT '0',
  `height` smallint(5) unsigned NOT NULL DEFAULT '0',
  `num` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) NOT NULL  DEFAULT  '0',
  `lang` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `yourphp_slide_data` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `fid` tinyint(3) unsigned DEFAULT '0',
  `title` varchar(30) NOT NULL   DEFAULT '',
  `small` varchar(150) NOT NULL   DEFAULT '',
  `pic` varchar(150)  NOT NULL   DEFAULT '',
  `link` varchar(150) NOT NULL   DEFAULT '',
  `description` varchar(255) NOT NULL   DEFAULT '',
  `data` text,
  `listorder` smallint(5) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `lang` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fid` (`fid`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `yourphp_tags` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `slug` varchar(100) NOT NULL DEFAULT '',
  `moduleid` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `module` varchar(30) NOT NULL   DEFAULT '',
  `num` smallint(5) unsigned NOT NULL DEFAULT '0',
  `lang` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`,`moduleid`),
  KEY `slug` (`slug`),
  KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


CREATE TABLE `yourphp_tags_data` (
  `id`  int(10) unsigned NOT NULL DEFAULT '0',
  `tagid`  int(10) unsigned NOT NULL DEFAULT '0',  
  `listorder` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`,`tagid`),
  KEY `tagid` (`tagid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;


CREATE TABLE `yourphp_online` (
  `sessionid` char(32) NOT NULL,
  `userid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `username` varchar(50) NOT NULL DEFAULT '',
  `ip` char(15) NOT NULL,
  `lastvisit` int(11) unsigned NOT NULL DEFAULT '0',
  `groupid` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`sessionid`),
  KEY `lastvisit` (`lastvisit`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8;


CREATE TABLE `yourphp_payment` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `pay_code` varchar(20) NOT NULL DEFAULT '',
  `pay_name` varchar(120) NOT NULL DEFAULT '',
  `pay_fee` varchar(10) NOT NULL DEFAULT '0',
  `pay_fee_type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `pay_desc` text NOT NULL, 
  `pay_config` text NOT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_cod` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `is_online` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `author` varchar(100) NOT NULL,
  `version` varchar(20) NOT NULL,
  `listorder` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `pay_code` (`pay_code`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `yourphp_cart` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `userid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `sessionid` char(32)  NOT NULL DEFAULT '',
  `moduleid` smallint(3) unsigned NOT NULL DEFAULT '0',
  `product_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `product_thumb` varchar(120) NOT NULL DEFAULT '',
  `product_name` varchar(120) NOT NULL DEFAULT '',
  `product_url` varchar(120) NOT NULL DEFAULT '',
  `product_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `price` decimal(10,2) unsigned  NOT NULL DEFAULT '0.00',
  `number` smallint(5) unsigned NOT NULL DEFAULT '0',
  `attr` text NOT NULL,  
  `goods_attr_id` varchar(255) NOT NULL DEFAULT '',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `isgift` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `sessionid` (`sessionid`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


CREATE TABLE `yourphp_order` (
  `id` int(11)  unsigned   NOT NULL auto_increment,
  `sn` bigint(19)  unsigned NOT NULL DEFAULT '0',
  `userid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `pay_status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `shipping_status` tinyint(1)  unsigned NOT NULL DEFAULT '0',

  `consignee` varchar(60) NOT NULL DEFAULT '',
  `country` smallint(5) unsigned NOT NULL DEFAULT '0',
  `province` smallint(5) unsigned NOT NULL DEFAULT '0',
  `city` smallint(5) unsigned NOT NULL DEFAULT '0',
  `area` smallint(5) unsigned NOT NULL DEFAULT '0',
  `address` varchar(255) NOT NULL DEFAULT '',
  `zipcode` varchar(60) NOT NULL DEFAULT '',
  `tel` varchar(60) NOT NULL DEFAULT '',
  `mobile` varchar(60) NOT NULL DEFAULT '',
  `email` varchar(60) NOT NULL DEFAULT '',

  `type` tinyint(1) unsigned NOT NULL default '0',

  `shipping_id` tinyint(3) unsigned  NOT NULL DEFAULT '0',
  `shipping_name` varchar(120) NOT NULL DEFAULT '',
  `shipping_sn` varchar(100)  NOT NULL DEFAULT '',
  `pay_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `pay_name` varchar(120) NOT NULL DEFAULT '',
  `pay_code` varchar(120) NOT NULL DEFAULT '',

  `amount` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `pay_fee` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',   
  `shipping_fee` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',  
  `insure_fee` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',  
  `invoice_fee` decimal(10,2) unsigned NOT NULL default '0.00',  
  `order_amount` decimal(10,2)unsigned  NOT NULL DEFAULT '0.00',
  `discount` decimal(10,2)  NOT NULL default '0.00',

  `add_time` int(10) unsigned NOT NULL DEFAULT '0',
  `pay_time` int(10) unsigned NOT NULL DEFAULT '0',
  `shipping_time` int(10) unsigned NOT NULL DEFAULT '0',
  `accept_time` int(10) unsigned NOT NULL DEFAULT '0',  
  `confirm_time` int(10) unsigned NOT NULL DEFAULT '0',

  `point` int(5) unsigned NOT NULL default '0', 

  `invoice` tinyint(1) unsigned NOT NULL default '0' ,
  `invoice_title` varchar(100)  NOT NULL DEFAULT '',
  
  `postmessage` varchar(255)  NOT NULL DEFAULT '',
  `note` text NOT NULL,

  PRIMARY KEY  (`id`),
  UNIQUE KEY `sn` (`sn`),
  KEY `userid` (`userid`),
  KEY `status` (`status`),
  KEY `shipping_status` (`shipping_status`),
  KEY `pay_status` (`pay_status`),
  KEY `shipping_id` (`shipping_id`),
  KEY `pay_id` (`pay_id`)
) ENGINE=MyISAM  AUTO_INCREMENT=1  DEFAULT CHARSET=utf8;


CREATE TABLE `yourphp_order_data` (
  `id` int(11) unsigned   NOT NULL auto_increment,
  `userid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `order_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `moduleid` smallint(3) unsigned NOT NULL DEFAULT '0',
  `product_id` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `product_thumb` varchar(120) NOT NULL DEFAULT '',
  `product_name` varchar(120) NOT NULL DEFAULT '',
  `product_url` varchar(120) NOT NULL DEFAULT '',
  `product_price` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
  `price` decimal(10,2)  unsigned NOT NULL DEFAULT '0.00',
  `number` smallint(5) unsigned NOT NULL DEFAULT '0',
  `attr` text NOT NULL,  
  `goods_attr_id` varchar(255) NOT NULL DEFAULT '',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `isgift` smallint(5) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `userid` (`userid`),
  KEY `product_id` (`product_id`)
) ENGINE=MyISAM  AUTO_INCREMENT=1  DEFAULT CHARSET=utf8;

CREATE TABLE `yourphp_area` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `parentid` int(10)  unsigned NOT NULL DEFAULT '0',
  `name` varchar(50)  NOT NULL DEFAULT '',
  `listorder` mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  AUTO_INCREMENT=1  DEFAULT CHARSET=utf8;


CREATE TABLE `yourphp_user_address` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `userid` mediumint(8) unsigned NOT NULL DEFAULT '0',
  `consignee` varchar(60) NOT NULL DEFAULT '',
  `email` varchar(60) NOT NULL DEFAULT '',
  `country` smallint(5) unsigned NOT NULL DEFAULT '0',
  `province` smallint(5) unsigned NOT NULL DEFAULT '0',
  `city` smallint(5) unsigned  NOT NULL DEFAULT '0',
  `area` smallint(5) unsigned  NOT NULL DEFAULT '0',
  `address` varchar(120) NOT NULL DEFAULT '',
  `zipcode` varchar(60) NOT NULL DEFAULT '',
  `tel` varchar(60) NOT NULL DEFAULT '',
  `mobile` varchar(60) NOT NULL DEFAULT '',
  `isdefault` tinyint(1) unsigned  NOT NULL default '0',
  PRIMARY KEY (`id`),
  KEY `userid` (`userid`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


CREATE TABLE `yourphp_shipping` (
  `id` int(11)  unsigned  NOT NULL auto_increment,
  `name` varchar(50) NOT NULL DEFAULT '',
  `description` varchar(50) NOT NULL DEFAULT '',
  `type` tinyint(1)  unsigned  NOT NULL default '0',
  `first_weight` int(11)  unsigned  NOT NULL default '0',
  `second_weight` int(11)  unsigned  NOT NULL default '0',
  `first_price` float(10,2)  unsigned  NOT NULL default '0.00' ,
  `second_price` float(10,2)  unsigned  NOT NULL default '0.00' ,
  `status` tinyint(1)  unsigned NOT NULL default '0',
  `listorder` mediumint(8) unsigned NOT NULL default '99',
  `is_insure` tinyint(1)  unsigned NOT NULL default '0',
  `insure_fee` int(11) unsigned NOT NULL default '0',
  `insure_low_price` float(10,2) unsigned NOT NULL default '0.00',
  `price_type` tinyint(1)  unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

		