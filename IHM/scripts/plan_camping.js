var sauvegardeX = null;
var sauvegardeY = null;

/**
 * @author adupuis
 */
$(document).ready(function() {

	$(".draggable").draggable();

	// Evenement onDragStart sur un emplacement
	$(".draggable").on("dragstart", onDragStartEmplacement);

	// Evenement onDragStop sur un emplacement
	$(".draggable").on("dragstop", onDragStopEmplacement);
	
	//Evenement pour l'impression
	$("#boutonImprimerEmplacements").on("click", function() {
		window.print();
	});

});

/**
 * Fonction déclenchée lors de démarrage du drag & drop d'un emplacement
 * 
 * @author adupuis
 * @param event
 * @param ui
 */
function onDragStartEmplacement(event, ui) {
	// On sauvegarde les coordonnées du bloc
	sauvegardeX = $(this).position().left;
	sauvegardeY = $(this).position().top;
}

/**
 * Fonction déclenchée à la fin du drag & drop d'un emplacement
 * 
 * @author adupuis
 * @param event
 * @param ui
 */
function onDragStopEmplacement(event, ui) {
	var xMinImage = 0;
	var xMaxImage = 0;
	var yMinImage = 0;
	var yMaxImage = 0;
	var imagePlan = $("#planCamping");
	var nouvellePositionX = $(this).position().left;
	var nouvellePositionY = $(this).position().top;
	var blocEmplacement = $(this);

	// On récupère les dimensions du plan
	if ($(imagePlan).size() > 0) {
		xMinImage = parseInt($(imagePlan).position().left);
		xMaxImage = parseInt($(imagePlan).position().left)
				+ parseInt($(imagePlan).width());
		yMinImage = parseInt($(imagePlan).position().top);
		yMaxImage = parseInt($(imagePlan).position().top)
				+ parseInt($(imagePlan).height());
	}

	// On vérifie que le bloc est dans le plan
	if ((nouvellePositionX > xMinImage) && (nouvellePositionX < xMaxImage)
			&& (nouvellePositionY > yMinImage)
			&& (nouvellePositionY < yMaxImage)) {
		// Si le bloc est dans le plan, on demande confirmation
		alertPop(MODIFIER_EMPLACEMENT_RESERVATION, TYPE_OUI_NON, function() {
			var idEmplacement = $(blocEmplacement).attr("id");

			// On sauvegarde les modifications de l'emplacement
			sauvegarderCoordonneesEmplacementEnAjax(idEmplacement.replace(
					"emplacementNumero", ""), parseInt(nouvellePositionX),
					parseInt(nouvellePositionY));

			$(this).dialog("close");
		}, function() {
			// On repositionne le bloc à sa position de départ
			$(blocEmplacement).css("left", sauvegardeX);
			$(blocEmplacement).css("top", sauvegardeY);
			$(this).dialog("close");
		});
	} else {
		// Si le bloc n'est pas dans le plan, on réinitialise sa position
		// d'origine
		$(this).css("left", sauvegardeX);
		$(this).css("top", sauvegardeY);
	}
}

/**
 * Enregistre les coordonnées d'un emplacement en AJAX
 * 
 * @author adupuis
 * @param idFiche
 *            Référence de la réservation
 * @param positionX
 *            Position X de l'emplacement
 * @param positionY
 *            Position Y de l'emplacement
 */
function sauvegarderCoordonneesEmplacementEnAjax(idFiche, positionX, positionY) {

	// Sauvegarde en AJAX
	$.ajax({
		type : "POST",
		url : "enregistrer_emplacement.php",
		data : {
			refFiche : idFiche,
			coordonneesX : positionX,
			coordonneesY : positionY
		},
		success : function(data) {
			if (data == true) {
				alertPop(EMPLACEMENT_ENREGISTRE);
			} else {
				alertPop(ERREUR_INCONNUE);
			}
		},
		error : function() {
			alertPop(ERREUR_INCONNUE);
		}
	});
}