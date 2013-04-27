<?php
include_once '../translation.php';
include_once '../Controller/PlanningCampingController.php';

session_start();

//Partie construction du calendrier
$controleur = new PlanningCampingController();
$tabCalendrier = $controleur->construireCalendrierReservations();

//Initialisation des lignes du tableau
$htmlAnnees = "<tr class=\"ligneCalendrier\">\n";
$htmlMois = "<tr class=\"ligneCalendrier\">\n";
$htmlJours = "<tr class=\"ligneCalendrier\">\n";
$nbJours = 0;
$numJour = 0;

//Traitement des années
if ($tabCalendrier) {
	foreach ($tabCalendrier as $annee => $tabA) {
		//Traitement des mois
		if ($annee) {
			foreach ($tabA as $mois => $tabM) {
				//Traitement des jours
				if ($mois) {
					foreach ($tabM as $jour => $v) {
						//On crée un input caché avec la date du jour
						$htmlJours .= "<th id=\"colonneJour_" . $numJour . "\" "
						. "class=\"header_jours_calendrier\">" . $jour
						. "<input class=\"date_jour_calendrier\" value=\"" . $v
						. "\" type=\"hidden\"/></th>\n";
						$numJour += 1;
					}
				}
				$nbJours += count($tabM);
				$htmlMois .= "<th class=\"header_mois_calendrier\" colspan=\"" . count($tabM) . "\">" . $mois . "</th>\n";
			}
		}
		$htmlAnnees .= "<th class=\"header_annees_calendrier\" colspan=\"" . $nbJours . "\">" . $annee . "</th>\n";
	}
}

//Fermeture des balises
$htmlAnnees .= "</tr>";
$htmlMois .= "</tr>";
$htmlJours .= "</tr>";
?>

<!doctype html>

<html lang="fr">
<head>
<title>Tableau des réservations</title>
<meta charset="utf-8">
<link rel="stylesheet" href="/resources/demos/style.css" />
<link rel="stylesheet" href="css/jquery-ui-1.10.2.custom.css" />
<link rel="stylesheet" href="css/page_reservations.css" />
<script type="text/javascript" src="scripts/jquery/jquery-1.9.1.js"></script>
<script type="text/javascript"
	src="scripts/jquery/jquery-ui-1.10.2.custom.js"></script>
<script type="text/javascript" src="scripts/translation.js"></script>
<script type="text/javascript" src="scripts/commun.js"></script>
<script type="text/javascript" src="scripts/planning.js"></script>
<script type="text/javascript" src="scripts/popup_ajout_reservation.js"></script>
<script type="text/javascript" src="scripts/popup_reglages.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
<?php
		//Partie affichage des réservations
		$tabReservations = $controleur->recupererToutesReservations();
		
		if (!is_null($tabReservations) && count($tabReservations) > 0) {
			$reservations = '';
			foreach ($tabReservations as $reservation) {
				//On crée les blocs de réservation en javascript
				echo 'var donnees = parseInfosReservation("' . str_replace('"', '\\"', 
					$controleur->convertirReservationPourIHM($reservation)) . '");' . "\n";
				echo 'creerBlocReservation("", donnees);' . "\n";
			}
		}
		
		//Gestion des messages Flash
		if (array_key_exists('message_flash', $_SESSION)) {
			if (array_key_exists('message_flash_statut', $_SESSION)) {
				//Pas de gestion pour l'instant
				unset($_SESSION["message_flash_statut"]);
			}
			echo 'alertPop("' . $_SESSION["message_flash"] . '")';
			unset($_SESSION["message_flash"]);
		} ?>
		
		//Calcul des statistiques par jour
		calculStatistiquesParJour();

	});
	</script>
</head>
<body>
	<!-- Bouton d'ajout d'une réservation -->
	<a id="boutonAjouterReservation"
		class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only">
		<span class="ui-icon ui-icon-circle-plus ui-span-button">&nbsp;</span>
		<span class="ui-button-text ui-span-button">Ajouter une réservation</span>
	</a>

	<!-- Bouton pour accéder au plan du camping -->
	<a id="boutonEmplacementReservation"
		class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only"
		href="plan_camping.php">
			<span class="ui-button-text ui-span-button">
				<img alt="Emplacements" src="images/carte.png" height="30px" width="30px">
				Emplacements</span>
	</a>
	
	<!-- Bouton vers la popup de réglages -->
	<a id="boutonReglages"
		class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only">
			<span class="ui-button-text ui-span-button">
				<img alt="Réglages" src="images/reglages.png" height="30px" width="30px">
				Réglages</span>
	</a>

	<br />
	<br />
	<table id="tableauCalend" class="tableau_calendrier" 
		style="width: <?php echo ($nbJours * 30) ;?>px">
		<thead>
		<?php
		//Header du tableau année, mois et jours
		echo $htmlAnnees;
		echo $htmlMois;
		echo $htmlJours;

		//Création de la première ligne avec les colonnes du tableau
		?>
		</thead>
		<tbody>
			<!-- La première ligne (cachée) sert au dimensionnement des cellules -->
			<tr id="ligneCalend_1" class="ligne_calendrier">
			<?php
			for ($i = 0 ; $i < ($nbJours) ; $i++) {
				?>
				<td class="cellule_calendrier">&nbsp;</td>
				<?php
			}
			?>
			</tr>
			<!-- Les deux lignes à la fin servent aux statistiques -->
			<tr id="ligneStatPersonnesCalendrier" class="ligne_stat_personnes_calendrier">
			<?php
			for ($i = 0 ; $i < ($nbJours) ; $i++) {
				?>
				<td id="celluleStatPersonnes_<?php echo $i; ?>"
					 class="cellule_stat_personnes_calendrier"
					title="Nombre de personnes / jour">
					<label>&nbsp;</label></td>
				<?php
			}
			?>
			</tr>
			<tr id="ligneStatEmplacementsCalendrier" class="ligne_stat_emplacements_calendrier">
			<?php
			for ($i = 0 ; $i < ($nbJours) ; $i++) {
				?>
				<td id="celluleStatEmplacements_<?php echo $i; ?>"
					 class="cellule_stat_emplacements_calendrier"
					title="Nombre d'emplacements / jour">
					<label>&nbsp;</label></td>
				<?php
			}
			?>
			</tr>
		</tbody>
	</table>
	
	<!-- Bloc des statistiques sur la saison -->
	<div id="statistiquesSaison">
		Moyenne de personnes jusqu'à aujourd'hui : <label id="moyennePersonnesJusquaAujourdhui">-</label><br/>
		CA camping : <label id="caCamping">- €</label><br/>
		CA camping + roulottes : <label id="caCampingEtRoulottes">- €</label><br/>
	</div>

	<!-- Popup d'alert -->
	<div id="popupAlert" title="Attention" class="popup">&nbsp;</div>

	<!-- Popup de création/modification d'un réservation -->
	<?php include("popup_creer_modifier_reservation.html"); ?>

	<!-- Popup de détail d'une réservation -->
	<?php include("popup_details_reservation.html"); ?>
	
	<!-- Popup de réglages (chargée en différé) -->
	<div id="popupReglages" title="Réglages" class="popup texte-moyen">&nbsp;</div>
</body>
</html>
