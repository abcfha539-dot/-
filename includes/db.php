<?php
declare(strict_types=1);
function nova_db(): PDO {
    static $pdo = null;
    if ($pdo instanceof PDO) return $pdo;
    $cfg = require __DIR__ . '/database-config.php';
    $dsn = 'mysql:host=' . $cfg['host'] . ';dbname=' . $cfg['database'] . ';charset=' . ($cfg['charset'] ?? 'utf8mb4');
    $pdo = new PDO($dsn, $cfg['username'], $cfg['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
    return $pdo;
}
function nova_json(array $data, int $status=200): never {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store, no-cache, must-revalidate');
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}
function nova_require_admin(): array {
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    $admin = $_SESSION['nova_admin'] ?? null;
    if (!$admin || empty($admin['id'])) nova_json(['ok'=>false,'error'=>'يلزم تسجيل دخول الإدارة.'], 401);
    return $admin;
}
function nova_body(): array {
    $raw = file_get_contents('php://input');
    $data = json_decode($raw ?: '{}', true);
    return is_array($data) ? $data : [];
}
