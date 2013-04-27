//Contient les lignes du tableau
var tabLignesCalendrier = Array();
// Contient les colonnes du tableau
var tabColonnesCalendrier = Array();
// Contient les coordonées d'origine des blocs des réservation
var tabCoordOrigineBloc = Array();

/**
 * @author adupuis
 */
$(document).ready(function() {

	$(".draggable").draggable();

	// Mise à jour des variables globales référencant les lignes et les colonnes
	// du tableau
	majLignesTableau();
	majColonnesTableau();

	// Evenement dès qu'on commence à déplacer un blos de réservation
	$(".draggable").on("dragstart", onDragStartBlocReservation);

	// Evenement quand on déplace un bloc de réservation
	$(".draggable").on("drag", onDragBlocReservation);

	// Evenement à la fin du déplacement d'un bloc de réservation
	$(".draggable").on("dragstop", onDragStopBlocReservation);

	// goto popup_ajout_reservation.js
	relierEvenementsAjoutReservation();
	
	// goto popup_reglages.js
	relierEvenementsPopupReglages();
});

/**
 * Met à jour la variable globale référencant les lignes du tableau de
 * réservations
 * 
 * @author adupuis
 */
function majLignesTableau() {
	var jTabLignes = $(".ligne_calendrier");
	var tabLigne = null;
	var element = null;

	// On liste toutes les lignes dans un tableau
	for ( var i = 0; i < $(jTabLignes).size(); i++) {
		tabLigne = Array();
		element = $(jTabLignes).eq(i);

		// On enregistre l'id de la ligne
		tabLigne["id"] = $(element).attr("id");
		// On enregistre la position supérieure (top)
		tabLigne["top"] = $(element).position().top;
		// On enregistre la position gauche (left)
		tabLigne["left"] = $(element).position().left;
		// On enregistre la hauteur (height)
		if ((i + 1) < $(jTabLignes).size()) {
			tabLigne["height"] = $(jTabLignes).eq(i + 1).position().top
					- $(element).position().top;
		} else {
			tabLigne["height"] = $(element).height();
		}
		// On enregistre la largeur (width)
		tabLigne["width"] = $(element).width();

		tabLignesCalendrier[i] = tabLigne;
	}
}

/**
 * Met à jour la variable globale référencant les colonnes du tableau de
 * réservations
 * 
 * @author adupuis
 */
function majColonnesTableau() {
	var jTabCellules = $('#tableauCalend th.header_jours_calendrier');
	var jTabStatPersonnes = $('td.cellule_stat_personnes_calendrier');
	var jTabStatEmplacements = $('td.cellule_stat_emplacements_calendrier');
	var tabColonne = null;
	var element = null;

	// On liste toute les colonnes dans un tableau
	for ( var i = 0; i < $(jTabCellules).size(); i++) {
		tabColonne = Array();
		element = $(jTabCellules).eq(i);

		// On enregistre l'id de la colonne
		tabColonne["id"] = $(element).attr("id");
		// On enregistre la position supérieure (top)
		tabColonne["top"] = $(element).position().top;
		// On enregistre la position gauche (left)
		tabColonne["left"] = $(element).position().left;
		// On enregistre la hauteur (height)
		tabColonne["height"] = $(element).height();
		// On enregistre la largeur (width)
		if ((i + 1) < $(jTabCellules).size()) {
			tabColonne["width"] = $(jTabCellules).eq(i + 1).position().left
					- $(element).position().left;
		} else {
			tabColonne["width"] = $(element).width();
		}

		// Date de la colonne
		tabColonne["date"] = $(element).find('input.date_jour_calendrier')
				.val();
		// Id de la case pour les stats du nombre de personnes
		tabColonne["idStatPersonnes"] = $(jTabStatPersonnes).eq(i).attr("id");
		// Id de la case pour les stats du nombre d'emplacements
		tabColonne["idStatEmplacements"] = $(jTabStatEmplacements).eq(i).attr(
				"id");

		tabColonnesCalendrier[i] = tabColonne;
	}
}

/**
 * @author adupuis
 * @param elemUi
 *            Object Objet renvoyé par l'événement jquery drag
 * @param tabLignes
 *            Array Tableau contenant les coordonées des lignes du tableau
 * @param tabColonnes
 *            Array Tableau contenant les coordonées des colonnes du tableau
 * @return Array Renvoie la ligne, la colonne et la date si l'objet est dans le
 *         tableau, null sinon
 */
