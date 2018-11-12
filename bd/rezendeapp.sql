-- MySQL Script generated by MySQL Workbench
-- Mon Nov 12 16:59:49 2018
-- Model: New Model    Version: 1.0
-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

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
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `rezendeapp`.`loja`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `rezendeapp`.`loja` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `razaoSocial` VARCHAR(100) NULL,
  `nomeFantasia` VARCHAR(100) NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `rezendeapp`.`checklist`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `rezendeapp`.`checklist` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `descricao` VARCHAR(255) NULL,
  `data_limite` DATETIME NULL,
  `data_cadastro` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
  `categoria_id` INT NOT NULL,
  `loja_id` INT NOT NULL,
  PRIMARY KEY (`id`, `categoria_id`, `loja_id`),
  INDEX `fk_checklist_categoria_idx` (`categoria_id` ASC),
  INDEX `fk_checklist_loja1_idx` (`loja_id` ASC),
  CONSTRAINT `fk_checklist_categoria`
    FOREIGN KEY (`categoria_id`)
    REFERENCES `rezendeapp`.`categoria` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_checklist_loja1`
    FOREIGN KEY (`loja_id`)
    REFERENCES `rezendeapp`.`loja` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `rezendeapp`.`tarefa`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `rezendeapp`.`tarefa` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `titulo` VARCHAR(100) NULL,
  `descricao` VARCHAR(255) NULL,
  `checklist_id` INT NOT NULL,
  PRIMARY KEY (`id`, `checklist_id`),
  INDEX `fk_tarefa_checklist1_idx` (`checklist_id` ASC),
  CONSTRAINT `fk_tarefa_checklist1`
    FOREIGN KEY (`checklist_id`)
    REFERENCES `rezendeapp`.`checklist` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `rezendeapp`.`pergunta`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `rezendeapp`.`pergunta` (
  `id` INT NOT NULL,
  `pergunta` VARCHAR(255) NULL,
  `tarefa_id` INT NOT NULL,
  PRIMARY KEY (`id`, `tarefa_id`),
  INDEX `fk_pergunta_tarefa1_idx` (`tarefa_id` ASC),
  CONSTRAINT `fk_pergunta_tarefa1`
    FOREIGN KEY (`tarefa_id`)
    REFERENCES `rezendeapp`.`tarefa` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
