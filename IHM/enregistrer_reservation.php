<?php
include_once '../translation.php';
include_once '../Controller/PlanningCampingController.php';

/**
 * Enregistre une réservation
 * @author Arnaud DUPUIS
 * @param string reservation données de la réservation
 * @return string Si succes, renvoie idFiche|idClient. Renvoie false sinon
 */
$retour = false;

//On récupère les données de réservation
if (array_key_exists('reservation', $_POST)) {
	$strReservation = $_POST['reservation'];
	
	$planningCamping = new PlanningCampingController();
	
	$retour = $planningCamping->enregistrerReservation($strReservation);
}

//Retour en json
echo $retour;
?>