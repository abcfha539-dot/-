<?php
require_once __DIR__ . '/../includes/db.php';
try {
  $rows = nova_db()->query('SELECT slug, is_open FROM application_settings')->fetchAll();
  $settings=[]; foreach ($rows as $r) $settings[$r['slug']] = ['open'=>(bool)$r['is_open']];
  nova_json(['ok'=>true,'settings'=>$settings]);
} catch (Throwable $e) { nova_json(['ok'=>false,'error'=>'تعذر الاتصال بقاعدة البيانات. تأكد من إعدادها.'],500); }
