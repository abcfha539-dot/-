<?php
declare(strict_types=1);
session_start();
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
$user = $_SESSION['discord_user'] ?? null;
if (!$user || empty($user['id'])) {
    http_response_code(401);
    echo json_encode(['loggedIn' => false], JSON_UNESCAPED_UNICODE);
    exit;
}
$name = $user['global_name'] ?: $user['username'];
$avatarUrl = '';
if (!empty($user['avatar'])) {
    $avatarUrl = 'https://cdn.discordapp.com/avatars/' . rawurlencode((string)$user['id']) . '/' . rawurlencode((string)$user['avatar']) . '.png?size=128';
}
echo json_encode([
    'loggedIn' => true,
    'id' => (string)$user['id'],
    'name' => (string)$name,
    'username' => (string)$user['username'],
    'avatar' => $avatarUrl,
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
