DROP TABLE IF EXISTS `Subscriptions`;

CREATE TABLE `Subscriptions` (
  `topic_id` tinyint(3) unsigned NOT NULL,
  `secret_url` char(64) CHARACTER SET latin1 NOT NULL,
  `created` int(10) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `verify_token` char(32) CHARACTER SET latin1 NOT NULL,
  `last_update` int(10) unsigned NOT NULL,
  `verified` int(10) unsigned NOT NULL,
  `id` int(11) unsigned NOT NULL,
  `deleted` int(10) unsigned NOT NULL,
  `last_request` int(10) unsigned NOT NULL,
  `last_request_photo_count` tinyint(3) unsigned NOT NULL,
  `last_update_photo_count` tinyint(3) unsigned NOT NULL,
  `url_id` int(10) unsigned NOT NULL,
  UNIQUE KEY `by_subid` (`id`),
  UNIQUE KEY `by_topic` (`user_id`,`topic_id`),
  KEY `recent_activity` (`last_request`,`last_update`),
  KEY `by_user` (`user_id`,`topic_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
