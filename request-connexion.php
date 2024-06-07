<?php
session_start();
$email = (isset($_POST["email"]) && !empty($_POST["email"])) ? htmlspecialchars($_POST["email"]) : null;
$mdp = (isset($_POST["mdp"]) && !empty($_POST["mdp"])) ? htmlspecialchars($_POST["mdp"]) : null;
var_dump($email, $mdp);
if ($email && $mdp) {
    include_once("connexion/connexion.php");
    try {
        $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        $qry = $pdo->prepare("SELECT * FROM user WHERE email=?");
        $qry->execute(array($email));
        $data_user = $qry->fetch();
        
        // Vérification du mot de passe
        if ($data_user && password_verify($mdp, $data_user["mdp"])) {
            // Vérification du type d'utilisateur
            if ($data_user["type-user"] == 1) {
                // Utilisateur normal, rediriger vers question.php
                header("Location: question.php");
            } elseif ($data_user["type-user"] == 2) {
                // Administrateur, rediriger vers admin.php
                header("Location: admin.php");
            }
            // Enregistrement des données de session
            $_SESSION["loggedin"] = true;
            $_SESSION["type-user"] = $data_user["type-user"];
            exit();
        } else {
            $error = "L'email ou le mot de passe est incorrect";
        }
    } catch (PDOException $err) {
        $_SESSION["compte-erreur-sql"] = $err->getMessage();
        header("location:pageerreur.php");
        exit();
    }
} else {
    $error = "L'email ou le mot de passe est incorrect";
}
?>
