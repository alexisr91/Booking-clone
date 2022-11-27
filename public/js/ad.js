

// add-collection-widget.js
$(document).ready(function () {
    $('.add-another-collection-widget').click(function (e) { // event bouton 
        var list = $($(this).attr('data-list-selector'));
        // Try to find the counter of the list or use the length of the list
        var counter = list.data('widget-counter') || list.children().length;

        // grab the prototype template
        var newWidget = list.attr('data-prototype');
        
        // replace the "__name__" used in the id and name of the prototype
        // with a number that's unique to your emails
        // end name attribute looks like name="contact[emails][2]"
        newWidget = newWidget.replace(/__name__/g, counter); // clean l'element et le dynamise 
        // Increase the counter
        counter++;
        // And store it, the length cannot be used if deleting widgets is allowed
        list.data('widget-counter', counter);

        // create a new list element and add it to the list
        var newElem = $(list.attr('data-widget-tags')).html(newWidget);
        addTagFormDeleteLink(newElem);
        newElem.appendTo(list);
    });
});

const addTagFormDeleteLink = (item) => { // Fonction qui genere un bouton auquel on ajoute un texte supprimer à l'interieur du bouton 
    const containerButton = document.createElement('div');
    containerButton.classList.add('button-delete')
    const removeFormButton = document.createElement('button');
    removeFormButton.innerText = 'X';
    removeFormButton.classList.add('btn-danger');
    removeFormButton.classList.add('btn');
    containerButton.append(removeFormButton);
    item.append(containerButton); // On lui ajoute dans sa méthode le bouton que l'on vient de créer 

    removeFormButton.addEventListener('click', (e) => { // Si on clique sur le bouton EVENT ça supprime le li qui contient les informations 
        e.preventDefault(); 
        // remove the li for the tag form
        item.remove();
    });
}

document
    .querySelectorAll('ul#images-fields-list li')
    .forEach((tag) => {
        addTagFormDeleteLink(tag)
    })

// ... the rest of the block from above


