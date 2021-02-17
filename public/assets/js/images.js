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