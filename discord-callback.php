<?php
declare(strict_types=1);
session_start();

function fail(string $message, int $status = 400): void {
    http_response_code($status);
    echo '<!doctype html><html lang="ar" dir="rtl"><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>تعذر تسجيل الدخول</title><body style="background:#101827;color:#fff;font-family:Arial;padding:40px;text-align:center"><h2>تعذر تسجيل الدخول إلى Discord</h2><p>' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</p><a style="color:#38bdf8" href="login.html">العودة</a></body></html>';
    exit;
}

$config = require __DIR__ . '/includes/discord-config.php';

if (isset($_GET['error'])) {
    fail('تم إلغاء تسجيل الدخول أو رفض Discord الطلب.');
}

$code = $_GET['code'] ?? '';
$state = $_GET['state'] ?? '';
$savedState = $_SESSION['discord_oauth_state'] ?? '';

if (!$code || !$state || !$savedState || !hash_equals($savedState, (string)$state)) {
    unset($_SESSION['discord_oauth_state']);
    fail('طلب تسجيل الدخول غير صالح أو انتهت صلاحيته. حاول مرة أخرى.');
}
unset($_SESSION['discord_oauth_state']);

if (!function_exists('curl_init')) {
    fail('إضافة PHP cURL غير مفعلة على الاستضافة.', 500);
}

$tokenCh = curl_init('https://discord.com/api/oauth2/token');
curl_setopt_array($tokenCh, [
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => http_build_query([
        'client_id' => $config['client_id'],
        'client_secret' => $config['client_secret'],
        'grant_type' => 'authorization_code',
        'code' => $code,
        'redirect_uri' => $config['redirect_uri'],
    ]),
    CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 20,
    CURLOPT_SSL_VERIFYPEER => true,
]);
$tokenBody = curl_exec($tokenCh);
$tokenStatus = (int)curl_getinfo($tokenCh, CURLINFO_HTTP_CODE);
$tokenError = curl_error($tokenCh);
curl_close($tokenCh);

if ($tokenBody === false || $tokenStatus < 200 || $tokenStatus >= 300) {
    fail('فشل Discord في إصدار رمز الدخول. راجع Client ID و Client Secret و Redirect URI.' . ($tokenError ? ' تفاصيل الاتصال: ' . $tokenError : ''), 502);
}

$tokenData = json_decode($tokenBody, true);
$accessToken = $tokenData['access_token'] ?? '';
if (!$accessToken) {
    fail('لم يتم استلام Access Token صالح من Discord.', 502);
}

$userCh = curl_init('https://discord.com/api/users/@me');
curl_setopt_array($userCh, [
    CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . $accessToken],
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 20,
    CURLOPT_SSL_VERIFYPEER => true,
]);
$userBody = curl_exec($userCh);
$userStatus = (int)curl_getinfo($userCh, CURLINFO_HTTP_CODE);
curl_close($userCh);

$user = json_decode((string)$userBody, true);
if ($userBody === false || $userStatus !== 200 || empty($user['id'])) {
    fail('تم التحقق من Discord لكن تعذر جلب بيانات الحساب.', 502);
}

session_regenerate_id(true);
$_SESSION['discord_user'] = [
    'id' => (string)$user['id'],
    'username' => (string)($user['username'] ?? ''),
    'global_name' => (string)($user['global_name'] ?? ''),
    'email' => (string)($user['email'] ?? ''),
    'avatar' => (string)($user['avatar'] ?? ''),
    'logged_in_at' => time(),
];

// حفظ حساب Discord في قاعدة MySQL المشتركة حتى يظهر لدى جميع الأونرات.
try {
    require_once __DIR__ . '/includes/db.php';
    $pdo = nova_db();
    $stmt = $pdo->prepare("INSERT INTO discord_users (discord_id, username, global_name, avatar_hash, email, first_login_at, last_login_at, login_count) VALUES (?, ?, ?, ?, ?, NOW(), NOW(), 1) ON DUPLICATE KEY UPDATE username=VALUES(username), global_name=VALUES(global_name), avatar_hash=VALUES(avatar_hash), email=VALUES(email), last_login_at=NOW(), login_count=login_count+1");
    $stmt->execute([(string)$user['id'], (string)($user['username'] ?? ''), (string)($user['global_name'] ?? ''), (string)($user['avatar'] ?? ''), (string)($user['email'] ?? '')]);
} catch (Throwable $e) {
    fail('تم التحقق من حساب Discord، لكن تعذر حفظه في قاعدة البيانات. تأكد من استيراد ملف SQL وضبط بيانات MySQL.', 500);
}

$name = $_SESSION['discord_user']['global_name'] ?: $_SESSION['discord_user']['username'];
$avatar = '';
if (!empty($user['avatar'])) {
    $avatar = 'https://cdn.discordapp.com/avatars/' . rawurlencode((string)$user['id']) . '/' . rawurlencode((string)$user['avatar']) . '.png?size=128';
}
?>
<!doctype html>
<html lang="ar" dir="rtl">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>تم تسجيل الدخول | NovaRolePlay</title>
<link rel="stylesheet" href="assets/plugins/global/plugins.bundle.css"><link rel="stylesheet" href="assets/css/style.bundle.css"><link rel="stylesheet" href="assets/css/custom.css"></head>
<body class="app-blank bg-body h-auto min-h-100">
<main class="container d-flex align-items-center justify-content-center" style="min-height:100vh">
<div class="nova-panel text-center" style="max-width:560px">
<?php if ($avatar): ?><img src="<?= htmlspecialchars($avatar, ENT_QUOTES, 'UTF-8') ?>" alt="Discord avatar" style="width:88px;height:88px;border-radius:50%;margin-bottom:20px"><?php endif; ?>
<h1 class="mb-3">تم تسجيل الدخول بنجاح</h1>
<p class="text-gray-400">أهلًا <?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?> في NovaRolePlay.</p>
<a class="btn btn-danger rounded-pill px-7 py-3" href="index.html">العودة للموقع</a>
<a class="d-block mt-4" href="discord-logout.php">تسجيل الخروج</a>
</div></main></body></html>
