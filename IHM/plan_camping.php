<?php
include_once '../translation.php';
include_once '../Controller/PlanningCampingController.php';

?>

<!doctype html>

<html lang="fr">
<head>
<title>Emplacement des réservations</title>
<meta charset="utf-8">
<!-- link rel="stylesheet" href="/resources/demos/style.css" /-->
<link rel="stylesheet" href="css/jquery-ui-1.10.2.custom.css" />
<link rel="stylesheet" href="css/plan_camping.css" />
<script type="text/javascript" src="scripts/jquery/jquery-1.9.1.js"></script>
<script type="text/javascript"
	src="scripts/jquery/jquery-ui-1.10.2.custom.js"></script>
<script type="text/javascript" src="scripts/translation.js"></script>
<script type="text/javascript" src="scripts/commun.js"></script>
<script type="text/javascript" src="scripts/plan_camping.js"></script>
</head>
<body>
	<!-- Bouton de retour au planning de réservation -->
	<a id="boutonPlanningReservation"
		class="ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only"
		href="index.php"> <span
		class="ui-icon ui-icon-arrowthick-1-w ui-span-button">&nbsp;</span> <span
		class="ui-button-text ui-span-button">Retour planning réservations</span>
	</a>
	<a id="boutonImprimerEmplacements"> <img
		alt="Imprimer les emplacements" src="images/print.png" height="40px"
		width="40px" />
	</a>
	<br />
	<br />

	<!-- Plan du camping -->
	<img id="planCamping" src="images/plan_camping.png"
		alt="Plan du camping" />

	<!-- Popup d'alert -->
	<div id="popupAlert" title="Attention" class="popup">&nbsp;</div>

	<?php
	//Affichage des emplacements des réservations
	$controleur = new PlanningCampingController();
	$tabReservations = $controleur->recupererReservationsDuJour();
	$yNonPlaceEnCours = 150;
	$pasYEntreEmplacementsNonPlace = 40;

	if (!is_null($tabReservations) && count($tabReservations) > 0) {
		foreach ($tabReservations as $reservation) {
			$client = $reservation->getClient();
			$nomPrenomClient = str_replace('"', '\\"', $client->getPrenom() . ' ' . $client->getNom());
			$labelLogement = "";
			$classLogement = "";
			$roulotte = false;

			//On définit le type de logement
			if ($reservation->getRoulotteRouge() > 0) {
				//Roulotte rouge
				$roulotte = true;
			}
			if ($reservation->getRoulotteBleue() > 0) {
				//Roulotte bleue
				$roulotte = true;
			}
			if ($reservation->getTenteSafari() > 0) {
				//Tente safari
				$roulotte = true;
			}
			if ($reservation->getNombreTarif3() > 0) {
				//Tarif 3
				$labelLogement = "Tarif 3 : ";
				$classLogement = "bloc-tarif3";
			} elseif ($reservation->getNombreTarif2() > 0) {
				//Tarif 2
				$labelLogement = "Tarif 2 : ";
				$classLogement = "bloc-tarif2";
			} elseif ($reservation->getNombreTarif1() > 0) {
				//Tarif 1
				$labelLogement = "Tarif 1 : ";
				$classLogement = "bloc-tarif1";
			}

			//Si la réservation n'a jamais été placée, on la dépose à un endroit calculé
			if (intval($reservation->getCoordonneesYEmplacement()) === 0) {
				$reservation->setCoordonneesYEmplacement($yNonPlaceEnCours);
				$yNonPlaceEnCours += $pasYEntreEmplacementsNonPlace;
			}
		
			//On crée les emplacements des réservations
			if ($roulotte == false) {
				echo '<div id="emplacementNumero' . $reservation->getReference()
					. '" class="bloc-emplacement draggable ' . $classLogement . '" title="'
					. $labelLogement . $nomPrenomClient
					. '"style="top: ' . $reservation->getCoordonneesYEmplacement()
					. 'px; left: ' . $reservation->getCoordonneesXEmplacement()
					. 'px;"><div class="pancarte-emplacement" title="Emplacement '
					. $reservation->getNumeroEmplacement() . '"><img alt="Emplacement '
					. $reservation->getNumeroEmplacement() . '" src="images/emplacement.png"><div>'
					. $reservation->getNumeroEmplacement()
					. '</div></div><span class="text-bloc-emplacement">'
					. $nomPrenomClient .'</span></div>';
			}
			else {
				if ($reservation->getRoulotteRouge() > 0) {
					//On ajoute l'emplacement de la roulotte rouge
					echo '<div id="emplacementRoulotteRouge" class="bloc-emplacement" '
						. 'title="Roulotte rouge : ' . $nomPrenomClient . '">'
						. '<span class="text-bloc-emplacement">' . $nomPrenomClient .'</span></div>';
				}
				if ($reservation->getRoulotteBleue() > 0) {
					//On ajoute l'emplacement de la roulotte bleue
					echo '<div id="emplacementRoulotteBleue" class="bloc-emplacement" '
						. 'title="Roulotte rouge : ' . $nomPrenomClient . '">'
						. '<span class="text-bloc-emplacement">' . $nomPrenomClient .'</span></div>';
				}
				if ($reservation->getTenteSafari() > 0) {
					//On ajoute l'emplacement de la tente safari
					echo '<div id="emplacementTenteSafari" class="bloc-emplacement" '
						. 'title="Tente safari : ' . $nomPrenomClient . '">'
						. '<span class="text-bloc-emplacement">' . $nomPrenomClient .'</span></div>';
				}
			}
		}
	} ?>
</body>
</html>
