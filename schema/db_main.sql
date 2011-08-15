DROP TABLE IF EXISTS `Users`;

CREATE TABLE `Users` (
  `username` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `deleted` int(10) unsigned NOT NULL,
  `created` int(10) unsigned NOT NULL,
  `password` char(64) DEFAULT NULL,
  `conf_code` char(24) DEFAULT NULL,
  `confirmed` int(10) unsigned NOT NULL,
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `cluster_id` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `by_email` (`email`,`deleted`),
  UNIQUE KEY `by_username` (`username`,`deleted`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `FlickrUsers`;

CREATE TABLE `FlickrUsers` (
  `user_id` int(11) unsigned NOT NULL,
  `nsid` varchar(20) NOT NULL,
  `auth_token` char(34) NOT NULL,
  `oauth_token` char(34) NOT NULL,
  `oauth_secret` char(16) NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `by_nsid` (`nsid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `InviteCodes`;

CREATE TABLE `InviteCodes` (
  `code` char(12) CHARACTER SET latin1 NOT NULL,
  `email` varchar(255) CHARACTER SET latin1 NOT NULL,
  `created` int(10) unsigned NOT NULL,
  `redeemed` int(10) unsigned NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  `sent` int(10) unsigned NOT NULL,
  `id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `by_email` (`email`),
  KEY `by_code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `SubscriptionUrls`;

CREATE TABLE `SubscriptionUrls` (
  `id` int(10) unsigned NOT NULL,
  `url` varchar(255) NOT NULL,
  `args` text,
  UNIQUE KEY `by_url` (`url`),
  KEY `by_id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
