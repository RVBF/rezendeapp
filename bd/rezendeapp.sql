-- MySQL Script generated by MySQL Workbench
-- sex 20 set 2019 16:43:18 -03
-- Model: New Model    Version: 1.0
-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema rezendeapp
-- -----------------------------------------------------
DROP SCHEMA IF EXISTS `rezendeapp` ;

-- -----------------------------------------------------
-- Schema rezendeapp
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `rezendeapp` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ;
SHOW WARNINGS;
USE `rezendeapp` ;

-- -----------------------------------------------------
-- Table `anexos`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `anexos` ;

SHOW WARNINGS;
CREATE TABLE IF NOT EXISTS `anexos` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `caminho` VARCHAR(255) NOT NULL,
  `tipo` VARCHAR(100) NULL,
  `resposta_id` INT NOT NULL,
  PRIMARY KEY (`id`, `resposta_id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `atuacao`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `atuacao` ;

SHOW WARNINGS;
CREATE TABLE IF NOT EXISTS `atuacao` (
  `loja_id` INT NOT NULL,
  `colaborador_id` INT NOT NULL,
  PRIMARY KEY (`loja_id`, `colaborador_id`))
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `categoria`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `categoria` ;

SHOW WARNINGS;
CREATE TABLE IF NOT EXISTS `categoria` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `titulo` VARCHAR(255) NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `checklist`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `checklist` ;

SHOW WARNINGS;
CREATE TABLE IF NOT EXISTS `checklist` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `titulo` VARCHAR(100) NULL,
  `tipoTarefa` VARCHAR(30) NULL,
  `descricao` VARCHAR(255) NULL,
  `data_limite` DATETIME NULL,
  `data_cadastro` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
  `encerrada` TINYINT(1) NULL,
  `questionador_id` INT UNSIGNED NOT NULL,
  `responsavel_id` INT UNSIGNED NOT NULL,
  `setor_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`, `questionador_id`, `responsavel_id`, `setor_id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `colaborador`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `colaborador` ;

SHOW WARNINGS;
CREATE TABLE IF NOT EXISTS `colaborador` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(80) NOT NULL,
  `sobrenome` VARCHAR(50) NOT NULL,
  `email` VARCHAR(80) NOT NULL,
  `usuario_id` INT NOT NULL,
  PRIMARY KEY (`id`, `usuario_id`))
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `grupo_usuario`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `grupo_usuario` ;

SHOW WARNINGS;
CREATE TABLE IF NOT EXISTS `grupo_usuario` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(100) NOT NULL,
  `descricao` VARCHAR(255) NULL,
  `eadmin` TINYINT(1) NULL DEFAULT 0,
  PRIMARY KEY (`id`))
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `loja`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `loja` ;

SHOW WARNINGS;
CREATE TABLE IF NOT EXISTS `loja` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `razaoSocial` VARCHAR(100) NULL,
  `nomeFantasia` VARCHAR(100) NULL,
  `checklist_id` INT NOT NULL,
  PRIMARY KEY (`id`, `checklist_id`))
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `opcao`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `opcao` ;

SHOW WARNINGS;
CREATE TABLE IF NOT EXISTS `opcao` (
  `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `opcao` VARCHAR(100) NOT NULL,
  `tipoAtributo` VARCHAR(45) NOT NULL,
  `pergunta_id` INT NOT NULL,
  `pergunta_questionario_id` INT NOT NULL,
  PRIMARY KEY (`id`, `pergunta_id`, `pergunta_questionario_id`))
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `pergunta`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `pergunta` ;

SHOW WARNINGS;
CREATE TABLE IF NOT EXISTS `pergunta` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `pergunta` VARCHAR(255) NULL,
  `questionario_id` INT NOT NULL,
  PRIMARY KEY (`id`, `questionario_id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `questionamento`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `questionamento` ;

SHOW WARNINGS;
CREATE TABLE IF NOT EXISTS `questionamento` (
  `checklist_id` INT NOT NULL,
  `questionario_id` INT NOT NULL,
  `status` VARCHAR(45) NULL,
  `resposta_id` INT UNSIGNED NOT NULL,
  `resposta_pergunta_id` INT NOT NULL,
  `pa_id` INT NOT NULL,
  PRIMARY KEY (`checklist_id`, `questionario_id`, `resposta_id`, `resposta_pergunta_id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `questionario`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `questionario` ;

SHOW WARNINGS;
CREATE TABLE IF NOT EXISTS `questionario` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `titulo` VARCHAR(45) NOT NULL,
  `descricao` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `resposta`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `resposta` ;

SHOW WARNINGS;
CREATE TABLE IF NOT EXISTS `resposta` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `opcaoSelecionada` INT NULL,
  `comentario` VARCHAR(200) NULL,
  `pergunta_id` INT NOT NULL,
  PRIMARY KEY (`id`, `pergunta_id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `setor`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `setor` ;

SHOW WARNINGS;
CREATE TABLE IF NOT EXISTS `setor` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `titulo` VARCHAR(45) NULL,
  `descricao` VARCHAR(255) NULL,
  `categoria_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`, `categoria_id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `usuario`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `usuario` ;

SHOW WARNINGS;
CREATE TABLE IF NOT EXISTS `usuario` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `login` VARCHAR(20) NOT NULL,
  `senha` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;

SHOW WARNINGS;
CREATE INDEX `login_usuario_index` ON `usuario` (`login` ASC) VISIBLE;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `usuario_grupo_usuario`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `usuario_grupo_usuario` ;

SHOW WARNINGS;
CREATE TABLE IF NOT EXISTS `usuario_grupo_usuario` (
  `usuario_id` INT NOT NULL,
  `grupo_usuario_id` INT NOT NULL,
  PRIMARY KEY (`usuario_id`, `grupo_usuario_id`))
ENGINE = InnoDB;

SHOW WARNINGS;
USE `rezendeapp` ;

-- -----------------------------------------------------
-- View `vw_colaborador_usuario`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `vw_colaborador_usuario` ;
SHOW WARNINGS;
USE `rezendeapp`;
CREATE or replace VIEW `vw_colaborador_usuario` AS select c.nome, c.sobrenome, c.email, u.login, u.senha from colaborador inner join usuario u on u.id = colaborador.usuario_id;
SHOW WARNINGS;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
