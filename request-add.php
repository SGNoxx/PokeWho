<?php
include_once("connexion/connexion.php");

// Vérification des jetons
session_start();

$nomAdd = isset($_POST["nomAdd"]) ? $_POST["nomAdd"] : "";
$pointAdd = isset($_POST["pointAdd"]) ? $_POST["pointAdd"] : "";
$pointType = isset($_POST["pointType"]) ? $_POST["pointType"] : "";

// Initialiser les chemins des images
$normalImagePath = null;
$heureuxImagePath = null;

$erreurs = [];

// Vérification du nom
if (!preg_match("/^[A-Za-z0-9\x{00c0}-\x{00ff}-]{1,}$/u", $nomAdd)) {
    $erreurs["nomAdd"] = "Le format de nomAdd est invalide";
}

// Vérification des points requis
if (!preg_match("/^(?:[1-9]?[0-9]|100)$/", $pointAdd)) {
    $erreurs["pointAdd"] = "Le format de pointAdd est invalide";
}

if (!preg_match("/^(?:[1-9]?[0-9]|100)$/", $pointType)) {
    $erreurs["pointType"] = "Le format de pointType est invalide";
}

// Protection XSS
$nomAdd = htmlspecialchars($nomAdd);
$pointAdd = htmlspecialchars($pointAdd);
$pointType = htmlspecialchars($pointType);

// Gestion de l'upload de l'image normale
if (!empty($_FILES['normalAdd']['name'])) {
    $normalImagePath = 'uploads/' . basename($_FILES['normalAdd']['name']);
    if (!move_uploaded_file($_FILES['normalAdd']['tmp_name'], $normalImagePath)) {
        $erreurs["normalAdd"] = "Erreur lors du téléchargement de l'image normale";
    }
}

// Gestion de l'upload de l'image heureuse
if (!empty($_FILES['heureuxAdd']['name'])) {
    $heureuxImagePath = 'uploads/' . basename($_FILES['heureuxAdd']['name']);
    if (!move_uploaded_file($_FILES['heureuxAdd']['tmp_name'], $heureuxImagePath)) {
        $erreurs["heureuxAdd"] = "Erreur lors du téléchargement de l'image heureuse";
    }
}

if (count($erreurs) > 0) {
    $_SESSION["donnees"]["nomAdd"] = $nomAdd;
    $_SESSION["donnees"]["pointAdd"] = $pointAdd;
    $_SESSION["donnees"]["pointType"] = $pointType;
    $_SESSION["erreurs"] = $erreurs;
    echo "Désolé, les champs ne sont pas corrects";
    echo "<a href='formulaire.php'>Vers la page formulaire</a>";
    exit(); // Arrête l'exécution en cas d'erreurs
}

include_once("connexion/connexion.php");

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Préparation de la requête pour vérifier si le Pokémon existe dans la base
    $sql = "SELECT COUNT(*) as nb FROM pokemon WHERE `pokemon-name`=?";
    $qry = $pdo->prepare($sql);
    $qry->execute([$nomAdd]);
    $row = $qry->fetch();

    // Vérification si le Pokémon existe
    if ($row["nb"] > 0) {
        echo "Le Pokémon existe déjà dans la base de données";
        echo "<a href='admin.php'>Vers la page admin</a>";
    } else {
        $sql = "INSERT INTO pokemon(`pokemon-name`, `picture-1`, `picture-2`, `point-required`, `#ID-point`) VALUES (:nomAdd, :normalAdd, :heureuxAdd, :pointAdd, :pointType)";
        $qry = $pdo->prepare($sql);
        $qry->bindParam(':nomAdd', $nomAdd);
        $qry->bindParam(':normalAdd', $normalImagePath);
        $qry->bindParam(':heureuxAdd', $heureuxImagePath);
        $qry->bindParam(':pointAdd', $pointAdd);
        $qry->bindParam(':pointType', $pointType);
        $qry->execute();
        unset($pdo);
        header("location:admin.php");
    }

} catch (PDOException $err) {
    // Gestion des erreurs
    $_SESSION["compte-erreur-sql"] = $err->getMessage();
    header("location:pageerreur.php");
    exit();
}
?>
