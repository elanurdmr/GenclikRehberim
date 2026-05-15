-- Migration: activities tablosuna max_score sütunu ekle
-- Çalıştırmadan önce veritabanını yedekleyin.

ALTER TABLE activities ADD COLUMN max_score INT NOT NULL DEFAULT 100;

UPDATE activities SET max_score = 100 WHERE type = 'bulmaca';
UPDATE activities SET max_score = 140 WHERE type = 'eslestirme';
UPDATE activities SET max_score = 170 WHERE type = 'kategori';
UPDATE activities SET max_score = 100 WHERE type = 'wordle';
