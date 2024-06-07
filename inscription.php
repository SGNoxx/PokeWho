<!DOCTYPE html>
<html>
<?php
    include("includes/head.php");
    include("includes/header.php");
?>
        <div class="group-carre-inscription">
            <div class="carre-front-inscription">
                <div class="text-inscription">
                    <p>S'inscrire</p>
                    <form class="formulaire" action="request.php" method="post" onsubmit="return validateForm()">
                        <div class="label">
                            <label class="label-solo" for="pseudo">Pseudo</label>
                            <input type="text" name="pseudo" id="pseudo" pattern="[/^[A-Za-z0-9\x{00c0}-\x{00ff}]{5,20}$/u]">
                        </div>
                        <div class="label">
                            <label class="label-solo" for="email">Adresse email</label>
                            <input type="text" name="email" id="email">
                        </div>
                        <div class="label">
                            <label class="label-solo" for="mdp">Mot de passe</label>
                            <input type="text" name="mdp" id="mdp" pattern="[A-Za-z0-9$]{8,}">
                        </div>
                        <div class="label">
                            <label class="label-solo" for="mdpVerif">Verification mot de passe</label>
                            <input type="text" name="mdpVerif" id="mdpVerif">
                        </div>
                        <div class="group-button-inscription">
                            <input class="button-carre-front-inscription" type="submit" value="S'inscrire">
                            <div class="button-carre-back-inscription"></div>
                        </div>
                    </form>
                    <div class="lien-inscription-i">
                        <a href="formulaire-connexion.php">Vous avez un compte? Connectez-vous!</a>
                    </div>
                </div>
            </div>

            <div class="carre-back-inscription"></div>
        </div>
        <?php
            include("includes/footer.php");
        ?>
        <script type="text/javascript" async>
            function validateForm() {
                var mdp = document.getElementById("mdp").value;
                var mdpVerif = document.getElementById("mdpVerif").value;
            
                if (mdp !== mdpVerif) {
                    alert("Les mots de passe ne correspondent pas.");
                    return false;
                }
            
                if (!/^[A-Za-z0-9$]{8,}$/.test(mdp)) {
                    alert("Le mot de passe doit contenir au moins 8 caractères et ne peut contenir que des lettres, des chiffres et les caractères spéciaux             et $.");
                    return false;
                }
            
                return true;
            }
        </script>
        <script src="script.js"></script>
    </body>
</html>