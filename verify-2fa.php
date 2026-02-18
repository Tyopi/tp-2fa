<?php
ini_set('session.cache_limiter', 'nocache');
session_cache_limiter('nocache');
session_start();
date_default_timezone_set('Europe/Paris');
require_once __DIR__ . '/TwoFactorAuthLight.php';
function h($v) {
return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}
// Vérifier session
if (!isset($_SESSION['user']) || !isset($_SESSION['user']['id']) || !
isset($_SESSION['tfa_secret_temp'])) {
die("<p> Session invalide. <a href='setup-2fa.php'>Recommencer laconfiguration</a>.</p>");
}
$userId = (int)$_SESSION['user']['id'];
$secret = $_SESSION['tfa_secret_temp'];
$userCode = preg_replace('/\D/', '', $_POST['code'] ?? '');
if (strlen($userCode) !== 6) {
die("<p> Code invalide (6 chiffres attendus).</p><p><ahref='setup-2fa.php'>Retour</a></p>");
}
// Connexion SQLite (manquait dans ton fichier)
$dbPath = __DIR__ . '/tp-2fa.db';
if (!file_exists($dbPath)) {
die("<p> Base introuvable : " . h($dbPath) . "</p>");
}
$db = new SQLite3($dbPath);
// Debug horaire
echo "Heure PHP : " . h(date('Y-m-d H:i:s')) . "<br>";
echo "Fuseau horaire PHP : " . h(date_default_timezone_get()) . "<br>";
$tzLine = @exec('timedatectl | grep "Time zone"');
if (!empty($tzLine)) {
echo "Fuseau horaire système : " . h($tzLine) . "<br>";
}
$tfa = new TwoFactorAuthLight();
$currentTimeSlice = floor(time() / 30);
echo "<h2> Débogage avancé TOTP</h2>";
echo "<p>Code saisi : <code>" . h($userCode) . "</code></p>";
echo "<p>Clé secrète : <code>" . h($secret) . "</code></p>";
echo "<p>TimeSlice courant : <code>" . h($currentTimeSlice) . "</code></p>";
echo "<h3> Codes TOTP autour de maintenant</h3>";
echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
echo "<tr><th>Décalage</th><th>TimeSlice</th><th>Début</th><th>Fin</th><th>Code</
th><th>Match ?</th></tr>";
for ($i = -3; $i <= 3; $i++) {
$timeSlice = $currentTimeSlice + $i;
$startTs = $timeSlice * 30;
// IMPORTANT : getCode attend timeSlice, pas timestamp
$expected = $tfa->getCode($secret, $timeSlice);
$match = hash_equals($expected, $userCode) ? " Oui" : " Non";
echo "<tr>";
echo "<td>" . h($i) . "</td>";
echo "<td>" . h($timeSlice) . "</td>";
echo "<td>" . h(date('Y-m-d H:i:s', $startTs)) . "</td>";
echo "<td>" . h(date('Y-m-d H:i:s', $startTs + 30)) . "</td>";
echo "<td>" . h($expected) . "</td>";
echo "<td>" . $match . "</td>";
echo "</tr>";
}
echo "</table>";
// Une seule vérification, fenêtre ±1 pas (30s)
$isValid = $tfa->verifyCode($secret, $userCode, 1);
if (!$isValid) {
echo "<h1> Code invalide</h1>";
echo "<p>Vérifie l'heure auto du téléphone et re-scanner le QR si besoin.</p>";
exit;
}
// Sauvegarde en base
// Colonnes attendues : tfa_secret, tfa_backup_codes (optionnel)
$backupCodes = [];
for ($i = 0; $i < 5; $i++) {
$backupCodes[] = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);
}
$backupCodesStr = implode(',', $backupCodes);
$hashedBackupCodes = password_hash($backupCodesStr, PASSWORD_DEFAULT);
/*$stmt = $db->prepare("UPDATE users SET tfa_secret = :secret, tfa_backup_codes
= :backupCodes WHERE id = :id");
$smt->bindValue(':secret', $secret, SQLITE3_TEXT);
$stmtt->bindValue(':backupCodes', $hashedBackupCodes, SQLITE3_TEXT);
$stmt->bindValue(':id', $userId, SQLITE3_INTEGER);*/
$res = $_SESSION['BDD'];
if (!$res) {
echo "<h1> Erreur SQL</h1>";
echo "<pre>" . h($db->lastErrorMsg()) . "</pre>";
exit;
}
// Session
$_SESSION['user']['tfa_secret'] = $secret;
unset($_SESSION['tfa_secret_temp']); // setup terminé
echo "<h1> 2FA activée avec succès !</h1>";
echo "<p>Codes de secours (à copier maintenant) :</p>";
echo "<pre>" . h($backupCodesStr) . "</pre>";
echo "<p><a href='login.php'>Retour login</a></p>";
?>