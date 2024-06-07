<?php
session_start();

// Vérification de l'état de connexion
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: formulaire-connexion.php");
    exit();
}

// Initialisation des variables de session
if (!isset($_SESSION['questions_answered'])) {
    $_SESSION['questions_answered'] = 0;
    $_SESSION['score'] = 0;
    $_SESSION['points_by_name'] = [];
    $_SESSION['point_names'] = [];
    $_SESSION['selected_reponses'] = [];
}

include_once("connexion/connexion.php");

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

    // Sélectionner une nouvelle question aléatoire qui n'a pas encore été posée
    $selected_reponses = $_SESSION['selected_reponses'] ?: [0]; // Si vide, utiliser [0] pour éviter une erreur SQL
    $sql1 = "SELECT * FROM question WHERE `ID-question` NOT IN (" . implode(',', $selected_reponses) . ") ORDER BY RAND() LIMIT 1";
    $qry1 = $pdo->prepare($sql1);
    $qry1->execute();
    $question = $qry1->fetch(PDO::FETCH_ASSOC);

    // Si toutes les questions ont été posées, rediriger vers la page de résultats
    if (!$question) {
        header('Location: resultat.php');
        exit();
    }

    $sql2 = "SELECT * FROM reponse WHERE `#ID_question` = :id_question";
    $qry2 = $pdo->prepare($sql2);
    $qry2->execute(['id_question' => $question['ID-question']]);
    $reponses = $qry2->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $err) {
    echo "Erreur de connexion à la base de données : " . htmlspecialchars($err->getMessage());
    exit();
}

// Traitement des réponses
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reponse'])) {
    $reponse_id = (int)$_POST['reponse'];

    // Récupérer les détails de la réponse sélectionnée
    $sql3 = "SELECT r.*, p.`Point-name` FROM reponse r JOIN point p ON r.`#ID-point` = p.`ID-point` WHERE `ID-reponse` = :id_reponse";
    $qry3 = $pdo->prepare($sql3);
    $qry3->execute(['id_reponse' => $reponse_id]);
    $reponse = $qry3->fetch(PDO::FETCH_ASSOC);

    if ($reponse) {
        $_SESSION['score'] += $reponse['Answer-point-amount'];

        if (!isset($_SESSION['points_by_name'][$reponse['Point-name']])) {
            $_SESSION['points_by_name'][$reponse['Point-name']] = 0;
        }
        $_SESSION['points_by_name'][$reponse['Point-name']] += $reponse['Answer-point-amount'];
        $_SESSION['point_names'][] = $reponse['Point-name'];
        $_SESSION['questions_answered']++;
        $_SESSION['selected_reponses'][] = $reponse['#ID_question'];

        // Si 5 questions ont été répondues, rediriger vers la page de résultats
        if ($_SESSION['questions_answered'] >= 5) {
            header('Location: resultat.php');
            exit();
        } else {
            header('Location: question.php');
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<?php
    include("includes/head.php");
    include("includes/header.php");
?>
<body>
    <main>
        <div class="group-carre-question">
            <div class="carre-front-question">
                
                <div class="text">
                    
                    <div class="groupe-reponse">
                        <?php foreach ($reponses as $reponse) { ?>
                        <form class="form-question" method="post">
                            <div class="solo-reponse">
                                <div class="group-button-question">
                                    <button class="button-carre-front-question" type="submit" name="reponse" value="<?= $reponse['ID-reponse'] ?>">
                                        <?= htmlspecialchars($reponse['Answer-content']) ?>
                                    </button>
                                    <div class="button-carre-back-question"></div>
                                </div>
                            </div>
                        </form>
                        <?php } ?>
                    </div>

                    <div class="reponse">
                    <div class="group-carre-reponse">
                        <div class="carre-front-reponse">
                            <p><?= htmlspecialchars($question['Text-question']) ?></p>
                        </div>
                        <div class="carre-back-reponse"></div>
                    </div>
                </div>
                </div>
            </div>
            <div class="carre-back-question"></div>
        </div>
    </main>
    <?php include("includes/footer.php"); ?>
</body>
</html>