function estDansTableau(elemUi, tabLignes, tabColonnes) {
	var ligneBloc = null;
	var colonneBloc = null;
	var retour = null;

	// On cherche si le bloc de réservation de trouve dans une des lignes du
	// tableau
	$(tabLignes)
			.each(
					function(index, ligne) {
						var hauteur = parseInt(ligne["height"], 10);
						var ligneTop = parseInt(ligne["top"], 10);

						if ((elemUi.position.top >= ligneTop)
								&& (elemUi.position.top <= (ligneTop + hauteur))) {
							ligneBloc = index;
						} else {
							// Si le bloc est en dehors du tableau, il a droit a
							// une marge avant d'être
							// considéré vraiment en dehors
							if ((index == 0)
									&& (elemUi.position.top >= ligneTop
											- (hauteur / 2))
									&& (elemUi.position.top <= (ligneTop + hauteur))) {
								ligneBloc = index;
							}
						}
					});

	// On cherche si le bloc de réservation de trouve dans une des colonne du
	// tableau
	$(tabColonnes)
			.each(
					function(index, colonne) {
						var colonneLeft = parseInt(colonne["left"], 10);
						var largueur = parseInt(colonne["width"], 10);

						if ((elemUi.position.left >= colonneLeft)
								&& (elemUi.position.left <= (colonneLeft + largueur))) {
							colonneBloc = index;
						} else {
							// Si le bloc est en dehors du tableau, il a droit a
							// une marge avant d'être
							// considéré vraiment en dehors
							if ((index == 0)
									&& (elemUi.position.left >= colonneLeft
											- (largueur / 2))
									&& (elemUi.position.left <= (colonneLeft + largueur))) {
								colonneBloc = index;
							}
						}
					});

	if ((ligneBloc != null) && (colonneBloc != null)) {
		retour = Array();
		retour["ligne"] = ligneBloc;
		retour["colonne"] = colonneBloc;
	}
	return retour;
}

/**
 * @author adupuis
 * @param tabColonnes
 *            Array Tableau contenant les coordonées des colonnes du tableau
 * @param tabBlocs
 *            Array Tableau d'objets JQuery contenant les blocs de réservation
 */
function optimiserTabReservation(tabColonnes, tabBlocs) {
	var tabLeft = {};
	var tabRight = {};
	var tabLignes = Array();
	var dernierElem = Array();
	var i;
	var tabLignesHtml = null;
	var nbLignesHtml = null;
	var tabTemp = null;

	// On crée un tableau contenant les valeurs des lefts des blocs
	$(tabBlocs).each(
			function(index, element) {
				tabLeft[$(element).attr("id")] = $(element).css("left");
				tabRight[$(element).attr("id")] = parseInt($(element).css(
						"left"), 10)
						+ parseInt($(element).css("width"), 10);
			});

	// Trie par ordre croissant du tableau le tableau left
	tabLeft = triTableauIndexEntier(tabLeft);

	// Calcul de l'optimisation des lignes pour que tous les blocs rentrent sans
	// se chevaucher
	if (estVide(tabLeft) != true) {
		$.each(tabLeft, function(index, element) {
			if ((tabLignes.length > 0) && (dernierElem.length > 0)) {
				i = 0;
				// On regarde si le left de l'élément actuel est inférieur au
				// right de l'élément
				// précédent (=> chevauchement)
				while ((dernierElem[i] != undefined)
						&& (tabLeft[index] < tabRight[dernierElem[i]])) {
					i = i + 1;
				}
				if (tabLignes[i] == undefined) {
					// Si la cellule du tableau n'existe pas => chevauchement
					tabLignes[i] = index;
				} else {
					tabLignes[i] = tabLignes[i] + "|" + index;
				}
				dernierElem[i] = index;
			} else {
				// Initialisation du tableau
				tabLignes[0] = index;
				dernierElem[0] = index;
			}
		});
	}

	// Création/suppression des lignes après optimisation
	tabLignesHtml = $('tr[id^="ligneCalend_"]');
	nbLignesHtml = $(tabLignesHtml).size();
	if (tabLignes.length > nbLignesHtml) {
		// On rajoute des lignes à la fin s'il n'y en a pas assez
		for ( var i = 0; i < (tabLignes.length - nbLignesHtml); i++) {
			tabLignesHtml.eq(nbLignesHtml - 1).clone().insertAfter(
					tabLignesHtml.eq(nbLignesHtml - 1));
		}
	} else {
		if (tabLignes.length < nbLignesHtml) {
			// On supprime des lignes s'il y en a trop (en commencant par les
			// dernières)
			for ( var i = 0; i < (nbLignesHtml - tabLignes.length); i++) {
				tabLignesHtml.eq(nbLignesHtml - 1).remove();
			}
		}
	}

	// On met à jour la variable globale qui référence les lignes du tableau
	majLignesTableau();

	// On positionne les blocs dans les lignes appropriées
	for ( var i = 0; i < tabLignes.length; i++) {
		if ((tabLignes[i] != undefined) && (tabLignes[i] != null)) {
			tabTemp = tabLignes[i].split('|');
			for ( var j = 0; j < tabTemp.length; j++) {
				$("#" + tabTemp[j]).offset({
					top : tabLignesCalendrier[i]["top"]
				});
			}
		}
	}
}

