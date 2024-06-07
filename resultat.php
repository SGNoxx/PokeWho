<?php
session_start();

// Vérification de l'état de connexion
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: formulaire-connexion.php");
    exit();
}

if (!isset($_SESSION['score']) || !isset($_SESSION['points_by_name']) || !isset($_SESSION['point_names'])) {
    header('Location: question.php');
    exit();
}

$points_by_name = $_SESSION['points_by_name'];

// Trouver les points les plus fréquents
$points_count = array_count_values($_SESSION['point_names']);
$max_frequency = max($points_count);
$most_frequent_points = array_keys($points_count, $max_frequency);
$most_frequent_point = $most_frequent_points[0];

$score_final = $_SESSION['score'];

include_once("connexion/connexion.php");

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

    // Récupérer l'ID-point correspondant au point_name le plus fréquent
    $sql_point_id = "SELECT `ID-point` FROM point WHERE `Point-name` = :point_name LIMIT 1";
    $qry_point_id = $pdo->prepare($sql_point_id);
    $qry_point_id->execute(['point_name' => $most_frequent_point]);
    $point_data = $qry_point_id->fetch(PDO::FETCH_ASSOC);

    if ($point_data) {
        $point_id = $point_data['ID-point'];

        // Récupérer le Pokémon correspondant
        $sql_pokemon = "SELECT `pokemon-name`, `picture-1`, `picture-2` FROM pokemon WHERE `#ID-point` = :point_id AND `point-required` = :point_required LIMIT 1";
        $qry_pokemon = $pdo->prepare($sql_pokemon);
        $qry_pokemon->execute(['point_id' => $point_id, 'point_required' => $score_final]);
        $pokemon = $qry_pokemon->fetch(PDO::FETCH_ASSOC);

        if ($pokemon) {
            $pokemon_name = $pokemon['pokemon-name'];
            $pokemon_image1 = $pokemon['picture-1'];
            $pokemon_image2 = $pokemon['picture-2'];
        } else {
            $pokemon_name = "Aucun Pokémon correspondant trouvé";
            $pokemon_image1 = "";
            $pokemon_image2 = "";
        }
    } else {
        $pokemon_name = "Aucun point correspondant trouvé";
        $pokemon_image1 = "";
        $pokemon_image2 = "";
    }
} catch (PDOException $err) {
    echo "Erreur de connexion à la base de données : " . htmlspecialchars($err->getMessage());
    exit();
}
?>

<!DOCTYPE html>
<html>
<?php
    include("includes/head.php");
    include("includes/header.php");
?>
        <main>
            <div class="group-carre-resultat">
                <div class="carre-front-resultat">
                        <div class="group-petit-carre-1-resultat">
                            <div class="petit-carre-front-1-resultat">
                                <p>Vous semblez etre...</p>
                            </div>
                            <div class="petit-carre-back-1-resultat"></div>
                        </div>
                        <?php if ($pokemon_image1 && $pokemon_image2): ?>
                            <img id="pokemon-image" class="correspondance" src="<?= htmlspecialchars($pokemon_image1) ?>" alt="<?= htmlspecialchars($pokemon_name) ?>">
                        <?php endif; ?>
                        <div class="group-petit-carre-2-resultat">
                            <div class="petit-carre-front-2-resultat">
                                <p><?= htmlspecialchars($pokemon_name) ?> !</p>
                            </div>
                            <div class="petit-carre-back-2-resultat"></div>
                        </div>
                        <div class="group-petit-carre-3-resultat">
                            <div class="petit-carre-front-3-resultat">
                                <a href="#popup1">
                                    <p>Afficher les statistiques</p>
                                </a>
                            </div>
                            <div class="petit-carre-back-3-resultat"></div>
                        </div>
                        <div id="popup1" class="overlay">
                            <div class="popup">
                                <div class="group-carre-resultat">
                                    <div class="carre-front-modal">
                                        <section class="section"><a class="close" href="#">&times;</a>
                                            <div class="content">
                                                <p class="titre-content">Vos resultats :</p>
                                                <div class="stats">
                                                    <div class="group-nature">
                                                    <?php foreach ($points_by_name as $pointName => $points): ?>
                                                        <div class="points">
                                                            <div class="nature">
                                                                <p><?= htmlspecialchars($pointName) ?></p>
                                                            </div>
                                                            <div class="point">
                                                                <p><?= $points ?> points</p>
                                                            </div> 
                                                        </div>
                                                    <?php endforeach; ?>
                                                    </div>
                                                    <!-- <div class="group-point">
                                                    </div> -->
                                                </div>
                                            </div>
                                            
                                        </section>
                                    </div>
                                    <div class="carre-back-modal"></div>
                                </div>
                            </div>
                        </div>
                        <p class="restart">Recommencer le test</p>
                        <div class="group-petit-carre-4-resultat">
                            <div class="petit-carre-front-4-resultat">
                                <form method="post" action="restart.php">
                                    <button class="btn-restart" type="submit">Recommencer</button>
                                </form>
                            </div>
                            <div class="petit-carre-back-4-resultat"></div>
                        </div>
                </div>
                <div class="carre-back-resultat"></div>
            </div>
        </main>
        <footer>
            <div class="copyrights">
                <p>Copyrights 2024;  Designed by Enzo</p>
            </div>
        </footer>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                var pokemonImage = document.getElementById('pokemon-image');
                pokemonImage.src = "<?= htmlspecialchars($pokemon_image2) ?>";
            }, 1000); // Changer l'image après 1 seconde
        });
    </script>
    </body>
</html>
<?php
// Réinitialiser la session après avoir affiché les résultats
session_unset();
session_destroy();
?>
