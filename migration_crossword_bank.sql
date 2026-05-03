-- Soru bankası + kelime tamamlama kayıtları (kare bulmaca)
USE db_genclik_rehberim;

CREATE TABLE IF NOT EXISTS crossword_bank (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    clue        TEXT        NOT NULL,
    answer      VARCHAR(64) NOT NULL,
    sort_order  INT         NOT NULL DEFAULT 0,
    active      TINYINT(1)  NOT NULL DEFAULT 1,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;

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

-- Başlangıç soru bankası (akran zorbalığı; cevaplar tek kelime / bitişik)
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
