<?php
session_start(); require_once __DIR__ . '/../includes/db.php';
if ($_SERVER['REQUEST_METHOD']!=='POST') nova_json(['ok'=>false],405);
$d=nova_body(); $u=trim((string)($d['username']??'')); $p=(string)($d['password']??'');
try { $q=nova_db()->prepare('SELECT id,username,password_hash,display_name FROM admins WHERE username=? LIMIT 1'); $q->execute([$u]); $a=$q->fetch();
 if (!$a || !password_verify($p,$a['password_hash'])) nova_json(['ok'=>false,'error'=>'اسم المستخدم أو كلمة المرور غير صحيحة.'],401);
 session_regenerate_id(true); $_SESSION['nova_admin']=['id'=>(int)$a['id'],'username'=>$a['username'],'displayName'=>$a['display_name']];
 nova_json(['ok'=>true,'user'=>['username'=>$a['username'],'displayName'=>$a['display_name']]]);
} catch(Throwable $e){nova_json(['ok'=>false,'error'=>'تعذر الاتصال بقاعدة البيانات.'],500);}
