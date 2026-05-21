-- NOT: Sıfırdan kurulum için schema.sql kullanın; bu dosya yalnızca eski DB'leri günceller.
-- ENUM, tüm oyun türlerini içerir; migration sırası artık önemli değildir.
-- Bu dosyadan ÖNCE migration_max_score.sql çalıştırılmış olmalıdır.
USE db_genclik_rehberim;

ALTER TABLE activities
  MODIFY type ENUM('bulmaca','eslestirme','kategori','wordle','cengel','bosluk') NOT NULL;

INSERT INTO activities (name, description, type, max_score)
VALUES ('Çengel Bulmaca', 'Kesişimli kare bulmaca ve gizli kelime', 'cengel', 100);
