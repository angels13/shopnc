INSERT INTO `shopnc_setting` (`name`, `value`) VALUES ('mobile_isuse', '1');

DROP TABLE IF EXISTS `shopnc_mb_ad`;
CREATE TABLE `shopnc_mb_ad` (
  `link_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '索引id',
  `link_title` varchar(100) DEFAULT NULL COMMENT '标题',
  `link_pic` varchar(150) DEFAULT NULL COMMENT '图片',
  `link_sort` tinyint(3) unsigned NOT NULL DEFAULT '255' COMMENT '排序',
  `link_keyword` varchar(10) DEFAULT NULL COMMENT '关键字',
  PRIMARY KEY (`link_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='手机端广告';

INSERT INTO `shopnc_mb_ad` VALUES (15,'户外旅行','a93071f217d821d262a6ad081d383838.jpg',255,NULL),(16,'电器专场','8b89103d674133e00aadb2a0aa350e48.jpg',111,NULL),(13,'日用百货','adbbb2e550645d098d3e4ab92226de2a.jpg',128,'百货'),(14,'服饰专场','bd4fba92244e62aea5629aeeab2d9d95.jpg',255,NULL);

DROP TABLE IF EXISTS `shopnc_mb_feedback`;
CREATE TABLE `shopnc_mb_feedback` (
  `id` mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
  `content` text,
  `type` enum('1','2') DEFAULT '2' COMMENT '1来自手机端2来自PC端',
  `ftime` int(10) unsigned DEFAULT '0' COMMENT '反馈时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='意见反馈';

DROP TABLE IF EXISTS `shopnc_mb_home`;
CREATE TABLE `shopnc_mb_home` (
  `h_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '索引',
  `h_title` varchar(6) NOT NULL COMMENT '标题',
  `h_desc` varchar(10) NOT NULL COMMENT '描述',
  `h_img` varchar(100) NOT NULL COMMENT '图片',
  `h_keyword` varchar(6) NOT NULL COMMENT '关键字',
  `h_sort` tinyint(3) unsigned NOT NULL DEFAULT '255' COMMENT '排序',
  `h_type` varchar(10) NOT NULL COMMENT '类型 (type1 type2)',
  `h_multi_keyword` varchar(50) DEFAULT NULL COMMENT '多关键字',
  PRIMARY KEY (`h_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='首页设置';

INSERT INTO `shopnc_mb_home` VALUES (1,'我就是女王','复古金属铆钉','d4b5e0cde3e4457de657c3513feed273.jpg','正品',255,'type1','男装,女装'),(2,'精选大牌','夏季流行靓装','6a3addc96ec4c36ecad610476438b580.jpg','夏',255,'type1',NULL),(3,'碧玺戒指','象征权力富贵','2f2fd447ac16d6157ff4d747f4995de4.jpg','饰品',255,'type1',NULL),(4,'美眉衣橱','最有爱单品','4f5c910133eb21c411241a850a918107.jpg','夏装',255,'type2',NULL),(5,'达人力荐','最热收藏清淡','8d5b45cd33d84c60b8d2d47ecfcb6e61.jpg','正版',255,'type2',NULL),(6,'居家生活','周周精彩','5006e172d8081f6bf6ce98ed526c5b98.jpg','盘',255,'type2',NULL),(7,'包包新品季','优惠享不停','b3d707bd4d564034475ebba209a7646f.jpg','包',255,'type2',NULL);

DROP TABLE IF EXISTS `shopnc_mb_category`;
CREATE TABLE `shopnc_mb_category` (
  `gc_id` smallint(5) unsigned DEFAULT NULL COMMENT '商城系统的分类ID',
  `gc_thumb` varchar(150) DEFAULT NULL COMMENT '缩略图'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='一级分类缩略图[手机端]';

INSERT INTO `shopnc_mb_category` VALUES (1,'b3270daacaca2c74dbfe1b7fdcefcd8d.png'),(2,'7ac89f535680e83b16a68e5b463706b0.png'),(3,'f64e7393c3f15bf23c9e28f65361a5d8.png'),(8,'6a96d167a9fb65d0b5290958d9176b2a.png'),(4,'4c08033155e5e6a3d611cf6d5a16795b.png'),(5,'55dac983f83d749ea9bfa49417ea7859.png'),(7,'83e0bd1790eb7a2484b58eebcfa813a0.png'),(6,'7f5e6dfbca829d704986c48f01516dcd.png');

DROP TABLE IF EXISTS `shopnc_mb_user_token`;
CREATE TABLE `shopnc_mb_user_token` (
  `token_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '令牌编号',
  `member_id` int(10) unsigned NOT NULL COMMENT '用户编号',
  `member_name` varchar(50) NOT NULL COMMENT '用户名',
  `token` varchar(50) NOT NULL COMMENT '登录令牌',
  `login_time` int(10) unsigned NOT NULL COMMENT '登录时间',
  `client_type` varchar(10) NOT NULL COMMENT '客户端类型 android wap',
  PRIMARY KEY (`token_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='移动端登陆令牌表';

