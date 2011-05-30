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
  UNIQUE KEY `by_user` (`user_id`,`topic_id`),
  UNIQUE KEY `by_subid` (`id`),
  KEY `by_url` (`secret_url`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
