<?php
session_start();

include_once("connexion/connexion.php");

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

    // Récupérer les points et le nom du point de la réponse choisie
    $sql = "SELECT `Answer-point-amount`, `Point-name` FROM reponse JOIN point ON reponse.`#ID-point` = point.`ID-point` WHERE `ID-reponse`=:id_reponse";
    $qry = $pdo->prepare($sql);
    $qry->execute(['id_reponse' => $_POST['reponse']]);
    $result = $qry->fetch(PDO::FETCH_ASSOC);

    $points = $result['Answer-point-amount'];
    $pointName = $result['Point-name'];

    // Mettre à jour le score et le nombre de questions répondues
    $_SESSION['score'] += $points;
    $_SESSION['questions_answered'] += 1;

    // Stocker les points par point_name
    if (!isset($_SESSION['points_by_name'][$pointName])) {
        $_SESSION['points_by_name'][$pointName] = 0;
    }
    $_SESSION['points_by_name'][$pointName] += $points;

    // Stocker le nom du point
    $_SESSION['point_names'][] = $pointName;

    // Rediriger vers la page de résultats après 5 questions
    if ($_SESSION['questions_answered'] >= 5) {
        header('Location: result-test.php');
        exit();
    } else {
        header('Location: question.php'); // Rediriger vers une nouvelle question
        exit();
    }
} catch (PDOException $err) {
    // Afficher l'erreur
    echo "Erreur de connexion à la base de données : " . htmlspecialchars($err->getMessage());
    exit();
}
?>
