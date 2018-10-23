-- MySQL Script generated by MySQL Workbench
-- Ter 23 Out 2018 12:40:00 -03
-- Model: New Model    Version: 1.0
-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema rezendeapp
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema rezendeapp
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `rezendeapp` DEFAULT CHARACTER SET utf8 ;
USE `rezendeapp` ;

-- -----------------------------------------------------
-- Table `rezendeapp`.`categoria`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `rezendeapp`.`categoria` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `titulo` VARCHAR(255) NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `rezendeapp`.`checklist`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `rezendeapp`.`checklist` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `titulo` VARCHAR(45) NULL,
  `descricao` VARCHAR(255) NULL,
  `cadastro` TIMESTAMP(6) NULL DEFAULT CURRENT_TIMESTAMP(6),
  `categoria_id` INT NOT NULL,
  PRIMARY KEY (`id`, `categoria_id`),
  INDEX `fk_checklist_categoria_idx` (`categoria_id` ASC),
  INDEX `titulo` (`titulo` ASC),
  CONSTRAINT `fk_checklist_categoria`
    FOREIGN KEY (`categoria_id`)
    REFERENCES `rezendeapp`.`categoria` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `rezendeapp`.`tarefa`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `rezendeapp`.`tarefa` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `titulo` VARCHAR(80) NULL,
  `descricao` VARCHAR(255) NULL,
  `checklist_id` INT NOT NULL,
  PRIMARY KEY (`id`, `checklist_id`),
  INDEX `fk_tarefa_checklist1_idx` (`checklist_id` ASC),
  INDEX `titulo` (`titulo` ASC),
  CONSTRAINT `fk_tarefa_checklist1`
    FOREIGN KEY (`checklist_id`)
    REFERENCES `rezendeapp`.`checklist` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
