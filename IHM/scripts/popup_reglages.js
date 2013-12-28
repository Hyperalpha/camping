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
			height : 590,
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
		$('#popupReglages .champ-date-sans-annee').datepicker(
				{
					dateFormat : DATE_FORMAT_SANS_ANNEE,
					firstDay : 1,
					monthNames : [ JANVIER, FEVRIER, MARS, AVRIL, MAI, JUIN,
							JUILLET, AOUT, SEPTEMBRE, OCTOBRE, NOVEMBRE,
							DECEMBRE ],
					dayNamesMin : [ DIMANCHE_MIN, LUNDI_MIN, MARDI_MIN,
							MERCREDI_MIN, JEUDI_MIN, VENDREDI_MIN, SAMEDI_MIN ]
				});
		$('#popupReglages .champ-date-avec-annee').datepicker(
				{
					dateFormat : DATE_FORMAT_AVEC_ANNEE,
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
	tableauAncienneValeurs['prixNuitTarif1PopupReglages'] = $('#popupReglages')
			.find('#prixNuitTarif1PopupReglages').val();
	tableauAncienneValeurs['prixNuitTarif2PopupReglages'] = $('#popupReglages')
			.find('#prixNuitTarif2PopupReglages').val();
	tableauAncienneValeurs['prixNuitTarif3PopupReglages'] = $('#popupReglages')
			.find('#prixNuitTarif3PopupReglages').val();
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
	tableauAncienneValeurs['prixTenteSafariPeriodeBassePopupReglages'] = $(
			'#popupReglages').find('#prixTenteSafariPeriodeBassePopupReglages')
			.val();
	tableauAncienneValeurs['prixTenteSafariPeriodeHautePopupReglages'] = $(
			'#popupReglages').find('#prixTenteSafariPeriodeHautePopupReglages')
			.val();
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
	var key = null;

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
	var nbTarif1 = parseInt($(formulaire).find(
			"#nbTarif1ClientPopupAjoutReservation").val());
	var nbTarif2 = parseInt($(formulaire).find(
			"#nbTarif2ClientPopupAjoutReservation").val());
	var nbTarif3 = parseInt($(formulaire).find(
			"#nbTarif3ClientPopupAjoutReservation").val());

	if (isNaN(nbAdultes) == true) {
		nbAdultes = 0;
	}
	if (isNaN(nbEnfants) == true) {
		nbEnfants = 0;
	}
	if (isNaN(nbTarif1) == true) {
		nbTarif1 = 0;
	}
	if (isNaN(nbTarif2) == true) {
		nbTarif2 = 0;
	}
	if (isNaN(nbTarif3) == true) {
		nbTarif3 = 0;
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
	if ((nbTarif1 + nbTarif2 + nbTarif3) < 1) {
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