CREATE DATABASE achados_perdidos
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE achados_perdidos;

-- Tipos de usuário (Administrador, Moderador, Operador)
CREATE TABLE tipos_usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(50) NOT NULL UNIQUE
);

-- Usuários do sistema
CREATE TABLE usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(150) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  senha_hash VARCHAR(255) NOT NULL,
  tipo_usuario_id INT NOT NULL,
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (tipo_usuario_id) REFERENCES tipos_usuarios(id)
);

-- Andares do prédio
CREATE TABLE andares (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(100) NOT NULL
);

-- Salas dentro dos andares
CREATE TABLE salas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  andar_id INT NOT NULL,
  nome VARCHAR(100) NOT NULL,
  FOREIGN KEY (andar_id) REFERENCES andares(id)
);

-- Categorias dos objetos (documentos, eletrônicos etc.)
CREATE TABLE categorias (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(100) NOT NULL
);

-- Postagens de achados e perdidos
CREATE TABLE postagens (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NOT NULL,    -- operador que criou
  categoria_id INT NOT NULL,
  tipo ENUM('achado','perdido') NOT NULL,
  titulo VARCHAR(150) NOT NULL,
  descricao TEXT,
  andar_id INT,
  sala_id INT,
  status ENUM('pendente','aprovado','rejeitado','resolvido') DEFAULT 'pendente',
  criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  resolvido_em TIMESTAMP NULL,
  
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
  FOREIGN KEY (categoria_id) REFERENCES categorias(id),
  FOREIGN KEY (andar_id) REFERENCES andares(id),
  FOREIGN KEY (sala_id) REFERENCES salas(id)
);