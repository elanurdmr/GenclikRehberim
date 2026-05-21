-- Mevcut veritabanına Wordle etkinliğini ekler (phpMyAdmin / mysql ile çalıştırın)
USE db_genclik_rehberim;

ALTER TABLE activities
  MODIFY type ENUM('bulmaca','eslestirme','kategori','wordle') NOT NULL;

INSERT INTO activities (name, description, type) VALUES
('Wordle — 5 Harfli Kelime', 'Altı denemede kelimeyi bul, Türkçe harf desteğiyle', 'wordle');
