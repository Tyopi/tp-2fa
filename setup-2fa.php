<?php

ini_set('session.cache_limiter', 'nocache');
session_cache_limiter('nocache');

session_start();
date_default_timezone_set('Europe/Paris');

if(is_file(__DIR__ . '/phpqrcode/qrlib.php')) require_once __DIR__ . '/phpqrcode/qrlib.php';
else "qrlib absent";

if(is_file(__DIR__ . '/TwoFactorAuthLight.php')) require_once __DIR__ . '/TwoFactorAuthLight.php';
else "qrlib absent";



if (!isset($_SESSION['user'])){

    header("Location: login.php?echec=1");
    exit;
}

$tfa = new TwoFactorAuthLight();
// IMPORTANT : ne générer le secret qu'une seule fois tant qu'on n'a pas validé
if (empty($_SESSION['tfa_secret_temp'])) {
    $_SESSION['tfa_secret_temp'] = $tfa->createSecret();
}
$secret = $_SESSION['tfa_secret_temp'];
$email = $_SESSION['user']['email'];
$issuer = 'MonApp';
// Avec ta classe actuelle, getQRCodeUrl($name, $secret) met issuer=$name.
// On met donc un label stable.
$label = $issuer . ':' . $email;
$otpauthUrl = $tfa->getQRCodeUrl($label, $secret);
// Génération image QR locale
$qrFile = __DIR__ . '/qrcode.png';
QRcode::png($otpauthUrl, $qrFile);

// Affichage
echo "<h2> Configuration de la 2FA</h2>";
echo "<p>Compte : <strong>" . htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ."</strong></p>";
echo "<p>Clé secrète : <code>" . htmlspecialchars($secret, ENT_QUOTES, 'UTF-8') ."</code></p>";
echo "<p>Longueur : " . strlen($secret) . " caractères</p>";
echo "<p>Valide (A-Z2-7) : " . (preg_match('/^[A-Z2-7]+$/', $secret) ? ' Oui' : ' Non'). "</p>";
echo "<h3> Scanne le QR code</h3>";
echo "<img src='qrcode.png?v=" . time() . "' alt='QR Code 2FA'><br><br>";
echo "<p>Si le QR ne marche pas, saisis la clé manuellement : <code>" .htmlspecialchars($secret, ENT_QUOTES, 'UTF-8') . "</code></p>";
echo "<p>URL OTP (debug) : <code>" . htmlspecialchars($otpauthUrl, ENT_QUOTES, 'UTF-8') ."</code></p>";
echo "<h3> Validation</h3>";
echo "<form method='POST' action='verify-2fa.php' autocomplete='off'>";
echo "<label for='code'>Code à 6 chiffres :</label><br>";
echo "<input type='text' name='code' id='code' inputmode='numeric' pattern='[0-9]{6}'maxlength='6' required><br><br>";
echo "<button type='submit'>Valider la 2FA</button>";
echo "</form>";
echo "<p><a href='setup-2fa.php'> Régénérer l'affichage (garde la même clé)</a></p>";
?>