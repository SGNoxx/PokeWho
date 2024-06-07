<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include_once("connexion/connexion.php");

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $pokemonName = $_POST['nom'];
        $pokePoint = $_POST['pointModif'];

        // Chemins pour stocker les images
        $normalImagePath = null;
        $heureuxImagePath = null;

        // Gestion de l'upload de l'image normale
        if (!empty($_FILES['normal']['name'])) {
            $normalImagePath = 'uploads/' . basename($_FILES['normal']['name']);
            if (!move_uploaded_file($_FILES['normal']['tmp_name'], $normalImagePath)) {
                echo "Erreur lors du téléchargement de l'image normale.";
                exit();
            }
        }

        // Gestion de l'upload de l'image heureuse
        if (!empty($_FILES['heureux']['name'])) {
            $heureuxImagePath = 'uploads/' . basename($_FILES['heureux']['name']);
            if (!move_uploaded_file($_FILES['heureux']['tmp_name'], $heureuxImagePath)) {
                echo "Erreur lors du téléchargement de l'image heureuse.";
                exit();
            }
        }

        if (isset($_POST['modifier'])) {
            // Mise à jour des informations du Pokémon dans la base de données
            $sqlUpdate = "UPDATE pokemon SET 
                          `picture-1` = COALESCE(:normalImage, `picture-1`), 
                          `picture-2` = COALESCE(:heureuxImage, `picture-2`),
                          `point-required` = :pokePoint
                          WHERE `pokemon-name` = :pokemonName";
            $stmt = $pdo->prepare($sqlUpdate);
            $stmt->bindParam(':normalImage', $normalImagePath);
            $stmt->bindParam(':heureuxImage', $heureuxImagePath);
            $stmt->bindParam(':pokePoint', $pokePoint);
            $stmt->bindParam(':pokemonName', $pokemonName);
            if (!$stmt->execute()) {
                echo "Erreur lors de la mise à jour du Pokémon.";
                exit();
            }
        }

        if (isset($_POST['supprimer'])) {
            // Suppression du Pokémon de la base de données
            $sqlDelete = "DELETE FROM pokemon WHERE `pokemon-name` = :pokemonName";
            $stmt = $pdo->prepare($sqlDelete);
            $stmt->bindParam(':pokemonName', $pokemonName);
            if (!$stmt->execute()) {
                echo "Erreur lors de la suppression du Pokémon.";
                exit();
            }
        }

        header('Location: admin.php'); // Redirection après le traitement
        exit();
    }
} catch (PDOException $err) {
    // Gestion des erreurs
    echo "Erreur SQL : " . $err->getMessage();
    exit();
}
?>
