<?php
session_start(); unset($_SESSION['nova_admin']); session_regenerate_id(true); header('Content-Type: application/json; charset=utf-8'); echo '{"ok":true}';
