<?php
/**
 * db.php — PDO Veritabanı Bağlantısı
 * Gençlik Rehberim | Akran Zorbalığı Farkındalık Projesi
 */

require_once __DIR__ . '/env.php';

// Bağlantı parametreleri — .env dosyasından okunur.
// .env yoksa standart XAMPP varsayılanları kullanılır (proje çalışmaya devam eder).
define('DB_HOST',    env('DB_HOST',    'localhost'));
define('DB_USER',    env('DB_USER',    'root'));
define('DB_PASS',    env('DB_PASS',    ''));
define('DB_NAME',    env('DB_NAME',    'db_genclik_rehberim'));
define('DB_CHARSET', env('DB_CHARSET', 'utf8mb4'));

/**
 * PDO bağlantısını oluşturur ve döndürür.
 * Hata durumunda uygulamayı sonlandırır.
 */
function getDB(): PDO {
    static $conn = null; // Bağlantıyı tek seferlik oluştur (Singleton)

    if ($conn === null) {
        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=%s',
            DB_HOST, DB_NAME, DB_CHARSET
        );
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Hataları istisna olarak fırlat
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Sonuçları ilişkisel dizi olarak getir
            PDO::ATTR_EMULATE_PREPARES   => false,                  // Gerçek prepared statement kullan
        ];

        try {
            $conn = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // Üretimde hata mesajını gösterme; loglama yap
            error_log('DB Bağlantı Hatası: ' . $e->getMessage());
            die(json_encode(['error' => 'Veritabanı bağlantısı kurulamadı.']));
        }
    }

    return $conn;
}
