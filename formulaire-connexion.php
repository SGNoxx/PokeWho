<!DOCTYPE html>
<html>
<?php
    include("includes/head.php");
    include("includes/header.php");
?>
<body>
    <div class="group-carre-connexion">
        <div class="carre-front-connexion">
            <div class="text-connexion">
                <p>Se connecter</p>
                <?php if (isset($error)) { echo "<p style='color: red;'>$error</p>"; } ?>
                <form class="formulaire" action="request-connexion.php" method="post">
                    <div class="label">
                        <label class="label-solo" for="email">Email</label>
                        <input type="text" name="email" id="email">
                    </div>
                    <div class="label">
                        <label class="label-solo" for="mdp">Mot de passe</label>
                        <input type="password" name="mdp" id="mdp" pattern="[A-Za-z0-9_$]{8,}" title="Le mot de passe doit contenir au moins 8 caractères alphanumériques">
                    </div>
                    <div class="group-button-connexion">
                        <input class="button-carre-front-connexion" type="submit" value="Se connecter">
                        <div class="button-carre-back-connexion"></div>
                    </div>
                </form>
                <div class="lien-inscription">
                    <a href="inscription.php">Vous n’avez pas de compte? Inscrivez-vous!</a>
                </div>
            </div>
        </div>

        <div class="carre-back-connexion"></div>
    </div>

    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <p>Erreur : Logs inconnus</p>
        </div>
    </div>
    <?php
        include("includes/footer.php");
    ?>
    <script src="script.js"></script>
</body>
</html>
