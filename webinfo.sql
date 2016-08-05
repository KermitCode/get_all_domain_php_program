/*
Navicat MySQL Data Transfer

Source Server         : 本地数据库127.0.0.1
Source Server Version : 50626
Source Host           : localhost:3306
Source Database       : webdata

Target Server Type    : MYSQL
Target Server Version : 50626
File Encoding         : 65001

Date: 2016-08-05 14:03:43
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for webinfo
-- ----------------------------
DROP TABLE IF EXISTS `webinfo`;
CREATE TABLE `webinfo` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `url` varchar(255) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `keywords` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `status` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `url` (`url`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of webinfo
-- ----------------------------
INSERT INTO `webinfo` VALUES ('1', 'https://www.hao123.com', null, null, null, '0');
