var TYPE_OK_SEUL = "1";
var TYPE_OUI_NON = "2";

$(document).ready(function() {
	
});

/**
 * Fonction qui affiche une alerte mais dans un popup
 * @author adupuis 
 * @param message string Message � afficher
 * @param typeBoutons Boutons affich�s (bouton OK par d�faut)
 * @param callbackOui Callback quand on clique sur le bouton Oui (ou OK)
 * @param callbackNon Callback quand on clique sur le bouton Non (ou Annuler)
 */
function alertPop(message, typeBoutons, callbackOui, callbackNon) {
	var bouton = [{text: OK, click: function() {$(this).dialog("close");}}];
	
	//D�finition des boutons
	if (typeof callbackOui != "undefined") {
		bouton = [{text: OK, click: callbackOui}];
	}
	if (typeof typeBoutons != "undefined") {
		if ((typeBoutons = TYPE_OUI_NON)
				&& (typeof callbackOui != "undefined")
				&& (typeof callbackNon != "undefined")) {
			bouton = [{text: NON, click: callbackNon}, {text: OUI, click: callbackOui}];
		}
	}
	
	//Message � afficher
	$("#popupAlert").html(message);
	
	$("#popupAlert").dialog({
		autoOpen: true,
		buttons: bouton,
		modal: true
	});
}

/**
 * Formatte le champ input pour faire apparaitre un nombre d�cimal
 * @author adupuis
 * @param jQuery input Objet jQuery contenant l'input
 */
function formaterChampDecimal(input) {
	var valeur = $(input).val();
	
	//On commence par remplacer la virgule par un point
	valeur = valeur.replace(',', '.');
	
	//On parse en d�cimal
	if (!isNaN(parseFloat(valeur))) {
		valeur = parseFloat(valeur);
	}
	else {
		valeur = '';
	}
	
	$(input).val(valeur);
}
