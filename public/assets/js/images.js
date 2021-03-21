window.onload = () => {
    let links = document.querySelectorAll("[data-delete]")

    for (link of links) {
        //on ecoute le clic
        link.addEventListener("click", function(e) {
            //on empeche la navigation
            e.preventDefault()
                //on demande confirmation
            if (confirm("Etes vous sûre de vouloir supprimer cette image ?")) {
                //promesse =>on envoie une requete ajax vers le href du lien avvec la methode DELETE
                fetch(this.getAttribute("href"), {
                    method: 'DELETE',
                    header: {
                        "X-Requested-With": "XMLHttpRequest",
                        "Content-Type": "application/json",
                    },
                    body: JSON.stringify({ "_token": this.dataset.token })
                }).then(
                    //on recupere la reponse en json
                    response => response.json()
                ).then(data => {
                    if (data.success)
                        this.parentElement.remove()
                    else
                        alert(data.error)
                }).catch(e => alert(e))
            }
        })
    }
}

let togg1 = document.getElementById("togg1");
let togg2 = document.getElementById("togg2");
let d1 = document.getElementById("d1");
let d2 = document.getElementById("d2");
togg1.addEventListener("click", () => {
    if (getComputedStyle(d1).display != "none") {
        d1.style.display = "none";
    } else {
        d1.style.display = "block";
    }
})

function togg() {
    if (getComputedStyle(d2).display != "none") {
        d2.style.display = "none";
    } else {
        d2.style.display = "block";
    }
};
togg2.onclick = togg;

$(function() {
    // On recupere la position du bloc par rapport au haut du site
    var position_top_raccourci = $("#navigation").offset().top;

    //Au scroll dans la fenetre on déclenche la fonction
    $(window).scroll(function() {

        //si on a defile de plus de 150px du haut vers le bas
        if ($(this).scrollTop() > position_top_raccourci) {

            //on ajoute la classe "fixNavigation" a <div id="navigation">
            $('#navigation').addClass("menuResponsif");
        } else {

            //sinon on retire la classe "fixNavigation" a <div id="navigation">
            $('#navigation').removeClass("menuResponsif");
        }
    });
});