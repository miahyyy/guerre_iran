-- Base de données pour le site d'information sur la guerre en Iran
DROP DATABASE IF EXISTS guerre_iran;
CREATE DATABASE IF NOT EXISTS guerre_iran;
USE guerre_iran;

-- Table des catégories
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table des articles
CREATE TABLE articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    contenu LONGTEXT NOT NULL,
    image VARCHAR(255),
    image_alt VARCHAR(255),
    categorie_id INT,
    meta_title VARCHAR(255),
    meta_description VARCHAR(160),
    meta_keywords VARCHAR(255),
    views INT DEFAULT 0,
    status ENUM('draft', 'published') DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (categorie_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Table des utilisateurs (BackOffice)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    role ENUM('admin', 'editor') DEFAULT 'editor',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insertion du compte admin par défaut
INSERT INTO users (username, password, email, role) 
VALUES ('admin', '$2y$10$g0T9W6WpVx6xKk0DADvpZecyuMpDRjc2DPwL7//3IAZfVjRJ0PgfK', 'admin@exemple.com', 'admin');