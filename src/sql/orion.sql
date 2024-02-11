DROP TABLE IF EXISTS `orion_event`;
CREATE TABLE `orion_event` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `unique_id` varchar(24) NOT NULL,
  `key_name` varchar(150) NOT NULL,
  `data_value` blob NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `orion_event_key_name` (`key_name`),
  KEY `orion_event_created` (`created`),
  KEY `orion_event_unique_id` (`unique_id`)
);


DROP TABLE IF EXISTS `orion_historical`;
CREATE TABLE `orion_historical` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `unique_id` varchar(24) NOT NULL,
  `key_name` varchar(150) NOT NULL,
  `data_value` varchar(255) NOT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `orion_historical_key_name` (`key_name`),
  KEY `orion_historical_created` (`created`),
  KEY `orion_historical_unique_id` (`unique_id`)
);


DROP TABLE IF EXISTS `orion_series`;
CREATE TABLE `orion_series` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `unique_id` varchar(24) NOT NULL,
  `series_id` varchar(24) NOT NULL,
  `key_name` varchar(150) NOT NULL,
  `data_value` longblob NOT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `orion_series_key_name` (`key_name`),
  KEY `orion_series_created` (`created`),
  KEY `orion_series_unique_id` (`unique_id`),
  KEY `orion_series_series_id` (`series_id`)
);