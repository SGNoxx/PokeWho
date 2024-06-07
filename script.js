function erreur(){
    // Récupérer le modal
    var modal = document.getElementById("myModal");
    
    // Récupérer le bouton de fermeture
    var span = document.getElementsByClassName("close")[0];
    
    // Afficher le modal
    modal.style.display = "block";
    
    // Fermer le modal après 2 secondes
    setTimeout(function() {
      modal.style.display = "none";
    }, 5000);
    
    // Quand l'utilisateur clique sur le bouton de fermeture, fermer le modal
    span.onclick = function() {
      modal.style.display = "none";
    }
    close();
}

function close(){
    var modal = document.getElementById("myModal");
    modal.onclick = function() {
        modal.style.display = "none";
    }
    fade(modal)
}

function fade(element) {
    var op = 1;  // initial opacity
    var timer = setInterval(function () {
        if (op <= 0.1){
            clearInterval(timer);
            element.style.display = 'none';
        }
        element.style.opacity = op;
        element.style.filter = 'alpha(opacity=' + op * 100 + ")";
        op -= op * 0.1;
    }, 50);
}

function redirectToNewPage() {
    window.location.href = "question.html";
}

function controle(){
    var pseudo = document.getElementById("pseudo").value;
    var mdp = document.getElementById("mdp").value;
    // localStorage.setItem('pseudoStored', pseudo);
    // localStorage.setItem('mdpStored', mdp);
    if (pseudo !== "root" && mdp !== "toto") {
        erreur();
    } else if (pseudo == "root" && mdp == "toto") {
        redirectToNewPage();
    }
}
