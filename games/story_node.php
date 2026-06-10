<?php
/**
 * story_node.php — Benim Hikayem Karar Ağacı AJAX Endpoint'i
 * Gençlik Rehberim | Akran Zorbalığı Farkındalık Projesi
 *
 * Kabul eder: POST JSON
 *   - { "scenario": 1|2 }         → Senaryonun başlangıç düğümünü döndürür
 *   - { "choice_id": N }          → Seçime göre sonraki düğümü döndürür
 *
 * Döndürür: JSON { success, node: {id,text,type,feedback,bonus_points}, choices: [...], points_earned }
 */

require_once '../includes/auth.php';
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Sadece POST istekleri kabul edilir.']);
    exit;
}

header('Content-Type: application/json; charset=utf-8');

if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'login_required' => true, 'message' => 'Giriş yapmanız gerekiyor.']);
    exit;
}

// Rate-limiting: 1 saniye aralık yeterli
$rateKey = 'last_story_node_req';
if (isset($_SESSION[$rateKey]) && time() - $_SESSION[$rateKey] < 1) {
    http_response_code(429);
    echo json_encode(['success' => false, 'message' => 'Çok hızlı istek.']);
    exit;
}
$_SESSION[$rateKey] = time();

$body = file_get_contents('php://input');
$data = json_decode($body, true);

if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Geçersiz JSON.']);
    exit;
}

$db = getDB();

try {
    if (isset($data['scenario'])) {
        // Senaryo başlangıç düğümünü getir
        $scenario = (int)$data['scenario'];
        if ($scenario < 1 || $scenario > 2) {
            echo json_encode(['success' => false, 'message' => 'Geçersiz senaryo.']);
            exit;
        }
        $stmt = $db->prepare(
            "SELECT id, text, type, feedback, bonus_points FROM story_nodes
             WHERE scenario = ? AND type = 'start' LIMIT 1"
        );
        $stmt->execute([$scenario]);
        $node = $stmt->fetch();
        if (!$node) {
            echo json_encode(['success' => false, 'message' => 'Senaryo düğümü bulunamadı.']);
            exit;
        }
        $choices     = fetchChoices($db, (int)$node['id']);
        $pointsEarned = 0;

    } elseif (isset($data['choice_id'])) {
        // Seçime göre sonraki düğümü getir
        $choiceId = (int)$data['choice_id'];
        if ($choiceId < 1) {
            echo json_encode(['success' => false, 'message' => 'Geçersiz seçim ID.']);
            exit;
        }
        $stmt = $db->prepare(
            'SELECT next_node_id, points FROM story_choices WHERE id = ? LIMIT 1'
        );
        $stmt->execute([$choiceId]);
        $choice = $stmt->fetch();
        if (!$choice) {
            echo json_encode(['success' => false, 'message' => 'Seçim bulunamadı.']);
            exit;
        }
        $nextNodeId   = (int)$choice['next_node_id'];
        $pointsEarned = (int)$choice['points'];

        $stmt = $db->prepare(
            'SELECT id, text, type, feedback, bonus_points FROM story_nodes WHERE id = ? LIMIT 1'
        );
        $stmt->execute([$nextNodeId]);
        $node = $stmt->fetch();
        if (!$node) {
            echo json_encode(['success' => false, 'message' => 'Sonraki düğüm bulunamadı.']);
            exit;
        }
        $choices = ($node['type'] !== 'end') ? fetchChoices($db, $nextNodeId) : [];

    } else {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'scenario veya choice_id gerekli.']);
        exit;
    }

    echo json_encode([
        'success'       => true,
        'node'          => [
            'id'          => (int)$node['id'],
            'text'        => $node['text'],
            'type'        => $node['type'],
            'feedback'    => $node['feedback'],
            'bonus_points' => (int)($node['bonus_points'] ?? 0),
        ],
        'choices'       => $choices,
        'points_earned' => $pointsEarned ?? 0,
    ], JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    error_log('story_node.php hatası: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Sunucu hatası.']);
}

/**
 * Verilen düğüm için seçenekleri döndürür.
 * Güvenlik: choice points burada açıkça dönüyor; bu tek-oyunculu eğitsel oyun
 * olduğu için client-side görünümü sorun yaratmaz.
 */
function fetchChoices(PDO $db, int $nodeId): array
{
    $stmt = $db->prepare(
        'SELECT id, choice_text, points FROM story_choices WHERE node_id = ? ORDER BY id ASC'
    );
    $stmt->execute([$nodeId]);
    return $stmt->fetchAll();
}
