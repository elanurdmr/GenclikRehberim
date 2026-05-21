-- ============================================================
-- Migration: activities tablosuna is_active sütunu ekle
-- Çalıştırma: yalnızca bu sütun henüz yoksa gereklidir.
-- ============================================================

ALTER TABLE activities
    ADD COLUMN IF NOT EXISTS is_active TINYINT(1) NOT NULL DEFAULT 1
        COMMENT 'Kullanıcıya görünen bağımsız oyun: 1. İç puanlama tipi: 0.';

-- Dahili tipler (eslestirme oyununun alt bölümleri) pasif işaretle
UPDATE activities SET is_active = 0 WHERE type IN ('kategori', 'bosluk');
