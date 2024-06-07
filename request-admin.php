<?php
include_once("connexion/connexion.php");
session_start();
// Vérification des jetons

$pseudo = isset($_POST["pseudo"]) ? $_POST["pseudo"] : "";
$email = isset($_POST["email"]) ? $_POST["email"] : "";
$mdp = isset($_POST["mdp"]) ? $_POST["mdp"] : "";
$mdpVerif = isset($_POST["mdpVerif"]) ? $_POST["mdpVerif"] : "";
var_dump("$pseudo", "$email", "$mdp", "$mdpVerif");
$erreurs = [];

// Vérification du pseudo
if (!preg_match("/^[A-Za-z0-9\x{00c0}-\x{00ff}]{1,20}$/u", $pseudo)) {
    $erreurs["pseudo"] = "Le format du pseudo est invalide";
}

// Vérification de l'email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $erreurs["email"] = "L'email n'est pas valide";
}

// Vérification du mot de passe
if (!preg_match("/^[A-Za-z0-9_$]{8,}$/", $mdp)) {
    $erreurs["mdp"] = "Le format du mot de passe n'est pas valide";
}

if ($mdp !== $mdpVerif) {
    $erreurs["mdpVerif"] = "Les mots de passe ne correspondent pas";
}


// Protection XSS
$pseudo = htmlspecialchars($pseudo);
$email = htmlspecialchars($email);
$mdp = htmlspecialchars($mdp);
// $mdpVerif = htmlspecialchars($mdpVerif);

var_dump($erreurs);
if (count($erreurs) > 0) {
    $_SESSION["donnees"]["pseudo"] = $pseudo;
    $_SESSION["donnees"]["email"] = $email;
    $_SESSION["donnees"]["mdp"] = $mdp;
    // $_SESSION["donnees"]["mdpVerif"] = $mdpVerif;
    $_SESSION["erreurs"] = $erreurs;
    echo "Désolé, les champs ne sont pas corrects";
    echo "<a href='inscription.php'>Vers la page formulaire</a>";
    exit(); // Ajouté pour arrêter l'exécution en cas d'erreurs
}
// Parcourir le tableau et valider les entrées
$tableauDonnes = [];
foreach ($_POST as $key => $val) {
    $tableauDonnes[":" . $key] = isset($val) && !empty($val) ? htmlspecialchars($val) : null;
}
// Cryptage du mot de passe
$mdp= password_hash($mdp, PASSWORD_BCRYPT);
// $tableauDonnes[":email"] = md5($email);
include_once("connexion/connexion.php");
try {
    $pdo=new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8",$db_user,$db_password);
    // Options de PDO
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    // Préparation de la requête pour vérifier si l'email existe dans la base
    $sql = "SELECT COUNT(*) as nb FROM user WHERE email=?";
    $qry = $pdo->prepare($sql);
    $qry->execute([$tableauDonnes[":email"]]);
    $row = $qry->fetch();
    // Vérification si l'email existe
    if ($row["nb"]>0) { // Changé de === 1 à > 0 pour être plus générique
        echo "L'email existe déjà dans la base de données";
        echo "<a href='inscription.php'>Vers la page d'inscription</a>";
    } else {
        $sql = "INSERT INTO user(pseudo, email, mdp) VALUES (:pseudo, :email, :mdp)";
        $qry = $pdo->prepare($sql);
        $qry->execute([
            'pseudo'=>$pseudo,
            'email'=>$email,
            'mdp'=>$mdp
        ]);
        unset($pdo);
        // echo "Vous êtes bien inscrit";
        // echo "<a href='accueil.php'>Vers la page d'accueil</a>";
        header("location:question.php");
    }
} catch (PDOException $err) {
    // Gestion des erreurs
    $_SESSION["compte-erreur-sql"] = $err->getMessage();
    header("location:pageerreur.php");
    exit();
}   