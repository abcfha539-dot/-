<?php
session_start();
unset($_SESSION['discord_user'], $_SESSION['discord_oauth_state']);
session_regenerate_id(true);
header('Location: login.html');
exit;