/**
 * Permet de placer horizontalement un bloc de réservation à la date spécifiée
 * 
 * @author adupuis
 * @param jQuery
 *            blocReservation Bloc de réservation à placer. Objet jQuery
 * @param Date
 *            dateDebut Date de début de la réservation.
 * @param boolean
 *            mettreAJourDonnees Mise à jour des données cachées pour les dates
 *            de réservation
 * @returns Boolean Renvoie true ou false suivant la réussite de l'opération
 */
function placerBlocReservationDate(blocReservation, dateDebut,
		mettreAJourDonnees) {
	var inputDateDebut = $('input[class="date_jour_calendrier"][value="'
			+ $.datepicker.formatDate(DATE_FORMAT, dateDebut) + '"]');
	var thDateDebut = $(inputDateDebut).parent();
	var largeurThDateDebut = null;
	var offsetGaucheThDateDebut = null;
	var tabDonnees = null;
	var dateArriveeClient = null;
	var dateDepartClient = null;
	var differenceDate = null;
	var retour = false;

	if ((typeof dateDebut != "undefined") && (dateDebut != null)) {
		if ($(thDateDebut).size() == 1) {
			// On place le bloc au milieu de la colonne de la date de début
			largeurThDateDebut = $(thDateDebut).width();
			offsetGaucheThDateDebut = $(thDateDebut).position().left;
			$(blocReservation).offset({
				left : (offsetGaucheThDateDebut + (largeurThDateDebut / 2))
			});
			retour = true;
		} else {
			// Si la date de la réservation n'est pas dans le tableau
			$(blocReservation).remove();
			alertPop(RESERVATION_EN_DEHORS_TABLEAU);
		}
	}

	// Mise à jour des dates de réservation
	if (mettreAJourDonnees == true) {
		tabDonnees = parseInfosReservation($(blocReservation).find(
				'input[id^="infosdraggable"]').val());
		dateArriveeClient = tabDonnees.dateArriveeClient;
		dateDepartClient = tabDonnees.dateDepartClient;
		differenceDate = dateDebut - dateArriveeClient;
		tabDonnees.dateArriveeClient = dateDebut;
		tabDonnees.dateDepartClient = new Date(dateDepartClient.getTime()
				+ differenceDate);
		// On sauvegarde les nouvelles valeurs (goto popup_ajout_reservation.js)
		serialiserInfosReservation(blocReservation, tabDonnees);
	}

	return retour;
}

/**
 * Fonction déclenchée lors de démarrage du drag & drop d'un bloc de réservation
 * 
 * @author adupuis
 * @param event
 * @param ui
 */
function onDragStartBlocReservation(event, ui) {
	var tabPos = Array();
	var idBloc = $(this).attr("id");

	// Au démarrage du drag, on enregistre la position de départ
	tabPos["top"] = $(this).css("top");
	tabPos["left"] = $(this).css("left");
	if (idBloc) {
		tabCoordOrigineBloc[idBloc] = tabPos;
	}
}

/**
 * Fonction déclenchée pendant le déplacement (drag & drop) d'un bloc de
 * réservation
 * 
 * @author adupuis
 * @param event
 * @param ui
 */
function onDragBlocReservation(event, ui) {
	var aDansTab = null;

	aDansTab = estDansTableau(ui, tabLignesCalendrier, tabColonnesCalendrier);

	// Quand un bloc de réservation est dans le tableau, on fige le déplacement
	// vertical
	if (aDansTab != null) {
		$(this).offset({
			top : tabLignesCalendrier[aDansTab["ligne"]]["top"]
		});
		$(this).draggable("option", "axis", "x");
	} else {
		$(this).draggable("option", "axis", false);
	}
}

