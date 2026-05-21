-- ============================================================
-- Gençlik Rehberim | Veritabanı Şeması (TEK YETKİLİ KAYNAK)
-- Akran Zorbalığı Farkındalık Projesi
-- ------------------------------------------------------------
-- Sıfırdan kurulum için yalnızca bu dosyayı çalıştırmanız yeterlidir.
-- migrations/ klasöründeki dosyalar yalnızca ESKİ kurulumları
-- güncellemek içindir; yeni kurulumda gerekmezler.
-- ============================================================

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

-- ============================================================
-- Tablo 2: activities — Oyun/Etkinlik tanımları
-- type ENUM TÜM oyun türlerini içerir (migration sırası önemsiz).
-- max_score sütunu save_score.php tarafından doğrulama için kullanılır.
-- ============================================================
CREATE TABLE IF NOT EXISTS activities (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100) NOT NULL,
    description TEXT,
    type        ENUM('bulmaca','eslestirme','kategori','wordle','cengel','bosluk') NOT NULL,
    max_score   INT      NOT NULL DEFAULT 100,
    is_active   TINYINT(1) NOT NULL DEFAULT 1   -- 0 = iç puanlama tipi, kullanıcıya gösterilmez
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

-- ============================================================
-- Tablo 4: crossword_bank — Çengel bulmaca soru bankası
-- ============================================================
CREATE TABLE IF NOT EXISTS crossword_bank (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    clue        TEXT        NOT NULL,
    answer      VARCHAR(64) NOT NULL,
    sort_order  INT         NOT NULL DEFAULT 0,
    active      TINYINT(1)  NOT NULL DEFAULT 1,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

-- ============================================================
-- Tablo 5: crossword_word_scores — Çengel bulmaca kelime başına puan
-- ============================================================
CREATE TABLE IF NOT EXISTS crossword_word_scores (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    user_id        INT         NOT NULL,
    puzzle_seed    VARCHAR(32) NOT NULL,
    direction      ENUM('across','down') NOT NULL,
    clue_number    INT         NOT NULL,
    points_awarded INT         NOT NULL,
    awarded_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_user_puzzle_clue (user_id, puzzle_seed, direction, clue_number),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

-- ============================================================
-- Tablo 6: user_badges — Kazanılan rozetler
-- ============================================================
CREATE TABLE IF NOT EXISTS user_badges (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    user_id    INT          NOT NULL,
    badge_name VARCHAR(100) NOT NULL,
    earned_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_user_badge (user_id, badge_name),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

-- ============================================================
-- Başlangıç Verileri
-- ============================================================

-- Varsayılan admin kullanıcısı (şifre: admin123)
INSERT INTO users (username, email, password, role) VALUES
('admin', 'admin@genclikrehberim.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin');

-- Etkinlik kayıtları — id sırası oyun JS yapılandırmasıyla uyumludur.
INSERT INTO activities (id, name, description, type, max_score, is_active) VALUES
(1, 'Zorba Davranışa Karşı Koyma Bulmacası', 'Zorbalıkla başa çıkma yöntemlerini bulmaca ile öğren', 'bulmaca',    100, 1),
(2, 'Doğru mu, Yanlış mı? Eşleştirme',        'Doğru ve yanlış davranışları eşleştir',                'eslestirme', 140, 1),
(3, 'Zorbalık mı, Değil mi? Kategori',         'Eslestirme Bölüm 2 — iç puanlama tipi',               'kategori',   170, 0),
(4, 'Wordle — 5 Harfli Kelime',                'Altı denemede kelimeyi bul, Türkçe harf desteğiyle',  'wordle',     100, 1),
(5, 'Çengel Bulmaca',                          'Kesişimli kare bulmaca ve gizli kelime',               'cengel',     100, 1),
(6, 'Boşluk Doldurma',                         'Eşleştirme oyunu Bölüm 2 — iç puanlama tipi',         'bosluk',      80, 0);

-- Çengel bulmaca soru bankası (akran zorbalığı; cevaplar tek kelime / bitişik)
INSERT INTO crossword_bank (clue, answer, sort_order) VALUES
('Zorbalık olunca ne istemeliyiz?', 'YARDIM', 1),
('Yaşadığımız olayı kime anlatırız?', 'YETİŞKİN', 2),
('Yardım bulamazsak ne yapmaya devam etmeliyiz?', 'ARAMAK', 3),
('Zorba karşısında nasıl durmalıyız?', 'DİK', 4),
('Korkunca sakinleşmek için ne alıp veririz?', 'NEFES', 5),
('"Başarabilirim" gibi ifadeler (bitişik yazın).', 'OLUMLUSÖZ', 6),
('Fiziksel zorbalıkta gideceğimiz güvenli yer (bitişik).', 'GÜVENLİYER', 7),
('Daha güvende olmak için tercih edilen ortam.', 'KALABALIK', 8),
('Korkuyu göstermemek zorbayı ne yapar?', 'UZAKLAŞTIRIR', 9),
('Sözel zorbalıkta bazen yapmamız gereken hareket.', 'UZAKLAŞIRIZ', 10),
('Zorbalığı yetişkine iletmek.', 'BİLDİRME', 11),
('Kendini karşıdakinin yerine koymak.', 'EMPATİ', 12),
('Arkadaşına destek olma tutumu.', 'DAYANIŞMA', 13),
('Zorbalığa karşı sınıfta oluşan olumlu hava.', 'SAYGI', 14),
('Güvendiğimiz bir yetişkin.', 'ÖĞRETMEN', 15);
