<?php
session_start();
session_destroy(); // Détruire la session pour réinitialiser toutes les variables de session
header('Location: question.php'); // Rediriger vers la première question
exit();
?>