/**
 * Fonction déclenchée à la fin du drag & drop d'un bloc de réservation
 * 
 * @author adupuis
 * @param event
 * @param ui
 */
function onDragStopBlocReservation(event, ui) {
	var idElem = $(this).attr("id");
	var topBlock = parseInt($(this).css("top"), 10);
	var bottomBlock = parseInt($(this).css("top"), 10)
			+ parseInt($(this).css("height"), 10);
	var leftBlock = parseInt($(this).css("left"), 10);
	var rightBlock = parseInt($(this).css("left"), 10)
			+ parseInt($(this).css("width"), 10);
	var besoinOptimTab = false;
	var blocRes = $(this);

	// On vérifie que le bloc est dans le tableau
	aDansTab = estDansTableau(ui, tabLignesCalendrier, tabColonnesCalendrier);

	if (aDansTab != null) {
		// Confirmation de changement de la date de réservation
		alertPop(
				DEPLACER_DATE_RESERVATION,
				TYPE_OUI_NON,
				function() {
					var rPlacer = null;
					var tabDonnees = parseInfosReservation($(blocRes).find(
							'input[id^="infosdraggable"]').val());
					var nbNuites = (tabDonnees.dateDepartClient - tabDonnees.dateArriveeClient) / 1000 / 3600 / 24;

					// Nouvelles date d'arrivée et de départ
					tabDonnees.dateArriveeClient = $.datepicker
							.parseDate(
									DATE_FORMAT,
									$("#tableauCalend")
											.find(
													"th#"
															+ tabColonnesCalendrier[aDansTab["colonne"]]["id"])
											.find("input.date_jour_calendrier")
											.val());

					tabDonnees.dateDepartClient
							.setTime(tabDonnees.dateArriveeClient.getTime()
									+ (nbNuites * 1000 * 3600 * 24));

					// On aligne le bloc avec les cellules du tableau
					$(blocRes).offset({
						top : tabLignesCalendrier[aDansTab["ligne"]]["top"]
					});
					rPlacer = placerBlocReservationDate(
							$(blocRes),
							$.datepicker
									.parseDate(
											DATE_FORMAT,
											$("#tableauCalend")
													.find(
															"th#"
																	+ tabColonnesCalendrier[aDansTab["colonne"]]["id"])
													.find(
															"input.date_jour_calendrier")
													.val()), true);

					if (rPlacer == true) {
						optimiserTabReservation(tabColonnesCalendrier,
								$(".draggable"));
					}

					// On sauvegarde les modifications
					sauvegardeReservationEnAjax(tabDonnees, $(blocRes).attr(
							"id"));

					// Recalcul des statistiques
					calculStatistiquesParJour();

					$(this).dialog("close");
				},
				function() {
					// On repositionne le bloc à sa position de départ
					$(blocRes).css("top", tabCoordOrigineBloc[idElem]["top"]);
					$(blocRes).css("left", tabCoordOrigineBloc[idElem]["left"]);
					$(this).dialog("close");
				});
	} else {
		// Si le bloc est positionné en dehors du tableau, on le remet à sa
		// place d'origine
		$(this).css("top", tabCoordOrigineBloc[idElem]["top"]);
		$(this).css("left", tabCoordOrigineBloc[idElem]["left"]);
	}
}

/**
 * Calcul le nombre de personnes et d'emplacement par jour
 * 
 * @author adupuis
 */
