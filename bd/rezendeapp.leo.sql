-- MySQL Script generated by MySQL Workbench
-- Thu Jan  2 20:31:19 2020
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
-- Table `acesso`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `acesso` ;

SHOW WARNINGS;
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
DROP TABLE IF EXISTS `anexos` ;

SHOW WARNINGS;
CREATE TABLE IF NOT EXISTS `anexos` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `caminho` TEXT NOT NULL,
  `tipo` VARCHAR(100) NULL,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  `questionamento_id` INT NOT NULL DEFAULT 0,
  `planoacao_id` INT NOT NULL,
  PRIMARY KEY (`id`, `questionamento_id`, `planoacao_id`))
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
-- Table `checklist`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `checklist` ;

SHOW WARNINGS;
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
DROP TABLE IF EXISTS `checklist_has_questionario` ;

SHOW WARNINGS;
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
DROP TABLE IF EXISTS `colaborador` ;

SHOW WARNINGS;
CREATE TABLE IF NOT EXISTS `colaborador` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `nome` VARCHAR(80) NOT NULL,
  `sobrenome` VARCHAR(50) NOT NULL,
  `email` VARCHAR(80) NOT NULL,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  `usuario_id` INT NOT NULL,
  `setor_id` INT UNSIGNED NOT NULL,
  `avatar_id` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`, `usuario_id`, `setor_id`, `avatar_id`))
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `endereco`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `endereco` ;

SHOW WARNINGS;
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
DROP TABLE IF EXISTS `grupo_usuario` ;

SHOW WARNINGS;
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
DROP TABLE IF EXISTS `hitoricoresponsabilidade` ;

SHOW WARNINGS;
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
DROP TABLE IF EXISTS `loja` ;

SHOW WARNINGS;
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
DROP TABLE IF EXISTS `pendencia` ;

SHOW WARNINGS;
CREATE TABLE IF NOT EXISTS `pendencia` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `status` VARCHAR(45) NOT NULL,
  `descricao` VARCHAR(255) NOT NULL,
  `descricaosolucao` VARCHAR(45) NOT NULL,
  `datalimite` DATETIME NOT NULL,
  `dataexecucao` DATETIME NULL,
  `datacadastro` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `responsavel_id` INT UNSIGNED NOT NULL,
  `loja_id` INT UNSIGNED NOT NULL DEFAULT 0,
  `deleted_at` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`, `responsavel_id`, `loja_id`))
ENGINE = InnoDB;

SHOW WARNINGS;

-- -----------------------------------------------------
-- Table `planoacao`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `planoacao` ;

SHOW WARNINGS;
CREATE TABLE IF NOT EXISTS `planoacao` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `status` VARCHAR(45) NOT NULL,
  `descricaonaoconformidade` VARCHAR(255) NOT NULL,
  `descricaosolucao` TEXT NOT NULL,
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
DROP TABLE IF EXISTS `questionamento` ;

SHOW WARNINGS;
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
DROP TABLE IF EXISTS `questionario` ;

SHOW WARNINGS;
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
DROP TABLE IF EXISTS `recurso` ;

SHOW WARNINGS;
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
DROP TABLE IF EXISTS `rota` ;

SHOW WARNINGS;
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
DROP TABLE IF EXISTS `setor` ;

SHOW WARNINGS;
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
DROP TABLE IF EXISTS `usuario` ;

SHOW WARNINGS;
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
-- procedure popularChecklistsTest
-- -----------------------------------------------------

USE `rezendeapp`;
DROP procedure IF EXISTS `popularChecklistsTest`;
SHOW WARNINGS;

DELIMITER $$
USE `rezendeapp`$$
CREATE  PROCEDURE `popularChecklistsTest`(IN _IDQuestionador INT, IN _IDReponsavel INT, IN _IDsetor INT, IN _IDLoja INT)
BEGIN
    DECLARE x, ultimoID  INT;
        
    SET x = 1;
        
    loop_label:  LOOP
        IF  x > 500 THEN 
            LEAVE  loop_label;
        END  IF;
		
        
        insert into `checklist`(status, titulo, tipoChecklist, descricao, data_limite, questionador_id, responsavel_id,setor_id, checklist_id,loja_id)
        values('Aguardando Execução', concat('checklist', x), 'Aguardando Execução','criacaoTeste', ADDDATE(now(), INTERVAL (x *2) DAY), _IDQuestionador, _IDReponsavel, _IDsetor, 0,_IDLoja);
		SET ultimoID = (SELECT LAST_INSERT_ID());
		INSERT INTO `checklist_has_questionario`(checklist_id, questionario_id) VALUES (ultimoID, 1);
		INSERT INTO `checklist_has_questionario`(checklist_id, questionario_id) VALUES (ultimoID, 2);
		INSERT INTO `questionamento`(status, formulariopergunta, indice, checklist_id, planoacao_id) VALUES ('Não Respondido', '{"id":"1","pergunta":"Pergunta 01 :"}', 1, ultimoID, 0);
		INSERT INTO `questionamento`(status, formulariopergunta, indice, checklist_id, planoacao_id) VALUES ('Não Respondido', '{"id":"2","pergunta":"Pergunta 02 :"}', 2, ultimoID, 0);
		INSERT INTO `questionamento`(status, formulariopergunta, indice, checklist_id, planoacao_id) VALUES ('Não Respondido', '{"id":"3","pergunta":"Pergunta 03 :"}', 3, ultimoID, 0);
		INSERT INTO `questionamento`(status, formulariopergunta, indice, checklist_id, planoacao_id) VALUES ('Não Respondido', '{"id":"4","pergunta":"Pergunta 04 :"}', 4, ultimoID, 0);
		INSERT INTO `questionamento`(status, formulariopergunta, indice, checklist_id, planoacao_id) VALUES ('Não Respondido', '{"id":"1","pergunta":"QUestão 01 :"}', 5, ultimoID, 0);
		INSERT INTO `questionamento`(status, formulariopergunta, indice, checklist_id, planoacao_id) VALUES ('Não Respondido', '{"id":"2","pergunta":"Questão 02 :"}', 6, ultimoID, 0);
		INSERT INTO `questionamento`(status, formulariopergunta, indice, checklist_id, planoacao_id) VALUES ('Não Respondido', '{"id":"3","pergunta":"QUestão 03 :"}', 7, ultimoID, 0);
        SET  x = x + 1;
		ITERATE  loop_label;
    END LOOP;
  End$$

