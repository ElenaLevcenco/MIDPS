DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `token` varchar(50) CHARACTER SET ascii DEFAULT NULL,
  `extern_token` varchar(320) CHARACTER SET ascii DEFAULT NULL,
  `last_used` datetime NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `extern_token` (`extern_token`) USING BTREE,
  KEY `user_token` (`user_id`,`token`) USING BTREE,
  KEY `user_id` (`user_id`) USING BTREE,
  CONSTRAINT `sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4294 DEFAULT CHARSET=utf8;