SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';


-- -----------------------------------------------------
-- Table `xerocopy_attachments`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `xerocopy_attachments` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `filename` VARCHAR(255) NOT NULL ,
  `added` DATETIME NOT NULL ,
  `original_mime` VARCHAR(45) NULL DEFAULT NULL ,
  `original_size_x` INT UNSIGNED NOT NULL ,
  `original_size_y` INT UNSIGNED NOT NULL ,
  `original_filesize` INT UNSIGNED NOT NULL ,
  `source` VARCHAR(1000) NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
