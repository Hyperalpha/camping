var separateur = "|";
var sauvegardeEmplacement = null;
var sourcePays = [ {
	value : PAYS_ALLEMAGNE_MIN,
	label : PAYS_ALLEMAGNE
}, {
	value : PAYS_FRANCE_MIN,
	label : PAYS_FRANCE
}, {
	value : PAYS_UK_MIN,
	label : PAYS_UK
}, {
	value : PAYS_PAYS_BAS_MIN,
	label : PAYS_PAYS_BAS
}, {
	value : PAYS_BELGIQUE_MIN,
	label : PAYS_BELGIQUE
}, {
	value : PAYS_IRLANDE_MIN,
	label : PAYS_IRLANDE
} ];

/**
 * Relie les événements pour la popup d'ajout d'une réservation
 * 
 * @author adupuis
 */
function relierEvenementsAjoutReservation() {
	$("#boutonAjouterReservation").button();

	$(".spinner").spinner();

	$("#boutonAjouterReservation").on("click", function() {
		// On vide les champs de la popup = nouvelle réservation
		viderChampsPopup("popupAjoutModifReservation");
		$("#creerModifPopupAjoutModifReservation span").html("Créer");
		// On ouvre la popup de création
		$("#popupAjoutModifReservation").dialog("open");
	});

	// Popup d'ajout d'une réservation
	$("#popupAjoutModifReservation").dialog({
		autoOpen : false,
		buttons : [ {
			text : FERMER,
			click : function() {
				$(this).dialog("close");
			}
		},
		// Le bouton suivant sert pour la création et la modification
		{
			text : CREER_MODIFIER,
			id : "creerModifPopupAjoutModifReservation",
			click : creerModifierReservation
		} ],
		height : 600,
		modal : true,
		width : 610
	});

	// Popup de détails d'une réservation
	$("#popupDetailsReservation")
			.dialog(
					{
						autoOpen : false,
						height : 302,
						resizable : false,
						width : 390,
						buttons : [
								{
									text : '',
									'id' : "imageImprimerPopupDetailsReservation",
									click : function() {
										var tabDonnees = parseInfosReservation($(
												this)
												.find(
														"#infosPopupDetailsReservation")
												.val());

										document.location = 'exporter_reservation.php?idFiche='
												+ tabDonnees.idFiche;
									}
								},
								{
									text : SUPPRIMER,
									'class' : "bouton-details-reservation",
									click : function() {
										var popup = $(this);

										alertPop(
												CONFIRMATION_SUPPRESSION_RESERVATION,
												TYPE_OUI_NON,
												function() {
													tabDonnees = parseInfosReservation($(
															popup)
															.find(
																	"#infosPopupDetailsReservation")
															.val());
													supprimerReservation(tabDonnees.idReservation);
													// Fermeture des popup
													$(this).dialog("close");
													$(popup).dialog("close");
												}, function() {
													$(this).dialog("close");
												});
									}
								},
								{
									text : MODIFIER,
									'class' : "bouton-details-reservation",
									click : function() {
										// On charge les données à modifier
										tabDonnees = parseInfosReservation($(
												this)
												.find(
														"#infosPopupDetailsReservation")
												.val());
										remplirChampsPopupModifReservation(tabDonnees);
										// On affiche les boutons de modif de la
										// popup
										$("#popupAjoutModifReservation").find(
												"#boutonCreerClient").css(
												"visibility", "visible");
										$(
												"#creerModifPopupAjoutModifReservation span")
												.html("Modifier");
										// On affiche le bouton pour imprimer la
										// réservation
										$("#popupAjoutModifReservation").find(
												"#boutonImprimerClient").attr(
												'href',
												'exporter_reservation.php?idFiche='
														+ tabDonnees.idFiche);
										$("#popupAjoutModifReservation").find(
												"#boutonImprimerClient").css(
												"visibility", "visible");
										// On ferme la popup
										$(this).dialog("close");
										// On ouvre la popup de modif
										$("#popupAjoutModifReservation")
												.dialog("open");
									}
								},
								{
									text : OK,
									'class' : "bouton-details-reservation",
									click : function() {
										var nouvelEmplacement = $(this)
												.find(
														"#numEmplacementClientPopupDetailsReservation")
												.val();
										var strInfos = $(this)
												.find(
														"#infosPopupDetailsReservation")
												.val();
										var tabInfos = parseInfosReservation(strInfos);

										// S'il y a eu modification de
										// l'emplacement, on le sauvegarde
										if ((sauvegardeEmplacement != null)
												&& (sauvegardeEmplacement != nouvelEmplacement)) {
											// On sauvegarde le numéro de
											// l'emplacement
											sauvegarderNumeroEmplacementEnAjax(
													tabInfos.idFiche,
													nouvelEmplacement);
										}
										$(this).dialog("close");
									}
								} ]
					});
	// On cache le titre de la popup
	$("#popupDetailsReservation").parent().find(".ui-dialog-titlebar").hide();

	/*
	 * Champs et boutons du formulaire
	 */
	$("#popupAjoutModifReservation").find('#refClientPopupAjoutReservation')
			.on(
					'change',
					function() {
						if ($(this).val() == '') {
							// Si la référence du client est vide, on masque le
							// bouton
							$("#popupAjoutModifReservation").find(
									"#boutonCreerClient").css("visibility",
									"hidden");
						} else {
							// Si la référence du client n'est pas vide, on
							// affiche le bouton
							$("#popupAjoutModifReservation").find(
									"#boutonCreerClient").css("visibility",
									"visible");
						}
					});
	// Bouton créer client
	$("a#boutonCreerClient").on("click", viderChampsClientPopup);

	// Bouton imprimer
	$("a#boutonImprimerClient").on("click", function(event) {
		event.stopPropagation();
		event.preventDefault();

		alertPop(ENREGISTRER_FICHE_RESERVATION, TYPE_OUI_NON, function() {
			$(this).dialog("close");
			document.location = $("a#boutonImprimerClient").attr("href");
		}, function() {
			$(this).dialog("close");
		});
	});

	// Champ prénom
	$("#prenomClientPopupAjoutReservation").autocomplete({
		source : function(request, response) {
			$.ajax({
				url : "rechercher_client.php",
				dataType : "json",
				data : {
					prenom : request.term
				},
				success : function(data) {
					response(data);
				}
			});
		},
		focus : onFocusNomPrenomPopupAjoutModifReservation,
		select : onSelectNomPrenomPopupAjoutModifReservation
	});

	// Champ nom
	$("#nomClientPopupAjoutReservation").autocomplete({
		source : function(request, response) {
			$.ajax({
				url : "rechercher_client.php",
				dataType : "json",
				data : {
					nom : request.term
				},
				success : function(data) {
					response(data);
				}
			});
		},
		focus : onFocusNomPrenomPopupAjoutModifReservation,
		select : onSelectNomPrenomPopupAjoutModifReservation
	});

	// Champ pays
	$("#paysClientPopupAjoutReservation").autocomplete({
		minLength : 0,
		source : sourcePays,
		select : function(event, ui) {
			$(this).val(ui.item.value);
			return false;
		}
	});

	// Champs date
	$("#dateArriveeClientPopupAjoutReservation")
			.datepicker(
					{
						dateFormat : DATE_FORMAT,
						firstDay : 1,
						monthNames : [ JANVIER, FEVRIER, MARS, AVRIL, MAI,
								JUIN, JUILLET, AOUT, SEPTEMBRE, OCTOBRE,
								NOVEMBRE, DECEMBRE ],
						dayNamesMin : [ DIMANCHE_MIN, LUNDI_MIN, MARDI_MIN,
								MERCREDI_MIN, JEUDI_MIN, VENDREDI_MIN,
								SAMEDI_MIN ]
					});
	$("#dateArriveeClientPopupAjoutReservation").on(
			"change",
			function() {
				// Si la date de départ est vide, on la renseigne avec la date
				// d'arrivée +1
				if ($("#dateDepartClientPopupAjoutReservation").val() == "") {
					var date = $(this).datepicker("getDate");
					date.setDate(date.getDate() + 1);
					$("#dateDepartClientPopupAjoutReservation").val(
							$.datepicker.formatDate(DATE_FORMAT, date));
				}

				recalculNbNuitesPopupAjoutRes();
			});
	$("#dateDepartClientPopupAjoutReservation")
			.datepicker(
					{
						dateFormat : DATE_FORMAT,
						firstDay : 1,
						monthNames : [ JANVIER, FEVRIER, MARS, AVRIL, MAI,
								JUIN, JUILLET, AOUT, SEPTEMBRE, OCTOBRE,
								NOVEMBRE, DECEMBRE ],
						dayNamesMin : [ DIMANCHE_MIN, LUNDI_MIN, MARDI_MIN,
								MERCREDI_MIN, JEUDI_MIN, VENDREDI_MIN,
								SAMEDI_MIN ]
					});
	$("#dateDepartClientPopupAjoutReservation").on(
			"change",
			function() {
				// Si la date de départ est vide, on la renseigne avec la date
				// d'arrivée +1
				if ($("#dateArriveeClientPopupAjoutReservation").val() == "") {
					var date = $(this).datepicker("getDate");
					date.setDate(date.getDate() - 1);
					$("#dateArriveeClientPopupAjoutReservation").val(
							$.datepicker.formatDate(DATE_FORMAT, date));
				}

				recalculNbNuitesPopupAjoutRes();
			});

	// Champs décimaux
	$('#popupAjoutModifReservation .champ-decimal').on("change", function() {
		formaterChampDecimal($(this));
	});

	// Champs roulotte
	$(
			'#roulotteRougePopupAjoutReservation, #roulotteBleuePopupAjoutReservation, #tenteSafariPopupAjoutReservation')
			.on(
					'click',
					function() {
						var popupAjoutModifReservation = $('#popupAjoutModifReservation');

						if (($('#roulotteRougePopupAjoutReservation').is(
								':checked') == true)
								|| ($('#roulotteBleuePopupAjoutReservation')
										.is(':checked') == true)
								|| ($('#tenteSafariPopupAjoutReservation').is(
										':checked') == true)) {
							// On grise les champs non pris en charge par la
							// roulotte
							$(popupAjoutModifReservation)
									.find(
											'#nbPetiteTenteClientPopupAjoutReservation, '
													+ '#nbGrandeTenteClientPopupAjoutReservation, '
													+ '#nbCaravaneClientPopupAjoutReservation, '
													+ '#nbVanClientPopupAjoutReservation, '
													+ '#nbCampingCarClientPopupAjoutReservation')
									.spinner("disable");
							$(popupAjoutModifReservation)
									.find(
											'#logoPetiteTentePopupAjoutReservation, '
													+ '#logoGrandeTentePopupAjoutReservation, '
													+ '#logoCaravanePopupAjoutReservation, '
													+ '#logoVanPopupAjoutReservation, '
													+ '#logoCampingCarPopupAjoutReservation, '
													+ '#logoElectriciteClientPopupAjoutReservation')
									.addClass("desactive");
							$(popupAjoutModifReservation)
									.find(
											'#nbAdultesClientPopupAjoutReservation, '
													+ '#nbEnfantsClientPopupAjoutReservation, '
													+ '#nbAnimauxClientPopupAjoutReservation, '
													+ '#electriciteClientPopupAjoutReservation, '
													+ '#nbNuitsVisiteursClientPopupAjoutReservation')
									.attr('disabled', 'disabled');
						} else {
							// On réactive les champs non pris en charge par la
							// roulotte
							$(popupAjoutModifReservation)
									.find(
											'#nbPetiteTenteClientPopupAjoutReservation, '
													+ '#nbGrandeTenteClientPopupAjoutReservation, '
													+ '#nbCaravaneClientPopupAjoutReservation, '
													+ '#nbVanClientPopupAjoutReservation, '
													+ '#nbCampingCarClientPopupAjoutReservation')
									.spinner("enable");
							$(popupAjoutModifReservation)
									.find(
											'#logoPetiteTentePopupAjoutReservation, '
													+ '#logoGrandeTentePopupAjoutReservation, '
													+ '#logoCaravanePopupAjoutReservation, '
													+ '#logoVanPopupAjoutReservation, '
													+ '#logoCampingCarPopupAjoutReservation, '
													+ '#logoElectriciteClientPopupAjoutReservation')
									.removeClass("desactive");
							$(popupAjoutModifReservation)
									.find(
											'#nbAdultesClientPopupAjoutReservation, '
													+ '#nbEnfantsClientPopupAjoutReservation, '
													+ '#nbAnimauxClientPopupAjoutReservation, '
													+ '#electriciteClientPopupAjoutReservation, '
													+ '#nbNuitsVisiteursClientPopupAjoutReservation')
									.attr('disabled', false);
						}
					});
}

