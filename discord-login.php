<?php
declare(strict_types=1);
session_start();
$config = require __DIR__ . '/includes/discord-config.php';

if (
    empty($config['client_id']) ||
    $config['client_id'] === 'PUT_DISCORD_CLIENT_ID_HERE' ||
    empty($config['client_secret']) ||
    $config['client_secret'] === 'PUT_DISCORD_CLIENT_SECRET_HERE'
) {
    http_response_code(500);
    exit('إعدادات Discord غير مكتملة. افتح includes/discord-config.php وأضف Client ID و Client Secret.');
}

$state = bin2hex(random_bytes(24));
$_SESSION['discord_oauth_state'] = $state;

$params = [
    'client_id' => $config['client_id'],
    'redirect_uri' => $config['redirect_uri'],
    'response_type' => 'code',
    'scope' => $config['scopes'] ?? 'identify email',
    'state' => $state,
    'prompt' => 'consent',
];

header('Location: https://discord.com/oauth2/authorize?' . http_build_query($params));
exit;
