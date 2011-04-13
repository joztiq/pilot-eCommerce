SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

CREATE SCHEMA IF NOT EXISTS `pec` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci ;
USE `pec` ;

-- -----------------------------------------------------
-- Table `pec`.`pec_categories`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `pec`.`pec_categories` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `description` VARCHAR(256) NULL ,
  `name` VARCHAR(45) NULL ,
  `image` BLOB NULL ,
  `imageType` VARCHAR(45) NULL ,
  `sortOrder` INT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `id_UNIQUE` (`id` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `pec`.`pec_currencies`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `pec`.`pec_currencies` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NULL ,
  `suffix` VARCHAR(12) NULL ,
  `prefix` VARCHAR(12) NULL ,
  `currencyCode` VARCHAR(3) NULL ,
  `modifier` VARCHAR(45) NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `id_UNIQUE` (`id` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `pec`.`pec_customCustomerFields`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `pec`.`pec_customCustomerFields` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NULL ,
  `customerId` INT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `id_UNIQUE` (`id` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `pec`.`pec_customers`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `pec`.`pec_customers` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `firstName` VARCHAR(45) NULL ,
  `lastName` VARCHAR(45) NULL ,
  `streetAddress` VARCHAR(45) NULL ,
  `postalCode` VARCHAR(45) NULL ,
  `city` VARCHAR(45) NULL ,
  `phone` VARCHAR(45) NULL ,
  `country` VARCHAR(45) NULL ,
  `email` VARCHAR(45) NULL ,
  `password` VARCHAR(128) NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `id_UNIQUE` (`id` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `pec`.`pec_manufacturers`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `pec`.`pec_manufacturers` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NULL ,
  `logo` BLOB NULL ,
  `logoType` VARCHAR(45) NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `id_UNIQUE` (`id` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `pec`.`pec_productAttributes`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `pec`.`pec_productAttributes` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `id_UNIQUE` (`id` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `pec`.`pec_productAttributeValues`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `pec`.`pec_productAttributeValues` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `attributeId` INT NULL ,
  `priceModifier` VARCHAR(45) NULL ,
  `quantity` INT NULL ,
  `value` VARCHAR(45) NULL ,
  `status` INT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `id_UNIQUE` (`id` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `pec`.`pec_products`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `pec`.`pec_products` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `categoryId` INT NULL ,
  `manufacturerId` VARCHAR(45) NULL ,
  `name` VARCHAR(45) NULL ,
  `shortDescription` VARCHAR(128) NULL ,
  `longDescription` VARCHAR(1024) NULL ,
  `SKU` VARCHAR(45) NULL ,
  `price` VARCHAR(45) NULL ,
  `taxId` INT NULL ,
  `weight` DECIMAL NULL ,
  `status` INT NULL ,
  `quantity` INT NULL ,
  `dateAdded` DATETIME NULL ,
  `useQuantity` TINYINT(1)  NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `id_UNIQUE` (`id` ASC) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `pec`.`pec_taxes`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `pec`.`pec_taxes` (
  `id` INT NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NULL ,
  `rate` DECIMAL NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `id_UNIQUE` (`id` ASC) )
ENGINE = InnoDB;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
