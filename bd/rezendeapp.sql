-- MySQL Script generated by MySQL Workbench
-- Sáb 03 Nov 2018 08:45:52 -03
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
  `descricao` VARCHAR(255) NULL,
  `data_cadastro` TIMESTAMP(6) NULL DEFAULT CURRENT_TIMESTAMP(6),
  `data_limite` DATETIME NULL,
  `categoria_id` INT NOT NULL,
  PRIMARY KEY (`id`, `categoria_id`),
  INDEX `fk_checklist_categoria_idx` (`categoria_id` ASC),
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


-- -----------------------------------------------------
-- Table `rezendeapp`.`loja`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `rezendeapp`.`loja` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(100) NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `rezendeapp`.`checklist_tem_loja`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `rezendeapp`.`checklist_tem_loja` (
  `checklist_id` INT NOT NULL,
  `loja_id` INT NOT NULL,
  PRIMARY KEY (`checklist_id`, `loja_id`),
  INDEX `fk_checklist_has_loja_loja2_idx` (`loja_id` ASC),
  INDEX `fk_checklist_has_loja_checklist2_idx` (`checklist_id` ASC),
  CONSTRAINT `fk_checklist_has_loja_checklist2`
    FOREIGN KEY (`checklist_id`)
    REFERENCES `rezendeapp`.`checklist` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_checklist_has_loja_loja2`
    FOREIGN KEY (`loja_id`)
    REFERENCES `rezendeapp`.`loja` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `rezendeapp`.`checklist_tem_loja`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `rezendeapp`.`checklist_tem_loja` (
  `checklist_id` INT NOT NULL,
  `loja_id` INT NOT NULL,
  PRIMARY KEY (`checklist_id`, `loja_id`),
  INDEX `fk_checklist_has_loja_loja2_idx` (`loja_id` ASC),
  INDEX `fk_checklist_has_loja_checklist2_idx` (`checklist_id` ASC),
  CONSTRAINT `fk_checklist_has_loja_checklist2`
    FOREIGN KEY (`checklist_id`)
    REFERENCES `rezendeapp`.`checklist` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_checklist_has_loja_loja2`
    FOREIGN KEY (`loja_id`)
    REFERENCES `rezendeapp`.`loja` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