DELIMITER ;
SHOW WARNINGS;

-- -----------------------------------------------------
-- View `vw_colaborador_usuario`
-- -----------------------------------------------------
DROP VIEW IF EXISTS `vw_colaborador_usuario` ;
SHOW WARNINGS;
USE `rezendeapp`;
CREATE  OR REPLACE VIEW `vw_colaborador_usuario` AS select c.nome, c.sobrenome, c.email, u.login from colaborador c inner join usuario u on u.id = c.usuario_id;
SHOW WARNINGS;

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

-- -----------------------------------------------------
-- Data for table `atuacao`
-- -----------------------------------------------------
START TRANSACTION;
USE `rezendeapp`;
INSERT INTO `atuacao` (`loja_id`, `colaborador_id`) VALUES (1, 1);
INSERT INTO `atuacao` (`loja_id`, `colaborador_id`) VALUES (2, 1);
INSERT INTO `atuacao` (`loja_id`, `colaborador_id`) VALUES (3, 1);

COMMIT;


-- -----------------------------------------------------
-- Data for table `colaborador`
-- -----------------------------------------------------
START TRANSACTION;
USE `rezendeapp`;
INSERT INTO `colaborador` (`id`, `nome`, `sobrenome`, `email`, `deleted_at`, `usuario_id`, `setor_id`, `avatar_id`) VALUES (1, 'Admin', 'Sistema', 'sistema@rezendeconstrucao.com.br', NULL, 1, 1, DEFAULT);

COMMIT;


-- -----------------------------------------------------
-- Data for table `endereco`
-- -----------------------------------------------------
START TRANSACTION;
USE `rezendeapp`;
INSERT INTO `endereco` (`id`, `cep`, `logradouro`, `numero`, `complemento`, `bairro`, `cidade`, `uf`, `deleted_at`) VALUES (1, '28613-210', 'Rua Moisés Amélio', 26, 'Loja', 'Centro', 'Nova Friburgo', 'RJ', NULL);
INSERT INTO `endereco` (`id`, `cep`, `logradouro`, `numero`, `complemento`, `bairro`, `cidade`, `uf`, `deleted_at`) VALUES (2, '28613-060', 'Rua Duque de Caxias', 13, 'Loja', 'Centro', 'Nova Friburgo', 'RJ', NULL);
INSERT INTO `endereco` (`id`, `cep`, `logradouro`, `numero`, `complemento`, `bairro`, `cidade`, `uf`, `deleted_at`) VALUES (3, '28635-000', 'Avenida Governador Roberto Silveira', 3550, 'Loja', 'Conselheiro Paulino', 'Nova Friburgo', 'RJ', NULL);

COMMIT;


-- -----------------------------------------------------
-- Data for table `loja`
-- -----------------------------------------------------
START TRANSACTION;
USE `rezendeapp`;
INSERT INTO `loja` (`id`, `razaoSocial`, `nomeFantasia`, `deleted_at`, `endereco_id`) VALUES (1, 'Rezende Construção', 'Matriz', NULL, 1);
INSERT INTO `loja` (`id`, `razaoSocial`, `nomeFantasia`, `deleted_at`, `endereco_id`) VALUES (2, 'Rezende Construção', 'Duque', NULL, 2);
INSERT INTO `loja` (`id`, `razaoSocial`, `nomeFantasia`, `deleted_at`, `endereco_id`) VALUES (3, 'Rezende Construção', 'Conselheiro', NULL, 3);

COMMIT;


-- -----------------------------------------------------
-- Data for table `setor`
-- -----------------------------------------------------
START TRANSACTION;
USE `rezendeapp`;
INSERT INTO `setor` (`id`, `titulo`, `descricao`, `deleted_at`) VALUES (1, 'Sistema', 'Sistema', NULL);

COMMIT;


-- -----------------------------------------------------
-- Data for table `usuario`
-- -----------------------------------------------------
START TRANSACTION;
USE `rezendeapp`;
INSERT INTO `usuario` (`id`, `login`, `senha`, `administrador`, `deleted_at`) VALUES (1, 'admin', '9fb576f8f8afddd7d2db96fe5a2bb3d9', 1, NULL);

COMMIT;

