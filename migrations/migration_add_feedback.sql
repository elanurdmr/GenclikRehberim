-- ============================================================
-- Migration: feedback tablosu
-- Öğrencilerin geri bildirim göndermesini sağlar.
-- ============================================================

CREATE TABLE IF NOT EXISTS feedback (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id     INT NOT NULL,
    category    ENUM('konu','platform','oyun','diger') NOT NULL DEFAULT 'diger',
    message     TEXT NOT NULL,
    is_read     TINYINT(1) NOT NULL DEFAULT 0,
    created_at  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_feedback_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
