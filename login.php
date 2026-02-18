<?php

session_start();



if (isset($_POST['deconexion'])) {
    unset($_SESSION['user']); 
    header("Location: login.php");
}




?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="css/index.css" rel="stylesheet">
</head>


<body>
  


<?php if (!isset($_SESSION['user'])) {  ?>
  
    <form method="post" action="check-login.php">
        <input type="text" name="email" value="<?php echo htmlspecialchars($_SESSION['BDD']["email"]); ?>"> <br>
        <input type="password" name = "pass" value = "Vivelephp!2026"> </br>
        <input type="submit"  name="conexion" value="connexion"> </br>
    </form>

    <?php if (isset($_GET['echec'])) { echo"mot de passe ou email incorect";}?>

<?php }else{ ?>

    <h1> VOS INFORMATION</h1>

    <p> <?php 
        foreach($_SESSION['user'] as $value)  {
            echo $value;
            echo "</br>";  
        }
    ?> </p>

    <form method="post">
        <input type="submit"  name="deconexion" value="deconexion">
    </form>
<?php } ?>



</body>


</html>