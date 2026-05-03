-- ============================================================
-- Gençlik Rehberim | Veritabanı Şeması
-- Akran Zorbalığı Farkındalık Projesi
-- ============================================================

-- Veritabanı oluştur
CREATE DATABASE IF NOT EXISTS db_genclik_rehberim
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_turkish_ci;

USE db_genclik_rehberim;

-- ============================================================
-- Tablo 1: users — Admin ve Öğrenci kullanıcıları
-- ============================================================
CREATE TABLE IF NOT EXISTS users (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    username    VARCHAR(50)  NOT NULL UNIQUE,
    email       VARCHAR(100) NOT NULL UNIQUE,
    password    VARCHAR(255) NOT NULL,                          -- bcrypt hash
    role        ENUM('admin','student') NOT NULL DEFAULT 'student',
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- Tablo 2: activities — Oyun/Etkinlik tanımları
-- ============================================================
CREATE TABLE IF NOT EXISTS activities (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100) NOT NULL,
    description TEXT,
    type        ENUM('bulmaca','eslestirme','kategori') NOT NULL
) ENGINE=InnoDB;

-- ============================================================
-- Tablo 3: scores — Kullanıcı puan kayıtları
-- ============================================================
CREATE TABLE IF NOT EXISTS scores (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    user_id     INT NOT NULL,
    activity_id INT NOT NULL,
    score       INT NOT NULL DEFAULT 0,
    max_score   INT NOT NULL DEFAULT 100,
    played_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id)     REFERENCES users(id)      ON DELETE CASCADE,
    FOREIGN KEY (activity_id) REFERENCES activities(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- Başlangıç Verileri
-- ============================================================

-- Varsayılan admin kullanıcısı (şifre: admin123)
INSERT INTO users (username, email, password, role) VALUES
('admin', 'admin@genclikrehberim.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Etkinlik kayıtları
INSERT INTO activities (name, description, type) VALUES
('Zorba Davranışa Karşı Koyma Bulmacası', 'Zorbalıkla başa çıkma yöntemlerini bulmaca ile öğren', 'bulmaca'),
('Doğru mu, Yanlış mı? Eşleştirme',       'Doğru ve yanlış davranışları eşleştir',               'eslestirme'),
('Zorbalık mı, Değil mi? Kategori',        'Davranışları doğru kutuya yerleştir',                 'kategori');
