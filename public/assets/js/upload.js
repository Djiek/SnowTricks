a2lix_lib.sfCollection.init({
    collectionsSelector: '#figure_videos',
    manageRemoveEntry: true,
    lang: {
        add: 'Ajouter',
        remove: 'supprimer'
    }
})

a2lix_lib.sfCollection.init({

    collectionsSelector: '#figure_images',
    manageRemoveEntry: true,
    lang: {
        add: 'Ajouter',
        remove: 'supprimer'
    }

})
$(document).ready(function() {
    bsCustomFileInput.init()
})