function calculStatistiquesParJour() {
	var tableauStat = Array();
	var stats = null;
	var elemUi = Array();
	var temp = Array();
	var nombrePersonnes = 0;
	var nombreEmplacements = 0;
	var nbNuites = 0;
	var tabDonnees = null;
	var moyennePersonnes = 0;
	var nbJoursMoyenne = 0;
	var demain = new Date();
	demain = demain.setDate(demain.getDate() + 1);
	var dateColonne = null;

	// Initialisation
	temp['top'] = null;
	temp['left'] = null;
	elemUi['position'] = temp;

	// Le tableau des colonnes doit être initialisé
	if ((tabColonnesCalendrier == null) || (tabColonnesCalendrier.length == 0)) {
		majColonnesTableau();
	}

	// Initialisation du tableau des stats
	// Le tableau des stats doit avoir la même dimension que le nombre de
	// colonnes
	for ( var i = 0; i < tabColonnesCalendrier.length; i++) {
		stats = Array();

		stats['personnes'] = 0;
		stats['emplacements'] = 0;
		tableauStat[i] = stats;
	}

	// On récupère toute les réservations pour faire le calcul des stats
	$(".draggable")
			.each(
					function(index, element) {
						tabDonnees = parseInfosReservation($(element).find(
								'input[id^="infosdraggable"]').val());

						nombrePersonnes = (parseInt(tabDonnees.nbAdultesClient) + parseInt(tabDonnees.nbEnfantsClient));
						// Un seul emplacement par réservation
						nombreEmplacements = 1;

						// Construction de l'objet à passer en paramètre
						elemUi['position']['top'] = $(element).position().top;
						elemUi['position']['left'] = $(element).position().left;

						aDansTab = estDansTableau(elemUi, tabLignesCalendrier,
								tabColonnesCalendrier);

						if (aDansTab != null) {

							// Calcul du nombre de nuitées (on divise la largeur
							// de la réservation par
							// la largeur d'une colonne)
							nbNuites = Math
									.round(parseInt($(element).width())
											/ parseInt(tabColonnesCalendrier[aDansTab["colonne"]]["width"]));

							// Affectation du nombre de personnes et
							// d'emplacements aux colonnes
							if ((isNaN(nbNuites) === false) && (nbNuites > 0)) {
								for ( var i = aDansTab["colonne"]; i < (aDansTab["colonne"] + nbNuites); i++) {
									if (i < tableauStat.length) {
										tableauStat[i]['personnes'] = (tableauStat[i]['personnes'] + nombrePersonnes);
										tableauStat[i]['emplacements'] = (tableauStat[i]['emplacements'] + nombreEmplacements);
									}
								}
							}
						}

					});

	// On affiche les résultats des stats
	$(tableauStat)
			.each(
					function(index, element) {
						dateColonne = $.datepicker.parseDate(DATE_FORMAT,
								tabColonnesCalendrier[index]["date"]);

						// Nombre de personnes par jour
						$(
								'#'
										+ tabColonnesCalendrier[index]['idStatPersonnes']
										+ ' label').html(element['personnes']);

						// Nombre d'emplacements par jour
						$(
								'#'
										+ tabColonnesCalendrier[index]['idStatEmplacements']
										+ ' label').html(
								element['emplacements']);

						// Pour le calcul de la moyenne du jour
						if (dateColonne < demain) {
							moyennePersonnes = moyennePersonnes
									+ parseInt(element['personnes']);
							nbJoursMoyenne = nbJoursMoyenne + 1;
						}
					});

	// Calcul de la moyenne de personnes par jour
	if (nbJoursMoyenne > 0) {
		moyennePersonnes = Math
				.round((moyennePersonnes / nbJoursMoyenne) * 100) / 100;
	} else {
		moyennePersonnes = 0;
	}
	$('label#moyennePersonnesJusquaAujourdhui').html(moyennePersonnes);
}

/**
 * Trie un tableau indexé contenant des entiers par ordre croissant (du plus
 * petit au plus grand)
 * 
 * @author adupuis
 * @param tableauATrier
 *            Array Tableau indéxé d'entiers à trier
 * @return Array Renvoie le tableau trié
 */
function triTableauIndexEntier(tableauATrier) {
	var newTab = {};
	var oldTab = tableauATrier;
	var valPlusPetite = null;
	var indexPlusPetit = null;

	while (estVide(oldTab) == false) {
		// On recherche la valeur la plus petite
		$.each(tableauATrier, function(index, value) {
			if ((valPlusPetite > parseInt(value, 10))
					|| (valPlusPetite == null)) {
				valPlusPetite = parseInt(value, 10);
				indexPlusPetit = index;
			}
		});
		// Ajout de l'élément au tableau de retour
		newTab[indexPlusPetit] = valPlusPetite;
		// Suppression de l'ancien tableau
		delete oldTab[indexPlusPetit];

		valPlusPetite = null;
		indexPlusPetit = null;
	}

	return newTab;
}

/**
 * Teste si un objet est vide
 * 
 * @author adupuis
 * @param obj
 * @returns Boolean Renvoie true si l'objet est vide, false sinon
 */
function estVide(obj) {
	var prop;
	for (prop in obj) {
		if (obj.hasOwnProperty(prop)) {
			return false;
		}
	}
	return true;
}
