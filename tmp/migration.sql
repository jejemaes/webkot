--
-- SYSTEM
--

-- User
RENAME TABLE user TO res_user;
ALTER TABLE  `res_user` CHANGE  `username` `login` VARCHAR( 32 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ;
ALTER TABLE res_user DROP COLUMN viewdet;
ALTER TABLE res_user DROP COLUMN isadmin;
ALTER TABLE res_user DROP COLUMN iswebkot;

--
-- MODULE BLOG
--
ALTER TABLE  `blog_post` DROP  `auteur` ;
ALTER TABLE  `blog_post` CHANGE  `userid`  `user_id` INT( 11 ) UNSIGNED NOT NULL ;
ALTER TABLE  `blog_post` CHANGE  `timestamp`  `date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ;
ALTER TABLE  `blog_post` ADD  `published` ENUM(  '0',  '1' ) NOT NULL DEFAULT  '0' AFTER  `date` ;

ALTER TABLE  `blog_comment` CHANGE  `postid`  `post_id` INT( 11 ) NOT NULL ;
ALTER TABLE  `blog_comment` CHANGE  `userid`  `user_id` INT( 11 ) UNSIGNED NOT NULL ;
ALTER TABLE  `blog_comment` CHANGE  `timestamp`  `create_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ;

CREATE TABLE IF NOT EXISTS `blog_tag` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS `blog_tag_rel` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `blog_post_id` int(10) NOT NULL,
  `blog_tag_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_log_tag_rel` (`blog_tag_id`),
  KEY `fk_log_tag_rel_post` (`blog_post_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

ALTER TABLE `blog_tag_rel`
  ADD CONSTRAINT `fk_log_tag_rel_post` FOREIGN KEY (`blog_post_id`) REFERENCES `blog_post` (`id`),
  ADD CONSTRAINT `fk_log_tag_rel_tag` FOREIGN KEY (`blog_tag_id`) REFERENCES `blog_tag` (`id`);


--
-- IR TABLES
--
CREATE TABLE IF NOT EXISTS `ir_config_parameter` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `value` text NOT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(64) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


CREATE TABLE IF NOT EXISTS `ir_view` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) DEFAULT NULL,
  `type` varchar(128) CHARACTER SET utf8 NOT NULL,
  `model` varchar(32) DEFAULT NULL,
  `arch` text NOT NULL,
  `sequence` int(11) NOT NULL DEFAULT '10',
  `active` enum('0','1') NOT NULL DEFAULT '1',
  `inherit_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;




--
-- ADMIN
--
CREATE TABLE IF NOT EXISTS `admin_menu` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) NOT NULL,
  `url` varchar(128),
  `sequence` int(11) NOT NULL DEFAULT '10',
  `icon` varchar(64) DEFAULT 'fa fa-cogs',
  `parent_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
