<?php
require_once __DIR__ . '/includes/db.php';
try {$pdo=nova_db();$count=(int)$pdo->query('SELECT COUNT(*) FROM admins')->fetchColumn();} catch(Throwable $e){http_response_code(500);die('إعداد قاعدة البيانات أولًا ثم افتح هذه الصفحة.');}
$msg='';
if($_SERVER['REQUEST_METHOD']==='POST' && $count===0){
 $names=['owner'=>trim($_POST['owner_name']??'Owner'),'coowner'=>trim($_POST['coowner_name']??'Co-Owner')];
 $passwords=['owner'=>$_POST['owner_pass']??'','coowner'=>$_POST['coowner_pass']??''];
 if(strlen($passwords['owner'])<10||strlen($passwords['coowner'])<10){$msg='اجعل كل كلمة مرور 10 أحرف على الأقل.';}
 else { $s=$pdo->prepare('INSERT INTO admins(username,password_hash,display_name) VALUES(?,?,?)'); foreach($names as $u=>$n)$s->execute([$u,password_hash($passwords[$u],PASSWORD_DEFAULT),$n]); $count=2;$msg='تم إنشاء حسابَي الإدارة. احذف ملف setup-admins.php الآن.'; }
}
?><!doctype html><html lang="ar" dir="rtl"><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>إعداد ملاك NovaRolePlay</title><body style="background:#101827;color:#fff;font:16px Arial;max-width:560px;margin:50px auto;padding:20px"><h2>إعداد حسابَي الأونر</h2><p><?=htmlspecialchars($msg)?></p><?php if($count===0):?><form method="post"><label>اسم عرض الأونر الأول</label><input name="owner_name" value="name" required style="display:block;width:100%;padding:12px;margin:8px 0 16px"><label>كلمة مرور الأونر الأول</label><input type="password" name="owner_pass" required minlength="10" style="display:block;width:100%;padding:12px;margin:8px 0 16px"><label>اسم عرض الأونر الثاني</label><input name="coowner_name" value="Co-Owner" required style="display:block;width:100%;padding:12px;margin:8px 0 16px"><label>كلمة مرور الأونر الثاني</label><input type="password" name="coowner_pass" required minlength="10" style="display:block;width:100%;padding:12px;margin:8px 0 16px"><button style="padding:12px 22px">إنشاء الحسابين</button></form><?php else:?><p>تم إعداد حسابات الإدارة أو سبق إعدادها. احذف هذا الملف الآن.</p><?php endif;?></body></html>
