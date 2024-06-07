<?php
session_start();
include_once("connexion/connexion.php");

// Vérifier si l'utilisateur est connecté et si son type d'utilisateur est égal à 2
if (!isset($_SESSION["loggedin"]) || $_SESSION["type-user"] != 2) {
    // Rediriger vers la page de connexion
    header("Location: pageerreur.php");
    exit();
}

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8", $db_user, $db_password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

    $sql = "SELECT * FROM pokemon";
    $qry = $pdo->prepare($sql);
    $qry->execute();
    $pokemons = $qry->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $err) {
    // Gestion des erreurs
    $_SESSION["compte-erreur-sql"] = $err->getMessage();
    header("location:pageerreur.php");
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
            <div class="group-carre-admin">
                <div class="carre-front-admin">
                    <div class="titre-admin"><p>Ajouter des Pokemons</p></div>
                    <form class="formulaire-admin" method="post" enctype="multipart/form-data" action="request-add.php">
                        <div class="label-admin">
                            <label class="label-solo-admin" for="nomAdd">Nom du Pokemon</label>
                            <input class="input-admin-admin" type="text" name="nomAdd" id="nomAdd" pattern="[A-Za-z0-9\x{00c0}-\x{00ff}-]{1,}">
                        </div>
                        <div class="label-admin">
                            <label class="label-solo-admin" for="normalAdd">Image Normale</label>
                            <input class="input-admin-file-admin" type="file" name="normalAdd" id="normalAdd" onchange="updateLabel('normalAdd')">
                            <label class="file-input-label-admin" for="normalAdd" id="label-normalAdd">Choisir une image normale</label>
                        </div>
                        <div class="label-admin">
                            <label class="label-solo-admin" for="heureuxAdd">Image Heureux</label>
                            <input class="input-admin-file-admin" type="file" name="heureuxAdd" id="heureuxAdd" onchange="updateLabel('heureuxAdd')">
                            <label class="file-input-label-admin" for="heureuxAdd" id="label-heureuxAdd">Choisir une image heureuse</label>
                        </div>
                        <div class="label-admin">
                            <label class="label-solo-admin" for="pointAdd">Points Requis</label>
                            <input class="input-admin-admin" type="number" name="pointAdd" id="pointAdd" pattern="(?:[1-9]?[0-9]|100)">
                        </div>
                        <div class="label-admin">
                            <label class="label-solo-admin" for="pointType">Type du pokemon</label>
                            <input class="input-admin-admin" type="number" name="pointType" id="pointType" pattern="(?:[1-9]?[0-9]|100)">
                        </div>
                        <div class="group-button-admin">
                            <input class="button-carre-front-admin" type="submit" value="Confirmer">
                            <div class="button-carre-back-admin"></div>
                        </div>
                    </form>
                    
                    <div class="titre-admin"><p>Liste des Pokemons</p></div>
                    <form class="formulaire-admin" method="POST" enctype="multipart/form-data" action="request-modif.php">
                        <div class="label-admin">
                            <label class="label-solo-admin" for="nom">Nom du Pokemon</label>
                            <select class="input-admin-admin" name="nom" id="nom" placeholder="Nom du Pokemon">
                                <?php foreach ($pokemons as $pokemon) { ?>
                                    <option value="<?= htmlspecialchars($pokemon["pokemon-name"]) ?>"><?= htmlspecialchars($pokemon["pokemon-name"]) ?></option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="label-admin">
                            <label class="label-solo-admin" for="normal">Image Normale</label>
                            <input class="input-admin-file-admin" type="file" name="normal" id="normal" onchange="updateLabel('normal')">
                            <label class="file-input-label-admin" for="normal" id="label-normal">Choisir une image normale</label>
                        </div>
                        <div class="label-admin">
                            <label class="label-solo-admin" for="heureux">Image Heureux</label>
                            <input class="input-admin-file-admin" type="file" name="heureux" id="heureux" onchange="updateLabel('heureux')">
                            <label class="file-input-label-admin" for="heureux" id="label-heureux">Choisir une image heureuse</label>
                        </div>
                        <div class="label-admin">
                            <label class="label-solo-admin" for="pointModif">Points Requis</label>
                            <input class="input-admin-admin" type="number" name="pointModif" id="pointModif">
                        </div>
                        <div class="group-button-admin">
                            <button class="button-carre-front-admin" type="submit" name="modifier">Modifier</button>
                            <div class="button-carre-back-admin"></div>
                        </div>
                        <div class="group-button-admin">
                            <button class="button-carre-front-admin" type="submit" name="supprimer">Supprimer</button>
                            <div class="button-carre-back-admin"></div>
                        </div>
                    </form>
                    
                </div>
                
                <div class="carre-back-admin"></div>
            </div>
        </main>
        <?php
            include("includes/footer.php");
        ?>
        <script>
        function updateLabel(inputId) {
            const input = document.getElementById(inputId);
            const label = document.getElementById('label-' + inputId);
            const fileName = input.files[0] ? input.files[0].name : 'Choisir une image';
            label.textContent = fileName;
        }
        </script>
    </body>
</html>