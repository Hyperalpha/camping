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

			//On définit le type de logement
			if ($reservation->getNombreCampingCars() > 0) {
				//Camping-car
				$labelLogement = "Camping-car : ";
				$classLogement = "bloc-camping-car";
			} else {
				if ($reservation->getNombreVans() > 0) {
					//Van
					$labelLogement = "Van : ";
					$classLogement = "bloc-van";
				} else {
					if ($reservation->getNombreCaravanes() > 0) {
						//Caravane
						$labelLogement = "Caravane : ";
						$classLogement = "bloc-caravane";
					} else {
						if ($reservation->getNombreGrandesTentes() > 0) {
							//Grande tente
							$labelLogement = "Grande tente : ";
							$classLogement = "bloc-grande-tente";
						} else {
							if ($reservation->getNombrePetitesTentes() > 0) {
								//Petite tente
								$labelLogement = "Petite tente : ";
								$classLogement = "bloc-petite-tente";
							}
						}
					}
				}
			}

			//Si la réservation n'a jamais été placée, on la dépose à un endroit calculé
			if (intval($reservation->getCoordonneesYEmplacement()) === 0) {
				$reservation->setCoordonneesYEmplacement($yNonPlaceEnCours);
				$yNonPlaceEnCours += $pasYEntreEmplacementsNonPlace;
			}

			//On crée les emplacements des réservations
			echo '<div id="emplacementNumero' . $reservation->getReference()
			. '" class="bloc-emplacement draggable ' . $classLogement . '" title="'
			. $labelLogement . $nomPrenomClient .'"
				 style="top: ' . $reservation->getCoordonneesYEmplacement()
			. 'px; left: ' . $reservation->getCoordonneesXEmplacement() . 'px;">
			<div class="pancarte-emplacement" title="Emplacement ' . '">
				<div>' . $reservation->getNumeroEmplacement() .'</div></div>
			<span class="text-bloc-emplacement">' . $nomPrenomClient .'</span></div>';
		}
	} ?>
</body>
</html>
