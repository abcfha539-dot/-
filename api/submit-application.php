<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') nova_json(['ok'=>false,'error'=>'طريقة الطلب غير مسموحة.'],405);
$d=nova_body();
$slug=trim((string)($d['type']??'')); $player=trim((string)($d['playerName']??'')); $discord=trim((string)($d['discordName']??''));
$age=(int)($d['age']??0); $experience=trim((string)($d['experience']??'')); $reason=trim((string)($d['reason']??''));
$types=require __DIR__ . '/../includes/application-types.php';
$title=$types[$slug]??null;
if (!$title || $player==='' || $discord==='' || $age<13 || $age>99 || $experience==='' || $reason==='') nova_json(['ok'=>false,'error'=>'راجع البيانات المطلوبة وحاول مرة أخرى.'],422);
try {
 $pdo=nova_db(); $st=$pdo->prepare('SELECT is_open FROM application_settings WHERE slug=?'); $st->execute([$slug]);
 if (!(bool)$st->fetchColumn()) nova_json(['ok'=>false,'error'=>'هذا التقديم مغلق حاليًا.'],403);
 $publicId='NOVA-'.strtoupper(bin2hex(random_bytes(5)));
 $discordId=$_SESSION['discord_user']['id']??null;
 $ins=$pdo->prepare('INSERT INTO applications (public_id,type_slug,type_title,player_name,discord_name,discord_id,age,experience,reason) VALUES (?,?,?,?,?,?,?,?,?)');
 $ins->execute([$publicId,$slug,$title,$player,$discord,$discordId,$age,$experience,$reason]);
 nova_json(['ok'=>true,'id'=>$publicId]);
} catch (Throwable $e) { nova_json(['ok'=>false,'error'=>'تعذر حفظ التقديم. تأكد من إعداد قاعدة البيانات.'],500); }
