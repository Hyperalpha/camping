var tableauAncienneValeurs = Array();

/**
 * Relie les événements pour la popup de réglage
 * 
 * @author adupuis
 */
function relierEvenementsPopupReglages() {

	// Chargement de la popup au premier clic
	$("#boutonReglages").on("click", function() {
		$.ajax({
			type : "GET",
			url : "charger_popup_reglages.php",
			success : successPremierClicPopupReglages
		});
	});
}

/**
 * Lors du chargement avec succès de la popup de réglages
 * 
 * @author adupuis
 * @param string
 *            data Contenu de la popup renvoyé
 */
function successPremierClicPopupReglages(data) {

	if (data != false) {
		// On remplace le contenu par le nouveau
		$("#popupReglages").replaceWith(data);

		// Bouton d'ouverture de la popup de réglages
		$("#boutonReglages").off("click");
		$("#boutonReglages").on("click", function() {
			$("#popupReglages").dialog("open");
		});

		// Initialisation de la popup de réglages
		$("#popupReglages").dialog({
			autoOpen : false,
			buttons : [ {
				text : FERMER,
				click : function() {
					$(this).dialog("close");
					// On restaure les anciennes valeurs
					restaurerAnciennesValeursPopupReglages();
				}
			}, {
				text : ENREGISTRER,
				id : "enregistrerReglagesPopupReglages",
				click : function() {
					// On soumet le formulaire de la popup de réglages
					$('#popupReglages form#formulairePopupReglages').submit();
				}
			} ],
			height : 530,
			modal : true,
			width : 600
		});

		// Initialisation des onglets
		$("#ongletsPopupReglages").tabs();

		// Initialisation des champs décimaux
		$('#popupReglages .champ-decimal').on("change", function() {
			formaterChampDecimal($(this));
		});

		// Initialisation des champs date
		$('#popupReglages .champ-date-sans-annees').datepicker(
				{
					dateFormat : DATE_FORMAT_SANS_ANNEE,
					firstDay : 1,
					monthNames : [ JANVIER, FEVRIER, MARS, AVRIL, MAI, JUIN,
							JUILLET, AOUT, SEPTEMBRE, OCTOBRE, NOVEMBRE,
							DECEMBRE ],
					dayNamesMin : [ DIMANCHE_MIN, LUNDI_MIN, MARDI_MIN,
							MERCREDI_MIN, JEUDI_MIN, VENDREDI_MIN, SAMEDI_MIN ]
				});

		// Sauvegarde des données d'origine
		mettreAJourValeursPopupReglages();

		// On clic pour ouvrir la popup
		$("#boutonReglages").click();
	} else {
		alertPop(ERREUR_INCONNUE);
	}
}

/**
 * Fonction qui met à jour les valeurs du tableau tableauAncienneValeurs avec
 * les valeurs de la popup actuelle
 * 
 * @author adupuis
 */
