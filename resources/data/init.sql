DROP TABLE IF EXISTS `user_event`;

CREATE TABLE `user_event` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `group` varchar(45) NOT NULL COMMENT '分类组',
  `name` varchar(45) NOT NULL COMMENT '事件名称',
  `event` varchar(45) NOT NULL COMMENT '事件名称',
  `description` varchar(1024) DEFAULT NULL COMMENT '描述',
  `sort` int(11) DEFAULT '0',
  `public` enum('yes','no') DEFAULT 'yes' COMMENT '是否公开，不公开只能自己可见',
  `need_record` enum('yes','no') DEFAULT 'yes' COMMENT '是否需要将该事件记录到 user_event_log表中',
  `template` varchar(1024) DEFAULT NULL,
  `status` enum('enable','disable') DEFAULT 'enable' COMMENT '状态',
  PRIMARY KEY (`id`),
  UNIQUE KEY `event_UNIQUE` (`event`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8 COMMENT='用户事件列表';

/*Data for the table `user_event` */

insert  into `user_event`(`id`,`group`,`name`,`event`,`description`,`sort`,`public`,`need_record`,`template`,`status`) values (1,'question','提问','event_question_create',NULL,1,'no','yes',NULL,'enable'),(2,'question','删除问题','event_question_delete',NULL,2,'no','yes',NULL,'enable'),(3,'question','投票问题','event_question_vote',NULL,3,'yes','yes','%s问题','enable'),(5,'question','收藏问题','event_question_favorite',NULL,5,'yes','yes',NULL,'enable'),(6,'question','取消收藏问题','event_question_unfavorite',NULL,6,'no','no',NULL,'enable'),(7,'question','关注问题','event_question_follow',NULL,7,'yes','yes',NULL,'enable'),(8,'question','问题公共编辑','event_question_common_edit',NULL,8,'no','yes',NULL,'enable'),(9,'question','取消关注问题','event_question_unfollow',NULL,9,'no','yes',NULL,'enable'),(10,'answer','回答','event_answer_create',NULL,50,'no','yes',NULL,'enable'),(11,'answer','删除回答','event_answer_delete',NULL,51,'no','yes',NULL,'enable'),(12,'answer','投票回答','event_answer_vote',NULL,52,'yes','yes','%s回答','enable'),(14,'answer','回答公共编辑','event_answer_common_edit',NULL,54,'no','yes',NULL,'enable'),(15,'answer_comment','添加评论','event_answer_comment_create',NULL,100,'no','yes',NULL,'enable'),(16,'answer_comment','删除评论','event_answer_comment_delete',NULL,101,'no','no',NULL,'enable'),(17,'tag','关注标签','event_tag_follow',NULL,150,'no','yes',NULL,'enable'),(18,'tag','取消关注标签','event_tag_unfollow',NULL,151,'no','yes',NULL,'enable'),(19,'tag','标签公共编辑','event_tag_comment_edit',NULL,152,'no','yes',NULL,'enable'),(20,'user','关注用户','event_user_follow',NULL,200,'no','yes',NULL,'enable'),(21,'user','取消关注用户','evetn_user_unfollow',NULL,201,'no','yes',NULL,'enable'),(23,'answer_comment','投票评论','event_answer_comment_vote',NULL,102,'no','no','%s评论','enable'),(25,'answer_comment','取消评论投票','event_answer_comment_cancel_vote',NULL,103,'no','no',NULL,'enable');

/*Table structure for table `user_event_log` */

DROP TABLE IF EXISTS `user_event_log`;

CREATE TABLE `user_event_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_event_id` int(11) unsigned NOT NULL,
  `associate_type` varchar(45) NOT NULL COMMENT '类型:question,answer,answer_comment,article',
  `associate_id` int(11) unsigned NOT NULL COMMENT '关联的对象ID',
  `associate_data` varchar(1024) DEFAULT NULL COMMENT '关联数据',
  `created_at` int(11) unsigned DEFAULT NULL COMMENT '创建时间',
  `created_by` int(11) unsigned DEFAULT NULL COMMENT '创建用户',
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`created_by`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8 COMMENT='用户动态';

/*Data for the table `user_event_log` */

insert  into `user_event_log`(`id`,`user_event_id`,`associate_type`,`associate_id`,`associate_data`,`created_at`,`created_by`) values (1,15,'answer_comment',130,'{\"question_id\":\"15\",\"answer_id\":\"32\"}',1452963771,1),(2,1,'question',16,'[]',1452963844,1),(3,10,'answer',33,'{\"question_id\":\"16\"}',1452963861,1),(4,3,'question',15,'{\"template\":[\"反对\"]}',1452996653,1),(5,12,'answer',32,'{\"question_id\":\"15\",\"template\":[\"推荐\"]}',1452965474,1),(6,1,'question',17,'[]',1452965736,1),(7,3,'question',16,'{\"template\":[\"推荐\"]}',1452965886,1),(8,12,'answer',1,'{\"question_id\":\"1\",\"template\":[\"反对\"]}',1452965842,1),(9,15,'answer_comment',131,'{\"question_id\":\"15\",\"answer_id\":\"32\"}',1453011745,1),(10,15,'answer_comment',132,'{\"question_id\":\"15\",\"answer_id\":\"32\"}',1453011825,1),(11,15,'answer_comment',133,'{\"question_id\":\"15\",\"answer_id\":\"32\"}',1453012080,1),(12,1,'question',18,'[]',1453185737,174),(13,10,'answer',34,'{\"question_id\":\"18\"}',1453205921,174),(14,15,'answer_comment',34,'{\"question_id\":\"18\",\"answer_id\":\"34\"}',1453260385,485),(15,10,'answer',35,'{\"question_id\":\"18\"}',1453260374,275),(16,15,'answer_comment',35,'{\"question_id\":\"18\",\"answer_id\":\"35\"}',1453260525,275),(17,15,'answer_comment',16,'{\"question_id\":\"13\",\"answer_id\":\"16\"}',1453284605,1);

/*Table structure for table `user_grade_rule` */

DROP TABLE IF EXISTS `user_grade_rule`;

CREATE TABLE `user_grade_rule` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL COMMENT '等级名称',
  `score` int(11) DEFAULT '0' COMMENT '分数（信用与货币）',
  `status` enum('enable','disable') NOT NULL DEFAULT 'enable' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COMMENT='用户等级规则';

/*Data for the table `user_grade_rule` */

insert  into `user_grade_rule`(`id`,`name`,`score`,`status`) values (1,'平民',0,'enable'),(2,'初涉江湖',50,'enable'),(3,'初露锋芒',100,'enable'),(4,'崭露头角',500,'enable'),(5,'声名鹊起',1000,'enable'),(6,'扬名立万',5000,'enable'),(7,'功成名就',10000,'enable'),(8,'誉满江湖',50000,'enable'),(9,'震烁古今',500000,'enable');

DROP TABLE IF EXISTS `user_score_rule`;

CREATE TABLE `user_score_rule` (
  `user_event_id` int(11) NOT NULL COMMENT '用户事件ID',
  `type` enum('currency','credit') NOT NULL COMMENT '变动类型',
  `score` int(11) NOT NULL DEFAULT '0' COMMENT '变动值',
  `limit_interval` enum('limitless','year','season','month','week','day','hour','minute','second') NOT NULL DEFAULT 'limitless' COMMENT '间隔时间',
  `limit_times` int(11) NOT NULL DEFAULT '1' COMMENT '限制次数',
  `status` enum('enable','disable') NOT NULL DEFAULT 'enable' COMMENT '状态',
  PRIMARY KEY (`user_event_id`,`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户得分规则';

/*Data for the table `user_score_rule` */

insert  into `user_score_rule`(`user_event_id`,`type`,`score`,`limit_interval`,`limit_times`,`status`) values (15,'currency',1,'day',10,'enable');