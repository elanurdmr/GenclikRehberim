-- migration_add_badges.sql
-- Rozet sistemi için user_badges tablosunu ekler.
-- Yeni kurulumlar schema.sql'i kullanır; bu dosya yalnızca mevcut DB'leri güncellemek içindir.

CREATE TABLE IF NOT EXISTS user_badges (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    user_id    INT          NOT NULL,
    badge_name VARCHAR(100) NOT NULL,
    earned_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_user_badge (user_id, badge_name),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_turkish_ci;