function mettreAJourValeursPopupReglages() {
	tableauAncienneValeurs['prixNuitAdultePopupReglages'] = $('#popupReglages')
			.find('#prixNuitAdultePopupReglages').val();
	tableauAncienneValeurs['prixNuitEnfantPopupReglages'] = $('#popupReglages')
			.find('#prixNuitEnfantPopupReglages').val();
	tableauAncienneValeurs['prixNuitAnimalPopupReglages'] = $('#popupReglages')
			.find('#prixNuitAnimalPopupReglages').val();
	tableauAncienneValeurs['prixNuitPetitEmplacementPopupReglages'] = $(
			'#popupReglages').find('#prixNuitPetitEmplacementPopupReglages')
			.val();
	tableauAncienneValeurs['prixNuitGrandEmplacementPopupReglages'] = $(
			'#popupReglages').find('#prixNuitGrandEmplacementPopupReglages')
			.val();
	tableauAncienneValeurs['prixNuitEmplacementCampingCarPopupReglages'] = $(
			'#popupReglages').find(
			'#prixNuitEmplacementCampingCarPopupReglages').val();
	tableauAncienneValeurs['prixNuitElectricitePopupReglages'] = $(
			'#popupReglages').find('#prixNuitElectricitePopupReglages').val();
	tableauAncienneValeurs['prixNuitVehiculeSupplementairePopupReglages'] = $(
			'#popupReglages').find(
			'#prixNuitVehiculeSupplementairePopupReglages').val();
	tableauAncienneValeurs['prixNuitVisiteurPopupReglages'] = $(
			'#popupReglages').find('#prixNuitVisiteurPopupReglages').val();
	tableauAncienneValeurs['prixRoulotteRougePeriodeBassePopupReglages'] = $(
			'#popupReglages').find(
			'#prixRoulotteRougePeriodeBassePopupReglages').val();
	tableauAncienneValeurs['prixRoulotteRougePeriodeHautePopupReglages'] = $(
			'#popupReglages').find(
			'#prixRoulotteRougePeriodeHautePopupReglages').val();
	tableauAncienneValeurs['prixRoulotteBleuePeriodeBassePopupReglages'] = $(
			'#popupReglages').find(
			'#prixRoulotteBleuePeriodeBassePopupReglages').val();
	tableauAncienneValeurs['prixRoulotteBleuePeriodeHautePopupReglages'] = $(
			'#popupReglages').find(
			'#prixRoulotteBleuePeriodeHautePopupReglages').val();
	tableauAncienneValeurs['dateDebutAffichageTableauReservationsPopupReglages'] = $(
			'#popupReglages').find(
			'#dateDebutAffichageTableauReservationsPopupReglages').val();
	tableauAncienneValeurs['dateFinAffichageTableauReservationsPopupReglages'] = $(
			'#popupReglages').find(
			'#dateFinAffichageTableauReservationsPopupReglages').val();
	tableauAncienneValeurs['dateDebutPeriodeHauteRoulottePopupReglages'] = $(
			'#popupReglages').find(
			'#dateDebutPeriodeHauteRoulottePopupReglages').val();
}

/**
 * Fonction qui restaure les anciennes valeurs de la popup de réglages à partir
 * du tableau tableauAncienneValeurs
 * 
 * @author adupuis
 */
function restaurerAnciennesValeursPopupReglages() {
	var key;

	for (key in tableauAncienneValeurs) {
		$('#popupReglages').find('#' + key).val(tableauAncienneValeurs[key]);
	}
}

/**
 * Vérifie les contraintes du formulaire de création d'une nouvelle réservation
 * 
 * @author adupuis
 * @param jQuery
 *            formulaire Formulaire à valider
 * @param Integer
 *            nbNuites Nombre de nuités prévues par la réservation
 * @returns Boolean Renvoie true si pas d'erreurs, false sinon
 */
function verifContraintesFormAjoutRes2(formulaire, nbNuites) {
	var champsErreur = "";
	var messageErreur = "";
	var erreur = false;

	var nbAdultes = parseInt($(formulaire).find(
			"#nbAdultesClientPopupAjoutReservation").val());
	var nbEnfants = parseInt($(formulaire).find(
			"#nbEnfantsClientPopupAjoutReservation").val());
	var nbPTente = parseInt($(formulaire).find(
			"#nbPetiteTenteClientPopupAjoutReservation").val());
	var nbGTente = parseInt($(formulaire).find(
			"#nbGrandeTenteClientPopupAjoutReservation").val());
	var nbCaravane = parseInt($(formulaire).find(
			"#nbCaravaneClientPopupAjoutReservation").val());
	var nbVan = parseInt($(formulaire)
			.find("#nbVanClientPopupAjoutReservation").val());
	var nbCampingCar = parseInt($(formulaire).find(
			"#nbCampingCarClientPopupAjoutReservation").val());

	if (isNaN(nbAdultes) == true) {
		nbAdultes = 0;
	}
	if (isNaN(nbEnfants) == true) {
		nbEnfants = 0;
	}
	if (isNaN(nbPTente) == true) {
		nbPTente = 0;
	}
	if (isNaN(nbGTente) == true) {
		nbGTente = 0;
	}
	if (isNaN(nbCaravane) == true) {
		nbCaravane = 0;
	}
	if (isNaN(nbVan) == true) {
		nbVan = 0;
	}
	if (isNaN(nbCampingCar) == true) {
		nbCampingCar = 0;
	}

	// Vérification des caractères interdits
	$(formulaire).find(".champFormulaire").each(function(index, elem) {
		var valeurChamp = $(elem).val();

		if (valeurChamp.indexOf("|") >= 0) {
			champsErreur = champsErreur + "<br/> - " + $(elem).attr("name");
		}
	});
	if (champsErreur != "") {
		messageErreur = messageErreur + CARACTERE_PIPE_INTERDIT_DANS_CHAMPS_2P
				+ champsErreur + '<br/><br/>';
		champsErreur = "";
	}

	// Vérification des champs obligatoires
	$(formulaire).find(".champObligatoire").each(function(index, elem) {
		if ($(elem).val() == "") {
			champsErreur = champsErreur + "<br/> - " + $(elem).attr("name");
		}
	});
	if (champsErreur != "") {
		messageErreur = messageErreur + CHAMPS_SUIVANTS_OBLIGATOIRES_2P
				+ champsErreur + '<br/><br/>';
		champsErreur = "";
	}

	// Vérification des champs numériques
	$(formulaire).find(".champNombre").each(function(index, elem) {
		if (isNaN($(elem).val()) == true) {
			champsErreur = champsErreur + "<br/> - " + $(elem).attr("name");
		}
	});
	if (champsErreur != "") {
		messageErreur = messageErreur + CHAMPS_SUIVANTS_DOIVENT_ETRE_NOMBRES
				+ champsErreur + '<br/><br/>';
		champsErreur = "";
	}

	// Vérification du nombre de nuités
	if (nbNuites <= 0) {
		messageErreur = messageErreur + NOMBRE_NUITEES_INCORRECT + '<br/><br/>';
	}

	// Vérification du nombre de personnes
	if ((nbAdultes + nbEnfants) < 1) {
		messageErreur = messageErreur + NOMBRE_PERSONNES_INCORRECT
				+ '<br/><br/>';
	}

	// Vérification de l'habitation
	if ((nbPTente + nbGTente + nbCaravane + nbVan + nbCampingCar) < 1) {
		messageErreur = messageErreur + NOMBRE_HABITATION_INCORRECT
				+ '<br/><br/>';
	}

	// Affichage des erreurs
	if (messageErreur != "") {
		alertPop(messageErreur);
		erreur = true;
	}

	return erreur;
}