/**
 * Création ou modification d'une réservation
 * 
 * @author adupuis
 * @param event
 */
function creerModifierReservation(event) {
	var nbNuites = null;
	var idNouveauBloc = "";
	var popupAjoutModifRes = $("#popupAjoutModifReservation");
	var erreur = false;
	var messageAvertissement = null;

	var idReservation = $(popupAjoutModifRes).find(
			"#idReservationPopupAjoutReservation").val();
	var idFiche = $(popupAjoutModifRes).find("#idFichePopupAjoutReservation")
			.val();

	event.stopPropagation();
	event.preventDefault();

	// Calcul du nombre de nuités
	nbNuites = recalculNbNuitesPopupAjoutRes();

	/*
	 * Vérification des contraintes sur les champs
	 */
	erreur = verifContraintesFormAjoutRes($("#popupAjoutModifReservation")
			.find("form"), nbNuites);

	// Si on est en modif, on supprime le bloc avant de le recréer
	if ((idReservation != "") && (idFiche != "")) {
		idNouveauBloc = idReservation;
	}

	if (erreur == false) {
		if ($(popupAjoutModifRes).find("#refClientPopupAjoutReservation").val() == "") {
			// messageAvertissement = CONFIRMATION_CREATION_CLIENT;
		} else if (($(popupAjoutModifRes).find(
				"#nomSauvClientPopupAjoutReservation").val() != $(
				popupAjoutModifRes).find("#nomClientPopupAjoutReservation")
				.val())
				|| ($(popupAjoutModifRes).find(
						"#prenomSauvClientPopupAjoutReservation").val() != $(
						popupAjoutModifRes).find(
						"#prenomClientPopupAjoutReservation").val())) {
			messageAvertissement = ECRASEMENT_CLIENT
					.replace(
							'{{ANCIEN_CLIENT}}',
							$(popupAjoutModifRes).find(
									"#nomSauvClientPopupAjoutReservation")
									.val()
									+ ' '
									+ $(popupAjoutModifRes)
											.find(
													"#prenomSauvClientPopupAjoutReservation")
											.val())
					.replace(
							'{{NOUVEAU_CLIENT}}',
							$(popupAjoutModifRes).find(
									"#nomClientPopupAjoutReservation").val()
									+ ' '
									+ $(popupAjoutModifRes)
											.find(
													"#prenomClientPopupAjoutReservation")
											.val());
		}
		if (messageAvertissement != null) {
			alertPop(messageAvertissement, TYPE_OUI_NON, function() {
				// On sauvegarde les infos du formulaire
				sauvegardeReservation($("#popupAjoutModifReservation").find(
						"form"), idNouveauBloc);

				// Fermeture de la popup
				$(this).dialog("close");
			}, function() {
				$(this).dialog("close");
			});
		} else {
			// On sauvegarde les infos du formulaire
			sauvegardeReservation(
					$("#popupAjoutModifReservation").find("form"),
					idNouveauBloc);
		}
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
function verifContraintesFormAjoutRes(formulaire, nbNuites) {
	var champsErreur = "";
	var messageErreur = "";
	var erreur = false;

	var nbAdultes = parseInt($(formulaire).find(
			"#nbAdultesClientPopupAjoutReservation").val());
	var nbEnfants = parseInt($(formulaire).find(
			"#nbEnfantsClientPopupAjoutReservation").val());
	var estRoulotte = ($(formulaire)
			.find("#roulotteRougePopupAjoutReservation").is(':checked')
			| $(formulaire).find("#roulotteBleuePopupAjoutReservation").is(
					':checked') | $(formulaire).find(
			"#tenteSafariPopupAjoutReservation").is(':checked'));
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
	if (((nbAdultes + nbEnfants) < 1) && (estRoulotte == false)) {
		messageErreur = messageErreur + NOMBRE_PERSONNES_INCORRECT
				+ '<br/><br/>';
	}

	// Vérification de l'habitation
	if (((nbPTente + nbGTente + nbCaravane + nbVan + nbCampingCar) < 1)
			&& (estRoulotte == false)) {
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
 * Création d'un nouveau bloc de réservation
 * 
 * @author adupuis
 * @param idNouveauBloc
 *            string Id du nouveau bloc. Si vide ou "" on calcul un id unique
 * @param tabDonnees
 *            array Tableau des valeurs de la réservations
 */
function creerBlocReservation(idNouveauBloc, tabDonnees) {
	var largeurUniteReservation = parseInt($("#tableauCalend").find(
			".cellule_calendrier").eq(3).offset().left)
			- parseInt($("#tableauCalend").find(".cellule_calendrier").eq(2)
					.offset().left);
	var idNouveauBlocInfos = "";
	var nouveauBloc = null;
	var nbNuites = (tabDonnees.dateDepartClient - tabDonnees.dateArriveeClient) / 1000 / 3600 / 24;
	var rPlacer = null;

	// On cherche un id unique pour le nouveau bloc de réservation
	if ((idNouveauBloc == "") || (idNouveauBloc == null)) {
		idNouveauBloc = $(".draggable").size() + 1;
		while ($("#draggable" + idNouveauBloc).size() > 0) {
			idNouveauBloc = idNouveauBloc + 1;
		}
		idNouveauBloc = "draggable" + idNouveauBloc;
	}
	idNouveauBlocInfos = "infos" + idNouveauBloc;

	// Si le bloc de réservation existe, on le supprime avant de le recréer
	if (idNouveauBloc != "") {
		$("body").find("#" + idNouveauBloc).remove();
	}

	// Ajout du nouveau bloc dans la page HTML
	$("body").append(
			$("<div></div>").attr("id", idNouveauBloc).attr("class",
					"draggable logo-habitation-mini").css("width",
					nbNuites * largeurUniteReservation + "px").attr("title",
					tabDonnees.prenomClient + " " + tabDonnees.nomClient).html(
					$("<span></span>").html("&nbsp;"))
					.append(
							$("<input id='" + idNouveauBlocInfos
									+ "' type='hidden'/>")).draggable().on(
							"dragstart", onDragStartBlocReservation).on("drag",
							onDragBlocReservation).on("dragstop",
							onDragStopBlocReservation));
	nouveauBloc = $("#" + idNouveauBloc);

	// Si le nombre de nuités est supérieur ou égal à 3, on affiche le nom des
	// personnes
	if (nbNuites >= 3) {
		$(nouveauBloc).append(
				$("<div></div>").attr("class", "div-interne-reservation").css(
						"width",
						(nbNuites * largeurUniteReservation) - 41 + "px").html(
						tabDonnees.nomClient + " " + tabDonnees.prenomClient));
	}

	// Choix de l'icone de la réservation (tente, caravane, ...)
	if (tabDonnees.roulotteRouge > 0) {
		$(nouveauBloc).addClass("logo-roulotte-rouge-mini");
	} else if (tabDonnees.roulotteBleue > 0) {
		$(nouveauBloc).addClass("logo-roulotte-bleue-mini");
	} else if (tabDonnees.tenteSafari > 0) {
		$(nouveauBloc).addClass("logo-tente-safari-mini");
	} else if (tabDonnees.nbCampingCarClient > 0) {
		// Icone camping-car
		$(nouveauBloc).addClass("logo-camping-car-mini");
	} else if (tabDonnees.nbVanClient > 0) {
		// Icone van
		$(nouveauBloc).addClass("logo-van-mini");
	} else if (tabDonnees.nbCaravaneClient > 0) {
		// Icone caravane
		$(nouveauBloc).addClass("logo-caravane-mini");
	} else if (tabDonnees.nbGrandeTenteClient > 0) {
		// Icone grande tente
		$(nouveauBloc).addClass("logo-grande-tente-mini");
	} else if (tabDonnees.nbPetiteTenteClient > 0) {
		// Icone petite tente
		$(nouveauBloc).addClass("logo-petite-tente-mini");
	}

	// On sauvegarde les champs dans l'input caché
	tabDonnees.idReservation = idNouveauBloc;
	$("#" + idNouveauBloc).find('input[id^="infosdraggable"]').val(
			serialiserInfosReservation(tabDonnees));

	// On affiche les détails d'une réservation quand on clique dessus
	$(nouveauBloc).on("click", afficherPopupDetails);

	// On place le bloc dans le tableau (goto planning.js)
	rPlacer = placerBlocReservationDate($(nouveauBloc),
			tabDonnees.dateArriveeClient, false);

	// On optimise le tableau (goto planning.js)
	if (rPlacer == true) {
		optimiserTabReservation(
				tabColonnesCalendrier,
				$('.draggable.logo-camping-car-mini, .draggable.logo-van-mini, .draggable.logo-caravane-mini, .draggable.logo-grande-tente-mini, .draggable.logo-petite-tente-mini'));
	}
}

/**
 * Fonction qui sauvegarde les informations d'une réservation
 * 
 * @author adupuis
 * @param string
 *            idFormulaire Id du formulaire contenant les données
 * @param string
 *            idBlocReservation Id du bloc de réservation (peut être vide)
 * @return array Renvoie toutes les données de la réservation dans un tableau
 */
function sauvegardeReservation(idFormulaire, idBlocReservation) {
	var donnees = Array();

	// Infos-Reservation
	donnees.version = "v1.0";
	donnees.refClient = $(idFormulaire).find("#refClientPopupAjoutReservation")
			.val();
	donnees.idFiche = $(idFormulaire).find("#idFichePopupAjoutReservation")
			.val();
	donnees.nomClient = $(idFormulaire).find("#nomClientPopupAjoutReservation")
			.val();
	donnees.prenomClient = $(idFormulaire).find(
			"#prenomClientPopupAjoutReservation").val();
	donnees.rueClient = $(idFormulaire).find("#rueClientPopupAjoutReservation")
			.val();
	donnees.complementAdresseClient = $(idFormulaire).find(
			"#complementAdresseClientPopupAjoutReservation").val();
	donnees.codePostalClient = $(idFormulaire).find(
			"#codePostalClientPopupAjoutReservation").val();
	donnees.villeClient = $(idFormulaire).find(
			"#villeClientPopupAjoutReservation").val();
	donnees.paysClient = $(idFormulaire).find(
			"#paysClientPopupAjoutReservation").val();
	donnees.portableClient = $(idFormulaire).find(
			"#portableClientPopupAjoutReservation").val();
	donnees.emailClient = $(idFormulaire).find(
			"#emailClientPopupAjoutReservation").val();
	donnees.pIdPresentClient = $(idFormulaire).find(
			"input[name='pIdPresentClientPopupAjoutRes']:checked").val();
	if (donnees.pIdPresentClient == undefined) {
		donnees.pIdPresentClient = "";
	}
	donnees.dateArriveeClient = $.datepicker
			.parseDate(DATE_FORMAT, $(idFormulaire).find(
					"#dateArriveeClientPopupAjoutReservation").val());
	donnees.dateDepartClient = $.datepicker.parseDate(DATE_FORMAT, $(
			idFormulaire).find("#dateDepartClientPopupAjoutReservation").val());
	donnees.nbAdultesClient = parseInt($(idFormulaire).find(
			"#nbAdultesClientPopupAjoutReservation").val());
	donnees.nbEnfantsClient = parseInt($(idFormulaire).find(
			"#nbEnfantsClientPopupAjoutReservation").val());
	donnees.nbAnimauxClient = parseInt($(idFormulaire).find(
			"#nbAnimauxClientPopupAjoutReservation").val());
	donnees.nbPetiteTenteClient = parseInt($(idFormulaire).find(
			"#nbPetiteTenteClientPopupAjoutReservation").val());
	donnees.nbGrandeTenteClient = parseInt($(idFormulaire).find(
			"#nbGrandeTenteClientPopupAjoutReservation").val());
	donnees.nbCaravaneClient = parseInt($(idFormulaire).find(
			"#nbCaravaneClientPopupAjoutReservation").val());
	donnees.nbVanClient = parseInt($(idFormulaire).find(
			"#nbVanClientPopupAjoutReservation").val());
	donnees.nbCampingCarClient = parseInt($(idFormulaire).find(
			"#nbCampingCarClientPopupAjoutReservation").val());
	donnees.electriciteClient = 0;
	if ($(idFormulaire).find("#electriciteClientPopupAjoutReservation").is(
			':checked') == true) {
		donnees.electriciteClient = 1;
	}
	donnees.nbNuitsVisiteursClient = $(idFormulaire).find(
			"#nbNuitsVisiteursClientPopupAjoutReservation").val();
	donnees.nbVehiculesSupp = $(idFormulaire).find(
			"#nbVehiculesSuppPopupAjoutReservation").val();
	donnees.roulotteRouge = '';
	if ($(idFormulaire).find("#roulotteRougePopupAjoutReservation").is(
			':checked') == true) {
		donnees.roulotteRouge = 1;
	}
	donnees.roulotteBleue = '';
	if ($(idFormulaire).find("#roulotteBleuePopupAjoutReservation").is(
			':checked') == true) {
		donnees.roulotteBleue = 1;
	}
	donnees.tenteSafari = '';
	if ($(idFormulaire).find("#tenteSafariPopupAjoutReservation")
			.is(':checked') == true) {
		donnees.tenteSafari = 1;
	}
	donnees.observationsClient = $(idFormulaire).find(
			"#observationsClientPopupAjoutReservation").val();
	donnees.idReservation = null;
	donnees.arrhes = parseFloat($(idFormulaire).find(
			"#arrhesClientPopupAjoutReservation").val());
	donnees.remiseExceptionnelle = parseFloat($(idFormulaire).find(
			"#remiseClientPopupAjoutReservation").val());
	donnees.numeroEmplacement = parseInt($(idFormulaire).find(
			"#numEmplacementPopupAjoutReservation").val());
	donnees.referenceFacture = $(idFormulaire).find(
			"#refFacturePopupAjoutReservation").val();

	// Mise en forme des valeurs par défaut
	if (isNaN(donnees.nbAdultesClient)) {
		donnees.nbAdultesClient = 0;
	}
	if (isNaN(donnees.nbEnfantsClient)) {
		donnees.nbEnfantsClient = 0;
	}
	if (isNaN(donnees.nbAnimauxClient)) {
		donnees.nbAnimauxClient = 0;
	}
	if (isNaN(donnees.nbPetiteTenteClient)) {
		donnees.nbPetiteTenteClient = 0;
	}
	if (isNaN(donnees.nbGrandeTenteClient)) {
		donnees.nbGrandeTenteClient = 0;
	}
	if (isNaN(donnees.nbCaravaneClient)) {
		donnees.nbCaravaneClient = 0;
	}
	if (isNaN(donnees.nbVanClient)) {
		donnees.nbVanClient = 0;
	}
	if (isNaN(donnees.nbCampingCarClient)) {
		donnees.nbCampingCarClient = 0;
	}
	if (isNaN(donnees.numeroEmplacement)) {
		donnees.numeroEmplacement = "";
	}
	if (isNaN(donnees.arrhes)) {
		donnees.arrhes = 0;
	}
	if (isNaN(donnees.remiseExceptionnelle)) {
		donnees.remiseExceptionnelle = 0;
	}

	// Sauvegarde en AJAX (création ou modification)
	sauvegardeReservationEnAjax(donnees, idBlocReservation);
}

/**
 * Fonction qui sauvegarde les informations d'une réservation
 * 
 * @author adupuis
 * @param string
 *            donneesSerialisees Données sérialisées à sauvegarder
 * @param string
 *            idBlocReservation Id du bloc de réservation (peut être vide)
 * @return array Renvoie toutes les données de la réservation dans un tableau
 */
function sauvegardeReservationEnAjax(donnees, idBlocReservation) {
	var strConcat = serialiserInfosReservation(donnees);

	$.ajax({
		type : "POST",
		url : "enregistrer_reservation.php",
		data : {
			reservation : strConcat
		},
		success : function(data) {
			if (data) {
				var tabDonnees = data.split("|");

				if (tabDonnees.length == 4) {
					// Id de la réservation
					donnees.idFiche = tabDonnees[0];

					// Référence du client
					donnees.refClient = tabDonnees[1];

					// Création d'un nouveau bloc de réservation
					idBlocReservation = creerBlocReservation(idBlocReservation,
							donnees);

					// Recalcul des statistiques
					calculStatistiquesParJour(tabDonnees[2], tabDonnees[3]);

					// Fermeture de la popup
					$("#popupAjoutModifReservation").dialog("close");

					alertPop(RESERVATION_ENREGISTREE);
				} else {
					alertPop(ERREUR_INCONNUE);
				}
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
 * Fonction qui supprime une réservation
 * 
 * @author adupuis
 * @param idBlocReservation
 *            string Id du bloc de réservation à supprimer
 */
function supprimerReservation(idBlocReservation) {
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
function sauvegarderNumeroEmplacementEnAjax(idFiche, numeroEmplacement) {

	// Sauvegarde en AJAX
	$.ajax({
		type : "POST",
		url : "enregistrer_emplacement.php",
		data : {
			refFiche : idFiche,
			numeroEmplacement : numeroEmplacement
		},
		success : function(data) {
			var strInfos = null;
			var tabInfos = null;

			if (data == true) {
				// On met à jour les données de l'emplacement
				strInfos = $('#popupDetailsReservation').find(
						"#infosPopupDetailsReservation").val();
				tabInfos = parseInfosReservation(strInfos);
				tabInfos.numeroEmplacement = numeroEmplacement;

				$("#" + tabInfos.idReservation).find(
						'input[id^="infosdraggable"]').val(
						serialiserInfosReservation(tabInfos));

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

/**
 * Fonction de mise à jour du champ nombre de nuités
 * 
 * @author adupuis
 * @returns integer Renvoie le nombre de nuités
 */
function recalculNbNuitesPopupAjoutRes() {
	var dateArrivee = $("#dateArriveeClientPopupAjoutReservation").datepicker(
			"getDate");
	var dateDepart = $("#dateDepartClientPopupAjoutReservation").datepicker(
			"getDate");
	var nbNuites = (dateDepart - dateArrivee) / 1000 / 3600 / 24;

	if ((dateArrivee != null) && (dateDepart != null) && (nbNuites > 0)) {
		$("label#nbNuites").html(nbNuites);
	} else {
		$("label#nbNuites").html("");
	}

	return nbNuites;
}

/**
 * Fonction qui charge et affiche les détails d'une réservation dans une popup
 * 
 * @author adupuis
 */
function afficherPopupDetails() {
	var reservationSelectionee = $(this);
	var tabDonnees = null;
	var popupDetailsReserv = $("#popupDetailsReservation");
	var divLogoLogement = $(popupDetailsReserv).find(
			"#caseLogoLogementPopupDetailsReservation");
	var strDonnees = null;

	// Récupération des données
	strDonnees = $(reservationSelectionee).find('input[id^="infosdraggable"]')
			.val();
	tabDonnees = parseInfosReservation(strDonnees);

	// Affichage des données
	// Partie Electricité
	if (tabDonnees.electriciteClient == "1") {
		$(popupDetailsReserv).find(
				"#caseElectricitePopupDetailsReservation span").css(
				"visibility", "visible");
	} else {
		$(popupDetailsReserv).find(
				"#caseElectricitePopupDetailsReservation span").css(
				"visibility", "hidden");
	}
	// Partie logo logement
	$(divLogoLogement).removeClass("logo-roulotte-rouge-moyen");
	$(divLogoLogement).removeClass("logo-roulotte-bleue-moyen");
	$(divLogoLogement).removeClass("logo-tente-safari-moyen");
	$(divLogoLogement).removeClass("logo-camping-car-moyen");
	$(divLogoLogement).removeClass("logo-van-moyen");
	$(divLogoLogement).removeClass("logo-caravane-moyen");
	$(divLogoLogement).removeClass("logo-grande-tente-moyen");
	$(divLogoLogement).removeClass("logo-petite-tente-moyen");
	if (tabDonnees.roulotteRouge > 0) {
		// Icone roulotte rouge
		$(divLogoLogement).addClass("logo-roulotte-rouge-moyen");
	} else if (tabDonnees.roulotteBleue > 0) {
		// Icone roulotte bleue
		$(divLogoLogement).addClass("logo-roulotte-bleue-moyen");
	} else if (tabDonnees.tenteSafari > 0) {
		// Icone tente safari
		$(divLogoLogement).addClass("logo-tente-safari-moyen");
	} else if (tabDonnees.nbCampingCarClient > 0) {
		// Icone camping-car
		$(divLogoLogement).addClass("logo-camping-car-moyen");
	} else if (tabDonnees.nbVanClient > 0) {
		// Icone van
		$(divLogoLogement).addClass("logo-van-moyen");
	} else if (tabDonnees.nbCaravaneClient > 0) {
		// Icone caravane
		$(divLogoLogement).addClass("logo-caravane-moyen");
	} else if (tabDonnees.nbGrandeTenteClient > 0) {
		// Icone grande tente
		$(divLogoLogement).addClass("logo-grande-tente-moyen");
	} else if (tabDonnees.nbPetiteTenteClient > 0) {
		// Icone petite tente
		$(divLogoLogement).addClass("logo-petite-tente-moyen");
	}
	// Partie Animal
	if (tabDonnees.nbAnimauxClient > 0) {
		$(popupDetailsReserv).find("#blocAnimalPopupDetailsReservation span")
				.css("visibility", "visible");
	} else {
		$(popupDetailsReserv).find("#blocAnimalPopupDetailsReservation span")
				.css("visibility", "hidden");
	}

	if ((tabDonnees.roulotteRouge > 0) || (tabDonnees.roulotteBleue > 0)
			|| (tabDonnees.tenteSafari > 0)) {
		$(popupDetailsReserv)
				.find("#caseNumEmplacementPopupDetailsReservation").css(
						'visibility', 'hidden');
	} else {
		$(popupDetailsReserv)
				.find("#caseNumEmplacementPopupDetailsReservation").css(
						'visibility', 'visible');
		// Partie numéro de l'emplacement
		$(popupDetailsReserv).find(
				"#numEmplacementClientPopupDetailsReservation").val(
				tabDonnees.numeroEmplacement);
	}
	// Partie numéro de fiche
	$(popupDetailsReserv).find("#caseNumFichePopupDetailsReservation").find(
			"#numFicheClientPopupDetailsReservation").html(tabDonnees.idFiche);

	// Partie détails du client
	$(popupDetailsReserv).find("#nomClientPopupDetailsReservation").html(
			tabDonnees.prenomClient + " " + tabDonnees.nomClient).attr("title",
			tabDonnees.prenomClient + " " + tabDonnees.nomClient);
	$(popupDetailsReserv).find("#nbAdultesClientPopupDetailsReservation").html(
			tabDonnees.nbAdultesClient);
	$(popupDetailsReserv).find("#nbEnfantsClientPopupDetailsReservation").html(
			tabDonnees.nbEnfantsClient);
	$(popupDetailsReserv).find("#nbAnimauxClientPopupDetailsReservation").html(
			tabDonnees.nbAnimauxClient);

	// Export de la facture
	$(popupDetailsReserv).find("a#boutonFactureReservation").off('click');
	$(popupDetailsReserv).find("a#boutonFactureReservation").on(
			'click',
			function(event) {
				var popupDetailsRes = $("#popupDetailsReservation");
				var strDonnees = null;

				event.stopPropagation();
				event.preventDefault();

				if (tabDonnees.referenceFacture !== '') {
					alertPop(REGENERER_FACTURE, TYPE_OUI_NON, function() {
						document.location = $(popupDetailsRes).find(
								"#urlExportFacturePopupDetailsReservation")
								.val()
								+ '?idFiche='
								+ tabDonnees.idFiche
								+ '&regenererFacture=true';
						$(this).dialog("close");
					}, function() {
						document.location = $(popupDetailsRes).find(
								"#urlExportFacturePopupDetailsReservation")
								.val()
								+ '?idFiche='
								+ tabDonnees.idFiche
								+ '&regenererFacture=false';
						$(this).dialog("close");
					});
				} else {
					// On marque la facture comme générée (avec une valeur
					// bidon)
					tabDonnees.referenceFacture = 'GENERATED';
					strDonnees = serialiserInfosReservation(tabDonnees);
					$(reservationSelectionee).find(
							'input[id^="infosdraggable"]').val(strDonnees);
					$(popupDetailsRes).find("#infosPopupDetailsReservation")
							.val(strDonnees);

					// Affichage de la facture
					document.location = $(popupDetailsRes).find(
							"#urlExportFacturePopupDetailsReservation").val()
							+ '?idFiche='
							+ tabDonnees.idFiche
							+ '&regenererFacture=true';
				}
			});

	// Partie observations
	$(popupDetailsReserv).find("#observationsPopupDetailsReservation").html(
			tabDonnees.observationsClient);
	// Sauvegarde des données
	$(popupDetailsReserv).find("#infosPopupDetailsReservation").val(strDonnees);
	sauvegardeEmplacement = $(popupDetailsReserv).find(
			"#numEmplacementClientPopupDetailsReservation").val();

	// Ouverture de la popup
	$(popupDetailsReserv).dialog("open");
}

/**
 * Lors du focus sur un des élements renvoyés par le recherche
 * 
 * @author adupuis
 * @param event
 * @param ui
 */
function onFocusNomPrenomPopupAjoutModifReservation(event, ui) {
	// On stoppe la propagation pour ne pas remplir le champ avec les données
	// brutes
	event.stopPropagation();
	event.preventDefault();
}

/**
 * Remplissage des champs du client quand on en sélectionne un par
 * autocomplétion
 * 
 * @author adupuis
 * @param event
 * @param ui
 */
function onSelectNomPrenomPopupAjoutModifReservation(event, ui) {
	// On stoppe la propagation pour ne pas remplir le champ avec les données
	// brutes
	event.stopPropagation();
	event.preventDefault();

	var popupAjoutModifRes = $("#popupAjoutModifReservation");

	objClient = parseInfosClient(ui.item.value);

	$(popupAjoutModifRes).find("#refClientPopupAjoutReservation").val(
			objClient.referenceClient);
	$(popupAjoutModifRes).find("#prenomClientPopupAjoutReservation").val(
			objClient.prenomClient);
	$(popupAjoutModifRes).find("#prenomSauvClientPopupAjoutReservation").val(
			objClient.prenomClient);
	$(popupAjoutModifRes).find("#nomClientPopupAjoutReservation").val(
			objClient.nomClient);
	$(popupAjoutModifRes).find("#nomSauvClientPopupAjoutReservation").val(
			objClient.nomClient);
	$(popupAjoutModifRes).find("#rueClientPopupAjoutReservation").val(
			objClient.rueClient);
	$(popupAjoutModifRes).find("#complementAdresseClientPopupAjoutReservation")
			.val(objClient.complementAdresseClient);
	$(popupAjoutModifRes).find("#codePostalClientPopupAjoutReservation").val(
			objClient.codePostalClient);
	$(popupAjoutModifRes).find("#villeClientPopupAjoutReservation").val(
			objClient.villeClient);
	$(popupAjoutModifRes).find("#paysClientPopupAjoutReservation").val(
			objClient.paysClient);
	$(popupAjoutModifRes).find("#portableClientPopupAjoutReservation").val(
			objClient.portableClient);
	$(popupAjoutModifRes).find("#emailClientPopupAjoutReservation").val(
			objClient.emailClient);

	if (objClient.referenceClient == '') {
		// Si la référence du client est vide, on masque le
		// bouton
		$("#popupAjoutModifReservation").find("#boutonCreerClient").css(
				"visibility", "hidden");
	} else {
		// Si la référence du client n'est pas vide, on
		// affiche le bouton
		$("#popupAjoutModifReservation").find("#boutonCreerClient").css(
				"visibility", "visible");
	}
}

/**
 * Remplissage des champs de la popup de modification d'un réservation
 * 
 * @author adupuis
 * @param array
 *            tabDonnees Tableau des données (le premier champ correspond à la
 *            version)
 */
function remplirChampsPopupModifReservation(tabDonnees) {
	var popupAjoutModifRes = $("#popupAjoutModifReservation");

	// Infos-Reservation + Infos-Client
	if (tabDonnees) {
		// Id Fiche
		$(popupAjoutModifRes).find("#idFichePopupAjoutReservation").val(
				tabDonnees.idFiche);
		// Référence Client
		$(popupAjoutModifRes).find("#refClientPopupAjoutReservation").val(
				tabDonnees.refClient);
		// Nom (+ sauvegarde)
		$(popupAjoutModifRes).find("#nomClientPopupAjoutReservation").val(
				tabDonnees.nomClient);
		$(popupAjoutModifRes).find("#nomSauvClientPopupAjoutReservation").val(
				tabDonnees.nomClient);
		// Prénom (+ sauvegarde)
		$(popupAjoutModifRes).find("#prenomClientPopupAjoutReservation").val(
				tabDonnees.prenomClient);
		$(popupAjoutModifRes).find("#prenomSauvClientPopupAjoutReservation")
				.val(tabDonnees.prenomClient);
		// Rue
		$(popupAjoutModifRes).find("#rueClientPopupAjoutReservation").val(
				tabDonnees.rueClient);
		// Complément adresse
		$(popupAjoutModifRes).find(
				"#complementAdresseClientPopupAjoutReservation").val(
				tabDonnees.complementAdresseClient);
		// Code postal
		$(popupAjoutModifRes).find("#codePostalClientPopupAjoutReservation")
				.val(tabDonnees.codePostalClient);
		// Ville
		$(popupAjoutModifRes).find("#villeClientPopupAjoutReservation").val(
				tabDonnees.villeClient);
		// Pays
		$(popupAjoutModifRes).find("#paysClientPopupAjoutReservation").val(
				tabDonnees.paysClient);
		// Portable
		$(popupAjoutModifRes).find("#portableClientPopupAjoutReservation").val(
				tabDonnees.portableClient);
		// Email
		$(popupAjoutModifRes).find("#emailClientPopupAjoutReservation").val(
				tabDonnees.emailClient);
		// Piece d'identité
		$(popupAjoutModifRes).find(
				'input[name="pIdPresentClientPopupAjoutRes"]').val(
				[ tabDonnees.pIdPresentClient ]);
		// Arrhes
		$(popupAjoutModifRes).find("#arrhesClientPopupAjoutReservation").val(
				tabDonnees.arrhes);
		// Remise exceptionnelle
		$(popupAjoutModifRes).find("#remiseClientPopupAjoutReservation").val(
				tabDonnees.remiseExceptionnelle);
		// Date d'arrivée
		$(popupAjoutModifRes).find("#dateArriveeClientPopupAjoutReservation")
				.val(
						$.datepicker.formatDate(DATE_FORMAT,
								tabDonnees.dateArriveeClient));
		// Date de départ
		$(popupAjoutModifRes).find("#dateDepartClientPopupAjoutReservation")
				.val(
						$.datepicker.formatDate(DATE_FORMAT,
								tabDonnees.dateDepartClient));
		// Nombre d'adultes
		$(popupAjoutModifRes).find("#nbAdultesClientPopupAjoutReservation")
				.val(tabDonnees.nbAdultesClient);
		// Nombre d'enfants
		$(popupAjoutModifRes).find("#nbEnfantsClientPopupAjoutReservation")
				.val(tabDonnees.nbEnfantsClient);
		// Nombre d'animaux
		$(popupAjoutModifRes).find("#nbAnimauxClientPopupAjoutReservation")
				.val(tabDonnees.nbAnimauxClient);
		// Nombre de petites tentes
		$(popupAjoutModifRes).find("#nbPetiteTenteClientPopupAjoutReservation")
				.val(tabDonnees.nbPetiteTenteClient);
		// Nombre de grandes tentes
		$(popupAjoutModifRes).find("#nbGrandeTenteClientPopupAjoutReservation")
				.val(tabDonnees.nbGrandeTenteClient);
		// Nombre de caravanes
		$(popupAjoutModifRes).find("#nbCaravaneClientPopupAjoutReservation")
				.val(tabDonnees.nbCaravaneClient);
		// Nombre de vans
		$(popupAjoutModifRes).find("#nbVanClientPopupAjoutReservation").val(
				tabDonnees.nbVanClient);
		// Nombre de camping cars
		$(popupAjoutModifRes).find("#nbCampingCarClientPopupAjoutReservation")
				.val(tabDonnees.nbCampingCarClient);
		// Roulotte rouge
		if (tabDonnees.roulotteRouge == 1) {
			// Bug Firefox => trigger click
			if ($(popupAjoutModifRes).find(
					"#roulotteRougePopupAjoutReservation").is(':checked') == false) {
				$(popupAjoutModifRes).find(
						"#roulotteRougePopupAjoutReservation").trigger('click');
			}
		} else {
			// Trigger click pour appeler les événements rattachés au clic
			if ($(popupAjoutModifRes).find(
					"#roulotteRougePopupAjoutReservation").is(':checked') == true) {
				$(popupAjoutModifRes).find(
						"#roulotteRougePopupAjoutReservation").trigger("click");
			}
		}
		// Roulotte bleue
		if (tabDonnees.roulotteBleue == 1) {
			// Bug Firefox => trigger click
			if ($(popupAjoutModifRes).find(
					"#roulotteBleuePopupAjoutReservation").is(':checked') == false) {
				$(popupAjoutModifRes).find(
						"#roulotteBleuePopupAjoutReservation").trigger('click');
			}
		} else {
			// Trigger click pour appeler les événements rattachés au clic
			if ($(popupAjoutModifRes).find(
					"#roulotteBleuePopupAjoutReservation").is(':checked') == true) {
				$(popupAjoutModifRes).find(
						"#roulotteBleuePopupAjoutReservation").trigger("click");
			}
		}
		// Tente safari
		if (tabDonnees.tenteSafari == 1) {
			// Bug Firefox => trigger click
			if ($(popupAjoutModifRes).find("#tenteSafariPopupAjoutReservation")
					.is(':checked') == false) {
				$(popupAjoutModifRes).find("#tenteSafariPopupAjoutReservation")
						.trigger('click');
			}
		} else {
			// Trigger click pour appeler les événements rattachés au clic
			if ($(popupAjoutModifRes).find("#tenteSafariPopupAjoutReservation")
					.is(':checked') == true) {
				$(popupAjoutModifRes).find("#tenteSafariPopupAjoutReservation")
						.trigger("click");
			}
		}
		// Electricité
		if (tabDonnees.electriciteClient == 1) {
			// Bug Firefox => trigger click
			if ($(popupAjoutModifRes).find(
					"#electriciteClientPopupAjoutReservation").is(':checked') == false) {
				$(popupAjoutModifRes).find(
						"#electriciteClientPopupAjoutReservation").trigger(
						'click');
			}
		} else {
			// Trigger click pour appeler les événements rattachés au clic
			if ($(popupAjoutModifRes).find(
					"#electriciteClientPopupAjoutReservation").is(':checked') == true) {
				$(popupAjoutModifRes).find(
						"#electriciteClientPopupAjoutReservation").trigger(
						"click");
			}
		}
		// Nombre de nuités visiteur
		$(popupAjoutModifRes).find(
				"#nbNuitsVisiteursClientPopupAjoutReservation").val(
				tabDonnees.nbNuitsVisiteursClient);
		// Nombre de véhicules supplémentaires
		$(popupAjoutModifRes).find("#nbVehiculesSuppPopupAjoutReservation")
				.val(tabDonnees.nbVehiculesSupp);
		// Observations
		$(popupAjoutModifRes).find("#observationsClientPopupAjoutReservation")
				.val(tabDonnees.observationsClient);
		// Id du bloc de réservation
		$(popupAjoutModifRes).find("#idReservationPopupAjoutReservation").val(
				tabDonnees.idReservation);
		// Numéro d'emplacement
		$(popupAjoutModifRes).find("#numEmplacementPopupAjoutReservation").val(
				tabDonnees.numeroEmplacement);
		// Référence facture
		$(popupAjoutModifRes).find("#refFacturePopupAjoutReservation").val(
				tabDonnees.referenceFacture);

		// Calcul du nombre de nuités
		recalculNbNuitesPopupAjoutRes();
	} else {
		viderChampsPopup("popupAjoutModifReservation");
	}
}

/**
 * Fonction découpant les données concaténées dans la chaine passée en
 * paramètre. Renvoie un tableau avec les données
 * 
 * @author adupuis
 * @param string
 *            strInfos Concaténation des infos (le séparateur est le |) Peut
 *            être un tableau ou un string
 * @returns Array Renvoie un tableau avec les données parsées
 */
function parseInfosReservation(strInfos) {
	var tabDonnees = strInfos.split("|");
	var tabRetour = Array();

	// Infos-Reservation
	if (tabDonnees[0] == "v1.0") {
		// Version
		tabRetour.version = tabDonnees[0];
		// Référence client
		tabRetour.refClient = tabDonnees[1];
		// Id Fiche
		tabRetour.idFiche = tabDonnees[2];
		// Nom
		tabRetour.nomClient = tabDonnees[3];
		// Prénom
		tabRetour.prenomClient = tabDonnees[4];
		// Rue
		tabRetour.rueClient = tabDonnees[5];
		// Complément adresse
		tabRetour.complementAdresseClient = tabDonnees[6];
		// Code postal
		tabRetour.codePostalClient = tabDonnees[7];
		// Ville
		tabRetour.villeClient = tabDonnees[8];
		// Pays
		tabRetour.paysClient = tabDonnees[9];
		// Portable
		tabRetour.portableClient = tabDonnees[10];
		// Email
		tabRetour.emailClient = tabDonnees[11];
		// Piece d'identité
		tabRetour.pIdPresentClient = tabDonnees[12];
		// Date d'arrivée
		tabRetour.dateArriveeClient = $.datepicker.parseDate(DATE_FORMAT,
				tabDonnees[13]);
		// Date de départ
		tabRetour.dateDepartClient = $.datepicker.parseDate(DATE_FORMAT,
				tabDonnees[14]);
		// Nombre d'adultes
		tabRetour.nbAdultesClient = tabDonnees[15];
		// Nombre d'enfants
		tabRetour.nbEnfantsClient = tabDonnees[16];
		// Nombre d'animaux
		tabRetour.nbAnimauxClient = tabDonnees[17];
		// Nombre de petites tentes
		tabRetour.nbPetiteTenteClient = tabDonnees[18];
		// Nombre de grandes tentes
		tabRetour.nbGrandeTenteClient = tabDonnees[19];
		// Nombre de caravanes
		tabRetour.nbCaravaneClient = tabDonnees[20];
		// Nombre de vans
		tabRetour.nbVanClient = tabDonnees[21];
		// Nombre de camping cars
		tabRetour.nbCampingCarClient = tabDonnees[22];
		// Electricité
		tabRetour.electriciteClient = tabDonnees[23];
		// Nombre de nuités visiteur
		tabRetour.nbNuitsVisiteursClient = tabDonnees[24];
		// Nombre de véhicules supplémentaires
		tabRetour.nbVehiculesSupp = tabDonnees[25];
		// Observations
		tabRetour.observationsClient = tabDonnees[26];
		// Id du bloc de réservation
		tabRetour.idReservation = tabDonnees[27];
		// Arrhes sur la réservation
		tabRetour.arrhes = tabDonnees[28];
		// Numéro d'emplacement de la réservation
		tabRetour.numeroEmplacement = tabDonnees[29];
		// Roulotte rouge
		tabRetour.roulotteRouge = tabDonnees[30];
		// Roulotte bleue
		tabRetour.roulotteBleue = tabDonnees[31];
		// Référence facture
		tabRetour.referenceFacture = tabDonnees[32];
		// Remise exceptionnelle sur la réservation
		tabRetour.remiseExceptionnelle = tabDonnees[33];
		// Tente safari
		tabRetour.tenteSafari = tabDonnees[34];
	}

	return tabRetour;
}

/**
 * Fonction découpant les données concaténées dans la chaine passée en
 * paramètre. Renvoie un tableau avec les données
 * 
 * @author adupuis
 * @param strInfos
 *            Concaténation des infos (le séparateur est le |)
 * @returns Array Renvoie un tableau avec les données parsées
 */
function parseInfosClient(strInfos) {
	var tabDonnees = strInfos.split("|");
	var tabRetour = Array();

	// Infos-Client
	if (tabDonnees[0] == "v1.0") {
		// Version
		tabRetour.version = tabDonnees[0];
		// Référence du client
		tabRetour.referenceClient = tabDonnees[1];
		// Nom
		tabRetour.nomClient = tabDonnees[2];
		// Prénom
		tabRetour.prenomClient = tabDonnees[3];
		// Rue
		tabRetour.rueClient = tabDonnees[4];
		// Complément adresse
		tabRetour.adresseClient = tabDonnees[5];
		// Code postal
		tabRetour.codePostalClient = tabDonnees[6];
		// Ville
		tabRetour.villeClient = tabDonnees[7];
		// Pays
		tabRetour.paysClient = tabDonnees[8];
		// Telephone
		tabRetour.telephoneClient = tabDonnees[9];
		// Portable
		tabRetour.portableClient = tabDonnees[10];
		// Email
		tabRetour.emailClient = tabDonnees[11];
		// Date de création
		tabRetour.dateCreationClient = tabDonnees[12];
		// Date de modification
		tabRetour.dateModificationClient = tabDonnees[13];
	}

	return tabRetour;
}

/**
 * Génère la chaine de caractère concaténant les infos de réservation
 * 
 * @author adupuis
 * @param objInfos
 *            Objet (tableau indexé) contenant les infos à sauvegarder
 * @return string Renvoie la chaine concaténée
 */
function serialiserInfosReservation(objInfos) {

	// Infos-Reservation
	return objInfos.version + separateur + objInfos.refClient + separateur
			+ objInfos.idFiche + separateur + objInfos.nomClient + separateur
			+ objInfos.prenomClient + separateur + objInfos.rueClient
			+ separateur + objInfos.complementAdresseClient + separateur
			+ objInfos.codePostalClient + separateur + objInfos.villeClient
			+ separateur + objInfos.paysClient + separateur
			+ objInfos.portableClient + separateur + objInfos.emailClient
			+ separateur + objInfos.pIdPresentClient + separateur
			+ $.datepicker.formatDate(DATE_FORMAT, objInfos.dateArriveeClient)
			+ separateur
			+ $.datepicker.formatDate(DATE_FORMAT, objInfos.dateDepartClient)
			+ separateur + objInfos.nbAdultesClient + separateur
			+ objInfos.nbEnfantsClient + separateur + objInfos.nbAnimauxClient
			+ separateur + objInfos.nbPetiteTenteClient + separateur
			+ objInfos.nbGrandeTenteClient + separateur
			+ objInfos.nbCaravaneClient + separateur + objInfos.nbVanClient
			+ separateur + objInfos.nbCampingCarClient + separateur
			+ objInfos.electriciteClient + separateur
			+ objInfos.nbNuitsVisiteursClient + separateur
			+ objInfos.nbVehiculesSupp + separateur
			+ objInfos.observationsClient + separateur + objInfos.idReservation
			+ separateur + objInfos.arrhes + separateur
			+ objInfos.numeroEmplacement + separateur + objInfos.roulotteRouge
			+ separateur + objInfos.roulotteBleue + separateur
			+ objInfos.referenceFacture + separateur
			+ objInfos.remiseExceptionnelle + separateur
			+ objInfos.tenteSafari + separateur;
}

/**
 * Vide les champs de la popup de modification d'une réservation
 * 
 * @author adupuis
 * @param string
 *            idPopupAVider Id de la popup avec les champs à vider
 */
function viderChampsPopup(idPopupAVider) {
	// Vidage des champs cachés
	$("#" + idPopupAVider).find('input[type="hidden"]').val("");

	// Vidage des champs input text
	$("#" + idPopupAVider).find('input[type="text"]').val("");

	// Vidage des champs input radio
	$("#" + idPopupAVider).find('input[type="radio"]').val([ "" ]);

	// Vidage des champs input checkbox (trigger click pour appeler les
	// événements rattachés au clic)
	$("#" + idPopupAVider).find('input[type="checkbox"]:checked').attr(
			"checked", true);
	$("#" + idPopupAVider).find('input[type="checkbox"]:checked').trigger(
			"click");

	// Vidage des champs textarea
	$("#" + idPopupAVider).find('textarea').val("");

	// On cache le bouton imprimer
	$("#" + idPopupAVider).find("#boutonImprimerClient").attr('href', '');
	$("#" + idPopupAVider).find("#boutonImprimerClient").css("visibility",
			"hidden");

	// On cache le bouton créer client
	$("#popupAjoutModifReservation").find("#boutonCreerClient").css(
			"visibility", "hidden");
}

/**
 * Vide les champs spécifiques au client de la popup de modification d'un
 * réservation
 * 
 * @author adupuis
 */
function viderChampsClientPopup() {
	var popupAjoutModifRes = $("#popupAjoutModifReservation");

	$(popupAjoutModifRes).find("#refClientPopupAjoutReservation").val('');
	$(popupAjoutModifRes).find("#prenomClientPopupAjoutReservation").val('');
	$(popupAjoutModifRes).find("#nomClientPopupAjoutReservation").val('');
	$(popupAjoutModifRes).find("#rueClientPopupAjoutReservation").val('');
	$(popupAjoutModifRes).find("#complementAdresseClientPopupAjoutReservation")
			.val('');
	$(popupAjoutModifRes).find("#codePostalClientPopupAjoutReservation")
			.val('');
	$(popupAjoutModifRes).find("#villeClientPopupAjoutReservation").val('');
	$(popupAjoutModifRes).find("#paysClientPopupAjoutReservation").val('');
	$(popupAjoutModifRes).find("#portableClientPopupAjoutReservation").val('');
	$(popupAjoutModifRes).find("#emailClientPopupAjoutReservation").val('');

	// On cache le bouton créer client
	$("#popupAjoutModifReservation").find("#boutonCreerClient").css(
			"visibility", "hidden");
}