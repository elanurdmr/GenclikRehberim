-- Boşluk doldurma aktivitesi ekle (GÖREV 8/9)
-- Eşleştirme oyununun yeni Bölüm 2'si (activityId = 6)

USE db_genclik_rehberim;

ALTER TABLE activities
    MODIFY type ENUM('bulmaca','eslestirme','kategori','wordle','cengel','bosluk') NOT NULL;

INSERT INTO activities (name, description, type, max_score)
VALUES ('Boşluk Doldurma', 'Eşleştirme oyunu Bölüm 2 — cümlelerdeki boşlukları doğru kelimelerle tamamla', 'bosluk', 80);
