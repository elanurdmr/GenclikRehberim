USE db_genclik_rehberim;

ALTER TABLE activities
  MODIFY type ENUM('bulmaca','eslestirme','kategori','wordle','cengel') NOT NULL;

INSERT INTO activities (name, description, type, max_score)
VALUES ('Çengel Bulmaca', 'Kesişimli kare bulmaca ve gizli kelime', 'cengel', 100);
