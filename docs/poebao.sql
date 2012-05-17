SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

DROP SCHEMA IF EXISTS `my_7727` ;
CREATE SCHEMA IF NOT EXISTS `my_7727` DEFAULT CHARACTER SET utf8 COLLATE utf8_polish_ci ;
USE `my_7727` ;

-- -----------------------------------------------------
-- Table `my_7727`.`posts`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `my_7727`.`posts` ;

CREATE  TABLE IF NOT EXISTS `my_7727`.`posts` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `post_id` VARCHAR(12) NOT NULL ,
  `title` VARCHAR(255) NOT NULL ,
  `author` VARCHAR(255) NOT NULL ,
  `source` VARCHAR(255) NULL ,
  `category` INT UNSIGNED NOT NULL DEFAULT 0 ,
  `flag_nsfw` TINYINT UNSIGNED NOT NULL DEFAULT 0 ,
  `status` ENUM('a','d') NOT NULL COMMENT 'a - active\nd - deleted\n' ,
  `added` DATETIME NOT NULL ,
  `moderated` DATETIME NULL ,
  `author_ip` VARCHAR(15) NOT NULL ,
  `updated` TIMESTAMP NOT NULL ,
  `agreement` TINYINT NOT NULL DEFAULT 0 ,
  `attachment_id` INT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `post_id_UNIQUE` (`post_id` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `my_7727`.`attachments`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `my_7727`.`attachments` ;

CREATE  TABLE IF NOT EXISTS `my_7727`.`attachments` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `filename` VARCHAR(255) NOT NULL ,
  `added` DATETIME NOT NULL ,
  `original_mime` VARCHAR(45) NULL ,
  `original_size_x` INT UNSIGNED NOT NULL ,
  `original_size_y` INT UNSIGNED NOT NULL ,
  `original_filesize` INT UNSIGNED NOT NULL ,
  `source` VARCHAR(1000) NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
