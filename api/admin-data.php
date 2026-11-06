<?php
require_once __DIR__ . '/../includes/db.php'; $admin=nova_require_admin(); $pdo=nova_db();
if ($_SERVER['REQUEST_METHOD']==='GET') {
 $apps=$pdo->query('SELECT public_id AS id,type_slug AS type,type_title AS typeTitle,player_name AS playerName,discord_name AS discordName,discord_id AS discordId,age,experience,reason,status,created_at AS createdAt FROM applications ORDER BY id DESC')->fetchAll();
 $settings=[]; foreach($pdo->query('SELECT slug,is_open FROM application_settings')->fetchAll() as $r)$settings[$r['slug']]=['open'=>(bool)$r['is_open']];
 $users=$pdo->query('SELECT discord_id AS id,username,global_name AS globalName,avatar_hash AS avatar,email,first_login_at AS firstLoginAt,last_login_at AS lastLoginAt,login_count AS loginCount FROM discord_users ORDER BY last_login_at DESC')->fetchAll();
 nova_json(['ok'=>true,'submissions'=>$apps,'settings'=>$settings,'users'=>$users,'admin'=>['username'=>$admin['username'],'displayName'=>$admin['displayName']]]);
}
if ($_SERVER['REQUEST_METHOD']==='POST') {
 $d=nova_body(); $action=$d['action']??'';
 if($action==='setting') { $slug=(string)($d['slug']??''); $open=!empty($d['open']); $types=require __DIR__.'/../includes/application-types.php'; if(!isset($types[$slug]))nova_json(['ok'=>false,'error'=>'نوع تقديم غير معروف.'],422); $s=$pdo->prepare('INSERT INTO application_settings(slug,is_open) VALUES(?,?) ON DUPLICATE KEY UPDATE is_open=VALUES(is_open)');$s->execute([$slug,$open?1:0]);nova_json(['ok'=>true]); }
 if($action==='all_closed'){$pdo->exec('UPDATE application_settings SET is_open=0');nova_json(['ok'=>true]);}
 if($action==='status'){$id=(string)($d['id']??'');$status=(string)($d['status']??'');if(!in_array($status,['pending','accepted','rejected'],true))nova_json(['ok'=>false],422);$s=$pdo->prepare('UPDATE applications SET status=? WHERE public_id=?');$s->execute([$status,$id]);nova_json(['ok'=>true]);}
 if($action==='delete'){$s=$pdo->prepare('DELETE FROM applications WHERE public_id=?');$s->execute([(string)($d['id']??'')]);nova_json(['ok'=>true]);}
 nova_json(['ok'=>false,'error'=>'إجراء غير معروف.'],400);
}
nova_json(['ok'=>false],405);
