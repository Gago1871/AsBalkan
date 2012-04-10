SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

DROP SCHEMA IF EXISTS `poebao` ;
CREATE SCHEMA IF NOT EXISTS `poebao` DEFAULT CHARACTER SET utf8 COLLATE utf8_polish_ci ;
USE `poebao` ;

-- -----------------------------------------------------
-- Table `poebao`.`posts`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `poebao`.`posts` ;

CREATE  TABLE IF NOT EXISTS `poebao`.`posts` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `post_id` VARCHAR(12) NOT NULL ,
  `title` VARCHAR(255) NOT NULL ,
  `author` VARCHAR(255) NOT NULL ,
  `source` VARCHAR(255) NULL ,
  `file` VARCHAR(1500) NOT NULL ,
  `status_waiting` TINYINT NOT NULL DEFAULT 1 ,
  `status_promoted` TINYINT NOT NULL DEFAULT 0 ,
  `status_moderated` TINYINT NOT NULL DEFAULT 0 ,
  `added` DATETIME NOT NULL ,
  `original_file` VARCHAR(1500) NOT NULL ,
  `author_ip` VARCHAR(15) NOT NULL ,
  `status` ENUM('a','d') NOT NULL COMMENT 'a - active\nd - deleted\n' ,
  `updated` TIMESTAMP NOT NULL ,
  `agreement` TINYINT NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `post_id_UNIQUE` (`post_id` ASC) )
ENGINE = InnoDB;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