/**
 * Fonction qui supprime une réservation
 * 
 * @author adupuis
 * @param idBlocReservation
 *            string Id du bloc de réservation à supprimer
 */
function supprimerReservation2(idBlocReservation) {
	var tabDonnees = parseInfosReservation($("#" + idBlocReservation).find(
			'input[id^="infosdraggable"]').val());

	// Suppression en AJAX
	$.ajax({
		type : "POST",
		url : "supprimer_reservation.php",
		data : {
			idFiche : tabDonnees.idFiche
		},
		success : function(data) {
			if (data == "1") {

				// Fermeture de la popup
				$("#popupAjoutModifReservation").dialog("close");

				// Suppression du bloc
				$("#" + idBlocReservation).remove();

				// Recalcul des statistiques
				calculStatistiquesParJour();

				alertPop(RESERVATION_SUPPRIMEE);
			} else {
				alertPop(ERREUR_INCONNUE);
			}
		},
		error : function() {
			alertPop(ERREUR_INCONNUE);
		}
	});
}

/**
 * Enregistre le numéro d'emplacement d'une réservation en AJAX
 * 
 * @author adupuis
 * @param idFiche
 *            Référence de la réservation
 * @param numeroEmplacement
 *            Numéro de l'emplacement
 */
function enregistrerReglages(idFiche, numeroEmplacement) {

	// // Sauvegarde en AJAX
	// $.ajax({
	// type : "POST",
	// url : "enregistrer_emplacement.php",
	// data : {
	// refFiche : idFiche,
	// numeroEmplacement : numeroEmplacement
	// },
	// success : function(data) {
	// var strInfos = null;
	// var tabInfos = null;
	//
	// if (data == true) {
	// // On met à jour les données de l'emplacement
	// strInfos = $('#popupDetailsReservation').find(
	// "#infosPopupDetailsReservation").val();
	// tabInfos = parseInfosReservation(strInfos);
	// tabInfos.numeroEmplacement = numeroEmplacement;
	//
	// $("#" + tabInfos.idReservation).find(
	// 'input[id^="infosdraggable"]').val(
	// serialiserInfosReservation(tabInfos));
	//
	// alertPop(EMPLACEMENT_ENREGISTRE);
	// } else {
	// alertPop(ERREUR_INCONNUE);
	// }
	// },
	// error : function() {
	// alertPop(ERREUR_INCONNUE);
	// }
	// });
}