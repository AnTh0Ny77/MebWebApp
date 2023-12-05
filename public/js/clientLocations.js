function validerCoordonneesGPS(coordonnees) {
    // Expression régulière pour le format "lat, lng"
    var regexCoordonnees = /^-?([1-8]?[1-9]|[1-9]0)\.{1}\d{1,6}\s*,\s*-?([1]?[0-7]?[0-9]|180)\.{1}\d{1,6}$/;

    // Vérifier si la chaîne correspond à la regex
    return regexCoordonnees.test(coordonnees);
}

function validerTextColumn(textColumn) {
    // Vérifier si le champ n'est pas vide et a entre 3 et 75 caractères
    return textColumn.trim() !== '' && textColumn.length >= 3 && textColumn.length <= 75;
}

function validerPostal(postal) {
    // Vérifier si le champ n'est pas vide et a entre 5 et 120 caractères
    return postal.trim() !== '' && postal.length >= 5 && postal.length <= 120;
}


function postLocation() {
    var textColumnInput = document.getElementById("textColumn").value;
    var coordonneesInput = document.getElementById("JsonColumn").value;
    var postalInput = document.getElementById("postal").value;

    // Vérifier si les coordonnées sont valides
    if (validerCoordonneesGPS(coordonneesInput)) {
        // Vérifier si le champ textColumn est valide
        if (validerTextColumn(textColumnInput)) {
            // Vérifier si le champ postal est valide
            if (validerPostal(postalInput)) {
                // Les coordonnées, textColumn et postal sont valides, vous pouvez poster le formulaire
                document.getElementById("clientLocationForms").submit();
            } else {
                alert("Le champ postal doit avoir entre 5 et 120 caractères.");
            }
        } else {
            alert("Le champ textColumn doit avoir entre 3 et 75 caractères.");
        }
    } else {
        alert("Les coordonnées GPS sont mal renseignées. Veuillez utiliser le format correct (lat, lng).");
    }
}


function EditLocation() {
    var textColumnInput = document.getElementById("textColumnM").value;
    var coordonneesInput = document.getElementById("JsonColumnM").value;
    var postalInput = document.getElementById("postalM").value;

    // Vérifier si les coordonnées sont valides
    if (validerCoordonneesGPS(coordonneesInput)) {
        // Vérifier si le champ textColumn est valide
        if (validerTextColumn(textColumnInput)) {
            // Vérifier si le champ postal est valide
            if (validerPostal(postalInput)) {
                // Les coordonnées, textColumn et postal sont valides, vous pouvez poster le formulaire
                document.getElementById("clientLocationModif").submit();
            } else {
                alert("Le champ postal doit avoir entre 5 et 120 caractères.");
            }
        } else {
            alert("Le champ textColumn doit avoir entre 3 et 75 caractères.");
        }
    } else {
        alert("Les coordonnées GPS sont mal renseignées. Veuillez utiliser le format correct (lat, lng).");
    }
}


function formatLatLng(obj) {
    // Vérifie si l'objet a les propriétés lat et lng
    if (obj && typeof obj.lat !== 'undefined' && typeof obj.lng !== 'undefined') {
        // Formate la chaîne avec les valeurs de lat et lng
        return obj.lat + ' , ' + obj.lng;
    } else {
        // Retourne une chaîne vide si les propriétés ne sont pas définies
        return '';
    }
}

function extractLocation(data){

    // Parse la chaîne JSON en objet JavaScript
    let jsonObject = JSON.parse(data);

    // Récupère les valeurs lat et lng
    let lat = jsonObject.lat;
    let lng = jsonObject.lng;

    // Crée une nouvelle chaîne avec les valeurs lat et lng
    return lat + ' , ' + lng;
}

document.getElementById("postlocationB").addEventListener("click", postLocation);

document.getElementById("postlocationBM").addEventListener("click", EditLocation);



document.addEventListener('DOMContentLoaded', function () {
    // Attend que le DOM soit chargé

    // Sélectionne les éléments avec la classe "link-none"
    var links = document.querySelectorAll('.link-none');

    // Ajoute un gestionnaire d'événement au clic de chaque lien
    links.forEach(function (link) {
        link.addEventListener('click', function (event) {
            // Empêche le comportement par défaut du lien
            event.preventDefault();
           
            var linkValue = link.getAttribute('value');
            // Récupère la valeur de l'input avec l'ID "date"
            var dateValue = document.getElementById('datetime').value;

            // Récupère la valeur de l'input avec l'ID "game"
            var gameValue = document.getElementById('select-games').value;

            // Construit la nouvelle URL avec les valeurs ajoutées comme paramètres
            var nouvelleURL = 'https://client.explorelab.app/qr2?default=' + encodeURIComponent(link.getAttribute('value')) +
                '&date=' + encodeURIComponent(dateValue) +
                '&game=' + encodeURIComponent(gameValue);

            // Redirige l'utilisateur vers la nouvelle URL
           window.location.href = nouvelleURL;
        });
    });

    // Sélectionne les éléments avec la classe "editC"
    var clickModal = document.querySelectorAll('.editC');

    // Ajoute un gestionnaire d'événement au clic de chaque élément
    clickModal.forEach(function (element) {
        element.addEventListener('click', function () {
            // Affiche la valeur de l'élément dans la console
           
            $data = JSON.parse(element.value);
            console.log($data);
            document.getElementById('JsonColumnM').value = extractLocation($data.jsonColumn[0]) ;
            document.getElementById('postalM').value = $data.postal;
            document.getElementById('textColumnM').value = $data.textColumn;
            document.getElementById('locationId').value = $data.id;
            
            var checkBox = document.getElementById('isActiveM');

            // Vérifiez la valeur de booleanColumn et cochez la case à cocher en conséquence
            if ($data.booleanColumn) {
                checkBox.checked = true;
            } else {
                checkBox.checked = false;
            }
           
        });
    });

    var deleteModal = document.querySelectorAll('.link-delete');
    deleteModal.forEach(function (element) {
        element.addEventListener('click', function (event) {
            var linkValue = document.getElementById('locationId').value;

            // Construit la nouvelle URL avec les valeurs ajoutées comme paramètres
            var nouvelleURL = 'https://client.explorelab.app/qr2?delete=' + encodeURIComponent(linkValue);

            // Redirige l'utilisateur vers la nouvelle URL
            window.location.href = nouvelleURL;
            
        });
    });
});

