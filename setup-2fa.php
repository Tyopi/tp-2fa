<?php

/*ini_set('session.cache_limiter', 'nocache');
session_cache_limiter('nocache');

session_start();
date_default_timezone_set('Europe/Paris');

require_once __DIR__ . '/phpqrcode/qrlib.php';

require_once __DIR__ . '/TwoFactorAuthLight.php';

if (isset($_SESSION['user'])){*/

    header("Location: login.php?echec=1");
    exit;


?>