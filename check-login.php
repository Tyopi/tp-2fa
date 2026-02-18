<?php

session_start();

if ( empty($_SERVER['HTTPS'])) {

    $host = $_SERVER['HTTP_HOST'];
    if ( $host !== 'localhost' ) {
        $httpsUrl = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        header("Location: $httpsUrl");
        exit();
    }
}

if (isset($_POST['conexion'])) {
    
    /*          Verification en bdd
    
        $email = $_POST['email'];

        $stmt = $db->prepare('SELECT * FROM users WHERE email = :email');
        $stmt->execute([':email' => $email]); 

        $user = $stmt->fetch(PDO::FETCH_ASSOC); 
        if ($user) {
            if (password_verify($_POST["pass"], $user['password']){
            
            }
        } else {

        }

     

    */ 
    $email = $_POST['email'];
    $pass = $_POST['pass'];

    $email_bdd = $_SESSION['BDD']["email"];
    $pass_bdd = $_SESSION['BDD']["password"];
    $user_id_bdd = $_SESSION['BDD']["id"];
    $tfa_bdd = $_SESSION['BDD']["secret_2fa"];


    if (isset($email_bdd) && $email_bdd == $email){
        
            if (password_verify($pass,$pass_bdd)){

                $_SESSION['user'] = $_SESSION['user'] = [
                    'id' => (int)$user_id_bdd,
                    'email' => (string)$email_bdd,
                    'tfa_secret' => (string)($tfa_bdd ?? '')
                ];
                unset($_SESSION['tfa_secret_temp']);

                if ($_SESSION['user']["tfa_secret"] == ''){
                    header("Location: check-2fa.php");
                    exit;
                }else{
                    header("Location: setup-2fa.php");
                    exit;
                }

                
            }

    }
    header("Location: login.php?echec=2");
    exit;


}
header("Location: login.php");
exit;
?>