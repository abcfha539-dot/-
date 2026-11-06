<?php
require_once __DIR__ . '/../includes/db.php';
session_start(); $a=$_SESSION['nova_admin']??null; nova_json(['ok'=>true,'user'=>$a?['username'=>$a['username'],'displayName'=>$a['displayName']]:null]);
