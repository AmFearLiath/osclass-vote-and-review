CREATE TABLE IF NOT EXISTS `/*TABLE_PREFIX*/t_votes_reviews_user` (
  `pk_i_id` int(10) NOT NULL AUTO_INCREMENT,
  `i_user_voted` int(10) unsigned NOT NULL,
  `i_user_voter` int(10) unsigned NOT NULL,
  `i_vote` int(10) unsigned NOT NULL,
  `dt_review` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `s_review` text NOT NULL,
  PRIMARY KEY (`pk_i_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `/*TABLE_PREFIX*/t_votes_reviews_items` (
  `pk_i_id` int(10) NOT NULL AUTO_INCREMENT,
  `fk_i_item_id` int(10) unsigned NOT NULL,
  `fk_i_user_id` int(10) unsigned DEFAULT NULL,
  `i_vote` int(10) unsigned NOT NULL,
  `s_hash` varchar(255) DEFAULT NULL,
  `dt_review` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `s_review` text NOT NULL,
  PRIMARY KEY (`pk_i_id`),
  KEY `fk_i_item_id` (`fk_i_item_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

ALTER TABLE `/*TABLE_PREFIX*/t_votes_reviews_items`
  ADD CONSTRAINT `/*TABLE_PREFIX*/t_votes_reviews_items_ibfk_1` FOREIGN KEY (`fk_i_item_id`) REFERENCES `/*TABLE_PREFIX*/t_item` (`pk_i_id`);