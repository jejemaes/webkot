
-- BLOG
ALTER TABLE `blog_post` CHANGE `timestamp` `date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE `blog_post` ADD `published` ENUM('0','1') NOT NULL DEFAULT '0' AFTER `content`;


ALTER TABLE `blog_comment` CHANGE `timestamp` `create_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;
UPDATE `blog_comment` SET `id` = '1' WHERE `blog_comment`.`id` = 0;
ALTER TABLE `blog_comment` CHANGE `id` `id` INT(11) NOT NULL AUTO_INCREMENT;


CREATE TABLE `blog_tag` (
  `id` int(11) unsigned NOT NULL,
  `name` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



CREATE TABLE `blog_tag_rel` (
  `id` int(10) unsigned NOT NULL,
  `blog_post_id` int(11) NOT NULL,
  `blog_tag_id` int(11) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

ALTER TABLE `blog_tag_rel`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `blog_tag_rel`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;

ALTER TABLE `blog_tag_rel` ADD INDEX(`blog_post_id`);
ALTER TABLE `blog_tag_rel` ADD INDEX(`blog_tag_id`);

ALTER TABLE `blog_tag_rel` ADD FOREIGN KEY (`blog_post_id`) REFERENCES `webkot5-test1`.`blog_post`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE `blog_tag_rel` ADD FOREIGN KEY (`blog_tag_id`) REFERENCES `webkot5-test1`.`blog_tag`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;





-- LINK
ALTER TABLE `link_category` ADD `id` INT UNSIGNED NOT NULL FIRST;
UPDATE `link_category` SET `id` = '1' WHERE `name` = 'autres';
UPDATE `link_category` SET `id` = '2' WHERE `name` = 'cercles';
UPDATE `link_category` SET `id` = '3' WHERE `name` = 'instances';
UPDATE `link_category` SET `id` = '4' WHERE `name` = 'kaps';
UPDATE `link_category` SET `id` = '5' WHERE `name` = 'regionales';

ALTER TABLE `link_category` ADD `slug` VARCHAR(64) NOT NULL AFTER `name`;

UPDATE link_category SET slug = name;

ALTER TABLE `link` ADD `category_id` INT UNSIGNED NOT NULL AFTER `url`;

UPDATE link SET category_id = (SELECT id FROM link_category WHERE `link_category`.`name` = `link`.`category`);

ALTER TABLE link DROP FOREIGN KEY fk_linkToCategory;
ALTER TABLE link DROP COLUMN category;

ALTER TABLE `link` ADD INDEX(`category_id`);

ALTER TABLE `link_category` DROP `name`;
ALTER TABLE `link_category` ADD PRIMARY KEY(`id`);

ALTER TABLE `link` ADD FOREIGN KEY (`category_id`) REFERENCES `link_category`(`id`) ON DELETE RESTRICT ON UPDATE RESTRICT;


