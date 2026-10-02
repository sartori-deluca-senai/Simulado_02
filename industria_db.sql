-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';


-- -----------------------------------------------------
-- Schema industria_db
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS industria_db DEFAULT CHARACTER SET utf8 ;
USE industria_db ;


-- -----------------------------------------------------
-- Table industria_db.usuarios
-- -----------------------------------------------------
DROP TABLE IF EXISTS industria_db.movimentacoes;
DROP TABLE IF EXISTS industria_db.produtos;
DROP TABLE IF EXISTS industria_db.usuarios;


CREATE TABLE IF NOT EXISTS industria_db.usuarios (
  id INT NOT NULL AUTO_INCREMENT,
  nome VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL,
  senha VARCHAR(255) NOT NULL,
  ativo TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (id),
  UNIQUE INDEX email_UNIQUE (email ASC) VISIBLE)
ENGINE = InnoDB;




-- -----------------------------------------------------
-- Table industria_db.produtos
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS industria_db.produtos (
  id INT NOT NULL AUTO_INCREMENT,
  codigo VARCHAR(45) NOT NULL,
  nome VARCHAR(150) NOT NULL,
  fabricante VARCHAR(100) NOT NULL,
  preco DECIMAL(10,2) NOT NULL,
  estoque_atual INT NOT NULL DEFAULT 0,
  estoque_minimo INT NOT NULL,
  ativo TINYINT(1) NOT NULL DEFAULT 1,
  categoria VARCHAR(45) NULL,
  material_fabricacao VARCHAR(45) NULL,
  PRIMARY KEY (id))
ENGINE = InnoDB;




-- -----------------------------------------------------
-- Table industria_db.movimentacoes
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS industria_db.movimentacoes (
  id INT NOT NULL AUTO_INCREMENT,
  tipo INT NOT NULL COMMENT '1- entrada 2- saída',
  date DATE NOT NULL,
  quantidade INT NOT NULL,
  saldo_anterior INT NOT NULL,
  usuarios_id INT NOT NULL,
  produtos_id INT NOT NULL,
  PRIMARY KEY (id),
  INDEX fk_movimentacoes_usuarios_idx (usuarios_id ASC) VISIBLE,
  INDEX fk_movimentacoes_produtos1_idx (produtos_id ASC) VISIBLE,
  CONSTRAINT fk_movimentacoes_usuarios
    FOREIGN KEY (usuarios_id)
    REFERENCES industria_db.usuarios (id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT fk_movimentacoes_produtos1
    FOREIGN KEY (produtos_id)
    REFERENCES industria_db.produtos (id)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- --------------------------------------------------
-- INSERÇÕES
-- --------------------------------------------------


INSERT INTO usuarios (id, nome, email, senha) VALUES 
  (1, 'Laysla', 'laysla@email.com', '1'),
  (2, 'Natan', 'natan@email.com', '12'),
  (3, 'Anna', 'anna@email.com', '123'),
  (4, 'Lara', 'lara@email.com', '1234'),
  (5, 'Maria', 'maria@email.com', '12345'),
  (6, 'Rafaela', 'rafaela@email.com', '12346');


INSERT INTO produtos (
  id, codigo, nome, fabricante, preco, estoque_atual, estoque_minimo, ativo, categoria, material_fabricacao
) VALUES 
  (1, 'p001', 'Chapa de Aço 3mm', 'Gerdau', 250.00, 15, 20, 1, 'Chapas', 'Aço Carbono'),
  (2, 'p002', 'Parafuso Sextavado M10', 'Ciser', 1.50, 450, 100, 1, 'Fixadores', 'Aço Inox'),
  (3, 'p003', 'Tinta Epóxi Cinza', 'WEG', 180.00, 8, 10, 1, 'Tintas', 'Resina Epóxi');


INSERT INTO movimentacoes (tipo, date, quantidade, saldo_anterior, usuarios_id, produtos_id) VALUES 
  (1, STR_TO_DATE('11/09/2026', '%d/%m/%Y'), 5, 2, 1, 3),
  (2, STR_TO_DATE('11/09/2026', '%d/%m/%Y'), 10, 20, 2, 2),
  (2, STR_TO_DATE('10/09/2026', '%d/%m/%Y'), 15, 50, 3, 1);


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;