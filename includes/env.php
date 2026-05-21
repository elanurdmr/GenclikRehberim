<?php
/**
 * env.php — Basit .env yükleyici (harici bağımlılık yok)
 * Gençlik Rehberim | Akran Zorbalığı Farkındalık Projesi
 *
 * Composer / vlucas-dotenv kurmadan, proje kökündeki .env dosyasını okur.
 * .env dosyası .gitignore ile dışlanır; sürüm kontrolüne girmez.
 */
declare(strict_types=1);

/**
 * Proje kökündeki .env dosyasını okuyup $_ENV'e aktarır.
 * Yalnızca bir kez çalışır; dosya yoksa sessizce geçer.
 */
function env_load(?string $path = null): void
{
    static $loaded = false;
    if ($loaded) {
        return;
    }
    $loaded = true;

    $path = $path ?? dirname(__DIR__) . '/.env';
    if (!is_file($path) || !is_readable($path)) {
        return; // .env yoksa env() varsayılanları devreye girer
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        return;
    }

    foreach ($lines as $line) {
        $line = trim($line);
        // Boş satır veya yorum (#) atla
        if ($line === '' || $line[0] === '#') {
            continue;
        }
        $eq = strpos($line, '=');
        if ($eq === false) {
            continue;
        }
        $key = trim(substr($line, 0, $eq));
        $val = trim(substr($line, $eq + 1));

        // Çevreleyen tırnakları kaldır ("değer" veya 'değer')
        $len = strlen($val);
        if ($len >= 2
            && ($val[0] === '"' || $val[0] === "'")
            && $val[$len - 1] === $val[0]) {
            $val = substr($val, 1, -1);
        }

        if ($key !== '') {
            $_ENV[$key] = $val;
            putenv($key . '=' . $val);
        }
    }
}

/**
 * Bir ortam değişkeni döndürür; tanımsız/boşsa $default döner.
 */
function env(string $key, ?string $default = null): ?string
{
    env_load();

    $val = $_ENV[$key] ?? getenv($key);
    if ($val === false || $val === null || $val === '') {
        return $default;
    }

    return (string)$val;
}
