-- MySQL Script generated by MySQL Workbench
-- qua 01 abr 2020 12:04:19 -03
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
CREATE SCHEMA IF NOT EXISTS `rezendeapp` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ;
SHOW WARNINGS;
USE `rezendeapp` ;

-- -----------------------------------------------------
-- Table `acesso`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `acesso` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `recursoId` INT NOT NULL,
  `acessanteTipo` VARCHAR(45) NULL,
  `acessanteId` INT NULL,
  `acao` VARCHAR(45) NULL DEFAULT 'Permitir',
  PRIMARY KEY (`id`),
  INDEX `recurso` USING BTREE (`recursoId`),
  INDEX `acessante` (`acessanteTipo` ASC, `acessanteId` ASC));

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `anexos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `anexos` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `caminho` TEXT NOT NULL,
  `tipo` VARCHAR(100) NULL,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  `questionamento_id` INT NULL DEFAULT 0,
  `planoacao_id` INT NULL,
  `pendencia_id` INT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `atuacao`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `atuacao` (
  `loja_id` INT NOT NULL,
  `colaborador_id` INT NOT NULL,
  PRIMARY KEY (`loja_id`, `colaborador_id`))
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `checklist`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `checklist` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `status` VARCHAR(45) NOT NULL,
  `titulo` VARCHAR(100) NULL,
  `tipoChecklist` VARCHAR(30) NULL,
  `descricao` VARCHAR(255) NULL,
  `data_limite` DATETIME NULL DEFAULT NOW(),
  `dataexecucao` DATETIME NULL,
  `data_cadastro` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `encerrado` TINYINT(1) NULL,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  `questionador_id` INT UNSIGNED NOT NULL,
  `responsavel_id` INT UNSIGNED NOT NULL,
  `setor_id` INT UNSIGNED NOT NULL,
  `checklist_id` INT NOT NULL DEFAULT 0,
  `loja_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`, `questionador_id`, `responsavel_id`, `setor_id`, `checklist_id`, `loja_id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `checklist_has_questionario`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `checklist_has_questionario` (
  `checklist_id` INT NOT NULL,
  `questionario_id` INT NOT NULL,
  PRIMARY KEY (`checklist_id`, `questionario_id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `colaborador`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `colaborador` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(80) NOT NULL,
  `sobrenome` VARCHAR(50) NOT NULL,
  `email` VARCHAR(80) NOT NULL,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  `usuario_id` INT NOT NULL,
  `setor_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`, `usuario_id`, `setor_id`))
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `endereco`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `endereco` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `cep` VARCHAR(9) NOT NULL,
  `logradouro` VARCHAR(255) NOT NULL,
  `numero` INT NOT NULL,
  `complemento` VARCHAR(255) NOT NULL,
  `bairro` VARCHAR(255) NOT NULL,
  `cidade` VARCHAR(255) NOT NULL,
  `uf` VARCHAR(255) NOT NULL,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `grupo_usuario`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `grupo_usuario` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(100) NOT NULL,
  `descricao` VARCHAR(255) NULL,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`))
ENGINE = MyISAM
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `hitoricoresponsabilidade`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `hitoricoresponsabilidade` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `datahoramudacanca` DATETIME NULL,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  `planoacao_id` INT NOT NULL,
  `responsavelatual_id` INT UNSIGNED NOT NULL,
  `responsavelanterior_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`id`, `planoacao_id`, `responsavelatual_id`, `responsavelanterior_id`))
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `loja`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `loja` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `razaoSocial` VARCHAR(100) NOT NULL,
  `nomeFantasia` VARCHAR(100) NOT NULL,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  `endereco_id` INT NOT NULL,
  PRIMARY KEY (`id`, `endereco_id`))
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `pendencia`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `pendencia` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `status` VARCHAR(45) NOT NULL,
  `descricao` VARCHAR(255) NOT NULL,
  `descricaosolucao` TEXT NOT NULL,
  `detalhesexecucao` TEXT NULL,
  `datalimite` DATETIME NOT NULL,
  `dataexecucao` DATETIME NULL,
  `datacadastro` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  `responsavel_id` INT UNSIGNED NOT NULL,
  `loja_id` INT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`, `responsavel_id`, `loja_id`))
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `planoacao`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `planoacao` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `status` VARCHAR(45) NOT NULL,
  `descricaonaoconformidade` VARCHAR(255) NOT NULL,
  `descricaosolucao` TEXT NOT NULL,
  `detalhesexecucao` TEXT NULL,
  `datalimite` DATETIME NOT NULL,
  `dataexecucao` DATETIME NULL,
  `responsabilidade` TINYINT(1) NULL,
  `datacadastro` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  `responsavel_id` INT UNSIGNED NOT NULL,
  `loja_id` INT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`, `responsavel_id`, `loja_id`))
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `questionamento`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `questionamento` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `status` VARCHAR(45) NOT NULL,
  `formulariopergunta` TEXT NOT NULL,
  `formularioresposta` TEXT NULL,
  `indice` INT NOT NULL,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  `checklist_id` INT NOT NULL DEFAULT 0,
  `planoacao_id` INT NOT NULL DEFAULT 0,
  `pendencia_id` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`, `checklist_id`, `planoacao_id`, `pendencia_id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `questionario`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `questionario` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `titulo` VARCHAR(45) NOT NULL,
  `descricao` VARCHAR(255) NOT NULL,
  `tipoQuestionario` VARCHAR(45) NOT NULL,
  `formulario` TEXT NOT NULL,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `recurso`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `recurso` (
  `id` INT UNSIGNED NOT NULL,
  `nome` VARCHAR(255) NOT NULL,
  `model` VARCHAR(80) NULL,
  PRIMARY KEY (`id`),
  INDEX `model` USING BTREE (`model`));

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `rota`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `rota` (
  `id` INT UNSIGNED NOT NULL,
  `caminho` VARCHAR(255) NULL,
  `metodo` VARCHAR(20) NULL,
  `recursoId` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `recurso` (`recursoId` ASC));

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `setor`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `setor` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `titulo` VARCHAR(45) NULL,
  `descricao` VARCHAR(255) NULL,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `usuario`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `usuario` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `login` VARCHAR(20) NOT NULL,
  `senha` VARCHAR(255) NOT NULL,
  `administrador` TINYINT(1) NULL,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `usuario_grupo_usuario`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `usuario_grupo_usuario` (
  `usuario_id` INT NOT NULL,
  `grupo_usuario_id` INT NOT NULL,
  PRIMARY KEY (`usuario_id`, `grupo_usuario_id`))
ENGINE = InnoDB;

SHOW WARNINGS;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
