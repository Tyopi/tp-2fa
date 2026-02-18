<?php

session_start();

 

/*  CONNEXION BDD
 
$dbPath = 'tp-2fa.db';
$db = new SQLite3($dbPath, SQLITE3_OPEN_READWRITE);

*/


$password = "Vivelephp!2026";

$hasher = password_hash($password, PASSWORD_BCRYPT);

echo $hasher;

/*  CRECUPERATION DU PASSWORD 
 
$result = $db->query('SELECT * FROM users');

*/

$result = [
    'id' => 1,
    'email' => 'user@test.fr',
    'password' => 'Vivelephp!2026',
    'secret_2fa' => '',
    'twofa_enabled' => 0,
];
$_SESSION['user'] = $result;

echo "</br>";
echo $result['password'];
echo "</br>";

$hasher = password_hash($result['password'], PASSWORD_BCRYPT);
echo $hasher;

/*  changement en bdd DU PASSWORD 
 
$result = $db->query('UPDATE users SET password = $hasher WHERE email = $result['email'];');

*/

$_SESSION['user']['password'] = $hasher;
$_SESSION['BDD'] = $_SESSION['user'];

?>

















