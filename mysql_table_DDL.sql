CREATE TABLE `entry` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `hash` varchar(10) CHARACTER SET latin1 DEFAULT NULL COMMENT 'null because we need to insert to the the auto_increment value, then generate a hash based on that.',
  `editor_hash` varchar(64) CHARACTER SET latin1 DEFAULT NULL,
  `text_content` text NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `date_edited` timestamp NULL DEFAULT NULL,
  `title` varchar(64) CHARACTER SET latin1 DEFAULT NULL,
  `description` varchar(256) CHARACTER SET latin1 DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `hash_index` (`hash`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;